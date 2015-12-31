f<?php
class home extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id()==16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->library('yisheng');
		$this->load->library('tehui');
		$this->load->library('sms');
		$this->load->model('privilege');
        $this->privilege->init($this->uid);
       if(!$this->privilege->judge('home')){
          die('Not Allow');
       }
	}
	public function index($param='') {
		$this->load->library('city');
		$this->load->library('pager');
		$per_page = 16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;
       $data['city'] = &$this->city;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;

		$data['data'] = $this->db->query("SELECT yuyue.*,u.alias as funame,users.alias,users.role_id FROM (`yuyue`) LEFT JOIN users ON users.id=yuyue.userto LEFT JOIN users as u ON u.id=yuyue.userby WHERE is_delete=0 GROUP BY yuyue.id ORDER BY yuyue.id DESC LIMIT $offset, $per_page ")->result_array();
		$data['total_rows'] = $this->db->query("SELECT yuyue.id FROM (`yuyue`) WHERE is_delete=0 GROUP BY yuyue.id")->num_rows();
		//$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/home/index/' . ($start -1)) : site_url('manage/home/');
		//$data['next'] = $offset+$per_page < $data['total_rows'] ? site_url('manage/home/index/' . ($start +1)) : site_url('manage/home/index/'.$start);
         $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
               // 'querystring_name'=>$fixurl.'page',
                'base_url'=>'manage/home/index',
                "pager_index"=>$start
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['yyres'] = $this->GCOP();
		$data['notlogin'] = $this->notlogin;
	    $data['managers'] = $this->Gmanager();
        $this->session->set_userdata('history_url', 'manage/home?page=' . $start);
        $data['message_element'] = "home";
		$this->load->view('manage', $data);
	}
	//sumarize
	public function tongji(){
		if($this->input->get('yuyueDateStart')){
            $data['cdate'] =  $this->input->get('yuyueDateStart');
            $data['edate'] =  $this->input->get('yuyueDateEnd');
		}else{
			$data['cdate'] =  date('Y-m-d');
			$data['edate']  = date("Y-m-d",strtotime("+1 day"));
		}
		$data['res'] = $this->sumOrder($data['cdate'],$data['edate']);
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "home_tongji";
		$this->load->view('manage', $data);
	}
	//add account
	public function ajaxAdd($sn=''){
		if($sn){
			if($this->input->get('comdata')){
               $query = $this->db->get_where('company', array('company.userid' => intval($this->input->get('comdata'))), 1)->result_array();
			   $comname = $query[0]['name'];
			}else{
               $comname = '';
			}
			$data = array(
               'uid' => $this->uid,
               'sn' => $sn,
               'jiesuan' => strtotime($this->input->get('addval')),
               'cmid' => intval($this->input->get('comdata')),
               'company' => $comname,
               'cdate' => time()
             );
          $this->db->insert('yueyueLog', $data);
		}

	}
	private function sumOrder($tart,$end=''){
		$tart = strtotime($tart);
		$this->db->where('cdate >= ', $tart);
        if(!$end){
        	$end = 3600*24+strtotime($tart);
        }else{
        	$end = strtotime($end);
        }
        $this->db->select('count(yuyueSend.id) as num,users.alias');
        $this->db->group_by(array("fuid"));
        $this->db->where('cdate <= ', $end);
        $this->db->from('yuyueSend');
        $this->db->join('users', 'users.id = yuyueSend.fuid');
        return $this->db->get()->result_array();

	}
	//get manager
	private function Gmanager(){
       $this->db->where('role_id', 16);
       $this->db->where('banned', 0);
       $this->db->select('id, alias');
       $this->db->from('users');
       return $this->db->get()->result_array();
	}
	//get cooperate hospital
	private function GCOP(){
       $this->db->where('state', 1);
       $this->db->select('userid, name,city, id');
       $this->db->from('company');
       return $this->db->get()->result_array();
	}
	//order detail
	public function detail($param=''){
        if($id = intval($param)){
        	$data['params'] = $id;
        	if($this->input->post('shoushu')){
        		$updateData = array();
        		$updateData['state'] = intval($this->input->post('state'));
        		$updateData['amout'] = $this->input->post('amout');
        		$updateData['name'] = $this->input->post('uname');
        		$updateData['city'] = $this->input->post('city');
        		$updateData['phone'] = $this->input->post('phone');
        		$updateData['ystate'] = $this->input->post('ystate');
        		$updateData['shoushu'] = $this->input->post('shoushu');
        		$updateData['remark'] = $this->input->post('remark');
        		$updateData['cremark'] = $this->input->post('cremark');
        		$updateData['nextdate'] = strtotime($this->input->post('nextdate'));
        		$updateData['admin_remark'] = $this->input->post('admin_remark');
        		$this->common->updateTableData('yuyue',$id,'',$updateData);
        		if($chongdan = $this->input->post('chongdan')){
                  foreach($chongdan as $r){
                    $data = array('chongdan' => 1);
                    $this->db->where('id', $r);
                    $this->db->update('yuyueSend', $data);
        		  }
        		}
        		if($this->input->post('savepaidan')){
        			redirect('manage/home/paidan/'.$id);
        		}else{
        			redirect($this->session->userdata('history_url'));
        		}

        	}
            $data['param'] = $param;
           $data['res'] = $this->db->query("SELECT yuyue.*,user_profile.city as city2,users.alias,users.phone as uphone,users.role_id FROM (`yuyue`) LEFT JOIN user_profile ON user_profile.user_id=yuyue.userto LEFT JOIN users ON users.id=yuyue.userto where yuyue.id = {$id} ORDER BY yuyue.id DESC LIMIT 1")->result_array();
           //chong dan
           $this->db->select('yuyueSend.id,yuyueSend.chongdan,users.alias,users.id as uid');
		   $this->db->from('yuyueSend');
		   $this->db->where('yuyueSend.sn', $data['res'][0]['sn']);
		   $this->db->order_by("yuyueSend.uid", "desc");
		   $this->db->join('users', 'users.id = yuyueSend.uid');
           $data['chongdan'] = $this->db->get()->result_array();
           $data['notlogin'] = $this->notlogin;
           $data['message_element'] = "yuyue_view";
		   $this->load->view('manage', $data);
        }
	}
	public function search($param='') {
		$start = intval($this->input->get('page'));
		$per_page = 16;
		$condition = ' WHERE is_delete=0 ';
		$fixurl = '';
		$this->load->library('pager');
		if($this->input->get('name')) {
			$fixurl.='name='.$this->input->get('name').'&';
			$condition .= ' AND yuyue.name="'.$this->input->get('name').'" ';
		}
		if($this->input->get('jiesuan')) {
			$fixurl.='jiesuan='.$this->input->get('jiesuan').'&';
			$condition .= ' AND yueyueLog.jiesuan="'.strtotime($this->input->get('jiesuan')).'" ';
		}
		if($this->input->get('ssstate')) {
			$fixurl.='ssstate='.$this->input->get('ssstate').'&';
			$condition .= ' AND yuyue.shoushu="'.$this->input->get('ssstate').'" ';
		}
		if($secuid = intval($this->input->get('ome'))) {
			$fixurl.='ome='.$this->input->get('ome').'&';
			$condition .= ' AND (yuyueSend.fuid="'.$secuid.'" OR yuyue.fuid="'.$secuid.'") ';
		}
		if($this->input->get('newmessage')) {
			$fixurl.='newmessage='.$this->input->get('newmessage').'&';
			$ctime = time()-24*3600;
			$condition .= ' AND yuyueTalk.fuid!=yuyueSend.fuid AND yuyueTalk.cdate>='.$ctime ;
		}
		if($this->input->get('nop') and !$this->input->get('ysp')) {
			$fixurl.='nop='.$this->input->get('nop').'&';
			$condition .= ' AND yuyue.sendState=0';
		}
		if($this->input->get('ysp') and !$this->input->get('nop')) {
			$fixurl.='ysp='.$this->input->get('ysp').'&';
			$condition .= ' AND yuyue.sendState>0';
		}
		if($this->input->get('tnc')) {
			$fixurl.='tnc='.$this->input->get('tnc').'&';
			if($this->input->get('yuyueDateStart')){
				$fixurl.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
				$fixurl.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
               $estart = strtotime($this->input->get('yuyueDateStart'));
               $end = strtotime($this->input->get('yuyueDateEnd'));
               $datys  = '';
               for(;$estart<=$end;$estart+=3600*24){
                   $datys.= ','.$estart;
               }
               $datys = substr($datys,1);
               $condition .= " AND yuyue.nextdate IN({$datys}) ";
			}else{
			 	$condition .= ' AND yuyue.nextdate='.strtotime(date('Y-m-d'));
			}
		}
		if($this->input->get('ID')) {
			$fixurl.='ID='.$this->input->get('ID').'&';
		 	$condition .= ' AND yuyue.userby='.$this->input->get('ID');
		}
		if($this->input->get('province')) {
			$fixurl.='province='.$this->input->get('province').'&';
		 	$condition .= " AND yuyueSend.address like '%".$this->input->get('province')."%'";
		}
		if($this->input->get('yy')) {
			$fixurl.='yy='.$this->input->get('yy').'&';
		 	$condition .= " AND yuyue.sn IN(SELECT sn FROM yuyueSend LEFT JOIN users ON users.id=yuyueSend.uid WHERE users.alias Like '%".$this->input->get('yy')."%')";
		}
		if($this->input->get('city')) {
			$fixurl.='city='.$this->input->get('city').'&';
		 	$condition .= " AND yuyueSend.address like '%".$this->input->get('city')."%'";
		}
		if($this->input->get('chongdan')) {
			$fixurl.='chongdan='.$this->input->get('chongdan').'&';
		 	$condition .= ' AND yuyueSend.chongdan= 1' ;
		}
		if($this->input->get('gktype')) {
			$fixurl.='gktype='.$this->input->get('gktype').'&';
		 	$condition .= ' AND yuyue.ystate= '.$this->input->get('gktype') ;
		}
		if(!$this->input->get('tnc')){
		if($this->input->get('yuyueDateStart')) {
			$fixurl.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
		 	$condition .= ' AND yuyueSend.cdate>='.strtotime($this->input->get('yuyueDateStart'));
		}
		if($this->input->get('yuyueDateEnd')) {
			$fixurl.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
		 	$condition .= ' AND yuyueSend.cdate<='.strtotime($this->input->get('yuyueDateEnd'));
		}}
        if($this->input->get('phone')) {
        	$fixurl.='phone='.$this->input->get('phone').'&';
			$condition .= ' AND yuyue.phone="'.$this->input->get('phone').'" ';
		}

		if ($start > 1)
			$offset = ($start -1) * $per_page;
		else{
			$start = 1;
			$offset = 0;
		}
 		$data['data'] = $this->db->query("SELECT yuyue.*,u.alias as funame,users.alias,users.role_id FROM (`yuyue`) LEFT JOIN users ON users.id=yuyue.userto LEFT JOIN yueyueLog ON yueyueLog.sn=yuyue.sn LEFT JOIN users as u ON u.id=yuyue.userby LEFT JOIN yuyueSend ON yuyueSend.sn=yuyue.sn  LEFT JOIN yuyueTalk ON yuyueTalk.talkid=yuyueSend.id ".$condition." GROUP BY yuyue.id ORDER BY yuyue.id DESC  LIMIT $offset, $per_page ")->result_array();
     //   echo $this->db->last_query();
		$data['total_rows'] = $this->db->query("SELECT yuyue.id FROM (`yuyue`) LEFT JOIN yueyueLog ON yueyueLog.sn=yuyue.sn  LEFT JOIN users ON users.id=yuyue.userto LEFT JOIN yuyueSend ON yuyueSend.sn=yuyue.sn  LEFT JOIN yuyueTalk ON yuyueTalk.talkid=yuyueSend.id  ".$condition." GROUP BY yuyue.id  ORDER BY yuyue.id DESC ")->num_rows();
	//	$data['offset'] = $offset +1;
	//	$data['preview'] = $start > 2 ? site_url('manage/home/search/' . ($start -1).$fixurl) : site_url('manage/search/'.$fixurl);
	//	$data['next'] = $offset+$per_page < $data['total_rows'] ? site_url('manage/home/search/' . ($start +1).$fixurl) : site_url('manage/home/search/'.$start.$fixurl);
         $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                'querystring_name'=>$fixurl.'page',
                'base_url'=>'manage/home/search',
                "pager_index"=>$start
            );
         $this->pager->init($config);
         $this->session->set_userdata('history_url', 'manage/home?'.$fixurl.'page=' . ($start -1));
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "home";
        $data['managers'] = $this->Gmanager();
		$this->load->view('manage', $data);
	}
	public function del($param=''){
		$condition = array('id'=>$param);
		$updateData['is_delete'] = 1;
        $this->common->updateTableData('yuyue','',$condition,$updateData);
        redirect($this->session->userdata('history_url'));
	}
	public function paidan($param=''){
		$data['id'] = $param;
		if($this->input->post('hostipt')){
			$condition = array('id'=>$param);
			$tmpdata = $this->common->getTableData('yuyue',$condition)->result_array();
			$idata = array();
			$idata['sn'] = $tmpdata[0]['sn'];
			$idata['uid'] = intval($this->input->post('hostipt'));
			$idata['is_view'] = 0;
			$idata['fuid'] = $this->uid;
			$idata['items'] = trim($this->input->post('items'));
			$idata['contactState'] = 0;
			$idata['address'] = $this->input->post('province').' '. $this->input->post('city');
			$idata['sendremark'] = $this->input->post('remarks');
			$idata['chongdan'] = 0;
			$idata['cdate'] = time();
            $this->common->insertData('yuyueSend',$idata);
            $this->db->query("UPDATE `yuyue` SET `sendState` = `sendState`+1   WHERE `id` ={$param}");

            $this->db->where('id', $idata['uid']);
            $this->db->select('phone');
            $tmp = $this->db->get('users')->result_array();
            if($tmp[0]['phone']){
               $message = "亲爱的用户，您有新的派单 {$idata['sn']}，请尽快上线与客户联系。退订回复0000";
		  	   $this->sms->sendSMS(array ("{$tmp[0]['phone']}"), $message);
            }
            $this->session->set_flashdata('msg', $this->common->flash_message('success', '派单成功!'));
            redirect($this->session->userdata('history_url'));
		}
        $data['message_element'] = "paidan";
        $this->load->view('manage', $data);
	}
	public function addyuyue($param=''){
		if($this->input->post('phone') and $param){
			$idata = array();
			$idata['sn'] = date('YmdHis').rand(100,999);
			$idata['userby'] = intval($param);
			$idata['fuid'] = $this->uid;
			$idata['amout'] = $this->input->post('amout');
			$idata['city'] = $this->input->post('city');
			$idata['remark'] = $this->input->post('remarks');
			$idata['name'] = $this->input->post('name');
			$idata['phone'] = $this->input->post('phone');
			$idata['age'] = $this->input->post('age');
			$idata['yuyueDate'] = strtotime($this->input->post('yuyueDate'));
			$idata['extraDay'] = $this->input->post('extraDay');
			$idata['shoushu'] = '否';
			$idata['state'] = 0;
			$idata['sendState'] = 0;
			$idata['cdate'] = time();
            $this->common->insertData('yuyue',$idata);
            redirect($this->session->userdata('history_url'));
		}
	   $this->db->where('is_delete', 0);
	   $this->db->where('userby', $param);
	   $this->db->from('yuyue');
       if($this->db->count_all_results()){
       	  $this->session->set_flashdata('msg', $this->common->flash_message('error', '用户已存在!'));
       	  redirect('http://www.meilimei.com/manage/search?ID='.$param);
       }
       $this->db->select('users.alias,users.phone,user_profile.city');
       $this->db->from('users');
       $this->db->where('id', $param);
       $this->db->join('user_profile', 'user_profile.user_id = users.id');
       $data['user'] = $this->db->get()->result();
       $data['message_element'] = "addyuyue";
       $this->load->view('manage', $data);
	}
   public function paidan_talk($param=''){
		if($this->input->post('talks') and $param){
			$idata = array();
			$idata['talkid'] = intval($param);
			$idata['fuid'] = $this->uid;
			$idata['touid'] = $this->input->post('touid');
			$idata['message'] = $this->input->post('talks');
			$idata['cdate'] = time();
			$idata['is_read'] = 1;
            $this->common->insertData('yuyueTalk',$idata);
            redirect('manage/home/paidan_talk/'.$param);
		}
	   $data['user'] = $this->db->get_where('yuyueSend', array('id'=>$param))->result();
	   $data['talk'] = $this->db->get_where('yuyueTalk', array('talkid'=>$param))->result();
       $data['message_element'] = "paidan_talk";
       $this->load->view('manage', $data);
	}
	public function paidan_del($param=''){
		$this->db->where('id', $param);
		$query = $this->db->get('yuyueSend')->result_array();
		$this->db->delete('yuyueSend', array('id' => $param));
		$this->db->query("update yuyue set sendState=sendState-1 where sn = '$query[0]['sn']' limit 1");
		redirect('manage/home/paidan_track/'.$query[0]['sn']);
	}
	public function paidan_track($param=''){
		$this->db->select('yuyueSend.*,users.alias');
		$this->db->from('yuyueSend');
		$this->db->where('yuyueSend.sn', $param);
		$this->db->order_by("yuyueSend.uid", "desc");
		$this->db->join('users', 'users.id = yuyueSend.uid');
        $data['res'] = $this->db->get()->result_array();

        //update talk state
        $updata = array(
               'is_read' => 0
        );
        $this->db->where('touid', $this->uid);
        $this->db->update('yuyueTalk', $updata);

        $data['message_element'] = "paidan_track";
        $this->load->view('manage', $data);
	}
	public function syncUserInfo(){
		//var_dump($this->config->item('WEN_salt'));
		$this->tehui->putZuiTuUserInfo();
		$this->session->set_flashdata('msg', $this->common->flash_message('success', '数据导入成功!'));
		redirect('manage/home');
	}
}
?>
