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
    public $i = '';
    public function __construct() {
        parent :: __construct();

        $sign = (isset($_REQUEST['signapp']) && !empty($_REQUEST['signapp'])) ? $_REQUEST['signapp']:0;
        $sessionid = (isset($_REQUEST['sessionid']) && !empty($_REQUEST['sessionid'])) ? $_REQUEST['sessionid']:0;

        $hashcode = strtoupper(md5($this->appkey));
        $this->i = $sessionid;
        $nologinMethod = array('getTehuiDetailById', 'getflashSaleList','itemsAll', 'getflashSaleByid','getHomeBodyIOS', 'getCommentFloor', 'OpenIM', 'getCommentFloorImage', 'sendsmspy','softInfoXmlForMeiliMei','review', 'isReview','softInfoXml','picmr2', 'leave','gettalks','getFront','getDiaryInfo','getTehuiList','getDoctorDetail', 'notify','getDiaryListIos','getDoctor', 'softInfo','getCommentListPageSize','getIndexBanner', 'getDiaryDetail','topicSearch', 'getMyFollow','getSearch', 'getMyNoteCategory','getMyNoteCategoryUserInfo','topicListWithOrder','getMyNoteCategoryUserInfo','getMyItemDiaryList', 'getFromUserZan','getIndexTopBanner', 'getCommentList','getBanner', 'getcate', 'getSales','getHomeBody' ,'getNoteCategoryDetail','getDiaryList','getDiaryNodeList', 'jsearch', 'getItemChildList','getDoctorHospital', 'jsearch','getDiaryMaxList', 'questions','getMyTopicList','getPageCount','getHotTags','allItems','getQuestionItems','getDiaryMiniList','sendsms','search','getItemInfo','faces','topicList','view','getAns','info', 'getMyItemDiaryList', 'getMyNoteCategoryUserInfo','Gcomments','facesTheDayTop','uinfo','infos', 'sendsmspy','getfansv2','getUserDiaryList','getnewcate','getNewTehuiList','getIndexFlashSale','wxnotify','getwebcate','getTehuiByJigouId','gcomments','getUserCouponList','getTehuiSearchItem');
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

    protected function getAge($age = 0){

        $arr_age = array("","18-29岁", "20-25岁", "26-30岁", "31-35岁", "36-40岁", "其他");
        return $arr_age[$age];
    }

    protected function getBasicInfo($rs){

        $item = array();
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
}
?>