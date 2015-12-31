<?php
$con = new mysqli("10.10.10.5","root","meilimei","mlm_test");
$con->query("set names UTF8");
$id = $_GET['id'];
$sql = "select * from new_items";
echo $sql;
$rs = $con->query($sql);

while($row = $rs->fetch_array()){
    $name_arr = explode(",",$row['oldname']);
    print_r($name_arr);
    if(count($name_arr)>1){

    }else{
    	$sql = "select new from cover2 where old = '$name_arr[0]'";
    	$coverrs = $con->query($sql)->fetch_array();
    	if($coverrs){
            $upsql = "update new_items set name = '$coverrs[0]' where oldname = '$name_arr[0]'";
            $uprs = $con->query($upsql);
            var_dump($uprs);
        }
    }
	echo "<br/>";
}

$con->free();