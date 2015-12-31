<?php
class questions extends CI_Controller {
	private $notlogin = true,$uid='',$imgurl = "http://pic.meilimei.com.cn/upload/",$qiniuimgurl="http://7xkdi8.com1.z0.glb.clouddn.com/";
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id()==16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('questions')){
          die('Not Allow');
       }
	}
	// no use
	public function order($param=''){
		$this->load->library('pager');
		$condition = '  WHERE 1 ';
		if ($param==1) {
	        $condition .= "  AND w.id IN (SELECT fuid FROM talk where qid={$v->id} order by id DESC limit 1 ) ";
		}else{
			$condition .= "  AND w.id IN () ";
		}
		$data['total_rows'] = $this->db->query("SELECT id FROM wen_questions ")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT w.id,w.title,w.description,w.device,w.state,w.cdate, a.id as uid,a.alias,a.email,a.phone FROM wen_questions as w LEFT JOIN users a ON a.id=w.fUid  {$condition} ORDER BY w.id DESC  LIMIT $offset , $per_page")->result();

        foreach($data['results'] as $k => $v){
            $doctors = $this->db->query("SELECT up.* FROM wen_questions as w left join wen_answer ans on ans.qid=w.id left join users up on up.id=ans.uid where w.id={$v->id}")->result_array();
            $tmp = $this->db->query("SELECT fuid FROM talk where qid={$v->id} order by id DESC limit 1 ")->result_array();
            if(!empty($tmp) and $tmp[0]['fuid'] == $v->uid){
            	$data['results'][$k]->has_complete = true;
            }else{
            	$data['results'][$k]->has_complete = false;
            }
            foreach($doctors as $k2 => $v2){
                $data['results'][$k]->doctors .= "<a href=\"".site_url('manage/yishi/detail/'.$v2['id'])."\">".$v2['alias']."</a>"."&nbsp;&nbsp;";
            }
        }

        //var_dump($data['results']);

		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/questions/index/' . ($start -1)) : site_url('manage/questions/');
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/questions/index/' . ($start +1)) : '';
		$config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
               // 'querystring_name'=>$fixurl.'page',
                'base_url'=>'manage/questions/index',
                "pager_index"=>$start
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "question_order";
		$this->load->view('manage', $data);
	}

	public function index($page='') {
		$this->load->library('pager');
		$condition = '  WHERE 1 ';
		$data['issubmit'] = false;
        $fixurl = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        if($this->input->get('yuyueDateStart')){
            $data['issubmit'] = true;
			if ($this->input->get('sname')) {
				$fixurl.='sname='.$this->input->get('sname').'&';
				$condition .= "  AND w.title like '%" . trim($this->input->get('sname')) . "%'";
			}
			$opendate = true;
			if ($this->input->get('uname')) {
				$opendate = false;
				$fixurl.='uname='.$this->input->get('uname').'&';
				$suname = strip_tags(trim($this->input->get('uname')));
				$condition .= "  AND (a.alias like '%" . trim($suname) . "%' OR a.phone= '{$suname}')";
			}

			$fixurl.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
			$fixurl.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
			$data['cdate'] = $this->input->get('yuyueDateStart');
			$data['edate'] = $this->input->get('yuyueDateEnd');
		   $cdate = strtotime($this->input->get('yuyueDateStart'));
           $edate = strtotime($this->input->get('yuyueDateEnd'));
          $opendate&& $condition .= " and w.cdate>= {$cdate} and w.cdate<= {$edate} ";
        }
        if($data['filts'] = $this->input->get('filts')){
        	$fixurl.='filts=1&';
           $group = ' group by a.id ';
           $nums_tmp = $this->db->query("SELECT count(distinct(a.id)) as num FROM wen_questions as w LEFT JOIN users a ON a.id=w.fUid  {$condition} ")->result_array();
        }else{
           $group = '';
           $nums_tmp = $this->db->query("SELECT count(*) as num FROM wen_questions as w LEFT JOIN users a ON a.id=w.fUid  {$condition}  ")->result_array();
        }

		$data['total_rows'] = $nums_tmp[0]['num'];
		$per_page = $data['issubmit'] ? 15 : 10;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;

        /*if($this->input->get('has_complete')){
            $condition .=' and talk.fuid =a.id'; LEFT JOIN talk on w.id= talk.qid
        }*/
		$data['results'] = $this->db->query("SELECT CRM_question.state as acstate,w.id,w.title,w.description,w.device,w.state,w.cdate, a.id as uid,a.alias,p.city,a.email,a.phone FROM wen_questions as w LEFT JOIN users a ON a.id=w.fUid LEFT JOIN user_profile p ON p.user_id=w.fUid LEFT JOIN CRM_question ON CRM_question.qid=w.id {$condition} {$group} ORDER BY w.id DESC  LIMIT $offset , $per_page")->result();

        foreach($data['results'] as $k => $v){
             $doctors = $this->db->query("SELECT up.* FROM wen_questions as w left join wen_answer ans on ans.qid=w.id left join users up on up.id=ans.uid where w.id={$v->id}")->result_array();
            $tmp = $this->db->query("SELECT fuid FROM talk where qid={$v->id} order by id DESC limit 1 ")->result_array();

            if(!empty($tmp) and $tmp[0]['fuid'] == $v->uid){
            	$data['results'][$k]->has_complete = true;
            }else{
            	$data['results'][$k]->has_complete = false;
            }
            foreach($doctors as $k2 => $v2){
                $data['results'][$k]->doctors .= "<a href=\"".site_url('manage/yishi/detail/'.$v2['id'])."\">".$v2['alias']."</a>"."&nbsp;&nbsp;";
            }
        }
        $data['has_complete'] = $this->input->get('has_complete') ? $this->input->get('has_complete') : 0;
        //var_dump($data['results']);

		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/questions/index/' . ($start -1)) : site_url('manage/questions/');
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/questions/index/' . ($start +1)) : '';
		$config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                 'querystring_name'=>$fixurl.'page',
                'base_url'=>'manage/questions/index',
                "pager_index"=>$start
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "question";
		$this->load->view('manage', $data);
	}
    public function contact($param=''){
       $data = array(
               'uid' => $this->uid ,
               'qid' => intval($param),
               'cdate' => time()
        );
        $this->db->insert('CRM_question', $data);
    }
    public function uinfo($param=''){
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "uinfo";
		$this->load->view('manage', $data);
    }
    public function newest($param='',$page=''){

		$data['issubmit'] = false;
		$data['total_rows'] = $this->db->query("SELECT id FROM talk ")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT  talk.*,u.Fname,u.Lname FROM  talk LEFT JOIN user_profile as u ON u.user_id = talk.fuid where talk.type='qa' ORDER BY  talk.id DESC  LIMIT $offset , $per_page")->result_array();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/questions/newest/index/' . ($start -1)) : site_url('manage/questions/newest/');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/questions/newest/index/' . ($start +1)) : '';
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "question_newest";
		$this->load->view('manage', $data);
    }
    //answer question
    public function comment($param=''){
      if(($qid = intval($param)) and ($fuid = intval($this->input->post('fuid'))) and $comment = $this->input->post('comment')){
          $tmp_c = $this->db->query("SELECT id FROM wen_answer WHERE qid = {$qid} AND uid ={$fuid} ORDER BY uid")->result_array();
			if(!empty($tmp_c)){
              $this->session->set_flashdata('msg',$this->common->flash_message('error', '您已经回答过不能重复回答，可以通过交谈和客户继续沟通！') );
              redirect('manage/questions/detail/'.$qid);
              exit;
			}

          $this->load->library('sms');
         	$data = array (
				'uid' => $fuid,
				'qid' => $qid,'new_comment'=>'1',
				'content' => $comment,
				'state' => 1,'cdate' => time());
        $this->db->insert('wen_answer', $data);
        $this->db->query("UPDATE `users` SET `rank_search` = `rank_search`+1,replys=replys+1 WHERE `id` ={$fuid}");
        //send sms
	     $tmp = $this->db->query("SELECT users.phone,users.email,users.id,users.clientid FROM users LEFT JOIN wen_questions ON wen_questions.fUid=users.id WHERE wen_questions.id = {$qid} LIMIT 1")->result_array();

       if(!empty($tmp)){

         //send sms
                $tmp[0]['phone']&&$this->sms->sendSMS(array (
						"{$tmp[0]['phone']}"
					), '亲爱的用户，你的咨询已被医师回复，请打开"美丽神器"手机APP查看回复内容。（本信息免费）退订回复0000 ');
                //update memory
                $mec = new Memcache();
                $mec->connect('127.0.0.1', 11211);
                $mec->set('state'.$tmp[0]['id'],'');
                $mec->close();
                //send IGTTUI push

               //$result['d'] = $clientid;
               if(!empty($tmp)) {
                   $this->load->library('igttui');
                   $d = $this->igttui->sendMessage($tmp[0]['clientid'], "zixun:" . $qid.":".$fuid.":你的咨询已被医师回复，请打开[美丽神器]手机APP查看回复内容:".$fuid);
                   //$result['d'] = $d;
               }else{
                   //send apple push
                   $this->load->model('push');
                   $push = array('type'=>'zixun','qid'=>$qid,'uid'=>$fuid);
                   $this->push->sendUser('你的咨询已被医师回复',$tmp[0]['id'],$push);
               }
			    $this->db->query("UPDATE `wen_notify` SET `new_answer` = `new_answer`+1   WHERE `user_id` ={$tmp[0]['id']}");
			    $qinfo = $this->db->query("SELECT  title FROM wen_questions WHERE `id` ={$qid}")->result_array();
                $this->load->model('Email_model');
			    $splVars = array (
				"{title}" => $qinfo[0]['title'], "{content}" =>'亲爱的用户，你的咨询'.$qinfo[0]['title'].'已被医师回复，请打开"美丽神器"手机AP查看回复内容.', "{time}" =>date('Y-m-d H:i',time()), "{site_name}" => '美丽神器');

			    $tmp[0]['email']!=''&& $this->Email_model->sendMail($tmp[0]['email'], "support@meilizhensuo.com", '美丽神器', 'yishi_reply', $splVars);
			   }
               $this->db->query("UPDATE `wen_questions` SET `new_answer` =`new_answer`+ 1,`has_answer` =`has_answer`+ 1   WHERE `id` ={$qid}");
                redirect('manage/questions/detail/'.$qid);
      }
    }

    public function detail($param='',$page=''){
    	$condition = '  WHERE 1 ';
		$data['issubmit'] = false;
        $data['tid'] = intval($param);
		$data['total_rows'] = $this->db->query("SELECT id FROM wen_answer WHERE qid = {$param}")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT w.id,w.content,w.cdate,users.id as uid,users.alias,users.email,users.phone FROM wen_answer as w LEFT JOIN users ON users.id=w.uid WHERE w.qid = {$param}  ORDER BY w.id DESC  LIMIT $offset , $per_page")->result();
        if(!isset($data['results'][0]->id)){
         //	$this->session->set_flashdata('msg',$this->common->flash_message('error', '咨询不存在！') );
         //  redirect('manage/questions');
        }
        
        foreach ($data['results'] as &$v){
        	if(!empty($v->imgfile)){
        		$v->imgurl = $this->qiniuimgurl.$v->imgfile;
        	}else{
        		$v->imgurl = $this->imgurl.$v->imgurl;
        	}
        }
        
        $data['qid'] = $param;
		$data['offset'] = $offset +1;

        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "question_detail";
		$data['qresults'] = $this->db->query("SELECT wen_questions.*,users.alias FROM wen_questions  LEFT JOIN users ON users.id=wen_questions.toUid WHERE wen_questions.id = {$param} ORDER BY wen_questions.id DESC  LIMIT 1")->result();
       if(empty($data['qresults'])){
          redirect('manage/questions');
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
            $this->db->select('id,savepath,imgfile');
            $data['attaches'] = $this->db->get()->result_array();
   
            foreach ($data['attaches'] as &$v){
            	if(!empty($v['imgfile'])){
            		$v['imgfile'] = $this->qiniuimgurl.$v['imgfile'];
            	}else{
            		$v['imgfile'] = $this->imgurl.$v['savepath'];
            	}
            }
		}
		$query = $this->db->get_where('wen_questions', array('id > ' => $param), 1, 0)->result_array();
	   	$data['preview'] = isset($query[0]['id'])?site_url('manage/questions/detail/'.$query[0]['id']):'';
	   	$this->db->where('id < ', $param);
	   	$this->db->order_by("id", "desc");
	   	$this->db->limit(1);
	   	$query = $this->db->get('wen_questions')->result_array();
		$data['next'] = isset($query[0]['id'])?site_url('manage/questions/detail/'.$query[0]['id']):'';
		$this->load->view('manage', $data);
    }
   //search doctor
    public function Suser(){
    	if($t = trim($this->input->get('term'))){
    		$tmp = $this->db->query("select id,alias as value,phone from users where (alias like '%{$t}%' OR phone like '%{$t}%') AND role_id=2 LIMIT 9")->result_array();

        $res  = array();
        foreach($tmp as $r){
        	$r['value'] = trim($r['value']);
        	if($r['value']==''){
             $r['value'] = $r['phone'];
        	}
        	$r['label'] = $r['value'] ;
            $res[] = $r;
        }
        echo json_encode($res);
    	}

    }
    public function commentview($param='',$param2=''){
    	$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "comment_detail";
        $data['tid'] = intval($param);
        $data['results'] = $this->getcomment($param,$param2);
        $data['qid'] = $param;
        $data['qans'] = $this->db->query("SELECT * FROM wen_answer WHERE  qid = {$param} and uid = {$param2}  ORDER BY id DESC  LIMIT 1")->result();

		$this->load->view('manage', $data);
    }
	private function getcomment($qid = 0, $uid = 0) {
		$tmp = $this->db->query("SELECT talk.*,user_profile.Lname as tFname FROM talk LEFT JOIN user_profile ON user_profile.user_id = talk.touid WHERE talk.qid = {$qid} AND (talk.fuid = {$uid} OR talk.touid = {$uid}) order by talk.id ASC")->result_array();

		$result = array ();
		foreach ($tmp as $row) {
			$row['haspic'] = 0;
			$row['pic'] = '';$row['urole'] = $uid==$row['fuid']?'医师':'用户';
			if ($t = unserialize($row['data'])) {
				$row['pic'] = base_url() . 'upload/' . $t['linkpic'];
				$row['haspic'] = 1;
			}
			unset ($row['qid']);
			unset ($row['data']);
			$row['cTime'] = date('Y-m-d H:i', $row['cTime']);
			$result[] = $row;
		}
		return $result;
	}
	public function del($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
			$condition=array('id'=>intval($param));
            $this->common->deleteTableData('wen_questions',$condition);

            $condition=array('qid'=>intval($param));
            $this->common->deleteTableData('wen_answer',$condition);

            $condition=array('q_id'=>intval($param));
            $this->common->deleteTableData('wen_weibo',$condition);

            $condition=array('qid'=>intval($param));
            $this->common->deleteTableData('question_state',$condition);
            redirect('manage/questions');
		}
	}
	public function commentdel($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
            $condition=array('id'=>intval($param));
            $this->common->deleteTableData('wen_answer',$condition);
            redirect('manage/questions');
		}
	}
}
?>
