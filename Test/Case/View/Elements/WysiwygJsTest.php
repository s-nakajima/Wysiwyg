<?php
/**
 * View/Elements/wysiwyg_jsのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygControllerTestCase', 'Wysiwyg.TestSuite');

/**
 * View/Elements/wysiwyg_jsのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\View\Elements\WysiwygJs
 */
class WysiwygViewElementsWysiwygJsTest extends WysiwygControllerTestCase {

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
		//テストコントローラ生成
		$this->generateNc('TestWysiwyg.TestViewElementsWysiwygJs');
	}

/**
 * View/Elements/wysiwyg_jsのテスト
 *
 * @return void
 */
	public function testWysiwygJs() {
		//テスト実行
		$this->_testGetAction(
			'/test_wysiwyg/test_view_elements_wysiwyg_js/wysiwyg_js',
			array('method' => 'assertNotEmpty'), null, 'view'
		);

		//チェック
		$pattern = '/' . preg_quote('View/Elements/wysiwyg_js', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->__assert();
	}

/**
 * View/Elements/wysiwyg_jsのmobileテスト
 *
 * @return void
 */
	public function testWysiwygJsMobile() {
		//テスト実行
		$this->_testGetAction(
			'/test_wysiwyg/test_view_elements_wysiwyg_js/wysiwyg_js_mobile',
			array('method' => 'assertNotEmpty'), null, 'view'
		);

		//チェック
		$pattern = '/' . preg_quote('View/Elements/wysiwyg_mobile', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->__assert();

		$pattern = '/' . preg_quote('/wysiwyg/css/mobile.css', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		Configure::delete('isMobile');
	}

/**
 * 評価
 *
 * @return void
 */
	private function __assert() {
		//チェック
		$expecteds = array(
			//JS
			'/components/tinymce/tinymce.min.js',
			'/components/angular-ui-tinymce/src/tinymce.js',
			'/components/simplePagination.js/jquery.simplePagination.js',
			'/net_commons/js/color_palette_value.js',
			'/wysiwyg/js/wysiwyg.js',
			'/wysiwyg/js/wysiwyg_app.js',
			'/wysiwyg/js/plugins/nc3_colorpicker/plugin.js',
			'/wysiwyg/js/plugins/tex/plugin.js',
			'/wysiwyg/js/plugins/tex/iframe.js',
			'/wysiwyg/js/plugins/file/plugin.js',
			'/wysiwyg/js/plugins/nc3_image/plugin.js',
			'/wysiwyg/js/plugins/nc3_preview/plugin.js',
			'/wysiwyg/js/plugins/titleicons/plugin.js',
			'/wysiwyg/js/plugins/nc3_textcolor/plugin.js',
			'/wysiwyg/js/plugins/booksearch/plugin.js',
			//CSS
			'/components/simplePagination.js/simplePagination.css',
			'/wysiwyg/css/tex.css',
		);
		foreach ($expecteds as $expected) {
			$pattern = '/' . preg_quote($expected, '/') . '/';
			$this->assertRegExp($pattern, $this->view);
		}

	}

}
