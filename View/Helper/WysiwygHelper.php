<?php
/**
 * WysiwygFormHelper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * WysiwygHelper
 *
 * @package NetCommons\NetCommons\View\Helper
 */
class WysiwygHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
		'NetCommons.TitleIcon',
	);

/**
 * wysiwyg用のスクリプト呼び出し対応
 *
 * @return String wysiwyg js
 */
	public function wysiwygScript() {
		// NetCommonsApp.constant で定義する変数の定義
		$constants = [];

		// タイトルアイコン用のファイルリスト
		$constants['title_icon_paths'] = $this->__getTitleIconFiles();

		// 言語情報
		$constants['lang'] = Current::read('Language.code');

		// wysiwyg で利用するスタイルシート
		$constants['content_css'] = [
			$this->NetCommonsHtml->url('/net_commons/css/style.css'),
			$this->NetCommonsHtml->url('/wysiwyg/css/style.css'),
		];

		// ファイル／画像プラグインアップロード時に必要なデータの用意
		$constants['block_key'] = Current::read('Block.key');
		$constants['room_id'] = Current::read('Block.room_id');

		// constsnts 設定を JavaScriptで利用するための設定に変換する
		//
		$this->NetCommonsHtml->scriptStart(array('inline' => false));
		echo "NetCommonsApp.service('nc3Configs', function() {";
			foreach ($constants as $key => $value) {
				if (is_array($value)) {
					echo 'this.' . $key . ' = ' . json_encode($value) . ';';
				} else {
					echo "this." . $key . " = '" . $value . "';";
				}
			}
		echo "});";
		$this->NetCommonsHtml->scriptEnd();

		return $this->_View->element('Wysiwyg.wysiwyg_js');
	}

/**
 * TitleIconFilesを取得して加工する
 *
 * @return Array
 */
	private function __getTitleIconFiles() {
		$files = json_decode($this->TitleIcon->getIconFiles());
		return array_chunk($files, 8);
	}
}
