<?php 

class Group extends AppModel{
    
    public function get_data(){
        $data=$this->find('list',[
            'recursive' => -1,
            'fields' => ['id','name'],
            'condtions' => ['deleted' => 0]
        ]);
        return $data;
    }
}