<?php
/**
 * 		$obj = new guest_info;
 * 		$obj->GetLang();		//获取访客语言：简体中文、繁體中文、English。
 * 		$obj->GetBrowser();		//获取访客浏览器：MSIE、Firefox、Chrome、Safari、Opera、Other。
 * 		$obj->GetOS();			//获取访客操作系统：Windows、MAC、Linux、Unix、BSD、Other。
 *		$obj->GetIP();			//获取访客IP地址。
 *		$obj->GetAdd();			//获取访客地理位置，使用 Baidu 隐藏接口。
 *		$obj->GetIsp();			//获取访客ISP，使用 Baidu 隐藏接口。
 */
class systeminfo {
	function GetLang() {
		$Lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
		//使用substr()截取字符串，从 0 位开始，截取4个字符
		if (preg_match('/zh-c/i', $Lang)) {
			//preg_match()正则表达式匹配函数
			$Lang = '简体中文';
		}
		elseif (preg_match('/zh/i', $Lang)) {
			$Lang = '繁體中文';
		} else {
			$Lang = 'English';
		}
		return $Lang;
	}
	function GetBrowser() {
		$Browser = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/MSIE/i', $Browser)) {
			$Browser = 'MSIE';
		}
		elseif (preg_match('/Firefox/i', $Browser)) {
			$Browser = 'Firefox';
		}
		elseif (preg_match('/Chrome/i', $Browser)) {
			$Browser = 'Chrome';
		}
		elseif (preg_match('/Safari/i', $Browser)) {
			$Browser = 'Safari';
		}
		elseif (preg_match('/Opera/i', $Browser)) {
			$Browser = 'Opera';
		} else {
			$Browser = 'Other';
		}
		return $Browser;
	}
	function GetOS() {
		$OS = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/win/i', $OS)) {
			$OS = 'Windows';
		}
		elseif (preg_match('/mac/i', $OS)) {
			$OS = 'MAC';
		}
		elseif (preg_match('/linux/i', $OS)) {
			$OS = 'Linux';
		}
		elseif (preg_match('/unix/i', $OS)) {
			$OS = 'Unix';
		}
		elseif (preg_match('/bsd/i', $OS)) {
			$OS = 'BSD';
		} else {
			$OS = 'Other';
		}
		return $OS;
	}
	function GetIP() {
		if (!empty ($_SERVER['HTTP_CLIENT_IP'])) {
			//如果变量是非空或非零的值，则 empty()返回 FALSE。
			$IP = explode(',', $_SERVER['HTTP_CLIENT_IP']);
		}
		elseif (!empty ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$IP = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}
		elseif (!empty ($_SERVER['REMOTE_ADDR'])) {
			$IP = explode(',', $_SERVER['REMOTE_ADDR']);
		} else {
			$IP[0] = 'None';
		}
		return $IP[0];
	}
	function getIPLoc($queryIP) {
		$url = 'http://ip.qq.com/cgi-bin/searchip?searchip1=' . $queryIP;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_ENCODING, 'gb2312');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
		$result = curl_exec($ch);
		$result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
		curl_close($ch);
		preg_match("@<span>(.*)</span></p>@iU", $result, $ipArray);
		$loc = $ipArray[1];
		return $loc;
	}
}
?>
