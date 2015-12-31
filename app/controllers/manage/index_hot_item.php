<?php
class index_hot_item extends CI_Controller {
    private $notlogin = true,$uid='';
    public function __construct() {
        parent :: __construct();
        $this->eventDB = $this->load->database('event', TRUE);
        if ($this->wen_auth->get_role_id() == 16) {
            $this->notlogin = false;
            $this->uid=$this->wen_auth->get_user_id();
        } else {
            redirect('');
        }
        $this->load->model('remote');
        $this->load->model('privilege');
        $this->privilege->init($this->uid);
//         if(!$this->privilege->judge('')){
//             die('Not Allow');
//         }
    }
    
    public function index(){
        $sql = "SELECT * FROM index_hot_item WHERE 1=1 AND display=1";
        $rs = $this -> eventDB -> query($sql) -> result_array();
        if($rs){
            $data['results'] = $rs;
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "index_hot_item";
            $this->load->view('manage', $data);
        }
       
    }
    
    public function itemadd($id=""){
        if(!empty($id)){
            $sql = "SELECT * FROM index_hot_item WHERE 1=1 AND id=?";
            $rs = $this -> eventDB -> query($sql,array($id)) -> row_array();
            $data['results'] = $rs;
            $data['item_city'] = explode(",", $rs['item_city']);
        }
        $data['city'] = $this->db->query("select * from city")->result_array();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "index_hot_itemadd";
        $this->load->view('manage', $data);
        
    }
    
    
    //上传图片
    public function itemuplode(){
        $uid="";$hdpic="";
        $item_name = $this->input->post("item_name");
        $level = $this->input->post("level");
        $city = $this->input->post("city");
        $uid = $this->input->post("uid");
        $hdpic = $this->input->post("hdpic");
        $pic = $_FILES == "" ?$hdpic:$_FILES;
        if(!empty($item_name) && !empty($level) && !empty($city)){
            $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
            $filename = uniqid(time(), false) . '.jpg';
            $tmppath = $upload_path . $filename;
            if($this->remote->cp($_FILES['picfile']['tmp_name'],$filename,$tmppath)){
                $hotadd['pic'] = $tmppath;
            }elseif(!empty($hdpic) && !$this->remote->cp($_FILES['picfile']['tmp_name'],$filename,$tmppath)){
                $hotadd['pic'] = $hdpic;
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '图片参数错误！'));
            redirect("manage/index_hot_item/itemadd/{$uid}", 'refresh');
            }
            $imm = implode(",",$city);
            $hotadd['item_name'] = $item_name;
            $hotadd['item_city'] = $imm;
            $hotadd['level'] = $level;
            $hotadd['create_time'] = time();
            if(empty($uid)){
                $add = $this->eventDB->insert('index_hot_item',$hotadd);
            }else{
                $this -> eventDB -> where('id' ,$uid );
                $upd = $this->eventDB->update('index_hot_item',$hotadd);
            }
            if($add){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加项目成功！'));
                redirect("manage/index_hot_item/itemadd/{$uid}", 'refresh');
            }
            if($upd){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '修改项目成功！'));
                redirect("manage/index_hot_item/itemadd/{$uid}", 'refresh');
            }
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
            redirect("manage/index_hot_item/itemadd/{$uid}", 'refresh');
        }else{
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '必要参数未填写！'));
            redirect("manage/index_hot_item/itemadd/{$uid}", 'refresh');
        }
            
    }
    
    public function del($uid=""){
        if(!empty($uid)){
            $this -> eventDB -> where('id' ,$uid );
            $dup = $this->eventDB->update('index_hot_item',array(display => '0'));
            if($dup){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '删除成功！'));
                redirect("manage/index_hot_item/", 'refresh');
            }
        }
        
    }
    
    
}