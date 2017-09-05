<?php
/**
 * Unitテスト用Fixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UploadFileFixture', 'Files.Test/Fixture');

/**
 * Unitテスト用Fixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Fixture
 */
class UploadFile4wysiwygFixture extends UploadFileFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UploadFile';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'upload_files';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'plugin_key' => 'test_wysiwyg',
			'content_key' => '',
			'field_name' => 'Wysiwyg.file',
			'original_name' => 'wysiwyg.jpg',
			'path' => 'files/upload_file/real_file_name/2/',
			'real_file_name' => 'real_file_name_1.jpg',
			'extension' => 'jpg',
			'mimetype' => 'image/jpg',
			'size' => 1,
			'download_count' => 1,
			'total_download_count' => 1,
			'room_id' => '2',
			'block_key' => '',
		),
		array(
			'id' => '2',
			'plugin_key' => 'test_wysiwyg',
			'content_key' => 'content_key_2',
			'field_name' => 'Wysiwyg.file',
			'original_name' => 'wysiwyg.doc',
			'path' => 'files/upload_file/real_file_name/1/',
			'real_file_name' => 'real_file_name_2.doc',
			'extension' => 'doc',
			'mimetype' => 'application/msword',
			'size' => 1,
			'download_count' => 1,
			'total_download_count' => 1,
			'room_id' => '1',
			'block_key' => 'block_key_2',
		),
		array(
			'id' => '3',
			'plugin_key' => 'test_wysiwyg',
			'content_key' => '',
			'field_name' => 'Wysiwyg.file',
			'original_name' => 'wysiwyg.xls',
			'path' => 'files/upload_file/real_file_name/2/',
			'real_file_name' => 'real_file_name_3.xls',
			'extension' => 'xls',
			'mimetype' => 'application/vnd.ms-excel',
			'size' => 1,
			'download_count' => 1,
			'total_download_count' => 1,
			'room_id' => '2',
			'block_key' => '',
		),
		array(
			'id' => '4',
			'plugin_key' => 'test_wysiwyg',
			'content_key' => 'wysiwyg_test_key',
			'field_name' => 'Wysiwyg.file',
			'original_name' => 'wysiwyg.xls',
			'path' => 'files/upload_file/real_file_name/2/',
			'real_file_name' => 'real_file_name_3.xls',
			'extension' => 'xls',
			'mimetype' => 'application/vnd.ms-excel',
			'size' => 1,
			'download_count' => 1,
			'total_download_count' => 1,
			'room_id' => '3',
			'block_key' => 'block_key_4',
		),
		array(
			'id' => '5',
			'plugin_key' => 'test_wysiwyg',
			'content_key' => 'wysiwyg_test_key',
			'field_name' => 'Wysiwyg.file',
			'original_name' => 'wysiwyg.xls',
			'path' => 'files/upload_file/real_file_name/2/',
			'real_file_name' => 'real_file_name_3.xls',
			'extension' => 'xls',
			'mimetype' => 'application/vnd.ms-excel',
			'size' => 1,
			'download_count' => 1,
			'total_download_count' => 1,
			'room_id' => '3',
			'block_key' => 'block_key_1',
		),
	);

}
