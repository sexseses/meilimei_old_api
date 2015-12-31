<?php
class app extends CI_Controller {
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
       if(!$this->privilege->judge('app')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$data['results'] = $this->db->get('softinfo')->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "app_manage";
		$this->load->view('manage', $data);
	}
    public function del($param = ''){
    	if ($param) {
    		$conditions['id'] = intval($param);
    		$this->common->deleteTableData('softinfo', $conditions);
    		redirect('manage/app');
    	}
    }
	public function edit($param = '') {
		if ($param) {
			$conditions['id'] = $data['artid'] = $param;
			if ($this->input->post('name')) {
				if ($_FILES["downurl"]['tmp_name']){
					$downurl = $this->upAPK($_FILES["downurl"]['tmp_name'],$this->input->post('versions'));

					$datas = array (
					    'size' => $_FILES['downurl']['size'],
						'extra' => $this->input->post('extras'
					), 'name' => $this->input->post('name'), 'version' => $this->input->post('versions'), 'effectver' => $this->input->post('effectver'), 'needupdate' => $this->input->post('needupdate'), 'downurl' => $downurl, 'cdate' => time());
				} else {
					$datas = array (
						'extra' => $this->input->post('extras'
					), 'name' => $this->input->post('name'), 'version' => $this->input->post('versions'), 'effectver' => $this->input->post('effectver'), 'needupdate' => $this->input->post('needupdate'), 'cdate' => time());
				}


				$this->common->updateTableData('softinfo', $param, '', $datas);
				redirect('manage/app', 'refresh');
			}
			$this->db->where('id', $param);
			$data['results'] = $this->db->get('softinfo')->result_array();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "app_edit";
			$this->load->view('manage', $data);
		}

	}
	public function add() {
			if ($this->input->post('name')) {
				if ($_FILES["downurl"]['tmp_name']){
					$downurl = $this->upAPK($_FILES["downurl"]['tmp_name']);
					$datas = array (
					 'size' => $_FILES['downurl']['size'],
						'extra' => $this->input->post('extras'
					), 'name' => $this->input->post('name'), 'version' => $this->input->post('versions'), 'effectver' => $this->input->post('effectver'), 'needupdate' => $this->input->post('needupdate'), 'downurl' => $downurl, 'cdate' => time());
				} else {
					$datas = array (
						'extra' => $this->input->post('extras'
					), 'name' => $this->input->post('name'), 'version' => $this->input->post('versions'), 'effectver' => $this->input->post('effectver'), 'needupdate' => $this->input->post('needupdate'), 'cdate' => time());
				}


				$this->common->insertData('softinfo',  $datas);
				redirect('manage/app', 'refresh');
			}
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "app_add";
			$this->load->view('manage', $data);

	}

	//upload apk
	function upAPK($file,$ver) {
		$dir = realpath(APPPATH.'../').'/';
		$return = 'app_down/'.time() . '_'.$ver.'.apk';
        if (!move_uploaded_file($file, $dir.$return)) {
			die('upload error!'.$dir.$return);
		}else{
            return $return;
		}
	}
}
?>
