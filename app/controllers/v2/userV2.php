<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class userV2 extends MY_Controller {
	private $notlogin = true, $uid = '';
	var $min_username = 4;
	var $max_username = 20;
	var $min_password = 4;
	var $max_password = 20;

	public function __construct() {
		parent :: __construct();
		$this->load->library('form_validation');
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->library('sms');
		$this->load->model('Users_model');
		$this->load->model('auth');
		$this->load->model('Email_model');
		$this->load->helper('file');
		$this->load->model('remote');
        $this->load->model('Diary_model');
	}

	/**  我的咨询
	 * @param string $param
	 */
	function myQuestion($param = '') {
		$result['state'] = '000';
        $result['debug'] = '000';
		$result['ustate'] = '001';
		if (!$this->notlogin) {
			$result['ustate'] = '000';
			$this->db->where('fUid', $this->uid);
		} else {
			$result['state'] = '012';
			echo json_encode($result);
			exit;
		}
		if (intval($this->input->post('state'))) {
			$this->db->where('state', intval($this->input->post('state')));
		}
		$page = intval($this->input->post('page')-1);
		$this->db->select('wen_questions.title,wen_questions.id,wen_questions.cdate,wen_questions.position as tags,wen_questions.state,wen_questions.new_answer');
		$this->db->order_by("id", "desc");
		$this->db->from('wen_questions');
			//$this->db->join('wen_answer', 'wen_answer.qid = wen_questions.id');
		$this->db->limit(10,$page*10);
		$tmp = $this->db->get()->result_array();
		$result['data'] = array();
		foreach ($tmp as $row) {
			$row['cdate'] = date('Y-m-d', $row['cdate']);
            $itemid = $this->Diary_model->getItemId($row['tags']);
            $row['other'] = is_null($this->Diary_model->isItemLevel($itemid,1))?'':$this->Diary_model->isItemLevel($itemid,1);
			$row['ans'] = $this->Gans($row['id'])?$this->Gans($row['id']):array();
			$result['data'][] = $row;
		}
        $this->db->where('fUid', $this->uid);
        $this->db->where('is_read', 0);
        $this->db->update("wen_questions",array('is_read'=>1));

		echo json_encode($result);
	}
	//get reply
	private function Gans($qid = '') {
		if ($qid) {
			$fields = 'wen_answer.uid,wen_answer.id,wen_answer.content,wen_answer.new_comment,wen_answer.is_talk,wen_answer.cdate,users.alias as yname,users.verify,users.banned';

			$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users ON users.id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC")->result_array();

			foreach ($tmp as $row) {

				if(!isset($row['verify'])){
					$row['verify'] = 0;
					$row['yname'] = '***';
					$row['banned'] = 1;
					$row['content'] = '已屏蔽';
				}
                $row['verify'] = 1;
				$row['cdate'] = date('Y-m-d', $row['cdate']);
				$row['thumb'] = $this->profilepic($row['uid'], 2);
				$result[] = $row;
			}

		} else {
			$result['state'] = '012';
		}
		return $result;
	}

	private function thumb($uid, $file) {
		if ($file != '') {
			$this->remote->uputhumb($file,$uid);
			return true;
		} else {
			return false;
		}
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

	private function getQstate($state = 0) {
		switch ($state) {
			case 1 :
			return '回答中';
			break;
			case 2 :
			return '关闭';
			break;
			case 4 :
			return '已过期';
			break;
			case 8 :
			return '已完结';
			break;
		}

	}
}
?>
