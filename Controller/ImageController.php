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

App::uses('FileController', 'Wysiwyg.Controller');

/**
 * Image Controller
 *
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @package NetCommons\Wysiwyg\Controller
 */
class ImageController extends FileController {

	protected $_validate = [
		'real_file_name' => [
			'rule' => ['isValidMimeType', ['image/gif', 'image/png', 'image/jpg', 'image/jpeg']],
			'message' => 'File is not a image'
		]
	];

/**
 * download action
 *
 * @param Int $id File id
 * @param String $size 画像のサイズ(big, midiul, small, thumb の4つから)
 * @throws NotFoundException
 * @return void
 */
	public function download($id, $size = '') {
		$options = [
				'field' => 'Wysiwyg.file',
		];

		// サイズ指定があるときにサイズ指定を行う。
		// 指定がなければオリジナルサイズ
		//
		if (!empty($size)) {
			// 指定したサイズが UploadFileモデル指定以外のサイズの時は 404 Not Found.
			if (array_key_exists($size, $this->_getThumbnailSizes()) === false) {
				throw new NotFoundException();
			}
			$options['size'] = $size;
		}

		return $this->Download->doDownloadByUploadFileId($id, $options);
	}

/**
 * ファイルモデルの画像サイズリストを取得する
 *
 * @return array
 */
	protected function _getThumbnailSizes() {
		$file = ClassRegistry::init('Files.UploadFile');
		return $file->actsAs['Upload.Upload']['real_file_name']['thumbnailSizes'];
	}
}
