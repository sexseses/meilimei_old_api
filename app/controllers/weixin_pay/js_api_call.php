 
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript">
		function onBridgeReady(){
		   WeixinJSBridge.invoke(
		       'getBrandWCPayRequest', <?php echo $jsApiParameters; ?>,
		       function(res){
		       		alert(res.err_msg);
		           if(res.err_msg == "get_brand_wcpay_request:ok" ) {}
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
</head>
<body>
	</br></br></br></br>
	<div align="center">
		<button type="button" onclick="callpay()" >贡献一下</button>
	</div>
</body>
</html>