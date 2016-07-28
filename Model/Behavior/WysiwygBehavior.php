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
App::uses('Purifiable', 'Wysiwyg.Utility');

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

	const WYSIWYG_REPLACE_PATH = 'wysiwyg\/.*\/download';

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
		$setting = (new Purifiable())->getSetting();
		$setting = Hash::merge(array(
			'fields' => $config['fields'],
			'overwrite' => true,
		), $setting);
		$model->Behaviors->load('Purifiable.Purifiable', $setting);
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
		// $this->_fields で定義された変数の REPLACE_BASE_URL キーワードを置換する
		//
		$baseUrl = h(Configure::read('App.fullBaseUrl'));

		foreach ($results as $key => $target) {
			if (isset($target[$model->alias]['id'])) {
				$results[$key] = $this->__replaceString($model, self::REPLACE_BASE_URL, $baseUrl, $target);
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
		$baseUrl = h(Configure::read('App.fullBaseUrl'));
		$model->data = $this->__replaceString($model, $baseUrl, self::REPLACE_BASE_URL, $model->data);

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
		$pattern = sprintf(
						'/%s\/%s\/[0-9]*\/([0-9]*)/',
						self::REPLACE_BASE_URL,
						self::WYSIWYG_REPLACE_PATH
					);
		$uploadFile = ClassRegistry::init('Files.UploadFile');

		foreach ($this->_fields[$model->alias] as $field) {
			if (isset($model->data[$model->alias][$field])) {
				// アップロードされたファイル・画像の ID を取得
				preg_match_all($pattern, $model->data[$model->alias][$field], $matches);

				$fileIds = $matches[1];

				// 更新対象となる Fileデータを取得
				// $fieIds より検索
				//
				$files = $uploadFile->find('all', ['conditions' => ['id' => $fileIds]]);

				foreach ($files as $file) {
					// content_key または block_key が NULL の時は新規の登録ファイルとなるので
					// 改めて content_key, block_key をセットする
					//
					if (empty($file['UploadFile']['content_key']) || empty($file['UploadFile']['block_key'])) {
						$file['UploadFile']['content_key'] = $model->data[$model->alias]['key'];
						$file['UploadFile']['block_key'] = $model->data['Block']['key'];

						$uploadFile->create();
						$uploadFile->save($file);
					}
				}
			}
		}
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
	private function __replaceString(Model $model, $search, $replace, $data) {
		// 検索対象に / があるとデリミタエラーが発生するので置換する
		$search = str_replace('/', '\/', $search);
		// 定義フィールド全てを置換
		foreach ($this->_fields[$model->alias] as $field) {
			// 定義フィールドが存在しない場合は無視する
			if (isset($data[$model->alias][$field])) {
				$pattern = sprintf('/%s\/(%s)\/([0-9]*)/', $search, self::WYSIWYG_REPLACE_PATH);
				$replacement = sprintf('%s/\1/\2', $replace);

				$data[$model->alias][$field] =
					preg_replace($pattern, $replacement, $data[$model->alias][$field]);
			}
		}

		return $data;
	}
}
