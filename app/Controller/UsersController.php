<?php
// remove group, add picture to the post ,specify the manager and the user roles, add link to go back to home screen
class UsersController extends AppController
{
	public $helpers = array('Form', 'Html');


	public function login()
	{
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$data = json_decode(file_get_contents('php://input'), true);
			$check = $this->User->find('all', [
				'recursive' => -1,
				'fields' => ['username', 'password', 'id'],
				'conditions' => ['username' => $data['username']]
			]);
			if (
				$data['username'] === $check[0]['User']['username']
				&& $data['password'] === $check[0]['User']['password']
			) {
				$this->Session->write('User.id', $check[0]['User']['id']);
				$check = 1;
			}
			echo json_encode($check);
		}
	}
	public function register()
	{
		$this->loadModel('Group');
		$this->loadModel('Role');
		$this->loadModel('UserRole');
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$data = json_decode(file_get_contents('php://input'), true);

			$temp = [
				'username' => $data['username'],
				'email' => $data['email'],
				'password' => $data['password'],
				'role_id' => $data['role_id'],
				'group_id' => 11,
				'first_name' => $data['first_name'],
				'family_name' => $data['family_name'],
			];
			$check = $this->User->find('first', [
				'recursive' => -1,
				'fields' => ['username'],
				'conditions' => ['username' => $temp['username']]
			]);
			if (empty($check)) {
				$check = 1;
				if ($this->User->save($temp)) {
					$id = $this->User->find('first', ['recursive' => -1, 'fields' => ['id'], 'conditions' => ['email' => $data['email']]]);
					foreach ($data['groups'] as $lo):
						$temp2 = ['user_id' => $id['User']['id'], 'group_id' => $lo, 'role_id' => $data['role_id']];
						$this->UserRole->create();
						$this->UserRole->save($temp2);
					endforeach;
				}
			}
			echo json_encode($check);
		} else {
			$group_options = $this->Group->find('list', [
				'recursive' => -1,
				'fields' => ['Group.id', 'Group.name']
			]);
			$this->set('group_options', json_encode($group_options));
		}
	}

	public function editUser($id = null)
	{
		if (empty($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if (empty($this->request->data)) {
			$user_info = $this->User->findById($id);
			$this->request->data = $user_info;
			unset($this->request->data['User']['password']);
		} else {
			if (!empty($this->request->data['User']['new_password']))
				$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('The user has been save'));
				return $this->redirect(['action' => 'index']);
			}
			unset($this->request->data['User']['new_password']);
		}
	}
	public function profile()
	{
		if ($this->request->is('POST')) {
			$this->autoRender = false;
			$this->loadModel('Post');
			$this->loadModel('Group');
			$this->loadModel('UserRole');
			$this->loadModel('UserList');
			$data = json_decode(file_get_contents('php://input'), true);
			$id = $data['id'];
			$approved_count = $this->Post->find('first', [
				'recursive' => -1,
				'fields' => ['count(id)'],
				'conditions' => ['user_id' => $id, 'approved' => 1],
				'group' => 'approved'
			]);
			$number_of_posts = $approved_count[0]['count(id)'];
			$approved_count1 = $this->Post->find('list', [
				'recursive' => -1,
				'fields' => ['likes'],
				'conditions' => ['user_id' => $id, 'approved' => 1]
			]);
			$sum = 0;
			foreach ($approved_count1 as $key => $val) {
				$sum += $val;
			}
			if ($sum != 0)
				$like_ratio = $number_of_posts / $sum;
			else
				$like_ratio = 0;
			$group_options = $this->Group->find('all', [
				'recursive' => -1,
				'fields' => ['id', 'name']
			]);
			$groups_id = $this->UserRole->find(
				'list',
				[
					'recursive' => -1,
					'fields' => ['group_id'],
					'conditions' => ['user_id' => $id]
				]
			);
			$final = [];
			//pr($group_options);die;
			foreach ($group_options as $a) {
				if (!empty(array_search($a['Group']['id'], $groups_id))) {
					$final[$a['Group']['id']] = $a['Group']['name'];
				}
			}
			$username = $this->User->find('first', [
				'recursive' => -1,
				'conditions' => ['id' => $id]
			]);
			$list_options = $this->UserList->find('all', [
				'recursive' => -1,
				'fields' => ['skill_name'],
				'conditions' => ['user_id' => $id]
			]);
			$arr = [];
			array_push($arr, $list_options);
			array_push($arr, $sum);
			array_push($arr, $final);
			array_push($arr, $username);
			array_push($arr, $number_of_posts);
			array_push($arr, $like_ratio);

			echo json_encode($arr);
		}

	}
	public function addpic()
	{
		$this->autoRender = false;
		$file = $_FILES['file'];
		$lastSavedID = $this->User->find('first', [
			'recursive' => -1,
			'fields' => ['id'],
			'order' => 'id DESC'
		]);
		if (empty($lastSavedID))
			$lastSavedID['User']['id'] = 0;
		$lastSavedID['User']['id']++;
		$k = 'user' . $lastSavedID['User']['id'] . '.jpg';
		$file['name'] = $k;
		$allowedTypes = array('jpg');
		$allowedSize = 1024 * 1024; // 1 MB
		if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedTypes)) {
			$this->Flash->error(__('Invalid file type.'));
		} else {
			$temp = array(
				'User' => array(
					'id' => $this->Session->read('User.id'),
					'pic_path' => $file['name'],
				)
			);
		}
		move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/' . $file['name']);
		$this->User->create();
		$this->User->save($temp);
	}
	public function addskill()
	{
		$this->autoRender = false;
		$this->loadModel('UserList');
		$data = json_decode(file_get_contents('php://input'), true);
		$id = $this->Session->read('User.id');
		$skill = $data['content'];
		$t = [
			'user_id' => $id,
			'skill_name' => $skill
		];
		$this->UserList->create();
		$this->UserList->save($t);
	}
	public function addmajor()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$major = $data['content'];
		$temp = array(
			'User' => array(
				'id' => $this->Session->read('User.id'),
				'major' => $major
			)
		);
		$this->User->create();
		$this->User->save($temp);
	}
}