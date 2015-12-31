<?php
class pages extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('pages')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$option['table'] = 'pages';
		$this->db->from('pages');
		$this->db->select('id,title');
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "pages";
		$this->load->view('manage', $data);
	}

	public function edit($param = '') {
		$id = intval($param);
        if($this->input->post('content')){
        	$adata['content'] =  $this->input->post('content') ;
        	$adata['title'] = $this->input->post('title');
        	$adata['showtype'] = $this->input->post('showtype');
        	$adata['cdate'] = time();
        	$adata['status'] = $this->input->post('status');
           $this->common->updateTableData('pages',$id,'',$adata);
           $this->session->set_flashdata('msg', $this->common->flash_message('success', '修改成功!'));
           redirect('manage/email');
        }
		$this->db->from('pages');
        $this->db->where('id',$id);
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "pagesedite";
		$this->load->view('manage', $data);
	}

	public function add() {
        if($this->input->post('content')){
        	$adata['content'] = $this->input->post('content') ;
        	$adata['title'] = $this->input->post('title');
        	$adata['showtype'] = $this->input->post('showtype');
        	$adata['cdate'] = time();
        	$adata['alias'] = time();
        	$adata['status'] = $this->input->post('status');
           $this->common->insertData('pages',$adata);
           $this->session->set_flashdata('msg', $this->common->flash_message('success', '页面添加成功!'));
           redirect('manage/pages');
        }
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "pagesadd";
		$this->load->view('manage', $data);
	}
}
?>
