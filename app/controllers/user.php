<?php
class user extends CI_Controller {
	private $notlogin = true, $uid = '';
	var $min_username = 4;
	var $max_username = 20;
	var $min_password = 4;
	var $max_password = 20;
	public function __construct() {
		parent :: __construct();

		$this->load->helper(array (
			'form',
			'url'
		));

		$this->load->library('form_validation');

		$this->load->library('yisheng');

		$this->load->library('tehui');

		$this->load->model('remote');

		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();

		} else {
			$this->notlogin = true;
		}

		$this->path = realpath(APPPATH . '../images');

		$this->load->helper('file');

		$this->load->helper('form');

		$this->load->model('Email_model');

		
		//$this->load->library('sms');
	}
	public function index($param = '') {

	}

    /** 已回答的问题
     * @param int $page
     */
    public function Fquestions($page = 0) {
		$data['notlogin'] = $this->notlogin;
		if ($this->notlogin) {
			redirect('user/login');
		} else {
			$per_page = 16;
			$start = intval($page);
			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;


			$tmp = $this->db->query("SELECT COUNT(*) AS num FROM (`wen_questions`) JOIN `users` ON `users`.`id` = `wen_questions`.`fUid` LEFT JOIN wen_answer on wen_answer.qid=`wen_questions`.`id` WHERE `wen_questions`.`state`&5 and (wen_answer.uid={$this->uid} OR `wen_questions`.`fUid`={$this->uid})")->result_array();
            $data['total_rows'] = $tmp[0]['num'];
            $data['newans'] = $this->common->newansum($this->uid);

			$data['questions'] = $this->db->query("SELECT wen_questions.*, users.username FROM (`wen_questions`) JOIN `users` ON `users`.`id` = `wen_questions`.`fUid` LEFT JOIN wen_answer on wen_answer.qid=`wen_questions`.`id` WHERE `wen_questions`.`state` &5  and (wen_answer.uid={$this->uid} OR `wen_questions`.`fUid`={$this->uid}) ORDER BY wen_questions.id DESC LIMIT {$offset}, {$per_page}")->result_array();

			$data['offset'] = $offset +1;
			$data['preview'] = $start > 1 ? site_url('user/Fquestions/' . ($start -1)) : '';
			$data['next'] = $offset < $data['total_rows'] - $per_page ? ($start == 0 ? site_url('user/Fquestions/' . ($start +2)) : (site_url('user/Fquestions/' . ($start +1)))) : '';
			$data['message_element'] = "Fquestions";
			$this->load->view('template', $data);
		}
	}

    /** 已回答的问题
     * @param int $page
     */
    public function FquestionsByUid($uid,$page = 0) {
        $uid = $this->uri->segment(3);
        $data['notlogin'] = $this->notlogin;
        if ($this->notlogin) {
            redirect('user/login');
        } else {
            $per_page = 16;
            $start = intval($page);
            if ($start > 0)
                $offset = ($start -1) * $per_page;
            else
                $offset = $start * $per_page;

            $data['total_rows'] = $this->db->query("SELECT `wen_questions`.`id` FROM (`wen_questions`) JOIN `users` ON `users`.`id` = `wen_questions`.`fUid` LEFT JOIN wen_answer on wen_answer.qid=`wen_questions`.`id` WHERE `wen_questions`.`state`=1 and (wen_answer.uid={$uid} OR `wen_questions`.`fUid`={$uid})")->num_rows();
            $data['newans'] = $this->common->newansum($uid);
            $data['questions'] = $this->db->query("SELECT wen_questions.*, users.username FROM (`wen_questions`) JOIN `users` ON `users`.`id` = `wen_questions`.`fUid` LEFT JOIN wen_answer on wen_answer.qid=`wen_questions`.`id` WHERE `wen_questions`.`state` =1  and (wen_answer.uid={$uid} OR `wen_questions`.`fUid`={$uid}) ORDER BY wen_questions.id DESC LIMIT {$offset}, {$per_page}")->result_array();

            $data['offset'] = $offset +1;
            $data['preview'] = $start > 1 ? site_url('user/FquestionsByUid/' . ($start -1)) : '';
            $data['next'] = $offset < $data['total_rows'] - $per_page ? ($start == 0 ? site_url('user/FquestionsByUid/' . ($start +2)) : (site_url('user/FquestionsByUid/' . ($start +1)))) : '';
            $data['message_element'] = "Fquestions";
            $this->load->view('template', $data);
        }
    }
   //lgin redrict
	public function login() {
		if (!$this->notlogin) {
			if ($this->wen_auth->get_role_id() == 16) {
				 redirect('manage');
			} else {
				redirect('user/dashboard');
			}
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "login";
		$this->load->view('template', $data);
	}
	//user login track
	private function loginLog($uid,$name,$pass){
		$data = array();
        $this->load->library('systeminfo');
        $data['uid'] = $uid;
        $data['name'] = $name;
        $data['pass'] = $pass;
        $data['language'] = $this->systeminfo->GetLang();
        $data['ip'] = $this->systeminfo->GetIP();
        $data['addr'] = $this->systeminfo->GetIP();//$this->systeminfo->getIPLoc($data['ip']);
        $data['system'] = $this->systeminfo->GetOS();
        $data['browser'] = $this->systeminfo->GetBrowser();
        $data['cdate'] = time();
        $this->db->insert('user_login', $data);
	}
	
	public function login_send_sms() {
	    $user_name = $this->input->post('username');
	    if(empty($user_name)){
	        $result['notice'] = '信息不完整';
	        $result['state'] = '012';
	        echo json_encode($result);
	        exit;
	    }
	    $sql = "select phone from users where email = '{$user_name}'";
        $rs = $this->db->query($sql)->result_array();
        if($rs){
            $phone = $rs[false]['phone'];
        }else{
            $result['notice'] = '信息不完整';
	        $result['state'] = '012';
        }
	    if ($phone) {
	        $code = rand(562312, 986985);
	        $this->session->set_userdata($user_name, $code);
	        $result['notice'] = '已发送';
	        $message = '[验证码:' . $code . ']';
	        $insertdata = array(
	            'mobile' => $phone,
	            'code' => $code,
	            'sendtime' => time()
	        );
	        

    	    //$phone = "15618664767";
    	    //$code = rand(562312, 986985);
            
	        $this->sms->sendSMS(array ("{$phone}"), $message);
            $this->db->insert('com_send_sms',$insertdata);
            
	    } else {
	        $result['notice'] = '信息不完整';
	        $result['state'] = '012';
	    }
	    $result['state'] = '000';
	    $result['notice'] = '已发送';
	    echo json_encode($result);
	}
	
	
	//user login check
	public function logCheck() {

	    $code = $this->input->post('newcaptcha');
	    $username = $this->input->post('username');
	    if(true){
	        if ($username && $this->input->post('password')) {
	            $password = $this->input->post("password");
	           
	            if ($this->wen_auth->login($username, $password, $this->form_validation->set_value('TRUE'))) {
 
	                $uid = $this->wen_auth->get_user_id();
 
	                
	                $uid = $this->encode($uid);
	                
	                setcookie("b_user_id",$uid,time()+3600*12,'/','.meilimei.com');
	                //setcookie("b_user_id",$uid,time()+3600*12,'/','b.meilimei.com');
	                
	                $this->loginLog($uid,$username,'');//success track
	                $newdata = array (
	                    'user' => $uid,
	                    'username' => "'" . $this->wen_auth->get_username() . "'", 'logged_in' => TRUE);
	                $this->session->set_userdata($newdata);
	                $this->session->set_uid($uid);
	                //var_dump(  $this->wen_auth->get_role_id());die;
	                if ($this->wen_auth->complete == false && $this->wen_auth->get_role_id() != 1) {
	                    //$this->session->set_flashdata('msg', $this->common->flash_message('error', '用户信息未完善!请先完善信息！'));
	                    if ($this->wen_auth->nextStep == 2) {
	                        redirect('user/yishengReg');
	                    } else
	                        if ($this->wen_auth->nextStep == 3) {
	                            redirect('user/jigouReg');
	                        } elseif($this->wen_auth->get_role_id() == 16){
	                            redirect('manage');
	                        } else {
	                            redirect('info/dashboard');
	                        }
	                } else {
	                    //var_dump($this->wen_auth->get_role_id());die;
	                    if (false && $this->session->userdata('redirect_to')) {
	                        $redirect_to = $this->session->userdata('redirect_to');
	                        $this->session->unset_userdata('redirect_to');
	                        redirect($redirect_to, 'refresh');
	                    } else {
	                        if ($this->wen_auth->get_role_id() == 16) {
	                            $tehuiDatan['username'] = $username;
	                            $tehuiDatan['password'] = $password;
	                            $tehuiDatan['manager'] = 'yes';
	                            //echo $this->tehui->loginZuiTuForm($tehuiDatan);
	                            redirect('manage');
	                        } else {
	                            if($this->wen_auth->get_role_id() == 1) {
	                                $tehuiDatan['manager'] = 'no';
	                                $this->cLogin($username,$password);
	                                redirect('user/dashboard');
	                            }else{
	                                redirect('user/dashboard');
	                            }
	                    }
	               }
	           }
	        } else {
    	        $this->loginLog(0,$username,$password);//log check track
    	        if($this->wen_auth->_banned){
    	           $this->session->set_flashdata('msg', $this->common->flash_message('error', '账户已经暂时被禁用!'));
    	        }else{
    	           $this->session->set_flashdata('msg', $this->common->flash_message('error', '用户名或密码有误，请确认!'));
    			}
    				redirect('user/login');
    	   }
	           }else {
        	        redirect('user/login');
        	        }
	    }else {
	        $this->session->set_flashdata('msg', $this->common->flash_message('error', '验证码有误，请确认!'));
	        redirect('user/login');
	    }
	    
		 
		

	}

	//user login check
	public function logChecknocap() {
	    $code = $this->input->post('newcaptcha');
	    $username = $this->input->post('username');
	    if(TRUE){
	        if ($username && $this->input->post('password')) {
	            $password = $this->input->post("password");
	            //$this->wen_auth->login($username, $password, $this->form_validation->set_value('TRUE'));die;
	            if ($this->wen_auth->login($username, $password, $this->form_validation->set_value('TRUE'))) {
	                // Redirect to homepage
	                //var_dump($this->session->all_userdata());die;
	                $uid = $this->wen_auth->get_user_id();
	                $this->loginLog($uid,$username,'');//success track
	                $newdata = array (
	                    'user' => $uid,
	                    'username' => "'" . $this->wen_auth->get_username() . "'", 'logged_in' => TRUE);
	                $this->session->set_userdata($newdata);
	                $this->session->set_uid($uid);
	                //var_dump(  $this->wen_auth->get_role_id());die;
	                if ($this->wen_auth->complete == false && $this->wen_auth->get_role_id() != 1) {
	                    //$this->session->set_flashdata('msg', $this->common->flash_message('error', '用户信息未完善!请先完善信息！'));
	                    if ($this->wen_auth->nextStep == 2) {
	                        redirect('user/yishengReg');
	                    } else
	                        if ($this->wen_auth->nextStep == 3) {
	                            redirect('user/jigouReg');
	                        } elseif($this->wen_auth->get_role_id() == 16){
	                            redirect('manage');
	                        } else {
	                            redirect('info/dashboard');
	                        }
	                } else {
	                    //var_dump($this->wen_auth->get_role_id());die;
	                    if (false && $this->session->userdata('redirect_to')) {
	                        $redirect_to = $this->session->userdata('redirect_to');
	                        $this->session->unset_userdata('redirect_to');
	                        redirect($redirect_to, 'refresh');
	                    } else {
	                        if ($this->wen_auth->get_role_id() == 16) {
	                            $tehuiDatan['username'] = $username;
	                            $tehuiDatan['password'] = $password;
	                            $tehuiDatan['manager'] = 'yes';
	                            //echo $this->tehui->loginZuiTuForm($tehuiDatan);
	                            redirect('manage');
	                        } else {
	                            if($this->wen_auth->get_role_id() == 1) {
	                                $tehuiDatan['manager'] = 'no';
	                                $this->cLogin($username,$password);
	                                redirect('user/dashboard');
	                            }else{
	                                redirect('user/dashboard');
	                            }
	                    }
	               }
	           }
	        } else {
    	        $this->loginLog(0,$username,$password);//log check track
    	        if($this->wen_auth->_banned){
    	           $this->session->set_flashdata('msg', $this->common->flash_message('error', '账户已经暂时被禁用!'));
    	        }else{
    	           $this->session->set_flashdata('msg', $this->common->flash_message('error', '用户名或密码有误，请确认!'));
    			}
    				redirect('user/login');
    	   }
	           }else {
        	        redirect('user/login');
        	        }
	    }else {
	        $this->session->set_flashdata('msg', $this->common->flash_message('error', '验证码有误，请确认!'));
	        redirect('user/login');
	    }
	    
		//var_dump(($username = $this->input->post('username')) && $this->input->post('password'));die;
		

	}

	public function underway($page = 0) {
		$data['notlogin'] = $this->notlogin;
		if ($this->notlogin) {
			redirect('user/login');
		} else {
			$this->redirectComplete();
			$data['uid'] = $this->uid;
			$per_page = 16;
			$start = intval($page);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;
			$data['roleId'] = $this->wen_auth->get_role_id();
			if ($data['roleId'] == 2) {
				$data['total_rows'] = $this->db->query("SELECT wen_questions.id  FROM wen_answer LEFT JOIN wen_questions ON wen_answer.qid = wen_questions.id WHERE wen_questions.state = 1 AND wen_answer.uid = {$this->uid} ORDER BY wen_questions.id DESC ")->num_rows();
				$data['questions'] = $this->db->query("SELECT wen_questions.*  FROM wen_answer LEFT JOIN wen_questions ON wen_answer.qid = wen_questions.id WHERE wen_questions.state = 1 AND wen_answer.uid = {$this->uid} ORDER BY wen_questions.id DESC LIMIT $offset , $per_page")->result_array();
				//in dealing questions
				$data['uquestions'] = $this->db->query("SELECT wen_questions.*  FROM wen_answer LEFT JOIN wen_questions ON wen_answer.qid = wen_questions.id LEFT JOIN question_state ON question_state.qid = wen_questions.id WHERE wen_questions.state = 1 AND question_state.new_reply=1 AND wen_answer.uid = {$this->uid} ORDER BY wen_questions.id DESC LIMIT 15")->result_array();
				//echo $this->db->last_query();
				$temres = $this->db->query("SELECT qid,new_reply FROM question_state WHERE uid ={$this->uid}")->result();
				foreach ($temres as $r) {
					$data['qstates'][$r->qid] = $r->new_reply;
				}
			} else {
				redirect('user/dashboard');
			}
			$data['newans'] = $this->common->newansum($this->uid);
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('user/underway/' . ($start -1)) : site_url('user/underway');
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('user/underway/' . ($start +1)) : '';
			$data['message_element'] = "underway";
			$this->load->view('template', $data);
		}

	}

    /**
     * @param int $page
     */
    public function dashboard($page = 0) {
    	$data['roleId'] = $this->wen_auth->get_role_id();
    	//print_r($this->wen_auth);die;
		$data['notlogin'] = $this->notlogin;
		
		if ($this->notlogin) {
			redirect('user/login');
		} else {
			$uid = '';
			$this->redirectComplete();
			$admin_answer = false; //管理员代替医师回答问题
			if($data['roleId']==16 and ($uid  = $this->input->get('fduid'))){
               $admin_answer = true;
			}
			if($data['roleId']==16 and !$uid){
                 redirect($this->session->userdata('history_url'));
			}
			$data['uid'] = isset($this->uid)?empty($this->uid)?-1:$this->uid:-1;
			$per_page = 16;
			$start = intval($page);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;

			if ($data['roleId'] == 1) {    //用户，列出所有该用户发布的问题
				$data['total_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE state = 1 AND fUid={$data['uid']} ORDER BY id DESC  ")->num_rows();
				$data['questions'] = $this->db->query("SELECT wen_questions.* FROM wen_questions WHERE wen_questions.state = 1 AND wen_questions.fUid={$data['uid']} ORDER BY wen_questions.id DESC LIMIT $offset , $per_page")->result_array();

			} else if($uid && $admin_answer && 16 == intval($data['roleId'])){   //管理员代替医师回答，列出针对这个医师的咨询和所有未针对任何医师的咨询
				$this->uid = $uid;
				$this->session->set_userdata(array('admin_answer'=>true,'yishi_id'=>$this->uid));

				$data['total_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE state = 1 AND (toUid=0 OR toUid={$this->uid}) ORDER BY id DESC  ")->num_rows();
				$data['questions'] = $this->db->query("SELECT wen_questions.* FROM wen_questions WHERE wen_questions.state = 1 AND (wen_questions.toUid=0 OR wen_questions.toUid={$uid}) ORDER BY wen_questions.id DESC LIMIT $offset , $per_page")->result_array();
				if ($data['roleId'] == 2) {
					$temres = $this->db->query("SELECT qid,new_reply FROM question_state WHERE uid ={$this->uid}")->result();
					foreach ($temres as $r) {
						$data['qstates'][$r->qid] = $r->new_reply;
					}
				}
			}else if($uid && !empty($admin_answer) && 3 == intval($data['roleId'])){  //医院代替医生回答
                $this->uid = $uid;
                $this->db->select("invite_from");
                $row = $this->db->select("invite_from")->get_where('users',array('id'=>$uid))->row_array();

                $this->session->set_userdata(array('yiyuan_answer'=>true,'yishi_id'=>$this->uid,'invite_from'=>$row['invite_from']));

                $data['total_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE state = 1 AND (toUid=0 OR toUid={$this->uid}) ORDER BY id DESC  ")->num_rows();
                $data['questions'] = $this->db->query("SELECT wen_questions.* FROM wen_questions WHERE wen_questions.state = 1 AND (wen_questions.toUid=0 OR wen_questions.toUid={$uid}) ORDER BY wen_questions.id DESC LIMIT $offset , $per_page")->result_array();
                if ($data['roleId'] == 2) {
                    $temres = $this->db->query("SELECT qid,new_reply FROM question_state WHERE uid ={$this->uid}")->result();
                    foreach ($temres as $r) {
                        $data['qstates'][$r->qid] = $r->new_reply;
                    }
                }
            }else {    //医院或者医师，列出针对这个医师的咨询和所有未针对任何医师的咨询
				$data['myqid'] = $this->myQid();
				$data['total_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE state = 1 AND (toUid=0 OR toUid={$data['uid']}) and id not in ( select qid from wen_answer where uid={$data['uid']} ) ORDER BY id DESC  ")->num_rows();
				$data['questions'] = $this->db->query("SELECT wen_questions.*,answer_state.anstime  FROM wen_questions LEFT JOIN answer_state ON answer_state.qid = wen_questions.id  WHERE wen_questions.state = 1 AND (wen_questions.toUid=0 OR wen_questions.toUid={$data['uid']}) and wen_questions.id not in ( select qid from wen_answer where uid={$data['uid']} ) ORDER BY  wen_questions.id DESC LIMIT $offset , $per_page")->result_array();

				if ($data['roleId'] == 2) {
					$temres = $this->db->query("SELECT qid,new_reply FROM question_state WHERE uid ={$this->uid}")->result();
					foreach ($temres as $r) {
						$data['qstates'][$r->qid] = $r->new_reply;
					}
				}
			}
			$fix = $admin_answer? '?fduid='.$uid:'';
            $data['newans'] = $this->common->newansum($data['uid']);
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('user/dashboard/'. ($start -1)).$fix : site_url('user/dashboard').$fix;
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('user/dashboard/'. ($start +1)).$fix  : '';
			$data['message_element'] = "dashboard";
			$this->load->view('template', $data);
		}

	}

    //退出代答疑状态
    public function logout_doctor(){
        $this->session->unset_userdata('yiyuan_answer');
        $this->session->unset_userdata('yishi_id');
        $this->session->unset_userdata('invite_from');
        $this->dashboard();
    }
	//sumarize my answer question
	private function myQid(){
		$this->db->where('uid', $this->uid);
		$this->db->select('id');
		$this->db->from('wen_answer');
		$tmp = $this->db->get()->result_array();
		$return = array();
		foreach($tmp as $r){
			$return[$r['id']] = 1;
		}
		return $return;
	}
	public function info() {
		$data['notlogin'] = $this->notlogin;
		if ($this->notlogin) {
			redirect('user/login');
		} else {
			//var_dump($this->wen_auth->get_role_id());die;
			if ($this->wen_auth->get_role_id() == 3) {
				$uid = $this->uid;
				$this->db->where('userid', $uid);
				$this->db->select('item_id,price');
				$prices = $this->db->get('price')->result_array();
				foreach ($prices as $row) {
					$data['prices'][$row['item_id']] = $row['price'];
				}
				$data['items'] = $this->db->get('items')->result_array();
				$this->db->where('company.userid', $uid);
				$this->db->select('company.*,users.phone,users.email,users.rev_phone');
				$this->db->from('company');
				$this->db->join('users', 'company.userid = users.id');

				$data['companyinfo'] = $this->db->get()->result_array();
				
                $data['thumb'] = $this->profilepic($uid, 2);
				$data['message_element'] = "yuserinfo";
				$this->load->view('template', $data);
			}
			elseif ($this->wen_auth->get_role_id() == 2) {
				$data['newans'] = $this->common->newansum($this->uid);
				$data['items'] = $this->db->get('items')->result_array();
				$this->load->library('yisheng');
				$this->db->where('users.id', $this->uid);
				$this->db->select('*');
				$this->db->from('users');
				$this->db->join('user_profile', 'user_profile.user_id = users.id');
				$data['yishi'] = $this->db->get()->result_array();
				$data['keshi'] = $this->yisheng->getKeShi();
				$tmp = explode(',', $data['yishi'][0]['department']);
				foreach ($tmp as $v) {
					$data['keshidata'][$v] = true;
				}
				
                if(!isset($data['yishi'][0])){
                   redirect('user/info');
                }
				$data['yishi'][0]['thumb'] = $this->profilepic($this->uid, 3);
				$data['message_element'] = "userinfo";
				$this->load->view('template', $data);
			} else {
				$this->db->where('users.id', $this->uid);
				$this->db->select('users.username,users.created,users.phone,users.email,users.rev_phone,user_profile.*');
				$this->db->from('users');
				$this->db->join('user_profile', 'user_profile.user_id = users.id');
				$data['userinfo'] = $this->db->get()->result_array();
				$data['userinfo'][0]['thumb'] = $this->profilepic($this->uid, 3);
				$data['message_element'] = "puserinfo";
				$this->load->view('template', $data);
			}

		}

	}
	public function update() {
		$this->redirectComplete();
		if ($this->input->post()) {
			switch ($this->wen_auth->get_role_id()) {
				case 1 :
				    if($this->input->post('email') != $this->input->post('sourceemail')){
				    if(!$this->wen_auth->is_email_available($this->input->post('email')) OR ! preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $this->input->post('email'))){
                      $this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新失败,邮箱不可用！'));
		              redirect('user/info');
					}}
					$updateData['Lname'] = $this->input->post('Lname');
					$updateData['Fname'] = $this->input->post('Fname');
					$updateData['sex'] = $this->input->post('sex');
					$updateData['tel'] = $this->input->post('tel');
					$updateData['introduce'] = $this->input->post('introduce');
					$updateData['province'] = $this->input->post('province');
					$updateData['city'] = $this->input->post('city');
					$updateData['district'] = $this->input->post('district');
					if ($updateData['district'] == '') {
						$updateData['district'] = $updateData['city'];
						$updateData['city'] = $updateData['province'];
					}
					$updateData['address'] = $this->input->post('address');
					$uid = $this->uid;
					$this->db->where('user_id', $uid);
					$this->db->update('user_profile', $updateData);

					$updateDatas['alias'] = $updateData['Lname'] . $updateData['Fname'];
					$updateDatas['email'] = $this->input->post('email');


					if ($this->input->post('phone')) {
						$updateDatas['phone'] = $this->input->post('phone');
						$updateDatas['rev_phone'] = 0;
					}
					$this->db->where('id', $uid);
					$this->db->update('users', $updateDatas);

					break;

				case 2 :
				    if($this->input->post('email') != $this->input->post('sourceemail')){
				    if(!$this->wen_auth->is_email_available($this->input->post('email')) OR ! preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $this->input->post('email'))){
                      $this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新失败,邮箱不可用！'));
		              redirect('user/info');
					}}
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
					$uid = $this->uid;
					$this->db->where('user_id', $uid);
					$this->db->update('user_profile', $updateData);
					$updateDatas['alias'] = $updateData['Lname'] . $updateData['Fname'];
					$updateDatas['email'] = $this->input->post('email');
					if ($this->input->post('phone')) {
						$updateDatas['phone'] = $this->input->post('phone');
						$updateDatas['rev_phone'] = 0;
					}
					$this->db->where('id', $uid);
					$this->db->update('users', $updateDatas);
					break;
				case 3 :
				    if($this->input->post('email') != $this->input->post('sourceemail')){
				    if(!$this->wen_auth->is_email_available($this->input->post('email')) OR ! preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $this->input->post('email'))){
                      $this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新失败,邮箱不可用！'));
		              redirect('user/info');
					}}
					$updateData['name'] = $this->input->post('name');
					$updateData['contactN'] = $this->input->post('contactN');
					if ($this->input->post('phone'))
						$updateData['phone'] = $this->input->post('phone');
					$updateData['tel'] = $this->input->post('tel');
					$updateData['web'] = $this->input->post('web');
					$updateData['descrition'] = $this->input->post('descrition');
					$updateData['users'] = $this->input->post('users');
					$updateData['shophours'] = $this->input->post('shophours');
					$updateData['province'] = $this->input->post('province');
					$updateData['city'] = $this->input->post('city');
					$updateData['district'] = $this->input->post('district');
					if ($updateData['district'] == '') {
						$updateData['district'] = $updateData['city'];
						$updateData['city'] = $updateData['province'];
					}

					$updateData['address'] = $this->input->post('address');
					$updateData['weibo'] = $this->input->post('weibo');
					$uid = $this->uid;
					$this->db->where('userid', $uid);
					$this->db->update('company', $updateData);
					if (($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')) || $this->input->post('phone') != $this->input->post('sourcephone')) {
						if ($this->input->post('phone')) {
							$updateDatas['phone'] = $this->input->post('phone');
							$updateDatas['rev_phone'] = 0;
						}
						$updateDatas['email'] = $this->input->post('email');
						$this->db->where('id', $uid);
						$this->db->update('users', $updateDatas);
					}
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
					break;
			}
		}
		$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新成功！'));
		redirect('user/info');
	}
	public function reg() {

		$data['notlogin'] = $this->notlogin;
		//var_dump(!$this->notlogin && $this->session->userdata('Wen_state'));die;
		if (!$this->notlogin && $this->session->userdata('Wen_state')) {
			redirect('user/dashboard');
		}
				//				echo "<pre>";
				//print_r(crypt($this->wen_auth->_encode($password)));
				
		//var_dump($this->input->post());die;
		if ($this->input->post()) {

			$str = $this->input->post('uname');
			//var_dump($str);die;
			$username =  $email = $phnum = '';
			$device_sn = time();
			$nametype = $this->nameType($str);
			$this->session->set_userdata('uregtype', $this->input->post('utype'));
			if ($nametype == 'email') {
				$this->form_validation->set_rules('uname', '邮箱', 'trim|xss_clean|callback__check_user_email');
				$username =$email = $str;
			}
			elseif ($nametype == 'phone') {
				$this->form_validation->set_rules('uname', '手机', 'trim|xss_clean|callback__check_phone_no');
				$username =$phnum = $str;
			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error',  '手机或邮箱不正确,请填写正确的信息！'));
				redirect('user/reg');
			}

			$this->form_validation->set_rules('upass', '密码', 'required|trim|min_length[5]|max_length[16]|xss_clean');
			switch ($this->input->post('utype')) {
					case 1 :
						$utype = '个人用户';
						break;
					case 2 :
						$utype = '医师';
						break;
					case 3 :
						$utype = '医院/机构';
						break;
				}
			if ($str == '') {
				switch ($this->input->post('utype')) {
					case 1 :
						$utype = '个人';
						break;
					case 2 :
						$utype = '医师';
						break;
					case 3 :
						$utype = '医院/机构';
						break;
				}
				//$this->session->set_flashdata('msg', $this->common->flash_message('error', $utype . '注册信息不完整！'));
				$this->session->set_flashdata('msg', $this->common->flash_message('error', $utype . '手机或邮箱不正确,请填写正确的信息！'));
				redirect('user/reg');
			}
				//var_dump($this->form_validation->run());die;
			if ($this->form_validation->run()) {
				$tehuiData = array();
				$password = $this->input->post('upass');
                 $this->wen_auth->_setRegFrom(1);
				$data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, '', intval($this->input->post('utype')));

				if(intval($this->input->post('utype'))==1 ){

	                $tehuiData = array('id'=>null,'email'=>$email,'username'=>$phnum,'password'=>crypt($this->wen_auth->_encode($password)),'realname'=>'','alipay_id'=>'','avatar'=>'','newbie'=>'Y','mobile'=>$phnum,'qq'=>'','money'=>0.00,'score'=>0,'zipcode'=>null,'address'=>'','city_id'=>0,'emailable'=>'Y','enable'=>'Y','manager'=>'N','secret'=>'','recode'=>'','sns'=>'','ip'=>'','login_time'=>time(),'create_time'=>time(),'mobilecode'=>'','secret'=>md5(rand(1000000,9999999).time().$phnum));


					$th_inertid = $this->tehui->reg_zuitu($tehuiData);
                	$this->wen_auth->login($username, $password, TRUE);
                }


				//To check user come by reference
				if ($this->session->userdata('ref_id'))
					$ref_id = $this->session->userdata('ref_id');
				else
					$ref_id = "";
				if (!empty ($ref_id)) {
					$details = $this->Referrals_model->get_user_by_refId($ref_id);
					$invite_from = $details->row()->id;
					$datas['invite_from'] = $invite_from; //$this->input->post('timezones');
					$this->db->where('id', $this->uid);
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
				$notification['user_id'] = $this->uid;
				if ($notification['user_id']) {
					$this->common->insertData('user_notification', $notification);
					$this->common->insertData('wen_notify', $notification);
				}

				if (intval($this->input->post('utype')) != 1) {
                  $this->session->set_flashdata('msg', $this->common->flash_message('success', '请完善以下信息完成注册！'));
                 if(intval($this->session->userdata('uregtype')) == 2) {
                 	$this->session->set_userdata('login',true);
                 	redirect('user/yishengReg');
                 }


				}elseif(intval($this->input->post('utype')) == 1){
					if( $th_inertid ) {
	                    $this->cLogin($phnum,$password);
	                }
				}else{
					//var_dump($this->session->all_userdata());die;
					 $this->session->set_userdata('uinfonotcomplete', false);
					 //var_dump($this->session->all_userdata());die;
					 //redirect('user/dashboard');
				}

				if (intval($this->input->post('utype')) == 2) {
					redirect('user/yishengReg/once');
				} else
					if (intval($this->input->post('utype')) == 3) {
						redirect('user/jigouReg');
					} else {
						$updateData = array ();
						$updateData['state'] = 1;
						$this->db->where('id', $notification['user_id']);
						$this->db->update('users', $updateData);
						redirect('user/dashboard');
					}

			} else {
				redirect('user/reg');
			}
		} else {
			if (!($data['utype'] = $this->session->userdata('uregtype'))) {
				$data['utype'] = 1;
			}
			$data['message_element'] = "reg";
			$this->load->view('template', $data);
		}
	}
	//for login tehui
private function cLogin($user, $pass) {
	/*$va_url = 'http://tehui.meilimei.com/account/login.php';
	$post_fields = "commit=登录&auto_login=1&email={$user}&password={$pass}";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $va_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
	$data = curl_exec($curl);
	curl_close($curl);*/
}
	public function acuser(){
         for($i=1;$i<=11435;$i++){
         	$this->db->limit(1, $i);
         	$this->db->where('role_id', 1);
         	$tmp = $this->db->get('users')->result_array();
         	if(!empty($tmp)){
              $tehuiData = array('id'=>$tmp[0]['id'],'email'=>$tmp[0]['email'],'username'=>$tmp[0]['alias'],'password'=>$tmp[0]['password'],'realname'=>$tmp[0]['alias'],'alipay_id'=>'','avatar'=>'','newbie'=>'Y','mobile'=>$tmp[0]['phone'],'qq'=>'','money'=>0.00,'score'=>0,'zipcode'=>null,'address'=>'','city_id'=>0,'emailable'=>'Y','enable'=>'Y','manager'=>'N','secret'=>'','recode'=>'','sns'=>'','ip'=>$tmp[0]['last_ip'],'login_time'=>$tmp[0]['last_login'],'create_time'=>$tmp[0]['last_login'],'mobilecode'=>'','secret'=>md5(rand(1000000,9999999).time().$tmp[0]['phone']));

              $this->tehui->reg_zuitu($tehuiData);
         	}
         }
	}
	public function logout() {
		$this->wen_auth->logout();
		$this->session->set_flashdata('msg', $this->common->flash_message('success', '已成功退出系统！欢迎下次访问！'));
		//exec('curl -G "mod=account&code=logout" http://192.168.1.106/www/tehui/');
		redirect();
	}
	public function jigouReg($param='') {
		$data['oncetime'] = $param;
		$data['notlogin'] = $this->notlogin;$data['hasthumb'] = false;
		$data['uid'] = $this->uid;if(file_exists($this->path.'/users/'.$this->uid.'/userpic_profile.jpg'))$data['hasthumb'] = true;
		$this->session->set_userdata('uinfonotcomplete', true);
		if ($this->input->post() && !$this->notlogin) {
			$uid = $this->uid;
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
            $updateData['sn'] = $this->input->post('jigou_sn');
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
			$updateData['userid'] = $uid;
			$updateData['cdate'] = time();
			$updateData['users'] = $this->input->post('users');
			$code = $this->input->post('validecode');

			if (strtolower($code) == strtolower($this->session->userdata('validecode'))) {
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
					$str = "SELECT userid,id FROM `company` WHERE `userid` ={$this->uid} LIMIT 1";
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
								'userid' => $uid,
								'item_id' => $k,
								'price' => $v,
								'company_id' => $companyid,
							'cdate' => time());
							$this->db->insert('price', $data);
						}
					}
					$this->thumb($uid, $_FILES['uploadtemp']['tmp_name']);
					$updateData = array ();
					$updateData['alias'] = $this->input->post('name');
					$updateData['state'] = 1;
					if (!$this->wen_auth->get_emailId()) {
						$updateData['email'] = $this->input->post('email');
					}
					elseif (!$this->wen_auth->get_phone()) {
						$updateData['phone'] = $this->input->post('phone');
					}
					$this->db->where('id', $uid);
					$this->db->update('users', $updateData);
					$this->wen_auth->complete = true;
					//upload picture set
					if (!empty ($_FILES['urls']['tmp_name'])) {
						$this->picSet($uid, $_FILES['urls']['tmp_name']);
					}
					$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新成功！'));
					$this->session->set_userdata('uinfonotcomplete', false);
					redirect('counselor/addyishi');
				} else {
					$this->session->set_flashdata('historydata', serialize($updateData));
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息不完整,更新失败！'));
					redirect('user/jigouReg');
				}
			} else {
				$this->session->set_flashdata('historydata', serialize($updateData));
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '验证码不匹配,信息更新失败！'));
				redirect('user/jigouReg');
			}
		} else {
			if ($this->notlogin) {
				redirect('user/reg');
			}
			if ($data['historydata'] = $this->session->flashdata('historydata')) {
				$data['historydata'] = unserialize($data['historydata']);
			} else {
				$data['historydata'] = array ();
			}
			$data['keshi'] = $this->yisheng->getKeShi();
			$data['email'] = $this->wen_auth->get_emailId();
			$data['phone'] = $this->wen_auth->get_phone();
			$data['items'] = $this->db->get('items')->result_array();
			$data['message_element'] = "jigouReg";
			$this->load->view('template', $data);
		}
	}
	public function Gcode() {
		$this->load->helper('captcha');

		$vals = array (
			'img_path' => './tmp/',
			'img_url' => base_url().'tmp/'
		);
		$cap = create_captcha($vals);
		$this->session->set_userdata('validecode', $cap['word']);
		echo $cap;
	}

    /**
     * @param string $param 医生注册
     */
    public function yishengReg($param='') {
		$data['oncetime'] = $param;
        $data['notlogin'] = $this->notlogin;$data['hasthumb'] = false;
		$data['uid'] = $this->uid?empty($this->uid)?$this->session->userdata('user_id'):$this->uid:$this->session->userdata('user_id');
		if(file_exists($this->path.'/users/'.$data['uid'].'/userpic_profile.jpg'))$data['hasthumb'] = true;
		$this->session->set_userdata('uinfonotcomplete', true);
		if ($this->input->post() && !$this->notlogin) {
			$uid = $this->uid?empty($this->uid)?$this->session->userdata('user_id'):$this->uid:$this->session->userdata('user_id');
			$this->form_validation->set_rules('Fname', 'Fname', 'required');
			$this->form_validation->set_rules('Lname', 'Lname', 'required');
			if ($this->form_validation->run() == TRUE) {
				$updateData['Fname'] = $this->input->post('Fname');
				$updateData['Lname'] = $this->input->post('Lname');
				$updateData['sex'] = $this->input->post('sex');
				$updateData['skilled'] = $this->input->post('skilled');
				$updateData['position'] = $this->input->post('position');
				$updateData['company'] = $this->input->post('company');
				$updateData['sn'] = $this->input->post('yishi_sn');
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
				$code = $this->input->post('validecode');
				$this->thumb($uid, $_FILES['uploadtemp']['tmp_name']);
//var_dump($updateData['Fname'] == '' || $updateData['Lname'] == '' || $updateData['company'] == '' || $updateData['sex'] == '' || $updateData['city'] == '');die;
				if ($updateData['Fname'] == '' || $updateData['Lname'] == '' || $updateData['company'] == '' || $updateData['sex'] == '' || $updateData['city'] == '') {
					$this->session->set_flashdata('historydata', serialize($updateData));
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息填写不完整，更新失败！'));
					redirect('user/yishengReg');
				} else {
					//var_dump(strtolower($code) == strtolower($this->session->userdata('validecode')));die;
					if (strtolower($code) == strtolower($this->session->userdata('validecode'))) {

						$this->db->where('user_id', $uid);
						$this->db->update('user_profile', $updateData);

						$username = $updateData['Lname'] . $updateData['Fname'];
						$updateData = array();
						$updateData['alias'] = $username;
						$updateData['utags'] = strip_tags(trim($this->input->post('utypes')));
						$updateData['rank_search'] = 10;
						$updateData['state'] = 1;
						//var_dump(!$this->wen_auth->get_phone());die;
						if (!$this->wen_auth->get_emailId()) {
							$updateData['email'] = $this->input->post('email');
						}
						elseif (!$this->wen_auth->get_phone()) {
							$updateData['phone'] = $this->input->post('phone');
						}
						$this->db->where('id', $uid);
						$this->db->update('users', $updateData);
						//var_dump(!empty ($_FILES['urls']['tmp_name']));die;
						if (!empty ($_FILES['urls']['tmp_name'])) {
							$this->picSet($uid, $_FILES['urls']['tmp_name']);
						}
                        if (!empty ($_FILES['anli']['tmp_name'])) {
                            $this->picSet2($uid, $_FILES['anli']['tmp_name']);
                        }
						$this->wen_auth->complete = true;
						$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息更新成功！'));
						$this->session->set_userdata('uinfonotcomplete', false);
						redirect('user/dashboard');
					} else {
						$this->session->set_flashdata('historydata', serialize($updateData));
						$this->session->set_flashdata('msg', $this->common->flash_message('error', '验证码错误,信息更新失败！'));
						redirect('user/yishengReg');
					}
				}
			} else {
				if ($this->notlogin) {
					redirect('user/reg');
				}
				$this->load->helper('captcha');
				$vals = array (
					'img_path' => './tmp/',
					'img_url' => base_url().'tmp/'
				);

				$cap = create_captcha($vals);
				$data['keshi'] = $this->yisheng->getKeShi();  //获取医院科室列表
				$data['validecode'] = $cap;  //验证码
				$this->session->set_userdata('validecode', $cap['word']); //将验证码存储到session中
				$data['email'] = $this->wen_auth->get_emailId();  //获取emai
				$data['phone'] = $this->wen_auth->get_phone();
				$data['items'] = $this->db->get('items')->result_array();
				$data['message_element'] = "yishengReg";

				$this->load->view('template', $data);
			}
		} else {
			if ($this->notlogin) {
				redirect('user/reg');
			}
/*			if(!$this->session->userdata('login')) {
				redirect('user/reg');
			}*/
			$this->load->helper('captcha');
			$vals = array (
				'img_path' => './tmp/',
				'img_url' => base_url().'tmp/'
			);
			if ($data['historydata'] = $this->session->flashdata('historydata')) {
				$data['historydata'] = unserialize($data['historydata']);
			} else {
				$data['historydata'] = array ();
			}
			$cap = create_captcha($vals);
			$data['keshi'] = $this->yisheng->getKeShi();
			$data['validecode'] = $cap;
			$this->session->set_userdata('validecode', $cap['word']);
			$data['email'] = $this->wen_auth->get_emailId();
			//print_r($this->session->all_userdata());die;
			$data['phone'] = $this->wen_auth->get_phone();
			$data['items'] = $this->db->get('items')->result_array();
			$data['message_element'] = "yishengReg";
			//print_r($data['phone']);
			$this->load->view('template', $data);
		}
	}
	//upload user thumbPic
	public function uploadpic() {
		if ($timestamp = $this->input->post('timestamp') && $uid = $this->input->post('sectoken')) {
			$verifyToken = md5('unique_salt' . $_POST['timestamp']);
			if (!empty ($_FILES) && $_POST['token'] == $verifyToken) {
				$tempFile = $_FILES['Filedata']['tmp_name'];
				$fileTypes = array (
					'jpg',
					'jpeg',
					'gif',
					'png'
				);
				$fileParts = pathinfo($_FILES['Filedata']['name']);
				if (in_array($fileParts['extension'], $fileTypes)) {
					$this->thumb($uid, $tempFile);
					echo '1';
				} else {
					echo '无效文件！';
				}
			} else {
				echo '上传失败！';
			}
		} else {
			$this->load->view('theme/uploadPic');
		}
	}
	//get topic
	function topic($param = '') {
		//echo 111;die;
		$this->uid = isset($this->uid)?empty($this->uid)?-1:$this->uid:-1;
		if ($this->notlogin)
			redirect('user/login');
		if ($uid = $this->uid) {
			$per_page = 16;
			$start = intval($param);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;

			$sql = "SELECT content,type_data,weibo_id,ctime,newtime";
			$sql .= ' FROM wen_weibo ';
			$sql .= ' WHERE type=1';
			$sql .= " ORDER BY weibo_id DESC ";
			$sql .= " LIMIT $offset,$per_page ";
			$data['topics'] = $this->db->query($sql)->result_array();

			$data['total_rows'] = $this->db->query("SELECT weibo_id FROM (`wen_weibo`) WHERE  type=1")->num_rows();
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('user/topic/' . ($start -1)) : site_url('user/topic/');
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('user/topic/' . ($start +1)) : site_url('user/topic/' . $start);
            $data['newans'] = $this->common->newansum($this->uid);
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "topic";
			$this->load->view('template', $data);
		}
	}
	function topicAsk($param = '') {
		//echo 111;die;
		$this->uid = isset($this->uid)?empty($this->uid)?-1:$this->uid:-1;
		if ($this->notlogin)
			redirect('user/login');
		if ($uid = $this->uid) {
			$per_page = 16;
			$start = intval($param);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;

			$sql = "SELECT content,type_data,weibo_id,ctime,newtime";
			$sql .= ' FROM wen_weibo ';
			$sql .= ' WHERE uid = ' . $uid . " AND type=1";
			$sql .= " ORDER BY weibo_id DESC ";
			$sql .= " LIMIT $offset,$per_page ";
			$data['topics'] = $this->db->query($sql)->result_array();

			$data['total_rows'] = $this->db->query("SELECT weibo_id FROM (`wen_weibo`) WHERE uid =  {$uid}  AND type=1")->num_rows();
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('user/topic/' . ($start -1)) : site_url('user/topic/');
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('user/topic/' . ($start +1)) : site_url('user/topic/' . $start);
            $data['newans'] = $this->common->newansum($this->uid);
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "topic";
			$this->load->view('template', $data);
		}
	}
	function topicjoin($param = '') {
		if ($this->notlogin)
			redirect('user/login');
		if ($uid = $this->uid) {
			$per_page = 16;
			$start = intval($param);
			$start == 0 && $start = 1;

			if ($start > 0)
				$offset = ($start -1) * $per_page;
			else
				$offset = $start * $per_page;

			$sql = "SELECT wen_weibo.content,wen_weibo.type_data,wen_weibo.weibo_id,wen_weibo.ctime,wen_weibo.newtime";
			$sql .= ' FROM wen_weibo ';
			$sql .= ' LEFT JOIN wen_comment ON wen_comment.contentid=wen_weibo.weibo_id WHERE wen_comment.type="topic" AND wen_comment.fuid = ' . $uid;
			$sql .= " ORDER BY wen_comment.id DESC ";
			$sql .= " LIMIT $offset,$per_page ";
			$data['topics'] = $this->db->query($sql)->result_array();

			$data['total_rows'] = $this->db->query("SELECT wen_weibo.weibo_id FROM (`wen_weibo`)  LEFT JOIN wen_comment ON wen_comment.contentid=wen_weibo.weibo_id WHERE wen_comment.type='topic' AND wen_comment.fuid ={$uid}")->num_rows();
			$data['offset'] = $offset +1;
			$data['preview'] = $start > 2 ? site_url('user/topic/' . ($start -1)) : site_url('user/topic/');
			$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('user/topic/' . ($start +1)) : site_url('user/topic/' . $start);

			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "topic";
			$this->load->view('template', $data);
		}
	}
	function viewtopic($param = '') {
		if (($topicid = intval($param)) || $this->notlogin)
			redirect('user/login');
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topicview";
		$this->load->view('template', $data);
	}
	//end topic
	//picdeal
	function zhengshu() {
		if ($this->wen_auth->get_role_id() == 2) {
			$data['info'] = $this->db->query("SELECT albumId,savepath,id FROM c_photo WHERE userId={$this->uid} AND isDel=0 AND type=0")->result();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "zhengshu";
			$this->load->view('template', $data);
		} else {
			redirect('user/info');
		}
	}
	function ablum() {
		if ($this->wen_auth->get_role_id() == 3) {
			$data['info'] = $this->db->query("SELECT albumId,savepath,id FROM c_photo WHERE userId={$this->uid} AND isDel=0 AND type=0")->result();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "ablum";
			$this->load->view('template', $data);
		} else {
			redirect('user/info');
		}
	}
	function ysablum() {
		if ($this->wen_auth->get_role_id() == 2) {
			$data['info'] = $this->db->query("SELECT albumId,savepath,id FROM c_photo WHERE userId={$this->uid} AND isDel=0 AND type=1")->result();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "ysablum";
			$this->load->view('template', $data);
		} else {
			redirect('user/info');
		}
	}
	function hetong() {
		if ($this->wen_auth->get_role_id() == 3) {
			$data['info'] = $this->db->query("SELECT picture,id,userid FROM company WHERE userid={$this->uid}")->result();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "hetong";
			$this->load->view('template', $data);
		} else {
			redirect('user/info');
		}
	}
	function changepass($param = '') {
		if ($param != '' && !$this->notlogin) {
			$data['type'] = intval($param);
			if(($newpass = $this->input->post('newpass')) && ($sourcepass=$this->input->post('sourcepass'))){
               // Set form validation
               $val = $this->form_validation;
			$val->set_rules('sourcepass', 'Old Password', 'trim|required|xss_clean|min_length[6]|max_length[12]');
			$val->set_rules('newpass', 'New Password', 'trim|required|xss_clean|min_length[6]|max_length[12]|matches[ennewpass]');
			$val->set_rules('ennewpass', 'Confirm new Password', 'trim|required|xss_clean');
            if ($val->run() AND $this->wen_auth->change_password($val->set_value('sourcepass'), $val->set_value('newpass'))) {
               $this->session->set_flashdata('msg', $this->common->flash_message('success', '密码成功修改！'));
               redirect('user/info');
			}else{
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '密码不匹配！或者密码太短！'));
               redirect('user/info');
			 }
			}
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "changepass";
			$this->load->view('template', $data);
		}else{
			redirect('user/info');
		}

	}
	function nameType($str) {
		if (preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $str)) {
			return 'email';
		}
		elseif (preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $str)) {
			return 'phone';
		}
		return '';
	}
	function _check_user_name() {
		$username = $this->input->post('uname');
		if (strlen($username) < 28 && !$this->hasnumber($username) && !preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $username)) {
			if ($this->wen_auth->is_username_available($username)) {
				return true;
			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '用户名已被使用或者无效！'));
				return false;
			}
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '用户名不能是邮箱,手机号,QQ以及其他非法字符！'));
			return false;
		}
	}
	function _check_user_email($email) {
		$email = $this->input->post('uname');
		if ($this->wen_auth->is_email_available($email) && preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $email)) {
			return true;
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '该邮箱已经被使用或者非法！'));
			return false;
		} //If end
	}

	function _check_phone_no() {
		$value = trim($this->input->post('uname'));
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

	private function cupload($uid, $fileUrl, $name = '', $info = '', $albumid = 0, $privacy = 0) {
		$target_path = realpath(APPPATH . '../upload/');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path . date('Y'))) {
				mkdir($target_path . date('Y'), 0777, true);
			}
			$target_path .= date('Y') . '/' . time() . 'jpg';
			move_uploaded_file($fileUrl, $target_path);
			$data['albumId'] = $albumid;
			$data['info'] = $info;
			$data['name'] = $name;
			$data['userId'] = $uid;
			$data['savepath'] = $fileUrl;
			$data['privacy'] = $privacy;
			$data['cTime'] = time();
			$this->db->insert('c_photo', $data);
			return true;
		}
		return false;
	}
	private function thumb($uid, $file) {
		if ($file != '') {
				 $this->remote->uputhumb($file,$uid);
				return true;
			} else {
				return false;
			}
	}
	//upload set pic
	private function picSet($userid = '', $tmp_name = array ()) {
		$datas['albumId'] = time();
		$datas['type'] = 3;
		$datas['userId'] = $userid;
		foreach ($tmp_name as $row) {
			if (is_file($row)) {
				$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = date('Y').'/' . date('m').'/' . date('d');
				$ext = '.jpg';
			    $datas['name'] = uniqid(time().rand(10000,99999), false) . $ext;
			    $datas['savepath'] .= '/' . $datas['name'];
				if(!$this->remote->cp($row,$filename,$datas['savepath'],array('width'=>600,'height'=>800),true)){
                    continue;
			    }
				$datas['cTime'] = time();
				$this->common->insertData('c_photo', $datas);
			}

		}
	}

    //上传医师案例图集
    private function picSet2($userid = '', $tmp_name = array ()) {
        $datas['type'] = 1;
        $datas['albumId'] = time();
        $datas['userId'] = $userid;
        foreach ($tmp_name as $row) {
            if (is_file($row)) {
				$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = date('Y').'/' . date('m').'/' . date('d');
				$ext = '.jpg';
			    $datas['name'] = uniqid(time().rand(10000,99999), false) . $ext;
			    $datas['savepath'] .= '/' . $datas['name'];
				if(!$this->remote->cp($row,$filename,$datas['savepath'],array('width'=>600,'height'=>800),true)){
                    continue;
			    }
				$datas['cTime'] = time();
				$this->common->insertData('c_photo', $datas);
			}

        }
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
	private function redirectComplete() {
		if ($this->session->userdata('uinfonotcomplete')) {
			//$this->session->set_flashdata('msg', $this->common->flash_message('error', '用户信息未完善!请先完善信息！'));
			//var_dump($this->wen_auth->get_role_id());die;
			if ($this->wen_auth->get_role_id() == 2) {
				redirect('user/yishengReg');
			} else if ($this->wen_auth->get_role_id() == 3) {
				redirect('user/jigouReg');
			} else if($this->wen_auth->get_role_id() == 1) {
				 //redirect('user/dashboard');
			} else {
				redirect('user/dashboard');
			}



		}
	}
	
	private function encode($string = '', $skey = 'meilimei') {
		$strArr = str_split(base64_encode($string));
		$strCount = count($strArr);
		foreach (str_split($skey) as $key => $value){
			$key < $strCount && $strArr[$key].=$value;
		}
	
		return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
	}
}
?>
