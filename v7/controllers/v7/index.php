<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * app首页
 * @package        WENRAN
 * @subpackage    Controllers
 */

require_once(__DIR__."/MyController.php");
class index extends MY_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {

        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $this->eventDB = $this->load->database('event', TRUE);
        $this->eventDB1 = $this->load->database('event1', TRUE);
	    if ($this->wen_auth->is_logged_in()) {
		    $this->notlogin = false;
		    $this->uid = $this->wen_auth->get_user_id();
	    } else {
		    $this->notlogin = true;

	    }

 	    $this->load->library('alicache');
         $this->load->model('auth');
        $this->load->model('remote');

    }

    /**
     * 广告
     */
    public function index($param = ''){

            //调取上面的广告
            $topAds = $this->db->query("select * from apple where adPos like '$1$'  order by cdate")->result_array();
            foreach($topAds as $k => $v){
                $topAds[$k]['picture']=base_url().$v['picture'];
            }

            //调去下面的文章列表
            $bottomAds = $this->db->query("select * from apple where adPos like '$2$' order by cdate desc ")->result_array();
            foreach($bottomAds as $k => $v){
                $bottomAds[$k]['picture']=base_url().$v['picture'];
            }
            $data = array();
            $data['status']='000';
            $data['topAds'] =$topAds;
            $data['bottomAds'] = $bottomAds;

        echo json_encode($data);
    }
    /**
     * 广告
     */
    public function getBanner($param = ''){

      if($this->input->get('tags') ){
         $this->db->like('tags',$this->input->get('tags'));
           //$this->db->where('spcid', $this->input->get('id'));
         if($this->input->get('limit'))
             $this->db->limit($this->input->get('limit'));

         $this->db->order_by("order", "desc");
         $this->db->select('title, id,spcid,subtype,picture,picture_key,sharepic,area,tehuiid,flashid,event_id');
         $tmp = $this->db->get('apple')->result_array();
         $city = $this->input->get('city');

         $result['data'] =  array();

         foreach($tmp as $r){
             if(intval($r['event_id']) > 0){
                 $this->eventDB1->where('id', $r['event_id']);
                 $rs = $this->eventDB1->get('event_topic')->row_array();
                 $r['title'] = $rs['event_title'];
             }
             if(empty($r['picture_key'])) {
                 if (in_array($city, unserialize($r['area']))) {
                     $r['picture'] = $this->remote->show(str_replace('upload/', '', $r['picture']));
                     $r['sharepic'] = $r['sharepic'] == '' ? $r['picture'] : $this->remote->show(str_replace('upload/', '', $r['sharepic']));
                     $result['data'][] = $r;
                 } else {
                     $r['picture'] = $this->remote->show(str_replace('upload/', '', $r['picture']));
                     $r['sharepic'] = $r['sharepic'] == '' ? $r['picture'] : $this->remote->show(str_replace('upload/', '', $r['sharepic']));
                     $result['data'][] = $r;
                 }
             }else{
                 if (in_array($city, unserialize($r['area']))) {
                     $r['picture'] = $this->remote->getQiniuImage($r['picture_key'], 640);
                     $r['sharepic'] = $this->remote->getQiniuImage($r['picture_key'], 160);
                     $result['data'][] = $r;
                 } else {
                     $r['picture'] = $this->remote->getQiniuImage($r['picture_key'], 640);
                     $r['sharepic'] = $this->remote->getQiniuImage($r['picture_key'], 160);
                     $result['data'][] = $r;
                 }
             }
          }
          $result['state'] = '000';
        }else{
          $result['state'] = '012';
        }

        echo json_encode($result);
    }

    public function getHomeBody(){

        $page = $this->input->get('page')?$this->input->get('page'):1;
        //if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
            if ($page == 1) {
                $tags = "middleBanner";

                $this->db->like('tags', $tags);

                $this->db->order_by("id", "asc");
                $this->db->select('title, id,spcid,url,picture, picture_key,sharepic,area,tehuiid,subtype');
                $tmp = $this->db->get('apple')->result_array();

                $result['data'] = array();
                foreach ($tmp as $r) {
                    if(empty($r['picture_key'])) {
                        if ($r['picture']) {
                            $r['picture'] = $this->remote->show(str_replace('upload/', '', $r['picture']));
                            unset($r['title']);
                        } else {
                            unset($r['picture']);
                        }
                    }else{
                        $r['picture'] = $this->remote->getQiniuImage($r['picture_key'], 640);
                    }
                    $temp[] = $r;
                }

                $ad = array();
                if (!empty($temp)) {
                    foreach ($temp as $key => $item) {
                        if (!isset($item['picture'])) {
                            $arrtmp = array();
                            $arrtmp = explode('|', $item['title']);
                            $tmp = array();
                            if (!empty($arrtmp)) {
                                $tmp['title'] = isset($arrtmp[0]) ? $arrtmp[0] : '';
                                $tmp['color'] = isset($arrtmp[1]) ? $arrtmp[1] : '';
                                $tmp['Transparent'] = isset($arrtmp[2]) ? $arrtmp[2] : '';
                            }
                            $ad['tags'][] = $tmp;
                        } else {
                            $ad['picture'] = $item['picture'];
                        }
                    }
                }

                $result['data']['ad'] = $ad;
                $result['data']['piazza'] = $this->getHN(2, 9);
                $result['data']['activities'] = $this->getTags("activities", 3);
            }
            $result['data']['reality'] = $this->getHN1();

            $result['state'] = '000';
        /*    $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        }else{
            $result = array();
            $result = unserialize($rs);
        }*/
        echo json_encode($result);    
    }

    public function getDiary(){

        $this->load->model('Diary_model');
        $res = array();

        //if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
            $page = $this->input->get('page') ? $this->input->get('page') : 1;
            $offset = ($page - 1) * 10;
            $result['data'] = $this->Diary_model->getDiaryFrontList($offset);
            $result['debug2'] = $this->db->last_query();
            $j = 1;
            if (!empty($result['data'])) {
                $i = 1;
                foreach ($result['data'] as $item) {

                    if(empty($item['imgfile'])){
                        $item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
                    }else{
                        $item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
                    }

                    $rs = $this->Diary_model->get_user_by_username($item['uid']);
                    $item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
                    $item['username'] = $item['username'] ? $item['username'] : '';
                    if (preg_match("/^1[0-9]{10}$/", $item['username'])) {
                        $item['username'] = substr($item['username'], 0, 4) . '****';
                    }
                    $item ['basicinfo'] = $this->getBasicInfo($rs[0]);
                    $item['thumb'] = $this->profilepic($item['uid']);
                    $item['zanNum'] = ($this->Diary_model->getZan($item['nid']) > 0) ? $this->Diary_model->getZan($item['nid']) : 0;
                    $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
                    $j =1;
                    if ($j == 1) {
                        if ($i % 2 == 0 && $page < 2) {
                            $rs = $this->getTags($i . "-1");
                            if (!empty($rs)) {
                                $res[] = $rs[0];
                            }
                        }
                    }
                    $itemid = $this->Diary_model->getItemId($item['item_name']);
                    $item['itemid'] = $itemid;
                    $item['other'] = $this->Diary_model->isItemLevel($itemid, 1);


                    $item['diary_items'] = array();
                    $tmp = $this->Diary_model->getItemsPrice($item['nid']);

                    if(!empty($tmp)){
                        foreach($tmp as $i){
                            $itemid = $this->Diary_model->getItemId($i['item_name']);
                            $i['other'] = $this->Diary_model->isItemLevel($itemid,1);
                            $item['diary_items'][] = $i;

                        }
                    }
                    if(empty($item['diary_items'])){
                        $item['diary_items'][0]['item_name'] = $item['item_name'];
                        $item['diary_items'][0]['item_price'] = $item['item_price'];
                        $item['diary_items'][0]['pointX'] = $item['pointX'];
                        $item['diary_items'][0]['pointY'] = $item['pointY'];
                        $item['diary_items'][0]['other'] = $item['other'];
                    }

                    if ($this->uid) {
                        if ($this->Diary_model->getstate($item['uid'], $this->uid)) {
                            $item['follow'] = 1;
                        } else {
                            $item['follow'] = 0;
                        }
                        $is = $this->Diary_model->isZan($this->uid, $item['nid']);
                        $item['isZan'] = $is ? 1 : 0;
                    } else {
                        $item['isZan'] = 0;
                        $item['follow'] = 0;
                    }
                    $item['type'] = 2;

                    $res[] = $item;
                    $i++;
                }
            }
            $result['data'] = array();
            $result['data']['reality'] = $res;

            $result['state'] = '000';
            //$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        /*}else{
            $result = array();
            $result = unserialize($rs);
        }*/
        echo json_encode($result);
    }
    
    
    

    public function getHomeBodyIOS(){
        if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
            $page = $this->input->get('page') ? $this->input->get('page') : 1;
            $result['debug'] = $this->input->cookie("username");
            if ($page == 1) {
                $tags = "middleBanner";

                $this->db->like('tags', $tags);

                $this->db->order_by("id", "asc");
                $this->db->select('title, id,spcid, url,picture,sharepic,area,tehuiid');
                $tmp = $this->db->get('apple')->result_array();

                $result['data'] = array();
                foreach ($tmp as $r) {
                    if ($r['picture']) {
                        $r['picture'] = $this->remote->show(str_replace('upload/', '', $r['picture']));
                        unset($r['title']);
                    } else {
                        unset($r['picture']);
                    }
                    $temp[] = $r;
                }

                $ad = array();
                if (!empty($temp)) {
                    foreach ($temp as $key => $item) {
                        if (!isset($item['picture'])) {
                            $arrtmp = array();
                            $arrtmp = explode('|', $item['title']);
                            $tmp = array();
                            if (!empty($arrtmp)) {
                                $tmp['title'] = isset($arrtmp[0]) ? $arrtmp[0] : '';
                                $tmp['color'] = isset($arrtmp[1]) ? $arrtmp[1] : '';
                                $tmp['Transparent'] = isset($arrtmp[2]) ? $arrtmp[2] : '';
                            }
                            $ad['tags'][] = $tmp;
                        } else {
                            $ad['picture'] = $item['picture'];
                        }
                    }
                }

                $result['data']['ad'] = $ad;
                $result['data']['piazza'] = $this->getHN(2, 9);
                $result['data']['activities'] = $this->getTags("activities", 3);
            }
            $result['data']['reality'] = $this->getHNIOS();

            $result['state'] = '000';
            $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
        }else{
            $result = array();
            $result = unserialize($rs);
        }
        echo json_encode($result);
    }

    private function getTags($tags = "middleBanner",$pageSize = 10){

        $this->db->like('tags',$tags);

        $this->db->order_by("id", "asc");
        $this->db->limit($pageSize);
        $this->db->select('title, id,spcid,subtype,picture,picture_key,sharepic,area,tehuiid,flashid,event_id');
        $tmp = $this->db->get('apple')->result_array();

        $result['data'] =  array();
        $temp = array();
        foreach($tmp as $r){
            if(empty($r['picture_key'])) {
                $r['picture'] = $this->remote->show(str_replace('upload/', '', $r['picture']));
                $r['sharepic'] = $r['sharepic'] == '' ? $r['picture'] : $this->remote->show(str_replace('upload/', '', $r['sharepic']));
            }else{
                $r['picture'] = $this->remote->getQiniuImage($r['picture_key'], 640);
                $r['sharepic'] = $this->remote->getQiniuImage($r['picture_key'], 160);
            }
            if($tags != "activities"){

                $r['type'] = 1;
            }else{
                unset($r['tehuiid']);
                unset($r['spcid']);
            }
            
            $temp[] = $r;
            
        }
        return $temp;
    }
    //获取圈子页面广告
    public function getAdBanner($tag = 'adbanner', $limit = 3){
        $result['data'] =  array();
        $tag = $this->input->get('tag')?$this->input->get('tag'):'adbanner';
        $limit = $this->input->get('limit')?$this->input->get('limit'):3;
        $result['data'] = $this->getTags($tag, $limit);

        $result['state'] = '000';
        echo json_encode($result);
    }
    public function getFront(){

        $result['data'] =  array();

        $result['data'] = $this->getHN1(1,5,2);

        $result['state'] = '000';
        echo json_encode($result);

    }

    private function getHNIOS($type = 1,$pageSize=10,$j = 1) {

        $this->load->model('Diary_model');
        $res = array();

        if($j == 1) {
            $page = $this->input->get('page')?$this->input->get('page'):1;
            $offset = ($page - 1) * 10;
            $result['data'] = $this->Diary_model->getDiaryFrontList($offset);
        }else{
            $result['data'] = $this->Diary_model->getDiaryLoadingList();
        }

        if(!empty($result['data'])){
            $i = 1;
            foreach($result['data'] as $item){
                if(empty($item['imgfile'])){
                    $item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
                }else{
                    $item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
                }

                $rs = $this->Diary_model->get_user_by_username($item['uid']);
                $item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
                $item['username'] = $item['username']?$item['username']:'';
                if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                    $item['username'] = substr($item['username'],0,4).'****';
                }
                $item['thumb'] = $this->profilepic($item['uid']);
                $item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
                if(isset($rs[0]['age'])){
                    $item['age'] = $this->getAge($item['uid']);
                }else{
                    $item['age'] = '';
                }
                $item['zanNum'] = ($this->Diary_model->getZan($item['nid'])>0)?$this->Diary_model->getZan($item['nid']):0;
                $item['created_at'] = date('Y-m-d H:i',$item['created_at']);
                /*if($j == 1) {
                    if ($i % 2 == 0) {
                        $rs = $this->getTags($i . "-1");
                        if(!empty($rs)) {
                            $res[] = $rs[0];
                        }
                    }
                }*/
                $itemid = $this->Diary_model->getItemId($item['item_name']);
                $item['itemid'] = $itemid;
                $item['other'] = $this->Diary_model->isItemLevel($itemid,1);
                if($this->uid) {
                    if ($this->Diary_model->getstate($item['uid'],$this->uid)) {
                        $item['follow'] = 1;
                    } else {
                        $item['follow'] = 0;
                    }
                    $is = $this->Diary_model->isZan($this->uid, $item['nid']);
                    $item['isZan'] = $is?1:0;

                }else{
                    $item['isZan'] = 0;
                    $item['follow'] = 0;
                }
                $item['type'] = 2;

                $res[] = $item;
                $i++;
            }
        }

        return $res;
    }
	//get new and hot topics
	private function getHN1($type = 1,$pageSize=5,$j = 1) {

		$this->load->model('Diary_model');
		$res = array();

        if($j == 1) {
            $page = $this->input->get('page')?$this->input->get('page'):1;
            $offset = ($page - 1) * 5;
            $result['data'] = $this->Diary_model->getDiaryFrontList($offset);

        }else{
            $result['data'] = $this->Diary_model->getDiaryLoadingList();
        }
        //echo  'xxx';
        //echo $this->db->last_query();
        if(!empty($result['data'])){
            $n = 1;
			foreach($result['data'] as $item){

                if(empty($item['imgfile'])){
                    $item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
                }else{
                    $item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
                }

                $rs = $this->Diary_model->get_user_by_username($item['uid']);
				$item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$item['username'] = isset($item['username'])?$item['username']:'';
                $item ['basicinfo'] = $this->getBasicInfo($rs[0]);
                $item['sex'] = isset($rs[0]['sex'])?$rs[0]['sex']:0;
                $item['level'] = $this->getLevel($rs[0]['jifen']);
                if(preg_match("/^1[0-9]{10}$/",$item['username'])){
                    $item['username'] = substr($item['username'],0,4).'****';
                }
                $item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
                $item['pageview'] = intval($item['views']);
                if(isset($rs[0]['age'])){
                    $item['age'] = $this->getAge($item['uid']);
                }else{
                    $item['age'] = '';
                }
				$item['thumb'] = $this->profilepic($item['uid']);
				$item['zanNum'] = ($this->Diary_model->getZan($item['nid'])>0)?$this->Diary_model->getZan($item['nid']):0;
				$item['created_at'] = date('Y-m-d H:i',$item['created_at']);
                $item['diary_items'] = array();
                $tmp = $this->Diary_model->getItemsPrice($item['nid']);
                $category = $this->Diary_model->getFrontImg($item['ncid']);

                if(!empty($category)){

                    if(!empty($category[0]['imgfile'])){

                        $item['operation_imgurl'] = $this->remote->getQiniuImage($category[0]['imgfile']);
                    }else {

                        $item['operation_imgurl'] = $this->remote->getLocalImage($category[0]['imgurl']);
                    }

                }else{

                    if(!empty($item['imgfile'])){

                        $item['operation_imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
                    }else {

                        $item['operation_imgurl'] = $this->remote->getLocalImage($item['imgurl']);
                    }
                }

                if(!empty($tmp)){
                    foreach($tmp as $i){
                        $itemid = $this->Diary_model->getItemId($i['item_name']);
                        $i['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        $item['diary_items'][] = $i;

                    }
                }
                if(empty($item['diary_items'])){
                    $item['diary_items'][0]['item_name'] = $item['item_name'];
                    $item['diary_items'][0]['item_price'] = $item['item_price'];
                    $item['diary_items'][0]['pointX'] = $item['pointX'];
                    $item['diary_items'][0]['pointY'] = $item['pointY'];
                    $item['diary_items'][0]['other'] = $item['other'];
                }

                if($j == 1) {

                    if ($n % 2 == 0 && $page < 2) {
                        if($n == 2){
                            //echo $i;
                            $flashSale = array();
                            $flashSale = $this->getIndexFlashSale();
                            if(!empty($flashSale)){
                                foreach($flashSale as $iitem){

                                    $res[] = $iitem;//$this->getIndexFlashSale();
                                }
                            }
                        }

                        $rs = $this->getTags($n . "-1");

                        if(!empty($rs)) {
                            $res[] = $rs[0];
                        }
                    }
                }
                $itemid = $this->Diary_model->getItemId($item['item_name']);
                $item['itemid'] = $itemid;
                $item['other'] = $this->Diary_model->isItemLevel($itemid,1);
                if($this->uid) {
					if ($this->Diary_model->getstate($item['uid'],$this->uid)) {
						$item['follow'] = 1;
					} else {
						$item['follow'] = 0;
					}
                    $is = $this->Diary_model->isZan($this->uid, $item['nid']);
                    $item['isZan'] = $is?1:0;

				}else{
                    $item['isZan'] = 0;
					$item['follow'] = 0;
				}
                $item['type'] = 2;

				$res[] = $item;
                $n++;
			}
		}

		return $res;
	}

    //get new and hot topics
    private function getHN5($type = 1,$pageSize=10) {
        $this->db->from('wen_weibo');

        $this->db->where('wen_weibo.isdel', 0);
        //$this->db->where('wen_weibo.chosen', 0);
        //$this->db->where('wen_weibo.chosentime >=', time());

        $this->db->limit($pageSize);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        $this->db->join('topic_pics_extra', 'wen_weibo.weibo_id = topic_pics_extra.weibo_id');
        $this->db->select('topic_pics_extra.items,wen_weibo.imgfile ,topic_pics_extra.points_x,topic_pics_extra.points_y,topic_pics_extra.price,topic_pics_extra.doctor,topic_pics_extra.yiyuan,wen_weibo.weibo_id,wen_weibo.tags,users.alias as uname,wen_weibo.views as vote,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

        $this->db->order_by('wen_weibo.newtime desc');

        $tmp = $this->db->get()->result_array();

        $res = array ();

        foreach ($tmp as $r) {
            if($type == 1){
                $tags = $this->getTags();
                if(!empty($tags)){
                    foreach($tags as $item){
                        $res[] = $item;
                    }
                }
                print_r($res);
            }

            $dtypd = unserialize($r['type_data']);
            $url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);



            if(empty($r['imgfile'])) {
                $r['url'] = $this->remote->getLocalImage($url);
            }else{
                $r['url'] = $this->remote->getQiniuImage($r['imgfile']);
            }

            if (!isset ($dtypd['pic']['height'])) {
                $psize = getimagesize($r['url']);
                if($r['height'] = $psize[1]){
                    $r['width'] = $psize[0];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }

                //$this->UdatePic($r['weibo_id'], $psize);
            } else {
                if($r['height'] = $dtypd['pic']['height']){
                    $r['width'] = $dtypd['pic']['width'];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }
            }
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }

            if(time()-$r['ctime']<3600*10){
                if(time()-$r['ctime']<3600){
                    $r['ctime'] = intval((time()-$r['ctime'])/60).'分钟前';
                }else{
                    $r['ctime'] = intval((time()-$r['ctime'])/3600).'小时前';
                }
            }else{
                $r['ctime'] = date('Y年m月d日',$r['ctime']);
            }
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
            $r['type'] = 2;
            if($type ==1){
                $r['points'][] = array('points_x'=>$r['points_x'], 'points_y'=>$r['points_y'], 'items'=>$r['items'], 'doctor'=>$r['doctor'], 'yiyuan'=>$r['yiyuan'], 'price'=>intval($r['price']));
                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            if($type != 1 ){

                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["uname"]);
                unset($r["vote"]);
                unset($r["comments"]);
                unset($r["uid"]);
                unset($r["thumb"]);
                unset($r["ctime"]);
                unset($r["type"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            unset($r["haspic"]);
            unset ($r['type_data']);
            $res[] = $r;
        }
        if($type == 1){
            $tags = $this->getTags();
            if(!empty($tags)){
                foreach($tags as $item){
                    $res[] = $item;
                }
            }
        }

        return $res;
    }
    /**
     * 获取置顶帖子
     */
    private function getTopTopic($type=2){
        $this->db->from('wen_weibo');

        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.top', 1);
        $time = time();
        $this->db->where('wen_weibo.toptime >', $time);

        $this->db->limit(2);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        $this->db->join('topic_pics_extra', 'wen_weibo.weibo_id = topic_pics_extra.weibo_id');
        $this->db->select('topic_pics_extra.items,wen_weibo.imgfile,topic_pics_extra.points_x,topic_pics_extra.points_y,topic_pics_extra.price,topic_pics_extra.doctor,topic_pics_extra.yiyuan,wen_weibo.weibo_id,wen_weibo.tags,users.alias as uname,wen_weibo.views as vote,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

        $this->db->order_by('wen_weibo.newtime desc');

        $tmp = $this->db->get()->result_array();

        $res = array ();

        foreach ($tmp as $r) {


            $dtypd = unserialize($r['type_data']);
            $url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);

            if(empty($r['imgfile'])) {
                $r['url'] = $this->remote->getLocalImage($url);
            }else{
                $r['url'] = $this->remote->getQiniuImage($r['imgfile']);
            }
            if (!isset ($dtypd['pic']['height'])) {
                $psize = getimagesize($r['url']);
                if($r['height'] = $psize[1]){
                    $r['width'] = $psize[0];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }

                $this->UdatePic($r['weibo_id'], $psize);
            } else {
                if($r['height'] = $dtypd['pic']['height']){
                    $r['width'] = $dtypd['pic']['width'];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }
            }
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }

            if(time()-$r['ctime']<3600*10){
                if(time()-$r['ctime']<3600){
                    $r['ctime'] = intval((time()-$r['ctime'])/60).'分钟前';
                }else{
                    $r['ctime'] = intval((time()-$r['ctime'])/3600).'小时前';
                }
            }else{
                $r['ctime'] = date('Y年m月d日',$r['ctime']);
            }
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
            $r['type'] = 2;
            if($type ==1){
                $r['points'][] = array('points_x'=>$r['points_x'], 'points_y'=>$r['points_y'], 'items'=>$r['items'], 'doctor'=>$r['doctor'], 'yiyuan'=>$r['yiyuan'], 'price'=>intval($r['price']));
                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            if($type != 1 ){

                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["uname"]);
                unset($r["vote"]);
                unset($r["comments"]);
                unset($r["uid"]);
                unset($r["thumb"]);
                unset($r["ctime"]);
                unset($r["type"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            unset($r["haspic"]);
            unset ($r['type_data']);
            $res[] = $r;
        }
        if($type == 1){
            $tags = $this->getTags();
            if(!empty($tags)){
                foreach($tags as $item){
                    $res[] = $item;
                }
            }
        }
        $res['debug'] = $this->db->last_query();
        return $res;
    }

	private function getHN($type = 1,$pageSize=10) {
        $this->load->model('Diary_model');
		$this->db->from('wen_weibo');

		$this->db->where('wen_weibo.isdel', 0);
		$this->db->where('wen_weibo.top', 1);
		$this->db->where('wen_weibo.top_start <', time());
        $this->db->where('wen_weibo.top_end >', time());

		$this->db->limit($pageSize);
		$this->db->join('users', 'users.id = wen_weibo.uid');
		$this->db->select('wen_weibo.weibo_id,wen_weibo.imgfile,wen_weibo.tags,wen_weibo.front_pic,users.alias as uname,wen_weibo.front_title,wen_weibo.views as vote,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');
        if($pageSize == 9) {
            $this->db->order_by('wen_weibo.top_start desc');
        }else{
            $this->db->order_by('wen_weibo.newtime desc');
        }
		$tmp = $this->db->get()->result_array();

		$res = array ();

		foreach ($tmp as $r) {
			if($type == 1){
				$tags = $this->getTags();
				if(!empty($tags)){
					foreach($tags as $item){
						$res[] = $item;
					}
				}
			}
            $r['zanNum'] = 0;

            $r['isZan'] = 0;

            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item){
                        if($item == '')
                            continue;
                        $arr = array();
                        $arr['tag'] = $item;
                        $itemid = $this->Diary_model->getItemId($item);
                        $arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        $r['tagss'][] = $arr;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }

            unset($r['tag']);
			$dtypd = unserialize($r['type_data']);

            $url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);


			if($r['front_pic']){
                $r['url'] = $this->remote->show320($r['front_pic']);
            }else {
                if(empty($r['imgfile'])) {
                    $r['url'] = $this->remote->getLocalImage($url);
                }else{
                    $r['url'] = $this->remote->getQiniuImage($r['imgfile']);
                }
            }
            if (!isset ($dtypd['pic']['height'])) {
				$psize = getimagesize($r['url']);
				if($r['height'] = $psize[1]){
					$r['width'] = $psize[0];
				}else{
					$r['height'] = 260;
					$r['width'] = 200;
				}

				//$this->UdatePic($r['weibo_id'], $psize);
			} else {
				if($r['height'] = $dtypd['pic']['height']){
					$r['width'] = $dtypd['pic']['width'];
				}else{
					$r['height'] = 260;
					$r['width'] = 200;
				}
			}
			$r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
			if (preg_match('/^\\d+$/', $r['uname'])) {
				$r['uname'] = substr($r['uname'], 0, 4) . '***';
			}

			if(time()-$r['ctime']<3600*10){
				if(time()-$r['ctime']<3600){
					$r['ctime'] = intval((time()-$r['ctime'])/60).'分钟前';
				}else{
					$r['ctime'] = intval((time()-$r['ctime'])/3600).'小时前';
				}
			}else{
				$r['ctime'] = date('Y年m月d日',$r['ctime']);
			}
			$dtypd = unserialize($r['type_data']);

			isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
            if(!empty($r['front_title'])){
                $r['content'] = $r['front_title'] ;
            }
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
			$r['type'] = 2;

			if($type ==1){
				$r['points'][] = array('points_x'=>$r['points_x'], 'points_y'=>$r['points_y'], 'items'=>$r['items'], 'doctor'=>$r['doctor'], 'yiyuan'=>$r['yiyuan'], 'price'=>intval($r['price']));
				unset($r["items"]);
				unset($r["price"]);
				unset($r["doctor"]);
				unset($r["yiyuan"]);
				unset($r["points_y"]);
				unset($r["points_x"]);
			}
			if($type != 1 ){

				unset($r["items"]);
				unset($r["price"]);
				unset($r["doctor"]);
				unset($r["yiyuan"]);
				unset($r["uname"]);
				unset($r["vote"]);
				unset($r["comments"]);
				unset($r["uid"]);
				unset($r["thumb"]);
				unset($r["ctime"]);
				unset($r["type"]);
				unset($r["points_y"]);
				unset($r["points_x"]);
			}
			unset($r["haspic"]);
			unset ($r['type_data']);
			$res[] = $r;
		}

		if($type == 1){
			$tags = $this->getTags();
			if(!empty($tags)){
				foreach($tags as $item){
					$res[] = $item;
				}
			}
		}

		return $res;
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
    // bannner detail
    public function info($param = ''){


      if($id = intval($this->input->get('id'))){
         $this->db->where('id', $id);
         $this->db->order_by("id", "desc");
         $this->db->select('id,title, content,cdate,url');
         $tmp = $this->db->get('apple')->result_array();
         $result['data'] =  array();
         $result['data']['url'] = $tmp[0]['url']?$tmp[0]['url']:site_url().'banner/mobile/'.$id;
         $result['data']['title'] = $tmp[0]['title'];
         $result['data']['content'] = '<meta name="viewport" content="initial-scale=1, width=device-width,  user-scalable=no"  /><style>img{max-width:100%;} #content{font-size:16px; line-height:180%; padding:10px;color:#333;margin:0 auto}   </style><div id="content">'.preg_replace('/(?<=img src=")(\W+)attached?\//i', base_url()."attached/", $tmp[0]['content']).'</div>';
         $result['data']['cdate'] = date('Y-m-d',$tmp[0]['cdate']);

         if($tmp[0]['id'] == 163 || $tmp[0]['id'] == 246)
             $result['data']['pic'] = 1;
         else
            $result['data']['pic'] = 0;
         $result['state'] = '000';
       }else{
         $result['state'] = '012';
       }
       echo json_encode($result);
    }
    
    /*
     * 获取首页闪购
     * */
    public function getIndexFlashSale(){
        $web = $this->input->get('web');
        $city = $this->input->get('city');
        
        $thistime= time();
        $fields = "id,city,type,begin,end,lbanner,title,discount,vbuy,lbanner_key,banner, banner_key";
        $condition = " and fs.begin <= {$thistime} and fs.end >= {$thistime}";
        
        $limit = "limit 0,2";

        $fs_sql = "select $fields from flash_sale as fs where 1=1 and id in(86,90) and display = 1 {$condition} {$limit}";
        
        $fs_rs = $this->db->query($fs_sql)->result_array();
        
        $result['fs_rs'] = array();
        $result['data'] = array();
        
        if(!empty($fs_rs)){
            foreach($fs_rs as &$v){
                $v['begin'] = intval($v['begin'] - time());
                $v['end'] =  intval($v['end'] - time());
                $v['flag'] = 4;
                $v['begin'] = abs($v['begin']);
                $v['end'] =  abs($v['end']);
                $v['type'] = 3;

                $bday = floor($v['begin']/(3600*24));
                $bsecond = $v['begin']%(3600*24);//除去整天之后剩余的时间
                $bhour = floor($bsecond/3600);
                $bsecond = $bsecond%3600;//除去整小时之后剩余的时间
                $bminute = floor($bsecond/60);
                $bsecond = $bsecond%60;//除去整分钟之后剩余的时间
                if($bday >0){
                    //返回字符串
                    $v['begin']  = $bday.'天';
                }else{
                    $v['begin']  = $bhour.'小时';
                }
                 
                 
                $day = floor($v['end']/(3600*24));
                $second = $v['end']%(3600*24);//除去整天之后剩余的时间
                $hour = floor($second/3600);
                $second = $second%3600;//除去整小时之后剩余的时间
                $minute = floor($second/60);
                $second = $second%60;//除去整分钟之后剩余的时间
        
                //$v['begin'] = date("d天h时i分",$v['begin']);
                $v['end'] = $day.'天'.$hour.'小时';
                
                $v['day'] = $day;
                $v['hour'] = $hour;
                $v['minute'] = $minute;
                $v['second'] = $second;
                
//                 if($day >0){
//                     //返回字符串
//                     $v['end']  = $day.'天';
//                 }else{
//                     $v['end']  = $hour.'小时';
//                 }
                if(empty($v['lbanner_key'])) {
                    $v['lbanner'] = $this->remote->getLocalImage($v['lbanner'], 640);
                }else{
                    $v['lbanner'] = $this->remote->getQiniuImage($v['lbanner_key'], 640);
                }
                if(empty($v['banner_key'])) {
                    $v['banner'] = $this->remote->getLocalImage($v['banner'], 640);
                }else{
                    $v['banner'] = $this->remote->getQiniuImage($v['banner_key'], 640);
                }
                //print_r($v['flag']);die;
            }

            //$this->result['sql'] = $this->db->last_query();
        }
        
        return $fs_rs;
    }
    
    /*
     * 获取首页特惠
     * */
    public  function getIndexTehui(){
        $web = $this->input->get('web');
        $city = $this->input->get('city');
        
        $time = time();
        $fields = 't.city_ids,t.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';
        $condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}' and flashSale = 0 ";


//         if($city != '全部地区'){
//             $city_sql = "select id from category where czone = ? and zone = ?";
//             $city_id_rs = $this->tehuiDB->query($city_sql,array($czone,'city'))->result_array();
//             foreach ($city_id_rs as $vids){
//                 $city_id_tmp[] = $vids['id'];
//             }
//             $city_id_string = implode($city_id_tmp,',');
//             $condition .= " and (city_id in ({$city_id_string}) or city_id = 0)";
//         }

        $order = ' t.id DESC ';
        $limit = "0,4";
        $result['data'] = array ();
        $tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} limit {$limit} ")->result_array();
        
        $randpic = date('Ymdhi',time());
        if($tmpinfo){
            foreach ($tmpinfo as $r) {
                $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
                $result['data'][] = $r;
                
            }
            $result['state'] = '000';
            $result['notice'] = '成功获取！';
        }else{
            $result['notice'] = '参数不全！';
            $result['state'] = '012';
        }
        
        
        echo json_encode($result);
    }
    
    /*
     * 获取首页热门项目
     * */
    public function getIndexHotItems(){
        $city = $this->input->get('city');
        $czone = $this->input->get('czone');
        
        $result['data'] = array ();
        $item_sql = "select pic,item_name from index_hot_item where display = 1 order by level desc";
        $item_rs  = $this->eventDB->query($item_sql)->result_array();

        if($item_rs){
            foreach($item_rs as &$v){
                $v['pic'] = $this->remote->showup($v['pic']);
            }
            $result['data'] = $item_rs;
            $result['notice'] = '成功获取！';
        }else{
            $result['notice'] = '参数不全！';
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    
   /**
     * 广告detail
     */
    public function detail($param = ''){

        $id = $this->input->post('id');
        if($id){
            $detail = $this->db->query("select * from apple where id = $id ")->row_array();
            $data['status'] = '000';
            $data['detail'] = $detail;
        }else{
            $data['status'] = '001';
        }
        echo "<style>
        *{
        margin:0px;
        padding:0px;
        border:0px;
        }
        #content{
        font-size:16px;
        line-height:180%;
        padding:10px;color:#333;
        }
        #content img{
        max-width:300px;
        }

        </style>
        <div id='content'>".preg_replace('/(?<=img src=")(\W+)attached?\//i', base_url()."attached/", $detail['content'])."
        </div>";
    }
    
    
    


}

?>
