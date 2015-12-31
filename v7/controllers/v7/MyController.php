<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */

if ($_REQUEST['test']) {
    ini_set('dispany_errors', 'On');
    error_reporting(-1);
}

class MY_Controller extends CI_Controller {

    protected $appkey = "AXU1CDPM90KIGTNS4LHERBF2VZ5J3OQY8W76";
    protected $fileUrl = 'http://7xkdi8.com1.z0.glb.clouddn.com/';
    public $token = '';
    public $uid = 0;
    public $notlogin = true;

    public function __construct() {
        parent :: __construct();
        $this->load->library('memc');
        $token = (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) ? $_REQUEST['token']:0;

        $this->token = $token;
        /*echo $this->token;
        $user_id = $this->memc->G($this->token);
        echo $user_id;
        exit();*/
        if($user_id = $this->memc->G($this->token)){
            $this->notlogin = false;
            $this->uid = $user_id;
        }
    }
    //get points data
    protected function Gpoint($picid){
        $this->db->where('pic_id',$picid);
        return $this->db->get('topic_pics_extra')->result_array();
    }

    protected function getAge($uid = 0){
        $arr_age = array();
	    $uid = intval($uid);
        if($uid <=0)
            return '';

        $user = $this->db->query("select *From users where id='{$uid}'")->result_array();

        if(!empty($user) && strlen(intval($user[0]['age'])) >= 4){
            return intval((intval(date('Y')) - intval($user[0]['age']) +1));
        }else{
            return 0;
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

        return 1;

    }

    //get set pic lists
    protected function Plist($id) {
        $this->db->select('savepath,height, width, imgfile');
        $this->db->where('attachId', $id);
        $this->db->from('topic_pics');
        $this->db->order_by('order','ASC');
        $res = $this->db->get()->result_array();
        $rt = array ();

        $i = 0;
        foreach ($res as $r) {

            $arr_url = explode('/',$r['savepath']);
            $url = '';
            if($i > 2)
                break;
            if (empty($r['imgfile'])) {
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
            } else {
                $r['savepath'] = $this->remote->getQiniuImage($r['imgfile']);
            }

            $rt[] = $r;
        }
        $result['total'] = count($res);
        $result['images'] = $rt;
        return $result;
    }

    protected function getDoctorComments(){

        return array('uid'=>1020,'name'=>'小二','thumb'=>'http://pic.meilimei.com.cn/thumb/409333_120_0?1447060670', 'verify'=>1, 'content'=>'回复的很好很不错哦', 'like'=>10);
    }
}
?>
