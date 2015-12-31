<?php
class topic extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id()==16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->load->model('track');
		$this->load->model('remote');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('topic')){
          die('Not Allow');
       }
	}
//topic list
	public function index($page='') {
		$page = $this->input->get('page');
		$condition = '  WHERE  type &25 and isdel = 0 ';
		$data['issubmit'] = false;
		$fix = '';
		$this->load->library('pager');
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			if ($this->input->get('phone')) {
				$condition .= " AND (users.phone = '" . $this->input->get('phone')."' OR users.alias = '" . $this->input->get('phone')."')";
			    $fix.='phone='.$this->input->get('phone').'&';
			}
			if ($this->input->get('sname')) {
				$condition .= "  AND (content like '%" . trim($this->input->get('sname')) . "%' or type_data like '%" . trim($this->input->get('sname')) . "%')";
			    $fix.='sname='.$this->input->get('sname').'&';
			}
			if ($this->input->get('types')) {
				$condition .= "  AND (wsource like '%" . trim($this->input->get('types')) . "%')";
			    $fix.='types='.$this->input->get('types').'&';
			}
			if ($this->input->get('city')) {
				$condition .= "  AND user_profile.city like '%" . trim($this->input->get('city')) . "%'";
				$fix.='city='.$this->input->get('city').'&';
			}
			$fix.='submit=true';
		}
		 $data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo LEFT JOIN users ON users.id = wen_weibo.uid {$condition} ")->num_rows();

		$per_page = 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$fields = "w.weibo_id,w.wsource,w.comments,w.q_id,w.content,w.uid,w.ctime,w.type_data,users.alias,users.phone,users.email";
		$data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} GROUP BY w.weibo_id ORDER BY  w.weibo_id DESC  LIMIT $offset , $per_page")->result();

		$data['offset'] = $offset +1;
		//$data['preview'] = $start > 2 ? site_url('manage/topic/index/' . ($start -1)).$fix : site_url('manage/topic/index').$fix;
		//$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/index/' . ($start +1)).$fix : '';

        $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                'querystring_name'=>$fix.'&page',
                'base_url'=>'manage/topic/index',
                "pager_index"=>$page
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topic";
		$this->session->set_userdata('history_url', 'manage/topic?page=' . ($start -1));
		$this->load->view('manage', $data);
	}
    //wait check
    public function cktopic(){
    	$this->load->library('pager');
        $data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo WHERE isdel = 1 ")->num_rows();
        $fix='';
        $per_page = 10;
        $page = intval($this->input->get('page'));
      //  $query = $this->db->get('mytable');
         $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                'querystring_name'=>$fix.'&page',
                'base_url'=>'manage/topic/index',
                "pager_index"=>$page
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
    	$data['results'] = $this->db->query("SELECT * FROM wen_weibo WHERE isdel = 1 order by weibo_id DESC limit {$page},{$per_page}")->result();
    	//print_r($data['results']);
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_check";
        $this->load->view('manage', $data);
    }
	//get all comments
	public function comments($page=''){
        $per_page = 26;
		$start = intval($page);
		$start == 0 && $start = 1;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$condition = '  wen_comment.is_delete=0';
		$fix = '?';
        if ($this->input->get('types')) {
				$condition .= "  AND (wen_comment.device like '%" . trim($this->input->get('types')) . "%')";
			    $fix.='types='.$this->input->get('types').'&';
	    }
		$data['offset'] = $offset +1;
		$fields = "wen_comment.fuid,wen_comment.contentid,wen_comment.comment,wen_comment.id,wen_comment.cTime,wen_comment.device,users.phone,wen_weibo.type_data";
		$data['results'] = $this->db->query("SELECT {$fields} FROM  wen_comment LEFT JOIN users ON users.id=wen_comment.fuid LEFT JOIN wen_weibo ON wen_weibo.weibo_id=wen_comment.contentid Where {$condition} ORDER BY  id DESC  LIMIT $offset , $per_page")->result();
		$data['total_rows'] = $this->db->query("SELECT id FROM  wen_comment Where {$condition} ORDER BY  id DESC ")->num_rows();
		$data['preview'] = $start > 2 ? site_url('manage/topic/comments/' . ($start -1)).$fix : site_url('manage/topic/comments').$fix;
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/comments/' . ($start +1)).$fix : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topic_comments";
		$this->load->view('manage', $data);
	}
	//convert to question
	public function toquestion($param=''){
		    $this->db->where('weibo_id', $param);
		    $res = $this->db->get('wen_weibo')->result_array();
		    $ipcs = array();
		    $tmps = unserialize($res[0]['type_data']);
            $data['title'] = $tmps[0]['title'];
			$data['position'] = '';
			$data['description'] =  $res[0]['content'];
			$data['extradata'] =  serialize($ipcs);
			$data['sex'] = 0;
			$data['address'] = '';
			$data['city'] = $this->input->post('city');
			$data['toUid'] = "";
			$data['state'] = 1;
			$data['has_answer'] = 0;
			$data['cdate'] = time();
			$this->common->insertData('wen_questions', $data);
    }
	//show no classify
	public function nocla($page=''){
		$condition = "  WHERE type &25 and tags=''";
		$data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo {$condition} ")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$fields = "w.wsource,w.weibo_id,w.content,w.uid,w.ctime,w.type_data,users.alias,users.phone,users.email";
		$data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} ORDER BY  w.weight DESC  LIMIT $offset , $per_page")->result();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/topic/order/' . ($start -1)) : site_url('manage/topic/order');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/order/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topic_nocla";
		$this->load->view('manage', $data);
	}
	// show with order
	public function order($page='') {
		$condition = '  WHERE type &25';
		$data['issubmit'] = false;
		if ($this->input->post('submit')) {
			$data['issubmit'] = true;
			if ($this->input->post('phone')) {
				$condition .= " AND users.phone = '" . $this->input->get('phone')."'";
			}
			if ($this->input->post('sname')) {
				$condition .= "  AND (content like '%" . trim($this->input->post('sname')) . "%' or type_data like '%" . trim($this->input->post('sname')) . "%')";
			}
			if ($this->input->post('city')) {
				$condition .= "  AND user_profile.city like '%" . trim($this->input->post('city')) . "%'";
			}
		}
		$data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo {$condition} ")->num_rows();

		$per_page = $data['issubmit'] ? 25 : 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$fields = "w.wsource,w.weibo_id,w.content,w.uid,w.ctime,w.type_data,w.weight,users.alias,users.phone,users.email";
		$data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} ORDER BY  w.weight DESC  LIMIT $offset , $per_page")->result();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/topic/order/' . ($start -1)) : site_url('manage/topic/order');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/order/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topic_order";
		$this->load->view('manage', $data);
	}
    /** 详细页
     * @param string $param
     * @param string $page
     */
    public function detail($param='',$page=''){
    	$condition = '  WHERE type & 25 ';
		$data['issubmit'] = false;

        //提交
//		if ($this->input->post('submit')) {
//			$data['issubmit'] = true;
//			if ($this->input->post('phone')) {
//				$condition .= " AND users.phone = '" . $this->input->get('phone')."'";
//			}
//			if ($this->input->post('sname')) {
//				$condition .= "  AND users.alias like '%" . trim($this->input->post('sname')) . "%'";
//			}
//			if ($this->input->post('city')) {
//				$condition .= "  AND user_profile.city like '%" . trim($this->input->post('city')) . "%'";
//			}
//		}


		$data['total_rows'] = $this->db->query("SELECT id FROM wen_comment WHERE contentid = {$param} AND type='topic' ")->num_rows(); //评论总数

		$per_page = $data['issubmit'] ? 25 : 16;      //16
		$start = intval($page);      //开始页
		$start == 0 && $start = 1;   //如果开始页等于0，令其等于1

		if ($start > 0)
			$offset = ($start -1) * $per_page; //移位
		else
			$offset = $start * $per_page;   //0
		$data['results'] = $this->db->query("SELECT id,comment,fuid,alias FROM wen_comment WHERE contentid = {$param} AND type='topic' ORDER BY id DESC  LIMIT $offset , $per_page")->result();      //评论结果
        //error_reporting(E_ALL);
        foreach($data['results'] as $k => $v){
            $row = $this->db->query("select username,alias,phone,role_id from users where id = ".$v->fuid)->row_array();
            if(is_array($row)&&count($row)>0){
                $data['results'][$k]->phone=$row['phone'];     //电话
                $data['results'][$k]->role_id=$row['role_id'];  //角色
            }else{
                $data['results'][$k]->phone='虚拟用户';     //电话
                $data['results'][$k]->role_id='虚拟用户';  //角色
            }
        }

        $data['minfo'] = $this->db->query("SELECT content,type_data,q_id,uid,type,ctime FROM wen_weibo WHERE weibo_id = {$param}  ORDER BY weibo_id DESC  LIMIT 1")->result();  //话题内容
        $type_data = unserialize($data['minfo'][0]->type_data);

        $data['extras']['haspic']=0;
        if($data['minfo'][0]->type ==1 ){
        	$this->db->where('wen_weibo.q_id', $data['minfo'][0]->q_id);
				$this->db->where('wen_weibo.type', 4);
				$this->db->from('wen_weibo');
				$this->db->select('wen_weibo.type_data,wen_weibo.favnum');
				$pctmp = $this->db->get()->result_array();
				 if(!empty($pctmp) and !empty($pctmp)){
                $pictmp = unserialize($pctmp[0]['type_data']);
                      $this->db->where('id', $pictmp[1]['id']);
				      $this->db->from('wen_attach');
				      $this->db->select('savepath');
				      $ptmp = $this->db->get()->result_array();
				         $data['extras']['haspic']="1";
				      	 $data['extras']['url'] =  'http://pic.meilimei.com.cn/upload/'.$ptmp['0']['savepath'];
				      }
                    }elseif($data['minfo'][0]->type ==8){
                      if(isset($type_data['pic'])){
				          $data['extras']['haspic']="1";
				      	  $data['extras']['url'] =  'http://pic.meilimei.com.cn/upload/'.$type_data['pic']['savepath'];
				      }
                    }elseif($data['minfo'][0]->type ==16){
                      if(isset($type_data['pic'])){
				          $data['extras']['haspic']="2";
				          $this->db->select('savepath,height,width,info');
		                  $this->db->where('attachId', $param);
		                   $this->db->from('topic_pics');
		                   $data['extras']['url'] =$this->db->get()->result_array();


				      }
                    }
		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/topic/detail/' . ($start -1)) : site_url('manage/topic/detail');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/detail/' . ($start +1)) : '';
        $data['notlogin'] = $this->notlogin;
        $data['tid'] = $param;
		$data['message_element'] = "topic_detail";
		$this->load->view('manage', $data);
    }
   //set topic param
   public function setting(){
   	  if($this->input->post()){
         $this->db->query("UPDATE settings SET int_value = {$this->input->post('addtopic')} WHERE code = 'WEIBO_JIFEN' limit 1 ");
         $this->db->query("UPDATE settings SET int_value = {$this->input->post('rtopic')} WHERE code = 'WEIBO_RJIFEN' limit 1 ");
   	     $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '修改成功！'));
         redirect('manage/topic/setting');
   	  }
   	  $data['add'] = $this->db->get_where('settings', array (
			'code' => 'WEIBO_JIFEN'
		))->row()->int_value;
	  $data['reply'] = $this->db->get_where('settings', array (
			'code' => 'WEIBO_RJIFEN'
		))->row()->int_value;
      $data['notlogin'] = $this->notlogin;
      $data['message_element'] = "topic_setting";
	  $this->load->view('manage', $data);
   }
   public function edit($param=''){
   	    if($this->input->post('title')){
            $udata = array();
            $tmp = unserialize($this->input->post('sourceinfo'));
            $tmp['title'] = trim($this->input->post('title'));
            if($this->input->post('mainpc')){
            	$tmp['pic']['savepath'] = trim($this->input->post('mainpc'));
            	$ptmp = $this->remote->info($tmp['pic']['savepath']);

                $tmp['pic']['width'] =  $ptmp[0];
			    $tmp['pic']['height'] =  $ptmp[1];
            }
            $udata['tags'] = trim($this->input->post('tags'));
            $udata['weight'] = intval($this->input->post('weight'));
            $udata['type_data'] = serialize($tmp);
            $udata['content'] = $this->input->post('content');

             $this->db->where('weibo_id', $param);
             $this->db->limit(1);
            $this->db->update('wen_weibo', $udata);
            redirect($this->session->userdata('history_url'));
   	    }
		$data['results'] = $this->db->query("SELECT * FROM wen_weibo WHERE 	weibo_id = {$param}  ORDER BY weibo_id DESC  LIMIT 1")->result();      //评论结果
        $data['pictures'] = $this->db->query("SELECT * FROM topic_pics WHERE attachId = {$param}  ORDER BY attachId DESC ")->result();
        $this->track->topic($param,$this->uid,1);
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "topic_edit";
		$this->load->view('manage', $data);
    }
    public function comment($param=''){
    	if($this->input->post('submit') and   $data['fuid'] = intval($this->input->post('fuid'))){
    		$data['contentid'] = intval($param);

    		$data['comment'] = $this->input->post('comment');
    		$data['alias'] = $this->input->post('alias');
    		$data['type'] = 'topic';
    		$data['cTime'] = time();
            $data['touid'] = $this->input->post('touid')?$this->input->post('touid'):$this->uid;
    		$data['status'] = 1;
    		$cid = $this->common->insertData('wen_comment',$data);

            $updata = array();
            $updata['newtime'] = time();
            $this->db->query("UPDATE  `wen_weibo` SET  `comments` = `comments`+1,`newtime` =  '{$updata['newtime']}',`commentnums` =  `commentnums`+1,`commentnums` =  `commentnums`+1 WHERE `weibo_id` = {$param} limit 1");
            $this->track->reply($cid,$this->uid);
    		redirect('manage/topic/detail/'.$param);
    	}else{
    		redirect();
    	}
    }
    public function total($param=''){
    	 $this->db->where('role_id', 16);
        $data['uers'] = $this->db->get('users')->result_array();
        $data['res'] = $t = array();
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
        foreach( $data['uers'] as $r){
           $t['name'] = $r['alias'];
           $t['topic'] = $this->track->total($r['id'],3,'topic',$cdate,$edate);
           $t['reply'] = $this->track->total($r['id'],3,'reply',$cdate,$edate);

           $data['res'][] = $t;
        }
        $data['ftags'] = $this->track->mtagSum($cdate,$edate);

        $data['tags'] = $this->track->tagSum(0,3, $cdate,$edate);
        for(;$cdate < $edate;$cdate+=86400){
        	 $tmp['date'] = date('d',$cdate);
             $tmp['num'] = $this->track->topicSum($cdate,$cdate+86400);
             $data['mtags'][] = $tmp;
        }
    	$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_total";
        $this->load->view('manage', $data);
    }
	public function cdel($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
            $updateData=array('is_delete'=>1);
            $this->common->updateTableData('wen_comment',intval($param),'',$updateData);
            redirect('manage/topic/comments');
		}
	}
	//pass check topic
	public function passcheck($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
            $data = array(
               'isdel' => 0
            );
            $this->db->where('weibo_id', intval($param));
            $this->db->update('wen_weibo', $data);
		}
	}
	public function del($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
            $condition=array('contentid'=>intval($param),'type'=>'topic');
            $this->common->deleteTableData('wen_comment',$condition);
            $this->db->where('attachId', intval($param));
            $tmp = $this->db->get('topic_pics')->result_array();

            foreach($tmp as $r){
            	if(!$this->remote->del($r['savepath'],true)){
            		die('file delete error!');
            	};
            }
            $condition=array('weibo_id'=>intval($param));
            $this->common->deleteTableData('wen_weibo',$condition);
            $condition=array('attachId'=>intval($param));
            $this->common->deleteTableData('topic_pics',$condition);
            redirect($this->session->userdata('history_url'));
		}
	}
	public function commentdel($param=''){
		if($param && $this->wen_auth->get_role_id()==16){
            $contentId = $this->db->query("select contentid from wen_comment where id=".intval($param))->row_array();
            $condition=array('id'=>intval($param),'type'=>'topic');
            $this->common->deleteTableData('wen_comment',$condition);
            redirect('manage/topic/detail/'.$contentId['contentid']);
		}
	}
	//user banned or open
	public function userbanned() {
		if (($uid = $this->input->get('uid'))) {
			$ban = intval($this->input->get('banned'));
			$data['banned'] = $ban;
			$this->db->where('id', $uid);
			$this->db->limit(1);
		    $this->db->update('users', $data);

		}
	}
    public function Suser(){
    	if($t = trim($this->input->get('term'))){
    		$tmp = $this->db->query("select id,alias as value,phone from users where (alias like '%{$t}%' OR phone like '%{$t}%') AND role_id=1 LIMIT 9")->result_array();
        $res  = array();
        foreach($tmp as $r){
        	$r['value'] = trim($r['value']);
        	if($r['value']==''){
             $r['value'] = $r['phone'];
        	}
        	$r['label'] = $r['value'] ;
            $res[] = $r;
        }
        echo json_encode($res);
    	}

    }

    //add topic
    public function add(){

        if($this->input->post()){

            $info = array ();
            //$info['address'] = '';
            $info['title'] = $this->input->post('title');    //问题描述
            //$info['sex'] = $this->input->post('sex');

            if($_FILES['picture']['tmp_name'][0]){ //上传图片
                   $tmpdir = date('Y').'/'.date('m').'/'.date('d').'/';;
                     $addpl = array();
                     $ext = '.jpg';
                          $datas['name'] = uniqid(time().rand(1000,9999), false)  . $ext;
					      $datas['savepath'] = $tmpdir . $datas['name'];
					      $ptmp = getimagesize($_FILES['picture']['tmp_name'][0]);

					      if(!$this->remote->cp($_FILES['picture']['tmp_name'][0],$datas['name'],$datas['savepath'],array('width'=>600,'height'=>800),true)){
					      	  $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                               redirect('manage/topic/add');
                               exit;
					      }
					      $info['pic']['type'] = substr($ext,1);
					      $info['pic']['savepath'] = $datas['savepath'];
					      //insert to db
					      $addpl['name'] = $datas['name'];
						  $addpl['savepath'] = $datas['savepath'];
						  $addpl['userId'] = $this->uid;
                          $picinfo = $this->input->post('picture_info');
                          $addpl['info'] = strip_tags($picinfo[0]);
						$info['pic']['width'] = $addpl['width'] = $ptmp[0];
						$info['pic']['height'] = $addpl['height'] = $ptmp[1];
						$addpl['cTime'] = time();
						$addpl['type'] = 'jpg';
						$addpl['privacy'] = 0;

            }
            $datas = array();
            $info['toUid'] = 0;
            $datas['type'] = 16;
            $datas['q_id'] = 0;
            if($this->input->post('suser_id')){
                 $datas['uid'] = $this->input->post('suser_id');
            }else{
            	$datas['uid'] = $this->uid;
            }

            if($ttag = $this->input->post('positions')){
            	$datas['tags'] = ',';
            	foreach($ttag as $r){
                    $datas['tags'].=$r.',';
            	}
            }
            $datas['weight'] = 1;
            $datas['wsource'] = 'windows';
            if($this->input->post('ctime')){
                $datas['newtime'] = $datas['ctime'] = strtotime($this->input->post('ctime').' '.$this->input->post('extrat').':'.$this->input->post('extrat1'));
            }else{
            	$datas['newtime'] = $datas['ctime'] = time();
            }

            $datas['type_data'] = serialize($info);
            $datas['content'] = $this->input->post('description'); //问题描述
            $datas['views'] = $this->input->post('views');
            $weibo_id = $this->common->insertData('wen_weibo', $datas);

              if($_FILES['picture']['tmp_name'][0]){ //上传图片
					$tmpdir = date('Y').'/'.date('m').'/'.date('d').'/';
					$i=0;
					foreach($_FILES['picture']['tmp_name'] as $key=>$r){
                       if($i==0){
                          $addpl['attachId'] = $weibo_id;
                          $this->common->insertData('topic_pics', $addpl);
                       }elseif($r){
                       	$addpl = array();
                       	$addpl['attachId'] = $weibo_id;
                           //insert to db
                        $ext = '.jpg';
					    $addpl['name'] = uniqid(time().rand(1000,99999), false) . '.jpg';
						$addpl['savepath'] = $tmpdir . $addpl['name'];
						$ptmp = getimagesize($r);
						if($this->remote->cp($r,$addpl['name'],$addpl['savepath'],array('width'=>600,'height'=>800),true)){
							$addpl['userId'] = $this->uid;
						$addpl['width'] = $ptmp[0];
						$addpl['height'] = $ptmp[1];
						$addpl['cTime'] = time();
						$addpl['type'] = 'jpg';
						$addpl['privacy'] = 0;
						$addpl['info'] = strip_tags($picinfo[$key]);
						$this->common->insertData('topic_pics', $addpl);
						}
                       }

                       $i++;
					}

            }
            //track user action
            if(isset($ttag)){
            	foreach($ttag as $r){
            		 strlen($r)>1&&$this->track->tags($r,$this->uid);
            	}
            }
            $this->track->topic($weibo_id,$this->uid);
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '添加成功！'));
            redirect('manage/topic/add');
        }
        $result = $this->db->query("select * from wen_weibo where type=1 and uid=".$this->session->userdata('WEN_user_id')." order by ctime desc")->result_array();
        $data['result'] = $result;
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_add";
        $this->load->view('manage', $data);
    }
}
?>
