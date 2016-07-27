<?php
/**
 * HTMLPurifier_Filter_Comment Utility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for HTMLPurifier_Filter_Comment Utility/Filter
 */
class HTMLPurifier_Filter_Comment extends HTMLPurifier_Filter {

/**
 * Filter name
 *
 * @var string
 */
	public $name = 'Comment';

/**
 * コメントを除去
 *
 * @param string $html パース対象HTML文字列
 * @param HTMLPurifier_Config $config HTMLPurifier_Config
 * @param HTMLPurifier_Context $context HTMLPurifier_Context
 * @return string
 */
	public function postFilter($html, $config, $context) {
		return preg_replace('/<!-{2,}(.*?)-{2,}>/', '', $html);
	}

}

// vim: et sw=4 sts=4
