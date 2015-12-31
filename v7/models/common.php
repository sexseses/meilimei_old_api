<?php

/**
 * WENRAN common Class
 *
 * helps to achieve common tasks related to the site like flash message formats,pagination variables.
 *
 * @package	Dropinn
 * @subpackage	Models
 * @category	Common_model
 */
class common extends CI_Model {

	public function __construct() {
		parent :: __construct();
		$this->load->model('page_model');

	}

	/**
	 * Set Style for the flash messages
	 *
	 * @access	public
	 * @param	string	the type of the flash message
	 * @param	string  flash message
	 * @return	string	flash message with proper style
	 */
	function flash_message($type, $message) {
		switch ($type) {
			case 'success' :
				$data = '<div class="clsShow_Notification"><p class="success"><span>' . $message . '</span></p></div>';
				break;
			case 'error' :
				$data = '<div class="clsShow_Notification"><p class="error"><span>' . $message . '</span></p></div>';
				break;
		}
		return $data;
	} //End of flash_message Function

	function getCountries($conditions = array ()) {
		if (count($conditions) > 0)
			$this->db->where($conditions);

		$this->db->from('country');
		$this->db->select('country.id,country.country_symbol,country.country_name');
		$result = $this->db->get();
		return $result;
	}

	/**
	 * Get getPages
	 *
	 * @access	public
	 * @param	array	conditions to fetch data
	 * @return	object	object with result set
	 */
	function getPages() {
		$conditions = array (
			'page.is_active' => 1
		);
		$pages = array ();
		$pages['staticPages'] = $this->page_model->getPages($conditions);
		return $pages['staticPages'];

	}
    function newansum($uid){
      $tmp = $this->db->query("SELECT SUM(new_reply) as res FROM question_state WHERE uid = {$uid} ORDER BY uid DESC ")->result_array();
	  return $tmp[0]['res'];
	}
    function weiboCommentSum($uid){
        $tmp = $this->db->query("SELECT SUM(commentnums) as res FROM wen_weibo WHERE uid = {$uid} and type!=4 and isdel=0 ORDER BY uid DESC ")->result_array();
        $tmp2 = $this->db->query("SELECT SUM(new_reply) as res FROM wen_comment WHERE fuid = {$uid}  and is_delete=0 ORDER BY fuid DESC ")->result_array();
        $res = 0;
        if(isset($tmp[0]['res'])){
           $res+=$tmp[0]['res'];
        }
        if(isset($tmp2[0]['res'])){
           $res+=$tmp2[0]['res'];
        }
        return $res;
    }

	function getTableData($table = '', $conditions = array (), $fields = '', $like = array (), $limit = array (), $orderby = array (), $like1 = array (), $order = array (), $conditions1 = array ()) {
		//Check For Conditions
		if (is_array($conditions) and count($conditions) > 0)
			$this->db->where($conditions);

		//Check For Conditions
		if (is_array($conditions1) and count($conditions1) > 0)
			$this->db->or_where($conditions1);

		//Check For like statement
		if (is_array($like) and count($like) > 0)
			$this->db->like($like);

		if (is_array($like1) and count($like1) > 0)
			$this->db->or_like($like1);

		//Check For Limit
		if (is_array($limit)) {
			if (count($limit) == 1)
				$this->db->limit($limit[0]);
			else
				if (count($limit) == 2)
					$this->db->limit($limit[0], $limit[1]);
		}

		//Check for Order by
		if (is_array($orderby) and count($orderby) > 0)
			$this->db->order_by('id', 'desc');

		//Check for Order by
		if (is_array($order) and count($order) > 0)
			$this->db->order_by($order[0], $order[1]);

		$this->db->from($table);

		//Check For Fields
		if ($fields != '')
			$this->db->select($fields);

		else
			$this->db->select();

		$result = $this->db->get();

		//pr($result->result());
		return $result;

	}
	// deleteTableData Function
	function deleteTableData($table = '', $conditions = array ()) {
		//Check For Conditions
		if (is_array($conditions) and count($conditions) > 0)
			$this->db->where($conditions);

		$this->db->delete($table);
		return $this->db->affected_rows();

	}
	// nsertData Function
	function insertData($table = '', $insertData = array ()) {
		$this->db->insert($table, $insertData);
		return $this->db->insert_id();
	}
	// updateTableData Function
	function updateTableData($table = '', $id = 0, $conditions = array (), $updateData = array ()) {
		if (is_array($conditions) and count($conditions) > 0)
			$this->db->where($conditions);
		else
			$this->db->where('id', $id);
		$this->db->update($table, $updateData);

	}

	function setNotify($uid = 0, $new_message = '', $new_question = '', $new_answer = '') {
		$updateTableData = array ();
		if ($uid != 0) {
			if ($new_message != '')
				$updateTableData['new_message'] = $new_message;
			if ($new_question != '')
				$updateTableData['new_question'] = $new_question;
			if ($new_answer != '')
				$updateTableData['new_answer'] = $new_answer;
				$conditions = array();
				if($uid!=''){
                   $conditions['user_id'] = $uid;
				}
			$this->updateTableData('wen_notify', '', $conditions, $updateTableData);
		} else {
			return '001';
		}
		return '000';
	}
}
?>