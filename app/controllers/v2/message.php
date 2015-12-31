<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api message Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class message extends MY_Controller {
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
		$this->load->model('auth');
	}
	public function send($param = '') {
		$result['state'] = '000';
		if ($this->notlogin) {
			$result['ustate'] = '002';
		} else {
			$userto = $this->input->post('userto');

			$result['follow'] = '0';
			$condition = array (
				'uid' => $userto,
				'fid' => $this->uid
			);
			$tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
			$condition = array (
				'uid' => $this->uid,
				'fid' => $userto
			);
			$tmp1 = $this->common->getTableData('wen_follow', $condition)->num_rows();
			if ($tmp > 0 && $tmp1 > 0) {
				$result['follow'] = '1';
			}

			if($result['follow'] == '1'){
				$t = time();
				$data = array (
					'userby' => $this->uid,
					'uid' => $this->uid,
					'conversation_id' => $t,
					'subject' => $this->input->post('subject'
				), 'userto' => $this->input->post('userto'), 'message' => $this->input->post('message'), 'message_type' => 1, 'is_read' => 0, 'created' => time());
				$this->db->insert('messages', $data);
				$result['postState'] = '000';
			}else{
				//no friends
				$result['postState'] = '003';
			}
		}
		echo json_encode($result);
	}
	public function getlist($param = '') {
		$result['state'] = '000';

		if ($this->notlogin) {
			$result['ustate'] = '002';
		} else {
			$result['data'] = array();
			$this->db->select('id, message,subject, is_read, created');
			$this->db->where('showType & ', 1);
			$this->db->where('userto', $this->uid);
			$this->db->order_by("is_read", "DESC");
			$start = intval($_GET['page'] - 1) * 10;
			$this->db->limit(10, $start);
			$this->db->from('messages');
			$tmp = $this->db->get()->result_array();
			$result['data'] = array();
			foreach ($tmp as $r) {
				($r['6082']==6082 || $r['6082']==6105)&&$r['uname'] = '管理员';
				$r['created'] = date('Y-m-d', $r['created']);
				$result['data'][] = $r;
			}
		}
		echo json_encode($result);
	}
	public function view($param = '') {
		$result['state'] = '000';

		if ($this->notlogin) {
			$result['ustate'] = '002';
		} else {
			$id = $this->input->get('id');
			$this->db->select('messages.id,messages.message,messages.subject,messages.is_read,messages.is_starred,messages.message_type,messages.created,users.alias as uname');
			$this->db->where('messages.showType & ', 1);
			$this->db->where('messages.userto', $this->uid);
			$this->db->order_by("messages.id", $id);
			$this->db->limit(1);
			$this->db->from('messages');
			$this->db->join('users', 'users.id = messages.userby');
			$tmp = $this->db->get()->result_array();
			if (!empty ($tmp)) {
				$tmp[0]['created'] = date('Y年m月d日 H:i:s', $tmp[0]['created']);
				$udata = array (
					'is_read' => 1,

				);
				$this->db->where('id', $id);
				$this->db->update('messages', $udata);
			}
			$result['data'] = $tmp[0];
		}
		echo json_encode($result);
	}
	public function del($param = '') {
		$result['state'] = '000';

		if ($this->notlogin) {
			$result['ustate'] = '002';
		} else {
             if($id = intval($this->input->post('id')))
             {
             	$this->db->delete('messages', array('id' => $id));
             }else{
             	$result['state'] = '012';
             }
		}
		echo json_encode($result);
	}
}
?>
