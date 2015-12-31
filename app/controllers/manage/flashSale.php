<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class flashSale extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
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
    
    public function index($page = ''){
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
        
        $sql = "select * from flash_sale where 1=1 and display = 1 order by id desc ";
        $sale_rs = $this->db->query($sql)->result_array();
        
       
        foreach($sale_rs as &$v){
        	if($v['banner_key'] != NULL){
        		$v['banner'] = "http://7xkdi8.com1.z0.glb.clouddn.com/".$v['banner_key'];
        	}else{
        		$v['banner'] = $this->remote->show($v['banner']);
        	}
        	if($v['lbanner_key'] != NULL){
        		$v['lbanner'] = "http://7xkdi8.com1.z0.glb.clouddn.com/".$v['lbanner_key'];
        	}else{
        		$v['lbanner'] = $this->remote->show($v['lbanner']);
        	}
        }
        
        $data['sale_rs'] = $sale_rs;
        $data['message_element'] = "flashSale";
        $this->load->view('manage', $data);
    }
    
    
    
    /*
     * 添加闪购
     * */
    public function add(){
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $begin = strtotime(trim($this->input->post('begin')));
            $end = strtotime(trim($this->input->post('end')));
            if(empty($begin) || empty($end)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/flashSale/add", 'refresh');
            }
            
            $title = $this->input->post('title');
            $context = $this->input->post('context');
            $type = $this->input->post('type');
            $type_id = $this->input->post('type_id');
            $city = $this->input->post('city');
            
            $discount = $this->input->post('discount');
            $vbuy = $this->input->post('vbuy');
            $level = $this->input->post('level');
            $share_title = $this->input->post('share_title');
            
            if(empty($title) || empty($context)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/flashSale/add", 'refresh');
            }
            $share_pic = "";
            $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
            if($_FILES['share_pic']['tmp_name']){
                $upload_rs = $this->remote->upload_qiniu($_FILES['share_pic']['tmp_name'], $file_name);
                $share_pic = $upload_rs['key'];
            }

            
            $insertData = array(
                'title' => $title,
                'context' => $context,
                'type' => $type,
                'type_id' => $type_id,
                'city' => $city,
                'begin' => $begin,
                'end' => $end,
                'discount' => $discount,
                'level' => $level,
                'vbuy' => $vbuy,
            	'share_pic' => $share_pic,
            	'share_title' => $share_title,
                'time' => time()
            );
            
            $rs = $this->db->insert('flash_sale',$insertData);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功！"));
                redirect("manage/flashSale", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("manage/flashSale/add", 'refresh');
            }
        }
        
        $data['message_element'] = "flashSale_add";
        $this->load->view('manage', $data);
    }
    
    public function edit($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/flashSale", 'refresh');
        }
        if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
            $begin = strtotime(trim($this->input->post('begin')));
            $end = strtotime(trim($this->input->post('end')));
            if(empty($begin) || empty($end)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/flashSale/edit/$id", 'refresh');
            }
            
            $title = $this->input->post('title');
            $context = $this->input->post('context');
            $type = $this->input->post('type');
            $type_id = $this->input->post('type_id');
            $discount = $this->input->post('discount');
            $vbuy = $this->input->post('vbuy');
            $city = $this->input->post('city');
            $level = $this->input->post('level');
            $share_title = $this->input->post('share_title');
            
        
            if(empty($title) || empty($context) ){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/flashSale/add", 'refresh');
            }
            
            if ($_FILES['share_pic']['tmp_name']) {
	            $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
	            $upload_rs = $this->remote->upload_qiniu($_FILES['share_pic']['tmp_name'], $file_name);
	            if(empty($upload_rs['key'])){
	            	$this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
	            	redirect('manage/flashSale/edit_banner/$id', 'refresh');
	            }else{
	            	$share_pic = $upload_rs['key'];
	            }
            }
            
        
            
            $insertData = array(
                'title' => $title,
                'context' => $context,
                'type' => $type,
                'type_id' => $type_id,
                'city' => $city,
                'begin' => $begin,
                'end' => $end,
                'discount' => $discount,
                'vbuy' => $vbuy,
                'level' => $level,
            	'share_pic' => $share_pic,
            	'share_title' => $share_title,
                'time' => time()
            );
            
            $this->db->where('id',$id);
            $rs = $this->db->update('flash_sale',$insertData);
        
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "更新成功！"));
                redirect("manage/flashSale", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "更新失败！"));
                redirect("manage/flashSale/edit/$id", 'refresh');
            }
        }
            
        $sql = "select * from flash_sale where 1=1 and id = ?";
        $sale_rs = $this->db->query($sql,array($id))->row_array();
        $data['sale_rs'] = $sale_rs;
        
        
        $data['id'] = $id;
        $data['message_element'] = "flashSale_edit";
        $this->load->view('manage', $data);
    }
    
    function del($id = ''){
        if(empty($id)){
            $id = $this->input->get('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
    
        $upData = array(
            'display' => 0,
        );
    
        $this->db->where('id',$id);
        $rs = $this->db->update('flash_sale',$upData);
    
        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "闪购删除成功！"));
            redirect("manage/flashSale", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "闪购删除失败！"));
            redirect("manage/flashSale", 'refresh');
        }
    
    }
    
    public function edit_banner($id=''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
        $banner_pic = '';
        
        if(isset($_POST['act']) && $_POST['act']=='edit'){
            if ($_FILES['banner']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                
                $upload_rs = $this->remote->upload_qiniu($_FILES['banner']['tmp_name'], $file_name);
                
                if(empty($upload_rs['key'])){
                	$this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                	redirect('manage/flashSale/edit_banner/$id', 'refresh');
                }else{
                	$banner_pic = $upload_rs['key'];
                }
                

                $upData = array(
                	'banner_key' => $banner_pic,
                    'banner' => $banner_pic
                );
                
                $this->db->where('id',$id);
                $rs = $this->db->update('flash_sale',$upData);
            }
            
            if ($_FILES['lbanner']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                $upload_rs = $this->remote->upload_qiniu($_FILES['lbanner']['tmp_name'], $file_name);
                if (empty($upload_rs['key'])) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error', '上传失败！'));
                    redirect('manage/flashSale/edit_banner/$id', 'refresh');
                }else{
                	$banner_pic = $upload_rs['key'];
                }
            
            
                $upData = array(
                	'lbanner_key' => $banner_pic,
                    'lbanner' => $banner_pic
                );
            
                $this->db->where('id',$id);
                $rs = $this->db->update('flash_sale',$upData);
            }
        }
        
            
        $sql = "select * from flash_sale where 1=1 and id = ?";
        $sale_rs = $this->db->query($sql,array($id))->row_array();
        if(!empty($sale_rs['banner_key'])){
        	$sale_rs['banner'] = "http://7xkdi8.com1.z0.glb.clouddn.com/".$sale_rs['banner_key'];
        }else{
        	$sale_rs['banner'] = $this->remote->show($sale_rs['banner']);
        }
        if(!empty($sale_rs['lbanner_key'])){
        	$sale_rs['lbanner'] = "http://7xkdi8.com1.z0.glb.clouddn.com/".$sale_rs['lbanner_key'];
        }else{
        	$sale_rs['lbanner'] = $this->remote->show($sale_rs['lbanner']);
        }
        
        
        $data['sale_rs'] = $sale_rs;
        $data['id'] = $id;
        $data['message_element'] = "flashSale_banner";
        $this->load->view('manage', $data);
    }
    
    public function del_banner($id = ''){
        if(empty($id)){
            $id = $this->input->get('id');
        }
        
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $type = $this->input->get('type');
        
        if(empty($type)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale/edit_banner/$id", 'refresh');
        }
        
        if($type == 1){
            $upData = array(
                'banner' => NULL
            );
        }else{
            $upData = array(
               'lbanner' => NULL
            );
        }
        
        
        $this->db->where('id',$id);
        $rs = $this->db->update('flash_sale',$upData);
        
        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "图片更新成功！"));
            redirect("manage/flashSale/edit_banner/$id", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "图片更新失败！"));
            redirect("manage/flashSale/edit_banner/$id", 'refresh');
        }
    }
    
    public function add_product($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $time = time();
        $fields = 't.id,t.title';
        $condition = "t.team_type='normal' and t.end_time >= '{$time}'";
        $order = ' t.begin_time DESC, t.id DESC';
        $result['tehui_rs'] = array ();
        $tehui_sql = "SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} ";
        $result['tehui_rs'] = $this->tehuiDB->query($tehui_sql)->result_array();
        
        if((isset($_POST['act'])&&$_POST['act']=='add')){
            $product = $this->input->post('pro');
            
            if(empty($product)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "商品不能为空！"));
                redirect("manage/flashSale/add_product/$id", 'refresh');
            }

            foreach ($product as $pro){
                $upData = array(
                    'fs_id' => $id,
                    'p_id' => $pro
                );

                $tehui_sql = "select * from flash_sale_tehui where fs_id = ? and p_id = ?";
                $tehui_rs = $this->db->query($tehui_sql,array($id,$pro))->row_array();
                if(empty($tehui_rs)){
                    $this->db->insert('flash_sale_tehui',$upData);
                }
            }
            
            $sproduct = serialize($product);
            $fsData = array(
                'product' => $sproduct
            );
            
            $this->db->where('id',$id);
            $rs = $this->db->update('flash_sale',$fsData);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "商品更新成功！"));
                redirect("manage/flashSale", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "商品更新失败！"));
                redirect("manage/flashSale/add_product/$id", 'refresh');
            }
        }
        $sql = "select * from flash_sale where 1=1 and id = ?";
        $sale_rs = $this->db->query($sql,array($id))->row_array();
        $data['sale_rs'] = $sale_rs;
        
        $data['id'] = $id;
        $data['tehui_rs'] = $result['tehui_rs'];
        $data['message_element'] = "flashSale_product";
        $this->load->view('manage', $data);
        
    }
    
    public function new_edit_product($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        
        $t_id_sql = "select * from flash_sale_tehui where 1=1 and display = 1 and fs_id = {$id} ";
        $t_id_rs = $this->db->query($t_id_sql)->result_array();
        

        
        if(empty($t_id_rs)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "请先添加商品！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $t_id_arr = array();
        foreach ($t_id_rs as $t_id){
            $t_id_arr[] = $t_id['p_id'];
        }
        
        //print_r($t_id_arr);die;
        $t_id_str = implode(',', $t_id_arr);
        $condition .= " and t.id in ({$t_id_str})";
        $time = time();
        $fields = 't.id,t.title';
        $condition .= " and t.team_type='normal' and t.end_time >= '{$time}'";
        $order = ' t.begin_time DESC, t.id DESC';
        $result['tehui_rs'] = array ();
        $tehui_sql = "SELECT {$fields} FROM team as t WHERE 1=1 {$condition} ORDER by {$order} ";
        $result['tehui_rs'] = $this->tehuiDB->query($tehui_sql)->result_array();
        
        foreach ($result['tehui_rs'] as &$ts){
            foreach ($t_id_rs as $t_id){
                if($ts['id'] == $t_id['p_id']){
                    $ts['level'] = $t_id['level'];
                }
            }
            
        }
        
        if((isset($_POST['act'])&&$_POST['act']=='edit')){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $data['id'] = $id;
        $data['tehui_rs'] = $result['tehui_rs'];
        $data['message_element'] = "flashSale_product_edit";
        $this->load->view('manage', $data);
    }
    
    public function del_product($fs_id='',$p_id = ''){
        if(empty($fs_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale)", 'refresh');
        }
        if(empty($p_id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale/new_edit_product/$fs_id)", 'refresh');
        }
        
        
        $sql = "select * from flash_sale_tehui where p_id = $p_id and fs_id = $fs_id";
        $rs = $this->db->query($sql)->row_array();
        if($rs){
            $del_sql = "update flash_sale_tehui set display=0 where p_id = $p_id and fs_id = $fs_id";
            $del_result = $this->db->query($del_sql);
            if($del_result){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "删除成功！"));
                redirect("manage/flashSale/new_edit_product/$fs_id", 'refresh');
            }
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale/new_edit_product/$fs_id", 'refresh');
        }
    }
    
    
    public function edit_product($fs_id='',$p_id = ''){

        $level = $this->input->post('level');
       
        if(empty($fs_id) || empty($p_id) || empty($level)){
            echo false;
            return;
        }

        $sql = "select * from flash_sale_tehui where p_id = $p_id and fs_id = $fs_id";
        $rs = $this->db->query($sql)->row_array();
         
        if($rs){
            $up_sql = "update flash_sale_tehui set level = $level where p_id = $p_id and fs_id = $fs_id";
            $up_result = $this->db->query($up_sql);
            if($up_result){
                echo true;
                return;
            }
        } 
         
    }
    
    public function IndexTopBanner(){
        $banner_sql = "select fsib.*,fs.title from flash_sale_index_banner fsib join flash_sale fs on fsib.tehui_id = fs.id where 1=1 and fsib.display = 1";
        $banner_rs = $this->db->query($banner_sql)->result_array();
        
 
        $data['banner_rs'] = $banner_rs;
        $data['message_element'] = "flashSale_topbanner";
        $this->load->view('manage', $data);
    }
    
    public function IndexTopBanner_add(){
        $banner_sql = "select * from flash_sale_index_banner where 1=1 and display = 1";
        $banner_rs = $this->db->query($banner_sql)->result_array();
        if(count($banner_rs) >= 4){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "数量超限制无法在添加置顶banner"));
            redirect("manage/flashSale/IndexTopBanner", 'refresh');
        } 
        
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
            $banner_pic = '';
            $begin = strtotime(trim($this->input->post('begin')));
            $end = strtotime(trim($this->input->post('end')));
            
            if(empty($begin) || empty($end)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "时间不能为空！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
            
            $insertData = array();
            
            if ($_FILES['banner']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['banner']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                    redirect('manage/flashSale/IndexTopBanner_add', 'refresh');
                }
                $banner_pic = $upload_path . $file_name;
                $insertData['banner_pic'] = $banner_pic;
            }    
        
            $tehui_id = $this->input->post('tehui_id');

            if(empty($tehui_id)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "特惠参数不能为空！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
        
            $insertData['tehui_id'] = $tehui_id;
            $insertData['begin'] = $begin;
            $insertData['end'] = $end;
            $insertData['createtime'] = time();
        
            $rs = $this->db->insert('flash_sale_index_banner',$insertData);
        
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功！"));
                redirect("manage/flashSale/indexTopBanner", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
        }
        $data['message_element'] = "flashSale_topbanner_add";
        $this->load->view('manage', $data);
    }
    
    public function IndexTopBanner_edit($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale", 'refresh');
        }
        
        $banner_sql = "select fsib.*,fs.title from flash_sale_index_banner fsib join flash_sale fs on fsib.tehui_id = fs.id where 1=1 and fsib.display = 1 and fsib.id = ?";
        //$banner_sql = "select * from flash_sale_index_banner where 1=1 and id = ?";
        $banner_rs = $this->db->query($banner_sql,array($id))->row_array();
        
        if(empty($banner_rs)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数错误！"));
            redirect("manage/flashSale/IndexTopBanner", 'refresh');
        }
        
        
        if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
            $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
            $banner_pic = '';
            $begin = strtotime(trim($this->input->post('begin')));
            $end = strtotime(trim($this->input->post('end')));
    
            if(empty($begin) || empty($end)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "时间不能为空！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
    
            $insertData = array();
    
            if ($_FILES['banner']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['banner']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error','上传失败！'));
                    redirect('manage/flashSale/edit_banner/$id', 'refresh');
                }
                $banner_pic = $upload_path . $file_name;
                $insertData['banner_pic'] = $banner_pic;
            }
    
            $tehui_id = $this->input->post('tehui_id');
    
            if(empty($tehui_id)){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "特惠参数不能为空！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
    
            $insertData['tehui_id'] = $tehui_id;
            $insertData['begin'] = $begin;
            $insertData['end'] = $end;
            $insertData['createtime'] = time();
    
            $rs = $this->db->insert('flash_sale_index_banner',$insertData);
    
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功！"));
                redirect("manage/flashSale/indexTopBanner", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("manage/flashSale/IndexTopBanner_add", 'refresh');
            }
        }
        
        

        $data['id'] = $id;
        $data['banner_rs'] = $banner_rs;
        $data['message_element'] = "flashSale_topbanner_edit";
        $this->load->view('manage', $data);
        
    }
    
    public function IndexTopBanner_del($id=''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
            redirect("manage/flashSale/IndexTopBanner", 'refresh');
        }
        $upData = array(
            'display' => 0,
        );
        
        $this->db->where('id',$id);
        $rs = $this->db->update('flash_sale_index_banner',$upData);
        
        if($rs){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "闪购删除成功！"));
            redirect("manage/flashSale/IndexTopBanner", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "闪购删除失败！"));
            redirect("manage/flashSale/IndexTopBanner", 'refresh');
        }
        
    }
    
}

