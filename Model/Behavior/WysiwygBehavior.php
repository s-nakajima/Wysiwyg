<?php
/**
 * Wysiwyg Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * Wysiwyg Behavior
 *
 * @package  NetCommons\NetCommons\Model\Befavior
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 */
class WysiwygBehavior extends ModelBehavior {

	/** Wysiwygを利用するテキストエリア */
	protected $_fields = array();

	const REPLACE_BASE_URL = '{{__BASE_URL__}}';

	const WYSIWYG_REPLACE_PATH = 'wysiwyg\/[a-z_]*?\/download';

/**
 * SetUp Attachment behavior
 *
 * @param Model $model instance of model
 * @param array $config array of configuration settings.
 * @throws CakeException 先にOriginalKeyが登録されてないと例外
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->_fields[$model->alias] = array();
		if (isset($config['fields'])) {
			$this->_fields[$model->alias] = $config['fields'];
		}
		$model->Behaviors->load('Wysiwyg.Purifiable', array(
			'fields' => $this->_fields,
		));
	}

/**
 * After find callback. Can be used to modify any results returned by find.
 *
 * @param Model $model Model using this behavior
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind(Model $model, $results, $primary = false) {
		return $this->convertBaseUrl($model, $results);
	}

/**
 * {{__BASE_URL}}を変換する
 *
 * @param Model $model Model using this behavior
 * @param mixed $results 変換対象のデータ
 * @return mixed 変換した結果
 */
	public function convertBaseUrl(Model $model, $results) {
		// $this->_fields で定義された変数の REPLACE_BASE_URL キーワードを置換する
		//
		$baseUrl = h(substr(Router::url('/', true), 0, -1));

		foreach ($results as $key => $target) {
			if (isset($target[$model->alias]['id'])) {
				$results[$key] = $this->__replacePath($model, self::REPLACE_BASE_URL, $baseUrl, $target);
			}
		}

		return $results;
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		// 保存前にファイル添付／画像挿入を行ったものについては
		// fullBaseUrl を REPLACE_BASE_URL に変換する
		//
		$baseUrl = h(substr(Router::url('/', true), 0, -1));
		$model->data = $this->__replacePath($model, $baseUrl, self::REPLACE_BASE_URL, $model->data);

		return true;
	}

/**
 * afterSave is called after a model is saved.
 *
 * Wysiwyg に登録したファイル・画像には、content_key, block_key が未定義のため
 * 登録されていない場合がある。
 * それらのファイルデータに対して、登録を実行する。
 *
 * @param Model $model Model using this behavior
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return bool
 * @see Model::save()
 */
	public function afterSave(Model $model, $created, $options = array()) {
		foreach ($this->_fields[$model->alias] as $field) {
			if (isset($model->data[$model->alias][$field])) {
				// content_key または block_key が NULL の時は新規の登録ファイルとなるので
				// 改めて content_key, block_key をセットする
				$this->updateUploadFile(
					$model,
					$model->data[$model->alias][$field],
					[
						'content_key' => Hash::get($model->data, $model->alias . '.key'),
						'block_key' => Hash::get($model->data, 'Block.key')
					]
				);
			}
		}
	}

/**
 * UploadFileデータの更新処理
 *
 * Wysiwyg に登録したファイル・画像には、content_key, block_key が未定義のため
 * 登録されていない場合がある。
 * それらのファイルデータに対して、登録を実行する。
 *
 * また、カレンダーのような公開対象でルームIDを可変にできるようなものは、新規のファイルに対して、
 * room_idも更新する必要がある。
 *
 * @param Model $model このビヘイビアメソッドで使用されるモデル
 * @param string $content コンテンツデータ
 * @param array $update 更新するデータ
 * @param bool $doReplaceUrl {{__BASE_URL__}}に変換するかどうか
 * @return bool
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function updateUploadFile(Model $model, $content, $update, $doReplaceUrl = false) {
		if ($doReplaceUrl) {
			$baseUrl = h(substr(Router::url('/', true), 0, -1));
			$content = $this->__replaceContent($baseUrl, self::REPLACE_BASE_URL, $content);
		}
		$update = array_merge(
			['content_key' => null, 'block_key' => null, 'room_id' => null],
			$update
		);
		$pattern = sprintf(
			'/%s\/%s\/[0-9]*?\/([0-9]*)?/', self::REPLACE_BASE_URL, self::WYSIWYG_REPLACE_PATH
		);

		$uploadFile = ClassRegistry::init('Files.UploadFile');

		// アップロードされたファイル・画像の ID を取得
		$matches = [];
		preg_match_all($pattern, $content, $matches);

		$fileIds = $matches[1];

		// 更新対象となる Fileデータを取得
		// $fieIds より検索
		$files = $uploadFile->find('all', ['conditions' => ['id' => $fileIds]]);

		foreach ($files as $file) {
			// 指定されたUPDATE情報と現在情報が食い違う場合は
			// 改めて 情報をセットしなおす
			if ($this->__hasDiffFileData($file['UploadFile'], $update)) {
				if ($update['content_key']) {
					$file['UploadFile']['content_key'] = $update['content_key'];
				}
				if ($update['block_key']) {
					$file['UploadFile']['block_key'] = $update['block_key'];
				}
				if ($update['room_id']) {
					$file['UploadFile']['room_id'] = $update['room_id'];
				}
				$uploadFile->create();
				$uploadFile->save($file, false, false);
			}
		}

		return true;
	}

/**
 * 違いがあるかチェックする
 *
 * @param array $original 元のファイルデータ
 * @param array $specified 今回更新を指定されているデータ
 * @return bool
 */
	private function __hasDiffFileData($original, $specified) {
		// 新規の場合は無条件で指定データで更新しなくてはならない
		if (empty($original['content_key']) || empty($original['block_key'])) {
			return true;
		}

		// uploadFileに既にデータが設定されており、
		// かつ、今回の更新データのcontent_keyがuploadFileのそれと異なる場合は、コピペの可能性高い
		if ($original['content_key'] != Hash::get($specified, 'content_key', '')) {
			// 元データのroom_idを変えてはいけない
			return false;
		}

		// 更新の場合でルームIDなどを変更するのはcontent_keyが一致している場合のみ
		// かつ、指定されているデータが元のuploadFileと異なる場合に限ります
		$specified = array_filter($specified);
		foreach ($specified as $key => $spec) {
			if ($original[$key] != $spec) {
				return true;
			}
		}
		return false;
	}

/**
 * コンテンツにUploadFileのアクセスパスが記載されている場合、
 * 対象のルームIDにマッチするようにアクセスパスを修正する
 *
 * @param Model $model このビヘイビアメソッドで使用されるモデル
 * @param string $content コンテンツデータ
 * @param int $roomId このデータが保存されるコンテンツ
 * @return mixed
 */
	public function consistentContent($model, $content, $roomId) {
		$pattern = sprintf(
			'/%s\/(%s)\/[0-9]*?\/([0-9]*)?/', self::REPLACE_BASE_URL, self::WYSIWYG_REPLACE_PATH
		);
		$replace = sprintf('%s/\1/%d/\2', self::REPLACE_BASE_URL, $roomId);
		$content = preg_replace($pattern, $replace, $content);
		return $content;
	}

/**
 * Wysiwygフィールド内の「ファイル／画像」のパスの変換処理
 *
 * @param Model $model Model using this behavior
 * @param String $search 検索する文字列
 * @param String $replace 置換する文字列
 * @param Array $data 置換対象データ
 * @return Array $data を置換した内容を返す
 */
	private function __replacePath(Model $model, $search, $replace, $data) {
		// 定義フィールド全てを置換
		foreach ($this->_fields[$model->alias] as $field) {
			// 定義フィールドが存在しない場合は無視する
			if (isset($data[$model->alias][$field])) {
				$data[$model->alias][$field] = $this->__replaceContent(
					$search, $replace, $data[$model->alias][$field]
				);
			}
		}

		return $data;
	}

/**
 * Wysiwygフィールド内の「ファイル／画像」のパスの変換処理
 *
 * @param String $search 検索する文字列
 * @param String $replace 置換する文字列
 * @param string $content 置換対象文字列
 * @return string 置換した内容を返す
 */
	private function __replaceContent($search, $replace, $content) {
		// 検索対象に / があるとデリミタエラーが発生するので置換する
		$search = str_replace('/', '\/', $search);

		// 定義フィールドが存在しない場合は無視する
		if ($content) {
			$pattern = sprintf('/%s\/(%s)\/([0-9]*)/', $search, self::WYSIWYG_REPLACE_PATH);
			$replacement = sprintf('%s/\1/\2', $replace);

			$content = preg_replace($pattern, $replacement, $content);
		}

		return $content;
	}
}
