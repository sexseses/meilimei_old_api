<?php
include_once("/mnt/meilimei/weixinpay/WxPayPubHelper.php");
 //使用通用通知接口
$notify = new Notify_pub();
	     
	    //存储微信的回调
	    $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	    $notify->saveData($xml);
	
	    //验证签名，并回应微信。
	    //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	    //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	    //尽可能提高通知的成功率，但微信不保证通知最终能成功。
	    if($notify->checkSign() == FALSE){
	        $notify->setReturnParameter("return_code","FAIL");//返回状态码
	        $notify->setReturnParameter("return_msg","签名失败");//返回信息
	    }else{
	        $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	    }
	    // 	    $returnXml = $notify->returnXml();
	    // 	    echo $returnXml;
	
	    //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	     
	
	    if($notify->checkSign() == TRUE)
	    {
	        if ($notify->data["return_code"] == "FAIL") {
	
	        }elseif($notify->data["result_code"] == "FAIL"){
	
	        }else{
	            $this->db->where('pay_id', $notify->data['attach']);
	            $order = $this->tehuiDB->get('order')->row_array();
	            if ($order['origin'] != ($notify->data['total_fee']*0.01)) {
	                echo 'error';
	                exit;
	            }
	            $this->tehuiDB->where('pay_id', $notify->data['attach']);
	            $this->tehuiDB->update('order', array (
	                'state' => 'pay',
	                'money' => ($notify->data['total_fee']*0.01),
	                'service' => 'wxpay',
	                'trade_no' => $notify->data['out_trade_no'],
	                'pay_time' => time()));
	            $this->payCall($order);
	            echo "success";
	        }
	    }
?>