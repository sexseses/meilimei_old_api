<?php
/*
 * WENRAN user visit manage
 *
 */
class user_visit extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}

	public function add($uid,$vuid,$state,$remark,$nxtdate=0){
       $data = array(
               'uid' =>$uid,
               'vuid' =>$vuid,
               'state' => $state,
               'remark' => $remark ,
               'nxtdate' => $nxtdate,
               'cdate' => time()
            );
      $this->db->insert('user_visit', $data);
      $data = array(
               'states' => $state
       );
      $this->db->where('user_id', $vuid);
      $this->db->update('user_profile', $data);
	}
	public function view($uid){
	   $sql = "select user_visit.*,users.alias ";
       $sql .="FROM user_visit LEFT JOIN users ON users.id = user_visit.uid ";
       $sql .="where user_visit.vuid = {$uid} ";
       $sql .="ORDER BY user_visit.id DESC";
       return $this->db->query($sql)->result_array();
	}
	public function today($uid){
       $time = strtotime(date('Y-m-d',time()));
	   $sql = "select user_visit.*,users.alias ";
       $sql .=" FROM user_visit LEFT JOIN users ON users.id = user_visit.vuid ";
       $sql .=" LEFT JOIN user_profile ON user_profile.user_id = user_visit.vuid ";
       $sql .=" where user_visit.uid = {$uid} and user_visit.nxtdate={$time}";
       $sql .=" ORDER BY user_visit.id DESC limit 100";
       return $this->db->query($sql)->result();
	}
	public function total($uid,$type=3,$tag='topic',$stime,$etime){
       $this->db->where('uid', $uid);
        $this->db->where($tag.' > ', 0);
       $this->db->where('type & ', $type);
       $this->db->where('cdate <= ', $etime);
       $this->db->where('cdate >= ', $stime);
       $this->db->from('user_track');
      return  $this->db->count_all_results();
	}
	public function tagSum($uid=0,$type=3,$stime,$etime){
	   $sql = "SELECT COUNT(id) as sum, tags FROM user_track where type & {$type} ";
       $sql .="AND cdate <= {$etime} AND cdate >= {$stime} AND tags!='' ";
       $uid>0&&$sql .="AND uid  = {$uid}  ";
       $sql .=" GROUP BY tags";
       return $this->db->query($sql)->result_array();
	}
}
?>
