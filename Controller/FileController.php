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
class FileController extends WysiwygAppController {

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
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// TinyMCE のアップロードフォームに Token埋め込み方法が未解決のため
		// ひとまずセキュリティ Component から uploadアクションを除外する
		$this->Security->unlockedActions = array('upload', 'download');
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
		$uploadFileModel = ClassRegistry::init('Files.UploadFile');

		// FileUploadコンポーネントからアップロードファイル情報の取得
		$file = $this->FileUpload->getTemporaryUploadFile('Wysiwyg.file');

		// uploadFile登録に必要な data（block_key）を作成する。
		$data = [
			'UploadFile' => [
				'block_key' => $this->data['Block']['key']
			]
		];
		$uploadFile = $uploadFileModel->registByFile($file, 'wysiwyg', null, 'Wysiwyg.file', $data);

		$requestFile = $this->data['Wysiwyg']['file'];

		// 戻りとして JSONを返す
		$this->viewClass = 'Json';
		$this->set(compact('uploadFile', 'requestFile'));
		$this->set('_serialize', ['uploadFile', 'requestFile']);
	}

/**
 * download action
 *
 * @param Int $id File id
 * @return void
 */
	public function download($id) {
		$options = [
				'field' => 'Wysiwyg.file',
				'download' => true,
		];
		return $this->Download->doDownloadByUploadFileId($id, $options);
	}
}
