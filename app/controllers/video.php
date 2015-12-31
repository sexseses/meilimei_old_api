<?php
class video extends CI_Controller {
	public function __construct() {
		parent :: __construct();
	}
	public function index($param='') {
		if($id = intval($param)){
			$this->db->where('weibo_id', $id);
		    $this->db->where('wen_weibo.type & ', 25);
		    $this->db->from('wen_weibo');
		    $tmp = $this->db->get()->result_array();
		    if(!empty($tmp[0])){
               $data['content'] = $tmp[0]['video'];
               $this->load->view('video', $data);
		    }
		}
	}
}
?>
