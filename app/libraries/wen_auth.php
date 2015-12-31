<?php

/**
 * wen_Auth Class
 *
 * Authentication library for Code Igniter.
 *
 * @author		WENRAN
 * @version		1.0.6
 */

class wen_Auth {
	// Private
	var $_banned,$complete=true,$nextStep;
	var $_ban_reason;
	var $_auth_error; // Contain user error when login
	var $_captcha_image;
	var $regsys = 'windows';
	var $regfrom = 1;
    private $auth;
	public function __construct() {
		$this->auth = & get_instance();
		$this->auth->load->library('Session');
		$this->auth->load->database();

		// Load WEN Auth config
		$this->auth->load->config('wen_auth');

		// Load WEN Auth event
		$this->auth->load->library('wen_auth_event');
		$this->auth->load->model('Email_model');
        $this->auth->load->model('wen_auth/login_attempts', 'login_attempts');
		$this->auth->load->model('Users_model');
        $this->auth->load->model('wen_auth/user_temp', 'user_temp');
        $this->auth->load->model('wen_auth/roles', 'roles');
		$this->auth->load->model('wen_auth/permissions', 'permissions');
		$this->auth->load->model('wen_auth/user_autologin', 'user_autologin');
		// Initialize
		$this->_init();
	}

	/* Private function */

	function _init() {
		$this->email_activation = false;
		$this->allow_registration = true;
	}

	function _gen_pass($len = 8) {
		// No Zero (for user clarity);
		$pool = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$str = '';
		for ($i = 0; $i < $len; $i++) {
			$str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
		}

		return $str;
	}

	/*
	* Function: _encode
	* Modified for WEN_Auth
	* Original Author: FreakAuth_light 1.1
	*/
	function _encode($password) {
		$majorsalt = $this->auth->config->item('WEN_salt');
        $_pass = str_split($password);
		// encrypts every single letter of the password
		foreach ($_pass as $_hashpass) {
			$majorsalt .= md5($_hashpass);
		}
		return md5($majorsalt);
	}

	function _array_in_array($needle, $haystack) {
		// Make sure $needle is an array for foreach
		if (!is_array($needle)) {
			$needle = array (
				$needle
			);
		}

		// For each value in $needle, return TRUE if in $haystack
		foreach ($needle as $pin) {
			if (in_array($pin, $haystack))
				return TRUE;
		}
		// Return FALSE if none of the values from $needle are found in $haystack
		return FALSE;
	}

	function _email($to, $from, $subject, $message) {
		$this->auth->load->library('Email');
		$email = $this->auth->email;

		$email->from($from);
		$email->to($to);
		$email->subject($subject);
		$email->message($message);

		return $email->send();
	}

	// Set last ip and last login function when user login
	function _set_last_ip_and_last_login($user_id) {
		$data = array ();

		if ($this->auth->config->item('WEN_login_record_ip')) {
			$data['last_ip'] = $this->auth->input->ip_address();
		}
        $data['last_login'] = local_to_gmt();

		if (!empty ($data)) {
			// Load model
			$this->auth->load->model('Users_model');
			// Update record
			$this->auth->Users_model->set_user($user_id, $data);
		}
	}

	// Increase login attempt
	function _increase_login_attempt() { }

	// Clear login attempts
	function _clear_login_attempts() { }

	// Get role data from database by id, used in _set_session() function
	// $parent_roles_id, $parent_roles_name is an array.
	function _get_role_data($role_id) {

		// Clear return value
		$role_name = '';
		$parent_roles_id = array ();
		$parent_roles_name = array ();
		$permission = array ();
		$parent_permissions = array ();

		/* Get role_name, parent_roles_id and parent_roles_name */

		// Get role query from role id
		$query = $this->auth->roles->get_role_by_id($role_id);

		// Check if role exist
		if ($query->num_rows() > 0) {
			// Get row
			$role = $query->row();

			// Get role name
			$role_name = $role->name;

			/*
				Code below will search if user role_id have parent_id > 0 (which mean role_id have parent role_id)
				and do it recursively until parent_id reach 0 (no parent) or parent_id not found.

				If anyone have better approach than this code, please let me know.
			*/

			// Check if role has parent id
			if ($role->parent_id > 0) {
				// Add to result array
				$parent_roles_id[] = $role->parent_id;

				// Set variable used in looping
				$finished = FALSE;
				$parent_id = $role->parent_id;

				// Get all parent id
				while ($finished == FALSE) {
					$i_query = $this->auth->roles->get_role_by_id($parent_id);

					// If role exist
					if ($i_query->num_rows() > 0) {
						// Get row
						$i_role = $i_query->row();

						// Check if role doesn't have parent
						if ($i_role->parent_id == 0) {
							// Get latest parent name
							$parent_roles_name[] = $i_role->name;
							// Stop looping
							$finished = TRUE;
						} else {
							// Change parent id for next looping
							$parent_id = $i_role->parent_id;

							// Add to result array
							$parent_roles_id[] = $parent_id;
							$parent_roles_name[] = $i_role->name;
						}
					} else {
						// Remove latest parent_roles_id since parent_id not found
						array_pop($parent_roles_id);
						// Stop looping
						$finished = TRUE;
					}
				}
			}
		}

		/* End of Get role_name, parent_roles_id and parent_roles_name */

		/* Get user and parents permission */

		// Get user role permission
		$permission = $this->auth->permissions->get_permission_data($role_id);

		// Get user role parent permissions
		if (!empty ($parent_roles_id)) {
			$parent_permissions = $this->auth->permissions->get_permissions_data($parent_roles_id);
		}

		/* End of Get user and parents permission */

		// Set return value
		$data['role_name'] = $role_name;
		$data['parent_roles_id'] = $parent_roles_id;
		$data['parent_roles_name'] = $parent_roles_name;
		$data['permission'] = $permission;
		$data['parent_permissions'] = $parent_permissions;

		return $data;
	}

	/* Autologin related function */

	function _create_autologin($user_id) {
		$result = FALSE;

		// User wants to be remembered
		$user = array (
		'key_id' => substr(md5(uniqid(rand() . $this->auth->input->cookie($this->auth->config->item('sess_cookie_name')))), 0, 16), 'user_id' => $user_id);


		// Prune keys
		$this->auth->user_autologin->prune_keys($user['user_id']);

		if ($this->auth->user_autologin->store_key($user['key_id'], $user['user_id'])) {
			// Set Users AutoLogin cookie
			$this->_auto_cookie($user);

			$result = TRUE;
		}

		return $result;
	}

	function autologin() {
		$result = FALSE;

		if ($auto = $this->auth->input->cookie($this->auth->config->item('WEN_autologin_cookie_name')) AND !$this->auth->session->userdata('WEN_logged_in')) {
			// Extract data
			$auto = unserialize($auto);

			if (isset ($auto['key_id']) AND $auto['key_id'] AND $auto['user_id']) {
				// Load Models
				$this->auth->load->model('wen_auth/user_autologin', 'user_autologin');

				// Get key
				$query = $this->auth->user_autologin->get_key($auto['key_id'], $auto['user_id']);

				if ($result = $query->row()) {
					// User verified, log them in
					$this->_set_session($result);
					// Renew users cookie to prevent it from expiring
					$this->_auto_cookie($auto);

					// Set last ip and last login
					$this->_set_last_ip_and_last_login($auto['user_id']);

					$result = TRUE;
				}
			}
		}

		return $result;
	}

	function _delete_autologin() {
		if ($auto = $this->auth->input->cookie($this->auth->config->item('WEN_autologin_cookie_name'))) {
			// Load Cookie Helper
			$this->auth->load->helper('cookie');

			// Load Models
			$this->auth->load->model('wen_auth/user_autologin', 'user_autologin');

			// Extract data
			$auto = unserialize($auto);

			// Delete db entry
			$this->auth->user_autologin->delete_key($auto['key_id'], $auto['user_id']);

			// Make cookie expired
			set_cookie($this->auth->config->item('WEN_autologin_cookie_name'), '', -1);
		}
	}

	function _set_session($data) {
		if (isset ($data->id) && isset ($data->username)  && isset ($data->ref_id) && isset ($data->role_id)) {
			// Get role data
			$role_data = $this->_get_role_data($data->role_id);

			// Set session data array
			$user = array (
				'WEN_user_id' => $data->id,
				'WEN_username' => $data->username,
			    'WEN_useralias' => $data->alias,
				'WEN_emailId' => $data->email,
				'WEN_phone' => $data->phone,
				'WEN_refId' => $data->ref_id,
				'WEN_role_id' => $data->role_id,
				'WEN_coupon_code'=>$data->coupon_code,
				'WEN_timezone' => $data->timezone,
				'WEN_role_name' => $role_data['role_name'],
					'WEN_parent_roles_id' => $role_data['parent_roles_id'], // Array of parent role_id
		'WEN_parent_roles_name' => $role_data['parent_roles_name'], // Array of parent role_name
	'WEN_permission' => $role_data['permission'],
				'WEN_parent_permissions' => $role_data['parent_permissions'],
				'WEN_logged_in' => TRUE
			);
			$this->auth->session->set_userdata($user);
		} else {
			redirect('user/logout');
		}
	}

	function _auto_cookie($data) {
		// Load Cookie Helper
		$this->auth->load->helper('cookie');

		$cookie = array (
			'name' => $this->auth->config->item('WEN_autologin_cookie_name'
		), 'value' => serialize($data), 'expire' => $this->auth->config->item('WEN_autologin_cookie_life'));

		set_cookie($cookie);
	}

	/* End of Auto login related function */

	/* Helper function */

	function check_uri_permissions($allow = TRUE) {
		// First check if user already logged in or not
		if ($this->is_logged_in()) {
			// If user is not admin
			if (!$this->is_admin()) {
				// Get variable from current URI
				$controller = '/' . $this->auth->uri->rsegment(1) . '/';
				if ($this->auth->uri->rsegment(2) != '') {
					$action = $controller . $this->auth->uri->rsegment(2) . '/';
				} else {
					$action = $controller . 'index/';
				}

				// Get URI permissions from role and all parents
				// Note: URI permissions is saved in 'uri' key
				$roles_allowed_uris = $this->get_permissions_value('uri');

				// Variable to determine if URI found
				$have_access = !$allow;
				// Loop each roles URI permissions
				foreach ($roles_allowed_uris as $allowed_uris) {
					if ($allowed_uris != NULL) {
						// Check if user allowed to access URI
						if ($this->_array_in_array(array (
								'/',
								$controller,
								$action
							), $allowed_uris)) {
							$have_access = $allow;
							// Stop loop
							break;
						}
					}
				}

				// Trigger event
				$this->auth->wen_auth_event->checked_uri_permissions($this->get_user_id(), $have_access);

				if (!$have_access) {
					// User didn't have previlege to access current URI, so we show user 403 forbidden access
					$this->deny_access();
				}
			}
		} else {
			// User haven't logged in, so just redirect user to login page
			$this->deny_access('login');
		}
	}

	/*
		Get permission value from specified key.
		Call this function only when user is logged in already.
		$key is permission array key (Note: permissions is saved as array in table).
		If $check_parent is TRUE means if permission value not found in user role, it will try to get permission value from parent role.
		Returning value if permission found, otherwise returning NULL
	*/
	function get_permission_value($key, $check_parent = TRUE) {
		// Default return value
		$result = NULL;

		// Get current user permission
		$permission = $this->auth->session->userdata('WEN_permission');

		// Check if key is in user permission array
		if (array_key_exists($key, $permission)) {
			$result = $permission[$key];
		}
		// Key not found
		else {
			if ($check_parent) {
				// Get current user parent permissions
				$parent_permissions = $this->auth->session->userdata('WEN_parent_permissions');

				// Check parent permissions array
				foreach ($parent_permissions as $permission) {
					if (array_key_exists($key, $permission)) {
						$result = $permission[$key];
						break;
					}
				}
			}
		}

		// Trigger event
		$this->auth->wen_auth_event->got_permission_value($this->get_user_id(), $key);

		return $result;
	}

	/*
		Get permissions value from specified key.
		Call this function only when user is logged in already.
		This will get user permission, and it's parents permissions.

		$array_key = 'default'. Array ordered using 0, 1, 2 as array key.
		$array_key = 'role_id'. Array ordered using role_id as array key.
		$array_key = 'role_name'. Array ordered using role_name as array key.

		Returning array of value if permission found, otherwise returning NULL.
	*/
	function get_permissions_value($key, $array_key = 'default') {
		$result = array ();

		$role_id = $this->auth->session->userdata('WEN_role_id');
		$role_name = $this->auth->session->userdata('WEN_role_name');

		$parent_roles_id = $this->auth->session->userdata('WEN_parent_roles_id');
		$parent_roles_name = $this->auth->session->userdata('WEN_parent_roles_name');

		// Get current user permission
		$value = $this->get_permission_value($key, FALSE);

		if ($array_key == 'role_id') {
			$result[$role_id] = $value;
		}
		elseif ($array_key == 'role_name') {
			$result[$role_name] = $value;
		} else {
			array_push($result, $value);
		}

		// Get current user parent permissions
		$parent_permissions = $this->auth->session->userdata('WEN_parent_permissions');

		$i = 0;
		foreach ($parent_permissions as $permission) {
			if (array_key_exists($key, $permission)) {
				$value = $permission[$key];
			}

			if ($array_key == 'role_id') {
				// It's safe to use $parents_roles_id[$i] because array order is same with permission array
				$result[$parent_roles_id[$i]] = $value;
			}
			elseif ($array_key == 'role_name') {
				// It's safe to use $parents_roles_name[$i] because array order is same with permission array
				$result[$parent_roles_name[$i]] = $value;
			} else {
				array_push($result, $value);
			}

			$i++;
		}

		// Trigger event
		$this->auth->wen_auth_event->got_permissions_value($this->get_user_id(), $key);

		return $result;
	}

	function deny_access($uri = 'deny') {
		$this->auth->load->helper('url');

		if ($uri == 'login') {
			redirect($this->auth->config->item('WEN_login_uri'), 'location');
		} else
			if ($uri == 'banned') {
				redirect($this->auth->config->item('WEN_banned_uri'), 'location');
			} else {
				redirect($this->auth->config->item('WEN_deny_uri'), 'location');
			}
		exit;
	}

	// Get Site Title
	function get_site_title() {
		return $this->auth->db->get_where('settings', array (
			'code' => 'SITE_TITLE'
		))->row()->string_value;
		;
	}
	// Set weibo jifen
	function set_weibo_jifen($uid=0) {
		if($uid){
			$jifen =  $this->auth->db->get_where('settings', array (
			'code' => 'WEIBO_JIFEN'
		))->row()->int_value;
		$this->auth->db->query("UPDATE users SET jifen = jifen + {$jifen} WHERE id = {$uid} limit 1 ");
		return true;
		}else{
			return false;
		}
	}
	// Set weibo reply jifen
	function set_weibo_rjifen($uid=0) {
		/*if($uid){
			$jifen =  $this->auth->db->get_where('settings', array (
			'code' => 'WEIBO_RJIFEN'
		))->row()->int_value;
		$this->auth->db->query("UPDATE users SET jifen = jifen + {$jifen} WHERE id = {$uid} limit 1 ");
		return true;
		}else{
			return false;
		}*/
	}
	// Get Site Super Admin Email ID
	function get_site_sadmin() {
		$site_admin = $this->auth->db->get_where('settings', array (
			'code' => 'SITE_ADMIN_MAIL'
		))->row()->string_value;
		return $site_admin;
	}

	// Get user id
	function get_user_id() {
		return $this->auth->session->userdata('WEN_user_id');
	}

	// Get username string
	function get_username() {
		return $this->auth->session->userdata('WEN_useralias');
	}

	// Get email string
	function get_emailId() {
		return $this->auth->session->userdata('WEN_emailId');
	}
    // Get phone string
	function get_phone() {
		return $this->auth->session->userdata('WEN_phone');
	}
	// Get refId string
	function get_refId() {
		return $this->auth->session->userdata('WEN_refId');
	}

	// Get user role id
	function get_role_id() {
		return $this->auth->session->userdata('WEN_role_id');
	}

	// Get user role name
	function get_role_name() {
		return $this->auth->session->userdata('WEN_role_name');
	}
    // Get user role name
	function get_coupon_code() {
		return $this->auth->session->userdata('WEN_coupon_code');
	}
	// Get user timezone
	function get_timezone() {
		return $this->auth->session->userdata('WEN_timezone');
	}

	// Check is user is has admin privilege
	function is_admin() {
		return strtolower($this->auth->session->userdata('WEN_role_name')) == 'admin';
	}
    // Check is user is has admin privilege
	function is_vip() {
		return strtolower($this->auth->session->userdata('WEN_role_name')) == 'vip';
	}
	// Check if user has $roles privilege
	// If $use_role_name TRUE then $roles is name such as 'admin', 'editor', 'etc'
	// else $roles is role_id such as 0, 1, 2
	// If $check_parent is TRUE means if roles not found in user role, it will check if user role parent has that roles
	function is_role($roles = array (), $use_role_name = TRUE, $check_parent = TRUE) {
		// Default return value
		$result = FALSE;

		// Build checking array
		$check_array = array ();

		if ($check_parent) {
			// Add parent roles into check array
			if ($use_role_name) {
				$check_array = $this->auth->session->userdata('WEN_parent_roles_name');
			} else {
				$check_array = $this->auth->session->userdata('WEN_parent_roles_id');
			}
		}

		// Add current role into check array
		if ($use_role_name) {
			array_push($check_array, $this->auth->session->userdata('WEN_role_name'));
		} else {
			array_push($check_array, $this->auth->session->userdata('WEN_role_id'));
		}

		// If $roles not array then we add it into an array
		if (!is_array($roles)) {
			$roles = array (
				$roles
			);
		}

		if ($use_role_name) {
			// Convert check array into lowercase since we want case insensitive checking
			for ($i = 0; $i < count($check_array); $i++) {
				$check_array[$i] = strtolower($check_array[$i]);
			}

			// Convert roles into lowercase since we want insensitive checking
			for ($i = 0; $i < count($roles); $i++) {
				$roles[$i] = strtolower($roles[$i]);
			}
		}

		// Check if roles exist in check_array
		if ($this->_array_in_array($roles, $check_array)) {
			$result = TRUE;
		}

		return $result;
	}

	// Check if user is logged in
	function is_logged_in() {
		return $this->auth->session->userdata('WEN_logged_in');
	}

	// Check if user is a banned user, call this only after calling login() and returning FALSE
	function is_banned() {
		return $this->_banned;
	}

	// Get ban reason, call this only after calling login() and returning FALSE
	function get_ban_reason() {
		return $this->_ban_reason;
	}
	// Check if phone is available to use, by making sure there is no same phone in the database
	function is_phone_available($phone) {
		// Load Models
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_temp', 'user_temp');

		$users = $this->auth->Users_model->check_phone($phone);
		//$temp = $this->auth->user_temp->check_phone($phone);

		return $users->num_rows() == 0;
	}
	// Check if username is available to use, by making sure there is no same username in the database
	function is_username_available($username) {
		// Load Models
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_temp', 'user_temp');

		$users = $this->auth->Users_model->check_username($username);
		$temp = $this->auth->user_temp->check_username($username);

		return $users->num_rows() + $temp->num_rows() == 0;
	}

	// Check if email is available to use, by making sure there is no same email in the database
	function is_email_available($email) {
		// Load Models
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_temp', 'user_temp');

		$users = $this->auth->Users_model->check_email($email);
		$temp = $this->auth->user_temp->check_email($email);

		return $users->num_rows() + $temp->num_rows() == 0;
	}

	// Check if login attempts bigger than max login attempts specified in config
	function is_max_login_attempts_exceeded() {
		$this->auth->load->model('wen_auth/login_attempts', 'login_attempts');

		return ($this->auth->login_attempts->check_attempts($this->auth->input->ip_address())->num_rows() >= $this->auth->config->item('WEN_max_login_attempts'));
	}

	function get_auth_error() {
		return $this->_auth_error;
	}

	/* End of Helper function */

	/* Main function */

	// $login is username or email or both depending on setting in config file
	function login($login, $password, $remember = TRUE, $is = TRUE) {
		// Load Models

		// Default return value
		$result = FALSE;

		if (!empty ($login) AND !empty ($password)) {
		 $get_user_function = 'get_login';
		 	//echo "<pre>";
		 	$query = $this->auth->Users_model-> $get_user_function ($login);//exit();
		 	//print_r($query->num_rows());exit();
			//echo $login;exit();
			// Get user query
			//var_dump($query = $this->auth->Users_model-> $get_user_function ($login) AND $query->num_rows() == 1);exit();
			if ($query = $this->auth->Users_model-> $get_user_function ($login) AND $query->num_rows() == 1) {
				// Get user record
				$row = $query->row();

				 if ($row->banned == 1) {// Check if user is banned or not
					$this->_banned = TRUE;
					$this->_ban_reason = $row->ban_reason;
				} else {

                     if($is === TRUE){
                        $password = $this->_encode($password);
                        $stored_hash = $row->password;

                        //var_dump(crypt($password, $stored_hash) === $stored_hash);die;
                        if (crypt($password, $stored_hash) === $stored_hash) {
                            // Log in user
                            //print_r($row);die;
                            $this->_set_session($row);

                            if ($row->newpass) {
                                // Clear any Reset Passwords
                                $this->auth->users->clear_newpass($row->id);
                            }

                            if ($remember) {
                                // Create auto login if user want to be remembered
                                $this->_create_autologin($row->id);
                            }

                            // Set last ip and last login
                            $this->_set_last_ip_and_last_login($row->id);
                            // Clear login attempts
                            $this->_clear_login_attempts();

                            // Trigger event
                            $this->auth->wen_auth_event->user_logged_in($row->id);

                            // Set return value
                            $result = TRUE;
                            if($row->state == 0){// Check if register complete
                                   $this->complete = FALSE;$this->nextStep = $row->role_id;
                            }
                        }
					}else if($is === False){
                         $this->_set_session($row);

                         if ($row->newpass) {
                             // Clear any Reset Passwords
                             $this->auth->users->clear_newpass($row->id);
                         }

                         if ($remember) {
                             // Create auto login if user want to be remembered
                             $this->_create_autologin($row->id);
                         }

                         // Set last ip and last login
                         $this->_set_last_ip_and_last_login($row->id);
                         // Clear login attempts
                         $this->_clear_login_attempts();

                         // Trigger event
                         $this->auth->wen_auth_event->user_logged_in($row->id);

                         // Set return value
                         $result = TRUE;
                         if($row->state == 0){// Check if register complete
                             $this->complete = FALSE;$this->nextStep = $row->role_id;
                         }
                     }else{
						// Increase login attempts
						$this->_increase_login_attempt();
						// Set error message
						$this->_auth_error = $this->auth->lang->line('auth_login_incorrect_password');
					}
				 }
			}
			// Check if login is still not activated
			elseif ($query = $this->auth->user_temp->$get_user_function ($login) AND $query->num_rows() == 1) {
				// Set error message
				$this->_auth_error = $this->auth->lang->line('auth_not_activated');
			} else {
				// Increase login attempts
				$this->_increase_login_attempt();
				// Set error message
				$this->_auth_error = $this->auth->lang->line('auth_login_username_not_exist');
			}
		}
		return $result;
	}

	function logout() {
		// Trigger event
		$this->auth->wen_auth_event->user_logging_out($this->auth->session->userdata('WEN_user_id'));
		
		setcookie("b_user_id","",time()-3600*12,'/','.meilimei.com');
		// Delete auto login
		if ($this->auth->input->cookie($this->auth->config->item('WEN_autologin_cookie_name'))) {
			$this->_delete_autologin();
		}
		//$this->delete_seesionid();
		// Destroy session
		$this->auth->session->sess_destroy();
	}
	// delete sessionid
	public function delete_seesionid(){
		$time = time() - 86400;
		$this->db->where('id', $this->get_user_id());
		$this->db->update('users', array('expire'=>$time));
		return $this->db->last_query();
	}

    function _setRegFrom($val,$regsys='windows'){
       $this->regfrom = $val;
       $this->regsys = $regsys;
    }
	function register($username, $password, $email,$phone='', $device_sn = '', $ref_id = '', $role_id=1,$coupon_code = '', $created = '', $user_id = '',$sendmail = false,$autoLog=true) {
		// Load Models

		if($role_id==1){
			$banned = 0;
		}else{
            $banned = 2;
		}
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_temp', 'user_temp');

		$this->auth->load->helper('url');

		// Default return value
		$result = FALSE;

		if ($coupon_code == '') {
		//	srand((double) microtime() * 1000000);
			$coupon_code = uniqid();
		} else {
			$coupon_code = $coupon_code;
		}

		if ($ref_id == '') {
			$ref_id = md5($username);
		} else {
			$ref_id = $ref_id;
		}

		if ($created == '') {
			$created = time();
		} else {
			$created = $created;
		}
        if($phone){
        	$re_phone=0;
        }else{
        	$re_phone=1;
        }
		// New user array
		$new_user = array ('regsys' => $this->regsys,
			'username' => $username,'regfrom' => $this->regfrom,
			'password' => crypt($this->_encode($password
		)),'rev_phone'=>$re_phone, 'email' => $email, 'ref_id' => $ref_id,'role_id'=>$role_id, 'device_sn' => $device_sn, 'phone' => $phone, 'timezone' => 'UP8', 'coupon_code' => $coupon_code, 'last_ip' => $this->auth->input->ip_address(),'last_login' => $created, 'created' => $created, 'banned' => $banned);

		if ($user_id != '') {
			$new_user['id'] = $user_id;
		}
//var_dump($this->auth->config->item('WEN_email_activation'));die;
		// Do we need to send email to activate user
		if ($this->auth->config->item('WEN_email_activation')) {
			$new_user['activation_key'] = md5(rand() . microtime());
			$insert = $this->auth->user_temp->create_temp($new_user);
		} else {
			//var_dump($new_user);die;
	    	$insert = $this->auth->Users_model->create_user($new_user);
//var_dump($insert);die;
			$admin_email = $this->get_site_sadmin();
			$admin_name = $this->get_site_title();

			$email_name = 'users_signin';
			$splVars = array (
			"{site_name}" => $this->get_site_title(), "{email}" => $new_user['email'], "{password}" => $password);

			if ($user_id == '' && $new_user['email']!='' && $sendmail) {
				//Send Mail
				$this->auth->Email_model->sendMail($new_user['email'], $admin_email, ucfirst($admin_name), $email_name, $splVars);
			}

        	$new_user['user_id'] = $insert;
			$user = array (
			    'WEN_user_id'=>$new_user['user_id'],
			    'WEN_emailId' => $new_user['email'],
			    'WEN_refId' => $new_user['ref_id'],
			    //'ref_id'=> $new_user['ref_id'],
			    'WEN_role_id' => $new_user['role_id'],
				'WEN_logged_in' => TRUE,
			    'Wen_state'=>FALSE,
			    'uinfonotcomplete'=>TRUE,
			    'user_id' => $new_user['user_id'],
			    'WEN_phone' => $new_user['phone'],
			    'WEN_coupon_code'=>$new_user['coupon_code'],
			    'WEN_timezone' => $new_user['timezone']

			);

			$autoLog&&$this->auth->session->set_userdata($user);
			// Trigger event
			$autoLog&&$this->auth->wen_auth_event->user_activated($insert);
		}
      //   $result = $new_user;
		if ($insert) {
			// Replace password with plain for email
			$new_user['password'] = $password;

			$result = $new_user;

			// Send email based on config

			// Check if user need to activate it's account using email
			if ($this->auth->config->item('WEN_email_activation')) {
				// Create email
				$from = $this->auth->config->item('WEN_webmaster_email');
				$subject = sprintf($this->auth->lang->line('auth_activate_subject'), $this->auth->config->item('WEN_website_name'));

				// Activation Link
				$new_user['activate_url'] = site_url($this->auth->config->item('WEN_activate_uri') . "{$new_user['username']}/{$new_user['activation_key']}");

				// Trigger event and get email content
				$this->auth->wen_auth_event->sending_activation_email($new_user, $message);

				// Send email with activation link
				$this->_email($email, $from, $subject, $message);
			} else {
				// Check if need to email account details
				if ($this->auth->config->item('WEN_email_account_details')) {
					// Create email
					$from = $this->auth->config->item('WEN_webmaster_email');
					$subject = sprintf($this->auth->lang->line('auth_account_subject'), $this->auth->config->item('WEN_website_name'));

					// Trigger event and get email content
					$this->auth->wen_auth_event->sending_account_email($new_user, $message);

					// Send email with account details
					$this->_email($email, $from, $subject, $message);
				}
			}
		}

		return $result;
	}

	function forgot_password($login) {
		// Default return value
		$result = FALSE;

		if ($login) {
			// Load Model
			$this->auth->load->model('Users_model');
			// Load Helper
			$this->auth->load->helper('url');

			// Get login and check if it's exist
			if ($query = $this->auth->users->get_login($login) AND $query->num_rows() == 1) {
				// Get User data
				$row = $query->row();

				// Check if there is already new password created but waiting to be activated for this login
				if (!$row->newpass_key) {
					// Appearantly there is no password created yet for this login, so we create new password
					$data['password'] = $this->_gen_pass();

					// Encode & Crypt password
					$encode = crypt($this->_encode($data['password']));

					// Create key
					$data['key'] = md5(rand() . microtime());

					// Create new password (but it haven't activated yet)
					$this->auth->Users_model->newpass($row->id, $encode, $data['key']);

					// Create reset password link to be included in email
					$data['reset_password_uri'] = site_url($this->auth->config->item('WEN_reset_password_uri') . "{$row->username}/{$data['key']}");

					// Create email
					$from = $this->auth->config->item('WEN_webmaster_email');
					$subject = $this->auth->lang->line('auth_forgot_password_subject');

					// Trigger event and get email content
					$this->auth->wen_auth_event->sending_forgot_password_email($data, $message);

					// Send instruction email
					$this->_email($row->email, $from, $subject, $message);

					$result = TRUE;
				} else {
					// There is already new password waiting to be activated
					$this->_auth_error = $this->auth->lang->line('auth_request_sent');
				}
			} else {
				$this->_auth_error = $this->auth->lang->line('auth_username_or_email_not_exist');
			}
		}

		return $result;
	}

	function reset_password($username, $key = '') {
		// Load Models
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_autologin', 'user_autologin');

		// Default return value
		$result = FALSE;

		// Default user_id set to none
		$user_id = 0;

		// Get user id
		if ($query = $this->auth->users->get_user_by_username($username) AND $query->num_rows() == 1) {
			$user_id = $query->row()->id;

			// Try to activate new password
			if (!empty ($username) AND !empty ($key) AND $this->auth->Users_model->activate_newpass($user_id, $key) AND $this->auth->db->affected_rows() > 0) {
				// Clear previously setup new password and keys
				$this->auth->user_autologin->clear_keys($user_id);

				$result = TRUE;
			}
		}
		return $result;
	}

	function activate($username, $key = '') {
		// Load Models
		$this->auth->load->model('Users_model');
		$this->auth->load->model('wen_auth/user_temp', 'user_temp');

		// Default return value
		$result = FALSE;

		if ($this->auth->config->item('WEN_email_activation')) {
			// Delete user whose account expired (not activated until expired time)
			$this->auth->user_temp->prune_temp();
		}

		// Activate user
		if ($query = $this->auth->user_temp->activate_user($username, $key) AND $query->num_rows() > 0) {
			// Get user
			$row = $query->row_array();

			$del = $row['id'];

			// Unset any unwanted fields
			unset ($row['id']); // We don't want to copy the id across
			unset ($row['activation_key']);

			// Create user
			if ($this->auth->users->create_user($row)) {
				// Trigger event
				$this->auth->wen_auth_event->user_activated($this->auth->db->insert_id());

				// Delete user from temp
				$this->auth->user_temp->delete_user($del);

				$result = TRUE;
			}
		}

		return $result;
	}

	function change_password($old_pass, $new_pass,$check=true) {
		// Load Models
		$this->auth->load->model('Users_model');

		// Default return value
		$result = FAlSE;

		// Search current logged in user in database
		if ($query = $this->auth->Users_model->get_user_by_id($this->auth->session->userdata('WEN_user_id')) AND $query->num_rows() > 0) {
			// Get current logged in user
			$row = $query->row();

			$pass = $this->_encode($old_pass);

			// Check if old password correct
			if (crypt($pass, $row->password) === $row->password || !$check) {
				// Crypt and encode new password
				$new_pass = crypt($this->_encode($new_pass));

				// Replace old password with new password
				$this->auth->Users_model->change_password($this->auth->session->userdata('WEN_user_id'), $new_pass);

				// Trigger event
				$this->auth->wen_auth_event->user_changed_password($this->auth->session->userdata('WEN_user_id'), $new_pass);

				$result = TRUE;
			} else {
				$this->_auth_error = $this->auth->lang->line('auth_incorrect_old_password');
			}
		}

		return $result;
	}

	function change_passwordbynologin($new_pass,$phone) {
		// Load Models
		$this->auth->load->model('Users_model');

		// Default return value
		 $result = false;
		// Search current logged in user in database
		if ($query = $this->auth->Users_model->get_user_by_phone($phone) AND $query->num_rows() > 0) {
			// Get current logged in user
			$new_pass = crypt($this->_encode($new_pass));
            $this->auth->Users_model->change_password_by_phone($phone, $new_pass);
            $result = true;
		}

		return $result;
	}
	function cancel_account($password) {
		// Load Models
		$this->auth->load->model('Users_model');

		// Default return value
		$result = FAlSE;

		// Search current logged in user in database
		if ($query = $this->auth->Users_model->get_user_by_id($this->auth->session->userdata('WEN_user_id')) AND $query->num_rows() > 0) {
			// Get current logged in user
			$row = $query->row();

			$pass = $this->_encode($password);

			// Check if password correct
			if (crypt($pass, $row->password) === $row->password) {
				// Trigger event
				$this->auth->wen_auth_event->user_canceling_account($this->auth->session->userdata('WEN_user_id'));

				// Delete user
				$result = $this->auth->Users_model->delete_user($this->auth->session->userdata('WEN_user_id'));

				// Force logout
				$this->logout();
			} else {
				$this->_auth_error = $this->auth->lang->line('auth_incorrect_password');
			}
		}

		return $result;
	}

	/* End of main function */

	/* Captcha related function */

	function captcha() {
		$this->auth->load->helper('url');

		//$this->auth->load->plugin('dx_captcha');

		$captcha_dir = trim($this->auth->config->item('WEN_captcha_path'), './');

		$vals = array (
			'img_path' => './' . $captcha_dir . '/',
		'img_url' => base_url() . $captcha_dir . '/', 'font_path' => $this->auth->config->item('WEN_captcha_fonts_path'), 'font_size' => $this->auth->config->item('WEN_captcha_font_size'), 'img_width' => $this->auth->config->item('WEN_captcha_width'), 'img_height' => $this->auth->config->item('WEN_captcha_height'), 'show_grid' => $this->auth->config->item('WEN_captcha_grid'), 'expiration' => $this->auth->config->item('WEN_captcha_expire'));

		//$cap = create_captcha($vals);

		$store = array (
			'captcha_word' => $cap['word'],
			'captcha_time' => $cap['time']
		);

		// Plain, simple but effective
		$this->auth->session->set_flashdata($store);

		// Set our captcha
		$this->_captcha_image = $cap['image'];
	}

	function get_captcha_image() {
		return $this->_captcha_image;
	}

	// Check if captcha already expired
	// Use this in callback function in your form validation
	function is_captcha_expired() {
		// Captcha Expired
		list ($usec, $sec) = explode(" ", microtime());
		$now = ((float) $usec + (float) $sec);

		// Check if captcha already expired
		return (($this->auth->session->flashdata('captcha_time') + $this->auth->config->item('WEN_captcha_expire')) < $now);
	}

	// Check is captcha match with code
	// Use this in callback function in your form validation
	function is_captcha_match($code) {
		if ($this->auth->config->item('WEN_captcha_case_sensitive')) {
			// Just check if code is the same value with flash data captcha_word which created in captcha() function
			$result = ($code == $this->auth->session->flashdata('captcha_word'));
		} else {
			$result = strtolower($code) == strtolower($this->auth->session->flashdata('captcha_word'));
		}

		return $result;
	}

	/* End of captcha related function */

	/* Recaptcha function */

	function get_recaptcha_reload_link($text = 'Get another CAPTCHA') {
		return '<a href="javascript:Recaptcha.reload()">' . $text . '</a>';
	}

	function get_recaptcha_switch_image_audio_link($switch_image_text = 'Get an image CAPTCHA', $switch_audio_text = 'Get an audio CAPTCHA') {
		return '<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">' . $switch_audio_text . '</a></div>
					<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">' . $switch_image_text . '</a></div>';
	}

	function get_recaptcha_label($image_text = 'Enter the words above', $audio_text = 'Enter the numbers you hear') {
		return '<span class="recaptcha_only_if_image">' . $image_text . '</span>
					<span class="recaptcha_only_if_audio">' . $audio_text . '</span>';
	}

	// Get captcha image
	function get_recaptcha_image() {
		return '<div id="recaptcha_image"></div>';
	}

	// Get captcha input box
	// IMPORTANT: You should at least use this function when showing captcha even for testing, otherwise reCAPTCHA image won't show up
	// because reCAPTCHA javascript will try to find input type with id="recaptcha_response_field" and name="recaptcha_response_field"
	function get_recaptcha_input() {
		return '<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />';
	}

	// Get recaptcha javascript and non javasript html
	// IMPORTANT: you should put call this function the last, after you are using some of get_recaptcha_xxx function above.
	function get_recaptcha_html() {
		// Load reCAPTCHA helper function
		$this->auth->load->helper('recaptcha');

		// Add custom theme so we can get only image
		$options = "<script>
					var RecaptchaOptions = {
						 theme: 'custom',
						 custom_theme_widget: 'recaptcha_widget'
					};
					</script>";

		// Get reCAPTCHA javascript and non javascript HTML
		$html = recaptcha_get_html($this->auth->config->item('WEN_recaptcha_public_key'));

		return $options . $html;
	}

	// Check if entered captcha code match with the image.
	// Use this in callback function in your form validation
	function is_recaptcha_match() {
		$this->auth->load->helper('recaptcha');

		$resp = recaptcha_check_answer($this->auth->config->item('WEN_recaptcha_private_key'), $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

		return $resp->is_valid;
	}

	/* End of Recaptcha function */
}
?>