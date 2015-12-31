<style type="text/css">
li{line-height:30px;height:30px;}
input{padding:2px 5px;}
</style>
<form method="post" name="fanlig" action="<?php echo site_url('manage/fanli/Gfanli/'.$id) ?>">
<ul>
<li><label>返积分比例</label><input type="text" value="<?php echo $rate ?>" name="jifen" /></li>
<li><label>返现金比例</label><input type="text" value="<?php echo $moneyrate ?>" name="xianjin" /></li>
<li><input type="submit" id="TB_closeWindowButton" name="submit" value="提交" /></li>
</ul>
</form>
 