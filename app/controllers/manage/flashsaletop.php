<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

class flashsaletop extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
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
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }

        $sql = "select fsi.id as topid,fs.* from flash_sale_index fsi left join flash_sale fs on fsi.fs_id = fs.id  where 1=1 and fsi.display = 1 order by fsi.id desc ";
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
            $v['state'] = "正常显示";
            if($v['end'] < time()){
                $v['state'] = "下架";
            }
            unset($v['context'],$v['product'],$v['vbuy']);
        }
        //print_r($sale_rs);die;
        $data['sale_rs'] = $sale_rs;
        $data['message_element'] = "flashsaletop";
        $this->load->view('manage', $data);
    }
	
	public function del($topid = 0){
		if(!empty($topid)){
			if(!empty($new_fs_id)){
				$updata['fs_id'] = $new_fs_id;
				$this->db->where('id',$topid);
				$rs = $this->db->update('flash_sale_index',$updata);
 
				if($rs){
					$this->session->set_flashdata('flash_message', $this->common->flash_message('success', "success"));
                redirect("manage/flashsaletop", 'refresh');
				}else{
					$this->session->set_flashdata('flash_message', $this->common->flash_message('error',"error"));
					redirect('manage/flashsaletop', 'refresh');
				}
			}
			$data['row'] = $tmp;
			$data['message_element'] = "flashsaletopedit";
			$this->load->view('manage', $data);
        }else{
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', $this->upload->display_errors()));
					redirect('manage/flashsaletop', 'refresh');
        }
	}

    public function edit($topid = 0){
        $new_fs_id = $this->input->post('new_fs_id');
		$sql = "select * from flash_sale_index where id = {$topid}";
		$tmp = $this->db->query($sql)->row_array();

        if(!empty($topid)){
			if(!empty($new_fs_id)){
				$updata['fs_id'] = $new_fs_id;
				$this->db->where('id',$topid);
				$rs = $this->db->update('flash_sale_index',$updata);
 
				if($rs){
					$this->session->set_flashdata('flash_message', $this->common->flash_message('success', "success!"));
                redirect("manage/flashsaletop", 'refresh');
				}else{
					$this->session->set_flashdata('flash_message', $this->common->flash_message('error',"error"));
					redirect('manage/flashsaletop', 'refresh');
				}
			}
			$data['row'] = $tmp;
			$data['message_element'] = "flashsaletopedit";
			$this->load->view('manage', $data);
        }else{
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', $this->upload->display_errors()));
					redirect('manage/flashsaletop', 'refresh');
        }
    }
	
	public function add(){
		$new_fs_id = $this->input->post('new_fs_id');
		if($new_fs_id){
			$updata['fs_id'] = $new_fs_id;
			$rs = $this->db->insert('flash_sale_index',$updata);

			if($rs){
				$this->session->set_flashdata('flash_message', $this->common->flash_message('success', "success!"));
				redirect("manage/flashsaletop", 'refresh');
			}else{
				$this->session->set_flashdata('flash_message', $this->common->flash_message('error',"error"));
				redirect('manage/flashsaletop', 'refresh');
			}
		}
        
	 
 
		$data['message_element'] = "flashsaletopadd";
		$this->load->view('manage', $data);

        
    }
}