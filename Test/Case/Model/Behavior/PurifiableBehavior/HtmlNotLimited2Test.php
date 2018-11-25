<?php
/**
 * WysiwygBehavior Test Case
 *
 * @author Mitsuru Mutaguchi <mutaguchi@opensource-workshop.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');

/**
 * WysiwygBehavior Test Case
 * Permission.html_not_limited.value = true の場合のテストケースその２
 * phpmd によりpublic 10メソッド越えのため、別テストクラスを作成
 */
class HtmlNotLimited2Test extends NetCommonsCakeTestCase {

/**
 * Model name
 *
 * @var string
 */
	private $__modelName = 'FakeModel';

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.wysiwyg.fake_model',
	);

/**
 * WysiwygBehaviorのロード
 *
 * @return bool|null|object
 */
	private function __loadBehavior() {
		Current::write('Permission.html_not_limited.value', true);
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		// PurifiableBehaviorのテストケースだが、呼び出しているのは、TestWysiwyg.FakeModelに指定されたWysiwygBehavior。
		// WysiwygBehaviorからPurifiableBehaviorは呼び出されていて、結果としてテストできている。
		$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeModel->create();
		return $FakeModel;
	}

/**
 * 指定のdata-size data-position data-imgid属性が除去されないこと
 */
	public function testSaveImg() {
		$FakeModel = $this->__loadBehavior();

		$content = '<img class="img-responsive nc3-img nc3-img-block" title="" src="{{__BASE_URL__}}/wysiwyg/image/download/1/9/big" alt="" data-size="big" data-position="" data-imgid="9" />';

		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		// HtmlNotLimitedであれば、htmlpurifierのチェックは通さないので、期待値は入力値と同じ。
		$expected = $content;

		$this->assertEquals($expected, $result[$this->__modelName]['content']);
	}
}
