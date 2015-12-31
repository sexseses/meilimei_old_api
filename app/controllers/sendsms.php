<?php
class sendsms extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->helper(array (
			'form',
			'url',
			'cookie'
		));
		$this->load->library('Form_validation');
	}
    public function tehui(){
    	$this->load->library('sms');
    	if($this->input->get("checkno") == 'fzs36589tx'){
    		$phone = intval($this->input->get('phones'));
    		$content = trim($this->input->get('content'));
            $status = $this->sms->sendSMS(array ("{$phone}"), $content . '退订回复TD ');
            echo '+OK';
    	}
    }
	public function index() {
		$data['message'] = '';
		$this->load->library('sms');
		static $status = '未初始化';
		if ($this->input->post() && $this->input->post("checkno") == 'sms36589tx') {
			$phonenum = $this->input->post("phonenum");
			$message = $this->input->post("message");
			$pattern = '/\n/';
			$phonenum = preg_split($pattern, $phonenum);
			$phonelist = '';
			foreach ($phonenum as $key => $val) {
				$val = trim($val);
				if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $val)) {


			            //$this->Common_model->insertData('phones', $datas);
			            $status = $this->sms->sendSMS(array ("$val"), $message . '退订回复TD ');

				}
			}
			redirect('sendsms');
		}
		$data['message'] = '发送状态:' . $status;
		$data['getBalance'] = $this->sms->getBalance();
		$data["title"] = 'Send SMS';
		$data["meta_keyword"] = '';
		$data["meta_description"] = '';
		$data['message_element'] = "sms/view_sms";
		$this->load->view('template', $data);
	}
}
?>
