<?php
class article extends CI_Controller {
	private $notlogin = true, $uid = '';
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
       if(!$this->privilege->judge('article')){
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
		$data['total_rows'] = $this->db->query("SELECT id,tag,title,cdate FROM article {$condition} ORDER BY id DESC")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT id,tag,title,cdate FROM article {$condition} ORDER BY id DESC  LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/article/' . ($start -1)) : site_url('manage/article/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/article/index/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "article";
		$this->load->view('manage', $data);
	}
	public function del($id = '') {
		$condition = array (
			'id' => $id
		);
		$this->common->deleteTableData('article', $condition);
		$this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
		redirect('manage/article', 'refresh');
	}
	public function add() {
		if ($this->input->post('title')) {
            if($_FILES["picture"]['size']==0){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '必须上传缩略图！'));
                redirect('manage/article/add', 'refresh');
                die;
            }
			$picure =  $this->upload($_FILES["picture"]);
			$datas = array (
				'author' => $this->uid,'dec' => $this->input->post('dec'),'picture' => $picure,
				'laiyuan' => $this->input->post('laiyuan'
			), 'title' => $this->input->post('title'), 'tag' => $this->input->post('tags'), 'content' => $this->input->post('content'), 'cdate' => time(),'consult_id' =>serialize($this->input->post("consult_cat")));
			$this->common->insertData('article', $datas);
		}

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "articleadd";
		$data['consult_cat'] = $this->db->query("SELECT * FROM consult_type   ORDER BY id DESC ")->result_array();
		$this->load->view('manage', $data);
	}
	public function edit($param = '') {
		if ($param) {
			$conditions['id'] = $data['artid'] = $param;
			$data['results'] = $this->common->getTableData('article', $conditions)->result_array();
			$data['consult_cat'] = $this->db->query("SELECT * FROM consult_type   ORDER BY id DESC ")->result_array();

			if ($this->input->post('title')) {
				$consult_cat = $this->input->post("consult_cat");
				if($consult_cat) {
					$data['results'][0]['consult_id'] = serialize($consult_cat);
				}
				$datas = array (
					'author' => $this->uid,'dec' => $this->input->post('dec'),
					'laiyuan' => $this->input->post('laiyuan'
				), 'title' => $this->input->post('title'), 'tag' => $this->input->post('tags'), 'content' => $this->input->post('content'),'consult_id' =>$data['results'][0]['consult_id']);
				if($_FILES["picture"]['name'] and $url = $this->upload($_FILES["picture"])){
					$datas['picture'] =  $url;
				}
				$this->common->updateTableData('article', $param, '', $datas);
				redirect('manage/article', 'refresh');
			}

			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "articleedit";
			$this->load->view('manage', $data);
		}

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
