<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */

require_once(__DIR__."/MyController.php");
class user extends MY_Controller {
	private $notlogin = true, $uid = '';
    private $eventDB = '';
	var $min_username = 4;
	var $max_username = 20;
	var $min_password = 4;
	var $max_password = 20;

	public function __construct() {
		parent :: __construct();
		$this->load->library('form_validation');

		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}

        $this->eventDB = $this->load->database('event', TRUE);
		$this->load->library('wen_auth_event');
		$this->load->library('sms');
		$this->load->library('tehui');
		$this->load->model('Users_model');
		$this->load->model('auth');
		$this->load->model('Email_model');
		$this->load->helper('file');
		$this->load->model('remote');
		$this->load->model('track');
        $this->load->model('Score_model');
	}

    function a1($param=''){
        //echo $this->Score_model->addScore(39,2);
        /*error_reporting(E_ALL);
        ini_set('display_errors','On');
        $this->Score_model->addScore(64,58609);*/
        /*$this->load->library('top_sdk');
        error_reporting(E_ALL);
        ini_set('display_errors','On');
        echo '<pre>';
        var_dump($this->top_sdk->getUser('335006,58609,7034,233373'));*/

        /*error_reporting(E_ALL);
        ini_set('display_errors','On');*/
        //$s = $this->sms->sendSMS(array("18664633705"), '新回复 评论饿了'.time());
        //$ss = $this->sms->getBalance();
        /*$this->load->model('Diary_model');
        $s1 = $this->Diary_model->getLastTagsForCategory(1307);
        echo '<pre>';
        print_r($s1);
        exit();*/
        $this->db->select('clientid');
        $this->db->where('phone',$this->input->get('phone'));
        $client = $this->db->get('users')->result_array();
        echo $client[0]['clientid'];
        exit();
        $this->load->library('igttui');
        $this->load->model('Users_model');

        $clientid = $this->input->get('clientid');// '6827206793fcc034b3858d9d89926193';
        $push = "diary:11:1:1111";
        $push = json_encode($push);
        //echo $push;
        $p = $this->igttui->sendMessage($clientid, $push);
        echo '<pre>';
        print_r($p);exit();
        #$result['debug'] = $clientid[0]['clientid'];
        if(!empty($clientid)) {
            $this->load->library('igttui');

            switch($this->input->get('type')) {
                case 'diary':

                    $push = "diary:11:1:[美人计]新回复 评论饿了";
                    //$push = json_encode($push);
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'topic':

                    $push = "topic:11:1:[美人计]新回复 评论饿了";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'other':

                    $push = "other:::[美人计]新回复 评论饿了:http://www.meilimei.com";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'notice':

                    $push = "notice:::[美人计]新回复 评论饿了";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'zixun':

                    $push = "zixun:11::[美人计]新回复 评论饿了:58609";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'houdong':
                    $push = "houdong:::[美人计]新回复 评论饿了:http://www.meilimei.com";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
                case 'tehui':
                    $push = "tehui:11::[美人计]新回复 评论饿了";
                    $arr = array('','');
                    foreach($arr as $item) {
                        $this->igttui->sendMessage($item, $push);
                    }
                    break;
                default:
                    $push = "diary:11:1:[美人计]新回复 评论饿了";
                        //$push = json_encode($push);
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "topic:11:1:[美人计]新回复 评论饿了";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "other:::[美人计]新回复 评论饿了:http://www.meilimei.com";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "notice:::[美人计]新回复 评论饿了";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "tehui:11::[美人计]新回复 评论饿了";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "zixun:11::[美人计]新回复 评论饿了:58609";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "shangou:11::闪购";
                    $this->igttui->sendMessage($clientid, $push);
                    $push = "houdong:::[美人计]新回复 评论饿了:http://www.meilimei.com";
                    $this->igttui->sendMessage($clientid, $push);
                    break;
            }
        }
        /*
        echo $this->Score_model->getScoreList(233373);
        error_reporting(E_ALL);
        ini_set('display_errors','On');
        $this->load->model('push');
        $push = array('type'=>'diary','id'=>'1243','page'=>1);
        $this->push->sendUser('[话题]新回复:'.'新回复',$this->input->get('uid'),$push);*/
    }

    public function guid($param='')
    {
        $result['state'] = '000';
        $result['data'] = array();

        $uid = $this->uid;
        $clientid = $this->input->post('clientid');

        if ($this->uid) {
            $result['data'] = $this->setUpdateClient($uid, $clientid);
            $result['notice'] = '获取成功!';
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
            $result['data'] = array();
        }
        echo json_encode($result);
    }

    public function checkCode(){
        $result['data'] = array();
        $result['state'] = '000';

        $code = $this->input->get('code');
        if ($this->session->userdata('veryCode') != $code) {
            $result['state'] = '016';
            $result['notice'] = '验证码错误'.$code.'===='.$this->session->userdata('veryCode').'=='.$this->session->userdata('veryCodeTime');
            echo json_encode($result);
            exit;
        }
        echo json_encode($result);
    }

    public function OpenIM(){
        $result = array();
        $result['state'] = '000';
        $this->load->library('top_sdk');
        $uid = $this->input->get('uid');
        $OpenIM = array();

        if(intval($uid) > 0){
            if($OpenIM = $this->top_sdk->getOpenIM($uid)) {
                $result['data'] = $OpenIM;
            }else{
                $result['state'] = '002';
                $result['notice'] = '获取参数出错';
                $result['data'] = array();
            }
        }else{
            if($OpenIM = $this->top_sdk->getOpenIM($uid)){
                $result['data'] = $OpenIM;
            }else{
                $result['state'] = '001';
                $result['notice'] = '获取参数出错';
                $result['data'] = array();
            }
        }
        echo json_encode($result);
    }
	//fast register
	private function freg($param='') {
		$result['state'] = '000';

		if ($this->input->post()) {
			$this->form_validation->set_rules('username', '用户名', 'trim|xss_clean');
			$email = $this->input->post('email')?$this->input->post('email'):md5(microtime())."@meilizhensuo.com";
			$phnum = $this->input->post('phone');
			$token = '';
			if ($this->input->post('token') != '') {
				$token = $this->input->post('tokentype') . '_' . $this->input->post('token');
			}

			//if ($email != '')
			//	$this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
			//if ($phnum != '')
			//	$this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');
			//$this->form_validation->set_rules('password', '密码', 'required|trim|min_length[5]|max_length[16]|xss_clean|matches[confirmpassword]');
			//$this->form_validation->set_rules('confirmpassword', '确认密码', 'required|trim|min_length[5]|max_length[16]|xss_clean');

			$device_sn = $email;//$this->input->post('phone');
			if (($phnum != '' || $email != '') && $device_sn != '') {
				//if ($this->form_validation->run()) {
					$username = $this->input->post('username');
                    $nickname = $this->input->post('nickname');
					if(strpos($username,'null') OR strlen($username)<3){
						$username = '';
					}
					$password = $this->input->post('password');
					$confirmpassword = $this->input->post('confirmpassword');
					if ($this->_check_user_alias($username, $result)) {
						$username = $username.$this->_check_user_alias($username, $result);
					}
					$username == '' && $username = $phnum;
					$username == '' && $username = $email;
					$utype = 1;
					if ($this->input->post('utype')) {
						$utype = intval($this->input->post('utype'));
					}


					//get system info
					$head = $_SERVER['HTTP_USER_AGENT'];
					if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
						$regsys = 'IOS';
					} else {
						$regsys = 'Android';
					}
					$this->wen_auth->_setRegFrom(2, $regsys);
					$data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, $token, $utype);
                    $this->db->where('id',$data['user_id']);
                    $this->db->update('users',array('alias'=>$username));
					$this->wen_auth->login($username, $password, TRUE);
                    $this->ckCoupon($phnum, $data['user_id']);
                    if(!empty($nickname)) {
                        $this->db->where('id', $data['user_id']);
                        $this->db->update('users', array('alias' => $nickname));
                    }
					//同步到特惠
                    if($this->input->post('clientid')){
                        $this->setUpdateClient($data['user_id'], $this->input->post('clientid'));
                    }
                    $result['messageNotReadCount'] = $this->getMessagesNotReadCount($data['user_id']);
					if (!empty ($data['user_id'])) {

						$tehuiData = array (
							'id' => $data['user_id'],
							'email' => $email,
							'username' => $phnum,
							'password' => crypt($this->wen_auth->_encode($password
						)), 'realname' => '', 'alipay_id' => '', 'avatar' => '', 'newbie' => 'Y', 'mobile' => $phnum, 'qq' => '', 'money' => 0.00, 'score' => 0, 'zipcode' => null, 'address' => '', 'city_id' => 0, 'emailable' => 'Y', 'enable' => 'Y', 'manager' => 'N', 'secret' => '', 'recode' => '', 'sns' => '', 'ip' => '', 'login_time' => time(), 'create_time' => time(), 'mobilecode' => '', 'secret' => md5(rand(1000000, 9999999) . time() . $phnum));
						$th_inertid = $this->tehui->reg_zuitu($tehuiData);

					}
                   $result['username'] = $username;
					if ($this->session->userdata('ref_id'))
						$ref_id = $this->session->userdata('ref_id');
					else
						$ref_id = "";
					if ($this->input->post('username') and $data['user_id'] and $this->_check_user_alias($this->input->post('username'), $result)) {
						$updateUser = array ();
						$result['username'] = $updateUser['alias'] = $updateUser['username'] = $this->input->post('username').$this->_check_user_alias($this->input->post('username'), $result);
						$this->updateinfo($updateUser, $data['user_id']);
					}
					if($this->input->post('thumb')){
						$updateUser = array ();
						$updateUser['icon'] = $this->input->post('thumb');
						$this->updateinfo($updateUser, $data['user_id']);
					}

					if ($this->input->post('device_sn') OR $this->input->post('device_mac')) {
						$updateUser = array ();
						$updateUser['device_sn'] = $this->input->post('device_sn');
						$updateUser['device_mac'] = $this->input->post('device_mac');
					}
					if (!empty ($ref_id)) {
						$details = $this->Referrals_model->get_user_by_refId($ref_id);
						$invite_from = $details->row()->id;
						$datas['invite_from'] = $invite_from; //$this->input->post('timezones');
						$this->db->where('id', $this->wen_auth->get_user_id());
						$this->db->update('users', $datas);
						$insertData = array ();
						$insertData['user'] = $invite_from;
						$insertData['price'] = 20;
						$insertData['code'] = $this->randomkeys();
						$insertData['expired'] = time() + 3600 * 24 * 30;
						$insertData['create_time'] = local_to_gmt();
						$insertData['reason'] = '推荐朋友 ' . $username . ' 注册而获取的优惠券';
						$this->Referrals_model->insertReferrals($insertData);
						$this->session->unset_userdata('ref_id');
					}
					//for user self

					$notification = array ();
					$notification['user_id'] = $this->wen_auth->get_user_id();
                    $result['messageNotReadCount'] = $this->getMessagesNotReadCount($this->wen_auth->get_user_id());
					if ($notification['user_id']) {
						$this->common->insertData('user_notification', $notification);
						$this->common->insertData('wen_notify', $notification);
					}
					if ($this->input->post('city') OR $this->input->post('sex')) {
						$upData = array ();
						$upData['sex'] = $this->input->post('sex');
						$upData['remark'] = $this->input->post('remark');
						$upData['city'] = $this->input->post('city');
						$cdi = array (
							"user_id" => $data['user_id']
						);
						$this->common->updateTableData('user_profile', '', $cdi, $upData);
					}
					//generate thumb
					if (isset ($_FILES['thumb']['tmp_name']) && $_FILES['thumb']['tmp_name']) {
						$this->thumb($data['user_id'], $_FILES['thumb']['tmp_name']);
					}
					elseif ($this->input->post('thumb')) {
						//	$result['bitdata'] = $this->input->post('thumb');
						// $bitdata = curl_get_file_contents($this->input->post('thumb'));
						$bitdata = '';
						$name = uniqid() . '.jpg';
						if ($bitdata) {
							file_put_contents('/var/www/meilimei.com/up_tmp/' . $name, $bitdata);
							$this->thumb($data['user_id'], '/var/www/meilimei.com/up_tmp/' . $name);
						}
					}
					$result['type'] = 1;
					$result['thumb'] = $this->remote->thumb($data['user_id'], '115');
					$result['ustate'] = '000';

					if ($idc = $this->input->post("idt")) {
						$this->insid($idc, $data['user_id']);
					}
					//send sms
					if ($phnum) {
						$this->sms->sendSMS(array (
							"{$phnum}"
						), "感谢你注册美丽神器 APP,你的账户:{$phnum},密码:{$password},请妥善保管");
					}
					
					$i = 0;
					for($i = 0; $i<5; $i++){
						$code = $this->tehui->tehuiSend($this->wen_auth->get_user_id(),true);
						if($phnum and $code.$i){
							$this->sms->sendSMS(array (
								"{$phone}"
							), '代金券号：'.$code.' 退订回复0000');
						}
					}
                    $jifen = $this->Score_model->addScore(39,$this->wen_auth->get_user_id());//第三方账号登陆注册加分20
                    $result['score'] = $jifen;
					//sned tehui
					$this->tehui->tehuiSend($this->wen_auth->get_user_id());

					
					//callback
					if (($device_sn = $this->input->post('device_sn')) or $device_sn = $this->input->post('device_mac')) {
						$this->callbackAdv($device_sn);
					}
					return $this->authlogin($this->input->post('tokentype'),$this->input->post('token'));
			} else {
				$result['ustate'] = '012';
			}
		} else {
			$result['ustate'] = '001'; //登入失败
		}
		//$result['xxx'] = $this->wen_auth->get_user_id();
		echo json_encode($result);

	}

	//get url file
	private function curl_get_file_contents($URL) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($c, CURLOPT_HEADER, 1);//输出远程服务器的header信息
		curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;' . $URL . ')');
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
		if ($contents) {
			return $contents;
		} else {
			return FALSE;
		}
	}
	//check token exist
	private function hasToken($token = '') {
		$this->db->like('title', 'match');
		$this->db->from('users');
		return $this->db->count_all_results();
	}

	private function isReg($type,$token){

		$query = $this->Users_model->get_by_ref_id($type . '_' . $token);
		$row = $query->row();
		$num = $query->num_rows();
		if ($num > 0) {
			return true;
		}else{
			return false;
		}
		
	}
	//normal register
	public function reg($param='') {

		$this->load->helper('form');
		$result['state'] = '000';

		if($this->input->post('type') == 2){
					
			$isReg = $this->isReg($this->input->post('tokentype'),$this->input->post('token'));

			if($isReg){

				$result['state'] = '002';

				return $this->authlogin($this->input->post('tokentype'),$this->input->post('token'));
			}

			return $this->freg();
		}


		
		if ($this->input->post()) {
			$this->form_validation->set_rules('username', '用户名', 'trim|xss_clean');
			$email = $this->input->post('email');
			$phnum = $this->input->post('phone');
            $nickname = $this->input->post('nickname');
			$token = '';
			if ($this->input->post('token') == '') {
				if ($code = $this->input->post('code')) {
                    $result['debug'] = $code."===".$this->session->userdata('veryCode');
					if ($this->session->userdata('veryCode') != $code) {
						$result['ustate'] = '016';
						$result['notice'] = '验证码错误';
						echo json_encode($result);
						exit;
					}
				}
				elseif ($phnum) {
					$result['notice'] = '参数不全';
					$result['state'] = '012';
					echo json_encode($result);
					exit;
				}
			} else {
				$token = $this->input->post('tokentype') . '_' . $this->input->post('token');
			}

			if ($email != '')
				$this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
			if ($phnum != '')
				$this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');
			$this->form_validation->set_rules('password', '密码', 'required|trim|min_length[5]|max_length[16]|xss_clean|matches[confirmpassword]');
			$this->form_validation->set_rules('confirmpassword', '确认密码', 'required|trim|min_length[5]|max_length[16]|xss_clean');

			if (($device_sn = $this->input->post('device_sn')) or $device_sn = $this->input->post('device_mac')) {
				$this->callbackAdv($device_sn);
			}
			if (($phnum != '' || $email != '') ) {
				if ($this->form_validation->run()) {
					$username = $this->input->post('username');

					$password = $this->input->post('password');
					$confirmpassword = $this->input->post('confirmpassword');
					$username == '' && $username = $phnum;
					$username == '' && $username = $email;
					$utype = 1;
					if ($this->input->post('utype')) {
						$utype = intval($this->input->post('utype'));
					}
					$result['username'] = $username;
					//get system info
					$head = $_SERVER['HTTP_USER_AGENT'];
					if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
						$regsys = 'IOS';
					} else {
						$regsys = 'Android';
					}
					if ($this->_check_user_alias($username, $result)) {
						$username = $username.$this->_check_user_alias($username, $result);
					}
					$this->wen_auth->_setRegFrom(2, $regsys);
					$data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, $token, $utype);
					$this->wen_auth->login($username, $password, TRUE);
                    $this->ckCoupon($phnum, $data['user_id']);
                    if($this->input->post('clientid')){
                        $this->setUpdateClient($data['user_id'], $this->input->post('clientid'));
                    }
                    if(!empty($nickname)) {
                        $this->db->where('id', $data['user_id']);
                        $this->db->update('users', array('alias' => $nickname));
                    }
                    //$this->db->where('phone',$this->input->post('phone'));
                    //$this->db->update('users',array('jifen'=>1000));
					//同步到特惠
					if (!empty ($data['user_id'])) {

						$tehuiData = array (
							'id' => $data['user_id'],
							'email' => $email,
							'username' => $phnum,
							'password' => crypt($this->wen_auth->_encode($password
						)), 'realname' => '', 'alipay_id' => '', 'avatar' => '', 'newbie' => 'Y', 'mobile' => $phnum, 'qq' => '', 'money' => 0.00, 'score' => 0, 'zipcode' => null, 'address' => '', 'city_id' => 0, 'emailable' => 'Y', 'enable' => 'Y', 'manager' => 'N', 'secret' => '', 'recode' => '', 'sns' => '', 'ip' => '', 'login_time' => time(), 'create_time' => time(), 'mobilecode' => '', 'secret' => md5(rand(1000000, 9999999) . time() . $phnum));
						$th_inertid = $this->tehui->reg_zuitu($tehuiData);
						//$th_inertid = $this->tehui->reg_zuitu($tehuiData);

					}

					if ($this->session->userdata('ref_id'))
						$ref_id = $this->session->userdata('ref_id');
					else
						$ref_id = "";
					if ($this->input->post('device_sn') OR $this->input->post('device_mac') OR true) {
						$updateUser = array ();
						$updateUser['device_sn'] = $this->input->post('device_sn');
						$updateUser['device_mac'] = $this->input->post('device_mac');
						$this->updateinfo($updateUser, $data['user_id']);
					}
					if ($this->input->post('remark') and $data['user_id']) {
						$updateUser['remark'] = $this->input->post('remark');
						$this->updateinfo($updateUser, $data['user_id']);
					}
					if (!empty ($ref_id)) {
						$details = $this->Referrals_model->get_user_by_refId($ref_id);
						$invite_from = $details->row()->id;
						$datas['invite_from'] = $invite_from; //$this->input->post('timezones');
						$this->db->where('id', $this->wen_auth->get_user_id());
						$this->db->update('users', $datas);
						$insertData = array ();
						$insertData['user'] = $invite_from;
						$insertData['price'] = 20;
						$insertData['code'] = $this->randomkeys();
						$insertData['expired'] = time() + 3600 * 24 * 30;
						$insertData['create_time'] = local_to_gmt();
						$insertData['reason'] = '推荐朋友 ' . $username . ' 注册而获取的优惠券';
						$this->Referrals_model->insertReferrals($insertData);
						$this->session->unset_userdata('ref_id');
					}
					//for user self

					$notification = array ();
					$notification['user_id'] = $this->wen_auth->get_user_id();
					if ($notification['user_id']) {
						$this->common->insertData('user_notification', $notification);
						$this->common->insertData('wen_notify', $notification);
					}
					if ($this->input->post('city')) {
						$upData = array ();
						$upData['city'] = $this->input->post('city');
						$cdi = array (
						"user_id" => $this->wen_auth->get_user_id());
						$this->common->updateTableData('user_profile', '', $cdi, $upData);
					}
					//send sms
					/*if ($phnum) {
						$this->sms->sendSMS(array (
							"{$phone}"
						), "感谢你注册美丽神器 APP,你的账户:{$phone},密码:{$password},请妥善保管" . '退订回复0000 ');
					}*/
					//send tehui
					/*$i = 0;
					for($i = 0; $i<5; $i++){
						$code = $this->tehui->tehuiSend($this->wen_auth->get_user_id(),true);
						if($phnum and $code.$i){
							$this->sms->sendSMS(array (
								"{$phone}"
							), ' 代金券号：'.$code.' 退订回复0000');
						}
					}*/
					$result['notice'] = '注册成功';
					$result['ustate'] = '000';
				} else {
                    //$this->db->where('phone',$this->input->post('phone'));
                    //$this->db->update('users',array('jifen'=>1000));
					$result['notice'] = '手机号不正确或者已使用';
					$result['ustate'] = '008';
				}
			} else {
				$result['notice'] = '参数不全';
				$result['ustate'] = '012';
			}
		} else {
			$result['notice'] = '账户已登入不能注册';
			$result['ustate'] = '001'; //登入失败
		}
        $jifen = $this->Score_model->addScore(39,$this->wen_auth->get_user_id());//登陆加分20
        $result['score'] = $jifen;
        $result['data']['logintype'] = '';
		echo json_encode($result);

	}

    public function reg41($param='') {

        $this->load->helper('form');
        $result['state'] = '000';

        if($this->input->post('type') == 2){

            $isReg = $this->isReg($this->input->post('tokentype'),$this->input->post('token'));

            if($isReg){

                $result['state'] = '002';

                return $this->authlogin($this->input->post('tokentype'),$this->input->post('token'));
            }

            return $this->freg();
        }



        if ($this->input->post() && $this->notlogin) {
            $this->form_validation->set_rules('username', '用户名', 'trim|xss_clean');
            $email = $this->input->post('email');
            $phnum = $this->input->post('phone');
            $token = '';
            if ($this->input->post('token') == '') {
                /*if ($code = $this->input->post('code')) {
                    $result['debug'] = $code."===".$this->session->userdata('veryCode');
                    if ($this->session->userdata('veryCode') != $code) {
                        $result['ustate'] = '016';
                        $result['notice'] = '验证码错误';
                        echo json_encode($result);
                        exit;
                    }
                }
                elseif ($phnum) {
                    $result['notice'] = '参数不全';
                    $result['state'] = '012';
                    echo json_encode($result);
                    exit;
                }*/
            } else {
                $token = $this->input->post('tokentype') . '_' . $this->input->post('token');
            }

            if ($email != '')
                $this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
            if ($phnum != '')
                $this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');
            $this->form_validation->set_rules('password', '密码', 'required|trim|min_length[5]|max_length[16]|xss_clean|matches[confirmpassword]');
            $this->form_validation->set_rules('confirmpassword', '确认密码', 'required|trim|min_length[5]|max_length[16]|xss_clean');

            if (($device_sn = $this->input->post('device_sn')) or $device_sn = $this->input->post('device_mac')) {
                $this->callbackAdv($device_sn);
            }
            if (($phnum != '' || $email != '') ) {
                if ($this->form_validation->run()) {
                    $username = $this->input->post('username');

                    $password = $this->input->post('password');
                    $confirmpassword = $this->input->post('confirmpassword');
                    $username == '' && $username = $phnum;
                    $username == '' && $username = $email;
                    $utype = 1;
                    if ($this->input->post('utype')) {
                        $utype = intval($this->input->post('utype'));
                    }
                    $result['username'] = $username;
                    //get system info
                    $head = $_SERVER['HTTP_USER_AGENT'];
                    if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
                        $regsys = 'IOS';
                    } else {
                        $regsys = 'Android';
                    }
                    if ($this->_check_user_alias($username, $result)) {
                        $username = $username.$this->_check_user_alias($username, $result);
                    }
                    $this->wen_auth->_setRegFrom(2, $regsys);
                    $data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, $token, $utype);
                    $this->wen_auth->login($username, $password, TRUE);
                    //$this->db->where('phone',$this->input->post('phone'));
                    //$this->db->update('users',array('jifen'=>1000));
                    //同步到特惠
                    if (!empty ($data['user_id'])) {

                        $tehuiData = array (
                            'id' => $data['user_id'],
                            'email' => $email,
                            'username' => $phnum,
                            'password' => crypt($this->wen_auth->_encode($password
                            )), 'realname' => '', 'alipay_id' => '', 'avatar' => '', 'newbie' => 'Y', 'mobile' => $phnum, 'qq' => '', 'money' => 0.00, 'score' => 0, 'zipcode' => null, 'address' => '', 'city_id' => 0, 'emailable' => 'Y', 'enable' => 'Y', 'manager' => 'N', 'secret' => '', 'recode' => '', 'sns' => '', 'ip' => '', 'login_time' => time(), 'create_time' => time(), 'mobilecode' => '', 'secret' => md5(rand(1000000, 9999999) . time() . $phnum));
                        $th_inertid = $this->tehui->reg_zuitu($tehuiData);

                    }

                    if ($this->session->userdata('ref_id'))
                        $ref_id = $this->session->userdata('ref_id');
                    else
                        $ref_id = "";
                    if ($this->input->post('device_sn') OR $this->input->post('device_mac') OR true) {
                        $updateUser = array ();
                        $updateUser['device_sn'] = $this->input->post('device_sn');
                        $updateUser['device_mac'] = $this->input->post('device_mac');
                        $this->updateinfo($updateUser, $data['user_id']);
                    }
                    if ($this->input->post('remark') and $data['user_id']) {
                        $updateUser['remark'] = $this->input->post('remark');
                        $this->updateinfo($updateUser, $data['user_id']);
                    }
                    if (!empty ($ref_id)) {
                        $details = $this->Referrals_model->get_user_by_refId($ref_id);
                        $invite_from = $details->row()->id;
                        $datas['invite_from'] = $invite_from; //$this->input->post('timezones');
                        $this->db->where('id', $this->wen_auth->get_user_id());
                        $this->db->update('users', $datas);
                        $insertData = array ();
                        $insertData['user'] = $invite_from;
                        $insertData['price'] = 20;
                        $insertData['code'] = $this->randomkeys();
                        $insertData['expired'] = time() + 3600 * 24 * 30;
                        $insertData['create_time'] = local_to_gmt();
                        $insertData['reason'] = '推荐朋友 ' . $username . ' 注册而获取的优惠券';
                        $this->Referrals_model->insertReferrals($insertData);
                        $this->session->unset_userdata('ref_id');
                    }
                    //for user self

                    $notification = array ();
                    $notification['user_id'] = $this->wen_auth->get_user_id();
                    if ($notification['user_id']) {
                        $this->common->insertData('user_notification', $notification);
                        $this->common->insertData('wen_notify', $notification);
                    }
                    if ($this->input->post('city')) {
                        $upData = array ();
                        $upData['city'] = $this->input->post('city');
                        $cdi = array (
                            "user_id" => $this->wen_auth->get_user_id());
                        $this->common->updateTableData('user_profile', '', $cdi, $upData);
                    }
                    //send sms
                    if ($phnum) {
                        $this->sms->sendSMS(array (
                            "{$phone}"
                        ), "感谢你注册美丽神器 APP,你的账户:{$phone},密码:{$password},请妥善保管" . '退订回复0000 ');
                    }
                    //send tehui
                    $i = 0;
                    for($i = 0; $i<5; $i++){
                        $code = $this->tehui->tehuiSend($this->wen_auth->get_user_id(),true);
                        if($phnum and $code.$i){
                            $this->sms->sendSMS(array (
                                "{$phone}"
                            ), '代金券号：'.$code.' 退订回复0000');
                        }
                    }
                    $result['notice'] = '注册成功';
                    $result['ustate'] = '000';
                } else {
                    //$this->db->where('phone',$this->input->post('phone'));
                    //$this->db->update('users',array('jifen'=>1000));
                    $result['notice'] = '手机号不正确或者已使用';
                    $result['ustate'] = '008';
                }
            } else {
                $result['notice'] = '参数不全';
                $result['ustate'] = '012';
            }
        } else {
            $result['notice'] = '账户已登入不能注册';
            $result['ustate'] = '001'; //登入失败
        }

        $result['data']['logintype'] = '';
        echo json_encode($result);

    }
	//callback advertise api
	private function callbackAdv($token = '') {
		$this->load->model('track');
		if (strlen($token) < 18) {
			$this->track->advNotify('', $token);
		} else {
			$this->track->advNotify($token, '');
		}
	}
	private function updateinfo($updata = array (), $uid) {
		$this->db->where('id', $uid);
		$this->db->limit('1');
		$this->db->update('users', $updata);
	}

    private function setUpdateClient($uid,$clientid=0){

        if(empty($clientid))
            return ;
        $this->db->where('id',$uid);
        return $this->db->update('users',array('clientid'=>$clientid));
    }

    public function signin($param='') {
        //$this->input->set_cookie("username",'xxxxx',60);
        $result['state'] = '000';
        $this->form_validation->set_error_delimiters($this->config->item('field_error_start_tag'), $this->config->item('field_error_end_tag'));
        $result['ustate'] = '001';

        if ($this->input->post("username") || $this->notlogin) {
            // Set form validation rules
            $this->form_validation->set_rules('username', '用户名', 'required|trim|xss_clean');
            $this->form_validation->set_rules('password', '密码', 'required|trim|xss_clean');
            $this->form_validation->set_rules('remember', 'Remember me', 'integer');

            if ($this->form_validation->run()) {
                $username = $this->input->post("username");
                $password = $this->input->post("password");

                if ($this->wen_auth->login($username, $password, true)) {
                    $result['ustate'] = '000';

                    $uid = $this->wen_auth->get_user_id();

                    $this->session->set_uid($this->wen_auth->get_user_id());
                    $tmp = $this->db->query("SELECT  * FROM users WHERE users.id = {$uid} LIMIT 1")->result_array();
                    if (strpos($tmp[0]['ref_id'], 'q_')) {
                        $result['regtype'] = 'qq';
                    }
                    elseif (strpos($tmp[0]['ref_id'], 'eibo_')) {
                        $result['regtype'] = 'weibo';
                    } else {
                        $result['regtype'] = '';
                    }

                    $uname = '';

                    if ($tmp[0]['alias'] != '' and preg_match('/^\\d+$/', $tmp[0]['alias'])) {
                        $uname = substr($tmp[0]['alias'], 0, 4) . '***';
                    }
                    elseif ($tmp[0]['alias'] != '') {
                        $uname = $tmp[0]['alias'];
                    } else {
                        $uname = substr($tmp[0]['phone'], 0, 4) . '***';
                    }


                    $newdata = array (
                        'user' => $uid,
                        'username' => "'" . $uname . "'", 'logged_in' => TRUE);
                    $this->session->set_userdata($newdata);

                    $result['username'] = $uname;
                    $result['type'] = $this->wen_auth->get_role_id();
                    $result['uid'] = $uid;
                    $result['phone'] = isset($tmp[0]['phone'])?$tmp[0]['phone']:'';
                    $result['expire'] = time()+86400*100;
                    $result['sessionid'] = md5($this->appkey.str_replace(' ','',microtime()));
                    $result['version'] = FACES_VERSION;
                    $result['thumb'] = $this->profilepic($uid, 2);
                    $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());
                    $result['phonetime'] = 0;
                    if(count($result['items']) < 3){
                        unset($result['items']);
                        $this->addFollowUser($this->wen_auth->get_user_id());
                        $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());
                    }
                    $result['messageNotReadCount'] = $this->getMessagesNotReadCount($this->wen_auth->get_user_id());
                    $result['follows'] = $this->getFollow($uid);
                    $result['jifen'] = $tmp[0]['jifen'];
                    $result['logintype'] = '';
                    $result['category_version'] = '1.0';
                    $result['background'] =  'http://pic.meilimei.com.cn/upload/bg/grzx_bg1@2x.png';
                    $updata_session = array();
                    $updata_session['login_num'] = $tmp[0]['login_num'] + 1;
                    $this->ckCoupon($result['phone'],$result['uid']);
                    if($updata_session['login_num'] == 1){
                        $result['zixun'] = '看看昆凌整了那么多，才收服了杰伦。
有什么整形美容的问题，想问全国顶级的专家吗？
点击这里直接问吧~ ';
                        $result['writeselfinfo'] = 1;
                    }else {
                        $result['writeselfinfo'] = 0;
                        if($tmp[0]['banned_zixun'] == 1){
                            $result['zixun'] = '';
                        }else{
                            if (($updata_session['login_num'] % 10) == 0) {
                                $result['zixun'] = '看看昆凌整了那么多，才收服了杰伦。
有什么整形美容的问题，想问全国顶级的专家吗？
点击这里直接问吧~ ';
                            } else {
                                $result['zixun'] = '';
                            }
                        }
                    }
                    $jifen = $this->Score_model->addScore(39,$this->wen_auth->get_user_id());//登陆加分20
                    $result['score'] = $jifen;
                    $updata_session['expire'] = $result['expire'] ;
                    $updata_session['sessionid'] = $result['sessionid'];
                    $this->db->where('id', $uid);
                    $this->db->update('users', $updata_session);
                    if($clientid = $this->input->post('clientid')) {
                        $this->setUpdateClient($uid, $clientid);
                    }
                    $result['debug'] = $this->input->post('clientid');
                    //log user location
                    if ($this->input->post("lat")) {
                        $updata = array (
                            'lng' => $this->input->post("lng"),
                            'lat' => $this->input->post("lat")
                        );
                        $this->db->where('id', $uid);
                        $this->db->update('users', $updata);
                    }
                    //log device
                    if ($idc = $this->input->post("idt")) {
                        $this->insid($idc, $uid);
                    }
                } else {
                    $result['ustate'] = '001';
                    $result['notice'] ='用户名和密码错误';
                }
            } else {
                $result['state'] = '012';
            }
        }
        //$result['ddd'] = $this->session->all_userdata();
        //$result['session_id'] = $this->session->userdata('session_id');
        echo json_encode($result);
    }
	//log user device info
	private function insid($idt, $uid, $bind = true) {
		$idt = trim($idt);
		$idt = str_replace(' ', '', $idt);
		$idt = substr($idt, 1, strlen($idt) - 2);
		$this->db->where('devicetoken', $idt);
		$this->db->from('apns_devices');
		$n = $this->db->count_all_results();
		!$bind && $uid = '';
		if ($n) {
			$data = array (
				'uid' => $uid,
			'modified' => time());
			$this->db->where('devicetoken', $idt);
			$this->db->update('apns_devices', $data);
		}
	}
	function qqlogin($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '001';

		if ($query = $this->Users_model->get_by_ref_id('qq_' . $this->input->post('qq_token')) AND $query->num_rows() == 1 and $this->notlogin) {
			$row = $query->row();
			if ($row->banned > 0) {
				$result['ustate'] = '001';
			} else {
				$this->wen_auth->_set_session($row);
				$this->wen_auth->_set_last_ip_and_last_login($row->id);
				$this->wen_auth->_clear_login_attempts();
				$this->wen_auth_event->user_logged_in($row->id);
				$result['ustate'] = '000';
				$result['username'] = $this->wen_auth->get_username();
				$result['type'] = $this->wen_auth->get_role_id();
				$result['uid'] = $this->wen_auth->get_user_id();
				$result['thumb'] = $this->profilepic($result['uid'], 2);
			}
		}

		echo json_encode($result);
	}

	//auth from qq weibo login
	private function authlogin($type, $token) {
		$result['state'] = '000';
		
		if ($token) {
			
			$query = $this->Users_model->get_by_ref_id($type . '_' . $token);
			if ($query) {
				$row = $query->row();

				if ($query->num_rows() == 0) {
					$result['state'] = '400';
					echo json_encode($result);
					exit;
				}
				if ($row->banned > 0) {
					$result['notice'] = '该用户被禁止登陆';
					$result['ustate'] = '001';
				} else {

					$this->wen_auth->_set_session($row);
					$this->wen_auth->_set_last_ip_and_last_login($row->id);
					$this->wen_auth->_clear_login_attempts();
					$this->wen_auth_event->user_logged_in($row->id);
                    $result['logintype'] = $type?$type:'';
					$result['ustate'] = '000';
					$result['version'] = FACES_VERSION;
					$result['username'] = $this->wen_auth->get_username();
					$result['type'] = $this->wen_auth->get_role_id();
					$result['uid'] = $this->wen_auth->get_user_id();

                    $result['phone'] = isset($row->phone)?$row->phone:'';

                    $update_session = array();
                    $update_session['login_num'] = $row->login_num + 1;

                    if($update_session['login_num'] == 1){
                        $result['phonetime'] = 1;
                        $result['zixun'] = '看看昆凌整了那么多，才收服了杰伦。
有什么整形美容的问题，想问全国顶级的专家吗？
点击这里直接问吧~ ';
                        $result['writeselfinfo'] = 1;
                    }else {
                        $result['writeselfinfo'] = 0;
                        if($row->banned_zixun == 1){
                            $result['zixun'] = '';
                        }else {
                            if (($update_session['login_num'] % 10) == 0) {
                                $result['zixun'] = '看看昆凌整了那么多，才收服了杰伦。
有什么整形美容的问题，想问全国顶级的专家吗？
点击这里直接问吧~ ';
                            } else {
                                $result['zixun'] = '';
                            }
                        }

                        if($row->banned_phone == 1){
                            $result['phonetime'] = 0;
                        }else{
                            if (($update_session['login_num'] % 5) == 0) {
                                $result['phonetime'] = 2;
                            }else{
                                $result['phonetime'] = 0;
                            }
                        }
                    }
                    $result['expire'] = time()+86400*100;
					$result['sessionid'] = md5($this->appkey.str_replace(' ','',microtime()));
					$this->db->query("update users set sessionid='".$result['sessionid'] ."',expire='".$result['expire']."', login_num='".$update_session['login_num']."' where id=".$this->wen_auth->get_user_id()." limit 1");
                    $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());

                    if(count($result['items']) < 3){
                        unset($result['items']);
                        $this->addFollowUser($this->wen_auth->get_user_id());
                        $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());
                    }
                    $result['jifen'] = $row->jifen?$row->jifen:0;
                    $result['category_version'] = '1.0';
                    $result['follows'] = $this->getFollow($this->wen_auth->get_user_id());
                    $result['background'] =  'http://pic.meilimei.com.cn/upload/bg/grzx_bg1@2x.png';
					$result['thumb'] = $row->icon?$row->icon:$this->profilepic($result['uid'], 2);
                    $result['uid'] = $this->wen_auth->get_user_id();
                    $result['city'] = isset($row->city)?$row->city:'';
                    if(isset($row->age)) {
                        $result['age'] = $this->getAge($row->age);
                    }else{
                        $result['age'] = '';
                    }
					$this->session->set_uid($result['uid']);
					if ($idc = $this->input->post("idt")) {
						$this->insid($idc, $result['uid'], true);
					}
				}
			}
		} else {
			$result['state'] = '012';
		}
		
		echo json_encode($result);
	}

    public function banned($param = ''){

        $result['state'] = '000';
        $result['data'] = array();
        //$this->uid= $this->input->get('uid');
        $type = $this->input->get('type')?$this->input->get('type'):0;
        if($this->uid) {
            if ($type == 1) {
                $this->db->where('id',$this->uid);
                $this->db->update('users', array('banned_zixun'=>1));
            } else {
                $this->db->where('id',$this->uid);
                $this->db->update('users', array('banned_phone'=>1));
            }
            $result['notice'] = '禁用成功！';
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }

        echo json_encode($result);
    }
//testing
    public function yy(){

        $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());

        if(count($result['items']) < 3){
            unset($result['items']);
            $this->addFollowUser($this->wen_auth->get_user_id());
            $result['items'] = $this->getUserItems($this->wen_auth->get_user_id());
        }

        echo json_encode($result);
    }
    private function getUserItems($uid = 0){

        $arr_item = $this->Users_model->get_user_fav_by_uid($uid);

        return $arr_item;
    }

    private function addFollowUser($uid){

        $rs = $this->getChildItem(0,$uid);

        $data = $rs[rand(0,count($rs)-1)];

        $user_fav = array('cid' => $data['id'], 'uid' => $this->uid, 'tag_img' => $data['surl'], 'tag' => $data['name'], 'colors'=>$data['colors'],'created_at' => time(), 'updated_at' => time());
        return $this->db->insert('user_fav', $user_fav);
    }

    private function getChildItem($pid='',$uid = 0){


        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,burl,colors,is_hot as num,img_png as surl');
        $tmp = $this->db->get('new_items')->result_array();

        $data = array();
        $key = array('261');
        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show($item['burl']);
            $item['surl'] = $this->remote->show320($item['surl']);

            if ($item['id'] == 261 or $item['id'] == 362 or $item['id'] == 399)
                continue;

            $itemtmp = array();
            $this->db->where('pid',$item['id']);
            $this->db->select("id, pid, name,surl,burl,colors,is_hot as num, img_png as surl");
            $itemtmp = $this->db->get('new_items')->result_array();

            foreach($itemtmp as $k=>$i){

                $itemtmp[$k]['burl'] = $this->remote->show($itemtmp[$k]['surl']);
                $itemtmp[$k]['surl'] = $this->remote->show320($itemtmp[$k]['surl']);
                $tmpitem = $itemtmp;
            }
            $item['child'] = $tmpitem ? $tmpitem : array();

            $data[] = $item['child'];
        }
        $res = array();
        if(!empty($data)){
            $this->db->where('uid',$uid);
            $this->db->select('tag');
            $q = $this->db->get('user_fav')->result_array();
            $arr_user = array();

            if(!empty($q)){
                foreach($q as $qitem){
                    $arr_user[] = $qitem['tag'];
                }
            }
            foreach($data as $item){
                if(!empty($item)) {
                    foreach ($item as $it) {
                        if(in_array($it['name'],$arr_user)){
                            continue;
                        }
                        $res[] = $it;
                    }
                }
            }
        }
        $follows = $this->getFollow($uid);
        if(count($follows) > 0){
            return $follows;
        }else {
            return $res;
        }
    }




	function logout($param = '') {
		$result['state'] = '000';

		if (!$this->notlogin) {
			
			$this->wen_auth->logout();
			if ($idc = $this->input->post("idt")) {
				$this->insid($idc, $this->uid, false);
			}
			$time = time() - 86400;
			$this->db->where('id', $this->uid);
			$this->db->update('users', array('expire'=>$time));

			$result['state'] = '000';
			$result['sessionClear'] = '000';

		} else {
			
			$result['state'] = '001';
			$result['sessionClear'] = '001';
		}
		echo json_encode($result);
	}

	public function bindIphoneToken($param = '') {
		$result['state'] = '000';
		$result['updateState'] = '001';
		if (!$this->notlogin) {
			$str = "UPDATE `users` SET `iphone_token` = '{$this->input->post('IphoneToken')}' WHERE `id` ={$this->wen_auth->get_user_id()}";
			$this->db->query($str);
			$result['updateState'] = '000';
		} else {
			$result['ustate'] = '001';
		}
		echo json_encode($result);
	}

	//get user information
	function getinfo($param='') {
		$result['state'] = '000';

		//$uid = intval($this->input->get('uid'));
		$uid = $this->uid;
		$tmp = $this->db->query("SELECT users.ref_id,users.id,users.jifen,users.grade, users.username ,users.age,users.alias,users.email,users.phone,users.created,user_profile.* FROM users LEFT JOIN user_profile ON user_profile.user_id = users.id WHERE users.id = {$uid} LIMIT 1")->result_array();
		$result['data'] = $tmp[0];
		if ($result['data']['birthday']) {
			$result['data']['birthday'] = date('Y-m-d', $result['data']['birthday']);
		}
        $result['data']['age'] = strval(intval($result['data']['age']));
		$result['data']['uname'] = '';
		if ($this->input->get('normal')) {
			if ($result['data']['alias'] != '') {
				$result['data']['uname'] = $result['data']['alias'];
			} else {
				$result['data']['uname'] = substr($result['data']['phone'], 0, 4) . '***';
			}
		} else {
			if ($result['data']['alias'] != '' and preg_match('/^\\d+$/', $result['data']['alias'])) {
				$result['data']['uname'] = substr($result['data']['alias'], 0, 4) . '***';
			}
			elseif ($result['data']['alias'] != '') {
				$result['data']['uname'] = $result['data']['alias'];
			} else {
				$result['data']['uname'] = substr($result['data']['phone'], 0, 4) . '***';
			}
		}
        if($result['data']['uname'] == "***"){
            $result['data']['uname'] = $result['data']['username'];
        }
        $arr = explode('_',$result['data']['ref_id']);
        if(isset($arr[0]) && ($arr[0]=='qq' || $arr[0] == 'weibo')) {
            $result['data']['logintype'] = 1;
		}else{
            $result['data']['logintype'] = 0;
        }
        $result['data']['city'] = $result['data']['city']?$result['data']['city']:'设置常住地';
        $result['data']['jifen'] = $result['data']['jifen']?$result['data']['jifen']:0;
        $result['data']['username'] = $result['data']['uname'];
        $result['data']['idname'] = $result['data']['username'];
        $result['data']['alias'] =  $result['data']['uname'];
		$result['data']['favrite'] = $this->countfavrite($uid);
		$result['data']['guangzhu'] = $this->countfollow($uid);
		$result['data']['fensi'] = $this->countffensi($uid);
		$result['data']['thumb'] = $this->profilepic($result['data']['id'], 2);
        $result['data']['totalCountDay'] = floor((time() - $result['data']['created'])/86400);
		$result['data']['created'] = date('Y-m-d', $result['data']['created']);

		$result['ustate'] = '000';
		echo json_encode($result);
	}

	private function countfavrite($uid) {
		$this->db->where('uid', $uid);
		$this->db->from('wen_favrite');
		return $this->db->count_all_results();
	}
	private function countfollow($uid) {
		$this->db->where('fid', $uid);
		$this->db->from('wen_follow');
		return $this->db->count_all_results();
	}
	private function countffensi($uid) {
		$this->db->where('uid', $uid);
		$this->db->from('wen_follow');
		return $this->db->count_all_results();
	}
	function countQuestion($param='') {

		$this->db->select('id');
		$this->db->from('wen_questions');
		if ($this->input->get('uid')) {
			$this->db->where('fUid =', $this->input->get('uid'));
		}
		if ($this->input->get('device_sn')) {
			$this->db->where('device_sn =', $this->input->get('device_sn'));
		}
		$this->db->order_by("id", "desc");
		$tmp = $this->db->get()->result_array();
		$result['num'] = count($tmp);
		$result['state'] = '000';
		echo json_encode($result);
	}

	function _check_user_name() {
		$username = $this->input->post('username');
		if (strlen($username) < 28 && !$this->hasnumber($username) && !preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $username)) {
			if ($this->wen_auth->is_username_available($username)) {
				return true;
			} else {
				$this->form_validation->set_message('_check_user_name', '用户名已被使用或者无效！');
				return false;
			}
		} else {
			$this->form_validation->set_message('_check_user_name', '用户名不能是邮箱,手机号,QQ以及其他非法字符！');
			return false;
		}
	}
	private function _check_user_alias($username = '', & $result) {
		if (!strpos($username, 'null') and $this->str_len($username) > 3 and $this->str_len($username) < 30 and preg_match('/^[\x80-\xff_a-zA-Z0-9]+$/', $username)) {
			$state = $this->db->query("select count(*) as num from users where alias = '{$username}' ")->result_array();
			$state = $state[0]['num'];
			$tmp = strtolower($username);
			//$result['notice'] = $state;
			if (is_int(strpos($tmp, 'null'))) {
				return false;
			}


			if ($state > 0) {
				return false;
			}
		} else {
			return false;
		}
	}
	private function str_len($str) {
		$length = strlen(preg_replace('/[x00-x7F]/', '', $str));
		if ($length) {
			return strlen($str) - $length +intval($length / 3) * 2;
		} else {
			return strlen($str);
		}
	}
	private function _check_user($username = '') {
		if ($this->str_len($username) > 3 and $this->str_len($username) < 30 and preg_match('/^[\x80-\xff_a-zA-Z0-9]+$/', $username)) {
			return true;
		} else {
			return false;
		}
	}
	function _check_user_email() {
		$email = $this->input->post('email');
		if ($this->wen_auth->is_email_available($email) && preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $email)) {
			return true;
		} else {
			$this->form_validation->set_message('_check_user_email', '该邮箱已经被使用！');
			return false;
		} //If end
	}

	function _check_phone_no($value) {
		$value = trim($value);
		if (true) {
			if ($this->wen_auth->is_phone_available($value)) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_check_phone_no', '该手机号码已被使用');
				return FALSE;
			}

		} else {
			$this->form_validation->set_message('_check_phone_no', '请输入有效的手机号码');
			return FALSE;
		}
	}

	function state($param='') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			$condition = array (
				"user_id" => $this->uid
			);
			$tmp = $this->Common_model->getTableData('wen_notify', $condition);
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	public function is_log($param='') {

		$result['ustate'] = '000';
		if ($this->notlogin) {
			$result['ustate'] = '001';
		}
		$result['state'] = '000';
		echo json_encode($result);
	}

    private function getMessageNotReadCount(){


        $result['uid'] = $this->uid;
        $result['message'] = array();

        if (!isset ($res['new_answer'])) {
            $tmp = $this->db->get_where('wen_notify', array(
                'user_id' => $this->uid
            ), 1)->result_array();
            if (empty ($tmp)) {
                $insdata = array(
                    'user_id' => $this->uid
                );
                $this->db->insert('wen_notify', $insdata);
                $res['new_message'] = $res['new_weibo_reply'] = $res['new_question'] = $res['new_answer'] = 0;
            } else {
                $res = $tmp[0];
            }
            $sql = "SELECT sum(new_answer) as num FROM wen_questions WHERE state=1 and fUid = {$this->uid} ";
            $tmp = $this->db->query($sql)->result_array();
            if (!empty ($tmp)) {
                $res['new_answer'] = $tmp[0]['num'] + 0;
            }
            $sql = "SELECT id,message FROM messages WHERE is_read=0 and uid = {$this->uid} and showType&1";
            $tmp = $this->db->query($sql)->result_array();

            $res['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);

        }

        $result['weiboCommentSum'] = $res['weiboCommentSum'];

        switch ($this->input->get('type')) {
            case 'message' :
                $result['data'] = $res['new_message'];
                break;
            case 'weibo_reply' :
                $result['data'] = $res['new_weibo_reply'];
                break;
            case 'question' :
                $result['data'] = $res['new_question'];
                break;
            case 'answer' :
                $result['data'] = $res['new_answer'];
                break;
        }
        if ($this->uid) {
            $result['ustate'] = '000';
        } else {
            $result['ustate'] = '001';
        }

        $result['push'] = array();

    }

	public function uState($param = '') {
		$res = array ();
		$result['state'] = '000';

		if ($this->notlogin) {
			if ($this->input->get('device_sn')) {
				$this->db->where('device_sn', $this->input->get('device_sn'));
				$this->db->where('state', 1);
				$this->db->select('new_answer');
				$this->db->order_by("id", "desc");
				$this->db->from('wen_questions');
				$tmp = $this->db->get()->result_array();
				$result['data'] = 0;
				foreach ($tmp as $row) {
					$result['data'] += $row['new_answer'];
				}
			}
			foreach ($_COOKIE as $k => $v) {
				setcookie($k, null, 0, "/", ".meilimei.com");
			}
			$result['push'] = array();
			$result['ustate'] = '001';
		} else {
			$result['uid'] = $this->uid;
			$result['message'] = array ();
			$mec = new Memcache();
			$mec->connect('127.0.0.1', 11211);
			if (!($res = $mec->get('state' . $this->uid)) OR !isset ($res['new_answer'])) {
				$tmp = $this->db->get_where('wen_notify', array (
					'user_id' => $this->uid
				), 1)->result_array();
				if (empty ($tmp)) {
					$insdata = array (
						'user_id' => $this->uid
					);
					$this->db->insert('wen_notify', $insdata);
					$res['new_message'] = $res['new_weibo_reply'] = $res['new_question'] = $res['new_answer'] = 0;
				} else {
					$res = $tmp[0];
				}
				$sql = "SELECT sum(new_answer) as num FROM wen_questions WHERE state=1 and fUid = {$this->uid} ";
				$tmp = $this->db->query($sql)->result_array();
				if (!empty ($tmp)) {
					$res['new_answer'] = $tmp[0]['num'] + 0;
				}
				$sql = "SELECT id,message FROM messages WHERE is_read=0 and uid = {$this->uid} and showType&1";
				$tmp = $this->db->query($sql)->result_array();
				//  $res['new_answer'] = $this->common->newansum($this->uid);
				$res['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
				$mec->set('state' . $this->uid, $res, 0, 600);
			}

			$result['weiboCommentSum'] = $res['weiboCommentSum'];

			switch ($this->input->get('type')) {
				case 'message' :
					$result['data'] = $res['new_message'];
					break;
				case 'weibo_reply' :
					$result['data'] = $res['new_weibo_reply'];
					break;
				case 'question' :
					$result['data'] = $res['new_question'];
					break;
				case 'answer' :
					$result['data'] = $res['new_answer'];
					break;
			}
			if ($this->uid) {
				$result['ustate'] = '000';
			} else {
				$result['ustate'] = '001';
			}
			if($result['push'] = $mec->get('push_' . $this->uid)){
			}else{
				$result['push'] = array();
			}
			$mec->close();
		}

		echo json_encode(array_merge($result, $res));
	}
    private function ckCoupon($mobile,$userid){

        if(!empty($mobile)) {
            $csql = "select * from coupons_sn where mobile = $mobile and states = 'N'";
            $crs = $this->eventDB->query($csql)->result_array();

            if ($crs) {
                foreach ($crs as $v) {
                    $usql = "update coupon_card set useid = $userid where sn ={$v['sn']} and batch = '{$v[batch]}'";
                    $urs = $this->db->query($usql);
                    if ($urs) {
                        $ucsql = "update coupons_sn set states = 'Y' where sn = {$v['sn']} and batch = '{$v[batch]}'";
                        $this->eventDB->query($ucsql);
                    }
                }
            }
        }
    }

	function dealState($param = '') {
		$result['state'] = '000';

		if ($this->notlogin) {
			$result['ustate'] = '001';
		} else {
			$mec = new Memcache();
			$mec->connect('127.0.0.1', 11211);
			$mec_state = false;
			if (($res = $mec->get('state' . $this->uid)) and !empty ($res)) {
				$mec_state = true;
			}

			$str = "UPDATE `wen_notify` SET ";
			$state = true;
			if (isset ($_POST['new_message'])) {
				$str .= '`new_message`=`new_message`-' . $this->input->post('new_message') . ',';
				$state = false;
				$mec_state && $res['new_message'] -= $this->input->post('new_message');
			}
			if (isset ($_POST['new_question'])) {
				$str .= 'new_question=new_question-' . $this->input->post('new_question') . ',';
				$state = false;
				$mec_state && $res['new_question'] -= $this->input->post('new_question');
			}
			if (isset ($_POST['new_answer'])) {
				$str .= 'new_answer=new_answer-' . $this->input->post('new_answer') . ',';
				$state = false;
				$mec_state && $res['new_answer'] -= $this->input->post('new_answer');
			}
			if ($state) {
				$result['state'] = '012';
			} else {
				$str = substr($str, 0, strlen($str) - 1);
				$str .= " WHERE user_id = " . $this->uid;
				$result['updatestate'] = '000';
				$this->db->query($str);
				$tmp = $this->db->query("SELECT new_answer FROM wen_notify WHERE uid = {$this->uid} ORDER BY uid DESC ")->result_array();
				if ($tmp[0]['new_answer'] > 65500) {
					$data = array (
						'new_answer' => 0
					);
					$this->db->where('uid', $this->uid);
					$this->db->update('wen_notify', $data);
				}
			}
			if ($mec_state) {
				$mec->set('state' . $this->uid, $res, 0, 600);
			} else {
				$mec->set('state' . $this->uid, array (), 0, 600);
			}

			$mec->close();
		}

		echo json_encode($result);
	}

    private function getMessagesNotReadCount($uid = 0){

        if($uid <=0)
            return 0;
        $topicCommentMyNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and touid={$uid}")->num_rows();
        //$topicMyCommentNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and fuid={$this->uid}")->num_rows();
        $diaryCommentMyNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and touid={$uid}")->num_rows();
        //$diaryMyCommentNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and fromuid={$uid}")->num_rows();
        $commnetTotal = $topicCommentMyNotReadCount + $diaryCommentMyNotReadCount;
        $zanMyNotReadCount = $this->db->query("select *from wen_zan where (type='diary' or type='topic') and is_read = 0 and touid={$uid}")->num_rows();
        //$myZanNotReadCount =$this->db->query("select *from wen_zan where is_read = 0 and uid={$this->uid}")->num_rows();
        $zanNotReadTotal = $zanMyNotReadCount;// + $myZanNotReadCount;
        $zixunNotReadCount =$this->db->query("select *from wen_questions left join wen_answer ON wen_questions.id=wen_answer.qid where wen_answer.new_comment = 1 and wen_questions.fUid={$uid}")->num_rows();
        $messageNotReadCount =  $zixunNotReadCount + $zanNotReadTotal + $commnetTotal;
        return $messageNotReadCount;
    }
    /**
     * @param string $params
     */
    public function getSettingInfo($params = ''){

        $result['state'] = '000';
        $result['data']  = array();

        if($this->uid){
            $this->db->select('jifen');
            $this->db->where('id',$this->uid);
            $result['data'] = $this->db->get('users')->result_array();

            $topicCommentMyNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and touid={$this->uid}")->num_rows();
            //$topicMyCommentNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and fuid={$this->uid}")->num_rows();
            $diaryCommentMyNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and touid={$this->uid}")->num_rows();
            //$diaryMyCommentNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and fromuid={$this->uid}")->num_rows();
            $commnetTotal = $topicCommentMyNotReadCount + $diaryCommentMyNotReadCount;
            $zanMyNotReadCount = $this->db->query("select *from wen_zan where (type='diary' or type='topic') and  is_read = 0 and touid={$this->uid}")->num_rows();
            //$myZanNotReadCount =$this->db->query("select *from wen_zan where is_read = 0 and uid={$this->uid}")->num_rows();
            $zanNotReadTotal = $zanMyNotReadCount;// + $myZanNotReadCount;
            $zixunNotReadCount =$this->db->query("select *from wen_questions left join wen_answer ON wen_questions.id=wen_answer.qid where wen_answer.new_comment = 1 and wen_questions.fUid={$this->uid}")->num_rows();
            $messageNotReadCount =  $zixunNotReadCount + $zanNotReadTotal + $commnetTotal;

            $zixunNotReviewCount =$this->db->query("select *from wen_questions left join reviews ON wen_questions.id=reviews.qid where reviews.userto={$this->uid}")->num_rows();

            $result['data']= array('messageNotReadCount'=>$messageNotReadCount,
                //'topicCommentMyNotReadCount'=>$topicCommentMyNotReadCount,
                //'topicMyCommentNotReadCount'=>$topicMyCommentNotReadCount,
                //'diaryCommentMyNotReadCount'=>$diaryCommentMyNotReadCount,
                //'diaryMyCommentNotReadCount'=>$diaryMyCommentNotReadCount,
                'commnetTotal'=>$commnetTotal,
		'num'=>$commnetTotal,
                //'myZanNotReadCount'=>$myZanNotReadCount,
                //'zanMyNotReadCount'=>$zanMyNotReadCount,
                'zanNotReadTotal'=>$zanNotReadTotal,
                'zixunNotReadCount'=>$zixunNotReadCount,
                'zixunNotReviewCount'=>$zixunNotReviewCount,
                'jifen'=>isset($result['data'][0]['jifen']) && is_numeric($result['data'][0]['jifen'])?$result['data'][0]['jifen']:0);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }

        //echo $this->remote->thumb(58609);
        echo json_encode($result);
    }

    /**
     * 申请为达人
     */
    function applyForDaren($param = ''){
        $result['state'] = '000';
        $result['data']  = array();

        if($this->uid){
            $this->db->where('id',$this->uid);
            $this->db->where('daren',1);
            $num = $this->db->get('users')->num_rows();
            if($num < 1) {
                $this->db->where('id', $this->uid);
                $this->db->update('users', array('daren' => 2));
                $result['notice'] = '已提交申请为达人！';
            }else{
                $result['state'] = '118';
                $result['notice'] = '该用户已申请！';
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }

        echo json_encode($result);
    }

    /**
     * @param string $param
     * 达人帮列表
     */
    function DarenRanking($param = ''){
        $result['state'] = '000';
        $result['data']  = array();

        $rs = $this->db->query('select id,username, alias, daren From users where users.daren=1 order by (select count(*) from wen_weibo where users.id=wen_weibo.uid) desc limit 5')->result_array();
        $daren = array();
        if(!empty($rs)){
            foreach($rs as $item){
                $item['alias'] = $item['alias'] ? $item['alias']:$item['username'];
                if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                    $item['alias'] = substr($item['alias'],0,4).'****';
                }

                if($this->isstate(8,$item['id'])){
                    $item['follow'] = 1;
                }else{
                    $item['follow'] = 0;
                }
                unset($item['username']);
                $item['thumb'] = $this->profilepic($item['id'],2);
                $daren[] = $item;
            }
            $result['data'] = $daren;
        }else{
            $result['state'] = '112';
            $result['notice'] = '没有数据！';
        }
        echo json_encode($result);
    }

    function DarenDetail($param = ''){
        $result['state'] = '000';
        $result['data']  = array();

        $rs = $this->db->query('select id,username, alias, daren, jifen From users where users.daren=1 order by (select count(*) from wen_weibo where users.id=wen_weibo.uid) desc limit 20')->result_array();
        $daren = array();
        if(!empty($rs)){
            foreach($rs as $item){
                $item['alias'] = $item['alias'] ? $item['alias']:$item['username'];
                if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                    $item['alias'] = substr($item['alias'],0,4).'****';
                }
                $item['city'] = isset($item['city'])?$item['city']:'';
                if(isset($rs[0]['age'])){
                    $item['age'] = $this->getAge($item['age']);
                }else{
                    $item['age'] = '';
                }
                $item['sex'] = 1;
                unset($item['username']);
                $this->db->where('uid',$item['id']);
                $this->db->order_by('created_at desc');
                $tmp = $this->db->get('note')->result_array();
                if(!empty($tmp)){
                    $ii = 0;
                    foreach($tmp as $i){
                        if(empty($i['imgfile'])) {
                            $item['note'][$ii]['img'] = $this->remote->getLocalImage($i['imgurl']);
                        }else{
                            $item['note'][$ii]['img'] = $this->remote->getQiniuImage($i['imgfile']);
                        }
                        $item['note'][$ii]['nid'] = $i['nid'];
                        $item['note'][$ii]['uid'] = $i['uid'];
                        $item['note'][$ii]['ncid'] = $i['ncid'];
                        $ii++;

                    }
                }else{
                    $item['note'] = array();
                }
                $item['level'] = $this->getLevel($item['jifen']);
                $item['thumb'] = $this->profilepic($item['id'],2);
                if($this->isstate(8,$item['id'])){
                    $item['follow'] = 1;
                }else{
                    $item['follow'] = 0;
                }
                $item['fans'] = $this->getfans($item['id']);
                $daren[] = $item;
            }
            $result['data'] = $daren;
        }else{
            $result['state'] = '112';
            $result['notice'] = '没有数据！';
        }
        echo json_encode($result);
    }


    private function getfans($uid,$type=0, $page=1){

        if ($uid) {
            $page = intval($page - 1);
            $start = $page * 9;
            $this->db->select('wen_follow.fid as fid,wen_follow.uid as uid');
            $this->db->from('wen_follow');
            if($type == 1) {
                $this->db->where('wen_follow.fid', $uid);
            }else{
                $this->db->where('wen_follow.uid', $uid);
            }
            $this->db->where('wen_follow.type', 8);
            $this->db->limit(10, $start);
            $tmp = $this->db->get()->result_array();

            $result =  array();
            foreach($tmp as $r){
                $r['thumb'] = $this->profilepic($r['fid'],0);

                $this->db->select('users.alias as uname, users.username as username,users.id as uid, users.jifen as jifen, users.city, users.age');
                $this->db->from('wen_follow');
                if($type == 1) {
                    $this->db->where('wen_follow.uid', $r['uid']);
                    $this->db->join('users', 'users.id = wen_follow.uid');
                }else{
                    $this->db->where('wen_follow.fid', $r['fid']);
                    $this->db->join('users', 'users.id = wen_follow.fid');
                }
                $this->db->limit(1);

                $retmp = $this->db->get()->result_array();
                if($type == 1) {
                    $r['thumb'] = $this->profilepic($r['uid'], 0);
                }else {
                    $r['thumb'] = $this->profilepic($r['fid'], 0);
                }
                if(!empty($retmp)){

                    $rs['daren'] = 1;
                    //$rs['state'] = $this->isstate(8,$retmp[0]['uid']);
                    //$rs['level'] = $this->isLevel($retmp[0]['jifen']);
                    unset($retmp[0]['jifen']);
                    $rs['thumb'] = $r['thumb'];
                    $this->db->where('uid',$retmp[0]['uid']);
                    $this->db->order_by('newtime','desc');
                    $res = $this->db->get('wen_weibo')->result_array();
                    if(!empty($res) && isset($res[0]['message'])) {
                        $rs['message'] = $res[0]['message'];
                    }else{
                        $rs['message'] = '';
                    }
                    $rs['alias'] = $retmp[0]['alias'] ? $retmp[0]['alias']:$retmp[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$rs['alias'] )){
                        $rs['alias']  = substr($rs['alias'] ,0,4).'****';
                    }
                    $rs['uname'] =  $rs['alias'];
                    unset($rs['alias']);
                    $rs['uid'] = $retmp[0]['uid'];
                    $result[] = $rs;

                }
            }
            return $result;
        }
        return ;
    }

    private function isstate($type = 8, $fid=0) {



        if ($this->uid) {

            if ($followuser = $fid) {
                $result['follow'] = '0';
                $condition = array (
                    'uid' => $followuser,
                    'fid' => $this->uid,
                    'type'=> $type
                );

                $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
                if ($tmp > 0) {
                    return 1;
                }else{
                    return 0;
                }

            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 0;
    }
	/**  我的咨询
	 * @param string $param
	 */
	function myQuestion($param = '') {
		$result['state'] = '000';

		if ($this->uid && $this->input->get('device_sn')) {
			$this->db->where('device_sn', $this->input->get('device_sn'));
		}
		elseif (!$this->notlogin) {
			$this->db->where('fUid', $this->uid);
		} else {
			$result['ustate'] = '001';
			$result['state'] = '012';
			echo json_encode($result);
		}
		if (intval($this->input->get('state'))) {
			$this->db->where('state', intval($this->input->get('state')));
		}
		$this->db->select('title, id,cdate,state,new_answer');
		$this->db->order_by("id", "desc");
		$this->db->from('wen_questions');
		$tmp = $this->db->get()->result_array();

		foreach ($tmp as $row) {
			$row['cdate'] = date('Y-m-d', $row['cdate']);
			$result['data'][] = $row;
		}

		echo json_encode($result);
	}

	function acQuestion($param) {
		$result['state'] = '000';
		if ($this->notlogin) {
			$condition = array (
				"device_sn" => $this->input->get('device_sn'
			));
		} else {
			$condition = array (
				"uid" => $this->uid
			);
		}
		if ($this->input->get('state') == 2 || $this->input->get('state') == 10) {
			$updateData = array (
				"state" => intval($this->input->get('state'
			)));
			$result = $this->common->updateTableData('wen_questions', '', $condition, $updateData);
		}
		echo json_encode($result);
	}

	function resetPassword($param) {
		$result['state'] = '000';

		if (!$this->uid) {
			if (($code = trim($this->input->post('code'))) && ($phnum = $this->input->post('phone')) && ($newpass = $this->input->post('newpass'))) {
				$result['debug'] = $this->session->userdata('veryCode');
                if ($this->session->userdata('veryCode') != $code) {

					$result['state'] = '0160';
                    $result['notice'] = '验证码错误';
				} else {
					if ($this->wen_auth->change_passwordbynologin($newpass, $phnum)) {
						$result['updatestate'] = '000';
					} else {
						$result['updatestate'] = '001';
					}
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['ustate'] = '000';
            $result['updatestate'] = '003';
		}

		echo json_encode($result);
	}

	function resetPasswordByPhone($param) {
		$result['state'] = '000';
		/*$result['post'] = $this->input->post();
		  $result['get'] = $param;
		  $result['res'] = $this->auth->checktoken($param);
		  $result['notlogin'] = $this->notlogin;
		  $result['checktoken'] = $this->auth->checktoken($param);
		  $result['code'] = $this->input->post('code');
		  $result['phone'] = $this->input->post('phone');
		  $result['newpass'] = $this->input->post('newpass');
		  $result['detail'] = $this->wen_auth->change_password('', $newpass,false);

		  $result['phone'] = $this->input->post('phone');
		  $result['newpass'] = $this->input->post('newpass');
		  $result['detail'] = $this->wen_auth->change_passwordbynologin($newpass,$this->input->post('phone'));*/
		if ($this->notlogin) {
			if (($code = $this->input->post('code')) && ($phnum = $this->input->post('phone')) && ($newpass = $this->input->post('newpass'))) {
				if ($this->session->userdata('veryCode') != $code) {
					$result['state'] = '016';
					exit;
				}

				if ($this->wen_auth->change_passwordbynologin($newpass, $this->input->post('phone'))) {
					$result['updatestate'] = '000';
				} else {
					$result['updatestate'] = '001';
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['ustate'] = '000';
		}
		echo json_encode($result);
	}
	/*
	* Function: _encode
	* Modified for WEN_Auth
	* Original Author: wenran 1.1
	*/
	private function _encode($password) {
		$majorsalt = $this->config->item('WEN_salt');
		$_pass = str_split($password);
		foreach ($_pass as $_hashpass) {
			$majorsalt .= md5($_hashpass);
		}
		return md5($majorsalt);
	}

    public function updatePhone($param = ''){

        $result['state'] = '000';
        $result['notice'] = '绑定成功！';
        $result['data'] = '';
        $phone = $this->input->post('phone');
        $code = $this->input->post('code');
        if($this->uid){
            /*if ($this->session->userdata('veryCode') != $code) {
                $result['state'] = '016';
                $result['notice'] = '验证码错误'.$code.'===='.$this->session->userdata('veryCode').'=='.$this->session->userdata('veryCodeTime');
                echo json_encode($result);
                exit;
            }*/
            if(!$this->_check_phone_no($phone)) {
                $this->db->where('id', $this->uid);
                $this->db->update('users', array('phone' => $phone));
                $this->ckCoupon($phone, $this->uid);
            }else{
                $result['state'] = '014';
                $result['notice'] = '手机号重复！';
            }
        }else {
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
        echo json_encode($result);
    }

    private function isphone($phone,$uid){

        /*$this->db->where('phone', $phone);
        if($uid){
            $this->db->where('id != ', $uid);
        }*/
        $num = $this->db->query("select id from users where id !={$uid} and phone={$phone}")->num_rows();

        if(intval($num) >= 1){
            return 1;
        }else{
            return 0;
        }
    }
	function changePassword($param) {
		$result['state'] = '000';
		/*$result['post'] = $this->input->post();
		  $result['get'] = $param;
		  $result['res'] = $this->auth->checktoken($param);*/

		if ($this->uid) {
			$val = $this->form_validation;
			// Set form validation
			$val->set_rules('old_password', 'Old Password', 'trim|required|xss_clean|min_length[' . $this->min_password . ']|max_length[' . $this->max_password . ']');
			$val->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->min_password . ']|max_length[' . $this->max_password . ']|matches[confirm_new_password]');
			$val->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean');

			// Validate rules and change password
			if ($val->run() AND $this->wen_auth->change_password($val->set_value('old_password'), $val->set_value('new_password'))) {
				$this->load->library('tehui');
				$new_pass = crypt($this->_encode($val->set_value('new_password')));
				$tehui['password'] = $new_pass;
				$this->tehui->updateUser($this->uid, $tehui);
				$result['updatestate'] = '000';
			} else {
				$result['updatestate'] = '001';
                $result['notice'] = '修改失败！';
			}
		} else {
			$result['ustate'] = '002';
            $result['notice'] = '用户未登录！';
		}

		echo json_encode($result);
	}

    /**
     * @param string useranme
     * @param string email
     * @param string phone
     * @param string alias
     * @param string birthday
     * @param string city
     * @param int sex
     * @param file attachPic
     * @param string $param
     */
	function updateuinfo($param = '') {
		$result['state'] = '000';

		$result['notice'] = '更新成功！';

		if ($this->uid) {
			$username = $this->input->post('username');
            $data['age'] = $this->input->post('age');
            $result['debugusername'] = $username."---xxxx";
			if ($username) {
				$this->db->where('username', $username);
				if ($this->input->post('email')) {
					if(preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $this->input->post('email'))){
						$this->db->or_where('email', $this->input->post('email'));
						$data['email'] = $this->input->post('email');
					}else{
                       $result['state'] = '401';
					   $result['notice'] = '邮箱不符合！';
					   echo json_encode($result);
					   exit;
					}
				}
				if ($this->input->post('phone')) {
					if(preg_match("/^1[0-9]{10}$/",$this->input->post('phone'))){
						$this->db->or_where('phone', $this->input->post('phone'));
                        $sss = $this->isphone($this->input->post('phone'), $this->uid);
                        if(!$this->isphone($this->input->post('phone'), $this->uid)) {
                            $this->ckCoupon($this->input->post('phone'), $this->uid);
                            $data['phone'] = $this->input->post('phone');
                        }else{
                            $result['state'] = '014';
                            $result['notice'] = '手机号重复！';
                        }

					}else{
                       $result['state'] = '402';
					   $result['notice'] = '手机不符合！';
					   echo json_encode($result);
					   exit;
					}

				}
				if ($this->_check_user_alias($username, $result)) {
					$result['state'] = '403';
					$result['notice'] = '昵称不符合或已被使用！';
					echo json_encode($result);
					exit;
				}
				//update tehui user
				$tehui = array ();
				$tehui['username'] = $username;
				$this->tehui->updateUser($this->uid, $tehui);
                $this->db->where('id',$this->uid);
				$query = $this->db->get('users')->result_array();
                $result['debug'] = $query;
				if ($query[0]['id'] == $this->uid || count($query) == 0) {
					//$data['username'] = $username;
					$data['alias'] = $this->input->post('alias') ? $this->input->post('alias') : $username;
                    $result['debug'] = $sss."xxxx";
					$uid = $this->uid;
					$this->db->where('id', $uid);
					$this->db->update('users', $data);

					$data = array (
						'birthday' => strtotime($this->input->post('birthday'
					)), 'city' => $this->input->post('city'), 'sex' => $this->input->post('sex'));
					if ($this->input->post('sex')) {
						$data['sex'] = intval($this->input->post('sex'));
					}
					$this->db->where('user_id', $uid);
					$this->db->update('user_profile', $data);

					if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {

						$f = $this->thumb($uid, $_FILES['attachPic']['tmp_name']);
                        $datainfo = array();
                        $datainfo['icon'] = 0;
                        $this->db->where('id',$uid);
                        $this->db->update('users',$datainfo);
                        //$result['debug'] = $f."x";
                        $result['file'] = $this->remote->thumb($uid);
					}
				} else {
					$result['notice'] = '更新失败！';
					$result['ustate'] = '112';
				}
			} else {
				$result['notice'] = '更新失败！';
				$result['username'] = $username;
				$result['state'] = '012';
			}
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '002';
		}
		echo json_encode($result);
	}
    function updateUserInfo($param = '') {
        $result['state'] = '000';

        $result['notice'] = '更新成功！';

        if ($this->uid) {

            $this->db->where('id',$this->uid);
            $rs = $this->db->get('users')->result_array();

            $data['alias'] = $this->input->post('alias') ? $this->input->post('alias'):'';

            $uid = $this->uid;
            $this->db->where('id', $uid);
            $this->db->update('users', $data);

            $data = array( 'city' => $this->input->post('city'), 'sex' => $this->input->post('sex'));

            if ($this->input->post('sex')) {
                $data['sex'] = intval($this->input->post('sex'));
            }
            $this->db->where('user_id', $uid);
            $this->db->update('user_profile', $data);

            if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {

                $f = $this->thumb($uid, $_FILES['attachPic']['tmp_name']);
                $datainfo = array();
                $datainfo['icon'] = 0;
                $datainfo['age'] = $this->input->post('age');
                $this->db->where('id', $uid);
                $this->db->update('users', $datainfo);
                $result['file'] = $this->remote->thumb($uid);
            }
            if(!empty($rs[0]['age'])){
                $this->db->where('id',$uid);
                $this->db->update('users',array('jifen'=>"600"));
                $result['score'] = 600;
            }
        }else{
            $result['notice'] = '登录失败！';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
	function sendsms($param = '') {
		$result['state'] = '000';
		if ($phone = $this->input->get('phone')) {
			$sn = rand(10000, 99999);
			$result['sn'] = $sn;
			$result['notice'] = '已发送';
			if (true || $this->session->userdata('veryCodeTime') > time()) {
				$code = rand(562312, 986985);
				$this->session->set_userdata('veryCode', $code);
				if ($this->input->get('type')) {
					if (!$this->_check_phone_no($phone)) {
						$message = '[编号:' . $sn . ']验证码：' . $code . ' 退订回复0000';
						$time = time() + 120;
						$result['vcode'] = $code;
						$this->session->set_userdata('veryCodeTime', $time);
						$this->sms->sendSMS(array (
							"{$phone}"
						), $message);
					} else {
						$result['notice'] = '手机号不正确';
						$result['state'] = '067';
					}
				} else {

                    $result['debug'] = $this->_check_phone_no($phone)."==".$phone.'==='.$this->db->last_query();;
					if ($this->_check_phone_no($phone)) {
						$message = '手机验证码是：' . $code . '退订回复0000 ';
						$time = time() + 120;
						$result['vcode'] = $code;
						$this->session->set_userdata('veryCodeTime', $time);
						$this->sms->sendSMS(array (
							"{$phone}"
						), $message);
					} else {
						$result['notice'] = '手机号已注册';
						$result['state'] = '066';
					}
				}

			} else {
				$result['notice'] = '短信验证发送间隔为一分钟';
				$result['state'] = '022';
			}
		} else {
			$result['notice'] = '信息不完整';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}

	function sendresetpwdsms($param = '') {
		$result['state'] = '000';

		if ($phone = $this->input->get('phone')) {
			if ($this->session->userdata('veryCodeTime') > time() || !$this->session->userdata('veryCodeTime')) {
				$code = rand(562312, 986985);
				$this->session->set_userdata('veryCode', $code);

				$message = '您的手机验证码是：' . $code . '退订回复0000 ';
				$time = time() + 120;
				$this->session->set_userdata('veryCodeTime', $time);
				$this->sms->sendSMS(array (
					"{$phone}"
				), $message);

			} else {
				$result['state'] = '022';
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}

	function getScore($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->get('uid')) {
			$condition = array (
				'id' => $uid
			);
			$tmp = $this->common->getTableData('users', $condition, 'voteNum,grade,sysgrade,sysvotenum')->result_array();
			if (empty ($tmp)) {
				$result['state'] = '400';
			} else {
				$result['votes'] = $tmp[0]['sysvotenum'] > 0 ? $tmp[0]['sysvotenum'] : $tmp[0]['voteNum'];
				$result['score'] = $tmp[0]['sysgrade'] > 0 ? $tmp[0]['sysgrade'] : $tmp[0]['grade'];
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}

	function setScore($param = '', $showres = true) {
		$result['state'] = '000';

		if ($uid = $this->input->post('uid')) {
			$condition = array (
				'id' => $uid
			);
			$tmp = $this->common->getTableData('users', $condition, 'voteNum,grade')->result_array();
			if (empty ($tmp)) {
				$result['state'] = '400';
			} else {
				$score = ($this->input->post('score') * 10 + $tmp[0]['grade'] * $tmp[0]['voteNum']) / ($tmp[0]['voteNum'] + 1);
				$data['grade'] = $score;
				$data['voteNum'] = $tmp[0]['voteNum'] + 1;
				$this->common->updateTableData('users', $uid, '', $data);
				$result['updatestate'] = '000';
				$result['votes'] = $tmp[0]['voteNum'];
				$result['score'] = $tmp[0]['grade'] / 10;

			}

		} else {
			$result['state'] = '012';
		}

		if ($showres)
			echo json_encode($result);
	}

    function getScoreList($params = ''){
        $result['state'] = '000';
        $result['data'] = array();
        //$this->uid = 233373;
        if($this->uid){
            $this->db->where('id',$this->uid);
            $rs = $this->db->get('users')->result_array();

            //$result['data']['price'] = intval($rs[0]['jifen'])/100;
            $tmp = $this->Score_model->getScoreList($this->uid);
            if(!empty($tmp)){
                foreach($tmp as $item){
                    $item['created_at'] = date('Y-m-d',$item['created_at']);
                    $result['data'][strtotime($item['created_at'])][] = $item;
                }
            }
        }else{
            $result['state'] = '012';
        }
        $result['data']['score'] = intval($rs[0]['jifen']);
        echo json_encode($result);
    }

	function review($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->post('uid')) {
			$data['userto'] = $uid;
			$data['userby'] = $this->uid;
			$data['type'] = 1;
			$data['qid'] = $this->input->post('qid');
			$data['score'] = $this->input->post('score') * 10;
			$data['review'] = $this->input->post('comment');
			$data['showtype'] = 3;
			$data['created'] = time();
			if ($this->db->query("SELECT reviews.id FROM reviews WHERE reviews.qid={$data['qid']} AND reviews.userto = {$uid}")->num_rows()) {
				$result['postState'] = '001';
			} else {
				$result['postState'] = '000';
				$this->common->insertData('reviews', $data);
				$this->setScore($param, false);
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}

	function getReview($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->get('uid')) {
			$offset = ($this->input->get('page') - 1) * 10;
			$result['num_rows'] = $this->db->query("SELECT reviews.id FROM reviews WHERE reviews.userto = {$uid}")->num_rows();
			$tmp = $this->db->query("SELECT reviews.review,reviews.score,reviews.created,user_profile.Lname,user_profile.Fname,users.email,users.phone FROM reviews LEFT JOIN users ON users.id=reviews.userby LEFT JOIN user_profile ON user_profile.user_id = reviews.userby WHERE reviews.userto = {$uid} and type=1 order by reviews.created desc LIMIT {$offset},10")->result_array();
			foreach ($tmp as $row) {
				$row['created'] = date('Y-m-d', $row['created']);
				$row['score'] = intval($row['score'] / 10);
				$row['showname'] = $row['phone'] != '' ? substr($row['phone'], 0, 3) . '***' : substr($row['email'], 0, 3) . '***';
				unset ($row['phone']);
				unset ($row['email']);
				$result['data'][] = $row;
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}

	//check review state
	public function checkReview($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($uid = $this->uid) {

			$sql = " SELECT users.alias,users.id as uid,w.id as qid ";
			$sql .= " FROM wen_questions as w ";
			$sql .= " LEFT JOIN wen_answer as ans ON ans.qid = w.id ";
			$sql .= " LEFT JOIN users ON ans.uid = users.id ";
			$sql .= " where w.fuid={$uid} AND ans.uid NOT IN ";
			$sql .= " (SELECT reviews.userto FROM reviews WHERE reviews.qid=ans.qid) ORDER BY w.id DESC LIMIT 1 ";
			$tmp = $this->db->query($sql)->result_array();
			$result['data'] = array ();
			if (!empty ($tmp)) {
				$result['data'] = $tmp[0];
			} else {
				$result['state'] = '400';
			}
		} else {
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}

	//check is review question to doctor
	function isReview($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->get('uid')) {
			$result['review'] = '1';
			$qid = $this->input->get('qid');
			if ($this->db->query("SELECT reviews.id FROM reviews WHERE reviews.qid={$qid} AND reviews.userto = {$uid}")->num_rows()) {
				$result['review'] = '0';
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	//add favorite info
	function addfav($param = '') {
		$result['state'] = '000';

		if ($contentid = $this->input->get('contentid')) {
			if ($this->uid) {
				$data = array (
					'type' => $this->input->get('type'
				), 'contentid' => $contentid, 'uid' => $this->uid, 'cTime' => time());
				$this->db->where('contentid', $contentid);
				$this->db->where('uid', $this->uid);
				$this->db->where('type', $this->input->get('type'));
				$tmp = $this->db->count_all_results('wen_favrite');
				if ($tmp) {
					$result['notice'] = '已经收藏过';
					$result['state'] = '011';
					echo json_encode($result);
					exit;
				}
				$result['notice'] = '成功收藏！';
				
				$this->db->insert('wen_favrite', $data);
				$result['insertState'] = '000';
			} else {
				$result['notice'] = '账户未登入！';
				$result['uState'] = '001';
			}
		} else {
			$result['notice'] = '数据不齐全！';
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	public function fav($param = ''){
		$contentid = $this->input->get('contentid');
		$type = $this->input->get('type');
		if($this->isfav($contentid,$type)){
			$this->addfav();
		}else{
			$this->unfav();
		}
	}

	function unfav($param = '') {
		$result['state'] = '000';

		if ($contentid = $this->input->get('contentid')) {
			$condition = array (
				'type' => $this->input->get('type'
			), 'contentid' => $contentid, 'uid' => $this->uid);
			$this->db->delete('wen_favrite', $condition);
			
			$result['updateState'] = '000';
			$result['notice'] = '成功取消收藏！';
		} else {
			$result['state'] = '012';
			$result['notice'] = '参数不全！';
		}
		echo json_encode($result);
	}

	private function isflag($contentid,$type){

		if ($contentid and $this->uid) {
			$condition = array (
				'type' => $type, 'uid' => $this->uid, 'contentid' => $contentid);

			if ($this->db->get_where('wen_favrite', $condition)->num_rows() > 0) {
				$result['isfav'] = '0';
			} else {
				$result['isfav'] = '1';
			};
		} else {
			$result['isfav'] = '0';
		}

		return $result['isfav'];
	}

	function isfav($param = '') {
		$result['state'] = '000';

		if (($contentid = $this->input->get('contentid')) and $this->uid) {
			$condition = array (
				'type' => $this->input->get('type'
			), 'uid' => $this->uid, 'contentid' => $contentid);

			if ($this->db->get_where('wen_favrite', $condition)->num_rows() > 0) {
				$result['isfav'] = '000';
			} else {
				$result['isfav'] = '001';
			};
		} else {
			$result['ustate'] = '001';
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	//is repeate
	function isRepeat($param = '') {
		$result['state'] = '000';

		if ($uname = $this->input->get('uname')) {
			$this->db->where('username', $uname);
			$this->db->or_where('alias', $uname);
			$tmp = $this->db->get('users')->result_array();
			if (!empty ($tmp)) {
				$result['isRepeat'] = 1;
			} else {
				$result['isRepeat'] = 0;
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	//is complete alias?
	function comalias($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->get('uid')) {
			$condition = array (
				'id' => $uid
			);
			$tmp = $this->db->get_where('users', $condition)->result_array();
			if ($tmp[0]['alias'] == '' OR preg_match('/^\\d+$/', $tmp[0]['alias'])) {
				$result['alias'] = 0;
			} else {
				$result['alias'] = 1;
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	function favList($param = '') {
		$result['state'] = '000';

		if ($uid = $this->input->get('uid') OR ($uid = $this->uid)) {
			if ($this->input->get('type')) {
				$page = intval($this->input->get('page')) - 1;

				$offpage = $page * 10;
				$condition = "wen_favrite.uid = {$uid}  AND wen_favrite.type = '{$this->input->get('type')}'";
				switch ($this->input->get('type')) {
					case 'article' :
						$result['data'] = $this->db->query("SELECT wp_posts.post_title,wp_posts.post_date,wp_posts.ID FROM wen_favrite LEFT JOIN wp_posts ON wp_posts.ID = wen_favrite.contentid WHERE {$condition} ORDER BY wen_favrite.id DESC LIMIT $offpage,10")->result_array();

						break;
					case 'qus' :
						$result['data'] = $this->db->query("SELECT wen_questions.title,wen_questions.id,wen_questions.cdate FROM wen_favrite LEFT JOIN wen_questions ON wen_questions.id = wen_favrite.contentid WHERE {$condition} ORDER BY wen_favrite.id DESC LIMIT $offpage,10")->result_array();

						break;
					case 'topic' :
						$fields = 'users.phone,users.alias,wen_weibo.uid,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.isdel,wen_weibo.comments,wen_weibo.type_data,wen_weibo.favnum,wen_weibo.ctime,wen_weibo.ctime';
						$tmp = $this->db->query("SELECT {$fields} FROM wen_favrite  LEFT JOIN wen_weibo ON wen_weibo.weibo_id = wen_favrite.contentid LEFT JOIN users ON users.id = wen_weibo.uid WHERE {$condition} ORDER BY wen_favrite.id DESC LIMIT $offpage,10")->result_array();
						$result['data'] = array ();
						foreach ($tmp as $r) {
							$r['uname'] = $r['alias'] != '' ? $r['alias'] : substr($r['phone'], 0, 3) . '***';
							$r['thumb'] = $this->remote->thumb($r['fuid'], '50');
							$r['ctime'] = date('Y-m-d', $r['ctime']);
							$tdata = unserialize($r['type_data']);
							$r['title'] = $tdata['title'];
							if (isset ($r['pic'])) {
								$r['haspic'] = 1;
							} else {
								$r['haspic'] = 0;
							}
							unset ($r['type_data']);
							$result['data'][] = $r;
						}
						break;
				}

			}
		} else {
			$result['ustate'] = '001';
		}
		echo json_encode($result);
	}

	function bindUser($param = '') {
		$result['state'] = '000';

		if ($this->uid) {
			if ($this->input->post('device_sn')) {
				$udata['device_sn'] = $this->input->post('device_sn');
				$this->common->updateTableData('users', $this->uid, '', $udata);
				$result['ustate'] = '000';
				$result['updateState'] = '000';
				$qdata['fUid'] = $this->uid;
				$condition['device_sn'] = trim($this->input->post('device_sn'));
				$this->common->updateTableData('wen_questions', '', $condition, $qdata);
			}
		} else {
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}

	private function thumb($uid, $file) {
		if ($file != '') {
			$this->remote->uputhumb($file, $uid);
			return true;
		} else {
			return false;
		}
	}

	//profile pic
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

	private function getQstate($state = 0) {
		switch ($state) {
			case 1 :
				return '回答中';
				break;
			case 2 :
				return '关闭';
				break;
			case 4 :
				return '已过期';
				break;
			case 8 :
				return '已完结';
				break;
		}

	}

    private function getFollow($uid = 0){

        $type = 9;
        $result['data'] = array();

        $this->uid = $uid;

        if ($this->uid) {
            $this->db->where('fid',$this->uid);
            $this->db->where('type',$type);
            $this->db->select('uid');
            $res = $this->db->get('wen_follow')->result_array();
            $arr_uid =array();
            if(!empty($res)){

                foreach($res as $item){
                    $arr_uid[] = $item['uid'];
                }
            }
            $res_items = array();
            if(!empty($arr_uid)){
                $this->db->where_in('id',$arr_uid);
                $this->db->select('id,name,burl,colors,img_png as surl');
                $ires=$this->db->get('new_items')->result_array();

                if(!empty($ires)){
                    $this->db->where('uid',$this->uid);
                    $this->db->select('tag');
                    $q = $this->db->get('user_fav')->result_array();
                    $arr_user = array();

                    if(!empty($q)){
                        foreach($q as $qitem){
                            $arr_user[] = $qitem['tag'];
                        }
                    }

                    foreach($ires as $r){

                        if(in_array($r['name'],$arr_user)){
                            continue;
                        }
                        if(empty($r['name']) || is_null($r['name'])){
                            continue;
                        }
                        $r['burl'] = $this->remote->show($r['burl']);
                        $r['surl'] = $this->remote->show320($r['surl']);
                        $res_items[] = $r;
                    }

                }
            }
            $result['data'] = $res_items?$res_items:array();
        }
        return $result['data'];
    }

    function sendsmspy($param = '') {
        $result['state'] = '000';
        if ($phone = $this->input->POST('phone')) {
            $sn = rand(10000, 99999);

            $message = '您的手机验证码是：' . $this->input->POST('code') . '退订回复0000';
            $this->sms->sendSMS(array (
                "{$phone}"
            ), $message);
            $result['notice'] = '发送成功';
        } else {
            $result['notice'] = '信息不完整';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    function sendsmsorderpy($param = '') {
        $result['state'] = '000';
        if ($phone = $this->input->POST('phone')) {
            $sn = rand(10000, 99999);
            $message = '订单信息：' . $this->input->POST('message') . ' 退订回复0000';

            $this->sms->sendSMS(array (
                "{$phone}"
            ), $message);
            $result['notice'] = '发送成功';
        } else {
            $result['notice'] = '信息不完整';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    function getFav($param = ''){

        $result['state'] = '000';
        $uid = $this->input->get('uid');
        $result['data'] = array();
        if ($uid) {
            $arr_item = $this->Users_model->get_user_fav_by_uid($uid);
            if(!empty($arr_item)){
                foreach($arr_item as $item){
                    $result['data'][] = $item;
                }
            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }


    /**
     * 用户签到获积分
     * @author wanglulu add by 2015-06-16
     */
    public function userSignin() {

        if ( !$this->uid ) {
            returnJsonData('账户未登入', '401');
        }

        //加载用户模型
        $this->load->model('users_model', 'users');

        $args_array = array(
            'uid' => $this->uid,
            'sid' => 77
        );
        $result_array = $this->users->userSignin( $args_array );

        if ( $result_array['code'] != '200' ) {
            returnJsonData($result_array['message'], $result_array['code']);
        }

        returnJsonData('ok', '200', $result_array['data']);

    }



}
?>
