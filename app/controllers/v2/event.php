<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class event extends MY_Controller {
    private $notlogin = true;
    private $uid = 0;
    public function __construct() {

        parent :: __construct();

        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
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
        $this->eventDB = $this->load->database('event1', TRUE);
        $this->tehuiDB = $this->load->database('tehui', TRUE);
    }

    /**
     *
     */
    public function getflashSaleByid(){

        $id = intval($this->input->get('s_id'));
        $order = $this->input->get('order')?$this->input->get('order'):0;
        if($order){
            $order="DESC";
        }else{
            $order="ASC";
        }
        $web = $this->input->get('web');
        $city = trim($this->input->get('city'));
        $start = $this->input->get('page')?$this->input->get('page'):1;
        $offset = ($start - 1) * 3;
        $fs_sql = "select * from flash_sale where 1=1 and id = ?";
        $fs_pr_sql = "select * from flash_sale_tehui where 1=1 and fs_id = ? order by level DESC";
        $fs_rs = $this->db->query($fs_sql,array($id))->row_array();
        $fs_pr_rs = $this->db->query($fs_pr_sql,array($id))->result_array();


        $result['data'] = array();
        $result['data']['tehui_list'] = array();

        $city_id = "";

        if(!empty($city)){
            $city_id = $this->tehuiDB->query("SELECT id FROM category WHERE name = '{$city}'")->row_array();
        }

        if(!empty($fs_rs)){
            $fs_rs['pro_arr'] = unserialize($fs_rs['product']);
            $tehui_fields = 't.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';



            $tehui_arr = array();

            if(!empty($fs_pr_rs)){

                foreach($fs_pr_rs as $fsv){
                    $tehui_condition = " and t.id = {$fsv['p_id']} ";
                    $time = time();
                    //$tehui_condition .= " and t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}'";
                    $tehui_condition .= " and t.team_type='normal' and t.end_time >= '{$time}'";
                    if (!empty ($city_id)) {
                        $tehui_condition .= " AND ((t.city_ids like '%@{$city_id['id']}@%' or t.city_ids like '%@0@%') or t.city_id in(0,{$city_id['id']}) OR t.areatype=1) order by id $order ";
                    }

                    $tehui_info = $this->tehuiDB->query("SELECT {$tehui_fields} FROM team as t WHERE 1=1 {$tehui_condition} limit $offset,5")->row_array();
                    $randpic = date('Ymdhi',time());

                    if(!empty($tehui_info)){
                        if(!strstr($tehui_info['image'],"http://pic")){
                            $tehui_info['image'] = 'http://tehui.meilimei.com/static/' . $tehui_info['image'].'?'.$randpic;
                        }

                        $tehui_sql = "select * from tehui_relation where tehui_id = ?";
                        $tehui_rs = $this->db->query($tehui_sql,array($tehui_info['id']))->row_array();
                        $m_id  = $tehui_rs['mechanism'];
                        $this->db->where('id',$m_id);

                        $tehui_info['mechanism'] = "";
                        $company = $this->db->get('company')->row_array();
                        if($company){
                            $tehui_info['mechanism'] = $company;
                            $tehui_info['mechanism'] = $tehui_info['mechanism']['name'];
                        }
                        //session 唯一标示付
                        $sid = $tehui_info['id'];

                        if(!empty($_SESSION[$sid])){
                            $tehui_info['order_num'] = $_SESSION[$sid];
                        }else{
                            $_SESSION[$sid] = rand(66, 88);
                            $tehui_info['order_num'] = $_SESSION[$sid];
                        }
                        $tehui_info['case_num'] = rand(50,66);

                        $tehui_info['reser_price'] = 0;
                        $tehui_info['deposit'] = 0;

                        if($tehui_rs['reser_price']){
                            $tehui_info['reser_price'] = $tehui_rs['reser_price'];
                        }

                        if($tehui_rs['deposit']){
                            $tehui_info['deposit'] = $tehui_rs['deposit'];
                        }

                        $tehui_arr[] = $tehui_info;
                    }
                }
            }

            $fs_rs['fm_end_1'] = date("Y年m月d日",$fs_rs['end']);
            $fs_rs['fm_begin_1'] = date("Y年m月d日 ",$fs_rs['begin']);

            $fs_rs['i_end'] =  $fs_rs['end'];
            $fs_rs['i_begin'] =  $fs_rs['begin'];

            //1 开始 2 未开始
            $fs_rs['nobegin'] = '1';
            if($fs_rs['begin'] > time()){
                $fs_rs['nobegin'] = '2';
            }

            $fs_rs['end'] =  abs($fs_rs['end']-time());
            $fs_rs['begin'] =   abs($fs_rs['begin']-time());



            $day = floor($fs_rs['end']/(3600*24));
            $second = $fs_rs['end']%(3600*24);//除去整天之后剩余的时间
            $hour = floor($second/3600);
            $second = $second%3600;//除去整小时之后剩余的时间
            $minute = floor($second/60);
            $second = $second%60;//除去整分钟之后剩余的时间

            //$v['begin'] = date("d天h时i分",$v['begin']);
            $fs_rs['fm_end_2'] = $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';

            //$fs_rs['fm_end_2'] = date("d天h时i分",$fs_rs['end']);
            //$fs_rs['fm_begin_2'] = date("d天h时i分",$fs_rs['begin']);
            $fs_rs['banner'] = $this->remote->show($fs_rs['banner']);
            $fs_rs['lbanner'] = $this->remote->show($fs_rs['lbanner']);
            $fs_rs['type_thumb'] = $this->profilepic($fs_rs['type_id'], 1);
            $fs_rs['context'] = str_replace('src="','src="http://www.meilimei.com/',$fs_rs['context']);

            $fs_rs['context'] = $this->gdetail($fs_rs['context'], 'tmp');


            $result['state'] = '000';

            if($web == 'web'){
                $result['data'] = $fs_rs;
                $result['data']['tehui_list'] = $tehui_arr;
            }else{
                if(intval($offset) > 0){
                    $result['tehui_list'] = $tehui_arr;
                }else{
                    $result['data'] = $fs_rs;
                    $result['tehui_list'] = $tehui_arr;
                }

            }
        }

        echo json_encode($result);
    }

    private function gdetail($content,$title){
        $content = preg_replace('/ style=\".*?\"/','',$content);

        return '
        <style>
            .mainc{
                    font-size:14px;
                    line-height:160%;
                    max-width:600px;
                    padding:10px;
                    color:#666666;
                    margin:auto;
            }
            .mainc img{
                    max-width:350px;
            }
            .mainc img {
                    width:100%;
	        }
            .wapper_form{
                    width:95%;
                    margin:0 auto;
	        }
            a:link{
                text-decoration:none;
                color:#fc85b6;
	        }
        </style>
        <div id="content" class="mainc">'.$content.'</div> ';
    }
    /**
     * 获取活动详细信息
     */

    public function getEventDetail(){
        $result['state'] = '000';
        $result['data'] = array();

        $event_id = $this->input->get('event_id');

        if(intval($event_id) > 0){
            $this->eventDB->select('event_title, event_pic, event_context as event_content, begin_time, end_time');
            $this->eventDB->where('id', $event_id);
            $this->eventDB->where('display', 1);
            $tmp = $this->eventDB->get('event_topic')->result_array();

            if(!empty($tmp)){
                foreach($tmp as $item){
                    $item['event_pic'] = $this->remote->show320($item['event_pic']);
                    $item['long_time'] = $item['end_time'];
                    $item['begin_time'] = date('Y-m-d H:i:s', $item['begin_time']);
                    $item['end_time'] = date('Y-m-d H:i:s', $item['end_time']);
                    $result['data'] = $item;
                }
            }else{
                $result['state'] = '013';
                $result['notice'] = '没有推广活动！';
            }
        }else{
            $result['state'] = '014';
            $result['notice'] = '请传入活动编号！';
        }

        echo json_encode($result);
    }

    /**
     *登记报名活动
     */

    public function setEntry(){
        $result['state'] = '000';
        $result['data'] = array();

        $event_id = $this->input->get('event_id');
        //$this->uid = $this->input->get('uid');
        if($this->uid){
            $this->eventDB->select('event_score');
            $this->eventDB->where('id', $event_id);
            $this->eventDB->where('display', 1);
            $tmp = $this->eventDB->get('event_topic')->result_array();

            if(!empty($tmp)){
                $this->db->select('users.username, users.phone, user_profile.city');
                $this->db->from('users');
                $this->db->join('user_profile', 'users.id=user_profile.user_id');
                $this->db->where('id', $this->uid);
                $utmp = $this->db->get()->result_array();

                if(!empty($utmp)){
                    $this->eventDB->where('event_id', $event_id);
                    $this->eventDB->where('user_id', $this->uid);
                    $etetmp = $this->eventDB->get('event_topic_enter')->result_array();
                    if(empty($etetmp)) {
                        $isInsert = $this->eventDB->insert('event_topic_enter', array('event_id' => $event_id, 'user_id' => $this->uid, 'username' => $utmp[0]['username'], 'realname' => '', 'mobile' => $utmp[0]['phone'], 'city' => $utmp[0]['city'], 'creattime' => time()));
                        if ($isInsert) {
                            $result['notice'] = '报名成功！';
                        } else {
                            $result['notice'] = '报名失败！';
                            $result['state'] = '018';
                        }
                    }else{
                        $result['notice'] = '已报名！';
                        $result['state'] = '019';
                    }
                }else{
                    $result['state'] = '013';
                    $result['notice'] = '没有推广活动！';
                }
            }

        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }

        echo json_encode($result);
    }

    /**
     * 获取闪购信息
     */
    public function getflashSale(){
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid){

        }else{
            $result['state'] = '012';
            $result['notice'] = '用户未登录！';
        }
    }

    /**
     * 获取活动话题
     */
    public function getTopicList(){

        $result['state'] = '000';
        $result['data'] = array();
        $event_id = $this->input->get('event_id')?$this->input->get('event_id'):0;
        $result['data'] = array($this->uid);
        $result['data'] = $this->topicList($event_id);
        echo json_encode($result);
    }

    private function topicList($event_id=0) {

        $start = ($this->input->get('page') - 1) * 3;

        $this->eventDB->select('topic_id');
        $this->eventDB->where('event_id', $event_id);
        $this->eventDB->limit(3,$start);
        $utopic = $this->eventDB->get('event_topic_detail')->result_array();

        if(!empty($utopic)) {
            $topicid = '';
            foreach($utopic as $item){
                if(!$topicid)
                    $topicid .= $item['topic_id'];
                else
                    $topicid .= ','.$item['topic_id'];
            }

            $sql = "SELECT u.jifen,u.age,u.city, u.daren,w.group_start,w.group_end,w.comments,w.content,w.newtime,w.pageview,w.hot,w.hot_start,w.hot_end,w.top,w.top_start,w.top_end,w.chosen,w.chosen_start,w.chosen_end,w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
            $sql .= ' FROM wen_weibo as w LEFT JOIN users as u ON w.uid=u.id';

            $ctime = time(); //set publish time

            $sql .= " and w.uid != 0 ";
            $sql .= " and w.weibo_id in ({$topicid})";
            $sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.newtime DESC ";

            if ($this->input->get('limit')) {
                $limit = $this->input->get('limit');
            } else {
                $limit = 3;
            }
            if ($this->input->get('page')) {
                $start = ($this->input->get('page') - 1) * 3;
                $sql .= " LIMIT $start,$limit ";
            } else {
                $sql .= " LIMIT 0,$limit ";
            }
            $tmp = $this->db->query($sql)->result_array();

            $res = array();
            if (!empty ($tmp)) {
                foreach ($tmp as $row) {

                    if ($row['top_start'] <= time() && $row['top_end'] >= time()) {
                        $row['top'] = 1;
                    } else {
                        $row['top'] = 0;
                    }
                    $row['pageview'] = intval($row['pageview']);
                    $row['age'] = $this->getAge(intval($row['age']));
                    $row['city'] = !empty($row['city']) ? $row['city'] : '';
                    if ($row['chosen_start'] <= time() && $row['chosen_end'] >= time()) {
                        $row['chosen'] = 1;
                    } else {
                        $row['chosen'] = 0;
                    }
                    $row['istopic'] = 1;
                    if ($row['hot_start'] <= time() && $row['hot_end'] >= time()) {
                        $row['hot'] = 1;
                    } else {
                        $row['hot'] = 0;
                    }
                    if ($row['group_start'] <= time() && $row['group_end'] >= time()) {
                        $row['top'] = 1;
                    } else {
                        $row['top'] = 0;
                    }
                    if($this->uid) {
                        if ($this->Diary_model->getstate($row['uid'],$this->uid)) {
                            $row['follow'] = 1;
                        } else {
                            $row['follow'] = 0;
                        }

                    }else{
                        $row['follow'] = 0;
                    }
                    $rs = $this->Diary_model->get_user_by_username($row['uid']);
                    $item ['basicinfo'] = $this->getBasicInfo($rs[0]);

                    $info = unserialize($row['type_data']);
                    isset ($info['title']) && $row['content'] = $info['title'];
                    unset ($row['type_data']);
                    $row['title'] = $info['title'];
                    $row['thumb'] = $this->profilepic($row['uid'], 2);
                    $row['zanNum'] = ($this->getZan($row['weibo_id']) > 0) ? $this->getZan($row['weibo_id']) : 0;
                    if (intval($this->uid) > 0) {
                        $iszan = $this->isAtZan($this->uid, $row['weibo_id']);
                        if ($iszan) {
                            $row['isZan'] = 1;
                        } else {
                            $row['isZan'] = 0;
                        }
                    } else {
                        $row['isZan'] = 0;
                    }
                    $row['level'] = $this->getLevel($row['jifen']);
                    $row['content'] = $row['content'];
                    $row['hasnew'] = $row['commentnums'];
                    if (!empty($row['tags'])) {
                        $row['tag'] = explode(',', $row['tags']);

                        foreach ($row['tag'] as $item) {
                            if (!empty($item)) {
                                $arr = array();
                                $arr['tag'] = $item;
                                $itemid = $this->Diary_model->getItemId($item);
                                $arr['other'] = $this->Diary_model->isItemLevel($itemid, 1);
                                $arr['tagid'] = $itemid;
                                $row['tagss'][] = $arr;
                                $row['tag1'][] = $item;
                            }
                        }
                    }
                    $row['uname'] = $row['alias'] ? $row['alias'] : '';
                    if (empty($row['tagss'])) {
                        $row['tagss'] = array();
                    }
                    if (empty($row['tag1'])) {
                        $row['tag1'] = array();
                    }
                    $row['tags'] = $row['tag1'];

                    if (empty($row['tags'])) {
                        $row['tags'] = array();
                    }
                    unset($row['tag']);
                    //$row['tags'] = explode(',',$row['tags']);
                    $row['showname'] = $this->GName($row['alias'], $row['phone']);
                    if (isset ($info[1]['id']) OR isset ($info['pic'])) {
                        $row['haspic'] = 1;
                    } else {
                        $row['haspic'] = 0;
                    }
                    if ($row['showname'] == '1816..') {
                        $row['showname'] = substr($row['ctime'], 0, 2) . substr($row['ctime'], 8, 2) . '..';
                    }
                    if ($row['ctime'] > time() - 3600) {
                        $row['ctime'] = intval((time() - $row['ctime']) / 60) . '分钟前';
                    } else {
                        $row['ctime'] = date('Y-m-d', $row['ctime']);
                    }
                    $row['newtime'] = date('Y-m-d', $row['newtime']);
                    if ($this->uid != $row['uid']) {
                        $row['hasnew'] = 0;
                    }
                    //$row['totalCount'] = $totalCount;
                    //if($this->input->get('width')){
                    $row['images'] = $this->Plist($row['weibo_id']);
                    //}
                    $res[] = $row;
                }
                //$result['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
            }
        }else{
            return array();
        }
        //$result['topicTotal'] = $this->Diary_model->getTopicCount($type)?$this->Diary_model->getTopicCount($type):0;
        //$result['diaryTotal'] = $this->Diary_model->getDiaryTotal($type)?$this->Diary_model->getDiaryTotal($type):0;
        //$s = $this->GroupTopicListTop();

        //$r = array_merge($s['data'],$result['data']);
        //$result['data'] = array();
        //$result['data'] = $r;
        //echo json_encode($result);
        return $res;
    }

    private function getZan($contentid) {
        $this->db->where('weibo_id', $contentid);
        $tmp = $this->db->get('wen_weibo')->result_array();

        if(isset($tmp[0]['zan']) && intval($tmp[0]['zan']) > 0){
            $where_condition = array ('type' => 'topic', 'contentid' => $contentid);
            return $this->db->get_where('wen_zan', $where_condition)->num_rows() + intval($tmp[0]['zan']);
        }else{
            $zan = rand(0,0);
            $this->db->where('weibo_id', $contentid);
            $this->db->update('wen_weibo', array('zan'=> $zan));
            $where_condition = array ('type' => 'topic', 'contentid' => $contentid);
            return $this->db->get_where('wen_zan', $where_condition)->num_rows() + $zan;
        }

    }

    //generate user show name
    private function GName($alias,$phone){
        if ($alias != '' and preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$alias)) {
            return substr($alias, 0, 4) . '***';
        }
        elseif ($alias != '') {
            return $alias;
        } else {
            return substr($phone, 0, 4) . '***';
        }
    }
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
    //get set pic lists
    private function Plist($id) {
        $this->db->select('id,savepath,height,width,info');
        $this->db->where('attachId', $id);
        $this->db->from('topic_pics');
        $this->db->order_by('order','ASC');
        $res = $this->db->get()->result_array();
        $rt = array ();
        //show pic width
        $width = 'auto';
        if ($this->input->get('width')) {
            $width = intval($this->input->get('width'));
        }

        foreach ($res as $r) {
            $r['points'] = $this->Gpoint($r['id']);
            $arr_url = explode('/',$r['savepath']);
            $url = '';
            if(substr($r['savepath'],strlen($r['savepath'])-4) == '.mp4'){
                $r['vedio'] = $r['savepath'];
                $r['savepath'] = '';
            }else {
                if (intval($arr_url[1]) >= 3 && intval(date('Y')) <= $arr_url[0]) {

                    if (isset($arr_url[1])) {

                        $url = str_replace('/' . $arr_url[1] . '/', '/' . $arr_url[1] . 'x1080/', $r['savepath']);
                    }

                    //echo $this->remote->show320($url, $width);
                    $r['savepath'] = $this->remote->show800($url, $width);
                    $r['vedio'] = '';
                } else {
                    if (isset($arr_url[1])) {

                        $url = str_replace('/' . $arr_url[1] . '/', '/' . $arr_url[1] . 'x640/', $r['savepath']);
                    }

                    $r['savepath'] = $this->remote->show320($url, $width);
                    $r['vedio'] = '';
                }
            }
            $rt[] = $r;
        }
        return $rt;
    }

    private function isAtZan($uid,$contentid = 0){

        $condition = array (
            'type' => 'topic',
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
}
?>
