<?php
class onetehui extends CI_Controller {
	private $notlogin = true,$uid='';

	public function __construct() {
		parent :: __construct();
		//报告所有错误
		//error_reporting(E_ALL ^ E_NOTICE);
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
		$this->load->model('remote_new');
		$this->privilege->init($this->uid);
//         if(!$this->privilege->judge('onetehui')){
//           die('not allow');
//        }
	}
	
	
	public function index($page = '') {
	    $this->load->library('pager');
	    try{
	        $sql = "SELECT * FROM tehui_yiyuan WHERE 1=1 ";
	        $data['results'] = $this->db->query($sql)->result_array();
	
	        if(!$data['results']){
	            redirect('manage', 'refresh');
	        }
	
	        $data['total_rows'] = $this->db->query($sql)->num_rows();
	    }catch(Exception $e) {
	        $this->session->set_flashdata('flash_message',$this->common->flash_message('error', $this->e->error));
	    }
	
	    $data['message_element'] = "onetehui";
	    $this->load->view('manage', $data);
	}


    public function onetehui_add(){
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
                }
                
           }
           
           if ($_FILES['event_pic']['tmp_name']) {
               $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
               $event_pic = $upload_path.$file_name;
               if (!$this->remote_new->cp($_FILES['event_pic']['tmp_name'], $file_name, $event_pic,array (), true)) {
                   $this->session->set_flashdata('flash_message',
                       $this->common->flash_message('error', $this->upload->display_errors())
                   );
               }
           
           }
           
           
            if($this->input->post("city")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '城市为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            
            if($this->input->post("event_name")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '活动名称为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            
            if($this->input->post("begin_time") || $this->input->post("end_time")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '开始时间，结束时间为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            
            if($this->input->post("tehuiurl") || $this->input->post("tehuiid")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '特惠信息为空'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            
            
            $insertdata = array (
                'author' => $this->uid,
                'city'=>serialize($this->input->post("city")),
                 // 多标签
                'tag_id' => serialize($this->input->post("tag")),
                // 预约价，定价
                'reser_price' => 0,
                'deposit' => 0,
                
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

            $rs = $this->db->insert('tehui_yiyuan', $insertdata);
            
            if($rs){
                redirect('manage/onetehui', 'refresh');
            }else{
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
        }	
        
        $data['message_element'] = "onetehui_add";
		$this->load->view('manage', $data);
    }

    /**
     * 给活动添加机构 
     * 
     */
    public function onetehui_edit_mechanism($event_id = '' ){
       
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            return;
        }

        $city_sql = "select city from tehui_yiyuan where 1=1 and id = {$event_id}";
        $city_rs = $this->db->query($city_sql)->row_array();
        $mechanism_rs = array();
        
        if($city_rs){
            $city =unserialize($city_rs['city']);
            foreach ($city as $v){
                $mechanism_sql = "select * from company where 1=1 and state = 1 and city = '{$v}'";
                $mechanism_rs[$v] = $this->db->query($mechanism_sql)->result_array();
            }
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            redirect('manage/onetehui', 'refresh');
        }
        $data['mechanism_rs'] = $mechanism_rs;
        
        if($this->input->post("act") == 'add'){
            $mechanism_id = $this->input->post('mechanism');
            
            $indata = array(
                'mechanism' => $mechanism_id
            );
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_yiyuan', $indata);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '机构已经关联！'));
                redirect('manage/onetehui', 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            
        }

        $data['event_id'] = $event_id;
        $data['message_element'] = "onetehui_edit_mechanism";
        $this->load->view('manage', $data);
    }
    
    
    
    /**
     * 给活动添加医师
     *
     */
    public function onetehui_edit_physician($event_id = '' ){
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            return;
        }
        
        $mechanism_sql = "select mechanism from tehui_yiyuan where 1=1 and id = {$event_id}";
        $mechanism_rs = $this->db->query($mechanism_sql)->row_array();
        $physician_rs = array();
         
        if($mechanism_rs){
            $mechanism_name_sql = "select name from company where 1=1 and id = '{$mechanism_rs['mechanism']}'";
            $mechanism_name_rs  = $this->db->query($mechanism_name_sql)->row_array();
            if($mechanism_rs){
                $physician_sql = "select u.username,up.* from users as u join user_profile as up 
                                    on u.id = up.user_id and up.company = '{$mechanism_name_rs['name']}'";
                $physician_rs  = $this->db->query($physician_sql)->result_array();
            }
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '还未关联机构！'));
            redirect('manage/onetehui/onetehui_edit_mechanism/$event_id', 'refresh');
        }
        
        $data['physician_rs'] = $physician_rs;
        //print_r($data);die;
        if($this->input->post("act") == 'add'){
            if(empty($this->input->post('physician'))){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '医师为空！'));
            }
        
            $indata = array(
                'physician' => serialize($this->input->post('physician'))
            );
        
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_yiyuan', $indata);
            if($rs){
                redirect('manage/onetehui', 'refresh');
            }else{
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
        }
        
        $data['event_id'] = $event_id;
        $data['message_element'] = "onetehui_edit_physician";
        $this->load->view('manage', $data);
    }

    
    
    public function onetehui_edit($event_id = ''){
        $data['city'] = $this->db->query("select * from city")->result_array();
        
        $onetehui_sql = "select * from tehui_yiyuan where id = {$event_id}";
        $data['onetehui'] = $this->db->query("select * from city")->row_array();
        
        if($this->input->post("act") == 'edit'){
            $upload_path = 'banner/';
            $banner_pic = '';
             
            if ($_FILES['banner_path']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['picture']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',
                        $this->common->flash_message('error', $this->upload->display_errors())
                    );
                    redirect('manage/onetehui/onetehui_edit', 'refresh');
                }
                $banner_pic = $upload_path . $file_name;
            }
        
        
            if($this->input->post("city")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '城市为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            if($this->input->post("event_name")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '活动名称为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
            if($this->input->post("begin_time") || $this->input->post("end_time")){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '开始时间，结束时间为空！'));
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
        
        
        
            $updatas = array (
                'author' => $this->uid,
                'city'=>serialize($this->input->post("city")),
                // 多标签
                'tag_id' => serialize($this->input->post("tag")),
                // 预约价，定价
                'reser_price' => 0,
                'deposit' => 0,
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
            $rs = $this->db->update('tehui_yiyuan',$updatas);
            
            if($rs){
                redirect('manage/onetehui', 'refresh');
            }else{
                redirect('manage/onetehui/onetehui_add', 'refresh');
            }
        }
        
        $data['event_id'] = $event_id;
        $data['message_element'] = "onetehui_edit";
        $this->load->view('manage', $data);
    }


 /*
      * 添加相关商品
      * */
    public function onetehui_edit_product($event_id = '' ){
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        $product_sql = "select * from tehui_yiyuan where 1=1 and id = {$event_id}";
        $product_rs = $this->db->query($product_sql)->row_array();

        $data['yiyuan_data'] = array();
        
        if($product_rs){
            
            $yiyuan_sql = "select * from tehui_yiyuan where 1=1 and mechanism = {$product_rs['mechanism']} and id <> {$product_rs['id']}";
            $yiyuan_result = $this->db->query($yiyuan_sql)->result_array();

            if($yiyuan_result){
                $data['yiyuan_data'] = $yiyuan_result;
            }
        }
        

        
        if($this->input->post("act") == 'add'){
            $product = $this->input->post('product');

            if(empty($product)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '产品为空！'));
                redirect('manage/onetehui/onetehui_edit_product', 'refresh');
            }
            
            
            
            $indata = array(
                'relation_product' => serialize($product)
            );
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_yiyuan', $indata);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加成功！'));
                redirect('manage/onetehui', 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加失败！'));
                redirect('manage/onetehui/onetehui_edit_product', 'refresh');
            }
        }else{
//             $product_sql = "select * from tehui_yiyuan where 1=1 and id = {$event_id}";
//             $product_rs = $this->db->query($product_sql)->row_array();
            $data['row'] = $product_rs;
            
        }
        
        $data['id'] = $event_id;
        $data['message_element'] = "onetehui_edit_product";
        $this->load->view('manage', $data);
    }
    
    
    
    public function onetehui_item($event_id = ''){
        $note_rs = array();
        $note_sql = "select * from tehui_yiyuan where 1=1 and id = {$event_id}";
        $note_rs = $this->db->query($note_sql)->row_array();

        $item_sql = "select * from items";
        $item_rs = $this->db->query($item_sql)->result_array();
            
        $data['item_data'] = array();
        if($item_rs){
            $data['item_data'] = $item_rs;
        }
        
        $data['row'] = $note_rs;
        
            
        $data['id'] = $event_id;
        $data['message_element'] = "onetehui_item";
        $this->load->view('manage', $data);
            
        
    }
    
    public function onetehui_edit_note($item_name = '',$event_id = ''){
        
        if(empty($item_name)){
            $item_name = $this->input->post('item_name');
        }
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        
        $data['note_data'] = array();
        if(!empty($item_name)){
            $note_sql = "select * from note where 1=1 and item_name = '".$item_name."'";
            $note_rs = $this->db->query($note_sql)->result_array();
            
            if($note_rs){
                $data['note_data'] = $note_rs;
            }
        }

        if($this->input->post("act") == 'add'){
            
            $note_content = "";
            if($data['note_data']){
                $note_content = $this->input->post("note");
                if(empty($note_content)){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '日志为空！'));
                    redirect('manage/onetehui/onetehui_edit_note', 'refresh');
                    die;
                }
            }
            

            
            $indata = array(
                'relation_note_item' => $item_name,
                'relation_note' => serialize($note_content)
            );
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_yiyuan', $indata);
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '日志关联成功！'));
                redirect('manage/onetehui', 'refresh');
                die;
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '日志关联失败！'));
                redirect('manage/onetehui/onetehui_edit_note', 'refresh');
                die;
            }
        }else{
            if(empty($event_id)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '日志关联失败！'));
                redirect('manage/onetehui', 'refresh');
                die;
            }
            
            $note_sql = "select * from tehui_yiyuan where 1=1 and id = $event_id";
            $note_rs = $this->db->query($note_sql)->row_array();
            $data['row'] = $note_rs;
        }
        
        $data['id'] = $event_id;
        $data['item_name'] = $item_name;
        $data['message_element'] = "onetehui_edit_note";
        $this->load->view('manage', $data);
    }

}
?>
