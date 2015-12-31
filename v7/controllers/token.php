<?php
class token extends CI_Controller {
	public function __construct() {
		parent :: __construct();
}
    public function index(){
    	$token = $this->session->userdata('upload_token');
    	$this->session->set_userdata(array('upload_token'=>false));
    	echo $token;
    }
}
?>






