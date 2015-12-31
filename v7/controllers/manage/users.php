<?php
class users extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
		//$this->load->library('alicache');
       if(!$this->privilege->judge('users')){
          die('Not Allow');
       }

	}

//user info lists
    public function daren() {
        $page = $this->input->get('page');
        $tmp = $this->privilege->getPri('users');
        if(!empty($tmp) and ($tmp = unserialize($tmp[0]['data'])) and $tmp['fromv']){
            //$condition = ' WHERE 1 = 1 and users.id>='.$tmp['fromv'].' and users.id<='.$tmp['tv'];
            $condition = "WHERE 1 = 1 and users.id between {$tmp['fromv']} and {$tmp['tv']}";
        }else{
            $condition = ' WHERE 1 = 1 ';
        }
        //加载第三方类库
        $this->load->library('pager');

        $data['issubmit'] = false;$fix = '';
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        if ($this->input->get('submit')) {
            $data['issubmit'] = true;
            $fix = 'submit=true';
            if ($this->input->get('phone')) {
                $condition .= " AND users.phone = '" . $this->input->get('phone')."'";
                $fix.=$fix==''?'?phone='.$this->input->get('phone'):'&phone='.$this->input->get('phone');
            }
            if ($this->input->get('email')) {
                $condition .= "  AND users.email like '%" . trim($this->input->get('email')) . "%'";
                $fix.=$fix==''?'?sname='.$this->input->get('sname'):'&sname='.$this->input->get('sname');
            }
            if ($this->input->get('noc')) {
                $condition .= "  AND user_profile.states = 0 ";
                $fix.=$fix==''?'?noc='.$this->input->get('noc'):'&noc='.$this->input->get('noc');
            }
            if ($this->input->get('daren') == 1) {
                $condition .= "  AND users.daren = 1 ";
                $fix.=$fix==''?'?daren='.$this->input->get('daren'):'&daren='.$this->input->get('daren');
            }
// 			if($secuid = intval($this->input->get('ome'))) {
// 			    $fix.=$fix==''?'?ome='.$this->input->get('ome'):'&ome='.$this->input->get('ome');
// 			    $condition .= ' AND v.uid='.$secuid;
// 		   }
            if ($this->input->get('city')) {
                $condition .= "  AND company.city like '%" . trim($this->input->get('city')) . "%'";
                $fix.=$fix==''?'?city='.$this->input->get('city'):'&city='.$this->input->get('city');
            }
            if($this->input->get('opendate')){
                $fix.=$fix==''?'?opendate=1&':'&opendate=1&';
                $fix.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
                $fix.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
                $data['cdate'] = $this->input->get('yuyueDateStart');
                $data['edate'] = $this->input->get('yuyueDateEnd');
                $cdate = strtotime($this->input->get('yuyueDateStart'));
                $edate = strtotime($this->input->get('yuyueDateEnd'));
                $condition .= " and users.created >= {$cdate} and users.created <= {$edate} ";
            }

        }else{
            $condition .= "  AND users.daren = 2 ";
        }

        $data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC")->num_rows();

        $per_page = 30;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0){
            $offset = ($start -1) * $per_page;
        }else{
            $offset = $start * $per_page;
        }

        $sql = "SELECT users.*,user_profile.* FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page";

   //     $sql = "SELECT distinct(v.vuid),users.*,user_profile.* FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page";
        $results =  $this->db->query($sql)->result();
//
        $data['results'] = $results;


        $data['results'] = $results;




        foreach($data['results'] as $k => $v){
            $data['results'][$k]->reNums = $this->db->query("select * from wen_questions where fUid = ".$v->id."")->num_rows();
        }


        $data['offset'] = $offset +1;
        //$data['preview'] = $start > 2 ? site_url('manage/users/index/' . ($start -1)).$fix : site_url('manage/users/index').$fix;
        //$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/users/index/' . ($start +1)).$fix : '';

        $config = array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>10,
            'querystring_name'=>$fix.'page',
            'base_url'=>'manage/users/daren',
            "pager_index"=>$page
        );
       // var_dump($config["base_url"]);exit;
        $this->pager->init($config);

        //print_r($this->pager->init($config);exit;

        $data['pagelink'] = $this->pager->builder_pager();

        //print_r($data['pagelink']);exit;

        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "users_daren";
        $data['managers'] = $this->Gmanager();
        $this->session->set_userdata('history_url', 'manage/users/daren?page=' . ($start -1).'&'.$fix);
        //
        $this->load->view('manage', $data);
    }
	//user info lists
	public function index() {
		$page = $this->input->get('page');
		$tmp = $this->privilege->getPri('users');
		if(!empty($tmp) and ($tmp = unserialize($tmp[0]['data'])) and $tmp['fromv']){
			//$condition = ' WHERE 1 = 1 and users.id>='.$tmp['fromv'].' and users.id<='.$tmp['tv'];
			$condition = "WHERE 1 = 1 and users.id between {$tmp['fromv']} and {$tmp['tv']}";
		}else{
			$condition = ' WHERE 1 = 1 ';
		}
		$this->load->library('pager');

		$data['issubmit'] = false;$fix = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true'.'&';
            //按查询条件进行分页
			if ($this->input->get('phone')) {
				$condition .= " AND users.phone = '" . $this->input->get('phone')."'";
				$fix.=$fix==''?'?phone='.$this->input->get('phone').'&':'&phone='.$this->input->get('phone').'&';
			}
            if ($this->input->get('username')) {
                $condition .= " AND (users.username like '%" . $this->input->get('username')."%' or users.alias like '%".$this->input->get('username')."%')";
                $fix.=$fix==''?'?username='.$this->input->get('username').'&':'&username='.$this->input->get('username').'&';
            }
			if ($this->input->get('email')) {
				$condition .= "  AND users.email like '%" . trim($this->input->get('email')) . "%'";
				$fix.=$fix==''?'?sname='.$this->input->get('sname').'&':'&sname='.$this->input->get('sname').'&';
			}
			if ($this->input->get('noc')) {
				$condition .= "  AND user_profile.states = 0 ";
				$fix.=$fix==''?'?noc='.$this->input->get('noc').'&':'&noc='.$this->input->get('noc').'&';
			}
// 			if($secuid = intval($this->input->get('ome'))) {
// 			    $fix.=$fix==''?'?ome='.$this->input->get('ome'):'&ome='.$this->input->get('ome');
// 			    $condition .= ' AND v.uid='.$secuid;
// 		   }
			if ($this->input->get('city')) {
				$condition .= "  AND company.city like '%" . trim($this->input->get('city')) . "%'";
				$fix.=$fix==''?'?city='.$this->input->get('city').'&':'&city='.$this->input->get('city').'&';
			}
			if($this->input->get('opendate')){
			$fix.=$fix==''?'?opendate=1&':'&opendate=1&';
            $fix.='yuyueDateStart='.$this->input->get('yuyueDateStart').'&';
			$fix.='yuyueDateEnd='.$this->input->get('yuyueDateEnd').'&';
			$data['cdate'] = $this->input->get('yuyueDateStart');
			$data['edate'] = $this->input->get('yuyueDateEnd');
		    $cdate = strtotime($this->input->get('yuyueDateStart'));
            $edate = strtotime($this->input->get('yuyueDateEnd'));
            $condition .= " and users.created >= {$cdate} and users.created <= {$edate} ";
			}

		}
 
		$data['total_rows'] = $this->db->query("SELECT users.id FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC")->num_rows();
		 
		$per_page = 30;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0){
			$offset = ($start -1) * $per_page;
		}else{
			$offset = $start * $per_page;
		}

        $sql = "SELECT users.*,user_profile.* FROM users LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page";

//      $sql = "SELECT distinct(v.vuid),users.*,user_profile.* FROM users LEFT JOIN user_visit as v ON v.vuid = users.id LEFT JOIN user_profile ON user_profile.user_id=users.id {$condition} ORDER BY users.id DESC  LIMIT $offset , $per_page";
	    $results =  $this->db->query($sql)->result();
// 			
 		$data['results'] = $results;

		
		//print_r($data['results']); exit;
		
		
        
		 
        foreach($data['results'] as $k => $v){
            $data['results'][$k]->reNums = $this->db->query("select * from wen_questions where fUid = ".$v->id."")->num_rows();
        }
         

		$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/users/index/' . ($start -1)).$fix : site_url('manage/users/index').$fix;
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/users/index/' . ($start +1)).$fix : '';

         $config = array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                "show_front_btn"=>true,
                "show_last_btn"=>true,
                'max_show_page_size'=>10,
                'querystring_name'=>$fix.'page',
                'base_url'=>'manage/users/index',
                "pager_index"=>$page
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();

        //print_r($data['pagelink']);exit;

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "users";
		$data['managers'] = $this->Gmanager();
		$this->session->set_userdata('history_url', 'manage/users/index?page=' . ($start -1).'&'.$fix);
		$this->load->view('manage', $data);
	}
	//export user info
	public function export($param=''){
		if($this->input->post('yuyueDateStart')){
			$start = strtotime($this->input->post('yuyueDateStart'));
			$end   = $start+3600*24;
			$sql = "select y.ystate,y.admin_remark,u.email,u.phone,u.alias,p.* from users as u ";
			$sql .= "LEFT JOIN user_profile as p ON u.id=p.user_id ";
			$sql .= "LEFT JOIN yuyue as y ON y.userby = u.id ";
			$sql .= "where u.role_id = 1 and u.created>={$start} and u.created<{$end}";
            $res = array();
             $res = $this->db->query($sql)->result_array();

            echo '<table>';
            foreach($res as $r){

               echo '<tr><td>'.$r['alias'].'</td>
               		 <td>'.$r['company'].'</td>
               		 <td>'.$r['department'].'</td>
                     <td>'.$r['position'].'</td>
               		 <td>'.$r['tel'].'</td>
               		 <td>'.$r['phone'].'</td>
               		 <td> </td>
               		 <td> </td>
               		 <td> </td>
               		 <td> </td>
               		 <td>'.$r['email'].'</td>
               		 <td> </td>
               	  	 <td> </td>
               		 <td> </td>
               		 <td> </td>
               		 <td>'.$r['province'].'</td>
               		 <td>'.$r['city'].'</td>
               		 <td>'.$r['address'].'</td>
               		 <td>'.$r['admin_remark'].'</td>
               		 <td> </td></tr>';
            }
            echo '</table>';
            exit;
		}
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "users_export";
		$this->load->view('manage', $data);
	}
    //批量关注此用户
    public function follow($param=''){
        if($uid=$this->input->post('uid')){
            $follow = $this->input->post('follow');
            if($fans=$this->input->post('fans')){
                foreach($fans as $item){

                    if($follow == 1){
                        $condition = array(
                            'uid' => $item,
                            'fid' => $uid,
                            'type' => 8
                        );
                    }else if($follow ==2){
                        $condition = array(
                            'uid' => $uid,
                            'fid' => $item,
                            'type' => 8
                        );
                    }
                    if(intval($item) <= 0)
                        continue;

                    $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
                    if ($tmp > 0) {

                        $this->db->select('wen_follow.fid as fid');
                        $this->db->from('wen_follow');
                        if($follow == 1){
                            $this->db->where('wen_follow.fid', $uid);
                        }else if($follow ==2){
                            $this->db->where('wen_follow.fid', $item);
                        }
                        $this->db->where('wen_follow.type', 8);
                        $num = $this->db->get()->num_rows();
                        $this->session->set_flashdata('msg', $this->common->flash_message('error', '已经关注！'));
                    } else {

                        if($follow == 1){
                            $data['fid'] = $uid;
                            $data['type'] = 8;
                            $data['uid'] = $item;
                        }else if($follow ==2){
                            $data['fid'] = $item;
                            $data['type'] = 8;
                            $data['uid'] = $uid;
                        }
                        $result['updateState'] = '000';
                        $this->common->insertData('wen_follow', $data);
                    }
                }
            }else{
                $this->session->set_flashdata('msg', $this->common->flash_message('error', '请输入粉丝用户！'));
               redirect('manage/users/follow/'.$uid);
            }
            redirect('manage/users/follow/'.$uid);
        }else{
            $data['uid'] = $param;
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "follow";
            $this->load->view('manage', $data);
        }
    }
	//edit user pass
	public function editpass($param=''){
         if($uid=$this->input->post('uid')){
             if( ($newpass=$this->input->post('newpass')) && ($enpass=$this->input->post('repeatpass')) ){
             	if($enpass==$newpass){
             	   $new_pass = crypt($this->_encode($newpass));
				   $this->Users_model->change_password($uid, $new_pass);
				   //update tehui user pass
				   $this->load->library('tehui');
				   $tehui['password'] = $new_pass;
				   $this->tehui->updateUser($uid,$tehui);
                   $this->session->set_flashdata('msg', $this->common->flash_message('success', '密码成功修改！'));
             	}else{
             		$this->session->set_flashdata('msg', $this->common->flash_message('error', '密码不匹配！'));
             	}

             }else{
             	 $this->session->set_flashdata('msg', $this->common->flash_message('error', '密码修改失败！'));

             }
             redirect('manage/users');
         }else{
           $data['uid'] = $param;
           $data['notlogin'] = $this->notlogin;
		   $data['message_element'] = "edityhpass";
	       $this->load->view('manage', $data);
         }
	}
		/*
	* Function: _encode
	* Modified for WEN_Auth
	* Original Author: FreakAuth_light 1.1
	*/
	private function _encode($password) {
		$majorsalt =$this->config->item('WEN_salt');

		// if PHP5
		if (function_exists('str_split')) {
			$_pass = str_split($password);
		}
		// if PHP4
		else {
			$_pass = array ();
			if (is_string($password)) {
				for ($i = 0; $i < strlen($password); $i++) {
					array_push($_pass, $password[$i]);
				}
			}
		}

		// encrypts every single letter of the password
		foreach ($_pass as $_hashpass) {
			$majorsalt .= md5($_hashpass);
		}

		// encrypts the string combinations of every single encrypted letter
		// and finally returns the encrypted password
		return md5($majorsalt);
	}

    //用户的所有咨询
    public function allquestion($param='',$page=''){
		$data['issubmit'] = false;

		$data['total_rows'] = $this->db->query("SELECT id FROM wen_questions WHERE fUid = {$param}")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$data['results'] = $this->db->query("SELECT w.id,w.title,w.description,w.state,w.cdate, users.id as uid,users.alias,users.email,users.phone FROM wen_questions as w LEFT JOIN users ON users.id=w.fUid WHERE fUid = {$param} ORDER BY w.id DESC  LIMIT $offset , $per_page")->result();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/users/allquestion/' .$param.'/'. ($start -1)) : site_url('manage/users/allquestion/'.$param.'/' );
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/users/allquestion/' .$param.'/'. ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "userquestion";
		$this->load->view('manage', $data);
	}

	//get usre detail info
	public function detail($param = '',$view='') {
		if ($param != '') {
			 $data['view'] = false;
			if($view!=''){
              $data['view'] = true;
			}
			if ($this->input->post('submit')) {
                //
			} else {
				$uid = intval($param);
				$data['uid'] = $uid;
				$this->db->where('userid', $uid);
				$this->db->select('item_id,price');
				$prices = $this->db->get('price')->result_array();
				foreach ($prices as $row) {
					$data['prices'][$row['item_id']] = $row['price'];
				}
				$data['items'] = $this->db->get('items')->result_array();
				$this->db->where('user_profile.user_id', $uid);
				$this->db->select('user_profile.*,users.*,CRM_user.contactNext,CRM_user.remark as cremark');
				$this->db->from('user_profile');
				$this->db->join('users', 'user_profile.user_id = users.id', 'left');
			 	$this->db->join('CRM_user', 'CRM_user.uid = users.id', 'left');
				$data['userinfo'] = $this->db->get()->result_array();
                $data['thumb'] = $this->profilepic($uid, 3);

//				if (empty ($data['companyinfo'])) {
//					$infos['userid'] = $param;
//					$infos['cdate'] = time();
//					$this->common->insertData('company', $infos);
//				}

				$data['notlogin'] = $this->notlogin;
				$data['message_element'] = "edituser";
				$this->load->view('manage', $data);
			}
		}
	}
	//update or insert to CRM of customer info
	private function updateCRM($uid,$data){
		$this->db->where('uid', $uid);
		$this->db->from('CRM_user');
		if($this->db->count_all_results()==0){
			$data['cdate'] = time();
			$this->db->insert('CRM_user', $data);
		}else{
			$this->db->where('uid', $uid);
            $this->db->update('CRM_user', $data);
		};
	}
	//update general user info
	public function update() {
		if ($uid = $this->input->post('uid')) {
			$updateData['Lname'] = $this->input->post('Lname');
			$updateData['Fname'] = $this->input->post('Fname');
            if($_FILES['thumb']['tmp_name']){
                $this->thumb($uid, $_FILES['thumb']['tmp_name']);
			}
			if ($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')){
			   $supdateData['email'] = $this->input->post('email');
			}
			if($this->input->post('sourcephone') and ($this->input->post('phone') != $this->input->post('sourcephone')) and $this->_check_phone_no($supdateData['phone']) ){
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '手机号不正确或重复！'));
               redirect('manage/users/detail/'.$uid);
			}
            if($this->input->post('sourceemail') and ($this->input->post('email') != $this->input->post('sourceemail')) and $this->_check_user_email($supdateData['email']) ){
            	$this->session->set_flashdata('msg', $this->common->flash_message('error', '邮箱不正确或重复！'));
               redirect('manage/users/detail/'.$uid);
            }
			$updateData['tel'] = $this->input->post('tel');
			$updateData['introduce'] = $this->input->post('introduce');
			$updateData['remark'] = $this->input->post('remark');
			$updateData['states'] = $this->input->post('states');
			$updateData['address'] = $this->input->post('address');

            $updateData['province'] = $this->input->post('province');
           $updateData['city'] = $this->input->post('city');
           $updateData['district'] = $this->input->post('district');
            $updateData['sex'] = $this->input->post('sex');
			$this->db->where('user_id', $uid);
			$this->db->update('user_profile', $updateData);
			$crm = array();
			$crm['uid'] = $uid;
			$crm['name'] = $this->input->post('introduce');
			$crm['contactNext'] = strtotime($this->input->post('contactNext'));
			$crm['remark'] = $this->input->post('cremark');
           // $this->updateCRM($uid,$crm);
			if($this->input->post('alias')){
				 $supdateData['alias'] = $this->input->post('alias');
			    }else{
				 $supdateData['alias'] = $updateData['Lname'] . $updateData['Fname'];
			    }

			if (($this->input->post('email') && $this->input->post('email') != $this->input->post('sourceemail')) || $this->input->post('phone') != $this->input->post('sourcephone')) {
				if ($this->input->post('phone')) {
					$supdateData['phone'] = $this->input->post('phone');
					$supdateData['rev_phone'] = 0;
				}
				$supdateData['email'] = $this->input->post('email');
			}
			if($this->input->post('role_id')){
				$supdateData['role_id'] = $this->input->post('role_id');
			}

			$supdateData['state'] = $this->input->post('state');
           $this->db->where('id', $uid);
           $this->db->update('users', $supdateData);
           
           if($this->input->post('submittype')==1){
               if($this->session->userdata('history_url')!=''){
                   redirect($this->session->userdata('history_url'));
               }else{
                   redirect('manage/priv');
               }
              
           }else{
           	  redirect('manage/users/track/'.$uid);
           }
		}
	}

	//sumarize user total
	public function total(){
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        if($this->input->get('yuyueDateStart')){
        	$data['cdate'] = $this->input->get('yuyueDateStart');
        	$data['edate']  = $this->input->get('yuyueDateEnd');
           $cdate = strtotime($this->input->get('yuyueDateStart'));
           $edate = strtotime($this->input->get('yuyueDateEnd'));
        }
        $data['res'] = $this->perTotal($cdate,$edate);

        $data['notlogin'] = $this->notlogin;
	    $data['message_element'] = "user_total";
	    $this->load->view('manage', $data);
	}

    //sumarize each day of user calling
    private function perTotal($stime,$etime){
       $sql = "SELECT v.uid,count(v.id) as num,users.alias FROM user_visit as v  ";
       $sql .=" left join users on users.id = v.uid  ";
       $sql .="where v.cdate <= {$etime} AND v.cdate >= {$stime}  ";
       $sql .="group BY v.uid";
       return $this->db->query($sql)->result_array();
    }

	//today contact
	public function today() {
		  $data['res'] = $this->user_visit->today($this->uid);
		  $data['notlogin'] = $this->notlogin;
		  $data['message_element'] = "users_today";
		  $this->load->view('manage', $data);
	}
	public function sendsms() {
		  $this->load->library('sms');
		  $status = '0';
		  if($this->input->post('message') and $this->input->post('phonenum')){
             $status =  $this->sms->sendSMS(array(
                            "{$this->input->post('phonenum')}"
                        ), ''.$this->input->post('message'). '退订回复0000');
		  }
		  $data['message'] = '发送状态:' . $status;
		  $data['getBalance'] = $this->sms->getBalance();
		  $data['notlogin'] = $this->notlogin;
		  $data['message_element'] = "users_sms";
		  $this->load->view('manage', $data);
	}
	public function track($param) {
		$data['uid'] = $uid = intval($param);
		if ($remark = $this->input->post('remark')) {
            $this->user_visit->add($this->uid,$uid,$this->input->post('states'),$this->input->post('remark'),strtotime($this->input->post('nDateStart')));
		     redirect('manage/users/track/'.$uid);
		}else{
		  $data['res'] = $this->user_visit->view($uid);
		  $data['notlogin'] = $this->notlogin;
		  $data['message_element'] = "users_track";
		  $this->load->view('manage', $data);
		}
	}
	//delete user
	public function del($uid){
		$condition = array('id'=>$uid);
        $this->common->deleteTableData('users',$condition);
        //profile
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('user_profile',$condition);
         //company
        $condition = array('userid'=>$uid);
        $this->common->deleteTableData('company',$condition);
        //wen_notify
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('wen_notify',$condition);
        //user_notification
        $condition = array('user_id'=>$uid);
        $this->common->deleteTableData('user_notification',$condition);
        //wen_weibo
        $condition = array('uid'=>$uid);
        $this->common->deleteTableData('wen_weibo',$condition);
        //wen_comment
        $condition = array('fuid'=>$uid);
        $this->common->deleteTableData('wen_comment',$condition);
         //wen_follow
        $condition = array('uid'=>$uid);
        $this->common->deleteTableData('wen_follow',$condition);

        //wen_questions
        $condition = array('fUid'=>$uid);
        $this->common->deleteTableData('wen_questions',$condition);

        $this->load->library('tehui');
        $this->tehui->delUser($uid);
        //thumb
      //  $this->deleteDir($this->path . '/users/' . $uid);
       redirect('manage/users');
	}
	//get manager
	private function Gmanager(){
       $this->db->where('role_id', 16);
       $this->db->where('banned', 0);
       $this->db->select('id, alias');
       $this->db->from('users');
       return $this->db->get()->result_array();
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
	private	function _check_phone_no($value) {
		if ($value == '') {
			return TRUE;
		} else {
			if (preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $value)) {
				if ($this->wen_auth->is_phone_available($value)) {
					return preg_replace('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', '($1) $2-$3', $value);
				} else {
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '该手机号码已被使用！'));
					return FALSE;
				}

			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '请输入有效的手机号码！'));
				return FALSE;
			}
		}
	}
	private function _check_user_email($email) {
		if ($this->wen_auth->is_email_available($email) && preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,5}$/', $email)) {
			return true;
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '该邮箱已经被使用或者非法！'));
			return false;
		} //If end
	}
	private function thumb($uid, $file) {
		if ($file != '') {
				 $this->remote->uputhumb($file,$uid);
				return true;
			} else {
				return false;
			}
	}
private function deleteDir($dir) {
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
        closedir($dh);
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }

    }

    public function userac() {
        if (($uid = $this->input->get('uid'))) {
            $this->load->model('Email_model');
            $banned = intval($this->input->get('banned'));
            $ban = intval($this->input->get('daren'));
            $data['daren'] = $ban;
            $data['banned'] = $banned;
            $this->common->updateTableData('users', $uid, '', $data);
            if ($banned == 0) {
                $conditions = array (
                    'id' => $uid
                );
                $info = $this->common->getTableData('users', $conditions)->result_array();
                $splVars = array (
                    "{title}" => '账户通过审核',
                    "{content}" => '恭喜您通过审核成为美神达人，将有20000美豆加入您的账号，双倍积分特权，欢迎成为最红达人！[如美豆加分有误请到APP首页快问小美处咨询]',
                    "{time}" => date('Y-m-d H:i',
                        time()), "{site_name}" => '美丽神器');

                $info[0]['email'] != '' && $this->Email_model->sendMail($info[0]['email'], "support@meilizhensuo.com", '美丽诊所', 'user_pass', $splVars);
                if ($info[0]['phone'] != '') {
                    $this->load->library('sms');
                    $this->sms->sendSMS(array (
                        "{$info[0]['phone']}"
                    ), '恭喜您通过审核成为美神达人，将有20000美豆加入您的账号，双倍积分特权，欢迎成为最红达人！[如美豆加分有误请到APP首页快问小美处咨询]');
                }
            } else {
                $this->db->limit(1);
                $this->db->where('uid',$uid );
                if($uid){
                    $this->db->delete('wensessions');
                }
            }
            echo 'success';
        }

    }
}
?>
