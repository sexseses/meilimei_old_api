<?php
set_time_limit(0);
class cron extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->model('push');
	}
	//notify  nolonger use app user
	public function checkUse() {
		//$this->db->where('uid', $uid);
		$ctime = time()+3600*8;
		$corns = $this->db->get('crons')->result_array();
        $this->db->select('devicetoken,uid');
		$this->db->where('pushalert', 'enabled');
		$users = $this->db->get('apns_devices')->result_array();


        foreach($corns as $l){
        	//deal send once
           if($l['datetype']==2 and $l['sdate']<=$ctime){
              $this->db->where('id', $l['id']);
              $this->db->delete('crons');
           }elseif($l['datetype']==2 and $l['sdate']>$ctime){
               continue;
           }
           if($l['usertype']==1){
              foreach ($users as $r) {
				$this->push->send($l['message'], $r['devicetoken'], $r['pushmusic']);
			  }
           }else{
           	  $users = explode(',',$l['suser']);
              foreach ($users as $r) {
              	$this->push->sendUser($l['message'], $r);
			  }
           }

        }
	}
}
?>