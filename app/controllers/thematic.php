<?php
class thematic extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
	}
	public function index() {
		$per_page = 8;
		$this->load->library('pagination');
		$config['base_url'] = site_url() . 'thematic?state=1';
		$config['per_page'] = 8;
		$config['enable_query_strings'] = true;
		$config['page_query_string'] = true;
		$config['total_rows'] = $data['total_rows'] = $this->db->query("SELECT id FROM thematic ORDER BY id DESC")->num_rows();
		$start = intval($this->input->get('per_page'));
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT id,title,cdate,picture,descm FROM thematic  ORDER BY id DESC  LIMIT $offset , $per_page")->result();
		$this->pagination->initialize($config);
		$data['pagelink'] = $this->pagination->create_links();
		$data['message_element'] = "thematic";
		$data['notlogin'] = $this->notlogin;
		$this->load->view('template', $data);
	}
	public function detail($param = '') {
		if ($param=='') {
			redirect('');
		} else {
			$this->db->from('thematic');
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
