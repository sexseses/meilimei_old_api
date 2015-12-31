<?php
class yiyuan extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		//error_reporting(E_ALL ^ E_NOTICE);
		ini_set("display_errors","On");
		$this->load->library('form_validation');
		$this->load->library('yisheng');
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');$this->load->model('Users_model');
		$this->load->model('privilege');
		$this->load->model('remote');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('yiyuan')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$condition = ' WHERE users.role_id = 3 ';
		$data['issubmit'] = false;$fix = '';
		$this->load->library('pager');
		//search start
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true';
			if ($this->input->get('phone')) {
				$condition .= " AND users.phone = '" . $this->input->get('phone')."'";
				$fix.=$fix==''?'?phone='.$this->input->get('phone'):'&phone='.$this->input->get('phone');
			}
			if ($this->input->get('sname')) {
				$condition .= "  AND company.name like '%" . trim($this->input->get('sname')) . "%'";
				$fix.=$fix==''?'?sname='.$this->input->get('sname'):'&sname='.$this->input->get('sname');
			}
			if ($this->input->get('state')) {
				$condition .= "  AND company.state = {$this->input->get('state')}";
				$fix.=$fix==''?'?state='.$this->input->get('state'):'&state='.$this->input->get('state');
			}
			if ($this->input->get('city')) {
				$condition .= "  AND company.city like '%" . trim($this->input->get('city')) . "%'";
				$fix.=$fix==''?'?city='.$this->input->get('city'):'&city='.$this->input->get('city');
			}
		}
		$data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN company ON company.userid=users.id {$condition} ORDER BY users.id DESC")->num_rows();

		$per_page =  16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
       $data['results'] = $this->db->query("SELECT users.id,users.banned,users.email,company.tel,users.phone,company.name,company.contactN,company.state,company.city FROM users LEFT JOIN company ON company.userid=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page")->result();
		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/yiyuan/index/' . ($start -1)).$fix : site_url('manage/yiyuan/index').$fix;
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/yiyuan/index/' . ($start +1)).$fix : '';
		$config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                 'querystring_name'=>$fix.'&page',
                'base_url'=>'manage/yiyuan/index',
                "pager_index"=>$start
          );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$this->session->set_userdata('history_url', 'manage/yiyuan?page=' . ($start -1));
		$data['message_element'] = "yiyuan";
		$this->load->view('manage', $data);
	}

	//commment and score fro yiyuan
	public function comment($param = ''){
		$data['results'] = array();
		$acuid = intval($param);
		if ($fuid = $this->input->post('fuid')) {
			    $idata = array();
                $idata['userto'] = $acuid;
                $idata['userby'] = $fuid;
                $idata['type'] = 2;
                $idata['qid'] = 0;
                $idata['score'] = $this->input->post('score') * 10;
                $idata['review'] = $this->input->post('comment');
                $idata['showtype'] = 3;
                $idata['created'] = time();
                $this->db->insert('reviews', $idata);
                $this->setScore($param);
                redirect('manage/yiyuan/comment/'.$param);
		} else {
			$uid = intval($param);
			$tmp = $this->db->query("SELECT reviews.id,reviews.review,reviews.score,reviews.created,user_profile.Lname,user_profile.Fname,users.email,users.phone FROM reviews LEFT JOIN users ON users.id=reviews.userby LEFT JOIN user_profile ON user_profile.user_id = reviews.userby WHERE reviews.userto = {$acuid} and type=2 order by reviews.created desc LIMIT 30")->result_array();
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
			$data['message_element'] = "jigou_comment";
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
    // get detail info
	public function detail($param = '') {
		$data['uid'] = $this->uid;
		if ($param != '') {
			if ($this->input->post('submit')) {

			} else {
				$uid = intval($param);
				$data['uid'] = $uid;
				$this->db->where('userid', $uid);
				$this->db->select('item_id,price');
				$prices = $this->db->get('price')->result_array();
				foreach ($prices as $row) {
					$data['prices'][$row['item_id']] = $row['price'];
				}
				$data['items'] = $this->db->get('items')->result_array();
				$this->db->where('company.userid', $uid);
				$this->db->select('company.*,users.phone,users.utags,users.email,users.rev_phone,users.state,users.sysreplys,users.sysvotenum,users.sysgrade');
				$this->db->from('company');
				$this->db->join('users', 'company.userid = users.id');
				$data['companyinfo'] = $this->db->get()->result_array();
                $data['thumb'] = $this->profilepic($uid, 3);
				if (empty ($data['companyinfo'])) {
					$infos['userid'] = $param;
					$infos['cdate'] = time();
					$this->common->insertData('company', $infos);
				}
                
				$data['notlogin'] = $this->notlogin;
				$data['message_element'] = "edityiyuan";
				$this->load->view('manage', $data);
			}
		}
	}
  //del review
  public function cdel($id=''){
  	if($id){
  		$this->db->delete('reviews', array('id' => $id));
  		echo 'success';
  	}
  }
	public function track($param='',$page){
		$data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN company ON company.userid=users.id {$condition} ORDER BY users.id DESC")->num_rows();

		$per_page =  16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->select('yuyueSend.*,yuyue.userby,yuyue.name,yuyue.phone');
		$this->db->where('yuyueSend.uid', $param);
		$this->db->where('yuyue.is_delete', 0);
		$this->db->join('yuyue', 'yuyue.sn = yuyueSend.sn');
	    $data['res'] = $this->db->get('yuyueSend')->result_array();
        $data['notlogin'] = $this->notlogin;
	    $data['message_element'] = "yiyuan_track";
	    $this->load->view('manage', $data);
	}
	public function editpass($param=''){
         if($uid=$this->input->post('uid')){
             if(($newpass=$this->input->post('newpass')) && ($enpass=$this->input->post('repeatpass')) ){

             	if($enpass == trim($newpass)){
             	   $new_pass = crypt($this->_encode($newpass));
				   $this->Users_model->change_password($uid, $new_pass);
                   $this->session->set_flashdata('msg', $this->common->flash_message('success', '密码成功修改！'));
             	}else{
             	  $this->session->set_flashdata('msg', $this->common->flash_message('error', '密码不匹配！'));
             	}

             }else{

             	 $this->session->set_flashdata('msg', $this->common->flash_message('error', '密码修改失败！'));

             }
              redirect($this->session->userdata('history_url'));
         }else{
           $data['uid'] = $param;
           $data['notlogin'] = $this->notlogin;
		   $data['message_element'] = "editjgpass";
	       $this->load->view('manage', $data);
         }
	}
	public function userac(){
		if (($uid = $this->input->get('uid'))) {
			$state = intval($this->input->get('state'));
			$data['state'] = $state;
			$this->db->limit(1);
			$this->db->where('userid', $uid);
			$this->db->update('company', $data);
			echo 'success';
		}
	}
	public function add() {
		if ($this->input->post('name') && $this->uid) {
			$this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
			$this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');

			if ($this->form_validation->run() == TRUE) {
				$this->wen_auth->_setRegFrom(1);
				$udata = $this->wen_auth->register($this->input->post('name'), $this->input->post('password'), $this->input->post('email'), $this->input->post('phone'), $this->input->post('phone'), '',3,'','','',false,false,false);

			$updateData['name'] = $this->input->post('name');
			$updateData['contactN'] = $this->input->post('contactN');
			$updateData['tel'] = $this->input->post('tel');
			$updateData['phone'] = $this->input->post('phone');
			$updateData['web'] = $this->input->post('web');
			$department = $this->input->post('department');
			if ($department) {
				$updateData['department'] = ',';
				foreach ($department as $k => $val) {
					$updateData['department'] .= $k . ',';
				}
			}

			$updateData['shophours'] = $this->input->post('shophours');

			$updateData['province'] = $this->input->post('province');
			$updateData['city'] = $this->input->post('city');
			$updateData['district'] = $this->input->post('district');
			if ($updateData['district'] == '') {
				$updateData['district'] = $updateData['city'];
				$updateData['city'] = $updateData['province'];
			}
			$updateData['address'] = $this->input->post('address');
			$updateData['descrition'] = $this->input->post('descrition');
			$updateData['weibo'] = $this->input->post('weibo');
			$updateData['userid'] = $udata['user_id'];
			$updateData['cdate'] = time();
			$updateData['users'] = $this->input->post('users');
			$code = $this->input->post('validecode');


				if ($updateData['district'] != '' && $updateData['name'] != '' && $updateData['tel'] != '') {
					//upload certificate pictures
					if (!empty ($_FILES['uploadspec']['tmp_name'])) {
						$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
						$basedir .= '/' . date('m');
						if (!is_dir($basedir)) {
							mkdir($basedir, 0777);
						}
						$savepath = array ();
						foreach ($_FILES['uploadspec']['tmp_name'] as $row) {
							if (is_file($row)) {
								$filename = uniqid(time(), false) . '.jpg';
								$tmppath = $basedir . '/' . $filename;
								move_uploaded_file($row, $tmppath);
								$savepath['CI'][] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
							}
						}
						$updateData['picture'] = serialize($savepath);
					}
					$str = "SELECT userid,id FROM `company` WHERE `userid` ={$udata['user_id']} LIMIT 1";
					$tjudge = $this->db->query($str)->result_array();
					if (empty ($tjudge)) {
						$this->db->insert('company', $updateData);
						$companyid = $this->db->insert_id();
					} else {
						$companyid = $tjudge[0]['id'];
					}
					unset ($updateData);

					$items = array_filter($this->input->post('items'));
					if ($items) {
						foreach ($items as $k => $v) {
							$data = array (
								'userid' => $udata['user_id'],
								'item_id' => $k,
								'price' => $v,
								'company_id' => $companyid,
							'cdate' => time());
							$this->db->insert('price', $data);
						}
					}
					$_FILES['uploadtemp']['tmp_name']&&$this->thumb($udata['user_id'], $_FILES['uploadtemp']['tmp_name']);

					//init and add user info
					$updateData = array ();
					$updateData['alias'] = $this->input->post('name');
					$updateData['state'] = 1;
					if (!$this->wen_auth->get_emailId()) {
						$updateData['email'] = $this->input->post('email');
					}
					elseif (!$this->wen_auth->get_phone()) {
						$updateData['phone'] = $this->input->post('phone');
					}
					//$updateData['tags'] = trim($this->input->post('tags'));
					$this->db->where('id', $udata['user_id']);
					$this->db->update('users', $updateData);
					$this->wen_auth->complete = true;
					//upload picture set
					if (!empty ($_FILES['urls']['tmp_name'])) {
						$this->picSet($udata['user_id'], $_FILES['urls']['tmp_name']);
					}
					$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新成功！'));
					$this->session->set_userdata('uinfonotcomplete', false);
					 redirect($this->session->userdata('history_url'));
				} else {
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息不完整,更新失败！'));
					redirect('manage/yiyuan/add');
				}

		}else{
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '手机或者邮箱重复！'));
			redirect('manage/yiyuan/add');
		}
		}
		$data['items'] = $this->db->get('items')->result_array();
		$data['keshi'] = $this->yisheng->getKeShi();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "jigou_add";
		$this->load->view('template', $data);
	}
		/*
	* Function: _encode
	* Modified for WEN_Auth
	*/
	private function _encode($password) {
		$majorsalt =$this->config->item('WEN_salt');

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
	public function update() {
		if ($uid = $this->input->post('uid')) {
			$updateData['name'] = $this->input->post('name');
			
			$updateData['contactN'] = $this->input->post('contactN');
			if ($this->input->post('phone'))
				$updateData['phone'] = trim($this->input->post('phone'));
			if($this->input->post('phone') and ($this->input->post('phone') != $this->input->post('sourcephone')) and !$this->_check_phone_no($updateData['phone']) ){
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '手机号不正确或重复！'));
               redirect('manage/yiyuan/detail/'.$uid);
			}
            if($this->input->post('email') and ($this->input->post('email') != $this->input->post('sourceemail')) and !$this->_check_user_email($this->input->post('email'))){
            	$this->session->set_flashdata('msg', $this->common->flash_message('error', '邮箱不正确或重复！'));
                redirect('manage/yiyuan/detail/'.$uid);
            }
			$updateData['tel'] = $this->input->post('tel');
			$updateData['remark'] = $this->input->post('remark');
			$updateData['rebate'] = $this->input->post('rebate');
			$updateData['coupon'] = $this->input->post('coupon');
			$updateData['web'] = $this->input->post('web');
			$updateData['descrition'] = $this->input->post('descrition');
			$updateData['users'] = $this->input->post('users');
			$updateData['shophours'] = $this->input->post('shophours');
			$updateData['country'] = $this->input->post('country');
			$updateData['province'] = $this->input->post('province');
			$updateData['city'] = $this->input->post('city');
			$updateData['district'] = $this->input->post('district');
			$updateData['team'] = $this->input->post('team');
			if ($updateData['district'] == '') {
				$updateData['district'] = $updateData['city'];
				$updateData['city'] = $updateData['province'];
			}

			$updateData['address'] = $this->input->post('address');
			$updateData['weibo'] = $this->input->post('weibo');

			$this->db->where('userid', $uid);
			$this->db->update('company', $updateData);
			//for user table
			$updateDatas['alias'] = $updateDatas['username'] = $this->input->post('name');
			$updateDatas['utags'] = trim($this->input->post('utags'));
			if (($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')) || $this->input->post('phone') != $this->input->post('sourcephone')) {
				if ($this->input->post('phone')) {
					$updateDatas['phone'] = trim($this->input->post('phone'));
					$updateDatas['rev_phone'] = 0;
				}
			 $updateDatas['sysvotenum'] = $this->input->post('sysvotenum');

			 $updateDatas['utags'] = trim($this->input->post('utags'));
			 $updateDatas['sysgrade'] = $this->input->post('sysgrade');
			 $updateDatas['email'] = trim($this->input->post('email'));

			}
            $updateDatas['sysreplys'] = $this->input->post('sysreplys');

			if($this->input->post('email') and $this->input->post('state')==1){
               $updateDatas['state'] = 1;
		    }
			$this->db->where('id', $uid);
			$this->db->update('users', $updateDatas);


			if ($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')) {
				$updateData['email'] = $this->input->post('sourceemail');
			}
			$items = array_filter($this->input->post('items'));
			if ($items) {
				$condition = array (
					'userid' => $uid
				);
				$this->common->deleteTableData('price', $condition);
				foreach ($items as $k => $v) {
					$data = array (
						'userid' => $uid,
						'item_id' => $k,
						'price' => $v,
						'company_id' => $this->input->post('companyid'
					), 'cdate' => time());
					$this->db->insert('price', $data);
				}
			}
			if($_FILES['thumb']['tmp_name']){
                $this->thumb($uid, $_FILES['thumb']['tmp_name']);
			}
			//echo '<script>window.history.back(-2);</script>';
			 redirect($this->session->userdata('history_url'));
		}
	}
	//delete user
	public function del($uid){
		$condition = array('id'=>$uid);
        $this->common->deleteTableData('users',$condition);
        //profile
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('user_profile',$condition);
         //profile
        $condition = array('userid'=>$uid);
        $this->common->deleteTableData('company',$condition);
        //profile
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('wen_notify',$condition);
        //notify
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('user_notification',$condition);
        //notify
        $condition = array('uid'=>$uid);
        $this->common->deleteTableData('wen_weibo',$condition);
        //notify
        $condition = array('uid'=>$uid);
        $this->common->deleteTableData('wen_follow',$condition);
        //notify
        $condition = array('fid'=>$uid);
        $this->common->deleteTableData('wen_follow',$condition);
        //notify
        $condition = array('fUid'=>$uid);
        $this->common->deleteTableData('wen_questions',$condition);
        //thumb
        $this->deleteDir($this->path . '/users/' . $uid);
        redirect($this->session->userdata('history_url'));
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		switch ($pos) {
			case 1:
			    return $this->remote->thumb($id,'36');
			case 0:
			    return $this->remote->thumb($id,'250');
		    case 2:
                return $this->remote->thumb($id,'120');
			default:
			    return $this->remote->thumb($id,'120');
				break;
		}
	}

	private function thumb($uid, $file) {
		if ($file != '') {
				 $this->remote->uputhumb($file,$uid);
				return true;
			} else {
				return false;
			}
	}
	function _check_phone_no($value) {
		if ($value == '') {
			return TRUE;
		} else {
			if (preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $value)) {
				if ($this->wen_auth->is_phone_available($value)) {
					return preg_replace('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', '($1) $2-$3', $value);
				} else {
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '该手机号码已被使用！'));
					return FALSE;
				}

			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '请输入有效的手机号码！'));
				return FALSE;
			}
		}
	}
	function _check_user_email($email) {
		if ($this->wen_auth->is_email_available($email) && preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $email)) {
			return true;
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '该邮箱已经被使用或者非法！'));
			return false;
		} //If end
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
