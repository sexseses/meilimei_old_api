<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class team extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        
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
        $page = $page ? $page : 1;
        $start = ($page -1) * 10;
        try{
            
            
            $fields = 't.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';
            
            $condition = "t.team_type='normal'";
            
            $limit = "{$start},10";
            $data['results'] = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition}  limit {$limit} ")->result_array();
            
            print_r($data['results']);die;
            
            //$data['results'] = $this->db->query($sql)->result_array();
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
    
}