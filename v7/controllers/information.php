<?php
class information extends CI_Controller {
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
	public function index() {
		$per_page = 8;
		$this->load->library('pagination');
		$config['base_url'] = site_url() . 'information?state=1';
		$config['per_page'] = 8;
		$config['enable_query_strings'] = true;
		$config['page_query_string'] = true;
            $config['first_link'] = "第一页";
            $config['last_link'] = "末页";
		$config['total_rows'] = $data['total_rows'] = $this->db->query("SELECT id FROM article ORDER BY id DESC")->num_rows();
		$start = intval($this->input->get('per_page'));
		$offset = $start;
		/*if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;*/
		$this->db->select('article.title,article.id,article.author,article.laiyuan,article.dec,article.picture,article.cdate,users.alias');
		$this->db->order_by("article.id", "desc");
		$this->db->join('users', 'article.author = users.id');
		$this->db->limit($per_page,$offset);
		$data['results'] = $this->db->get('article')->result_array();
		$this->pagination->initialize($config);
		$data['pagelink'] = $this->pagination->create_links();
		$data['message_element'] = "information";
		$data['notlogin'] = $this->notlogin;
		$data['banner'] = $this->banner('information',1);
		$data['WEN_PAGE_TITLE'] = '资讯';
		$this->load->view('template', $data);
	}
	private function banner($tag='',$pos='') {
		if ($tag) {
			$data = array ();
			if($pos){
              $this->db->like('pos', $pos);
			}
			$this->db->like('tags', $tag, 'both');
            $this->db->order_by("id", "desc");
            $this->db->limit(10);
            return $this->db->get('banner')->result_array();
		}
	}
}
?>
