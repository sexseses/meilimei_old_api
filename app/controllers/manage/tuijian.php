<?php
class tuijian extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('tuijian')){
          die('Not Allow');
       }
	}
	public function index($param = '') {
		$data['issubmit'] = false;
		$condition = ' users.role_id = 2 ';
		if ($this->input->post('submit')) {
			$data['issubmit'] = true;
			if ($this->input->post('phone')) {
				$condition .= " AND users.phone = " . $this->input->post('phone');
			}
			if ($this->input->post('sname')) {
				$condition .= "  AND users.alias like '%" . $this->input->post('sname') . "%'";
			}
			if ($this->input->post('city')) {
				$condition .= "  AND user_profile.city like '%" . $this->input->post('city') . "%'";
			}
		}
		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($param);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;

		$data['data'] = $this->db->query("SELECT users.id,user_profile.city,user_profile.tel,users.alias,users.email,users.phone,users.rank_search FROM (`users`) LEFT JOIN user_profile ON user_profile.user_id=users.id WHERE {$condition} ORDER BY users.rank_search DESC LIMIT $offset, $per_page ")->result_array();
		$data['total_rows'] = $this->db->query("SELECT users.id FROM (`users`) LEFT JOIN user_profile ON user_profile.user_id=users.id WHERE {$condition} ORDER BY users.rank_search DESC ")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/tuijian/index/' . ($start -1)) : site_url('manage/tuijian/index/');
		$data['next'] = $offset+$per_page < $data['total_rows'] ? site_url('manage/tuijian/index/' . ($start +1)) : site_url('manage/tuijian/index/'.$start);

		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "tuijian";
		$this->load->view('manage', $data);
	}

 public function yiyuan($param = '') {
		$data['issubmit'] = false;
		$condition = ' users.role_id = 3 ';
		if ($this->input->post('submit')) {
			$data['issubmit'] = true;
			if ($this->input->post('tel')) {
				$condition .= " AND company.tel like '%" . $this->input->post('tel'). "%'";
			}
			if ($this->input->post('sname')) {
				$condition .= "  AND company.name like '%" . $this->input->post('sname') . "%'";
			}
			if ($this->input->post('city')) {
				$condition .= "  AND company.city like '%" . $this->input->post('city') . "%'";
			}
		}
		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($param);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;

		$data['data'] = $this->db->query("SELECT users.id,company.city,company.tel,company.name,users.email,company.contactN,users.rank_search FROM (`users`) LEFT JOIN company ON company.userid=users.id WHERE {$condition} ORDER BY users.rank_search DESC LIMIT $offset, $per_page ")->result_array();
		$data['total_rows'] = $this->db->query("SELECT users.id FROM (`users`) LEFT JOIN company ON company.userid=users.id WHERE {$condition} ORDER BY users.rank_search DESC ")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/tuijian/yiyuan/' . ($start -1)) : site_url('manage/tuijian/yiyuan/');
		$data['next'] = $offset+$per_page < $data['total_rows'] ? site_url('manage/tuijian/yiyuan/' . ($start +1)) : site_url('manage/tuijian/yiyuan/'.$start);

		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "tuijian_yiyuan";
		$this->load->view('manage', $data);
	}
}
?>
