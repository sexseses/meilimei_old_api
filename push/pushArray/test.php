#!/usr/bin/php
<?php
/**
 * Begin Document
 */
 
// Put your private key's passphrase here:
$passphrase = '123456';

// $text_sql = "select * from sms_text";
// $text_rs = $textcon->query($text_sql);
 
// // $text_row = $text_rs->fetch_array();
// $text = strip_tags($text_row['text']);
$message = 'Let’s talk脸大是一种怎样的体验';
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
 
 
$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
$msg = chr(0) . pack('n', 32) . pack('H*', '3ca724dc325eca78b4c0d25b66e3dc03d4b8b8829d85ba00339c5d335e3990f0'). pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
fclose($fp);
// $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
// $msg = chr(0) . pack('n', 32) . pack('H*', '0149b5283daaa60c4c12d27aa9dd8620e487989cb6c92427d7b08bbc98fc2e83'). pack('n', strlen($payload)) . $payload;
// // Send it to the server
// $result = fwrite($fp, $msg, strlen($msg));
// fclose($fp);
 

echo "发送完毕";
?>