<?php
/**
 * FakeModelFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for FakeModelFixture
 */
class FakeModelFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => '', 'default' => '1', 'length' => '8', 'key' => 'primary'),
		'content' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'content | コンテンツ |  | ', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'created datetime | 作成日時 |  | '),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'modified datetime | 更新日時 |  | '),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'content' => 'Lorem ipsum dolor sit amet',
			'created' => '2016-08-01 10:32:05',
			'modified' => '2016-08-01 10:32:05',
		),
	);

}
