<?php
class logtrack extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->library('form_validation');
	   $this->load->model('Users_model');
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('logtrack')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$condition = ' WHERE 1 ';
		$data['issubmit'] = false;$fix = '';
		$this->load->library('pager');
		//search start
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';
			if ($this->input->get('email')) {
				$condition .= " AND user_login.name = '" . trim($this->input->get('email'))."'";
				$fix.=$fix==''?'?email='.$this->input->get('email'):'&email='.$this->input->get('email');
			}
			if ($this->input->get('uid')) {
				$condition .= " AND user_login.uid = '" .intval($this->input->get('uid'))."'";
				$fix.=$fix==''?'?uid='.$this->input->get('uid'):'&uid='.$this->input->get('uid');
			}
		}
		$data['total_rows'] = $this->db->query("SELECT user_login.id FROM user_login   {$condition} ORDER BY user_login.id DESC")->num_rows();

		$per_page =  16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
       $data['results'] = $this->db->query("SELECT user_login.*,users.role_id as rid,users.banned,users.alias FROM user_login LEFT JOIN users ON user_login.uid=users.id {$condition} ORDER BY user_login.id DESC  LIMIT $offset , $per_page")->result();
		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/yiyuan/index/' . ($start -1)).$fix : site_url('manage/yiyuan/index').$fix;
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yiyuan/index/' . ($start +1)).$fix : '';
		$config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                 'querystring_name'=>$fix.'&page',
                'base_url'=>'manage/yiyuan/index',
                "pager_index"=>$start
          );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$this->session->set_userdata('history_url', 'manage/logtrack?page=' . ($start -1));
		$data['message_element'] = "logtrack";
		$this->load->view('manage', $data);
	}
}
?>
