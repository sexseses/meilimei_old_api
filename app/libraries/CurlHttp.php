<?php
/**
 * curlHttp数据传输类
 * @package  htdocs
 * @author   wanglulu
 * @version  1.0
 */

class CurlHttp {

    public $ap='ok';

    public function put( $url, $data, $http_opts = null ) {

        if (!isset($url) || empty($url)) {
            return array( 'code' => 400, 'message' => '缺少请求链接' );
        }
        if (!isset($data) || empty($data)) {
            return array( 'code' => 400 ,'message' => '缺少请求参数');
        }
        if (is_array($data)) {
            $data = http_build_query($data);
        }

        $curl_handler = curl_init();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HEADER => false,
            CURLOPT_USERAGENT => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36',
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array('Content-Length: ' . strlen($data))
        );

        if (is_array($http_opts)) {
            foreach ($http_opts as $key => $value){
                $options[$key] = $value;
            }
        }

        curl_setopt_array($curl_handler, $options);
        $curl_result = curl_exec($curl_handler);
        $curl_http_status = curl_getinfo($curl_handler,CURLINFO_HTTP_CODE);
        if ($curl_result == false) {
            $error = curl_error($curl_handler);
            curl_close($curl_handler);
            return array( 'code' => $curl_http_status, 'message' => $error);
        }
        curl_close($curl_handler);

        error_log($curl_result);
        $encode = mb_detect_encoding($curl_result, array('ASCII', 'UTF-8','GB2312', 'GBK', 'BIG5'));
        if ($encode != 'UTF-8') {
            $curl_result = iconv($encode, 'UTF-8', $curl_result);
        }

        $result = json_decode($curl_result, true);
        if (is_null($result)) {
            $result = $curl_result;
        }

        return array( 'code'=>200, 'message'=>'ok', 'data'=>$result );

    }


    public function post( $url, $data, $http_opts = null ) {

        if (!isset($url) || empty($url)) {
            return array( 'code'=>400, 'message'=>'缺少请求链接' );
        }
        if (!isset($data)) {
            return array( 'code'=>400 ,'message'=>'缺少请求参数');
        }

        $curl_handler = curl_init();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HEADER	 => false,
            CURLOPT_USERAGENT => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36',
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $data
        );

        if (is_array($http_opts)) {
            foreach ($http_opts as $key => $value){
                $options[$key] = $value;
            }
        }

        curl_setopt_array($curl_handler, $options);
        $curl_result = curl_exec($curl_handler);
        $curl_http_status = curl_getinfo($curl_handler,CURLINFO_HTTP_CODE);
        if ($curl_result == false) {
            $error = curl_error($curl_handler);
            curl_close($curl_handler);
            return array( 'code' => $curl_http_status, 'message' => $error);
        }
        curl_close($curl_handler);

        $encode = mb_detect_encoding($curl_result, array('ASCII', 'UTF-8','GB2312', 'GBK', 'BIG5'));
        if ($encode != 'UTF-8') {
            $curl_result = iconv($encode, 'UTF-8', $curl_result);
        }

        $result = json_decode($curl_result, true);
        if (is_null($result)) {
            $result = $curl_result;
        }

        return array( 'code' => 200, 'message' => 'ok', 'data' => $result );

    }


    public function get( $url, $http_opts = null ) {

        if (!isset($url) || empty($url)) {
            return array( 'code' => 400, 'message' => '缺少请求链接' );
        }

        $curl_handler = curl_init();

        $options = array(
            CURLOPT_URL             => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_HEADER			=> false,
            CURLOPT_USERAGENT       => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36',
        );

        if (is_array($http_opts)) {
            foreach ($http_opts as $key => $value){
                $options[$key] = $value;
            }
        }

        curl_setopt_array($curl_handler, $options);
        $curl_result = curl_exec($curl_handler);
        $curl_http_status = curl_getinfo($curl_handler,CURLINFO_HTTP_CODE);
        if ($curl_result === false) {
            $error = curl_error($curl_handler);
            curl_close($curl_handler);
            return array( 'code' => $curl_http_status, 'message' => $error );

        }
        $encode = mb_detect_encoding($curl_result, array('ASCII', 'UTF-8','GB2312', 'GBK', 'BIG5'));
        if ($encode != 'UTF-8') {
            $curl_result = iconv($encode, 'UTF-8', $curl_result);
        }
        $result = json_decode($curl_result,true);
        if (is_null($result) || empty($result)) {
            $result = $curl_result;
        }

        curl_close($curl_handler);

        return array( 'code' => $curl_http_status, 'data' => $result );

    }


}

/**
 * 发送短信类(大汉三通)
 * Created by PhpStorm.
 * User: wanglulu
 * Date: 15/5/4
 * Time: 下午5:43
 */

class smsAPI extends CurlHttp{

    /* 帐号 */
    private $user = 'dh56281';


    /* 密码 */
    private $pwd = '$156hqZO';


    /* 入口地址 */
    static $gateway = 'http://wt.3tong.net/http/sms/Submit';

    static $balance = 'http://wt.3tong.net/http/sms/Balance';


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
        $send_array = array(
            'account' => $this->user,
            'password' => md5($this->pwd),
            'phones' => $mobile,
            'content' => mb_convert_encoding( $sms_content, $this->encoding ),
            'sign' => $this->Signature,
            'sendtime' => ''
        );

        $result_array = $this->post( self::$gateway, 'message=' . $this->xmlEncode($send_array) );

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

    public function getParentBalance(){

        $send_array = array(
            'account' => $this->user,
            'password' => md5($this->pwd),
        );

        $result_array = $this->post( self::$balance, 'message=' . $this->xmlEncode($send_array) );
        if ($result_array['code'] == 200) {

            return $data_array = $this->xmlDecode($result_array['data']);
        }else{
            return array('message'=> '获取余额失败', 'code'=>404, 'data'=>$result_array );
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