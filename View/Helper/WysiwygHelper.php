<?php
/**
 * WysiwygFormHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * WysiwygHelper
 *
 * @package NetCommons\NetCommons\View\Helper
 */
class WysiwygHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
	);

/**
 * wysiwyg用のスクリプト呼び出し対応
 *
 * @return String wysiwyg js
 */
	public function wysiwygScript() {
		return $this->_View->element('Wysiwyg.wysiwyg_js');
	}
}
