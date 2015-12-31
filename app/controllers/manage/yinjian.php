<?php
class yinjian extends CI_Controller {
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
       if(!$this->privilege->judge('priv')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$condition = '';
		if ($this->input->post('submit')) {
			if ($this->input->post('phone')) {
				if ($this->input->post('sname')) {
					$condition = " WHERE users.phone = " . $this->input->post('phone') . " AND users.alias like '%" . $this->input->post('sname') . "%'";
				} else {
					$condition = " WHERE users.phone = " . $this->input->post('phone');
				}
			} else {
				if ($this->input->post('sname')) {
					$condition = " WHERE users.alias like '%" . $this->input->post('sname') . "%'";
				}
			}
		}
    //	$tmp = $this->db->query("SELECT  tongji.coupon_code FROM tongji {$condition} group by tongji.coupon_code ")->count_all_results();
        $data['total_rows'] = 300;
		$per_page = 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT count(tongji.coupon_code) as nums, tongji.coupon_code,tongji.url FROM tongji  {$condition} group by tongji.coupon_code order by nums DESC LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/yinjian/index/' . ($start -1)) : site_url('manage/yinjian/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yinjian/index/' . ($start +1)) : site_url('manage/yinjian');

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "yinjian";
		$this->load->view('manage', $data);
	}

	public function detail($param = '',$page='') {
		if ($param != '') {
			$data['total_rows'] = $this->db->query("SELECT tongji.id FROM tongji WHERE coupon_code=$param")->num_rows();

			$per_page = 16;
			$start = intval($page);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;
			$data['results'] = $this->db->query("SELECT * FROM tongji WHERE coupon_code=$param ORDER BY id  DESC LIMIT $offset , $per_page")->result();
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('manage/yinjian/detail/'.$param .'/'. ($start -1)) : site_url('manage/yinjian/detail/'.$param);
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yinjian/detail/' .$param.'/'.($start +1)) : site_url('manage/yinjian/detail/'.$param);

			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "yinjian_detail";
			$this->load->view('manage', $data);
		}
	}
}
?>
