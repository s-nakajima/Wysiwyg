<?php
/**
 * WysiwygHelper::wysiwyg()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygHelperTestCase', 'Wysiwyg.TestSuite');

/**
 * WysiwygHelper::wysiwyg()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\Case\View\Helper\WysiwygHelper
 */
class WysiwygHelperWysiwygTest extends WysiwygHelperTestCase {

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

		//テストデータ生成
		$viewVars = array();
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Wysiwyg.Wysiwyg', $viewVars, $requestData, $params);
	}

/**
 * wysiwyg()のテスト
 *
 * @return void
 */
	public function testWysiwyg() {
		//データ生成
		$fieldName = 'Model.field';
		$attributes = array();

		//テスト実施
		$result = $this->Wysiwyg->wysiwyg($fieldName, $attributes);

		$expected =
			'<div class="form-group">' .
				'<textarea name="data[Model][field]" ui-tinymce="tinymce.options" ng-model="model.field" ' .
							'rows="10" class="form-control" cols="30" id="ModelField">' .
				'</textarea>' .
				'<div class="has-error"></div>' .
			'</div>';
		$this->assertEquals($expected, $result);

		$script = $this->Wysiwyg->_View->fetch('script');

		$pattern = preg_quote('NetCommonsApp.service(\'nc3Configs\', function() {', '/') .
				'.*?' .
				preg_quote('this.is_mobile = \'\';});', '/');
		$this->assertRegExp('/' . $pattern . '/', $script);

		$pattern = '/' . preg_quote('/components/tinymce/tinymce.min.js', '/') . '/';
		$this->assertRegExp($pattern, $script);
	}

}
