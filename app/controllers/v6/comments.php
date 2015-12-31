<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__ . "/MyController.php");

class comments extends MY_Controller
{
    private $uid = '';

    public function __construct()
    {
        parent:: __construct();
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            $this->notlogin = true;
        }
        $this->load->library('emoji');//表情处理类
        $this->load->library('alicache');
        $this->load->library('filter');
        $this->load->model('auth');
        $this->load->model('remote');
        $this->load->model('Diary_model');
        $this->load->model('track_error');
        $this->load->model('Score_model');
    }

    //send to topic
    public function sendcomment($param = '')
    {
        $result['state'] = '000';
        $result['ustate'] = '000';
        //$this->uid = 58609;
        if (!$this->uid) {
            $result['notice'] = '账户未登入！';
            $result['ustate'] = '001';
        } else {
            $this->db->where('id',$this->uid);
            $this->db->where('banned',1);
            $num = $this->db->get('users')->num_rows();

            if(intval($num) > 0){
                $result['state'] = '012';
                $result['notice'] = '该用户被禁用或者已经被删除！';
                echo json_encode($result);
                exit;
            }
            if (($type = strip_tags($this->input->post('type'))) and ($contentid = intval($this->input->post('contentid')))) {
                $pid = intval($this->input->post('pid'));

                $touid = intval($this->input->post('touid'));
                if (strlen($this->input->post('comment')) < 2) {
                    $result['state'] = '012';
                    $result['notice'] = '评论内容过短！';
                    $this->track_error->L($this->input->post('comment') . $result['state']);
                    echo json_encode($result);
                    exit;
                }
                //check illegal word
                if (!$this->filter->judge($this->input->post('comment'))) {
                    $result['state'] = '012';
                    $this->track_error->L($this->input->post('comment') . $result['state']);
                    $result['notice'] = '含有广告等信息！';
                    echo json_encode($result);
                    exit;
                }

                //check time
                if ($tmpTime = $this->getLastComment($this->uid)) {

                    $longtime = time() - $tmpTime[0]['cTime'];
                    if($longtime <= 5){
                        $result['state'] = '400';
                        $this->track_error->L($this->input->post('comment') . $result['state']);
                        $result['notice'] = '评论发布太快啦！';
                        echo json_encode($result);
                        exit;
                    }
                }

                //check weibo
                if (!$this->cktopic($contentid)) {
                    $result['state'] = '400';
                    $this->track_error->L($this->input->post('comment') . $result['state']);
                    $result['notice'] = '该话题已经被删除！';
                    echo json_encode($result);
                    exit;
                }
                if ($pid > 0) {
                    $PCID = $this->GPCID($pid);
                    if (!$PCID) {
                        $this->track_error->L($this->input->post('comment') . $pid . 'PCID:' . $PCID);
                        $result['notice'] = '该评论已被删除！';
                        $result['state'] = '012';
                        echo json_encode($result);
                        exit;
                    }
                } else {
                    $PCID = 0;
                }
                $PCID = 0;

                $head = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Android';
                if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
                    $Idata['device'] = 'IOS';
                } else {
                    $Idata['device'] = 'Android';
                }
                //special commment?
                if ($this->wen_auth->get_role_id() != 1) {
                    $Idata['type'] = 'ans';
                }
                $Idata['type'] = $type;
                $Idata['pid'] = $pid;
                $Idata['pcid'] = $PCID;
                $Idata['contentid'] = $contentid;
                $Idata['fuid'] = $this->uid;
                $Idata['touid'] = $touid;
                $Idata['cTime'] = time();
                $Idata['comment'] = $this->emoji->emoji2str($this->input->post('comment'));
                $Idata['imgfile'] = $this->input->post('key1');

                $this->db->insert('wen_comment', $Idata);
                $cid = $this->db->insert_id();
                if ($PCID > 0) {
                    $this->db->query("update wen_comment set new_reply = new_reply+1 where id = {$PCID} limit 1 ");
                }
                $this->wen_auth->set_weibo_rjifen($this->uid);
                $this->db->query("update wen_weibo set comments=comments+1,commentnums=commentnums+1 where weibo_id = '$contentid' limit 1 ");
                $this->db->query("update wen_weibo set newtime='".time()."' where weibo_id = '$contentid' limit 1 ");
                $judge = $this->db->query("select uid from  wen_weibo where weibo_id = '$contentid' limit 1 ")->result_array();

                //get comment in page
                $this->db->where('type', 'topic');
                $this->db->where('contentid', $contentid);
                $this->db->where('pid', 0);
                $this->db->from('wen_comment');
                $tmpage = $this->db->count_all_results() / 5;

                $result['pagesize'] = 5;
                if (is_int($tmpage)) {
                    $result['page'] = $tmpage;
                } else {
                    $result['page'] = intval($tmpage) + 1;
                }
                $result['pageCount'] = $result['page'];

                //deal extra chain data
                $this->load->model('user_sum');
                $num =$this->db->count_all_results();
                if($num == 10){
                    $result['data']['score'] = $this->Score_model->addScore(57,$touid);
                }else if($num == 50){
                    $result['data']['score'] = $this->Score_model->addScore(58,$touid);
                }else if($num == 100){
                    $result['data']['score'] = $this->Score_model->addScore(59,$touid);
                }
                //只要被评论就+5分
                $result['data']['score'] = $this->Score_model->addScore(57,$touid);
                //评论者每次+3分
                $result['data']['score'] = $this->Score_model->addScore(64,$this->uid);
                $this->user_sum->addGrowth($this->uid, 'GROW_RTOPIC');
                if($this->getNextCID($cid, 'xxx') > 0){
                    $lastid  = $this->getNextCID($cid, 'xxx') ;
                }else{
                    $lastid = "";
                }


                if($this->uid != $touid){
                    //send IGTTUI push
                    $this->load->model('Users_model');
                    $clientid = $this->Users_model->getClientID($contentid);
                    //$result['debug'] = $clientid[0]['clientid'];
                    try {
                        if (!empty($clientid)) {
                            $this->load->library('igttui');
                            if (count($judge) and $this->uid != $judge[0]['uid']) {
                                $d = $this->igttui->sendMessage($clientid[0]['clientid'], "topic:" . $contentid . ":" . $result['page'] . ':'.$lastid.':' . $Idata['comment']);
                            }
                            //$result['d'] = $d;
                        } else {
                            if (count($judge) and $this->uid != $judge[0]['uid']) {
                                //send apple push
                                $this->load->model('push');
                                $push = array('type' => 'topic', 'id' => $contentid, 'page' => $result['page']);
                                $this->push->sendUser('[话题]新回复:' . $Idata['comment'], $judge[0]['uid'], $push);
                            }
                        }
                    }catch (Exception $e){
                        $result['notice'] = '回复成功，没有推送给楼主！';
                    }
                }

                if($pid > 0){
                    $fields = 'users.banned,c.fuid,users.alias as uname,users.phone, users.jifen, users.city, users.age,c.id,c.contentid,c.touid,c.comment,c.cTime,c.is_delete,c.data, c.imgfile';

                    $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";

                    $sql .= "WHERE  c.id = {$cid} and is_delete=0  and users.banned=0 ";
                    $tmp = $this->db->query($sql)->row_array();

                    $tmp['comment'] = $this->emoji->str2emoji($tmp['comment']);
                    if (time() - $tmp['cTime'] < 3600 * 10) {
                        if (time() - $tmp['cTime'] < 3600) {
                            $tmp['cTime'] = intval((time() - $tmp['cTime']) / 60) . '分钟前';
                        } else {
                            $tmp['cTime'] = intval((time() - $tmp['cTime']) / 3600) . '小时前';
                        }
                    } else {
                        $tmp['cTime'] = date('Y年m月d日', $tmp['cTime']);
                    }
                    $tmp['haspic'] = '0';
                    $rtmp = unserialize($tmp['data']);


                    if (!empty($tmp['imgfile']) && $tmp['imgfile'] != 0) {

                        $tmp['haspic'] = '1';
                        $tmp['picture'] = $this->remote->getQiniuImage($tmp['imgfile']);
                        $tmp['height'] = 200;
                        $tmp['width'] = 200;
                    }else if (isset($rtmp[0]['path']) and $rtmp[0]['path']) {

                        $tmp['haspic'] = '1';
                        $tmp['picture'] = $this->remote->getLocalImage($rtmp[0]['path']);
                        $tmp['height'] = isset($rtmp[0]['height']) ? $rtmp[0]['height'] : 200;
                        $tmp['width'] = isset($rtmp[0]['width']) ? $rtmp[0]['width'] : 200;
                    }
                    $tmp['age'] = $this->getAge($tmp['uid']);
                    $tmp['level'] = $this->getLevel($tmp['jifen']);
                    unset($tmp['data']);
                    $touser = $this->getUserName($tmp['touid']);

                    if ($touser[0]['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $touser[0]['uname'])) {
                        $tmp['toname'] = substr($touser[0]['uname'], 0, 4) . '***';
                    } elseif ($touser[0]['uname'] == '') {
                        $tmp['toname'] = substr($touser[0]['phone'], 0, 4) . '***';
                    } else {
                        $tmp['toname'] = $touser[0]['uname'];
                    }

                    if ($tmp['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $tmp['uname'])) {
                        $tmp['fromname'] = substr($tmp['uname'], 0, 4) . '***';
                    } elseif ($tmp['uname'] == '') {
                        $tmp['fromname'] = substr($tmp['phone'], 0, 4) . '***';
                    } else {
                        $tmp['fromname'] = $tmp['uname'];
                    }
                    $tmp['fromname'] = $tmp['fromname'];
                    unset($tmp['uname']);
                    $tmp['comment'] = $tmp['comment'] . '';
                    $tmp['is_delete'] = $tmp['is_delete'] . '';
                    $tmp['banned'] && $tmp['is_delete'] = 1;

                }else{
                    $fields = 'users.banned,users.city,users.alias as uname,users.phone,users.id as uid, users.jifen, users.age,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete, c.imgfile';

                    $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";

                    $sql .= "WHERE  c.id = {$cid} and is_delete=0  and users.banned=0 ";
                    $tmp = $this->db->query($sql)->row_array();
                    $tmp['imgfile'] = $this->remote->getQiniuImage($tmp['imgfile']);

                    $tmp['comment'] = $this->emoji->str2emoji($tmp['comment']);
                    if (time() - $tmp['cTime'] < 3600 * 10) {
                        if (time() - $tmp['cTime'] < 3600) {
                            $tmp['cTime'] = intval((time() - $tmp['cTime']) / 60) . '分钟前';
                        } else {
                            $tmp['cTime'] = intval((time() - $tmp['cTime']) / 3600) . '小时前';
                        }
                    } else {
                        $tmp['cTime'] = date('Y年m月d日', $tmp['cTime']);
                    }
                    if ($tmp['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $tmp['uname'])) {
                        $tmp['uname'] = substr($tmp['uname'], 0, 4) . '***';
                    } elseif ($tmp['uname'] == '') {
                        $tmp['uname'] = substr($tmp['phone'], 0, 4) . '***';
                    }

                    unset($tmp['phone']);
                    $tmp['floor'] = $i;
                    $tmp['banned'] && $r['is_delete'] = 1;
                    if (!$tmp['uid']) {
                        $tmp['banned'] = 1;
                        $tmp['uid'] = 0;
                        $tmp['is_delete'] = 1;
                    }

                    if (isset($this->uid)) {
                        $is = $this->Diary_model->isZan($this->uid, $tmp['id'], 'topic_comments');
                        $tmp['isZan'] = $is ? 1 : 0;
                    } else {
                        $tmp['isZan'] = 0;
                    }
                    $r['zanNum'] = ($this->Diary_model->getZan($tmp['id'], 'topic_comments') > 0) ? $this->Diary_model->getZan($tmp['id'], 'topic_comments') : 0;
                    $tmp['age'] = $this->getAge($tmp['uid']);
                    $tmp['level'] = $this->getLevel($tmp['jifen']);
                    //clear user new_reply
                    $tmp['thumb'] = $this->profilepic($tmp['uid'], 2);
                    $tmp['city'] = isset($tmp['city'])?$tmp['city']:'';
                    if(isset($tmp['age'])){
                        $tmp['age'] = $this->getAge($tmp['uid']);
                    }else{
                        $tmp['age'] = '';
                    }
                    $tmp['haspic'] = '0';
                    $tmp1 = unserialize($tmp['data']);

                    if (!empty($tmp['imgfile']) && $tmp['imgfile'] != 0) {

                        $tmp['haspic'] = '1';
                        $tmp['picture'] = $this->remote->getQiniuImage($tmp['imgfile']);
                        $tmp['height'] = 200;
                        $tmp['width'] = 200;
                    }else if (isset($tmp1[0]['path']) and $tmp1[0]['path']) {
                        $tmp['haspic'] = '1';
                        $tmp['picture'] = $this->remote->getLocalImage($tmp1[0]['path']);
                        $tmp['height'] = isset($tmp1[0]['height']) ? $tmp1[0]['height'] : 200;
                        $tmp['width'] = isset($tmp1[0]['width']) ? $tmp1[0]['width'] : 200;
                    }
                    unset($tmp['data']);
                    $tmp['is_reply'] = '0';
                }
                $result['data'] = $tmp;

                $result['notice'] = '回复成功！';
            } else {
                $log['api'] = 'comments/sendcomment';
                $log['type'] = $this->input->post('type');
                $log['contentid'] = $this->input->post('contentid');
                $this->track_error->L($log);
                $result['notice'] = '信息不完整！';
                $result['state'] = '012';
            }
        }

        echo json_encode($result);
    }
    //last comments

    public function getLastComment($uid=0){

        if($uid <= 0)
            return;

        $this->db->where('fuid',$uid);
        $this->db->order_by('id desc');
        $this->db->limit(1);
        return $this->db->get('wen_comment')->result_array();

    }
    // comments like
    public function zan($param = '')
    {
        $result['state'] = '000';

        if ($wid = $this->input->post('id')) {
            $this->db->query("UPDATE wen_comment SET zan=zan+1 WHERE id = {$wid} LIMIT 1");
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    //check exists topic
    private function cktopic($wid)
    {
        $this->db->where('weibo_id', $wid);
        $this->db->select('weibo_id');
        $this->db->from('wen_weibo');
        return $this->db->count_all_results();
    }

    //get top parent comment id
    private function GPCID($pid)
    {
        $this->db->where('id', $pid);
        $this->db->select('pid,id,fuid');
        $query = $this->db->get('wen_comment')->result_array();
        if (!empty($query)) {
            $this->load->model('push');
            $this->push->sendUser('[话题]你的评论有新回复', $query[0]['fuid']);
            return $query[0]['id'];
        } else {
            $this->GPCID($query[0]['pid']);
        }
    }

    public function getPageCount($param = '')
    {
        $result['state'] = '000';
        if ($contentid = intval($this->input->get('contentid'))) {
            $this->db->where('type', 'topic');
            $this->db->where('contentid', $contentid);
            $this->db->where('pid', 0);
            $this->db->from('wen_comment');
            $num = $this->db->count_all_results() / 5;
            $result['data']['g'] = ceil($num);
            $result['data']['pageCount'] = ceil($num);
            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $sql .= "WHERE type = 'topic' and data like '%s:4:\"path\"%' and contentid={$contentid}  order by c.id ASC ";
            $num = $this->db->count_all_results() / 5;
            $result['commentsImage']['pageCount'] = ceil($num) ? ceil($num) : 0;

            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $this->db->where('weibo_id', $contentid);
            $item = $this->db->get('wen_weibo')->result_array();
            $sql .= "WHERE type = 'topic' and contentid={$contentid} and fuid={$item[0]['uid']}  order by c.id ASC";
            $num = $this->db->count_all_results() / 5;
            $result['commentsFloor']['pageCount'] = ceil($num) ? ceil($num) : 0;
        }
        echo json_encode($result);
    }

    // 获取楼主的评论
    public function getCommentFloor($params = '')
    {
        $result['state'] = '000';

        if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid')))) {
            if (!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
                $page = intval($this->input->get('page')) - 1;
                $start = $page < 1 ? 0 : $page * 5;

                $fields = 'users.banned,users.alias as uname,users.city,users.age,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete, c.imgfile';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                $this->db->where('weibo_id', $contentid);
                $item = $this->db->get('wen_weibo')->result_array();
                $sql .= "WHERE type = '{$type}' and contentid={$contentid} and fuid={$item[0]['uid']}  order by c.id ASC limit $start, 5";
                $tmp = $this->db->query($sql)->result_array();
                $result['data'] = array();
                $i = 1 + $start;
                $result['ans'] = array();
                if ($start == 0) {
                    $result['ans'] = $this->Gans($contentid);
                }

                foreach ($tmp as $r) {
                    $r['comment'] = $this->emoji->str2emoji($r['comment']);
                    if (time() - $r['cTime'] < 3600 * 10) {
                        if (time() - $r['cTime'] < 3600) {
                            $r['cTime'] = intval((time() - $r['cTime']) / 60) . '分钟前';
                        } else {
                            $r['cTime'] = intval((time() - $r['cTime']) / 3600) . '小时前';
                        }
                    } else {
                        $r['cTime'] = date('Y年m月d日', $r['cTime']);
                    }
                    if ($r['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])) {
                        $r['uname'] = substr($r['uname'], 0, 4) . '***';
                    } elseif ($r['uname'] == '') {
                        $r['uname'] = substr($r['phone'], 0, 4) . '***';
                    }

                    unset($r['phone']);
                    $r['floor'] = $i;
                    $r['banned'] && $r['is_delete'] = 1;
                    if (!$r['uid']) {
                        $r['banned'] = 1;
                        $r['uid'] = 0;
                        $r['is_delete'] = 1;
                    }

                    if (isset($this->uid)) {
                        $is = $this->Diary_model->isZan($this->uid, $r['id'], 'topic_comments');
                        $r['isZan'] = $is ? 1 : 0;
                    } else {
                        $r['isZan'] = 0;
                    }
                    $r['zanNum'] = ($this->Diary_model->getZan($r['id'], 'topic_comments') > 0) ? $this->Diary_model->getZan($r['id'], 'topic_comments') : 0;

                    //clear user new_reply
                    $this->setnew($r['id'], $r['uid']);
                    $r['thumb'] = $this->profilepic($r['uid'], 1);
                    $r['city'] = isset($r['city'])?$r['city']:'';
                    if(isset($r['age'])){
                        $r['age'] = $this->getAge($r['uid']);
                    }else{
                        $r['age'] = '';
                    }
                    $r['haspic'] = '0';
                    $tmp = unserialize($r['data']);
                    if (!empty($r['imgfile'])) {

                        $item['haspic'] = '1';
                        $item['picture'] = $this->remote->getQiniuImage($r['imgfile']);
                        $item['height'] = 200;
                        $item['width'] = 200;
                    }else if (isset($tmp[0]['path']) and $tmp[0]['path']) {
                        $r['haspic'] = '1';
                        $r['picture'] = $this->remote->getLocalImage($tmp[0]['path']);
                        $r['height'] = isset($tmp[0]['height']) ? $tmp[0]['height'] : 200;
                        $r['width'] = isset($tmp[0]['width']) ? $tmp[0]['width'] : 200;
                    }
                    unset($r['data']);
                    $r['is_reply'] = '0';
                    $fields = 'users.banned,c.fuid,users.alias as uname,users.phone,c.id,c.contentid,c.touid,c.comment,c.cTime,c.is_delete,c.data, c.imgfile';
                    $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                    $sql .= "WHERE type = '{$type}' and c.pid={$r['id']} and users.banned=0 order by c.id ASC";
                    $tmps = $this->db->query($sql)->result_array();
                    if (!empty($tmps)) {
                        $r['is_reply'] = '1';
                        foreach ($tmps as $item) {
                            $item['comment'] = $this->emoji->str2emoji($item['comment']);
                            if (time() - $r['cTime'] < 3600 * 10) {
                                if (time() - $r['cTime'] < 3600) {
                                    $item['cTime'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                                } else {
                                    $item['cTime'] = intval((time() - $item['cTime']) / 3600) . '小时前';
                                }
                            } else {
                                $item['cTime'] = date('Y年m月d日', $item['cTime']);
                            }
                            $item['haspic'] = '0';
                            $rtmp = unserialize($item['data']);
                            if (!empty($item['imgifle'])) {

                                $item['haspic'] = '1';
                                $item['picture'] = $this->remote->getQiniuImage($item['imgfile']);
                                $item['height'] = 200;
                                $item['width'] = 200;
                            }else if (isset($rtmp[0]['path']) and $rtmp[0]['path']) {
                                $item['haspic'] = '1';
                                $item['picture'] = $this->remote->getLocalImage($rtmp[0]['path']);
                                $item['height'] = isset($rtmp[0]['height']) ? $rtmp[0]['height'] : 200;
                                $item['width'] = isset($rtmp[0]['width']) ? $rtmp[0]['width'] : 200;
                            }
                            unset($item['data']);
                            $touser = $this->getUserName($item['touid']);

                            if ($touser[0]['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $touser[0]['uname'])) {
                                $item['toname'] = substr($touser[0]['uname'], 0, 4) . '***';
                            } elseif ($touser[0]['uname'] == '') {
                                $item['toname'] = substr($touser[0]['phone'], 0, 4) . '***';
                            } else {
                                $item['toname'] = $touser[0]['uname'];
                            }

                            if ($item['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $item['uname'])) {
                                $item['fromname'] = substr($item['uname'], 0, 4) . '***';
                            } elseif ($item['uname'] == '') {
                                $item['fromname'] = substr($item['phone'], 0, 4) . '***';
                            } else {
                                $item['fromname'] = $item['uname'];
                            }
                            $item['fromname'] = $item['fromname'];
                            unset($item['uname']);
                            $item['comment'] = $item['comment'] . '';
                            $item['is_delete'] = $item['is_delete'] . '';
                            $item['banned'] && $item['is_delete'] = 1;
                            $r['replay'][] = $item;
                        }
                    }
                    $tmps = null;


                    $result['data'][] = $r;
                    $i++;
                }

                $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                $this->db->where('weibo_id', $contentid);
                $item = $this->db->get('wen_weibo')->result_array();
                $sql .= "WHERE type = '{$type}' and contentid={$contentid} and fuid={$item[0]['uid']}  order by c.id ASC";
                $num = $this->db->count_all_results() / 5;
                $result['pageCount'] = ceil($num) ? ceil($num) : 0;
                $this->alicache->set($_SERVER['REQUEST_URI'], serialize($result));
            } else {
                $result = array();
                $result = unserialize($rs);
            }
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    //获取带图片的评论

    public function getCommentFloorImage($params = '')
    {
        $result['state'] = '000';

        if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid')))) {
            if (!($rs11 = $this->alicache->get($_SERVER['REQUEST_URI']))) {
                $page = intval($this->input->get('page')) - 1;
                $start = $page < 1 ? 0 : $page * 5;
                $fields = 'users.banned,users.age,users.city,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete, c.imgfile';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";

                $sql .= "WHERE type = '{$type}' and data like '%s:4:\"path\"%' and contentid={$contentid}  order by c.id ASC limit $start, 5";

                $tmp = $this->db->query($sql)->result_array();

                $result['data'] = array();
                $i = 1 + $start;
                $result['ans'] = array();
                if ($start == 0) {
                    $result['ans'] = $this->Gans($contentid);
                }

                foreach ($tmp as $r) {
                    $r['comment'] = $this->emoji->str2emoji($r['comment']);
                    if (time() - $r['cTime'] < 3600 * 10) {
                        if (time() - $r['cTime'] < 3600) {
                            $r['cTime'] = intval((time() - $r['cTime']) / 60) . '分钟前';
                        } else {
                            $r['cTime'] = intval((time() - $r['cTime']) / 3600) . '小时前';
                        }
                    } else {
                        $r['cTime'] = date('Y年m月d日', $r['cTime']);
                    }
                    if ($r['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])) {
                        $r['uname'] = substr($r['uname'], 0, 4) . '***';
                    } elseif ($r['uname'] == '') {
                        $r['uname'] = substr($r['phone'], 0, 4) . '***';
                    }

                    unset($r['phone']);
                    $r['floor'] = $i;
                    $r['banned'] && $r['is_delete'] = 1;
                    if (!$r['uid']) {
                        $r['banned'] = 1;
                        $r['uid'] = 0;
                        $r['is_delete'] = 1;
                    }

                    if (isset($this->uid)) {
                        $is = $this->Diary_model->isZan($this->uid, $r['id'], 'topic_comments');
                        $r['isZan'] = $is ? 1 : 0;
                    } else {
                        $r['isZan'] = 0;
                    }
                    $r['zanNum'] = ($this->Diary_model->getZan($r['id'], 'topic_comments') > 0) ? $this->Diary_model->getZan($r['id'], 'topic_comments') : 0;

                    //clear user new_reply
                    $this->setnew($r['id'], $r['uid']);
                    $r['thumb'] = $this->profilepic($r['uid'], 1);
                    $r['city'] = isset($r['city'])?$r['city']:'';
                    if(isset($r['age'])){
                        $r['age'] = $this->getAge($r['uid']);
                    }else{
                        $r['age'] = '';
                    }
                    $r['haspic'] = '0';
                    $tmp = unserialize($r['data']);
                    if (!empty($r['imgifle'])) {

                        $r['haspic'] = '1';
                        $r['picture'] = $this->remote->getQiniuImage($r['imgfile']);
                        $r['height'] = 200;
                        $r['width'] = 200;
                    }else if (isset($tmp[0]['path']) and $tmp[0]['path']) {
                        $r['haspic'] = '1';
                        $r['picture'] = $this->remote->getLocalImage($r[0]['path']);
                        $r['height'] = isset($tmp[0]['height']) ? $tmp[0]['height'] : 200;
                        $r['width'] = isset($tmp[0]['width']) ? $tmp[0]['width'] : 200;
                    }
                    unset($r['data']);
                    $r['is_reply'] = '0';


                    $fields = 'users.banned,c.fuid,users.alias as uname,users.phone,c.id,c.contentid,c.touid,c.comment,c.cTime,c.is_delete,c.data, c.imgfile';
                    $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                    $sql .= "WHERE type = '{$type}' and c.pid={$r['id']} and users.banned=0 order by c.id ASC";
                    $tmps = $this->db->query($sql)->result_array();
                    if (!empty($tmps)) {
                        $r['is_reply'] = '1';
                        foreach ($tmps as $item) {
                            $item['comment'] = $this->emoji->str2emoji($item['comment']);
                            if (time() - $r['cTime'] < 3600 * 10) {
                                if (time() - $r['cTime'] < 3600) {
                                    $item['cTime'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                                } else {
                                    $item['cTime'] = intval((time() - $item['cTime']) / 3600) . '小时前';
                                }
                            } else {
                                $item['cTime'] = date('Y年m月d日', $item['cTime']);
                            }
                            $item['haspic'] = '0';
                            $rtmp = unserialize($item['data']);
                            if (!empty($item['imgifle'])) {

                                $item['haspic'] = '1';
                                $item['picture'] = $this->remote->getQiniuImage($item['imgfile']);
                                $item['height'] = 200;
                                $item['width'] = 200;
                            }else if (isset($rtmp[0]['path']) and $rtmp[0]['path']) {
                                $item['haspic'] = '1';
                                $item['picture'] = $this->remote->getLocalImage($rtmp[0]['path']);
                                $item['height'] = isset($rtmp[0]['height']) ? $rtmp[0]['height'] : 200;
                                $item['width'] = isset($rtmp[0]['width']) ? $rtmp[0]['width'] : 200;
                            }
                            unset($item['data']);
                            $touser = $this->getUserName($item['touid']);

                            if ($touser[0]['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $touser[0]['uname'])) {
                                $item['toname'] = substr($touser[0]['uname'], 0, 4) . '***';
                            } elseif ($touser[0]['uname'] == '') {
                                $item['toname'] = substr($touser[0]['phone'], 0, 4) . '***';
                            } else {
                                $item['toname'] = $touser[0]['uname'];
                            }

                            if ($item['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $item['uname'])) {
                                $item['fromname'] = substr($item['uname'], 0, 4) . '***';
                            } elseif ($item['uname'] == '') {
                                $item['fromname'] = substr($item['phone'], 0, 4) . '***';
                            } else {
                                $item['fromname'] = $item['uname'];
                            }
                            $item['fromname'] = $item['fromname'];
                            unset($item['uname']);
                            $item['comment'] = $item['comment'] . '';
                            $item['is_delete'] = $item['is_delete'] . '';
                            $item['banned'] && $item['is_delete'] = 1;
                            $r['replay'][] = $item;
                        }
                    }
                    $tmps = null;

                    $result['data'][] = $r;
                    $i++;
                }
                $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                $sql .= "WHERE type = '{$type}' and data like '%s:4:\"path\"%' and contentid={$contentid}  order by c.id ASC ";
                $num = $this->db->count_all_results() / 5;
                $result['pageCount'] = ceil($num) ? ceil($num) : 0;
                $this->alicache->set($_SERVER['REQUEST_URI'], serialize($result));
            } else {
                $result = array();
                $result = unserialize($rs11);
            }
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    //get comments
    public function getAndroidComments($param = '')
    {
        $result['state'] = '000';

        if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid')))) {
            //if (!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {

            $lastid = $this->input->get('lastid') ? $this->input->get('lastid') : 0;
            $sort = $this->input->get('sort');
            $page = intval($this->input->get('page')) - 1;
            $page = intval($this->input->get('page')) - 1;
            $start = $page < 1 ? 0 : $page * 5;

            $fields = 'users.banned,users.city,users.alias as uname,users.phone,users.id as uid, users.jifen, users.age,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete, c.imgfile';

            if(strtolower($sort) == 'asc'){
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                if ($lastid) {

                    $sql .= "WHERE type = '{$type}' and c.id > {$lastid} and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit 5";
                } else {

                    $sql .= "WHERE type = '{$type}' and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit $start, 5";
                }
            }else{
                $num = $this->db->query("SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid WHERE type = '{$type}' and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0")->num_rows();
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                if ($lastid) {
                    $sql = "select *From (SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                    $sql .= "WHERE type = '{$type}' and c.id < {$lastid} and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id DESC limit 5) as tmp order by id ASC";
                } else {

                    if($num > 5){
                        $sql .= "WHERE type = '{$type}' and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit ".abs($num - 5).", 5";
                    }else{
                        $sql .= "WHERE type = '{$type}' and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit  5";
                    }
                }
            }
            $tmp = $this->db->query($sql)->result_array();
            $result['debug1'] = $this->db->last_query();
            $result['data'] = array();
            $i = 1 + $start;
            $result['ans'] = array();
            if ($start == 0) {
                $result['ans'] = $this->Gans($contentid);
            }

            foreach ($tmp as $r) {
                $r['comment'] = $this->emoji->str2emoji($r['comment']);
                $r['cTime'] = $this->getCommentDateTime($r['cTime']);
                if ($r['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])) {
                    $r['uname'] = substr($r['uname'], 0, 4) . '***';
                } elseif ($r['uname'] == '') {
                    $r['uname'] = substr($r['phone'], 0, 4) . '***';
                }

                unset($r['phone']);
                $r['floor'] = $i;
                $r['banned'] && $r['is_delete'] = 1;
                if (!$r['uid']) {
                    $r['banned'] = 1;
                    $r['uid'] = 0;
                    $r['is_delete'] = 1;
                }

                if (isset($this->uid)) {
                    $is = $this->Diary_model->isZan($this->uid, $r['id'], 'topic_comments');
                    $r['isZan'] = $is ? 1 : 0;
                } else {
                    $r['isZan'] = 0;
                }
                $r['zanNum'] = ($this->Diary_model->getZan($r['id'], 'topic_comments') > 0) ? $this->Diary_model->getZan($r['id'], 'topic_comments') : 0;
                $r['age'] = $this->getAge($r['uid']);
                $r['level'] = $this->getLevel($r['jifen']);
                //clear user new_reply
                $this->setnew($r['id'], $r['uid']);
                $r['thumb'] = $this->profilepic($r['uid'], 2);
                $r['city'] = isset($r['city'])?$r['city']:'';
                if(isset($r['age'])){
                    $r['age'] = $this->getAge($r['uid']);
                }else{
                    $r['age'] = '';
                }
                $r['haspic'] = '0';
                $tmp = unserialize($r['data']);

                if (!empty($r['imgfile'])) {

                    $r['haspic'] = '1';
                    $r['picture'] = $this->remote->getQiniuImage($r['imgfile']);
                    $r['height'] = 200;
                    $r['width'] = 200;
                }else if (isset($tmp[0]['path']) and $tmp[0]['path']) {
                    $r['haspic'] = '1';
                    $r['picture'] = $this->remote->getLocalImage($r[0]['path']);
                    $r['height'] = isset($tmp[0]['height']) ? $tmp[0]['height'] : 200;
                    $r['width'] = isset($tmp[0]['width']) ? $tmp[0]['width'] : 200;
                }
                unset($r['data']);
                $r['is_reply'] = '0';


                $fields = 'users.banned,c.fuid,users.alias as uname,users.phone, users.jifen, users.city, users.age,c.id,c.contentid,c.touid,c.comment,c.cTime,c.is_delete,c.data, c.imgfile';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                $sql .= "WHERE type = '{$type}' and c.pid={$r['id']} and users.banned=0 order by c.id ASC";
                $tmps = $this->db->query($sql)->result_array();
                if (!empty($tmps)) {
                    $r['is_reply'] = '1';
                    foreach ($tmps as $item) {
                        $item['comment'] = $this->emoji->str2emoji($item['comment']);
                        $item['cTime'] = $this->getCommentDateTime($item['cTime']);
                        $item['haspic'] = '0';
                        $rtmp = unserialize($item['data']);


                        if (!empty($item['imgfile'])) {

                            $item['haspic'] = '1';
                            $item['picture'] = $this->remote->getQiniuImage($item['imgfile']);
                            $item['height'] = 200;
                            $item['width'] = 200;
                        }else if (isset($rtmp[0]['path']) and $rtmp[0]['path']) {

                            $item['haspic'] = '1';
                            $item['picture'] = $this->remote->getLocalImage($rtmp[0]['path']);
                            $item['height'] = isset($rtmp[0]['height']) ? $rtmp[0]['height'] : 200;
                            $item['width'] = isset($rtmp[0]['width']) ? $rtmp[0]['width'] : 200;
                        }
                        $r['age'] = $this->getAge($r['uid']);
                        $r['level'] = $this->getLevel($r['jifen']);
                        unset($item['data']);
                        $touser = $this->getUserName($item['touid']);

                        if ($touser[0]['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $touser[0]['uname'])) {
                            $item['toname'] = substr($touser[0]['uname'], 0, 4) . '***';
                        } elseif ($touser[0]['uname'] == '') {
                            $item['toname'] = substr($touser[0]['phone'], 0, 4) . '***';
                        } else {
                            $item['toname'] = $touser[0]['uname'];
                        }

                        if ($item['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $item['uname'])) {
                            $item['fromname'] = substr($item['uname'], 0, 4) . '***';
                        } elseif ($item['uname'] == '') {
                            $item['fromname'] = substr($item['phone'], 0, 4) . '***';
                        } else {
                            $item['fromname'] = $item['uname'];
                        }
                        $item['fromname'] = $item['fromname'];
                        unset($item['uname']);
                        $item['comment'] = $item['comment'] . '';
                        $item['is_delete'] = $item['is_delete'] . '';
                        $item['banned'] && $item['is_delete'] = 1;
                        $r['replay'][] = $item;
                    }
                }
                $tmps = null;

                $result['data'][] = $r;
                $i++;
            }
            $this->db->where('type', 'topic');
            $this->db->where('contentid', $contentid);
            $this->db->where('pid', 0);
            $this->db->from('wen_comment');
            $num = $this->db->count_all_results() / 5;
            $result['pageCount'] = ceil($num);

            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $sql .= "WHERE type = 'topic' and data like '%s:4:\"path\"%' and contentid={$contentid}  order by c.id ASC ";
            $num = $this->db->count_all_results() / 5;
            $result['commentsImagePageCount'] = ceil($num) ? ceil($num) : 0;

            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $this->db->where('weibo_id', $contentid);
            $item = $this->db->get('wen_weibo')->result_array();
            $sql .= "WHERE type = 'topic' and contentid={$contentid} and fuid={$item[0]['uid']}  order by c.id ASC";
            $num = $this->db->count_all_results() / 5;
            $result['commentsFloorPageCount'] = ceil($num) ? ceil($num) : 0;
            //$this->alicache->set($_SERVER['REQUEST_URI'], serialize($result));
            /*} else {
                $result = array();
                $result = unserialize($rs);
            }*/
            $result['debug'] = $_SERVER['REQUEST_URI'];
        } else {
            $result['state'] = '012';
        }
        $result['pageSize'] = 5;
        echo json_encode($result);
    }
    //get comments
    public function Gcomments($param = '')
    {
        $result['state'] = '000';

        if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid')))) {
            //if (!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {

            $lastid = $this->input->get('lastid') ? $this->input->get('lastid') : 0;

            $page = intval($this->input->get('page')) - 1;
            $page = intval($this->input->get('page')) - 1;
            $start = $page < 1 ? 0 : $page * 5;
            $fields = 'users.banned,users.age,users.city,users.alias as uname,users.phone,users.id as uid, users.jifen, users.city, users.age,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete, c.imgfile';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            if ($lastid) {

                $sql .= "WHERE type = '{$type}' and c.id > {$lastid} and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit 5";
            } else {

                $sql .= "WHERE type = '{$type}' and contentid={$contentid} and is_delete=0  and pid=0 and users.banned=0 order by c.id ASC limit $start, 5";
            }
            $tmp = $this->db->query($sql)->result_array();
            $result['data'] = array();
            $i = 1 + $start;
            $result['ans'] = array();
            if ($start == 0) {
                $result['ans'] = $this->Gans($contentid);
            }

            foreach ($tmp as $r) {
                $r['comment'] = $this->emoji->str2emoji($r['comment']);
                if (time() - $r['cTime'] < 3600 * 10) {
                    if (time() - $r['cTime'] < 3600) {
                        $r['cTime'] = intval((time() - $r['cTime']) / 60) . '分钟前';
                    } else {
                        $r['cTime'] = intval((time() - $r['cTime']) / 3600) . '小时前';
                    }
                } else {
                    $r['cTime'] = date('Y年m月d日', $r['cTime']);
                }
                if ($r['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])) {
                    $r['uname'] = substr($r['uname'], 0, 4) . '***';
                } elseif ($r['uname'] == '') {
                    $r['uname'] = substr($r['phone'], 0, 4) . '***';
                }

                unset($r['phone']);
                $r['floor'] = $i;
                $r['banned'] && $r['is_delete'] = 1;
                if (!$r['uid']) {
                    $r['banned'] = 1;
                    $r['uid'] = 0;
                    $r['is_delete'] = 1;
                }

                if (isset($this->uid)) {
                    $is = $this->Diary_model->isZan($this->uid, $r['id'], 'topic_comments');
                    $r['isZan'] = $is ? 1 : 0;
                } else {
                    $r['isZan'] = 0;
                }
                $r['zanNum'] = ($this->Diary_model->getZan($r['id'], 'topic_comments') > 0) ? $this->Diary_model->getZan($r['id'], 'topic_comments') : 0;
                $r['age'] = $this->getAge($r['uid']);
                $r['level'] = $this->getLevel($r['jifen']);
                //clear user new_reply
                $this->setnew($r['id'], $r['uid']);
                $r['thumb'] = $this->profilepic($r['uid'], 2);
                $r['city'] = isset($r['city'])?$r['city']:'';
                if(isset($r['age'])){
                    $r['age'] = $this->getAge($r['uid']);
                }else{
                    $r['age'] = '';
                }
                $r['haspic'] = '0';
                $tmp = unserialize($r['data']);

                if (!empty($r['imgfile'])) {

                    $r['haspic'] = '1';
                    $r['picture'] = $this->remote->getQiniuImage($r['imgfile']);
                    $r['height'] = 200;
                    $r['width'] = 200;
                }else if (isset($tmp[0]['path']) and $tmp[0]['path']) {
                    $r['haspic'] = '1';
                    $r['picture'] = $this->remote->getLocalImage($r[0]['path']);
                    $r['height'] = isset($tmp[0]['height']) ? $tmp[0]['height'] : 200;
                    $r['width'] = isset($tmp[0]['width']) ? $tmp[0]['width'] : 200;
                }
                unset($r['data']);
                $r['is_reply'] = '0';


                $fields = 'users.banned,c.fuid,users.alias as uname,users.phone, users.jifen, users.city, users.age,c.id,c.contentid,c.touid,c.comment,c.cTime,c.is_delete,c.data, c.imgfile';
                $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
                $sql .= "WHERE type = '{$type}' and c.pid={$r['id']} and users.banned=0 order by c.id ASC";
                $tmps = $this->db->query($sql)->result_array();
                if (!empty($tmps)) {
                    $r['is_reply'] = '1';
                    foreach ($tmps as $item) {
                        $item['comment '] = $this->emoji->str2emoji($item['comment']);
                        if (time() - $r['cTime'] < 3600 * 10) {
                            if (time() - $r['cTime'] < 3600) {
                                $item['cTime'] = intval((time() - $item['cTime']) / 60) . '分钟前';
                            } else {
                                $item['cTime'] = intval((time() - $item['cTime']) / 3600) . '小时前';
                            }
                        } else {
                            $item['cTime'] = date('Y年m月d日', $item['cTime']);
                        }
                        $item['haspic'] = '0';
                        $rtmp = unserialize($item['data']);


                        if (!empty($item['imgfile'])) {

                            $item['haspic'] = '1';
                            $item['picture'] = $this->remote->getQiniuImage($item['imgfile']);
                            $item['height'] = 200;
                            $item['width'] = 200;
                        }else if (isset($rtmp[0]['path']) and $rtmp[0]['path']) {

                            $item['haspic'] = '1';
                            $item['picture'] = $this->remote->getLocalImage($rtmp[0]['path']);
                            $item['height'] = isset($rtmp[0]['height']) ? $rtmp[0]['height'] : 200;
                            $item['width'] = isset($rtmp[0]['width']) ? $rtmp[0]['width'] : 200;
                        }
                        $r['age'] = $this->getAge($r['uid']);
                        $r['level'] = $this->getLevel($r['jifen']);
                        unset($item['data']);
                        $touser = $this->getUserName($item['touid']);

                        if ($touser[0]['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $touser[0]['uname'])) {
                            $item['toname'] = substr($touser[0]['uname'], 0, 4) . '***';
                        } elseif ($touser[0]['uname'] == '') {
                            $item['toname'] = substr($touser[0]['phone'], 0, 4) . '***';
                        } else {
                            $item['toname'] = $touser[0]['uname'];
                        }

                        if ($item['uname'] != '' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $item['uname'])) {
                            $item['fromname'] = substr($item['uname'], 0, 4) . '***';
                        } elseif ($item['uname'] == '') {
                            $item['fromname'] = substr($item['phone'], 0, 4) . '***';
                        } else {
                            $item['fromname'] = $item['uname'];
                        }
                        $item['fromname'] = $item['fromname'];
                        unset($item['uname']);
                        $item['comment'] = $item['comment'] . '';
                        $item['is_delete'] = $item['is_delete'] . '';
                        $item['banned'] && $item['is_delete'] = 1;
                        $r['replay'][] = $item;
                    }
                }
                $tmps = null;

                $result['data'][] = $r;
                $i++;
            }
            $this->db->where('type', 'topic');
            $this->db->where('contentid', $contentid);
            $this->db->where('pid', 0);
            $this->db->from('wen_comment');
            $num = $this->db->count_all_results() / 5;
            $result['pageCount'] = ceil($num);

            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $sql .= "WHERE type = 'topic' and data like '%s:4:\"path\"%' and contentid={$contentid}  order by c.id ASC ";
            $num = $this->db->count_all_results() / 5;
            $result['commentsImagePageCount'] = ceil($num) ? ceil($num) : 0;

            $fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
            $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
            $this->db->where('weibo_id', $contentid);
            $item = $this->db->get('wen_weibo')->result_array();
            $sql .= "WHERE type = 'topic' and contentid={$contentid} and fuid={$item[0]['uid']}  order by c.id ASC";
            $num = $this->db->count_all_results() / 5;
            $result['commentsFloorPageCount'] = ceil($num) ? ceil($num) : 0;
            //$this->alicache->set($_SERVER['REQUEST_URI'], serialize($result));
            /*} else {
                $result = array();
                $result = unserialize($rs);
            }*/
            $result['debug'] = $_SERVER['REQUEST_URI'];
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    private function getUserName($uid = 0)
    {
        if ($uid < 0)
            return;
        $this->db->where('id', $uid);
        $this->db->select('alias as uname,phone');
        return $this->db->get('users')->result_array();
    }

    //get answer type commment
    private function Gans($id)
    {
        $res = array();
        $fields = 'wen_comment.*,users.alias as uname,user_profile.company,users.age,users.city';
        $gtmp = $this->db->query("SELECT {$fields} FROM wen_comment LEFT JOIN users  ON users.id = wen_comment.fuid LEFT JOIN user_profile ON user_profile.user_id = wen_comment.fuid WHERE wen_comment.contentid = {$id} AND type='ans' AND wen_comment.is_delete = 0 order by wen_comment.id DESC")->result_array();
        //  print_r($tmp);
        foreach ($gtmp as $row) {
            $row['comment'] = $this->emoji->str2emoji($row['comment']);
            $row['cdate'] = date('Y-m-d', $row['cTime']);
            $row['thumb'] = $this->profilepic($row['fuid'], 1);
            $row['city'] = isset($row['city'])?$row['city']:'';
            if(isset($row['age'])){
                $row['age'] = $this->getAge($row['fuid']);
            }else{
                $row['age'] = '';
            }
            $res[] = $row;
        }
        return $res;
    }
    //clear new_reply
    private function setnew($id = 0, $cuid = 0)
    {
        if ($id && $cuid && $cuid == $this->uid) {
            $data = array(
                'new_reply' => 0
            );
            $this->db->limit(1);
            $this->db->where('id', $id);
            $this->db->where('fuid', $cuid);
            $this->db->update('wen_comment', $data);
            $mec = new Memcache();
            $mec->connect('127.0.0.1', 11211);
            $mec->set('state' . $this->uid, array(), 0, 3600);
            $mec->close();
        }
    }

    //profile pic
    private function profilepic($id, $pos = 0)
    {
        switch ($pos) {
            case 1:
                return $this->remote->thumb($id, '36');
            case 0:
                return $this->remote->thumb($id, '250');
            case 2:
                return $this->remote->thumb($id, '120');
            default:
                return $this->remote->thumb($id, '120');
                break;
        }
    }

}