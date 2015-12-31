<?php
$con = new mysqli("10.10.10.5","root","meilimei","mlm_test");
$con->query("set names UTF8");

$sql = "select nid,tag from note limit 4001,1000";
echo $sql;
$rs = $con->query($sql);

while($row = $rs->fetch_array()){
    $name_arr = explode("，",$row['tag']);
    print_r($name_arr);
    if(count($name_arr)>1){

    }else{
    	$sql = "select name from new_items where oldname = '$name_arr[0]'";
    	$coverrs = $con->query($sql)->fetch_array();
    	if($coverrs){
            $upsql = "update note set item_name = '$coverrs[0]' where nid = '$row[0]'";
			echo $upsql;
            $uprs = $con->query($upsql);
            var_dump($uprs);
        }
    }
	echo "<br/>";
}

$con->free();