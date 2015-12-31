<?php
class Gbos extends CI_Controller{
public function __construct() {
		parent :: __construct();
		//报告所有错误
		//error_reporting(E_ALL);
		//error_reporting(E_ALL ^ E_NOTICE);
		ini_set("display_errors","On");
		header("Content-type: text/html; charset=utf-8");
		
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		
		$this->load->library('form_validation');
		$this->load->library('yisheng');
		$this->load->helper('file');
		$this->load->model('users_model');
		$this->load->model('privilege');
		$this->load->model('remote');
		$this->privilege->init($this->uid);
        // if(!$this->privilege->judge('yiyuanevent')){
        //   die('not allow');
        // }
	}

    public function index(){
        $page = $this->input->get('page');
        $this->load->library('pager');
        
        $data['issubmit'] = false;$fix = '';
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        $condition = " where 1=1 ";

//         $name = $this->input->get("name");
//         $city = $this->input->get("city");
//         $mobile = $this->input->get("mobile");
//         $tag = $this->input->get("tag");
//         $images = $this->input->get("images");
//         if(empty($name) || empty($city) || empty($mobile)  || empty($tag))
//         {
//             $this->result['msg'] = '数据不能为空！';
//             exit;
//         }
//         $data_arr = array(
//             'name' => $name,
//             'city' => $city,
//             'mobile' => $mobile,
//             'tag' => $tag,
//             'images' => $images
//         );
        
//         $sql = "select * from gbos where mobile = '$mobile'";
//         //echo $sql;die;
//         $rs = $this->db->query($sql)->result_array();
//         if(count($rs) <= 0){
//             $rs = $this->db->insert('gbos', $data_arr);
//         }
        
        $data['total_rows'] = $this->db->query("select * from gbos {$condition}")->num_rows();

        $per_page = 30;
        $start = intval($page);
        //$start == 0 && $start = 1;
        
        if ($start > 0)
            $offset = ($start -1) * $per_page;
        else
            $offset = $start * $per_page;
        $data['results'] = $this->db->query("select * from gbos {$condition} ORDER BY gbosid DESC  LIMIT $offset , $per_page")->result();
        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>10,
            'querystring_name'=>$fix.'&page',
            'base_url'=>'manage/gbos',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "gbos";
        $this->load->view('manage', $data);
    }  
    }
?>