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
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'NetCommons.TitleIcon',
	);

/**
 * WYSIWYGの初期処理
 *
 * @param string $fieldName フィールド名（"Modelname.fieldname"形式）
 * @param array $attributes HTML属性のオプション配列
 * @return string WYSIWYGのHTML
 */
	public function wysiwyg($fieldName, $attributes = array()) {
		$ngModel = Hash::expand(array($fieldName => 0));
		$ngModel = NetCommonsAppController::camelizeKeyRecursive($ngModel);
		$ngModel = Hash::flatten($ngModel);
		$ngModel = array_flip($ngModel);

		$defaultAttributes = array(
			'type' => 'textarea',
			'ui-tinymce' => 'tinymce.options',
			'ng-model' => $ngModel[0],
			'rows' => 5,
		);
		$attributes = Hash::merge($defaultAttributes, $attributes);

		// wysiwygに関連する js読み込みを Wysiwygプラグインから行う
		$html = '';
		$html .= $this->wysiwygScript();
		$html .= $this->NetCommonsForm->input($fieldName, $attributes);

		return $html;
	}

/**
 * wysiwyg用のスクリプト呼び出し対応
 *
 * @return String wysiwyg js
 */
	public function wysiwygScript() {
		// NetCommonsApp.constant で定義する変数の定義
		$constants = [
			// タイトルアイコン用のファイルリスト
			'title_icon_paths' => $this->__getTitleIconFiles(),

			// 言語情報
			'lang' => Current::read('Language.code'),
			'lang_js' => $this->NetCommonsHtml->url('/wysiwyg/js/langs/' . Current::read('Language.code') . '.js'),

			// wysiwyg で利用するスタイルシート
			'content_css' => [
				$this->NetCommonsHtml->url('/net_commons/css/style.css'),
				$this->NetCommonsHtml->url('/components/bootstrap/dist/css/bootstrap.css'),
				$this->NetCommonsHtml->url('/wysiwyg/css/style.css'),
			],

			// ファイル／画像プラグインアップロード時に必要なデータの用意
			'blockKey' => Current::read('Block.key'),
			'roomId' => Current::read('Room.id'),

			// 独自ツールバーアイコン
			'book_icon' => $this->NetCommonsHtml->url('/wysiwyg/img/title_icons/book.svg'),
			'fileup_icon' => $this->NetCommonsHtml->url('/wysiwyg/img/title_icons/fileup.svg'),
			'tex_icon' => $this->NetCommonsHtml->url('/wysiwyg/img/title_icons/tex.svg'),

			// MathJax JSのリンク
			'mathjax_js' => $this->NetCommonsHtml->url('/components/MathJax/MathJax.js?config=TeX-MML-AM_CHTML'),

			// ファイル・画像アップロードパス
			'file_upload_path' => $this->NetCommonsHtml->url('/wysiwyg/file/upload'),
			'image_upload_path' => $this->NetCommonsHtml->url('/wysiwyg/image/upload'),
		];

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
		$files = json_decode($this->TitleIcon->getIconFiles(false));
		return array_chunk($files, 8);
	}
}
