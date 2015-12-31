<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>管理</title>
<link rel="stylesheet" href="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.css">
<link rel="stylesheet" href="<?php echo site_url() ?>game/main.css">
<script src="<?php echo site_url() ?>game/jquery-2.1.1.min.js"></script>
<script src="<?php echo site_url() ?>game/jquery.mobile-1.4.3.min.js"></script>
</head>
<body>
<div data-role="page" class="jqm-demos jqm-home">
  <div data-role="header" role="banner" style="overflow:hidden;">
    <h4 style="text-align:center;">管理页</h4>
  </div>
  <ol id="datalist" data-role="listview" class="ui-listview">
  <?php foreach($res as $r){
	  echo '<li class="ui-li ui-li-static ui-body-c">'.$r['name'].'， 手机'.$r['phone'].'， 状态:'.(12-$r['order']>0?'未完成':'完成').'<br><span > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;完成:'.$r['order'].'项，时间:'.$r['time'].'</span></li>';
  }
  ?> 
  </ol>
</div>
<script>
function refreshdata(){
	$.get('http://www.meilimei.com/game/getdata',{},function(res) {
		$("#datalist").html(res);
    });
}
$(function(){
	 setInterval("refreshdata()",2500);
})
</script>
</body>
</html>
