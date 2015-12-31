<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class doctor extends MY_Controller {
    var $path = '';
    private $uid = '';
    public function __construct() {
        parent :: __construct();
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            $this->notlogin = true;
        }
        $this->load->helper('cookie');
        $this->load->library('yisheng');
        $this->path = realpath(APPPATH . '../images');
        $this->load->model('remote');
        $this->load->model('Users_model');
        $this->load->model('auth');
    }
    //auto complte jquery search
    public function jsearch(){
        $result = array();
        //$jg = strip_tags(trim($this->input->get('jg')));
        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = intval($page -1)*10;
        $c = strip_tags(trim($this->input->get('ys')));
        $reg = "/[1-9][0-9]{0,}+/";
        $c = preg_replace($reg, '', $c);
        /*if($jg){
        	 $SQL = "select alias as name from users where role_id=2 and alias like '{$c}%' limit 10";
        }else{*/
            $SQL = "select u.alias as name from users as u LEFT JOIN user_profile as p ON u.id=p.user_id where u.role_id=2 and u.alias like '{$c}%' limit {$offset} ,10";
        //}
        if($c){
            $tmp = $this->db->query($SQL)->result_array();
            $result = $tmp;
        }
        echo json_encode($result);
    }


    public function search($param = '') {
        $result['state'] = '000';
        $result['data'] = array();
        $type = mysql_real_escape_string($this->input->get('newtype'));


        $sql = "SELECT users.utags,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,users.verify,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled";
        $sql .= ' FROM users LEFT JOIN user_profile ';
        $sql .= ' ON user_profile.user_id = users.id  WHERE ';

        if ($this->input->get('utype')) {
            $sql .= ' users.role_id = ' . $this->input->get('utype') . ' AND ';
        }
        if($this->input->get('district')){
            $sql .=" (user_profile.district LIKE '%".$this->input->get('district')."%' OR ";
            $sql .=" user_profile.address LIKE '%".$this->input->get('district')."%' ) AND ";

        }
///*		if ($this->input->get('lastid')) {
//			$lastid = $this->input->get('lastid')?$this->input->get('lastid'):0;
//			$sql .= " users.id >'" . $lastid. "' AND ";
//		}*/
        if ($this->input->get('city')) {
            $sql .= " user_profile.city = '" . $this->input->get('city') . "' AND ";
        }
        if ($this->input->get('department')) {
            $sql .= " user_profile.department LIKE '%," . $this->input->get('department') . ",%' AND ";
        }

        if($type != '') {
            if ($type != '认证专家') {
                if ($type == "全部") {
                    $sql .= " 1=1 AND ";
                } else {
                    $sql .= " user_profile.category LIKE '%" . $type . "%' AND ";
                }
            } else {
                $sql .= " users.suggested = '1'  AND ";
            }
        }
        if($this->input->get('province')){
            $sql .= " user_profile.province LIKE '%" . $this->input->get('province') . "%' AND ";
        }

        if ($this->input->get('name')) {
            $sql .= " users.username  LIKE '%" . $this->input->get('name') . "%' OR ";
            $sql .= " users.alias  LIKE '%" . $this->input->get('name') . "%' AND ";
        }

        if (strstr($sql, 'AND')) {
            $sql = substr($sql, 0, strlen($sql) - 4);
        } else {
            $sql = substr($sql, 0, strlen($sql) - 7);
        }

        if ($this->input->get('keys')) {
            $sql .= " AND (user_profile.company LIKE '%" . $this->input->get('keys') . "%' OR ";
            $sql .= " users.alias LIKE '%" . $this->input->get('keys') . "%' OR ";
            $sql .= " users.phone LIKE '%" . $this->input->get('keys') . "%')";

        }

        $forder = '';
        if($this->input->get('grade')){
            $forder .= ' users.grade DESC, ';

        }

        $sql .= " AND users.banned = 0";
        $sql .= ' ORDER BY '.$forder.' users.rank_search DESC ,users.id DESC';

        if ($this->input->get('page') && $this->input->get('page') != 1) {
            $start = $this->input->get('page') * 10;
            $sql .= " LIMIT $start,10 ";
        } else {
            $sql .= " LIMIT 0,10 ";
        }
        //$result['sql'] = $sql;
        $tmp = $this->db->query($sql)->result_array();
        if (!empty ($tmp)) {
            foreach ($tmp as $row) {
                $row['thumbUrl'] = $this->profilepic($row['user_id'], 2);
                switch ($row['sex']) {
                    case 1 :
                        $row['sex'] = '女';
                        break;
                    case 2 :
                        $row['sex'] = '男';
                        break;
                    default :
                        $row['sex'] = '保密';
                        break;
                }
                if ($row['department']) {
                    $row['department'] = $this->yisheng->search($row['department']);
                }
                $row['utags'] = explode(',',$row['utags']);
                $row['tconsult'] = $row['systconsult']+$row['tconsult']+rand(25,45);;
                $row['replys'] = $row['sysreplys']+$row['replys']+rand(45,95) ;

                $row['voteNum'] = $row['sysvotenum']+$row['voteNum'];
                $row['grade'] =  $row['sysgrade']>0?$row['sysgrade']:$row['grade'];
                unset($row['sysvotenum']);unset($row['sysgrade']);unset($row['systconsult']);;unset($row['sysreplys']);
                $row['position'] = str_replace('&nbsp;', ' ', $row['position']);
                $row['created'] = date('Y-m-d', $row['created']);
                $row['verify'] = $row['suggested'];
                $row['casenum'] = rand(50,100);
                unset($row['utags']);
                unset($row['introduce']);
                unset($row['skilled']);
                unset($row['city']);
                unset($row['created']);
                unset($row['tconsult']);
                unset($row['replys']);
                $result['data'][] = $row;
            }
        }
        echo json_encode($result);
    }

    public function answer($param = '') {
        $result['state'] = '000';

        if ($this->notlogin) {
            $result['ustate'] = '002';
        } else {
            $data = array (
                'uid' => $this->uid,
                'qid' => $this->input->post('qid'
                ), 'content' => $this->input->post('myaswer'), 'state' => 1, 'cdate' => time());
            $this->db->insert('wen_answer', $data);
            $result['postState'] = '000';
        }

        echo json_encode($result);
    }
    public function getAns($param = '') {
        $result['state'] = '000';
        if (FALSE && $this->notlogin) {
            $result['ustate'] = '002';
        } else {
            if ($qid = $this->input->get('qid')) {
                $result['data'] =  array();
                $fields = 'wen_answer.uid,wen_answer.id,wen_answer.content,wen_answer.new_comment,wen_answer.is_talk,wen_answer.cdate,user_profile.Lname,user_profile.Fname';
                if ($rid = $this->input->get('fromuid')) {
                    $tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND wen_answer.uid = {$rid} order by wen_answer.id ASC")->result_array();
                    $result['talks'] = $this->getcomment($qid, $rid);
                    $tmp[0]['cdate'] = date('Y-m-d', $tmp[0]['cdate']);
                    $result['thumb'] = $this->profilepic($tmp[0]['uid'], 1);
                    $result['data'] = $tmp[0];
                    $id = $tmp[0]['id'];
                    $this->db->query("UPDATE `wen_answer` SET `new_comment` = 0   WHERE `id` ={$id}");
                } else {
                    $tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC")->result_array();
                    foreach ($tmp as $row) {
                        $row['cdate'] = date('Y-m-d', $row['cdate']);
                        $row['thumb'] = $this->profilepic($row['uid'], 2);
                        $result['data'][] = $row;
                    }

                }
            } else {
                $result['state'] = '012';
            }
        }
        echo json_encode($result);
    }
    private function getcomment($qid = 0, $uid = 0) {
        $tmp = $this->db->query("SELECT talk.*,user_profile.Fname as tFname FROM talk LEFT JOIN user_profile ON user_profile.user_id = talk.touid WHERE talk.qid = {$qid} AND (talk.fuid = {$uid} OR talk.touid = {$uid}) order by talk.id ASC")->result_array();
        $result = array ();
        foreach ($tmp as $row) {
            $row['haspic'] = 0;
            $row['pic'] = '';
            if ($t = unserialize($row['data'])) {
                $row['pic'] = $this->remote->show($t['linkpic']) ;
                $row['haspic'] = 1;
            }
            unset ($row['qid']);
            unset ($row['data']);
            $row['cTime'] = date('Y-m-d H:i', $row['cTime']);
            $result[] = $row;
        }

        return $result;
    }
    public function talk($param = '') {
        $result['state'] = '000';

        if ($this->notlogin) {
            $result['ustate'] = '002';
        } else {
            if ($qid = $this->input->post('qid') and ((isset ($_FILES['attachPic']['tmp_name']) and $_FILES['attachPic']['tmp_name']!='') OR $this->input->post('comment'))) {
                $extra = $returnSend = array ();
                $returnSend['haspic'] = 0;
                if (isset ($_FILES['attachPic']['tmp_name'])) {
                    $name = uniqid() . '.jpg';
                    $savepath = date('Y') . '/' . date('m') . '/' .date('m') . '/'. $name;
                    if(!$this->remote->cp($_FILES['attachPic']['tmp_name'],$name,$savepath,array('width'=>600,'height'=>800),true)){
                        $result['state'] = '001';
                        $result['notice'] = '图片上传失败！';
                        echo json_encode($result);
                        exit;
                    }
                    $extra['linkpic'] = $savepath;
                    $returnSend['haspic'] = 1;
                    $returnSend['pic'] = $this->remote->show($savepath);

                }
                $data = array (
                    'fuid' => $this->uid,
                    'comment' => $this->input->post('comment'
                    ), 'contentid' => $qid, 'touid' => $this->input->post('touid'), 'status' => 1, 'type' => 'qa', 'data' => serialize($extra), 'cTime' => time());
                $this->db->insert('wen_comment', $data);
                $result['postState'] = '000';
                $returnSend['fuid'] = $this->uid;
                $returnSend['content'] = $this->input->post('comment');
                $returnSend['touid'] = $this->input->post('touid');
                $returnSend['cTime'] = date('Y-m-d H:i:s');
                $result['data'] = $returnSend;
                //chage state

                $tmp = $this->db->query("SELECT  users.id,users.email,wen_questions.title FROM users LEFT JOIN wen_questions ON wen_questions.fUid=users.id WHERE wen_questions.id = {$qid} LIMIT 1")->result_array();
                if($tmp['0']['id']=$this->uid){
                    $auid = $this->input->post('touid');
                    $tmp2 = $this->db->query("SELECT uid FROM question_state WHERE uid = {$auid} AND qid = {$qid} LIMIT 1")->result_array();
                    if(!empty($tmp2)){
                        $this->db->query("UPDATE `question_state` SET `new_reply` =`new_reply`+ 1  WHERE `qid` ={$qid} AND uid = {$auid}");
                    }else{
                        $insertData = array();
                        $insertData['uid'] =$auid;
                        $insertData['qid'] =$qid;
                        $insertData['new_reply'] = 1;
                        $insertData['cdate'] =time();
                        $this->common->insertData('question_state', $insertData);
                    }
                }
            } else {
                $result['state'] = '012';
            }
        }
        echo json_encode($result);
    }

    public function uinfo($param = '') {
        $result['state'] = '000';
        if($this->input->get('name')){
            $uid = $this->getId($this->input->get('name'),$this->input->get('company'));
        }else{
            $uid = intval($this->input->get('uid'));
        }
        if($uid){
            $tmp = $this->db->query("SELECT users.id,users.verify,users.alias,users.suggested,users.voteNum,users.jifen,users.grade,users.tconsult,users.replys,users.sysgrade,users.sysvotenum,users.sysreplys,users.systconsult,users.created,user_profile.Fname,user_profile.Lname,user_profile.department,user_profile.address,user_profile.introduce,user_profile.company,user_profile.skilled,user_profile.sex,user_profile.position FROM users LEFT JOIN user_profile ON user_profile.user_id = users.id WHERE users.id = {$uid} LIMIT 1")->result_array();

            $result['data'] = $tmp[0];
            $result['data']['voteNum'] = $result['data']['sysvotenum']>0?$result['data']['sysvotenum']:$result['data']['voteNum'];
            $result['data']['grade'] = $result['data']['sysgrade']>0?$result['data']['sysgrade']:$result['data']['grade'];
            $result['data']['department'] = $this->yisheng->search($result['data']['department']);
            $result['data']['username'] = $result['data']['alias'];
            unset ($result['data']['alias']);
            $result['data']['thumb'] = $this->profilepic($uid, 2);
            $abstate = false;
            $result['data']['ablum'] = $this->ablum($uid,$abstate);
            if($abstate ){
                $result['data']['hasthumb2'] = 1;
                foreach($result['data']['ablum'] as $r){
                    $result['data']['ablum_2'][] = $r.'_2.jpg';
                }
            }else{
                $result['data']['hasthumb2'] = 0;
            }
            if(!empty($result['data']['ablum'])){
                $result['data']['hasablum'] = 1;
            }else{
                $result['data']['hasablum'] = 0;
            }
            $result['data']['verify'] = $result['data']['suggested'];
            $result['data']['tconsult'] = $result['data']['systconsult']+$result['data']['tconsult']+rand(25,45);;
            $result['data']['replys'] = $result['data']['sysreplys']+$result['data']['replys']+rand(45,95) ;
            $result['data']['reviews'] = $this->getreviews($uid) ;
            $result['data']['qustions'] = $this->getqustions($uid);
            $result['data']['casenum'] = rand(50,1200);
            $result['data']['ryuyue']  = rand(5,20);
            $result['data']['rconsult']  = $result['data']['ryuyue']+rand(5,20);
            $result['data']['hasreviews'] = empty($result['data']['reviews'])?0:1;
            $edu = array('本科','硕士','博士');
            $result['data']['education'] = $edu[rand(0,2)];

        }else{
            $result['data'] = '';
        }
        echo json_encode($result);
    }
    //use doctor name get its id
    private function getId($name='',$company=''){
        if($name){
            $this->db->select('users.id');
            $this->db->where('users.alias', $name);
            if($company){
                $this->db->where('user_profile.company', $company);
                $this->db->join('user_profile', 'user_profile.user_id = users.id');
            }
            $this->db->limit(1);
            $tmp = $this->db->get('users')->result_array();
            if(!empty($tmp)){
                return $tmp[0]['id'];
            }else{
                return 0;
            }
        }
    }
    //count yuyue num
    private function getyuyue(){

    }
    private function getqustions($uid){
        $sql = "SELECT user_profile.sex,q.title,q.cdate,a.qid,w.type_data FROM wen_questions as q LEFT JOIN wen_weibo as w ON w.q_id = q.id LEFT JOIN user_profile ON q.fUid=user_profile.user_id LEFT JOIN wen_answer as a ON a.qid = q.id WHERE a.uid={$uid} and  w.type_data like '%savepath%' GROUP BY a.qid order by q.cdate desc limit 3";
        $rmp = $this->db->query($sql)->result_array();
        $tmp =  array();
        foreach($rmp as $r){
            $tmf = unserialize($r['type_data']);
            $r['pictures'] = array();
            if(isset($tmf['pic']['savepath'])){
                //$tmps = $this->db->get_where('wen_attach', array('id' => $tmf[1]['id']), 1,0)->result_array();

                $r['pictures'][] = $this->remote->show($tmf['pic']['savepath'],128);
            }
            $r['type_data'] = null;
            switch ($r['sex']) {
                case 1 :
                    $r['sex'] = '女';
                    break;
                case 2 :
                    $r['sex'] = '男';
                    break;
                default :
                    $r['sex'] = '保密';
                    break;
            }
            $r['cdate'] = date('Y-m-d',$r['cdate']);
            $tmp[] = $r;
        }
        return $tmp ;
    }
    private function ablum($abid,&$state=false){
        $sql = "SELECT savepath,id FROM c_photo WHERE userId = {$abid} AND isDel=0";
        $tmp = $this->db->query($sql)->result_array() ;
        $result = array();
        foreach($tmp as $r){
            $result[] = site_url().$r['savepath'];
        }
        (!empty($tmp) && file_exists('../'.$tmp[0]['savepath']))&&$state=true;
        return $result;
    }
    private function getreviews($abid){
        $sql = "SELECT reviews.review,reviews.score,reviews.created as reviewdate,p.email,p.phone FROM reviews LEFT JOIN users as p on p.id=reviews.userby WHERE reviews.userto = {$abid} AND reviews.type=1 order by reviewdate desc limit 3";
        $rmp = $this->db->query($sql)->result_array();
        $tmp =  array();
        foreach($rmp as $r){
            $r['showname'] = $r['phone']!=''?substr($r['phone'],0,3).'***':substr($r['email'],0,3).'***';
            unset($r['phone']);unset($r['email']);
            $r['reviewdate'] = date('Y-m-d',$r['reviewdate']);
            $tmp[] = $r;
        }
        return $tmp ;
    }

    public function questionState($param = '') {
        $result['state'] = '000';

        if (($qid = $this->input->post('qid')) ) {
            $num = $this->input->post('plus');
            $this->db->query("UPDATE `wen_questions` SET `new_answer` =`new_answer`-{$num}   WHERE `id` ={$qid}");
            if ($this->input->post('asyn') && $this->uid) {
                $this->dealState($num);
            }
            $result['updatestate'] = '000';
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    private function dealState($param = '') {
        $str = "UPDATE `wen_notify` SET ";
        $str .= 'new_answer=new_answer-' . $param  ;
        $str .= " WHERE user_id = " . $this->uid;
        $this->db->query($str);

    }
    //get question detail
    public function qdetail($param = '') {
        $result['state'] = '000';

        if ($qid = $this->input->get('qid')) {
            $tmp = $this->db->query("SELECT id,fUid,position,title,address,description,cdate FROM wen_questions WHERE id ={$qid} ORDER BY id DESC  LIMIT 1 ")->result_array();
            if (!empty ($tmp[0])) {
                $tmp[0]['cdate'] = date('Y-m-d', $tmp[0]['cdate']);
                if($tmp[0]['description']=='')$tmp[0]['description'] =  $tmp[0]['title'];
                $result['data'] = $tmp[0];
            } else {
                $result['data'] = null;
            }

        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    //get answer questions　lastid分页
    public function questions($param = '') {
        $result['state'] = '000';

        if ($uid = intval($this->input->get('uid'))) {
            $start = intval($this->input->get('page')-1) * 12;

            $sql = "SELECT wen_questions.id,wen_questions.position as tag,wen_questions.fuid,wen_questions.title, wen_questions.cdate,user_profile.sex,wen_questions.address,wen_weibo.type_data ";
            $sql.="FROM wen_questions LEFT JOIN wen_answer ON wen_questions.id = wen_answer.qid ";
            $sql.="LEFT JOIN user_profile ON user_profile.user_id = wen_questions.fuid ";
            $sql.="LEFT JOIN wen_weibo ON wen_weibo.q_id = wen_questions.id ";

            $sql .= "WHERE wen_answer.uid = {$uid} and wen_weibo.type=4 ORDER BY wen_questions.id DESC LIMIT $start,12";

            $tmp = $this->db->query($sql)->result_array();
            //echo $this->db->last_query();

            foreach ($tmp as $row) {

                switch ($row['sex']) {
                    case 1 :
                        $row['sex'] = '女';
                        break;
                    case 2 :
                        $row['sex'] = '男';
                        break;
                    default :
                        $row['sex'] = '保密';
                        break;
                }
                $row['pictures'] = array();
                $tmps =unserialize($row['type_data']);

                $user = $this->Users_model->get_user_name($row['fuid']);
                $row['alias'] = $user[0]['alias']?$user[0]['alias']:substr($user[0]['phone'],0,4)."***";

                $row['tag'] = array($row['tag']);
                unset($row['type_data']);
                if(isset($tmps[1]['id'])){
                    $ptmp = $this->db->get_where('wen_attach', array('id' => $tmps[1]['id']),1,0)->result_array();

                    $r['savepath'] = $this->remote->show($ptmp[0]['savepath'],128);

                    $r['width'] = $ptmp[0]['width'];
                    $r['height'] = $ptmp[0]['height'];
                    $row['pictures'][] = $r;
                }

                $row['thumb'] =$this->remote->thumb($row['fuid'],'50',1);
                $row['cdate'] = date('Y-m-d', $row['cdate']);
                $result['data'][] = $row;
            }


            $sqlCount = "SELECT wen_questions.id,wen_questions.position as tag,wen_questions.fuid,wen_questions.title, wen_questions.cdate,user_profile.sex,wen_questions.address,wen_weibo.type_data ";
            $sqlCount.="FROM wen_questions LEFT JOIN wen_answer ON wen_questions.id = wen_answer.qid ";
            $sqlCount.="LEFT JOIN user_profile ON user_profile.user_id = wen_questions.fuid ";
            $sqlCount.="LEFT JOIN wen_weibo ON wen_weibo.q_id = wen_questions.id ";

            $sqlCount .= "WHERE wen_answer.uid = {$uid} and wen_weibo.type=4 ORDER BY wen_questions.id DESC";

            $tmpCount = $this->db->query($sqlCount)->result_array();
            $i = 0 ;
            foreach ($tmpCount as $row) {

                $row['pictures'] = array();
                $tmps =unserialize($row['type_data']);

                $user = $this->Users_model->get_user_name($row['fuid']);
                $row['alias'] = $user[0]['alias']?$user[0]['alias']:substr($user[0]['phone'],0,4)."***";

                $row['tag'] = array($row['tag']);
                unset($row['type_data']);
                if(isset($tmps[1]['id'])){
                    $ptmp = $this->db->get_where('wen_attach', array('id' => $tmps[1]['id']),1,0)->result_array();

                    $r['savepath'] = $this->remote->show($ptmp[0]['savepath'],128);

                    $r['width'] = $ptmp[0]['width'];
                    $r['height'] = $ptmp[0]['height'];
                    $row['pictures'][] = $r;
                    $i ++;
                }

            }

            $count  = $i;
            $result['count'] = $count;
            $tmpage = $count/12;
            $result['pagesize'] = 12;
            if(is_int($tmpage)){
                $result['page'] = $tmpage;
            }else{
                $result['page'] = intval($tmpage)+1;
            }

        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    public function rating($param = '') {
        $result['state'] = '000';
        $uid = intval($this->input->get('uid'));
        $score = intval($this->input->get('score'));
        if ($uid && $score) {
            $this->db->select('grade,voteNum,id');
            $this->db->from('users');
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get()->result_array();
            if (!empty ($tmp)) {
                $vnum = $tmp[0]['voteNum'] + 1;
                $data = array (
                    'voteNum' => $vnum,
                    'grade' => ($tmp[0]['voteNum'] * $tmp[0]['grade'] + $score
                        ) / $vnum);
                $this->db->where('id', $uid);
                $this->db->update('users', $data);
            } else {
                $result['state'] = '016';
            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    public function toQuestion($param = '') {
        $result['state'] = '000';
        $uid = intval($this->input->get('uid'));
        $page = intval($this->input->get('page'));
        if ($uid && $page) {
            $this->db->select('id,fUid,position,title,city,address,cdate');
            $this->db->from('wen_questions');
            $this->db->where('toUid', $uid);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get()->result_array();
            $result['data'] = array();
            foreach($tmp as $r){
                $r['cdate'] = date('Y-m-d',$r['cdate']);
                $result['data'][] = $r;
            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1:
                return $this->remote->thumb($id,'36',2);
            case 0:
                return $this->remote->thumb($id,'250',2);
            case 2:
                return $this->remote->thumb($id,'120',2);
            default:
                return $this->remote->thumb($id,'120',2);
                break;
        }
    }
    private function getQstate($state = 0) {
        switch ($state) {
            case 1 :
                return '回答中';
                break;
            case 2 :
                return '关闭';
                break;
            case 4 :
                return '已过期';
                break;
            case 8 :
                return '已完结';
                break;
        }

    }
    public function getYiShengInfoByAddress($param = '韩国') {
        $result['state'] = '000';
        $result['ustate'] = '000';
        $result['doctor_info'] = '';

        if($this->notlogin) {
            $result['ustate'] = '002';
        }else{
            $sql = "select u.* ,up.* from users as u join user_profile as up on u.id = up.user_id where up.adress='".$param."'";
            $result['doctor_info'] = $this->db->query($sql);
        }

        return $result;
    }
}
?>