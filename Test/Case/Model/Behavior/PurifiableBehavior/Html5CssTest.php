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
 * HTML5のCSSが除去されないことのテスト
 */
class Html5CssTest extends NetCommonsCakeTestCase {

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'FakeModel';

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.wysiwyg.fake_model',
	);

/**
 * assert
 *
 * @param string $content
 */
	private function __assert($content) {
		Current::write('Permission.html_not_limited.value', false);
		$FakeModel = ClassRegistry::init('FakeModel');
		$FakeModel->create();

		$data = array();
		$data[$this->_modelName]['content'] = $content;

		$result = $FakeModel->save($data);
		$find = $FakeModel->findById($result[$this->_modelName]['id']);
		$this->assertEquals($content, $find[$this->_modelName]['content']);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * position,top,left,bottom,right,z-index
 */
	public function testSaveHtml5CssNotRemove1() {
		$content = '<p style="position:relative;z-index:2;">z-index:2' .
			'<span style="position:absolute;top:10px;left:20px;">AAAA</span></p>' .
			'<p style="position:relative;z-index:1;">z-index:1' .
			'<span style="position:absolute;bottom:30px;right:40px;">BBBB</span></p>';
		$this->__assert($content);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * direction,unicode-bidi
 */
	public function testSaveHtml5CssNotRemove2() {
		$content = '<p style="direction:rtl;unicode-bidi:bidi-override;">direction:rtl;unicode-bidi;</p>' .
			'<p style="direction:ltr;">direction:ltr;</p>';
		$this->__assert($content);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * width,height,min-width,min-height,max-width,max-height
 */
	public function testSaveHtml5CssNotRemove3() {
		$content = '<p style="width:100px;height:100px;color:#ff0000;">RED</p>' .
			'<p style="min-width:70px;min-height:70px;color:#0000ff;">BLUE</p>' .
			'<p style="max-width:50px;max-height:50px;color:#00ff00;">GREEN</p>';
		$this->__assert($content);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * text-justify,text-underline-position
 */
	public function testSaveHtml5CssNotRemove4() {
		$content = '<p style="text-justify:inter-word;">text-justify:inter-word</p>' .
			'<p style="text-decoration:underline;text-underline-position:below;">underline</p>';
		$this->__assert($content);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * empty-cells
 */
	public function testSaveHtml5CssNotRemove5() {
		$content = '<p style="border:solid 1px #ff0000;empty-cells:show;">SHOW</p>';
		$this->__assert($content);
	}

/**
 * 以下のCSSが除去されないこと
 *
 * cursor
 */
	public function testSaveHtml5CssNotRemove6() {
		$content = '<p style="cursor:pointer;">SHOW</p>';
		$this->__assert($content);
	}
}