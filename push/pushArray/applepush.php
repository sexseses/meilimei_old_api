#!/usr/bin/php
<?php
/**
 * Begin Document
 */
 
// Put your private key's passphrase here:
$passphrase = '123456';
 
$message = '居然有这么奇葩的美容方法，胆小勿点！';

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert',  '/usr/cert/online.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
stream_context_set_option($ctx, 'ssl', 'cafile',  '/usr/entrust_2048_ca.cer');

 

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'whiz'
	);

$push = array('type' => 'topic', 'id' => 104593, 'page' => 1);

$body['aps'] = array_merge($body['aps'],$push);
// Encode the payload as JSON
$payload = json_encode($body);

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
echo "发送完毕";
?>




 




