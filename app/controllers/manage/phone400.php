<?php
class phone400 extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			 $this->uid = $this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('tixian')){
          die('Not Allow');
       }
	}
	public function index() {
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "phone400";
		$this->load->view('manage', $data);
	}
}
?>
