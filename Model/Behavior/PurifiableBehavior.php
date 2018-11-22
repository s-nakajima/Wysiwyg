<?php
/**
 * Purifiable Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Current', 'NetCommons.Utility');
App::uses('UserRole', 'UserRoles.Model');
App::uses('HTMLPurifier_Filter_Comment', 'Wysiwyg.Utility/Filter');

/**
 * Purifiable Utility
 *
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @package NetCommons\Wysiwyg\Utility
 */
class PurifiableBehavior extends ModelBehavior {

/**
 * Contains configuration settings for use with individual model objects.
 * Individual model settings should be stored as an associative array,
 * keyed off of the model name.
 *
 * @var array
 * @access public
 * @see Model::$alias
 */
	private $__settings = array(
		'fields' => array(),
		'overwrite' => false,
		'affix' => '_clean',
		'affix_position' => 'suffix',
		'config' => array(
			'HTML' => array(
				'DefinitionID' => 'purifiable',
				'DefinitionRev' => 1,
				'TidyLevel' => 'heavy',
				'Doctype' => 'XHTML 1.0 Transitional'
			),
			'Core' => array(
				'Encoding' => 'UTF-8'
			),
		),
		'customFilters' => array(
		)
	);

/**
 * 共通設定
 *
 * @var array
 */
	private $__commonConfig = array();

/**
 * HTMLタグ使用権限がある場合の設定
 *
 * @var array
 */
	private $__htmlNotLimitedConfig = array();

/**
 * HTMLタグ使用権限がない場合の設定
 *
 * @var array
 */
	private $__htmlLimitedConfig = array();

/**
 * キャッシュファイルのパス
 *
 * @var string
 */
	protected $__cachePath = '';

/**
 * コンストラクタ
 */
	public function __construct() {
		$this->__cachePath = CACHE . 'HTMLPurifier' . DS;
		if (! file_exists($this->__cachePath)) {
			mkdir($this->__cachePath);
		}
	}

/**
 * SetUp Attachment behavior
 *
 * @param Model $model instance of model
 * @param array $config array of configuration settings.
 * @throws CakeException 先にOriginalKeyが登録されてないと例外
 * @return void
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function setup(Model $model, $config = array()) {
		$this->__commonConfig = array(
			'Attr' => array(
				'AllowedFrameTargets' => array(
					'_blank',
					'_self',
					'_parent',
					'_top',
				),
				'AllowedRel' => array(
					'alternate',
					'author',
					'bookmark',
					'help',
					'icon',
					'license',
					'next',
					'nofollow',
					'noreferrer',
					'prefetch',
					'prev',
					'search',
					'stylesheet',
					'tag',
				),
				'EnableID' => true,
			),
			'Cache' => array(
				'SerializerPath' => $this->__cachePath,
			),
			'CSS' => array(
				'AllowDuplicates' => true,
				'AllowImportant' => true,
				'AllowTricky' => true,
				'DefinitionRev' => 1,
				'Proprietary' => true,
				'Trusted' => true,
			),
			'Core' => array(
				'AllowHostnameUnderscore' => true,
				'ConvertDocumentToFragment' => false,
				'DisableExcludes' => true,
				'Encoding' => 'UTF-8',
				'MaintainLineNumbers' => false,
			),
			'HTML' => array(
				'Doctype' => 'XHTML 1.0 Transitional',
				'SafeIframe' => true,
				'FlashAllowFullScreen' => true,
				'TargetNoreferrer' => false,
				'Trusted' => true,
			),
			'URI' => array(
				'SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/)%',
			),
			'Output' => array(
				'FlashCompat' => true,
			),
		);
		$this->__htmlNotLimitedConfig = array(
			'Core' => array(
				'HiddenElements' => array(),
			),
			'HTML' => array(
				'Proprietary' => true,
				'SafeEmbed' => true,
			),
			'URI' => array(
				'SafeIframeRegexp' => '%^(https?:)?%',
			),
		);
		$this->__htmlLimitedConfig = array(
			'CSS' => array(
				'AllowedProperties' => array(
					'color' => true,
					'background-color' => true,
					'margin' => true,
					'text-align' => true,
					'margin-left' => true,
					'margin-right' => true,
					'margin-top' => true,
					'margin-bottom' => true,
					'padding' => true,
					'padding-left' => true,
					'padding-right' => true,
					'padding-top' => true,
					'padding-bottom' => true,
					'border' => true,
					'border-left' => true,
					'border-right' => true,
					'border-top' => true,
					'border-bottom' => true,
					'border-width' => true,
					'border-left-width' => true,
					'border-right-width' => true,
					'border-top-width' => true,
					'border-bottom-width' => true,
					'border-style' => true,
					'border-left-style' => true,
					'border-right-style' => true,
					'border-top-style' => true,
					'border-bottom-style' => true,
					'border-color' => true,
					'border-left-color' => true,
					'border-right-color' => true,
					'border-top-color' => true,
					'border-bottom-color' => true,
					'display' => true,
					'float' => true,
					'clear' => true,
					'width' => true,
					'height' => true,
					'vertical-align' => true,
					'overflow' => true,
					'visibility' => true,
					'background' => true,
					'background-image' => true,
					'background-repeat' => true,
					'background-attachment' => true,
					'background-position' => true,
					'font' => true,
					'font-style' => true,
					'font-variant' => true,
					'font-weight' => true,
					'font-size' => true,
					'line-height' => true,
					'font-family' => true,
					'text-indent' => true,
					'text-decoration' => true,
					'letter-spacing' => true,
					'text-transform' => true,
					'white-space' => true,
					'table-layout' => true,
					'border-spacing' => true,
					'border-collapse' => true,
				),
			),
			'HTML' => array(
				'Allowed' =>
					'div,' .
					'span,' .
					'h1[align],' .
					'h2[align],' .
					'h3[align],' .
					'h4[align],' .
					'h5[align],' .
					'h6[align],' .
					'br[clear],' .
					'img[src|vspace|hspace|border|alt|height|width],' .
					'ol[compact|start|type],' .
					'ul[compact|type],' .
					'li[type|value],' .
					'a[href|target],' .
					'hr[align|color|noshade|size|width],' .
					'table[cellspacing|cellpadding|border|align],' .
					'tbody[align|bgcolor|char|charoff|valign],' .
					'tr[colspan|rowspan],' .
					'td[colspan|rowspan|bgcolor|align|valign|height|width|nowrap|char|charoff' .
						'|abbr|axis|headers|scope],' .
					'blockquote[cite],' .
					'p[align],' .
					'th[colspan|rowspan|bgcolor|align|valign|height|width|nowrap|char|charoff' .
						'|abbr|axis|headers|scope],' .
					'strong,' .
					'caption[align|valign],' .
					'cite,' .
					'code,' .
					'kbd,' .
					'pre[cols|width|wrap],' .
					'q,' .
					'small,' .
					'sub,' .
					'sup,' .
					'object[archive|border|classid|code|codebase|codetype|data|declare|name' .
						'|standby|tabindex|type|usemap|align|width|height|hspace|vspace],' .
					'param[name|value],' .
					'em,' .
					'i,' .
					'iframe[src|height|width|hspace|vspace|marginheight|marginwidth' .
						'|allowtransparency|frameborder|border|bordercolor|allowfullscreen],' .
					'col[span],' .
					'colgroup[span],' .
					'dl[compact],' .
					'dt,' .
					'dd,' .
					// HTML5から採用--ここから
					'rb,' .
					'ruby,' .
					'rp,' .
					'rt,' .
					'wbr,' .
					'embed[src|height|width|hspace|vspace|units|border|frameborder|play|loop' .
						'|quality|pluginspage|type|allowscriptaccess|allowfullscreen|flashvars],' .
					// HTML5から採用--ここまで
					// HTML5で廃止--ここから
					'font[size|color|face],' .
					'big,' .
					'center,' .
					'tt,' .
					'u,' .
					's,' .
					'strike,' .
					'noembed,' .
					// HTML5で廃止--ここまで
					// 全要素共通
					'*[class|id|title|cite|background|style|align|dir|lang|language]',
			),
			'URI' => array(
				'AllowedSchemes' => array(
					'http' => true,
					'https' => true,
					'mailto' => true,
					'ftp' => true,
				),
			),
		);

		if (Current::permission('html_not_limited')) {
			// HTMLタグ使用権限がある場合
			$purifyConfig = $this->__htmlNotLimitedConfig;
			$customFilters = array();
		} else {
			$purifyConfig = $this->__htmlLimitedConfig;
			$customFilters = array('HTMLPurifier_Filter_Comment');
		}
		$this->__settings = Hash::merge(
			$this->__settings, array(
				'fields' => $config['fields'],
				'config' => Hash::merge($this->__commonConfig, $purifyConfig),
				'customFilters' => $customFilters,
			)
		);
	}

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$this->purify($model, $this->__settings['fields']);
		return true;
	}

/**
 * 設定の取得
 *
 * @param Model $model Model instance
 * @param array $fields column that will be sanitized
 * @return array setting
 */
	public function purify(Model $model, $fields) {
		foreach ($fields[$model->alias] as $fieldName) {
			if (empty($model->data[$model->alias][$fieldName])) {
				continue;
			}

			$model->data[$model->alias][$fieldName]
				= $this->clean($model, $model->data[$model->alias][$fieldName]);
		}
	}

/**
 * Sanitizes content
 *
 * @param Model $model Model instance
 * @param string $fieldValue value that will be sanitized
 * @return bool
 */
	public function clean(Model $model, $fieldValue) {
		// ルームでスプリクトを許可されてる権限だったら、PurifiableBehaviorのチェックは通さない
		// W3Cに準拠してないタグの拡張属性が消えるため。
		//
		// 例）
		// ・bootstrapのbuttonタグの拡張属性(data-toggle data-target).
		// ・bootstrapのimgタグの拡張属性(data-size data-position data-imgid). <- Wysiwygで画像追加時 にセットされる <img class="" title="" src="" alt="" data-size="big" data-position="" data-imgid="9" />
		// ・Chromeのvideoタグの拡張属性(controlsList). <- ダウンロードボタン非表示の時につかう。<video controlsList="nodownload">
		if (!Current::permission('html_not_limited')) {
			return $fieldValue;
		}

		//the next few lines allow the config __settings to be cached
		$config = HTMLPurifier_Config::createDefault();
		foreach ($this->__settings['config'] as $namespace => $values) {
			foreach ($values as $key => $value) {
				$config->set("{$namespace}.{$key}", $value);
			}
		}

		if ($this->__settings['customFilters']) {
			$filters = array();
			foreach ($this->__settings['customFilters'] as $customFilter) {
				$filters[] = new $customFilter;
			}
			$config->set('Filter.Custom', $filters);
		}

		$this->__addHtmlDef($config);
		$this->__addCssDef($config);

		$cleaner = new HTMLPurifier($config);
		return $cleaner->purify($fieldValue);
	}

/**
 * HTML定義を追加
 *
 * @param HTMLPurifier_Config $config HTMLPurifier_Config instance
 * @return void
 */
	private function __addHtmlDef(HTMLPurifier_Config $config) {
		if ($def = $config->getHTMLDefinition(true, true)) {

			// http://developers.whatwg.org/sections.html
			$def->addElement('article', 'Block', 'Flow', 'Common');
			$def->addElement('section', 'Block', 'Flow', 'Common');
			$def->addElement('nav', 'Block', 'Flow', 'Common');
			$def->addElement('aside', 'Block', 'Flow', 'Common');
			$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
			$def->addElement('header', 'Block', 'Flow', 'Common');
			$def->addElement('footer', 'Block', 'Flow', 'Common');

			// http://developers.whatwg.org/grouping-content.html
			$def->addElement('figure', 'Block',
				'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
			$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

			// http://developers.whatwg.org/the-video-element.html#the-video-element
			$def->addElement('video', 'Block',
				'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
					'src' => 'URI',
					'type' => 'Text',
					'width' => 'Length',
					'height' => 'Length',
					'poster' => 'URI',
					'preload' => 'Enum#auto,metadata,none',
					'controls' => 'Bool',
				)
			);
			$def->addElement('source', 'Block', 'Flow', 'Common', array(
				'src' => 'URI',
				'type' => 'Text',
			));

			// http://developers.whatwg.org/text-level-semantics.html
			$def->addElement('s', 'Inline', 'Inline', 'Common');
			$def->addElement('mark', 'Inline', 'Inline', 'Common');
			$def->addElement('wbr', 'Inline', 'Empty', 'Core');
			$def->addElement('ruby', 'Block', 'Flow', 'Common');
			$def->addElement('rt', 'Block', 'Flow', 'Common');
			$def->addElement('rp', 'Block', 'Flow', 'Common');

			// NetCommonsで許可するタグ、属性を追加
			$def->addElement('embed', 'Block', 'Flow', 'Common');
			$def->addElement('noembed', 'Block', 'Flow', 'Common');

			$def->addAttribute('hr', 'color', 'Text');
			$def->addAttribute('tbody', 'bgcolor', 'Text');
			$def->addAttribute('tbody', 'char', 'Text');
			$def->addAttribute('tr', 'colspan', 'Text');
			$def->addAttribute('tr', 'rowspan', 'Text');
			$def->addAttribute('td', 'char', 'Text');
			$def->addAttribute('td', 'axis', 'Text');
			$def->addAttribute('td', 'headers', 'Text');
			$def->addAttribute('th', 'char', 'Text');
			$def->addAttribute('th', 'axis', 'Text');
			$def->addAttribute('th', 'headers', 'Text');
			$def->addAttribute('caption', 'valign', 'Text');
			$def->addAttribute('pre', 'cols', 'Text');
			$def->addAttribute('pre', 'wrap', 'Text');
			$def->addAttribute('object', 'border', 'Text');
			$def->addAttribute('object', 'code', 'Text');
			$def->addAttribute('object', 'usemap', 'Text');
			$def->addAttribute('object', 'align', 'Text');
			$def->addAttribute('object', 'hspace', 'Text');
			$def->addAttribute('object', 'vspace', 'Text');
			$def->addAttribute('iframe', 'hspace', 'Text');
			$def->addAttribute('iframe', 'vspace', 'Text');
			$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
			$def->addAttribute('iframe', 'allowtransparency', 'Bool');
			$def->addAttribute('iframe', 'border', 'Text');
			$def->addAttribute('iframe', 'bordercolor', 'Text');
			$embedAttributes = array(
				'src', 'height', 'width', 'hspace', 'vspace', 'units', 'border', 'frameborder', 'play',
				'loop', 'quality', 'pluginspage', 'type', 'allowscriptaccess', 'allowfullscreen', 'flashvars',
			);
			foreach ($embedAttributes as $attribute) {
				$def->addAttribute('embed', $attribute, 'Text');
			}

			// 全要素で使用する属性を設定
			$def->addElement('*', 'Block', 'Flow', 'Common');
			$def->info_global_attr = array(
				'class' => true, 'id' => true, 'title' => true, 'cite' => true, 'background' => true,
				'style' => true, 'align' => true, 'dir' => true, 'lang' => true, 'language' => true,
			);

			if ($def->manager) {
				$def->manager->addModule('Ruby');
			}
		}
	}

/**
 * CSS定義を追加
 *
 * @param HTMLPurifier_Config $config HTMLPurifier_Config instance
 * @return void
 */
	private function __addCssDef(HTMLPurifier_Config $config) {
		if ($def = $config->getCSSDefinition()) {
			$def->info['position'] = new HTMLPurifier_AttrDef_Enum(
				array('absolute', 'fixed', 'relative', 'static')
			);
			$def->info['top'] =
			$def->info['bottom'] =
			$def->info['left'] =
			$def->info['right'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_CSS_Length(),
					new HTMLPurifier_AttrDef_CSS_Percentage(),
					new HTMLPurifier_AttrDef_Enum(array('auto'))
				)
			);
			$def->info['z-index'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_CSS_Number(),
					new HTMLPurifier_AttrDef_Enum(array('auto')),
				)
			);
			$def->info['direction'] = new HTMLPurifier_AttrDef_Enum(
				array('ltr', 'rtl')
			);
			$def->info['unicode-bidi'] = new HTMLPurifier_AttrDef_Enum(
				array('normal', 'embed', 'bidi-override')
			);
			$def->info['width'] =
			$def->info['height'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_CSS_Length(),
					new HTMLPurifier_AttrDef_CSS_Percentage(),
					new HTMLPurifier_AttrDef_Enum(array('auto')),
				)
			);
			$def->info['min-width'] =
			$def->info['min-height'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_CSS_Length(),
					new HTMLPurifier_AttrDef_CSS_Percentage(),
				)
			);
			$def->info['max-width'] =
			$def->info['max-height'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_CSS_Length(),
					new HTMLPurifier_AttrDef_CSS_Percentage(),
					new HTMLPurifier_AttrDef_Enum(array('none')),
				)
			);
			$def->info['text-justify'] = new HTMLPurifier_AttrDef_Enum(
				array('auto', 'distribute', 'distribute-all-lines', 'inter-cluster',
					'inter-ideograph', 'inter-word', 'kashida', 'newspaper')
			);
			$def->info['text-underline-position'] = new HTMLPurifier_AttrDef_Enum(
				array('above', 'below')
			);
			$def->info['empty-cells'] = new HTMLPurifier_AttrDef_Enum(
				array('show', 'hide')
			);
			$def->info['cursor'] = new HTMLPurifier_AttrDef_CSS_Composite(
				array(
					new HTMLPurifier_AttrDef_Enum(array('auto', 'default', 'pointer', 'crosshair',
						'move', 'text', 'wait', 'help', 'n-resize', 's-resize', 'w-resize',
						'e-resize', 'ne-resize', 'nw-resize', 'se-resize', 'sw-resize',
						'progress', 'hand', 'no-drop', 'all-scroll', 'col-resize', 'row-resize',
						'not-allowed', 'vertical-text')),
					new HTMLPurifier_AttrDef_CSS_URI(),
				)
			);
		}
	}
}
