<?php
/**
 * View/Elements/mathjax_jsテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/mathjax_jsテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Test\test_app\Plugin\TestWysiwyg\Controller
 */
class TestViewElementsMathjaxJsController extends AppController {

/**
 * mathjax_js
 *
 * @return void
 */
	public function mathjax_js() {
		$this->autoRender = true;
	}

}
