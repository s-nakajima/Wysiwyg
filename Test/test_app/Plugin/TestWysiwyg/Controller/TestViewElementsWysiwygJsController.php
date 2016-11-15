<?php
/**
 * View/Elements/wysiwyg_jsテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/wysiwyg_jsテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\test_app\Plugin\TestWysiwyg\Controller
 */
class TestViewElementsWysiwygJsController extends AppController {

/**
 * wysiwyg_js
 *
 * @return void
 */
	public function wysiwyg_js() {
		$this->autoRender = true;
	}

/**
 * wysiwyg_js
 *
 * @return void
 */
	public function wysiwyg_js_mobile() {
		$this->autoRender = true;
		Configure::write('isMobile', true);
	}

}
