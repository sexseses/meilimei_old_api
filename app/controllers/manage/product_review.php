<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class product_review extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
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
        
        $this->load->library('pager');
        $this->load->library('form_validation');
        $this->load->library('yisheng');
        $this->load->helper('file');
        $this->load->model('users_model');
        $this->load->model('privilege');
        $this->load->model('remote');
        $this->privilege->init($this->uid);
    
    }
    
    public function index(){
        $page = $this->input->get('page');
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
         
        
        $team_sql = "select * from team_temp where 1=1 order by id DESC  limit $offset , $per_page";
        $team_rs = $this->tehuiDB->query($team_sql)->result_array();
        
        $data['total_rows'] = $this->tehuiDB->query("select * from team_temp")->num_rows();
        
        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>5,
 
            'base_url'=>'manage/product_review/index',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['team_rs'] = $team_rs;
        $data['message_element'] = "product_review";
        $this->load->view('manage', $data);
    }
    
    public function noreviewlist(){
        $page = $this->input->get('page');
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
         
        
        $team_sql = "select * from team_temp where 1=1 and review <> ? order by id DESC limit $offset , $per_page";
        $team_rs = $this->tehuiDB->query($team_sql,array(1))->result_array();
        
        $data['total_rows'] = $this->tehuiDB->query("select * from team_temp where 1=1 and review <> ?",array(1))->num_rows();
        
        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>5,
 
            'base_url'=>'manage/product_review/noreviewlist',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['team_rs'] = $team_rs;
        $data['message_element'] = "product_review";
        $this->load->view('manage', $data);
    }
    
    public function reviewlist(){
        $page = $this->input->get('page');
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
         
        
        $team_sql = "select * from team_temp where 1=1 and review = ? order by id DESC limit $offset , $per_page";
        $team_rs = $this->tehuiDB->query($team_sql,array(1))->result_array();
        
        $data['total_rows'] = $this->tehuiDB->query("select * from team_temp where 1=1 and review = ?",array(1))->num_rows();
        
        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>5,
        
            'base_url'=>'manage/product_review/reviewlist',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['team_rs'] = $team_rs;
        $data['message_element'] = "product_review";
        $this->load->view('manage', $data);
    }
    
    public function review($id = ''){
        if($id == ''){
            $id = $this->input->post('id');
            if($id == ''){
                $this->session->set_flashdata('flash_message','参数错误!');
                redirect('manage/product_review', 'refresh');
            }
        }
        echo $id;
        $field = " user_id,areatype,title,tags,summary,city_id,city_ids,group_id,partner_id,system,team_price,market_price,product,condbuy,per_number,permin_number,min_number,max_number,pre_number,allowrefund,image,image1,image2,credit,card,fare,farefree,bonus,address,detail,notice,express,delivery,state,conduser,buyonce,team_type,sort_order,expire_time,begin_time,end_time,reach_time,close_time,outdatefun,express_relate ";
            
        $team_sql = "select {$field} from team_temp where 1=1 and id = ? and display = ?";
        $team_rs = $this->tehuiDB->query($team_sql,array($id,1))->row_array();
 var_dump($team_rs);
        if($this->input->post('act') == "review"){
            if(count($team_rs)>0){
                //$data['content'] = str_replace('<img src="','<img src="http://116.228.53.169',$data['content']);
                $team_insert = $team_rs;
                $team_insert['detail'] = str_replace('src="','src="http://www.meilimei.com/',$team_rs['detail']);
                $team_insert['user_id'] = 1;
                $insert_rs = $this->tehuiDB->insert('team',$team_insert);
                if($insert_rs){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "审核通过！"));
                    redirect("manage/product_review", 'refresh');
                }else{
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "审核出了意外！"));
                    redirect("manage/product_review", 'refresh');
                }
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "数据出错！"));
                redirect("manage/product_review", 'refresh');
            }
        }elseif($this->input->post('act') == "noreview"){
            if($this->input->post('review_memo') == ''){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "请填写不通过问题！"));
                redirect("manage/product_review/review/{$id}", 'refresh');
            }
            $review_memo = array(
                'review_memo' => $this->input->post('review_memo')
            );
            $this->tehuiDB->where('id',$id);
            $this->tehuiDB->update('team_temp',$review_memo);
        }
        
        $data['id'] = $id;
        $data['team_rs'] = $team_rs;
        $data['message_element'] = "product_review_preview";
        $this->load->view('manage', $data);
    }
    
}