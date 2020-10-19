<?php
/**
 * Wysiwyg zip file
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */


App::uses('ZipDownloader', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');
App::uses('UploadFile', 'Files.Model');

/**
 * WysiwygUtility
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Wysiwyg\Utility
 */
class WysiwygZip {

/**
 * wysiwyg attachment file path
 *
 * @var string
 */
	const WYSIWYG_FILE_KEY_PATTERN =
		'/(.*?)\/wysiwyg\/(?:image|file)\/download\/(\d*?)\/(\d*?)(?:\/|")(.*?)>/';
	const WYSIWYG_FILE_IMPORT_KEY_PATTERN =
		'/(?:src|href)="(.*?)\/wysiwyg\/(?:image|file)\/download\/(\d*?)\/(\d*?)(?:\/|")(.*?)>/';

/**
 * Constructor
 */
	public function __construct() {
		$this->UploadFile = ClassRegistry::init('Files.UploadFile');
	}

/**
 * getFromWysiwygZip
 * ZIPファイルにまとめられているWysiwygエディタの内容を読み取ります。
 * 中に添付ファイルがある場合は、それらの取り込みも行います。
 *
 * １．指定されたパスにあるZIPファイルを読み取り、
 * ２．中に添付ファイルがあるようなら、それをDB、また所定のフォルダに正しく取込み
 * ３．Wysiwygテキスト文字列のUPされたファイルパスを適宜書き直し
 * ４．そのテキスト文字列を返す
 *
 * @param string $zipFilePath Zipファイルへのパス
 * @param string $fileName Zipファイルの中にある読み取るべきファイル名
 * @return string wysiswyg editor data
 * @throws InternalErrorException
 */
	public function getFromWysiwygZip($zipFilePath, $fileName = 'document.txt') {
		// ZIPファイル解凍
		$unZip = new UnZip($zipFilePath);
		$tmpFolder = $unZip->extract();
		if ($tmpFolder === false) {
			return false;
		}

		// $fileの中を開く
		$filePath = $unZip->path . DS . $fileName;
		$file = new File($filePath);
		$data = $file->read();

		$roomId = Current::read('Room.id');
		// uploadFile登録に必要な data（block_key）を作成する。
		$uploadBlockKey = [
			'UploadFile' => [
				'block_key' => Current::read('Block.key'),
				'room_id' => Current::read('Block.room_id'),
			]
		];

		// 添付ファイルがある場合に備えて準備
		// imgタグやリンクａタグを１行ずつに分解する
		$tmpStr = str_replace('<img', "\n<img", $data);
		$tmpStr = str_replace('<a', "\n<a", $tmpStr);
		$tmpStr = str_replace('>', ">\n", $tmpStr);
		$tmpStrArr = explode("\n", $tmpStr);

		$retStr = '';
		// 1行ずつ処理
		foreach ($tmpStrArr as $line) {
			// wysiwyg行があるか？
			$matchCount = preg_match(self::WYSIWYG_FILE_IMPORT_KEY_PATTERN, $line, $matches);
			// ある
			if ($matchCount > 0) {
				// その中に書かれているwysiwygで設定されたファイルのリスト（uploadId)を得る
				$uploadId = $matches[3];
				// imageなのかfileなのか
				if (preg_match('/^<img/', $line)) {
					$type = 'image';
				} else {
					$type = 'file';
				}

				// uploadIdに一致するファイルを取り出す
				$upFileNames = $tmpFolder->find($uploadId . '.*');
				if (empty($upFileNames)) {
					CakeLog::error('Can not find wysiwyg file ' . $uploadId);
					throw new InternalErrorException();
				}
				// そのファイルは現在テンポラリフォルダにあるので、そのパス取得
				$upFile = new File($tmpFolder->path . DS . $upFileNames[0]);

				// そのファイルをUPLOAD処理する
				$uploadedFile = $this->UploadFile->registByFile(
					$upFile,
					'wysiwyg',
					null,
					'Wysiwyg.file',
					$uploadBlockKey);
				if (! $uploadedFile) {
					CakeLog::error('Can not upload wysiwyg file ' . $uploadId);
					throw new InternalErrorException();
				}

				// wysiwygのパス情報を新ルームIDと新UPLOADIDに差し替える
				// %s と wysiwyg のワードがくっついてしまっていやらしいが、
				// ここで”／”を区切りで入れると
				// Routerが返す文字列が”／”付けて返してくるので、セパレータ入が重複する
				if ($type == 'image') {
					$line = sprintf('<img class="img-responsive nc3-img" src="%swysiwyg/%s/download/%d/%d/%s>',
						//$matches[1],
						Router::url('/', true),
						$type,
						$roomId,
						$uploadedFile['UploadFile']['id'],
						$matches[4]);
				} else {
					$line = sprintf('<a href="%swysiwyg/%s/download/%d/%d target="_blank" rel="noopener noreferrer"">',
						//$matches[1],
						Router::url('/', true),
						$type,
						$roomId,
						$uploadedFile['UploadFile']['id']);
				}
			}
			// wysiwygテキスト再構築
			$retStr .= $line;
		}
		// 構築したテキストを返す
		return $retStr;
	}
/**
 * createWysiwygZip
 *
 * @param string $data wysiswyg editor content
 * @param string $fileName wysiwygのテキストをまとめるファイル名
 * @return string 作成したZIPファイルへのパス
 * @throws InternalErrorException
 */
	public function createWysiwygZip($data, $fileName = 'document.txt') {
		$zip = new ZipDownloader();

		// UPLOADされているファイル情報を取り出す
		$tmpStr = $data;
		$tmpStr = str_replace('<img', "\n<img", $tmpStr);
		$tmpStr = str_replace('<a', "\n<a", $tmpStr);
		$matchCount = preg_match_all(self::WYSIWYG_FILE_KEY_PATTERN, $tmpStr, $matches);
		if ($matchCount > 0) {
			// ファイルのUPLOAD_IDを取り出す
			foreach ($matches[3] as $uploadId) {
				// ファイル情報を取得してくる
				$uploadFile = $this->UploadFile->findById($uploadId);
				if ($uploadFile) {
					$uploadFile = $uploadFile['UploadFile'];
					// ルームチェック
					if ($uploadFile['room_id']) {
						$roomId = Current::read('Room.id');
						if ($uploadFile['room_id'] != $roomId) {
							CakeLog::error('Can not find wysiwyg file ' . $uploadId);
							throw new InternalErrorException();
						}
					}
					if ($uploadFile['block_key']) {
						// block_keyによるガード
						$Block = ClassRegistry::init('Blocks.Block');
						$uploadFileBlock = $Block->findByKey(
							$uploadFile['block_key']
						);
						if ($Block->isVisible($uploadFileBlock) === false) {
							CakeLog::error('Can not find wysiwyg file ' . $uploadId);
							throw new InternalErrorException();
						}
					}
					// そのファイルをZIPに含める
					$path = UPLOADS_ROOT . trim($uploadFile['path'], '/') . '/' .
						$uploadId . '/' . $uploadFile['real_file_name'];

					$zip->addFile($path, $uploadId . '.' . $uploadFile['extension']);
				}
			}
		}
		$zip->addFromString($fileName, $data);
		$zip->close();
		return $zip->path;
	}
}
