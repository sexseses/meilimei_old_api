<?php
class yiyuanevent extends CI_Controller {
	private $notlogin = true,$uid='';

	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(E_ALL);
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
		$this->load->model('remote_new');
		$this->privilege->init($this->uid);
        if(!$this->privilege->judge('yiyuanevent')){
          die('not allow');
       }
	}


    public function event_add(){
        $data['city'] = $this->db->query("select * from city")->result_array();
        

        if($this->input->post("act") == 'add'){
           $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
           $banner_pic = '';
           if ($_FILES['banner_path']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                $banner_pic = $upload_path.$file_name;
                if (!$this->remote_new->cp($_FILES['banner_path']['tmp_name'], $file_name, $banner_pic,array (), true)) {
                    $this->session->set_flashdata('flash_message', 
                        $this->common->flash_message('error', $this->upload->display_errors())
                    );
                    redirect('manage/yiyuanevent/event_add', 'refresh');
                }
                
           }
           
           if ($_FILES['event_pic']['tmp_name']) {
               $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
               $event_pic = $upload_path.$file_name;
               if (!$this->remote_new->cp($_FILES['event_pic']['tmp_name'], $file_name, $event_pic,array (), true)) {
                   $this->session->set_flashdata('flash_message',
                       $this->common->flash_message('error', $this->upload->display_errors())
                   );
                   redirect('manage/yiyuanevent/event_add', 'refresh');
               }
           
           }
           
           
            if($this->input->post("city")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '城市为空！'));
            }
            
            if($this->input->post("event_name")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '活动名称为空！'));
            }
            if($this->input->post("begin_time") || $this->input->post("end_time")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '开始时间，结束时间为空！'));
            }
            if($this->input->post("tehuiurl") || $this->input->post("tehuiid")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '特惠信息为空'));
            }
            
            
            $datas = array (
                'author' => $this->uid,
                'city'=>serialize($this->input->post("city")),
                'name' => trim($this->input->post('event_name')), 
                'begin_time' => strtotime($this->input->post('begin_time')), 
                'end_time' => strtotime($this->input->post('end_time')),
                'tehuiurl' => htmlspecialchars($this->input->post('tehuiurl')),
                'tehuiid' => $this->input->post('tehuiid'),
                'banner_path' => $banner_pic,
                'banner_title' => trim($this->input->post('banner_title')),
                'page_save' => $this->input->post('save_page'), 
                'cover' => $this->input->post('cover'), 
                'event_context' => $this->input->post('event_context'), 
                'gift_rule' => $this->input->post('gift_rule'), 
                'virtual_support'=> $this->input->post('virtual_support')
            );

            $rs = $this->db->insert('ml_one_event', $datas);
            
            if($rs){
                redirect('manage/yiyuanevent', 'refresh');
            }else{
                redirect('manage/yiyuanevent/event_add', 'refresh');
            }
        }	
        
        $data['message_element'] = "yiyuanevent_add";
		$this->load->view('manage', $data);
    }
    
   // $start = microtime(true);  
   // echo microtime(true)-$start;
    
    /**
     * 给活动添加机构 
     * 
     */
    public function event_edit_mechanism($event_id = '' ){
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
        }
        //$event_id = $this->input->get('event_id');
        $event_sql = "select city from ml_one_event where 1=1 and id = {$event_id}";
        $result = $this->db->query($event_sql)->result_array();
        $mechanism_rs = array();
        
        if($result){
            $city =unserialize($result[0]['city']);
            foreach ($city as $v){
                $mechanism_sql = "select * from company where 1=1 and city = '{$v}'";
                $mechanism_rs[$v] = $this->db->query($mechanism_sql)->result_array();
            }
        }
        $data['results'] = $mechanism_rs;
        
        if($this->input->post("act") == 'add'){
            if(empty($this->input->post('mechanism'))){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '机构为空！'));
            }
            
            $indata = array(
                'mechanism' => $this->input->post(mechanism)
            );
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('ml_one_event', $indata);
            
            if($rs){
                redirect('manage/yiyuanevent', 'refresh');
            }else{
                redirect('manage/yiyuanevent/event_add', 'refresh');
            }
            
        }

        $data['event_id'] = $event_id;
        $data['message_element'] = "yiyuanevent_edit_mechanism";
        $this->load->view('manage', $data);
    }
    
    
    
    /**
     * 给活动添加医师
     *
     */
    public function event_edit_physician($event_id = '' ){
        //$event_id = $this->input->get('event_id');
        $event_sql = "select mechanism from ml_one_event where 1=1 and id = {$event_id}";
        $result = $this->db->query($event_sql)->row_array();
        $physician_rs = array();
         
        if($result){
            $mechanism_sql = "select name from company where 1=1 and id = '{$result['mechanism']}'";
            $mechanism_rs  = $this->db->query($mechanism_sql)->result_array();
            if($mechanism_rs){
                $physician_sql = "select u.username,up.* from users as u join user_profile as up 
                                    on u.id = up.user_id and up.company = '{$mechanism_rs[0]['name']}'";
                $physician_rs  = $this->db->query($physician_sql)->result_array();
            }
            
        }
        
        $data['results'] = $physician_rs;
        //print_r($data);die;
        if($this->input->post("act") == 'add'){
            if(empty($this->input->post('physician'))){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '医师为空！'));
            }
        
            $indata = array(
                'physician' => serialize($this->input->post('physician'))
            );
        
            $this->db->where('id', $event_id);
            $rs = $this->db->update('ml_one_event', $indata);
            if($rs){
                redirect('manage/yiyuanevent', 'refresh');
            }else{
                redirect('manage/yiyuanevent/event_add', 'refresh');
            }
        }
        
        $data['event_id'] = $event_id;
        $data['message_element'] = "yiyuanevent_edit_physician";
        $this->load->view('manage', $data);
    }

    
    
    public function event_edit($event_id = ''){
        $data['city'] = $this->db->query("select * from city")->result_array();
        $data['row'] = $this->db->query("select * from apple where id = {$event_id} ")->row_array();
        $data['message_element'] = "yiyuanevent_edit";
        
        if($this->input->post("act") == 'edit'){
            $upload_path = 'banner/';
            $banner_pic = '';
             
            if ($_FILES['banner_path']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['picture']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',
                        $this->common->flash_message('error', $this->upload->display_errors())
                    );
                    redirect('manage/yiyuanevent/event_edit', 'refresh');
                }
                $banner_pic = $upload_path . $file_name;
            }
        
        
            if($this->input->post("city")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '城市为空！'));
            }
            if($this->input->post("event_name")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '活动名称为空！'));
            }
            if($this->input->post("begin_time") || $this->input->post("end_time")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '开始时间，结束时间为空！'));
            }
        
        
        
            $updatas = array (
                'author' => $this->uid,
                'city'=>serialize($this->input->post("city")),
                'name' => trim($this->input->post('event_name')),
                'begin_time' => strtotime($this->input->post('begin_time')),
                'end_time' => strtotime($this->input->post('end_time')),
                'banner_path' => $banner_pic,
                'banner_title' => trim($this->input->post('banner_title')),
                'page_save' => $this->input->post('save_page'),
                'cover' => $this->input->post('cover'),
                'event_context' => $this->input->post('event_context'),
                'gift_rule' => $this->input->post('gift_rule'),
                'virtual_support'=> $this->input->post('virtual_support')
            );
        
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('ml_one_event',$updatas);
            
            if($rs){
                redirect('manage/yiyuanevent', 'refresh');
            }else{
                redirect('manage/yiyuanevent/event_add', 'refresh');
            }
        }
        
        $data['event_id'] = $event_id;
        $this->load->view('manage', $data);
    }




	public function index($page = '') {
		$this->load->library('pager');
		try{
		    $sql = "SELECT * FROM ml_one_event WHERE 1=1 ";
		    $data['results'] = $this->db->query($sql)->result_array();
		    
		    if(!$data['results']){
		        redirect('manage', 'refresh');
		    }
		    
		    $data['total_rows'] = $this->db->query($sql)->num_rows();
		}catch(Exception $e) {
		    $this->session->set_flashdata('flash_message', 
		        $this->common->flash_message('error', $this->e->error));
        }
		
        $data['message_element'] = "yiyuanevent";
		//print_r($data);die;
		$this->load->view('manage', $data);
	}

}
?>
