<?php
/*
 * WENRAN Filter
 */
class filter {
	private $allergicWord = array (
		'殴打','微信','联系我','QQ','qq','扣扣','电话','有意者','代理',
		'脏话','V信','v信','U信','u信','兼职','天猫','淘宝'
	);

	public $str;
	//judege whether have illegal words
	function judge($str) {
		foreach ($this->allergicWord as $key) {
			if (is_int(strpos($str, $key))) {
				return false;
			}
		}
		return true;
	}
	function rmtags() {
		$this->str = strip_tags($this->str,'<p><br><br/>');
	}
	//filt illegal words
	function filts(& $str,$rtag=true) {
		$this->str = & $str;
		foreach ($this->allergicWord as $key) {
			$this->str = str_replace($key, '', $this->str);
		}
		$this->str = str_replace('\\', '-', $this->str);
		$this->remail();
		$this->rqq();
		$this->rcnumber();
		$this->rurl();
		$rtag?$this->rmtags():'';
	}
	//remove email
	function remail() {
		$reg = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
		$this->str = preg_replace($reg, '', $this->str);
	}
    function GetAlabNum($fnum){
       $arr = array("０"=>'0',"１"=>'1',"２"=>'2',"３"=>'3',"４"=>'4',"５"=>'5',"６"=>'6',"７"=>'7',"８"=>'8',"９"=>'9');
       return strtr($this->str, $arr);
    }
	//remove phone
	function rqq() {
		$this->str = $this->GetAlabNum($this->str);
		$reg = "/[1-9][0-9]{4,}+/";
		$this->str = preg_replace($reg, '', $this->str);
	}
	//remove special number
	function rcnumber() {
		$reg = "/[一二三四五六七八九]{1}[0一二三四五六七八九]{10}+/";
		$this->str = preg_replace($reg, '', $this->str);
		$reg = "/[壹贰叁肆伍陆柒玖]{1}[零壹贰叁肆伍陆柒玖]{10}+/";
		$this->str = preg_replace($reg, '', $this->str);
	}
	//remove web url
	function rurl() {
		$reg = '/((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?/';
		$this->str = preg_replace($reg, '', $this->str);
	}
}
?>
