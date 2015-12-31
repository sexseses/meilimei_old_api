<?php

class Users_model extends CI_Model
{
	public function Users_model()
	{
		parent::__construct();

		// Other stuff
		$this->_prefix = $this->config->item('wen_table_prefix');
		$this->_table = $this->_prefix.$this->config->item('wen_users_table');
		$this->_roles_table = $this->_prefix.$this->config->item('wen_roles_table');
	}

	// General function
    public function getClientID($contentid, $type=0){
        if(0 > intval($contentid)){
            return ;
        }
        if($type == 0) {
            $this->db->where('weibo_id', $contentid);
            $res = $this->db->get('wen_weibo')->result_array();
        }else{
            $this->db->where('nid', $contentid);
            $res = $this->db->get('note')->result_array();
        }

        if(!empty($res)) {
            $uid = $res[0]['uid'];
            if(0 > intval($uid))
                return;
            $this->db->where('id', $uid);
            $this->db->select('clientid');
            return $this->db->get('users')->result_array();
        }
    }

	function get_all($offset = 0, $row_count = 0)
	{
		$users_table = $this->_table;
		$roles_table = $this->_roles_table;

		if ($offset >= 0 AND $row_count > 0)
		{
			$this->db->select("$users_table.*", FALSE);
			$this->db->select("$roles_table.name AS role_name", FALSE);
			$this->db->join($roles_table, "$roles_table.id = $users_table.role_id");
			$this->db->order_by("$users_table.id", "ASC");

			$query = $this->db->get($this->_table, $row_count, $offset);
		}
		else
		{
			$query = $this->db->get($this->_table);
		}

		return $query;
	}

    public function get_user_name($uid){
        if(intval($uid) < 0){
            return;
        }
        $this->db->where('id',$uid);
        $this->db->select('alias','phone');
        return $this->db->get('users')->result_array();
    }

    public function get_department($uid){
        if(intval($uid) < 0){
            return;
        }

        $this->db->select("*");
        $this->db->where('user_id',$uid);
        return $this->db->get('user_profile')->result_array();
    }

	public function get_user_fav_by_uid($uid){
		if(intval($uid) < 0)
			return ;

		$this->db->where('uid',$uid);
        $this->db->select('uid as id, tag_img as surl, tag as name, colors');
		return $this->db->get('user_fav')->result_array();
	}

	function get_user_by_id($user_id)
	{
		$this->db->where('id', $user_id);
		return $this->db->get($this->_table);
	}
	function get_user_by_phone($phone)
	{
		$this->db->where('phone', $phone);
		return $this->db->get($this->_table);
	}
    function get_by_fb_id($user_id)
	{
		$this->db->where('fb_id', $user_id);
		return $this->db->get($this->_table);
	}
    function get_by_ref_id($user_id)
	{
		$this->db->where('ref_id', $user_id);
		return $this->db->get($this->_table);
	}
	function get_user_by_username($username)
	{
		$this->db->where('username', $username);
		 return $this->db->get($this->_table);
	}

	function get_user_by_email($email)
	{
		$this->db->where('email', $email);
		return $this->db->get($this->_table);
	}

	function get_login($login)
	{
		$this->db->where('phone', $login);
		$this->db->or_where('email', $login);
		return $this->db->get($this->_table);
	}

	function check_ban($user_id)
	{
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		$this->db->where('banned', '1');
		return $this->db->get($this->_table);
	}

	function check_username($username)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(username)=', strtolower($username));
		return $this->db->get($this->_table);
	}
	function check_phone($phone)
	{
		$this->db->select('1', FALSE);
		$this->db->where('phone', $phone);
		/*if( $this->wen_auth->is_logged_in()){
			$this->db->where('id != ', $this->wen_auth->get_user_id());
		}*/
		return $this->db->get('users');
	}
	function check_email($email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(email)=', strtolower($email));
		if( $this->wen_auth->is_logged_in() ){
			$this->db->where('id != ', $this->wen_auth->get_user_id());
		}
		return $this->db->get($this->_table);
	}

	function ban_user($user_id, $reason = NULL)
	{
		$data = array(
			'banned' 			=> 1,
			'ban_reason' 	=> $reason
		);
		return $this->set_user($user_id, $data);
	}

	function unban_user($user_id)
	{
		$data = array(
			'banned' 			=> 0,
			'ban_reason' 	=> NULL
		);
		return $this->set_user($user_id, $data);
	}

	function set_role($user_id, $role_id)
	{
		$data = array(
			'role_id' => $role_id
		);
		return $this->set_user($user_id, $data);
	}

	// User table function

	function create_user($data)
	{
		 $this->db->insert($this->_table, $data);
		 return $this->db->insert_id();
	}

	function get_user_field($user_id, $fields)
	{
		$this->db->select($fields);
		$this->db->where('id', $user_id);
		return $this->db->get($this->_table);
	}

	function set_user($user_id, $data)
	{
		$this->db->where('id', $user_id);
		$this->db->limit(1);
		return $this->db->update($this->_table, $data);
	}

	function delete_user($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete($this->_table);
		return $this->db->affected_rows() > 0;
	}

	function delete_user_fb($fb_email)
	{
	 $this->db->where('email', $fb_email);
		$row = $this->db->get('users')->row();

		$this->db->where('id', $row->id);
		$this->db->delete('users');

		$this->db->where('id', $row->id);
		$this->db->delete('profiles');

		return array($row->ref_id, $row->coupon_code, $row->created, $row->id);
	}

	// Forgot password function

	function newpass($user_id, $pass, $key)
	{
		$data = array(
			'newpass' 			=> $pass,
			'newpass_key' 	=> $key,
			'newpass_time' 	=> date('Y-m-d h:i:s', time() + $this->config->item('DX_forgot_password_expire'))
		);
		return $this->set_user($user_id, $data);
	}

	function activate_newpass($user_id, $key)
	{
		$this->db->set('password', 'newpass', FALSE);
		$this->db->set('newpass', NULL);
		$this->db->set('newpass_key', NULL);
		$this->db->set('newpass_time', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('newpass_key', $key);
		return $this->db->update($this->_table);
	}

	function clear_newpass($user_id)
	{
		$data = array(
			'newpass' 			   => NULL,
			'newpass_key' 	 => NULL,
			'newpass_time' 	=> NULL
		);
		return $this->set_user($user_id, $data);
	}

	// Change password function
	function change_password($user_id, $new_pass)
	{
		$this->db->set('password', $new_pass);
		$this->db->where('id', $user_id);
		$this->db->limit(1);
		return $this->db->update($this->_table);
	}

	// Change password by phone
	function change_password_by_phone($phone, $new_pass)
	{
		$this->db->set('password', $new_pass);
		$this->db->where('phone', $phone);
		$this->db->limit(1);
		return $this->db->update($this->_table);
	}


    /**
     * 查询用户详情
     * @author wanglulu add by 2015-06-16
     * @param $args_array
     * @return null
     */
    public function searchUserDetail( $args_array ) {

        //sql字符串
        $sql = '';

        //参数值数组
        $params_array = array();

        //用户id
        if ( isset($args_array['id']) && is_numeric($args_array['id']) ) {
            $sql .= ' AND id = ?';
            $params_array[] = $args_array['id'];
        }

        /*//用户角色id  1、用户 2、医生 3、机构
        if ( isset($args_array['role_id']) && is_numeric($args_array['role_id']) ) {
            $sql .= ' AND role_id = ?';
            $params_array[] = $args_array['role_id'];
        }*/

        //用户名
        if ( isset($args_array['username']) && !empty($args_array['username']) ) {
            $sql .= ' AND username = ?';
            $params_array[] = $args_array['username'];
        }

        if ($sql) {
            $sql = ' WHERE ' . substr($sql, 4);
        }

        if (isset($args_array['rp']) && $args_array['rp'] > 1) {

            //排序
            if (isset($args_array['sortname']) && !empty($args_array['sortname']))
                $sql .= sprintf(' ORDER BY %s %s', $args_array['sortname'], $args_array['sortorder']);

            //分页
            if (!isset($args_array['page']) || !is_numeric($args_array['page']) || $args_array['page'] <= 0)
                $args_array['page'] = 1;

            $sql .= sprintf(' LIMIT %d, %d', ($args_array['page'] - 1) * $args_array['rp'], $args_array['rp']);

        } else {

            //排序
            if (isset($args_array['sortname']) && !empty($args_array['sortname']))
                $sql .= sprintf(' ORDER BY %s %s', $args_array['sortname'], $args_array['sortorder']);

        }

        $search_sql = 'SELECT * FROM users' . $sql;
        $query = $this->db->query($search_sql, $params_array);

        if ($query->num_rows() <= 0) {
            return null;
        }

        return $query->row_array();

    }


    /**
     * 查询积分科目
     * @author wanglulu add by 2015-06-16
     * @param $args_array
     * @return null
     */
    public function searchScoreDetail( $args_array ) {

        //sql字符串
        $sql = '';

        //参数值数组
        $params_array = array();

        //科目id
        if (isset($args_array['sid']) && is_numeric($args_array['sid'])) {
            $sql .= ' AND sid = ?';
            $params_array[] = $args_array['sid'];
        }

        //科目名称
        if (isset($args_array['name']) && !empty($args_array['name'])) {
            $sql .= ' AND name = ?';
            $params_array[] = $args_array['name'];
        }

        if ($sql) {
            $sql = ' WHERE ' . substr($sql, 4) ;
        }

        $search_sql = 'SELECT * FROM score' . $sql;
        $query = $this->db->query($search_sql, $params_array);

        if ($query->num_rows() <= 0) {
            return null;
        }

        return $query->row_array();

    }


    /**
     * 查询用户签到
     * @author wanglulu add by 2015-06-16
     * @param $args_array
     * @return null
     */
    public function searchUserSignin( &$args_array ) {

        //sql字符串
        $sql = '';

        //参数值数组
        $params_array = array();

        //签到记录id
        if ( isset($args_array['id']) && !empty($args_array['id']) ) {
            $sql .= ' AND id = ?';
            $params_array[] = $args_array['id'];
        }

        //签到用户uid
        if ( isset($args_array['uid']) && is_numeric($args_array['uid']) ) {
            $sql .= ' AND uid = ?';
            $params_array[] = $args_array['uid'];
        }

        //签到日期
        if ( isset($args_array['calDate']) && !empty($args_array['calDate']) ) {
            $sql .= ' AND calDate = ?';
            $params_array[] = $args_array['calDate'];
        }

        //签到获取的积分
        if ( isset($args_array['points']) && !empty($args_array['points']) ) {
            $sql .= ' AND points = ?';
            $params_array[] = $args_array['points'];
        }

        if ($sql) {
            $sql = ' WHERE ' . substr($sql, 4);
        }

        if (isset($args_array['rp']) && $args_array['rp'] > 1) {

            //排序
            if (isset($args_array['sortname']) && !empty($args_array['sortname']))
                $sql .= sprintf(' ORDER BY %s %s', $args_array['sortname'], $args_array['sortorder']);

            //分页
            if (!isset($args_array['page']) || !is_numeric($args_array['page']) || $args_array['page'] <= 0)
                $args_array['page'] = 1;

            $sql .= sprintf(' LIMIT %d, %d', ($args_array['page'] - 1) * $args_array['rp'], $args_array['rp']);

        } else {

            //排序
            if (isset($args_array['sortname']) && !empty($args_array['sortname']))
                $sql .= sprintf(' ORDER BY %s %s', $args_array['sortname'], $args_array['sortorder']);

        }

        $search_sql = 'SELECT * FROM user_signin' . $sql;
        $query = $this->db->query($search_sql, $params_array);

        if ($query->num_rows() <= 0) {
            $args_array['count'] = 0;
            return null;
        }

        $args_array['count'] = $query->num_rows();

        return $query->result_array();

    }


    /**
     * 用户签到
     * @author wanglulu add by 2015-06-16
     * @param $args_array
     * @return array
     */
    public function userSignin( $args_array ) {

        if ( !isset($args_array['uid'], $args_array['sid']) ) {
            return array('code'=>'400', 'message'=>'参数错误');
        }

        if ( empty($args_array['uid']) || !is_numeric($args_array['uid']) ) {
            return array('code'=>'400', 'message'=>'用户ID不能为空');
        }

        if ( empty($args_array['sid']) || !is_numeric($args_array['sid']) ) {
            return array('code'=>'400', 'message'=>'错误的参数sid');
        }

        try {

            //开启事务
            $this->db->trans_begin();

            //查询会员
            $arg_array = array(
                'id' => $args_array['uid'],
                'role_id' => 1
            );

            $users = $this->searchUserDetail( $arg_array );
            if ( !$users ) {
                throw new Exception('用户信息没有找到', '404');
            }

            //查询积分科目
            $arg_array = array(
                'sid' => $args_array['sid']
            );
            $score = $this->searchScoreDetail( $arg_array );
            if ( !$score ) {
                throw new Exception('积分科目没有找到', '404');
            }

            //查询用户签到
            $arg_array = array(
                'uid' => $users['id'],
                'calDate' => date('Y-m-d')
            );
            $userSignin = $this->searchUserSignin( $arg_array );
            if ( $userSignin ) {
                throw new Exception('你已经签过，不可重复签到', '400');
            }

            //当前日期时间
            $currentDateTime = date('Y-m-d H:i:s');

            //记录签到日志
            $signin_arg_array = array(
                'createTime' => $currentDateTime,
                'timeStamp' => $currentDateTime,
                'uid' => $users['id'],
                'calDate' => date('Y-m-d'),
                'points' => $score['score']
            );
            $this->db->insert('user_signin', $signin_arg_array);

            //更新用户积分日志
            $score_logger_arg_array = array(
                'uid' => $users['id'],
                'desc' => $score['name'],
                'score' => $score['score'],
                'created_at' => time()
            );
            $this->db->insert('score_logger', $score_logger_arg_array);

            //更新用户积分
            $sql = 'UPDATE users SET jifen = jifen+? WHERE id = ?';
            $query = $this->db->query($sql, array( $score['score'], $users['id']) );
            if ( !$query ) {
                throw new Exception('会员积分更新失败', '400');
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('取消失败', '400');
            }

            //提交事务
            $this->db->trans_commit();

            $return_array = array(
                'points' => $users['jifen'] + $score['score']
            );

            return array( 'code'=>'200', 'message'=>'ok', 'data'=>$return_array );

        } catch( Exception $e ) {

            //回滚事务
            $this->db->trans_rollback();

            if ($e->getCode() > 0) {
                return array('code'=>$e->getCode(), 'message'=>$e->getMessage());
            } else {
                return array('code'=>'500', 'message'=>'系统错误');
            }

        }

    }


    /**
     * 用户登录
     * @author ken.zhang add by 2015-06-16
     * @param $args_array
     * @return array
     */
    public function login(string $phone, string $password){

    }




}
?>