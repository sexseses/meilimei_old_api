<?php
class articles extends CI_Controller {
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
			$this->db->select('title, id, cdate');
			$this->db->like('tag', $tag, 'both');
			$this->db->order_by("id", "desc");
			$this->db->limit(10);
			$data['infos'] = $this->db->get('article')->result_array();
			$this->load->view('theme/include/articles', $data);
		}
	}
	public function detail($param = '') {
		if($param!='' and $param = intval($param)){
		$uid = $this->wen_auth->get_user_id();
		if($this->input->post() && !$this->notlogin){
			$code = $this->input->post('validecode');
           if(strtolower($code) == strtolower($this->session->userdata('validecode'))){
               $insertdata['uid'] = $uid;
               $insertdata['cid'] = $param;
               $insertdata['content'] = $this->input->post('contents');
               $insertdata['cdate'] = time();
               $this->db->insert('comment', $insertdata);
               $this->session->set_flashdata('msg', $this->common->flash_message('success', '评论已提交等待审核！'));
			   redirect('articles/detail/'.$param);
           }else{
             $this->session->set_flashdata('msg', $this->common->flash_message('error', '验证码不匹配,信息更新失败！'));
			  redirect('articles/detail/'.$param);
           }
		}
		$data = array ();
		$data['notlogin'] = $this->notlogin;
		$this->db->where('article.id', $param);
		$this->db->select("article.*,users.alias");
        $this->db->join('users', 'users.id = article.author');
		$this->db->order_by("article.id", "desc");
		$this->db->limit(1);
		$data['results'] = $this->db->get('article')->result_array();

		$this->db->select('title, id, cdate');
		$this->db->order_by("id", "desc");
		$this->db->limit(10);
		$data['infos'] = $this->db->get('article')->result_array();


        $this->db->select("id,username,alias");
		$this->db->order_by("id", "desc");
		$this->db->where('id', $uid);
		$this->db->limit(1);
		$data['users'] = $this->db->get('users')->result_array();
		$data['WEN_PAGE_TITLE'] = $data['results'][0]['title'];
		$data['WEN_PAGE_KEYWORDS'] = $data['results'][0]['keywords'];
		$data['message_element'] = "include/article_detail";
		$this->load->view('template', $data);
		}else{
		     redirect('');
		}
	}
}
?>
