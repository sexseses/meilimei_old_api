<?php
/**
 * 发送短信类(大汉三通)
 * Created by PhpStorm.
 * User: wanglulu
 * Date: 15/5/4
 * Time: 下午5:43
 */

class smsAPI {

    /* 帐号 */
    private $user = 'dh56281';


    /* 密码 */
    private $pwd = '$156hqZO';


    /* 入口地址 */
    static $gateway = 'http://wt.3tong.net/http/sms/Submit';


    /* 编码 */
    private $encoding = 'UTF-8';


    /* 签名 */
    private $Signature = '【美丽神器】';


    /**
     * 生成短信验证码
     * @param int $num  验证码位数
     * @return string
     */
    public function createSmsCode( $num=6 ) {

        //验证码字符数组
        $n_array = range(0, 9);

        //随机生成$num位验证码字符
        $code_array = array_rand($n_array, $num);

        //重新排序验证码字符数组
        shuffle( $code_array );

        //生成验证码
        $code = implode('', $code_array);

        return $code;

    }


    /**
     * 短信内容
     * @param $template
     * @param $data_array
     * $data_array = array(
     *      'yzm' => string 验证码,
     *      'mobile' => int 手机号
     * )
     * @return string
     */
    public function getSmsContent( $template, $data_array ) {

        $content = '';

        if ( $template == 'register' ) {
            $content .= '尊敬的客户，您的本次验证码为' . $data_array['yzm'] . '有效时间' . APP_SMS_CACHE_TIME . '秒';
        } else if ( $template == 'login' ) {
            $content .= '尊敬的客户，您的本次验证码为' . $data_array['yzm'] . '有效时间' . APP_SMS_CACHE_TIME . '秒';
        }

        return $content;

    }


    /**
     * 发送短信
     * @param int $mobile  手机号
     * @param string $sms_content 短信内容
     * @return array
     */
    public function sendSms( $mobile, $sms_content ) {

        //实例化http类
        $curlHttp = new curlHttp();

        $send_array = array(
            'account' => $this->user,
            'password' => md5($this->pwd),
            'phones' => $mobile,
            'content' => mb_convert_encoding( $sms_content, $this->encoding ),
            'sign' => $this->Signature,
            'sendtime' => ''
        );

        $result_array = $curlHttp->post( self::$gateway, 'message=' . $this->xmlEncode($send_array) );

        if ($result_array['code'] == 200) {

            $data_array = $this->xmlDecode( $result_array['data'] );

            if ($data_array['result'] == 0) {

                return array( 'message' => 'ok','code'=>200, 'data'=>$result_array );
            } else {

                return array( 'message' => '短信发送失败', 'code'=>404, 'data'=>$result_array );
            }

        } else {

            return array('message'=> '短信发送失败', 'code'=>404, 'data'=>$result_array );
        }

    }


    /**
     * 封装xml字符串为数组
     * @param $data
     * @return string
     */
    private function xmlEncode($data) {

        if (!is_array($data)) {
            return null;
        }

        $xml = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
        $xml .= '<message>';

        foreach($data as $key=>$val) {
            $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
        }
        $xml .= '</message>';

        return $xml;

    }

    /**
     * 解析xml字符串为数组
     * @param $xml_string
     * @return array|null
     */
    private function xmlDecode($xml_string) {

        if (!isset($xml_string) || empty($xml_string)) {
            return null;
        }

        if ($this->encoding != 'UTF-8') {
            $xml_string = iconv($this->encoding, 'UTF-8', $xml_string);
        }

        $xml_array = (array)simplexml_load_string( $xml_string, 'SimpleXMLElement', LIBXML_NOCDATA );
        if (!$xml_array) {
            return null;
        }
        foreach($xml_array as &$value) {
            if (is_object($value)) {
                $value = (array)$value;
            }
        }
        return $xml_array;

    }


}