<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class www extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->helper('form');
	}
	 public function index($param='') { echo 'sdfsdf';
	 	if($this->input->post('code') && $this->input->post('code')=='ilovefamily'){
           $this->session->set_userdata('is_log', true);
           redirect('/user/reg');
	 	}
	 	if($this->session->userdata('is_log')){
             redirect('/user/reg');
	 	}else{
	 		 $this->load->view('www.php');
	 	}
	 }

}
?>
