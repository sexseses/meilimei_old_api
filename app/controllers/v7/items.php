<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api info Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class items extends MY_Controller {
    public function __construct() {
        parent :: __construct();
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            $this->notlogin = true;
        }
        $this->load->model('auth');
        $this->load->model('remote');
        $this->load->library('alicache');
    }
    function getItems($param = '') {

        $pid = intval($this->input->get('pid'));
        if ($pid == 261) {
            $this->db->where('is_hot', 1);
        } else {
            $this->db->where('pid', $pid);
        }
        $this->db->select('id, pid, name, surl, burl');
        $this->db->order_by("order", "desc");
        $tmp = $this->db->get('new_items')->result_array();
        $result['data'] = array ();
        foreach ($tmp as $r) {
            $r['burl'] = site_url() . 'upload/' . $r['burl'];
            $r['surl'] = site_url() . 'upload/' . $r['surl'];
            $result['data'][] = $r;
        }

        echo json_encode($result);
    }

    public function addItem($params = ""){
        $itemName = $this->input->get('itemname');
        $result['state'] = '000';
        $result['data'] = array();

        if($this->uid && !empty($itemName)){
            $result['data'] = $this->db->insert('new_items',array('pid'=>362,'name'=>$itemName));
        }else{
            $result['notice'] = '参数不全';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    //auto complte jquery search
    public function jsearch() {
        $result = array ();
        $c = strip_tags(trim($this->input->get('xm')));
        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = intval($page -1)*10;

        $SQL = "select name from new_items where name like '{$c}%' limit {$offset},10";
        /*if ($c) {
            $mec = new Memcache();
            $mec->connect('127.0.0.1', 11211);
            if ($result = $mec->get('itm_' . $c)) {
            } else {*/
                $result = $this->db->query($SQL)->result_array();
                /*if (!empty ($result)) {
                    $mec->set('itm_' . $c, $result, 0, 1800);
                }*/
            /*}
            $mec->close();
        }*/
        echo json_encode($result);
    }
    function cItems($param = '') {

        $result['state'] = '000';
        $this->db->select('id, pid, name,surl,burl');
        $this->db->order_by("order", "desc");
        $this->db->where("app", 1);
        $tmp = $this->db->get('new_items')->result_array();
        $this->db->last_query();
        $result['data'] = array ();

        foreach ($tmp as $r) {
            $r['burl'] = $this->remote->show($r['burl']);
            $r['surl'] = $this->remote->show($r['surl']);
            $result['data'][] = $r;
        }

        echo json_encode($result);
    }
    function allItems($param = '') {

        $result['state'] = '000';

        $result['data'] = $this->getChild(0,$this->uid);

        echo json_encode($result);
    }

    private function compareCategory($version){

    }

    function getQuestionItems(){
        $result['state'] = '000';
        $version = $this->input->get('category_version');
        /*if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {*/
            $result['data'] = $this->getChild(0);
            foreach ($result['data'] as $key => $item) {
                foreach ($item['child'] as $k => $ii) {

                    $ii['child'] = $this->getChild($ii['id'], $this->uid) ? $this->getChild($ii['id'], $this->uid) : array();
                    $item['child'][$k] = $ii;

                }
                $result['data'][$key] = $item;
            }

            $result['hot'] = $this->getHot();
        /*    $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        }else{
            $result = array();
            $result = unserialize($rs);
        }*/
        $result['category_version'] = '1.0';
        echo json_encode($result);
    }
    function getQuestionItemsIOS(){
        $result['state'] = '000';
        $version = $this->input->get('category_version');
        /*if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {*/
        $result['data'] = $this->getChild(0);
        foreach ($result['data'] as $key => $item) {
            foreach ($item['child'] as $k => $ii) {

                $ii['child'] = $this->getChild($ii['id'], $this->uid) ? $this->getChild($ii['id'], $this->uid) : array();
                $item['child'][$k] = $ii;

            }
            $result['data'][$key] = $item;
        }

        $result['hot'] = $this->getHot();
        /*    $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        }else{
            $result = array();
            $result = unserialize($rs);
        }*/
        $result['category_version'] = '1.0';
        echo json_encode($result);
    }

    function getAllItems($param = '') {

        $result['state'] = '000';
        $version = $this->input->get('category_version');
        //$this->uid = 201964;
        $result['data'] = $this->getChild(0,$this->uid);

        foreach($result['data'] as $key=>$item){
            foreach($item['child'] as $k=>$ii){

                $ii['child'] = $this->getChild($ii['id'],$this->uid) ? $this->getChild($ii['id'],$this->uid): array();

                $item['child'][$k] = $ii;

            }

            $result['data'][$key] = $item;
        }
        $result['hot'] = $this->getHot();
        $result['category_version'] = '1.0';
        echo json_encode($result);
    }

    function itemsAll($param = '') {

        $result['state'] = '000';
        $result['data'] = $this->getChildItem(0,$this->uid);

        $result['category_version'] = '1.0';
        echo json_encode($result);
    }

    private function getHot($pid = 261){
        $tmp = array();
        $this->db->where('is_hot',1);
        $this->db->select('id, pid, name,burl,colors,is_hot as num,img_png as surl');
        $tmp = $this->db->get('new_items')->result_array();
        $data = array();

        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show($item['burl']);
            $item['surl'] = $this->remote->show320($item['surl']);

            $data[] = $item;
        }

       // array('#7ee3a1'=>'126,227,161','#ff91b4'=>'255,145,180','#ce83f1'=>'206,131,241','#fe7951'=>'254,121,81');
        return $data;
    }

    private function getChildItem($pid = 0,$uid=0){
        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,surl,colors,is_hot as num,img_x as burl');
        $this->db->order_by('order asc');
        $tmp = $this->db->get('new_items')->result_array();

        $data = array();
        $key = array('261');
        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show320($item['burl']);
            $item['surl'] = $this->remote->show($item['surl']);

            if ($item['id'] == 261 or $item['id'] == 362 or $item['id'] == 399)
                continue;

            $itemtmp = array();
            $this->db->where('pid',$item['id']);
            $this->db->select("id, pid, name,burl,colors,is_hot as num, img_l as surl");
            $itemtmp = $this->db->get('new_items')->result_array();

            foreach($itemtmp as $k=>$i){
                if($uid) {

                    if ($this->isstate($itemtmp[$k]['id'], 9)) {

                        $itemtmp[$k]['follow'] = 1;//$this->remote->show($itemtmp[$k]['burl']);
                    } else {

                        $itemtmp[$k]['follow'] = 0;//$this->remote->show($itemtmp[$k]['burl']);
                    }
                }else{
                    $itemtmp[$k]['follow'] = 0;
                }

                $itemtmp[$k]['burl'] = $this->remote->show320($itemtmp[$k]['burl']);
                $itemtmp[$k]['surl'] = $this->remote->show($itemtmp[$k]['surl']);
                $tmpitem = $itemtmp;
            }
            $item['child'] = $tmpitem ? $tmpitem : array();
            $data[] = $item;
        }
        return $data;
    }

    private function getChild($pid = 0,$uid=0){
        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,burl,colors,is_hot as num,img_png as surl');

        $this->db->order_by('order asc');
        $tmp = $this->db->get('new_items')->result_array();

        $data = array();
        $key = array('261');
        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show($item['burl']);
            $item['surl'] = $this->remote->show320($item['surl']);

            if ($item['id'] == 261 or $item['id'] == 362 or $item['id'] == 399)
                continue;

            $itemtmp = array();
            $this->db->where('pid',$item['id']);
            $this->db->select("id, pid, name,surl,burl,colors,is_hot as num, img_png as surl");
            $itemtmp = $this->db->get('new_items')->result_array();

            foreach($itemtmp as $k=>$i){
                if($uid) {
                    if ($this->isstate($itemtmp[$k]['id'], 9)) {

                        $itemtmp[$k]['follow'] = 1;//$this->remote->show($itemtmp[$k]['burl']);
                    } else {

                        $itemtmp[$k]['follow'] = 0;//$this->remote->show($itemtmp[$k]['burl']);
                    }
                }else{
                    $itemtmp[$k]['follow'] = 0;
                }

                $itemtmp[$k]['burl'] = $this->remote->show($itemtmp[$k]['burl']);
                $itemtmp[$k]['surl'] = $this->remote->show320($itemtmp[$k]['surl']);
                $tmpitem = $itemtmp;
            }
            $item['child'] = $tmpitem ? $tmpitem : array();
            $data[] = $item;
        }
        return $data;
    }


    private function isstate($followuser,$type) {

        if ($followuser) {

            if ($followuser) {
                $result['follow'] = '0';
                $condition = array (
                    'uid' => $followuser,
                    'fid' => $this->uid,
                    'type'=> $type
                );

                $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();

                if ($tmp > 0) {
                    return 1;
                }

            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 0;
    }

    //new get item info width more info api
    function getIMtemInfo($param = '') {

        if ($id = intval($this->input->get('id'))) {
            $result['state'] = '000';
            $this->db->where('id', $id);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get('new_items')->result_array();
            $result['iteminfo'] = $tmp[0];
            if($tmp[0]['surl']) {
                $result['iteminfo']['surl'] = $this->remote->show($tmp[0]['surl']);
                $result['iteminfo']['burl'] = $this->remote->show($tmp[0]['burl']);
            }else{
                $result['iteminfo']['surl'] = "http://pic.meilimei.com.cn/upload/fenlitu/jiazai.jpg";
                $result['iteminfo']['burl'] = "http://pic.meilimei.com.cn/upload/fenlitu/katong.jpg";
            }
            $result['iteminfo']['items'] = $this->getTags();
            $result['tehui'] = $this->getSales($tmp[0]['name']);
            $result['price'] = $this->getPrice($tmp[0]['id']);
            $result['doctor'] = $this->getDoctor($tmp[0]['name']);
        } else {
            $result['notice'] = '参数不全';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    // get Doctor with tags
    private function getDoctor($tag = ''){
        $sql = "select p.company,u.alias,u.id,u.suggested,u.grade,u.verify from users as u ";
        $sql .= "LEFT JOIN user_profile as p ON p.user_id = u.id where u.utags = '{$tag}' limit 5";
        $tmpinfo = $this->db->query($sql)->result_array();
        $res = array();
        foreach($tmpinfo as $r){
            $r['thumb'] = $this->profilepic($r['id']);
            $res[] = $r;
        }
        return $res;
    }
    // get price fo jigou
    private function getPrice($id = ''){
        $sql = "select p.price,c.userid,c.name FROM price as p";
        $sql .= " LEFT JOIN company as c ON c.id = p.company_id where p.item_id = '{$id}' limit 5";
        $tmpinfo = $this->db->query($sql)->result_array();
        return $tmpinfo;
    }
    // get tehui product with tags
    private function getSales($tag = '') {
        $time = time();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $fields = 't.id,t.user_id,t.title,t.summary,t.image,t.team_price, t.now_number,t.market_price';
        $condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}'";
        $condition .= " AND INSTR(t.tags,'{$tag}')";
        $order = ' t.sort_order DESC,t.begin_time DESC, t.id DESC';
        $res = array ();
        $tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t  WHERE {$condition} ORDER by {$order} limit 5 ")->result_array();

        $randpic = date('Ymdhi', time());
        foreach ($tmpinfo as $r) {
            $r['team_price'] = intval($r['team_price']);
            $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'] . '?' . $randpic;
            $res[] = $r;
        }
        return $res;
    }
    //get sumarize intem info api
    public function getItemChildList($param = '') {

        $id = $result['data'] = '';
        if($name = $this->input->get('name')){
            $id = $this->getId($name);
        }
        if ($id OR ($id = intval($this->input->get('id')))) {
            $result['state'] = '000';
            $this->db->where('id', $id);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get('new_items')->result_array();
            $result['data'] = array ();
            $result['data']['id'] = $tmp[0]['id'];
            $result['data']['pid'] = $tmp[0]['pid'];
            $result['data']['name'] = $tmp[0]['name'];

            $result['data']['price'] = intval($tmp[0]['price']);
            $result['data']['des'] = $tmp[0]['des'];

            $result['data']['child'] = $this->getItemChild($tmp[0]['id'])?$this->getItemChild($tmp[0]['id']):array();
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function getChildLevel(){

        $id = $result['data'] = array();
        if($name = $this->input->get('name')){
            $id = $this->getId($name);
        }
        if ($id OR ($id = intval($this->input->get('id')))) {
            $result['state'] = '000';

            $this->db->select('name,des,img_png as surl');
            $this->db->where('pid', $id);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get('new_items')->result_array();
            $result['data'] = array ();
            $result['data']['item'] = array();
            if(!empty($tmp)) {
                foreach($tmp as $key=>$item) {
                    $result['data']['item'][$key]['des'] = $item['des'];
                    $result['data']['item'][$key]['name'] = $item['name'];
                    $result['data']['item'][$key]['surl'] = "http://pic.meilimei.com.cn/upload/fenlitu%2Ftouming%2Fzx%2Fzx_kq%402x.png";//$this->remote->show320($item['surl']);
                }
            }
            $result['data']['count'] = count($result['data']['item']);
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function getChildLevelItem(){

        $id = $result['data'] = '';
        if($name = $this->input->get('name')){
            $id = $this->getId($name);
        }
        if ($id OR ($id = intval($this->input->get('id')))) {
            $result['state'] = '000';

            $this->db->select('name,des, treatment as dstreatments');
            $this->db->where('pid', $id);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get('new_items')->result_array();
            $result['data'] = array ();
            if(!empty($tmp)) {

                foreach($tmp as $key=>$item) {
                    $result['data']['item'][$key]['des'] = $item['des'];
                    $result['data']['item'][$key]['name'] = $item['name'];
                    $result['data']['item'][$key]['dstreatments'] = $item['dstreatments'];
                }
            }

            $this->db->select('u.id,u.grade,u.verify, u.suggested,u.username,up.position, up.skilled, up.company, up.user_id');
            $this->db->from('users u');
            $this->db->where('up.skilled <>','');
            $this->db->where('u.role_id',2);
            $this->db->join('user_profile up','u.id=up.user_id');
            $this->db->limit(103);
            $doctors = $this->db->get()->result_array();

            if(!empty($doctors)){
                foreach($doctors as $key=>$doctor){
                    $result['data']['doctors'][$key]['position'] = $doctor['position'];
                    $result['data']['doctors'][$key]['skilled'] = $doctor['skilled'];
                    $result['data']['doctors'][$key]['company'] = $doctor['company'];
                    $result['data']['doctors'][$key]['uid'] = $doctor['id'];
                    $result['data']['doctors'][$key]['grade'] = $doctor['grade'];
                    $result['data']['doctors'][$key]['verify'] = $doctor['verify'];
                    $result['data']['doctors'][$key]['suggested'] = $doctor['suggested'];
                    $result['data']['doctors'][$key]['username'] = $doctor['username'];
                    $result['data']['doctors'][$key]['thumb'] = $this->remote->thumb($item['id']);
                    $this->db->select('systconsult, tconsult');
                    $this->db->where('id', $item['user_id']);
                    $u = $this->db->get('users')->result_array();
                    $result['data']['doctors'][$key]['tconsult'] =$u[0]['systconsult']+$u[0]['tconsult'] + rand(25,45);
                }
                $temp = $result['data']['doctors'];
                unset($result['data']['doctors']);
                $result['data']['doctors'][] = $temp[rand(3,103)];
                $result['data']['doctors'][] = $temp[rand(3,103)];
                $result['data']['doctors'][] = $temp[rand(3,103)];
            }
        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    public function getItemInfo(){

        $id = $result['data'] = '';

        if($name = $this->input->get('name')){
            $id = $this->getId($name);
        }
        if ($id OR ($id = intval($this->input->get('id')))) {
            $result['state'] = '000';
            $this->db->where('id', $id);
            $this->db->order_by("id", "desc");
            $tmp = $this->db->get('new_items')->result_array();
            $result['data'] = array ();
            $rr = array();
            if(!empty($tmp)){
                foreach($tmp as $r){
                    if($r['img_s'] ) {
                        $r['surl'] = $this->remote->show320($r['img_s']);
                        $r['burl'] = $this->remote->show320($r['img_m']);
                    }else{
                        $r['surl']= "http://pic.meilimei.com.cn/upload/fenlitu/jiazai.jpg";
                        $r['burl'] = "http://pic.meilimei.com.cn/upload/fenlitu/katong.jpg";
                    }

                    $r['price'] = intval($r['price']);

                    $rr = $r;
                }
            }
            $result['data'] = $rr;
        } else {
            $result['state'] = '014';
            $result['notice'] = '该项目不存在！';
        }

        echo json_encode($result);
    }

    private function getItemChild($pid = 0){
        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,surl,burl,colors,is_hot as num');
        $tmp = $this->db->get('new_items')->result_array();

        $data = array();
        $key = array('261');
        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->showmeilimei($item['burl']);
            $item['surl'] = $this->remote->showmeilimei($item['surl']);

            $data[] = $item ? $item : array();

        }
        return $data;
    }


    //use jigou name get its id
    private function getId($name=''){
        if($name){
            $this->db->select('id');
            $this->db->where('name', $name);
            $this->db->limit(1);
            $tmp = $this->db->get('new_items')->result_array();
            if(!empty($tmp)){
                return $tmp[0]['id'];
            }else{
                return 0;
            }
        }
    }
    //get tags of item
    private function getTags() {
        $res = array ();
        $res['DStreatments'] = '治疗方法';
        $res['XGtreatment'] = '治疗效果';
        $res['notice'] = '注意事项';
        $res['crowd'] = '适合人群';
        $res['treatment_time'] = '治疗次数';
        $res['recovery_process'] = '恢复过程';
        return $res;
    }
    // get user favorite tags
    function getMyTag($param = '') {

        if ($this->uid) {
            $result['state'] = '000';
            $this->db->where('uid', $this->uid);
            $this->db->order_by("id", "desc");
            $result['data'] = $this->db->get('myTags')->result_array();
        } else {
            $result['ustate'] = '001';
        }

        echo json_encode($result);
    }

    function getItemsWithUser($param = '') {

        if ($this->input->get('page')) {
            if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
                $result['state'] = '000';
                $pid = intval($this->input->get('pid'));
                if ($pid == 261) {
                    $this->db->where('is_hot', 1);
                }
                elseif ($pid == -1) {

                } else {
                    $this->db->where('pid', $pid);
                }
                $page = intval($this->input->get('page') - 1);
                $this->db->limit(8, 8 * $page);
                if ($this->input->get('type') == 1) {
                    $this->db->join('myTags', 'myTags.tagid = items.id');
                    $this->db->where('myTags.uid', $this->uid);
                }
                $this->db->select('items.id, items.pid, items.name,items.surl,items.burl,items.des');
                $this->db->order_by("order", "DESC");
                $tmp = $this->db->get('new_items')->result_array();
                $result['data'] = array ();
                foreach ($tmp as $r) {
                    $r['burl'] = $this->remote->show($r['burl']);
                    $r['surl'] = $this->remote->show($r['surl']);
                    ;
                    $r['users'] = $this->getUser($r['name']);
                    $result['data'][] = $r;
                }
                $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
                //print_r($this->alicache->get($_SERVER['REQUEST_URI']));
            }else{
                $result = array();
                $result = unserialize($rs);

            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    private function addFollow($followuser,$type) {
        $result['state'] = '000';
        $result['updateState'] = '001';
        $data['uid'] = $followuser;
        //$this->uid = 67868;
        if ($data['uid']) {
            $this->db->where('fid',$this->uid);
            $this->db->where('uid',$data['uid']);
            $this->db->where('type',$type);
            $this->db->from('wen_follow');
            if($this->db->count_all_results()){
                return 0;
            }else{
                $data['fid'] = $this->uid;
                $data['type'] = $type;
                $result['updateState'] = '000';
                $this->common->insertData('wen_follow', $data);

            }
            return 1;
        } else {

            return 0;
        }

    }

    public function getFollow($params = ''){
        $result['state'] = '000';
		$result['data'] = array();
        $type = 9;
        if($this->input->get('uid')) {
            $this->uid = $this->input->get('uid');
        }
        //$this->uid = 67868;
        if ($this->uid) {
            $this->db->where('fid',$this->uid);
            $this->db->where('type',$type);
            $this->db->select('uid');
            $res = $this->db->get('wen_follow')->result_array();
            $arr_uid =array();
            if(!empty($res)){

                foreach($res as $item){
                    $arr_uid[] = $item['uid'];
                }
            }
            $res_items = array();
            if(!empty($arr_uid)){
                $this->db->where_in('id',$arr_uid);
                $this->db->select('id,name,burl,colors,img_png as surl');
                $ires=$this->db->get('new_items')->result_array();

                if(!empty($ires)){
                    $this->db->where('uid',$this->uid);
                    $this->db->select('tag');
                    $q = $this->db->get('user_fav')->result_array();
                    $arr_user = array();

                    if(!empty($q)){
                        foreach($q as $qitem){
                            $arr_user[] = $qitem['tag'];
                        }
                    }

                    foreach($ires as $r){

                        if(in_array($r['name'],$arr_user)){

                            continue;
                        }
                        $r['burl'] = $this->remote->show($r['burl']);
                        $r['surl'] = $this->remote->show320($r['surl']);
                        $res_items[] = $r;
                    }

                }
            }
            $result['userItems'] = $q;
            $result['data'] = $res_items?$res_items:array();
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    public function getMyFollow($params = ''){
        $result['state'] = '000';
        $result['data'] = array();
        $type = 9;
        if($this->input->get('uid')) {
            $this->uid = $this->input->get('uid');
        }
        if ($this->uid) {
            $this->db->where('fid',$this->uid);
            $this->db->where('type',$type);
            $this->db->select('uid');
            $res = $this->db->get('wen_follow')->result_array();
            $arr_uid =array();
            if(!empty($res)){

                foreach($res as $item){
                    $arr_uid[] = $item['uid'];
                }
            }
            $res_items = array();
            if(!empty($arr_uid)){
                $this->db->where_in('id',$arr_uid);
                $this->db->select('id,name,img_png as surl,burl,colors');
                $ires=$this->db->get('new_items')->result_array();

                if(!empty($ires)){
                    $this->db->where('uid',$this->uid);
                    $this->db->select('cid');
                    $q = $this->db->get('user_fav')->result_array();
                    foreach($ires as $r){
                        if(in_array($r['id'],$p[0])){
                            continue;
                        }
                        $r['burl'] = $this->remote->show($r['burl']);
                        $r['surl'] = $this->remote->show320($r['surl']);
                        $res_items[] = $r;
                    }

                }
            }
            $result['data'] = $res_items?$res_items:array();
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    public function updateUserItem($param = '')
    {

        $tags = $this->input->post('tags');

        $result['state'] = '000';
        $result['data'] = array();
        //$this->uid=67868;

        if ($this->uid) {
            if ($tags) {
                $this->db->trans_start();
                $this->db->delete("user_fav", array('uid' => $this->uid));
                $arr_tags = explode(',', $tags);

                if (count($arr_tags) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach ($arr_tags as $item) {
                        $sql .= " name='" . $item . "' or";
                    }
                    $sqltmp = " and (" . substr($sql, 0, strlen($sql) - 2) . ")";

                    $query = $this->db->query("select id,name,img_png as surl,colors from new_items where 1=1" . $sqltmp);
                    $this->db->limit(3);
                    $res = $query->result_array();
                    $result['debug'] = $this->db->last_query();
                    if (!empty($res)) {

                        foreach ($res as $item) {
                            $user_fav = array('cid' => $item['id'], 'uid' => $this->uid, 'tag_img' => $this->remote->show320($item['surl']),'colors'=>$item['colors'], 'tag' => $item['name'], 'created_at' => time(), 'updated_at' => time());
                            $result['r'] = $this->addFollow($item['id'],9);
                            $this->db->insert('user_fav', $user_fav);
                        }
                    }
                }
                $this->db->trans_complete();
            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
    public function addUserItem($param = ''){

        $tags = $this->input->post('tags');
        $type = $this->input->post('type');
        $follow = $this->input->post('follow');

        $result['state'] = '000';
        //$this->uid=67868;

        if($this->uid){
            $this->db->trans_start();
            if($tags) {
                $this->db->delete("user_fav", array('uid' => $this->uid));
                $arr_tags = explode(',',$tags);

                if(count($arr_tags) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach ($arr_tags as $item) {
                        $sql .= " name='" . $item . "' or";
                    }
                    $sqltmp = " and (" . substr($sql, 0, strlen($sql) - 2) . ")";

                    $query = $this->db->query("select id,name,img_png as surl,colors from new_items where 1=1".$sqltmp);
                    $this->db->limit(3);
                    $res = $query->result_array();
                    if (!empty($res)) {

                        foreach ($res as $item) {
                            $user_fav = array('cid' => $item['id'], 'uid' => $this->uid, 'tag_img' => $this->remote->show320($item['surl']), 'tag' => $item['name'], 'colors'=>$item['colors'],'created_at' => time(), 'updated_at' => time());
                            $result['r'] = $this->addFollow($item['id'],$type);
                            $this->db->insert('user_fav', $user_fav);
                        }
                    }
                }

            }

            if($follow){
                $arr_follow = explode(',',$follow);

                if(count($arr_follow) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach($arr_follow as $item){
                        $sql .= " name='".$item."' or";
                    }
                    $sqltmp = " and (".substr($sql,0,strlen($sql)-2).")";

                    $query = $this->db->query("select id,name,surl from new_items where 1=1".$sqltmp);

                    $res = $query->result_array();

                    //$result['sql'] = $this->db->last_query();
                    if (!empty($res)) {
                        foreach ($res as $item) {
                            $result['r'] = $this->addFollow($item['id'],$type);
                        }
                    }
                }

            }
            $this->db->trans_complete();
        }else{
            $result['state'] = '012';
        }
        echo json_encode($result);
    }

    public function getHotTags(){

        $this->db->where('is_hot',1);
        $this->db->select('id,name,colors,img_png as surl');
        $this->db->order_by('order desc');
        $this->db->limit(9);
        $res = $this->db->get('new_items')->result_array();
        $result['data'] = array();
        $result['state'] = '000';
        if(!empty($res)){
            foreach($res as $item){

                $item['surl'] =$this->remote->show320($item['surl']);;
                $result['data']['hot_tags'][] = $item;
            }
        }

        $this->db->where('is_default',1);
        $this->db->select('id,name,colors, img_png as surl');
        $rs_default = $this->db->get('new_items')->result_array();
        if(!empty($rs_default)){
            foreach($rs_default as $item){
                $item['surl'] =$this->remote->show320($item['surl']);;
                $result['data']['default_tags'][] = $item;
            }
        }
        echo json_encode($result);
    }

    private function getUser($item) {
        $this->db->like('tags', $item);
        $this->db->select('uid');
        //$this->db->distinct('uid');
        $this->db->limit(8);
        $this->db->group_by(array('uid','weibo_id'));
        $this->db->order_by("weibo_id", "desc");
        $tmp = $this->db->get('wen_weibo')->result_array();
        $result = array ();
        foreach ($tmp as $r) {
            $r['picture'] = $this->profilepic($r['uid'], 1);
            $result[] = $r;
        }
        return $result;
    }

    //profile pic
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1 :
                return $this->remote->thumb($id, '44');
            case 0 :
                return $this->remote->thumb($id, '250');
            case 2 :
                return $this->remote->thumb($id, '120');
            default :
                return $this->remote->thumb($id, '120');
                break;
        }
    }

    public function getGroup(){

        $result['data'] = array();
        $result['state'] = '000';

        $this->db->select('id,name');
        $this->db->where('pid',14);
        $this->db->where('is_display',1);
        $groups = $this->db->get('mlm_items')->result_array();

        if(!empty($groups)){

            foreach($groups as $item){

                $this->db->where('gid',$item['id']);
                $tags = $this->db->get('mlm_tags')->result_array();
                if(!empty($tags)){

                    foreach($tags as $tag){
                        $result['data'][$item['id']][] = $tag;
                    }
                }else{
                    $result['data'][$item['id']] = array();
                }
                $result['data'][] = $item;

            }
        }

        echo json_encode($result);
    }
}
?>