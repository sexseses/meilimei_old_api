<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2015/12/23
 * Time: 17:30
 */
class sendsms
{
    //send sms
    public function send($mobile,$content)
    {
        $curl_handler = curl_init();
        $data = array(
            'account' => 'dh56281',
            'password' => md5('$156hqZO'),
            'phones' => $mobile,
            'content' => mb_convert_encoding($content, 'utf-8'),
            'sign' => '【美丽神器】',
        );

        $options = array(
            CURLOPT_URL => 'http://wt.3tong.net/http/sms/Submit',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => 'message='.$this->xmlEncode($data),
        );

        curl_setopt_array($curl_handler, $options);
        $ret = curl_exec($curl_handler);
        curl_close($curl_handler);
        $msg = simplexml_load_string($ret);
        return $msg->result;
    }

    //generate xml
    public function xmlEncode($data) {
        if (!is_array($data)) {
            return null;
        }

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<message>';

        foreach($data as $key=>$val) {
            $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
        }
        $xml .= '</message>';

        return $xml;

    }

}
