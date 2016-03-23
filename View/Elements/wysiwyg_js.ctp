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
		'/components/tinymce-dist/tinymce.min.js',
		'/components/angular-ui-tinymce/src/tinymce.js',
		'/net_commons/js/color_palette_value.js',
		'/wysiwyg/js/wysiwyg.js',
		'/wysiwyg/js/wysiwyg_app.js',
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

echo $this->NetCommonsHtml->css(
	array(
		'/wysiwyg/css/adjust.css',
	)
);
