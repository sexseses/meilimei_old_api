<?php
class index extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id()==16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		//$this->load->library('sms');
        $this->privilege->init($this->uid);
	}
	public function index($param='') {
	    //$data['getBalance'] = $this->sms->getBalance();
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "default";
		$this->load->view('manage', $data);
	}
}
?>
