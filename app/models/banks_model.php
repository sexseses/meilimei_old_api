<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');

/**
 * Web Bank Interface Model
 *
 * @author wenran
 */
class banks_model extends CI_Model {

	var $aliapy_config;
	var $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';
	function __construct() {
		parent :: __construct();
		$this->load->helper('form');
		$this->aliapy_config['partner'] = '2088701536205434';
		$this->aliapy_config['key'] = '3hv4z3isrt68b6ej68f00ave1l3dxbhj';

		//签约支付宝账号或卖家支付宝帐户
		$this->aliapy_config['seller_email'] = '747242966@qq.com';
		$this->aliapy_config['return_url'] = base_url() . "chongzhi/success";
		$this->aliapy_config['notify_url'] = base_url() . "chongzhi/payreturn";
		$this->aliapy_config['show_url'] = base_url();
		$this->aliapy_config['sign_type'] = 'MD5';
		$this->aliapy_config['input_charset'] = 'utf-8';
		$this->aliapy_config['transport'] = 'http';
		$this->aliapy_config['payment_type'] = 1;

	}

	/**
	 * 构造纯网关接口
	 * @param $para_temp 请求参数数组
	 * @return 表单提交HTML信息
	 */
	function create_direct_pay_by_user($para_temp) {
		//设置按钮名称
		$button_name = "确认";
		//生成表单提交HTML文本信息
		$this->load->library('alipay_submit');
		$html_text = $this->alipay_submit->buildForm($para_temp, $this->alipay_gateway_new, "get", $button_name, $this->aliapy_config);

		return $html_text;
	}

	/**
	 * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
	 * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
	 * return 时间戳字符串
	 */
	function query_timestamp() {
		$url = $this->alipay_gateway_new . "service=query_timestamp&partner=" . trim($this->aliapy_config['partner']);
		$encrypt_key = "";

		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName("encrypt_key");
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;

		return $encrypt_key;
	}

	/**
	 * 构造支付宝其他接口
	 * @param $para_temp 请求参数数组
	 * @return 表单提交HTML信息/支付宝返回XML处理结果
	 */
	function alipay_interface($para_temp) {
		//获取远程数据
		$alipaySubmit = new AlipaySubmit();
		$html_text = "";
		//请根据不同的接口特性，选择一种请求方式
		//1.构造表单提交HTML数据:（$method可赋值为get或post）
		//$alipaySubmit->buildForm($para_temp, $this->alipay_gateway, "get", $button_name,$this->aliapy_config);
		//2.构造模拟远程HTTP的POST请求，获取支付宝的返回XML处理结果:
		//注意：若要使用远程HTTP获取数据，必须开通SSL服务，该服务请找到php.ini配置文件设置开启，建议与您的网络管理员联系解决。
		//$alipaySubmit->sendPostInfo($para_temp, $this->alipay_gateway, $this->aliapy_config);

		return $html_text;
	}
	function notify_verify(){
		$this->load->library('alipay_notify');
      if($this->alipay_notify->verifyNotify($this->aliapy_config)){
      	return true;
      }else{
      	return false;
      };
	}
}