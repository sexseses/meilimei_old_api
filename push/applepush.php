#!/usr/bin/php
<?php
/**
 * Begin Document
 */
 
// Put your private key's passphrase here:
$passphrase = '123456';

// Put your alert message here:
$con = new mysqli("dbmeilimei.mysql.rds.aliyuncs.com","dbuser","1QAZWSX12_","meilimei");
//$textcon = new mysqli("kingsley.mysql.rds.aliyuncs.com","kingsley","123123","mlm_event");
//$textcon->query("SET NAMES utf8");

if (!$con){
  die('Could not connect: ' . mysql_error());
}


// $text_sql = "select * from sms_text";
// $text_rs = $textcon->query($text_sql);
 
// // $text_row = $text_rs->fetch_array();
// $text = strip_tags($text_row['text']);
$message = '新版本更新啦！快来看看';

 

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert',  '/usr/cert/online.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
stream_context_set_option($ctx, 'ssl', 'cafile',  '/usr/entrust_2048_ca.cer');

 

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'whiz'
	);

// Encode the payload as JSON
$payload = json_encode($body);
$start = intval($argv['1']);
$num = $start;
$start = ($start * 1000)+1;
 

$sql = "SELECT devicetoken,uid FROM apns_devices limit {$start},1000";
$rs = $con->query($sql); 

$row = $rs->fetch_array();

 

while($row){
 
 
    // CREATE DATABASE OBJECT ( MAKE SURE TO CHANGE LOGIN INFO IN CLASS FILE )
    // Open a connection to the APNS server
    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    
    if ($fp){
    	   echo 'Connected to APNS' . 'num='.$num.PHP_EOL;
    }

	// Build the binary notification
	$msg = chr(0) . pack('n', 32) . pack('H*', $row[0]) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));
	fclose($fp);
}

?>




 




