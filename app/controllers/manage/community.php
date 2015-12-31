<?php
class community extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $this->eventonedb = $this->load->database('event1', TRUE);
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
        $this->load->helper('file');
        $this->load->model('users_model');
        $this->load->model('privilege');
        $this->load->model('remote');
        $this->privilege->init($this->uid);
    }
    
    public function index(){
    	$this->load->library('pager');
    	$page = $this->input->get('page');
        $per_page = 15;
        $start = intval($page);
        $start == 0 && $start = 1;
    
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
    
        $sql = "select * from event_topic where 1=1 and display= 1 order by id desc limit {$offset},{$per_page}";
        $event_rs = $this->eventonedb->query($sql)->result_array();
        
        $data['total_rows'] = $this->eventonedb->query("select * from event_topic where 1=1 and display= 1 order by id")->num_rows();
        $config =array(
        		"record_count"=>$data['total_rows'],
        		"pager_size"=>$per_page,
        		"show_jump"=>true,
        		"show_front_btn"=>true,
        		"show_last_btn"=>true,
        		'max_show_page_size'=>10,
        		'querystring_name'=>'page',
        		'base_url'=>'manage/community/index',
        		"pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();

        $data['event_rs'] = $event_rs;
        $data['message_element'] = "community";
        $this->load->view('manage', $data);
    }
    
    public function add(){
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $event_title = $this->input->post('event_title');
            $event_context = $this->input->post('event_context');
            $event_score = abs(intval($this->input->post('event_score')));
            $event_type = $this->input->post('event_type');
            $begin_time = strtotime($this->input->post('begin_time'));
            $end_time = strtotime($this->input->post('end_time'));
            $share_title = $this->input->post('share_title');

            $upload_path = 'banner/'.date('Y').'/'.date('m').'/';


            if(empty($event_title) || empty($begin_time) || empty($end_time) ){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/community/add", 'refresh');
            }


            
            if ($_FILES['share_pic']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['share_pic']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                    redirect('manage/community', 'refresh');
                }
                $share_pic = $upload_path . $file_name;
            }

            $insertData = array(
                'event_title' => $event_title,
                'begin_time' => $begin_time,
                'end_time' => $end_time,
                'event_context' => $event_context,
                'event_score' => $event_score,
                'event_type' => $event_type,
                'share_pic' => $share_pic,
            	'share_title' => $share_title,
                'event_creattime' => time()
            );
    
            $rs = $this->eventonedb->insert('event_topic',$insertData);
    
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功！"));
                redirect("manage/community", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("manage/community/add", 'refresh');
            }
        }
    
        $data['message_element'] = "community_add";
        $this->load->view('manage', $data);
    }
    
    
    public function edit($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/community", 'refresh');
        }
        
        if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
            $begin_time = strtotime($this->input->post('begin_time'));
            $end_time = strtotime($this->input->post('end_time'));
            $share_title = $this->input->post('share_title');
            $share_pic = $this->input->post('share_pic');
            
            $upload_path = 'banner/'.date('Y').'/'.date('m').'/';

            if(empty($begin_time) || empty($end_time)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/community/edit/$id", 'refresh');
            }
            
            if ($_FILES['new_share_pic']['tmp_name']) {
            	$file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
            	if (!$this->remote->cp($_FILES['new_share_pic']['tmp_name'], $file_name, $upload_path . $file_name)) {
            		$this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
            		redirect('manage/community', 'refresh');
            	}
            	$share_pic = $upload_path . $file_name;
            }
    
            $event_title = $this->input->post('event_title');
            $event_context = $this->input->post('event_context');
            $event_score = abs(intval($this->input->post('event_score')));
            $event_type = $this->input->post('event_type');
    
            if(empty($event_title)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/community/edit/$id", 'refresh');
            }
    
            $upData = array(
                'event_title' => $event_title,
                'begin_time' => $begin_time,
                'end_time' => $end_time,
                'event_context' => $event_context,
                'event_score' => $event_score,
            	'share_pic' => $share_pic,
            	'share_title' => $share_title,
                'event_type' => $event_type
            );
    
            $this->eventonedb->where('id',$id);
            $rs = $this->eventonedb->update('event_topic',$upData);
    
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "更新成功！"));
                redirect("manage/community", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "更新失败！"));
                redirect("manage/community/edit/$id", 'refresh');
            }
        }
    
        $sql = "select * from event_topic where 1=1 and id = ?";
        $community_rs = $this->eventonedb->query($sql,array($id))->row_array();
        $data['community_rs'] = $community_rs;
    
    
        $data['id'] = $id;
        $data['message_element'] = "community_edit";
        $this->load->view('manage', $data);
    }
    
    function del($id = ''){
        if(empty($id)){
            $id = $this->input->get('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/community", 'refresh');
        }
    
        $upData = array(
            'display' => 0,
        );
    
        $this->eventonedb->where('id',$id);
        $rs = $this->eventonedb->update('event_topic',$upData);
    
        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "删除成功！"));
            redirect("manage/community", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "删除失败！"));
            redirect("manage/community", 'refresh');
        }
    
    }
    
    public function edit_banner($id=''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/community", 'refresh');
        }
    
        $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
        $event_pic = '';
    
        if(isset($_POST['act']) && $_POST['act']=='edit'){
            
            if ($_FILES['pic']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['pic']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                    redirect('manage/community', 'refresh');
                }
                $event = $upload_path . $file_name;
     
    
                $upData = array( 
                    'event_pic' => $event
                );

                $this->eventonedb->where('id',$id);
                $rs = $this->eventonedb->update('event_topic',$upData);
                if($rs){
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传成功'));
                    redirect('manage/community', 'refresh');
                }
            }else{
                
                $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                redirect('manage/community', 'refresh');
            }
        }
    
    
        $sql = "select * from event_topic where 1=1 and id = ?";
        $community_rs = $this->eventonedb->query($sql,array($id))->row_array();
        $data['community_rs'] = $community_rs;
        $data['id'] = $id;
        $data['message_element'] = "community_banner";
        $this->load->view('manage', $data);
    }
    
    public function del_banner($id = ''){
        if(empty($id)){
            $id = $this->input->get('id');
        }
    
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/community", 'refresh');
        }
    
        $type = $this->input->get('type');
    
         
    
        $upData = array(
                'event_pic' => NULL
        );
    
    
        $this->eventonedb->where('id',$id);
        $rs = $this->eventonedb->update('event_topic',$upData);
    
        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "图片更新成功！"));
            redirect("manage/community", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "图片更新失败！"));
            redirect("manage/community", 'refresh');
        }
    }
    
    public function baomingCollection($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "页面参数错误！"));
            redirect("manage/community", 'refresh');
        }
        
        $table_name = "event_topic_enter";
        $sql = "select * from {$table_name} where 1=1 and event_id = ?";
        $collection_rs = $this->eventonedb->query($sql,array($id))->result();
        
        $data['coll_rs'] = $collection_rs;
        $data['event_id'] = $id;
        $data['message_element'] = "community_enter_collection";
        $this->load->view('manage', $data);
        
    }
    
    public function collection_del($id = '',$table='1'){
    	if(empty($id)){
    		$id = $this->input->post('id');
    	}
    
    	if(empty($id)){
    		$this->session->set_flashdata('flash_message', $this->common->flash_message('error', "页面参数错误！"));
    		redirect("manage/community", 'refresh');
    	}
    	
    	if($table){
    		$table_name = "event_topic_detail";
    	}else{
    		$table_name = "event_topic_enter";
    	}
    	
    	$sql = "select * from {$table_name} where 1=1 and id = ?";
    	$collection_rs = $this->eventonedb->query($sql,array($id))->result();
    	
    
    	$data['coll_rs'] = $collection_rs;
    	$data['event_id'] = $id;
    	$data['message_element'] = "community_detail_collection";
    	$this->load->view('manage', $data);
    }
    
    public function fatieCollection($id = ''){
    	$this->load->library('pager');
        if(empty($id)){
            $id = $this->input->post('id');
        }
        
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "页面参数错误！"));
            redirect("manage/community", 'refresh');
        }
        
        $page = $this->input->get('page');
        $per_page = 15;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
        	$offset = ($start -1) * $per_page;
        }else{
        	$offset = $start * $per_page;
        }
        
        
        $table_name = "event_topic_detail";
        $sql = "select * from {$table_name} where 1=1 and event_id = ?  limit {$offset},{$per_page}";
        $collection_rs = $this->eventonedb->query($sql,array($id))->result();
        
        $data['total_rows'] = $this->eventonedb->query("select * from {$table_name} where 1=1 and event_id = $id")->num_rows();
        
        
        if(!empty($collection_rs)){
            foreach($collection_rs as &$v){
                $sql = "select ww.weibo_id, ww.type_data,ww.content,ww.ctime,u.username,u.id as userid,u.alias from wen_weibo as ww LEFT join users as u on ww.uid = u.id where ww.weibo_id = {$v->topic_id}";
                $tmp = $this->db->query($sql)->row();
                $v->topic_context = $tmp->content;
                $v->ctime = $tmp->ctime;
				$v->topic_type = unserialize($tmp->type_data);
				$v->topic_type = $v->topic_type['title'];
				$v->alias = $tmp->alias;
				$v->userid = $tmp->userid;
				$v->weibo_id = $tmp->weibo_id;
            }
        }
        
        $config =array(
        		"record_count"=>$data['total_rows'],
        		"pager_size"=>$per_page,
        		"show_jump"=>true,
        		"show_front_btn"=>true,
        		"show_last_btn"=>true,
        		'max_show_page_size'=>10,
        		'querystring_name'=>'page',
        		'base_url'=>'manage/community/fatieCollection/{$id}',
        		"pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		
		//print_r($collection_rs);die;
        $data['coll_rs'] = $collection_rs;
        $data['event_id'] = $id;
        $data['message_element'] = "community_detail_collection";
        $this->load->view('manage', $data);
    }

    public  function sendAllSmsone(){
        $ids = $this->input->post('ids');
        $event_id = $this->input->post('event_id');
        $smstext = $this->input->post('smstext');

    	foreach ($ids as $v){
            $error = $this->community_enter_sendSms_detail($v,$smstext,'event_topic_detail');
            sleep(10);
        }
    
        if($error){
        	$this->session->set_flashdata('flash_message', $this->common->flash_message('error', "短信发送完成！"));
        	redirect("http://www.meilimei.com/manage/community/baomingCollection/{$event_id}", 'refresh');
        }else{
        	$this->session->set_flashdata('flash_message', $this->common->flash_message('error', "出错了！"));
        	redirect("http://www.meilimei.com/manage/community/baomingCollection/{$event_id}", 'refresh');
        }
    }

    public  function sendAllSmstwo(){
        $ids = $this->input->post('ids');
        $event_id = $this->input->post('event_id');
        $smstext = $this->input->post('smstext');
    
        foreach ($ids as $v){
            $error = $this->community_enter_sendSms_detail($v,$smstext,'event_topic_detail');
            sleep(10);
        }
    
 
        if($error){
        	$this->session->set_flashdata('flash_message', $this->common->flash_message('error', "短信发送完成！"));

        	redirect("http://www.meilimei.com/manage/community/fatieCollection/{$event_id}", 'refresh');
        }else{
        	$this->session->set_flashdata('flash_message', $this->common->flash_message('error', "出错了！"));
        
        	redirect("http://www.meilimei.com/manage/community/fatieCollection/{$event_id}", 'refresh');
        }
    }
    
    private function community_enter_sendSms_detail($id='',$smstext='',$tablename='') {
    	$this->load->library('sms');
    	 
    	$smstext = $this->input->post('smstext');
    	if($id == 0 || empty($id)) {
    		$this -> jsonarr['msg'] = 'sendSms_detail参数错误！';
    		echo "sendSms_detail参数错误！";
    		return false;
    	}
    
    	$sql = "select user_id from {$tablename} where id = $id ";
    	$rs = $this->eventonedb->query($sql)->row_array();
    	
 
    	if($rs){
    		$user_sql = "select phone from users where id = {$rs['user_id']}";
    		$userrs = $this->db->query($user_sql)->row_array();
    	}else{
    		$this -> jsonarr['msg'] = '没有改条帖子！';
    		echo "没有改条帖子！";
    		return false;
    	}


    	$status = $this->sms->sendSMS(array($userrs['phone']), $smstext);

    	if($status===false || $status =='' || $status <0){
    		$this -> jsonarr['msg'] = '短信参数错误！';
    		echo "短信错误！";
    		return false;
    	}
    
    	$updata = array(
    			'smscontext' => $smstext,
    			'sms' => 'Y'
    	);
    
    	$this->eventonedb->where('id',$id);
    	$uprs = $this->eventonedb->update($tablename,$updata);
		if($uprs){
			return true;
		}
    }
    
    
    private function community_enter_sendSms($id='',$smstext='',$tablename='') {
        $this->load->library('sms');
         
        $smstext = $this->input->post('smstext');
        if($id == 0 || empty($id)) {
            $this -> jsonarr['msg'] = 'sendSms参数错误！';
            return false;
        }
    
        $sql = "select * from {$tablename} where id = $id ";
        $rs = $this->eventonedb->query($sql)->row_array();
    
        $updata = array(
            'smscontext' => $smstext,
            'sms' => 'Y'
        );
        
        $this->eventonedb->where('id',$id);
        $uprs = $this->eventonedb->update($tablename,$updata);
    
        $status = $this->sms->sendSMS(array ($rs['mobile']), $smstext);
        if($status===false || $status =='' || $status <0){
            $this -> jsonarr['msg'] = '短信参数错误！';
            echo "短信错误！";
            return false;
        }
    
        $updata = array(
            'smscontext' => $smstext,
            'sms' => 'Y'
        );
    
        $this->eventonedb->where('id',$id);
        $uprs = $this->eventonedb->update($tablename,$updata);
        if($uprs){
        	return true;
        }
         
    }
}