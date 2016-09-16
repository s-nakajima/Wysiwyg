<?php
/**
 * Element of wysiwyg include
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

// wysiwyg呼び出し
echo $this->NetCommonsHtml->script(
	array(
		'/components/tinymce/tinymce.min.js',
		'/components/angular-ui-tinymce/src/tinymce.js',
		'/components/simplePagination.js/jquery.simplePagination.js',
		'/net_commons/js/color_palette_value.js',
		'/wysiwyg/js/wysiwyg.js',
		'/wysiwyg/js/wysiwyg_app.js',
		'/wysiwyg/js/plugins/nc3_colorpicker/plugin.js',
		'/wysiwyg/js/plugins/tex/plugin.js',
		'/wysiwyg/js/plugins/tex/iframe.js',
		'/wysiwyg/js/plugins/file/plugin.js',
		'/wysiwyg/js/plugins/nc3_image/plugin.js',
		'/wysiwyg/js/plugins/nc3_preview/plugin.js',
		'/wysiwyg/js/plugins/titleicons/plugin.js',
		'/wysiwyg/js/plugins/nc3_textcolor/plugin.js',
		'/wysiwyg/js/plugins/booksearch/plugin.js',
	)
);

$cssArray = [
	'/components/simplePagination.js/simplePagination.css',
	'/wysiwyg/css/tex.css',
];
// mobile 時のみ CSSの適用を行う
if (Configure::read('isMobile')) {
	$cssArray[] = '/wysiwyg/css/mobile.css';
}
echo $this->NetCommonsHtml->css($cssArray);
