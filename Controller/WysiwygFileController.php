<?php
/**
 * File Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygAppController', 'Wysiwyg.Controller');

/**
 * File Controller
 *
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @package NetCommons\Wysiwyg\Controller
 */
class WysiwygFileController extends WysiwygAppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Files.FileUpload',
		'Files.Download',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
	);

/**
 * uploadFileモデル用の validation設定
 *
 * @var array
 */
	protected $_validate = [];

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * upload action
 *
 * file: data[Wysiwyg][file]
 * block_key: data[Block][key]
 * としてそれぞれ POSTされるものとして作成。
 *
 * @return void
 */
	public function upload() {
		// 初期処理
		$uploadFileModel = $this->_getUploadFileModel();

		// Wysiwyg.file 情報が与えられていない時はエラーを返す。
		$uploadFile = false;
		if ($this->_isUploadedFile($this->data['Wysiwyg'])) {
			// FileUploadコンポーネントからアップロードファイル情報の取得
			$file = $this->FileUpload->getTemporaryUploadFile('Wysiwyg.file');

			// uploadFile登録に必要な data（block_key）を作成する。
			$data = [
				'UploadFile' => [
					'block_key' => $this->data['Block']['key'],
					'room_id' => $this->data['Block']['room_id'],
				]
			];
			$uploadFile = $uploadFileModel->registByFile($file, 'wysiwyg', null, 'Wysiwyg.file', $data);
		}

		// 戻り値として生成する値を返す
		// $file: ファイル情報
		// $message: エラーメッセージ
		// $result: 結果の OK/NG
		// ＄statusCode: responseとしても返す
		//
		$file = [];
		$message = '';
		if ($uploadFile) {
			$statusCode = 200;	// Status 200(OK)
			$result = true;

			// アップロードしたファイルのパスを作成
			$url = NetCommonsUrl::actionUrl(
				array(
					'plugin' => 'wysiwyg',
					'controller' => Inflector::underscore($this->name),
					'action' => 'download',
					$uploadFile['UploadFile']['room_id'],
					$uploadFile['UploadFile']['id']
				),
				true
			);

			$file = [
				'id' => $uploadFile['UploadFile']['id'],
				'original_name' => $uploadFile['UploadFile']['original_name'],
				'path' => $url,
			];
		} else {
			$statusCode = 400;	// Status 400(Bad request)
			$result = false;
			if ($uploadFileModel->validationErrors) {
				$message = $uploadFileModel->validationErrors['real_file_name'];
			} else {
				$message = 'File is required.';
			}
		}

		// JSONを返す
		$this->viewClass = 'Json';
		$this->response->statusCode($statusCode);
		$this->set(compact('statusCode', 'result', 'message', 'file'));
		$this->set('_serialize', ['statusCode', 'result', 'message', 'file']);
	}

/**
 * download action
 *
 * @param Int $roomId Room id
 * @param Int $id File id
 * @return void
 */
	public function download($roomId, $id) {
		$options = [
			'field' => 'Wysiwyg.file',
			'download' => true,
		];
		return $this->Download->doDownloadByUploadFileId($id, $options);
	}

/**
 * UploadFileモデルの取得
 *
 * 取得と同時にファイル関係の Validateをセットする
 *
 * @return UploadFile $file UploadFiloモデル
 */
	protected function _getUploadFileModel() {
		// UploadFileモデルを取得
		$file = ClassRegistry::init('Files.UploadFile');

		// validateルールの設定
		$file->validate = $this->_validate;

		return $file;
	}

/**
 * requestの中でファイルのアップロードエラーがあるかどうかを調べる
 *
 * @param Array $params 調べるリクエストデータ
 * @return bool
 */
	protected function _isUploadedFile($params) {
		$val = array_shift($params);
		if ((isset($val['error']) && $val['error'] == 0) ||
			(!empty( $val['tmp_name']) && $val['tmp_name'] != 'none')
		) {
			return is_uploaded_file($val['tmp_name']);
		}
		return false;
	}
}
