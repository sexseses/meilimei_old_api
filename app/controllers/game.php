<?php
class game extends CI_Controller {
	private $islog = false;
	protected $mec = null;
	private $cur = array (
		1 => '鼠',
		2 => '牛',
		3 => '虎',
		4 => '兔',
		5 => '龙',
		6 => '蛇',
		7 => '马',
		8 => '羊',
		9 => '猴',
		10 => '鸡',
		11 => '狗',
		12 => '猪'
	);
	public function __construct() {
		parent :: __construct();
		$this->mec = new Memcache();
		$this->mec->connect('127.0.0.1', 11211);
		if ($this->session->userdata('phone')) {
			$this->islog = true;
		}
	}

	public function reg() {
		if($this->input->post('resetbm')){
			$session = array (
				'phone' => false
			);
            $this->session->set_userdata($session);
			$this->islog = false;
		}
		$data = array ();
		if (!$this->islog and ($uname = trim($this->input->post('uname'))) and $phone = trim($this->input->post('phone'))) {
			$session = array (
				'phone' => $phone
			);
			$gameUserPhone = $this->mec->get('gameUserPhone');
			$gameUserPhone[] = $phone;

			$gameUserName = $this->mec->get('gameUserName');
			$gameUserName[$phone] = $uname;

			//record user step
			$gameRes = array ();
			$this->mec->set('gUser_' . $phone, $gameRes, 0, 3600 * 3);

			//use time
			$this->mec->set('gUTime_' . $phone, time(), 0, 3600 * 3);

			$this->mec->set('gameUserPhone', $gameUserPhone, 0, 3600 * 3);
			$this->mec->set('gameUserName', $gameUserName, 0, 3600 * 3);

			$this->session->set_userdata($session);
			$this->islog = true;
		}
		$data['islog'] = $this->islog;
		$this->load->view('game/reg', $data);
	}
    public function state(){
    	$data = array ();
        $data['step'] = $this->mec->get('gUser_' . $this->session->userdata('phone'));
        $this->load->view('game/state', $data);
    }
	public function step($param = '') {
		if ($param and $param > 0 and $param < 13) {
			if ($this->islog) {
				$step = $this->mec->get('gUser_' . $this->session->userdata('phone'));
				//update game time
				if(!isset($step[$param])){
					$this->mec->set('gUTime_' . $this->session->userdata('phone'), time(), 0, 3600 * 3);
				}
				$step[$param] = true;
                $this->mec->set('gUser_' . $this->session->userdata('phone'), $step, 0, 3600 * 3);
				$data = array ();
				if (count($step) == 12) {
					$this->load->view('game/success', $data);
				} else {
					$data['text'] = $this->cur[$param];
					$data['pic'] = intval($param);
					$this->load->view('game/step', $data);
				}

			} else {
				echo '您还未报名,不能参加游戏!';
			}
		} else {
			echo '访问错误!';
		}
	}

	public function manage() {
		$data = array ();
		$gameUserPhone = $this->mec->get('gameUserPhone');
		$gameUserName = $this->mec->get('gameUserName');
		$res = array ();
		foreach ($gameUserPhone as $r) {
			$tmp = array ();
			$tmp['phone'] = $r;
			$tmp['name'] = $gameUserName[$r];
			$tmp['order'] = count($this->mec->get('gUser_' . $r));
			$tmp['time'] = date('H:i:s', $this->mec->get('gUTime_' . $r));
			$res[$tmp['order']][] = $tmp;
		}
		$i = 1;
		foreach ($res as $r) {
			foreach ($r as $l) {
				$data['res'][$i] = $l;
				$i++;
			}
		}
		$this->load->view('game/manage', $data);
	}
	function getdata() {
		$data = array ();
		$gameUserPhone = $this->mec->get('gameUserPhone');
		$gameUserName = $this->mec->get('gameUserName');
		$res = array ();
		foreach ($gameUserPhone as $r) {
			$tmp = array ();
			$tmp['phone'] = $r;
			$tmp['name'] = $gameUserName[$r];
			$tmp['order'] = count($this->mec->get('gUser_' . $r));
			$tmp['time'] = date('H:i:s', $this->mec->get('gUTime_' . $r));
			$res[$tmp['order']][] = $tmp;
		}
		krsort($res);
		$i = 1;
		foreach ($res as $r) {
			foreach ($r as $l) {
				$data['res'][$i] = $l;
				$i++;
			}
		}
		foreach ($data['res'] as $r) {
			echo '<li class="ui-li ui-li-static ui-body-c ui-body-inherit ui-first-child ui-last-child">' . $r['name'] . '， 手机' . $r['phone'] . '， 状态:' . (12 - $r['order'] > 0 ? '未完成' : '完成') . '<br><span > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;完成:' . $r['order'] . '项，时间:' . $r['time'] . '</span></li>';
		}
		exit;
	}
	function resetinfo() {
		$this->mec->set('gameUserPhone', '', 0, 0);
		$this->mec->set('gameUserName', '', 0, 0);
		echo '游戏已经重置,可以重新开始统计!';
	}
	function __destruct() {
		$this->mec->close();
	}
}
?>
