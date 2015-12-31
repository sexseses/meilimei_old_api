<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>美丽美 - 美丽神器App - 你的私人美丽顾问</title>
<link href="http://static.meilimei.com.cn/public/new_css/boilerplate.css" rel="stylesheet" type="text/css">
<link href="http://static.meilimei.com.cn/public/new_css/meilishenqi.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="wapper0"><img src="http://static.meilimei.com.cn/public/new_css/bg.jpg"></div>
	<div class="wapper">
    	<div class="top">
        	<div class="topmain">
            	<div class="logo"><a href="http://www.meilimei.com/"><img src="http://static.meilimei.com.cn/public/new_css/logo.png"></a></div>
            	<div class="menu">
                	<ul>
                    	<li><a href="http://www.meilimei.com/info/about">关于我们</a></li><?php if($notlogin){?>
                        <li><a href="http://www.meilimei.com/user/login">登录</a></li>
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
        <div class="login">
            <div class="login_left"><?php echo form_open("user/logCheck",array('id' => 'login'))?>
                <ul>
                    <li class="on">已成为美丽美会员，快速登录</li>
                    <li><input type="text" value="" name="username" pattern="{6,}" placeholder="用户名（邮箱账号或手机号）"></li>
                    <li><input type="password" name="password" value="" pattern="{6,}" placeholder="请输入密码"></li>
                    <li><input type="submit" class="button1" value="立即登录"></li>
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
                <div class="guwen_right">
                    <ul>
                        <li><strong>30</strong> 元/月</li>
                        <li><input type="submit" value="立即购买"></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
        <div class="about">
            <div class="about_main"><strong>&nbsp;关于我们</strong><br><span>&nbsp;&nbsp;美丽美是一个专注于美丽的在线平台，提供给广大闺蜜们最in最hot的资讯和互动空间，想更多的了解我们？快去苹果AppStore或各大安卓市场<br>&nbsp;&nbsp;搜索“美丽神器”下载看看吧！</span><br>【活动】<br><span>&nbsp;&nbsp; 所有关于美丽的周活动、月活动、季活动都在美丽神器第一时间更新，第一手获得信息赢得美丽神器活动终生免费券</span><br>【秘密】<br><span>&nbsp;&nbsp;你在这里的一切活动都将被保密</span><br>【在线闺蜜】<br><span>&nbsp;&nbsp;寻找和你一样经历的MM，成为最亲密的闺蜜</span><br><br><span>&nbsp;&nbsp;如遇到问题，请与我们联系：<br>&nbsp;&nbsp;电话：400-6677-245<br>&nbsp;&nbsp;Q Q：747242966<br>&nbsp;&nbsp;邮件：hi@meilimei.com</span></div>
        </div>
        <div class="foot"><img src="http://static.meilimei.com.cn/public/new_css/weixin.jpg" width="114" height="114"><br>关注美丽美官方微信:meilishenqiapp<br>2013-2014 © 版权所有 - 美丽美    客服电话: 400-6677-245  沪ICP备13027544号-1</div>
    </div>
</body>
</html>
