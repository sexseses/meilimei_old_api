<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__ . "/MyController.php");

class yiyuan extends MY_Controller
{
    public function __construct()
    {
        parent:: __construct();
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
        }
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $this->load->library('yisheng');
        $this->path = realpath(APPPATH . '../images');
        $this->load->model('auth');
        $this->load->model('remote');
        $this->load->model('Users_model');
        $this->load->library('alicache');
    }

    private function getDoctorNum($hospital=''){

        $sql = "SELECT users.utags,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,users.verify,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled";
        $sql .= ' FROM users LEFT JOIN user_profile ';
        $sql .= ' ON user_profile.user_id = users.id  WHERE ';

        if ($hospital) {
            $sql .= " user_profile.company = '" . $this->input->get('company') . "' ";
        }


        $sql .= " AND users.banned = 0";
        $sql .= ' ORDER BY users.id DESC';

        //$result['sql'] = $sql;
        $query = $this->db->query($sql);
        //echo "<pre>";

        return $query->num_rows()?$query->num_rows():0;
    }
    /**
     * @param $param
     */
    public function infos($param)
    {
        $this->load->model('Diary_model');
        $result['state'] = '000';
        $result['data'] = '';
        $uid = intval($this->input->get('id'));

        if ($uid == 0) {
            $uid = $this->getId(strip_tags(trim($this->input->get('name'))));
        }

        if ($uid != 0) {
            $tmp = $this->db->query("SELECT users.id,users.utags,users.alias,users.suggested as verify,users.voteNum,users.jifen,users.grade,users.tconsult,users.replys,users.sysgrade,users.sysvotenum,users.sysreplys as buildDate,users.systconsult,users.created,company.contactN,company.shophours,company.tel,company.address,company.descrition,company.weibo as ProjectIntroduction FROM users LEFT JOIN company ON company.userid = users.id WHERE users.id = {$uid} LIMIT 1")->result_array();
            $result['data'] = $tmp[0];
            if (strpos($result['data']['tel'], '、')) {
                $tmp = explode('、', $result['data']['tel']);
                $result['data']['tel'] = $tmp[0];
            }
            $result['data']['background'] = $this->gettBg($result['data']['utags']);
            $result['data']['ryuyue'] = rand(5, 20);
            $result['data']['rconsult'] = $result['data']['ryuyue'] + rand(5, 20);
            $result['data']['yuyue'] = rand(5, 20);
            $result['data']['replys'] = $result['data']['sysreplys'] > 0 ? $result['data']['sysreplys'] : $result['data']['replys'];
            $result['data']['voteNum'] = $result['data']['sysvotenum'] > 0 ? $result['data']['sysvotenum'] : $result['data']['voteNum'];
            $result['data']['grade'] = $result['data']['sysgrade'] > 0 ? $result['data']['sysgrade'] : $result['data']['grade'];
            $result['data']['doctorNum'] = '10';
            $result['data']['fanCount'] = $this->Diary_model->getFunCount($uid,10);
            $result['data']['recommend'] = '';
            $result['data']['Evaluation'] = rand(50,300);//随机
            $result['data']['descrition'] = strip_tags($result['data']['descrition']);
            $map = $this->getMap($uid);
            $result['data']['lng'] = $map[0]['lng'];
            $result['data']['lat'] = $map[0]['lat'];
            $result['data']['thumb'] = $this->profilepic($uid, 2);
            $abstate = false;
            $result['data']['ablum'] = $this->ablum($uid, $abstate);
            if ($abstate) {
                $result['data']['hasthumb2'] = 1;
                foreach ($result['data']['ablum'] as $r) {
                    $result['data']['ablum_2'][] = $r . '_2.jpg';
                }
            } else {
                $result['data']['hasthumb2'] = 0;
            }
            if (!empty($result['data']['ablum'])) {
                $result['data']['hasablum'] = 1;
            } else {
                $result['data']['hasablum'] = 0;
            }

            $result['data']['casenum'] = rand(50,1200);
            //$result['data']['buildDate'] = date('Y-m-d');
            $result['data']['created'] = date('Y-m-d',$result['data']['created']);
            $result['data']['reviews'] = $this->getreviews($uid);
            $result['data']['hasreviews'] = empty($result['data']['reviews']) ? 0 : 1;
        } else {
            $result['state'] = '000';
        }
        /*unset($result['data']['sysvotenum']);
        unset($result['data']['sysgrade']);
        unset($result['data']['systconsult']);;
        unset($result['data']['sysreplys']);
        unset($result['data']['sysreplys']);
        unset($result['data']['sysreplys']);
        unset($result['data']['sysreplys']);
        unset($result['data']['reviews']);
        unset($result['data']['hasreviews']);*/

        echo json_encode($result);
    }


    private function getMap($uid = 0){
        if(intval($uid) <0){
            return;
        }
        $this->db->where('userid',$uid);
        $temp = $this->db->get('map')->result_array();
        if(!empty($temp))
            return $temp;
        return;
    }
    //use jigou name get its id
    private function getId($name = '')
    {
        if ($name) {
            $this->db->select('userid');
            $this->db->where('name', $name);
            $this->db->limit(1);
            $tmp = $this->db->get('company')->result_array();
            if (!empty($tmp)) {
                return $tmp[0]['userid'];
            } else {
                return 0;
            }
        }
    }

    //get user home bg
    private function gettBg($tags = '')
    {
        $tags = str_replace(',', '', $tags);
        switch ($tags) {
            case '口腔':
                return 'http://static.meilimei.com.cn/images/userbg/image17@2x.png';
            case '彩妆':
                return 'http://static.meilimei.com.cn/images/userbg/image16@2x.png';
            case '美甲':
                return 'http://static.meilimei.com.cn/images/userbg/image18@2x.png';
            case '美发':
                return 'http://static.meilimei.com.cn/images/userbg/image15@2x.png';
            case '瑜伽':
                return 'http://static.meilimei.com.cn/images/userbg/image20@2x.png';
            case '瘦身纤体':
                return 'http://static.meilimei.com.cn/images/userbg/image14@2x.png';
            default:
                return 'http://static.meilimei.com.cn/images/userbg/image7@2x.png';
        }
    }

    //count yuyue num
    private function getyuyue()
    {

    }

    //get reviews lists
    private function getreviews($abid)
    {
        $sql = "SELECT reviews.review,reviews.score,reviews.created as reviewdate,p.email,p.phone FROM reviews LEFT JOIN users as p on p.id=reviews.userby WHERE reviews.userto = {$abid} AND reviews.type=2 ORDER BY reviews.id DESC limit 2";
        $rmp = $this->db->query($sql)->result_array();
        $tmp = array();
        foreach ($rmp as $r) {
            $r['showname'] = $r['phone'] != '' ? substr($r['phone'], 0, 3) . '***' : substr($r['email'], 0, 3) . '***';
            unset($r['phone']);
            unset($r['email']);
            $r['reviewdate'] = date('Y-m-d', $r['reviewdate']);
            $tmp[] = $r;
        }
        return $tmp;
    }

    public function getreview($param)
    {
        $result['state'] = '000';

        if ($uid = $this->input->get('uid')) {
            $page = intval($this->input->get('page'));
            $start = ($page - 1) * 10;
            $sql = "SELECT reviews.review,reviews.score,reviews.created as reviewdate,p.email,p.phone FROM reviews LEFT JOIN users as p on p.id=reviews.userby WHERE reviews.userto = {$uid} AND reviews.type=2 ORDER BY reviews.id DESC limit {$start},10";

            $rmp = $this->db->query($sql)->result_array();
            foreach ($rmp as $r) {
                $r['reviewdate'] = date('Y-m-d', $r['reviewdate']);
                $r['showname'] = $r['phone'] != '' ? substr($r['phone'], 0, 3) . '***' : substr($r['email'], 0, 3) . '***';
                unset($r['phone']);
                unset($r['email']);
                $result['data'][] = $r;
            }

        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function reviewState($param)
    {
        $result['state'] = '000';

        if ($uid = $this->input->get('uid')) {
            $this->db->select('id');
            $this->db->where('userto', $uid);
            $this->db->where('userby', $this->wen_auth->get_user_id());
            $this->db->from('reviews');
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    private function ablum($abid, &$state = false)
    {
        $sql = "SELECT savepath,id FROM c_photo WHERE userId = {$abid} AND isDel=0";
        $tmp = $this->db->query($sql)->result_array();
        $result = array();
        foreach ($tmp as $r) {
            $result[] = $this->remote->show(str_replace('upload/', '', $r['savepath']));
        }
        (!empty($tmp) && file_exists('../' . $tmp[0]['savepath'])) && $state = true;
        return $result;
    }

    function review($param = '')
    {
        $result['state'] = '000';

        if ($uid = $this->input->post('uid')) {
            $data['userto'] = $uid;
            $data['userby'] = $this->wen_auth->get_user_id();
            $data['type'] = 2;
            $data['score'] = $this->input->post('score') * 10;
            $data['review'] = $this->input->post('comment');
            $data['showtype'] = 3;
            $data['created'] = time();

            if ($this->db->query("SELECT id FROM reviews WHERE userto={$uid} AND  userby = {$data['userby']} AND reviews.type = 2")->num_rows()) {
                $result['postState'] = '001';
            } else {
                $result['postState'] = '000';
                $this->common->insertData('reviews', $data);
                $this->setScore($this->input->post('score'), $uid);
            }
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    private function setScore($param, $uid)
    {
        if ($uid) {
            $condition = array('id' => $uid);
            $tmp = $this->common->getTableData('users', $condition, 'voteNum,grade')->result_array();
            if (empty($tmp)) {
                $result['state'] = '400';
            } else {
                $score = ($param * 10 + $tmp[0]['grade'] * $tmp[0]['voteNum']) / ($tmp[0]['voteNum'] + 1);
                $data['grade'] = $score;
                $data['voteNum'] = $tmp[0]['voteNum'] + 1;
                $this->common->updateTableData('users', $uid, '', $data);
            }

        } else {
            $result['state'] = '012';
        }

    }

    public function getDoctorHospital($param = '') {
        $result['state'] = '000';
        $result['data'] = array();
        $sql = "SELECT users.utags,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,users.verify,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled";
        $sql .= ' FROM users LEFT JOIN user_profile ';
        $sql .= ' ON user_profile.user_id = users.id  WHERE ';

        if ($this->input->get('company')) {
            $count = $this->getDoctorNum($this->input->get('company'));
            $result['count'] = $count;
            $result['page'] = ceil($count/10);
            $sql .= " user_profile.company = '" . $this->input->get('company') . "' ";
        }
        $sql .=" and users.role_id=2 ";

        $sql .= " AND users.banned = 0";
        $sql .= ' ORDER BY users.rank_search DESC, users.grade DESC,users.id DESC';
        $limit = $this->input->get('limited')?$this->input->get('limited'):10;
        if ($this->input->get('page') && $this->input->get('page') != 1) {
            $start = $this->input->get('page') * 10;
            $sql .= " LIMIT $start,$limit ";
        } else {
            $sql .= " LIMIT 0,$limit ";
        }
        //$result['sql'] = $sql;
        $tmp = $this->db->query($sql)->result_array();
        //$result['sql'] = $this->db->last_query();
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
                unset($row['sysvotenum']);unset($row['sysgrade']);unset($row['systconsult']);unset($row['sysreplys']);
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

    public function getlist($param)
    {
        $result['state'] = '000';

        if ($this->input->get('city')) {
            $city = trim($this->input->get('city'));
            $page = intval($this->input->get('page'));
            $start = $page * 10;
            $this->db->limit(10, $start);
            $this->db->select('company.name,company.id,company.userid,company.address,company.tel,company.city,company.department,users.username,users.grade,users.suggested');
            $this->db->from('users');
            $this->db->join('company', 'company.userid = users.id');
            $this->db->order_by('company.state DESC,users.rank_search DESC, users.grade DESC,users.id DESC');
            $this->db->where('company.city =', $city);
            $this->db->where('users.banned =', 0);
            $tmp = $this->db->get()->result_array();

            foreach ($tmp as $row) {
                if (strpos($row['tel'], '、')) {
                    $tmp = explode('、', $row['tel']);
                    $row['tel'] = $tmp[0];
                }
                $row['juli'] = 0;
                $row['youhui'] = 0;
                if (strpos($row['tel'], '添加')) {
                    $row['tel'] = '暂无';
                }
                $row['thumb'] = $this->profilepic($row['userid'], 2);
                $result['data'][] = $row;
            }
        } else {
            $result['getState'] = '021';
        }


        echo json_encode($result);
    }
    private function getChild($type = 0){
        $tmp = array();
        $data = array();

        $sqlItem = "select id from items where name like '%".$type."%'";
        $citems = $this->db->query($sqlItem)->result_array();

        return $citems[0]['id'];
    }

    public function search($param = '')
    {
        $result['state'] = '000';

        $type = mysql_real_escape_string($this->input->get('newtype'));
        $pid = $this->getChild($type);
        $tmpitem = array();
        $typeSql = '';
        if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
            if ($pid) {
                $sqlItem = "select name from items where pid = '{$pid}'";
                $citems = $this->db->query($sqlItem)->result_array();


                if (!empty($citems)) {
                    foreach ($citems as $item) {
                        $typeSql .= " (users.utags like '%" . $item['name'] . "%') OR";
                        $tmpitem[] = $item['name'];
                    }
                }
            }

            $sqltmp = substr($typeSql, 0, strlen($typeSql) - 2);

            $sql = "SELECT company.id,company.name,company.tel,company.shophours,company.department,company.address,company.city,users.suggested as verify,users.suggested,company.userid,users.grade, users.created ";
            $forder = '';
            if (($curLat = $this->input->get('lat')) and $curLng = $this->input->get('lng')) {
                $sql .= ",( 6371 * acos( cos( radians( $curLat ) ) * cos( radians( lat ) )
                  * cos( radians( lng ) - radians( $curLng ) ) + sin( radians( $curLat ) )
                  * sin( radians( lat ) ) ) ) AS distance FROM company LEFT JOIN  map on company.userid=map.userid   ";
                $forder = ' distance ASC, ';
                $sql .= ' LEFT JOIN users ON company.userid = users.id  WHERE ';
            } else {
                $sql .= ' FROM company LEFT JOIN users ';
                $sql .= ' ON company.userid = users.id  WHERE ';
            }
            if ($this->input->get('consult')) {
                $forder .= ' users.tconsult DESC, ';
            }
            if ($this->input->get('grade')) {
                $forder .= ' users.grade DESC, ';

            }
            if ($this->input->get('city') and !$this->input->get('lat')) {
                $sql .= " company.city = '" . $this->input->get('city') . "' AND ";
            }
            if ($this->input->get('dist') and !$this->input->get('lat')) {
                $sql .= " (company.district LIKE '%" . $this->input->get('district') . "%' OR ";
                $sql .= " company.address LIKE '%" . $this->input->get('district') . "%' ) AND ";
            }

            if ($this->input->get('newtype')) {
                if ($this->input->get('newtype') == '全部') {
                    $sql .= " users.utags = '' AND ";
                } else {
                    if ($sqltmp)
                        $sql .= $sqltmp . " AND ";
                }
            }
//        if ($this->input->get('lastid')) {
//            $lastid = $this->input->get('lastid') ? $this->input->get('lastid') : 0;
//            $sql .= " company.userid >'" . $lastid . "' AND ";
//        }

            if ($this->input->get('department')) {
                $sql .= " company.department LIKE '%," . $this->input->get('department') . ",%' AND ";

            }
            if ($this->input->get('company')) {
                $sql .= " company.name LIKE '%" . $this->input->get('company') . "%' AND ";
            }


            if (strstr($sql, 'AND')) {
                $sql = substr($sql, 0, strlen($sql) - 4);
            } else {
                $sql = substr($sql, 0, strlen($sql) - 7);
            }
            if ($this->input->get('province')) {
                $sql .= " and company.province LIKE '%" . $this->input->get('province') . "%'";
            }

            if ($this->input->get('keys')) {
                $sql .= " AND (company.name LIKE '%" . $this->input->get('keys') . "%' OR ";
                $sql .= " company.address LIKE '%" . $this->input->get('keys') . "%' OR ";
                $sql .= " company.descrition LIKE '%" . $this->input->get('keys') . "%')";

            }
            $sql .= " AND users.role_id=3 and  users.banned = 0";
            $sql .= ' ORDER BY ' . $forder . ' users.rank_search DESC';
            //$sql .= ' ORDER BY company.userid ASC';

            if ($this->input->get('page') && $this->input->get('page') != 1) {
                $start = $this->input->get('page') * 10;
                $sql .= " LIMIT $start,10 ";
            } else {
                $sql .= " LIMIT 0,10 ";
            }

            //$result['sql'] = $sql;
            $tmp = $this->db->query($sql)->result_array();
            if (!empty($tmp)) {
                foreach ($tmp as $row) {
                    if (strpos($row['tel'], '添加')) {
                        $row['tel'] = '暂无';
                    }
                    $row['thumb'] = $this->profilepic($row['userid'], 2);
                    if ($row['department']) {
                        $row['department'] = $this->yisheng->search($row['department']);
                    }
                    $cid = $this->getCompanyId($row['name']);
                    $row['tehui'] = $this->getTehuiByJigouId($cid);
                    $row['recommend'] = '';
                    $row['tehuiCount'] = count($row['tehui']);
                    $row['verifyCount'] = $this->getVerifyCount($row['name']);
                    $row['chenghao'] = '';
                    $row['created'] = date('Y-m-d', $row['created']);
                    $row['casenum'] = rand(50, 100);
                    $result['data'][] = $row;
                }
            }

            $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        }else{
            $result = array();
            $result = unserialize($rs);
        }


        echo json_encode($result);
    }

    private function getVerifyCount($company){
        $sql = "SELECT users.utags,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,users.verify,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled";
        $sql .= ' FROM users LEFT JOIN user_profile ';
        $sql .= ' ON user_profile.user_id = users.id  WHERE ';

        if ($company) {
            $sql .= " users.suggested=1 and user_profile.company = '" .$company . "' ";
        }


        $sql .= " AND users.banned = 0";
        $sql .= ' ORDER BY users.id DESC';

        //$result['sql'] = $sql;
        $query = $this->db->query($sql);
        //echo "<pre>";

        return $query->num_rows()?$query->num_rows():0;
    }
    private function getCompanyId($name = ''){
        if($name == '')
            return;
        $this->db->where('name',$name);
        $rs = $this->db->get('company')->result_array();
        return $rs[0]['userid'];
    }

    private function getTehuiByJigouId($m_id){
        $result['t_rs'] = array ();
        $mechanism = "";


        if(!empty($m_id)){
            $this->db->where('userid',$m_id);
            $company = $this->db->get('company')->row_array();

            $this->db->where('id',$company['id']);
            $company = $this->db->get('company')->row_array();

            if($company){
                $mechanism = $company['name'];
            }

            $tr_sql = "select tehui_id from tehui_relation where 1=1 and mechanism = ?";
            $tr_rs = $this->db->query($tr_sql,array($company['id']))->result_array();

        }else{
            return;
        }

        if($tr_rs){
            foreach ($tr_rs as &$trv) {
                $trv = $trv['tehui_id'];
            }
            $tr_rs = implode(',',$tr_rs);
            $tehui_fields = 't.id,t.title,t.team_price';
            $time = time();
            $tehui_condition = " and t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}' and flashSale = 0 and t.id in ({$tr_rs})";
            $tehui_info = $this->tehuiDB->query("SELECT {$tehui_fields} FROM team as t WHERE 1=1 {$tehui_condition}")->result_array();
            //echo "SELECT {$tehui_fields} FROM team as t WHERE 1=1 {$tehui_condition}";
            //exit();
            $randpic = date('Ymdhi',time());
            foreach ($tehui_info as &$r) {
                if(!strstr($r['image'],"http://pic")  && strpos($r['image'], 'clouddn.com') === false) {
                    $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
                }
                $r['mechanism'] = $mechanism;
                $r['order_num'] = rand(66, 88);
                $r['case_num'] = rand(50,66);
            }

            return $tehui_info;
        }else{
            return;
        }
    }
    //auto complte jquery search
    public function jsearch()
    {
        $result = array();
        $skey = array('上' => 1, '上海' => 1, '北' => 1, '北京' => 1, '广州' => 1, '广' => 1, '杭州' => 1, '杭' => 1, '中国' => 1, '中' => 1, '成都' => 1, '成' => 1);

        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = intval($page -1)*10;

        $c = strip_tags(trim($this->input->get('yy')));
        $SQL = "select name from company where name like '{$c}%' limit {$offset},10";
        if ($c) {
            if (isset($skey[$c])) {
                $mec = new Memcache();
                $mec->connect('127.0.0.1', 11211);
                if ($result = $mec->get('k_' . $c)) {

                } else {
                    $result = $this->db->query($SQL)->result_array();
                    $mec->set('k_' . $c, $result, 0, 7200);
                }
                $mec->close();
            } else {
                $tmp = $this->db->query($SQL)->result_array();
                $result = $tmp;
            }
        }
        echo json_encode($result);
    }

    public function simplelist($param)
    {
        $result['state'] = '000';

        if ($this->input->get('city')) {
            $city = trim($this->input->get('city'));
            $page = intval($this->input->get('page'));
            $size = intval($this->input->get('pagesize'));
            $size == 0 && $size = 20;
            $start = $page * $size;
            $tmp = $this->db->query("SELECT name,id,userid FROM company WHERE city = '$city' LIMIT $start,$size")->result_array();
            $result['data'] = $tmp;
        } else {
            $result['getState'] = '021';
        }


        echo json_encode($result);
    }

    private function profilepic($id, $pos = 0)
    {
        switch ($pos) {
            case 1:
                return $this->remote->thumb($id, '36', 3);
            case 0:
                return $this->remote->thumb($id, '250', 3);
            case 2:
                return $this->remote->thumb($id, '120', 3);
            default:
                return $this->remote->thumb($id, '120', 3);
                break;
        }
    }

}

?>