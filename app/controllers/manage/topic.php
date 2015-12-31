<?php

class topic extends CI_Controller {

    private $notlogin = true, $uid = '',$imgurl = "http://pic.meilimei.com.cn/upload/",$qiniuimgurl="http://7xkdi8.com1.z0.glb.clouddn.com/";

    public function __construct() {
        parent :: __construct();
        $this->eventDB = $this->load->database('event1', TRUE);
        if ($this->wen_auth->get_role_id() == 16) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            redirect('');
        }
        $this->load->model('privilege');
        $this->load->model('track');
        $this->load->model('remote');
        $this->load->model('Diary_model');
        $this->privilege->init($this->uid);

        if (!$this->privilege->judge('topic')) {
            die('Not Allow');
        }
    }
    


//topic list
    public function index($page = '') {

        $page = $this->input->get('page');
        $condition = '  WHERE  type &25 and isdel = 0 ';
        $data['issubmit'] = false;
        $fix = '';
        $this->load->library('pager');
        if ($this->input->get('submit')) {
            $data['issubmit'] = true;
            if ($this->input->get('phone')) {
                $condition .= " AND (users.phone = '" . mysql_real_escape_string($this->input->get('phone')) . "' OR users.alias = '" . mysql_real_escape_string($this->input->get('phone')) . "')";
                $fix.="phone={$this->input->get('phone')}&";
            }
            if ($sname = mysql_real_escape_string($this->input->get('sname'))) {
                $condition .= "  AND (content like '%" . trim($sname) . "%' or type_data like '%" . trim($sname) . "%')";
                $fix.="sname=".urlencode($this->input->get('sname'))."&";
            }
            if($this->input->get('types') == 1){
                $condition .= "  AND (wsource not like '%windows%')";
                $fix.='types=' . $this->input->get('types') . '&';
            }else{
                $condition .= "  AND (wsource like '%" . trim($this->input->get('types')) . "%')";
                $fix.='types=' . $this->input->get('types') . '&';
            }
            if($this->input->get('top') == 1){
            	$condition .= "  AND top = 1 ";
                $fix.='top=' . $this->input->get('top') . '&';
            }

            if($this->input->get('tags')){
                $condition .= "  AND (tags like '%".$this->input->get('tags')."%')";
                $fix.='tags=' . $this->input->get('tags') . '&';
            }

            if ($this->input->get('city')) {
                $condition .= "  AND user_profile.city like '%" . trim($this->input->get('city')) . "%'";
                $fix.='city=' . $this->input->get('city') . '&';
            }
            $fix.='submit=true';
        }
        $data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo LEFT JOIN users ON users.id = wen_weibo.uid {$condition} ")->num_rows();

        $per_page = 16;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start - 1) * $per_page;
        else
            $offset = $start * $per_page;
        $fields = "w.weibo_id,w.tags,w.wsource,w.comments,w.q_id,w.content,w.uid,w.ctime,w.type_data,users.alias,users.phone,users.email,users.state,w.pageview";
        $data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} group by w.weibo_id ORDER BY  w.weibo_id DESC  LIMIT $offset , $per_page")->result();
        foreach ($data['results'] as &$v){
        	if(!empty($v->imgfile)){
        		$v->imgurl = $this->qiniuimgurl.$v->imgfile;
        	}else{
        		$v->imgurl = $this->imgurl.$v->imgurl;
        	}
        }
        $data['offset'] = $offset + 1;
        //$data['preview'] = $start > 2 ? site_url('manage/topic/index/' . ($start -1)).$fix : site_url('manage/topic/index').$fix;
        //$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/index/' . ($start +1)).$fix : '';
        $data['tags'] = $this->db->query('select name from items')->result_array();
        $config = array(
            "record_count" => $data['total_rows'],
            "pager_size" => $per_page,
            "show_jump" => true,
            'querystring_name' => $fix . '&page',
            'base_url' => 'manage/topic/index',
            "pager_index" => $page
            );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic";
        $this->session->set_userdata('history_url', 'manage/topic?page=' . ($start - 1));
        $this->load->view('manage', $data);
    }

    //wait check
    public function cktopic() {
        $this->load->library('pager');
        $data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo WHERE isdel = 1 ")->num_rows();
        $fix = '';
        $per_page = 10;
        $page = intval($this->input->get('page'));
        //  $query = $this->db->get('mytable');
        $config = array(
            "record_count" => $data['total_rows'],
            "pager_size" => $per_page,
            "show_jump" => true,
            'querystring_name' => $fix . '&page',
            'base_url' => 'manage/topic/index',
            "pager_index" => $page
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
    public function comments($page = '') {
        $per_page = 26;
        $start = intval($page);
        $start == 0 && $start = 1;
        if ($start > 0)
            $offset = ($start - 1) * $per_page;
        else
            $offset = $start * $per_page;
        $condition = "  wen_comment.is_delete=0 and wen_comment.type='topic'";
        $fix = '?';

        if($this->input->get('types') == 1){
            $condition .= "  AND (wen_comment.device not like '%windows%')";
            $fix.='types=' . $this->input->get('types') . '&';
        }else{
            $condition .= "  AND (wen_comment.device like '%" . trim($this->input->get('types')) . "%')";
            $fix.='types=' . $this->input->get('types') . '&';
        }

        if ($this->input->get('kw')) {
            $condition .= "  AND (wen_comment.comment like '%" . trim($this->input->get('kw')) . "%')";
            $fix.='kw=' . $this->input->get('kw') . '&';
        }
        if ($this->input->get('uid')) {
            $condition .= "  AND ((users.username = {$this->input->get('uid')}) or (users.phone = {$this->input->get('uid')}))";
            $fix.='uid=' . $this->input->get('uid') . '&';
        }
        $data['offset'] = $offset + 1;
        //添加了登陆操作者用户名
        $fields = "users.banned,wen_comment.fuid,wen_comment.data,wen_comment.contentid,wen_comment.comment,wen_comment.id,wen_comment.cTime,wen_comment.loginuser,wen_comment.device,users.phone,wen_weibo.type_data,wen_weibo.uid";
        $data['results'] = $this->db->query("SELECT {$fields} FROM  wen_comment LEFT JOIN users ON users.id=wen_comment.fuid LEFT JOIN wen_weibo ON wen_weibo.weibo_id=wen_comment.contentid Where {$condition} ORDER BY  id DESC  LIMIT $offset , $per_page")->result();

        if(!empty($data['results'])){
            foreach($data['results'] as $key=> $r) {
                $tmp = unserialize($r->data);
                if (isset($tmp[0]['path']) && !empty($tmp[0]['path'])) {
                    $r->picture = $this->remote->show320($tmp[0]['path']);

                }

                $data['results'][$key] = $r;
                unset($r);
            }
        }

        $data['total_rows'] =  $this->db->query("SELECT {$fields} FROM  wen_comment LEFT JOIN users ON users.id=wen_comment.fuid LEFT JOIN wen_weibo ON wen_weibo.weibo_id=wen_comment.contentid Where {$condition} ORDER BY  id DESC")->num_rows();
        $data['preview'] = $start > 2 ? site_url('manage/topic/comments/' . ($start - 1)) . $fix : site_url('manage/topic/comments') . $fix;
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/comments/' . ($start + 1)) . $fix : '';
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_comments";
        $in = '';
        if(!empty($data['results'])){

            foreach($data['results'] as $item){
                if($item->uid){
                    if(empty($in)){
                        $in = $item->uid;
                    }else{
                        $in .= ','.$item->uid;
                    }
                }
            }
        }

        if(!empty($in)){

            $resUser = $this->db->query('SELECT users.id, users.phone FROM users where users.id in ('.$in.')')->result();

            foreach($data['results'] as $item){
                foreach($resUser as $uitem){
                    if($item->uid == $uitem->id){
                        $item->mPhone = $uitem->phone;
                    }
                }

            }
        }

        //echo "<pre>";
        //print_r($data['results']);
        $this->load->view('manage', $data);
    }

    //convert to question
    public function toquestion($param = '') {
        $this->db->where('weibo_id', $param);
        $res = $this->db->get('wen_weibo')->result_array();
        $ipcs = array();
        $tmps = unserialize($res[0]['type_data']);
        $data['title'] = $tmps[0]['title'];
        $data['position'] = '';
        $data['description'] = $res[0]['content'];
        $data['extradata'] = serialize($ipcs);
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
    public function nocla($page = '') {
        $condition = "  WHERE type &25 and tags=''";
        $data['total_rows'] = $this->db->query("SELECT weibo_id FROM wen_weibo {$condition} ")->num_rows();

        $per_page = $data['issubmit'] ? 25 : 16;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start - 1) * $per_page;
        else
            $offset = $start * $per_page;
        $fields = "w.wsource,w.weibo_id,w.content,w.uid,w.ctime,w.type_data,users.alias,users.phone,users.email";
        $data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} ORDER BY  w.weight DESC  LIMIT $offset , $per_page")->result();

        $data['offset'] = $offset + 1;
        $data['preview'] = $start > 2 ? site_url('manage/topic/order/' . ($start - 1)) : site_url('manage/topic/order');
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/order/' . ($start + 1)) : '';
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_nocla";
        $this->load->view('manage', $data);
    }

    // show with order
    public function order($page = '') {
        $condition = '  WHERE type &25';
        $data['issubmit'] = false;
        if ($this->input->post('submit')) {
            $data['issubmit'] = true;
            if ($this->input->post('phone')) {
                $condition .= " AND users.phone = '" . $this->input->get('phone') . "'";
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
            $offset = ($start - 1) * $per_page;
        else
            $offset = $start * $per_page;
        $fields = "w.wsource,w.weibo_id,w.content,w.uid,w.ctime,w.type_data,w.weight,users.alias,users.phone,users.email";
        $data['results'] = $this->db->query("SELECT {$fields} FROM  wen_weibo as w LEFT JOIN users ON users.id = w.uid  {$condition} ORDER BY  w.weight DESC  LIMIT $offset , $per_page")->result();

        $data['offset'] = $offset + 1;
        $data['preview'] = $start > 2 ? site_url('manage/topic/order/' . ($start - 1)) : site_url('manage/topic/order');
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/topic/order/' . ($start + 1)) : '';
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_order";
        $this->load->view('manage', $data);
    }

    /** 详细页
     * @param string $param
     * @param string $page
     */
    public function detail($param = '', $page = '') {
        $condition = '  WHERE type & 25 ';
        $data['issubmit'] = false;

        $sql = '';
        if($this->input->get('wsource')){
            $sql .= " and device != 'windows'";
        }
        $data['total_rows'] = $this->db->query("SELECT id FROM wen_comment WHERE contentid = {$param} $sql AND type='topic' ")->num_rows(); //评论总数

        $per_page = $data['issubmit'] ? 25 : 16;      //16
        $start = intval($page);      //开始页
        $start == 0 && $start = 1;   //如果开始页等于0，令其等于1

        if ($start > 0)
            $offset = ($start - 1) * $per_page; //移位
        else
            $offset = $start * $per_page;   //0
        $data['results'] = $this->db->query("SELECT id,comment,fuid, touid,alias,wen_comment.data FROM wen_comment WHERE contentid = {$param} AND  pid = 0 AND type='topic' $sql AND  is_delete=0  ORDER BY id DESC  LIMIT $offset , $per_page")->result();      //评论结果
        //error_reporting(E_ALL);
        foreach ($data['results'] as $k => $v) {
            $row = $this->db->query("select username,alias,phone,role_id from users where id = " . $v->fuid)->row_array();
            if (is_array($row) && count($row) > 0) {
                $data['results'][$k]->phone = $row['phone'];     //电话
                $data['results'][$k]->role_id = $row['role_id'];  //角色

                $child = $this->db->query("SELECT id,comment,fuid, touid,alias,wen_comment.data FROM wen_comment WHERE pid=$v->id AND type='topic' $sql AND  is_delete=0  ORDER BY id DESC")->result();
                if(!empty($child)){
                    $data['results'][$k]->child = $child;
                }else{
                    $data['results'][$k]->child = array();
                }
            } else {
                $data['results'][$k]->phone = '虚拟用户';     //电话
                $data['results'][$k]->role_id = '虚拟用户';  //角色
            }
        }
        //get doctor comments
        $data['doctor_comments'] = $this->db->query("SELECT wen_comment.id,wen_comment.comment,wen_comment.fuid,users.alias FROM wen_comment left join users ON users.id = wen_comment.fuid WHERE wen_comment.contentid = {$param} AND wen_comment.type='ans' ORDER BY wen_comment.id DESC ")->result();

        $data['minfo'] = $this->db->query("SELECT weibo_id,content,type_data,q_id,uid,type,ctime FROM wen_weibo WHERE weibo_id = {$param} ORDER BY weibo_id DESC  LIMIT 1")->result();  //话题内容
        $type_data = unserialize($data['minfo'][0]->type_data);

        $data['extras']['haspic'] = 0;
        if ($data['minfo'][0]->type == 1) {
            $this->db->where('wen_weibo.q_id', $data['minfo'][0]->q_id);
            $this->db->where('wen_weibo.type', 4);
            $this->db->from('wen_weibo');
            $this->db->select('wen_weibo.type_data,wen_weibo.favnum');
            $pctmp = $this->db->get()->result_array();

            if (!empty($pctmp) and ! empty($pctmp)) {
                $pictmp = unserialize($pctmp[0]['type_data']);

                $this->db->where('id', $pictmp[1]['id']);
                $this->db->from('wen_attach');
                $this->db->select('savepath');
                $ptmp = $this->db->get()->result_array();
                $data['extras']['haspic'] = "1";
            	if($type_data['pic']['imgfile']){
                	$data['extras']['url'] = 'http://7xkdi8.com1.z0.glb.clouddn.com/' . $type_data['pic']['savepath'];
                }else{
                	$data['extras']['url'] = 'http://pic.meilimei.com.cn/upload/' . $type_data['pic']['savepath'];
                }
            }
        } elseif ($data['minfo'][0]->type == 8) {
            if (isset($type_data['pic'])) {
                $data['extras']['haspic'] = "1";
                $data['extras']['url'] = 'http://pic.meilimei.com.cn/upload/' . $type_data['pic']['savepath'];
                if($type_data['pic']['imgfile']){
                	$data['extras']['url'] = 'http://7xkdi8.com1.z0.glb.clouddn.com/' . $type_data['pic']['savepath'];
                }else{
                	$data['extras']['url'] = 'http://pic.meilimei.com.cn/upload/' . $type_data['pic']['savepath'];
                }
            }
        } elseif ($data['minfo'][0]->type == 16) {
            if (isset($type_data['pic'])) {
                $data['extras']['haspic'] = "2";
                $this->db->select('savepath,height,width,info,imgfile');
                $this->db->where('attachId', $param);
                $this->db->from('topic_pics');
                $data['extras']['url'] = $this->db->get()->result_array();
				foreach ($data['extras']['url'] as &$v){
					if(!empty($v->imgfile)){
						$v->savepath = $this->qiniuimgurl.$v->imgfile;
					}else{
						$v->savepath = $this->imgurl.$v->imgurl;
					}
				}
					
            }
        }
        $this->db->select('uid');
        $this->db->where('uid',$data['minfo'][0]->uid);
        $zanUserIds = $this->db->get('wen_zan')->result_array();

        $zanUserList = array();
        if(!empty($zanUserIds)){
            foreach($zanUserIds as $item){
                $this->db->select('username');
                $this->db->where('id',$item['uid']);
                $rsuser = $this->db->get('users')->result_array();
                $zanUserList[] = $rsuser[0]['username'];
            }
        }
        $data['pictures'] = $this->db->query("SELECT * FROM topic_pics WHERE attachId = {$param}  ORDER BY attachId DESC ")->result();
        foreach ($data['pictures'] as &$v){
        	if(!empty($v->imgfile)){
        		$v->savepath = $this->qiniuimgurl.$v->imgfile;
        	}else{
        		$v->savepath = $this->imgurl.$v->savepath;
        	}
        }
        $data['zanUserList'] = $zanUserList;
        $page < 1 && $page = 1;
        $data['preview'] = site_url('manage/topic/detail/' . $param . '/' . ($page - 1));
        $data['next'] = site_url('manage/topic/detail/' . $param . '/' . ($page + 1));
        $data['offset'] = $offset + 1;

        $data['notlogin'] = $this->notlogin;
        $data['tid'] = $param;
        $data['message_element'] = "topic_detail";
        $this->load->view('manage', $data);
    }


    //set topic param
    public function setting() {
        if ($this->input->post()) {
            $this->db->query("UPDATE settings SET int_value = {$this->input->post('addtopic')} WHERE code = 'WEIBO_JIFEN' limit 1 ");
            $this->db->query("UPDATE settings SET int_value = {$this->input->post('rtopic')} WHERE code = 'WEIBO_RJIFEN' limit 1 ");
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '修改成功！'));
            redirect('manage/topic/setting');
        }
        $data['add'] = $this->db->get_where('settings', array(
            'code' => 'WEIBO_JIFEN'
            ))->row()->int_value;
        $data['reply'] = $this->db->get_where('settings', array(
            'code' => 'WEIBO_RJIFEN'
            ))->row()->int_value;
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_setting";
        $this->load->view('manage', $data);
    }

    public function edit($param = '') {
        if ($this->input->post('title')) {
            if ($this->input->post('deleteinfo')) {
                $this->del($param);
                redirect($this->session->userdata('history_url'));
                exit;
            }

            $udata = array();
            $tmp = unserialize($this->input->post('sourceinfo'));
            $tmp['title'] = trim($this->input->post('title'));
            if ($this->input->post('mainpc')) {
                $tmp['pic']['savepath'] = trim($this->input->post('mainpc'));
                $ptmp = $this->remote->info($tmp['pic']['savepath']);

                $tmp['pic']['width'] = $ptmp[0];
                $tmp['pic']['height'] = $ptmp[1];
            }
            if ($this->input->post('passSend')) {
                $udata['isdel'] = 0;
                // $udata['cTime'] = time();
            }
            $udata['hots'] = intval($this->input->post('hots'));
            $udata['tehui_ids'] = $this->input->post('tehui_ids');
            $udata['fs_ids'] = $this->input->post('fs_ids');
            $udata['tags'] = trim($this->input->post('tags'));
            if(intval($this->input->post('views')) > 0){
                $udata['views'] = intval($this->input->post('views'));
            }else{
                $udata['views'] = rand(30000,50000);
            }

            $udata['weight'] = intval($this->input->post('weight'));
            $udata['type_data'] = serialize($tmp);
            $udata['content'] = $this->input->post('content');
            $udata['chosen'] = $this->input->post('chosen');
            if($udata['chosen']){
                $udata['chosentime'] = intval($this->input->post('days')) * 86400 + time();
                $udata['days'] = intval($this->input->post('days'));
            }
                        //piazza 
            $udata['piazza'] = $this->input->post('piazza');
            if($udata['piazza']){
                $udata['piazzatime'] = intval($this->input->post('piazza_days')) * 86400 + time();
                $udata['piazza_days'] = intval($this->input->post('piazza_days'));
            }
            $udata['top'] = $this->input->post('top');
            if($udata['top']){
                $udata['toptime'] = intval($this->input->post('top_days')) * 86400 + time();
                $udata['top_days'] = intval($this->input->post('top_days'));
            }
            $udata['hot'] = $this->input->post('hot');
            $udata['hot_start'] = strtotime($this->input->post('hot_start'));
            $udata['hot_end'] = strtotime($this->input->post('hot_end'));
            if($udata['hot']){
                $udata['hottime'] = intval($this->input->post('hot_days')) * 86400 + time();
                $udata['hot_days'] = intval($this->input->post('hot_days'));
            }
            $udata['top_start'] = strtotime($this->input->post('top_start'));
            $udata['top_end'] = strtotime($this->input->post('top_end'));
            $udata['chosen_start'] = strtotime($this->input->post('chosen_start'));
            $udata['chosen_end'] = strtotime($this->input->post('chosen_end'));
            $udata['piazza_start'] = strtotime($this->input->post('piazza_start').' '.$this->input->post('piazza_hour'));
            $udata['piazza_end'] = strtotime($this->input->post('piazza_end'));
            if(intval($udata['piazza_start']) >0) {
                $udata['newtime'] = $udata['nstime'] = $udata['piazza_start'];
            }else{
                $udata['newtime'] = $udata['nstime']  = time();
            }

            $udata['tehui_ids'] = $this->input->post('tehui_ids');
            $udata['fs_ids'] = $this->input->post('fs_ids');
            $udata['extra_ids'] = $this->input->post('extra_ids');
            $res = $this->db->get_where('wen_weibo', array('weibo_id' => $param))->result_array();
            $udata['vaoc'] = $res[0]['ctime'] + $udata['weight'];
            ///小组置顶推荐
            $udata['group_start'] = strtotime($this->input->post('group_start'));
            $udata['group_end'] = strtotime($this->input->post('group_end'));
            $udata['grouptop'] = $this->input->post('grouptop');
            if($udata['grouptop']){
                $udata['grouptime'] = intval($this->input->post('group_days')) * 86400 + time();
                $udata['group_days'] = intval($this->input->post('group_days'));
            }
            // 设置大图
            if ($_FILES['frontpic']['tmp_name']) {
                $upruduct = array();
                $upruduct['name'] = uniqid() . rand(1000, 99999) . '.jpg';
                $upruduct['savepath'] = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                $upload_rs = $this->remote->upload_qiniu($_FILES['frontpic']['tmp_name'], $upruduct['name']);

                if (!empty($upload_rs['key'])) {
                    $udata['front_q_pic'] = $upload_rs['key'];
                }

            }
            
            $udata['type_pic'] = $this->input->post('typepic');
            $udata['front_title'] = $this->input->post('frontdesc');

            $this->db->where('weibo_id', $param);
            $this->db->limit(1);
            $this->db->update('wen_weibo', $udata);

            $picorder = $this->input->post('picorder');
            foreach ($picorder as $k => $v) {
                $this->db->where('id', $k);
                $this->db->limit(1);
                $this->db->update('topic_pics', array('order' => $v));
            }
            
            $picinfo = $this->input->post('picinfo');
            foreach ($picinfo as $k => $v) {
                $this->db->where('id', $k);
                $this->db->limit(1);
                $this->db->update('topic_pics', array('info' => $v));
            }
            
            $savepath = $this->input->post('savepath');
            foreach ($savepath as $k => $v) {
                $this->db->where('id', $k);
                $this->db->limit(1);
                $this->db->update('topic_pics', array('savepath' => $v));
            }

            redirect($this->session->userdata('history_url'));
        }
        $data['results'] = $this->db->query("SELECT * FROM wen_weibo WHERE 	weibo_id = {$param}  ORDER BY weibo_id DESC  LIMIT 1")->result();      //评论结果

        $data['pictures'] = $this->db->query("SELECT * FROM topic_pics WHERE attachId = {$param}  ORDER BY attachId DESC ")->result();
        $this->track->topic($param, $this->uid, 1);
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_edit";
        $this->load->view('manage', $data);
    }
    //add topic picture point data
  /*  public function addpoint($param = ''){
    	$this->db->where('id', intval($param));
    	$tmp = $this->db->get('topic_pics')->result_array();
    	if(!empty($tmp)){
    		$data['res'] = $tmp[0];
    	}
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_pic_addpoint";
        $this->load->view('manage', $data);
    }*/
    private function getMyZanTopic($tid, $offset=0, $limit=10){
        if(intval($tid) < 0){
            return;
        }

        $this->db->select('content, uid, type_data');
        $this->db->where('weibo_id', $tid);
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_weibo')->result_array();
    }

    private function Zan($contentid, $uid){

        $rs = $this->getMyZanTopic($contentid);
        $touid = 0;
        if(!empty($rs)){
            $touid = $rs[0]['uid'];
        }else{
            $rs = array();
            $rs = $this->getDiaryUser($contentid);
            $touid = $rs[0]['uid'];
        }
        $data = array (
            'type' => 'topic',
            'contentid' => $contentid,
            'uid' => $uid,
            'touid' => $touid,
            'cTime' => time());
        $this->db->insert('wen_zan', $data);
        $where_condition = array (
            'type' => 'topic',
            'contentid' => $contentid);

        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;
        return $num;
    }

    public function addZan($param = '') {

        $contentid =intval($param);
        $uid = $this->input->post('suid');

        if ($uid) {

            $rs =  $this->Diary_model->getMyZanTopic($contentid);

            $this->db->where('touid', $rs[0]['uid']);
            $this->db->where('type','topic');
            $num = $this->db->get('wen_zan')->num_rows();

            $isZan = $this->Diary_model->isZan($uid, $contentid);

            if(!$isZan) {
                $result['data']['zan'] = $this->Zan($contentid, $uid);
            }else{
                $result['data']['zan'] = $this->Diary_model->getZanNum($contentid,'topic');
            }

            redirect('manage/topic/detail/'.$param);


        } else {
            redirect();
        }
    }

    public function comment($param = '') {

        if ($this->input->post('submit') and $data['fuid'] = intval($this->input->post('fuid'))) {
            $data['contentid'] = intval($param);

            $contentid = $data['contentid'];

            if ($commentTo = intval($this->input->post('commentTo'))) {
                $PCID = $this->GPCID($commentTo);
                if (!$PCID) {
                    //$data['pcid'] = 0;
                    $data['pid'] = 0;
                } else {
                    //$data['pcid'] = $PCID;
                    $data['pid'] = $PCID;
                }
            }

            if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {

                $Idata = array();

                $datas['name'] = uniqid(time(), false) . '.jpg';
                $picturesave = date('Y').'/' . date('m').'/' . date('d').'/' . $datas['name'];;
                $tmpinfo = getimagesize($_FILES['attachPic']['tmp_name']);
                if(!$this->remote->cp($_FILES['attachPic']['tmp_name'],$datas['name'],$picturesave,array('width'=>500,'height'=>500),true)){
                 $result['state'] = '001';
                 $result['notice'] = '图片上传失败！';
                 mail('muzhuquan@126.com','debug',serialize($datas));
                 echo json_encode($result);
                 exit;
             }
             $result['updatePictureState'] = '000';
             $upicArr[0]['type'] = 'jpg';
             $upicArr[0]['height'] = $tmpinfo[1];
             $upicArr[0]['width'] = $tmpinfo[0];
             $upicArr[0]['path'] = $picturesave;
             $upicArr[0]['uploadTime'] = time();
             $data['data'] = serialize($upicArr);

         }
         
         if($contentid){
         	$this->db->where('contentid',$contentid);
         	$this->db->select('cTime');
         	$this->db->order_by('cTime','DESC');
         	$this->db->from('wen_comment');
         	$topic_com_rs = $this->db->get()->row_array();
         }
         
         
        
         
         
         $last_send_time = $topic_com_rs['cTime'];
         $now_time = time();
         
         $data['comment'] = $this->input->post('comment');
         $data['alias'] = $this->input->post('alias');
         $data['type'] = $this->input->post('commenttype');
         $data['cTime'] = time();
         $data['touid'] = $this->input->post('touid') ? $this->input->post('touid') : $this->uid;
            $data['loginuid'] = $this->uid;  //登陆用户id
            $data['loginuser'] = $this->wen_auth->get_username(); //登陆用户名
            $data['status'] = 1;
            $cid = $this->common->insertData('wen_comment', $data);
             

            $updata = array();
            $updata['newtime'] = time();
            $this->db->query("UPDATE  `wen_weibo` SET  `comments` = `comments`+1,`newtime` =  '{$updata['newtime']}',`commentnums` =  `commentnums`+1,`commentnums` =  `commentnums`+1 WHERE `weibo_id` = {$param} limit 1");


            if($this->input->post('push')) {
                $judge = $this->db->query("select uid from  wen_weibo where weibo_id = '$contentid' limit 1 ")->result_array();

                //send IGTTUI push
                $this->load->model('Users_model');
                $clientid = $this->Users_model->getClientID($contentid);
                $result['d1'] = $clientid;
                if (!empty($clientid) && $now_time-$last_send_time >= 10800 ) {
                    $this->load->library('igttui');
                    $d = $this->igttui->sendMessage($clientid[0]['clientid'],$Idata['comment'],$message = '您收到一条短消息', $title='美丽神器', $type = 1);
                    $result['d'] = $d;
                }else{
                    if (count($judge) and $this->uid != $judge[0]['uid']) {
                        //send apple push
                        $this->load->model('push');
                        $push = array('type' => 'topic', 'id' => $contentid, 'page' => $result['page']);
                        $this->push->sendUser('[话题]新回复:' . $Idata['comment'], $judge[0]['uid'], $push);

                    }
                }
            }

            $this->track->reply($cid, $this->uid);
            if(strpos($_SERVER['HTTP_REFERER'],'comments') != 0){

                header('location:'.$_SERVER['HTTP_REFERER']);
            }else{
                redirect('manage/topic/detail/'.$param);
            }
        
        } else {
            redirect();
        }
    }

    //get top parent comment id
    private function GPCID($pid) {
        $this->db->where('id', $pid);
        $this->db->select('pid');
        $query = $this->db->get('wen_comment')->result_array();
        if (count($query)) {
            if ($query[0]['pid'] == 0) {
                return $pid;
            } else {
            	return $query[0]['pid'];
                //$this->GPCID($query[0]['pid']);
            }
        } else {
            return 0;
        }
    }

    public function total($param = '') {
        $this->db->where('role_id', 16);
        $data['uers'] = $this->db->get('users')->result_array();
        $data['res'] = $t = array();
        $data['cdate'] = date('Y-m-d');
        $data['edate'] = date("Y-m-d", strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate = $cdate + 3600 * 24;
        if ($this->input->get('yuyueDateStart')) {
            $data['cdate'] = $this->input->get('yuyueDateStart');
            $data['edate'] = $this->input->get('yuyueDateEnd');
            $cdate = strtotime($this->input->get('yuyueDateStart'));
            $edate = strtotime($this->input->get('yuyueDateEnd'));
        }
        foreach ($data['uers'] as $r) {
            $t['name'] = $r['alias'];
            $t['topic'] = $this->track->total($r['id'], 3, 'topic', $cdate, $edate);
            $t['reply'] = $this->track->total($r['id'], 3, 'reply', $cdate, $edate);

            $data['res'][] = $t;
        }
        $data['ftags'] = $this->track->mtagSum($cdate, $edate);

        $data['tags'] = $this->track->tagSum(0, 3, $cdate, $edate);
        for (; $cdate < $edate; $cdate+=86400) {
            $tmp['date'] = date('d', $cdate);
            $tmp['num'] = $this->track->topicSum($cdate, $cdate + 86400);
            $data['mtags'][] = $tmp;
        }
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_total";
        $this->load->view('manage', $data);
    }

    public function cdel($param = '') {
        if ($param && $this->wen_auth->get_role_id() == 16) {
            $updateData = array('is_delete' => 1);

            $this->common->updateTableData('wen_comment', intval($param), '', $updateData);
            $this->track->delReply($param);
            //$this->db->query("update wen_weibo SET commentnums=commentnums-1 where weibo_id=".$contentId." limit 1");
            redirect('manage/topic/comments');
        }
    }

    //pass check topic
    public function passcheck($param = '') {
        if ($param && $this->wen_auth->get_role_id() == 16) {
            $data = array(
                'isdel' => 0,
                'cTime' => time()
                );
            $this->db->where('weibo_id', intval($param));
            $this->db->update('wen_weibo', $data);
        }
    }

    public function del($param = '') {
        if ($param && $this->wen_auth->get_role_id() == 16) {
            $user_sql = "select uid from wen_weibo where weibo_id = {intval($param)} ";
            $temp = $this->db->query($user_sql)->row_array();
            $uid = $temp['uid'];
            
            
            $condition = array('contentid' => intval($param), 'type' => 'topic');
            $this->common->deleteTableData('wen_comment', $condition);
            $this->db->where('attachId', intval($param));
            $tmp = $this->db->get('topic_pics')->result_array();
            // 2014年12月25日 注释帖子图片
            /*foreach ($tmp as $r) {
                if (!$this->remote->del($r['savepath'], true)) {
                  //  die('file delete error!');
                };
            }*/
            $condition = array('weibo_id' => intval($param));
            $del_rs = $this->common->deleteTableData('wen_weibo', $condition);
 
            $condition = array('attachId' => intval($param));
            $this->common->deleteTableData('topic_pics', $condition);
            
//             if($del_rs){
//                 $user_jf_sql = " select jifen from users where id = {$uid}";
//                 $jf_rs = $this->db->query($user_jf_sql)->row_array();
//                 if($jf_rs){
//                 $jf = $jf_rs['jifen'] - 200;
//                     $jf = $jf_rs<0?0:$jf;
//                 }
//                 $jf_up_sql = "update users set jifen = $jf where id = {$uid} ";
//                 $jf_up_rs = $this->db->query($user_sql)->row_array();

//             }
            
            
            
            $data['display'] = 0;
            $this->eventDB->where('topic_id',intval($param));  
            $this->eventDB->update('event_topic_detail',$data);

            $track_updata['display'] = 0;
            $this->db->where('topic', intval($param));
            $this->db->update('user_track', $track_updata);
            
            redirect($this->session->userdata('history_url'));
        }
    }

    public function commentdel($param = '') {
        if ($param && $this->wen_auth->get_role_id() == 16) {
            $contentId = $this->db->query("select contentid from wen_comment where id=" . intval($param))->row_array();
            $condition = array('id' => intval($param), 'type' => 'topic');
            // $this->common->deleteTableData('wen_comment',$condition);
            $data['is_delete'] = 1;
            $this->db->where('id', intval($param));
            $this->db->update('wen_comment', $data);
            
            $track_updata['display'] = 0;
            $this->db->where('reply', intval($param));
            $this->db->update('user_track', $track_updata);
            //   $this->db->query("update wen_weibo SET comments=comments-1 where id=".$contentId." limit 1");
            redirect('manage/topic/detail/' . $contentId['contentid']);
        }
    }

    //user banned or open
    public function userbanned() {
        if (($uid = $this->input->get('uid'))) {
            $ban = intval($this->input->get('banned'));
            $data['banned'] = $ban;
            $this->db->where('id', $uid);
            $this->db->limit(1);
            $s = $this->db->update('users', $data);
            echo $uid;
            if ($uid) {
                $this->db->where('uid', $uid);
                $this->db->limit(1);
                $this->db->delete('wensessions');
            }
        }
    }

    public function Suser() {
    	$term = trim($this->input->get('term'));
    	$type = $this->input->get('type');
        if ($term) {
            if (!$type  OR $type == 'topic') {
                $tmp = $this->db->query("select id,alias as value,phone,role_id  from users where (alias like '%{$term}%' OR phone like '%{$term}%') AND (role_id=1 or role_id =16) LIMIT 9")->result_array();
            } elseif ( $type == 'ans') {
                $tmp = $this->db->query("select id,alias as value,phone,role_id from users where (alias like '%{$term}%' OR phone like '%{$term}%') AND role_id=2 LIMIT 9")->result_array();
            }

            $res = array();
            foreach ($tmp as &$r) {
                $r['value'] = trim($r['value']);
                if ($r['value'] == '') {
                    $r['value'] = $r['phone'];
                }
                $r['label'] = $r['value'];
                $res[] = $r;
            }
            echo json_encode($res);
        }
    }
    public function Sdoctor() {
        if ($t = trim($this->input->get('term'))) {

            $tmp = $this->db->query("select u.id,u.alias as value,u.phone,p.company as company from users u left join user_profile p on u.id=p.user_id where (alias like '%{$t}%' OR phone like '%{$t}%') AND role_id=2 LIMIT 9")->result_array();

            $res = array();
            foreach ($tmp as $r) {
                $r['value'] = trim($r['value']);
                if ($r['value'] == '') {
                    $r['value'] = $r['phone'];
                }
                $r['label'] = $r['value']."<".$r['company'].">";
                $res[] = $r;
            }
            echo json_encode($res);
        }
    }
    public function Shospital() {
        if ($t = trim($this->input->get('term'))) {

            $tmp = $this->db->query("select id,name as value from company where (name like '%{$t}%') LIMIT 9")->result_array();


            $res = array();
            foreach ($tmp as $r) {
                $r['value'] = trim($r['value']);

                $r['label'] = $r['value'];
                $res[] = $r;
            }
            echo json_encode($res);
        }
    }
    public function dc() {
        if ($t = trim($this->input->get('uid'))) {
            $this->db->where('uid',$t);
            $this->db->select('ncid, title');
            $tmp = $this->db->get('note_category')->result_array();

            $res = array();
            foreach ($tmp as $r) {
                $res[] = $r;
            }
            echo json_encode($res);
        }
    }
    //add topic
    public function add() {

        if ($this->input->post()) {

            $info = array();
            //$info['address'] = '';
            $info['title'] = $this->input->post('title');    //问题描述
            //$info['sex'] = $this->input->post('sex');

            if ($_FILES['picture']['tmp_name'][0]) { //上传图片
                $tmpdir = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                $addpl = array();
                $ext = $this->extendName($_FILES['picture']['name'][0]);
                $datas['name'] = uniqid() . rand(1000, 9999) . $ext;
                $datas['savepath'] = $tmpdir . $datas['name'];
                $ptmp = getimagesize($_FILES['picture']['tmp_name'][0]);
                //$upload_rs = $this->remote->upload_qiniu($_FILES['picture']['tmp_name'][0], $datas['name']);

                if (!$this->remote->cp($_FILES['picture']['tmp_name'][0], $datas['name'], $datas['savepath'], array('width' => 600, 'height' => 800), true)) {
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                    redirect('manage/topic/add');
                    exit;
                }
                $info['pic']['type'] = substr($ext, 1);
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
            if ($this->input->post('suser_id')) {
                $datas['uid'] = $this->input->post('suser_id');
            } else {
                $datas['uid'] = $this->uid;
            }

            $ttag = $this->input->post('positions');
            if ($ttag) {
                $datas['tags'] = ',';
                foreach ($ttag as $r) {
                    $datas['tags'].=$r . ',';
                }
            }
            $datas['weight'] = 1;
            $datas['wsource'] = 'windows';
            if ($this->input->post('ctime')) {
                $datas['newtime'] = $datas['ctime'] =$datas['nstime']= strtotime($this->input->post('ctime') . ' ' . $this->input->post('extrat') . ':' . $this->input->post('extrat1'));
            } else {
                $datas['newtime'] = $datas['ctime'] =$datas['nstime'] = time();
            }
            $datas['type_data'] = serialize($info);
            $datas['content'] = $this->input->post('description');
            
            if(intval($this->input->post('views')) > 0){
                $datas['views'] = $this->input->post('views');
            }else{
                $datas['views'] = rand(30000,50000);
            }
            $datas['hots'] = intval($this->input->post('hots'));
            $datas['tehui_ids'] = $this->input->post('tehui_ids');
            $datas['fs_ids'] = $this->input->post('fs_ids');
            $datas['extra_ids'] = $this->input->post('extra_ids');
            $datas['chosen'] = $this->input->post('chosen');
            if($datas['chosen']){
                $datas['chosentime'] = intval($this->input->post('days')) * 86400 + time();
                $datas['days'] = intval($this->input->post('days'));
                //帖子加精，用户积分+20
                $sql = "UPDATE users SET jifen=jifen+20 WHERE id = {$this->uid} limit 1";
                $this->db->query($sql);
            }
            $datas['top'] = $this->input->post('top');
            if($datas['top']){
                $datas['toptime'] = intval($this->input->post('top_days')) * 86400 + time();
                $datas['top_days'] = intval($this->input->post('top_days'));
            }
            $datas['hot'] = $this->input->post('hot');
            $datas['hot_start'] = strtotime($this->input->post('hot_start'));
            $datas['hot_end'] = strtotime($this->input->post('hot_end'));

            if($datas['hot']){
                $datas['hottime'] = intval($this->input->post('hot_days')) * 86400 + time();
                $datas['hot_days'] = intval($this->input->post('hot_days'));
            }

            $datas['top_start'] = strtotime($this->input->post('top_start'));
            $datas['top_end'] = strtotime($this->input->post('top_end'));
            $datas['chosen_start'] = strtotime($this->input->post('chosen_start'));
            $datas['chosen_end'] = strtotime($this->input->post('chosen_end'));
            $datas['piazza_start'] = strtotime($this->input->post('piazza_start'));
            $datas['piazza_end'] = strtotime($this->input->post('piazza_end'));
                //piazza
            $datas['piazza'] = $this->input->post('piazza');
            if($datas['piazza']){
                $datas['piazzatime'] = intval($this->input->post('piazza_days')) * 86400 + time();
                $datas['piazza_days'] = intval($this->input->post('piazza_days'));
            }

            ///小组置顶推荐
            $datas['group_start'] = strtotime($this->input->post('group_start'));
            $datas['group_end'] = strtotime($this->input->post('group_end'));
            $datas['grouptop'] = $this->input->post('grouptop');
            if($datas['grouptop']){
                $datas['grouptime'] = intval($this->input->post('group_days')) * 86400 + time();
                $datas['group_days'] = intval($this->input->post('group_days'));
            }
            //deal upload video
            $datas['video'] = $this->input->post('video');
            $datas['videoHeight'] = $this->input->post('videoHeight');
            if ($_FILES['uvideo']['tmp_name']) {
                /* $uvideo = array();
                  $uvideo['name'] = uniqid().rand(1000,99999) . substr($_FILES['uvideo']['tmp_name'],strlen($_FILES['uvideo']['tmp_name'])-5);
                  $uvideo['savepath'] = date('Y').'/'.date('m').'/'.date('d').'/';
                  if($this->remote->cpf($_FILES['uvideo']['tmp_name'],$uvideo['name'],$uvideo['savepath'],true)){

                  } */
              }
            //deal upload extral product
              if ($_FILES['extrapruduct_picture']['tmp_name']) {
                $upruduct = array();
                $upruduct['name'] = uniqid() . rand(1000, 99999) . '.jpg';
                $upruduct['savepath'] = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                if ($this->remote->cp($_FILES['extrapruduct_picture']['tmp_name'], $upruduct['name'], $upruduct['savepath'] . $upruduct['name'], true)) {
                    $tmpinsert = array();
                    $tmpinsert[0]['title'] = $this->input->post('extrapruduct_title');
                    $tmpinsert[0]['price'] = $this->input->post('extrapruduct_price');
                    $tmpinsert[0]['market_price'] = $this->input->post('extrapruduct_mprice');
                    $tmpinsert[0]['url'] = $this->input->post('extrapruduct_link');
                    $tmpinsert[0]['image'] = $upruduct['savepath'] . $upruduct['name'];
                    $tmpinsert[0]['uid'] = $this->uid;
                    $datas['product_data'] = serialize($tmpinsert);
                }
            }

            // 设置大图
            if ($_FILES['frontpic']['tmp_name']) {
                $upruduct = array();
                $upruduct['name'] = uniqid() . rand(1000, 99999) . '.jpg';
                $upruduct['savepath'] = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                $upload_rs = $this->remote->upload_qiniu($_FILES['frontpic']['tmp_name'], $upruduct['name']);

                if (!empty($upload_rs['key'])) {
                    $datas['front_q_pic'] = $upload_rs['key'];
                    $datas['type_pic'] = $this->input->post('typepic');
                    $datas['front_title'] = $this->input->post('frontdesc');
                }

            }


            $datas['vaoc'] = $datas['ctime'] + $datas['weight'];
            $weibo_id = $this->common->insertData('wen_weibo', $datas);
            $picorder = $this->input->post('picture_order');
            //topic pic
            if ($_FILES['picture']['tmp_name'][0]) { //上传图片
                $tmpdir = date('Y') . '/' . date('m') . '/' . date('d') . '/';
                $i = 0;
                foreach ($_FILES['picture']['tmp_name'] as $key => $r) {
                    if ($i == 0) {
                        $addpl['order'] = $picorder[0];
                        $addpl['attachId'] = $weibo_id;
                        $this->common->insertData('topic_pics', $addpl);
                    } elseif ($r) {
                        $addpl = array();
                        $addpl['attachId'] = $weibo_id;
                        //insert to db
                        $ext = $this->extendName($_FILES['picture']['name'][$i]);
                        ;
                        $addpl['name'] = uniqid(time() . rand(1000, 99999), false) . '.jpg';
                        $addpl['savepath'] = $tmpdir . $addpl['name'];
                        $ptmp = getimagesize($r);
                        if ($this->remote->cp($r, $addpl['name'], $addpl['savepath'], array('width' => 600, 'height' => 800), true)) {
                            $addpl['userId'] = $this->uid;
                            $addpl['width'] = $ptmp[0];
                            $addpl['height'] = $ptmp[1];
                            $addpl['cTime'] = time();
                            $addpl['type'] = 'jpg';
                            $addpl['privacy'] = 0;
                            $addpl['order'] = $picorder[$key];
                            $addpl['info'] = strip_tags($picinfo[$key]);
                            $this->common->insertData('topic_pics', $addpl);
                        }
                    }

                    $i++;
                }
            }
            //track user action
            if (isset($ttag)) {
                foreach ($ttag as $r) {
                    strlen($r) > 1 && $this->track->tags($r, $this->uid);
                }
            }
            $this->track->topic($weibo_id, $this->uid);
            $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '添加成功！'));
            redirect('manage/topic/add');
        }
        $result = $this->db->query("select * from wen_weibo where type=1 and uid=" . $this->session->userdata('WEN_user_id') . " order by ctime desc")->result_array();
        $data['result'] = $result;
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "topic_add";
        $this->load->view('manage', $data);
    }

    private function extendName($file_name) {
        $extend = pathinfo($file_name);
        $extend = strtolower($extend["extension"]);
        return '.' . $extend;
    }

    public function pageview(){

        $threeday = time() - 3*86400;
		
		$this->db->query("update wen_weibo set views=views + FLOOR(50 + (RAND() * 100)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update wen_weibo set views=views + FLOOR(100 + (RAND() * 500)) where ctime < (? - 3600) and ctime > (? - 24*3600)", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update wen_weibo set views=views + FLOOR(1000 + (RAND() * 5000)) where ctime < (? - 86400) and ctime > (? - 3*24*3600)", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update wen_weibo set views=views + FLOOR(5000 + (RAND() * 20000)) where ctime < {$threeday} limit 100");
        echo $this->db->last_query();
/*
		$this->db->query("update wen_weibo set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
		//$this->db->query("update note set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
		$this->db->query("update wen_comment set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
		//$this->db->query("update diary_comment set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));


		$this->db->query("update wen_weibo set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));
		//$this->db->query("update note set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));
		$this->db->query("update wen_comment set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));
		//$this->db->query("update diary_comment set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));*/
        redirect('manage/topic/');
    }

}

?>
