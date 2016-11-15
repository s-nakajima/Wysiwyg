<?php
/**
 * View/Elements/mathjax_jsのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygControllerTestCase', 'Wysiwyg.TestSuite');

/**
 * View/Elements/mathjax_jsのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\View\Elements\MathjaxJs
 */
class WysiwygViewElementsMathjaxJsTest extends WysiwygControllerTestCase {

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
		$this->generateNc('TestWysiwyg.TestViewElementsMathjaxJs');
	}

/**
 * View/Elements/mathjax_jsのテスト
 *
 * @return void
 */
	public function testMathjaxJs() {
		//テスト実行
		$this->_testGetAction(
			'/test_wysiwyg/test_view_elements_mathjax_js/mathjax_js',
			array('method' => 'assertNotEmpty'), null, 'view'
		);

		//チェック
		$pattern = '/' . preg_quote('View/Elements/mathjax_js', '/') . '/';

		$view = $this->_parseView($this->view);
		$this->assertRegExp($pattern, $view);

		$expected =
			'<script type="text/x-mathjax-config">' .
				'//<![CDATA[ ' .
					'MathJax.Hub.Config({ ' .
						'skipStartupTypeset: true, ' .
						'tex2jax: { ' .
							'inlineMath: [[\'$$\',\'$$\'], [\'\\\\\\\\(\',\'\\\\\\\\)\']], ' .
							'displayMath: [[\'\\\\\\\\[\',\'\\\\\\\\]\']] ' .
						'}, ' .
						'asciimath2jax: { delimiters: [[\'$$\',\'$$\']] } }' .
					'); ' .
				'//]]>' .
			'</script>';
		$this->assertTextContains($expected, $view);

		$pattern = preg_quote('<script type="text/javascript" src="', '/') . '.*?' .
				preg_quote('/components/MathJax/MathJax.js?config=TeX-MML-AM_CHTML"></script>', '/');
		$this->assertRegExp('/' . $pattern . '/', $view);

		$expected =
			'<script type="text/javascript">' .
				'//<![CDATA[ ' .
					'$(document).ready(function(){ MathJax.Hub.Queue([\'Typeset\', MathJax.Hub, \'nc-container\']); }); ' .
				'//]]>' .
			'</script>';
		$this->assertTextContains($expected, $view);
	}

}
