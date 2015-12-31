<?php
$con = new mysqli("dbmeilimei.mysql.rds.aliyuncs.com","dbuser","1QAZWSX12_","tehui");
$con->query("set names UTF8");
$id = $_GET['id'];
$sql = "select * from team where id > {$id} order by  id  desc";
echo $sql;
$rs = $con->query($sql);

while($row = $rs->fetch_array()){
    $city_arr = explode(",",$row['city_ids']);
    //print_r($city_arr);
     foreach ($city_arr as &$v){
         $v = trim($v,"@");
		 $v = explode("@",$v);
		 $czone = "@";
		 foreach($v as $sub_v){
			 if($sub_v <> 0){
				$sub_sql = "select czone from category where id = $sub_v";
				//echo $sub_sql;
			 	$sub_rs = $con->query($sub_sql);
				$sub_row = $sub_rs->fetch_array();
				$czone .= $sub_row['czone']."@";
				//UPDATE `team` SET `czone`='@上海@' WHERE (`id`='1536')
				$u_sql = "UPDATE `team` SET `czone`='$czone' WHERE (`id`= $row[id])";
		     	$con->query($u_sql);
			 }
		 }
     }
	echo $czone;
	echo "<br/>";
}