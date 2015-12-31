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
<!doctype html>
<html>
<head>
	<title>美丽神器</title>
    <meta charset="utf-8">
    <meta http-equiv="cleartype" content="on">
    <meta name="description" content="美丽神器">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui">
    <meta name="format-detection" content="telephone=no, email=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="http://m.meilimei.com/wx/skin/css/app.css">
    <script src="http://m.meilimei.com/wx/skin/js/plugin/zepto.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript">
		function onBridgeReady(){
		   WeixinJSBridge.invoke(
		       'getBrandWCPayRequest', <?php echo $jsApiParameters; ?>,
		       function(res){
		           if(res.err_msg == "get_brand_wcpay_request:ok" ) {
		           		 window.location.href="http://m.meilimei.com/wx/#!/return/<?php echo $param_arr['2']; ?>"; 
		           }else{
		           		alert('你已取消支付！');
		           		window.location.href="http://m.meilimei.com/wx/#!/continue_checkout/<?php echo $param_arr['2']; ?>";
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
		Zepto(function($){
				$.ajax({
					type: 'GET',
					url:'http://m.meilimei.com/wx/checkout/returnorder/<?php echo $param_arr['2']; ?>?callback=JSON_CALLBACK',
					dataType: 'jsonp',
					timeout: 3000,
					success: function(data){
						var ck = data.data['order_info'];
						//$('#order_id').html('订单号：<?php echo $param_arr['2']; ?>');
						$('#title1,#title2').html(ck.title);
						$('#address').html(ck.address);
						$('#mobile').html('手机号：'+ck.tel);
						$('#image').attr('src','http://tehui.meilimei.com/static/'+ck.image);
						$('#quantity').html('数量 x'+ck.quantity);
						$('#team_price').html(ck.team_price*ck.quantity);
						$('#reser_price').html(ck.reser_price);
						$('#market_price').html(ck.market_price);
					}
				});
		})
	</script>
</head>
<body>
	<div id="sp-page">
		<div class="ng-scope">
			<header>
				<a class="pl mlm m-a back" href="http://m.meilimei.com/wx/#!/continue_checkout/<?php echo $param_arr['2']; ?>"></a>
				<span class="tit">订单确认</span>
			</header>
			<article id="">
				<div class="return">
					<span class="spands">你的信息</span>
					<span class="spands orderid" style="color:#333;" id="mobile"></span>
					<span class="spands">机构</span>
					<span class="spands" style="color:#333;" id="title1"></span>
					<span class="spands" id="address"></span>
					<div class="checkout">
						<ul class="from_li list">
							<li style="padding: 0;"><img src="" id="image" width="120" height="100" /></li>
							<li>
								<span class="spands mll10" id="title2"></span>
								<div class="mll10">
									<span style="color:#000" id="quantity"></span>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="th_view">
					<div class="addtocart">
						<span class="fl">
							<span class="spands deposit">支付定金:<b class=" m-rmb" id="team_price"></b></span>
							<span class="spands make">预约价<small class="m-rmb" id="reser_price"></small><del class="m-rmb" id="market_price"></del></span>
						</span>
						<span class="spandt fr">
							<span class="add_but" onclick="callpay()">结算</span>
						</span>
					</div>
				</div>
			</article>
		</div>
	</div>
	<div class="footer-container">
	    <a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.work.beauty">
	        <div class="footer">
	            <ul>
	              <li class="flogo_mini fl"><img src="http://m.meilimei.com/skin/images/logo_mini.png"></li>
	              <li><div class="fcotop">下载美丽神器,全部评论在此~</div>
	              <span>评论<em>(91)</em></span>
	              <span>专家推荐<em>(10)</em></span></li>
	             <li class="flook pr"><span class="bbutton bradius tolinks">立即查看</span></li>
	            </ul>
	        </div>
	    </a>
	</div>
</body>
</html>