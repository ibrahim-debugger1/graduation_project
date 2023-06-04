<?php

class GroupsController extends AppController
{

    public function index()
    {
        $this->set('group_info', $this->Group->get_data());
    }
    public function add()
    {
        $this->loadModel('UserRole');
        if (empty($this->request->data)) {
            $group_options = $this->Group->get_data();
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
                if (empty(array_search($key, $groups_id))) {
                    $final[$key] = $value;
                }
            }
            $this->set('all_options', $final);
        } else {
            foreach ($this->request->data['group']['group_options'] as $lo) :
                $temp2 = ['user_id' => $this->Session->read('User.id'), 'group_id' => $lo, 'role_id' => 1];
                $this->UserRole->create();
                $this->UserRole->save($temp2);
            endforeach;
            $this->Flash->success(__('The groups have been saved'));
            return $this->redirect(['controller' => 'users','action' => 'index']);

        }
    }
}
