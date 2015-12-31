<?php
class test extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->helper('form_helper');$this->load->model('Email_model');
}
    public function index(){
    	$data = array();
		$this->load->view('program_test', $data);
    }
    public function n(){
    	$mec = new Memcache();
		$mec->connect('127.0.0.1', 11211);
		$mec->set('push_58609',array('message'=>'hello'),MEMCACHE_COMPRESSED, 30);
		$mec->close();
    }
    public function home(){
    	$data = array();
		$this->load->view('new_meili', $data);
    }
    public function start(){
    	$arr = array('if','test');
    	if($this->input->post('user') and array_search($this->input->post('user'),$arr)){
    		$data = array();
    		$this->session->set_userdata('test_user', $this->input->post('user'));
    		$this->session->set_userdata('test_user_start', time());
    	    $data['user'] = strip_tags($this->input->post('user'));
		    $this->load->view('program_test_start', $data);
    	}else{
    		echo '未知用户';
    	}
    }
    public function submit(){
    	$arr = array('if','test');
    	if($this->session->userdata('test_user')){
    		$score = 5;
    		$usertime = time()-$this->session->userdata('test_user_start');
            if($this->input->post('t1')!=4){
            	$score--;
            }
            if(strtolower($this->input->post('t2'))!='c'){
            	$score--;
            }
            if($this->input->post('t3')!=5){
            	$score--;
            }if($this->input->post('t4')!='' and  $this->input->post('t4')==0){
            }else{
            	$score--;
            }
            if($this->input->post('t5')!=3){
            	$score--;
            }
            if($score!=0){
            	$score = 80-$usertime+$score*18;
            }else{
            	$score = 0;
            }
            if($usertime<20){
            	$score = 0;
            }
            if($score<0){
            	$score = 0;
            }else{
            	$score = $score/1.5;
            }
            echo '得分:'.$score.' 测试完毕！结果已发送无需重复测试！';
    	}
    }
	public function mod() {
		$this->load->model('wen_auth/roles', 'roles');
		$query = $this->roles->get_role_by_id(1);
	}
	public function pic() {
		if ($this->input->get('pid')) {
           $this->thumb($this->input->get('pid'));
		}
	}
	private function thumb($uid) {
		$this->load->library('upload');
		$this->load->helper('file');
		$target_path = realpath(APPPATH . '../images/users');
		$target_path = $target_path . '/' . $uid . '/';

		GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_thumb.jpg', 36, 36);
		GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_profile.jpg', 120, 120);
		echo 'file of folder '.$uid.' success!';

	}
	private function g()
	{
      for($i=1;$i<=6060;$i++)
      {
      	$tmp = $this->db->query("SELECT user_id,department FROM user_profile WHERE user_id = {$i} LIMIT 1")->result_array();
      	if(!empty($tmp))
      	{
      	   $data['department'] = ','.$tmp[0]['department'].',';
           $this->db->where('user_id', $tmp[0]['user_id']);
		   $this->db->update('user_profile', $data);
      	}
      }
      	echo 'success';
	}
	private function export()
	{
         $tmp = $this->db->query("SELECT users.id,users.email,users.phone,company.name,company.address,company.tel,company.web FROM users LEFT JOIN company on company.userid=users.id WHERE users.role_id = 3");
         echo '<table><thead><tr style="height:30px;background:#ddd"> <th style="width:60px;text-align:center">ID</th><th>pass</th><th>email</th><th>phone</th><th>name</th><th>tel</th><th>web</th><th>address</th> </tr></thead>';
         foreach($tmp->result() as $row){
         	if($row->id!=5389 && $row->id!=5368){
            $pass = rand(784232,9234218);
         	$this->change_password($row->id, $pass);
            echo "<tr><td style='background:#eee'>$row->id</td><td>$pass</td><td style='background:#eee'>$row->email</td><td>$row->phone</td><td style='background:#eee'>$row->name</td><td  >$row->tel</td><td>$row->web</td><td style='background:#eee'>$row->address</td></tr>";

         	}
         	  }
         echo '</table>';
	}
	public function push(){
		$this->load->model('push');
		$this->push->setTest();
       $state = $this->push->sendUser($this->input->get('m'),7034);
	   if($state){
	   	 echo 'success';
	   }else{
	   	echo 'error';
	   }
	}
	private function setPass()
	{
         if ($this->input->post('submit')) {

             if($this->input->post('uid') && $this->input->post('pass') && $this->input->post('safepass')=='safeChange123' ){
                  $this->change_password($this->input->post('uid'), $this->input->post('pass'));
             }
             redirect('test/setPass');
		} else {
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			$pass = rand(784232,9234218);
            echo form_open("test/setPass",array('id' => 'aquestion')).'用户ID:<input type="text" name="uid" value="" size="40" maxlength="40"/><br>用户密码：<input type="text" name="pass" value="'.$pass.'" size="40" maxlength="40"/><br>安全码：<input type="text" name="safepass"  size="40" maxlength="40"/><br><input type="submit" name="submit" value="设置"/></form>';
		    echo '<body>
</body>
</html>';

		}
	}

   	private function change_password($user_id, $new_pass) {
		// Load Models
		$this->load->model('Users_model');
	 $new_pass = crypt($this->wen_auth->_encode($new_pass));
        $this->Users_model->change_password($user_id, $new_pass);
	}
	private function sys(){
         $this->session->set_flashdata('msg','sdfsdf');
		 redirect('user/reg');

	}
}
?>






