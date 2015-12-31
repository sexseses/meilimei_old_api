<!DOCTYPE HTML>
<html>
    <head><meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
     <title><?php echo $WEN_PAGE_TITLE ?></title>
    <meta name="keywords" content="<?php echo $WEN_PAGE_KEYWORDS ?>"> 
    <meta name="description" content="<?php echo $WEN_PAGE_DESCRIPTION ?>"> 
    <script type="text/javascript">

        var base_url =  '<?php echo base_url() ?>';
    </script>
    <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/public.css" >
	<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/content.css" >
	<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/jquery-ui.css" >
     <?php if($this->uri->segment(1) == 'jigouDetail' || $this->uri->segment(1) == 'yishengDetail'){?>
	<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.h.js"></script> 
    <?php }else{ ?>
    <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.js"></script> 
    <?php } ?>
    <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery-ui.js"></script> 
	</head>
    <body>
		<div class="wapper2" style=" background:url(http://static.meilimei.com.cn/public/images/middel_body_bg.gif) repeat-x center top;"> <div class="top"> 
                <div class="top_cont">
                	<div class="logo"><a href="<?php echo base_url() ?>"><img src="http://static.meilimei.com.cn/public/images/logo.png" width="83" height="85"></a></div>
                    <div class="shortcuts">
                        <ul><?php if(isset($notlogin) && !$notlogin &&$this->wen_auth->get_role_id()!=16){?>
						<li><a href="<?php echo base_url() ?>user/dashboard">账户中心</a></li><li><a href="<?php echo base_url() ?>user/logout">退出</a></li>
						<?php }else{ ?><li><a href="<?php echo base_url() ?>user/reg">注册</a></li>
                            <li><a href="<?php echo base_url() ?>user/login">登录</a></li><?php  }; ?>
                        	
                        	<li><a target="_blank" href="http://weibo.com/meilizhensuo"><img src="http://static.meilimei.com.cn/public/images/sina.png" width="25" height="25" alt="关注新浪微博"></a></li>
                            <li><a target="_blank" href="http://t.qq.com/meilizhensuo"><img src="http://static.meilimei.com.cn/public/images/qq.png" width="25" height="25" alt="关注腾讯微博"></a></li>
                        </ul>
                    </div>
                    <div class="menu">
                    	<ul>
                        	<li<?php  if($this->uri->segment(1) == '') echo ' class="selected"' ?>><a href="<?php echo base_url() ?>">首页</a></li>
                            <li<?php  if($this->uri->segment(1) == 'searchYisheng') echo ' class="selected"' ?>> <a href="<?php echo base_url('searchYisheng') ?>">找医生</a></li>
                            <li<?php  if($this->uri->segment(1) == 'searchJigou') echo ' class="selected"' ?>> <a href="<?php echo base_url('searchJigou') ?>">找医院</a></li>
                             <!--li<?php  if($this->uri->segment(1) == 'thematic') echo ' class="selected"' ?>> <a href="<?php echo base_url('thematic') ?>">专题</a></li-->
                       <li<?php  if($this->uri->segment(1) == 'information') echo ' class="selected"' ?>> <a href="<?php echo base_url('information') ?>">资讯</a></li>
                             <!--li><a href="http://korea.meilizhensuo.com/">赴韩整形</a></li-->
                             <!-- li><a href="http://tehui.meilizhensuo.com">限时特惠</a></li-->
                           <?php if(!isset($notlogin) || $notlogin){?>                           
                            <?php } ?>
                           <li<?php  if($this->uri->segment(2) == 'about') echo ' class="selected"' ?>><a href="<?php echo base_url() ?>info/about">关于我们</a></li>
                        </ul>
                    </div>
                </div>
            </div>
