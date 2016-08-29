<?php
/**
 * WysiwygBehavior Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');

/**
 * WysiwygBehavior Test Case
 * Permission.html_not_limited.value = false の場合のテストケース
 */
class HtmlLimitedTest extends NetCommonsCakeTestCase {

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
		Current::write('Permission.html_not_limited.value', false);
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeModel->create();
		return $FakeModel;
	}

/**
 * 指定のstyle属性が除去されないこと（html_not_limited=false）
 */
	public function testSaveHtmlLimitedCSS() {
		$FakeModel = $this->__loadBehavior();

		$content = '<div id="div" style="display:block;">div</div>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のstyle属性が除去されること（html_not_limited=false）
 */
	public function testSaveHtmlLimitedCSSRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<div id="div" style="-moz-animation:none;">div</div>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<div id="div">div</div>', $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のタグ属性が除去されること（html_not_limited=false）
 */
	public function testSaveHtmlLimitedTag() {
		$FakeModel = $this->__loadBehavior();

		$content = '<pre font="large">pre</pre>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<pre>pre</pre>', $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のタグが除去されること（html_not_limited=false）
 */
	public function testSaveHtmlLimitedTagRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<tag id="tag">TAG</tag>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('TAG', $result[$this->__modelName]['content']);
	}

/**
 * 指定のスキーマが除去されないこと（html_not_limited=false）
 */
	public function testSaveHtmlLimitedAllowedSchemes() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="http://example.com">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のスキーマが除去されないこと（html_not_limited=false）
 */
	public function testSaveHtmlLimitedAllowedSchemesRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="nntp://example.com">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<a>anchor</a>', $result[$this->__modelName]['content']);
	}

/**
 * scriptタグが除去されること（html_not_limited=false）
 */
	public function testSaveHtmlLimitedScriptTagRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<script type="text/javascript">alert("alert");</script>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('', $result[$this->__modelName]['content']);
	}

/**
 * Youtube以外のURLを許可しないこと（html_not_limited=false）
 */
	public function testSaveHtmlLimitedExceptYoutubeRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<iframe src="http://www.example.com/" width="560" height="314"></iframe>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<iframe width="560" height="314"></iframe>', $result[$this->__modelName]['content']);
	}

/**
 * コメントが除去されること（html_not_limited=false）
 */
	public function testSaveHtmlLimitedCommentRemove() {
		$FakeModel = $this->__loadBehavior();

		$FakeModel->create();
		$content = '<!-- コメント --><p>コメント</p>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<p>コメント</p>', $result[$this->__modelName]['content']);
	}
}
