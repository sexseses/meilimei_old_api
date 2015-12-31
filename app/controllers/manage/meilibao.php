<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
 
 
class Meilibao extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(0);
		//ini_set("display_errors","On");
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->library('sms');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
       if(!$this->privilege->judge('users')){
          die('Not Allow');
       }
       
	}
	/**
	 * 美丽保用户
	 * 
	 */
	public function index() {
		$page = $this->input->get('page');
		$tmp = $this->privilege->getPri('users');
		if(!empty($tmp) and ($tmp = unserialize($tmp[0]['data'])) and $tmp['fromv']){
			$condition = ' WHERE 1 = 1 and users.id>='.$tmp['fromv'].' and users.id<='.$tmp['tv'];
		}else{
			$condition = ' WHERE 1 = 1 ';
		}
		$this->load->library('pager');

		$data['issubmit'] = false;$fix = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';
			if ($this->input->get('phone')) {
				$condition .= " AND users.phone = '" . $this->input->get('phone')."'";
				$fix.=$fix==''?'?phone='.$this->input->get('phone'):'&phone='.$this->input->get('phone');
			}

		    if ($this->input->get('tbzt_status')==='0' ||$this->input->get('tbzt_status')===0) {
		        $condition .= "  AND ppu.tbzt_status is NULL ";
		        $fix.=$fix.'&tbzt_status='.$this->input->get('tbzt_status');
		    }elseif($this->input->get('tbzt_status')=='1'||$this->input->get('tbzt_status')=='2'){
		        $condition .= "  AND ppu.tbzt_status = ".$this->input->get('tbzt_status');
		        $fix.=$fix.'&tbzt_status='.$this->input->get('tbzt_status');
		    }

			if ($this->input->get('noc')) {
				$condition .= "  AND user_profile.states = 0 ";
				$fix.=$fix==''?'?noc='.$this->input->get('noc'):'&noc='.$this->input->get('noc');
			}
			if($secuid = intval($this->input->get('ome'))) {
			    $fix.=$fix==''?'?ome='.$this->input->get('ome'):'&ome='.$this->input->get('ome');
			    $condition .= ' AND v.uid='.$secuid;
		   }
			if ($this->input->get('city')) {
				$condition .= "  AND company.city like '%" . trim($this->input->get('city')) . "%'";
				$fix.=$fix==''?'?city='.$this->input->get('city'):'&city='.$this->input->get('city');
			}
			if($this->input->get('opendate')){
			$fix.=$fix==''?'?opendate=1&':'&opendate=1&';
            $fix.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
			$fix.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
			$data['cdate'] = $this->input->get('yuyueDateStart');
			$data['edate'] = $this->input->get('yuyueDateEnd');
		    $cdate = strtotime($this->input->get('yuyueDateStart'));
            $edate = strtotime($this->input->get('yuyueDateEnd'));
            $condition .= " and ppu.tb_time>= {$cdate} and users.created<= {$edate} ";
			}
		}
		$data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id LEFT JOIN pingan_policy_user ppu ON ppu.user_id = users.id {$condition} ORDER BY users.id DESC")->num_rows();
		
		$per_page = 30;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT distinct(v.vuid),
		        users.id,users.alias,users.phone,users.regfrom,users.regsys,
		        user_profile.states, user_profile.city,
		        ppu.tbzt_status, ppu.tb_time FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id LEFT JOIN pingan_policy_user ppu ON ppu.user_id = users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page")->result();
		//print_r($data['results']);
        foreach($data['results'] as $k => $v){
            $data['results'][$k]->reNums = $this->db->query("select * from wen_questions where fUid = ".$v->id."")->num_rows();
        }
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
                'base_url'=>'manage/meilibao/index',
                "pager_index"=>$page
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "meilibao";
		$data['managers'] = $this->Gmanager();
		$this->session->set_userdata('history_meilibao_url', 'manage/meilibao/index?page=' . ($start -1).'&'.$fix);
		$this->load->view('manage', $data);
	}
	/**
	 * 	get manager
	 */
	private function Gmanager(){
	    $this->db->where('role_id', 16);
	    $this->db->where('banned', 0);
	    $this->db->select('id, alias');
	    $this->db->from('users');
	    return $this->db->get()->result_array();
	}
	/** get usre detail info
	 * @param string $id
	 * 
	 */	
	public function detail($id = '') {
		if ($this->input->post('submit')) {
            //
		} else {
		    $data =array();
		    $id = intval($id);
		    if($id==0) exit('invalid access');
			$sql = "select * from pingan_policy_user where user_id =$id";
			$rs = $this->db->query($sql)->result_array();
			if(count($rs)>0){
			    $data =$rs['0'];
			}
			$data['user_id'] = $id;
		    $data['notlogin'] = $this->notlogin;
		    $data['message_element'] = "editmeilibao";
		    $this->load->view('manage', $data);
		}
	}
	
	/** update usre detail info
	 * @param string $id
	 *
	 */
	public function update() {
	    $user_id = $this->input->post('user_id');
	    
	    $name = $this->input->post('name');
	    $id_card = $this->input->post('id_card');
	    $sex = $this->input->post('sex');
	    $birthday = $this->input->post('birthday');
	    $city = $this->input->post('city');
	    $telphone = $this->input->post('telphone');
	    $tbzt_status = $this->input->post('tbzt_status');
        
	    if(empty($user_id)||empty($name)||empty($id_card)||empty($sex)||empty($birthday)||empty($city)||empty($telphone)||empty($tbzt_status)){
	    	echo 'param error';exit;
	    }
	    $sql = "select * from pingan_policy_user where user_id=$user_id";
	    $num = $this->db->query($sql)->num_rows();
	    $arr = array(
	            'name' => $name,
	            'id_card' => $id_card,
	            'sex' => $sex,
	            'birthday' => strtotime($birthday),
	            'city' => $city,
	            'telphone' => $telphone,
	            'tbzt_status' => $tbzt_status,
	            'creat_time'=> time(),
	            'tb_time' => '0',
	    );
	    if($num==0){
	        $arr['user_id'] = $user_id;
	    	$this->db->insert('pingan_policy_user',$arr);
	    	$lastInsertId = $this->db->insert_id();
	    }else{
	        $this->db->where('user_id',$user_id);
	        $this->db->update('pingan_policy_user',$arr);
	        $rs = $this->db->query('select id from pingan_policy_user where user_id='.$user_id)->row_array();
	        $lastInsertId = $rs['id'];
	    }
	    if($tbzt_status=='2'){//投保成功
	    	//插入保单 
	    	$this->db->insert('pingan_policy',array('pingan_policy_id'=>$lastInsertId,'user_id'=>$user_id,'content'=>'手动下单','creat_time'=>time()));
	    	//更新投保时间
	    	$this->db->where('user_id',$user_id);
	    	$this->db->update('pingan_policy_user',array('tb_time'=>time()));
	    	//清空验证码
	    	$this->db->delete('pingan_code',array('telphone'=>$telphone));
	    }
	    
//         if($this->session->userdata('history_meilibao_url')!=''){
//             redirect($this->session->userdata('history_meilibao_url'));
//         }else{
             redirect('manage/meilibao/index');
//         }
	    
	}
	/** send 验证码
	 * @param string $code
	 *  @param int $id
	 */
	public function send() {
	    $code = $this->input->post('code');
	    $id = $this->input->post('id');
	    if((int)$code ==0 ||(int)$id ==0) {
	    	echo json_encode(array('code'=>'0','msg'=>'invalid params'));exit;
	    }
	    $sql = "select * from pingan_policy_user where user_id =$id";
	    $rs = $this->db->query($sql)->result_array();
	    if(count($rs)==0 || !$this->is_phone_num($rs['0']['telphone'])){
	        $sql = "select * from users where id =$id";
	        $rs = $this->db->query($sql)->result_array();
	        if(count($rs)==0|| !$this->is_phone_num($rs['0']['phone'])){
	            echo json_encode(array('code'=>'0','msg'=>'no telephone or invalid telphone'));exit;
	        }elseif($this->is_phone_num($rs['0']['phone'])){
	            $yzm =$this->makecode();
	            $content = $code.$yzm;
	            $status = $this->db->insert('pingan_code',array('status'=>$code,'code'=>$content,'telphone'=>$rs['0']['phone'],'creat_time'=>time()));
	            if(!$status){
	                echo json_encode(array('code'=>'0','msg'=>'insert error'));exit;
	            }else{
	                $status = $this->sms->sendSMS(array ($rs['0']['phone']), '美丽保验证码'.$content . '退订回复0000 ');
	                if($status===false || $status =='' || $status<0){
	                    echo json_encode(array('code'=>'0','msg'=>'old phone send fail'));exit;
	                }
	            }
	        }
	    }elseif($this->is_phone_num($rs['0']['telphone'])){
	        $yzm =$this->makecode();
	        $content = $code.$yzm;
	        $status =$this->db->insert('pingan_code',array('status'=>$code,'code'=>$content,'telphone'=>$rs['0']['telphone'],'creat_time'=>time()));
	        if(!$status){
	            echo json_encode(array('code'=>'0','msg'=>'insert error'));exit;
	        }else{
	            $status = $this->sms->sendSMS(array ($rs['0']['telphone']), '美丽保验证码'.$content . '退订回复0000 ');
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
	    for($i=0;$i<8;$i++){
	        $randnum=rand(0,9); // 10;
	        $authnum.=$list[$randnum];
	    }
	    return $authnum;
	}
	public function test(){
	    //var_dump($this->sms->sendSMS(array ('13661484743','13622041643'), '美丽保验证码' . '退订回复0000 '));exit;
// //	    $this->db->delete('pingan_code',array('id'=>2));
//  	    print_r($this->db->query('select * from pingan_code')->result_array());
//  	    print_r(($this->db->query('select * from pingan_code')->row_array()));
// // 	    echo $this->db->query('select * from pingan_code')->num_rows();exit;
// //  	    $data = array('telphone' => '123');
// // // 	    $where = "id=1";
// //  	    $this->db->where('code','12345000');
// // 	    $this->db->update('pingan_code',$data);
// // 	    $this->db->select('LAST_INSERT_ID()');
// // 	    print_r($this->db->get()->row_array());exit;
// // 	    $update_sting = $this->db->update_string('pingan_code',array('status'=>4),'id=1');
// // 	    $this->db->query($update_sting);
// //	    echo $this->db->update_id();
// // 	    exit;
// // 	    $str = $this->db->update_string('pingan_code', $data, $where);
// // 	    exit;
// // 	    $this->db->insert('pingan_code',array('status'=>3));
// // 	    echo $this->db->insert_id();exit;
// //		print_r($this->db->query('select * from pingan_code')->row()->id);exit;
// // 		$this->db->where('id=1');
// // 		$this->db->update('pingan_code',array('status'=>2));
// // 		echo $this->db->insert_id();exit;
// 		//$this->db->update_string('pingan_code',array('status'=>0),'id=1');
// 		//echo $this->db->insert_id();
		exit;
	}
}
?>