<?php
class game extends CI_Model {
	
	public function __construct() {
		parent :: __construct();
	}
	
	public function getGameList(){
		//return $this->db->get('game')->result_array();
	}

	public function __destruct(){

	}
}
?>
