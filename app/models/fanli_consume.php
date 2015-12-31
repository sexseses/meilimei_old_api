<?php

class fanli_consume extends CI_Model
{
	public function __construct()
	{
		parent::__construct();

		// Other stuff
		$this->_table = 'fanli';
	}

	// General function

    public function get_list($perpage,$page) {
    	$offset =($page -1)*$perpage;
    	return $this->db->query("select * from fanli limit ".$offset.",".$perpage."")->result_array();
    }
    public function upload_process($uploads_dir) {
		if (!is_writable($uploads_dir)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($uploads_dir .'/'. date('Y'))) {
				mkdir($uploads_dir .'/'. date('Y'), 0777, true);
			}
			$extend =explode("." , $_FILES["image"]["name"]);
            $va=count($extend)-1;
            $tmp = date('Y') . '/' . time().'.' . $extend[$va];
			$uploads_dir .= '/' .$tmp;
			move_uploaded_file($_FILES["image"]["tmp_name"], $uploads_dir);
			return  $tmp;
		}
    }
    public function putContents($data) {
    	$this->db->insert($this->_table,$data);
    	return $this->db->insert_id();
    }
    public function putMoneyToUser($money,$id) {
    	$this->db->update($this->_table,$money,array('id'=>$id));
    }
    public function putScoreToUser($score,$id) {
    	$this->db->update($this->_table,$score,array('id'=>$id));
    }
    public function getFanLiTotal() {

    	//$this->db->where('user_id',$user_id);
    	$this->db->from($this->_table);
		return $this->db->count_all_results();
    }
}

?>