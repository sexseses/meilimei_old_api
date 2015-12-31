<?php

header("Content-Type: text/html; charset=utf8");

    error_reporting(2047);
    $link = mysql_connect('dbmeilimei.mysql.rds.aliyuncs.com','dbuser','1QAZWSX12_');
    
    mysql_select_db('meilimei');
    mysql_query('set names utf8');

    $sql = "SELECT * FROM flash_sale WHERE 1=1 AND display = 1";
    $result = mysql_query($sql,$link);
   while($row = mysql_fetch_object($result)){
          $pd = unserialize($row->product);
          print_r($pd);
          foreach($pd as $vs){
              $upd_sql="insert into flash_sale_tehui (p_id,fs_id) values ({$vs},{$row->id})";
              mysql_query($upd_sql,$link);
       }
    }
    die;
?>