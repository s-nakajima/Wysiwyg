<?php
/**
 * WysiwygBehavior Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryohei Ohga <ohga.ryohei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');

/**
 * WysiwygBehavior Test Case
 * HTML5のタグが除去されないことのテスト
 */
class Html5TagTest extends NetCommonsCakeTestCase {

/**
 * Model name
 *
 * @var string
 */
	private $__modelName = 'FakeModel';

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.wysiwyg.fake_model',
	);

/**
 * assert
 *
 * @param string $content
 */
	private function __assert($content) {
		Current::write('Room.id', '2');
		Current::writePermission('2', 'html_not_limited', true);

		NetCommonsCakeTestCase::loadTestPlugin($this, 'Wysiwyg', 'TestWysiwyg');
		$FakeModel = ClassRegistry::init('TestWysiwyg.FakeModel');
		$FakeModel->create();

		$data = array();
		$data[$this->__modelName]['content'] = $content;

		$result = $FakeModel->save($data);
		$find = $FakeModel->findById($result[$this->__modelName]['id']);
		$this->assertEquals($content, $find[$this->__modelName]['content']);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * section
 */
	public function testSaveHtml5TagNotRemove1() {
		$content = '<section><h1>The facts</h1>' .
			'<p>1500+ shows, 14+ countries</p>' .
			'</section>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * article,header,footer
 */
	public function testSaveHtml5TagNotRemove2() {
		$content = '<article><header><h1>Hard Trance is My Life</h1>' .
			'<p>By DJ Steve Hill and Technikal</p>' .
			'</header></article><article><p>Happy 2nd birthday Masif Saturdays!!!</p>' .
			'<footer>Posted 3 weeks ago</footer></article>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * nav,mark
 */
	public function testSaveHtml5TagNotRemove3() {
		$content = '<nav><p><a href="/">Home</a></p></nav>' .
			'Elderflower cordial, with one <mark>part</mark>' .
			'cordial to ten <mark>part</mark>s water.';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * hgroup
 */
	public function testSaveHtml5TagNotRemove4() {
		$content = '<hgroup><h1>Burning Music</h1>' .
			'<h2>The Guide To Music On The Playa</h2>' .
			'</hgroup>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * address
 */
	public function testSaveHtml5TagNotRemove5() {
		$content = '<address> For more details, contact' .
			'<a href="mailto:js@example.com">John Smith</a>.' .
			'</address>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * aside,ins,del
 */
	public function testSaveHtml5TagNotRemove6() {
		$content = '<aside><ins><p> I like fruit. </p></ins>' .
			'</aside><ul><li>Empty the dishwasher</li>' .
			'<li><del>Watch Walter Lewin\'s lectures</del></li><li>Buy a printer</li></ul>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * video,source
 */
	public function testSaveHtml5TagNotRemove7() {
		$content = '<video><source src="sample.ogv"' .
			' type="video/ogg; codecs=\'theora, vorbis\'"><p>sample.ogv</p>' .
			'</source></video>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * figure,figcaption
 */
	public function testSaveHtml5TagNotRemove8() {
		$content = '<figure><figcaption>Carl Sagan, in "<cite>Wonder and Skepticism</cite>"' .
			'</figcaption></figure>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * embed,noembed
 */
	public function testSaveHtml5TagNotRemove9() {
		$content = '<embed src="catgame.swf" allowscriptaccess="never"' .
			' allownetworking="internal" type="application/x-shockwave-flash">' .
			'<noembed>再生するにはプラグインが必要です。</noembed></embed>';
		$this->__assert($content);
	}

/**
 * 以下のHTMLタグが除去されないこと（html_not_limited=true）
 *
 * ruby,rt,rp
 */
	public function testSaveHtml5TagNotRemove10() {
		$content = '<ruby>漢<rp>(</rp><rt>かん</rt><rp>)</rp>字<rp>(</rp><rt>じ</rt><rp>)</rp></ruby>';
		$this->__assert($content);
	}
}