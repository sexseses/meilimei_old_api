<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class sns2 extends CI_Controller {
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
				$data['fid'] = $this->uid;
				$data['type'] = 8;
				$result['updateState'] = '000';
				$this->common->insertData('wen_follow', $data);
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
	function addTag($param = ''){
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if(!$this->notlogin){
				if($this->input->post('tagid')){
$idata = array (
					'uid' => $this->uid,
					'tagid' => $this->input->post('tagid')
				);
				$result['notice'] = '关注成功!';
				$this->common->insertData('myTags',$idata);
				}else{
					$result['notice'] = '参数不全';
					$result['state'] = '012';
				}

			}else{
				$result['notice'] = '账户未登入';
                 $result['ustate'] = '001';
			}
		} else {
			$result['notice'] = 'Token错误';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function removeTag($param = ''){
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if($this->uid){
				$condition = array (
					'uid' => $this->uid,
					'tagid' => $this->input->post('tagid')
				);
				$result['notice'] = '关注取消!';
				$this->common->deleteTableData('myTags',$condition);
			}else{
				$result['notice'] = '账户未登入!';
                 $result['ustate'] = '001';
			}
		} else {
			$result['notice'] = 'Token错误';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function getTags($param = ''){
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if(!$this->notlogin){
                 $this->db->where('uid', $this->uid);
                 $this->db->from('myTags');
                 $this->db->join('items', 'items.id = myTags.tagid');
                 $query = $this->db->get();
			}else{
                 $result['ustate'] = '001';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function myfavorites($param = '') {
		file_put_contents("/var/www/test/logres3",var_export($this->session->all_userdata(),true).'----time='.date("Y-m-d h:i:s",time()));
		$result['state'] = '000';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if ($type = $this->input->get('type')) {
				$this->db->where('wen_follow.fid', $this->uid);
				$this->db->where('users.role_id', $type);
				if ($type == 2) {

					$this->db->select('users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled');
					$this->db->from('wen_follow');
					$offset = ($this->input->get('page') - 1) * 10;
					$this->db->limit(10, $offset);
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				} else {
					$this->db->select('company.name,company.tel,company.shophours,company.department,company.address,company.city,company.userid as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$offset = ($this->input->get('page') - 1) * 10;
					$this->db->limit(10, $offset);
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
