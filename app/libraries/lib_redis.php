<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lib_redis{
    private $size ;
    private $redis ;
    private $channel_queue;
    private $current_index ;
    public function __construct() {
        $this->size = 100;
        $this->redis = new Redis();
        $this->channel_queue = 'staffs';
        $this->redis->connect('127.0.0.1', '6379');
        $this->set_index();
    }
    public function set_index($index=0){
        $this->redis->set('current_index',$index);
    }
    public function en_queue($key,$value) {
        return  $this->redis->rpush($this->channel_queue, $key) && $this->redis->set($key,$value);
    }
    public function is_empty(){
        return  $this->redis->lsize('admins')<=0;
    }

    public function is_set($key){
        return  $this->redis->exists($key);
    }
    public function is_full(){
        return $this->redis->lsize($this->channel_queue) >= $this->size;
    }
    public function remove($value){
        return $this->redis->lRem($this->channel_queue,$value,2);
    }
    public function get_list(){
        return $this->redis->lrange($this->channel_queue,0,-1);
    }
    public function delete_key($key){
        return $this->redis->delete($key);
    }
    public function get_value($key){
        return  $this->redis->get($key);
    }
    public function allocate_admin(){
        $index = $this->redis->get('current_index');
        $size  = $this->redis->lsize('admins');
        if($size ==0){
            return false;
        }
        if($index<$size){
            $key =  $this->redis->lindex('staffs',$index);
            if($this->redis->get($key)<=1){
                $this->remove($key);
                return $key;
            }else{
                $this->redis->decr($key);
                $this->redis->incr('current_index');
                return $key ;

            }

        }else{
            $this->redis->set('current_index',0);
            $this->allocate_admin();
        }
    }

}