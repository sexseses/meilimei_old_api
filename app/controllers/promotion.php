<?php
class promotion extends CI_Controller {
	private $imgstr='wenran';
	private $imgname='wenran';
	public function __construct() {
		parent :: __construct();$this->load->helper('cookie');
	}
	public function getUrl() {

	}
    public function setStr($imgstr=''){
        $this->imgstr = $imgstr;
    }
	public function getUrlImg() {
		$PNG_TEMP_DIR = FCPATH . 'temp' . DIRECTORY_SEPARATOR;
		$PNG_WEB_DIR = base_url().'temp/';
		include FCPATH."phpqrcode/qrlib.php";
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);

		$errorCorrectionLevel = 'L';
		$matrixPointSize = 4;
		// user data
	    $filename = $PNG_TEMP_DIR . $this->imgname . '.png';
		QRcode :: png($this->imgstr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		header('Content-Type:image/png');
		header('Content-Length:'.filesize($filename));
		readfile($filename);
		//echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" /> ';

	}

	//view download
	public function download($param='') {
		if($param!=''){
			$this->db->from('tongji_url');
        	$this->db->where('coupon_code',strip_tags($param));
			$results = $this->db->get()->result_array();
		}
        if($this->input->get('tag')){
        	$tags =  strip_tags(trim($this->input->get('tag')));
        }else{
        	$tags = '';
        }

        if(isset($results[0])){
             $url = $results[0]['url'];
        }elseif($this->is_mobile()){
        	$url = 'https://itunes.apple.com/cn/app/mei-li-zhen-suo/id575458870';
        }else{
           $url = site_url();
        }

		if ($this->is_mobile() && $param!='') {
			if(!get_cookie('coupon_code_used')){
				$systype = explode(' ',$_SERVER['HTTP_USER_AGENT']);
				$data = array('tags'=>'','coupon_code'=>$param,'url'=>$url,'systype'=>$systype[3].' '.$systype[4].' '.$systype[5].' '.$systype[15],'ip'=>$_SERVER['REMOTE_ADDR'],'cdate'=>time());
			    $this->common->insertData('tongji',$data);
			    set_cookie('coupon_code_used','wen');
			}
			$time = 2;

			header("refresh:{$time};url={$url}");
			echo ('<div style="border:solid 1px #ff6899;color:#fff;padding:20px 10px;width:90%;background:#ff6899;margin:35% auto;font-size:250%; text-align:center"><img src="'.base_url().'public/images/loading.gif" width="80" height="80" /><br>亲，正在加载...<br>稍等一下下哦~</div>');
		}else{
			if(!get_cookie('coupon_code_used')){
				$systype = explode(' ',$_SERVER['HTTP_USER_AGENT']);
				$data = array('tags'=>'','coupon_code'=>$param,'url'=>$url,'systype'=>$systype[3].' '.$systype[4].' '.$systype[5].' '.$systype[15],'ip'=>$_SERVER['REMOTE_ADDR'],'cdate'=>time());
			    $this->common->insertData('tongji',$data);
			     set_cookie('coupon_code_used','wen');
			}
			$time = 1;
            redirect($url);
	  }
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
