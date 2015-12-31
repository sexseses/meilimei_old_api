<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class customerV2 extends MY_Controller {
	private $uid='';
	public function __construct() {
		parent :: __construct();
		$this->load->library('yisheng');
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}$this->load->library('sms');
		$this->load->model('auth');
		$this->load->helper('file');
		$this->load->model('Email_model');
		$this->load->model('remote');
        $this->load->model('Score_model');
		error_reporting(E_ALL);
	}
	//new send consult and to topic api
	public function ask($param='') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		$result['postState'] = '000';
		$data['title'] = strip_tags($this->input->post('title'));     //标题
		$data['position'] = $this->input->post('position');    //type
		$data['description'] = strip_tags($this->input->post('description'));   //miao shu
		$data['sex'] = $this->input->post('sex');
		$data['address'] = strip_tags($this->input->post('address'));
		$data['city'] = $this->input->post('city');
        $data['mobile'] = $this->input->post('mobile')?$this->input->post('mobile'):0;
		$data['toUid'] = "";
		$enc = md5(trim($data['title']));
		/*if(isset($_COOKIE['topic_senddata']) and $_COOKIE['topic_senddata'] == $enc ){
			$result['state'] = '012';
			$result['notice'] = '1咨询重复发送！';
			echo json_encode($result);
			exit;
		}else{
			setcookie('topic_senddata',$enc);
		}*/
		if(time()-$this->session->userdata('ask_ctime')<5){
			$result['state'] = '012';
			$result['notice'] = '咨询重复发送！';
			echo json_encode($result);
			exit;
		}elseif(time()-$this->session->userdata('ask_ctime')<120){
			$result['state'] = '012';
			$result['notice'] = '信息发送间隔2分钟！';
			echo json_encode($result);
			exit;
		}

		if ($this->input->post('toUid')) {
            //error_reporting(E_ALL);
			$data['toUid'] = intval($this->input->post('toUid'));
			//update state
			$this->db->query("update users SET tconsult=tconsult+1,systconsult=systconsult+1 where id={$data['toUid']} ");
			$this->db->where('id', $data['toUid']);
			$tmp = $this->db->get('users')->result_array();
			if (isset ($tmp[0]['email']) && $tmp[0]['email'] != '') {

				$email_name = 'yishi_require';
				$splVars = array (
					"{site_name}" => "美丽美", "{content}" => $data['description'], "{title}" => $data['title']);
				$this->auth->Email_model->sendMail($tmp[0]['email'], "support@meilimei.com", "美丽美", $email_name, $splVars);
			}
		}
        $result['debug'] = $this->uid."xxx".$this->i."username:".$this->input->cookie("username");

        if (strlen($data['title'])>3 && $data['position'] != '') {

			if (!$this->uid) {

				$data['device_sn'] = trim($this->input->post('device_sn'));
				$datas['device_sn'] = $data['device_sn'];
				if ($data['device_sn'] == '' && strlen($data['device_sn']) > 6) {
					$result['postState'] = '001';
					echo json_encode($result);
					exit;
				}
				if ($this->session->userdata('ask_time') > 1) {
					$result['postState'] = '011';
					echo json_encode($result);
					exit;
				}
			} else {

                $data['fUid'] = $datas['uid'] = $this->uid;
                if(!empty($data['mobile'])) {
                    $this->db->query("update users SET phone='{$data['mobile']}' where id={$this->uid} ");
                }
                $this->common->setNotify($data['fUid'], '', 1);


			}
			$data['state'] = 1;$data['has_answer'] = 0;
			$data['cdate'] = time();
			if (isset($_POST['isquestion']) && $this->input->post('isquestion') == 0) {       //发布咨询
				$result['qid'] = 0;
			}else{
				$head = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Android';
				if ((stristr($head,'iPhone') and !stristr($head,'U;')) OR  stristr($head,'ipod')) {
					$data['device'] = 'IOS';
				} else {
					$data['device'] = 'Android';
				}
				$result['qid'] = $this->common->insertData('wen_questions', $data);  //发布咨询
			}
			$result['isquestion'] = $this->input->post('isquestion');

			//create topic
			$info = array ();
			$info['address'] = $data['address'];
			$info['title'] = $data['title'];
			$info['sex'] = $data['sex'];
			$info['toUid'] = $data['toUid'];
			$datas['type'] = 1;
			$datas['tags'] = ','.trim($data['position']).',';
			$datas['q_id'] = $result['qid'];
			$datas['ctime'] = time();
			$datas['newtime'] = time();
			$datas['type_data'] = serialize($info);
			$datas['content'] = $data['description'];
		    //share to topic
			if ($this->input->post('share') == 1 && !$this->uid && trim($info['title'])) {
				$datas['dataType'] = $data['position'];
				$head = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Android';
				if ((stristr($head,'iPhone') and !stristr($head,'U;')) OR  stristr($head,'ipod')) {
					$datas['wsource'] = 'IOS';
				} else {
					$datas['wsource'] = 'Android';
				}
				$datas['isdel'] = 1;
				$this->common->insertData('wen_weibo', $datas);
                $result['notice'] = '咨询成功发布！';
			}else{
                $result['state'] = '000';
                $result['notice'] = '咨询成功发布！';
            }

			if($picnum = intval($this->input->post('has_pic'))){
                if(!$this->session->userdata('ask_ctime')){
                    $this->session->set_userdata('ask_ctime', time());
                }
				$upstate = false;
				if($this->sendpic($_FILES['attachPic']['tmp_name'],$result['qid'])){
					$result['state'] = '000';
					$result['notice'] = '咨询成功发布！';
				}else{
					$result['notice'] = '咨询发布失败！';
				}
                if($this->input->post('share') == 1) {
                    $result['data']['score'] = $this->Score_model->addScore(66,$this->uid);
                }else{
                    $result['data']['score'] = $this->Score_model->addScore(67,$this->uid);
                }
			}

		} else {
            $result['state'] = '015';
			$result['notice'] = '信息不全！';
			$result['postState'] = '015';
		}
		echo json_encode($result);
	}
   //private function  close topic and question
	private function clsQuestion($qid=''){
		if($qid = intval($qid)){
			$condition=array('id'=>$qid);
			$this->common->deleteTableData('wen_questions',$condition);

			$condition=array('q_id'=>$qid);
			$this->common->deleteTableData('wen_weibo',$condition);

			$condition=array('qid'=>$qid);
			$this->common->deleteTableData('question_state',$condition);

			$this->session->set_userdata('ask_ctime', 0);
		}
	}
	// question attach pictures
	private function sendpic($file='',$qid='') {
		if($file and $qid and $this->uid){
			$data['uid'] =  $this->uid;
			$data['q_id'] = $qid;
			if ($file and $qid) {
				$id = $qid;
				$ext = '.jpg';
				$datas['name'] = uniqid().rand(1000,9999) . $ext;
				$datas['savepath'] = date('Y').'/' . date('m').'/' . date('d').'/' . $datas['name'];
				if(!$this->remote->cp($_FILES['attachPic']['tmp_name'],$datas['name'],$datas['savepath'],array('width'=>500,'height'=>500),true)){

					$this->clsQuestion($id);
					return false;
				}
				$datas['userId'] = $this->uid;
				$datas['uploadTime'] = time();
				$datas['type'] = str_replace('.','',$ext);
				$datas['private'] = 1;
				$pictureid = $this->common->insertData('wen_attach', $datas);
				$result['updatePictureState'] = '000';
				$result['postState'] = '000';
				$upweibo = array();
                    //update weibo
				$this->db->where('q_id', intval($qid));
				$this->db->limit(1);
				$querys = $this->db->get('wen_weibo')->result_array();
				if(!empty($querys)){
					$type_datas = unserialize($querys[0]['type_data']);
					$picinfo = $upweibo = array();
					$picinfo['type'] = str_replace('.','',$ext);
					$picinfo['savepath'] = $datas['savepath'];
					$type_datas['pic'] = $picinfo;
					$upweibo['type_data'] = serialize($type_datas);
					$upweibo['type'] = 8;
					$this->db->limit(1);
					$this->db->where('weibo_id', $querys[0]['weibo_id']);
					$this->db->update('wen_weibo', $upweibo);
				}
                   //new pic weibo for question
				$upicArr = array ();
				$upicArr[]['type'] = str_replace('.','',$ext);
				$upicArr[]['id'] = $pictureid;
				$data['type_data'] = serialize($upicArr);
				$data['type'] = 4;
					//get system info
				$head = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Android';
				if ((stristr($head,'iPhone') and !stristr($head,'U;')) OR  stristr($head,'ipod')) {
					$data['wsource'] = 'IOS';
				} else {
					$data['wsource'] = 'Android';
				}
				$data['ctime'] = time();
				$this->common->insertData('wen_weibo', $data);
				return true;
			}
		}
	}
	//deal notify
	public function sendemail($param=''){
		$result['state'] = '000';
		$result['sendState'] = '001';
		$splVars = array (
			"{title}" => $this->input->get('title'), "{content}" =>$this->input->get('content'), "{time}" => $this->input->get('time'), "{site_name}" => '美丽神器');

		if($tuid=$this->input->get('touid')){
			$tmp = $this->db->query("SELECT users.email,users.phone FROM users LEFT JOIN user_notification as n ON n.user_id=users.id WHERE users.id={$tuid} AND n.new_ask=1 ")->result();

			if($tmp[0]->email){
				$this->Email_model->sendMail($tmp[0]->email, "support@meilizhensuo.com", '美丽神器', 'yishi_require', $splVars);
			}
		}elseif($tocisy =$this->input->get('tocity')){
			if($tocisy=='上海'){
				$tmp = $this->db->query("SELECT users.email,users.phone FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id LEFT JOIN user_notification ON user_notification.uid=users.id WHERE user_profile.city='{$tocisy}' AND user_notification.new_ask=1 ORDER BY users.rank_search DESC, users.grade DESC LIMIT 30")->result();
				foreach($tmp as $row){
					$this->Email_model->sendMail($row->email, "support@meilizhensuo.com", '美丽神器', 'yishi_require', $splVars);
				}
			}else{
				$tmp = $this->db->query("SELECT users.email,users.phone FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id LEFT JOIN user_notification ON user_notification.uid=users.id WHERE user_profile.city='{$tocisy}' AND user_notification.new_ask=1 ORDER BY users.rank_search DESC, users.grade DESC LIMIT 30")->result();
				foreach($tmp as $row){
					$this->Email_model->sendMail($row->email, "support@meilizhensuo.com", '美丽神器', 'yishi_require', $splVars);
				}
				$tmp = $this->db->query("SELECT users.email,users.phone FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id LEFT JOIN user_notification ON user_notification.uid=users.id WHERE user_profile.city='上海' AND user_notification.new_ask=1 ORDER BY users.rank_search DESC, users.grade DESC  LIMIT 30")->result();
				foreach($tmp as $row){
					$this->Email_model->sendMail($row->email, "support@meilizhensuo.com", '美丽神器', 'yishi_require', $splVars);
				}
			}

		}
		$result['sendState'] = '000';
		echo json_encode($result);
	}


	public function yuyueAC($param=''){
		$result['state'] = '000';

		if(true){

		}else{

		}

		echo json_encode($result);
	}

	public function yuyue($param) {
		$result['state'] = '000';

		$result['postState'] = '001';

		if($this->uid){
			$data['userby'] = $this->uid;
		}else{
			$data['userby'] = 0;
		}

		$data['userto'] = $this->input->post('yishi');
		$data['name'] = $this->input->post('name');
		$data['phone'] = $this->input->post('phone');
		$data['sex'] = $this->input->post('sex');
		$data['age'] = $this->input->post('age');
		$data['yuyueDate'] = strtotime($this->input->post('yuyueDate'));
		$data['extraDay'] = trim($this->input->post('extraDay'));
		$data['keshi'] = $this->input->post('keshi');
		$data['remark'] = $this->input->post('remark');
		$data['shoushu'] = '否';
		$data['state'] = 0;
		$data['sendState'] = 0;
		$head = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Android';
		if ((stristr($head,'iPhone') and !stristr($head,'U;')) OR  stristr($head,'ipod')) {
			$data['source'] = 'IOS';
		} else {
			$data['source'] = 'Android';
		}
		$data['cdate'] = time();
		$data['sn'] = date('YmdHis').rand(100,999);
		$result['postState'] = '000';
		if ( $data['name'] != '' && $this->_check_phone_no($data['phone']) ) {
			$result['postState'] = '000';
			!$data['sex']&&$data['sex']='未知';
			$result['result'] = '亲爱的用户，你的' . $data['yuyueDate'] . '预约已提交，预约号:' . $data['sn'] . ' 等待医院/医师确认中;谢谢。';
			$this->common->insertData('yuyue', $data);
			$tmp = $this->db->query("SELECT email, phone, alias FROM users WHERE id='{$data['userto']}'")->result_array();
			$keshi = $data['keshi']?$this->yisheng->search($data['keshi']):'';
			$message = '';
			!empty($tmp)&&$message = "手机:{$data['phone']},姓名:{$data['name']},性别:{$data['sex']},科室:{$keshi};预约(医师或医院): {$tmp[0]['alias']},手机:{$tmp[0]['phone']},退订回复0000";

			if($message){
				//$this->sms->sendSMS(array ("13564181025"), $message);
				$splVars = array ( "{site_name}" => '美丽神器', "{content}" => $message, "{title}" => '预约信息');
				$this->auth->Email_model->sendMail('747242966@qq.com', "support@meilizhensuo.com", '美丽神器', 'yuyue', $splVars);
			}
            $result['data']['score'] = $this->Score_model->addScore(68,$this->uid);
			$result['notice'] = '感谢你的预约，在消费后只要在“个人中心”的“消费返现”中上传你的消费清单照片，我们将给予特别奖励';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
			$result['postState'] = '001';
		}


		echo json_encode($result);
	}
	public function yuyue_active($param) {
		$result['state'] = '000';

		$result['postState'] = '000';
		//$data['userby'] = $this->uid;
		//$data['userto'] = $this->input->post('yishi');
		$data['name'] = $this->input->post('name');
		$data['phone'] = $this->input->post('phone');
		//$data['sex'] = $this->input->post('sex');
		$data['age'] = $this->input->post('age');
		//$data['yuyueDate'] = $this->input->post('yuyueDate');
		//$data['keshi'] = $this->input->post('keshi');
		$data['remark'] = $this->input->post('remark');
		$data['state'] = 0;
		$data['cdate'] = time();
		$data['sn'] = date('YmdHis');
		$data['y_type'] = 1;
		$users['username'] = '赴韩整形';
		$data['userto'] = $this->common->insertData('users', $users);
		if ($data['userto']  && $data['name'] != '' && $this->_check_phone_no($data['phone']) && $data['userto']!=0 ) {
			$result['postState'] = '000';
			!$data['sex']&&$data['sex']='未知';
			$result['result'] = '亲爱的用户，你的预约已提交，预约号:' . $data['sn'] . ' 等待医院/医师确认中;谢谢。退订回复0000 ';


			$this->common->insertData('yuyue', $data);
		    //$tmp = $this->db->query("SELECT email, phone, alias FROM users WHERE id='{$data['userto']}'")->result_array();
            //$keshi = $data['keshi']?$this->yisheng->search($data['keshi']):'';
			$message = "手机:{$data['phone']},姓名:{$data['name']},年龄:{$data['age']};预约时间:{$data['cdate']}";
			$this->sms->sendSMS(array ("13564181025"), $message);
			$splVars = array ( "{site_name}" => '美丽神器', "{content}" => $message, "{title}" => '预约信息');
			$this->auth->Email_model->sendMail('747242966@qq.com', "support@meilizhensuo.com", '美丽神器', 'yuyue', $splVars);
		} else {
			$result['state'] = '012';
			$result['postState'] = '001';
		}
		
		echo json_encode($result);
	}
	public function yuyueList($param=''){
		$result['state'] = '000';

		if ($this->uid) {
			$result['data'] =array();
			$page = intval($this->input->get('page')-1)*10;
			$page<0&&$page=0;
			if($this->wen_auth->get_role_id()==1){
				$info = $this->db->query("SELECT * FROM yuyue   WHERE yuyue.userby = {$this->uid} LIMIT $page,10 ")->result_array();
			}else{
				$info = $this->db->query("SELECT * FROM yuyue   WHERE yuyue.userto = {$this->uid} LIMIT $page,10 ")->result_array();
			}

			foreach($info as $row){
				$weekarray=array("日","一","二","三","四","五","六");
				$row['yuyueDate'] = $row['yuyueDate']>1383208705?date('Y年m月d日 ',$row['yuyueDate'])."星期".$weekarray[date("w",$row['yuyueDate'])]:'';
				$row['cdate'] = date('Y年m月d日 H:i:s',$row['cdate'])."星期".$weekarray[date("w",$row['cdate'])];;
				$row['keshi'] = $this->yisheng->search($row['keshi']);
				$result['data'][] = $row;
			}
		} else {
			$result['ustate'] = '001';
		}


		echo json_encode($result);
	}
	public function speckeshi($param = '') {
		$result['state'] = '000';
		$result['hasdata'] = '0';

		if (($uid = $this->input->get('uid')) && $type = $this->input->get('type')) {
			if($type=='yisheng'){
				$info = $this->db->query("SELECT department FROM user_profile WHERE user_id = {$uid} LIMIT 1")->result_array();
				$result['data'] = $this->yisheng->fullsec($info[0]['department']);

			}else{
				$info = $this->db->query("SELECT department FROM company WHERE userid = {$uid} LIMIT 1")->result_array();
				$result['data'] = $this->yisheng->fullsec($info[0]['department']);
			}
			if(!empty($result['data'])){
				$result['hasdata'] = '1';
			}
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	public function keshi($param = '') {
		$result['state'] = '000';

		$tmp = $this->yisheng->getKeShi();$result['data'][] = array (
			'name' => '不限',
			'id' => 0
			);
		foreach ($tmp as $k => $v) {
			$result['data'][] = array (
				'name' => $v,
				'id' => $k
				);
		}

		echo json_encode($result);
	}
///delete question
	public function delQ($param = ''){
		$result['state'] = '000';
		$result['ustate'] = '000';

		if(!$this->uid){
			$result['notice'] = '账户未登入!';
			$result['ustate'] = '001';
		}else{
			if($qid = intval($this->input->post('qid'))){
				$this->db->where('id', $qid);
				$this->db->where('fUid', $this->uid);
				$this->db->delete('wen_questions');
              //delete answer
				$this->db->where('qid', $qid);
				$this->db->delete('wen_answer');
               //delete state
				$this->db->where('qid', $qid);
				$this->db->delete('question_state');
				$result['notice'] = '删除成功!';
			}else{
				$result['notice'] = '参数不全!';
				$result['state'] = '012';
			}
		}

		echo json_encode($result);
	}
	private function _check_phone_no($phone) {
		$value = trim($phone);
		if ($value == '') {
			return FALSE;
		} else {
			if (preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $value)) {
				return true;
			} else {
				return FALSE;
			}
		}
	}
}

?>