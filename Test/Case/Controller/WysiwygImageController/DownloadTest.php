<?php
/**
 * WysiwygImageController::download()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygControllerTestCase', 'Wysiwyg.TestSuite');

/**
 * WysiwygImageController::download()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\Controller\WysiwygImageController
 */
class WysiwygImageControllerDownloadTest extends WysiwygControllerTestCase {

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
	protected $_controller = 'wysiwyg_image_download';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログイン
		TestAuthGeneral::login($this);
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
 * download()アクションのGetリクエストテストのDataProvider
 *
 * ### 戻り値
 *  - size サイズ
 *  - exception Exception
 *
 * @return array データ
 */
	public function dataProvider() {
		return array(
			array('size' => '', 'exception' => null),
			array('size' => 'big', 'exception' => null),
			array('size' => 'medium', 'exception' => null),
			array('size' => 'small', 'exception' => null),
			array('size' => 'thumb', 'exception' => null),
			array('size' => 'biggest', 'exception' => null),
			array('size' => 'aaaa', 'exception' => 'NotFoundException'),
		);
	}

/**
 * download()アクションのGetリクエストテスト
 *
 * @param string $size サイズ
 * @param string|null $exception Exception
 * @return void
 * @dataProvider dataProvider
 */
	public function testDownloadGet($size, $exception) {
		$options = array('field' => 'Wysiwyg.file');
		if ($size) {
			$options['size'] = $size;
		}

		if (! $exception) {
			$this->generateNc(Inflector::camelize($this->_controller), array('components' => array(
				'Files.Download' => array('doDownloadByUploadFileId')
			)));
			$this->controller->Download
				->expects($this->once())->method('doDownloadByUploadFileId')
				->with('1', $options)
				->will($this->returnValue('true'));
			$view = 'json';
		} else {
			$view = 'view';
		}

		//テスト実行
		$params = array(
			'action' => 'download', '2', '1'
		);
		if ($size) {
			$params[] = $size;
		}
		$this->_testGetAction($params, null, $exception, $view);
	}

}
