#!/usr/bin/php
<?php
/**
 * Begin Document
 */
 
// Put your private key's passphrase here:
$passphrase = '123456';

// Put your alert message here:
$con = new mysqli("dbmeilimei.mysql.rds.aliyuncs.com","dbuser","1QAZWSX12_","meilimei");
$con->query("SET NAMES utf8");

if (!$con){
  die('Could not connect: ' . mysql_error());
}


// $text_sql = "select * from sms_text";
// $text_rs = $textcon->query($text_sql);
 
// // $text_row = $text_rs->fetch_array();
// $text = strip_tags($text_row['text']);
$message = '[独家直播]上周日小美娘送免费瘦脸针，幸运美粉里有你吗？';
$push = array('type' => 'topic', 'id' => 106795, 'page' => 1);
 

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert',  '/usr/cert/online.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
stream_context_set_option($ctx, 'ssl', 'cafile',  '/usr/entrust_2048_ca.cer');

 

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'whiz'
	);



 $body['aps'] = array_merge($body['aps'],$push);
// Encode the payload as JSON
$payload = json_encode($body);
 
 

$begin = 1;
$end = 70000;
$sql = "SELECT devicetoken FROM apns_devices WHERE  1 = 1 and id > $begin and id < $end";
$rs = $con->query($sql);



$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
$msg = chr(0) . pack('n', 32) . pack('H*', '0def801a57010d0d3779c86b3e8b4424f3ed5880581fc349d78e0ee09e0a4aee'). pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
fclose($fp);

$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
$msg = chr(0) . pack('n', 32) . pack('H*', '82a9041c7685980bd104b6da00b7b6a85bd6516d5553d7b1cbf8a6301e3ed6c6'). pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
fclose($fp);

$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
$msg = chr(0) . pack('n', 32) . pack('H*', '3ca724dc325eca78b4c0d25b66e3dc03d4b8b8829d85ba00339c5d335e3990f0'). pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
fclose($fp);
$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
$msg = chr(0) . pack('n', 32) . pack('H*', '0149b5283daaa60c4c12d27aa9dd8620e487989cb6c92427d7b08bbc98fc2e83'). pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
fclose($fp);
 
$num = $begin;
while($row = $rs->fetch_array()){
    // CREATE DATABASE OBJECT ( MAKE SURE TO CHANGE LOGIN INFO IN CLASS FILE )
    // Open a connection to the APNS server
    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    $num++;
    if($num > $end){
        break;
    }
    if ($fp){
    	   echo 'Connected to APNS' . 'num='.$num.PHP_EOL;
    	   // Build the binary notification
    	   $msg = chr(0) . pack('n', 32) . pack('H*', $row['devicetoken']) . pack('n', strlen($payload)) . $payload;
    	   
    	   // Send it to the server
    	   $result = fwrite($fp, $msg, strlen($msg));
    	   fclose($fp);
    }else{
        echo '链接出错！';
    }
}
echo "发送完毕";
?>