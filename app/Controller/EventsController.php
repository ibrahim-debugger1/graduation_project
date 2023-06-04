<?php

class EventsController extends AppController
{
	public $helpers = array('Form', 'Html');
	public function index()
	{
		$this->loadModel('User');
		$events = $this->Event->find('all', [
			'recursive' => -1,
			'order' => array('start')
		]);
		$temp = [];
		foreach ($events as $key) {
			$datetime = $key['Event']['end']; // The datetime you want to check

			$currentDatetime = new DateTime(); // Current datetime
			$checkDatetime = new DateTime($datetime);

			if ($checkDatetime > $currentDatetime) {
				array_push($temp, $key);
			}
		}
		$currentRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['role_id'],
			'conditions' => ['id' => $this->Session->read('User.id')]
		]);
		$this->set('userRole', json_encode($currentRole));
		$this->set('future_event', json_encode($temp));
	}
	public function add()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$temp = [
			'title' => $data['text'],
			'body' => $data['body'],
			'start' => $data['datetime-local'],
			'end' => $data['datetime-local2'],
			'location' => $data['text2']
		];
		$this->Event->create();
		$this->Event->save($temp);
	}

	public function delete()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$this->Event->delete($data['id']);
	}
}