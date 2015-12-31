<?php
class email extends CI_Controller {
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
       if(!$this->privilege->judge('email')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$option['table'] = 'email_templates';
		$this->db->from('email_templates');
		$this->db->select('id,type,title');
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "email";
		$this->load->view('manage', $data);
	}

	public function detail($param = '') {
		$data['detail'] = intval($param);
        if($this->input->post('email_body_html')){
        	$updata['mail_subject'] = $this->input->post('mail_subject');
        	$updata['email_body_html'] = $this->input->post('email_body_html');
           $this->common->updateTableData('email_templates',$data['detail'],'',$updata);
           $this->session->set_flashdata('msg', $this->common->flash_message('success', '邮件模板修改成功!'));
           redirect('manage/email');
        }
		$this->db->from('email_templates');
        $this->db->where('id',$data['detail']);
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "editemail";
		$this->load->view('manage', $data);
	}
}
?>
