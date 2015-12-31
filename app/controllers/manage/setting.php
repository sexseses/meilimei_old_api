<?php
class setting extends CI_Controller {
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
		if (!$this->privilege->judge('setting')) {
			die('Not Allow');
		}
	}
	public function index() {
		if ($this->input->post('updategrow') || $this->input->post('updatejifen') OR $this->input->post('updategrade')) {
			if ($this->input->post('updategrade')) {
				$gname = $this->input->post('group');
				$gv1 = $this->input->post('groupv1');
				$gv2 = $this->input->post('groupv2');
				$gv3 = $this->input->post('groupv3');
				foreach ($gname as $k => $v) {
					$data = array (
						'grouptitle' => $v,
						'creditshigher' => $gv1[$k],
						'discount' => $gv3[$k],
						'creditslower' => $gv2[$k]
					);
					$this->db->where('groupid', $k);
					$this->db->update('user_group', $data);
				}
			}elseif($this->input->post('updategrow')){
                $this->db->where('code', 'GROW_TOPIC');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_TOPIC'
				)));

				$this->db->where('code', 'GROW_RTOPIC');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_RTOPIC'
				)));

				$this->db->where('code', 'GROW_RFTOPIC');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_RFTOPIC'
				)));

				$this->db->where('code', 'GROW_ATTEND');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_ATTEND'
				)));

				$this->db->where('code', 'GROW_SPWEIBO');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_SPWEIBO'
				)));

				$this->db->where('code', 'GROW_LIMIT');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('GROW_LIMIT'
				)));
			}else {
				$this->db->where('code', 'JIFEN_WEIBO');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('JIFEN_WEIBO'
				)));
				$this->db->where('code', 'JIFEN_RWEIBO');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('JIFEN_RWEIBO'
				)));

				$this->db->where('code', 'JIFEN_ZIXUN');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('JIFEN_ZIXUN'
				)));

				$this->db->where('code', 'JIFEN_RZIXUN');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('JIFEN_RZIXUN'
				)));

				$this->db->where('code', 'JIFEN_REG');
				$this->db->update('settings', array (
					'int_value' => $this->input->post('JIFEN_REG'
				)));
			}
		}
		$this->db->from('settings');
		$tmp = $this->db->get()->result_array();
		$data['results'] = array ();
		foreach ($tmp as $r) {
			$data['results'][$r['code']] = $r['int_value'];
		}

		$this->db->from('user_group');
		$data['grade'] = $this->db->get()->result_array();

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "setting";
		$this->load->view('manage', $data);
	}

	public function email($page = '') {
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
		if ($this->input->post('email_body_html')) {
			$updata['mail_subject'] = $this->input->post('mail_subject');
			$updata['email_body_html'] = $this->input->post('email_body_html');
			$this->common->updateTableData('email_templates', $data['detail'], '', $updata);
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '邮件模板修改成功!'));
			redirect('manage/email');
		}
		$this->db->from('email_templates');
		$this->db->where('id', $data['detail']);
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "editemail";
		$this->load->view('manage', $data);
	}
}
?>
