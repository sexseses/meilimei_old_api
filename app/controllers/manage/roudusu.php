<?php
class roudusu extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		//error_reporting(E_ALL);
		//ini_set("display_errors","On");
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

	/**
	 * 肉毒素
	 * 
	 */
	public function index() {
		$page = $this->input->get('page');
		$this->load->library('pager');

		$data['issubmit'] = false;$fix = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        $event_name = "第二季";
        $condition = " where 1=1 AND event_name= '".$event_name."'";
        
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';
			if ($this->input->get('phone')) {
				$condition .= " AND mobile = '" . $this->input->get('phone')."'";
			}

			if($this->input->get('opendate')){
			$fix.=$fix==''?'?opendate=1&':'&opendate=1&';
            $fix.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
			$fix.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
			$data['cdate'] = $this->input->get('yuyueDateStart');
			$data['edate'] = $this->input->get('yuyueDateEnd');
		    $cdate = strtotime($this->input->get('yuyueDateStart'));
            $edate = strtotime($this->input->get('yuyueDateEnd'));
            $condition .= " and time>= {$cdate} and time<= {$edate}  ";
			}
		}
		$data['total_rows'] = $this->db->query("select * from tehui_event_creotoxin {$condition}")->num_rows();
			
		$per_page = 30;
		$start = intval($page);
		//$start == 0 && $start = 1;
		
		if ($start > 0)
		    $offset = ($start -1) * $per_page;
		else
		    $offset = $start * $per_page;
		$data['results'] = $this->db->query("select * from tehui_event_creotoxin {$condition} ORDER BY id DESC  LIMIT $offset , $per_page")->result();
		$config =array(
		        "record_count"=>$data['total_rows'],
		        "pager_size"=>$per_page,
		        "show_jump"=>true,
		        "show_front_btn"=>true,
		        "show_last_btn"=>true,
		        'max_show_page_size'=>10,
		        'querystring_name'=>$fix.'&page',
		        'base_url'=>'manage/roudusu',
		        "pager_index"=>$page
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "roudusu";
		$this->load->view('manage', $data);
	}
	/** send 验证码
	 * @param string $code
	 *  @param int $id
	 */
	public function send() {
	    $this->load->library('sms');
	    $id = $this->input->post('id');
	    if((int)$id ==0) {
	        echo json_encode(array('code'=>'0','msg'=>'invalid params'));exit;
	    }
	    $sql = "select * from tehui_event_creotoxin where id =$id";
	    $rs = $this->db->query($sql)->result_array();
	    if(count($rs)==0 || !$this->is_phone_num($rs['0']['mobile'])){
	        echo json_encode(array('code'=>'0','msg'=>'no telephone or invalid telphone'));exit;
	    }else{
	        $yzm =$this->makecode();
	        $content = $yzm;
	        $this->db->where('id',$id);
	        $status =$this->db->update('tehui_event_creotoxin',array('capture'=>$content));
	        if($status===false){
	            echo json_encode(array('code'=>'0','msg'=>'update error'));exit;
	        }else{
	            $status = $this->sms->sendSMS(array ($rs['0']['mobile']), "感谢您成功购买860元瘦脸针特惠，验证码：$content。请务必保留好验证码，我们客服将在1个工作日内与您联系确认服务的时间。如有任何问题，请拨打免费客服热线：400-6677-245");
	            if($status===false || $status =='' || $status <0){
	                echo json_encode(array('code'=>'0','msg'=>'phone send fail'));exit;
	            }
	        }
	    }
	    echo json_encode(array('code'=>'1'));exit;
	}
	/**
	 * 手机合法性验证
	 */
	function is_phone_num($str){
	    return preg_match("/(1(?:3[4-9]|5[012789]|8[78])\d{8}|1(?:3[0-2]|5[56]|8[56])\d{8}|18[0-9]\d{8}|1[35]3\d{8})|14[57]\d{8}/s", $str);
	}
	/**
	 * 生成随即验证码
	 */
	public function makecode(){
	    //生成8位随机数
	    $ychar="0,1,2,3,4,5,6,7,8,9";
	    $list=explode(",",$ychar);
	    $authnum ='';
	    for($i=0;$i<6;$i++){
	        $randnum=rand(0,9); // 10;
	        $authnum.=$list[$randnum];
	    }
	    return $authnum;
	}
}