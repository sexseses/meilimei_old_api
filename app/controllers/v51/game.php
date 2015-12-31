<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class game extends CI_Controller {
	public function __construct() {
		parent :: __construct();
	}
	// Game list
	public function getGameList($param) {
		$result['state'] = '000';
		$result['data'] = array();
		$result['data'] = $this->db->get('game')->result_array();
		echo json_encode($result);
	}
}
?>