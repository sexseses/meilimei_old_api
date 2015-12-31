<?php
class tuiguang extends CI_Controller {
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
       if(!$this->privilege->judge('tuiguang')){
          die('Not Allow');
       }
	}
	public function index($page='') {
		$condition = ''; $data['issubmit'] = false;
		if($this->input->post('submit')){$data['issubmit'] = true;
			if($this->input->post('city')){
			  $condition = " WHERE advert.city like '%".$this->input->post('city')."%'";
			}
		}
		$data['total_rows'] = $this->db->query("SELECT advert.id FROM advert {$condition}")->num_rows();

		$per_page = 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT advert.*,users.alias FROM advert LEFT JOIN users ON users.id=advert.uid {$condition} ORDER BY advert.id LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/tuiguang/index/' . ($start -1)) : site_url('manage/tuiguang/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/tuiguang/index/' . ($start +1)) : '';

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "tuiguang";
		$this->load->view('manage', $data);
	}
}
?>
