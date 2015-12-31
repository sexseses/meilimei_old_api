<?php
class test1 extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->model('remote');
	}
	public function index(){
		$this->remote->dcp('139855874754744535c501b99a37.jpg');
	}
}
?>
