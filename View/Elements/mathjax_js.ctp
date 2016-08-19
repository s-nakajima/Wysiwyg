<?php
/**
 * Element of MathJax include
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

// skipStartupTypeset: true によって MathJaxの自動読み込みを無効にする
echo $this->Html->scriptStart(array('inline' => false, 'type' => 'text/x-mathjax-config'));
?>
MathJax.Hub.Config({
	skipStartupTypeset: true,
	tex2jax: {
		inlineMath: [['$$','$$'], ['\\\\(','\\\\)']],
		displayMath: [['\\\\[','\\\\]']]
	},
	asciimath2jax: {
		delimiters: [['$$','$$']]
	}
});
<?php
echo $this->Html->scriptEnd();

// wysiwyg呼び出し
echo $this->NetCommonsHtml->script(
	array(
		'/components/MathJax/MathJax.js?config=TeX-MML-AM_CHTML',
	)
);
?>

<?php
// nc-system-{header | main | footer} 要素に対して
// MathJax の実行を行う
//
echo $this->Html->scriptStart(array('inline' => false));
?>
$(document).ready(function(){
	MathJax.Hub.Queue(['Typeset', MathJax.Hub, 'nc-container']);
});
<?php echo $this->Html->scriptEnd();
