<?php
class magic extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
       if(!$this->privilege->judge('users')){
          die('Not Allow');
       }
	}
	//user info lists
	public function index() {

		$page = $this->input->get('page');

		$this->load->library('pager');

		$data['issubmit'] = false;$fix = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';

		}

		echo $data['total_rows'] = $this->db->query("SELECT f.score FROM  faces f left join users u on f.uid=u.id ORDER BY f.score DESC")->num_rows();

		$per_page = 100;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT f.id, f.uid,f.score,f.pic,f.skins,u.phone,u.alias FROM faces f left join users u on f.uid=u.id ORDER BY f.score DESC  LIMIT $offset , $per_page")->result();


        //var_dump($data['results']);   die;
		$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/users/index/' . ($start -1)).$fix : site_url('manage/users/index').$fix;
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/users/index/' . ($start +1)).$fix : '';

         $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                "show_front_btn"=>true,
                "show_last_btn"=>true,
                'max_show_page_size'=>10,
                'querystring_name'=>$fix.'page',
                'base_url'=>'manage/magic/index',
                "pager_index"=>$page
            );
         $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "magic";
		$this->session->set_userdata('history_url', 'manage/magic/index?page=' . ($start -1).'&'.$fix);

		$this->load->view('manage', $data);
	}
	//delete user
	public function del($uid){
		$condition = array('id'=>$uid);
        $this->db->delete('faces',$condition);
        //thumb
      //  $this->deleteDir($this->path . '/users/' . $uid);
       redirect('manage/magic');
	}
}
?>
