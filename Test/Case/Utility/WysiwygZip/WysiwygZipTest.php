<?php
/**
 * Wysiwyg zip file test
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('WysiwygZip', 'Wysiwyg.Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * WysiwygZip Test Case
 *
 * @property TestWysiwygZip $Wysiwyg テスト用モデル
 */
class WysiwygZipTest extends NetCommonsCakeTestCase {

/**
 * wysiwyg test path
 *
 * @var string
 */
	private $__testPath;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'plugin.files.upload_file',
		'plugin.files.upload_files_content',
		'plugin.site_manager.site_setting',
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->__testPath = APP . 'webroot/files/upload_file/test';
		new Folder($this->__testPath, true, 0755);
	}
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$dir = new Folder($this->__testPath);
		$dir->delete();
		parent::tearDown();
	}

/**
 * test create
 *
 * @return void
 */
	public function testCreateWysiwygZipNoImg() {
		$fileName = 'document.txt';
		$fileContent = 'wysiwyg zip create test1';

		$wZip = new WysiwygZip();
		$zipFilePath = $wZip->createWysiwygZip($fileContent);

		$this->assertFileExists($zipFilePath);

		$unzip = new ZipArchive();
		$unzip->open($zipFilePath);
		$unzipFolder = new TemporaryFolder();
		$unzip->extractTo($unzipFolder->path);
		//
		$this->assertFileExists($unzipFolder->path . DS . $fileName);
		$fileSize = filesize($unzipFolder->path . DS . $fileName);
		$this->assertTrue($fileSize > 0);
		//
		$readContent = file_get_contents($unzipFolder->path . DS . $fileName);
		$this->assertTextEquals($fileContent, $readContent);
	}
/**
 * test create
 * ファイルの実態がなくてエラーになるパターン
 *
 * @return void
 */
	public function testCreateWysiwygZipWithWrongRoom() {
		Current::$current['Room']['id'] = '5';
		$wZip = new WysiwygZip();
		$this->setExpectedException('InternalErrorException');
		$wZip->createWysiwygZip(
			'wysiwyg zip create test2 <img src="logo.gif" />logo is important for design.' .
			'<img src="/wysiwyg/image/download/2/5" />');
	}
/**
 * test create
 *
 * @return void
 */
	public function testCreateWysiwygZipWithWrongBlock() {
		Current::$current['Room']['id'] = '2';
		$wZip = new WysiwygZip();
		$this->setExpectedException('InternalErrorException');
		$wZip->createWysiwygZip(
			'wysiwyg zip create test3 logo is important for design.' .
			'<img src="/wysiwyg/image/download/2/13" />');
	}
/**
 * test create
 *
 * @return void
 */
	public function testCreateWysiwygZipWithImg() {
		Current::$current['Room']['id'] = '2';
		Current::$current['Language']['id'] = 2;
		$dir = new Folder($this->__testPath);
		$dir->create('12');
		copy(APP . 'Plugin/Wysiwyg/Test/Fixture/michel2.gif', $this->__testPath . '/12/michel2.gif');

		$fileName = 'document.txt';
		$content = 'wysiwyg zip create test3 logo is important for design.<img src="/wysiwyg/image/download/2/12" />';

		$wZip = new WysiwygZip();
		$zipFilePath = $wZip->createWysiwygZip($content);

		// zipファイルはできているか
		$this->assertFileExists($zipFilePath);

		// 出来立てZIPを解凍してみて
		$unzip = new ZipArchive();
		$unzip->open($zipFilePath);
		$unzipFolder = new TemporaryFolder();
		$unzip->extractTo($unzipFolder->path);
		// textファイルはあるか
		$this->assertFileExists($unzipFolder->path . DS . $fileName);
		$fileSize = filesize($unzipFolder->path . DS . $fileName);
		$this->assertTrue($fileSize > 0);
		// 中身はあっているか
		$readContent = file_get_contents($unzipFolder->path . DS . $fileName);
		$this->assertTextEquals($content, $readContent);
		// 画像ファイルも入っているか ファイル名はupload_idにすり替わる
		$this->assertFileExists($unzipFolder->path . DS . '12.gif');
	}
/**
 * test get
 *
 * @return void
 */
	public function testGetWysiwygZip() {
		Current::$current['Room']['id'] = '2';
		$wZip = new WysiwygZip();
		$content = $wZip->getFromWysiwygZip(APP . 'Plugin/Wysiwyg/Test/Fixture/test_wysiwyg.zip');
		$this->assertTextContains('create test3 logo is', $content);
		$this->assertTextContains('/wysiwyg/image/download/2/14', $content);
	}
}
