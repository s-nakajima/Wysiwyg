<?php
/**
 * WysiwygFileController::download()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygControllerTestCase', 'Wysiwyg.TestSuite');

/**
 * WysiwygFileController::download()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\Controller\WysiwygFileController
 */
class WysiwygFileControllerDownloadTest extends WysiwygControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'wysiwyg';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'wysiwyg_file';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);

		$this->generateNc(Inflector::camelize($this->_controller), array('components' => array(
			'Files.Download' => array('doDownloadByUploadFileId')
		)));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * download()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testDownloadGet() {
		$this->controller->Download
			->expects($this->once())->method('doDownloadByUploadFileId')
			->with('1', ['field' => 'Wysiwyg.file', 'download' => true])
			->will($this->returnValue('true'));

		//テスト実行
		$this->_testGetAction(
			array('action' => 'download', '2', '1'),
			array('method' => 'assertNotEmpty'), null, 'json'
		);
	}

}
