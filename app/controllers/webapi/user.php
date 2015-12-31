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
		$this->path = realpath(APPPATH . '../images');
		$this->load->library('wen_auth_event');
		$this->load->library('sms');
		$this->load->library('tehui');
		$this->load->model('Users_model');
		$this->load->model('auth');
		$this->load->model('Email_model');
		$this->load->helper('file');
		$this->load->model('remote');
		$this->load->library('alicache');
		$this->load->model('track');
	}


	//fast register
	private function freg($param) {
		$result['state'] = '000';

		if ($this->input->post() && $this->notlogin) {
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
					$this->wen_auth->login($username, $password, TRUE);
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
							), '【美丽神器】 代金券号：'.$code.' 退订回复TD');
						}
					}
					
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
	public function reg($param) {
//print_r($this->session->userdata('veryCode').'2222222222222222');exit;
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
				if ($code = $this->input->post('code')) {
				
					if ($this->alicache->get(md5($code)) != $code) {
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
						), "【美丽神器】感谢你注册美丽神器 APP,你的账户:{$phone},密码:{$password},请妥善保管" . '退订回复TD ');
					}
					//send tehui
					$i = 0;
					for($i = 0; $i<5; $i++){
						$code = $this->tehui->tehuiSend($this->wen_auth->get_user_id(),true);
						if($phnum and $code.$i){
							$this->sms->sendSMS(array (
								"{$phone}"
							), '【美丽神器】 代金券号：'.$code.' 退订回复TD');
						}
					}
					$result['uid'] = $this->wen_auth->get_user_id();
					$result['notice'] = '注册成功';
					$result['ustate'] = '000';
				} else {
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
	public function signin($param) {

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
					$newdata = array (
						'user' => $uid,
					'username' => "'" . $this->wen_auth->get_username() . "'", 'logged_in' => TRUE);
					$this->session->set_userdata($newdata);
					$this->session->set_uid($this->wen_auth->get_user_id());
					$tmp = $this->db->query("SELECT  users.ref_id FROM users WHERE users.id = {$uid} LIMIT 1")->result_array();
					if (strpos($tmp[0]['ref_id'], 'q_')) {
						$result['regtype'] = 'qq';
					}
					elseif (strpos($tmp[0]['ref_id'], 'eibo_')) {
						$result['regtype'] = 'weibo';
					} else {
						$result['regtype'] = '';
					}
					$result['username'] = $this->wen_auth->get_username();
					$result['type'] = $this->wen_auth->get_role_id();
					$result['uid'] = $uid;
					$result['expire'] = time()+86400*100;
					$result['sessionid'] = md5($this->appkey.str_replace(' ','',microtime()));
					$result['version'] = FACES_VERSION;
					$result['thumb'] = $this->profilepic($uid, 2);
					$result['items'] = $this->Users_model->get_user_fav_by_uid($uid);
					//	$result['ustate']= $this->wen_auth->get_user_id();
					//echo json_encode($result);//['ustate'];
					//exit();
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
				}
			} else {
				$result['state'] = '012';
			}
		}
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
					$result['ustate'] = '000';
					$result['version'] = FACES_VERSION;
					$result['username'] = $this->wen_auth->get_username();
					$result['type'] = $this->wen_auth->get_role_id();
					$result['uid'] = $this->wen_auth->get_user_id();
					$result['expire'] = time()+86400*100;
					$result['sessionid'] = md5($this->appkey.str_replace(' ','',microtime()));
					$this->db->query("update users set sessionid='".$result['sessionid'] ."',expire='".$result['expire']."' where id=".$this->wen_auth->get_user_id()." limit 1");
					$result['items'] = $this->Users_model->get_user_fav_by_uid($this->wen_auth->get_user_id());
					$result['thumb'] = $row->icon?$row->icon:$this->profilepic($result['uid'], 2);
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
	function getinfo($param) {
		$result['state'] = '000';

		$uid = intval($this->input->get('uid'));
		$uid == 0 && $uid = $this->uid;
		$tmp = $this->db->query("SELECT users.id,users.jifen,users.grade, users.alias,users.email,users.phone,users.created,user_profile.* FROM users LEFT JOIN user_profile ON user_profile.user_id = users.id WHERE users.id = {$uid} LIMIT 1")->result_array();
		$result['data'] = $tmp[0];
		if ($result['data']['birthday']) {
			$result['data']['birthday'] = date('Y-m-d', $result['data']['birthday']);
		}

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

		$result['data']['username'] = $result['data']['uname'];
		$result['data']['favrite'] = $this->countfavrite($uid);
		$result['data']['guangzhu'] = $this->countfollow($uid);
		$result['data']['fensi'] = $this->countffensi($uid);
		$result['data']['thumb'] = $this->profilepic($result['data']['id'], 2);
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
	function countQuestion($param) {

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
				return $state;
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

	function state($param) {
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
	public function is_log($param) {

		$result['ustate'] = '000';
		if ($this->notlogin) {
			$result['ustate'] = '001';
		}
		$result['state'] = '000';
		echo json_encode($result);
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

	/**  我的咨询
	 * @param string $param
	 */
	function myQuestion($param = '') {
		$result['state'] = '000';

		if ($this->notlogin && $this->input->get('device_sn')) {
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
		/*$result['post'] = $this->input->post();
		  $result['get'] = $param;
		  $result['res'] = $this->auth->checktoken($param);
		  $result['notlogin'] = $this->notlogin;
		  $result['checktoken'] = $this->auth->checktoken($param);
		  $result['code'] = $this->input->post('code');
		  $result['phone'] = $this->input->post('phone');
		  $result['newpass'] = $this->input->post('newpass');
		  $result['detail'] = $this->wen_auth->change_password('', $newpass,false);
		  */
		if ($this->notlogin) {
			if (($code = trim($this->input->post('code'))) && ($phnum = $this->input->post('phone')) && ($newpass = $this->input->post('newpass'))) {
				if ($this->alicache->get(md5($code)) != $code) {
					$result['state'] = '0160';
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

	function changePassword($param) {
		$result['state'] = '000';
		/*$result['post'] = $this->input->post();
		  $result['get'] = $param;
		  $result['res'] = $this->auth->checktoken($param);*/

		if (!$this->notlogin) {
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
			}
		} else {
			$result['ustate'] = '002';
		}

		echo json_encode($result);
	}

	function updateuinfo($param = '') {
		$result['state'] = '000';

		$result['notice'] = '更新成功！';
		if (!$this->notlogin) {
			$username = $this->input->post('username');
			if ($username) {
				$this->db->where('username', $username);
				if ($this->input->post('email')) {
					if(preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $this->input->post('email'))){
						$this->db->or_where('email', $this->input->post('email'));
						$data['email'] = $this->input->post('email');
					}else{
                       $result['state'] = '403';
					   $result['notice'] = '邮箱不符合！';
					   echo json_encode($result);
					   exit;
					}
				}
				if ($this->input->post('phone')) {
					if(preg_match("/^1[0-9]{10}$/",$this->input->post('phone'))){
						$this->db->or_where('phone', $this->input->post('phone'));
					    $data['phone'] = $this->input->post('phone');
					}else{
                       $result['state'] = '403';
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
				$query = $this->db->get('users')->result_array();
				if ($query[0]['id'] == $this->uid || count($query) == 0) {
					$data['username'] = $username;
					$data['alias'] = $this->input->post('alias') ? $this->input->post('alias') : $username;
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
						$this->thumb($uid, $_FILES['attachPic']['tmp_name']);
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

	function sendsms($param = '') {
		$result['state'] = '000';
		if ($phone = $this->input->get('phone')) {
			$sn = rand(10000, 99999);
			$result['sn'] = $sn;
			$result['notice'] = '已发送';
			if (true ||$this->alicache->get(md5("veryCodeTime".$code)) > time()) {
				$code = rand(562312, 986985);
				$this->alicache->set(md5($code),$code);
				if ($this->input->get('type')) {
					if (!$this->_check_phone_no($phone)) {
						$message = '【美丽神器】[编号:' . $sn . ']您的手机验证码是：' . $code . '退订回复TD';
						$time = time() + 120;
						$result['vcode'] = $code;
						//$this->session->set_userdata('veryCodeTime', $time);
						$this->alicache->set(md5("veryCodeTime".$code),$time);
						$this->sms->sendSMS(array (
							"{$phone}"
						), $message);
					} else {
						$result['notice'] = '手机号不正确';
						$result['state'] = '067';
					}
				} else {
					if ($this->_check_phone_no($phone)) {
						$message = '【美丽神器】手机验证码是：' . $code . '退订回复TD ';
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

				$message = '【美丽神器】您的手机验证码是：' . $code . '退订回复TD ';
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
}
?>
