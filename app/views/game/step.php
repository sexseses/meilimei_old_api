<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>报到</title>
<link rel="stylesheet" href="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.css">
<link rel="stylesheet" href="<?php echo site_url() ?>game/main.css">
<script src="<?php echo site_url() ?>game/jquery-2.1.1.min.js"></script>
<script src="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.js"></script>
</head>
<body>
<div data-role="page" class="jqm-demos jqm-home">
  <div class="logo"> </div>
  <div class="resbg" style="background:url(<?php echo site_url() ?>game/images/pic_<?php echo $pic ?>.png) no-repeat;background-size:100% auto;"></div>
  <div class="resinfo">
  <h3>恭喜您已成功找到</h3>
  <h2>“<?php echo $text ?>”</h2>
  <h3>赶紧去寻找下一个吧</h3>
  <a href="http://www.meilimei.com/game/state" id="submitbtn" class="ui-btn ui-corner-all">查看已完成</a>
  </div>
</div>
</body>
</html>
