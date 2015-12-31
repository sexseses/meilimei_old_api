<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class event extends CI_Controller {
    protected  $eventDB;
    public function __construct() {
        parent :: __construct();
        $this->eventDB = $this->load->database('event', TRUE);
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
        
        $sql = "select * from tehui_event where 1=1 and display= 1 order by id desc limit {$offset},{$per_page}";
        $event_rs = $this->eventDB->query($sql)->result_array();
        $data['event_rs'] = $event_rs;
        $data['message_element'] = "event";
        $this->load->view('manage', $data);
    }
    
    /*
     * 添加闪购
     * */
    public function add(){
        if(isset($_POST['act']) && $this->input->post("act") == 'add'){
            $event_name = $this->input->post('event_name');
            $begin_time = strtotime($this->input->post('begin'));
            $end_time = strtotime($this->input->post('end'));
            $subject = $this->input->post('subject');
            $price = $this->input->post('price');
            $sms = $this->input->post('sms');
    
            if(empty($event_name) || empty($begin_time) || empty($end_time) ){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/event/add", 'refresh');
            }
    
            $insertData = array(
                'event_name' => $event_name,
                'begin_time' => $begin_time,
                'end_time' => $end_time,
                'subject' => $subject,
                'price' => $price,
                'sms' => $sms,
                'create_time' => time()
            );
    
            $rs = $this->eventDB->insert('tehui_event',$insertData);
    
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功！"));
                redirect("manage/event", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("manage/event/add", 'refresh');
            }
        }
    
        $data['message_element'] = "event_add";
        $this->load->view('manage', $data);
    }
    
    public function edit($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        $sql = "select * from tehui_event where 1=1 and id = ?";
        $event_rs = $this->eventDB->query($sql,array($id))->row_array();
        
        if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
            $event_name = $this->input->post('event_name');
            $begin_time = strtotime($this->input->post('begin'));
            $end_time = strtotime($this->input->post('end'));
            $subject = $this->input->post('subject');
            $price = $this->input->post('price');
            $sms = $this->input->post('sms');
            
            if(empty($event_name) || empty($begin_time) || empty($end_time) ){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数不能为空！"));
                redirect("manage/event/add", 'refresh');
            }
            
            $upData = array(
                'event_name' => $event_name,
                'begin_time' => $begin_time,
                'end_time' => $end_time,
                'subject' => $subject,
                'price' => $price,
                'sms' => $sms,
                'create_time' => time()
            );
            
            $this->eventDB->where('id',$id);
            $rs = $this->eventDB->update('tehui_event',$upData);
            
            if($rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "修改成功！"));
                redirect("manage/event", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "修改失败！"));
                redirect("manage/event/edit", 'refresh');
            }
        }
        
        $data['id'] = $id;
        $data['event_rs'] = $event_rs;
        $data['message_element'] = "event_edit";
        $this->load->view('manage', $data);
    }
    
  
    public function collection($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        $sql = "select * from tehui_event_collection where 1=1 and event_id = ?";
        $collection_rs = $this->eventDB->query($sql,array($id))->result();
        
        $data['coll_rs'] = $collection_rs;
        $data['message_element'] = "event_collection";
        $this->load->view('manage', $data);
    }
    
    public function del($id = ''){
        if(empty($id)){
            $id = $this->input->post('id');
        }
        
        $upData['display'] = 0;
        
        $this->eventDB->where('id',$id);
        $rs = $this->eventDB->update('tehui_event',$upData);
        
        
        if($rs){
          $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "修改成功！"));
          redirect("manage/event", 'refresh');
        }
    }
}