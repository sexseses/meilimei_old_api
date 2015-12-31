<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class tools{
    /**
	 * 生成随机码
	 * @param string $mobile
	 * @param string $message
	 */
	public  static  function makecode(){
	    //生成8位随机数
	    $ychar="0,1,2,3,4,5,6,7,8,9";
	    $list=explode(",",$ychar);
	    $authnum ='';
	    for($i=0;$i<6;$i++){
	        $randnum=rand(0,9); // 10;
	        $authnum.=$list[$randnum];
	    }
	    return $authnum;
	}
	

	/**
	 * 发送短信
	 * @param string $mobile  发送短信手机
	 * @param string $message 发送短信文案
	 */
	public function sendMessage($mobile,$message) {
	    $CI =& get_instance();
	    $CI->load->library('sms');
	    //$this->load->library('sms');
	    $status = $CI->sms->sendSMS(array($mobile),$message);
	    return $status;
	}
}