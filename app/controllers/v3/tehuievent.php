<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');

require_once(__DIR__."/tehui_alipay/alipay.config.php");
require_once(__DIR__."/tehui_alipay/lib/alipay_submit.class.php");

class Tehuievent extends CI_Controller {
    private $result=array(); 
    public function __construct() {
        parent :: __construct();
        //报告所有错误
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        header("Content-Type:text/html; charset=utf-8");
        
        //$this->load->library('sms');
        $this->load->library('tehui');
        
        $this->result['code'] = '0';
        //$this->result['data'] = '';
        $this->result['msg'] = '成功！';
        $this->base_url = "http://www.meilimei.com/";
    }

    public function index(){
    }
    
    /**
     * 生成随即验证码
     */
    public function makecode(){
        //生成8位随机数
        $ychar="0,1,2,3,4,5,6,7,8,9";
        $list=explode(",",$ychar);
        $authnum ='';
        for($i=0;$i<6;$i++){
            $randnum=rand(0,9); // 10;
            $authnum.=$list[$randnum];
        }
        return $authnum;
    }
    
    public function return_url(){
        
    }
    
    public function notify_url(){
        $this->load->library('sms');
        $out_trade_no = $this->input->get('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->get('trade_no');
        //交易状态
        $trade_status = $this->input->get('trade_status');
       
        $yzm =$this->makecode();
        $content = $yzm;
        
        $this->db->where('order_no',$out_trade_no);
        $status =$this->db->update('tehui_event_creotoxin',array('capture'=>$content,'pay_state'=>'1','trade_no' => $trade_no));

        $sql = "select * from tehui_event_creotoxin where order_no = '".$out_trade_no."'";
	    $rs = $this->db->query($sql)->result_array();
        
        $status = $this->sms->sendSMS(array ($rs['0']['mobile']), "美丽神器】感谢您成功支付瑞蓝2号玻尿酸特惠注射定金，验证码：{$content}。请务必保留好验证码，我们的客服将在1个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");    }

    public function call_back_url(){
        $this->load->library('sms');
        $out_trade_no = $this->input->get('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->get('trade_no');
        //交易状态
        $trade_status = $this->input->get('trade_status');
       
        $yzm =$this->makecode();
        $content = $yzm;
        
        $this->db->where('order_no',$out_trade_no);
        $status =$this->db->update('tehui_event_creotoxin',array('capture'=>$content,'pay_state'=>'1','trade_no' => $trade_no));

        $sql = "select * from tehui_event_creotoxin where order_no = '".$out_trade_no."'";
        $rs = $this->db->query($sql)->result_array();
        
        $status = $this->sms->sendSMS(array ($rs['0']['mobile']), "感谢您成功支付瑞蓝2号玻尿酸特惠注射定金，验证码：{$content}。请务必保留好验证码，我们的客服将在1个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");
        echo "<div style='font-size:200%; width:100% text-align: center;'><p style=' text-align: center; background: #3ec1dd; border-radius: 5px; color: #fff; display: block; font-family: Microsoft YaHei; line-height: 50px; margin: 30% auto 0; padding: 15px; width: 90%;'><span style='display:block;'>感谢您的支付，系统将在5分钟内发送消费验证码到您手机上。<br>如没收到短信，请拨打免费客服热线：<br><b><big>400-6677-245</big></b></span><a href='http://a.app.qq.com/o/simple.jsp?pkgname=com.work.beauty' style='border-radius: 5px; display: inline-block; text-align: center; text-decoration: none; margin-top: 20px; background: none repeat scroll 0px 0px rgb(255, 201, 84); color: rgb(255, 255, 255); width: 80%; padding: 6px 10px;'>确定</a></p></div>";
        //echo "感谢您的支付，系统将在5分钟内发送给您消费验证码。如没有收到短信，请拨打免费客服电话：400-6677-245";
    }
    
    //添加
    public function addevent(){
       $endtime=time();
       $eventnum = $this->input->get('eventnum');
       //1420214400
       if($eventnum != 2 || $endtime >= 1420300800 ){
            echo "<script>alert('活动已经结束！');</script>";       	
       	    exit;
       }
        

        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']       = '2088111063773467';
        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']           = 'n5g1utqti8brcshbdbzcvwadgf8hfcj7';
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';


        $mechanism = $this->input->get('mechanism');//机构名称
        $alipay_account = $this->input->get('alipay_account');//支付宝账号
        $order_no = '' ;//生成唯一订单号
        $mobile = $this->input->get('mobile');//电话
        $name = $this->input->get('name');//姓名
        $time = time();
        $pay_state = 0;//支付状态

        if(empty($mechanism) || empty($mobile) || empty($name)){
//             $this->result['code'] = '0';
//             $this->result['msg'] = '数据不能为空！';
            //跳转
            redirect('http://m.meilimei.com/zt/christmas-ruilan?error=0');
            exit;
        }

        if($this->checkBuy($mobile)){//已经购买
            redirect('http://m.meilimei.com/zt/christmas-ruilan?error=1');
            exit;
        }
        
        //一天之内机构不能大于5次 
        $thistime = time();
        //$thistime = FROM_UNIXTIME($thistime, '%Y%m%d')
        $thisdate = date('Y-m-d',$thistime );
        $sql = "select * from tehui_event_creotoxin where mechanism = '".$mechanism."' and pay_state= '1' and DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')";
        //echo $sql;die;
        $rs = $this->db->query($sql)->result_array();
        if(count($rs) >= 5)
        {
            $this->result['code'] = '3';
            $this->result['msg'] = '机构已达上限，！';
            //跳转
            redirect('http://m.meilimei.com/zt/christmas-ruilan?error=2');
            echo json_encode($this->result);exit;
        }
 
        $ycode = array('5', '6', '8', '1', '2', '3', '4', '7', '9', '0');
        $order_no = date('YmdHis') . $ycode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        
        $data_arr = array(
            'mechanism' => $mechanism,
            'order_no' => $order_no,
            'mobile' => $mobile,
            'name' => $name,
            'time' =>$time,
            'pay_state' => $pay_state,
            	'event_name' => "第二季"
        );
        $event_name = "第二季";
        //这边刷新数据库出现多条同样的数据 需要判断
        $sql = "select * from tehui_event_creotoxin where mobile = '".$mobile."' and pay_state= '0' and event_name = '".$event_name."'";
        //echo $sql;die;
        $rs = $this->db->query($sql)->result_array();
        if(count($rs) <= 0)
        {
            $rs = $this->db->insert('tehui_event_creotoxin', $data_arr);
        }
        

        if($rs){
            $computer = $this->input->get('computer'); 
            if($computer != 'pc'){
                redirect("http://www.meilimei.com/paywap/alipayapi.php?mobile={$mobile}");         
                //redirect('http://262911.m.weimob.com/tg/goods/detail/pid/262911/bid/347707/wechatid/fromUsername/goods_id/18128/');
            }
            
            //支付类型
            $payment_type = '1';
            //必填，不能修改
            //服务器异步通知页面路径
            $notify_url = "{$this->base_url}v2/tehuievent/notify_url";
            //需http://格式的完整路径，不能加?id=123这类自定义参数        

            //页面跳转同步通知页面路径
            $return_url = "{$this->base_url}v2/tehuievent/call_back_url";
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/        

            //卖家支付宝帐户
            $seller_email = 'rolaner@qq.com';
            //必填        
            //商户订单号
            $out_trade_no = $order_no;
            //商户网站订单系统中唯一订单号，必填

            //订单名称
            $subject = "美丽神器，瑞蓝2号玻尿酸注射预约金!";
            //必填        

            //付款金额
            $total_fee = '100';
            //必填

            //防钓鱼时间戳
            $anti_phishing_key = "";
            //若要使用请调用类文件submit中的query_timestamp函数        

            //客户端的IP地址
            $exter_invoke_ip = "";

            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "payment_type"  => $payment_type,
                "notify_url"    => $notify_url,
                "return_url"    => $return_url,
                "seller_email"  => $seller_email,
                "out_trade_no"  => $out_trade_no,
                "subject"   => $subject,
                "total_fee" => $total_fee,
                "buyer_email"=>$alipay_account,
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip"   => $exter_invoke_ip,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
            );

            //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
            echo $html_text;
            //echo json_encode($this->result);exit;

		}else{
            $this->result['code'] = '1';
            $this->result['msg'] = '数据库插入失败！';
            echo json_encode($this->result);exit;
        }
    }
    
    //查询   根据名字查询
    public function queryEvent(){
        $name = $_GET['name'];
        if(empty($_GET['name'])){
            echo json_encode( array('code'=>'0','data'=>'参数不全'));exit;
        }else {
            $sql = "select * from tehui_event_creotoxin where name='$name'";
            $rs = $this->db->query($sql)->result_array();
            echo json_encode( array('code'=>'0','data'=>$rs));exit;
        }         
    }

    //注册
    private function isReg($phone){
        $sql = "select * from users where phone='$phone'";
        $rs = $this->db->query($sql)->result_array();
        if (count($rs) > 0) {
            return true;
        }else{
            return false;
        }
    }
    private function checkBuy($phone){
        $sql = "select * from tehui_event_creotoxin where mobile='$phone' and pay_state = '1' and event_name = '第二季'";
        $rs = $this->db->query($sql)->result_array();

        if (count($rs) > 0) {
            return true;
        }else{
            return false;
        }
    }
    private function callBack($data){
        if($_GET['callback']){
            echo $_GET['callback'].'('.json_encode($data).')';
        }else{
            echo json_encode($data);
        }
        exit;
    }
    //normal register
    public function reg() {
        $this->load->library('sms');
		$this->load->helper('form');
		$password = $_GET['password'];
		$confirmpassword = $_GET['confirmpassword'];
		$phnum = $_GET['phone'];
// 		$phnum = '13600001111';
// 		$password = '123456';
// 		$confirmpassword = '123456';
	    if(empty($phnum)||empty($password)||empty($confirmpassword)){
	        $result['notice'] = '参数不全';
	        $result['ustate'] = '012'; 
	        //echo json_encode($result);exit;
	        echo $this->callBack($result);exiit;
	    }elseif($password!==$confirmpassword){
	        $result['notice'] = '密码不一致';
	        $result['ustate'] = '012';
	        //echo json_encode($result);exit;
	        echo $this->callBack($result);exiit;
	    }
		if ($_GET) {
			if ($phnum != '') {
			    $data = $this->isReg($phnum);
				if ($data) {
					$utype = 1;
					$this->wen_auth->_setRegFrom(2, 'Wap');
					$data = $this->wen_auth->register('', $password, '', $phnum, '', '', $utype);
					$this->wen_auth->login($phnum, $password, TRUE);
					//同步到特惠
					if (!empty ($data['user_id'])) {
						$tehuiData = array (
							'id' => null,
							'email' => '',
							'username' => $phnum,
							'password' => crypt($this->wen_auth->_encode($password
						)), 'realname' => '', 'alipay_id' => '', 'avatar' => '', 'newbie' => 'Y', 'mobile' => $phnum, 'qq' => '', 'money' => 0.00, 'score' => 0, 'zipcode' => null, 'address' => '', 'city_id' => 0, 'emailable' => '', 'enable' => 'Y', 'manager' => 'N', 'secret' => '', 'recode' => '', 'sns' => '', 'ip' => '', 'login_time' => time(), 'create_time' => time(), 'mobilecode' => '', 'secret' => md5(rand(1000000, 9999999) . time() . $phnum));
						$th_inertid = $this->tehui->reg_zuitu($tehuiData);
					}
					//for user self
					$notification = array ();
					$notification['user_id'] = $this->wen_auth->get_user_id();
					if ($notification['user_id']) {
						$this->common->insertData('user_notification', $notification);
						$this->common->insertData('wen_notify', $notification);
					}
					//send sms
					if ($phnum) {
						$this->sms->sendSMS(array (
							"{$phnum}"
						), "【美丽神器】感谢你注册美丽神器 APP,你的账户:{$phnum},密码:{$password},请妥善保管" . '退订回复0000 ');
					}
					//send tehui
					$i = 0;
					for($i = 0; $i<5; $i++){
						$code = $this->tehui->tehuiSend($this->wen_auth->get_user_id(),true);
						if($phnum and $code.$i){
							$this->sms->sendSMS(array (
								"{$phnum}"
							), '【美丽神器】 代金券号：'.$code.' 退订回复0000');
						}
					}
					$result['notice'] = '注册成功';
					$result['ustate'] = '000';
				} else {
					$result['notice'] = '手机号不正确或者已使用';
					$result['ustate'] = '008';
				}
			}
		} else {
			$result['notice'] = '账户已登入不能注册';
			$result['ustate'] = '001'; //登入失败
		}
		echo json_encode($result);

		echo $this->callBack($result);exit;

	}
}

?>