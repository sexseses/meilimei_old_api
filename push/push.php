<?php

class Push {
    public $deviceToken;//需要在构造时候设置

    //本地证书和密码
    public $localcert ='ck.pen';
    public $passphrase = '';

    /*
     * 功能：构造函数，设置deviceToken
    */
    function Push($deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }
    /*
    功能：生成发送内容并且转化为json格式
    */

    private function createPayload($message,$type,$sound)
    {
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => $sound,
            'type' =>$type
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        return $payload;
    }

    // Put your private key's passphrase here:
   public function  pushData($message,$type,$sound)
    {

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert',$this->localcert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);

        // Open a connection to the APNS server
        //这个为正是的发布地址
         //$fp = stream_socket_client(“ssl://gateway.push.apple.com:2195“, $err, $errstr, 60, //STREAM_CLIENT_CONNECT, $ctx);
        //这个是沙盒测试地址，发布到appstore后记得修改哦
        $fp = stream_socket_client(
        'ssl://gateway.sandbox.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
        exit("Failed to connect: $err $errstr" . PHP_EOL);

        echo 'Connected to APNS' . PHP_EOL;


        // 创建消息
        $payload =$this->createPayload($message,$type,$sound);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $this ->deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
        {
            echo 'Message not delivered' . PHP_EOL;
        }
        else
        {
            echo 'Message successfully delivered' . PHP_EOL;
        }

        // Close the connection to the server
        fclose($fp);
    }
   }
   $n = new Push('f34dfdd1d09b59c3696e61fb7cf7f9aa9ed19c650b91a39129dab823f585471c');
   $n->pushData('dsd',1,'default');
?>