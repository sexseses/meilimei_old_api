<?php
/**
 * SMS短信接口类
 * Created by PhpStorm.
 * User: wanglulu
 * Date: 15/5/7
 * Time: 上午10:05
 */
interface smsInterFace {


    /**
     * 生成短信验证码
     * @param $num
     * @return mixed
     */
    public function createSmsCode( $num );


    /**
     * 获取短信内容
     * @param $template
     * @param $data_array
     * @return mixed
     */
    public function getSmsContent( $template, $data_array );


    /**
     * 发送短信
     * @param $mobile
     * @param $sms_content
     * @return mixed
     */
    public function sendSms( $mobile, $sms_content );



}