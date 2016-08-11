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
 * Permission.html_not_limited.value = true の場合のテストケース
 */
class HtmlNotLimitedTest extends NetCommonsCakeTestCase {

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
		$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeModel->create();
		return $FakeModel;
	}

/**
 * 指定のtarget属性が除去されないこと
 */
	public function testSaveFrameTargets() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="#" target="_blank">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のtarget属性が除去されること
 */
	public function testSaveFrameTargetsRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="#" target="_hoge">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<a href="#">anchor</a>', $result[$this->__modelName]['content']);
	}

/**
 * 指定のrel属性が除去されないこと
 */
	public function testSaveAllowedRel() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="#" rel="author">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * 指定以外のrel属性が除去されること
 */
	public function testSaveAllowedRelRemove() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="#" rel="hoge">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<a href="#">anchor</a>', $result[$this->__modelName]['content']);
	}

/**
 * id属性が除去されないこと
 */
	public function testSaveEnableID() {
		$FakeModel = $this->__loadBehavior();

		$content = '<a href="#" id="anchor">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * style属性が除去されないこと
 */
	public function testSaveCSS() {
		$FakeModel = $this->__loadBehavior();

		$contents = array(
			'display:none !important;',
			'color:#FF0000;color:#00FF00;',
		);
		$pattern = '<span style="%s">span</span>';
		foreach ($contents as $content) {
			$content = sprintf($pattern, $content);
			$data[$this->__modelName]['content'] = $content;
			$result = $FakeModel->save($data);
			$this->assertEquals($content, $result[$this->__modelName]['content']);
		}
	}

/**
 * scriptタグが除去されないこと（html_not_limited=true）
 */
	public function testSaveHtmlNotLimitedScriptTag() {
		$FakeModel = $this->__loadBehavior();

		$content = '<script type="text/javascript">alert("alert");</script>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals('<script type="text/javascript"><!--//--><![CDATA[//><!--
alert("alert");
//--><!]]></script>', $result[$this->__modelName]['content']);
	}

/**
 * YoutubeのURLが許可されること
 */
	public function testSaveYoutube() {
		$FakeModel = $this->__loadBehavior();

		$content = '<iframe src="http://www.youtube.com/embed/PCwL3-hkKrg" width="560" height="314"></iframe>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * Youtube以外のURLを許可すること（html_not_limited=true）
 */
	public function testSaveHtmlNotLimitedExceptYoutube() {
		$FakeModel = $this->__loadBehavior();

		$content = '<iframe src="http://www.example.com/" width="560" height="314"></iframe>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}

/**
 * コメントが除去されないこと（html_not_limited=true）
 */
	public function testSaveHtmlNotLimitedComment() {
		$FakeModel = $this->__loadBehavior();

		$content = '<!-- コメント --><p>コメント</p>';
		$data[$this->__modelName]['content'] = $content;
		$result = $FakeModel->save($data);
		$this->assertEquals($content, $result[$this->__modelName]['content']);
	}
}
