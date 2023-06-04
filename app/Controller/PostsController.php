<?php

class PostsController extends AppController
{
	public $helpers = array('Html', 'Form');
	public function index()
	{
		$this->loadModel('Post');
		$this->loadModel('PostCounter');
		$this->loadModel('UserRole');
		$this->loadModel('User');
		$this->loadModel('Group');

		$id = $this->Session->read('User.id');
		$groups_id = $this->UserRole->find(
			'all',
			[
				'recursive' => -1,
				'fields' => ['group_id'],
				'conditions' => ['user_id' => $id]
			]
		);
		$temp = [];
		foreach ($groups_id as $gi):
			array_push($temp, $gi['UserRole']['group_id']);
		endforeach;
		$group_name = $this->Group->find('list', [
			'recursive' => -1,
			'fields' => ['name'],
			'conditions' => ['id' => $temp]
		]);
		$posts = $this->Post->find(
			'all',
			[
				'recursive' => -1,
				'fields' => ['Post.title', 'Post.body', 'Post.id', 'Post.likes', 'Post.pic_path', 'User.id', 'User.username', 'User.role_id', 'User.pic_path', 'Group.name', 'Group.id', 'PostCounter.id'],
				'conditions' => ['Post.group_id' => $temp, 'Post.approved' => 1],
				'order' => 'Post.id DESC',
				'joins' => [
					[
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => ['Post.user_id = User.id']
					],
					[
						'table' => 'groups',
						'alias' => 'Group',
						'type' => 'inner',
						'conditions' => ['Post.group_id = Group.id']
					],
					[
						'table' => 'post_counters',
						'alias' => 'PostCounter',
						'type' => 'left',
						'conditions' => array('Post.id = PostCounter.post_id', 'PostCounter.user_id' => $this->Session->read('User.id'))
					]
				]
			]
		);
		$currentRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['role_id'],
			'conditions' => ['id' => $this->Session->read('User.id')]
		]);
		$approved_count = $this->Post->find('first', [
			'recursive' => -1,
			'fields' => ['count(id)'],
			'conditions' => ['approved' => 0],
			'group' => 'approved'
		]);
		$this->set('approved_count', json_encode($approved_count));
		$this->set('posts', json_encode($posts));
		$this->set('userRole', json_encode($currentRole));
		$this->set('groups', json_encode($group_name));
	}
	public function like()
	{
		$this->loadModel('PostCounter');
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$like = $data['liked'];
		$post_id = $data['id'];
		if ($like == 1) {
			$post = $this->Post->findById($post_id);
			$post['Post']['likes']++;
			$this->Post->updateAll(
				['likes' => $post['Post']['likes']],
				['id' => $post_id]
			);
			$this->PostCounter->create();
			$this->PostCounter->save(['user_id' => $this->Session->read('User.id'), 'post_id' => $post_id]);
		} else {
			$post = $this->Post->findById($post_id);
			$post['Post']['likes']--;
			$this->Post->updateAll(
				['likes' => $post['Post']['likes']],
				['id' => $post_id]
			);
			$like_id = $this->PostCounter->find('first', [
				'recursive' => -1,
				'fields' => ['id'],
				'conditions' => ['user_id' => $this->Session->read('User.id'), 'post_id' => $post_id]
			]);
			$this->PostCounter->delete($like_id['PostCounter']['id']);
		}
		echo json_encode($post['Post']['likes']);
	}
	public function comment()
	{
		$this->autoRender = false;
		$this->loadModel('Comment');
		$this->loadModel('User');
		$post_id = json_decode(file_get_contents('php://input'), true);
		$comment = $this->Comment->find('all', [
			'recursive' => -1,
			'fields' => ['Comment.id', 'Comment.body', 'Comment.created', 'Comment.user_id', 'User.username'],
			'conditions' => ['post_id' => $post_id],
			'joins' => [
				[
					'table' => 'users',
					'alias' => 'User',
					'type' => 'inner',
					'conditions' => ['Comment.user_id = User.id']
				]
			]
		]);
		echo json_encode($comment);
	}
	public function addcom()
	{
		$this->autoRender = false;
		$this->loadModel('Comment');
		$data = json_decode(file_get_contents("php://input"), true);
		$body = $data['content'];
		$user_id = $this->Session->read('User.id');
		$post_id = $data['post_id'];
		$qw = [
			'body' => $body,
			'user_id' => $user_id,
			'post_id' => $post_id
		];
		$this->Comment->save($qw);
	}
	public function deletepos()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$post_id = $data['post_id'];

		$this->Post->delete($post_id);
	}
	public function deletecom()
	{
		$this->autoRender = false;
		$this->loadModel('Comment');
		$data = json_decode(file_get_contents('php://input'), true);
		$id = $data['id'];

		$this->Comment->delete($id);

	}
	public function editPost()
	{
		$this->autoRender = false;
		$data = json_decode(file_get_contents('php://input'), true);
		$id = $data['id'];
		$title = $data['title'];
		$body = $data['body'];
		$data1 = array(
			'Post' => array(
				'id' => $id,
				// Assuming you have the record ID
				'title' => $title,
				'body' => $body
			)
		);
		$this->Post->save($data1);
	}
	public function editcom()
	{
		$this->autoRender = false;
		$this->loadModel('Comment');
		$data = json_decode(file_get_contents('php://input'), true);
		$comment_id = $data['id'];
		$comment_body = $data['content'];
		$data1 = array(
			'Comment' => array(
				'id' => $comment_id,
				'body' => $comment_body
			)
		);
		$this->Comment->save($data1);
	}
	public function view($id = null)
	{
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$this->loadModel('PostCounter');
		$this->loadModel('Comment');
		if (empty($this->request->data)) {
			$post = $this->Post->findById($id);
			if (!$post) {
				throw new NotFoundException(__('Invalid post'));
			}
			$this->loadModel('Comment');
			$this->loadModel('User');
			$temp = $this->Comment->find('all', [
				'recursive' => -1,
				'fields' => ['Comment.id', 'Comment.body', 'Comment.created', 'Comment.user_id', 'User.username'],
				'conditions' => ['post_id' => $id],
				'joins' => [
					[
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => ['Comment.user_id = User.id']
					]
				]
			]);
			$t = $this->PostCounter->find('all', [
				'recursive' => -1,
				'fields' => ['post_id', 'user_id'],
				'conditions' => ['post_id' => $id, 'user_id' => $this->Session->read('User.id')]
			]);
			if (!empty($t)) {
				$post['Post']['t'] = 1;
			} else {
				$post['Post']['t'] = 0;
			}
			$this->set('comments', $temp);
			$this->set('post', $post);
		} else {
			$check = $this->PostCounter->find('all', [
				'recursive' => -1,
				'fields' => ['id', 'post_id', 'user_id'],
				'conditions' => ['post_id' => $id, 'user_id' => $this->Session->read('User.id')]
			]);
			if (empty($check) && !empty($this->request->data['comment']['like'])) {
				$this->PostCounter->create();
				$this->PostCounter->save(['post_id' => $id, 'user_id' => $this->Session->read('User.id')]);
			} else if (!empty($check) && empty($this->request->data['comment']['like'])) {
				$this->PostCounter->deleteAll(['post_id' => $id, 'user_id' => $this->Session->read('User.id')]);
			}
			$temp = $this->PostCounter->find('first', [
				'recursive' => -1,
				'fields' => ['count(PostCounter.post_id)'],
				'conditions' => ['PostCounter.post_id' => $id],
				'group' => 'post_id'
			]);
			if (!empty($temp)) {
				$this->Post->updateAll(
					['likes' => $temp[0]['count(`PostCounter`.`post_id`)']],
					['id' => $id]
				);
			} else {
				$this->Post->updateAll(
					['likes' => 0],
					['id' => $id]
				);
			}
			$this->Comment->create();
			if (empty($this->request->data['comment']['body'])) {
				return $this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
			$temp = [
				'body' => $this->request->data['comment']['body'],
				'user_id' => $this->request->data['comment']['user_id'],
				'post_id' => $this->request->data['comment']['post_id']
			];
			if ($this->Comment->save($temp)) {
				$this->Flash->success(__('Your Comment has been saved.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
		}
	}

	public function add()
	{
		$this->loadModel('Group');
		$this->loadModel('UserRole');
		$this->loadModel('User');

		if ($this->request->is('post')) {
			$this->autoRender = false;
			$data = $this->request->data;
			$currentRole = $this->User->find('all', [
				'recursive' => -1,
				'fields' => ['role_id'],
				'conditions' => ['id' => $this->Session->read('User.id')]
			]);
			if ($currentRole[0]['User']['role_id'] == 1)
				$access = 0;
			else
				$access = 1;
			if (!empty($_FILES)) {
				$file = $_FILES['file'];
				$allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
				// $allowedSize = 10 * 1024 * 1024; // 1 MB
				if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedTypes)) {
					$this->Flash->error(__('Invalid file type.'));
				} else {
					$temp = [
						'title' => $data['title'],
						'body' => $data['content'],
						'pic_path' => $file['name'],
						'likes' => 0,
						'user_id' => $this->Session->read('User.id'),
						'group_id' => $data['group_id'],
						'approved' => $access
					];
				}
				move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/' . $file['name']);
			} else {
				$temp = [
					'title' => $data['title'],
					'body' => $data['content'],
					'likes' => 0,
					'user_id' => $this->Session->read('User.id'),
					'group_id' => $data['group_id'],
					'approved' => $access
				];
			}
			$this->Post->create();
			$this->Post->save($temp);
			echo json_encode($temp);

		} else {
			$this->loadModel('Group');
			$group_options = $this->Group->find('all', [
				'recursive' => -1,
				'fields' => ['id', 'name']
			]);
			$id = $this->Session->read('User.id');
			$groups_id = $this->UserRole->find(
				'list',
				[
					'recursive' => -1,
					'fields' => ['group_id'],
					'conditions' => ['user_id' => $id]
				]
			);
			$final = [];
			foreach ($group_options as $key => $value) {
				if (!empty(array_search($key, $groups_id))) {
					$final[$key] = $value;
				}
			}
			$this->set('group_info', $final);
		}
	}



	public function edit($id = null)
	{
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$post = $this->Post->findById($id);
		if (!$post) {
			throw new NotFoundException(__('Invalid post'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Post->id = $id;
			if ($this->Post->save($this->request->data)) {
				$this->Flash->success(__('Your post has been updated.'));
				return $this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
			$this->Flash->error(__('Unable to update your post.'));
		}
		if (!$this->request->data) {
			$this->request->data = $post;
		}
	}
	public function delete($id = null)
	{
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$this->loadModel('PostCounter');
		$this->loadModel('Comment');
		if ($this->Post->delete($id, true)) {
			$this->Flash->success(__('Your post has been deleted.'));
			return $this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
	}
	public function request()
	{
		$this->loadModel('Post');
		$this->loadModel('PostCounter');
		$this->loadModel('UserRole');
		$this->loadModel('User');
		$this->loadModel('Group');
		if ($this->request->is('post')) {
			$this->autoRender = false;
			$data = json_decode(file_get_contents("php://input"), true);
			$value = $data['approved'];
			$post_id = $data['post_id'];
			if ($value == 1) {
				$post = $this->Post->findById($post_id);
				$this->Post->updateAll(
					['approved' => 1],
					['id' => $post_id]
				);
			} else {
				$this->Post->delete($post_id);
			}
		} else {
			$id = $this->Session->read('User.id');
			$groups_id = $this->UserRole->find(
				'all',
				[
					'recursive' => -1,
					'fields' => ['group_id'],
					'conditions' => ['user_id' => $id]
				]
			);
			$temp = [];
			foreach ($groups_id as $gi):
				array_push($temp, $gi['UserRole']['group_id']);
			endforeach;
			$group_name = $this->Group->find('list', [
				'recursive' => -1,
				'fields' => ['name'],
				'conditions' => ['id' => $temp]
			]);
			$posts = $this->Post->find(
				'all',
				[
					'recursive' => -1,
					'fields' => ['Post.title', 'Post.body', 'Post.id', 'Post.likes', 'Post.pic_path', 'User.id', 'User.username', 'User.role_id', 'User.pic_path', 'Group.name', 'PostCounter.id'],
					'conditions' => ['Post.group_id' => $temp, 'Post.approved' => 0],
					'order' => 'Post.id DESC',
					'joins' => [
						[
							'table' => 'users',
							'alias' => 'User',
							'type' => 'inner',
							'conditions' => ['Post.user_id = User.id']
						],
						[
							'table' => 'groups',
							'alias' => 'Group',
							'type' => 'inner',
							'conditions' => ['Post.group_id = Group.id']
						],
						[
							'table' => 'post_counters',
							'alias' => 'PostCounter',
							'type' => 'left',
							'conditions' => array('Post.id = PostCounter.post_id', 'PostCounter.user_id' => $this->Session->read('User.id'))
						]
					]
				]
			);
			$currentRole = $this->User->find('all', [
				'recursive' => -1,
				'fields' => ['role_id'],
				'conditions' => ['id' => $this->Session->read('User.id')]
			]);
			$approved_count = $this->Post->find('first', [
				'recursive' => -1,
				'fields' => ['count(id)'],
				'conditions' => ['approved' => 0],
				'group' => 'approved'
			]);
			$this->set('approved_count', $approved_count);
			$this->set('posts', json_encode($posts));
			$this->set('userRole', json_encode($currentRole));
			$this->set('groups', json_encode($group_name));
		}
	}

	public function group()
	{
		$this->loadModel('Post');
		$this->loadModel('PostCounter');
		$this->loadModel('UserRole');
		$this->loadModel('User');
		$this->loadModel('Group');

		$id = $this->Session->read('User.id');
		$groups_id = $this->UserRole->find(
			'all',
			[
				'recursive' => -1,
				'fields' => ['group_id'],
				'conditions' => ['user_id' => $id]
			]
		);
		$temp = [];
		foreach ($groups_id as $gi):
			array_push($temp, $gi['UserRole']['group_id']);
		endforeach;
		$group_name = $this->Group->find('list', [
			'recursive' => -1,
			'fields' => ['name'],
			'conditions' => ['id' => $temp]
		]);
		$posts = $this->Post->find(
			'all',
			[
				'recursive' => -1,
				'fields' => ['Post.title', 'Post.body', 'Post.id', 'Post.likes', 'Post.pic_path', 'User.id', 'User.username', 'User.role_id', 'User.pic_path', 'Group.name', 'Group.id', 'PostCounter.id'],
				'conditions' => ['Post.group_id' => $temp, 'Post.approved' => 1],
				'order' => 'Post.id DESC',
				'joins' => [
					[
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => ['Post.user_id = User.id']
					],
					[
						'table' => 'groups',
						'alias' => 'Group',
						'type' => 'inner',
						'conditions' => ['Post.group_id = Group.id']
					],
					[
						'table' => 'post_counters',
						'alias' => 'PostCounter',
						'type' => 'left',
						'conditions' => array('Post.id = PostCounter.post_id', 'PostCounter.user_id' => $this->Session->read('User.id'))
					]
				]
			]
		);
		$currentRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['role_id'],
			'conditions' => ['id' => $this->Session->read('User.id')]
		]);
		$approved_count = $this->Post->find('first', [
			'recursive' => -1,
			'fields' => ['count(id)'],
			'conditions' => ['approved' => 0],
			'group' => 'approved'
		]);
		$this->set('approved_count', json_encode($approved_count));
		$this->set('posts', json_encode($posts));
		$this->set('userRole', json_encode($currentRole));
		$this->set('groups', json_encode($group_name));
	}
	public function addgroup()
	{
		$this->autoRender = false;
		$this->loadModel('Group');
		$this->loadModel('UserRole');
		$group_options = $this->Group->find('all', [
			'recursive' => -1,
			'fields' => ['id', 'name']
		]);
		$id = $this->Session->read('User.id');
		$groups_id = $this->UserRole->find(
			'list',
			[
				'recursive' => -1,
				'fields' => ['group_id'],
				'conditions' => ['user_id' => $id]
			]
		);
		$final = [];
		foreach ($group_options as $a) {
			if ($a['Group']['id'] == 11)
				continue;
			if (empty(array_search($a['Group']['id'], $groups_id))) {
				$final[$a['Group']['id']] = $a['Group']['name'];
			}
		}
		$approved_count = $this->UserRole->find('all', [
			'recursive' => -1,
			'fields' => ['group_id', 'count(group_id)'],
			'group' => 'group_id'
		]);
		$response = array(
			'members' => $approved_count,
			'groups' => $final
		);
		echo json_encode($response);
	}
	public function appendgroup()
	{
		$this->autoRender = false;
		$this->loadModel('UserRole');
		$data = json_decode(file_get_contents('php://input'), true);
		$group_id = $data['id'];
		$user_id = $this->Session->read('User.id');
		$temp = [
			'user_id' => $user_id,
			'group_id' => $group_id,
			'role_id' => 1
		];
		$this->UserRole->create();
		$this->UserRole->save($temp);
		echo json_encode($temp);
	}
	public function creategroup()
	{
		$this->autoRender = false;
		$this->loadModel('Group');
		$this->loadModel('User');
		$this->loadModel('UserRole');
		$data = json_decode(file_get_contents('php://input'), true);
		$group_name = $data['group_name'];
		$temp = [
			'name' => $group_name,
			'deleted' => 0
		];
		$this->Group->create();
		$this->Group->save($temp);
		$lastRecord = $this->Group->find(
			'first',
			array(
				'order' => array('Group.created' => 'DESC'),
				'limit' => 1
			)
		);
		$group_id = $lastRecord['Group']['id'];
		$usersRole = $this->User->find('all', [
			'recursive' => -1,
			'fields' => ['id'],
			'conditions' => ['role_id' => 2]
		]);
		foreach ($usersRole as $key) {
			$t = [
				'user_id' => $key['User']['id'],
				'group_id' => $group_id,
				'role_id' => 1
			];
			$this->UserRole->create();
			$this->UserRole->save($t);
		}
	}
	public function deletegroup()
	{
		$this->autoRender = false;
		$this->loadModel('Group');
		$data = json_decode(file_get_contents('php://input'), true);
		$group_id = $data['id'];
		$this->Group->delete($group_id);
	}
	public function leavegroup()
	{
		$this->autoRender = false;
		$this->loadModel('UserRole');
		$data = json_decode(file_get_contents('php://input'), true);
		$group_id = $data['id'];
		$user_id = $this->Session->read('User.id');

		$id = $this->UserRole->find('first', [
			'recursive' => -1,
			'fields' => ['id'],
			'conditions' => ['group_id' => $group_id, 'user_id' => $user_id]
		]);
		$this->UserRole->delete($id['UserRole']['id']);
	}
}