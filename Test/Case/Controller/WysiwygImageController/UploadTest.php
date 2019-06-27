<?php
/**
 * WysiwygImageDownloadController
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('WysiwygControllerTestCase', 'Wysiwyg.TestSuite');
App::uses('File', 'Utility');

/**
 * WysiwygImageDownloadController::upload()のテスト
 */
class WysiwygImageControllerUploadTest extends WysiwygControllerTestCase {

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
	protected $_controller = 'wysiwyg_image';

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
 * upload()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testUploadOnFailure() {
		//テスト実行
		$this->_testGetAction(
			array('action' => 'upload'), null, null, 'view'
		);

		//チェック
		$this->assertEquals($this->vars['_serialize'], array(
			0 => 'statusCode',
			1 => 'result',
			2 => 'message'
		));
		$this->assertEquals($this->vars['statusCode'], 400);
		$this->assertEquals($this->vars['result'], false);
		$this->assertEquals($this->vars['message'], __d('net_commons', 'Bad Request'));
	}

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __data() {
		$this->generateNc(Inflector::camelize($this->_controller), array('components' => array(
			'Files.FileUpload' => array('getTemporaryUploadFile'),
			'Wysiwyg.Wysiwyg' => array('isUploadedFile'),
		)));

		//ログイン
		TestAuthGeneral::login($this);

		$this->controller->UploadFile = $this->getMockForModel(
			'Files.UploadFile',
			array('registByFile', 'uploadSettings'),
			array('plugin' => Inflector::underscore('UploadFile'))
		);

		$thumbnailSizes = array(
			'big' => '800ml',
			'medium' => '400ml',
			'small' => '200ml',
			'thumb' => '80x80',
			'biggest' => '1200ml',
			'origin_resize' => '1200ml',
		);
		$this->controller->UploadFile
			->expects($this->once())->method('uploadSettings')
			->with('real_file_name', 'thumbnailSizes', $thumbnailSizes);

		$data = array(
			'Block' => array(
				'key' => 'block_1',
				'room_id' => '2'
			),
			'Room' => array(
				'id' => '2',
			),
			'Wysiwyg' => array(
				'file' => array()
			)
		);
		return $data;
	}

/**
 * upload()アクションのテスト
 *
 * @return void
 */
	public function testUpload() {
		$post = $this->__data();

		$fileObj = new File(TMP . 'dummy');
		$this->controller->FileUpload
			->expects($this->once())->method('getTemporaryUploadFile')
			->with('Wysiwyg.file')
			->will($this->returnValue($fileObj));

		$data = array(
			'UploadFile' => array(
				'block_key' => 'block_1',
				'room_id' => '2',
			)
		);
		$file = Hash::merge($data, array(
			'UploadFile' => array(
				'id' => '1',
				'original_name' => 'dummy'
			)
		));
		$this->controller->UploadFile
			->expects($this->once())->method('registByFile')
			->with($fileObj, 'wysiwyg', null, 'Wysiwyg.file', $data)
			->will($this->returnValue($file));

		$this->controller->Wysiwyg
			->expects($this->once())->method('isUploadedFile')
			->will($this->returnValue(true));

		//テスト実行
		$this->_testPostAction('post', $post, array('action' => 'upload'), null, 'view');

		//チェック
		$this->assertEquals($this->vars['_serialize'], array(
			0 => 'statusCode',
			1 => 'result',
			2 => 'message',
			3 => 'file',
		));
		$this->assertEquals($this->vars['statusCode'], 200);
		$this->assertEquals($this->vars['result'], true);
		$this->assertEquals($this->vars['message'], '');
		$this->assertEquals(array_keys($this->vars['file']), ['id', 'original_name', 'path']);
		$this->assertEquals($this->vars['file']['id'], '1');
		$this->assertEquals($this->vars['file']['original_name'], 'dummy');
		$this->assertTextContains('/wysiwyg/image/download/2/1', $this->vars['file']['path']);
	}

/**
 * upload()アクションのWysiwyg->isUploadedFile()=falseのテスト
 *
 * @return void
 */
	public function testUploadOnIsUploadedFileFalse() {
		$post = $this->__data();

		$this->controller->FileUpload
			->expects($this->exactly(0))->method('getTemporaryUploadFile');

		$this->controller->UploadFile
			->expects($this->exactly(0))->method('registByFile');

		$this->controller->Wysiwyg
			->expects($this->once())->method('isUploadedFile')
			->will($this->returnValue(false));

		//テスト実行
		$this->_testPostAction('post', $post, array('action' => 'upload'), null, 'view');

		//チェック
		$this->assertEquals($this->vars['_serialize'], array(
			0 => 'statusCode',
			1 => 'result',
			2 => 'message',
			3 => 'file',
		));
		$this->assertEquals($this->vars['statusCode'], 400);
		$this->assertEquals($this->vars['result'], false);
		$this->assertEquals($this->vars['message'], 'File is required.');
		$this->assertEquals(array_keys($this->vars['file']), []);
	}

/**
 * upload()アクションのテスト
 *
 * @return void
 */
	public function testUploadValidationError() {
		$post = $this->__data();

		$fileObj = new File(TMP . 'dummy');
		$this->controller->FileUpload
			->expects($this->once())->method('getTemporaryUploadFile')
			->with('Wysiwyg.file')
			->will($this->returnValue($fileObj));

		$data = array(
			'UploadFile' => array(
				'block_key' => 'block_1',
				'room_id' => '2',
			)
		);
		$this->controller->UploadFile
			->expects($this->once())->method('registByFile')
			->with($fileObj, 'wysiwyg', null, 'Wysiwyg.file', $data)
			->will($this->returnValue(false));

		$this->controller->Wysiwyg
			->expects($this->once())->method('isUploadedFile')
			->will($this->returnValue(true));

		$this->controller->UploadFile->validationErrors['real_file_name'] = array(
			'validation error message'
		);

		//テスト実行
		$this->_testPostAction('post', $post, array('action' => 'upload'), null, 'view');

		//チェック
		$this->assertEquals($this->vars['_serialize'], array(
			0 => 'statusCode',
			1 => 'result',
			2 => 'message',
			3 => 'file',
		));
		$this->assertEquals($this->vars['statusCode'], 400);
		$this->assertEquals($this->vars['result'], false);
		$this->assertEquals($this->vars['message'], ['validation error message']);
		$this->assertEquals(array_keys($this->vars['file']), []);
	}

}
