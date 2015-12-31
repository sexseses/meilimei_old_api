<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
 
 
class Tehuiad extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(0);
		//ini_set("display_errors","On");
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
       if(!$this->privilege->judge('users')){
          die('Not Allow');
       }
       
	}

	/**
	 * 默认控制器
	 * 
	 */
	public function index() {
	    $this->db->select('*');
	    $this->db->from('tehui_index_ad');
	    $data['results'] = $this->db->get()->result_array();
	    $data['message_element'] = "tehuiad";
	    $this->load->view('manage', $data);
	}


	public function tehuiad_update($id=''){
	    if($id){
	        $this->db->select('*');
	        $this->db->from('tehui_index_ad');
	        $this->db->where('id',$id);
	        $rs = $this->db->get()->result_array();
	        $data = $rs[false];
	    }else{
	    	
	    }
	    $data['message_element'] = "tehuiad_update";
	    $data['id'] = $id;
	    $this->load->view('manage', $data);
	}


	/**
	 * 
	 * @param string $id
	 *
	 */
	public function update($id='') {
	    $url = $this->input->post('url');
	    if(empty($id)){
            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误！'));
        }
	    $arr = array(
	            'url' => $url,
	    );

	    if ($_FILES['file']['tmp_name']) {
	        $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
	        $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
	        $banner_pic = $upload_path.$file_name;
	        if (!$this->remote->cp($_FILES['file']['tmp_name'], $file_name, $banner_pic,array (), true)) {
	            $this->session->set_flashdata('flash_message',
	                    $this->common->flash_message('error', $this->upload->display_errors())
	            );
	        }else{
	            $arr['banner_pic'] = $banner_pic;
	        }
	    }

	    $sql = " select * from tehui_index_ad where id = {$id} ";
		$rs = $this->db->query($sql)->result_array();
		if(count($rs)>0){
		    $this->db->where('id',$id);
		    $this->db->update('tehui_index_ad',$arr);
		}else{
		    $this->db->insert('tehui_index_ad',$arr);
		}
		echo "apc_bin_dump()";
	    redirect('manage/tehuiad/index');
	}
}
?>