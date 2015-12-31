<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct() {
		parent :: __construct();
		$this->load->helper('cookie');
		$this->load->helper('url');
		$this->load->helper('curl');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	
	public  function index()
	 {
		redirect('manage');
	 }
	 
	 public function usercookie(){
		if($this->session->userdata('is_logged_in')){
			echo TRUE;
		}else{
			echo FALSE;
		}
	 }
	 
	 public function login(){
		if($this->input->post('username')){
			$signifo = array('username'=>$this->input->post('username'),'password'=>$this->input->post('logpassword'),'remember'=>1);
			$userreg =json_decode(curl_return_post($signifo,'user/signin?'),TRUE);
				if($userreg['state']=='000' && $userreg['ustate']=='000'){
 					$reg_cookie = array(
						'username' => $userreg['username'],
						'is_logged_in' => true
					);
					echo "1";
					$this->session->set_userdata($reg_cookie);
				}else{
					echo "用户名或密码错误";
				}
		}else{
			echo '用户名或密码错误';
		}
	 }
	
	public function signup(){
		header("Content-type:text/html;charset=utf-8");
		$data['title']='用户注册';
		$user = curl_return('items/allItems?');
		$data['allItems']=$user;
		//$data['title'] = $this->session->userdata('username');
		$this->load->view('account/signup',$data); 
		
/* 		$is_logged_in = $this->session->userdata('is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			$this->load->view('includes/template');
		}else{
			redirect('manage');
		} */
	}
	
	public function signupsend(){
		header("Content-type:text/html;charset=utf-8");
		$phone = $this->input->post('phone');
		$vcode = $this->input->post('captcha');
		$password = $this->input->post('password');
		if(isset($_COOKIE["iphone"]) && isset($_COOKIE["ivcode"])){
			if($_COOKIE["iphone"] !=  $phone || $_COOKIE["ivcode"]  != $vcode){
				echo '验证码错误';
			}else{
				
				//$signifo=array("phone"=>$phone,"ivcode"=>$vcode,"password"=>md5($password));
				//$userreg = json_decode(curl_return_post($signifo,'user/reg?'),TRUE);
				
				$signifo=array('username'=>$phone,'email'=>'','phone'=>$phone,'code'=>$vcode,'device_sn'=>'','password'=>$password,'confirmpassword'=>$password,'utype'=>1);
				$userreg = json_decode(curl_return_post($signifo,'user/reg?'),TRUE);
				if($userreg['state']=='000' && $userreg['ustate']=='000'){				
 					$reg_cookie = array(
						'username' => $userreg['username'],
						'is_logged_in' => true
					);
					$this->session->set_userdata($reg_cookie);
					echo '1';
				}else{
					echo $userreg['notice'];
				}
				//echo $userreg;
			}
		}else{
			echo '验证码错误';
		} 
	}
	
	public function phone(){
		$error = "";
		$vcode = "";
		$value = trim($this->input->get('phone'));
		if ($value == '') {
			return TRUE;
		} else {
			if (preg_match("/1[3458]{1}\d{9}$/",$value)) {
				if($this->input->get('type')){
					$user = curl_return2('user/sendsms?phone='.$value.'&type=1&');
				}else{
					$user = curl_return2('user/sendsms?phone='.$value.'&');
				}
 				if($user['state']=='000'){
					$vcode=1;
					setcookie("iphone",  $this->input->get('phone'), time()+1800);
					setcookie("ivcode",  $user['vcode'], time()+1800);
				}elseif($user['state']=='066'){
					$error .= "手机号已注册";
				}else{
					$error .= "请输入有效的手机号码！";
				}
				echo '{"error":"'.$error.'","vcode":"'.$user['vcode'].'"}';

			} else {
				echo  '请输入有效的手机号码！';
				return FALSE;
			}
		}
	}
	
	public function findpass(){
		header("Content-type:text/html;charset=utf-8");
		$phone = $this->input->post('newphone');
		$vcode = $this->input->post('newcaptcha');
		$newpassword = $this->input->post('newpassword');
		$signifo=array('phone'=>$phone,'code'=>$vcode,'newpass'=>$newpassword);
		//$userreg = json_decode(curl_return_post($signifo,'user/resetPassword?'),TRUE);
		$userreg = json_decode(curl_return_post($signifo,'user/resetPassword?'),TRUE);
		if($userreg['state']=='000'){			
			$reg_cookie = array(
				'username' => $userreg['username'],
				'is_logged_in' => true
			);
			$this->session->set_userdata($reg_cookie);
			echo '1';
		}else{
			echo $userreg['notice'];
		}
	}

}