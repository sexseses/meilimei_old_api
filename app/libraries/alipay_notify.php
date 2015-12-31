<?php
/* *
 * 类名：AlipayNotify
 * 功能：支付宝通知处理类
 * 详细：处理支付宝各接口通知返回
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考

 *************************注意*************************
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */

class alipay_notify {
    /**
     * HTTPS形式消息验证地址
     */
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP形式消息验证地址
     */
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	var $aliapy_config;

    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyNotify($aliapy_config){
		$this->aliapy_config = $aliapy_config;
		if(empty($_POST)) {
			return false;
		}
		else {
			$mysign = $this->getMysign($_POST);
			$responseTxt = 'true';
			if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}
			if (strtolower(trim($responseTxt))=='true' && $mysign == $_POST["sign"]) {
				return true;
			} else {
				return false;
			}
		}
	}

    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyReturn(){
		if(empty($_GET)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$mysign = $this->getMysign($_GET);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty($_GET["notify_id"])) {$responseTxt = $this->getResponse($_GET["notify_id"]);}


			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $mysign == $_GET["sign"]) {
				return true;
			} else {
				return false;
			}
		}
	}

    /**
     * 根据反馈回来的信息，生成签名结果
     * @param $para_temp 通知返回来的参数数组
     * @return 生成的签名结果
     */
	function getMysign($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = $this->argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildMysign($para_sort, trim($this->aliapy_config['key']), strtoupper(trim($this->aliapy_config['sign_type'])));

		return $mysign;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	function getResponse($notify_id) {
		$transport = strtolower(trim($this->aliapy_config['transport']));
		$partner = trim($this->aliapy_config['partner']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = $this->getHttpResponse($veryfy_url);

		return $responseTxt;
	}
	//functions


/**
 * 生成签名结果
 * @param $sort_para 要签名的数组
 * @param $key 支付宝交易安全校验码
 * @param $sign_type 签名类型 默认值：MD5
 * return 签名结果字符串
 */
function buildMysign($sort_para,$key,$sign_type = "MD5") {
	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = $this->createLinkstring($sort_para);
	//把拼接后的字符串再与安全校验码直接连接起来
	$prestr = $prestr.$key;
	//把最终的字符串签名，获得签名结果
	$mysgin = $this->sign($prestr,$sign_type);
	return $mysgin;
}
/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
}

/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para) {
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}
/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $sign_type 签名类型 默认值：MD5
 * return 签名结果
 */
function sign($prestr,$sign_type='MD5') {
	$sign='';
	if($sign_type == 'MD5') {
		$sign = md5($prestr);
	}elseif($sign_type =='DSA') {
		//DSA 签名方法待后续开发
		die("DSA 签名方法待后续开发，请先使用MD5签名方式");
	}else {
		die("支付宝暂不支持".$sign_type."类型的签名方式");
	}
	return $sign;
}


/**
 * 远程获取数据
 * 注意：该函数的功能可以用curl来实现和代替。curl需自行编写。
 * $url 指定URL完整路径地址
 * @param $input_charset 编码格式。默认值：空值
 * @param $time_out 超时时间。默认值：60
 * return 远程输出的数据
 */
function getHttpResponse($url, $input_charset = '', $time_out = "60") {
	$urlarr     = parse_url($url);
	$errno      = "";
	$errstr     = "";
	$transports = "";
	$responseText = "";
	if($urlarr["scheme"] == "https") {
		$transports = "ssl://";
		$urlarr["port"] = "443";
	} else {
		$transports = "tcp://";
		$urlarr["port"] = "80";
	}
	$fp=@fsockopen($transports . $urlarr['host'],$urlarr['port'],$errno,$errstr,$time_out);
	if(!$fp) {
		die("ERROR: $errno - $errstr<br />\n");
	} else {
		if (trim($input_charset) == '') {
			fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
		}
		else {
			fputs($fp, "POST ".$urlarr["path"].'?_input_charset='.$input_charset." HTTP/1.1\r\n");
		}
		fputs($fp, "Host: ".$urlarr["host"]."\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $urlarr["query"] . "\r\n\r\n");
		while(!feof($fp)) {
			$responseText .= @fgets($fp, 1024);
		}
		fclose($fp);
		$responseText = trim(stristr($responseText,"\r\n\r\n"),"\r\n");

		return $responseText;
	}
}

}
?>
