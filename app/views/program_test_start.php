<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Prorammer Test</title>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/public.css" ><style type="text/css">li{line-height:25px;margin:10px auto;input{border:solid 1px #666666;}</style>
</head>
<body>
<div class="wapper2" > 
<form method="post" style="width:300px;margin:200px auto;" action="<?php echo site_url('test/submit'); ?>"> 
<ul><input type="hidden" name="uname" value="<?php echo $this->input->post('user')?> ">
<li>请最快速度完成，这个很重要</li>
<li><label>第一题</label><img src="http://www.meilimei.com/images/ss.png">约等于<input type="text" name="t1" value=""></li>
<li><label>第二题</label><br>现今非常流行的SQL（数据库语言）注入攻击属于下列哪一项漏洞的利用？ A. 域名服务的欺骗漏洞
B. 邮件服务器的编程漏洞
C. WWW服务的编程漏洞
D. FTP服务的编程漏洞<br> <input type="text" name="t2" value=""></li>
<li><label>第三题</label>方程x+y+z=8;x-y=5;x-z=2</li>
<li>x=<input type="text" name="t3" value=""></li>
<li>y=<input type="text" name="t4" value=""></li>
<li>z=<input type="text" name="t5" value=""></li> 
</ul>
<button type="submit">提交</button>
</form>
</div>
</body>
</html> 