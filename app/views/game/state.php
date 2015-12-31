<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>十二生肖收集状态</title>
<link rel="stylesheet" href="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.css">
<link rel="stylesheet" href="<?php echo site_url() ?>game/main.css">
<script src="<?php echo site_url() ?>game/jquery-2.1.1.min.js"></script>
<script src="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.js"></script>
</head>
<body>
<div data-role="page" class="jqm-demos jqm-home"> <div data-role="header" role="banner" style="overflow:hidden;">
    <h4 style="text-align:center;">十二生肖收集状态</h4>
  </div>
  <ul  class="ui-listview listviews">
    <li <?php echo !isset($step[1])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/1.png" width="100%"> <label>鼠</label></li>
    <li <?php echo !isset($step[2])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/2.png" width="100%"> <label>牛</label></li>
    <li <?php echo !isset($step[3])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/3.png" width="100%"> <label>虎</label></li>
    <li <?php echo !isset($step[4])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/4.png" width="100%"> <label>兔</label></li>
    <li <?php echo !isset($step[5])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/5.png" width="100%"> <label>龙</label></li>
    <li <?php echo !isset($step[6])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/6.png" width="100%"> <label>蛇</label></li>
    <li <?php echo !isset($step[7])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/7.png" width="100%"> <label>马</label></li>
    <li <?php echo !isset($step[8])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/8.png" width="100%"> <label>羊</label></li>
    <li <?php echo !isset($step[9])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/9.png" width="100%"> <label>猴</label></li>
    <li <?php echo !isset($step[10])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/10.png" width="100%"> <label>鸡</label></li>
    <li <?php echo !isset($step[11])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/11.png" width="100%"> <label>狗</label></li>
    <li <?php echo !isset($step[12])?'class="gray"':''?>><img src="<?php echo site_url() ?>game/images/12.png" width="100%"> <label>猪</label></li>
  </ul>
</div>
</body>
</html>
