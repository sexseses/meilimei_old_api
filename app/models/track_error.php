<?php
/*
 * track program bug
 */
class track_error extends CI_Model {
	private $table = 'site_log';
	public function __construct() {
		parent :: __construct();
	}
	//log bug
	public function L($info = array ()) {
		$data = array (
			'data' => serialize($info),
			'cdate	' => time()
		);
		$this->db->insert($this->table, $data);
	}
}
?>