<?php
/**
 * WysiwygBehavior::updateUploadFile()のテスト
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
 * WysiwygBehavior::updateUploadFile()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\Model\Behavior\WysiwygBehavior
 */
class WysiwygBehaviorUpdateUploadFileTest extends WysiwygModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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
		$this->TestModel = ClassRegistry::init('TestWysiwyg.TestWysiwygBehaviorModel');
	}

/**
 * updateUploadFile()テストのDataProvider
 *
 * ### 戻り値
 *  - content コンテンツデータ
 *  - updContentKey 更新するコンテンツキー
 *  - updBlockKey 更新するブロックキー
 *  - updRoomId 更新するルームID
 *
 * ### ケース：ルームIDを更新する（※そのほかは、afterSaveでテスト実施）
 *
 * @return array データ
 */
	public function dataProvider() {
		$record = (new TestWysiwygBehaviorSaveModelFixture())->records[0];

		$result[0] = array();
		$result[0]['content'] = $record['content'];
		$result[0]['update'] = [
			'content_key' => $record['key'],
			'block_key' => 'block_key_1',
			'room_id' => '3',
		];
		$result[0]['mergeExpected'] = [
			0 => [
				'UploadFile' => [
					'id' => '1',
					'room_id' => '3',
				],
			],
			2 => [
				'UploadFile' => [
					'id' => '3',
					'room_id' => '3',
				],
			],
		];

		return $result;
	}

/**
 * updateUploadFile()のテスト
 *
 * @param string $content コンテンツデータ
 * @param array $update 更新データ
 * @param array $mergeExpected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testUpdateUploadFile($content, $update, $mergeExpected) {
		//テスト実施
		$result = $this->TestModel->updateUploadFile($content, $update, true);

		//チェック
		$this->assertTrue($result);

		$UploadFile = ClassRegistry::init('Files.UploadFile');
		$actual = $UploadFile->find('all', [
			'recursive' => -1,
			'fields' => ['id', 'plugin_key', 'content_key', 'field_name', 'room_id', 'block_key'],
			'order' => 'id'
		]);
		$expected = Hash::merge([
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
		], $mergeExpected);
		$this->assertEquals($expected, $actual);
	}

}
