<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Track extends CI_Controller {

	public function index()
	{
		echo '查询错误';
	}
	
	public function tracklist($param = '')
	{
	if($param != ''){
		$this->load->library('pager');
		$per_page = 16;
		$start = $page = intval($this->input->get('page'));
		$start == 0 && $start = 1;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->from('survey_log');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['total_rows'] = $this->db->count_all_results();
		$config = array (
			"record_count" => $data['total_rows'],
			"pager_size" => $per_page,
			"show_jump" => true,
			'base_url' => 'manage/apple/track',
			"pager_index" => $page
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		$this->db->limit($per_page, $offset);
		$this->db->order_by("survey_log.id", "desc");
		$this->db->select('survey_log.*, apple.title');
		$this->db->join('apple', 'survey_log.banner_id = apple.id');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['results'] = $this->db->get('survey_log')->result();
		$this->load->view('/doctor/track.php',$data);
	}else{
		$data['terror']=1;
	}
		
	}
}
