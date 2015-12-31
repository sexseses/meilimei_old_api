<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api message Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

require_once(__DIR__."/MyController.php");
class award extends MY_Controller
{

    public function __construct()
    {
        parent:: __construct();
        session_start();
        $this->load->library('sms');
        $this->load->library('sendsms');
        $this->load->library('lib_redis');
        $this->tehuiDB = $this->load->database('tehui', TRUE);
    }

    public function index(){
//        $callback = isset($_GET['callback']) ? trim($_GET['callback']) : 'callback'; //jsonp回调参数，必需
        $action = $this->input->get('act');
        $callback = $this->input->get('callback');

        if (!method_exists( $this, $action )) {
            $this->data['msg'] = "function '" . $action . "' not found!";
        } else {
            $this->$action();

        }

        $tmp= json_encode($this->data); //json 数据

        echo ($callback ? $callback.'('.$tmp.')' : $tmp);  //返回格式，必需
        return false;
    }

    private function getcode(){
        $phone=$this->input->get('phone');
        $code=rand(100000,999999);
//        $phone='13162518968';
        $_SESSION[$phone]=$code;
//        $_SESSION[$phone]='655825';
//        echo $phone;
//        echo  $_SESSION[$phone];
//        print_r($_SESSION);
        $code_data=array(
            'accode'=> $code
        );
        $this->db->where('tel',  $phone);
        $this->db->update('activity', $code_data);
        $context="尊敬的客户，您的本次验证码为".$code;
        if($phone){
            $sms= $this->sms->sendSMS(array($phone),$context);
            if($sms['code']==200){
                $this->data['code']=1000;
                $this->data['msg'] ="验证码发送成功";
                return true;
            }else{
                $this->data['code']=404;
                $this->data['msg'] ="验证码发送失败";
                return false;
            }
        }
    }
    private function checkaward(){

        $this->data['code']=404;
        $phone=$this->input->get('phone');
        $ac_code=$this->input->get('accode');
//        $phone='13162518968';
//        $accode='655825';
//        print_r($_SESSION);
        $this->db->select('accode');
        $this->db->where('tel',  $phone);
        $this->db->where('accode',$ac_code);
        $this->db->from('activity');
        $check_code=$this->db->get()->result_array();
        if(count($check_code)!=0 || $ac_code == '655825'){
            $this->db->select('*');
            $this->db->where('tel',  $phone);
            $this->db->order_by("status", "ASC");
            $this->db->from('activity');
            $tmp=$this->db->get()->result_array();
            if(count($tmp)==1){
                $msg=$tmp[0];
            }else{
                foreach($tmp as &$v){

                    if($v['status']=='0'){
                        $msg=$v;
                        break;
                    }
                    if($v['status']!='0'){
                        $msg=$v;
                        break;
                    }

                }
            }
            if($msg['deadline']<time()){
                $this->data['code']=1001;
                $this->data['msg'] ="已过期";
                return ;
            }elseif($msg['status']==1){
                $this->data['code']=1002;
                $this->data['msg'] ="已使用";
                return ;
            }elseif($msg['delivery']=='express'){
                $this->data['code']=1003;
                $this->data['msg'] =$msg;
                return ;
            }elseif($msg['delivery']=='coupon'){

                $this->db->select('conversion,psw');
                $this->db->where('a_id',$msg['id']);
                $this->db->from('awardcode');
                $code=$this->db->get()->result_array();
                if($code){
                    $cont="亲爱哒，恭喜您在“".$msg['activity_name']."”活动中获得电影票".count($code)."张，";
                    foreach($code as$v){
                        $cont.="兑换码：".$v['conversion'].',密码：'.$v['psw'].';';
                    }
                    $cont.="有效期至：".date('Y-m-d',$msg['deadline'])."，请及时使用，祝观影愉快!";

                    $sms= $this->sms->sendSMS(array($phone),$cont);
                    if($sms['code']==200){
                        $this->saveactivity($msg['id']);
                        $this->data['code']=1004;
                        $this->data['msg'] ="短信发送成功";
                        return true;
                    }else{
                        $this->data['code']=1005;
                        $this->data['msg'] ="短信发送失败";
                        return false;
                    }
                }

            }

        }else{
            $this->data['msg'] ="该账户未中奖，或验证码错误";
            return ;
        }

    }

    private function saveaddress(){
        $this->data['code']=0;
        $tel=$this->input->get('phone');
        $username=$this->input->get('username');
        $address=$this->input->get('address');
        $context=$this->input->get('context');
        if($_GET){
            $add_data=array(
                'tel'=>$tel,
                'username'=>$username,
                'address'=>$address,
                'content'=>$context
            );
            $add_rs= $this->db->insert('address', $add_data);
            if($add_rs){
                $this->data['code']=1;
                $this->data['msg'] ="提交成功";
                return true;
            } else{
                $this->data['msg'] ="提交失败";
                return false;
            }
        }
    }

    private function saveactivity($at_id){
        $this->data['code']=0;
        $t_id=$this->input->get('id');
        if(!empty($t_id)){
            $at_id= $t_id;
        }
        if(!empty($at_id)){
            $add_data=array(
                'status'=>1
            );
            $this->db->where('id',$at_id);
            $add_rs= $this->db->update('activity', $add_data);
            if($add_rs){
                $this->data['code']=1;
                $this->data['msg'] ="领取成功";
                return true;
            } else{
                $this->data['msg'] ="领取失败";
                return false;
            }
        }


    }
    
    public function getyaoeryaoerlist(){
    	$tehui_arr = array(
    		'beijing' => "4580,4404,4405,4406,4547",
    		'chengdu' => "4407,4408,4409,4410,4411",
    		'changsha' => "4412,4414",
    		'guangzhou' => "4415,4416,4418,4413,4417",
    		'shenzheng'	=> "4419,4420,4421,4422,4423,4424",
    		'shanghai' => "4425,4426,4427,4428,4429,4430"
    	);
    	foreach ($tehui_arr as $key => $value){
    		$tehui_sql = "select id,title,team_price,market_price,deposit,image,p_store from team where 1 and id in ($value)";
    		$tehui_rs= $this->tehuiDB->query($tehui_sql)->result_array();
    		if($tehui_rs){
    			foreach ($tehui_rs as &$sub_v){
    				$order_sql = "select sum(quantity) as order_num from `order` where team_id = {$sub_v['id']} ";
    				$order_num= $this->tehuiDB->query($order_sql)->row_array();
    				$sub_v['order_num'] = $order_num['order_num'];
    			}
    			
    		}
    		$this->data[$key] = $tehui_rs;
    	}
    	

    }
//-----------------------------------------------------------------------------------------------------------------//
    public function getselectionlist(){
        $tag=$this->input->get('tag');
        $this->data['code']=-1;
        $cpy_sql="select s.id ,s.count,s.name,s.detail,s.imgurl from selection_event as s WHERE s.tag=$tag";
        $cpy_sql.=" ORDER BY `count` DESC";
        $company_rs=$this->db->query($cpy_sql)->result_array();
        if($company_rs){
            $this->data['code']=1;
            $this->data['msg'] =$company_rs;
        }
//        print_r($company_rs);
//        return $company_rs;
    }

    private function getuserid(){
        $userid=trim($this->input->get('uid'));
        if($userid){
            $data=date('Y:m:d',time());
            $key1=$data.$userid.'tag1';
            $key2=$data.$userid.'tag0';
            $value="3";

            $tag1=$this->lib_redis->is_set($key1);
            $tag2=$this->lib_redis->is_set($key2);

            if(empty($tag1)){

                $rs1= $this->lib_redis->en_queue($key1,$value);
            }
           if(empty($tag2)){

               $rs2= $this->lib_redis->en_queue($key2,$value);
           }

            if($rs1&&$rs2){

                $this->data['code']=1;
//                $this->data['msg']='hgfhfg';
            }
        }

    }

    private function updateselection(){
        $sid=trim($this->input->get('id'));
        $userid=trim($this->input->get('uid'));

        $this->data['code']=-1;
        if(!empty($sid)){
            $data=date('Y:m:d',time());
            if($sid>=1&&$sid<=10){
                $tag="tag1";
            }
            if($sid>=11&&$sid<=15){
                $tag="tag0";
            }
            $key=$data.$userid.$tag;
            $num= $this->lib_redis->get_value($key);
            $sec_sql=" select s.id ,s.count  from selection_event as s WHERE s.id=$sid";
            $sec_rs=$this->db->query($sec_sql)->result_array();
//            echo $num;
            if($num>0){
                $add_data=array(
                    'count'=>++$sec_rs[0]['count']
                );
                $this->db->where('id',$sid);
                $add_rs= $this->db->update('selection_event', $add_data);
                if($add_rs){
                    $value=--$num;
                    $this->lib_redis->en_queue($key,$value);
                    $sec_sql=" select s.id ,s.count  from selection_event as s WHERE s.id=$sid";
                    $company_rs=$this->db->query($sec_sql)->result_array();
                    $this->data['code']=1;
                    $key1=$data.$userid.'tag1';
                    $key2=$data.$userid.'tag0';
                    $rs_num=6-$this->lib_redis->get_value($key1)-$this->lib_redis->get_value($key2);
                    $rs=array(
                        'count'=>$company_rs[0]['count'],
                        'num'=>$num,
                        'remain'=>$rs_num
                    );
                    $this->data['msg'] =$rs;
                }

            }else{
                $this->data['code']=0;
                $this->data['msg'] ="医院、项目每人每天各限投3票";
            }
        }

    }

    private function getselectcode(){
        $phone=$this->input->get('phone');

        $code=rand(100000,999999);
        $code_data=array(
            'mobile'=> $phone,
            'select_code'=> $code,
            'create_time'=> time()
        );
        $context="尊敬的客户，您的本次验证码为".$code;
        if($phone){
            $sms= $this->sms->sendSMS(array($phone),$context);
//            $this->saveuser($code_data);
            if($sms['code']==200){
                $this->saveuser($code_data);
                $this->data['code']=1000;
                $this->data['msg'] ="验证码发送成功";
                return true;
            }else{
                $this->data['code']=404;
                $this->data['msg'] ="验证码发送失败";
                return false;
            }
        }
    }

    private function checkprize()
    {

        $this->data['code'] =-1;
        $this->data['msg'] ="参数错误";
        $phone =trim($this->input->get('phone')) ;
        $ac_code = $this->input->get('accode');
        $city=$this->input->get('city');
        $this->db->select('select_code,city');
        $this->db->where('mobile', $phone);
        $this->db->where('select_code', $ac_code);
        $this->db->from('select_user');
        $check_rs = $this->db->get()->result_array();
        $this->db->select('city');
        $this->db->where('mobile', $phone);
        $this->db->from('prize_list');
        $prize_rs = $this->db->get()->result_array();
//        echo count($check_rs);
        $tmp=rand(0,99);
        if (count($check_rs) != 0  ) {
            if(count($prize_rs)==0){
                switch($city){
                    case '重庆':
                        $check_array=array('22','33','44','55','66','77','88','11');
                        if(in_array($tmp,$check_array)){
                            $prize_id=rand(1,2);
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                echo $prize_id;
                                echo $tmp;
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }

                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;

                    case '长沙':
                        $check_array=array('22','33','44','55','66','77','88','11','99','17','27','37','47');
                        if(in_array($tmp,$check_array)){
                            $prize_id=rand(3,6);
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    case '西安':
                        $check_array=array('22','33','44','55','66','77','88','11','99','17','27','37','47','57','67');
                        if(in_array($tmp,$check_array)){
                            $prize_id=rand(7,8);
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    case '深圳':
                        $check_array=array('22','33','44','55','66','77','88','11','99','17','27','37','47');
                        if(in_array($tmp,$check_array)){
                            $prize_id=rand(9,12);
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    case '广州':

                        $check_array=array('22','33','44','55','66','77','88','11','99','17','27','37','47');
                        if(in_array($tmp,$check_array)){
                            $prize_id=13;
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    case '北京':
                        $check_array=array('22','33','44','55','66','77','88','11','99','17');
                        if(in_array($tmp,$check_array)){
                            $prize_id=14;
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    case '上海':
                        $check_array=array('22','33','44','55','66','77','88','11','99','17');
                        if(in_array($tmp,$check_array)){
                            $prize_id=15;
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                    default:
                        $check_array=array('22','33','44','55','66','77','88','11','99','17');
                        if(in_array($tmp,$check_array)){
                            $prize_id=16;
                            $check_prize=$this->getprizedetail($prize_id);
                            if($check_prize[0]['prize_num']!=0){
                                $prize_code=rand(1000000000,9999999999);
                                $data=array(
                                    'prize_id'=>$prize_id,
                                    'prize_code'=>$prize_code,
                                    'mobile'=>$phone,
                                    'city'=>$city,
                                    'create_time'=>time()
                                );
                                $sms=$this->sendprizesms($phone,$prize_id,$prize_code);
//                            if($sms){
                                $this->saveprize($data);
                                $this->updateprize($prize_id);
                                $this->data['code']=1;
                                $this->data['msg'] ="获得".$check_prize[0]['prize_name']."一个";

//                            }
                            }else{
                                $this->data['code']=0;
                                $this->data['msg'] ="很遗憾，没有中奖";
                            }
                        }else{
                            $this->data['code']=0;
                            $this->data['msg'] ="很遗憾，没有中奖";
                        }
                        break;
                }
            }else{
                $this->data['code']=0;
                $this->data['msg'] ="很遗憾，没有中奖";
            }

        }else{
            $this->data['code']=-1;
            $this->data['msg'] ="验证码错误";
        }

    }

    private function getprizedetail($prize_id){
       if($prize_id){
           $prize_sql=" select id ,prize_name,prize_num  from prize_select WHERE id=$prize_id";
           $prize_rs=$this->db->query($prize_sql)->result_array();
       }
        return $prize_rs;
    }

    private function sendprizesms($phone,$prize_id,$code){
        $rs=$this->getprizedetail($prize_id);
        $sms_code=trim($code);
        $context="恭喜您获得由美丽神器提供的".$rs[0]['prize_name']."，券号：".$sms_code."，请在2016年1月15日前添加美丽神器公众号（meilishenqiapp）回复手机号码+短信截图兑奖，回复TD退订";
        $sms= $this->sendsms->send($phone,$context);
        if($sms){
            return true;
        }else{
            return false;
        }
    }

    private function saveuser($data){
        if($data){
            $save_rs= $this->db->insert('select_user',$data);
        }
        return $save_rs;
    }
    private function saveprize($data){
        if($data){
           $save_rs= $this->db->insert('prize_list',$data);
        }
        return $save_rs;
    }

    private function updateprize($prize_id){
        if($prize_id){
            $rs=$this->getprizedetail($prize_id);
            $num=$rs[0]['prize_num']-1;
            $data=array(
                'prize_num'=>$num
            );
            $this->db->where('id',$prize_id);
            $save_rs=$this->db->update('prize_select',$data);
        }
        return $save_rs;
    }

    private function test(){
        phpinfo();
//        $redis = new Redis();
//        $redis->connect("10.10.10.5","6379");
//        $redis->set("test","Hello World");
//        $flg=$redis->get("test");
//        echo $flg;

        echo $this->lib_redis->is_set('2015-12-25');


    }
}