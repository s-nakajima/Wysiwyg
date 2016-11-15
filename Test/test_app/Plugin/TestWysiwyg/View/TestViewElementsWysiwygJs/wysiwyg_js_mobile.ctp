<?php
/**
 * View/Elements/wysiwyg_jsテスト用Viewファイル
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

?>

View/Elements/wysiwyg_mobile

<?php echo $this->element('Wysiwyg.wysiwyg_js');
echo $this->fetch('css');
echo $this->fetch('script');