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
		'Wysiwyg.Wysiwyg',
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
 * upload action
 *
 * file: data[Wysiwyg][file]
 * block_key: data[Block][key]
 * としてそれぞれ POSTされるものとして作成。
 *
 * HACK: コントローラが仕事をやりすぎてる
 *
 * @return void
 */
	public function upload() {
		// JSONを返す
		$this->viewClass = 'Json';

		if (! $this->request->is('post')) {
			$statusCode = 400;	// Status 400(Bad request)
			$result = false;
			$message = __d('net_commons', 'Bad Request');
			$this->set(compact('statusCode', 'result', 'message'));
			$this->set('_serialize', ['statusCode', 'result', 'message']);
			return;
		}

		// 初期処理
		$this->_setUploadFileModel();

		// Wysiwyg.file 情報が与えられていない時はエラーを返す。
		$uploadFile = false;
		if ($this->Wysiwyg->isUploadedFile($this->data['Wysiwyg'])) {
			// FileUploadコンポーネントからアップロードファイル情報の取得
			/** @var TemporaryUploadFile $file */
			$file = $this->FileUpload->getTemporaryUploadFile('Wysiwyg.file');

			// uploadFile登録に必要な data（block_key）を作成する。
			$data = [
				'UploadFile' => [
					'block_key' => $this->data['Block']['key'],
					'room_id' => $this->data['Block']['room_id'],
				]
			];
			$uploadFile = $this->UploadFile->registByFile($file, 'wysiwyg', null, 'Wysiwyg.file', $data);
			if ($uploadFile) {
				$uploadFile = $this->Wysiwyg->overwriteOriginFile($uploadFile, 'origin_resize_');
			}
		}

		// 戻り値として生成する値を返す
		// $file: ファイル情報
		// $message: エラーメッセージ
		// $result: 結果の OK/NG
		// ＄statusCode: responseとしても返す
		//
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
			$file = [];
			$statusCode = 400;	// Status 400(Bad request)
			$result = false;
			if ($this->UploadFile->validationErrors) {
				$message = $this->UploadFile->validationErrors['real_file_name'];
			} else {
				$message = 'File is required.';
			}
		}

		// JSONを返す
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
			'size' => ''
		];

		// ファイル情報取得 plugin_keyとコンテンツID、フィールドの情報が必要
		$UploadFile = ClassRegistry::init('Files.UploadFile');

		$file = $UploadFile->findById($id);
		$response = $this->Download->doDownloadByUploadFile($file, $options);

		$inlineExts = ['wav', 'pdf'];
		if (isset($file['UploadFile']['extension']) &&
				in_array($file['UploadFile']['extension'], $inlineExts)) {
			$downloadFileName = $file['UploadFile']['original_name'];
			$content = 'inline;';
			$content .= 'filename*=UTF-8\'\'' . rawurlencode($downloadFileName);
			$response->header('Content-Disposition', $content);
		}

		return $response;
	}

/**
 * UploadFileモデルの取得
 *
 * 取得と同時にファイル関係の Validateをセットする
 *
 * @return void
 */
	protected function _setUploadFileModel() {
		//UploadFileモデルを取得
		//※テストでMockに差し替えが必要。
		//@codeCoverageIgnoreStart
		if (empty($this->UploadFile)) {
			$this->UploadFile = ClassRegistry::init('Files.UploadFile');
		}
		//@codeCoverageIgnoreEnd

		$thumbnailSizes = $this->UploadFile->actsAs['Upload.Upload']['real_file_name']['thumbnailSizes'];
		$thumbnailSizes['biggest'] = '1200ml';
		// 元ファイルをリサイズする大きさ
		$thumbnailSizes['origin_resize'] = '1200ml';
		$this->UploadFile->uploadSettings('real_file_name', 'thumbnailSizes', $thumbnailSizes);

		// validateルールの設定
		$this->UploadFile->validate = $this->_validate;
	}
}
