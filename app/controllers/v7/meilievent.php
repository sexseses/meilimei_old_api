<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
 
 
class Meilievent extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(E_ALL);
		ini_set("display_errors","On");
	} 
	
	public function index(){
		echo "aadb";
	}
	
	public function addEvent(){
		$event_name = $this->input->post('event_name');
		$event_content = $this->input->post('event_content');
		$event_mobile = $this->input->post('event_mobile');
		$user_name = $this->input->post('user_name');
		$event_time = time();

		$data_arr = array(
			'event_name' => $event_name,
			'event_content' => $event_content,
			'event_mobile' => $event_mobile,
			'user_name' => $user_name,
			'event_time' => $event_time
		);
		

		if($this->db->insert('mlm_event', $data_arr)){
			echo TRUE;
		}else{
			echo FALSE;
		}
		
	}


	public function addEvent1(){

		$event_name = $this->input->post('event_name');
		$event_content = $this->input->post('event_content');
		$event_mobile = $this->input->post('event_mobile');
		$user_name = $this->input->post('user_name');
		$event_time = time();

		$data_arr = array(
			'event_name' => $event_name,
			'event_content' => $event_content,
			'event_mobile' => $event_mobile,
			'user_name' => $user_name,
			'event_time' => $event_time
		);
		

		if($this->db->insert('mlm_event', $data_arr)){
			echo TRUE;
		}else{
			echo FALSE;
		}
		
	}
	public function activity(){
		$array = array(1,2,3);
		$array1 = array(6,7,8);
		$tmp = 0;
		$rs = $this->db->query("select count(1) as num from mlm_event where lucky=2 and event_time <".time()." and event_time >".strtotime(date('Y-m-d')))->result_array();
		if(rand(1,3) == 1){
			if(isset($rs[0]['num']) && intval($rs[0]['num']) <=5){
				if(in_array(rand(1,99),$array)){
					$tmp = 2;
				}
				
			}
		}else{

			$rs1 = $this->db->query("select count(1) as num from mlm_event where lucky=3 and event_time <".time()." and event_time >".strtotime(date('Y-m-d')))->result_array();
			if(isset($rs1[0]['num']) && intval($rs1[0]['num']) <=10){
				if(in_array(rand(1,99),$array1)){
					$tmp = 3;
				}
				
			}
		}

		if($tmp != 0){
			$event_name = $this->input->post('event_name');
			$event_content = $this->input->post('event_content');
			$event_mobile = $this->input->post('event_mobile');
			$user_name = $this->input->post('user_name');
			$event_time = time();

			$data_arr = array(
				'event_name' => $event_name,
				'event_content' => $event_content,
				'event_mobile' => $event_mobile,
				'user_name' => $user_name,
				'event_time' => $event_time,
				'lucky' => $tmp
			);
			

			if($this->db->insert('mlm_event', $data_arr)){
				echo $tmp;
			}
		}else{
			echo $tmp;
		}
	}
}
?>