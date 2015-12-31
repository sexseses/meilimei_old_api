<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class sns extends CI_Controller {
	private $notlogin = true;
	private $uid = 0, $path = '';
	public function __construct() {
		parent :: __construct();
		$this->load->library('yisheng');
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->model('auth');
		$this->load->model('remote');
	}
	function add($param = '') {
		$result['state'] = '000';
		$result['updateState'] = '001';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if ($data['uid'] = $this->input->post('followuser')) {
				$this->db->where('fid',$this->uid);
				$this->db->where('uid',$data['uid']);
                $this->db->from('wen_follow');
                if($this->db->count_all_results()){
                    $result['state'] = '006';
                }else{
                	$data['fid'] = $this->uid;
					$data['type'] = 8;
					$result['updateState'] = '000';
					$this->common->insertData('wen_follow', $data);
                }
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function plus($param = '') {
		$result['state'] = '000';
		$result['updateState'] = '001';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if ($data['uid'] = $this->input->post('followuser')) {
				$condition = array (
					'uid' => $data['uid'],
					'fid' => $this->uid
				);
				$this->common->deleteTableData('wen_follow', $condition);
				$result['updateState'] = '000';
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function getstate($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if ($followuser = $this->input->get('followuser')) {
				$result['follow'] = '0';
				$condition = array (
					'uid' => $followuser,
					'fid' => $this->uid
				);
				$tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
				if ($tmp > 0) {
					$result['follow'] = '1';
				}

			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function myfavorites($param = '') {
		//file_put_contents("/var/www/test/logres2",var_export($this->session->userdata('session_id'),true).'----time='.date("Y-m-d h:i:s",time()));
		$result['state'] = '000';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if ($type = $this->input->get('type')) {
				$this->db->where('wen_follow.fid', $this->uid);
				$this->db->where('users.role_id', $type);
				$offset = ($this->input->get('page') - 1) * 10;
				$this->db->limit(10, $offset);
				$result['data'] = array();
				if ($type == 2) {
					$this->db->select('users.tconsult,users.verify,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				} elseif($type == 1) {
					$this->db->select('user_profile.address,user_profile.city,user_profile.user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				}else {
					$this->db->select('company.name,users.verify,company.tel,company.shophours,company.department,company.address,company.city,company.userid as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('company', 'company.userid = wen_follow.uid', 'left');
				}

				$tmp = $this->db->get()->result_array();
            //    $result['sql'] = $this->db->last_query();
				foreach ($tmp as $row) {
					$row['tconsult'] = $row['systconsult'] > 0 ? $row['systconsult'] : $row['tconsult'];
					$row['replys'] = $row['sysreplys'] > 0 ? $row['sysreplys'] : $row['replys'];
					$row['voteNum'] = $row['sysvotenum'] > 0 ? $row['sysvotenum'] : $row['voteNum'];
					$row['grade'] = $row['sysgrade'] > 0 ? $row['sysgrade'] : $row['grade'];
					unset ($row['sysvotenum']);
					unset ($row['sysgrade']);
					unset ($row['systconsult']);
					unset ($row['sysreplys']);
					$row['department'] = $this->yisheng->search($row['department']);
					$row['thumb'] = $this->profilepic($row['user_id'], 2);
					$result['data'][] = $row;
				}

			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function getfavorites($param = '') {
 		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if (($type = $this->input->get('type')) && $uid = intval($this->input->get('uid'))) {
				$this->db->where('wen_follow.fid', $uid);
				$this->db->where('users.role_id', $type);
				$offset = ($this->input->get('page') - 1) * 10;
				$this->db->limit(10, $offset);
				$result['data'] = array();
				if ($type == 2) {
					$this->db->select('users.tconsult,users.verify,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				} elseif($type == 1) {
					$this->db->select('user_profile.address,user_profile.city,user_profile.user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				}else {
					$this->db->select('company.name,users.verify,company.tel,company.shophours,company.department,company.address,company.city,company.userid as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('company', 'company.userid = wen_follow.uid', 'left');
				}

				$tmp = $this->db->get()->result_array();

				foreach ($tmp as $row) {
					$row['tconsult'] = $row['systconsult'] > 0 ? $row['systconsult'] : $row['tconsult'];
					$row['replys'] = $row['sysreplys'] > 0 ? $row['sysreplys'] : $row['replys'];
					$row['voteNum'] = $row['sysvotenum'] > 0 ? $row['sysvotenum'] : $row['voteNum'];
					$row['grade'] = $row['sysgrade'] > 0 ? $row['sysgrade'] : $row['grade'];
					unset ($row['sysvotenum']);
					unset ($row['sysgrade']);
					unset ($row['systconsult']);
					unset ($row['sysreplys']);
					if($type == 1){
						$row['guangzhu'] = $this->Cfensi($row['user_id']);
					    $row['fensi'] = $this->Cgz($row['user_id']);
					    $row['shoucang'] = $this->Cfavr($row['user_id']);
					}
					$type!=1&&$row['department'] = $this->yisheng->search($row['department']);
					$row['thumb'] = $this->profilepic($row['user_id'], 2);
					$result['data'][] = $row;
				}

			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	public function myfans($param = ''){
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if($this->notlogin){
              $result['ustate'] = '002';
			}else{
			  if ($this->input->get('page')) {
				$page = intval($this->input->get('page')-1);
				$start = $page * 15;
				$this->db->select('users.alias as uname, users.id as uid');
				$this->db->from('wen_follow');
				$this->db->where('uid', $this->uid);
				$this->db->limit(15, $start);
				$this->db->join('users', 'users.id = wen_follow.fid');
				$tmp = $this->db->get()->result_array();
				$result['data'] =  array();
				foreach($tmp as $r){
					$r['thumb'] = $this->profilepic($r['uid'],1);
					$r['fensi'] = $this->Cfensi($r['uid']);
					$r['guangzhu'] = $this->Cgz($r['uid']);
					$r['shoucang'] = $this->Cfavr($r['uid']);
                    $result['data'][] = $r;
				}
			  } else {
				$result['state'] = '012';
			  }
		   }
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	public function getfans($param = ''){
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			  if ($this->input->get('page') and $uid = $this->input->get('uid')) {
				$page = intval($this->input->get('page')-1);
				$start = $page * 15;
				$this->db->select('users.alias as uname, users.id as uid');
				$this->db->from('wen_follow');
				$this->db->where('uid', $uid);
				$this->db->limit(15, $start);
				$this->db->join('users', 'users.id = wen_follow.fid');
				$tmp = $this->db->get()->result_array();
				$result['data'] =  array();
				foreach($tmp as $r){
					$r['thumb'] = $this->profilepic($r['uid'],1);
					$r['fensi'] = $this->Cfensi($r['uid']);
					$r['guangzhu'] = $this->Cgz($r['uid']);
					$r['shoucang'] = $this->Cfavr($r['uid']);
                    $result['data'][] = $r;
				}
			  } else {
				$result['state'] = '012';
			  }

		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
    //guangzhu
	private function Cgz($uid){
       $this->db->where('uid', $uid);
       $this->db->from('wen_follow');
       return $this->db->count_all_results();
	}
	//favrite
	private function Cfavr($uid){
       $this->db->where('uid', $uid);
       $this->db->from('wen_favrite');
       return $this->db->count_all_results();
	}
	private function Cfensi($uid){
       $this->db->where('fid', $uid);
       $this->db->from('wen_follow');
       return $this->db->count_all_results();
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		switch ($pos) {
			case 1:
			    return $this->remote->thumb($id,'36');
			case 0:
			    return $this->remote->thumb($id,'250');
		    case 2:
                return $this->remote->thumb($id,'120');
			default:
			    return $this->remote->thumb($id,'120');
				break;
		}
	}
}
?>
