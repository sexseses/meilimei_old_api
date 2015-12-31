<?php
class down extends CI_Controller {
	public function index($param = '') {
		if (($type = $this->input->get('type')) and ($version = $this->input->get('version'))) {
			$this->load->helper('download');
			$this->db->where('type', $type);
			$this->db->where('version', $version);
			if($extra = $this->input->get('extra')){
				$this->db->where('extra', $extra);
			}
			$this->db->limit(1);
			$query = $this->db->get('softinfo')->result_array();
			if (!empty ($query)) {
			//	$data = file_get_contents($query[0]['downurl']);
				$this->output($query[0]['name'].'.apk', $query[0]['downurl']);
			}
		}
	}
	public function judge(){
		$head = strtolower($_SERVER['HTTP_USER_AGENT']);
		if($this->is_mobile()){
        if(is_int(strpos('iphone')) OR is_int(strpos('ipod'))){
            echo 'http://itunes.apple.com/CN/app/id654644428?l=zh&mt=8';
        }else{
            echo 'http://www.meilimei.com/m/meilishenqi.apk';
        }
		}else{
            echo 'www.meilimei.com/m/';
		}
	}
	private function output($name,$url) {
		$file = fopen($url, "r");
		$size = filesize($url);
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: " . $size);
		Header("Content-Disposition: attachment; filename=" . $name);
		echo fread($file, $size);
		fclose($file);
		exit;
	}
	//functions
	function is_mobile() {
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'windows') && !stristr($_SERVER['HTTP_USER_AGENT'], 'windows ce')) {
			return false;
		}
		$mobile_keywords = array (
			'up.browser',
			'up.link',
			'windows ce',
			'iemobile',
			'mini',
			'mmp',
			'symbian',
			'smartphone',
			'midp',
			'wap',
			'phone',
			'pocket',
			'mobile',
			'pda',
			'psp',


		);
		if (preg_match('~(' . implode('|', $mobile_keywords) . ')~i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
		if (stristr($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') || stristr($_SERVER['HTTP_ACCEPT'], 'wap') || isset ($_SERVER['HTTP_X_WAP_PROFILE']) || isset ($_SERVER['HTTP_PROFILE'])) {
			return true;
		}
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array (
			'acs-',
			'alav',
			'alca',
			'amoi',
			'audi',
			'aste',
			'avan',
			'benq',
			'bird',
			'blac',
			'blaz',
			'brew',
			'cell',
			'cldc',
			'cmd-',
			'dang',
			'doco',
			'eric',
			'hipt',
			'inno',
			'ipaq',
			'java',
			'jigs',
			'kddi',
			'keji',
			'leno',
			'lg-c',
			'lg-d',
			'lg-g',
			'lge-',
			'maui',
			'maxo',
			'midp',
			'mits',
			'mmef',
			'mobi',
			'mot-',
			'moto',
			'mwbp',
			'nec-',
			'newt',
			'noki',
			'oper',
			'opwv',
			'palm',
			'pana',
			'pant',
			'pdxg',
			'phil',
			'play',
			'port',
			'prox',
			'qtek',
			'qwap',
			'sage',
			'sams',
			'sany',
			'sch-',
			'sec-',
			'send',
			'seri',
			'sgh-',
			'shar',
			'sie-',
			'siem',
			'smal',
			'smar',
			'sony',
			'sph-',
			'symb',
			't-mo',
			'teli',
			'tim-',
			'tosh',
			'tsm-',
			'upg1',
			'upsi',
			'vk-v',
			'voda',
			'w3c ',
			'wap-',
			'wapa',
			'wapi',
			'wapp',
			'wapr',
			'webc',
			'winw',
			'winw',
			'xda',
			'xda-',


		);
		if (in_array($mobile_ua, $mobile_agents)) {
			return true;
		}
		if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
			return true;
		}
	}
}
?>
