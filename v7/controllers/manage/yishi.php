<?php
class yishi extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->library('yisheng');
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
		if (!$this->privilege->judge('yishi')) {
			die('Not Allow');
		}
	}
	public function index($page = '') {
		$condition = ' WHERE users.role_id = 2 ';
		$data['issubmit'] = false;
		$this->load->library('pager');
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';
			if ($this->input->get('phone')) {
				$condition .= " AND users.phone = '" . $this->input->get('phone') . "'";
				$fix .= $fix == '' ? '?phone=' . $this->input->get('phone') : '&phone=' . $this->input->get('phone');
			}
			if ($this->input->get('sname')) {
				$condition .= "  AND users.alias like '%" . trim($this->input->get('sname')) . "%'";
				$fix .= $fix == '' ? '?sname=' . $this->input->get('sname') : '&sname=' . $this->input->get('sname');
			}
			if ($this->input->get('city')) {
				$condition .= "  AND user_profile.city like '%" . trim($this->input->get('city')) . "%'";
				$fix .= $fix == '' ? '?city=' . $this->input->get('city') : '&city=' . $this->input->get('city');
			}

            if ($this->input->get('isrecommend')) {
                $condition .= "  AND user_profile.isrecommend='" . trim($this->input->get('isrecommend')) . "'";
                $fix .= $fix == '' ? '?isrecommend=' . $this->input->get('isrecommend') : '&isrecommend=' . $this->input->get('isrecommend');
            }
            if ($this->input->get('company')){
                $condition .= "  AND user_profile.company like '%" . trim($this->input->get('company')) . "%'";
                $fix .= $fix == '' ? '?company = ' . $this->input->get('company') : '&company =' . $this->input->get('company');
            }
		}
		
		
		
		$data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC")->num_rows();

		$per_page = 16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT if(users.state=1,'完整','不完整')as state,users.id,users.banned,users.email,users.suggested,user_profile.tel,users.phone,user_profile.item,user_profile.isrecommend,user_profile.sort,user_profile.sex,users.alias,user_profile.city FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page")->result();

		foreach ($data['results'] as $k => $v) {
			$data['results'][$k]->reNums = $this->db->query("SELECT wen_answer.cdate,wen_questions.id FROM wen_answer LEFT JOIN wen_questions ON wen_questions.id = wen_answer.qid  where wen_answer.uid =  " . $v->id)->num_rows();
		}

		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/yishi/' . ($start -1)) : site_url('manage/yishi/index');
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yishi/index/' . ($start +1)) : '';
		$config = array (
			"record_count" => $data['total_rows'],
			"pager_size" => $per_page,
			"show_jump" => true,
			'querystring_name' => $fix . '&page',
			'base_url' => 'manage/yishi/index',
			"pager_index" => $start
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "yishi";
		$this->session->set_userdata('history_url', 'manage/yishi?page=' . ($start -1));
		$this->load->view('manage', $data);
	}

	public function userac() {
		if (($uid = $this->input->get('uid'))) {
			$this->load->model('Email_model');
			$ban = intval($this->input->get('banned'));
			$data['banned'] = $ban;
			$this->common->updateTableData('users', $uid, '', $data);
			if ($ban == 0) {
				$conditions = array (
					'id' => $uid
				);
				$info = $this->common->getTableData('users', $conditions)->result_array();
				$splVars = array (
					"{title}" => '账户通过审核',
					"{content}" => '亲爱的用户，你的账户<br>用户名:' . $info[0]['alias'] . '<br>邮箱:' . $info[0]['email'] . '<br>手机:' . $info[0]['phone'] . '已通过审核！',
					"{time}" => date('Y-m-d H:i',
				time()), "{site_name}" => '美丽诊所');

				$info[0]['email'] != '' && $this->Email_model->sendMail($info[0]['email'], "support@meilizhensuo.com", '美丽诊所', 'user_pass', $splVars);
				if ($info[0]['phone'] != '') {
					$this->load->library('sms');
					$this->sms->sendSMS(array (
						"{$info[0]['phone']}"
					), '亲爱的用户，你的账户<br>用户名:' . $info[0]['alias'] . '<br>邮箱:' . $info[0]['email'] . '<br>手机:' . $info[0]['phone'] . '已通过审核！');
				}
			} else {
				$this->db->limit(1);
				$this->db->where('uid',$uid );
				if($uid){
            	   $this->db->delete('wensessions');
                }
			}
			echo 'success';
		}

	}
	// yishi comments lists
	public function yishi_comment($param = '') {
		$data['results'] = array();
		$acuid = intval($param);
		if ($fuid = $this->input->post('fuid')) {
			    $idata = array();
                $idata['userto'] = $acuid;
                $idata['userby'] = $fuid;
                $idata['type'] = 1;
                $idata['qid'] = 0;
                $idata['score'] = $this->input->post('score') * 10;
                $idata['review'] = $this->input->post('comment');
                $idata['showtype'] = 3;
                $idata['created'] = time();
                $this->db->insert('reviews', $idata);
                $this->setScore($param);

                redirect('manage/yishi/yishi_comment/'.$param);
		} else {
			$uid = intval($param);
			$tmp = $this->db->query("SELECT reviews.id,reviews.review,reviews.score,reviews.created,user_profile.Lname,user_profile.Fname,users.email,users.phone FROM reviews LEFT JOIN users ON users.id=reviews.userby LEFT JOIN user_profile ON user_profile.user_id = reviews.userby WHERE reviews.userto = {$acuid} and type=1 order by reviews.created desc LIMIT 30")->result_array();
			foreach ($tmp as $row) {
				$row['created'] = date('Y-m-d', $row['created']);
				$row['score'] = intval($row['score'] / 10);
				$row['showname'] = $row['phone'] != '' ? $row['phone'] : $row['email'];
				unset ($row['phone']);
				unset ($row['email']);
				$data['results'][] = $row;
			}
			$data['acuid'] = $acuid;
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "yishi_comment";
			$this->load->view('manage', $data);
		}
	}
	//set yishi score
   private function setScore($uid = '')
    {   if ($uid) {
                $condition = array('id' => $uid);
                $tmp = $this->common->getTableData('users', $condition, 'voteNum,grade')->result_array();
                if (empty($tmp)) {
                    die('error!');
                } else {
                    $score = ($this->input->post('score') * 10 + $tmp[0]['grade'] * $tmp[0]['voteNum']) / ($tmp[0]['voteNum'] + 1);
                    $data['grade'] = $score;
                    $data['voteNum'] = $tmp[0]['voteNum'] + 1;
                    $this->common->updateTableData('users', $uid, '', $data);

                }

            } else {
                $result['state'] = '012';
            }

    }
  //del review
  public function cdel($id=''){
  	if($id){
  		$this->db->delete('reviews', array('id' => $id));
  		echo 'success';
  	}
  }
	public function add() {
		if ($this->input->post() && !$this->notlogin) {
			$this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
			$this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');
			if ($this->form_validation->run() == TRUE) {
				$this->wen_auth->_setRegFrom(1);

				$udata = $this->wen_auth->register($this->input->post('phone'), $this->input->post('password'), $this->input->post('phone'), $this->input->post('phone'), $this->input->post('phone'), '', 2, '', '', '', false, false, false);
                $updateData['category'] = $this->input->post('category');
				$updateData['Fname'] = $this->input->post('Fname');
				$updateData['Lname'] = $this->input->post('Lname');
				$updateData['sex'] = $this->input->post('sex');
				$updateData['skilled'] = $this->input->post('skilled');
				$updateData['position'] = $this->input->post('position');
				$updateData['company'] = $this->input->post('company');
				$department = $this->input->post('department');
				$updateData['department'] = ',';
				if ($department) {
					foreach ($department as $k => $val) {
						$updateData['department'] .= $k . ',';
					}
				}

				$updateData['province'] = $this->input->post('province');
				$updateData['city'] = $this->input->post('city');
				$updateData['district'] = $this->input->post('district');
				if ($updateData['district'] == '') {
					$updateData['district'] = $updateData['city'];
					$updateData['city'] = $updateData['province'];
				}
				$updateData['address'] = $this->input->post('address');
				$updateData['introduce'] = $this->input->post('introduce');
				$items = $this->input->post('items');
				$updateData['items'] = '';
				if ($items) {
					foreach ($items as $k => $val) {
						$updateData['items'] .= ',' . $k;
					}
					$updateData['items'] = substr($updateData['items'], 1);
				}

				$updateData['tel'] = $this->input->post('tel');
				$this->thumb($udata['user_id'], $_FILES['uploadtemp']['tmp_name']);
				//var_dump($updateData['Fname'] == '' || $updateData['Lname'] == '' || $updateData['company'] == '' || $updateData['sex'] == '' || $updateData['city'] == '');die;
				if ($updateData['Fname'] == '' || $updateData['Lname'] == '' || $updateData['company'] == '' || $updateData['sex'] == '' || $updateData['city'] == '') {
					//$this->session->set_flashdata('historydata', serialize($updateData));
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息填写不完整，更新失败！'));
					redirect('manage/yishi/add');
				} else {
					$updateData['user_id'] = $udata['user_id'];
					$this->db->insert('user_profile', $updateData);

					$username = $updateData['Lname'] . $updateData['Fname'];
					unset ($updateData);
					$updateData['alias'] = $username;
					$updateData['rank_search'] = 10;
					$updateData['state'] = 1;
					//var_dump(!$this->wen_auth->get_phone());die;
					$updateData['email'] = $this->input->post('email');
					$updateData['phone'] = $this->input->post('phone');
					$this->db->where('id', $udata['user_id']);
					$this->db->update('users', $updateData);
					//var_dump(!empty ($_FILES['urls']['tmp_name']));die;
					if (!empty ($_FILES['urls']['tmp_name'])) {
						$this->picSet($udata['newuser']['user_id'], $_FILES['urls']['tmp_name']);
					}
					if (!empty ($_FILES['anli']['tmp_name'])) {
						$this->picSet2($udata['newuser']['user_id'], $_FILES['anli']['tmp_name']);
					}
					redirect('manage/yishi');

				}
			}
		}
		$data['items'] = $this->db->get('items')->result_array();
		$data['keshi'] = $this->yisheng->getKeShi();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "yisheng_add";
		$this->load->view('template', $data);
	}
	public function suggest() {
		if (($uid = $this->input->get('uid'))) {
			$ban = intval($this->input->get('suggest'));
			$data['suggested'] = $ban;
			$this->common->updateTableData('users', $uid, '', $data);
			echo 'success';
		}

	}
	public function editpass($param = '') {
		if ($uid = $this->input->post('uid')) {
			if (($newpass = $this->input->post('newpass')) && ($enpass = $this->input->post('repeatpass'))) {
				if ($enpass == $newpass) {
					$new_pass = crypt($this->_encode($newpass));
					$this->Users_model->change_password($uid, $new_pass);
					$this->session->set_flashdata('msg', $this->common->flash_message('success', '密码成功修改！'));
				} else {
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '密码不匹配！'));
				}

			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '密码修改失败！'));

			}
			redirect($this->session->userdata('history_url'));
		} else {
			$data['uid'] = $param;
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "edityspass";
			$this->load->view('manage', $data);
		}
	}
	/*
	* Function: _encode
	* Modified for WEN_Auth
	* Original Author: FreakAuth_light 1.1
	*/
	private function _encode($password) {
		$majorsalt = $this->config->item('WEN_salt');

		// if PHP5
		if (function_exists('str_split')) {
			$_pass = str_split($password);
		}
		// if PHP4
		else {
			$_pass = array ();
			if (is_string($password)) {
				for ($i = 0; $i < strlen($password); $i++) {
					array_push($_pass, $password[$i]);
				}
			}
		}

		// encrypts every single letter of the password
		foreach ($_pass as $_hashpass) {
			$majorsalt .= md5($_hashpass);
		}

		// encrypts the string combinations of every single encrypted letter
		// and finally returns the encrypted password
		return md5($majorsalt);
	}
	public function detail($param = '') {
		if ($param != '') {
			if ($this->input->post('submit')) {

			} else {
				$uid = intval($param);
				$data['uid'] = $uid;
				$data['items'] = $this->db->get('items')->result_array();
				$this->load->library('yisheng');
				$this->db->where('users.id', $uid);
				$this->db->select('*');
				$this->db->from('users');
				$this->db->join('user_profile', 'user_profile.user_id = users.id');
				$data['yishi'] = $this->db->get()->result_array();
				if (empty ($data['yishi'])) {
					$infos['user_id'] = $param;
					$this->common->insertData('user_profile', $infos);
				}
				$data['keshi'] = $this->yisheng->getKeShi();
				$tmp = explode(',', $data['yishi'][0]['department']);
				foreach ($tmp as $v) {
					$data['keshidata'][$v] = true;
				}

				$data['yishi'][0]['thumb'] = $this->profilepic($uid, 3);

				$data['notlogin'] = $this->notlogin;
				$data['message_element'] = "edityishi";

				$this->load->view('manage', $data);
			}
		}
	}

	public function update() {
		if ($uid = $this->input->post('uid')) {
			$updateData['Lname'] = $this->input->post('Lname');
			$updateData['Fname'] = $this->input->post('Fname');
			$updateData['sex'] = $this->input->post('sex');
			$updateData['tel'] = $this->input->post('tel');
			$updateData['position'] = trim($this->input->post('position'));
			$department = $this->input->post('department');
			$updateData['department'] = ',';
			if ($department) {
				foreach ($department as $k => $val) {
					$updateData['department'] .= $k . ',';
				}
			}

			$items = $this->input->post('items');
			$updateData['items'] = '';
			if ($items) {
				foreach ($items as $k => $val) {
					$updateData['items'] .= ',' . $k;
				}
				$updateData['items'] = substr($updateData['items'], 1);
			}
            $updateData['sort'] = $this->input->post('sort');
            $updateData['isrecommend'] = $this->input->post('isrecommend');
            $updateData['item'] = $this->input->post('item');
            $updateData['category'] = $this->input->post('category');
			$updateData['company'] = $this->input->post('company');
			$updateData['skilled'] = $this->input->post('skilled');
			$updateData['introduce'] = $this->input->post('introduce');
			$updateData['province'] = $this->input->post('province');
			$updateData['city'] = $this->input->post('city');
			$updateData['district'] = $this->input->post('district');
			if ($updateData['district'] == '') {
				$updateData['district'] = $updateData['city'];
				$updateData['city'] = $updateData['province'];
			}
			$updateData['address'] = trim($this->input->post('address'));
			$updata['assistphone'] = trim($this->input->post('assistphone'));

			$this->db->where('user_id', $uid);
			$this->db->update('user_profile', $updateData);

			$updateDatas['alias'] = $updateData['Lname'] . $updateData['Fname'];
			$updateDatas['sysvotenum'] = $this->input->post('sysvotenum');
			$updateDatas['sysreplys'] = $this->input->post('sysreplys');
			$updateDatas['sysgrade'] = $this->input->post('sysgrade');
			if ($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')) {
				$updateDatas['email'] = $this->input->post('email');
			}
			$updateDatas['utags'] = trim($this->input->post('utags'));

			if ($this->input->post('state') == 1) {
				$updateDatas['state'] = 1;
			}
			if ($this->input->post('phone')) {
				$updateDatas['phone'] = $this->input->post('phone');
				$updateDatas['rev_phone'] = 0;
			}

			$this->db->where('id', $uid);
			$this->db->update('users', $updateDatas);
			//echo $this->db->last_query();exit;
			if ($_FILES['thumb']['tmp_name']) {
				$this->thumb($uid, $_FILES['thumb']['tmp_name']);
			}
			redirect($this->session->userdata('history_url'));
		}
	}
	public function answers($params = '', $page = '') {
		$start = intval($page);
		$data['uid'] = intval($params);
		$start == 0 && $start = 1;
		$per_page = 10;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$condition = ' WHERE wen_answer.uid = ' . $data['uid'];

		$data['results'] = $this->db->query("SELECT wen_questions.title,wen_answer.cdate,wen_questions.id,wen_answer.content FROM wen_answer LEFT JOIN wen_questions ON wen_questions.id = wen_answer.qid {$condition} ORDER BY wen_questions.id DESC  LIMIT $offset , $per_page")->result();

		$data['total_rows'] = $this->db->query("SELECT wen_questions.id FROM wen_answer LEFT JOIN wen_questions ON wen_questions.id = wen_answer.qid {$condition} ORDER BY wen_questions.id DESC ")->num_rows();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/yishi/' . ($start -1)) : site_url('manage/yishi/index');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yishi/index/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "answers";
		$this->load->view('manage', $data);
	}
	//delete user
	public function del($uid) {
		$condition = array (
			'id' => $uid
		);
		$this->common->deleteTableData('users', $condition);
		//profile
		$condition = array (
			'user_id' => $uid
		);
		$this->common->deleteTableData('user_profile', $condition);
		//profile
		$condition = array (
			'user_id' => $uid
		);
		$this->common->deleteTableData('wen_notify', $condition);
		//notify
		$condition = array (
			'user_id' => $uid
		);
		$this->common->deleteTableData('user_notification', $condition);
		//notify
		$condition = array (
			'uid' => $uid
		);
		$this->common->deleteTableData('wen_weibo', $condition);
		//notify
		$condition = array (
			'uid' => $uid
		);
		$this->common->deleteTableData('wen_follow', $condition);
		//notify
		$condition = array (
			'fid' => $uid
		);
		$this->common->deleteTableData('wen_follow', $condition);
		//notify
		$condition = array (
			'fUid' => $uid
		);
		$this->common->deleteTableData('wen_questions', $condition);
		//thumb
		$this->deleteDir($this->path . '/users/' . $uid);
		redirect($this->session->userdata('history_url'));
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

	private function thumb($uid, $file) {
		if ($file != '') {
			$this->remote->uputhumb($file, $uid);
			return true;
		} else {
			return false;
		}
	}
	//upload set pic
	private function picSet($userid = '', $tmp_name = array ()) {
		$datas['albumId'] = time();
		$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
		$basedir .= '/' . date('m');

		if (!is_dir($basedir)) {
			mkdir($basedir, 0777);
		}

		$datas['userId'] = $userid;
		foreach ($tmp_name as $row) {
			if (is_file($row)) {
				$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = $basedir . '/' . $filename;
				move_uploaded_file($row, $datas['savepath']);
				$datas['cTime'] = time();
				$datas['savepath'] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
				$this->common->insertData('c_photo', $datas);
			}

		}
	}

	//上传医师案例图集
	private function picSet2($userid = '', $tmp_name = array ()) {
		$datas['type'] = 1;
		$datas['albumId'] = time();
		$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
		$basedir .= '/' . date('m');

		if (!is_dir($basedir)) {
			mkdir($basedir, 0777);
		}

		$datas['userId'] = $userid;
		foreach ($tmp_name as $row) {
			if (is_file($row)) {
				$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = $basedir . '/' . $filename;
				move_uploaded_file($row, $datas['savepath']);
				$datas['cTime'] = time();
				$datas['savepath'] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
				$this->common->insertData('c_photo', $datas);
			}

		}
	}
	private function deleteDir($dir) {
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if ($file != "." && $file != "..") {
				$fullpath = $dir . "/" . $file;
				if (!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		if (rmdir($dir)) {
			return true;
		} else {
			return false;
		}

	}
}
?>
