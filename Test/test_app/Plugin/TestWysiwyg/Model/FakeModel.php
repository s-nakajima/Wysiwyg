<?php
/**
 * Wysiwyg Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * テスト用FakeModel
 */
class FakeModel extends CakeTestModel {

/**
 * @var array ビヘイビア
 */
	public $actsAs = array(
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('content'),
		),
	);
}
