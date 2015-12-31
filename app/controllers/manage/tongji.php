<?php
class tongji extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('tongji')){
          die('Not Allow');
       }
	}
	public function index($page = '') {
		$data['question_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE device_sn!=''")->num_rows();
		$data['question_trows'] = $this->db->query("SELECT id FROM wen_questions ")->num_rows();
		$data['ansrows'] = $this->db->query("SELECT id FROM wen_answer ")->num_rows();
		$data['eansrows'] = $this->db->query("SELECT id FROM wen_answer GROUP BY qid")->num_rows();
		$data['talkrows'] = $this->db->query("SELECT id FROM wen_comment WHERE  type = 'qa' GROUP BY contentid ")->num_rows();
		$data['contactUser'] = $this->db->query("SELECT user_id FROM user_profile WHERE  states = 1 ")->num_rows();
		$data['users'] = $this->db->query("SELECT count(id) as num FROM users  GROUP BY role_id")->result();
        $data['yuyuerows'] = $this->db->query("SELECT count(id) as num FROM yuyue where is_delete=0")->result();
        $data['yuyueByUserrows'] = $this->db->query("SELECT count(id) as num FROM yuyue where is_delete=0 and userby=0")->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "tongji";
		$this->load->view('manage', $data);
	}
	public function fenduan2(){
		if($this->input->get('yuyueDateStart')){
            $data['cdate'] =  $this->input->get('yuyueDateStart');
            $data['edate'] =  $this->input->get('yuyueDateEnd');
		}else{
			$data['cdate'] =  date('Y-m-d');
			$data['edate']  = date("Y-m-d",strtotime("+1 day"));
		}
		$data['newJg'] = $data['sumQ'] = array();
		$stime = strtotime($data['cdate']);
		for(;$stime<strtotime($data['edate']);$stime+=3600*24){
           $tmprg = $this->sumNewjigou($stime);
           $smprq = $this->sumQuestion($stime);
           $data['newJg'][] = array('day'=>date('d',$stime),'jg'=>$tmprg['jg'],'us'=>$tmprg['us'],'ys'=>$tmprg['ys']);
           $data['sumQ'][] = array('day'=>date('d',$stime),'has'=>$smprq['has'],'no'=>$smprq['no']);
		}
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "fenduan_2";
		$this->load->view('manage', $data);
	}
   //total online user one day
   public function online(){
        if($this->input->get('yuyueDateStart')){
            $data['cdate'] =  $this->input->get('yuyueDateStart');
            $data['edate'] =  $this->input->get('yuyueDateEnd');
		}else{
			$data['cdate'] =  date('Y-m-d');
			$data['edate']  = date("Y-m-d",strtotime("+1 day"));
		}
		$data['res']  = array();
		$stime = strtotime($data['cdate']);
		for(;$stime<strtotime($data['edate']);$stime+=3600*24){
           $data['res'][] = array('num'=>$this->sumUserOnline($stime),'day'=>date('d',$stime));
		}
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "tongji_online";
		$this->load->view('manage', $data);
   }
	//no and yes ans question
	private function sumUserOnline($tart,$end=''){
        $this->db->where('last_login >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('count(*) as num');
        $this->db->where('last_login <= ', $end);
        $this->db->from('users');
        $tmp = $this->db->get()->result_array();
        return $tmp[0]['num'];
	}
	//no and yes ans question
	private function sumQuestion($tart,$end=''){
        $this->db->where('cdate >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('count(id) as num,has_answer');
        $this->db->group_by('has_answer');
        $this->db->where('cdate <= ', $end);
        $this->db->from('wen_questions');
        $tmp = $this->db->get()->result_array();
        $res = array();
        $res['has'] = $res['no'] = 0;
        foreach($tmp as $r){
        	if($r['has_answer']>0){
        		$res['has'] = $r['num'];
        	}else{
        		$res['no'] = $r['num'];
        	}
        }
        return $res;
	}
	//new jigou and doctor nums
	private function sumNewjigou($tart,$end=''){
        $this->db->where('created >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('count(id) as num,role_id');
        $this->db->group_by('role_id');
        $this->db->where('created <= ', $end);
        $this->db->from('users');
        $tmp = $this->db->get()->result_array();
        $res = array();
        $res['ys'] = $res['jg'] = $res['us'] = 0;
        foreach($tmp as $r){
        	if($r['role_id']==2){
        		$res['ys'] = $r['num'];
        	}elseif($r['role_id']==3){
                $res['jg'] = $r['num'];
        	}else{
        		$res['us'] = $r['num'];
        	}
        }
        return $res;
	}
	public function fenduan(){
		if($this->input->get('yuyueDateStart')){
            $data['cdate'] =  $this->input->get('yuyueDateStart');
            $data['edate'] =  $this->input->get('yuyueDateEnd');
		}else{
			$data['cdate'] =  date('Y-m-d');
			$data['edate']  = date("Y-m-d",strtotime("+1 day"));
		}
		$data['userReg'] = array();
		$data['qsuTotal'] = array();
		$data['qsTotal'] = array();
		$stime = strtotime($data['cdate']);
		for(;$stime<strtotime($data['edate']);$stime+=3600*24){
			$tmprg = $this->sumRuserByDay($stime);
            $tmpqs = $this->sumConsultUserByDay($tmprg,$stime);
            $data['userReg'][]= array('day'=>date('d',$stime),'sum'=>count($tmprg),'rate'=>$tmpqs['rate']);

            $data['qsTotal'][]= array('day'=>date('d',$stime),'sum'=>$tmpqs['total'],'qsum'=>$this->sumConsultQsByDay($stime));
		}

        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "fenduan";
		$this->load->view('manage', $data);
	}

	//New registered users
	private function sumRuserByDay($tart,$end=''){
        $this->db->where('created >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('id');
        $this->db->where('created <= ', $end);
        $this->db->from('users');
        $tmp = $this->db->get()->result_array();
        $res = array();
        foreach($tmp as $r){
          $res[] = $r['id'];
        }
        return $res;
	}
	//the consulting question nums
	private function sumConsultQsByDay($tart,$end=''){
        $this->db->where('cdate >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('count(id) as num');
        $this->db->where('cdate <= ', $end);
        $this->db->from('wen_questions');
        $tmp = $this->db->get()->result_array();
        return $tmp[0]['num'];
	}
	//The new consulting users
	private function sumConsultUserByDay($uids,$tart,$end=''){
        $this->db->where('cdate >= ', $tart);
        if(!$end){
        	$end = 3600*24+$tart;
        }
        $this->db->select('distinct(fUid)');
       // $this->db->group_by("uid");
        $this->db->where('cdate <= ', $end);
        $this->db->from('wen_questions');
        $query = $this->db->get()->result_array();
        $res['rate'] = 0;
        $res['total'] = count($query);
        foreach($query as $r){
           if(array_search($r['fUid'],$uids)){
              $res['rate']++;
           }
        }
        $res['rate'] = intval($res['rate']/count($uids)*100);
        return $res;
	}
}
?>
