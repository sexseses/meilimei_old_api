<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class logs extends CI_Controller {
	public function __construct() {
		parent :: __construct();
        $this->load->model('track');
	}
	public function catLog($param = '') {
		$result['state'] = '000';
		if ($param == '3KfMl321l') {
			$ip = array (
				"1" => "183.136.134.121",
				"2" => "101.71.123.249"
			);
			if (($this->input->get('mac') or $this->input->get('idfa')) and array_search($_SERVER['REMOTE_ADDR'], $ip)) {
				$p['mac'] = strip_tags(trim($this->input->get('mac')));
                $p['idfa'] = strip_tags(trim($this->input->get('idfa')));
                $this->track->advLog($p);
                echo 'true';
                exit;
			}
			$result['notice'] = 'params not exisits OR forbid!';
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	public function nty($param = '') {
		if($this->track->advNotify('BFFC78C0-C46B-464D-8F41-3C2C13254564','')){
			echo 'sdfsd';
		}else{
			echo 'dsf';
		}
	}
}
?>
