<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>spider</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">
<div style="margin:auto ; width:600px;background:#fff;padding:20px;border:solid 1px #800080">
  <?php echo form_open("spider/submit",array('id' => 'spider'))?>

  <label style="width:80px;display:inlie-block">抓取网址：</label><input type="text" name="url" value="" style="line-height:25px;height:25px;width:350px;" size="46" maxlength="60"/><br><br>
  <label style="width:80px;display:inlie-block">页面数：</label> <input type="text" readonly="readonly" name="page" value="1" style="line-height:25px;height:25px;width:80px;" size="40" maxlength="40"/>
  <label style="width:80px;display:inlie-block">城市名称：</label><input type="text" name="city" value="" style="line-height:25px;height:25px;width:120px;" size="20" maxlength="40"/><br><br>
  <input type="submit" name="submit" onClick="dealing()" value="开始抓取"/>
  </form>
  <div id="statusTxt" style="display:none;padding:20px; 30px;font-size:14px;"><img src="http://static.5lulu.com/images/public/loading-publish.gif"/> 系统抓取中....</div>
  <script language="JavaScript" type="text/javascript">
  function dealing(){document.getElementById("statusTxt").style.display ="block"; document.getElementById("spider").style.display ="none"; }
</script>

</div>
<div style="height:30px"></div>
</body>
</html>
