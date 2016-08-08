<?php
/**
 * Purifiable Test
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * Purifiable Test Case
 */
class PurifiableTest extends NetCommonsControllerTestCase {

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
 * test purify
 *
 * @return void
 */
	public function testPurifyFail() {
		$FakeFailModel = ClassRegistry::init($this->_modelName);
		$FakeFailModel->create();

		$data = array();
		$content = '<a href="#" target="_hoge">anchor</a>';
		$data[$this->_modelName]['fail'] = $content;
		$result = $FakeFailModel->save($data);
		// FakeModelにはfailカラムがないため不正なtarget属性値でも除去されない
		$this->assertEquals($content, $result[$this->_modelName]['fail']);
	}
}
