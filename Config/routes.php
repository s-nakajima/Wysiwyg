<?php
/**
 * routes configuration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

Router::connect(
	'/wysiwyg/image/download/*',
	array('plugin' => 'wysiwyg', 'controller' => 'wysiwyg_image_download', 'action' => 'download')
);

Router::connect(
	'/wysiwyg/file/:action/*',
	array('plugin' => 'wysiwyg', 'controller' => 'wysiwyg_file')
);

Router::connect(
	'/wysiwyg/image/:action/*',
	array('plugin' => 'wysiwyg', 'controller' => 'wysiwyg_image')
);

Router::connect(
	'/wysiwyg/:controller/:action/*',
	array('plugin' => 'wysiwyg')
);
