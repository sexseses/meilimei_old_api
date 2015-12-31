<!DOCTYPE HTML>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>美丽诊所</title>
    <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/public.css" >
	<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/content.css" >
    <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" >
    <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/manage_center.css" >
    <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/new.css" >
        <script type="text/javascript">  
            var base_url =  '<?php echo base_url() ?>';
        </script>
	<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.js"></script> 
	<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/jquery-ui.css" >
	
	<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery-ui.js"></script> 
 
	</head>
    <body>
		<div class="wapper2"> <div class="top"> 
                <div class="top_cont">
                	<div class="logo"><a href="<?php echo base_url() ?>"><img src="http://static.meilimei.com.cn/public/images/logo.png" width="83" height="85"></a></div>
                    <div class="shortcuts">
                        <ul><?php if(isset($notlogin) && !$notlogin){?>
							<li><a href="<?php echo base_url() ?>manage">管理中心</a></li>
						<?php }else{ ?><li><a href="<?php echo base_url() ?>user/reg">注册</a></li>
                            <li><a href="<?php echo base_url() ?>user/login">登录</a></li><?php  }; ?>
                        	
                        	<li><a href="http://weibo.com/meilizhensuo"><img src="http://static.meilimei.com.cn/public/images/sina.png" width="25" height="25" alt="关注新浪微博"></a></li>
                            <li><a href="http://t.qq.com/meilizhensuo"><img src="http://static.meilimei.com.cn/public/images/qq.png" width="25" height="25" alt="关注腾讯微博"></a></li>
                        </ul>
                    </div>
                    <div class="menu">
                    	<ul>
                        	                   </ul>
                    </div>
                </div>
            </div>