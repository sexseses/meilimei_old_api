<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * Event api Controller Class
 * @author        kingsley
 * @date 2014-11-27 
 */
         
//ini_set("display_errors","On");
//error_reporting(E_ALL);

require_once("v2/tehui_alipay/alipay.config.php");
require_once("v2/tehui_alipay/lib/alipay_submit.class.php");

 

class event extends CI_Controller{
    protected $json_arr,$phoneReg;
    
    public function __construct(){
        parent:: __construct();
        header("Content-type: text/html; charset=utf-8");
        $this->eventDB = $this->load->database('event', TRUE);
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $this->load->model('remote');
        $this->load->library('session');
        $this->load->library('tools');
        
        //error_reporting(E_ALL ^ E_NOTICE);


        $this->json_arr = array (
            'code' => - 1,
            'msg' => 'parameter error!'
        );
        $this->base_url = "http://www.meilimei.com/";
        //手机正则
        $this->phoneReg = '/^1[3|4|5|8][0-9]\d{4,8}$/';
    }
    
    
    
    public function weixin_notify_url(){

        
        $inputdata['get'] =  $_GET;
        $inputdata['post'] =  $_POST;
        
        $this->eventDB->insert('tmp',$inputdata);
    }
    
    //支付宝回调函数
    public function notify_url(){
        $this->load->library('sms');
        $out_trade_no = $this->input->get('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->get('trade_no');
        //交易状态
        $trade_status = $this->input->get('trade_status');
         
        $yzm =$this->makecode();
        $content = $yzm;
    
        $this->eventDB->where('order_no',$out_trade_no);
        $status =$this->eventDB->update('tehui_event_collection',array('capture'=>$content,'pay_state'=>'1','trade_no' => $trade_no));
    
        $sql = "select * from tehui_event_collection where order_no = '".$out_trade_no."'";
        $rs = $this->eventDB->query($sql)->row_array();
    
        $status = $this->sms->sendSMS(array ($rs['mobile']), "感谢您成功支付美丽神器活动定金，验证码：{$content}。请务必保留好验证码，我们的客服将在2个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");
    }
    
    //微信回调函数
    public function wx_notify_url(){
        include_once("/mnt/meilimei/eventwxpay/WxPayPubHelper.php");
        $this->load->library('sms');

        //使用通用通知接口
        $notify = new Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        // 	    $returnXml = $notify->returnXml();
        // 	    echo $returnXml;
        if($notify->checkSign() == TRUE){
            $out_trade_no = $notify->data['attach'];
            $yzm =$this->makecode();
            $content = $yzm;
            $this->eventDB->where('order_no',$out_trade_no);
            $status =$this->eventDB->update('tehui_event_collection',array('capture'=>$content,'pay_state'=>'2','trade_no' => $notify->data['out_trade_no']));
            $sql = "select * from tehui_event_collection where order_no = '".$out_trade_no."'";
            $rs = $this->eventDB->query($sql)->row_array();
            $this->sms->sendSMS(array($rs['mobile']), "【美丽神器】感谢您成功支付美丽神器活动定金，验证码：{$content}。请务必保留好验证码，我们的客服将在2个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");
            echo "success";
            if ($notify->data["return_code"] == "FAIL") {
        
            }elseif($notify->data["result_code"] == "FAIL"){
        
            }else{
//                 $out_trade_no = $notify->data['attach'];
//                 $yzm =$this->makecode();
//                 $content = $yzm;
//                 $this->eventDB->where('order_no',$out_trade_no);
//                 $status =$this->eventDB->update('tehui_event_collection',array('capture'=>$content,'pay_state'=>'2','trade_no' => $notify->data['out_trade_no']));
//                 $sql = "select * from tehui_event_collection where order_no = '".$out_trade_no."'";
//                 $rs = $this->eventDB->query($sql)->row_array();
//                 $this->sms->sendSMS(array($rs['mobile']), "【美丽神器】感谢您成功支付美丽神器活动定金，验证码：{$content}。请务必保留好验证码，我们的客服将在2个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");
//                 echo "success";
            }
        }
    }
    
    public function call_back_url(){
        $this->load->library('sms');
        $out_trade_no = $this->input->get('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->get('trade_no');
        //交易状态
        $trade_status = $this->input->get('trade_status');
         
        $yzm =$this->makecode();
        $content = $yzm;
    
        $this->eventDB->where('order_no',$out_trade_no);
        $status =$this->eventDB->update('tehui_event_collection',array('capture'=>$content,'pay_state'=>'1','trade_no' => $trade_no));
    
        $sql = "select * from tehui_event_collection where order_no = '".$out_trade_no."'";
        $rs = $this->eventDB->query($sql)->row_array();
    
        $status = $this->sms->sendSMS(array ($rs['mobile']), "感谢您成功支付美丽神器活动定金，验证码：{$content}。请务必保留好验证码，我们的客服将在2个工作日内与您联系确认到院时间，如有任何问题，请拨打免费客服热线：400-6677-245");
        echo "<div style='font-size:200%; width:100% ;text-align: center;'><p style=' text-align: center; background: #3ec1dd; border-radius: 5px; color: #fff; display: block; font-family: Microsoft YaHei; line-height: 50px; margin: 30% auto 0; padding: 10px; width: 95%;'><span style='display:block;'>感谢您的支付，系统将在5分钟内发送消费验证码到您手机上。<br>如遇支付或有任何疑问，请拨打免费客服热线：<br><b><big>400-6677-245</big></b></span><a href='http://m.meilimei.com/cs/vface' style='border-radius: 5px; display: inline-block; text-align: center; text-decoration: none; margin-top: 20px; background: none repeat scroll 0px 0px rgb(255, 201, 84); color: rgb(255, 255, 255); width: 80%; padding: 4px 0px;'>确定</a></p></div>";
        //echo "感谢您的支付，系统将在5分钟内发送给您消费验证码。如没有收到短信，请拨打免费客服电话：400-6677-245";
    }
    
    
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1 :
                return $this->remote->thumb($id, '36');
            case 0 :
                return $this->remote->thumb($id, '250');
            case 2 :
                return $this->remote->thumb($id, '120');
            default :
                return $this->remote->thumb($id, '120');
                break;
        }
    }
    
    /**
     * jsonp跨域请求
     * return
     * ?({"code":-1,"msg":"parameter error!"})		提交的参数错误
     * ?({"code":0,"msg":"no data!"})               参数正确，但取不到数据 
     * ?({"code":1,"msg":"", "data":"..."})			正常返回
     */
    public function index() {
        //$callback = $this->getRequest()->getQuery('callback');
        $action = $this->input->get('act');
        $callback = $this->input->get('callback');
        
        if (!method_exists( $this, $action )) {
            $this->json_arr['msg'] = "function '" . $action . "' not found!";
        } else {
            $this->$action();
        }
        $result = json_encode ($this->json_arr);
        echo ($callback ? $callback.'('.$result.')' : $result);
        return false;
    }
    
    public function getCount(){

        $team_id = intval($this->input->get('team_id'));

        if($team_id > 0) {
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '';
            $this->tehuiDB->where('team_id', $team_id);
            $nums = $this->tehuiDB->get('order')->num_rows();

            $this->json_arr['count'] = 300 + intval($nums);
        }else{
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '';
            $this->json_arr['count'] = 300;
        }
        return;
    }



	private function exChangeCode(){
		$code = $this->input->get('code');
		if(empty($code)){
            $this->json_arr['msg'] = '数据不能为空！';
            return;
        }
		
		$sql = "select coupon.id as cid,order.mobile as tel  from coupon left join order on coupon.order_id = order.id where coupon.id = {$code} limit 0,1";
		$result = $this->tehuiDB->query($sql)->row_array();
		
		if($result){
			$total_sql = "select * from 560event where 1=1 and conversion = 'N'";
			$total = $this->eventDB->query($total_sql)->num_rows();
			$rand_row = rand(0,$total - 1);
        
			$excode_sql = "select * from 560event where 1=1 and conversion = 'N' limit {$rand_row},1"; 
			$excode = $this->eventDB->query($excode_sql)->row_array();
			
			if($excode['sn'] == ""){
				$this->json_arr['msg'] = '兑换码获取失败!';
				return;
			}
			
			$updata = array(
				'code' => '$code',
                'conversion' => 'Y'
            );
			
            $this->db->update('560event', $updata);
            
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '兑换成功！';
            $this->json_arr['excode'] = $excode['sn'];
            $excode = $excode['sn'];
            $message = "兑换码: {$excode} 请妥善保存！如有疑问请致电 400-6677-245 客服电话！";
            $this->tools->sendMessage($result['tel'],$message);
    	        //$this->json_arr['data'] = $rs; 
            return;
			
		}else{
			$this->json_arr['msg'] = '兑换码异常！';
            return;
		}
	}
    
    /*
     * 甜品活动api
     * @param string name 活动人员的名字 
     * @param string mobile  活动人员的手机
     * */
    public function dessert_collection(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        
        $name = $this->input->get('name');
        $mobile = $this->input->get('mobile');
        $code = $this->input->get('code');
        
        

        if(empty($name) || empty($mobile) || empty($code)){
            $this->json_arr['msg'] = '数据不能为空！';
            return;
        }
 
        if($code <> $this->session->userdata($mobile)){
            $this->json_arr['msg'] = '验证码不正确！';
            return;
        }
        
        

        $sql = "select * from sweetmeats where 1=1 and mobile = {$mobile}";
        $result = $this->db->query($sql)->result_array();

        if(count($result) > 0){
            $this->json_arr['msg'] = '你已经参加过申请！';
            return;
        }
        
        $total_sql = "select * from dessert_event where 1=1 and state = 1";
        $total = $this->db->query($total_sql)->num_rows();
        
        
        $rand_row = rand(0,$total - 1);
        
        $excode_sql = "select * from dessert_event where 1=1 and state = 1 limit {$rand_row},1"; 
        $excode = $this->db->query($excode_sql)->row_array();

        if($excode['sn'] == ""){
            $this->json_arr['msg'] = '兑换码获取失败!';
            return;
        }
        
        $insertdata = array(
            'name' => $name,
            'mobile' => $mobile,
            'excode' => $excode['sn'],
            'time' => time()
        );
        
        $rs = $this->db->insert('sweetmeats',$insertdata);
        if($rs){
            $this->db->where('id', $excode['id']);
            $updata = array(
                'state' => '2'
            );
            $this->db->update('dessert_event', $updata);
            
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '申请成功！';
            $this->json_arr['excode'] = $excode['sn'];
            $excode = $excode['sn'];
            $message = "甜品兑换码: {$excode} 请妥善保存！如有疑问请致电 400-6677-245 客服电话！";
            $this->tools->sendMessage($mobile,$message);
    	        //$this->json_arr['data'] = $rs;
            return;
        }else{
            $this->json_arr['msg'] = '甜品活动申请不成功！';
            return;
        }
    }
    
    /*
     * 甜品活动api
     * @param string mobile  活动手机号
     * @param string message 活动短信内容
     * */
    public function verification(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $this->load->library('tools');
        $mobile = $this->input->get("mobile");
        if(empty($mobile)){
            $this->json_arr['msg'] = '手机号为空！';
            return;
        }
        
        $yzm =  $this->tools->makecode();
        $message = "验证码: $yzm 输入验证码，点击立即兑换，即可获得甜品兑换券。（5分钟内有效）";
        
        $this->session->set_userdata($mobile,$yzm);
        
        $status = $this->tools->sendMessage($mobile,$message);
        if($status===false || $status =='' || $status <0){
            $this->json_arr['msg'] = '短信未发送成功！';
            return;
        }
        $this->json_arr['code'] = 1;
        $this->json_arr['msg'] = '短信发送成功！';
        
    }
    
    
    /*
     * 甜品活动api
     * @param string mobile  活动手机号
     * @param string message 活动短信内容
     * */
    public function sendcodetomobile(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $this->load->library('tools');
        $mobile = $this->input->get('mobile');
        $excode = $this->input->get('excode');
        
        $mobile_sql = "select * from sweetmeats where mobile = '".$mobile."' and excode = '".$excode."'";
        $rs = $this->db->query($mobile_sql)->result_array();
        
        if(count($rs)<=0){
            echo "<script>alert('参数异常！');</script>";
        }

        $message = "甜品兑换码: {$excode} 请妥善保存！如有疑问请致电 400-6677-245 客服电话！";

        $status = $this->tools->sendMessage($mobile,$message);
        if($status===false || $status =='' || $status <0){
            echo "<script>alert('短信未发送成功！');</script>";
            return;
        }
        
        $insertdata = array(
            'states' => '2'
        );
        $this->db->where('id', $rs[0]['id']);
        $uprs = $this->db->update('sweetmeats',$insertdata);
        if($uprs){
            echo "<script>alert('短信发送成功！');</script>";
            redirect("http://www.meilimei.com/other/dessert.php");
            return;
        }
        
        
    }
    
    /*
     * 甜品活动api 验证甜品数量
     * */
    public function ck_dessert_num(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $exsql = "select * from dessert_event where 1=1 and state = 1 ";
        $excode = $this->db->query($exsql)->result_array();
        
        if(count($excode) <= 0){
            $this->json_arr['msg'] = '亲，兑换码已经被前面一群吃货抢走了!';
            return;
        }else{
            $this->json_arr['data'] = 1;
            return;
        }
    }

    /*
     * 玻尿酸活动api
     * @param string name 活动人员的名字
     * @param string mobile  活动人员的手机
     * */
    public function hyaluronic_collection(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
//         $name = "";
//         $mobile = "";
        $name = $this->input->get("name");
        $city = $this->input->get("city");
        $mobile = $this->input->get("mobile");
        $tag = $this->input->get("tag");
        $images = $this->input->get("images");
        if(empty($name) || empty($city) || empty($mobile)  || empty($tag))
        {
            $this->json_arr['msg'] = '数据不能为空！';
            return;
        }
        
        $insertdata = array(
            'name' => $name,
            'city' => $city,
            'mobile' => $mobile,
            'tag' => $tag,
            'images' => $images
        );
//         $sql = "select * from gbos where mobile = '$mobile'";
//         $rs = $this->db->query($sql)->result_array();
//         $result = false;
//         if(count($rs) <= 0){}

        $result = $this->db->insert('gbos',$insertdata);
        if($result){
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '申请成功！';
            return;
        }else{
            $this->json_arr['msg'] = '玻尿酸活动申请不成功！';
            return;
        }
    }
    
    
    /*
     * 猪八戒活动api
     * @param string name 活动人员的名字
     * @param string mobile  活动人员的手机
     * @param string pic_url  活动人员的图片地址
     *  @param string  user_score 活动人员的评分
     *  @param string user_bonus  活动人员的基友分
     * */
    public function zhubajie(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $uid = $this->input->get('uid');
        $name = $this->input->get('name');
        $mobile = $this->input->get('mobile');
        $pic_url = $this->input->get('picurl');
        $web_url = $this->input->get('weburl');
        $user_score = rand(70,100);

        
        $create_time = time();
        
        if(empty($uid)){
            $this->json_arr['msg'] = '参数非法！';
            return;
        }
        
        $ckusersql = "select * from event_zhubajie where 1=1 and uid = ? ";
        $ckuser_result = $this->db->query($ckusersql,array($uid))->result_array();
        if($ckuser_result){
            $this->json_arr['code'] = 0;
            $this->json_arr['msg'] = '已经参加过了！';
            return;
        }
        
        if(empty($name) || empty($mobile) || empty($pic_url) || empty($user_score)){
            $this->json_arr['msg'] = '数据不能为空！';
            return;
        }
        
        $insertdata = array(
            'uid' => $uid,
            'name' => $name,
            'mobile' => $mobile,
            'user_score' => $user_score,
            'user_bonus' => 0,
            'pic_url' => $pic_url,
            'web_url' => $web_url,
            'time' => $create_time
        );
        
        
        $result = $this->db->insert('event_zhubajie',$insertdata);
        if($result){
            $this->json_arr['code'] = 1;
            $this->json_arr['msg'] = '申请成功！';
            return;
        }else{
            $this->json_arr['msg'] = '活动申请不成功！';
            return;
        }
    }
    
    public function zhubajie_getInfo(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $user_id  = $this->input->get('uid');
        $user_info_sql = "select * from event_zhubajie where uid = ?";
        $user_info_result = $this->db->query($user_info_sql,array($user_id))->row_array();
        if($user_info_result){
            $user_info_result['mobile'] = substr_replace($user_info_result['mobile'],"****",3,4);
            $user_info_result['useravater'] = $this->profilepic($user_id);
            $user_info_result['total_num'] = $user_info_result['user_score']+$user_info_result['user_bonus'];
            $this->json_arr['code'] = 1;
            $this->json_arr['user_score'] = $user_info_result;
            return;
        }
    }
    
    /*
     * 猪八戒活动api 点赞
     * @param uid 用户 id
     * 
     * */
    public function zhubajie_praise(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $user_id  = $this->input->get('uid');
        
        if($user_id){
            $ckusersql = "select * from event_zhubajie where uid = ?";
            $user_rs = $this->db->query($ckusersql,array($user_id))->row_array();
            
            if($user_rs){
                $sql = "update event_zhubajie set user_bonus = user_bonus + 1 where uid = {$user_id}";
                $rs = $this->db->query($sql);
                
                if($rs){
                    $this->json_arr['code'] = 1;
                    $this->json_arr['msg'] = "点赞成功!";
                    $userscore_sql = "select user_bonus,(user_score + user_bonus) as total_num  from event_zhubajie where uid = ?";
                    $userscore_result = $this->db->query($userscore_sql,array($user_id))->row_array();
                    $this->json_arr['user_score'] = $userscore_result;
                    $this->json_arr['user_score']['user_id'] = $user_id;
                    return;
                }
            }
        }else{
            $this->json_arr['code'] = -1;
            $this->json_arr['msg'] = "参数不正确!";
        }
    }
    
    
    /*
     * 猪八戒活动api 排行
     * @param uid 用户 id
     *
     * */
    public function zhubajie_ranking(){
        echo "<script>alert('活动已经结束！');</script>";
        exit;
        $user_id  = $this->input->get('uid');
        //select id,score,(select count(1) from event_zhubajie where user_bonus >= (select user_bonus from event_zhubajie where id = {$user_id} order by user_bonus desc limit 1)) as rank from event_zhubajie where id = {$user_id};
        
        $ranking_sql = "select *, (user_score + user_bonus) as total_num  from event_zhubajie order by  total_num DESC limit 5";
        $ranking_result = $this->db->query($ranking_sql)->result_array();
        if($ranking_result){
            foreach ($ranking_result as &$v){
                $v['mobile'] = substr_replace($v['mobile'],"****",3,4);
                $v['useravater'] = $this->profilepic($v['uid']);
            }
            $this->json_arr['code'] = 1;
            $this->json_arr['ranking'] = $ranking_result;
        }else{
            $this->json_arr['msg'] = '排行榜为空！';
            return;
        }
        
        
        if($user_id){
            $userscore_sql = "select (user_score + user_bonus) as total_num  from event_zhubajie where uid = ?";
            $userscore_result = $this->db->query($userscore_sql,array($user_id))->result_array();
             
            $this->json_arr['user_score'] = $userscore_result;
        }
    }
    
    /*
     * 玻尿酸活动api
     * @param string upload_path 图片日期
     * @param string banner_pic  回调地址
     * */
    public function uploadfile(){
        $this->load->model('remote');
        $upload_path = '/'.date('Y').'/'.date('m').'/'.date('d').'/';
        //print_r($_FILES);die;
        $banner_pic = '';
        if ($_FILES['image_path']['tmp_name']) {
            $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
            if (!$this->remote->cp($_FILES['image_path']['tmp_name'], $file_name, $upload_path . $file_name)) {
                $this->json_arr['msg'] = '图片上传不成功！';
            }else {
                $banner_pic = $upload_path . $file_name;
                $this->json_arr['code'] = 1;
                $this->json_arr['msg'] = '图片上传成功！';
                $this->json_arr['data'] = $banner_pic;
            }
        } 
    }
    
    /**
     * 快的打车送券活动
     * 接受前台手机并发送验证码
     */
    public function postmagkuaidi(){
    	$iphone = $this -> input -> get('iphone');
    	$zhenze='/^1[3|4|5|8][0-9]\d{4,8}$/';
    	if(!preg_match($zhenze,$iphone)){ 
    		$this -> json_arr['msg'] = '手机号码有误！';
    		return ;
    	}
    	$ychar="0,1,2,3,4,5,6,7,8,9";
    	$list=explode(",",$ychar);
    	$authnum ='';
    	for($i=0;$i<4;$i++){
    		$randnum=rand(0,9); // 10;
    		$authnum.=$list[$randnum];
    	}    	
    	$this->session->set_userdata($iphone,$authnum);  	
    	$message = "验证码: {$authnum}！如有疑问请致电 400-6677-245 客服电话！";
    	$status = $this -> tools -> sendMessage($iphone,$message);
 
       if($status===false || $status =='' || $status <0){
            $this -> json_arr['msg'] = "短信未发送成功！";
            return false;
        }else{
            $this -> json_arr['code'] = 1;
            $this -> json_arr['msg'] = "短信发送成功！";
            return false;
       }
    }
    
    /**
     * 快的打车送券活动
     * 接受前台手机和验证码进行比对
     */
    public function postauthbunkuaidi(){ 
     	$iphone = $this -> input -> get('iphone');
     	$authnum = $this -> input -> get('authnum');
     	$zhenze='/^1[3|4|5|8][0-9]\d{4,8}$/';
     	if(!preg_match($zhenze,$iphone)){ 
    		$this -> json_arr['msg'] = '手机号码有误！';
     		return false;
     	} 
        if (empty($authnum) || ($authnum <> $this->session->userdata($iphone))){
     		$this -> json_arr['msg'] = '验证码有误！，请重新输入！';
     		return false;
     	} 

        //签名   	
    	$tmp = "sdhdsyh3wh23g23128dbactivityId9a0uRXM2mob{$iphone}sourcemeilishenqivendorkeylanglvzlsdhdsyh3wh23g23128db";
    	//把签名进行加密
    	$string =strtoupper( md5($tmp));
    	//传递给快的获取数据的数据
    	$inpalapa = array(
    			'source' => "meilishenqi",
    			'activityId' => "9a0uRXM2",
    			'mob' => $iphone,
    			'vendorkey' => "langlvzl",
    			'sign' => $string
    	);  
    	//根据快的接口进行发送	
    	$url = "http://api.kuaidadi.com:9898/taxi/cva/getVoucher.htm";
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL,$url) ;
    	curl_setopt($ch, CURLOPT_POST,1) ;  // 启用时会发送一个常规的POST请求，类型为：app   	
    	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $inpalapa );
    	//接受的参数$return
    	$return = curl_exec ( $ch ); 
    	curl_close ( $ch );  
    	//解析收取的json数组  	
    	$arr_decode = json_decode($return, true);
         //解析13位时间戳转换为10位时间戳      
        $lenstr = substr($arr_decode['expdate'], 0,10);

    	//放入数据库
    	$kdsql = array(
    			'iphone' => $iphone,
    			'code' =>$arr_decode['code'],
    			'msg' => $arr_decode['msg'],
    			'money' => $arr_decode['money'],
    			'expdate' => date('Y-m-d H:i:s',$lenstr ),
    			'vid' => $arr_decode['vid'],
    			'creat_time' =>  date('Y-m-d H:i:s',time())
    	);
    	$rs = $this->eventDB->insert('kuadi_activities',$kdsql);
    	if($arr_decode['code'] == 0){
    	$this -> json_arr['code'] = 1;
    	$this -> json_arr['msg'] =$arr_decode['msg']; 
    	} else{
    		$this -> json_arr['msg'] =$arr_decode['msg'];
    	}  	
        if(count($rs)>0){     
            $this -> json_arr['vid'] = $arr_decode['vid'];
            return $this -> json_arr['vid'];
        }
    }   
    
    public function smsqueue(){
    	       $sql = " SELECT k1.id, k1.priority, k1.iphone FROM  kuadi_activities k1 WHERE k1.send_status = 0 ORDER BY priority LIMIT 10";
         $rs = $this->eventDB->query($sql)->result_array();

         foreach ($rs as $value) {
            $sql_update_status = 'UPDATE kuadi_activities SET send_status = 1,send_count = send_count + 1 WHERE id = ?';
            $rs = $this->eventDB->query($sql,array($value['id']))->result_array();
            $update_status = $this->eventDB->query($sql);
            if($update_status){                      
                $statusCode = $this->tools->sendSMS($value['iphone'],$message);
                $sql_update = "UPDATE kuadi_activities SET submit_time = time() WHERE id = ?";
                $this->eventDB->query($sql,array($value['id']));
            } 
        }
    }

    
    
    
    //添加
    private function tehui_event_collection(){
        $time = time();
        $event_id = $this->input->get('event_id'); //
        $pay_way =$this->input->get('pay_way')?$this->input->get('pay_way'):'alipay'; 
        if(empty($event_id)){
            $this -> json_arr['msg'] = '项目不能为空！';
            return;
        }
        
        $name = $this->input->get('name');//姓名
        $mobile = $this->input->get('mobile');//电话
        $city = $this->input->get('city');//城市
        $mechanism = $this->input->get('mechanism'); //机构名称
        $mechanism_pro = $this->input->get('mechanism_pro'); //机构产品
        
        if($this->checkBuy($mobile,$event_id)){//已经购买
            $this -> json_arr['msg'] = '已经购买！';
            //             redirect('http://m.meilimei.com/zt/christmas-ruilan?error=1');
            return;
        }
        
        
        $sql = "select * from tehui_event where 1=1 and id = ?";
        $event_rs = $this->eventDB->query($sql,array($event_id))->row_array();
 
        
        if($time < $event_rs['begin_time']){
            echo "<script>alert('活动还没开始！');window.history.back(-1);</script>";
            exit;
        }
        
        
        if($time > $event_rs['end_time']){
            echo "<script>alert('活动已经结束！');window.history.back(-1);</script>";
            exit;
        }
        
   
        
        

        $order_no = '' ;//生成唯一订单号 
        $pay_state = 0;//支付状态
    
        if(empty($mechanism) || empty($mobile) || empty($name)){
            $this -> json_arr['msg'] = '数据不能为空！';
            //跳转
            //redirect('http://m.meilimei.com/zt/christmas-ruilan?error=0');
            return;
        }
    
       

        $sql = "select * from tehui_event_collection where mechanism = '".$mechanism."' and pay_state= '1' and DATE_FORMAT(FROM_UNIXTIME(time),'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')";

        $rs = $this->eventDB->query($sql)->result_array();
        if(count($rs) >= 10)
        {
            $this -> json_arr['code'] = '3';
            $this -> json_arr['msg'] = '机构已达上限，！';
            return;
            //跳转
            //redirect('http://m.meilimei.com/zt/christmas-ruilan?error=2');
            //echo json_encode($this->result);exit;
        }
    
        $ycode = array('5', '6', '8', '1', '2', '3', '4', '7', '9', '0');
        $order_no = date('YmdHis') . $ycode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    
        $data_arr = array(
            'mechanism' => $mechanism,
            'mechanism_pro' => $mechanism_pro,
            'city' => $city,
            'order_no' => $order_no,
            'mobile' => $mobile,
            'name' => $name,
            'time' =>$time,
            'pay_state' => $pay_state,
            'event_id' => $event_id
        );
 
        //这边刷新数据库出现多条同样的数据 需要判断
        $sql = "select * from tehui_event_collection where mobile = '".$mobile."' and pay_state= '0' and event_id = ?";

        $rs = $this->eventDB->query($sql,array($event_id))->result_array();
        if(count($rs) <= 0){
            $rs = $this->eventDB->insert('tehui_event_collection', $data_arr);
        }
        
        
        if($rs && $pay_way == "wxpay"){
            $param = $event_rs['subject']."|".($event_rs['price']*100)."|".$order_no;
            $this->json_arr['code'] = 1;
            $this -> json_arr['msg'] = $param;
            
            return;
            //redirect("http://www.meilimei.com/eventwxpay/js_api_call.php?body={$param}");
        }

        
        if($rs && $pay_way == "alipay"){
            //↓↓↓↓↓↓↓↓↓↓请在这里支付宝的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
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

            $computer = $this->input->get('computer');
            
            $event_id = $event_rs['id'];
            if($computer != 'pc'){
                redirect("http://www.meilimei.com/paywap/alipayapi.php?mobile={$mobile}&e_id={$event_id}");
                //redirect('http://262911.m.weimob.com/tg/goods/detail/pid/262911/bid/347707/wechatid/fromUsername/goods_id/18128/');
            }

            //支付类型
            $payment_type = '1';
            //必填，不能修改
            //服务器异步通知页面路径
            $notify_url = "{$this->base_url}event/notify_url";
            //需http://格式的完整路径，不能加?id=123这类自定义参数
    
            //页面跳转同步通知页面路径
            $return_url = "{$this->base_url}event/call_back_url";
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
    
            //卖家支付宝帐户
            $seller_email = 'rolaner@qq.com';
            //必填
            //商户订单号
            $out_trade_no = $order_no;
            //商户网站订单系统中唯一订单号，必填
    
            //订单名称
            $subject = $event_rs['subject'];
            //必填
    
            //付款金额
            $total_fee = $event_rs['price'];
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
                "buyer_email"=> '',
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip"   => $exter_invoke_ip,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
            );
    
            //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "正在跳转...");
            echo $html_text;
            //echo json_encode($this->result);exit;
    
        }else{

            $this -> json_arr['msg'] = '数据库插入失败！';
            return;;
        }
    }
    
        
        
        
        private function checkBuy($phone = '',$event_id = ''){
            if($phone ==  ''){
                $phone = $this->input->get('phone');
            }
            if($event_id == ''){
                $event_id = $this->input->get('e_id');
            }
            
            $sql = "select * from tehui_event_collection where mobile= ? and (pay_state = '1' or pay_state = 2) and event_id = ? ";
            $rs = $this->eventDB->query($sql,array($phone,$event_id))->result_array();
        
            if (count($rs) > 0) {
                $this -> json_arr['code'] = 1;
                $this -> json_arr['msg'] = '已经购买！';
                return true;
            }else{
                $this -> json_arr['msg'] = '还未购买！';
                return false;
            }
        }
        
        /**
         * 生成随即验证码
         */
        private function makecode(){
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
        
        private function is_phone_num($str){
            return preg_match('/(1(?:3[4-9]|5[012789]|8[78])\d{8}|1(?:3[0-2]|5[56]|8[56])\d{8}|18[0-9]\d{8}|1[35]3\d{8})|14[57]\d{8}/s', $str);
        }
        
        
        private function send() {
            $this->load->library('sms');
            $id = $this->input->post('id');
            if((int)$id ==0 ) {
                $this -> json_arr['msg'] = '参数错误！';
                return;
            }
            $sql = "select * from tehui_event_collection where id =$id";
            $rs = $this->eventDB->query($sql)->row_array();
            if(count($rs)==0 || !$this->is_phone_num($rs['mobile'])){
                $this -> json_arr['msg'] = '参数错误！';
                return;
            }else{
                $status = $this->sms->sendSMS(array ($rs['mobile']), "感谢您成功支付美丽神器一元脱毛活动定金，验证码：{$rs['capture']}。请务必保留好验证码，我们客服将在1个工作日内与您联系确认服务的时间。如有任何问题，请拨打免费客服热线：400-6677-245");
                if($status===false || $status =='' || $status <0){
                   $this -> json_arr['msg'] = '参数错误！';
                   return;
                } 
            }            
            $this -> json_arr['code'] = 1;
            $this -> json_arr['msg'] = '更新成功！';
            return;
        }
		

    
/**
     * 2015新年礼0元美肤活动
     */
    public function oneyuanbeauty(){
    	//活动结束时间
    	$overtime = time();
    	if($overtime >= 1425139200){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] ="活动已经结束！！";
    	}
    	$iphone = $this -> input -> get('iphone');
    	$zhenze='/^1[3|4|5|8][0-9]\d{4,8}$/';
    	if(!preg_match($zhenze,$iphone)){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = '手机号码有误！';
    		return false;
    	}
    	
    	//数据库查询手机是否已经注册过
    	$sql = "SELECT * FROM one_yuan_act WHERE 1=1 and tel = {$iphone}";
    	$prs = $this->eventDB->query($sql)->row_array();
    	if($prs){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] ="手机号码重复！！";
    		return false;
    	}
    	//产生随机数
   		 $ychar="0,1,2,3,4,5,6,7,8,9";
            $list=explode(",",$ychar);
            $authnum ='';
            for($i=0;$i<6;$i++){
                $randnum=rand(0,9); // 10;
                $authnum.=$list[$randnum];
            }
            
            $randrandnum = $authnum+time();
		 //先存入数据库，在发送至手机
    	$yiyuan=array(
    		'tel' => $iphone,
    		'code' => $randrandnum,	
    		'reg_time' => time()	
    	);
    	$rs = $this->eventDB->insert('one_yuan_act',$yiyuan);
    	if($rs){
    		$message = "零元美肤活动验证码:   {$randrandnum}   ！如有疑问请致电 400-6677-245 客服电话！";
    		$status = $this -> tools -> sendMessage($iphone,$message);
    		$this -> json_arr['code'] = 1;
    		$this -> json_arr['msg'] ="{$randrandnum}";  		
    		return true;
    	}
    	$this -> json_arr['code'] = -1;
    	$this -> json_arr['msg'] ="参数错误";   	
    }
    
    /**
     * 用兑换码兑换0元美肤活动
     * @return boolean
     */
    public function oneyuanexchange(){
    	//活动结束时间
    	$overtime = time();
    	if($overtime >= 1425139200){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] ="活动已经结束！！";
    	}
    	$code =	$this -> input -> get('cdkey'); 		//兑换码
    	$hospital = $this -> input -> get('hospital');		//医院
    	$project = $this -> input -> get('project');		//所选项目
    	//判断用户姓名、兑换码、医院和所选项目是否齐全
    	if(empty($hospital) || empty($project) || empty($code)){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = "兑换码、医院或项目没有选择！请重新填写！";
    		return false;
    	}  	
    	
    	
    	//判断兑换码是否正确
    	$cdksql = "SELECT * FROM one_yuan_act WHERE 1=1 AND code = ?";
    	$crs = $this->eventDB->query($cdksql,array($code))->row_array();
    	if(!$crs){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = '验证码错误！';
    		return false;
    	}
    	
    	//判断是否领取过
    	$namesql = "SELECT * FROM one_yuan_act WHERE 1=1 AND code = ? AND receive = '2'";
    	$nrs = $this->eventDB->query($namesql,array($code))->row_array();
    	if($nrs){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = '已经领取过！';
    		return false;
    	}
    	
    	//先将今天时间插入
    	$daytime = date('Y-m-d',time());
  	
    	//查询是否超过60人
    	$daysql="SELECT count(*) as count FROM one_yuan_act WHERE 1=1 AND receive = '2'";
    	$drs = $this-> eventDB -> query($daysql) -> row_array();
    	if($drs['count'] >= 60){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = "所有名额已满！谢谢参与！";
    		return false;
    	}  
    	  	  	
    	//查询医院是否超过10条记录   	
    	$hossql = "SELECT count(*) as count FROM  one_yuan_act WHERE 1=1 AND hospital = ? ";
    	$hrs = $this->eventDB->query($hossql,array($hospital)) -> row_array();
    	//echo  $hrs['count'];die;
    	if($hrs['count'] ==10){
    		$this -> json_arr['code'] = -1;
    		$this -> json_arr['msg'] = '此医院名额已满！谢谢参与！';
    		return false;
    	}
    		
    	$yiyuan = array(
    		'hospital' => $hospital,
    		'project' => $project,
    		'sel_pro_time' => $daytime,
    		'receive' => '2'	
    	);
    	$this -> eventDB ->where('code',$code);
    	$yiyuanre = $this -> eventDB -> update('one_yuan_act',$yiyuan);
    	if($yiyuanre){
    		$this -> json_arr['code'] = 1;
    		$this -> json_arr['msg'] = '兑换成功！';
    		return true;
    	}
    	$this -> json_arr['code'] = -1;
    	$this -> json_arr['msg'] = '参数错误！';
    }
    
    public function kodingMember(){
       $shouji =	$this -> input -> get('shouji'); 		 
    	   $mima = $this -> input -> get('mima');
        
    	   $data = array(
    	       'phone' => $shouji,
    	       'pawd' => $mima
    	   );

    	   $rs = $this -> eventDB -> insert('keding',$data);
    	   
    	   if($rs){
    	       $this -> json_arr['code'] = 1;
    	       $this -> json_arr['msg'] = '成功！';
           return;
    	   }
    	   $this -> json_arr['code'] = -1;
    	   $this -> json_arr['msg'] = '参数错误！';
    }
    
    /**
     * 注册并领取积分活动
     * @return boolean
     */
    public function integraljifen(){
    	$name = $this -> input -> get('name');
    	$iphone = $this -> input -> get('iphone');
    	//判断手机正在
    	$zhenze='/^1[3|4|5|8][0-9]\d{4,8}$/';
    	if(!preg_match($zhenze,$iphone)){
    		$this -> json_arr['code'] = 2;
    		$this -> json_arr['msg'] = '手机号码有误！';
    		return false;
    	}
    	//判断手机与姓名是否填写
    	if(empty($name)||empty($iphone)){
    		$this -> json_arr['code'] = 2;
    		$this -> json_arr['msg'] = '姓名活手机号码为填写！';
    		return false;
    	}
    	
    	//将人员加入数据库
    	$sql = "SELECT * FROM Integral_activities WHERE 1=1 AND tel = ? ";
    	$rs = $this->eventDB->query($sql,array($iphone)) -> row_array();
    	if($rs){
    		$this -> json_arr['code'] = 2;
    		$this -> json_arr['msg'] = '手机号码已经使用或已参加过此活动！';
    		return false;
    	}
    	 $arr = array(
    		'name' => $name,
    			'tel' => $iphone,
    			'receive_time' => date("Y-m-d" ,time())
    	);
    	$this -> eventDB -> insert('Integral_activities',$arr); 
    	
    	//curl进行操作
    	//进行注册操作
    	$password = '111111';
    	$curlPost = array(
    		'username' => $iphone,
    		'phone' => $iphone,
    		'password' => $password,
    		'confirmpassword' => $password,
    		'utype' => 1,
    	);
    	$ch = curl_init(); //初始化一个CURL对象 
    	$url = "http://www.meilimei.com/v2/user/reg41?test=1"; //设置你所需要抓取的URL 
    	curl_setopt($ch, CURLOPT_URL,$url) ;
    	curl_setopt($ch, CURLOPT_POST,1) ;  // 启用时会发送一个常规的POST请求，类型为：app   	
    	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $curlPost );
    	$return = curl_exec($ch); //运行curl,请求网页。 
    	curl_close($ch);
    	$data = json_decode($return, true);
    	//判断是否是已注册美丽神器的用户，已注册为2，未注册为1
    	$level = '';
    	switch ($data['ustate']){
    		case '000':
    			$level = '1';  			
    			break;
    		case '008':
    			$level = '2';				
    			break;
    		case '':
    			break;
    	}

    	//$level不是空是将积分存入用户账号内
    	if(!empty($level)){
    		$usersql = "SELECT * FROM users WHERE 1=1 AND username = ? OR phone = ?";
    		$uss = $this->db->query($usersql,array($iphone,$iphone))->row_array();
	    		if($uss){
	    			$jifeng = intval($uss['jifen'])+1000;
	    			$userarr = array(
	    					'jifen' => $jifeng
	    			);
	    			$this -> db -> where('id',$uss['id']);
	    			$users1 = $this -> db -> update('users',$userarr);
			    			if($users1){
			    				$arrs = array(
			    						'level' => '1'
			    				);
			    				$this -> eventDB -> where('tel',$iphone);
			    				$this -> eventDB -> update('Integral_activities',$arrs);
			    			}
	    		}
    	}

	    if($level == '1'){
		    	$this -> json_arr['code'] = 1;
		    	$this -> json_arr['msg'] = "成功注册为美丽神器App用户并了领取1000积分！用户名：{$iphone}；密码：111111";
	    	}
    	
    	if($level == '2'){
    		$this -> json_arr['code'] = 1;
    		$this -> json_arr['msg'] = '你已经注册过美丽神器App，现为你账号充值1000积分！';
    	}
    	
    	if($level == ''){
    		$this -> json_arr['code'] = 0;
    		$this -> json_arr['msg'] = '领取失败，填写参数错误！';
    	}
    }

    public function generalActivity(){
        $name = $this -> input -> get('name');
        $mobile = $this -> input -> get('mobile');
        $city = $this -> input -> get('city');
        $source = $this -> input -> get('from');
        
        //活动与2015年5月31日结束
        $overtime = time();
    	if($overtime >= 1433001600){
    		$this -> json_arr['code'] = 0;
    		$this -> json_arr['msg'] ="活动已经结束！！";
    		return;
    	}
    	
    	$mobile_sql = "SELECT * FROM general_activity WHERE 1=1 AND mobile = ?";
    	$mobile_rs = $this->eventDB->query($mobile_sql,array($mobile))->row_array();
    	if($mobile_rs){
    	    $this -> json_arr['msg'] = '已经注册！';
    	    $this -> json_arr['code'] = '2';
    	    return;
    	}
    	
    	if(empty($mobile)){
    	    $this -> json_arr['msg'] = '参数错误！';
    	    $this -> json_arr['code'] = '2';
    	}else{
                $daytime = date('Y-m-d',time());
                $user = array(
                    'name' => $name,
                    'mobile' => $mobile,
                    'city' => $city,
                    'reg_time' => $daytime,
                    'source' => $source
                );
                $reg = $this -> eventDB -> insert('general_activity',$user);
                if($reg){
                    $this -> json_arr['msg'] = '注册成功！';
                    $this -> json_arr['code'] = '1';
                    return;
                }
            }
            
        }
        
        //领取99原优惠券活动
        private function coupons_sn_create(){
            //$mobile = $this -> input -> get('mobile');
            $mobile = "13800000099";
            $jigou = $this -> input -> get('jigou');
            if(empty($mobile)){ 
                $this -> json_arr['msg'] = '参数错误！';
                $this -> json_arr['code'] = '0';
            }else{
                $mb_sql = "SELECT * FROM coupons_sn WHERE 1=1 AND mobile=$mobile";
                $mb_rs = $this -> eventDB -> query($mb_sql,array($mobile))->row_array();
                if($mb_rs){
                    $this -> json_arr['msg'] = '此手机已参加过活动！';
                    $this -> json_arr['code'] = '2';
                }else{
                    if(!empty($jigou) && $jigou == "mlm"){
                       $batch = "99RMBquan";
                    }
                    if(empty($jigou) && $jigou != "mlm"){
                        $batch = "botox";
                    }
                    
                $ssn = $this->GenSecret(8);
                $uid = '6082';
                $begin_time = time();// + 86400 * 10000;
                $end_time = time() + 86400 * 10000;
                $card = array(
                    'uid' => $uid,
                    'sn' => $ssn,
                    'batch'=> $batch,
                    'credit' => 99,
                    'quota' => 500,
                    'consume' => 'N',
                    'begin_time' => $begin_time,
                    'end_time' => $end_time
                );
                $ned = $this->db->insert('coupon_card', $card);
                $timetime=date('Y-m-d H:i:s',time());
                if($ned){
                    $cdsn = array(
                        'sn' => $ssn,
                        'batch'=> $batch,
                        'states' => 'N',
                        'mobile' => $mobile,
                        'dtime' => $timetime,
                        'mechanism' => $batch
                    );
                    $ssnd= $this->eventDB->insert('coupons_sn', $cdsn);
                    if($ssnd){
                        $this -> json_arr['msg'] = "优惠券号码为（{$ssn}）！";
                        $this -> json_arr['code'] = '1';
                    }else{
                        $this -> json_arr['msg'] = "异常错误！";
                        $this -> json_arr['code'] = '0';
                    }
                    
                    }
                }
            }
        }
        
        //生成99原优惠券活动
        private function GenSecret($len=6, $type=2){
            $secret = '';
            for ($i = 0; $i < $len;  $i++) {
                if ( 2==$type ){
                    if (0==$i) {
                        $secret .= chr(rand(49, 57));
                    } else {
                        $secret .= chr(rand(48, 57));
                    }
                }else if ( 1==$type ){
                    $secret .= chr(rand(65, 90));
                }else{
                    if ( 0==$i ){
                        $secret .= chr(rand(65, 90));
                    } else {
                        $secret .= (0==rand(0,1))?chr(rand(65, 90)):chr(rand(48,57));
                    }
                }
            }
            return $secret;
        }
    
        public function get_coupon_card(){
            
        }
    
}

    
?>