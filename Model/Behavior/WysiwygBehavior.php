<?php
/**
 * OriginalKey Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * OriginalKey Behavior
 *
 * @package  NetCommons\NetCommons\Model\Befavior
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 */
class WysiwygBehavior extends ModelBehavior {

	/** wysiwig利用を行うテキストエリア */
	protected $_fields = array();

	const REPLACE_BASE_URL = '{{__BASE_URL__}}';

/**
 * SetUp Attachment behavior
 *
 * @param Model $model instance of model
 * @param array $config array of configuration settings.
 * @throws CakeException 先にOriginalKeyが登録されてないと例外
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->_fields = $config['_fields'];
	}

}
