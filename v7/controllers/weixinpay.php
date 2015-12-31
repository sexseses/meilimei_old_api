<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * weixin api Controller Class
 * @author        kingsley
 * @date 2015-04-01
 */

//require_once(__DIR__."/weixin_pay/WxPayPubHelper.php");


class weixinpay extends CI_Controller{
    public function __construct(){
        parent:: __construct();
        header("Content-type: text/html; charset=utf-8");
        $this->eventDB = $this->load->database('event', TRUE);
        
        //error_reporting(E_ALL ^ E_NOTICE);
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        var_dump(__DIR__);
        echo "aaa";die;
    }
    
    
    
    
}