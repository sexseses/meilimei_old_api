<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class vote extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->helper('form');
	}
	 public function show($param='') {
	 	if($id = intval($param)){
	 		$data['param'] = $id;
	 		$this->db->where('vid', $id);
	 		$this->db->order_by("votes", "desc");
	 		$tmp = $this->db->get('votes')->result_array();
	 		//asort($tmp);
	 		$data['res'] = array();
	 		$i = 1;
	 		foreach($tmp as $v){
	 		   $v['order'] = $i;
               $data['res'][$v['id']] = $v;
               $i++;
	 		}
		   $this->load->view('other/v'.$param, $data);
	 	}
	 }
	 public function ac($param=''){
	 	if($id = intval($param)){
	 		$did = intval($this->input->get('dataid'));
	 		$this->db->query("update votes SET votes=votes+1 WHERE id = $did and vid = $id");

	 		exit;
	 	}else{
	 		echo false;
	 	}
	 }

}
?>
