<?php
/**
 * Created by PhpStorm.
 * User: zhangyi
 * Date: 14-10-30
 * Time: 下午4:27
 */
require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');


class igttui extends IGeTui {

    const APPKEY = 'vJAKH0sxUg80tLvAj6cMC6';
    const MASTERSECRET = 'G7fYJ6YNWv9niNfafrnUs6';
    const APPID = 'lnlsdHqk6J7WgpouvVQAw8';
    const HOST = 'http://sdk.open.api.igexin.com/apiex.htm';

    public function __construct(){
        parent::__construct(self::HOST, self::APPKEY, self::MASTERSECRET);
    }

    /**
     * @return Array
     */
    public function sendMessage($cid = '',$content ='',$message = '您收到一条短消息', $title='美丽神器', $type = 0){

        if($type == 0) {
            $template = $this->IGtTransmissionTemplateDemo($content);
        }else{
            $template = $this->IGtNotificationTemplateDemo(0, $title, $content);
        }
        //$template = IGtLinkTemplateDemo();
        //$template = IGtNotificationTemplateDemo();
        //$template = IGtTransmissionTemplateDemo();

        //个推信息体
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(500);//离线时间
        $message->set_data($template);//设置推送消息类型


        //接收方
        $target = new IGtTarget();
        $target->set_appId(self::APPID);

        $target->set_clientId($cid);

        return $this->pushMessageToSingle($message, $target);
    }

    function IGtNotificationTemplateDemo($tsid = 0 ,$title = '', $content = ''){
        $template =  new IGtNotificationTemplate();
        $template->set_appId(self::APPID);//应用appid
        $template->set_appkey(self::APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($tsid);//透传内容
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo("http://www.meilimei.com/images/logo1031-114.png");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        // iOS推送需要设置的pushInfo字段
        $arr = explode(':',$content);
        if(strlen($content) > 100 || strlen($arr[3]) > 100) {
            $substr_content = substr($content,0,100);
            $substr_arr = substr($arr[3],0,100);
            $template->set_pushInfo("a", 0, $content, "com.gexin.ios.silence", $substr_content, $substr_arr, "", "");
        }else{
            $template->set_pushInfo("a", 0, $content, "com.gexin.ios.silence", $content, $arr[3], "", "");
        }

        //$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("test",1,"message","","","","","");
        return $template;
    }

    function IGtTransmissionTemplateDemo($content=''){
        $template =  new IGtTransmissionTemplate();
        $template->set_appId(self::APPID);//应用appid
        $template->set_appkey(self::APPKEY);//应用appkey
        $template->set_transmissionType(2);//透传消息类型
        $template->set_transmissionContent($content);//透传内容

        $arr = explode(':',$content);
        if(strlen($content) > 100 || strlen($arr[3]) > 100) {
            $substr_content = substr($content,0,100);
            $substr_arr = substr($arr[3],0,100);
            $template->set_pushInfo("a", 0, $content, "com.gexin.ios.silence", $substr_content, $substr_arr, "", "");
        }else{
            $template->set_pushInfo("a", 0, $content, "com.gexin.ios.silence", $content, $arr[3], "", "");
        }
        //iOS推送需要设置的pushInfo字段
        //$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("", 0, "", "", "", "", "", "");
        return $template;
    }

}