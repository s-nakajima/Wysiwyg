<?php
/**
 * File Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WysiwygFileController', 'Wysiwyg.Controller');
App::uses('AppModel', 'Model');

/**
 * Image Controller
 *
 * @author Satoru Majima <neo.otokomae@gmail.com>
 * @package NetCommons\Wysiwyg\Controller
 */
class WysiwygImageController extends WysiwygFileController {

/**
 * uploadFileモデル用の validation設定
 *
 * @var array
 */
	protected $_validate = [
		'real_file_name' => [
			'rule' => ['isValidMimeType', ['image/gif', 'image/png', 'image/jpg', 'image/jpeg']],
			'message' => 'File is not a image'
		]
	];

/**
 * Loads Model classes based on the uses property
 * see Controller::loadModel(); for more info.
 * Loads Components and prepares them for initialization.
 *
 * @return mixed true if models found and instance created.
 * @see Controller::loadModel()
 * @link http://book.cakephp.org/2.0/en/controllers.html#Controller::constructClasses
 * @throws MissingModelException
 */
	public function constructClasses() {
		//$this->components['NetCommons.AccessCtrl']['enabled'] = false;
		//$this->components['Auth']['enabled'] = false;
		//$this->components['Flash']['enabled'] = false;
		//$this->components['MobileDetect.MobileDetect']['enabled'] = false;
		//$this->components['NetCommons.Asset']['enabled'] = false;
		$this->components['NetCommons.Permission']['enabled'] = false;
		$this->components['NetCommons.NetCommons']['enabled'] = false;
		$this->components['NetCommons.NetCommonsTime']['enabled'] = false;
		$this->components['RequestHandler']['enabled'] = false;
		$this->components['Session']['enabled'] = false;
		$this->components['Workflow.Workflow']['enabled'] = false;
		$this->components['Security']['enabled'] = false;
		//$this->components['Files.FileUpload']['enabled'] = false;
		//$this->components['Files.Download']['enabled'] = false;
		$this->components['Wysiwyg.Wysiwyg']['enabled'] = false;

		return parent::constructClasses();
	}

/**
 * Called before the controller action. You can use this method to configure and customize components
 * or perform logic that needs to happen before each controller action.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		/* @var $Room AppModel */
		// シンプルにしたかったためAppModelを利用。インスタンス生成時少し速かった。
		$settings = [
			'table' => 'rooms',
			'alias' => 'Room',
		];
		$Room = new AppModel($settings);
		$params = [
			'belongsTo' => [
				'TrackableCreator',
				'TrackableUpdater',
			]
		];
		$Room->unbindModel($params);
		$Room->Behaviors->unload('Trackable');

		$params = [
			'hasOne' => [
				'Space' => [
					'className' => 'Space',
					'foreignKey' => false,
					'conditions' => [
						'Space.id = Room.space_id',
					],
					'fields' => ['type']
				],
				'RolesRoomsUser' => [
					'className' => 'RolesRoomsUser',
					'conditions' => [
						'RolesRoomsUser.user_id' => AuthComponent::user('id'),
					],
					'fields' => ['id']
				],
			],
		];
		$Room->bindModel($params);

		$query = [
			'conditions' => [
				'Room.id' => $this->request->params['pass'][0]
			],
			'recursive' => 0,
			'callbacks' => false,
		];
		$room = $Room->find('first', $query);
		if (!$room) {
			return;
		}

		App::uses('Space', 'Rooms.Model');
		if ($room['Space']['type'] === Space::PUBLIC_SPACE_ID ||
			isset($room['RolesRoomsUser']['id'])
		) {
			Current::setCurrent($room);
		}
	}

/**
 * download action
 *
 * @param Int $roomId Room id
 * @param Int $id File id
 * @param String $size 画像のサイズ(big, midiul, small, thumb の4つから)
 * @throws NotFoundException
 * @return void
 */
	public function download($roomId, $id, $size = '') {
		$options = [
			'field' => 'Wysiwyg.file',
		];

		// サイズ指定があるときにサイズ指定を行う。
		// 指定がなければオリジナルサイズ
		//
		if (!empty($size)) {
			// 指定したサイズが UploadFileモデル指定以外のサイズの時は 404 Not Found.
			if (array_key_exists($size, $this->_getThumbnailSizes()) === false) {
				throw new NotFoundException();
			}
			$options['size'] = $size;
		}

		return $this->Download->doDownloadByUploadFileId($id, $options);
	}

/**
 * ファイルモデルの画像サイズリストを取得する
 *
 * @return array
 */
	protected function _getThumbnailSizes() {
		$file = ClassRegistry::init('Files.UploadFile');
		$thumbnailSizes = $file->actsAs['Upload.Upload']['real_file_name']['thumbnailSizes'];
		$thumbnailSizes['biggest'] = '1200ml';
		return $thumbnailSizes;
	}
}
