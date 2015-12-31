<?php
class counselor extends CI_Controller {
	private $uid = '',$tehuiDB = '';
	public function __construct() {
		parent :: __construct();
		$this->load->library('yisheng');
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('user/login');
		}
		
		//error_reporting(E_ALL);
		ini_set("display_errors","On");
		$this->tehuiDB = $this->load->database('tehui', TRUE);
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('Email_model');
		$this->load->library('pager');
		
	}
	public function yuyue($param = '') {
		$per_page = 16;
		$start = intval($param);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$tmp = $this->db->query("SELECT jifen FROM `users` WHERE `id` ={$this->uid}")->result_array();
		$data['jifen'] = $tmp[0]['jifen'];
		$data['data']['subdata'] = array ();
		$data['data'] = $this->db->query("SELECT y.name,y.phone,y.items,y.amout,y.userby ,s.* FROM `yuyue` as y LEFT JOIN yuyueSend as s ON y.sn=s.sn WHERE y.is_delete=0 and s.`uid` = '{$this->uid}' ORDER BY s.id DESC LIMIT $offset, $per_page ")->result_array();
		//print_r($data);

		$data['total_rows'] = $this->db->query("SELECT yuyueSend.id FROM (`yuyueSend`) LEFT JOIN yuyue ON yuyue.sn = yuyueSend.sn WHERE yuyue.is_delete=0 and yuyueSend.uid = '{$this->uid}'")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('counselor/yuyue/' . ($start -1)) : site_url('counselor/yuyue/');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('counselor/yuyue/' . ($start +1)) : site_url('counselor/yuyue/' . $start);
		$data['newans'] = $this->common->newansum($this->uid);
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "counselor/yuyue";
		$this->load->view('template', $data);
	}
	public function search($param = '') {
		$start = intval($param);
		$per_page = 16;
		$condition = ' WHERE yuyue.is_delete=0 AND yuyueSend.uid = ' . $this->uid;
		$fixurl = '?';
		$morejoin = '';
		if ($this->input->get('ac')) {
			switch ($this->input->get('ac')) {
				case 'today' :
					$fixurl .= 'ac=today&';
					$condition .= ' AND yuyueSend.cdate= ' . strtotime(date('Y-m-d'));
					break;
                case 'newms' :
					$fixurl .= 'ac=newms&';
					$condition .= ' AND yuyueTalk.is_read = 1'  ;
					$morejoin = 'LEFT JOIN yuyueTalk ON yuyueTalk.talkid = yuyueSend.id ';
					break;
				case 'month' :
					{
						$month = date('m');
						$year = date('Y');
						$last_month = date('m') + 1;
						if ($month == 12) {
							$last_month = 1;
							$year = $year +1;
						}

					}
					$fixurl .= 'ac=month&';
				 	$condition .= ' AND yuyueSend.cdate >= ' . strtotime(date('Y-m')).' AND yuyueSend.cdate <= '.strtotime($year.'-'.$last_month);
					break;
			}
		}
		if ($this->input->get('name')) {
			$fixurl .= 'name=' . $this->input->get('name') . '&';
			$condition .= ' AND yuyue.name="' . $this->input->get('name') . '" ';
		}
		if ($this->input->get('ssstate')) {
			$fixurl .= 'ssstate=' . $this->input->get('name') . '&';
			$condition .= ' AND yuyue.shoushu="' . $this->input->get('ssstate') . '" ';
		}
		if ($this->input->get('tnc')) {
			$fixurl .= 'tnc=' . $this->input->get('tnc') . '&';
			$condition .= ' AND yuyueSend.nextdate=' . strtotime(date('Y-m-d'));
		}
		if ($this->input->get('ID')) {
			$fixurl .= 'ID=' . $this->input->get('ID') . '&';
			$condition .= ' AND yuyue.userby=' . $this->input->get('ID');
		}
		if ($this->input->get('province')) {
			$fixurl .= 'province=' . $this->input->get('province') . '&';
			$condition .= " AND yuyueSend.address like '%" . $this->input->get('province') . "%'";
		}
		if ($this->input->get('city')) {
			$fixurl .= 'city=' . $this->input->get('city') . '&';
			$condition .= " AND yuyueSend.address like '%" . $this->input->get('city') . "%'";
		}
		if ($this->input->get('chongdan')) {
			$fixurl .= 'chongdan=' . $this->input->get('chongdan') . '&';
			$condition .= ' AND yuyueSend.chongdan= 1';
		}
		if ($this->input->get('gktype')) {
			$fixurl .= 'gktype=' . $this->input->get('gktype') . '&';
			$condition .= ' AND yuyue.ystate= ' . $this->input->get('gktype');
		}
		if ($this->input->get('yuyueDateStart')) {
			$fixurl .= 'yuyueDateStart=' . $this->input->get('yuyueDateStart') . '&';
			$condition .= ' AND yuyueSend.cdate>=' . strtotime($this->input->get('yuyueDateStart'));
		}
		if ($this->input->get('yuyueDateEnd')) {
			$fixurl .= 'yuyueDateEnd=' . $this->input->get('yuyueDateEnd') . '&';
			$condition .= ' AND yuyueSend.cdate<=' . strtotime($this->input->get('yuyueDateEnd'));
		}
		if ($this->input->get('phone')) {
			$fixurl .= 'phone=' . $this->input->get('phone') . '&';
			$condition .= ' AND yuyue.phone="' . $this->input->get('phone') . '" ';
		}

		if ($start > 1)
			$offset = ($start -1) * $per_page;
		else {
			$start = 1;
			$offset = 0;
		}
		$fields = "yuyue.name,yuyue.phone,yuyue.items,yuyue.amout,yuyue.userby ,yuyueSend.*";
		$data['data'] = $this->db->query("SELECT {$fields} FROM (`yuyue`) LEFT JOIN users ON users.id=yuyue.userto  LEFT JOIN yuyueSend ON yuyueSend.sn=yuyue.sn " .$morejoin. $condition . " ORDER BY yuyue.id DESC LIMIT $offset, $per_page ")->result_array();

		$data['total_rows'] = $this->db->query("SELECT yuyue.id FROM (`yuyue`)   LEFT JOIN users ON users.id=yuyue.userto LEFT JOIN yuyueSend ON yuyueSend.sn=yuyue.sn " . $morejoin . $condition . "  ORDER BY yuyue.id DESC ")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/home/search/' . ($start -1) . $fixurl) : site_url('manage/search/' . $fixurl);
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/home/search/' . ($start +1) . $fixurl) : site_url('manage/home/search/' . $start . $fixurl);

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "counselor/yuyue";
		$this->load->view('template', $data);
	}

	public function danview($param) {
		if ($id = intval($param)) {
			if ($this->input->post('nextdate')) {
				$updateData = array ();
				$updateData['contactState'] = intval($this->input->post('contactState'));
				$updateData['nextdate'] = strtotime($this->input->post('nextdate'));
				if ($this->input->post('chongdan') and $_FILES["picture"]['name'] and $picture =  $this->upload($_FILES["picture"])) {
                   $updateData['linkpic'] = $picture;
                   $updateData['chongdan'] = 1;
				}
				$this->common->updateTableData('yuyueSend', $id, '', $updateData);
				redirect(site_url('counselor/yuyue'));
			}
			$data['res'] = $this->db->query("SELECT yuyueSend.is_view,yuyueSend.sendremark,yuyueSend.nextdate as cnextdate,yuyueSend.cdate,yuyueSend.linkpic,yuyueSend.chongdan,yuyueSend.contactState,yuyue.*,users.alias,users.role_id FROM (`yuyue`) LEFT JOIN users ON users.id=yuyue.userto LEFT JOIN yuyueSend ON yuyueSend.sn=yuyue.sn where yuyueSend.id = {$id} and yuyueSend.uid = {$this->uid} ORDER BY yuyueSend.id DESC LIMIT 1")->result_array();
            if(empty($data['res'])) redirect('user/dashboard');
            if($data['res'][0]['is_view']==0){
            	$updateData = array();
            	$updateData['is_view'] = 1;
            	$this->common->updateTableData('yuyueSend', $id, '', $updateData);
            }
            $data['param'] = $param;
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "counselor/yuyuedetail";
			$this->load->view('template', $data);
		}
	}
	public function paidantalk($param = '') {
		if ($this->input->post('talks') and $param) {
			$idata = array ();
			$idata['talkid'] = intval($param);
			$idata['fuid'] = $this->uid;
			$idata['touid'] = 0;
			$idata['message'] = $this->input->post('talks');
			$idata['cdate'] = time();
			$idata['is_read'] = 1;
			$this->common->insertData('yuyueTalk', $idata);
			redirect('counselor/paidantalk/' . $param);
		}
		$this->db->select('users.id,users.alias');
		$this->db->from('yuyueSend');
		$this->db->where('yuyueSend.id', $param);
		$this->db->join('users', 'users.id = yuyueSend.uid');
		$data['user'] = $this->db->get()->result();

		//update talk state
		$updata = array(
               'is_read' => 0
        );
        $this->db->where('touid', $this->uid);
        $this->db->where('id', $param);
        $this->db->update('yuyueTalk', $updata);

        //get talk info
		if (empty ($data['user'])) {
			redirect();
		}
		$data['talk'] = $this->db->get_where('yuyueTalk', array (
			'talkid' => $param
		))->result();
		$this->load->view('theme/counselor/paidantalk', $data);
	}
	public function addgengzong($param = '') {
		if (!$this->notlogin and $param) {
			if ($remark = $this->input->post('remark')) {
				$data = array (
					'sn' => $param,
					'uid' => $this->uid,
					'remark' => $remark,
				'cdate' => time());
				$this->db->insert('yuyueTrack', $data);
			}
			$this->db->where('sn', $param);
			$this->db->where('uid', $this->uid);
			$this->db->order_by("id", "desc");
			$data['res'] = $this->db->get('yuyueTrack')->result_array();
			$data['notlogin'] = $this->notlogin;
			$data['message_element'] = "counselor/addgengzong";
			$this->load->view('template', $data);
		}
	}
	public function submitBeizhu() {
		if ($dataid = $this->input->post('dataid')) {
			$updateData['comment'] = $this->input->post('comments');
			$this->common->updateTableData('yuyue', $dataid, array (), $updateData);

		}
		redirect('counselor/yuyue');
	}
	public function del($param = '') {
		if (!empty ($param)) {
			$condition = array (
				'id' => $param
			);
			$this->common->deleteTableData('yuyue', $condition);
		}
		redirect('counselor/yuyue');
	}
	public function submitNewBeizhu() {
		if ($this->input->post('comments')) {
			$updateData['comment'] = $this->input->post('comments');
			$updateData['is_record'] = 1;
			$updateData['userto'] = $this->session->userdata('WEN_user_id') ? $this->session->userdata('WEN_user_id') : 0;
			if (!empty ($updateData['userto'])) {
				$this->common->insertData('yuyue', $updateData);
			}
		}
		redirect('counselor/yuyue');
	}
	public function submitAdminBeizhu() {
		if ($dataid = $this->input->post('dataid')) {
			$updateData['admin_remark'] = $this->input->post('comments');
			$this->common->updateTableData('yuyue', $dataid, array (), $updateData);

		}
		redirect('manage');
	}
	public function submitSubBeizhu() {
		if ($dataid = $this->input->post('dataid')) {
			$insertData['comment'] = $this->input->post('comments');
			$insertData['pid'] = $dataid;
			$this->common->insertData('yuyue', $insertData);

		}
		redirect('counselor/yuyue');
	}
	public function beizhu($param = '') {
		$this->load->view("theme/yuyue_comment");
	}
	public function new_beizhu($param = '') {
		$this->load->view("theme/new_yuyue_comment", $param);
	}
	public function admin_beizhu($param = '') {
		$this->load->view("theme/yuyue_admin_comment");
	}
	public function subbeizhu($param = '') {
		$this->load->view("theme/sub_yuyue_comment");
	}
	public function talk($param = '') {
		$this->load->library('filter');
		$talkconent = $this->input->post('talkconent');
		$this->filter->filts($talkconent, true);
		if (!$this->notlogin && ($talkconent) && strlen($talkconent) > 2 && ($this->input->post('vtokens')==md5(($this->input->post('talk_id')+$this->input->post('data_id'))*2))) { //如果已经登录并且评论内容不为空且评论字符长度大于2
			$extra = array ();
			$qid = $this->input->post('data_id');
			if($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id'))) {
			    $this->uid = $this->session->userdata('yishi_id');
		    }
			$insertData = array (
				'fuid' => $this->uid,
				'comment' => $talkconent,
				'contentid' => $qid,
				'touid' => $this->input->post('talk_id'
			), 'status' => 1, 'type' => 'qa', 'data' => serialize($extra), 'cTime' => time());
			$this->common->insertData('wen_comment', $insertData);
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息已发送!'));
			$tmp = $this->db->query("SELECT  users.id,users.email,wen_questions.title FROM users LEFT JOIN wen_questions ON wen_questions.fUid=users.id LEFT JOIN user_notification ON user_notification.user_id=users.id WHERE wen_questions.id = {$qid} AND user_notification.new_reply=1 LIMIT 1")->result_array();
			if ($tmp[0]['id'] != $this->input->post('talk_id')) {
				$auid = $this->input->post('talk_id');
				$splVars = array (
					"{title}" => $tmp[0]['title'],
					"{content}" => '亲爱的医师，你的回答的' . $tmp[0]['title'] . '已被客户回复，请登录软件查看回复内容.',
					"{time}" => date('Y-m-d H:i',
				time()), "{site_name}" => '美丽美');
				$email = $this->db->query("SELECT users.email FROM users LEFT JOIN user_notification as n ON n.user_id=users.id WHERE users.id = {$this->input->post('talk_id')} AND n.new_ask=1  LIMIT 1")->result_array();
				$email = $email[0]['email'];
				$email != '' && $this->Email_model->sendMail($email, "support@meilizhensuo.com", '美丽神器', 'yishi_reply', $splVars);

				$tmp2 = $this->db->query("SELECT uid FROM question_state WHERE uid = {$auid} AND qid = {$qid} LIMIT 1")->result_array();

				if (!empty ($tmp2)) {
					$this->db->query("UPDATE `question_state` SET `new_reply` =`new_reply`+ 1  WHERE `qid` ={$qid} AND uid = {$auid}");
				} else {
					$insertData = array ();
					$insertData['uid'] = $auid;
					$insertData['qid'] = $qid;
					$insertData['new_reply'] = 1;
					$insertData['cdate'] = time();
					$this->common->insertData('question_state', $insertData);
				}

			} else {
				$this->db->query("update users SET replys=replys+1 where id={$this->uid} ");
				$this->sms->sendSMS(array (
					"{$tmp[0]['phone']}"
				), '亲爱的用户，你的咨询已被医师回复，请打开"美丽神器"手机APP查看回复内容。（本信息免费）退订回复0000');

				$auid = $this->uid;
				$splVars = array (
					"{title}" => $tmp[0]['title'],
					"{content}" => '亲爱的用户，你提问的' . $tmp[0]['title'] . '已被医师回复，请登录软件查看回复内容.',
					"{time}" => date('Y-m-d H:i',
				time()), "{site_name}" => '美丽神器');
				$email = $tmp[0]['email'];
				$email != '' && $this->Email_model->sendMail($email, "support@meilizhensuo.com", '美丽神器', 'yishi_reply', $splVars);
			}
			//update memory
		    $mec = new Memcache();
            $mec->connect('127.0.0.1', 11211);
            $mec->set('state'.$tmp[0]['id'],'');
            $mec->close();
			isset($tmp[0]['id'])&&$this->db->query("UPDATE `wen_questions` SET `new_answer` = `new_answer`+1   WHERE `id` ={$qid}");
			isset($tmp[0]['id'])&&$this->db->query("UPDATE `wen_notify` SET `new_answer` = `new_answer`+1   WHERE `user_id` ={$tmp[0]['id']}");
			isset($tmp[0]['id'])&&$this->db->query("UPDATE `wen_answer` SET `new_comment` = `new_comment`+1   WHERE `qid` = {$qid} AND `uid` = {$auid}");
             if($tmp[0]['id']){
                //send apple push
               $this->load->model('push');
               $push = array('type'=>'zixun','qid'=>$qid,'uid'=>$this->uid);
               $this->push->sendUser('你的咨询已被医师回复',$tmp[0]['id'],$push);
               }
			redirect('question/' . $this->input->post('data_id'));
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '发送的信息太简短!'));
			redirect('question/' . $this->input->post('data_id'));
		}
	}
	public function gtalk($param = '') {
		if (!$this->notlogin && ($talk_id = $this->input->get('talk_id')) && ($data_id = $this->input->get('data_id'))) {
			$fields = 'wen_answer.uid,wen_answer.content,wen_answer.is_talk,wen_answer.cdate,user_profile.Lname';
			$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$data_id} AND wen_answer.uid = {$talk_id} order by wen_answer.id ASC")->result_array();

			$data['talks'] = $this->getcomment($data_id, $talk_id);
			$tmp[0]['cdate'] = date('Y-m-d', $tmp[0]['cdate']);
			//$data['thumb'] = $this->profilepic($tmp[0]['uid'],1);
			$data['data'] = $tmp[0];
			$data['talk_id'] = $talk_id;
			$data['uid'] = $this->uid;
			$this->load->view("theme/counselor/questiontalk", $data);
		}
	}

	public function myyishi($param = '') {
		$per_page = 16;
		$start = intval($param);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['data'] = $this->db->query("SELECT users.id,users.alias,users.email,users.phone,user_profile.position FROM (`users`) LEFT JOIN user_profile ON user_profile.user_id=users.id WHERE `invite_from` = '{$this->uid}' ORDER BY id DESC LIMIT $offset, $per_page ")->result_array();

		foreach ($data['data'] as $k => $v) {
			$row = $this->db->query("select count(*) as sum from wen_answer where uid=" . $v['id'])->row();

			$data['data'][$k]['replyNums'] = $row->sum;
		}

		$data['total_rows'] = $this->db->query("SELECT id FROM (`users`) WHERE `invite_from` = '{$this->uid}'")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('counselor/yuyue/' . ($start -1)) : site_url('counselor/yuyue/');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('counselor/yuyue/' . ($start +1)) : site_url('counselor/yuyue/' . $start);
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "counselor/yishi";
		$this->load->view('template', $data);
	}
	
	public function edityishi($param = '') {
	    if ($param != '') {
	        
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
	           
	            //$data['yishi'][0]['thumb'] = $this->profilepic($uid, 3);

	            $data['notlogin'] = $this->notlogin;
	            $data['message_element'] = "counselor/edityishi";
	            $this->load->view('template', $data);
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
	        redirect('counselor/myyishi');
	        //redirect($this->session->userdata('history_url'));
	    }
	}
	
	
	public function similaryishi($param = '') {
		$per_page = 16;
		$start = intval($param);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$tmp = $this->db->query("SELECT name FROM (`company`) ORDER BY id DESC LIMIT 1 ")->result_array();
		$data['data'] = $tmp[0]['name'] != '' ? $this->db->query("SELECT users.id,users.alias,users.email,users.phone,user_profile.position FROM (`users`) LEFT JOIN user_profile ON user_profile.user_id=users.id WHERE `user_profile`.company LIKE '%{$tmp[0]['name']}%' ORDER BY id DESC LIMIT $offset, $per_page")->result_array() : array ();
		$data['total_rows'] = $this->db->query("SELECT users.id FROM (`users`) LEFT JOIN user_profile ON user_profile.user_id=users.id WHERE `user_profile`.company LIKE '%{$tmp[0]['name']}%' ORDER BY id DESC ")->num_rows();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('counselor/yuyue/' . ($start -1)) : site_url('counselor/yuyue/');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('counselor/yuyue/' . ($start +1)) : site_url('counselor/yuyue/' . $start);

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "counselor/similaryishi";
		$this->load->view('template', $data);
	}
	public function tuijian() {
		if (!$this->notlogin) {
			if ($this->input->post('jurl')) {
				$this->db->from('tongji_url');
				$this->db->where('uid', $this->uid);
				$tnum = $this->db->get()->result_array();
				/*  if(!(strpos($this->input->post('jurl'),'www.meilimei.com') || strpos($this->input->post('jurl'),'qq.com/') || strpos($this->input->post('jurl'),'apple.com/'))){
				      $this->session->set_flashdata('msg', $this->common->flash_message('error', '非法网址!'));
				      redirect('counselor/tuijian');
				  }*/
				$adata['coupon_code'] = $this->input->post('codes');
				$adata['url'] = $this->input->post('jurl');
				$adata['cdate'] = time();
				$adata['uid'] = $this->uid;
				$this->session->set_flashdata('msg', $this->common->flash_message('success', '设置成功!'));
				if (empty ($tnum)) {
					$this->common->insertData('tongji_url', $adata);
				} else {
					$this->common->updateTableData('tongji_url', '', array (
						'uid' => $this->uid
					), $adata);
				}
				redirect('counselor/tuijian');
			}
			$PNG_TEMP_DIR = FCPATH . 'temp' . DIRECTORY_SEPARATOR;
			$PNG_WEB_DIR = base_url() . 'temp/';
			$data['aucode'] = $aucode = $this->wen_auth->get_coupon_code();
			include FCPATH . "phpqrcode/qrlib.php";
			$filename = $PNG_TEMP_DIR . $aucode . '.png';
			if (!file_exists($filename)) {
				$errorCorrectionLevel = 'L';
				$matrixPointSize = 4;
				$data['url'] = base_url() . 'promotion/' . $aucode;
				QRcode :: png($data['url'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$data['image'] = $PNG_WEB_DIR . basename($filename);
			} else {
				$data['url'] = base_url() . 'promotion/' . $aucode;
				$data['image'] = $PNG_WEB_DIR . basename($filename);
			}
			$this->db->from('tongji_url');
			$this->db->where('uid', $this->uid);
			$data['results'] = $this->db->get()->result();
			$data['notlogin'] = $this->notlogin;
			$data['newans'] = $this->common->newansum($this->uid);
			$data['message_element'] = "tuijian";
			$this->load->view('template', $data);
		}

	}
	public function tongji($page = '') {
		$data['total_rows'] = $this->db->query("SELECT tongji.id FROM tongji LEFT JOIN users on users.coupon_code=tongji.coupon_code WHERE users.id=$this->uid")->num_rows();

		$per_page = 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT tongji.* FROM tongji  LEFT JOIN users on users.coupon_code=tongji.coupon_code WHERE users.id=$this->uid ORDER BY tongji.id  DESC LIMIT $offset , $per_page")->result();
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('counselor/tongji/' . ($start -1)) : site_url('counselor/tongji');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('counselor/tongji/' . ($start +1)) : site_url('counselor/tongji');

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "tongji";
		$this->load->view('template', $data);
	}
	public function tuiguang() {
		if ($this->wen_auth->get_role_id() == 3) {
			$tmp = $this->db->query("SELECT name,city FROM company WHERE userid = {$this->uid} order by userid ASC LIMIT 1")->result_array();
			$data['info'] = $tmp[0];
		} else {
			$tmp = $this->db->query("SELECT Fname,Lname,city FROM user_profile WHERE user_id = {$this->uid} order by user_id ASC LIMIT 1")->result_array();

			if (!$tmp[0]['city']) {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '请完善资料信息!'));
				redirect('user/info');
			}
			$data['info']['city'] = $tmp[0]['city'];
			$data['info']['name'] = $tmp[0]['Lname'] . $tmp[0]['Fname'];
		}
		$tmp = $this->db->query("SELECT startday,endday,id FROM advert WHERE uid = {$this->uid} AND state=0 order by id DESC")->result_array();
		$data['tuiguanginfo'] = '您当前没有相关推广!';
		if (!empty ($tmp) && ($tmp[0]['endday'] < time())) {
			$updateData['state'] = 8;
			$this->common->updateTableData('advert', $tmp[0]['id'], '', $updateData);
		}
		elseif (!empty ($tmp)) {
			$data['tuiguanginfo'] = '您当前已有的推广的时间：' . date('Y-m-d', $tmp[0]['startday']) . ' 至 ' . date('Y-m-d', $tmp[0]['endday']);
		}
		$data['newans'] = $this->common->newansum($this->uid);
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "tuiguang";
		$this->load->view('template', $data);
	}
	public function chongzhi() {
		$data['notlogin'] = $this->notlogin;
		$data['newans'] = $this->common->newansum($this->uid);
		$tmp = $this->db->query("SELECT amount,id FROM users WHERE id = {$this->uid} order by id ASC LIMIT 1")->result_array();
		$data['amount'] = $tmp[0]['amount'];
		$data['message_element'] = "chongzhi";
		$this->load->view('template', $data);
	}
	public function addyishi() {
		$this->load->library('yisheng');
		if ($this->input->post('upass')) {
			$username = time();
			$password = $this->input->post('upass');
			$email = $this->input->post('email');
			$phnum = $this->input->post('phone');
			$device_sn = $username;

			if (!$this->_check_user_email($email)) {
				redirect('counselor/addyishi');
			}
			if (!$this->_check_phone_no($phnum)) {
				redirect('counselor/addyishi');
			}

			$data = $this->wen_auth->register($username, $password, $email, $phnum, $device_sn, '', 2,'','','',false,false);
			$notification = array ();
			$user_id = $notification['user_id'] = $data['user_id'];
			if ($notification['user_id']) {
				$this->common->insertData('user_notification', $notification);
				$this->common->insertData('wen_notify', $notification);
			}
			//register info
			$tmp = $this->db->query("SELECT name,province,city,district,address,tel FROM company WHERE userid = {$this->uid} order by id ASC LIMIT 1")->result_array();
			$updateData['Fname'] = $this->input->post('Fname');
			$updateData['Lname'] = $this->input->post('Lname');
			$updateData['sex'] = $this->input->post('sex');
			$updateData['skilled'] = $this->input->post('skilled');
			$updateData['position'] = $this->input->post('position');
			$updateData['company'] = $tmp[0]['name'];
			$department = $this->input->post('department');
			$updateData['department'] = ',';
			if ($department) {
				foreach ($department as $k => $val) {
					$updateData['department'] .= $k . ',';
				}
			}

			$updateData['province'] = $tmp[0]['province'];
			$updateData['city'] = $tmp[0]['city'];
			$updateData['district'] = $tmp[0]['district'];
			$updateData['address'] = $tmp[0]['address'];
			$updateData['introduce'] = $this->input->post('introduce');
			$items = $this->input->post('items');
			$updateData['items'] = '';
			if ($items) {
				foreach ($items as $k => $val) {
					$updateData['items'] .= ',' . $k;
				}
				$updateData['items'] = substr($updateData['items'], 1);
			}

			$updateData['tel'] = $tmp[0]['tel'];
			$this->thumb($data['user_id'], $_FILES['uploadtemp']['tmp_name']);
			$updateData['assistphone'] = trim($this->input->post('assistphone'));
			$this->db->where('user_id', $user_id);
			$this->db->from('user_profile');
			if($this->db->count_all_results()){
				$this->db->update('user_profile', $updateData);
			}else{
				$updateData['user_id'] = $user_id;
				$this->db->insert('user_profile',$updateData);
			}

			$username = $updateData['Lname'] . $updateData['Fname'];
			unset ($updateData);
			$updateData['alias'] = $username;
			$updateData['rank_search'] = 10;

			$updateData['state'] = 1;
			$updateData['banned'] = 0;
			$updateData['invite_from'] = $this->uid;
			$this->db->where('id', $user_id);
			$this->db->update('users', $updateData);
			if (!empty ($_FILES['urls']['tmp_name'])) {
				$this->picSet($user_id, $_FILES['urls']['tmp_name']);
			}
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '医师添加成功！'));

			redirect('counselor/addyishi');

		} else {
			$data['notlogin'] = $this->notlogin;
			$data['keshi'] = $this->yisheng->getKeShi();
			$data['items'] = $this->db->get('items')->result_array();
			$data['message_element'] = "counselor/yishengReg";
			$this->load->view('template', $data);
		}

	}
	public function submittuiguang() {
	    if($this->input->post('selectdate')){
            $selectdate = explode(',',$this->input->post('selectdate'));
            $insdata = array();
            $insdata['cdate'] = time();
            $insdata['sn'] = date('YmdHis').rand(1,10);
            $insdata['city'] = $this->input->post('city');
            $insdata['state'] = 0;
            $insdata['uid'] = $this->uid;
            $data['fees'] = 200*count($selectdate);
            $tmp = $this->db->query("SELECT amount,id FROM users WHERE id = {$this->uid} order by id ASC LIMIT 1")->result_array();
            $data['amount'] = $tmp[0]['amount'];
            if($tmp[0]['amount']>=$data['fees']){
                $insdata['state'] = 2;
            }

            foreach($selectdate as $r){
            	$r = str_replace('年','-',$r);
            	$r = str_replace('月','-',$r);
            	$r = str_replace('日','',$r);
            	if($insdata['fdate'] = strtotime($r)){
            		$insdata['tdate'] = $insdata['fdate']+3600*24;
                	$this->db->insert('book_adv', $insdata);
            	 }
            }
            if(($lamount = $tmp[0]['amount']-$data['fees'])>=0){
            	 $this->db->query("Update users SET  amount = {$lamount} where id = {$this->uid} order by id ASC LIMIT 1");
            	 redirect();
            }
	    }else{
	    	redirect('counselor/tuiguang');
	    }
		$data['message_element'] = "chongzhi";
		$this->load->view('template', $data);
	}
	public function notice() {
		if ($this->input->post('save')) {
			$updateData = array (
				'new_ask' => intval($this->input->post('zixun'
			)), 'new_reply' => intval($this->input->post('szixun')));
			$this->common->updateTableData('user_notification', '', array (
				'user_id' => $this->uid
			), $updateData);
		}
		$data['notification'] = $this->common->getTableData('user_notification', array (
			'user_id' => $this->uid
		))->result();

		$data['message_element'] = "notice";
		$this->load->view('template', $data);
	}
	//doctor bind to yiyuan
	public function bindyy(){
		if($this->input->post('companyid')){
		   $this->db->where('id', $this->input->post('companyid'));
           $this->db->limit(1);
           $com = $this->db->get('company')->result_array();

           $data = array(
               'invite_from' => $com[0]['userid']
            );
           $this->db->where('id', $this->uid);
           $this->db->limit(1);
           $this->db->update('users', $data);

           $this->db->where('user_id', $this->uid);
           $this->db->limit(1);
           $data = array(
               'company' => $this->input->post('company')
            );
           $this->db->update('user_profile', $data);

		}else{
			$this->db->select('company.name,company.userid');
			$this->db->from('users');
			$this->db->limit(1);
		    $this->db->where('users.id', $this->uid);
			$this->db->join('company', 'company.userid = users.invite_from');
			$tmp = $this->db->get()->result_array();
		}
		$data = array();
		if(isset($tmp[0])){
			$data = $tmp[0];
		}else{
			$data['name'] = '';
			$data['userid'] = 0;
		}
        $data['message_element'] = "bindyy";
		$this->load->view('template', $data);
	}
	public function dealing() {
		echo '<div class="showstate"><h3>已跳转到付款页面</h3><div><a style="color:#fff;" href="#" onclick="top.tb_remove();">付款遇到问题</a><a style="color:#fff;" href="' . site_url() . '">完成付款</a></div></div>';
		exit;
	}
	
	
	public function product(){
	    $page = $this->input->get('page');
        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;
        
        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }
	    
        $sql = "select * from team_temp where 1=1 and user_id = ? order by id DESC  limit $offset , $per_page";
        $rs = $this->tehuiDB->query($sql,array($this->uid))->result_array();;

        $data['total_rows'] = $this->tehuiDB->query("select * from team_temp where 1=1 and user_id = ? order by id DESC  limit $offset , $per_page",array($this->uid))->num_rows();
        
        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>5,
        
            'base_url'=>'counselor/product/index',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        
        $data['team_rs'] = $rs;
	    $data['message_element'] = "product";
	    $this->load->view('template', $data);
	}
	
	public function productnoreview(){
	    $page = $this->input->get('page');
	    $per_page = 30;
	    $start = intval($page);
	    $start == 0 && $start = 1;
	
	    if ($start > 0){
	        $offset = ($start -1) * $per_page;
	    }else{
	        $offset = $start * $per_page;
	    }
	     
	    $sql = "select * from team_temp where 1=1 and user_id = ? and review = ? order by id DESC  limit $offset , $per_page";
	    $rs = $this->tehuiDB->query($sql,array($this->uid,0))->result_array();;
	
	    $data['total_rows'] = $this->tehuiDB->query("select * from team_temp where 1=1 and user_id = ? and review = ? order by id DESC  limit $offset , $per_page",array($this->uid,0))->num_rows();
	
	    $config =array(
	        "record_count"=>$data['total_rows'],
	        "pager_size"=>$per_page,
	        "show_jump"=>true,
	        "show_front_btn"=>true,
	        "show_last_btn"=>true,
	        'max_show_page_size'=>5,
	
	        'base_url'=>'counselor/product/index',
	        "pager_index"=>$page
	    );
	    $this->pager->init($config);
	    $data['pagelink'] = $this->pager->builder_pager();
	
	    $data['team_rs'] = $rs;
	    $data['message_element'] = "product";
	    $this->load->view('template', $data);
	}
	
	public function productreview(){
	    $page = $this->input->get('page');
	    $per_page = 30;
	    $start = intval($page);
	    $start == 0 && $start = 1;
	
	    if ($start > 0){
	        $offset = ($start -1) * $per_page;
	    }else{
	        $offset = $start * $per_page;
	    }
	
	    $sql = "select * from team_temp where 1=1 and user_id = ? and review = ? order by id DESC  limit $offset , $per_page";
	    $rs = $this->tehuiDB->query($sql,array($this->uid,1))->result_array();;
	
	    $data['total_rows'] = $this->tehuiDB->query("select * from team_temp where 1=1  and user_id = ? and review = ? order by id DESC  limit $offset , $per_page",array($this->uid,1))->num_rows();
	
	    $config =array(
	        "record_count"=>$data['total_rows'],
	        "pager_size"=>$per_page,
	        "show_jump"=>true,
	        "show_front_btn"=>true,
	        "show_last_btn"=>true,
	        'max_show_page_size'=>5,
	
	        'base_url'=>'counselor/product/index',
	        "pager_index"=>$page
	    );
	    $this->pager->init($config);
	    $data['pagelink'] = $this->pager->builder_pager();
	
	    $data['team_rs'] = $rs;
	    $data['message_element'] = "product";
	    $this->load->view('template', $data);
	}
	
	public function product_add(){
	    //城市
	    $city_sql = "select * from category where zone = ?";
	    $city_rs = $this->tehuiDB->query($city_sql,array('city'))->result_array();
	    //类型
	    $group_sql = "select * from category where zone = ? and fid = ? ";
	    $group_rs = $this->tehuiDB->query($group_sql,array('group',1))->result_array();
        //项目
        $item_sql = "select * from new_items where id in (2,3,4,5,6,7,8,9)";
        $item_rs = $this->db->query($item_sql)->result_array();
	    
	    if(isset($_GET['act']) && $this->input->get("act") == 'add'){
	        $team = $_POST;
	        $team['user_id'] = $this->uid;
	        $team['state'] = 'none';
	        $team['tags'] = strip_tags($team['tags']);
	        $team['begin_time'] = strtotime($team['begin_time']);
	        $team['city_id'] = abs(intval($team['city_id']));
	        $team['partner_id'] = abs(intval($team['partner_id']));
	        $team['sort_order'] = abs(intval($team['sort_order']));
	        $team['fare'] = abs(intval($team['fare']));
	        $team['farefree'] = intval($team['farefree']);
	        $team['pre_number'] = abs(intval($team['pre_number']));
	        $team['end_time'] = strtotime($team['end_time']);
	        $team['expire_time'] = strtotime($team['expire_time']);
 	        $team['image'] = $this->upload_image('upload_image','team');
	        $team['image1'] = $this->upload_image('upload_image1','team');
	        $team['image2'] = $this->upload_image('upload_image2','team');
	        
	        /* 序列化选取的城市 */
	        if (!empty($team['city_ids'])) {
	            if(in_array(0, $team['city_ids'])) {
	                $team['city_id'] = 0; $team['city_ids'] = '@0@';
	            }
	            else {
	                $team['city_id'] = abs(intval($team['city_ids'][0]));
	                $team['city_ids'] = '@'.implode('@', $team['city_ids']).'@';
	            }
	        }
	        
//else {
// 	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "请选择项目发布的城市！"));
// 	            redirect("counselor/product_add", 'refresh');
// 	            return ;
// 	        }
	        if(empty($team['allowrefund']))  $team['allowrefund'] = 'N';
	        //if(empty($team['outdataFun']))  $team['outdataFun'] = 'N';
	        
	        /* 自定义快递价格 */
	        $express_relate = $team['express_relate'];
	        foreach ($express_relate as $k=>$v) {
	            $e[$k]['id'] = $v;
	            $e[$k]['price'] = $team["express_price_{$v}"];
	        }
	        $team['express_relate'] = serialize($e);
	        
	        //team_type == goods
	        if($team['team_type'] == 'goods'){
	            $team['min_number'] = 1;
	            $team['conduser'] = 'N';
	        }
	        
	        $insert_rs = $this->tehuiDB->insert('team_temp',$team);
            if($insert_rs){
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', "添加成功，等待审核！"));
                redirect("counselor/product", 'refresh');
            }else{
                $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "添加失败！"));
                redirect("counselor/product_add", 'refresh');
            }
            
	    }
	    
	    $data['item_rs'] = $item_rs;
	    $data['city_rs'] = $city_rs;
	    $data['group_rs'] = $group_rs;
	    $data['message_element'] = "product_add";
	    $this->load->view('template', $data);
	}
	
	
	public function product_edit($id = ''){
	    if(!$id){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数错误！"));
	        redirect("counselor/product", 'refresh');
	    }
	    
	    $team_sql = "select * from team_temp where 1=1 and id = ?";
	    $team_rs = $this->tehuiDB->query($team_sql,array($id))->row_array();
	    //print_r( $team_rs);die;
	    if($team_rs['review'] == 1){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "该产品已经审核完成无法修改！"));
	        redirect("counselor/product", 'refresh');
	    }
	    //城市
	    $city_sql = "select * from category where zone = ?";
	    $city_rs = $this->tehuiDB->query($city_sql,array('city'))->result_array();
	    //类型
	    $group_sql = "select * from category where zone = ? and fid = ? ";
	    $group_rs = $this->tehuiDB->query($group_sql,array('group',1))->result_array();
	    $id = abs(intval($id));

	   
	    if(isset($_GET['act']) && $this->input->get("act") == 'edit'){
	        $team = $_POST;
	        $team['user_id'] = $this->uid;
	        $team['state'] = 'none';
	        $team['tags'] = strip_tags($team['tags']);
	        $team['begin_time'] = strtotime($team['begin_time']);
	        $team['city_id'] = abs(intval($team['city_id']));
	        $team['partner_id'] = abs(intval($team['partner_id']));
	        $team['sort_order'] = abs(intval($team['sort_order']));
	        $team['fare'] = abs(intval($team['fare']));
	        $team['farefree'] = intval($team['farefree']);
	        $team['pre_number'] = abs(intval($team['pre_number']));
	        $team['end_time'] = strtotime($team['end_time']);
	        $team['expire_time'] = strtotime($team['expire_time']);
	        
	        $team['image'] = $this->upload_image('upload_image','team');
	        $team['image1'] = $this->upload_image('upload_image1','team');
	        $team['image2'] = $this->upload_image('upload_image2','team');
	         
	        /* 序列化选取的城市 */
	        if (!empty($team['city_ids'])) {
	            if(in_array(0, $team['city_ids'])) {
	                $team['city_id'] = 0; $team['city_ids'] = '@0@';
	            }
	            else {
	                $team['city_id'] = abs(intval($team['city_ids'][0]));
	                $team['city_ids'] = '@'.implode('@', $team['city_ids']).'@';
	            }
	        }else {
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "请选择项目发布的城市！"));
	            redirect("counselor/product_add", 'refresh');
	        }
	         
	         
	         
	        if(empty($team['allowrefund']))  $team['allowrefund'] = 'N';
	        //if(empty($team['outdataFun']))  $team['outdataFun'] = 'N';
	         
	        /* 自定义快递价格 */
	        $express_relate = $team['express_relate'];
	        foreach ($express_relate as $k=>$v) {
	            $e[$k]['id'] = $v;
	            $e[$k]['price'] = $team["express_price_{$v}"];
	        }
	        $team['express_relate'] = serialize($e);
	         
	        //team_type == goods
	        if($team['team_type'] == 'goods'){
	            $team['min_number'] = 1;
	            $team['conduser'] = 'N';
	        }
	         
            $this->tehuiDB->where('id',$id);
	        $update_rs = $this->tehuiDB->update('team_temp',$team);
	   }
	   
	   $data['team_rs'] = $team_rs;
	   $data['city_rs'] = $city_rs;
	   $data['group_rs'] = $group_rs;
	   $data['message_element'] = "product_edit";
	   $this->load->view('template', $data);
	   
	}
	
	
	public function product_del($id = ''){
	    if($id){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "参数错误！"));
	        redirect("counselor/product", 'refresh');
	    }
	    $team['display']  = 0;
	    
	    $this->tehuiDB->where('id',$id);
	    $update_rs = $this->tehuiDB->update('team_temp',$team);
	    $data['message_element'] = "product_edit";
	    $this->load->view('template', $data);
	}
	
	
	public function coupon_card(){
	    $coupon_sql = "select * from coupon_card where 1=1 and uid = ? ";
	    $coupon_rs = $this->db->query($coupon_sql,array($this->uid))->row_array();
	    
	    $data['coupon_rs'] = $coupon_rs;

	    $data['message_element'] = "counselor/coupon_card";
	    $this->load->view('template', $data);
	}
	
	public function coupon_card_add(){
 
	    if(isset($_POST['act']) && $this->input->post("act") == 'add'){
        	    $card = $_POST;
        	    $card['quantity'] = abs(intval($card['quantity']));
        	    $card['credit'] = abs(intval($card['money']));
        	    $card['begin_time'] = strtotime($card['begin']);
        	    $card['end_time'] = strtotime($card['end']);
        	    
        	    
        	    $error = array();
        	    if ( $card['credit'] < 1 ) {
        	        $error[] = "代金券面额不能小于1元";
        	    }
        	    if ( $card['quantity'] < 1 || $card['quantity'] > 100 ) {
        	        $error[] = "代金券每次只能生产1-100枚";
        	    }
        	     
        	    $today = strtotime(date('Y-m-d'));
        	    if ( $card['begin_time'] < $today ) {
        	        $error[] = "开始时间不能小于当天";
        	    }
        	    elseif ( $card['end_time'] < $card['begin_time'] ) {
        	        $error[] = "结束时间不能小于开始时间";
        	    }
                
        	    if (!$error && $this->coupon_card_create($card)) {
        	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "代金券生成成功！"));
        	        redirect("counselor/coupon_card", 'refresh');
        	    }else{
        	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"代金券生成不成功！"));
        	        redirect("counselor/coupon_card", 'refresh');
        	    }
        	    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "代金券生成成功！"));
        	    redirect("counselor/coupon_card", 'refresh');
	    } 
	    $data['message_element'] = "counselor/coupon_card_add";
	    $this->load->view('template', $data);
	}
	
	public function coupon_card_edit(){
	    
	}
	
	public function coupon_card_validate(){
	    
	}
	
	private function coupon_card_create($query){
 
	    $need = $query['quantity'];
	    for($i=1; $i<=$need; $i++){
	        $card = array(
	            'uid' => $this->uid,
	            'sn' => $this->GenSecret(8),
	            'batch'=> $query['batch'],
	            //'partner_id' => $query['partner_id'],
	            //'team' => $query['team'],
	            //'order_id' => 0,
	            'credit' => $query['credit'],
	            'quota' => $query['quota'],
	            'consume' => 'N',
	            'begin_time' => $query['begin_time'],
	            'end_time' => $query['end_time']
	        );
	        $this->db->insert('coupon_card', $card);
	        //$need = ($this->db->insert('coupon_card', $card)) ? 1 : 0;
	        //if ( $need <= 0 ) return true;
	    }
	    
	    return true;
	}
	
	private function GenSecret($len=6, $type=2){
	    $secret = '';
	    for ($i = 0; $i < $len;  $i++) {
	        if ( 2==$type ){
	            if (0==$i) {
	                $secret .= chr(rand(49, 57));
	            } else {
	                $secret .= chr(rand(48, 57));
	            }
	        }else if ( 1==$type ){
	            $secret .= chr(rand(65, 90));
	        }else{
	            if ( 0==$i ){
	                $secret .= chr(rand(65, 90));
	            } else {
	                $secret .= (0==rand(0,1))?chr(rand(65, 90)):chr(rand(48,57));
	            }
	        }
	    }
	    return $secret;
	}
	
	function upload_image($input, $type='team', $scale=false) {
	    $year = date('Y'); 
	    $day = date('md'); 
	    $n = time().rand(1000,9999).'.jpg';
	    $z = $_FILES[$input];
	    $image = "/Users/kingsley/Documents/develop/PHP/tehui/static";
	    $path = "{$type}/{$year}/{$day}/{$n}";
	    var_dump("$image."/".{$type}/{$year}/{$day}");
	    if(!is_dir("$image."/".{$type}/{$year}/{$day}")){
	        mkdir($image."/"."{$type}/{$year}/{$day}",0777,true);
	    }
	    
	    if ($z && strpos($z['type'], 'image')===0 && $z['error']==0) {
	        $image .= "/".$path;
	        if($type=='team') {
	            move_uploaded_file($z['tmp_name'], $image);
	        }
	        return $path;
	    }
        return $path;
	}
	
	
	private function getcomment($qid = 0, $uid = 0) {
		$tmp = $this->db->query("SELECT talk.*,user_profile.Lname as tFname FROM talk LEFT JOIN user_profile ON user_profile.user_id = talk.touid WHERE talk.qid = {$qid} AND (talk.fuid = {$uid} OR talk.touid = {$uid}) order by talk.id ASC")->result_array();

		$result = array ();
		foreach ($tmp as $row) {
			$row['haspic'] = 0;
			$row['pic'] = '';
			if ($t = unserialize($row['data'])) {
				$row['pic'] = 'http://static.meilimei.com.cn/upload/' . $t['linkpic'];
				$row['haspic'] = 1;
			}
			unset ($row['qid']);
			unset ($row['data']);
			$row['cTime'] = date('Y-m-d H:i', $row['cTime']);
			$result[] = $row;
		}
		return $result;
	}
	private function _check_user_name($username) {
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
	private function _check_user_email($email) {
		if ($this->wen_auth->is_email_available($email) && preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $email)) {
			return true;
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '该邮箱已经被使用或者非法！'));
			return false;
		} //If end
	}
	private function _check_phone_no($value) {
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
	private function thumb($uid, $file) {
		$target_path = realpath(APPPATH . '../images/users');
		if (!is_writable($target_path)) {
			return false;
		} else {
			if (!is_dir($target_path . '/' . $uid)) {
				mkdir($target_path . '/' . $uid, 0777, true);
			}
			$target_path = $target_path . '/' . $uid . '/';
			if ($file != '') {
				$get_content = file_get_contents($file);
				@ file_put_contents($target_path . 'userpic.jpg', $get_content);
				$this->load->library('upload');
				GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_thumb.jpg', 36, 36);
				GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_profile.jpg', 120, 120);
				GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic.jpg', 250, 250);
				return true;
			} else {
				return false;
			}
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
    private function upload($file) {
		$target_path = realpath(APPPATH . '../upload');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path .'/'. date('Y'))) {
				mkdir($target_path .'/'. date('Y'), 0777, true);
			}
			$extend =explode("." , $file["name"]);
            $va=count($extend)-1;
            $tmp = date('Y') . '/' . time().'.' . $extend[$va];
			$target_path .= '/' .$tmp;
			move_uploaded_file($file["tmp_name"], $target_path);
			return 'upload/'.$tmp;
		}
		return false;
	}
}
?>
