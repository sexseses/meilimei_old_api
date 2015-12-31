<?php
/*
 * WENRAN user jifen and level manage
 * @access public
 * @param $J_table   积分设定表
 * @param $L_table   用户等级权限表
 */
class user_sum extends CI_Model {
	public $J_table = 'settings', $L_table = 'user_group';
	private $uid,$mec = null;
	public function __construct() {
		parent :: __construct();
	}

	// add user action score
	public function addJifen($uid, $type) {
		/*$this->db->from($this->J_table);
		$this->db->where('code', $type);
		$tmp = $this->db->get()->result_array();
		if (!empty ($tmp)) {
			$sql = "UPDATE users SET jifen=jifen+{$tmp[0]['int_value']} WHERE id = {$uid} limit 1";
			 $this->db->query($sql);
			return true;
		}
		return false;*/
	}
	//check today could add jifen and growth
    private function ckAplus($uid,$plus,$limit){
        /*if($this->uid){
        	if(!$this->mec){
        		$this->mec = new Memcache();
                $this->mec->connect('127.0.0.1', 11211);
        	}
        	$lasttime = 24-date('H');
        	if($tmp = $this->mec->get('plus_jifen_'.$this->uid)){
                if($tmp>=$limit){
                	$this->mec->close();
                    return false;
                }else{
                	$tmp += $plus;
                	$this->mec->set('plus_jifen_'.$this->uid,$tmp,0,$lasttime*3600);
                }
        	}else{
        		$this->mec->set('plus_jifen_'.$this->uid,$plus,0,$lasttime*3600);
        	}
        	$this->mec->close();
        	return true;
        }else{
        	return false;
        }*/
    }
	// add user action Grow Score
	public function addGrowth($uid, $type) {
		/*$this->db->from($this->J_table);
		$this->db->where('code', $type);
		$tmp = $this->db->get()->result_array();
		if (!empty ($tmp)) {
			$sql = "UPDATE users SET growthv=growthv+{$tmp[0]['int_value']} WHERE id = {$uid} limit 1";
			if($this->ckAplus($uid,$tmp[0]['int_value'],200)){
			  $this->db->query($sql);
			}
			return true;
		}
		return false;*/
	}
	//check user level
	public function userLevel($uid, $level = '') {
		if ($level == '') {
			$this->db->from('users');
			$this->db->where('id', $uid);
			$tmp = $this->db->get()->result_array();
			$level = $tmp[0]['growthv'];
		}
		$this->db->from($this->L_table);
		$this->db->where('type', 'system');
		$this->db->where('creditshigher', $level);
		$this->db->where('creditslower', $level);
		return $this->db->get()->result_array();
	}
}
?>
