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
	header("Access-Control-Allow-Origin:*");
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
}
?>