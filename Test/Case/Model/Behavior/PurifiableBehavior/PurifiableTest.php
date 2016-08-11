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

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');

/**
 * Purifiable Test Case
 */
class PurifiableTest extends NetCommonsCakeTestCase {

/**
 * Model name
 *
 * @var string
 */
	private $__modelName = 'FakeModel';

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
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		$FakeFailModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeFailModel->create();

		$data = array();
		$content = '<a href="#" target="_hoge">anchor</a>';
		$data[$this->__modelName]['fail'] = $content;
		$result = $FakeFailModel->save($data);
		// FakeModelにはfailカラムがないため不正なtarget属性値でも除去されない
		$this->assertEquals($content, $result[$this->__modelName]['fail']);
	}

/**
 * ディレクトリの再帰削除
 *
 * @param $dir
 */
	private function __rmdir($dir) {
		if (! file_exists($dir)) {
			return;
		}
		$dhandle = opendir($dir);
		if ($dhandle) {
			while (false !== ($fname = readdir($dhandle))) {
				if (is_dir("{$dir}/{$fname}")) {
					if (($fname != '.') && ($fname != '..')) {
						$this->__rmdir("$dir/$fname");
					}
				} else {
					unlink("{$dir}/{$fname}");
				}
			}
			closedir($dhandle);
		}
		rmdir($dir);
	}

/**
 * test rmdir
 *
 * @return void
 */
	public function testRmdir() {
		$cachePath = CACHE . 'HTMLPurifier' . DS;
		$this->__rmdir($cachePath);

		try {
			Current::write('Permission.html_not_limited.value', true);

			NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
			$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
			$FakeModel->create();

			$content = '<a href="#" target="_blank">anchor</a>';
			$data[$this->__modelName]['content'] = $content;
			$FakeModel->save($data);
		} catch (Exception $e) {
			$this->assertEquals('Base directory ' . $cachePath . ' does not exist,
                    please create or change using %Cache.SerializerPath', $e->getMessage());
		}
	}

/**
 * test mkdir
 *
 * @return void
 */
	public function testMkdir() {
		$cachePath = CACHE . 'HTMLPurifier' . DS;

		Current::write('Permission.html_not_limited.value', true);

		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeModel->create();

		$content = '<a href="#" target="_blank">anchor</a>';
		$data[$this->__modelName]['content'] = $content;
		$FakeModel->save($data);
		$this->assertFileExists($cachePath);
	}
}
