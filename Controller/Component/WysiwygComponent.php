<?php
/**
 * Wysiwyg Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * Wysiwyg Component
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Wysiwyg\Controller\Component
 */
class WysiwygComponent extends Component {

/**
 * requestの中でファイルのアップロードエラーがあるかどうかを調べる
 * ※Unitテストで使用するため、別ファイルに出す。
 *
 * @param Array $params 調べるリクエストデータ
 * @return bool
 * @codeCoverageIgnore
 */
	public function isUploadedFile($params) {
		$val = array_shift($params);
		if (isset($val['error']) && $val['error'] == 0 ||
				!empty( $val['tmp_name']) && $val['tmp_name'] != 'none') {
			return is_uploaded_file($val['tmp_name']);
		}
		return false;
	}

}
