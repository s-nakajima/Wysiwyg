<?php
/**
 * WysiwygBehavior::consistentContent()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allceator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygModelTestCase', 'Wysiwyg.TestSuite');
App::uses('TestWysiwygBehaviorSaveModelFixture', 'Wysiwyg.Test/Fixture');

/**
 * WysiwygBehavior::consistentContent()のテスト
 *
 * @author AllCreator <info@allceator.net>
 * @package NetCommons\Wysiwyg\Test\Case\Model\Behavior\WysiwygBehavior
 */
class WysiwygBehaviorConsistentContentTest extends WysiwygModelTestCase {

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
 * consistentContent()テストのDataProvider
 *
 * ### 戻り値
 *  - content コンテンツデータ
 *  - updRoomId 更新するルームID
 *
 * ### ケース：ルームIDを更新する（※そのほかは、afterSaveでテスト実施）
 *
 * @return array データ
 */
	public function dataProvider() {
		$content = 'Wysiwyg Test <img class="img-responsive nc3-img nc3-img-block" title="" src="{{__BASE_URL__}}/wysiwyg/image/download/3/1/big" alt="" /> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/3/2">wysiwyg.doc</a> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/3/3">wysiwyg.xls</a> Wysiwyg Test';
		$expected = 'Wysiwyg Test <img class="img-responsive nc3-img nc3-img-block" title="" src="{{__BASE_URL__}}/wysiwyg/image/download/3/1/big" alt="" /> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/3/2">wysiwyg.doc</a> Wysiwyg Test <a href="{{__BASE_URL__}}/wysiwyg/file/download/3/3">wysiwyg.xls</a> Wysiwyg Test';
		$result[0] = array();
		$result[0]['content'] = $content;
		$result[0]['roomId'] = 3;
		$result[0]['expected'] = $expected;

		return $result;
	}

/**
 * consistentContent()のテスト
 *
 * @param string $content コンテンツデータ
 * @param int $roomId ルームID
 * @param string $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testConsistentContent($content, $roomId, $expected) {
		//テスト実施
		$actual = $this->TestModel->consistentContent($content, $roomId);

		$this->assertEquals($expected, $actual);
	}
}