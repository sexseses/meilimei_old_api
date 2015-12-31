<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if(!empty($infos)): 
?>
<div class="leftboard"><h3>热门排行/TOP</h3>
<?php 
$i = 1;
foreach($infos as $r){
	echo '<a href="#"><strong>'.$i.'</strong>  '.$r['title'].'</a>';
	$i++;
}
?>
 </div> 
<?php endif ?>