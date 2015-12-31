<?php
class yisheng {
	//respond to table items main item
	var $list = array (
		1 => '除皱',
		2 => '面部轮廓',
		3 => '减肥塑形',
		4 => '皮肤美容',
		5 => '眼部',
		6 => '鼻部',
		7 => '胸部',
		8 => '口唇',
		9 => '私密整形',
		117 => '牙齿'
	);
	function getKeShi() {
		return $this->list;
	}
	function search($str = '') {
		$list = explode(',', $str);
		$res ='';
		if (count($list) > 0) {
			foreach ($list as $row) {
                $res.=isset($this->list[$row])?$this->list[$row].' ':'';
			}
		}
		return $res;
	}
	function fullsec($str = '') {
		$list = explode(',', $str);
		$res = array();
		if (count($list) > 0) {
			foreach ($list as $row) { $tmp = array();
				if(isset($this->list[$row])){
					$tmp['key'] = $row;
					$tmp['name'] = $this->list[$row];
					$res[] = $tmp;
				}
			}
		}
		return $res;
	}
    function getsex($str = ''){
		switch ($str) {
			case 1:
                return '女';
				break;
            case 2:
                return '男';
				break;
			default:
			    return '保密';
				break;
		}
	}
}
?>