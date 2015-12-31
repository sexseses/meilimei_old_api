<?php
header("location:http://www.meilimei.com/user/reg");
exit();
if ($wen_msg =$this->session->flashdata('msg')) {
    echo $wen_msg;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>美丽美 - 美丽神器App - 你的私人美丽顾问</title>
<link href="http://static.meilimei.com.cn/public/new_css/boilerplate.css" rel="stylesheet" type="text/css">
<link href="http://static.meilimei.com.cn/public/new_css/meilishenqi.css" rel="stylesheet" type="text/css">
<style type="text/css">
#capsend { background:#4fbfb7; border:none; border-radius: 10px; color: #fff; font-size: 14px; height: auto; line-height: inherit; padding: 5px 10px; position: absolute; right: 30px; top: 10px; width: auto; cursor: pointer;}
.login_left {float: left; width: auto;}
.login_right {float: right;}
#bug {color:#f00;}
.gray {background: #ccc !important;color: #999 !important;}
</style>
</head>
<body>
	<div class="wapper0"><img src="http://static.meilimei.com.cn/public/new_css/bg.jpg"></div>
	<div class="wapper">
    	<div class="top">
        	<div class="topmain">
            	<div class="logo"><a href="http://www.meilimei.com/"><img src="http://static.meilimei.com.cn/public/new_css/logo.png"></a></div>
            	<div class="menu">
                	<ul>
                    	<li><a href="#about">关于我们</a></li><?php if($notlogin){?>
                        <li><a href="#login">登录</a></li>
                        <li><a href="http://www.meilimei.com/user/reg">注册</a></li><?php }else{ ?>
                        <li><a href="http://www.meilimei.com/user/dashboard">账户中心</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="banner">
        	<ul>
            	<li><a href="http://itunes.apple.com/cn/app/id654644428/">苹果下载</a></li>
                <li><a href="http://www.meilimei.com/m/meilishenqi.apk">安卓下载</a></li>
            </ul>
        </div>
        <?php if($notlogin){?>
        <div class="login" style=" overflow: hidden; height: auto;"><a id="reg"></a><a id="login"></a>
            <div class="login_left"><?php echo form_open("user/logCheck",array('id' => 'login'))?>
                <ul>
                    <li class="on">已成为美丽美会员，快速登录</li>
                    <li><input id="username" type="text" value="<?php echo isset($username)? $username:'' ?>" name="username" autofocus  required  placeholder="用户名（邮箱账号）"></li>
                    <li><input type="password" name="password" required value="<?php echo isset($password)? $password:'' ?>" placeholder="请输入密码"></li>
                    <?php if($_COOKIE['user_login_error'] > 4){ ?>
                    <li>
                    	<div class="regp" style="position: relative;">
							<input style="background:#fff;" id="newcaptcha" type="text" name="newcaptcha" value="<?php echo isset($code)? $code:''?>"  autocomplete="off" placeholder="验证码"  maxLength="6" onkeyup="value=this.value.replace(/\D+/g,'')" required="required"/>
							<input type="button" value="获取验证码" class="capsend" id="capsend" name="capsend">
						</div>
					</li>
                    <?php } ?>
					<li><span id="bug"></span></li>
					<!-- <li class=""><input type="submit" class="" value="立即登录"> -->
                    <li class="loginbutton"><input type="submit" class="button1" value="立即登录">
				    <div><span style="dispaly:black;color:red;"><?php echo isset($msg)? $msg:'' ?></span></div></li>
                </ul><?php echo form_close()?>
            </div>
            <div class="login_right">
                <ul>
                    <li class="on2">我还不是会员，我要注册</li>
                    <li class="on3"><a href="http://www.meilimei.com/user/reg">马上注册</a></li>
                </ul>
            </div>
        </div><?php } ?>
        <div class="guwen">
            <div class="guwen_main">
                <div class="guwen_left"><strong>&nbsp;美丽顾问服务</strong><br>【美丽也可以私人定制】<br><span>&nbsp;&nbsp;资深美丽顾问一对一帮你解决所有关于美丽的问题，那些无从下口、难以启齿的美丽问题统统解决</span><br>【全球一手美容咨询】<br><span>&nbsp;&nbsp;全年365天实时更新全球美容咨询，欧美范、日系、韩流等从头到脚统统一手掌握，让你成为百变天后</span></div>
              
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
<div class="about">
            <div class="about_main"><a id="about"></a><strong>&nbsp;关于我们</strong><br><span>&nbsp;&nbsp;美丽美是一个专注于美丽的在线平台，提供给广大闺蜜们最in最hot的资讯和互动空间，想更多的了解我们？快去苹果AppStore或各大安卓市场<br>&nbsp;&nbsp;搜索“美丽神器”下载看看吧！</span><br>【活动】<br><span>&nbsp;&nbsp; 所有关于美丽的周活动、月活动、季活动都在美丽神器第一时间更新，第一手获得信息赢得美丽神器活动终生免费券</span><br>【秘密】<br><span>&nbsp;&nbsp;你在这里的一切活动都将被保密</span><br>【在线闺蜜】<br><span>&nbsp;&nbsp;寻找和你一样经历的MM，成为最亲密的闺蜜</span><br><br>
            <span>&nbsp;&nbsp;如遇到问题，请与我们联系：<br>
            &nbsp;&nbsp;电话：400-6677-245<br>
            &nbsp;&nbsp;商务推广合作QQ：598674598  &nbsp;&nbsp;机构专家入驻QQ：164666898   &nbsp;&nbsp;资本合作QQ：747242966<br>
            &nbsp;&nbsp;邮件： hi&lt;a&gt;meilimei.com</span></div>
        </div>
        <div class="foot"><img src="http://static.meilimei.com.cn/public/new_css/weixin.jpg" width="114" height="114"><br>关注美丽美官方微信:meilishenqiapp<br>荣浪信息科技（上海）有限公司<br />2013-2014 © 版权所有 - 美丽美    客服电话: 400-6677-245  沪ICP备13027544号-1</div>
        <div style="text-align: center;">
            <a id='___szfw_logo___' href='https://search.szfw.org/cert/l/CX20150128006507006607' target='_blank'><img src='images/meilimei.jpg'></a>
            <script type='text/javascript'>(function(){document.getElementById('___szfw_logo___').oncontextmenu = function(){return false;}})();</script>
        </div>
    </div>
<script src="http://kefu.qycn.com/vclient/state.php?webid=50729" language="javascript" type="text/javascript"></script> 
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.js"></script>
<?php if($_COOKIE['user_login_error'] > 4 ){ ?>}
<script>
	var wait=60; 
	var wait2=60; 
	function time(o) {
		if (wait == 0) {  
			$(o).removeAttr("disabled");
			$(o).val('获取验证码');
			wait = 60;  
		}else{
			$(o).attr("disabled", true); 
			$(o).val("重新发送(" + wait + ")");
			wait--;  
			setTimeout(function() {
				time(o)  
			},  
			1000)  
		}  
	}
	$("#capsend").click(function(){
		if($('#username').val()!=''){
			$.ajax({
			   type: "POST",
			   url: "http://www.meilimei.com/user/login_send_sms",
			   data:{ username: $('#username').val()},
			   dataType: 'json',
			   success: function(data){
			   		if(data.state=='000'){
			   			time("#capsend");
			   			$('.button1').removeAttr('disabled').removeClass('gray');
			   		}
			   },
				error: function (data, status, e)
				{
					alert(data.notice);
				}
			});
		}else{
			$('#bug').html('用户名不能为空');
		}
	}); 
</script>
<?php } ?>
</body>
<?php print_r($ipinfo); ?>
</html>
