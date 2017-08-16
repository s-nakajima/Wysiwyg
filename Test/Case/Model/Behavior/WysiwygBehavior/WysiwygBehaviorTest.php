<?php
/**
 * WysiwygBehaviorTest Test Case
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('WysiwygBehavior', 'Wysiwyg.Model/Behavior');

/**
 * Summary for WysiwygBehaviorTest Test Case
 */
class WysiwygBehaviorTest extends CakeTestCase {

/**
 * WysiwygBehavior
 *
 * @var WysiwygBehavior
 */
	public $WysiwygBehavior = null;

/**
 * Dummy Model for WysiwygBehavior
 *
 * @var Model
 */
	public $dummyModel = null;

/**
 * WysiwygBehavior
 *
 * @var string
 */
	public $fieldName = 'test';

/**
 * Default Router::fullBaseUrl
 *
 * @var string
 */
	public $defaultFullBaseUrl = null;

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->WysiwygBehavior = new WysiwygBehavior();
		$this->dummyModel = new Model();
		$this->WysiwygBehavior->setup(
			$this->dummyModel,
			[
				'fields' => [
					$this->fieldName
				]
			]
		);
		$this->defaultFullBaseUrl = Router::fullBaseUrl();
		Router::fullBaseUrl('http://example.com/example');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->WysiwygBehavior, $this->dummyModel);
		Router::fullBaseUrl($this->defaultFullBaseUrl);

		parent::tearDown();
	}

/**
 * Testing replaced content for download action on BeforeSave
 *
 * @return void
 */
	public function testReplacedContentForDownloadActionOnBeforeSave() {
		$url = Router::url('/', true);
		$alias = $this->dummyModel->alias;
		$fieldName = $this->fieldName;
		$imagePath = 'wysiwyg/image/download/999';
		$filePath = 'wysiwyg/wysiwyg_file/download/999';

		// 改行あり
		$expected = 'dummy' . WysiwygBehavior::REPLACE_BASE_URL . '/' . $imagePath . "\n" .
			'dummy' . WysiwygBehavior::REPLACE_BASE_URL . '/' . $filePath;

		$this->dummyModel->data[$alias][$fieldName] = 'dummy' . $url . $imagePath . "\n" .
			'dummy' . $url . $filePath;
		$this->WysiwygBehavior->beforeSave($this->dummyModel);

		$this->assertEquals($expected, $this->dummyModel->data[$alias][$fieldName]);

		// 改行なし
		$expected = 'dummy' . WysiwygBehavior::REPLACE_BASE_URL . '/' . $imagePath .
			'dummy' . WysiwygBehavior::REPLACE_BASE_URL . '/' . $filePath;

		$this->dummyModel->data[$alias][$fieldName] = 'dummy' . $url . $imagePath .
			'dummy' . $url . $filePath;
		$this->WysiwygBehavior->beforeSave($this->dummyModel);

		$this->assertEquals($expected, $this->dummyModel->data[$alias][$fieldName]);
	}

}
