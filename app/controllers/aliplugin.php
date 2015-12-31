<?php
class aliplugin extends CI_Controller {
	public function __construct() {
		parent :: __construct();
        $this->load->library('tehui');
        //error_reporting(E_ALL);
       // ini_set('display_errors','On');
	}
	public function index() {
        if($this->input->get('aliplugin') == 1){
            $tehuiData = array();
            $password = $this->input->post('upass');
            $this->wen_auth->_setRegFrom(1);
            $device_sn = time();
            $username = $phnum = $str = $this->input->post('uname');
            $email = $username.'@meilishenqi.com';
            $data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, '', intval($this->input->post('utype')));
            $this->db->where('id',$data['user_id']);
            $this->db->update('users', array('aliplugin'=>$this->input->post('alikey')));
            $tehuiData = array('id'=>null,'email'=>$email,'username'=>$phnum,'password'=>crypt($this->wen_auth->_encode($password)),'realname'=>'','alipay_id'=>'','avatar'=>'','newbie'=>'Y','mobile'=>$phnum,'qq'=>'','money'=>0.00,'score'=>0,'zipcode'=>null,'address'=>'','city_id'=>0,'emailable'=>'Y','enable'=>'Y','manager'=>'N','secret'=>'','recode'=>'','sns'=>'','ip'=>'','login_time'=>time(),'create_time'=>time(),'mobilecode'=>'','secret'=>md5(rand(1000000,9999999).time().$phnum));

            $th_inertid = $this->tehui->reg_zuitu($tehuiData);

            redirect('aliplugin/index?uid='.$data['user_id']);
        }else {
            if ($uid = $this->input->get('uid')) {
                $data = array();
                if(strlen($uid) <32 ) {
                    $this->db->where('id', $uid);
                }else{
                    $this->db->where('aliplugin',$uid);
                }
                $data['infos'] = $this->db->get('users')->result_array();
                if(strlen($uid) <32 ) {
                    $data['login'] = 1;
                }else{
                    if (isset($data['infos'][0]['aliplugin'])) {
                        $data['login'] = 1;
                    } else {
                        $data['login'] = 0;
                    }
                }


                $this->load->view('theme/include/aliplugin', $data);
            } else {
                $data = array();
                $data['login'] = 0;
                $this->load->view('theme/include/aliplugin', $data);
            }
        }
	}
}
?>
