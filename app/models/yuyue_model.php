<?php

class Yuyue_model extends CI_Model
{
	public function Yuyue_model()
	{
		parent::__construct();

		// Other stuff
		$this->_prefix = $this->config->item('wen_table_prefix');
		$this->_table = 'yuyue';
	}


	function get_phone_by_id($id)
	{
		$this->db->where('id', $id);
		return $this->db->get($this->_table);
	}





}

?>