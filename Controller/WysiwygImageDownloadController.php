<?php
/**
 * WysiwygImageDownloadController
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');
App::uses('AppModel', 'Model');

/**
 * WysiwygImageDownloadController
 *
 */
class WysiwygImageDownloadController extends Controller {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommons',
		'Files.Download',
	);

/**
 * beforeFilter
 *
 * @return void
 * @codeCoverageIgnore
 */
	public function beforeFilter() {
		//テストの時間測定用
		$this->startTime = microtime(true);
	}

/**
 * Called after the controller action is run and rendered.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/ja/controllers.html#request-life-cycle-callbacks
 * @codeCoverageIgnore
 */
	public function afterFilter() {
		//テストの時間測定用
		$this->endTime = microtime(true);
	}

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		// WysiwygImageControllerDownloadTest::testDownloadGet 用の処理
		// @see https://github.com/NetCommons3/NetCommons/blob/3.1.2/Controller/NetCommonsAppController.php#L241
		// @see https://github.com/NetCommons3/NetCommons/blob/3.1.2/Controller/Component/NetCommonsComponent.php#L58
		App::uses('NetCommonsAppController', 'NetCommons.Controller');
		$this->NetCommons->renderJson();
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
		// シンプルにしたかったためAppModelを利用。インスタンス生成時少し速かった。
		/* @var $Room AppModel */
		$Room = ClassRegistry::init('Room');
		/* @var $Space AppModel */
		$Space = ClassRegistry::init('Space');
		/* @var $RolesRoomsUser AppModel */
		$RolesRoomsUser = ClassRegistry::init('RolesRoomsUser');
		/* @var $RoomRolePermissions AppModel */
		$RoomRolePerms = ClassRegistry::init('RoomRolePermissions');

		$query = [
			'fields' => [
				'Room.id',
				'Space.type',
				'RolesRoomsUser.id',
				'RoomRolePermissions.value',
			],
			'conditions' => ['Room.id' => $roomId],
			'recursive' => -1,
			'callbacks' => false,
			'joins' => [
				[
					'table' => $Space->table,
					'alias' => $Space->alias,
					'type' => 'INNER',
					'conditions' => [
						'Space.id = Room.space_id',
					],
				],
				[
					'table' => $RolesRoomsUser->table,
					'alias' => $RolesRoomsUser->alias,
					'type' => 'LEFT',
					'conditions' => [
						'RolesRoomsUser.room_id = Room.id',
						'RolesRoomsUser.user_id' => AuthComponent::user('id'),
					],
				],
				[
					'table' => $RoomRolePerms->table,
					'alias' => $RoomRolePerms->alias,
					'type' => 'LEFT',
					'conditions' => [
						'RoomRolePermissions.roles_room_id = RolesRoomsUser.roles_room_id',
						'RoomRolePermissions.permission' => 'block_editable',
					],
				],
			],
		];
		$room = $Room->find('first', $query);
		if (!$room) {
			return;
		}

		// 参加していれば、Current::setCurrent を呼び出し、参照権限チェックの判断データを作成しとく。
		// 不参加は、参照権限チェックの判断データは空なので、ForbiddenException
		// @see https://github.com/NetCommons3/Files/blob/3.1.2/Controller/Component/DownloadComponent.php#L109-L127
		App::uses('Space', 'Rooms.Model');
		if ($room['Space']['type'] === Space::PUBLIC_SPACE_ID ||
				isset($room['RolesRoomsUser']['id'])) {
			// RoomRolePermissions データ は、keyが違う
			// @see https://github.com/NetCommons3/NetCommons/blob/3.1.2/Utility/CurrentFrame.php#L317
			// @see https://github.com/NetCommons3/NetCommons/blob/3.1.2/Utility/CurrentBase.php#L332-L338
			$room['Permission']['block_editable'] = $room['RoomRolePermissions'];
			unset($room['RoomRolePermissions']);

			Current::setCurrent($room);
			Current::writePermission(
				$room['Room']['id'],
				'block_editable',
				$room['Permission']['block_editable']['value']
			);
		}

		App::uses('Room', 'Rooms.Model');
		ClassRegistry::removeObject('Room');
		ClassRegistry::removeObject('Space');
		ClassRegistry::removeObject('RolesRoomsUser');
		ClassRegistry::removeObject('RoomRolePermissions');

		$options = ['field' => 'Wysiwyg.file'];

		// サイズ指定があるときにサイズ指定を行う。
		// 指定がなければオリジナルサイズ
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
