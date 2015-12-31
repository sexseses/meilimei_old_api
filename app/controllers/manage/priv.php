<?php

/**
 * WERAN privilege Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
class priv extends CI_Controller {
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
		if (!$this->privilege->judge('priv')) {
			die('Not Allow');
		}
	}
	public function index() {
		$this->db->select('id,alias');
		$this->db->where('role_id', 16);
		$data['res'] = $this->db->get('users')->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "privilege";
		$this->load->view('manage', $data);
	}
	public function edit($param='') {
		if($id = intval($param)){
			$this->privilege->init($id);//init privilege params

		if ($this->input->post('privs')) {
			$tmp = array ();
			foreach ($this->input->post('privs') as $r) {
				$tmp[$r] = true;
				if (!$this->privilege->judge($r)) {
					$data = array (
						'funs' => $r,
						'uid' => $id
					);

					$this->db->insert('privilege', $data);
					//echo $this->db->last_query();
				}
			}
			foreach ($this->privilege->privilege as $k=>$r) {
				if (!isset($tmp[$k])) {
					$this->db->delete('privilege', array('funs' => $k,'uid' => $id));
                    //echo $this->db->last_query();
				}
			}
			$data = array();
			$data['uid'] = $id;
			$data['type'] = 'users';
			$data['data'] = serialize(array('tv'=>$this->input->post('tv'),'fromv'=>$this->input->post('fromv')));
			$this->privilege->setPri('users',$data);

			redirect('manage/priv');
		}
        $tmp = $this->privilege->getPri('users');
        $data['users'] = array('tv'=>'','fromv'=>'');
        if(!empty($tmp)){
           $data['users'] = unserialize($tmp[0]['data']);
        }
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "privilege_set";
		$this->load->view('manage', $data);
		}
	}
}