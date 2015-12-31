<?php
class testImage extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        
        //报告所有错误
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        header("Content-type: text/html; charset=utf-8");
    
        if ($this->wen_auth->get_role_id() == 16) {
            $this->notlogin = false;
            $this->uid=$this->wen_auth->get_user_id();
        } else {
            redirect('');
        }
    
        $this->load->library('form_validation');
        $this->load->helper('file');
        $this->load->model('users_model');
        $this->load->model('privilege');
        $this->load->model('remote');
        $this->privilege->init($this->uid);
    }
    
    public function index(){
    	$data['message_element'] = "testImage";
    	$this->load->view('manage', $data);
    }
    
    public function upload(){
    	$upload_path = 'banner/'.date('Y').'/'.date('m').'/';
    	$event_pic = '';
    	
    	if(isset($_POST['act']) && $_POST['act']=='upimage'){
    		if ($_FILES['pic']['tmp_name']) {
    			$file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
    			if (!$this->remote->upload_qiniu($_FILES['pic']['tmp_name'], $file_name)) {
    				echo "错误！";
    			}
    			$event = $upload_path . $file_name;
    			echo $event;
    			
    		}
    	}
    } 
    
    
}