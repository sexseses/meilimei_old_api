<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if(!empty($infos)): 
?>
<div class="leftboard"> 
<?php 
$i = 1;
foreach($infos as $r){
	echo '<a target="'.$r['type'].'" title="'.$r['title'].'" href="'.$r['link'].'"><img alt="'.$r['title'].'"  title="'.$r['title'].'" style="max-width:300px" src="'.site_url().$r['picture'].'"/></a>';
	$i++;
}
?>
 </div>  
<?php endif ?> 