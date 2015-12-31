<?php
class thematic extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
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
		$data['total_rows'] = $this->db->query("SELECT id FROM thematic {$condition} ORDER BY id DESC")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT id,tags,weigh,title,cdate,picture FROM thematic {$condition} ORDER BY id DESC  LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/article/' . ($start -1)) : site_url('manage/article/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/article/index/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "thematic";
		$this->load->view('manage', $data);
	}
	public function add() {
		if ($this->input->post('title')) {
			$insertData = array ();
			$insertData['title'] = $this->input->post('title');
			$insertData['tags'] = $this->input->post('tags');
			$insertData['content'] = $this->input->post('contents');
			$insertData['cdate'] = time();
			$insertData['showtype'] = intval($this->input->post('showtype'));
			$picure = $this->upload($_FILES["picture"]);
			$insertData['picture'] = $picure;
			$this->common->insertData('thematic', $insertData);
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "thematicadd";
		$this->load->view('manage', $data);
	}
	public function del($id=''){
        $condition = array('id'=>$id);
        $contents = $this->common->getTableData('thematic',$condition)->result_array();
        unlink( $contents[0]['picture']);
        $this->common->deleteTableData('thematic',$condition);
        $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
		redirect('manage/thematic');
	}
	public function edit($id=''){

		$condition = array('id'=>$id);
		if ($this->input->post('title')) {
			$insertData = array ();
			$insertData['title'] = $this->input->post('title');
			$insertData['tags'] = $this->input->post('tags');
			$insertData['content'] = $this->input->post('content');
			$insertData['descm'] = $this->input->post('descm');
            $insertData['showtype'] = intval($this->input->post('showtype'));
			if($_FILES["picture"]['size']>0){
				$picure = $this->upload($_FILES["picture"]);
			    $insertData['picture'] = $picure;
			}

			$this->common->updateTableData('thematic',$id, $condition, $insertData);
			$this->session->set_flashdata('flash_message', $this->common->flash_message('success', '成功更新！'));
			redirect('manage/thematic');
		}
		$data['id'] = $id;
        $data['results'] = $this->common->getTableData('thematic',$condition)->result_array();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "thematicedit";
		$this->load->view('manage', $data);
	}
	private function upload($file) {
		$target_path = realpath(APPPATH . '../upload');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path . '/' . date('Y'))) {
				mkdir($target_path . '/' . date('Y'), 0777, true);
			}
			$extend = explode(".", $file["name"]);
			$va = count($extend) - 1;
			$tmp = date('Y') . '/' . time() . '.' . $extend[$va];
			$target_path .= '/' . $tmp;
			move_uploaded_file($file["tmp_name"], $target_path);
			return 'upload/' . $tmp;
		}
		return false;
	}
}
?>
