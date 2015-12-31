<?php
require_once('soap/soap_client.php');
require_once('CurlHttp.php');
class sms extends CI_Model{
	private $url='http://sdkhttp.eucp.b2m.cn/sdk/SDKService', $serialNumber='3SDK-EMS-0130-MJVTK',$password='719017',$sessionKey='123456',
	      $soap,$namespace = 'http://sdkhttp.eucp.b2m.cn/',$outgoingEncoding = "UTF-8",$incomingEncoding = '';

	function __construct($proxyhost = false,$proxyport = false,$proxyusername = false, $proxypassword = false, $timeout = 0, $response_timeout = 30)
	{
		$this->soap = new nusoap_client($this->url,false,$proxyhost,$proxyport,$proxyusername,$proxypassword,$timeout,$response_timeout);
		$this->soap->soap_defencoding = $this->outgoingEncoding;
		$this->soap->decode_utf8 = false;
        $this->db = $this->load->database('default', TRUE);
	}
    function sendSMS($mobiles=array(),$content,$sendTime='',$addSerial='',$charset='UTF-8',$priority=5)
    {
        $sms = new smsAPI();
        //var_dump($mobiles);
        foreach ($mobiles as $mobile) {
            $this->db->where('phone',$mobile);
            $tmp = $this->db->get('sms')->result_array();
            $longtime = time();
            $str = 1;
            if(preg_match('/验证码/',$content)) {
                if (!empty($tmp)) {
                    if (($longtime - $tmp[0]['created_at']) <= 60 && $tmp[0]['minute'] < 1) {
                        $this->db->where('phone', $mobile);
                        $this->db->update('sms', array('minute_num' => 1, 'hour_num' => $tmp[0]['hour_num'] + 1, 'day_num' => $tmp[0]['day_num'] + 1, 'content'=>$content.":".$mobile));
                        $result = $sms->sendSms($mobile, $content);
                        $str = 2;
                         $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
                    }

                    if (($longtime - $tmp[0]['created_at']) <= 3600 && $tmp[0]['hour_num'] < 4 && ($longtime - $tmp[0]['created_at']) > 60) {
                        $this->db->where('phone', $mobile);
                        $this->db->update('sms', array('hour_num' => $tmp[0]['hour_num'] + 1, 'day_num' => $tmp[0]['day_num'] + 1, 'content'=>$content.":".$mobile));
                        $result = $sms->sendSms($mobile, $content);
                         $str = 3;
                        $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
                    }

                    if (($longtime - $tmp[0]['created_at']) <= 86400 && $tmp[0]['day_num'] < 5 && ($longtime - $tmp[0]['created_at']) > 3600) {
                        $this->db->where('phone', $mobile);
                        $this->db->update('sms', array('day_num' => $tmp[0]['day_num'] + 1, 'content'=>$content.":".$mobile));
                        $result = $sms->sendSms($mobile, $content);
                         $str = 4;
                        $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
                    }

                        if (($longtime - $tmp[0]['created_at']) > 86400) {
                            $this->db->where('phone', $mobile);
                            $this->db->update('sms', array('day_num' => 0, 'hour_num' => 0, 'minute_num' => 0, 'created_at' => time()));

                         $this->db->where('phone', $mobile);
                        $this->db->update('sms', array('minute_num' => 1, 'hour_num' => $tmp[0]['hour_num'] + 1, 'day_num' => $tmp[0]['day_num'] + 1, 'content'=>$content.":".$mobile));
                        $result = $sms->sendSms($mobile, $content);
                         $str = 5;
                         $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
                        }

                } else {

                    $this->db->insert('sms', array('phone' => $mobile, 'minute_num' => 1, 'hour_num' => 1, 'day_num' => 1, 'created_at' => time()));
                    $result = $sms->sendSms($mobile, $content);

                    $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
                }
            }else{
                $result = $sms->sendSms($mobile, $content);
                $str = 7;
                $this->db->insert('sms_log', array('phone' => $mobile, 'content'=>serialize($result),'created_at' => time()));
            }
            break;

        }

        //var_dump($result);
        return $result;
    }
	/**
	 * 设置发送内容 的字符编码
	 * @param string $outgoingEncoding 发送内容字符集编码
	 */
	function setOutgoingEncoding($outgoingEncoding)
	{
		$this->outgoingEncoding =  $outgoingEncoding;
		$this->soap->soap_defencoding = $this->outgoingEncoding;

	}


	/**
	 * 设置接收内容 的字符编码
	 * @param string $incomingEncoding 接收内容字符集编码
	 */
	function setIncomingEncoding($incomingEncoding)
	{
		$this->incomingEncoding =  $incomingEncoding;
		$this->soap->xml_encoding = $this->incomingEncoding;
	}



	function setNameSpace($ns)
	{
		$this->namespace = $ns;
	}

	function getSessionKey()
	{
		return $this->sessionKey;
	}

	function getError()
	{
		return $this->soap->getError();
	}


	/**
	 *
	 * 指定一个 session key 并 进行登录操作
	 *
	 * @param string $sessionKey 指定一个session key
	 * @return int 操作结果状态码
	 *
	 * 代码如:
	 *
	 * $sessionKey = $client->generateKey(); //产生随机6位数 session key
	 *
	 * if ($client->login($sessionKey)==0)
	 * {
	 * 	 //登录成功，并且做保存 $sessionKey 的操作，用于以后相关操作的使用
	 * }else{
	 * 	 //登录失败处理
	 * }
	 *
	 *
	 */
	function login()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey, 'arg2'=>$this->password);
		$result = $this->soap->call("registEx",$params,	$this->namespace);
		return $result;
	}


	/**
	 * 注销操作  (注:此方法必须为已登录状态下方可操作)
	 *
	 * @return int 操作结果状态码
	 *
	 * 之前保存的sessionKey将被作废
	 * 如需要，可重新login
	 */
	function logout()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		print_r($params);
		$result = $this->soap->call("logout", $params ,
			$this->namespace
		);

		return $result;
	}

	/**
	 * 获取版本信息
	 * @return string 版本信息
	 */
	function getVersion()
	{
		$result = $this->soap->call("getVersion",
			array(),
			$this->namespace
		);
		return $result;
	}



	/**
	 * 短信发送  (注:此方法必须为已登录状态下方可操作)
	 *
	 * @param array $mobiles		手机号, 如 array('159xxxxxxxx'),如果需要多个手机号群发,如 array('159xxxxxxxx','159xxxxxxx2')
	 * @param string $content		短信内容
	 * @param string $sendTime		定时发送时间，格式为 yyyymmddHHiiss, 即为 年年年年月月日日时时分分秒秒,例如:20090504111010 代表2009年5月4日 11时10分10秒
	 * 								如果不需要定时发送，请为'' (默认)
	 *
	 * @param string $addSerial 	扩展号, 默认为 ''
	 * @param string $charset 		内容字符集, 默认GBK
	 * @param int $priority 		优先级, 默认5
	 * @return int 操作结果状态码
	 */
	/*function sendSMS($mobiles=array(),$content,$sendTime='',$addSerial='',$charset='UTF-8',$priority=5)
	{
	    $status1 = $this->getBalance();//获取余额

		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,'arg2'=>$sendTime,
			'arg4'=>$content,'arg5'=>$addSerial, 'arg6'=>$charset,'arg7'=>$priority
			);

		foreach($mobiles as $mobile)
		{
			array_push($params,new soapval("arg3",false,$mobile));
		}
		$result = $this->soap->call("sendSMS",$params,$this->namespace);
		
		$status2 = $this->getBalance();//获取余额
		//给tony发短信邮箱的验证
		$k =0;
		if($status1>=3987 && $status2 <=3987 && $k==0){
		    $this->sendSMS(array ('13661484743'), '【美丽神器】短信余额已不足200元，请及时充值');//发送短信通知
		    $key = 1;
		}
		return $result;

	}*/


	/**
	 * 余额查询  (注:此方法必须为已登录状态下方可操作)
	 * @return double 余额
	 */
	function getBalance()
	{
        $sms = new smsAPI();
        $result = $sms->getParentBalance();
		return $result['sms']['amount'];

	}

	/**
	 * 取消短信转发  (注:此方法必须为已登录状态下方可操作)
	 * @return int 操作结果状态码
	 */
	function cancelMOForward()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$result = $this->soap->call("cancelMOForward",$params,$this->namespace);
		return $result;
	}

	/**
	 * 短信充值  (注:此方法必须为已登录状态下方可操作)
	 * @param string $cardId [充值卡卡号]
	 * @param string $cardPass [密码]
	 * @return int 操作结果状态码
	 *
	 * 请通过亿美销售人员获取 [充值卡卡号]长度为20内 [密码]长度为6
	 */
	function chargeUp($cardId, $cardPass)
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,'arg2'=>$cardId,'arg3'=>$cardPass);
		$result = $this->soap->call("chargeUp",$params,$this->namespace);
		return $result;
	}


	/**
	 * 查询单条费用  (注:此方法必须为已登录状态下方可操作)
	 * @return double 单条费用
	 */
	function getEachFee()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$result = $this->soap->call("getEachFee",$params,$this->namespace);
		return $result;
	}


	/**
	 * 得到上行短信  (注:此方法必须为已登录状态下方可操作)
	 *
	 * @return array 上行短信列表, 每个元素是Mo对象, Mo对象内容参考最下面
	 *
	 *
	 * 如:
	 *
	 * $moResult = $client->getMO();
	 * echo "返回数量:".count($moResult);
	 * foreach($moResult as $mo)
	 * {
	 * 	  //$mo 是位于 Client.php 里的 Mo 对象
	 * 	  echo "发送者附加码:".$mo->getAddSerial();
	 *	  echo "接收者附加码:".$mo->getAddSerialRev();
	 *	  echo "通道号:".$mo->getChannelnumber();
	 *	  echo "手机号:".$mo->getMobileNumber();
	 * 	  echo "发送时间:".$mo->getSentTime();
	 *	  echo "短信内容:".$mo->getSmsContent();
	 * }
	 *
	 *
	 */
	function getMO()
	{
		$ret = array();
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$result = $this->soap->call("getMO",$params,$this->namespace);
		//print_r($this->soap->response);
		//print_r($result);
		if (is_array($result) && count($result)>0)
		{
			if (is_array($result[0]))
			{
				foreach($result as $moArray)
					$ret[] = new Mo($moArray);
			}else{
				$ret[] = new Mo($result);
			}

		}
		return $ret;
	}

	/**
	 * 得到状态报告  (注:此方法必须为已登录状态下方可操作)
	 * @return array 状态报告列表, 一次最多取5个
	 */
	function getReport()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$result = $this->soap->call("getReport",$params,$this->namespace);
		return $result;
	}




	/**
	 * 企业注册  [邮政编码]长度为6 其它参数长度为20以内
	 *
	 * @param string $eName 	企业名称
	 * @param string $linkMan 	联系人姓名
	 * @param string $phoneNum 	联系电话
	 * @param string $mobile 	联系手机号码
	 * @param string $email 	联系电子邮件
	 * @param string $fax 		传真号码
	 * @param string $address 	联系地址
	 * @param string $postcode  邮政编码
	 *
	 * @return int 操作结果状态码
	 *
	 */
	function registDetailInfo($eName,$linkMan,$phoneNum,$mobile,$email,$fax,$address,$postcode)
	{

		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,
			'arg2'=>$eName,'arg3'=>$linkMan,'arg4'=>$phoneNum,
			'arg5'=>$mobile,'arg6'=>$email,'arg7'=>$fax,'arg8'=>$address,'arg9'=>$postcode
		);

		$result = $this->soap->call("registDetailInfo",$params,$this->namespace);
		return $result;

	}



   	/**
   	 * 修改密码  (注:此方法必须为已登录状态下方可操作)
   	 * @param string $newPassword 新密码
   	 * @return int 操作结果状态码
   	 */
   	function updatePassword($newPassword)
   	{

   		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,
			'arg2'=>$this->password,'arg3'=>$newPassword
		);

		$result = $this->soap->call("serialPwdUpd",$params,$this->namespace);
		return $result;

   	}

   	/**
   	 *
   	 * 短信转发
   	 * @param string $forwardMobile 转发的手机号码
   	 * @return int 操作结果状态码
   	 *
   	 */
   	function setMOForward($forwardMobile)
   	{

   		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,
			'arg2'=>$forwardMobile
		);

		$result = $this->soap->call("setMOForward",$params,$this->namespace);
		return $result;
   	}

   	/**
   	 * 短信转发扩展
   	 * @param array $forwardMobiles 转发的手机号码列表, 如 array('159xxxxxxxx','159xxxxxxxx');
   	 * @return int 操作结果状态码
   	 */
   	function setMOForwardEx($forwardMobiles=array())
   	{

		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);

		/**
		 * 多个号码发送的xml内容格式是
		 * <arg2>159xxxxxxxx</arg2>
		 * <arg2>159xxxxxxx2</arg2>
		 * ....
		 * 所以需要下面的单独处理
		 *
		 */
		foreach($forwardMobiles as $mobile)
		{
			array_push($params,new soapval("arg2",false,$mobile));
		}

		$result = $this->soap->call("setMOForwardEx",$params,$this->namespace);
		return $result;


   	}


	/**
	 * 生成6位随机数
	 */
	function generateKey()
	{
		return rand(100000,999999);
	}


}

class Mo{

	/**
	 * 发送者附加码
	 */
	var $addSerial;

	/**
	 * 接收者附加码
	 */
	var $addSerialRev;

	/**
	 * 通道号
	 */
	var $channelnumber;

	/**
	 * 手机号
	 */
	var $mobileNumber;

	/**
	 * 发送时间
	 */
	var $sentTime;

	/**
	 * 短信内容
	 */
	var $smsContent;

	function Mo(&$ret=array())
	{
		$this->addSerial = $ret[addSerial];
		$this->addSerialRev = $ret[addSerialRev];
		$this->channelnumber = $ret[channelnumber];
		$this->mobileNumber = $ret[mobileNumber];
		$this->sentTime = $ret[sentTime];
		$this->smsContent = $ret[smsContent];

	}

	function getAddSerial()
	{
		return $this->addSerial;
	}
	function getAddSerialRev()
	{
		return $this->addSerialRev;
	}
	function getChannelnumber()
	{
		return $this->channelnumber;
	}
	function getMobileNumber()
	{
		return $this->mobileNumber;
	}
	function getSentTime()
	{
		return $this->sentTime;
	}
	function getSmsContent()
	{
		return $this->smsContent;
	}

}

?>
