<?php
/**
 * JS_API支付demo
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
*/
//使用jsapi接口
include_once("WxPayPubHelper.php");
	//使用jsapi接口
	$jsApi = new JsApi_pub();

	//=========步骤1：网页授权获取用户openid============
	//通过code获得openid
	if (!isset($_GET['code']))
	{
		//触发微信返回code码
		$tmp_url = WxPayConf_pub::JS_API_CALL_URL;
		$url = $jsApi->createOauthUrlForCode($tmp_url."?body=".$_GET['body']);
		Header("Location: $url"); 
	}else
	{
		//获取code码，以获取openid
	    $code = $_GET['code'];
		$jsApi->setCode($code);
		$openid = $jsApi->getOpenId();
	}
	
	//=========步骤2：使用统一支付接口，获取prepay_id============
	//使用统一支付接口
	$unifiedOrder = new UnifiedOrder_pub();
	
	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	$param_arr=explode('|', $_GET['body']);
	$unifiedOrder->setParameter("openid","$openid");//商品描述
	$unifiedOrder->setParameter("body",$param_arr['0']);//商品描述
	//自定义订单号，此处仅作举例
	$timeStamp = time();
	$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
	$unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号 
	$unifiedOrder->setParameter("total_fee",$param_arr['1']);//总金额
	$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	//非必填参数，商户可根据实际情况选填
	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
	$unifiedOrder->setParameter("attach",$param_arr['2']);//附加数据 
	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
	//$unifiedOrder->setParameter("openid","XXXX");//用户标识
	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

	$prepay_id = $unifiedOrder->getPrepayId();
	//=========步骤3：使用jsapi调起支付============
	$jsApi->setPrepayId($prepay_id);

	$jsApiParameters = $jsApi->getParameters();
	//echo $jsApiParameters;die;
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>微信安全支付</title>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript">
		function onBridgeReady(){
		   WeixinJSBridge.invoke(
		       'getBrandWCPayRequest', <?php echo $jsApiParameters; ?>,
		       function(res){
		       	   //alert(res.err_msg);
		           if(res.err_msg == "get_brand_wcpay_request:ok" ) {
		           		window.location.href="http://m.meilimei.com/zt/vface"; 
		           }
		       }
		   ); 
		}
		function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    onBridgeReady();
			}
		}
	</script>
	<style type="text/css">
        *{ font-family: "Microsoft yahei", Arial;}
        body{ margin: 0px auto; padding: 0px;}
        section{width: 100%; margin: 0px auto; padding: 0px;}
        i{ font-style: normal;}
        /*--header--*/
        .header-sp{ display: block; line-height: 60px; color: #ff558a; font-size: 1em; width: 100%; margin: 0px auto; text-align: center; border-bottom: 1px solid #a7a7a7;}
        .yiyuan-sp{ display: block; background: #f6f6f6; color:#808080; line-height: 50px;text-indent: 1em;}
        .contion-div{ width: 100%; margin: 0px auto;}
        .sp-fg{ display: block; border-bottom: 1px solid #f6f6f6; color: #808080; font-size: 1em; line-height: 50px; width: 95%; margin: 0px auto;}
        .sp-fg i{ float: right;}
        .qrzf-sp {
    display: block;
    margin: 40px 20px 0;
    text-align: center;
}
        .but-zf {
    background: #e472a0 none repeat scroll 0 0;
    border: 0 none;
    border-radius: 8px;
    color: #ffffff;
    font-size: 1.2em;
    margin: 0;
    padding: 10px 0;
    width: 100%;
}
        .juz{ text-align: center;}
        .juz i{ color: #ff558a;}
        @media only screen and (max-width: 320px){
            .header-sp{ font-size: 0.8em; line-height:40px;}
            .yiyuan-sp,.sp-fg{ line-height: 40px; font-size: 0.8em;}
            .but-zf{ border-radius: 6px; padding: 6px 0px; font-size: 1em;}
        }
        .wei-zx{ display: block; line-height: 30px; color: #808080; font-size: 0.8em; width: 90%; margin: 0px auto; text-align: center;}
        .opction-sp{ margin-top: 20px;}
    </style>
</head>
<body onload="">
	<section>
        <span class="header-sp">订单详情</span>
        <div class="contion-div">
            <span class="sp-fg">定金<i>1.00元</i></span>
            <span class="sp-fg">项目<i>“880元”跳楼价瘦脸针</i></span>
            <span class="sp-fg">数量<i>1</i></span>
        </div>
        <span class="yiyuan-sp juz">立即支付：<i>1.00元</i> 定金</span>
        <span class="qrzf-sp"><button type="button" class="but-zf" onclick="callpay()">确认支付</button></span>
        <span class="wei-zx opction-sp">如遇微信支付有任何疑问请拨打免费客服热线：</span>
        <span class="wei-zx">400-6677-245</span>
    </section>
</body>
</html>