<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api info Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class new_items extends MY_Controller {
	
    public function __construct() {
        parent :: __construct();
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            $this->notlogin = true;
        }
        $this->load->model('auth');
        $this->load->model('remote');
        //$this->load->library('alicache');
    }
    
    public function allItems(){
    	$result['state'] = '000';
    
    	$items_sql = "select * from mlm_items where pid = 0";
    	$result = $this->db->query($items_sql)->result_array();
    	foreach ($result as &$rs_v){
    		$sub_items_sql = "select * from mlm_items where pid = {$rs_v['id']}";
    		$rs_v['child'] = $this->db->query($sub_items_sql)->result_array();
    	}

    	$result['data'] = $result;
    	echo json_encode($result);
    }
}