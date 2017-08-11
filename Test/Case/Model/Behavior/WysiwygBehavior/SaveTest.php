<?php
/**
 * WysiwygBehavior::save()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygModelTestCase', 'Wysiwyg.TestSuite');
App::uses('TestWysiwygBehaviorSaveModelFixture', 'Wysiwyg.Test/Fixture');

/**
 * WysiwygBehavior::save()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\Model\Behavior\WysiwygBehavior
 */
class WysiwygBehaviorSaveTest extends WysiwygModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.wysiwyg.test_wysiwyg_behavior_save_model',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'wysiwyg';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		$this->TestModel = ClassRegistry::init('TestWysiwyg.TestWysiwygBehaviorSaveModel');
	}

/**
 * save()のテスト
 *
 * @return void
 */
	public function testSave() {
		//テストデータ
		$data = array(
			'TestWysiwygBehaviorSaveModel' => (new TestWysiwygBehaviorSaveModelFixture())->records[0],
			'Block' => [
				'key' => 'block_key_1'
			],
		);

		//テスト実施
		$result = $this->TestModel->save($data);

		//チェック
		$expected = [
			'TestWysiwygBehaviorSaveModel' => [
				'id' => '1',
				'key' => 'wysiwyg_test_key',
				'content' => 'Wysiwyg Test <img class="img-responsive nc3-img nc3-img-block" title="" src="{{__BASE_URL__}}/wysiwyg/image/download/1/1/big" alt="" /> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/1/2">wysiwyg.doc</a> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/1/3">wysiwyg.xls</a> Wysiwyg Test',
			],
			'Block' => [
				'key' => 'block_key_1',
			],
		];
		unset($result['TestWysiwygBehaviorSaveModel']['modified']);
		$this->assertEquals($expected, $result);

		$UploadFile = ClassRegistry::init('Files.UploadFile');
		$actual = $UploadFile->find('all', [
			'recursive' => -1,
			'fields' => ['id', 'plugin_key', 'content_key', 'field_name', 'room_id', 'block_key'],
			'order' => 'id'
		]);
		$expected = [
			0 => [
				'UploadFile' => [
					'id' => '1',
					'plugin_key' => 'test_wysiwyg',
					'content_key' => 'wysiwyg_test_key',
					'field_name' => 'Wysiwyg.file',
					'room_id' => '2',
					'block_key' => 'block_key_1',
				],
			],
			1 => [
				'UploadFile' => [
					'id' => '2',
					'plugin_key' => 'test_wysiwyg',
					'content_key' => 'content_key_2',
					'field_name' => 'Wysiwyg.file',
					'room_id' => '1',
					'block_key' => 'block_key_2',
				],
			],
			2 => [
				'UploadFile' => [
					'id' => '3',
					'plugin_key' => 'test_wysiwyg',
					'content_key' => 'wysiwyg_test_key',
					'field_name' => 'Wysiwyg.file',
					'room_id' => '2',
					'block_key' => 'block_key_1',
				],
			],
		];
		$this->assertEquals($expected, $actual);
	}

}
