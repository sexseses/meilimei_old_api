<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */


class MY_Controller extends CI_Controller {

    protected $appkey = "AXU1CDPM90KIGTNS4LHERBF2VZ5J3OQY8W76";
    protected $fileUrl = 'http://7xkdi8.com1.z0.glb.clouddn.com/';
    public $i = '';
    public function __construct() {
        parent :: __construct();

        $sign = (isset($_REQUEST['signapp']) && !empty($_REQUEST['signapp'])) ? $_REQUEST['signapp']:0;
        $sessionid = (isset($_REQUEST['sessionid']) && !empty($_REQUEST['sessionid'])) ? $_REQUEST['sessionid']:0;

        $hashcode = strtoupper(md5($this->appkey));
        $this->i = $sessionid;
        $nologinMethod = array('startBigAd','getItemTehuiList','getTehuiDetailById', 'getflashSaleList','itemsAll', 'getflashSaleByid','getHomeBodyIOS', 'getCommentFloor', 'OpenIM', 'getCommentFloorImage', 'sendsmspy','softInfoXmlForMeiliMei','review', 'isReview','softInfoXml','picmr2', 'leave','gettalks','getFront','getDiaryInfo','getTehuiList','getDoctorDetail', 'notify','getDiaryListIos','getDoctor', 'softInfo','getCommentListPageSize','getIndexBanner', 'getDiaryDetail','topicSearch', 'getMyFollow','getSearch', 'getMyNoteCategory','getMyNoteCategoryUserInfo','topicListWithOrder','getMyNoteCategoryUserInfo','getMyItemDiaryList', 'getFromUserZan','getIndexTopBanner', 'getCommentList','getBanner', 'getcate', 'getSales','getHomeBody' ,'getNoteCategoryDetail','getDiaryList','getDiaryNodeList', 'jsearch', 'getItemChildList','getDoctorHospital', 'jsearch','getDiaryMaxList', 'questions','getMyTopicList','getPageCount','getHotTags','allItems','getQuestionItems','getDiaryMiniList','sendsms','search','getItemInfo','faces','topicList','view','getAns','info', 'getMyItemDiaryList', 'getMyNoteCategoryUserInfo','Gcomments','facesTheDayTop','uinfo','infos', 'sendsmspy','getfansv2','getUserDiaryList','getnewcate','getNewTehuiList','getIndexFlashSale','wxnotify','getwebcate','getTehuiByJigouId','gcomments','getUserCouponList','getTehuiSearchItem');
        $loginMethod = array('signin','reg','resetPassword','getDoctorRankinglist','reg41','DarenDetail','DarenRanking','DarenDetail', 'OpenIM','getTopBanner','getnewcate','getNewTehuiList','getfavorites','getBookInfo','order','getCouponBySn','getUserCouponList','getListbyCondition');

        if($this->router->fetch_method() == 'notify'){

        }else {
            //echo (in_array($this->router->fetch_method(),$nologinMethod) && $hashcode == $sign);exit();
            $test=1;
            if (!$test) {

                if (in_array($this->router->fetch_method(), $loginMethod) && $hashcode == $sign) {

                } else {

                    if (in_array($this->router->fetch_method(), $nologinMethod) && $hashcode == $sign) {



                    } else {

                        if ($sign != strtoupper(md5($this->appkey))) {
                            $result['notice'] = 'signapp 不对!';
                            $result['state'] = '00２';
                            echo json_encode($result);
                            exit();
                        }

                        if (!$this->checkUser($sessionid)) {

                            $result['notice'] = 'Token失效!';
                            $result['state'] = '001';
                            echo json_encode($result);
                            exit();
                        }
                    }
                }
            }
        }
    }
    //get points data
    private function Gpoint($picid){
        $this->db->where('pic_id',$picid);
        return $this->db->get('topic_pics_extra')->result_array();
    }
    protected function checkUser($sessionid){

        if(empty($sessionid)){
            return false;
        }
        $expire = time();
        $res = $this->db->query("select *From users where sessionid=? and expire > ? ",array($sessionid,$expire))->result_array();

        if(count($res) > 0){
            return 1;
        }

        return 0;
    }

    protected function getAge($uid = 0){
        $arr_age = array();
	    $uid = intval($uid);
        if($uid <=0)
            return '';

        $user = $this->db->query("select *From users where id='{$uid}'")->result_array();

        if(!empty($user) && strlen(intval($user[0]['age'])) >= 4){
            return strval((intval(date('Y')) - intval($user[0]['age']) +1));
        }else{
            return '';
        }
    }

    protected function getBasicInfo($rs){

        $item = array();
        $item['basicinfo'] = '';
	    $rs['age'] = intval($rs['age']);
        if(isset($rs['age'])){
            $arr_city = array('','18-29岁', '20-25岁', '26-30岁', '31-35岁', '36-40岁', '其他');
            $item['basicinfo'] .= ' '. $arr_city[$rs['age']];
        }
        if(isset($rs['city']) && !is_null($rs['city'])){
            $item['basicinfo'] .= ' '.$rs['city'];
        }

        if(isset($rs['sex'])){
            $arr_sex = array('保密', '女', '男');
            $item['basicinfo'] .= ' '.$arr_sex[$rs['sex']];
        }
        return $item['basicinfo'];
    }

    protected function getLevel($jifen = 0){

        if(intval($jifen) < 1500){
            return 1;
        }

        if(intval($jifen) >= 1500 && intval($jifen) < 6000){
            return 2;
        }

        if(intval($jifen) >= 6000 && intval($jifen) < 12000){
            return 3;
        }

        if(intval($jifen) >= 12000 && intval($jifen) < 25000){
            return 4;
        }

        if(intval($jifen) >= 25000 && intval($jifen) < 50000){
            return 5;
        }

        if(intval($jifen) >= 50000 ){
            return 6;
        }

    }

    //get set pic lists
    protected function plist($id, $width_tmp = 360 ) {
        $this->eventDB = $this->load->database('event', TRUE);
        $this->eventDB1 = $this->load->database('event1', TRUE);
        $this->db->select('id,savepath,height,width,info,imgfile');
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
                if(empty($r['imgfile'])) {
                    if (intval($arr_url[1]) >= 3 && intval(date('Y')) <= $arr_url[0]) {

                        if (isset($arr_url[1])) {
                            $url = $r['savepath'];
                        }

                        //echo $this->remote->show320($url, $width);
                        $r['savepath'] = $this->remote->getLocalImage($url);
                        $r['vedio'] = '';
                    } else {
                        if (isset($arr_url[1])) {

                            $url = $r['savepath'];
                        }

                        $r['savepath'] = $this->remote->getLocalImage($url);
                        $r['vedio'] = '';
                    }
                }else{
                    $r['savepath'] = $this->remote->getQiniuImagewater($r['imgfile'],$width_tmp);
                    $r['savepathsize'] =  getimagesize($r['savepath']);
                    $r['height'] =  $r['savepathsize'][1];
                    $r['width'] = $r['savepathsize'][0];
                    $r['savepath_big'] = $this->remote->getQiniuImagewater($r['imgfile'],640);
                }
            }
            $rt[] = $r;
        }
        return $rt;
    }
    protected function getNextCID($cid =0, $type='diary'){
        $result['data'] = array();

        if(intval($cid) > 0){
            if($type !='diary'){
                $this->db->where('id', $cid);
                $comment = $this->db->get('wen_comment')->row_array();
                
                if(!empty($comment)){
                    if(intval($comment['pid']) == 0){
                        $this->db->where('contentid', $comment['contentid']);
                        $this->db->where('id >', $comment['id']);
                        $this->db->limit(1);
                        $t = $this->db->get('wen_comment')->row_array();
                        $result['data']['lastid'] = intval($t['id']);
                    }else{
                        
                        $this->db->where('contentid', $comment['contentid']);
                        $this->db->where('id >', $comment['pid']);
                        $this->db->limit(1);
                        $t = $this->db->get('wen_comment')->row_array();
                        $result['data']['lastid'] = intval($t['id']);       
                    }
                }else{
                    $result['data']['lastid'] = "";
                }
            }else{
                $this->db->where('cid', $cid);
                $comment = $this->db->get('note_comment')->row_array();
                if(!empty($comment)){
                    if(intval($comment['pcid']) == 0){
                        $this->db->where('nid', $comment['nid']);
                        $this->db->where('cid >', $comment['cid']);
                        $this->db->limit(1);
                        $t = $this->db->get('note_comment')->row_array();
                        $result['data']['lastid'] = intval($t['cid']);
                    }else{
                        $this->db->where('nid', $comment['nid']);
                        $this->db->where('cid >', $comment['pcid']);
                        $this->db->limit(1);
                        $t = $this->db->get('note_comment')->row_array();
                        $result['data']['lastid'] = intval($t['cid']);  
                    }
                }else{
                    $result['data']['lastid'] = "";   
                }   
            }
        }else{
            $result['data']['lastid'] = "";
        }

        return $result['data']['lastid'];
    }

    protected function getCommentDateTime($time = 0){

        if($time > (time()  - 60)){
            $result = '刚刚';
        }else if(($time > time() - 3600)){
            $result = '1分钟前';
        }else if($time > time() - 86400){
            $result = '1小时前';
        }else if ($time > time() - (86400 *2)) {
            $result = '昨天';
        } else if($time > time() - (86400*15)){
            $result = '2天前';
        }else{
            $result = date('m-d', $time);
        }
        return $result;
    }

    protected function getContentDateTime($time){

        if($time > (time()  - 60)){
            $result = '刚刚';
        }else if(($time > time() - 3600) ){
            $result = '1分钟前';
        }else if($time > time() - 86400){
            $result = '1小时前';
        }else{
            $result = date('m-d', $time);
        }
        return $result;
    }
}
?>
