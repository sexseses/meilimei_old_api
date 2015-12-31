<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class tehui extends CI_Controller {
	public function __construct() {
		parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
		//报告所有错误
		//error_reporting(E_ALL);
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
 
	}

	public function index($page = '') {
		$this->load->library('pager');
		try{
		    $sql = "SELECT tr.*,cp.name as cpname FROM tehui_relation as tr left join company as cp on tr.mechanism = cp.id WHERE 1=1 order by tehui_id DESC";
		    $data['results'] = $this->db->query($sql)->result_array();
		    if($data['results']){
		        foreach ($data['results'] as &$v){
		            $tehuisql = "SELECT title FROM team WHERE 1=1 and id = ?";
		            $title= $this->tehuiDB->query($tehuisql,$v['tehui_id'])->row_array();
		            $v['name'] = $title['title'];
		        }
		    }		    
		    $data['total_rows'] = $this->db->query($sql)->num_rows();
		}catch(Exception $e) {
		    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', $this->e->error));
        }
		
        $data['message_element'] = "tehui";
		$this->load->view('manage', $data);
	}

    public function tehui_upload_img(){
        $upload_path = $upload_path = 'banner/'.date('Y').'/'.date('m').'/'; 
        $banner_pic = '';
        if(isset($_POST['act'])&&$_POST['act']=='add'){     
        if ($_FILES['banner_path']['tmp_name']) {
             $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['banner_path']['tmp_name'], $file_name, $upload_path . $file_name)) {
//                     $this->session->set_flashdata('flash_message',
//                         $this->common->flash_message('error', $this->upload->display_errors())
//                     );
//                     redirect('manage/tehui', 'refresh');
                }
                $banner_pic = $upload_path . $file_name;
            }
        }
            
        $data['message_element'] = "tehui_add";
        $this->load->view('manage', $data);
    }

    /**
     * 添加特惠关联
     * @param int $tehui_id, 特惠后台的id号
     * @param float $reser_price, 预约价，团购价
     * @param float $deposit, 定金
     */ 
	public function tehui_add(){
	    if(isset($_POST['act'])&&$_POST['act']=='add'){
            $tehui_id = trim($this->input->post("tehuiid"));
            $reser_price =  trim($this->input->post("reser_price"));
            $deposit =  trim($this->input->post("deposit"));
           
            if(!empty($tehui_id)){
                $sql = "select * from tehui_relation where 1=1 and tehui_id = ? ";
                $cktehui = $this->db->query($sql,array($tehui_id))->row_array();
                if(!empty($cktehui)){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '已经有该特惠！'));
                    redirect('manage/tehui', 'refresh');
                }
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '特惠id不能为空！'));
                redirect('manage/tehui/tehui_add', 'refresh');
            }
 

        
            $updatas = array (
                'tehui_id' => $tehui_id,
                'reser_price' => $reser_price,
                'deposit' => $deposit,
                'sub_ids' => implode(',',$this->input->post('cate'))
            );

            $rs = $this->db->insert('tehui_relation',$updatas);
            
            if($rs){
                $teupdatas = array(
                    // 预约价，定价
                    'reser_price' => $reser_price,
                    'deposit' => $deposit,
                    'newversion' => 1,
                		'relation' => 1,
                    'sub_ids' => implode(',',$this->input->post('cate'))
                );
                
                $this->tehuiDB->where('id',$this->input->post("tehuiid"));
                $teamrs = $this->tehuiDB->update('team',$teupdatas);
                if($teamrs){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '关联成功！'));
                    redirect('manage/tehui', 'refresh');
                }else{
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '关联失败！'));
                    redirect('manage/tehui', 'refresh');
                }
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                redirect('manage/tehui/tehui_add', 'refresh');
            }
        }else{
	        $data['city'] = $this->db->query("select * from city")->result_array();
            $data['cate'] = $this->tehuiDB->query("select * from category where  zone = ? and  fid <> ?",array('group',0))->result_array();
	        $data['tehui'] =  $this->tehuiDB->query("select * from team")->result_array();

	        $data['message_element'] = "tehui_add";
	        $this->load->view('manage', $data);
	    }
		
	}

	public function tehui_edit($event_id = ''){
	    if(empty($event_id)){
	        $event_id = $this->input->post("event_id");
	    }
	    if(empty($event_id)){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误，特惠id不能为空！'));
	        redirect('manage/tehui', 'refresh');
	    }

        if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
	        $tehui_id = trim($this->input->post("tehuiid"));
	        $reser_price =  trim($this->input->post("reser_price"));
	        $deposit =  trim($this->input->post("deposit"));

            $updatas = array (
                'tehui_id' => $tehui_id,
                // 预约价，定价
                'reser_price' => $reser_price,
                'deposit' => $deposit,
                'sub_ids' => implode(',',$this->input->post('cate'))
            );
            
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_relation',$updatas);
            
            if($rs){
                $teupdatas = array(
                    // 预约价，定价
                    'reser_price' => $reser_price,
                    'deposit' => $deposit,
                    'sub_ids' => implode(',',$this->input->post('cate'))
                );
                
                $this->tehuiDB->where('id',$tehui_id);
                $teamrs = $this->tehuiDB->update('team',$teupdatas);
                if(teamrs){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '关联成功'));
                    redirect('manage/tehui', 'refresh');
                }else{
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                    redirect('manage/tehui', 'refresh');
                }
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                redirect("manage/tehui/tehui_edit/{$event_id}", 'refresh');
            }
	    }
	    
	    $data['row'] = $this->db->query("select * from tehui_relation where id = {$event_id} ")->row_array();
        $data['cate'] = $this->tehuiDB->query("select * from category where  zone = ? and  fid <> ?",array('group',0))->result_array();
        $data['tehui'] =  $this->tehuiDB->query("select * from team")->result_array();
	    $data['message_element'] = "tehui_edit";
	    $data['event_id'] = $event_id;
	    $this->load->view('manage', $data);
	}
	
	/**
     * 给活动添加机构 
     * 
     */
    public function tehui_edit_mechanism($event_id = '' ){
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            redirect('manage/tehui', 'refresh');
        }
        
        $event_sql = "select tehui_id from tehui_relation where 1=1 and id = {$event_id}";
        $result = $this->db->query($event_sql)->row_array();
        
        $mechanism_rs = array();
        
        if($result){
            $team_sql = "select p.username from team as t join partner as p on t.partner_id = p.id where t.id = ?";
            $team_rs = $this->tehuiDB->query($team_sql,array($result['tehui_id']))->row_array();
             
            if(count($team_rs)>0){
                $mechanism_sql = "select id,name from company where 1=1  and name = ?";
                $mechanism_rs = $this->db->query($mechanism_sql,array($team_rs['username']))->result_array();
            }

            if(empty($mechanism_rs)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '没有匹配机构'));
                redirect('manage/tehui', 'refresh');
            }else{
                $data['results'] = $mechanism_rs;
            }
        }
        
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $mechanism = $this->input->post('mechanism');
            
            if(empty($mechanism)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '机构为空！'));
                redirect('manage/tehui', 'refresh');
            }
            
            $indata = array(
                'mechanism' => $this->input->post('mechanism')
            );
           
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_relation', $indata);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '修改成功！'));
                redirect('manage/tehui', 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '修改失败！'));
                redirect('manage/tehui/tehui_add', 'refresh');
            }
            
        }
        
            
        $sql = "select `mechanism` from tehui_relation where id = {$event_id}";
        $result = $this->db->query($sql)->row_array();
        $data['mechanism'] = $result['mechanism'];
        $data['event_id'] = $event_id;
        $data['message_element'] = "tehui_edit_mechanism";
        $this->load->view('manage', $data);
    }

    /**
     * 给活动添加医师
     *
     */
    public function tehui_edit_physician($event_id = '' ){
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            redirect('manage/tehui', 'refresh');
        }
        $event_sql = "select tehui_id from tehui_relation where 1=1 and id = {$event_id}";
        $result = $this->db->query($event_sql)->row_array();
        $mechanism_rs = array();
        $mechanism_sql = "select p.username from team as t join partner as p on t.partner_id = p.id where t.id = ?";
        $mechanism_rs = $this->tehuiDB->query($mechanism_sql,array($result['tehui_id']))->row_array();
        
        
        $physician_rs = array();
        if($mechanism_rs){
            $mechanism_sql = "select id,name from company where 1=1  and name = ?";
            $mechanism_rs = $this->db->query($mechanism_sql,array($mechanism_rs['username']))->result_array();
            if(count($mechanism_rs)<=0){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '没有关联到机构！'));
                redirect('manage/tehui', 'refresh');
            }else{
                $physician_sql = "select u.alias,u.username,up.* from users as u join user_profile as up on u.id = up.user_id and up.company = '{$mechanism_rs[0]['name']}'";
                $physician_rs  = $this->db->query($physician_sql)->result_array();
                
                if(count($physician_rs) > 0){
                    $data['results'] = $physician_rs;
                }else{
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '该机构没有医师！'));
                    redirect('manage/tehui', 'refresh');
                }
            }
        }

        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $physician = $this->input->post('physician');
            if(empty($physician)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '没有选择医师！'));
                redirect("manage/tehui/tehui_edit_physician/$event_id", 'refresh');
            }
            
            $indata = array(
                'physician' => serialize($this->input->post('physician'))
            );
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_relation', $indata);
            if($rs){
                redirect('manage/tehui', 'refresh');
            }else{
                redirect('manage/tehui/tehui_add', 'refresh');
            }
        }else{
            $sql = "select physician from tehui_relation where id = {$event_id}";
            $result = $this->db->query($sql)->result_array();
            $data['physician'] = unserialize($result['0']['physician']);
        }
        
        $data['event_id'] = $event_id;
        $data['message_element'] = "tehui_edit_physician";
        $this->load->view('manage', $data);
    }
    
    
     /*
      * 添加相关商品
      * */
    public function tehui_edit_product($event_id = '' ){
        if(empty($event_id)){
            $event_id = $this->input->post('event_id');
        }
        
        if(empty($event_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            redirect('manage/tehui', 'refresh');
        }
        
        $tehui_sql = "select tehui_id from tehui_relation where 1=1 and id = {$event_id}";
        $tehui_rs = $this->db->query($tehui_sql)->row_array();
        
        $product_rs = array();
        $data['event_data'] = array();
        
        $time = time();
        if($tehui_rs){
            $teamproduct_sql = "select id,title from team where partner_id in (select partner_id from team where 1=1 and id = ?) and partner_id <>    {$tehui_rs['tehui_id']} and team_type='normal' and begin_time <= '{$time}' and end_time >= '{$time}'";
            $teamproduct_rs = $this->tehuiDB->query($teamproduct_sql,array($tehui_rs['tehui_id']))->result_array();

            if(count($teamproduct_rs)>0){
                $data['product_data'] = $teamproduct_rs;
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '没有相关产品！'));
                redirect('manage/tehui', 'refresh');
            }
        }
        
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $product = $this->input->post('product');
            
            if(empty($product)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '产品未选择！'));
                redirect('manage/tehui/tehui_edit_product', 'refresh');
            }

            $indata = array(
                'relation_product' => serialize($product)
            );
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_relation', $indata);
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加成功！'));
                redirect('manage/tehui', 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加失败！'));
                redirect('manage/tehui/tehui_edit_product', 'refresh');
            }
        }
        
        $product_sql = "select * from tehui_relation where 1=1 and id = {$event_id}";
        $data['row'] = $this->db->query($product_sql)->row_array();
        $data['event_id'] = $event_id;
        $data['message_element'] = "teihui_edit_product";
        $this->load->view('manage', $data);
    }
    
    
    
    public function tehui_item($event_id = ''){
        $note_rs = array();
        $note_sql = "select * from tehui_relation where 1=1 and id = {$event_id}";
        $note_rs = $this->db->query($note_sql)->row_array();

        $item_sql = "select * from new_items";
        $item_rs = $this->db->query($item_sql)->result_array();
            
        $data['item_data'] = array();
        if($item_rs){
            $data['item_data'] = $item_rs;
        }
        
        $data['row'] = $note_rs;
        
            
        $data['id'] = $event_id;
        $data['message_element'] = "tehui_item";
        $this->load->view('manage', $data);
            
        
    }
    
    public function tehui_edit_note($item_name = '',$event_id = ''){
        
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
                    redirect('manage/tehui/tehui_edit_note', 'refresh');
                    die;
                }
            }
            

            
            $indata = array(
                'relation_note_item' => $item_name,
                'relation_note' => serialize($note_content)
            );
            
            $this->db->where('id', $event_id);
            $rs = $this->db->update('tehui_relation', $indata);
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '日志关联成功！'));
                redirect('manage/tehui', 'refresh');
                die;
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '日志关联失败！'));
                redirect('manage/tehui/tehui_edit_note', 'refresh');
                die;
            }
        }else{
            if(empty($event_id)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '日志关联失败！'));
                redirect('manage/tehui/tehui_edit_note', 'refresh');
                die;
            }
            
            $note_sql = "select * from tehui_relation where 1=1 and id = $event_id";
            $note_rs = $this->db->query($note_sql)->row_array();
            $data['row'] = $note_rs;
        }
        
        $data['id'] = $event_id;
        $data['item_name'] = $item_name;
        $data['message_element'] = "tehui_edit_note";
        $this->load->view('manage', $data);
    }
    
    public function tehui_edit_items($event_id=''){
        $item_sql = "select * from items";
        $item_rs = $this->db->query($item_sql)->result_array();
        $data['item_data'] = $item_rs;
        if($this->input->post("act") == 'add'){
            //需要修改 
            $items = $this->input->post('items');

            $tehuisql = "select tehui_id from tehui_relation where 1=1 and id = ?";
            $tehuiid = $this->db->query($tehuisql,array($event_id))->row_array();
            
            
            //$this->db->where('id', $event_id);
            //$rs = $this->db->update('tehui_relation', $indata);   
            //$items = serialize($items);
            
            $sql = " UPDATE tehui_relation SET items = ? WHERE id = ?";
            $rs = $this->db->query($sql,array(serialize($items),$event_id));
            
            $sql = " UPDATE team SET items = ? WHERE id = ?";
            $rs = $this->tehuiDB->query($sql,array(serialize($items),$tehuiid['tehui_id']));
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加成功！'));
                redirect('manage/tehui', 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('sussess', '添加失败！'));
                redirect('manage/tehui/tehui_edit_items', 'refresh');
            }
        }else{
            $items_sql = "select * from tehui_relation where 1=1 and id = {$event_id}";
            $items_rs = $this->db->query($items_sql)->row_array();
            $data['row'] = $items_rs;
        
        }
        
        $data['id'] = $event_id;
        $data['message_element'] = "tehui_edit_items";
        $this->load->view('manage', $data);
    }
    
    public function flashSale($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/tehui", 'refresh');
        }
    
        $tehui_sql = "select * from  tehui_relation  where 1=1 and id = ?";
        $tehui_rs = $this->db->query($tehui_sql,array($id))->row_array();
        
        $team_sql = "select * from team where 1=1 and id = ?";
        $team_rs = $this->tehuiDB->query($team_sql,array($tehui_rs['tehui_id']))->row_array();

        if($team_rs['flashSale'] == 0){
            $updata['flashSale'] = 1;
        }else{
            $updata['flashSale'] = 0;
        }

        $this->tehuiDB->where('id',$team_rs['id']);
        $rs = $this->tehuiDB->update('team',$updata);
        
        $this->db->where('id',$id);
        $rs = $this->db->update('tehui_relation',$updata);

        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "修改成功！"));
            redirect("manage/tehui", 'refresh');
        }
        
    }
}