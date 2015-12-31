<?php
class diary extends CI_Controller {
	private $notlogin = true,$uid='',$imgurl = "http://pic.meilimei.com.cn/upload/",$qiniuimgurl="http://7xkdi8.com1.z0.glb.clouddn.com/";
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
        //error_reporting(E_ALL);
        //ini_set("display_errors","On");
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
        $this->load->model('Diary_model');
        $this->load->model('track');
       if(!$this->privilege->judge('diary')){
          die('Not Allow');
       }
	}
	//user info lists
	public function index() {
		$this->load->library('pager');
		$page = $this->input->get('page')?$this->input->get('page'):0;
		if($page < 0){
			$page = 0 ;
		}

		$data['issubmit'] = false;//
        $fix = '';
		$data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));//？
        $edate  = $cdate+3600*24;//
		if ($this->input->get('submit')) {
			$data['issubmit'] = true;
			$fix = 'submit=true&';
            $sname = '';//关键词
            $phone = '';
            $types = '';//类型
            $is_front = '';//推荐
            $loading = '';//启动
            $tags = '';//
            $userid = '';//

            $sname = trim($this->input->get('sname'));
            $username = trim($this->input->get('username'));//用户名
            $userid = trim($this->input->get('userid'));//用户名
            $types = $this->input->get('types');
            $is_front = $this->input->get('is_front');
            $loading = $this->input->get('loading');
            $tags = $this->input->get('tags');
            $stime = $this->input->get('stime');//开始时间
            $etime = $this->input->get('etime');//结束时间

            $sql = '';//预备sql拼接

            if(!empty($sname)){//n
                $sql .= " and n.content like '%{$sname}%'";//1.匹配关键词
                $fix.="sname={$sname}&";//2.拼接关键词url
            }
            
            if(!empty($userid)){
            	$sql .= " and n.uid = {$userid}";//1.匹配关键词
            	$fix.="userid={$userid}&";//2.拼接关键词url
            }

            if(!empty($username)){//u
                $sql .= " and (u.username like '%{$username}%' or u.alias like '%{$username}%')";
                $fix .= "username={$username}&";
            }
            
            if($stime && $etime){
                $fix.="stime={$stime}&etime={$etime}&";
                $stime = $stime." 00:00";//定制格式
                $etime = $etime." 23:59";
                //条件:
                $sql .= " and (n.created_at <=  ".strtotime($etime)." and n.created_at >=".strtotime($stime).")";

            }else{
                if($this->input->get('stime')){//
                    $fix.="stime={$stime}&etime=&";//url
                    $stime = $this->input->get('stime')." 00:00";//$stime = $stime." 00:00";
                    $etime = $this->input->get('stime')." 23:59";

                    $sql .= " and (n.created_at <=  ".strtotime($etime)." and n.created_at >=".strtotime($stime).")";
                }

                if($this->input->get('etime')){
                    $fix.="stime=&etime={$etime}&";//url
                    $stime = $this->input->get('etime')." 00:00";
                    $etime = $this->input->get('etime')." 23:59";

                    $sql .= " and (n.created_at <=  ".strtotime($etime)." and n.created_at >=".strtotime($stime).")";
                }
            }
            
            
            
            if($types == "win"){//n.os
                $sql .= " and n.os = 1";
                $fix.="types={$types}";  
            }elseif($types == "nowin"){
            	$sql .= " and n.os = 0";
            	$fix.="types={$types}";
            }
            

            
            
            
            if($is_front == "is_front"){
            	$sql .= " and n.is_front = 1";
            	$fix .= "is_front={$is_front}&";
            }elseif($is_front == "no_front"){
            	$sql .= " and n.is_front = 0";
            	$fix .= "is_front={$is_front}&";
            }
            
//             if(!empty($is_front)) {//推荐
//             	$sql .= " and n.is_front = '{$is_front}'";
//             	$fix .= "is_front={$is_front}&";
//             }
//             if(isset($loading)){//启动
//                 $sql .=" and n.loading = '{$loading}'";
//                 //var_dump($sql);exit;
//                 $fix.="loading={$loading}&";
//             }

            
            
            if($tags){//n.item_name
                $sql .= " and n.item_name = '{$tags}'";
                $fix .="tags={$tags}&";
            }
		}
        //总行数---n----u
		$data['total_rows'] = $this->db->query("select * from note n left join users u on n.uid=u.id where 1 = 1 $sql")->num_rows();
		//$data['abc_sql'] = "select * from note n left join users u on n.uid=u.id where 1 = 1 $sql";
        //var_dump($data['total_rows']);exit;177 194 每页12条，多余17条。需要2页放存
        //方案:分页时如果加上时间进行分页的话，按分页按钮(让时间固定在前面选择的下面，而不应该点击分页 按钮发生改变)
		$per_page = 12;//每页33行
		$start = intval($page);
		$start == 0 && $start = 1;//0 && 1

		if ($start > 0)
			$offset = ($start -1) * $per_page;//偏移量（6-1）*每行数
		else
			$offset = $start * $per_page;
        //nid？  limit;返回结果放置$data中--前台遍历的数据
		$data['results'] = $this->db->query("select * from note n left JOIN users u on n.uid = u.id where 1 = 1 $sql order by n.nid desc limit $offset , $per_page")->result();
		
		//echo $this->db->last_query();
		//echo "select * from note n left JOIN users u on n.uid = u.id where 1 = 1 $sql order by n.nid desc limit $offset , $per_page";die;
		foreach ($data['results'] as &$v){
			if(!empty($v->imgfile)){
				$v->imgurl = $this->qiniuimgurl.$v->imgfile;
			}else{
				$v->imgurl = $this->imgurl.$v->imgurl;
			}
		}
		
        //echo $this->db->last_query();exit;
        //echo $this->db->last_query();

        //var_dump($data['results']);   die;
		$data['offset'] = $offset +1;//偏移量价 1
        //$page>2时，($start -1)).$fix=>?;$data['preview']=>上一页
		$data['preview'] = $start > 2 ? site_url('manage/diary/index/' . ($start -1)).$fix : site_url('manage/diary/index').$fix;
        //下一页
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/diary/index/' . ($start +1)).$fix : '';
        
         $config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                "show_front_btn"=>true,
                "show_last_btn"=>true,
                'max_show_page_size'=>6,
                'querystring_name'=>$fix.'&page',//查询字符？
                'base_url'=>'manage/diary/index',
                "pager_index"=>$page
            );
        //进行初始化
        $this->pager->init($config);
        //建立pagelink
        $data['pagelink'] = $this->pager->builder_pager();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "diary";//项目管理单元
        //$start -1
		$this->session->set_userdata('history_url', 'manage/diary/index?page=' . ($start -1).'&'.$fix);

		$this->load->view('manage', $data);
	}

    public function add(){
        $ncid = $this->input->post('ncid');
        $uid = $this->input->post('uid');
        $content = $this->input->post('content') ? $this->input->post('content') : '';

        if($this->input->post('tag')){
            $item_name = $this->input->post('tag') ? $this->input->post('tag') : '';
        }else {
            $item_name = $this->input->post('item_name') ? $this->input->post('item_name') : '';
        }

        $item_price = $this->input->post('item_price') ? $this->input->post('item_price'): '';
        $doctor = $this->input->post('doctor') ? $this->input->post('doctor') : '';
        $hospital = $this->input->post('hospital') ? $this->input->post('hospital') : '';
        $pointX = $this->input->post('pointX')?$this->input->post('pointX'):'0.00';
        $pointY = $this->input->post('pointY')?$this->input->post('pointY'):'0.00';
        $type = $this->input->post('type')?$this->input->post('type'):0;
        $is_front = $this->input->post('isFront')?$this->input->post('isFront'):0;
        $loading = $this->input->post('loading')?$this->input->post('loading'):0;
        $sort = $this->input->post('sort')?$this->input->post('sort'):0;
        $filepath = $this->input->post('filepath')?$this->input->post('filepath'):0;
        $ctime = $this->input->post('ctime')?$this->input->post('ctime'):0;
        $views = $this->input->post('views')?$this->input->post('views'):0;
        $hour = $this->input->post('hour')?$this->input->post('hour'):'00:00';
        $imgurl = '';
        $arr_imgurl = array();
        $arr_imgurl = explode('/upload/',$filepath);
        $imgurl = isset($arr_imgurl[1])?$arr_imgurl[1]:'';


        $check = 1;
        $setting = 0;

        $result['state'] = '000';


        if(!empty($content) && $uid){

            if(!empty($ctime)){

                $time =strtotime($ctime." ".$hour);
                $oneday = strtotime($ctime);
            }else{
                $time =time();
                $oneday = strtotime(date('Y-m-d'));
            }

            if($type != 1) {


                $isItem = $this->Diary_model->isItem($item_name);
                if($isItem){
                    $this->Diary_model->addItem($item_name);
                }
                //计算这个目录第几天
                $lastNote = $this->Diary_model->getLastNote($uid,$ncid);
                //计算这个项目第几天
                $itemLastNote = $this->Diary_model->getItemLastNote($uid,$item_name);

                $categoryDay = 0;

                if(empty($lastNote)){
                    $categoryDay = 1 ; //第一天
                }else {
                    //计算到第几天
                    $datetime = 0;
                    if (!empty($ctime)) {

                        $datetime = intval(strtotime($ctime)) - intval(strtotime(date("Y-m-d",$lastNote[0]['created_at'])));
                    } else {
                        $datetime = intval(strtotime(date("Y-m-d",time()))) - intval(strtotime(date("Y-m-d",$lastNote[0]['created_at'])));
                    }

                    $categoryDay = ceil(($datetime/86400)) + $lastNote[0]['cday'];

                }

                $itemDay = 0;

                if(empty($itemLastNote)){
                    $itemDay = 1 ; //第一天
                }else{
                    //计算到第几天
                    $datetime = 0;

                    if (!empty($ctime)) {

                        $datetime = intval(strtotime($ctime)) - intval(strtotime(date("Y-m-d",$itemLastNote[0]['created_at'])));
                    } else {
                        $datetime = intval(strtotime(date("Y-m-d",time()))) - intval(strtotime(date("Y-m-d",$itemLastNote[0]['created_at'])));
                    }
                    $itemDay = ceil(($datetime / 86400)) + $itemLastNote[0]['itemday'];

                }


                $data = array('uid' => $uid,
                    'ncid' => $ncid,
                    'imgurl' => $imgurl,
                    'content' => $content,
                    'item_name' => $item_name,
                    'item_price' => $item_price,
                    'doctor' => $doctor,
                    'hospital' => $hospital,
                    'review' => $check,
                    'setting' => $setting,
                    'cday' => $categoryDay,
                    'itemday' => $itemDay,
                    'oneday' => $oneday,
                    'pointX' => $pointX,
                    'pointY' => $pointY,
                    'is_front' => $is_front,
                    'loading' => $loading,
                    'sort' => $sort,
                    'os' => 1,
                    'views'=>$views,
                    'created_at' => $time,
                    'updated_at' => $time
                );

                $isCategoryPic = $this->Diary_model->isCategoryPic($ncid);
                if (!$isCategoryPic) {
                    $flag = $this->Diary_model->updateCategoryPic($ncid, $imgurl);
                    if (!$flag) {
                        $result['state'] = '014';
                        $result['notice'] = '用户美人记封面更新不成功';
                    }
                }


                $result['data'] = $this->Diary_model->saveUserDiary($data);
                $nid = $this->Diary_model->getLastID();
                $this->db->insert('note_item',array('nid'=> $nid, 'item_name'=>$item_name,'item_price'=>$item_price,'pointx'=>$pointX, 'pointy'=>$pointY ,'created_at'=>time()));
                $this->track->diary($result['data'], $this->uid,2);
                redirect('manage/diary');
            }else{
                $nid = $this->input->post('nid')?$nid = $this->input->post('nid'):0;
                $data = array('uid' => $uid,
                    'ncid' => $ncid,
                    'imgurl' => $imgurl,
                    'content' => $content,
                    'item_name' => $item_name,
                    'item_price' => $item_price,
                    'doctor' => $doctor,
                    'hospital' => $hospital,
                    'review' => $check,
                    'setting' => $setting,
                    'oneday' => $oneday,
                    'pointX' => $pointX,
                    'pointY' => $pointY,
                    'is_front' => $is_front,
                    'loading' => $loading,
                    'sort' => $sort,
                    'views'=> $views,
                    //'created_at' => $time,
                    'updated_at' => $time
                );

                $result['data'] = $this->Diary_model->updateUserDiary($nid, $data);

                $this->track->diary($nid, $this->uid,2);
                $this->db->where('nid',$nid);
                $this->db->update('note_item',array('item_name'=>$item_name,'item_price'=>$item_price,'pointx'=>$pointX, 'pointy'=>$pointY));
                redirect('manage/diary');
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录';
        }
        
        //标签问题
        $items_sql = "select id,pid,name from mlm_items";
        $items_sql = "select * from mlm_items where pid = 0";
        $items_rs = $this->db->query($items_sql)->result_array();
        
        foreach ($items_rs as &$rs_v){
        	$sub_items_sql = "select * from mlm_items where pid = {$rs_v['id']}";
        	$rs_v['child'] = $this->db->query($sub_items_sql)->result_array();
        	foreach ($rs_v['child'] as &$v){
        		$three_items_sql = "select * from mlm_items where pid = {$v['id']}";
        		$v['three_child'] = $this->db->query($three_items_sql)->result_array();
        	}
        }

        //print_r($items_rs);die;
        $data['items_rs'] = $items_rs;

        if($this->input->get('type') == 1){
            $result = $this->db->query("select n.imgfile,n.sort,n.nid,n.uid,n.content,n.imgurl,n.item_name,n.item_price,n.doctor,n.hospital,n.pointX,n.pointY,n.is_front,n.loading,nc.title,nc.ncid,u.username,n.created_at,n.updated_at from note n left join note_category nc on n.ncid=nc.ncid left join users u on n.uid=u.id where n.nid=" . $this->input->get('nid') . " order by n.created_at desc")->result();
            $data['result'] = $result;
            foreach ($data['result'] as &$v){
            	if(!empty($v->imgfile)){
            		$v->imgurl = $this->qiniuimgurl.$v->imgfile;
            	}else{
            		$v->imgurl = $this->imgurl.$v->imgurl;
            	}
            }
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "diary_edit";
        }else {
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "diary_add";
        }
        $this->load->view('manage', $data);
    }

    public function category(){

        $page = $this->input->get('page');

        $this->load->library('pager');

        $data['issubmit'] = false;$fix = '';
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        $sql = '';
        if ($this->input->get('submit')) {

            $data['issubmit'] = true;
            $fix = 'submit=true';
            if($sname = $this->input->get('sname')){
                $sql .= " and c.title like '%{$sname}%'";
                $fix.="sname={$sname}&";
            }

            $username = $this->input->get('username');


            if($username){
                $sql .= " and (u.username like '%{$username}%' or u.alias like '%{$username}%')";
                $fix.="username={$username}&";
            }

        }

        $data['total_rows'] = $this->db->query("select * from note_category c left join users u on c.uid=u.id where 1=1 $sql ")->num_rows();
        //print_r($data['total_rows']);exit;
        $per_page = 20;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start -1) * $per_page;
        else
            $offset = $start * $per_page;
        
        $data['results'] = $this->db->query("select * from note_category c left join users u on c.uid=u.id where 1=1 $sql order by c.ncid desc limit $offset , $per_page")->result();
        foreach ($data['results'] as &$v){
        	if(!empty($v->imgfile)){
        		$v->imgurl = $this->qiniuimgurl.$v->imgfile;
        	}else{
        		$v->imgurl = $this->imgurl.$v->imgurl;
        	}
        }

        //var_dump($data['results']);   die;
        $data['offset'] = $offset +1;
        $data['preview'] = $start > 2 ? site_url('manage/diary/category/' . ($start -1)).$fix : site_url('manage/diary/category').$fix;
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/diary/category/' . ($start +1)).$fix : '';
        
        $config =array(
            "record_count"=>$data['total_rows'],//每页分页尺寸
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>10,
            'querystring_name'=>$fix.'page',
            'base_url'=>'manage/diary/category',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "diary_category";
        $this->session->set_userdata('history_url', 'manage/diary/category?page=' . ($start -1).'&'.$fix);

        $this->load->view('manage', $data);
    }

    public function delcategory($ncid){
        $condition = array('ncid'=>$ncid);
        $this->db->delete('note_category',$condition);
        redirect('manage/diary/category');
    }

    public function addcategory(){
        if ($this->input->post()) {

            $is = $this->input->post('is');
            $title = $this->input->post('title');
            $desc = $this->input->post('desc');
            $type = $this->input->post('type');
            $ncid = $this->input->post('ncid');
            $this->uid = $this->input->post('uid');
            $operation_time = strtotime($this->input->post('operation_time'));

            if (isset ($_FILES['noteCategoryPic']['name']) && $_FILES['noteCategoryPic']['name'] != '') {
                $result['notice'] = '美人记发布成功！';
                $imgurl = date('Y') . '/' . date('m') . '/' . date('d');
                $ext = '.jpg';
                $filename = uniqid().rand(1000,9999) . $ext;
                $imgurl .= '/' . $filename;
                $ptmp = getimagesize($_FILES['noteCategoryPic']['tmp_name']);
                if (!$this->remote->cp($_FILES['noteCategoryPic']['tmp_name'], $filename, $imgurl, array (
                    'width' => 600,
                    'height' => 800
                ), true)) {

                    $result['state'] = '001';
                    $result['notice'] = '图片上传失败！';
                    echo json_encode($result);
                    exit;
                }
            }
            if($imgurl != '') {
                $data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc, 'imgurl' => $imgurl, 'operation_time'=>$operation_time,'created_at' => time(), 'updated_at' => time());
            }else{
                $data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc,'operation_time'=>$operation_time, 'created_at' => time(), 'updated_at' => time());
            }

            //$result['debug'] = $data;
            if($type == 1) {
                $isCategory = $this->Diary_model->isCategory($title, $this->uid);
                if (intval($isCategory) <= 0) {
                    $result['data'][] = $this->Diary_model->addNoteCategory($data);
                    $result['return'] = array('ncid' => $this->db->insert_id(), 'title' => $title);
                    $result['notice'] = '日记目录添加成功！';

                    $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '添加成功！'));
                    redirect('manage/diary/category');
                } else {
                    $result['state'] = '013';
                    $result['notice'] = '日记目录名称重复！';
                }
            }else{
                if(intval($ncid) > 0){
                    $result['data'][] = $this->Diary_model->updateNoteCategory($ncid ,$data);
                }else{
                    $result['state'] = '014';
                    $result['notice'] = '请传入更改目录的编号！';
                }
                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '修改成功！'));
                redirect('manage/diary/category');
            }
        }


        if($this->input->get('type') == 2){
            $result = $this->db->query("select * from note_category where ncid=" . $this->input->get('ncid') . " order by created_at desc")->result_array();
            $data['result'] = $result;
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "diary_category_edit";
        }else {
            $data['notlogin'] = $this->notlogin;
            $data['message_element'] = "diary_category_add";
        }
        $this->load->view('manage', $data);
    }

    public function view(){

    }

    /** 详细页
     * @param string $param
     * @param string $page
     */
    public function detail($param = '', $page = '') {

        $data['issubmit'] = false;
         $sql = '';
         if($this->input->get('wsource')){
            $sql .= " and nc.os = 0";
        }
        $data['total_rows'] = $this->db->query("SELECT cid FROM note_comment nc WHERE nid = {$param} $sql")->num_rows(); //评论总数

        $per_page = $data['issubmit'] ? 25 : 16;      //16
        $per_page = 20;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start -1) * $per_page;
        else
            $offset = $start * $per_page;
        $data['results'] = $this->db->query("select nc.content,nc.fromusername,nc.nid,nc.cid,nc.imgurl as ncimgurl,nc.imgfile as ncimgfile,n.content as ncontent, n.imgurl as nimgurl,u.username from note_comment nc left JOIN note n on nc.nid = n.nid left join users u on n.uid=u.id where n.nid={$param} $sql order by nc.cid desc limit $offset , $per_page")->result();
        
        echo "select nc.content,nc.fromusername,nc.nid,nc.cid,nc.imgurl as ncimgurl,nc.imgfile as ncimgfile,n.content as ncontent, n.imgurl as nimgurl,u.username from note_comment nc left JOIN note n on nc.nid = n.nid left join users u on n.uid=u.id where n.nid={$param} $sql order by nc.cid desc limit $offset , $per_page";
        foreach ($data['results'] as &$v){
        	if(!empty($v->ncimgfile)){
        		$v->ncimgurl = $this->qiniuimgurl.$v->ncimgfile;
        	}else{
        		$v->ncimgurl = $this->imgurl.$v->ncimgurl;
        	}
        }

        $data['noteInfo'] = $this->db->query("select *From note where nid={$param} limit 1")->result();
        
        foreach ($data['noteInfo'] as &$v){
        	if(!empty($v->imgfile)){
				$v->imgurl = $this->qiniuimgurl.$v->imgfile;
			}else{
				$v->imgurl = $this->imgurl.$v->imgurl;
			}
        }

        $this->db->select('uid');
        $this->db->where('touid',$data['noteInfo'][0]->uid);
        $this->db->where('contentid',$param);
        $this->db->where('type','diary');
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
        $data['zanUserList'] = $zanUserList;

        $page < 1 && $page = 1;
        $data['preview'] = site_url('manage/diary/detail/' . $param . '/' . ($page - 1));
        $data['next'] = site_url('manage/diary/detail/' . $param . '/' . ($page + 1));
        $data['offset'] = $offset + 1;

        $data['notlogin'] = $this->notlogin;
        $data['tid'] = $param;
        $data['message_element'] = "diary_detail";
        $this->load->view('manage', $data);
    }

    public function comments(){

        $page = $this->input->get('page');

        $this->load->library('pager');

        $data['issubmit'] = false;$fix = '';
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;

        if ($this->input->get('submit')) {
            $data['issubmit'] = true;
            $fix = 'submit=true&';
            $kw = $this->input->get('kw');

            $types = $this->input->get('types');

            $uid = $this->input->get('uid');

            $sql = '';

            if($kw){
                $sql .= " and nc.content like '%{$kw}%'";
                $fix .="kw={$kw}&";
            }

            if($uid){
                $sql .= " and u.phone like '%".$uid."%'";
                $fix .="uid={$uid}&";
            }

            if($types != ''){
                $sql .= " and nc.os = '{$types}'";
                $fix .="types={$types}&";
            }

        }


        $data['total_rows'] = $this->db->query("select nc.*,n.content as ncontent, n.imgurl as nimgurl,u.username from note_comment nc left JOIN note n on nc.nid = n.nid left join users u on n.uid=u.id where 1=1 $sql order by nc.cid desc")->num_rows();
        //echo "select nc.*,n.content as ncontent, n.imgurl as nimgurl,u.username from note_comment nc left JOIN note n on nc.nid = n.nid left join users u on n.uid=u.id where 1=1 $sql order by nc.cid desc";
        $per_page = 20;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start -1) * $per_page;
        else
            $offset = $start * $per_page;
        $data['results'] = $this->db->query("select nc.*,n.content as ncontent,n.imgfile as nimgfile, n.imgurl as nimgurl,u.username,u.banned from note_comment nc left JOIN note n on nc.nid = n.nid left join users u on n.uid=u.id where 1=1 $sql order by nc.cid desc limit $offset , $per_page")->result();
        foreach ($data['results'] as &$v){
        	if($v->nimgfile){
        		$v->nimgurl = $this->qiniuimgurl.$v->nimgfile;
        	}else{
        		$v->nimgurl = $this->imgurl.$v->nimgurl;
        	}
        }
 
        //echo '<pre>';
        //print_r($data['results']);
        //echo $this->db->last_query();
        //echo '<pre>';
        //print_r($data['results']);
        //var_dump($data['results']);   die;.
        $data['offset'] = $offset +1;
        $data['preview'] = $start > 2 ? site_url('manage/diary/comments/' . ($start -1)).$fix : site_url('manage/diary/category').$fix;
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/diary/category/' . ($start +1)).$fix : '';

        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>10,
            'querystring_name'=>$fix.'page',
            'base_url'=>'manage/diary/comments',
            "pager_index"=>$page
        );
        
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "diary_comments";
        $this->session->set_userdata('history_url', 'manage/diary/comments?page=' . ($start -1).'&'.$fix);

        $this->load->view('manage', $data);
    }

    public function sendcomment($nid){

        //$fromusername = $this->input->post('user_id');
        $fromuid = $this->input->post('fuid');
        $comment = $this->input->post('comment');
        $pcid = $this->input->post('commentTo');
        
        $this->db->where('id',$fromuid);
        $this->db->select('username');
        $fromusername = $this->db->get('users')->row_array();

        $crs = '';
        $urs = '';

        if($pcid){
            $this->db->where('cid',$pcid);
            $this->db->select('fromuid, fromusername');
            $crs = $this->db->get('note_comment')->result_array();
            $tousername = $crs[0]['fromusername'];
            $touid = $crs[0]['fromuid'];
        }else{
            $this->db->where('note.nid',$nid);
            $this->db->select('note.uid, users.username');
            $this->db->from('note');
            $this->db->join('users','note.uid = users.id');
            $urs = $this->db->get()->result_array();
            $tousername = $urs[0]['username'];
            $touid = $urs[0]['uid'];
        }

        
        if($nid){
        	$this->db->where('nid',$nid);
        	$this->db->select('created_at');
        	$this->db->order_by('created_at','DESC');
        	$this->db->from('note');
        	$note_com_rs = $this->db->get()->row_array();
        }
        
 
        $last_send_time = $note_com_rs['created_at'];
        $now_time = time();
       
        
        
        $result['state'] = '000';

        if($nid && $comment && $fromuid){
        	$Idata = array();
            $data = array(
                'nid'=>$nid,
                'fromusername'=>$fromusername['username'],
                'fromuid' => $fromuid,
                'tousername' => $tousername,
                'touid'=>$touid,
                'content'=>$comment,
                'pcid'=>$pcid,
                'os' =>1,
                'oper'=>$this->wen_auth->get_username(),
                'created_at'=>time(),
                'updated_at'=>time()
            );

            $result['data'] = $this->Diary_model->saveComment($data);

            if(isset($result['data']) && $result['data']){
                $this->Diary_model->updateTotalCommnetsForNote($nid);
            }
            $result['total_comments'] = $this->Diary_model->getCommentCount($nid);
            $result['page'] = ceil(intval($result['total_comments'])/10);
            
        	//send IGTTUI push
            $this->load->model('Users_model');
            $clientid = $this->Users_model->getClientID($nid, 1);

            $result['debug'] = $clientid[0]['clientid'];
            $this->db->where('nid',$nid);
            $tmpnote = $this->db->get('note')->row_array();
            
            $sub_comment = mb_substr($comment, 0,10,'UTF8');
          
            
            if($this->uid != $tmpnote['uid'] && ($now_time-$last_send_time >= 10800)) {
                if (!empty($clientid)) {
                    $this->load->library('igttui');
                    $d = $this->igttui->sendMessage($clientid[0]['clientid'], "diary:" . $nid . ":" . $result['page'] . ':' . $sub_comment);
                    $result['d'] = $d;
                } else {
                    $this->load->model('push');
                    $push = array('type' => 'diary', 'id' => $nid, 'page' => $result['page']);
                    $this->push->sendUser('[美人计]新回复:' . $sub_comment, $touid, $push);
                }
            }
            
            
            $this->track->comments($result['data'], $this->uid,2);
            redirect('manage/diary/detail/'.$nid);
            
            

        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function sendcommentx($nid){
        $fromusername = $this->input->post('user_id');
        $fromuid = $this->input->post('fuid');
        $comment = $this->input->post('comment');
        $pcid = $this->input->post('commentTo');

        $crs = '';
        $urs = '';

        if($pcid){
            $this->db->where('cid',$pcid);
            $this->db->select('fromuid, fromusername,pcid');
            $crs = $this->db->get('note_comment')->result_array();
            $tousername = $crs[0]['fromusername'];
            $touid = $crs[0]['fromuid'];
            $pcid = $crs[0]['pcid'];
        }else{
            $this->db->where('note.nid',$nid);
            $this->db->select('note.uid, users.username');
            $this->db->from('note');
            $this->db->join('users','note.uid = users.id');
            $urs = $this->db->get()->result_array();
            $tousername = $urs[0]['username'];
            $touid = $urs[0]['uid'];
        }

        $result['state'] = '000';

        if($nid && $comment && $fromuid){
            $data = array(
                'nid'=>$nid,
                'fromusername'=>$fromusername,
                'fromuid'=>$fromuid,
                'tousername'=>$tousername,
                'touid'=>$touid,
                'content'=>$comment,
                'pcid'=>$pcid,
                'created_at'=>time(),
                'updated_at'=>time()
            );

            $result['data'] = $this->Diary_model->saveComment($data);
            $this->track->comments($result['data'], $this->uid,2);
            if(isset($result['data']) && $result['data']){
                $this->Diary_model->updateTotalCommnetsForNote($nid);
            }
            $result['total_comments'] = $this->Diary_model->getCommentCount($nid);
            $result['page'] = ceil(intval($result['total_comments'])/10);

            redirect('manage/diary/comments/');
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function delcomment($cid){
        $condition = array('cid'=>$cid);
        $this->db->delete('note_comment',$condition);
        
        $track_updata['display'] = 0;
        $this->db->where('comments', intval($cid));
        $this->db->update('user_track', $track_updata);
        
        redirect('manage/diary/comments');
    }

    public function commentdel($cid){
        $condition = array('cid'=>$cid);
        $this->db->delete('note_comment',$condition);
        
        $track_updata['display'] = 0;
        $this->db->where('comments', intval($cid));
        $this->db->update('user_track', $track_updata);
        
        redirect('manage/diary/detail/'.$this->input->get('nid'));
    }

    public function review($nid){

        if(intval($nid) > 0){
            $this->db->where('nid',$nid);
            $this->db->update('note',array('review'=>1));
            redirect('manage/diary/check');
        }
        redirect('manage/diary/check');
    }
    public function check(){
        $page = $this->input->get('page');

        $this->load->library('pager');

        $data['issubmit'] = false;$fix = '';
        $data['cdate'] =  date('Y-m-d');
        $data['edate']  = date("Y-m-d",strtotime("+1 day"));
        $cdate = strtotime(date('Y-m-d'));
        $edate  = $cdate+3600*24;
        if ($this->input->get('submit')) {
            $data['issubmit'] = true;
            $fix = 'submit=true';

        }

        $data['total_rows'] = $this->db->query("select * from note n left join note_category nc on n.ncid=nc.ncid left join users u on n.uid=u.id where nc.`is`=1  and n.`review` = 0")->num_rows();

        $per_page = 25;
        $start = intval($page);
        $start == 0 && $start = 1;

        if ($start > 0)
            $offset = ($start -1) * $per_page;
        else
            $offset = $start * $per_page;
        $data['results'] = $this->db->query("select n.*,u.banned from note n left join note_category nc on n.ncid=nc.ncid left join users u on n.uid=u.id where nc.`is`=1  and n.`review` = 0 order by nid desc limit $offset , $per_page")->result();
        foreach ($data['results'] as &$v){
        	if(!empty($v->imgfile)){
        		$v->imgurl = $this->qiniuimgurl.$v->imgfile;
        	}else{
        		$v->imgurl = $this->imgurl.$v->imgurl;
        	}
        }

        //var_dump($data['results']);   die;
        $data['offset'] = $offset +1;
        $data['preview'] = $start > 2 ? site_url('manage/diary/check/' . ($start -1)).$fix : site_url('manage/diary/check').$fix;
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/diary/check/' . ($start +1)).$fix : '';

        $config =array(
            "record_count"=>$data['total_rows'],
            "pager_size"=>$per_page,
            "show_jump"=>true,
            "show_front_btn"=>true,
            "show_last_btn"=>true,
            'max_show_page_size'=>10,
            'querystring_name'=>$fix.'page',
            'base_url'=>'manage/check/index',
            "pager_index"=>$page
        );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "check";
        $this->session->set_userdata('history_url', 'manage/diary/check?page=' . ($start -1).'&'.$fix);

        $this->load->view('manage', $data);
    }

	//delete user
	public function del($nid){
		$user_sql = "select uid from note where nid = {$nid} ";
		$temp = $this->db->query($user_sql)->row_array();
        $uid = $temp['uid'];
		
	    $condition = array('nid'=>$nid);
        $del_rs = $this->db->delete('note',$condition);
        
//         if($del_rs){
//             $user_jf_sql = " select jifen from users where id = {$uid}"; 
//             $jf_rs = $this->db->query($user_jf_sql)->row_array();
//             if($jf_rs){
//                 $jf = $jf_rs['jifen'] - 200;
//                 $jf = $jf_rs<0?0:$jf;
//             }
//             $jf_up_sql = "update users set jifen = $jf where id = {$uid} ";
//             $jf_up_rs = $this->db->query($user_sql)->row_array();
            
//             //$this->db->insert('score_logger', array('uid'=>$uid, 'desc'=>'美人计', 'score'=>200, 'created_at'=>time()));
//         }
        
        $track_updata['display'] = 0;
        $this->db->where('diary', intval($nid));
        $this->db->update('user_track', $track_updata);
        
        redirect('manage/diary');
	}


    public function img_save_to_file(){

        $imagePath = "/mnt/meilimei/t/";

        $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
        $temp = explode(".", $_FILES["img"]["name"]);
        $extension = end($temp);

        if ( in_array($extension, $allowedExts))
        {
            if ($_FILES["img"]["error"] > 0)
            {
                $response = array(
                    "status" => 'error',
                    "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                );
                echo "Return Code: " . $_FILES["img"]["error"] . "<br>";
            }
            else
            {

                $filename = $_FILES["img"]["tmp_name"];
                list($width, $height) = getimagesize( $filename );
                move_uploaded_file($filename,  $imagePath . $_FILES["img"]["name"]);

                $response = array(
                    "status" => 'success',
                    "url" => 'http://www.meilimei.com/t/'.$_FILES["img"]["name"],
                    "width" => $width,
                    "height" => $height
                );
            }
        }
        else
        {
            $response = array(
                "status" => 'error',
                "message" => 'something went wrong',
            );
        }

        print json_encode($response);

    }

    public function img_crop_to_file(){
        $arrImgUrl = explode('/t/',$_POST['imgUrl']);
        $imgInitW = $_POST['imgInitW'];
        $imgInitH = $_POST['imgInitH'];
        $imgW = $_POST['imgW'];
        $imgH = $_POST['imgH'];
        $imgY1 = $_POST['imgY1'];
        $imgX1 = $_POST['imgX1'];
        $cropW = $_POST['cropW'];
        $cropH = $_POST['cropH'];

        $imgUrl = '/mnt/meilimei/t/'.$arrImgUrl[1];
        $jpeg_quality = 100;

        $output_filename = "/mnt/meilimei/t/croppedImg_".rand();

        $what = getimagesize($imgUrl);
        switch(strtolower($what['mime']))
        {
            case 'image/png':
                $img_r = imagecreatefrompng($imgUrl);
                $source_image = imagecreatefrompng($imgUrl);
                $type = '.png';
                break;
            case 'image/jpeg':
                $img_r = imagecreatefromjpeg($imgUrl);
                $source_image = imagecreatefromjpeg($imgUrl);
                $type = '.jpeg';
                break;
            case 'image/gif':
                $img_r = imagecreatefromgif($imgUrl);
                $source_image = imagecreatefromgif($imgUrl);
                $type = '.gif';
                break;
            default: die('image type not supported');
        }

        $resizedImage = imagecreatetruecolor($imgW, $imgH);
        imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW,
            $imgH, $imgInitW, $imgInitH);


        $dest_image = imagecreatetruecolor($cropW, $cropH);
        imagecopyresampled($dest_image, $resizedImage, 0, 0, $imgX1, $imgY1, $cropW,
            $cropH, $cropW, $cropH);


        imagejpeg($dest_image, $output_filename.$type, $jpeg_quality);

        if (isset ($output_filename) && $output_filename != '') {
            $result['notice'] = '美人记发布成功！';
            $imgurl1 = date('Y') . '/' . date('m') . '/' . date('d');
            $ext = '.jpg';
            $filename = uniqid() . rand(1000, 9999) . $ext;
            $imgurl1 .= '/' . $filename;
            $ptmp = getimagesize($output_filename);


            if (!$this->remote->cp2($output_filename.$type, $filename, $imgurl1, array(
                'width' => 480,
                'height' => 480
            ), true)
            ) {

                $result['state'] = '001';
                $result['notice'] = '图片上传失败！';
                echo json_encode($result);
                exit;
            }
        }
        $response = array(
            "status" => 'success',
            "url" => 'http://pic.meilimei.com.cn/upload/'.$imgurl1
        );
        print json_encode($response);
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
            $t['diary'] = $this->track->total($r['id'], 3, 'diary', $cdate, $edate);
            $t['comments'] = $this->track->total($r['id'], 3, 'comments', $cdate, $edate);
 

            $data['res'][] = $t;
        }

       // $data['ftags'] = $this->track->mtagSum($cdate, $edate);

        //$data['tags'] = $this->track->tagSum(0, 5, $cdate, $edate);
        for (; $cdate < $edate; $cdate+=86400) {
            $tmp['date'] = date('d', $cdate);
            $tmp['num'] = $this->track->diarySum($cdate, $cdate + 86400);
            $data['mtags'][] = $tmp;
        }
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "diary_total";

        $this->load->view('manage', $data);
    }

    public function addZan($param = ''){

        $contentid = intval($param);
        $this->uid = $this->input->post('suid');
        $type = $this->input->post('type')?$this->input->post('type'):'diary';
        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';

        if($contentid && $this->uid){

            $rs =  $this->Diary_model->getMyZanTopic($contentid);
            $this->db->where('touid', $rs[0]['touid']);
            $this->db->where('type',$type);
            $num = $this->db->get('wen_zan')->num_rows();

            $isZan = $this->Diary_model->isZan($this->uid, $contentid,$type);
            $result['debug'] = $this->uid."==".$contentid."==".$type."==".$isZan;
            if(!$isZan) {
                $result['data']['zan'] = $this->Diary_model->addZan($contentid, $this->uid,$type);
                $result['data']['flag'] = 1;
            }else{
                $result['data']['zan'] = $this->Diary_model->getZanNum($contentid,$type);
                $result['data']['flag'] = 0;
            }

            redirect('manage/diary/detail/'.$contentid);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }


    public function pageview(){
       /* error_reporting(E_ALL);
        ini_set('display_errors','On');
        $threeday = time() - 3*86400;
		
		$this->db->query("update note set views=views + FLOOR(50 + (RAND() * 100)) where created_at > (? - 3600) and created_at < ?", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update note set views=views + FLOOR(100 + (RAND() * 500)) where created_at < (? - 3600) and created_at > (? - 24*3600)", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update note set views=views + FLOOR(1000 + (RAND() * 5000)) where created_at < (? - 86400) and created_at > (? - 3*24*3600)", array(time(), time()));
        echo $this->db->last_query();
        $this->db->query("update note set views=views + FLOOR(5000 + (RAND() * 20000)) where created_at < {$threeday} limit 100");
        echo $this->db->last_query();

		//$this->db->query("update wen_weibo set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
		$this->db->query("update note set zan=zan + FLOOR(10 + (RAND() * 50)) where created_at > (? - 3600) and created_at < ?", array(time(), time()));
		//$this->db->query("update topic_comment set zan=zan + FLOOR(10 + (RAND() * 50)) where ctime > (? - 3600) and ctime < ?", array(time(), time()));
		$this->db->query("update note_comment set zan=zan + FLOOR(10 + (RAND() * 50)) where created_at > (? - 3600) and created_at < ?", array(time(), time()));


		//$this->db->query("update wen_weibo set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));
		$this->db->query("update note set zan=zan + ? where created_at < (? - 3600)", array(10, time(), time()));
		//$this->db->query("update topic_comment set zan=zan + ? where ctime < (? - 3600)", array(10, time(), time()));
		$this->db->query("update note_comment set zan=zan + ? where created_at < (? - 3600)", array(10, time(), time()));

        redirect('manage/diary/');*/
    }
}
?>
