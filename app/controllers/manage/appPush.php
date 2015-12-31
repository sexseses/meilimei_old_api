<?php
class appPush extends CI_Controller {
	private $notlogin = true, $uid = '';
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
		if (!$this->privilege->judge('appPush')) {
			die('Not Allow');
		}
	}
	public function index($page = '') {
		$data['results'] = $this->db->get('crons')->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "appPush";
		$this->load->view('manage', $data);
	}
	public function add() {
		if ($this->input->post('title')) {
			$datas['uid'] = $this->uid;
			$datas['title'] = $this->input->post('title');
			$datas['message'] = $this->input->post('message');
			$datas['sdate'] = strtotime($this->input->post('sdate'));
			$datas['datetype'] = $this->input->post('datetype');
			$datas['suser'] = $this->input->post('suser');
			$datas['cdate'] = time();
			$datas['usertype'] = $this->input->post('usertype');
			$this->db->insert('crons', $datas);
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "appPush_add";
		$this->load->view('manage', $data);
	}
	public function del($id = '') {
		if ($id) {
			$this->db->where('id', $id);
			$this->db->delete('crons');
			redirect('manage/appPush');
		}
	}
}
?>
