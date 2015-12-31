<?php
class message extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('home')){
          die('Not Allow');
       }
	}
	public function index($page = '') {

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "message";
		$this->load->view('manage', $data);
	}
}
?>
