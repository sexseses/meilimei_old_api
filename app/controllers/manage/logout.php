<?php
class logout extends CI_Controller {
	private $notlogin = true;
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		}else{
			redirect('');
		}
		$this->load->model('privilege');
	}
	public function index() {
		$this->wen_auth->logout();
		redirect('');
	}
}
?>
