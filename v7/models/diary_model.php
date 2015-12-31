<?php

class Diary_model extends CI_Model
{
    public function Diary_model()
    {
        parent::__construct();
        //error_reporting(E_ALL);
        ini_set("display_errors","On");
        //$this->db = $this->load->database();显示空白页  
    }

    public function jserach($c = '', $offset = 0, $limit = 10){
        if(empty($c)) {
            return;
        }

        $this->db->like('content',$c);
        $this->db->or_like('item_name', $c);
        $this->db->where('created_at <=',time());
        $this->db->limit($limit, $offset);
        return $this->db->get('note')->result_array();
/*
        $this->db->like('note.content',$c);
        $this->db->or_like('note.item_name', $c);
        $this->db->from('note');
        $this->db->join('note_category','note.ncid=note_category.ncid');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
  */  }
    /**
     * 获得日记小图显示方式
     * @param int $lastid 该页面日记最大nid为lastid
     * @param int $limit  限制页面的记录数，默认为10
     * @return mixed
     */
    public function getDiaryMiniList($uid = 0, $ncid=0, $cday=0,$offset=0, $limit =10){

        if((intval($ncid) < 0) && (intval($uid) < 0))
            return ;

        $this->db->where('note.uid', $uid);
        $this->db->where('note.ncid', $ncid);
        $this->db->where('note.cday', $cday);

        $this->db->select('note_category.ncid, note_category.title, note_category.desc, note.total_comments, note.nid, note.content, note.imgurl,note.imgfile, note.imgfilezan,note.item_name, note.doctor, note.item_price,note.hospital, note.cday, note.itemday, note.pointX, note.pointY,note.oneday');
        $this->db->from('note_category');

        $this->db->join('note','note_category.ncid=note.ncid');
        $this->db->where('note.created_at <=',time());
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
        //return $this->db->last_query();
    }

    public function getDayDiaryList($uid =0, $ncid = 0, $offset =0 , $limit = 5){

        if(intval($ncid) > 0){
            $this->db->where('note.ncid', $ncid);
        }

        $this->db->where('note.uid',$uid);
        $this->db->where('note.created_at <=',time());
        $this->db->group_by('note.cday');
        $this->db->order_by('note.cday asc');
        $this->db->limit($limit, $offset);
        return $this->db->get('note')->result_array();
    }

    public function getLastPage($contentid=0, $pagesize){

        $this->db->where('nid',$contentid);
        $this->db->from('note_comment');
        return ($this->db->count_all_results() / $pagesize);
    }
    /**
     * 获取我的每个项目所对应的日记
     * @param int $lastid   传入最大nid
     * @param string $item_name  传入项目名称
     * @param int $limit 限制每次展示的图片数量
     * @return mixed    返回一个数组
     */
    public function getMyItemDiaryList($uid = 0,$item_name='', $doctor='', $yiyuan = '', $day=''){

        if((intval($uid) < 0) && (intval($uid) < 0) && empty($day)){
            return;
        }

        $this->db->where('note.uid',$uid);
        $this->db->where('note.oneday',$day);

        if(!empty($item_name)) {
            $this->db->where('note.item_name', $item_name);
        }
        if(!empty($doctor)) {
            $this->db->where('note.doctor', $doctor);
        }
        if(!empty($yiyuan)) {
            $this->db->where('note.hospital', $yiyuan);
        }
        $this->db->where('note.created_at <=',time());
        $this->db->select('note_category.ncid, note_category.title, note_category.desc, note.total_comments, note.nid, note.content, note.imgurl,note.imgfile, note.item_name, note.doctor, note.item_price,note.hospital, note.cday, note.itemday,note.pointX, note.pointY,note.oneday');
        $this->db->from('note_category');
        $this->db->join('note','note_category.ncid=note.ncid');
        return $this->db->get()->result_array();

    }

    /**
     *获取设置首页的美人计
     */
    public function getUserDiaryList($offset, $uid,$limit =5){

        $this->db->where('uid', $uid);
        $this->db->limit($limit,$offset);
        $this->db->where('created_at <=',time());

        $this->db->order_by('created_at','desc');
        return $this->db->get('note')->result_array();
    }

    /**
     *获取设置首页的美人计
     */
    public function getDiaryFrontList($offset,$type=1, $limit =5){

        $this->db->limit($limit,$offset);
        if($type == 1) {
            $this->db->where('is_front', 1);
        }
        #$this->db->where('review',1);
        $this->db->where('updated_at <=',time());
        if($type == 2){
            $this->db->order_by('updated_at','desc');
        }else{
            $this->db->order_by(' is_front desc,updated_at desc');
        }
        return $this->db->get('note')->result_array();
    }

    protected function getAge($uid = 0){
        $arr_age = array();
        $uid = intval($uid);
        if($uid <=0)
            return 0;

        $user = $this->db->query("select *From users where id='{$uid}'")->result_array();

        if(!empty($user) && strlen(intval($user[0]['age'])) >= 4){
            return (intval(date('Y')) - intval($user[0]['age']));
        }else{
            return 0;
        }
    }

    //get new and hot topics
    private function getHN($type = 1, $uid) {
        $this->db->from('wen_weibo');
        $this->db->where('wen_weibo.type != ', 4);
        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.chosen', 1);
        $this->db->where('wen_weibo.ctime <', time());
        $this->db->where('wen_weibo.wsource', 'IOS');
        $this->db->or_where('wen_weibo.wsource', 'android');

        $offset = ($this->input->get('page') - 1) * 8;
        $this->db->limit(8, $offset);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        //	$this->db->join('wen_comment', 'wen_comment.contentid = wen_weibo.weibo_id');
        $this->db->select('users.city, users.age,wen_weibo.weibo_id,wen_weibo.pageview,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.newtime,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,users.alias as uname,users.jifen, wen_weibo.views,wen_weibo.tags,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_ewibo.imgfile,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

        if ($type == 1) {
            $this->db->order_by('wen_weibo.newtime desc');
        } else {
            //$this->db->select('users.alias as uname,users.phone,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

            $this->db->order_by("wen_weibo.hots", "desc");
            $this->db->order_by("wen_weibo.comments", "desc");
        }

        $tmp = $this->db->get()->result_array();
        $res = array ();

        foreach ($tmp as $r) {
            if($uid) {
                if ($this->getstate($r['uid'],$uid)) {
                    $r['follow'] = 1;
                } else {
                    $r['follow'] = 0;
                }

            }else{
                $item['follow'] = 0;
            }
            if($r['top_start'] <= time() && $r['top_end'] >= time()){
                $r['top'] = 1;
            }else{
                $r['top'] = 0;
            }

            if($r['chosen_start'] <= time() && $r['chosen_end'] >= time()){
                $r['chosen'] = 1;
            }else{
                $r['chosen'] = 0;
            }
            $age = $this->getAge($r['uid']);
            $r['age'] = isset($age)?$this->getAge($r['uid']):'';
            $r['sex'] = 1;
            if($r['hot_start'] <= time() && $r['hot_end'] >= time()){
                $r['hot'] = 1;
            }else{
                $r['hot'] = 0;
            }
            $r['level'] = $this->getLevel($r['jifen']);
            $r['pageview'] = intval($r['views']);
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id'])>0)?$this->getZan($r['weibo_id']):0;
            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item && !is_null($item)){
                        if($item == '' || is_null($item))
                            continue;
                        $arr = array();
                        $arr['tag'] = str_replace('null','',$item);
                        $itemid = $this->getItemId($item);
                        $arr['other'] = $this->isItemLevel($itemid,1);
                        if(!empty($arr['tag']))
                            $r['tagss'][] = $arr;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }
            $rs = $this->Diary_model->get_user_by_username($r['uid']);
            $item ['basicinfo'] = $this->getBasicInfo($rs[0]);
            $r['ishot'] = 0;
            $r['ctime'] = date('Y-m-d', $r['ctime']);
            $r['newtime'] = date('Y-m-d', $r['newtime']);
            $dtypd = unserialize($r['type_data']);
            isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
            $r['haspic'] = 0;
            if (!empty ($dtypd) and isset ($dtypd['pic'])) {
                $r['haspic'] = 1;
            }
            if ($this->input->get('thumbsize')) {
                $r['thumb'] = $this->remote->thumb($r['uid'], intval($this->input->get('thumbsize')));
            } else {
                $r['thumb'] = $this->profilepic($r['uid'], 2);
            }
            if($this->input->get('width')){
                $r['images'] = $this->Plist($r['weibo_id']);
            }
            unset ($r['type_data']);
            $res[] = $r;
        }
        return $res;
    }

    /**
     *
     */
    public function getDiaryFrontListV2($offset){

        $this->db->limit(10,$offset);
        $this->db->where('created_at <=',time());
        $this->db->order_by('created_at','desc');
        return $this->db->get('note')->result_array();
    }

    /**
     *
     */
    public function getDiaryLoadingList(){

        $this->db->limit(5);
        $this->db->where('loading',1);
        $this->db->where('created_at <=',time());
        $this->db->order_by('updated_at','desc');
        return $this->db->get('note')->result_array();
    }
    /**
     * 获得日记小图显示方式
     * @param int $lastid 该页面日记最大nid为lastid
     * @param int $limit  限制页面的记录数，默认为10
     * @return mixed
     */
    public function getDiaryBackgroundList($ncid=0, $offset = 0, $limit = 10){

        if(intval($ncid) < 0)
            return ;

        $this->db->where('note.ncid',$ncid);
        $this->db->select('note.imgurl as imgurl,note.imgfile, note.nid as nid, note.ncid as ncid');
        $this->db->limit($limit,$offset);
        $this->db->order_by('note.created_at','asc');
        return $this->db->get('note')->result_array();
    }

    /**
     * 修改美人计封面背景图
     * @param int ncid
     * @param int nid
     * @param string $param
     */
    public function updateDiaryBackground($ncid = 0, $nid = 0){

        if(intval($ncid) < 0){
            return;
        }

        $imgs = $this->getDiaryImg($nid);

        if(!empty($imgs)){

            $this->db->where('ncid', $ncid);
            $is = $this->db->update('note_category', array('imgurl'=>$imgs[0]['imgurl']));
            return $is?1:0;
        }else{
            return 0;
        }

    }

    public function getDiaryImg($nid){

        if(intval($nid) < 0){
            return;
        }

        $this->db->select('imgurl');
        $this->db->where('nid',$nid);
        return $this->db->get('note')->result_array();
    }
    /**
     * 获取大图模式显示日记 暂时不用
     * @param int $lastid 该页面日记最大nid为lastid
     * @param int $limit  限制页面的记录数，默认为10
     * @return mixed
     */
    public function getDiaryMaxList($offset = 0, $limit = 10){


        $this->db->limit($limit);
        $this->db->order_by('oneday','desc');
        $this->db->limit($limit, $offset);
        return $this->db->get('note')->result_array();
    }

    /**
     * 获取每个项目所对应的日记
     * @param int $lastid   传入最大nid
     * @param string $item_name  传入项目名称
     * @param int $limit 限制每次展示的图片数量
     * @return mixed    返回一个数组
     */
    public function getDiaryNodeList($item_name='', $doctor='', $yiyuan = '', $offset=0, $limit = 10){

        $sql = "";
        if(!empty($item_name)) {

            $sql .= " and (`note`.`item_name` = '{$item_name}'";
            $sql .= $this->sx($item_name).")";
        }
        if(!empty($doctor)) {

            $sql .= " and `note`.`doctor` = '{$doctor}'";
        }
        if(!empty($yiyuan)) {

            $sql .= " and `note`.`hospital` = '{$yiyuan}'";
        }

        return $this->db->query("select *from note n where nid in (SELECT MAX(`note`.`nid`) AS nid FROM (`note`) JOIN `users` ON `note`.`uid` = `users`.`id` JOIN `note_category` ON `note`.`ncid` = `note_category`.`ncid` WHERE 1=1 and note.created_at <=".time()." $sql GROUP BY `note`.`uid`) order by n.nid desc limit $offset,$limit")->result_array();
        //return $this->db->last_query();

    }

    /**
     *获取设置首页的美人计
     */
    public function getDiaryFrontListV3($offset,$type=1, $limit =5, $item_name='', $doctor='', $yiyuan = ''){

        $this->db->limit($limit,$offset);
        if($type == 1) {
            $this->db->where('is_front', 1);
        }
        if(!empty($item_name)) {
            $this->db->where('item_name', $item_name);
        }
        if(!empty($doctor)) {
            $this->db->where('doctor', $doctor);
        }
        if(!empty($yiyuan)) {
            $this->db->where('hospital', $yiyuan);
        }
        #$this->db->where('review',1);
        $this->db->where('updated_at <=',time());
        if($type == 2){
            $this->db->order_by('created_at','desc');
        }else{
            $this->db->order_by(' is_front desc,created_at desc');
        }
        return $this->db->get('note')->result_array();
    }

    public function getDiaryNoteListV2($item_name='', $doctor='', $yiyuan = '', $offset=0, $limit = 10){

        $sql = "";
        if(!empty($item_name)) {

            $sql .= " and (`note`.`item_name` = '{$item_name}'";
            $sql .= $this->sx($item_name).")";
        }
        if(!empty($doctor)) {

            $sql .= " and `note`.`doctor` = '{$doctor}'";
        }
        if(!empty($yiyuan)) {

            $sql .= " and `note`.`hospital` = '{$yiyuan}'";
        }

        return $this->db->query("select n.uid,n.total_comments, n.imgurl,n.imgfile,n.ncid,n.cday  from note n where nid in (SELECT MAX(`note`.`nid`) AS nid FROM (`note`) JOIN `users` ON `note`.`uid` = `users`.`id` JOIN `note_category` ON `note`.`ncid` = `note_category`.`ncid` WHERE 1=1 and note.created_at <=".time()." $sql GROUP BY `note`.`uid`) order by n.nid desc limit $offset,$limit")->result_array();
        //return $this->db->last_query();

    }
    private function sx($type){

        $pid = $this->getChild($type);
        $sql = "";
        $tmpitem = array();
        if($pid){
            $sqlItem = "select name from items where pid = '{$pid}'";
            $citems = $this->db->query($sqlItem)->result_array();
            $typeSql = '';

            if(!empty($citems)){
                foreach($citems as $item){
                    $sql .= " or `note`.`item_name` = '{$item['name']}'";
                }
            }
        }
        return $sql;
    }

    /**  获取目录
     * @param string $param,9=>Q+add topic
     */
    private function getChild($type = 0){
        $tmp = array();
        $data = array();

        $sqlItem = "select id from items where name like '%".$type."%'";
        $citems = $this->db->query($sqlItem)->result_array();

        return $citems[0]['id'];
    }
    /**
     * 获取日记的总天数
     */
    public function getDiaryTotalDay($item_name='')
    {

        if (empty($item_name)) {
            return;
        }

        $this->db->where('item_name', $item_name);
        $this->db->from('note');
        return $this->db->get()->num_rows();
    }

    public function getLastID(){
        return $this->db->insert_id();
    }

    public function addEvalution($data = array()){

        if(!is_array($data) && empty($array)){
            return;
        }
        return $this->db->insert('diary_evaluation',array('uid'=>$data['uid'], 'doctor'=>$data['doctor'], 'hospital'=>$data['hospital'], 'skilled'=>$data['skilled'], 'satisfied'=>$data['satisfied'], 'evalution_content'=>$data['evaluation_content']));
    }
    public function getItemsPrice($nid=0){

        if(intval($nid) <0)
            return;
        $this->db->select('item_name,item_price,pointX,pointY');
        $this->db->where('nid',$nid);
        return $this->db->get('note_item')->result_array();
    }
    /**
     * 获取美人计详情
     * @param $id  传入日记列表
     * @return mixed
     */
    public function getDiaryDetail($id){

        if(intval($id) < 0)
            return ;
        $this->db->select('uid,ncid, content, imgfile, imgurl,created_at');
        $this->db->where('nid',$id);
        return $this->db->get('note')->result_array();
    }

    /**
     * 添加日记
     * @param array $data 传入日记需要的字段
     * @return mixed
     */
    public function saveUserDiary($data = array()){

        if(empty($data)){
            return ;
        }
        $this->db->insert('note',$data);
        return $this->db->insert_id();
    }

    /**
     * 如果没有添加选项就新加一个选项
     * @param string $params
     */
    public function addItem($item_name=''){
        $itemName = $item_name;
        $result['state'] = '000';
        $result['data'] = array();


        return $this->db->insert('new_items',array('pid'=>362,'name'=>$itemName));
    }

    public function getTopicCount($item_name = ''){

        if(empty($item_name)) {
           return;
        }
        $this->db->like('wen_weibo.tags', $item_name);
        $this->db->from('wen_weibo');

        return $this->db->get()->num_rows();
    }

    public function getDiaryTotal($item_name = ''){

        if(empty($item_name)) {
            return;
        }

        $this->sx($item_name);
        $this->db->or_where('note.item_name',$item_name);
        $this->db->from('note');
        $this->db->order_by('note.nid desc');
        return $this->db->get()->num_rows();
    }

    public function isItem($item_name=''){

        if(!empty($item_name)){
            $this->db->where('name', $item_name);
            $this->db->where('pid', 362);
            return $this->db->get('items')->num_rows();
        }

        return 0;
    }
    /**
     * 删除日记，该删除为伪删除
     * @param $id
     * @return mixed
     */
    public function delUserDiary($id){

        if(intval($id) < 0){
            return ;
        }
        $this->db->trans_start();
        $this->db->where('nid', $id);
        $this->db->delete('note');
        $this->db->where('nid', $id);
        $this->db->delete('note_comment');
        $this->db->where('contentid', $id);
        $this->db->where('type','diary');
        $this->db->delete('wen_zan');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return 0;
        }else{
            return 1;
        }
    }

    /**
     * 更新美人计
     * @param $id 美人计编号
     * @param array $data 要修改的美人计字段
     * @return mixed
     */
    public function updateUserDiary($id, $data = array()){

        if(intval($id) < 0){
            return;
        }

        if(empty($data)){
            return;
        }

        $this->db->where('nid', $id);
        return $this->db->update('note', $data);
    }

    /**
     * 保存评论
     * @param array $data 评论数据
     * @return mixed
     */
    public function saveComment($data = array()){

        if(empty($data)){
            return;
        }
        $this->db->insert('note_comment', $data);
        return $this->db->insert_id();
    }

    public function updateTotalCommnetsForNote($nid = 0){

        if(intval($nid) < 0 ){
            return;
        }

        $this->db->trans_start();
        $rstmp = $this->getCommentCount($nid);
        $this->db->where('nid',$nid);
        $this->db->update('note',array('total_comments'=>$rstmp));
        $this->db->trans_complete();
    }
    /**
     * 删除评论美人计
     * @param $id  美人计评论id
     * @return mixed
     */
    public function delcomment($id){

        if(intval($id) < 0){
            return;
        }
        $this->db->where('cid', $id);
        return $this->db->delete('note_comment');
    }

    /**
     * 获取美人计评论列表
     * @param int $lastid 该页面美人计评论id的最大的一个
     * @param int $limit  限制列表页面最大的返回的条数
     * @return mixed
     */
    public function getCommentList($nid = 0, $lastid = 0, $limit=10, $sort = "ASC"){
        
        $this->db->select('n.fromuid, up.sex, up.city, n.created_at, n.cid, n.content');
        $this->db->where('n.nid',$nid);
        $this->db->where('n.pcid',0);
        $this->db->from('note_comment n');
        $this->db->join('user_profile up','n.fromuid = up.user_id');
        $total = $this->db->get()->num_rows();


        $this->db->select('n.fromuid, up.sex, up.city, n.created_at, n.cid, n.content');
        $this->db->where('n.nid',$nid);
        $this->db->where('n.pcid',0);
        $this->db->from('note_comment n');
        $this->db->join('user_profile up','n.fromuid = up.user_id');

        if ($sort == "ASC") {
            if($lastid > 0){
                $this->db->where('n.cid >', $lastid);
                $this->db->order_by('n.cid','asc');
            }else{
                $this->db->limit($limit);
                $this->db->order_by('n.cid','asc');
            }
        } else {
                    //效率有问题
            if($lastid > 0){
                $this->db->where('n.cid <', $lastid);
                $this->db->order_by('n.cid','asc');
            }else{

                $this->db->limit($limit,  $total - 10);
                $this->db->order_by('n.cid','asc');
            }
        }
        return $this->db->get()->result_array();
        //return $this->db->last_query();
    }

    /**
     * 修改美人计评论
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function updateComment($id, $data = array()){

        $this->db->where('cid',$id);
        return $this->db->update('note_comment',$data);
    }
    // 判断评论的评论
    public function isComment($pcid = 0){

        if(intval($pcid) <= 0){
            return;
        }

        $this->db->where('cid',$pcid);

        return $this->db->get('note_comment')->num_rows();
    }

    public function getChildReply($pcid = 0){

        if(intval($pcid) < 0) {
            return array();
        }
        $this->db->select('fromusername, fromuid, content,tousername, created_at');
        $this->db->where('pcid',$pcid);
        return $this->db->get('note_comment')->result_array();
    }
    /**
     *
     *获取美人计的评论数
     *
     * @param int nid 大于等于0
     */
    public function getCommentCount($nid = 0, $type=0){

        if(intval($nid) < 0){
            return 0;
        }

        $this->db->where('nid',$nid);
        if($type == 1){
            $this->db->where('pcid',0);
        }
        return $this->db->get('note_comment')->num_rows();
    }

    /**
     * 判断同一个用户创建的项目是否重复
     * @param $title
     * @param $uid
     * @return mixed
     */
    public function isCategory($title,$uid){


        if(intval($uid) < 0 || empty($title)){
            return ;
        }

        $this->db->where('title',$title);
        $this->db->where('uid',$uid);
        $this->db->from('note_category');

        return $this->db->count_all_results();
    }

    /**
     * 返回用户自己美人计项目目录
     * @param int $uid  传入用户id
     * @return mixed
     */
    public function getMyNoteList($uid = 0, $offset=0, $limit=10){

        if(intval($uid) < 0){
            return;
        }

        $this->db->select_max('note.nid');
        $this->db->select('note_category.ncid, note_category.title, note.imgurl,note.imgfile');
        $this->db->from('note_category');
        $this->db->join('note','note_category.ncid=note.ncid');
        $this->db->where('note_category.uid',$uid);
        $this->db->group_by('note.ncid');
        $this->db->order_by('note.nid desc');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    /**
     * 添加目录
     * @param array $data 传入数组结构
     * @return mixed
     */
    public function addNoteCategory($data = array()){

        return $this->db->insert('note_category',$data);
    }
    /**
     * 获取封面图
     * @param array $data 传入数组结构
     * @return mixed
     */
    public function getFrontImg($ncid = 0){

        if(intval($ncid) <= 0){
            return;
        }
        $this->db->select('imgurl,imgfile');
        $this->db->where('ncid',$ncid);
        return $this->db->get('note_category')->result_array();
    }

    /**
     * 更新目录
     * @param int $ncid  目录编号
     * @param array $data 传入目录数据结构
     * @return mixed
     */
    public function updateNoteCategory($ncid = 0, $data = array()){

        $this->db->where('ncid',$ncid);
        return $this->db->update('note_category', $data);
    }

    /**
     * 获得项目名称
     * @param int $uid　
     * @param int $lastid
     * @return mixed
     */
    public function getMyNoteCategoryList($uid = 0 ,$offset = 0, $limit =10){

        $this->db->select('ncid,title,imgurl,imgfile,uid,operation_time');
        $this->db->where('uid',$uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('updated_at','desc');
        return $this->db->get('note_category')->result_array();
    }

    /**
     * 根据日记id删除日记
     * @param int $nid  日记编号
     */
    public function delNote($nid = 0){

        if(intval($nid) < 0){
            return ;
        }
        $this->db->where('nid', $nid);
        $res = $this->db->get('note')->result_array();

        if (!empty($res)) {

            foreach ($res as $item) {
                $this->db->delete('note_comment', array('nid' => $item['nid']));
            }
        }
        $this->db->delete('note', array('nid'=>$nid));
    }

    public function getDiaryCategoryCount($uid = 0){

        if(intval($uid) < 0){
            return ;
        }

        $this->db->where('uid',$uid);
        return $this->db->get('note_category')->num_rows();
    }

    public function getDiaryCount($ncid = 0){

        if(intval($ncid) < 0){
            return ;
        }

        $this->db->where('ncid',$ncid);
        return $this->db->get('note')->num_rows();
    }
    /**
     * 删除美人计目录
     * @param int $ncid 美人计目录id
     */
    public function delNoteCategory($ncid = 0)
    {

        if (intval($ncid) < 0) {
            return;
        }
        $id = $ncid;
        $this->db->trans_start();
        $this->db->where('ncid',$id);
        $this->db->select('nid');
        $this->db->group_by('nid');
        $tmp = $this->db->get('note')->result_array();
        if(empty($tmp)) {
            foreach($tmp as $item) {
                $this->db->where('nid', $item['nid']);
                $this->db->delete('note_comment');
                $this->db->where('contentid', $item['nid']);
                $this->db->where('type', 'diary');
                $this->db->delete('wen_zan');
            }
        }
        $this->db->where('ncid', $id);
        $this->db->delete('note_category');
        $this->db->where('ncid', $id);
        $this->db->delete('note');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return 0;
        }else{
            return 1;
        }
    }

    /**
     *判断是否有封面图片
     * $param int $ncid
     * @return boolean
     */
    public function isCategoryPic($ncid = 0){

        if(intval($ncid) < 0){
            return 0;
        }

        $this->db->where('ncid',$ncid);
        $this->db->select('imgurl');
        $tmp = $this->db->get('note_category')->result_array();

        if(!empty($tmp[0]['imgurl']) && isset($tmp[0]['imgurl'])){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 更新目录图片
     * @param int $ncid
     * @param string $path
     * @return int
     */
    public function updateCategoryPic($ncid = 0, $path = ''){

        if(intval($ncid) < 0){
            return 0;
        }

        if(!$path){
            return 0;
        }

        $this->db->where('ncid', $ncid);
        return $this->db->update('note_category', array('imgurl'=>$path));
    }

    /**
     * @param int $ncid
     * @return mixed
     */
    public function getNoteCategoryDetail($ncid = 0){

        if(intval($ncid) < 0){
            return;
        }

        $this->db->where('ncid',$ncid);
        $this->db->select('note_category.title,note_category.ncid,note_category.desc,note_category.operation_time, note_category.is,note_category.imgurl, note_category.imgfile,note_category.leftimgurl,note_category.leftimgfile,note_category.rightimgurl,note_category.rightimgfile,note_category.is,note_category.uid, users.alias,users.username');
        $this->db->from('note_category');
        $this->db->join('users','note_category.uid=users.id');
        return $this->db->get()->result_array();
    }
    /**
     * 添加点赞
     * @param $contentid
     * @param $uid
     * @return int
     */
    public function addZan($contentid, $uid,$type='diary'){


        $touid = 0;

        if($type == 'topic'){
            $this->db->where('weibo_id', $contentid);
            $tmp = $this->db->get('wen_weibo')->result_array();
        }
        if($type == 'diary'){
            $this->db->where('nid', $contentid);
            $tmp = $this->db->get('note')->result_array();
        }

        if($type == 'topic_comments'){
            $this->db->where('id', $contentid);
            $tmp = $this->db->get('wen_comment')->result_array();
        }

        if($type == 'diary_comments'){
            $this->db->where('cid', $contentid);
            $tmp = $this->db->get('note_comment')->result_array();
        }

        if($type == 'topic'){
            $rs = $this->getMyZanTopic($contentid);
            $touid = $rs[0]['uid'];

        }

        if($type == 'diary'){
            $rs = array();
            $rs = $this->getDiaryUser($contentid);
            $touid = $rs[0]['uid'];
        }

        if($type == 'diary_comments'){
            $rs = array();
            $rs = $this->getDiaryCommentsUser($contentid);
            $touid = $rs[0]['uid'];
        }

        if($type == 'topic_comments'){

            $rs = array();
            $rs = $this->getTopicCommentsUser($contentid);
            $touid = $rs[0]['uid'];
        }

        $data = array (
            'type' => $type,
            'contentid' => $contentid,
            'uid' => $uid,
            'touid' => $touid,
            'cTime' => time());
        $this->db->insert('wen_zan', $data);
        $where_condition = array (
            'type' => $type,
            'contentid' => $contentid);
        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;
        return $num + intval($tmp[0]['zan']);
    }

    /**
     * @param $contentid
     * @param $type ,topic,diary,topic_comments,diary_comments 默认为美人计
     * @return int
     */
    public function getZanNum($contentid,$type = 'diary'){

        $where_condition = array (
            'type' => $type,
            'contentid' => $contentid);
        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;

        return $num;
    }

    public function getDiaryUser($contentid){

        if(intval($contentid) < 0)
            return ;

        $this->db->select('uid');
        $this->db->where('nid',$contentid);
        return $this->db->get('note')->result_array();
    }

    public function getDiaryCommentsUser($contentid){

        if(intval($contentid) < 0)
            return ;

        $this->db->select('fromuid as uid');
        $this->db->where('cid',$contentid);
        return $this->db->get('note_comment')->result_array();
    }

    public function getTopicCommentsUser($contentid){

        if(intval($contentid) < 0)
            return ;

        $this->db->select('fuid as uid');
        $this->db->where('id',$contentid);
        return $this->db->get('wen_comment')->result_array();
    }
    public function getZanMyList($uid = 0,$offset=0, $limit = 10){

        if(intval($uid) < 0){
            return;
        }
        $where = "(type='diary' or type='topic') and touid='{$uid}'";
        $this->db->where($where);
        $this->db->order_by('cTime','desc');
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_zan')->result_array();
    }

    public function getNotZanMyList($uid = 0,$offset=0, $limit = 10){

        if(intval($uid) < 0){
            return;
        }
        $where = "(type='diary' or type='topic') and is_read=0 and touid='{$uid}'";
        $this->db->where($where);
        $this->db->order_by('cTime','desc');
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_zan')->result_array();
    }
    /**
     *
     */
    public function getMyZanTopic($tid, $offset=0, $limit=10){
        if(intval($tid) < 0){
            return;
        }

        $this->db->select('content, uid, type_data, imgfile');
        $this->db->where('weibo_id', $tid);
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_weibo')->result_array();
    }

    public function getMyZanDiary($nid, $offset=0, $limit=10){
        if(intval($nid) < 0){
            return;
        }

        $this->db->select('content, uid, imgurl, imgfile');
        $this->db->where('nid', $nid);
        $this->db->limit($limit, $offset);
        return $this->db->get('note')->result_array();
    }

    public function getMyZan($uid, $offset=0, $limit=10){

        if(intval($uid) < 0){
            return;
        }

        $where = "(type='diary' or type='topic') and uid='{$uid}'";
        $this->db->where($where);
        $this->db->order_by('cTime', 'desc');
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_zan')->result_array();
    }

    public function Plist($tid) {

        if(intval($tid) < 0){
            return;
        }
        $id = $tid;
        $this->db->select('id,savepath,height,width,info');
        $this->db->where('attachId', $id);
        $this->db->from('topic_pics');
        $this->db->order_by('order','ASC');
        $res = $this->db->get()->result_array();
        $rt = array ();
        //$rt['item'] = $this->db->last_query();

        //show pic width
        $width = 105;

        foreach ($res as $r) {

            $arr_url = explode('/',$r['savepath']);
            $url = '';
            if(isset($arr_url[1])){

                $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x640/',$r['savepath']);
            }

            $r['savepath'] = $this->remote->show320($url, $width);
            $rt[] = $r;
        }
        return $rt;
    }
    /**
     * 取消点赞
     * @param $contentid
     * @param $uid
     * @return int
     */
    public function cancelZan($contentid, $uid=0, $type='diary'){

        if(intval($contentid) < 0){
            return ;
        }

        if(intval($uid) < 0){
            return ;
        }

        if($type == 'topic'){
            $this->db->where('weibo_id', $contentid);
            $tmp = $this->db->get('wen_weibo')->result_array();
        }
        if($type == 'diary'){
            $this->db->where('nid', $contentid);
            $tmp = $this->db->get('note')->result_array();
        }

        if($type == 'topic_comments'){
            $this->db->where('id', $contentid);
            $tmp = $this->db->get('wen_comment')->result_array();
        }

        if($type == 'diary_comments'){
            $this->db->where('cid', $contentid);
            $tmp = $this->db->get('note_comment')->result_array();
        }


        $this->db->query("delete from wen_zan where type='$type' and contentid='{$contentid}' and uid='{$uid}' limit 1");

        $where_condition = array (
            'type' => $type,
            'contentid' => $contentid);

        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;
        return $num + intval($tmp[0]['zan']);
    }
    /**
     * 获取点赞数据量
     * @return mixed
     */
    public function getZan($contentid = 0, $type = 'diary'){
        if($type == 'topic'){
            $this->db->where('weibo_id', $contentid);
            $tmp = $this->db->get('wen_weibo')->result_array();
        }
        if($type == 'diary'){
            $this->db->where('nid', $contentid);
            $tmp = $this->db->get('note')->result_array();
        }

        if($type == 'topic_comments'){
            $this->db->where('id', $contentid);
            $tmp = $this->db->get('wen_comment')->result_array();
        }

        if($type == 'diary_comments'){
            $this->db->where('cid', $contentid);
            $tmp = $this->db->get('note_comment')->result_array();
        }

        if(isset($tmp[0]['zan']) && intval($tmp[0]['zan']) > 0){
            $where_condition = array ('type' => $type, 'contentid' => $contentid);
            return $this->db->get_where('wen_zan', $where_condition)->num_rows() + intval($tmp[0]['zan']);
        }else{
            $zan = rand(0,0);
            if($type == 'topic') {
                $this->db->where('weibo_id', $contentid);
                $this->db->update('wen_weibo', array('zan' => $zan));
            }

            if($type == 'diary'){
                $this->db->where('nid', $contentid);
                $this->db->update('note', array('zan' => $zan));
            }

            if($type == 'topic_comments'){
                $this->db->where('id', $contentid);
                $this->db->update('wen_comment', array('zan' => $zan));
            }

            if($type == 'diary_comments'){
                $this->db->where('cid', $contentid);
                $this->db->update('note_comment', array('zan' => $zan));
            }

            $where_condition = array ('type' => 'topic', 'contentid' => $contentid);
            return $this->db->get_where('wen_zan', $where_condition)->num_rows() + $zan;
        }
    }

    /**
     * @param $uid
     * @return mixed
     */
    function get_user_by_username($uid)
    {
        $this->db->where('id', $uid);
        //sex 2男 1女
        $this->db->select('users.username, users.alias,users.city,users.age,users.jifen, users.daren, users.city, users.age, user_profile.sex');
        $this->db->from('users');
        $this->db->join('user_profile','users.id=user_profile.user_id');
        return $this->db->get()->result_array();
    }
    /**
     * 获取点赞来自用户列表
     * @param int nid 传日记id
     * @return mixed
     */
    public function getFromUserZan($nid = 0, $offset = 0, $limit = 10){

        if(intval($nid) < 0) {
            return;
        }

        $where_condition = array ('type' => 'diary', 'contentid' => $nid);
        $this->db->select('uid');
        $this->db->order_by('uid','asc');
        $this->db->limit($limit, $offset);
        return $this->db->get_where('wen_zan', $where_condition)->result_array();
    }
    /**
     * 判断点赞数量 settime('',1111)
     * @param $uid 用户编号
     * @param int $contentid 用户内容编号
     * @return boolean  返回boolean
     */
    public function isZan($uid,$contentid = 0, $type='diary'){

        $condition = array (
            'type' => $type,
            'uid' => $uid,
            'contentid' => $contentid
        );

        $num = $this->db->get_where('wen_zan', $condition)->num_rows();

        if ($num > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 判断是否关注
     *
     * @param string $param
     */
    public function getstate($uid, $touid) {

        if (intval($uid) < 0) {
            return 0;
        }

        $condition = array (
            'uid' => $uid,
            'fid' => $touid,
            'type'=> 8
        );
        $tmp = $this->db->get_where('wen_follow', $condition)->num_rows();
        if ($tmp > 0) {
            $result['follow'] = '1';
        }else{
            $result['follow'] = '0';
        }

        return $result['follow'];
    }
    /**
     * 获取每个美人计目录最后一条美人计的标签
     * @param $uid
     * @return int
     */

    public function getLastTagsForCategory($ncid = 0){

        if(intval($ncid) <= 0){
            return ;
        }
        $this->db->select('nid,doctor,hospital,item_name,item_price,pointX,pointY');
        $this->db->where('ncid',$ncid);
        $this->db->order_by('nid', 'desc');
        $this->db->limit(1);
        $tmp = $this->db->get('note')->result_array();

        if($tmp){
            $temp = $this->db->query("select item_name, item_price, pointX,pointY From note_item where nid={$tmp[0]['nid']}")->result_array();
            $tmp[0]['tags'] = isset($temp)?$temp:array();
            return $tmp[0];
        }else{
            return;
        }

    }

    /**
     * 我的粉丝数量
     * @param $uid
     * @return int
     */
    public function getFunCount($uid, $type =8){

        if (intval($uid) < 0) {
            return 0;
        }

        $this->db->where('uid',$uid);
        $this->db->where('type',$type);
        $this->db->from('wen_follow w');
        $this->db->join('users u', 'w.fid=u.id');
        return $this->db->get()->num_rows();
    }

    /**
     * 我的关注数量
     * @param $uid
     * @return int
     */
    public function getFollowerCount($uid){
        if (intval($uid) < 0) {
            return 0;
        }

        $this->db->where('fid',$uid);
        $this->db->where('type',8);
        $this->db->from('wen_follow w');
        $this->db->join('users u', 'w.uid=u.id');
        return $this->db->get()->num_rows();
    }

    public function getMySendCommnetsList($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }
        $this->db->select('contentid, comment, cTime');
        $this->db->where('fuid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('ctime desc');
        return $this->db->get('wen_comment')->result_array();
    }

    public function getMySendCommnetsListV3($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }
        $this->db->select('contentid, comment, cTime,fuid,touid');
        $this->db->or_where('fuid', $uid);
        $this->db->or_where('touid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('ctime desc');
        return $this->db->get('wen_comment')->result_array();
    }

    public function getMyFollowCommentsList($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }

        $this->db->select('id as itemid,contentid, comment, fuid, cTime, is_read');
        $this->db->where('touid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('is_read desc,ctime desc');
        return $this->db->get('wen_comment')->result_array();
    }
    public function getMyFollowCommentsListV2($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }

        $this->db->select('id as itemid,contentid, comment, fuid, cTime, is_read');
        $this->db->where('touid', $uid);
        $this->db->where('is_read',0);
        $this->db->limit($limit, $offset);
        $this->db->order_by('ctime desc');
        return $this->db->get('wen_comment')->result_array();
    }
    public function getDiaryMySendCommnetsListV3($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }
        $this->db->select('nid, content, touid,fromuid, created_at');
        $this->db->or_where('fromuid', $uid);
        $this->db->or_where('touid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at desc');
        return $this->db->get('note_comment')->result_array();
    }
/*
    public function getDiaryMySendCommnetsListV3($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }
        $this->db->select('nid, content, touid,fromuid, created_at');
        $this->db->or_where('fromuid', $uid);
        $this->db->or_where('touid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at desc');
        return $this->db->get('note_comment')->result_array();
    }
*/
    public function getDiaryMySendCommnetsList($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }
        $this->db->select('nid, content, touid, created_at');
        $this->db->where('fromuid', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at desc');
        return $this->db->get('note_comment')->result_array();
    }
    public function getDiaryMyFollowCommentsList($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }

        $this->db->select('nid, content, fromuid, fromusername, created_at, is_read');
        $this->db->where('touid', $uid);
        $this->db->or_where('tousername', $uid);
        $this->db->limit($limit, $offset);
        $this->db->order_by('is_read asc, created_at desc');
        return $this->db->get('note_comment')->result_array();
    }
    public function getDiaryMyFollowCommentsListV2($uid = 0, $offset=0, $limit = 10){
        if(intval($uid) < 0){
            return;
        }

        $this->db->select('cid as itemid,nid, content, fromuid, fromusername, created_at, is_read');
        $this->db->where('touid', $uid);
        $this->db->where('is_read',0);
        $this->db->limit($limit, $offset);
        $this->db->order_by('created_at desc');
        return $this->db->get('note_comment')->result_array();
    }
    public function getLastNote($uid=0, $ncid=0)
    {

        if ((intval($uid) < 0) && (intval($ncid) < 0)) {
            return;
        }

        $this->db->where('uid',$uid);
        $this->db->where('ncid',$ncid);
        $this->db->order_by('created_at desc');
        $this->db->limit(1);
        return $this->db->get('note')->result_array();
        //return $this->db->last_query();
    }

    public function getItemLastNote($uid=0, $item_name=''){

        if ((intval($uid) < 0) && (empty($item_name))) {
            return;
        }

        $this->db->where('uid',$uid);
        $this->db->where('item_name',$item_name);
        $this->db->order_by('created_at desc');
        $this->db->limit(1);
        return $this->db->get('note')->result_array();
    }

    public function isItemLevel($itemid = 0, $i = 1){
        if(intval($itemid) < 0){
            return;
        }

        $this->db->where('id',$itemid);
        $this->db->select('id,pid');
        $rs = $this->db->get('new_items')->result_array();

        if($rs[0]['pid'] == 0){
            return $i;
        }else if($rs[0]['pid'] == 362){
            return 50;
        }else{
            $i = $i + 1;
            return $this->isItemLevel($rs[0]['pid'],$i);
        }
    }

    public function getItemId($name){
        if(empty($name)){
            return ;
        }
        $this->db->flush_cache();
        $this->db->where('name',$name);
        $this->db->select('id');
        $rs = $this->db->get('new_items')->result_array();

        return $rs[0]['id'];
    }

}

?>