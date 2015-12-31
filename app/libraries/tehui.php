<?php
class tehui extends CI_Model {
	private $tehui;
	function __construct() {
		$this->db = $this->load->database('default', TRUE);
		$this->tehui = $this->load->database('tehui', TRUE);

	}
	//update tehui userinfo
	public function updateUser($id, $data = array ()) {
		$this->tehui->where('id', $id);
		return $this->tehui->update('user', $data);
	}
	//tehui db excute query
	public function q($str) {
		return $this->tehui->query($str)->result_array();
	}

	//tehui card send to user
	public function tehuiSend($uid = '', $sendSms = false) {
		if ($uid) {
			$ctime = time();
			if ($sendSms) {
				$this->tehui->where('uid', 0);
				$this->tehui->where('consume', 'N');
				$this->tehui->limit(1);
				$this->tehui->where('end_time > ', $ctime);
				$tmp = $this->tehui->get('card')->result_array();

                if(!empty($tmp)){
                	$this->tehui->limit(1);
				    $this->tehui->where('id', $tmp[0]['id']);
				    $data['uid'] = $uid;
				    $this->tehui->update('card', $data);
				    return $tmp[0]['id'];
                }
				return '';
			} else {
				$this->tehui->like('code', 'reguser');
				$this->tehui->where('uid', 0);
				$this->tehui->where('consume', 'N');
				$this->tehui->limit(1);
				$this->tehui->where('end_time > ', $ctime);
				$data['uid'] = $uid;
				$this->tehui->update('card', $data);
			}
		}
	}
	//delete tehui user
	public function delUser($uid = '') {
		$this->tehui->where('id', $uid);
		$this->tehui->delete('user');
	}
	//将用户注册人特惠表
	public function reg($data) {
		$this->tehui->insert('cenwor_system_members', $data);
		return $this->tehui->insert_id();
	}
	//将用户注册人特惠表
	public function reg_zuitu($data) {
		$this->tehui->insert('user', $data);
		return $this->tehui->insert_id();
	}
	//用户特惠表登录
	public function loginForm($data) {
		$is_manager = '';
		if (isset ($data['manager'])) {
			$is_manager = $data['manager'];
		}
		$thhtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
				 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh" lang="zh">
				<head><script type="text/javascript" src="' . base_url() . 'public/js/jquery.js"></script></head>
				<body><div style="display:none"><form method="POST" action="http://tehui.meilizhensuo.com/?mod=account&code=login&op=done" id="tehuiform">
										<input type="hidden" name="FORMHASH" value="12bd037ac6d367a5" >
										<input name="username" type="text" value="' . $data['username'] . '">
										<input name="tehui" type="hidden" value="tehui">
										<input name="password" type="password" class="f-l" value="' . $data['password'] . '">
										<input type="hidden" name="manager" value="' . $is_manager . '">
										<input type="hidden" name="return_url" value="' . rtrim(base_url(), '/') . '">
										</form>
										</div>
										<script type="text/javascript">$(document).ready(function() {$("#tehuiform").submit()})</script>
										</body></html>';
		return $thhtml;

	}
	//用户特惠表登录
	public function loginZuiTuForm($data) {
		$is_manager = '';
		if (isset ($data['manager'])) {
			$is_manager = $data['manager'];
		}
		$thhtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
				 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh" lang="zh">
				<head><script type="text/javascript" src="' . base_url() . 'public/js/jquery.js"></script></head>
				<body><div style="display:none"><form id="tehuiform" method="post" action="http://tehui.meilizhensuo.com/account/login.php" class="validator">
				                            <input type="text" size="30" name="email" id="login-email-address" class="f-input" value="' . $data['username'] . '">
				                            <input name="password" type="password" class="f-l" value="' . $data['password'] . '">
				                            <input name="tehui" type="hidden" value="tehui">
				                            <input type="hidden" name="manager" value="' . $is_manager . '">
				<input type="hidden" name="return_url" value="' . rtrim(base_url(), '/') . '">
				                    </form>
										</div>
										<script type="text/javascript">$(document).ready(function() {$("#tehuiform").submit()})</script>
										</body></html>';
		return $thhtml;

	}
	//获取用户
	public function getUserInfo($uid) {
		//return $this->tehui->get_where('cenwor_system_members', array('uid' => $uid))->result();
		$this->tehui->where('uid', $uid);
		return $this->tehui->get('cenwor_system_members');

	}
	/**
	 *@functiuon 将美丽诊所的数据导入特惠
	 */
	public function putUserInfo() {
		//return $this->tehui->get_where('cenwor_system_members', array('uid' => $uid))->result();
		$this->db->where('role_id', 1);
		$users = $this->db->get('users');

		if ($users->num_rows > 0) {
			//var_dump($users->result_array());
			foreach ($users->result_array() as $k => $v) {
				$sql = "INSERT IGNORE INTO `cenwor_system_members`  set username='" . $v['username'] . "', password='" . $v['password'] . "',role_id=3,role_type='normail',phone='" . $v['phone'] . "',checked=1";
				$res = $this->tehui->query($sql);
			}
		}

	}
	/**
	*@functiuon 将美丽诊所的数据导入特惠
	*/
	public function putZuiTuUserInfo() {
		//return $this->tehui->get_where('cenwor_system_members', array('uid' => $uid))->result();
		$this->db->where('role_id', 1);
		$users = $this->db->get('users');

		if ($users->num_rows > 0) {
			//var_dump($users->result_array());
			foreach ($users->result_array() as $k => $v) {
				$sql = "INSERT IGNORE INTO `user`  set id='" . $v['id'] . "',username='" . $v['username'] . "', password='" . $v['password'] . "',manager='N',mobile='" . $v['phone'] . "'";
				$res = $this->tehui->query($sql);
			}
		}

	}
}
?>
