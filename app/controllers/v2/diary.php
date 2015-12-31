<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class diary extends MY_Controller {
    private $notlogin = true;
    private $uid = 0;
    private $user_daren = 0;
    public function __construct() {

        parent :: __construct();

        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
            $this->user_daren = 1;//$this->wen_auth->get_user_daren();
        } else {
            $this->notlogin = true;
        }

        $this->load->library('filter');
        $this->load->library('alicache');
        $this->load->model('auth');
        $this->load->model('remote');
        $this->load->helper('file');
        $this->load->model('Diary_model');
        $this->load->model('Score_model');

    }


    //profile pic
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1 :
                return $this->remote->thumb($id, '36');
            case 0 :
                return $this->remote->thumb($id, '250');
            case 2 :
                return $this->remote->thumb($id, '120');
            default :
                return $this->remote->thumb($id, '120');
                break;
        }
    }


    /**
     * 搜索日记　
     * @param string $p 搜索的内容
     * @param int page 默认为１
     * @param string $param
     */
    public function getSearch($param = ""){

        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = intval($page -1)*10;
        $p = $this->input->get('p');
        $result['state'] = '000';
        $result['data'] = array();

        if(!empty($p)){

            $rs = $this->Diary_model->jserach($p, $offset);

            if(!empty($rs)){
                foreach($rs as $item){
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
                    $item['thumb'] = $this->remote->thumb($item['uid']);
                    $temp = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['zanNum'] = ($this->Diary_model->getZan($item['nid'])>0)?$this->Diary_model->getZan($item['nid']):0;
                    $item['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                    $item['username'] = $item['username'] ? $item['username'] : '';
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    $result['data'][] = $item;
                }
            }
        }

        echo json_encode($result);
    }
    /**
     * 获取全部美人
     */
    public function getAllDiary(){

        $offset = intval($this->input->get('page')-1)*10;
        /*$item_name = $this->input->get('item_name');
        $doctor = $this->input->get('doctor');
        $yiyuan = $this->input->get('yiyuan');*/
        //$uid = $this->input->get('uid');
        $result['state'] = '000';
        $item_name = '';
        $doctor = '';
        $yiyuan = '';

        if(true){
            $result['data'] = $this->Diary_model->getDiaryNoteListV2($item_name,$doctor,$yiyuan, $offset);
            //$result['topicTotal'] = $this->Diary_model->getTopicCount($item_name)?$this->Diary_model->getTopicCount($item_name):0;
            //$result['diaryTotal'] = $this->Diary_model->getDiaryTotal($item_name)?$this->Diary_model->getDiaryTotal($item_name):0;
            if(!empty($result['data'])){

                foreach($result['data'] as $key=>$item){
                    $item['thumb'] = $this->remote->thumb($item['uid'], '36');
                    //$day = $this->Diary_model->getDiaryTotalDay($item['item_name']);
                    $userInfo = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['alias'] = $userInfo[0]['alias'] ? $userInfo[0]['alias']:$userInfo[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                        $item['alias'] = substr($item['alias'],0,4).'****';
                    }
                    $item['sImgUrl'] = $this->remote->show($item['imgurl'], 80);
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    unset($item['username']);

                    $item['day'] = '第'.$item['cday'].'天';

                    $result['data'][$key] = $item;
                }
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    /**
     * 获得我的美人计列表表详情
     * @param int ncid ncid 大于　０
     * @param string $param
     * android 版本
     */
    public function getDiaryList($param = ''){

        $offset = intval($this->input->get('page')-1)*100;
        $ncid = $this->input->get('ncid');
        $uid = $this->input->get('uid');
        $result['state'] = '000';
        $result['data'] = array();

        if(intval($uid) > 0){

            $tmpDayDiaryList = $this->Diary_model->getDayDiaryList($uid ,$ncid, $offset);

            $arr_item = array();
            if(!empty($tmpDayDiaryList)){

                foreach($tmpDayDiaryList as $k=>$item) {

                    $tmp = $this->Diary_model->getDiaryMiniList($uid,$ncid, $item['cday']);

                    foreach ($tmp as $key => $citem) {

                        $citem['day'] = '第'.$citem['cday'].'天';

                        $arr_item[$k]['day'] = $citem['day'];
                        unset($citem['title']);
                        unset($citem['desc']);
                        unset($citem['day']);
                        unset($citem['desc']);
                        unset($citem['title']);
                        unset($citem['ncid']);
                        unset($citem['oneday']);
                        if(isset($this->uid)) {
                            $is = $this->Diary_model->isZan($this->uid, $citem['nid']);
                            $citem['isZan'] = $is?1:0;
                        }else{
                            $citem['isZan'] = 0;
                        }
                        $itemid = $this->Diary_model->getItemId($item['item_name']);
                        $item['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        $citem['zanNum'] = ($this->Diary_model->getZan($citem['nid'])>0)?$this->Diary_model->getZan($citem['nid']):0;
                        $citem['sImgUrl'] = $this->remote->show($citem['imgurl'], 80);
                        $citem['imgurl'] = $this->remote->show320($citem['imgurl']);
                        $citem['created_at'] = date('Y-m-d H:i',$citem['created_at']);
                        $itemid = $this->Diary_model->getItemId($item['item_name']);
                        $item['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        $item['thumb'] = $this->profilepic($uid);
                        $arr_item[$k]['all'][] = $citem;
                    }

                }
                $result['data'] = $arr_item;
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function getUserDiaryList($param = ''){
        $offset = intval($this->input->get('page')-1)*5;
        $uid = $this->input->get('uid');
        $result['state'] = '000';
        $result['data'] = array();
        if(intval($uid) > 0){

            $this->db->where('uid',$uid);
            $tmpDayDiaryList = $this->db->get('note')->result_array();

            $arr_item = array();
            if(!empty($tmpDayDiaryList)){
                foreach($tmpDayDiaryList as $k=>$citem) {
                    $citem['day'] = '第'.$citem['cday'].'天';
                    $arr_item[$k]['day'] = $citem['day'];
                    unset($citem['title']);
                    unset($citem['desc']);
                    unset($citem['day']);
                    unset($citem['desc']);
                    unset($citem['title']);
                    unset($citem['ncid']);
                    unset($citem['oneday']);
                    if(isset($this->uid)) {
                        $is = $this->Diary_model->isZan($this->uid, $citem['nid']);
                        $citem['isZan'] = $is?1:0;
                    }else{
                        $citem['isZan'] = 0;
                    }
                    $citemid = $this->Diary_model->getItemId($citem['item_name']);
                    $citem['other'] = $this->Diary_model->isItemLevel($citemid,1);
                    $citem['zanNum'] = ($this->Diary_model->getZan($citem['nid'])>0)?$this->Diary_model->getZan($citem['nid']):0;
                    $citem['sImgUrl'] = $this->remote->show($citem['imgurl'], 80);
                    $citem['imgurl'] = $this->remote->show320($citem['imgurl']);
                    $citem['daren'] =  1;//$this->user_daren;
                    $citemid = $this->Diary_model->getItemId($citem['item_name']);
                    $citem['other'] = $this->Diary_model->isItemLevel($citemid,1);
                    $citem['thumb'] = $this->profilepic($uid);
                    $citem['created_at'] = date('Y-m-d H:i',$citem['created_at']);
                    $citem['updated_at'] = date('Y-m-d H:i',$citem['updated_at']);
                    $arr_item[$k]['all'][] = $citem;


                }
                $result['data'] = $arr_item;
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    /**
     * 获得我的美人计列表表详情
     * @param int ncid ncid 大于　０
     * @param string $param
     * ios 版本
     */
    public function getDiaryListIos($param = ''){

        $offset = intval($this->input->get('page')-1)*5;
        $ncid = $this->input->get('ncid');
        $uid = $this->input->get('uid');
        $result['state'] = '000';
        $result['data'] = array();
        if(intval($uid) > 0){

            $tmpDayDiaryList = $this->Diary_model->getDayDiaryList($uid ,$ncid, $offset);

            $arr_item = array();
            if(!empty($tmpDayDiaryList)){

                foreach($tmpDayDiaryList as $k=>$item) {

                    $tmp = $this->Diary_model->getDiaryMiniList($uid,$ncid, $item['cday']);

                    foreach ($tmp as $key => $citem) {

                        $citem['day'] = '第'.$citem['cday'].'天';

                        $arr_item[$item['oneday']]['day'] = $citem['day'];
                        unset($citem['title']);
                        unset($citem['desc']);
                        unset($citem['day']);
                        unset($citem['desc']);
                        unset($citem['title']);
                        unset($citem['ncid']);
                        unset($citem['oneday']);
                        if(isset($this->uid)) {
                            $is = $this->Diary_model->isZan($this->uid, $citem['nid']);
                            $citem['isZan'] = $is?1:0;
                        }else{
                            $citem['isZan'] = 0;
                        }
                        $itemid = $this->Diary_model->getItemId($item['item_name']);
                        $item['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        $citem['zanNum'] = ($this->Diary_model->getZan($citem['nid'])>0)?$this->Diary_model->getZan($citem['nid']):0;
                        $citem['sImgUrl'] = $this->remote->show($citem['imgurl'], 80);
                        $citem['imgurl'] = $this->remote->show320($citem['imgurl']);
                        $arr_item[$item['oneday']]['all'][] = $citem;
                    }

                }
                $result['data'] = $arr_item;
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '请传入用户id!';
        }
        $result['count'] = count($result['data']);
        $result['sort'] = 1;
        echo json_encode($result);
    }
    /**
     * 获得目录详情
     * @param int ncid ncid 大于　０
     * @param string $param
     */
    public function getNoteCategoryDetail($param = ""){

        $ncid = $this->input->get('ncid');
        $result['data'] = "";
        $result['state'] = '000';
        if(intval($ncid) > 0){
            $rstemp = $this->Diary_model->getNoteCategoryDetail($ncid);
            $arr_tmp = array();

            if(!empty($rstemp)){
                foreach($rstemp as $item){
                    $item['thumb'] = $this->remote->thumb($item['uid'], '36');

                    $item['alias'] = $item['alias'] ? $item['alias']:$item['username'];
                    if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                        $item['alias'] = substr($item['alias'],0,4).'****';
                    }
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    $item['desc'] = $item['desc'] ? $item['desc'] : '';
                    unset($item['username']);
                    $arr_tmp = $item;
                }
                $result['data'] = $arr_tmp;
            }
        }else{
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    /**
     * 获得美人计封面背景图
     * @param string $param
     */
    public function getDiaryBackgroundList($param = ''){

        $offset = intval($this->input->get('page')-1)*10;
        $ncid = $this->input->get('ncid');
        //$this->uid = $this->input->get('uid');
        $result['state'] = '000';
        $result['data'] = array();
        if(intval($ncid) > 0){

            $tmp = $this->Diary_model->getDiaryBackgroundList($ncid, $offset);

            if(!empty($tmp)){

                foreach($tmp as $item){

                    $item['imgurl'] = $this->remote->show($item['imgurl']);
                    $result['data'][]=$item;
                }
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    /**
     * 修改美人计封面背景图
     * @param int ncid
     * @param int nid
     * @param string $param
     */
    public function updateDiaryBackground($param = ''){

        $nid = $this->input->post('nid');
        $ncid = $this->input->post('ncid');

        $result['state'] = '000';
        $result['data'] = array();
        if($this->uid){
            $result['data'] = $this->Diary_model->updateDiaryBackground($ncid, $nid);
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    /**
     * @param int lastid 该页面里面最大的日记编号
     * @param string item_name 项目名字
     * @param int $doctor　专家名字
     * @param int $yiyuan  医院名称
     * @param string $param
     */
    public function getDiaryNodeList($param = ''){

        $offset = intval($this->input->get('page')-1)*10;
        $item_name = $this->input->get('item_name');
        $doctor = $this->input->get('doctor');
        $yiyuan = $this->input->get('yiyuan');
        //$uid = $this->input->get('uid');
        $result['state'] = '000';

        if($item_name || $doctor || $yiyuan){
            $result['data'] = $this->Diary_model->getDiaryNodeList($item_name,$doctor,$yiyuan, $offset);
            $result['topicTotal'] = $this->Diary_model->getTopicCount($item_name)?$this->Diary_model->getTopicCount($item_name):0;
            $result['diaryTotal'] = $this->Diary_model->getDiaryTotal($item_name)?$this->Diary_model->getDiaryTotal($item_name):0;
            if(!empty($result['data'])){

                foreach($result['data'] as $key=>$item){
                    $item['thumb'] = $this->remote->thumb($item['uid'], '36');
                    //$day = $this->Diary_model->getDiaryTotalDay($item['item_name']);
                    $userInfo = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['alias'] = $userInfo[0]['alias'] ? $userInfo[0]['alias']:$userInfo[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                        $item['alias'] = substr($item['alias'],0,4).'****';
                    }
                    $item['sImgUrl'] = $this->remote->show($item['imgurl'], 80);
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    unset($item['username']);

                    $item['day'] = '第'.$item['cday'].'天';

                    $result['data'][$key] = $item;
                }
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    /**
     * 获取用户信息
     * @param int uid // 根据用户ｉｄ获取用户信息
     */
    public function getDiaryUserInfo($param = ""){

        $uid = $this->input->get('uid');

        $result['state'] = '000';
        $result['state'] = '000';
        $result['data'] = array();
        if($uid){
            $temp = $this->Diary_model->get_user_by_username($uid);
            if(!empty($temp)){
                foreach($temp as $item){
                    $item['thumb'] = $this->remote->thumb($uid);
                    $item['username'] = $item['username'] ? $item['username'] : $item['alias'];
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    $item['userBackgroundImg'] = "";
                    unset($item['alias']);
                    $result['data'][] = $item;
                }
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
        echo json_encode($result);
    }

    public function getDiaryInfo(){

        $nid = $this->input->get('nid');
        $nid = $nid ? intval($nid) : 0 ;
        $result['state'] = '000';
        $result['data'] = array();
        if($nid > 0 ){
            $rs = $this->Diary_model->getDiaryDetail($nid);

            if(!empty($rs)){

                foreach($rs as $item){
                    $temp = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                    $item['username'] = $item['username'] ? $item['username'] : '';
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    $item['thumb'] = $this->remote->thumb($item['uid']);
                    $item['created_at'] = date('Y-m-d',$item['created_at']);
                    unset($item['ncid']);
                    unset($item['imgurl']);
                    unset($item['content']);
                    unset($item['item_name']);
                    unset($item['item_price']);
                    unset($item['doctor']);
                    unset($item['hospital']);
                    unset($item['review']);
                    unset($item['setting']);
                    unset($item['cday']);
                    unset($item['itemday']);
                    unset($item['oneday']);
                    unset($item['total_comments']);
                    unset($item['pointX']);
                    unset($item['pointY']);
                    unset($item['updated_at']);
                    unset($item['isdel']);
                    unset($item['oper']);
                    $result['data'] = $item;
                }
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    /**
     * @param int uid 用户编号
     * @param string item_name 项目名字
     * @param int $doctor　专家名字
     * @param int $yiyuan  医院名称
     * @param int page 页数
     */
    public function getMyItemDiaryList($param = ''){

        $offset = intval($this->input->get('page')-1)*2;
        $uid = $this->input->get('uid');
        $item_name = $this->input->get('item_name')?$this->input->get('item_name'):'';
        $doctor = $this->input->get('doctor')?$this->input->get('doctor'):'';
        $yiyuan = $this->input->get('yiyuan')?$this->input->get('yiyuan'):'';


        $result['state'] = '000';
        $result['data'] = array();

        if(intval($uid) > 0){

            $tmpDayDiaryList = $this->Diary_model->getDayDiaryList($uid ,0, $item_name, $doctor, $yiyuan,$offset);

            $arr_item = array();
            if(!empty($tmpDayDiaryList)){

                foreach($tmpDayDiaryList as $k=>$item) {
                    $tmp = $this->Diary_model->getMyItemDiaryList($uid,$item_name,$doctor,$yiyuan,$item['oneday']);

                    foreach ($tmp as $key => $citem) {

                        $citem['day'] = '第'.$citem['cday'].'天';
                        $tmp = $citem['itemday'];
                        $arr_item[$tmp]['day'] = $citem['day'];
                        unset($citem['title']);
                        unset($citem['desc']);
                        unset($citem['day']);
                        unset($citem['desc']);
                        unset($citem['title']);
                        unset($citem['ncid']);
                        unset($citem['oneday']);
                        if(isset($this->uid) && $this->uid) {
                            $is = $this->Diary_model->isZan($this->uid, $citem['nid']);
                            $citem['isZan'] = $is?1:0;
                        }else{
                            $citem['isZan'] = 0;
                        }
                        $citem['zanNum'] = ($this->Diary_model->getZan($citem['nid'])>0)?$this->Diary_model->getZan($citem['nid']):0;
                        $citem['sImgUrl'] = $this->remote->show($citem['imgurl'], 80);
                        $citem['imgurl'] = $this->remote->show320($citem['imgurl']);
                        $arr_item[$tmp]['all'][] = $citem;
                    }

                }
                $result['data'][] = $arr_item;
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    /**
     * 获取日记详情
     * @param int $nid
     * @param string $param
     */
    public function getDiaryDetail($param = ''){

        $nid = $this->input->get('nid');
        $nid = $nid ? intval($nid) : 0 ;
        $result['state'] = '000';
        $result['data'] = array();
        if($nid > 0 ){
            $rs = $this->Diary_model->getDiaryDetail($nid);

            if(!empty($rs)){

                foreach($rs as $item){
                    $temp = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['username'] = $temp[0]['alias'] ? $temp[0]['alias'] : $temp[0]['username'];
                    $item['username'] = $item['username'] ? $item['username'] : '';
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    $item['created_at'] = date('Y-m-d',$item['created_at']);
                    $item['countZan'] = $this->Diary_model->getZan($nid);
                    $item['zanNum'] = $this->Diary_model->getZan($nid);
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    $item['thumb'] = $this->remote->thumb($item['uid']);
                    $item['uid'] = $item['uid'];
                    $result['data'][] = $item;

                    $this->db->where('nid',$nid);
                    $this->db->update('note',array('pageview'=>(intval($item['pageview'])+1)));
                }

            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    /**
     *获取美人计评论总页数
     *＠params int nid 传美人计id
     */

    public function getCommentListPageSize($param = ""){
        $nid = $this->input->get('nid');
        $result['state'] = '000';
        $result['data'] = array();


        if(intval($nid) > 0){

            $result['data']['total_comments'] = $this->Diary_model->getCommentCount($nid);
            $result['data']['page_size'] = ceil(intval($result['data']['total_comments'])/10);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
        echo json_encode($result);
    }
    /**
     * 添加美人计　当为修改的时候可以只传content, type, nid
     * @param string $param
     * @param int ncid
     * @param string content　　
     * @param string item_name
     * @param float item_price
     * @param string doctor
     * @param string hospital
     * @param file $diaryPic
     * @param boolean type 当type为１的时候执行修改操作,为０的时候执行添加操作
     * @param int nid 当type为１的时候nid 参数必填否则可以不填写
     */
    public function saveUserDiary($param = ''){

        $ncid = $this->input->post('ncid');
        $content = $this->input->post('content') ? $this->input->post('content') : '';
        $item_name = $this->input->post('item_name') ? $this->input->post('item_name') : '';
        $item_price = $this->input->post('item_price') ? $this->input->post('item_price'): '';
        $doctor = $this->input->post('doctor') ? $this->input->post('doctor') : '';
        $hospital = $this->input->post('hospital') ? $this->input->post('hospital') : '';
        $pointX = $this->input->post('x')?$this->input->post('x'):'0.00';
        $pointY = $this->input->post('y')?$this->input->post('y'):'0.00';
        $type = $this->input->post('type')?$this->input->post('type'):0;

        $imgurl = '';

        $check = 0;
        $setting = 0;

        $result['state'] = '000';


        if(!empty($content) && $this->uid){
            if($type != 1) {
                if (isset ($_FILES['diaryPic']['name']) && $_FILES['diaryPic']['name'] != '') {
                    $result['notice'] = '美人记发布成功！';
                    $imgurl = date('Y') . '/' . date('m') . '/' . date('d');
                    $ext = '.jpg';
                    $filename = uniqid() . rand(1000, 9999) . $ext;
                    $imgurl .= '/' . $filename;
                    $ptmp = getimagesize($_FILES['diaryPic']['tmp_name']);
                    if (!$this->remote->cp($_FILES['diaryPic']['tmp_name'], $filename, $imgurl, array(
                        'width' => 600,
                        'height' => 600
                    ), true)
                    ) {

                        $result['state'] = '001';
                        $result['notice'] = '图片上传失败！';
                        echo json_encode($result);
                        exit;
                    }
                }
                $isItem = $this->Diary_model->isItem($item_name);
                if($isItem){
                    $this->Diary_model->addItem($item_name);
                }
                //计算这个目录第几天
                $lastNote = $this->Diary_model->getLastNote($this->uid,$ncid);
                //计算这个项目第几天
                $itemLastNote = $this->Diary_model->getItemLastNote($this->uid,$item_name);

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

                if($doctor){
                    $this->Score_model->addScore(51,$this->uid);
                }else if($hospital){
                    $this->Score_model->addScore(50,$this->uid);
                }
                $this->Score_model->addScore(49,$this->uid);

                $data = array('uid' => $this->uid,
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
                    'oneday' => strtotime(date('Y-m-d')),
                    'pointX' => $pointX,
                    'pointY' => $pointY,
                    'created_at' => time(),
                    'updated_at' => time()
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
            }else{
                $nid = $this->input->post('nid')?$nid = $this->input->post('nid'):0;

                $data = array(
                    'content' => $content,
                    'updated_at' => time()
                );

                $result['data'] = $this->Diary_model->updateUserDiary($nid, $data);
                $result['notice'] = '用户美人记更新成功';
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录';
        }

        echo json_encode($result);
    }

    public function s($p){

        $data = array();
        //计算这个目录第几天
        $ncid = 1;
        $item_name = '美发';
        $this->uid = 58609;

        $lastNote = $this->Diary_model->getLastNote($this->uid,$ncid);
        //计算这个项目第几天
        $itemLastNote = $this->Diary_model->getItemLastNote($this->uid,$item_name);

        $categoryDay = 0;

        if(empty($lastNote)){
            $categoryDay = 1 ; //第一天
        }else{
            //计算到第几天
            $datetime = 0;
            $datetime = time() - intval($lastNote[0]['created_at']);
            $categoryDay = ceil(($datetime/86400));

        }

        $itemDay = 0;

        if(empty($itemLastNote)){
            $itemDay = 1 ; //第一天
        }else{
            //计算到第几天
            $datetime = 0;
            $datetime = time() - intval($itemLastNote[0]['created_at']);
            $itemDay = ceil(($datetime/86400));
        }
        echo '<pre>';
        print_r($lastNote);
        print_r($itemLastNote);
        //echo json_encode($data);
    }
    public function delUserDiary($param = ''){
        $result['state'] = '000';
        $nid = $this->input->get('nid');
        if(intval($nid) > 0){
            $result['data'][] = $this->Diary_model->delUserDiary($nid);
        }else{
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    /**
     * ＠param int nid 评论美人计id
     * @param string content 评论内容
     * @param string fromusername 来自评论的人的名字
     * @param int fromuid 来自评论人的ｉｄ
     * @param string tousername 被回复人的名字
     * @param int touid 被回复人的ｉｄ
     * @param int pcid  cid
     * @param string $param
     * @param file $commentPic
     */
    public function saveComment($param = ''){

        $nid = $this->input->post('nid');
        $content = $this->input->post('content');
        $fromusername = $this->input->post('fromusername');
        $fromuid = $this->input->post('fromuid');
        $tousername = $this->input->post('tousername');
        $touid = $this->input->post('touid');
        $pcid = $this->input->post('pcid')?$this->input->post('pcid'):0;

        $result['state'] = '000';

        if($nid && $content && $this->uid){


            if (isset ($_FILES['commentPic']['name']) && $_FILES['commentPic']['name'] != '') {
                $result['notice'] = '美人记发布成功！';
                $imgurl = date('Y') . '/' . date('m') . '/' . date('d');
                $ext = '.jpg';
                $filename = uniqid() . rand(1000, 9999) . $ext;
                $imgurl .= '/' . $filename;
                $ptmp = getimagesize($_FILES['commentPic']['tmp_name']);
                if (!$this->remote->cp($_FILES['commentPic']['tmp_name'], $filename, $imgurl, array(
                    'width' => 600,
                    'height' => 600
                ), true)
                ) {

                    $result['state'] = '001';
                    $result['notice'] = '图片上传失败！';
                    echo json_encode($result);
                    exit;
                }
            }

            $this->db->where('touid', $touid);
            $num = $this->db->get('note_comment')->num_rows();
            if($num == 10){
                $this->Score_model->addScore(57,$touid);
            }else if($num == 50){
                $this->Score_model->addScore(58,$touid);
            }else if($num == 100){
                $this->Score_model->addScore(59,$touid);
            }
            $this->Score_model->addScore(64,$this->uid);

            $data = array(
                'nid'=>$nid,
                'fromusername'=>$fromusername,
                'fromuid'=>$fromuid,
                'tousername'=>$tousername,
                'touid'=>$touid,
                'content'=>$content,
                'imgurl'=>$imgurl,
                'pcid'=>$pcid,
                'created_at'=>time(),
                'updated_at'=>time()
            );

            $result['data'] = $this->Diary_model->saveComment($data);

            if(isset($result['data']) && $result['data']){
                $this->Diary_model->updateTotalCommnetsForNote($nid);
            }
            $result['total_comments'] = $this->Diary_model->getCommentCount($nid);
            $result['notice'] = '评论成功!';
            $result['page'] = ceil(intval($result['total_comments'])/10);
            $this->load->model('push');
            $push = array('type'=>'diary','id'=>$this->Diary_model->getLastID(),'page'=>$result['page']);
            $this->push->sendUser('[美人计]新回复:'.$content,$this->uid,$push);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录';
        }

        echo json_encode($result);
    }

    public function delcomment($id){

    }

    /**
     * 获取美人计的评论列表
     * ＠param int nid
     * @param string $param
     */
    public function gett(){
        echo $this->remote->thumb('52761');
    }
    public function getCommentList($param = ''){

        $offset = intval($this->input->get('page')-1)*10;
        $nid = $this->input->get('nid');
        $result['state'] = '000';
        $result['data'] = array();

        if(intval($nid) > 0){
            $tmp = $this->Diary_model->getCommentList($nid, $offset);

            if(!empty($tmp)){

                foreach($tmp as $item){

                    $item['thumb'] = $this->remote->thumb($item['fromuid'],105);
                    $item['imgurl'] = isset($item['imgurl'])?$this->remote->show320($item['imgurl']):'';
                    if ($item['created_at'] > time() - 3600) {
                        $item['created_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
                    } else {
                        $item['created_at'] = date('Y-m-d', $item['created_at']);
                    }

                    if(isset($this->uid)) {
                        $is = $this->Diary_model->isZan($this->uid, $item['cid'], 'diary_comments');
                        $item['isZan'] = $is?1:0;
                    }else{
                        $item['isZan'] = 0;
                    }
                    $item['zanNum'] = ($this->Diary_model->getZan($item['cid'], 'diary_comments')>0)?$this->Diary_model->getZan($item['cid'], 'diary_comments'):0;

                    $item['isreply'] = 0;// 判断是否有评论
                    $iscomment = $this->Diary_model->isComment($item['pcid']);
                    if(intval($iscomment) > 0){
                        $item['isreply'] = 1;// 判断是否有评论
                        $item['reply'] = $this->Diary_model->getChildReply($item['cid']);
                    }
                    $result['data'][] =$item;
                }
                $result['total_comments'] = $this->Diary_model->getCommentCount($nid);
                $result['page_size'] = ceil(intval($result['total_comments'])/10);
            }
            //$result['data'] = $this->Diary_model->getCommentList($nid, $lastid);
        }
        echo json_encode($result);
    }
    /*
    public function updateComment($id, $data = array()){


    }
    */
    /**
     * @param string $param
     */
    public function getMyNoteList($param = ''){

        $offset = intval($this->input->get('page')-1)*10;

        $result['state'] = '000';

        if($this->uid){
            $result['data'] = $this->Diary_model->getMyNoteList($this->uid,$offset);

            if(!empty($result['data'])){

                foreach($result['data'] as $key=>$item){


                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    unset($item['nid']);

                    $result['data'][$key] = $item;
                }
            }

        }else{
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    /**
     * 获取用户项目列表
     * @param int lastiｄ　大于等于０
     * @param int limit 默认值为１０
     * @param string $param
     */
    public function getMyNoteCategory($param = ''){

        $result['state'] = '000';
        $result['data'] = array();
        $page = $this->input->post('page')?$this->input->post('page'):1;
        $offset = intval($this->input->post('page')-1)*10;
        $limit = $this->input->post('limit')?$this->input->post('limit'):10;
        $this->uid = $this->input->post('uid');
        //$result['debug'] = $this->input->post('uid').'=='.$this->input->post('page').'=='.$this->input->post('limit');
        if($this->uid){

            if(intval($limit) > 0){
                $temp = $this->Diary_model->getMyNoteCategoryList($this->uid,$offset,$limit);
            }else {
                $temp = $this->Diary_model->getMyNoteCategoryList($this->uid);
            }
            $total = $this->Diary_model->getDiaryCategoryCount($this->uid);
            if(!empty($temp)){
                foreach($temp as $item){
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    $item['diaryCount'] = $this->Diary_model->getDiaryCount($item['ncid']);
                    $item['totalCount'] = $total;
                    $result['data'][] = $item;
                }
            }

        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
        echo json_encode($result);
    }
    /**
     * 获取用户项目列表
     * @param int lastiｄ　大于等于０
     * @param int limit 默认值为１０
     * @param string $param
     */
    public function getAddMyNoteCategory($param = ''){

        $result['state'] = '000';
        $result['data'] = array();


        $offset = 0;
        $limit = 100;

        if($this->uid){

            if(intval($limit) > 0){
                $temp = $this->Diary_model->getMyNoteCategoryList($this->uid,$offset,$limit);
            }else {
                $temp = $this->Diary_model->getMyNoteCategoryList($this->uid);
            }
            $total = $this->Diary_model->getDiaryCategoryCount($this->uid);
            if(!empty($temp)){
                foreach($temp as $item){
                    $item['imgurl'] = $this->remote->show320($item['imgurl']);
                    $item['diaryCount'] = $this->Diary_model->getDiaryCount($item['ncid']);
                    $item['totalCount'] = $total;
                    $result['data'][] = $item;
                }
            }

        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
        echo json_encode($result);
    }
    /**
     * 添加美人计目录
     * @param bool $is 1 自己看到　０是全部人都能看到
     * @param string $title 不能为空
     * @param string $desc
     * @param boolean type =1 添加　０为修改
     * @param int ncid  如果ｔｙｐｅ＝０的时候ｎｃｉｄ必填否则可以不填写
     * @param file noteCategoryPic 上传图片
     * @param string $param
     */
    public function addNoteCategory($param = ''){
        $is = $this->input->post('is');
        $title = $this->input->post('title');
        $desc = $this->input->post('desc');
        $type = $this->input->post('type');
        $ncid = $this->input->post('ncid');
        $operation_time = strtotime($this->input->post('operation_time'));

        $result['state'] = '000';
        $result['data'] = array();
        $imgurl = '';

        if($title && $this->uid){

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
                $data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc, 'imgurl' => $imgurl,'operation_time'=>$operation_time, 'created_at' => time(), 'updated_at' => time());
            }else{
                $data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc,'operation_time'=>$operation_time,'created_at' => time(), 'updated_at' => time());
            }
            //$result['debug'] = $data;
            if($type == 1) {
                $isCategory = $this->Diary_model->isCategory($title, $this->uid);
                if (intval($isCategory) <= 0) {
                    $result['data'][] = $this->Diary_model->addNoteCategory($data);
                    $result['return'] = array('ncid' => $this->db->insert_id(), 'title' => $title, 'desc' => $desc);
                    $result['notice'] = '日记目录添加成功！';
                } else {
                    $result['state'] = '013';
                    $result['notice'] = '日记目录名称重复！';
                }
            }else{
                if(intval($ncid) > 0){
                    $result['data'][] = $this->Diary_model->updateNoteCategory($ncid ,$data);

                    $rstemp = $this->Diary_model->getNoteCategoryDetail($ncid);
                    $arr_tmp = array();

                    if(!empty($rstemp)){
                        foreach($rstemp as $item){
                            $item['thumb'] = $this->remote->thumb($item['uid'], '36');

                            $item['alias'] = $item['alias'] ? $item['alias']:$item['username'];
                            if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
                                $item['alias'] = substr($item['alias'],0,4).'****';
                            }
                            $item['imgurl'] = $this->remote->show320($item['imgurl']);
                            $item['desc'] = $item['desc'] ? $item['desc'] : '';
                            unset($item['username']);
                            unset($item['alias']);
                            unset($item['thumb']);
                            $arr_tmp = $item;
                        }
                    }

                    $result['return'] = $arr_tmp;
                    $result['notice'] = '更新目录成功！';
                }else{
                    $result['state'] = '014';
                    $result['notice'] = '请传入更改目录的编号！';
                }
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }

    /**
     * 获取用户信息
     * @param int lastiｄ　大于等于０
     * @param string $param
     */
    public function getMyNoteCategoryUserInfo($param = ''){

        $result['state'] = '000';
        $result['data'] = array();

        $this->uid = $this->input->post('uid');

        if($this->uid){
            $temp = $this->Diary_model->get_user_by_username($this->uid);
            if(!empty($temp)){
                foreach($temp as $item){
                    $item['thumb'] = $this->remote->thumb($this->uid);
                    $item['username'] = $item['alias'] ? $item['alias'] : $item['username'];
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    $item['funCount'] = $this->Diary_model->getFunCount($this->uid);
                    $item['followerCount'] = $this->Diary_model->getFollowerCount($this->uid);
                    $this->load->model('Background_Model');
                    $item['userBackgroundImg'] = $this->remote->show320($this->Background_Model->getUserBackground($this->uid,1));
                    unset($item['alias']);
                    $result['data'] = $item;
                }
            }

        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }

        echo json_encode($result);
    }
    /**
     * 删除美人计划
     * @param int nid  大于 0
     */
    public function delMyDiary(){

        $nid = $this->input->get('nid');
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){

            if(intval($nid) > 0 ){
                $result['data'][] = $this->Diary_model->delMyDiary($nid);
            }else{
                $result['state'] = '013';
                $result['notice'] = '请传入nid！';
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }
    /**
     * 删除美人计相册
     * @param int ncid 大于 0
     * @param string $param
     */
    public function delMyNoteCategory($param = ""){

        $ncid = $this->input->get('ncid');
        $result['state'] = '000';
        $result['data'] = array();
        $this->uid = $this->input->get('ncid');
        if($this->uid){

            if(intval($ncid) > 0 ){
                $result['data'][] = $this->Diary_model->delNoteCategory($ncid);
            }else{
                $result['state'] = '013';
                $result['notice'] = '请传入ncid！';
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }

    /**
     * 获取该日记点过赞的用户列表
     * ＠param int nid 传nid　
     * @param　int lastid 控制分页
     * @param int limit 控制页面显示个数
     * @param string $param
     */
    public function getFromUserZan($param = ''){

        $nid = $this->input->get('nid');
        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = intval($page-1)*15;
        $limit = $this->input->get('limit');

        $nid = $nid ? $nid:0;

        $result['state'] = '000';
        $result['data'] = array();

        if(intval($nid) > 0){

            $tmp = $this->Diary_model->getFromUserZan($nid, $offset, $limit);

            if(!empty($tmp)){

                foreach($tmp as $item){
                    $item['thumb'] = $this->remote->thumb($item['uid'], 105);
                    $res = $this->Diary_model->get_user_by_username($item['uid']);

                    $item['username'] = $res[0]['alias'] ? $res[0]['alias'] : $res[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                        $item['username'] = substr($item['username'],0,4).'****';
                    }
                    if($this->uid) {
                        if ($this->Diary_model->getstate($item['uid'],$this->uid)) {
                            $item['follow'] = 1;
                        } else {
                            $item['follow'] = 0;
                        }
                    }else{
                        $item['follow'] = 0;
                    }
                    $result['data'][] = $item;
                }
            }
        }else{
            $result['state'] = '012';
        }
        $result['uid'] = $this->uid;
        echo json_encode($result);
    }

    /**
     * @param string $params
     */
    public function getMyZanList($params = ""){
        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){
            $tmp = $this->Diary_model->getMyZan($this->uid, $offset);
            $userInfo = $this->Diary_model->get_user_by_username($this->uid);
            if(!empty($tmp)){
                foreach($tmp as $item){
                    if(isset($item['type']) && $item['type'] == 'topic') {
                        $rs = $this->Diary_model->getMyZanTopic($item['contentid']);
                        $item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
                        if (empty($item['content'])) {
                            $r = unserialize($rs[0]['type_data']);
                            $item['content'] = isset($r['title']) ? $r['title'] : '';
                            $item['pic'] = $this->remote->show320($r['pic']['savepath']);
                        }

                    }else{
                        $rs = $this->Diary_model->getMyZanDiary($item['contentid']);
                        $rstmp = $this->Diary_model->getDiaryDetail($item['contentid']);
                        if (!empty($rstmp)) {

                            foreach ($rstmp as $it) {
                                $temp = $this->Diary_model->get_user_by_username($it['uid']);

                                $it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                                $it['username'] = $it['username'] ? $it['username'] : '';
                                $it['zanNum'] = $this->Diary_model->getZan($it['nid']);
                                $it['imgurl'] = $this->remote->show320($it['imgurl']);
                                if(isset($this->uid)) {
                                    $is = $this->Diary_model->isZan($this->uid, $it['nid']);
                                    $it['isZan'] = $is?1:0;
                                }else{
                                    $it['isZan'] = 0;
                                }
                                $item['detail'] = $it;
                            }
                        }
                        $item['content'] = $rs[0]['content'];
                        $item['pic'] = $this->remote->show320($rs[0]['imgurl']);

                    }
                    //$item['fromusername'] = $userInfo[0]['username']?$userInfo[0]['username']:$userInfo[0]['alias'];
                    //$item['thumb'] = $this->remote->thumb($this->uid);
                    $user = $this->Diary_model->get_user_by_username($rs[0]['uid']);

                    $item['username'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];

                    if ($item['cTime'] > time() - 3600) {
                        $item['created_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                    } else {
                        $item['created_at'] = date('Y-m-d H:i', $item['cTime']);
                    }

                    unset($item['touid']);
                    unset($item['uid']);
                    unset($item['id']);
                    unset($item['cTime']);
                    $result['data'][] = $item;
                    //$this->db->query('update wen_zan set is_read=1 where is_read=0 and uid={$this->uid}');
                }
            }
        }
        echo json_encode($result);
    }

    public function getZanMyList($params = ""){

        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();


        if($this->uid){
            $tmp = $this->Diary_model->getZanMyList($this->uid, $offset);
            $userInfo = $this->Diary_model->get_user_by_username($this->uid);
            if(!empty($tmp)){
                foreach($tmp as $item){
                    if(isset($item['type']) && $item['type'] == 'topic') {
                        $rs = $this->Diary_model->getMyZanTopic($item['contentid']);
                        $item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
                        if (empty($item['content'])) {
                            $r = unserialize($rs[0]['type_data']);
                            $item['content'] = isset($r['title']) ? $r['title'] : '';
                            $item['pic'] = $this->remote->show320($r['pic']['savepath']);
                        }

                    }else{
                        $rs = $this->Diary_model->getMyZanDiary($item['contentid']);
                        $rstmp = $this->Diary_model->getDiaryDetail($item['contentid']);
                        if (!empty($rstmp)) {

                            foreach ($rstmp as $it) {
                                $temp = $this->Diary_model->get_user_by_username($it['uid']);

                                $it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                                $it['username'] = $it['username'] ? $it['username'] : '';
                                $it['zanNum'] = $this->Diary_model->getZan($it['nid']);
                                $it['imgurl'] = $this->remote->show320($it['imgurl']);
                                if(isset($this->uid)) {
                                    $is = $this->Diary_model->isZan($this->uid, $it['nid']);
                                    $it['isZan'] = $is?1:0;
                                }else{
                                    $it['isZan'] = 0;
                                }
                                $item['detail'] = $it;
                            }
                        }
                        $item['content'] = $rs[0]['content'];
                        $item['pic'] = $this->remote->show320($rs[0]['imgurl']);

                    }
                    $item['myname'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
                    $item['thumb'] = $this->remote->thumb($item['uid']);
                    $user = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['username'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
                    if ($item['cTime'] > time() - 3600) {
                        $item['created_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                    } else {
                        $item['created_at'] = date('Y-m-d H:i', $item['cTime']);
                    }

                    unset($item['touid']);
                    unset($item['uid']);
                    unset($item['id']);
                    unset($item['cTime']);
                    $result['data'][] = $item;

                }

            }
            $this->db->where('touid', $this->uid);
            $this->db->where('is_read', 0);
            $this->db->update("wen_zan",array('is_read'=>1));
        }
        echo json_encode($result);
    }
    /**
     * 添加点赞
     * @param int nid 被点赞的日记
     * @param string type diray为日记，diary_comments为日记评论,topic_comments为帖子评论
     *
     */
    public function addZan($param = ''){

        $contentid = $this->input->post('nid');
        $type = $this->input->post('type')?$this->input->post('type'):'diary';
        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';

        if($contentid && $this->uid){

            $rs =  $this->Diary_model->getMyZanTopic($contentid);
            $this->db->where('touid', $rs[0]['touid']);
            $this->db->where('type',$type);
            $num = $this->db->get('wen_zan')->num_rows();

            if($num == 10){
                $this->Score_model->addScore(60,$rs[0]['touid']);
            }else if($num == 50){
                $this->Score_model->addScore(61,$rs[0]['touid']);
            }else if($num == 100){
                $this->Score_model->addScore(62,$rs[0]['touid']);
            }

            $isZan = $this->Diary_model->isZan($this->uid, $contentid,$type);
            $result['debug'] = $this->uid."==".$contentid."==".$type."==".$isZan;
            if(!$isZan) {
                $result['data']['zan'] = $this->Diary_model->addZan($contentid, $this->uid,$type);
                $result['data']['flag'] = 1;
            }else{
                $result['data']['zan'] = $this->Diary_model->getZanNum($contentid,$type);
                $result['data']['flag'] = 0;
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }

    /**
     * 取消点赞
     * @param $nid
     * @param $uid
     * @return int
     */
    public function cancelZan($param = ''){

        $contentid = $this->input->post('nid');
        $type = $this->input->post('type')?$this->input->post('type'):'diary';
        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';


        if($contentid && $this->uid){


            $result['data']['zan'] = $this->Diary_model->cancelZan($contentid, $this->uid, $type);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }

    /**
     * 判断该用户是否点过该日记赞
     * @param int nid 被点赞的日记
     * @param string $param
     */
    public function isZan($param = ''){

        $contentid = $this->input->get('nid');
        $type = $this->input->get('type')?$this->input->get('type'):'diary';
        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';

        if($contentid && $this->uid){
            $result['data']['isZan'] = $this->Diary_model->isZan($this->uid, $contentid, $type);
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }

        echo json_encode($result);
    }

    public function getTopicMySendCommnetsList($param = ""){

        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){
            $tmp = $this->Diary_model->getMySendCommnetsListV3($this->uid, $offset);
            $userInfo = $this->Diary_model->get_user_by_username($this->uid);
            if(!empty($tmp)){
                foreach($tmp as $item){
                    if($this->uid == $item['fuid']) {
                        $item['is_send'] = 1;
                        $rs = $this->Diary_model->getMyZanTopic($item['contentid']);
                        $item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
                        if (empty($item['content'])) {
                            $r = unserialize($rs[0]['type_data']);
                            $item['content'] = isset($r['title']) ? $r['title'] : '';
                            $item['pic'] = $this->remote->show320($r['pic']['savepath']);
                        }
                        $user = $this->Diary_model->get_user_by_username($rs[0]['uid']);
                        $item['yourname'] = isset($user[0]['alias']) ? $user[0]['alias'] : $user[0]['username'];
                        $item['yourname'] = isset($item['yourname']) ? $item['yourname'] : '';
                        $item['mycomment'] = $item['comment'];
                        $item['myname'] = $userInfo[0]['alias'] ? $userInfo[0]['alias'] : $userInfo[0]['username'];
                        $item['mythumb'] = $this->remote->thumb($this->uid);
                        if ($item['cTime'] > time() - 3600) {
                            $item['created_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                        } else {
                            $item['created_at'] = date('Y-m-d H:i', $item['cTime']);
                        }
                        unset($item['comment']);
                        unset($item['touid']);
                        unset($item['uid']);
                        unset($item['id']);
                        unset($item['cTime']);
                        $result['data'][] = $item;
                        //$this->db->query('update wen_comment set is_read=1 where  is_read=0 and fuid={$this->uid}');
                    }else{
                        $item['is_send'] = 0;
                        $rs = $this->Diary_model->getMyZanTopic($item['contentid']);

                        $item['content'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                        if(empty($item['content'])){
                            $r = unserialize($rs[0]['type_data']);
                            $item['content'] = isset($r['title'])?$r['title']:'';
                            $item['pic'] = $this->remote->show320($r['pic']['savepath']);
                        }
                        $item['yourcomment'] = $item['comment'];
                        $user = $this->Diary_model->get_user_by_username($item['fuid']);
                        $item['myname'] = isset($userInfo[0]['alias'])?$userInfo[0]['alias']:$userInfo[0]['username'];
                        $item['yourname'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
                        $item['yourname'] = $item['yourname']?$item['yourname']:'';
                        $item['yourthumb'] = $this->remote->thumb($item['fuid']);

                        if ($item['cTime'] > time() - 3600) {
                            $item['created_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                        } else {
                            $item['created_at'] = date('Y-m-d H:i', $item['cTime']);
                        }
                        unset($item['comment']);
                        unset($item['touid']);
                        unset($item['uid']);
                        unset($item['id']);
                        unset($item['cTime']);
                        $result['data'][] = $item;
                    }
                }
            }
        }
        echo json_encode($result);
    }

    /**
     * 获取帖子评论
     * @param string $params
     */
    public function getTopicMyFollowCommentsList($params = ""){

        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();
        if($this->uid){
            $tmp = $this->Diary_model->getMyFollowCommentsList($this->uid, $offset);
            $userInfo = $this->Diary_model->get_user_by_username($this->uid);

            if(!empty($tmp)){
                foreach($tmp as $item){
                    $rs = $this->Diary_model->getMyZanTopic($item['contentid']);

                    $item['content'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                    if(empty($item['content'])){
                        $r = unserialize($rs[0]['type_data']);
                        $item['content'] = isset($r['title'])?$r['title']:'';
                        $item['pic'] = $this->remote->show320($r['pic']['savepath']);
                    }
                    $item['yourcomment'] = $item['comment'];
                    $user = $this->Diary_model->get_user_by_username($item['fuid']);
                    $item['myname'] = isset($userInfo[0]['alias'])?$userInfo[0]['alias']:$userInfo[0]['username'];
                    $item['yourname'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
                    $item['yourname'] = $item['yourname']?$item['yourname']:'';
                    $item['yourthumb'] = $this->remote->thumb($item['fuid']);

                    if ($item['cTime'] > time() - 3600) {
                        $item['created_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                    } else {
                        $item['created_at'] = date('Y-m-d H:i', $item['cTime']);
                    }
                    unset($item['comment']);
                    unset($item['touid']);
                    unset($item['uid']);
                    unset($item['id']);
                    unset($item['cTime']);
                    $result['data'][] = $item;
                }
                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("wen_comment",array('is_read'=>1));

            }
        }
        echo json_encode($result);
    }


    public function getDiaryMySendCommnetsList($uid = 0, $limit = 10){

        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){
            $tmp = $this->Diary_model->getDiaryMySendCommnetsListV3($this->uid, $offset);
            $userInfo = $this->Diary_model->get_user_by_username($this->uid);
            if(!empty($tmp)){
                foreach($tmp as $item){
                    if($this->uid == $item['fromuid']) {
                        $item['is_send'] = 1;
                        $item['mycomment'] = $item['content'];
                        $item['myname'] = $userInfo[0]['username'] ? $userInfo[0]['username'] : $userInfo[0]['alias'];
                        $item['mythumb'] = $this->remote->thumb($this->uid);
                        $rs = $this->Diary_model->getDiaryDetail($item['nid']);
                        $item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
                        $item['pic'] = $this->remote->show320($rs[0]['imgurl']);
                        $user = $this->Diary_model->get_user_by_username($rs[0]['uid']);
                        $item['yourname'] = isset($user[0]['alias']) ? $user[0]['alias'] : $user[0]['username'];
                        $item['yourname'] = isset($item['yourname']) ? $item['yourname'] : '';

                        $rstmp = $this->Diary_model->getDiaryDetail($item['nid']);
                        if (!empty($rstmp)) {

                            foreach ($rstmp as $it) {
                                $temp = $this->Diary_model->get_user_by_username($it['uid']);

                                $it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                                $it['username'] = $it['username'] ? $it['username'] : '';
                                $it['zanNum'] = $this->Diary_model->getZan($it['nid']);
                                $it['imgurl'] = $this->remote->show320($it['imgurl']);
                                if (isset($this->uid)) {
                                    $is = $this->Diary_model->isZan($this->uid, $it['nid']);
                                    $it['isZan'] = $is ? 1 : 0;
                                } else {
                                    $it['isZan'] = 0;
                                }
                                $item['detail'] = $it;
                            }
                        }


                        if ($item['created_at'] > time() - 3600) {
                            $item['created_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
                        } else {
                            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
                        }

                        $result['data'][] = $item;
                    }else{
                        $item['is_send'] = 0;
                        $item['yourcomment'] = $item['content'];
                        $item['myname'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
                        $item['yourthumb'] = $this->remote->thumb($item['fromuid']);
                        $rs = $this->Diary_model->getDiaryDetail($item['nid']);
                        $item['content'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                        $item['pic'] = $this->remote->show320($rs[0]['imgurl']);

                        $rstmp = $this->Diary_model->getDiaryDetail($item['nid']);
                        if (!empty($rstmp)) {

                            foreach ($rstmp as $it) {
                                $temp = $this->Diary_model->get_user_by_username($it['uid']);

                                $it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                                $it['username'] = $it['username'] ? $it['username'] : '';
                                $it['zanNum'] = $this->Diary_model->getZan($it['nid']);
                                $it['imgurl'] = $this->remote->show320($it['imgurl']);
                                if(isset($this->uid)) {
                                    $is = $this->Diary_model->isZan($this->uid, $it['nid']);
                                    $it['isZan'] = $is?1:0;
                                }else{
                                    $it['isZan'] = 0;
                                }
                                $item['detail'] = $it;
                            }
                        }

                        $item['yourname'] = is_null($item['fromusername'])?'':$item['fromusername'];

                        if ($item['created_at'] > time() - 3600) {
                            $item['created_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
                        } else {
                            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
                        }
                        $result['data'][] = $item;
                    }

                }
                //$this->db->query('update note_comment set is_read=1 where  is_read=0 and fromuid={$this->uid}');
            }
        }
        echo json_encode($result);
    }

    /**
     * 获取别人评论我的
     * @param string $params
     */
    public function getDiaryMyFollowCommentsList($params = ""){

        $offset = intval($this->input->get('page')-1)*10;
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){
            $tmp = $this->Diary_model->getDiaryMyFollowCommentsList($this->uid,$offset);

            $userInfo = $this->Diary_model->get_user_by_username($this->uid);

            if(!empty($tmp)){

                foreach($tmp as $item){
                    $item['yourcomment'] = $item['content'];
                    $item['myname'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
                    $item['yourthumb'] = $this->remote->thumb($item['fromuid']);
                    $rs = $this->Diary_model->getDiaryDetail($item['nid']);
                    $item['content'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                    $item['pic'] = $this->remote->show320($rs[0]['imgurl']);

                    $rstmp = $this->Diary_model->getDiaryDetail($item['nid']);
                    if (!empty($rstmp)) {

                        foreach ($rstmp as $it) {
                            $temp = $this->Diary_model->get_user_by_username($it['uid']);

                            $it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
                            $it['username'] = $it['username'] ? $it['username'] : '';
                            $it['zanNum'] = $this->Diary_model->getZan($it['nid']);
                            $it['imgurl'] = $this->remote->show320($it['imgurl']);
                            if(isset($this->uid)) {
                                $is = $this->Diary_model->isZan($this->uid, $it['nid']);
                                $it['isZan'] = $is?1:0;
                            }else{
                                $it['isZan'] = 0;
                            }
                            $item['detail'] = $it;
                        }
                    }

                    $item['yourname'] = $item['fromusername'];

                    if ($item['created_at'] > time() - 3600) {
                        $item['created_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
                    } else {
                        $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
                    }
                    $result['data'][] = $item;
                }
                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("note_comment",array('is_read'=>1));
                $result['debug'] = $this->db->last_query();
            }
        }
        echo json_encode($result);
    }

    /**
     * 获取消息动态
     */
    public function getMessages($params = ''){

        $result['state'] = '000';
        $result['data'] = array();
        $this->uid = $this->input->get('uid');
        if($this->uid){

            if(1){
                $tmp = $this->Diary_model->getDiaryMyFollowCommentsList($this->uid,$offset);

                $userInfo = $this->Diary_model->get_user_by_username($this->uid);

                if(!empty($tmp)){

                    foreach($tmp as $item){
                        $item['fromcomment'] = $item['content'];
                        $item['tousername'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
                        $item['fromthumb'] = $this->remote->thumb($item['fromuid']);
                        $item['tothumb'] = $this->remote->thumb($this->uid);
                        $rs = $this->Diary_model->getDiaryDetail($item['nid']);
                        $item['tocontent'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                        $item['touid'] = $this->uid;

                        $item['ctime']=$item['created_at'];
                        if ($item['fromcreated_at'] > time() - 3600) {
                            $item['fromcreated_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
                        } else {
                            $item['fromcreated_at'] = date('Y-m-d H:i', $item['created_at']);
                        }
                        unset($item['created_at']);
                        unset($item['content']);
                        $item['id'] = $item['nid'];
                        unset($item['nid']);
                        $item['type'] = 1;
                        $result['data'][$item['ctime']] = $item;
                    }
                }
            }
            if(2){
                $tmp = $this->Diary_model->getMyFollowCommentsList($this->uid, $offset);
                $userInfo = $this->Diary_model->get_user_by_username($this->uid);

                if(!empty($tmp)){
                    foreach($tmp as $item){
                        $rs = $this->Diary_model->getMyZanTopic($item['contentid']);

                        $item['tocontent'] = isset($rs[0]['content'])?$rs[0]['content']:'';
                        if(empty($item['tocontent'])){
                            $r = unserialize($rs[0]['type_data']);
                            $item['tocontent'] = isset($r['title'])?$r['title']:'';
                            //$item['pic'] = $this->remote->show320($r['pic']['savepath']);
                        }
                        $item['fromcomment'] = $item['comment'];
                        $user = $this->Diary_model->get_user_by_username($item['fuid']);
                        $item['fromuid'] = $item['fuid'];
                        $item['tousername'] = isset($userInfo[0]['alias'])?$userInfo[0]['alias']:$userInfo[0]['username'];
                        $item['yourname'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
                        $item['fromusername'] = $item['yourname']?$item['yourname']:'';
                        $item['fromthumb'] = $this->remote->thumb($item['fuid']);
                        $item['tothumb'] = $this->remote->thumb($this->uid);

                        if ($item['cTime'] > time() - 3600) {
                            $item['fromcreated_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                        } else {
                            $item['fromcreated_at'] = date('Y-m-d H:i', $item['cTime']);
                        }
                        unset($item['yourname']);
                        unset($item['comment']);
                        unset($item['touid']);
                        unset($item['uid']);
                        unset($item['id']);
                        unset($item['fuid']);
                        //unset($item['cTime']);
                        $item['id'] = $item['contentid'];
                        unset($item['contentid']);
                        $item['type'] = 2;
                        $result['data'][$item['cTime']] = $item;
                    }

                }
            }
            if(3){


                $this->db->where('fUid', $this->uid);
                $this->db->where('is_read',1);
                $this->db->select('wen_questions.title,wen_questions.id,wen_questions.cdate');
                $this->db->order_by("id", "desc");
                $this->db->from('wen_questions');
                $this->db->limit(10);
                $tmp = $this->db->get()->result_array();
                $userInfo = $this->Diary_model->get_user_by_username($this->uid);
                foreach ($tmp as $row) {

                    $row['ctime'] = $row['cdate'];
                    $row['cdate'] = date('Y-m-d', $row['cdate']);
                    $row['tothumb'] = $this->profilepic($this->uid, 2);
                    $row['tousername'] = isset($userInfo[0]['alias'])?$userInfo[0]['alias']:$userInfo[0]['username'];
                    $row['tocontent'] = $row['title'];
                    $row['touid'] = $this->uid;
                    $row['ans'] = $this->Gans($row['id'])?$this->Gans($row['id']):array();

                    $row['fromthumb'] = $row['ans'][0]['fromthumb'];
                    $row['fromcontent'] = $row['ans'][0]['fromcontent'];
                    $row['fromusername'] = $row['ans'][0]['fromusername'];
                    $row['fromuid'] = $row['ans'][0]['fromuid'];
                    $row['fromcreated_at'] = $row['ans'][0]['fromcreated_at'];

                    $row['type'] = 3;
                    unset($row['ans']);
                    unset($row['cdate']);
                    unset($row['title']);
                    $result['data'][$row['ctime']] = $row;
                }

            }
            krsort($result['data']);
            if(!empty($result['data'])){
                $rs = array();
                foreach($result['data'] as $it){
                    $rs[] =$it;
                }
                unset($result['data']);
                $result['data'] = $rs;
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }
        echo json_encode($result);
    }

    private function Gans($qid = '') {
        if ($qid) {
            $fields = 'wen_answer.uid,wen_answer.id,wen_answer.content,wen_answer.cdate,users.alias as yname';

            $tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users ON users.id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC limit 1")->result_array();

            foreach ($tmp as $row) {
                $row['fromthumb'] = $this->profilepic($row['uid'], 2);
                $row['fromcontent'] = $row['content'];
                $row['fromusername'] = $row['yname'];
                $row['fromuid'] = $row['uid'];
                if ($row['cdate'] > time() - 3600) {
                    $row['fromcreated_at'] = intval((time() - $row['cdate']) / 60) . '分钟前';
                } else {
                    $row['fromcreated_at'] = date('Y-m-d H:i', $row['cdate']);
                }
                unset($row['uid']);
                unset($row['cdate']);
                unset($row['content']);
                unset($row['yname']);
                $result[] = $row;
            }

        } else {
            $result['state'] = '012';
        }
        return $result;
    }

    public function readMessage(){
        $type = $this->input->get('type');
        if($this->uid){
            if($type == 4){
                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("note_comment",array('is_read'=>1));
                $result['debug'] = $this->db->last_query();

                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("wen_comment",array('is_read'=>1));

                $this->db->where('fUid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("wen_questions",array('is_read'=>1));
            }elseif($type == 3){
                $this->db->where('fUid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("wen_questions",array('is_read'=>1));
            }elseif($type == 2){
                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("wen_comment",array('is_read'=>1));
            }elseif($type == 1){
                $this->db->where('touid', $this->uid);
                $this->db->where('is_read', 0);
                $this->db->update("note_comment",array('is_read'=>1));
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录!';
        }
        echo json_encode($result);
    }

    public function getCustomerService(){

        $result['state'] = '000';
        $result['data'] = array();

        $rs[] = array(
            'uid'=>75909,
            'username'=>'小美Ａ',
            'thumb' => $this->remote->thumb(75909),
            'desc'  => '从事整形美容行业十年'
        );
        $rs[] = array(
            'uid'=>153737,
            'username'=>'小美Ｂ',
            'thumb' => $this->remote->thumb(153737),
            'desc'  => '美丽神器金牌客服，从事美容整形行业５年'
        );
        $rs[] = array(
            'uid'=>27804,
            'username'=>'小美Ｃ',
            'thumb' => $this->remote->thumb(27804),
            'desc'  => '从事整形美容行业十年'
        );
        $rs[] = array(
            'uid'=>46826,
            'username'=>'小美Ｄ',
            'thumb' => $this->remote->thumb(46826),
            'desc'  => '美丽神器金牌客服，从事美容整形行业５年'
        );
        $rs[] = array(
            'uid'=>46826,
            'username'=>'小美Ｅ',
            'thumb' => $this->remote->thumb(46826),
            'desc'  => '美丽神器金牌客服，从事美容整形行业５年'
        );
        $result['data'] = $rs;
        echo json_encode($result);
    }

    public function isItemLevel($param = 0){
        echo  $this->Diary_model->getItemId('眼部');

    }

}
?>