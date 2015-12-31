<?php
class banner extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('banner')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$condition = ' WHERE 1 ';
		$data['issubmit'] = false;
		if ($this->input->post('submit')) {
			$data['issubmit'] = true;

			if ($this->input->post('sname')) {
				$condition .= "  AND title like '%" . trim($this->input->post('sname')) . "%'";
			}
		}
		$data['total_rows'] = $this->db->query("SELECT id FROM banner {$condition} ORDER BY id DESC")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT id,tags,weigh,title,cdate,picture FROM banner {$condition} ORDER BY id DESC  LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/article/' . ($start -1)) : site_url('manage/article/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/article/index/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "banner";
		$this->load->view('manage', $data);
	}

	public function add(){
        if($this->input->post('title')){
        	$picure =  $this->upload($_FILES["picture"]);
        	$datas = array('pos'=>$this->input->post('pos'),'machine'=>$this->input->post('machine'),'title'=>$this->input->post('title'),'weigh'=>$this->input->post('weigh'),'tags'=>$this->input->post('tags'),'type'=>$this->input->post('type'),'picture'=>$picure,'link'=>$this->input->post('link'),'cdate'=>time());
            $this->common->insertData('banner',$datas);
        }
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "banneradd";
		$this->load->view('manage', $data);
	}
   public function edit($param=''){
        if($this->input->post('title') && $param){
        	if(isset($_FILES["picture"]) && $_FILES["picture"]['size']>0){
        		$picure =  $this->upload($_FILES["picture"]);
        		unlink($this->input->post('sourcefile'));
        	}else{
        		$picure =  $this->input->post('sourcefile');
        	}
        	$datas = array('pos'=>$this->input->post('pos'),'title'=>$this->input->post('title'),'machine'=>$this->input->post('machine'),'weigh'=>$this->input->post('weigh'),'tags'=>$this->input->post('tags'),'type'=>$this->input->post('type'),'picture'=>$picure,'link'=>$this->input->post('link'));
            $this->common->updateTableData('banner',$param,'',$datas);
            redirect('manage/banner');
        }
        $conditions['id'] = $data['bannerid'] = $param;
        $data['results'] = $this->common->getTableData('banner', $conditions)->result_array();
		$data['notlogin'] = $this->notlogin;

		$data['message_element'] = "banneredit";
		$this->load->view('manage', $data);
	}
	public function del($id=''){
        $condition = array('id'=>$id);
        $contents = $this->common->getTableData('banner',$condition)->result_array();
        unlink( $contents[0]['picture']);
        $this->common->deleteTableData('banner',$condition);
        $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
		redirect('manage/banner', 'refresh');
	}
	private function upload($file) {
		$target_path = realpath(APPPATH . '../upload');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path .'/'. date('Y'))) {
				mkdir($target_path .'/'. date('Y'), 0777, true);
			}
			$extend =explode("." , $file["name"]);
            $va=count($extend)-1;
            $tmp = date('Y') . '/' . time().'.' . $extend[$va];
			$target_path .= '/' .$tmp;
			move_uploaded_file($file["tmp_name"], $target_path);
			return 'upload/'.$tmp;
		}
		return false;
	}

}
?>
