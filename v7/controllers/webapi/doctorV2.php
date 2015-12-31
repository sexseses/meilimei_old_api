<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class doctorV2 extends CI_Controller {
	var $path = '';
	private $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('yisheng');
		$this->path = realpath(APPPATH . '../images');
		$this->load->model('remote');
		$this->load->model('auth');
	}

	public function getAns($param = '') {
		$result['state'] = '000';
		//if ($this->auth->checktoken($param)) {

			if ($qid = $this->input->get('qid')) {
				$result['data'] = array ();
				$fields = 'wen_answer.uid,wen_answer.id,wen_answer.content,wen_answer.new_comment,wen_answer.is_talk,wen_answer.cdate,users.alias as Uname,users.verify,users.suggested,users.grade,user_profile.company,user_profile.department,users.sysgrade';
				if ($rid = $this->input->get('fromuid')) {
					$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users ON users.id = wen_answer.uid LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND wen_answer.uid = {$rid} order by wen_answer.id ASC")->result_array();

					$tmp[0]['sysgrade'] != '' && $tmp[0]['grade'] = $tmp[0]['sysgrade'];
					$insertd['question'] = $this->qdetail($qid);
					$insertd['mainans']['content'] = $tmp[0]['content'];
					$insertd['mainans']['cTime'] = date('Y-m-d H:i:s', $tmp[0]['cdate']);
					$insertd['mainans']['fuid'] = $tmp[0]['uid'];

					$result['talks'] = $this->getcomment($tmp[0]['id'], $rid, $insertd);
					$tmp[0]['thumb'] = $this->profilepic($tmp[0]['uid'], 2);
					if ($tmp[0]['department']) {
						$tmp[0]['department'] = $this->yisheng->search($tmp[0]['department']);
					}
					$tmp[0]['cdate'] = date('Y-m-d H:i:s', $tmp[0]['cdate']);
					$result['data'] = $tmp[0];
					$id = $tmp[0]['id'];
					$this->db->query("UPDATE `wen_answer` SET `new_comment` = 0   WHERE `id` ={$id}");
				} else {
					$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users  ON users.id = wen_answer.uid LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC")->result_array();

					foreach ($tmp as $row) {
						$row['cdate'] = date('Y-m-d', $row['cdate']);
						$row['thumb'] = $this->profilepic($row['uid'], 1);
						$result['data'][] = $row;
					}

				}
			} else {
				$result['state'] = '012';
			}

		//} else {
			//$result['state'] = '001';
		//}
		echo json_encode($result);
	}
	private function qdetail($param = '') {
		$result = null;
		if ($qid = $param) {
			$tmp = $this->db->query("SELECT id,fUid as fuid,position,title,address,description,cdate FROM wen_questions WHERE id ={$qid} ORDER BY id DESC  LIMIT 1 ")->result_array();
			if (!empty ($tmp[0])) {
				$tmp[0]['cTime'] = $tmp[0]['cdate'] = date('Y-m-d  H:i:s', $tmp[0]['cdate']);
				$tmps = $this->db->query("SELECT type_data,dataType FROM wen_weibo WHERE q_id ={$qid} and type=4 ORDER BY q_id DESC  LIMIT 1 ")->result_array();
				$tmp[0]['pic'] = '';
				$tmp[0]['haspic'] = 0;
				if (!empty ($tmps) and $tmps[0]['type_data'] != '') {
					$tmp[0]['haspic'] = 1;
					$pinfo = unserialize($tmps[0]['type_data']);
					$tmps = $this->db->query("SELECT savepath FROM wen_attach WHERE id ={$pinfo[1]['id']} ORDER BY id DESC  LIMIT 1 ")->result_array();
					$tmp[0]['pic'] = $this->remote->show($tmps[0]['savepath']) ;
				}
				if ($tmp[0]['description'] == '')
					$tmp[0]['description'] = $tmp[0]['title'];
				$result = $tmp[0];
			}
		}
		return $result;
	}
	private function getcomment($qid = 0, $uid = 0, $insert) {
		$tmp = $this->db->query("SELECT talk.*,users.alias as tFname FROM talk LEFT JOIN users ON users.id = talk.touid WHERE talk.qid = {$qid} AND (talk.fuid = {$uid} OR talk.touid = {$uid}) order by talk.id ASC")->result_array();
		$result = array ();
		$result[] = $insert['question'];
		$result[] = $insert['mainans'];
		foreach ($tmp as $row) {
			$row['haspic'] = 0;
			$row['pic'] = '';
			if ($t = unserialize($row['data'])) {
				$row['pic'] =$this->remote->show($t['linkpic']);
				$row['haspic'] = 1;
			}
			unset ($row['qid']);
			unset ($row['data']);
			$row['cTime'] = date('Y-m-d H:i', $row['cTime']);
			$result[] = $row;
		}

		return $result;
	}
	public function gettalks($param = '') {
		$result['state'] = '000';
		//if ($this->auth->checktoken($param)) {
			if($this->input->get('qid') and $this->input->get('uid')){
				$page = intval($this->input->get('page')-1);
				$start = $page*3;
			$tmp = $this->db->query("SELECT talk.*,users.alias as tFname FROM talk LEFT JOIN users ON users.id = talk.touid WHERE talk.qid = {$this->input->get('qid')} AND (talk.fuid = {$this->input->get('uid')} OR talk.touid = {$this->input->get('uid')}) order by talk.id ASC LIMIT $start,3")->result_array();
			$result['data'] = array ();
			foreach ($tmp as $row) {
				$row['haspic'] = 0;
				$row['pic'] = '';
				if ($t = unserialize($row['data'])) {
					$row['pic'] = $this->remote->show($t['linkpic']);
					$row['haspic'] = 1;
				}
				unset ($row['qid']);
				unset ($row['data']);
				$row['cTime'] = date('Y-m-d H:i', $row['cTime']);
				$result['data'][] = $row;
			}
			}
		//} else {
		//	$result['state'] = '001';
		//}
		echo json_encode($result);
	}
	public function talk($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->notlogin) {
				$result['ustate'] = '002';
			} else {
				if ($qid = $this->input->post('qid')) {
					$extra = array ();
					if (isset ($_FILES['attachPic']['tmp_name'])) {

							$name = time() . '.jpg';
							$savepath = date('Y') . '/' . date('m') . '/' .date('m') . '/'. $name;
							if(!$this->remote->upload($_FILES['attachPic']['tmp_name'],$savepath,array('width'=>600,'height'=>800))){

						    }
							$extra['linkpic'] = $savepath;

					}
					$data = array (
						'fuid' => $this->uid,
						'comment' => $this->input->post('comment'
					), 'contentid' => $qid, 'touid' => $this->input->post('touid'), 'status' => 1, 'type' => 'qa', 'data' => serialize($extra), 'cTime' => time());
					$this->db->insert('wen_comment', $data);
					$result['postState'] = '000';
					//chage state

					$tmp = $this->db->query("SELECT  users.id,users.email,wen_questions.title FROM users LEFT JOIN wen_questions ON wen_questions.fUid=users.id WHERE wen_questions.id = {$qid} LIMIT 1")->result_array();
					if ($tmp['0']['id'] = $this->uid) {
						$auid = $this->input->post('touid');
						$tmp2 = $this->db->query("SELECT uid FROM question_state WHERE uid = {$auid} AND qid = {$qid} LIMIT 1")->result_array();
						if (!empty ($tmp2)) {
							$this->db->query("UPDATE `question_state` SET `new_reply` =`new_reply`+ 1  WHERE `qid` ={$qid} AND uid = {$auid}");
						} else {
							$insertData = array ();
							$insertData['uid'] = $auid;
							$insertData['qid'] = $qid;
							$insertData['new_reply'] = 1;
							$insertData['cdate'] = time();
							$this->common->insertData('question_state', $insertData);
						}

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
	public function uinfo($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			$uid = intval($this->input->get('uid'));
			$tmp = $this->db->query("SELECT users.id,users.alias,users.suggested,users.voteNum,users.jifen,users.grade,users.tconsult,users.replys,users.sysgrade,users.sysvotenum,users.sysreplys,users.systconsult,users.created,user_profile.Fname,user_profile.Lname,user_profile.department,user_profile.address,user_profile.introduce,user_profile.company,user_profile.skilled,user_profile.sex,user_profile.position FROM users LEFT JOIN user_profile ON user_profile.user_id = users.id WHERE users.id = {$uid} LIMIT 1")->result_array();

			$result['data'] = $tmp[0];
			$result['data']['voteNum'] = $result['data']['sysvotenum'] > 0 ? $result['data']['sysvotenum'] : $result['data']['voteNum'];
			$result['data']['grade'] = $result['data']['sysgrade'] > 0 ? $result['data']['sysgrade'] : $result['data']['grade'];
			$result['data']['department'] = $this->yisheng->search($result['data']['department']);
			$result['data']['username'] = $result['data']['alias'];
			unset ($result['data']['alias']);
			$result['data']['thumb'] = $this->profilepic($uid, 2);
			$abstate = false;
			$result['data']['ablum'] = $this->ablum($uid, $abstate);
			if ($abstate) {
				$result['data']['hasthumb2'] = 1;
				foreach ($result['data']['ablum'] as $r) {
					$result['data']['ablum_2'][] = $r . '_2.jpg';
				}
			} else {
				$result['data']['hasthumb2'] = 0;
			}
			if (!empty ($result['data']['ablum'])) {
				$result['data']['hasablum'] = 1;
			} else {
				$result['data']['hasablum'] = 0;
			}
			$result['data']['reviews'] = $this->getreviews($uid);
			$result['data']['qustions'] = $this->getqustions($uid);
			$result['data']['hasreviews'] = empty ($result['data']['reviews']) ? 0 : 1;
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
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