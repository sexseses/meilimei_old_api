<?php
class question extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}$this->load->model('Email_model');

	}
	public function index($param = '') {
		//var_dump($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id')));
	    if($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id'))) {
			$this->uid = $this->session->userdata('yishi_id');
		}
		$data['current_uid'] = $this->uid;
		$data['notlogin'] = $this->notlogin;
		if ($param == '' || $this->notlogin) {
			redirect('user/dashboard');
		}else{
			$param = intval($param);
		}
        $data['roleId'] = $this->wen_auth->get_role_id();
		$this->db->where('wen_questions.state', 1);
		$this->db->where('wen_questions.id', $param);
		//$this->db->where('wen_weibo.type', 1);
		$this->db->select('wen_questions.*,wen_weibo.type_data');
		$this->db->from('wen_questions');
		$this->db->join('wen_weibo', 'wen_weibo.q_id = wen_questions.id', 'left');
		$this->db->order_by("wen_questions.id", "desc");
		//$this->db->join('users', 'users.id = wen_questions.fUid');
		//$this->db->join('user_profile', 'user_profile.user_id = wen_questions.fUid', 'left');
		$data['questions'] = $this->db->get()->result_array();
		
		 
		
		if (empty ($data['questions']) || !(($data['questions'][0]['toUid']==$this->uid and ($data['questions'][0]['has_answer'] OR time()-$data['questions'][0]['cdate'] < 3600*24) ) || $data['roleId']!=1 || $data['questions'][0]['fUid']==$this->uid) )
		{
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '该问题您无权限查看！'));
			redirect('user/dashboard');
		}

		// get attach
		$data['attaches'] = array();
	    $this->db->where('wen_weibo.type', 4);
	    $this->db->where('wen_weibo.q_id', $param);
		$this->db->select('wen_weibo.type_data');
		$this->db->from('wen_weibo');
		$this->db->order_by("wen_weibo.q_id", "desc");
		$pquestions = $this->db->get()->result_array();
		if($pquestions[0]['type_data']!=''){
			$attach = unserialize($pquestions[0]['type_data']);

			$this->db->where('id', $attach[1]['id']);
            $this->db->from('wen_attach');
            $this->db->select('id,savepath');
            $data['attaches'] = $this->db->get()->result_array();


		}

        //get next and forward link
        $this->db->where('state', 1);
		$this->db->where('id > ', $param);
		$this->db->select("wen_questions.id");
		$this->db->limit(1);
		$this->db->from('wen_questions');
		$this->db->order_by("wen_questions.id", "desc");
        $data['forward'] = $this->db->get()->result_array();

        $this->db->where('state', 1);
		$this->db->where('id < ', $param);
		$this->db->select("wen_questions.id");
		$this->db->limit(1);
		$this->db->from('wen_questions');
		$this->db->order_by("wen_questions.id", "desc");
        $data['backward'] = $this->db->get()->result_array();

        //get answers
		$this->db->where('wen_answer.state', 1);
		$this->db->where('wen_answer.qid', $param);
		if($this->uid!=$data['questions'][0]['fUid']){
			$this->db->where('wen_answer.uid', $this->uid);
		}

		$this->db->select('wen_answer.*,users.username,users.id as uid');
		$this->db->from('wen_answer');
		 $this->db->join('users', 'users.id = wen_answer.uid');
		//$this->db->join('user_profile', 'user_profile.user_id = wen_answer.uid', 'left');
		$this->db->order_by("wen_answer.id", "DESC");
		$data['answers'] = $this->db->get()->result_array();
		//var_dump($data['answers']);
		//deal state
		if($data['questions'][0]['cdate']<time()-3600*24*30){
		   $updateData['state'] = 4;
           $this->common->updateTableData('wen_questions',$param,'',$updateData);
           $data['diff'] = '该问题已过期！';
           $this->session->set_flashdata('msg', $this->common->flash_message('error', '该问题已更新为过期状态！'));
           redirect('user/dashboard');
		}else{
			$data['answerauth'] = rand('32523221','99879898');
			$this->session->set_userdata(array('answerauth'=>$data['answerauth']));
			$data['diff'] = '离问题过期还有'.( 7-round(abs(strtotime(date('y-m-d'))-$data['questions'][0]['cdate'])/86400, 0 )).'天';
		}
		if($data['roleId']==1 && $data['questions'][0]['fUid']==$this->uid && $data['questions'][0]['new_answer']>0){
			$updateData =array();
            $updateData['new_answer'] = 0;
            $this->common->updateTableData('wen_questions',$param,'',$updateData);
            $num = $data['questions'][0]['new_answer'];
            $str="UPDATE `wen_notify` SET new_answer=new_answer-{$num} WHERE user_id = {$this->uid}";
            $this->db->query($str);
		}
		if($data['roleId']==2 OR ($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id')))){
          //  $temres = $this->db->query("SELECT new_reply FROM question_state WHERE uid ={$this->uid} AND qid={$param} ")->result();
            $this->db->query("UPDATE `question_state` SET `new_reply` = 0  WHERE uid ={$this->uid} AND qid={$param} ");
		}
		$data['message_element'] = "question";
		$this->load->view('template', $data);
	}
	public function view($param = ''){
		$data['notlogin'] = $this->notlogin;
		$param = intval($param);
		$sql = "SELECT `wen_questions`.*, `users`.`username`, `user_profile`.`sex`, `user_profile`.`city` FROM (`wen_questions`)  JOIN `users` ON `users`.`id` = `wen_questions`.`fUid` LEFT JOIN `user_profile` ON `user_profile`.`user_id` = `wen_questions`.`fUid` WHERE  `wen_questions`.`id` = '{$param}' ";
		$data['results'] = $this->db->query($sql)->result_array();
		$data['message_element'] = "questionView";



		$this->db->where('wen_answer.state', 1);
		$this->db->where('wen_answer.qid', $param);

		$this->db->select('wen_answer.*, users.username,user_profile.sex,user_profile.city');
		$this->db->from('wen_answer');
		$this->db->join('users', 'users.id = wen_answer.uid');
		$this->db->join('user_profile', 'user_profile.user_id = wen_answer.uid', 'left');
		$data['answers'] = $this->db->get()->result_array();
		if($data['results'][0]['cdate']<time()-3600*24*30){
           $data['diff'] = '该问题已过期！';
		}else{
			$data['answerauth'] = rand('32523221','99879898');
			$this->session->set_userdata(array('answerauth'=>$data['answerauth']));
			$data['diff'] = '离问题过期还有'.( 7-round(abs(strtotime(date('y-m-d'))-$data['results'][0]['cdate'])/86400, 0 )).'天';
			if($this->uid==$data['results'][0]['fUid']){
			//	$this->db->query("UPDATE `wen_questions` SET `new_answer` = 0   WHERE `id` ={$qid}");
			}
		}
		$this->load->view('template', $data);
	}

	public function answer() {
		//var_dump($this->session->all_userdata());die;
		if($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id'))) {
			$this->uid = $this->session->userdata('yishi_id');
		}

		$data['notlogin'] = $this->notlogin;
		if (!$this->notlogin && ($qid =$this->input->post('qid')) ) {
			if(!$this->session->userdata('answerauth') || $this->session->userdata('answerauth')!=$this->input->post('answerauth') ){
				 $this->session->set_flashdata('msg',$this->common->flash_message('error', '您没有权限进行回答！') );
                  redirect('question/'.$this->input->post('qid'));
			}
           //check send info
           if(is_int(strpos($this->input->post('myaswer'),'联系方式'))){
           	  $this->session->set_flashdata('msg',$this->common->flash_message('error', '回答信息不能含有联系信息！') );
              redirect('question/'.$this->input->post('qid'));
           }
			$this->load->library('sms');
            $this->load->library('filter');
            $myaser =  $this->input->post('myaswer');
            $this->filter->filts($myaser,true);
			if(strlen($myaser)>10 && $this->uid){
			   $tmp = $this->db->query("SELECT id FROM wen_answer WHERE qid = {$this->input->post('qid')}  GROUP BY uid ORDER BY uid")->result_array();
			   if(count($tmp)>6){
                $this->session->set_flashdata('msg',$this->common->flash_message('error', '该问题参与医生回答用户已经超过！') );
			   }else{
			   	$data = array (
				'uid' => $this->uid,
				'qid' => $this->input->post('qid'),'new_comment'=>'1',
				'content' => $myaser,
				'state' => 1,'cdate' => time());
			   	if($this->session->userdata('admin_answer') && 16 == intval($this->session->userdata('WEN_role_id'))) {
			   		$data['who'] = $this->session->userdata('WEN_role_id');
			   	} else {
			   		$data['who'] = 0;
			   	}

			$tmp_c = $this->db->query("SELECT id FROM wen_answer WHERE qid = {$this->input->post('qid')} AND uid ={$this->uid} ORDER BY uid")->result_array();
			if(!empty($tmp_c)){
              $this->session->set_flashdata('msg',$this->common->flash_message('error', '您已经回答过不能重复回答，可以通过交谈和客户继续沟通！') );
              redirect('question/'.$this->input->post('qid'));
			}

			$this->db->insert('wen_answer', $data);
			$this->db->query("UPDATE `users` SET `rank_search` = `rank_search`+1,replys=replys+1 WHERE `id` ={$this->uid}");

			$this->session->set_flashdata('msg',$this->common->flash_message('success', '回答成功！') );

			//send sms
			 $tmp = $this->db->query("SELECT users.phone,users.email,users.id FROM users LEFT JOIN wen_questions ON wen_questions.fUid=users.id WHERE wen_questions.id = {$this->input->post('qid')} LIMIT 1")->result_array();

			 if(!empty($tmp)){
                $tmp[0]['phone']&&$this->sms->sendSMS(array (
						"{$tmp[0]['phone']}"
					), '亲爱的用户，你的咨询已被医师回复，请打开"美丽神器"手机APP查看回复内容。（本信息免费）退订回复TD');
                //update memory
                $mec = new Memcache();
                $mec->connect('127.0.0.1', 11211);
                $mec->set('state'.$tmp[0]['id'],'');
                $mec->close();
                 //send apple push
               $this->load->model('push');
               $push = array('type'=>'zixun','qid'=>$qid,'uid'=>$this->uid);
               $this->push->sendUser('你的咨询已被医师回复',$tmp[0]['id'],$push);

			    $this->db->query("UPDATE `wen_notify` SET `new_answer` = `new_answer`+1   WHERE `user_id` ={$tmp[0]['id']}");
			    $qinfo = $this->db->query("SELECT  title FROM wen_questions WHERE `id` ={$this->input->post('qid')}")->result_array();

			    $splVars = array (
				"{title}" => $qinfo[0]['title'], "{content}" =>'亲爱的用户，你的咨询'.$qinfo[0]['title'].'已被医师回复，请打开"美丽神器"手机AP查看回复内容.', "{time}" =>date('Y-m-d H:i',time()), "{site_name}" => '美丽神器');

			    $tmp[0]['email']!=''&& $this->Email_model->sendMail($tmp[0]['email'], "support@meilizhensuo.com", '美丽神器', 'yishi_reply', $splVars);
			   }
               $this->db->query("UPDATE `wen_questions` SET `new_answer` =`new_answer`+ 1,`has_answer` =`has_answer`+ 1   WHERE `id` ={$qid}");

			 }
			}else{
                $this->session->set_flashdata('msg',$this->common->flash_message('error', '回答内容太短！') );
			}
		 	redirect('question/'.$this->input->post('qid'));
		} else {
			redirect('user/dashboard');
		}
	}
}
?>
