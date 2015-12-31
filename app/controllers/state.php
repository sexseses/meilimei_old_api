<?php
class state extends CI_Controller {
	private $islogin = 0;
	public function __construct() {
		parent :: __construct();
		$this->load->helper(array (
			'form',
			'url'
		));
		$this->load->library('form_validation');
		$this->load->library('yisheng');
		if ($this->wen_auth->is_logged_in() ) {
			$this->islogin = 1;
		} else {
			$this->islogin = 0;
		}
	}
	public function islog($param = '') {
        echo $this->islogin;
	}
	public function gethash(){
        echo '<input type="hidden" name="'.$this->security->get_csrf_token_name().'" value="'.$this->security->get_csrf_hash().'" size="40" maxlength="40"/>';
	}
	public function getfee(){
		if($this->islogin && $this->input->get('days')){
           $days = explode(',',$this->input->get('days'));
           $ressult=array();
           $result['days'] = count($days);
           $result['fees'] = 200*$result['days'];
           echo json_encode($result);
		}
	}

}
?>