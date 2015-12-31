<?php
class banners extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
	}
	public function index() {
		if ($tag = $this->input->get('param')) {
			$data = array ();
			if($this->input->get('pos')){
              $this->db->like('pos', $this->input->get('pos'));
			}
			$this->db->like('tags', $tag, 'both');
            $this->db->order_by("id", "desc");
            $this->db->limit(10);
            $data['infos'] = $this->db->get('banner')->result_array();
			$this->load->view('theme/include/banners', $data);
		}
	}
}
?>
