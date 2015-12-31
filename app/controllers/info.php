<?php
class info extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
	}
	public function concern($param = '') {
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "concern";
		$this->load->view('template', $data);
	}
	public function about($param = '') {
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "about";
		$data['WEN_PAGE_TITLE'] = '关于我们';
		$this->load->view('template', $data);
	}
	public function question() {
		if ($remark = $this->input->post('remark')) {
			$this->load->model('Email_model');
			$splVars = array (
				"{contents}" => $remark,
				"{time}" => date('Y-m-d H:i',
			time()), "{site_name}" => '美丽诊所');
			$this->Email_model->sendMail("747242966@qq.com", "support@meilizhensuo.com", '美丽诊所', 'send_require', $splVars);
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '留言已成功发送!'));
			echo 1;
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '留言失败!'));
			echo 0;
		}
		echo '';
	}
}
?>
