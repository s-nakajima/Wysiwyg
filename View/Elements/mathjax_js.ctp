<?php
/**
 * Element of MathJax include
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

// wysiwyg呼び出し
echo $this->NetCommonsHtml->script(
	array(
		'/components/MathJax/MathJax.js?config=TeX-MML-AM_CHTML',
	)
);
