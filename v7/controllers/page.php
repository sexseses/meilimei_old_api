<?php
class page extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->uid = $this->wen_auth->get_user_id();
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
	}
	public function index($param = '') {
		if ($param=='') {
			redirect('');
		} else {
			$this->db->from('pages');
			$this->db->where('id', intval($param));
            $this->db->or_where('alias', $param);
			$data['notlogin'] = $this->notlogin;
			$data['results'] = $this->db->get()->result();
			if (isset ($data['results'][0])) {
				if ($data['results'][0]->showtype == 0) {
					$this->load->view('theme/pages_blank', $data);
				} else {
					$data['message_element'] = "pages";
					$this->load->view('template', $data);
				}
			} else {
				redirect('');
			}
		}
	}
}
?>
