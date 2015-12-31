<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
class auth extends CI_Controller {
	//max per second use 100
	private $mec = null,$ID,$VT=600,$maxuse=60000;
	public function __construct() {
		parent :: __construct();
        if(!($this->ID = $_COOKIE['API_ID'])){
        	$this->ID = uniqid();
            setcookie('API_ID',$this->ID,time()+7200,'/');
        }
        $this->mec = new Memcache();
        $this->mec->connect('127.0.0.1', 11211);
	}
	public function login($param='') {
        $accept = array (
			'k5m36st21c' => true,
			'wrm16et62n' => true
		);


		$result = array ();
		$param = strtolower($param);

		$result['state'] = '001';
		$result['token'] = '';
		$result['gtime'] = time();
		$Ctime = time() - $this->VT;
		if (isset ($accept[$param])) {
			$result['state'] = '000';
			if ($this->mec->get('use_num'.$this->ID) > $this->maxuse) {
				$result['state'] = '003';
				$result['token'] = $result['gtime'] = '';
				$this->mec->set('api_token'.$this->ID, '', 0,$this->VT);
				$this->mec->set('api_key'.$this->ID, '', 0, $this->VT);
			} else {
				if ($this->mec->get('api_time'.$this->ID) > $Ctime) {
					$tnum = $this->mec->get('use_num'.$this->ID) + 1;
					$this->mec->set('use_num'.$this->ID, $tnum, 0,$this->VT);
					$result['gtime'] = $this->mec->get('api_time'.$this->ID);
					$result['token'] = $this->mec->get('api_token'.$this->ID);
				} else {
					$result['token'] = $this->token();
					$this->mec->set('api_time'.$this->ID, $result['gtime'], 0,$this->VT);
					$this->mec->set('api_key'.$this->ID, $this->pkey($result['token']), 0,$this->VT);
					$this->mec->set('api_token'.$this->ID, strtolower($result['token']), 0,$this->VT);
				}
			}
		}
		echo json_encode($result);
	}

	//generate   token
	private function token() {
		/*$pattern = '12CE4J75KLNBPQKR3STMGDHA96UVWXF8YZ'; //字符池
		$key = '';
		$pos = rand(1101211211010, 9999899998989) . '';
		for ($i = 0; $i < 8; $i++) {
			$key .= $pattern[$pos[$i]]; //生成php随机数
		}*/
		$key = rand(11012112, 29998999) . rand(2, 9) . rand(356, 998) . rand(1, 7) . '';
		return $key;
	}
	//
	private function pkey($num) {
		$string = '' . $num;
		$sourcenum = intval($num / 100000);
		$result = $tmp = '';
		$minus = $string[9] . $string[10] . $string[11];
		$tmp = ($sourcenum - $minus) * $string[8] . '';
		for ($i = 1; $i < $string[12] + 1; $i++) {
			$result .= $tmp[$i];
		}
		return $result;
	}
	public function hash($param)
	{  $result['state'] = '000';
	   if(true || $this->checktoken($param)){
		 $result['paramname'] =  $this->security->get_csrf_token_name();
         $result['paramval']  =  $this->security->get_csrf_hash();
	   }else{
	   	$result['state'] = '001';
	   }
	   echo json_encode($result);
	}
	public function mytoken() {
		$Ctime = time() - 1800;
		if ($this->mec->get('api_time'.$this->ID) > $Ctime && $this->mec->get('api_token'.$this->ID) != '') {
			$result['state'] = '000';
			$result['token'] = $this->mec->get('api_token'.$this->ID);
			//$result['api_key'] = $this->mec->get('api_key');
		} else {
			$result['state'] = '001'; //过期
			$result['token'] = '';
		}
		echo json_encode($result);
	}
		//check fetch data token
	public function checktoken($param) {
		$param = strtolower($param);
		$tnum = $this->mec->get('use_num'.$this->ID) + 1;
		$this->mec->set('use_num'.$this->ID, $tnum, 0,$this->VT);
		if ($param != '' && $param == $this->mec->get('api_key'.$this->ID) && $tnum < 6000) {
			return true;
		} else {
			return false;
		}
	}
	function __destruct(){
		$this->mec->close();
	}
}
?>
