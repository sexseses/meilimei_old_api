<?php
class jigouDetail extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('yisheng');
		$this->load->library('sms');
		$this->load->model('Gallery');
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('Email_model');
	}
	public function index($param = '') {
		$data['notlogin'] = $this->notlogin;
		$uid = intval($param);
		if ($this->input->post('qtitle')) {
			if($this->uid){
				$this->sendqs();
			}else{
               $this->session->set_flashdata('msg', $this->common->flash_message('error', '没有登入不能发布问题!'));
                redirect('yishengDetail/'.$param);
			}
		}
		if ($this->input->post('mystar')) {
			if(!$this->sendremk($uid)){
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '没有登入不能评分!'));
                redirect('yishengDetail/'.$param);
			};

		}
		if($this->input->post('uname')){
			$data['userby'] = $this->uid;
			$data['userto'] = $uid;
			$data['name'] = $this->input->post('uname');
			$data['phone'] = $this->input->post('phone');
			$data['sex'] = '';
			if($data['phone'] == '123456'){
			    $this->session->set_flashdata('msg', $this->common->flash_message('error', '预约提交错误!'));
			    redirect('jigouDetail/'.$param);
			} 
			
			$data['age'] = strip_tags($this->input->post('age'));
			$data['yuyueDate'] = strip_tags($this->input->post('yuyueDate'));
			$data['keshi'] = 0;
			$data['remark'] = strip_tags($this->input->post('remark'));
			
			$data['state'] = 0;
			$data['cdate'] = time();
			$data['sn'] = date('YmdHis');
			if(isset($data['notlogin'])) {
				unset($data['notlogin']);
			}
			!$data['sex']&&$data['sex']='未知';
				$result['result'] = '亲爱的用户，你的' . $data['yuyueDate'] . '预约已提交，预约号:' . $data['sn'] . ' 等待医院/医师确认中;谢谢。退订回复0000';
				$this->common->insertData('yuyue', $data);
			    $tmp = $this->db->query("SELECT email, phone, alias FROM users WHERE id='{$data['userto']}'")->result_array();
                $keshi = $data['keshi']?$this->yisheng->search($data['keshi']):'';
				$message = "手机:{$data['phone']},姓名:{$data['name']},性别:{$data['sex']},科室:{$keshi};预约(医师或医院): {$tmp[0]['alias']},手机:{$tmp[0]['phone']}";
				//$this->sms->sendSMS(array ($data['phone']), $message);
                $splVars = array ( "{site_name}" => '美丽诊所', "{content}" => $message, "{title}" => '预约信息');
				$this->Email_model->sendMail('747242966@qq.com', "support@meilizhensuo.com", '美丽诊所', 'yuyue', $splVars);
			    $this->session->set_flashdata('msg', $this->common->flash_message('error', '预约已提交!'));
                redirect('yishengDetail/'.$param);
		}
        if(!$this->uid || $this->getcomstate($this->uid,$uid)){
            $data['remarkstate'] = false;
        }else{
            $data['remarkstate'] = true;
        }
		$data['commentrows'] = $this->comrows($uid);
		$sql = "SELECT users.tconsult,users.verify,users.suggested,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.grade,users.sysgrade,company.userid,company.name,company.tel,company.descrition,company.address";

		$csql = ' FROM users LEFT JOIN company ';
		$csql .= ' ON company.userid = users.id  WHERE ';
		$csql .= ' users.role_id = 3 AND users.id = ' . $uid;
		$csql .= " AND users.banned = 0";
		$csql .= ' ORDER BY users.id DESC';
		$tmp = $this->db->query($sql . $csql)->result_array();

		if (!empty ($tmp)) {
			$data['yisheng'] = $tmp[0];
			$data['thumbUrl'] = $this->Gallery->profilepic($uid, 2);
			$data['message_element'] = "jigou-detail";
			$data['yisheng']['tconsult'] = $tmp[0]['systconsult'] > 0 ? $tmp[0]['systconsult'] : $tmp[0]['tconsult'];
			$data['yisheng']['replys'] = $tmp[0]['sysreplys'] > 0 ? $tmp[0]['sysreplys'] : $tmp[0]['replys'];
			//$data['yisheng']['department'] = $this->yisheng->search($tmp[0]['department']);
			$tmp[0]['grade'] = $tmp[0]['sysgrade'] > 0 ? $tmp[0]['sysgrade'] : $tmp[0]['grade'];
			$data['questions'] = $this->getqustions($uid);
			$data['reviews'] = $this->getreviews($uid);
			$data['ablum_state'] = false;
			$data['ablum'] = $this->ablum($uid,$data['ablum_state']);
			$data['WEN_PAGE_TITLE'] =   $data['yisheng']['name'];
			$this->load->view('template', $data);
		} else {
			redirect();
		}
	}
    private function ablum($userId,&$state=false){
    	$sql = "SELECT savepath,id FROM c_photo WHERE userId = {$userId} AND isDel=0";
        $tmp = $this->db->query($sql)->result_array() ;
        $result = array();
        foreach($tmp as $r){
            $result[] = site_url().$r['savepath'];
        }
        (!empty($tmp) && file_exists('../'.$tmp[0]['savepath']))&&$state=true;
        return $result;
    }
	private function getcomstate($uid,$touid){
        $sql = "SELECT id FROM reviews WHERE userby  = {$uid} AND userto  = {$touid} AND reviews.type=2 limit 1";
		return $this->db->query($sql)->num_rows();
	}
	private function comrows($uid){
        $sql = "SELECT id FROM reviews WHERE userto  = {$uid} AND reviews.type=2 ";
		return $this->db->query($sql)->num_rows();
	}
	private function sendremk($uid) {
		$score = intval($this->input->post('mystar'))*10;
		if ($uid && $score && $this->uid) {
			$this->db->select('grade,voteNum,id');
			$this->db->from('users');
			$this->db->order_by("id", "desc");
			$tmp = $this->db->get()->result_array();
			if (!empty ($tmp)) {
				$vnum = $tmp[0]['voteNum'] + 1;
				$data = array (
					'voteNum' => $vnum,
					'grade' => ($tmp[0]['voteNum'] * $tmp[0]['grade'] + $score
				) / $vnum);
				$this->db->where('id', $uid);
				$this->db->update('users', $data);
			}
			$insertData = array();
			$insertData['userby'] = $this->uid;
			$insertData['userto'] = $uid;
			$insertData['type'] = 2;
			$insertData['review'] = $this->input->post('commentes');
			$insertData['showtype'] = 3;
			$insertData['score'] = $score;
			$insertData['created'] = time();
			$this->common->insertData('reviews', $insertData);
			return true;
		}else{
			return false;
		}
	}
	private function getreviews($abid) {
		$sql = "SELECT reviews.review,reviews.score,reviews.created as reviewdate,p.email,p.phone FROM reviews LEFT JOIN users as p on p.id=reviews.userby WHERE reviews.userto = {$abid} AND reviews.type=2 limit 6";
		$rmp = $this->db->query($sql)->result_array();
		$tmp = array ();
		foreach ($rmp as $r) {
			$r['showname'] = $r['phone'] != '' ? substr($r['phone'], 0, 3) . '***' : substr($r['email'], 0, 3) . '***';
			unset ($r['phone']);
			unset ($r['email']);
			$r['reviewdate'] = date('Y-m-d', $r['reviewdate']);
			$tmp[] = $r;
		}
		return $tmp;
	}
	private function sendqs() {
		if ($this->uid) {
			$data['fUid'] = $this->uid;
			$data['title'] = $this->input->post('qtitle');
			//$data['position'] = $this->input->post('position');
			$data['description'] = $this->input->post('qdes');
			//$data['sex'] = $this->input->post('sex');
			//$data['address'] = $this->input->post('address');
			//	$data['city'] = $this->input->post('city');
			//$data['toUid'] = "";
			$data['state'] = 1;
			$data['has_answer'] = 0;
			$data['cdate'] = time();
			$id = $this->common->insertData('wen_questions', $data);
			if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name'] && $id != 0) {

				$target_path = realpath(APPPATH . '../upload');
				if (is_writable($target_path)) {
					if (!is_dir($target_path . '/' . round($id / 1000))) {
						mkdir($target_path . '/' . round($id / 1000), 0777, true);
					}

					$datas['name'] = time() . '.jpg';
					$datas['savepath'] = round($id / 1000) . '/' . $datas['name'];
					$target_path = $target_path . '/' . $datas['savepath'];
					move_uploaded_file($_FILES['attachPic']['tmp_name'], $target_path);
					GenerateThumbFile($target_path, $target_path, 550, 650);
					$datas['userId'] = $this->uid;
					$datas['uploadTime'] = time();
					$datas['type'] = 'jpg';
					$datas['private'] = 1;
					$pictureid = $this->common->insertData('wen_attach', $datas);
					$result['updatePictureState'] = '000';
					$result['postState'] = '000';

					$upicArr = array ();
					$upicArr[]['type'] = 'jpg';
					$upicArr[]['id'] = $pictureid;
					$wdata['uid'] = $this->uid;
					$wdata['content'] = '';
					$wdata['q_id'] = $id;
					$wdata['type_data'] = serialize($upicArr);
					$wdata['type'] = 4;
					$wdata['ctime'] = time();
					$this->common->insertData('wen_weibo', $wdata);
				}
			}
			$this->session->set_flashdata('msg', $this->common->flash_message('sucess', '咨询已经发布！'));
			redirect('user/dashboard');
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '登入后才能发布咨询！'));
			redirect('user/login');
		}

	}
	private function getqustions($uid) {
		$sql = "SELECT q.sex,q.title,q.cdate,a.qid,a.content,users.phone,users.email  FROM wen_questions as q LEFT JOIN users ON users.id=q.fUid LEFT JOIN wen_answer as a ON a.qid = q.id WHERE a.uid={$uid} GROUP BY a.qid limit 6";
		$rmp = $this->db->query($sql)->result_array();
		$tmp = array ();
		foreach ($rmp as $r) {
			$r['sex'] = $r['sex'] == 1 ? '女' : '男';
			$r['cdate'] = date('Y-m-d', $r['cdate']);
			$r['showname'] = $r['phone'] != '' ? substr($r['phone'], 0, 3) . '***' : substr($r['email'], 0, 3) . '***';
			$tmp[] = $r;
		}
		return $tmp;
	}
}
?>
