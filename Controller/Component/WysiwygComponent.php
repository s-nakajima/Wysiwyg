<?php
/**
 * Wysiwyg Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * Wysiwyg Component
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Controller\Component
 */
class WysiwygComponent extends Component {

/**
 * requestの中でファイルのアップロードエラーがあるかどうかを調べる
 * ※Unitテストで使用するため、別ファイルに出す。
 *
 * @param array $params 調べるリクエストデータ
 * @return bool
 * @codeCoverageIgnore
 */
	public function isUploadedFile($params) {
		$val = array_shift($params);
		if ((isset($val['error']) && $val['error'] == 0) ||
			(!empty( $val['tmp_name']) && $val['tmp_name'] !== 'none')) {
			return is_uploaded_file($val['tmp_name']);
		}
		return false;
	}

/**
 * 元ファイルをリサイズしたファイルで上書き
 *
 * @param array $uploadFile UploadFileデータ
 * @param string $overwriteFilePrefix リサイズされた画像のprefix このprefixのついたファイルを元画像あつかいにする。
 * @return array|false UploadFile::save()の結果
 * @throws InternalErrorException
 */
	public function overwriteOriginFile(array $uploadFile, $overwriteFilePrefix) {
		$uploadFileModel = ClassRegistry::init('Files.UploadFile');
		// 元ファイル削除
		$originFilePath = $uploadFileModel->getRealFilePath($uploadFile);

		//  origin_resizeからprefix削除
		$originResizePath = substr($originFilePath, 0, -1 * strlen($uploadFile['UploadFile']['real_file_name'])) .
			$overwriteFilePrefix . $uploadFile['UploadFile']['real_file_name'];

		if (! file_exists($originResizePath)) {
			//リネームするファイルが泣けr場、そのままuploadFileを返す。
			return $uploadFile;
		}

		unlink($originFilePath);
		rename($originResizePath, $originFilePath);

		//  uploadFileのsize更新
		$stat = stat($originFilePath);
		$uploadFile['UploadFile']['size'] = $stat['size'];
		try {
			$uploadFile = $uploadFileModel->save(
				$uploadFile,
				['callbacks' => false, 'validate' => false]
			);
		} catch (Exception $e) {
			throw new InternalErrorException('Failed Update UploadFile.size');
		}
		return $uploadFile;
	}

}
