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
  <?php if($islog){ ?>
  <h3 class="islogs">您已经报名成功，<br>可以开始游戏了！<br>每小时只有前10名哦~请加油！</h3>
  <?php echo form_open(); ?>
   <input type="hidden" name="resetbm" value="1">
  <button type="submit" id="submitbtn"  class="ui-btn ui-corner-all">取消报名</button>
  </form>
  <?php }else{ ?>
  <?php echo form_open(); ?>
  <div id="regf">
     <input type="text" style="margin:10px 0 5px;" name="uname" id="uname" required placeholder="输入名字" value=""> 
     <input type="text"  name="phone" id="phone" pattern="^0{0,1}(1[2-9][0-9])[0-9]{8}$" required placeholder="输入手机" value="">
     </div>
     <ul id="fontindex"><li>输入姓名+手机号，进行报名</li><li>最短的时间里找齐十二生肖</li><li>率先成功找齐十二生肖的小伙伴
1-6名<br>将获得“幸运大转盘”的抽奖机会7-10名<br>将获得1933提供的纪念礼品一份</li>
</ul>
<button type="submit" id="submitbtn" class="ui-btn ui-corner-all">报 名</button>
  </form><?php } ?>
</div>
<script>
$(function(){
	$("#submitbtn").click(function(){$(this).value('提交中...');})
})
</script>
</body>
</html>
