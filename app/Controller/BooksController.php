<?php

class BooksController extends AppController
{
	public function index()
	{
		$approved_count = $this->Book->find('first', [
			'recursive' => -1,
			'fields' => ['count(id)'],
			'conditions' => ['status' => 1],
			'group' => 'status'
		]);
		if (empty($approved_count[0]['count(id)']))
			$approved_count[0]['count(id)'] = 0;
		$approved_count2 = $this->Book->find('first', [
			'recursive' => -1,
			'fields' => ['count(id)'],
			'conditions' => ['status' => 3],
			'group' => 'status'
		]);
		if (empty($approved_count2[0]['count(id)']))
			$approved_count2[0]['count(id)'] = 0;

		$sum = $approved_count[0]['count(id)'] + $approved_count2[0]['count(id)'];
		$this->loadModel('User');
		$currentRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['role_id'],
			'conditions' => ['id' => $this->Session->read('User.id')]
		]);
		$pending = $this->Book->find('all', [
			'recursive' => -1,
			'conditions' => ['status' => 1]
		]);
		$requested = $this->Book->find('all', [
			'recursive' => -1,
			'conditions' => ['status' => 3]
		]);
		$this->set('pendingPosts', json_encode($pending));
		$this->set('numberOfRequests', json_encode($sum));
		$this->set('userRole', json_encode($currentRole));
		$this->set('requested', json_encode($requested));
	}

	public function requestedbooks()
	{
		$this->autoRender = false;
		$data = $this->Book->find('all', [
			'recursive' => -1,
			'conditions' => ['status' => 3]
		]);
		echo json_encode($data);
	}
	public function pendingbooks()
	{
		$this->autoRender = false;
		$data = $this->Book->find('all', [
			'recursive' => -1,
			'conditions' => ['status' => 1]
		]);
		echo json_encode($data);
	}
	public function add()
	{
		$this->autoRender = false;
		$this->loadModel('User');
		$currentRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['username'],
			'conditions' => ['id' => $this->Session->read('User.id')]
		]);
		$data = $this->request->data;
		$file = $_FILES['file'];
		$lastSavedID = $this->Book->find('first', [
			'recursive' => -1,
			'fields' => ['id'],
			'order' => 'id DESC'
		]);

		if (empty($lastSavedID['Book']['id']))
			$lastSavedID['Book']['id'] = 0;
		$lastSavedID['Book']['id'] = $lastSavedID['Book']['id'] + 1;
		$k = $lastSavedID['Book']['id'] . '.jpg';
		$file['name'] = $k;
		$allowedTypes = array('jpg');
		$allowedSize = 1024 * 1024; // 1 MB
		if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedTypes)) {
			$this->Flash->error(__('Invalid file type.'));
		} else {
			$temp = [
				'name' => $data['name'],
				'pic_path' => $file['name'],
				'username' => $currentRole[0]['User']['username']
			];
		}
		move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/' . $file['name']);



		$this->Book->create();
		$this->Book->save($temp);

	}
	public function addapproved()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents("php://input"), true);
		$id = $data['id'];
		$data1 = array(
			'Book' => array(
				'id' => $id,
				'status' => 2
			)
		);
		$this->Book->save($data1);
	}
	public function adddeclined()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents("php://input"), true);
		$this->Book->delete($data['id']);
	}
	public function makerequest()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents("php://input"), true);
		$currentDate = date('Y-m-d');
		$id = $data['id'];
		$data1 = array(
			'Book' => array(
				'id' => $id,
				'requested_date' => $currentDate,
				'status' => 3
			)
		);
		$this->Book->save($data1);
	}
	public function availablebooks()
	{
		$data = $this->Book->find('all', [
			'recursive' => -1,
			'conditions' => ['status' => 2]
		]);
		$this->set('books', json_encode($data));
	}
	public function yourbooks()
	{

	}
}