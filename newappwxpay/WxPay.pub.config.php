<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx1ab655ecf11026a2';
	//const APPID = 'wx14ea5bb933c41d8f';
	//
	
	
	//受理商ID，身份标识
	const MCHID = '1288891401';
	//1236006502
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '8d8fe9IMIO86MJGTRjiuds8979mkmlsq';
	//
	
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = 'a75d1918f4f73af74129eb0ca1f5943d';
 
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://www.meilimei.com/weixinpay/js_api_call.php';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/mnt/meilimei/wexinpay/apiclient_cert_new.pem';
	const SSLKEY_PATH = '/mnt/meilimei/wexinpay/apiclient_key_new.pem';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://m.kodin.cn/weixin/sdk.php';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>