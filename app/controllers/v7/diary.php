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
		$this->tehuiDB = $this->load->database('tehui', TRUE);

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
					if(!empty($item['imgfile'])){
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
					}else {
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
					}

					$item['created_at'] = date('Y-m-d H:i', $item['created_at']);
					$item['thumb'] = $this->remote->thumb($item['uid']);
					$item['city'] = isset($item['city'])?$item['city']:'';
					$item['pageview'] = intval($item['pageview']);
					if(isset($item['age'])){
						$item['age'] = $this->getAge($item['uid']);
					}else{
						$item['age'] = '';
					}
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
					$temp = $this->Diary_model->get_user_by_username($item['uid']);
					$item ['basicinfo'] = $this->getBasicInfo($temp[0]);
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
            if($this->input->get('page') > 1){
                $tmpDayDiaryList = $this->Diary_model->getDayDiaryList($uid ,$ncid, 0,0);
            }else{
			    $tmpDayDiaryList = $this->Diary_model->getDayDiaryList($uid ,$ncid, 0,150);
            }
			$arr_item = array();
			if(!empty($tmpDayDiaryList)){

				foreach($tmpDayDiaryList as $k=>$item) {

					$tmp = $this->Diary_model->getDiaryMiniList($uid,$ncid, $item['cday']);

					foreach ($tmp as $k => $citem) {

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
						if(empty($citem['imgfile'])) {
							if ($citem['imgurl']) {
								$citem['sImgUrl'] = $this->remote->getLocalImage($citem['imgurl'], 360);
								$citem['imgurl'] = $this->remote->getLocalImage($citem['imgurl'], 640);
							} else {
								$citem['sImgUrl'] = '';
								$citem['imgurl'] = '';
							}
						}else{

							$citem['sImgUrl'] = $this->remote->getQiniuImage($citem['imgfile'], 360);
							$citem['imgurl'] = $this->remote->getQiniuImage($citem['imgfile'], 640);
						}
						$citem['created_at'] = date('Y-m-d H:i',$citem['created_at']);
						$itemid = $this->Diary_model->getItemId($item['item_name']);
						$citem['other'] = $this->Diary_model->isItemLevel($itemid,1);
						$citem['thumb'] = $this->profilepic($uid);
						$citem['diary_items'] = array();
						$tmp = $this->Diary_model->getItemsPrice($citem['nid']);

						if(!empty($tmp)){
							foreach($tmp as $i){
								$itemid = $this->Diary_model->getItemId($i['item_name']);
								$i['other'] = $this->Diary_model->isItemLevel($itemid,1);
								$citem['diary_items'][] = $i;

							}
						}
						if(empty($citem['diary_items'])){
							$citem['diary_items'][0]['item_name'] = $citem['item_name'];
							$citem['diary_items'][0]['item_price'] = $citem['item_price'];
							$citem['diary_items'][0]['pointX'] = $citem['pointX'];
							$citem['diary_items'][0]['pointY'] = $citem['pointY'];
							$citem['diary_items'][0]['other'] = $citem['other'];
						}
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
					if(empty($citem['imgfile'])) {
						$citem['sImgUrl'] = $this->remote->getLocalImage($citem['imgurl'], 360);
						$citem['imgurl'] = $this->remote->getLocalImage($citem['imgurl'], 640);
					}else{
						$citem['sImgUrl'] = $this->remote->getQiniuImage($citem['imgfile'], 360);
						$citem['imgurl'] = $this->remote->getQiniuImage($citem['imgfile'], 640);
					}
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
						$time = time();
						$citem['day'] = '第'.$citem['cday'].'天';
						if($item['oneday']) {
							$arr_item[$item['oneday']]['day'] = $citem['day'];
						}else{
							$arr_item[$time]['day'] = $citem['day'];
						}

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
						if(empty($citem['imgfile'])) {
                            $surl = $citem['imgurl'];
							$citem['sImgUrl'] = $this->remote->getLocalImage($surl, 360);
							$citem['imgurl'] = $this->remote->getLocalImage($surl, 640);
                            $citem['shareurl'] = $this->remote->getLocalImage($surl, 150);
						}else{
                            $surl = $citem['imgfile'];
							$citem['sImgUrl'] = $this->remote->getQiniuImage($surl, 360);
							$citem['imgurl'] = $this->remote->getQiniuImage($surl, 640);
                            $citem['shareurl'] = $this->remote->getQiniuImage($surl, 150);
						}

						$citem['diary_items'] = array();
						$tmp = $this->Diary_model->getItemsPrice($citem['nid']);

						if(!empty($tmp)){
							foreach($tmp as $i){
								$itemid = $this->Diary_model->getItemId($i['item_name']);
								$i['other'] = $this->Diary_model->isItemLevel($itemid,1);
								$citem['diary_items'][] = $i;

							}
						}
						if(empty($citem['diary_items'])){
							$citem['diary_items'][0]['item_name'] = $citem['item_name'];
							$citem['diary_items'][0]['item_price'] = $citem['item_price'];
							$citem['diary_items'][0]['pointX'] = $citem['pointX'];
							$citem['diary_items'][0]['pointY'] = $citem['pointY'];
							$citem['diary_items'][0]['other'] = $citem['other'];
						}
						if($item['oneday']) {
							$arr_item[$item['oneday']]['all'][] = $citem;
						}else{
							$arr_item[$time]['all'][] = $citem;
						}
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
					if(empty($item['imgfile'])) {
                        $surl = $item['imgurl'];
						$item['imgurl'] = $this->remote->getLocalImage($surl);
                        $item['shareurl'] = $this->remote->getLocalImage($surl, 150);
					}else{
                        $surl = $item['imgfile'];
						$item['imgurl'] = $this->remote->getQiniuImage($surl);
                        $item['shareurl'] = $this->remote->getQiniuImage($surl, 150);
					}

					if(empty($item['leftimgfile'])) {
						$item['leftimgurl'] = $this->remote->getLocalImage($item['leftimgurl']);
					}else{
						$item['leftimgurl'] = $this->remote->getQiniuImage($item['leftimgfile']);
					}

					if(empty($item['rightimgfile'])) {
						$item['rightimgurl'] = $this->remote->getLocalImage($item['rightimgurl']);
					}else{
						$item['rightimgurl'] = $this->remote->getQiniuImage($item['rightimgfile']);
					}

					$item['operation_time'] = date('Y-m-d',$item['operation_time']);
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
					if(empty($item['imgfile'])){
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
					}else {
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
					}
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

					$item ['basicinfo'] = $this->getBasicInfo($userInfo[0]);
					$item['alias'] = $userInfo[0]['alias'] ? $userInfo[0]['alias']:$userInfo[0]['username'];
					if(preg_match("/^1[0-9]{10}$/",$item['alias'])){
						$item['alias'] = substr($item['alias'],0,4).'****';
					}

					if(empty($item['imgfile'])){
						$item['sImgurl'] = $this->remote->getLocalImage($item['imgurl'], 360);
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
					}else {
						$item['sImgurl'] = $this->remote->getQiniuImage($item['imgfile'], 360);
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
					}

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
	 * @param int lastid 该页面里面最大的日记编号
	 * @param string item_name 项目名字
	 * @param int $doctor　专家名字
	 * @param int $yiyuan  医院名称
	 * @param string $param
	 */
	public function getDiaryNodeListV2($pageSize=9) {
		//error_reporting(E_ALL);
		//ini_set('display_errors', 'On');

		$result = array();
		$result['state'] = '000';
		$result['data'] = array();
		$pageSize=9;
		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = ($page - 1) * $pageSize;
		$tmp = $this->Diary_model->getDiaryFrontList($offset,2, $pageSize);
		$item_name = $this->input->get('item_name');
		$doctor = $this->input->get('doctor');
		$yiyuan = $this->input->get('yiyuan');
		//$uid = $this->input->get('uid');

		$tmp = $this->Diary_model->getDiaryNodeList($item_name,$doctor,$yiyuan, $offset,$pageSize);


		$k = 0;
		if(!empty($tmp)){
			$n = 1;
			foreach($tmp as $item){

				if(empty($item['imgfile'])){
					$item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
				}else{
					$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
				}
				$rs = $this->Diary_model->get_user_by_username($item['uid']);
				$item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$item['username'] = isset($item['username'])?$item['username']:'';
				$item ['basicinfo'] = $this->getBasicInfo($rs[0]);
				$item['level'] = $this->getLevel($rs[0]['jifen']);
				$item['sex'] = isset($rs[0]['sex'])?$rs[0]['sex']:0;
				if(preg_match("/^1[0-9]{10}$/",$item['username'])){
					$item['username'] = substr($item['username'],0,4).'****';
				}
				$item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
				$item['pageview'] = intval($item['views']);
				$item['views'] = intval($item['views']);
				$item['zan'] = intval($item['zan']);
				/*if(is_null($item['doctor']) || empty($item['doctor'])){
				 $item['doctor'] = '';
				 }

				 if(is_null($item['hospital']) || empty($item['hospital'])){
				 $item['hospital'] = '';
				 }*/
				$item['istopic'] = 0;
				if(isset($rs[0]['age'])){
					$item['age'] = $this->getAge($item['uid']);
				}else{
					$item['age'] = '';
				}
				$item['thumb'] = $this->profilepic($item['uid']);
				$item['zanNum'] = ($this->Diary_model->getZan($item['nid'])>0)?intval($this->Diary_model->getZan($item['nid'])):0;
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
				$item['istopic'] =0; //美人计
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


				$result['data'][] = $item;

				$n++;
			}
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
					$item ['basicinfo'] = $this->getBasicInfo($temp[0]);
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
					$item ['basicinfo'] = $this->getBasicInfo($temp[0]);
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

						if(empty($citem['imgfile'])) {
							$citem['sImgUrl'] = $this->remote->getLocalImage($citem['imgurl'], 360);
							$citem['imgurl'] = $this->remote->getLocalImage($citem['imgurl'], 640);
						}else{
							$citem['sImgUrl'] = $this->remote->getQiniuImage($citem['imgfile'], 360);
							$citem['imgurl'] = $this->remote->getQiniuImage($citem['imgfile'], 640);
						}

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
					$item ['basicinfo'] = $this->getBasicInfo($temp[0]);
					$item['username'] = $temp[0]['alias'] ? $temp[0]['alias'] : $temp[0]['username'];
					$item['username'] = $item['username'] ? $item['username'] : '';
					$item['jifen'] = isset($item['jifen'])?$item['username']:0;
					$item['daren'] = $temp[0]['daren'];
					$item['level'] = $this->getLevel($temp[0]['jifen']);
					$item['age'] = $this->getAge($item['uid']);
					if(preg_match("/^1[0-9]{10}$/",$item['username'])){
						$item['username'] = substr($item['username'],0,4).'****';
					}
					$item['created_at'] = date('Y-m-d',$item['created_at']);
					$item['countZan'] = $this->Diary_model->getZan($nid);
					$item['zanNum'] = $this->Diary_model->getZan($nid);

					if(empty($item['imgfile'])){
                        $surl = $item['imgurl'];
						$item['imgurl'] = $this->remote->getLocalImage($surl, 640);
						$item['shareurl'] = $this->remote->getLocalImage($surl, 150);

					}else{
                        $surl = $item['imgfile'];
						$item['imgurl'] = $this->remote->getQiniuImage($surl, 640);
						$item['shareurl'] = $this->remote->getQiniuImage($surl, 150);
					}

					$item['thumb'] = $this->remote->thumb($item['uid']);

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


					$item['uid'] = $item['uid'];
					$result['data'][] = $item;
					$this->db->where('nid',$nid);
					$this->db->update('note',array('views'=>(intval($item['views'])+1)));
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
		$type = $this->input->get('type')?$this->input->get('type'):0;
		$result['state'] = '000';
		$result['data'] = array();


		if(intval($nid) > 0){
			if($type == 1) {
				$result['data']['total_comments'] = $this->Diary_model->getCommentCount($nid, $type);
				$result['data']['page_size'] = ceil(intval($result['data']['total_comments']) / 10);
			}else{
				$result['data']['total_comments'] = $this->Diary_model->getCommentCount($nid);
				$result['data']['page_size'] = ceil(intval($result['data']['total_comments']) / 10);
			}
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
		$cday = $this->input->post('day');  //第几天
		$datetime = $this->input->post('datetime'); //图片拍照的日期


		$imgurl = '';

		$check = 0;
		$setting = 0;

		$result['state'] = '000';


		if(!empty($content) && $this->uid){
			$this->db->where('id',$this->uid);
			$this->db->where('banned',1);
			$num = $this->db->get('users')->num_rows();

			if(intval($num) > 0){
				$result['state'] = '012';
				$result['notice'] = '该用户被禁用或者已经被删除！';
				echo json_encode($result);
				exit;
			}
			if($type != 1) {
				/*if (isset ($_FILES['diaryPic']['name']) && $_FILES['diaryPic']['name'] != '') {
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
				 }*/
				$imgurl = $this->input->post('key1');
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
					$result['score'] =  $this->Score_model->addScore(51,$this->uid);
				}else if($hospital){
					$result['score'] = $this->Score_model->addScore(50,$this->uid);
				}
				$result['score'] = $this->Score_model->addScore(49,$this->uid);

				if(!$this->input->post('day')){
					$oneday = strtotime(date('Y-m-d'));
					$result['debug'] =1;
				}else{
					$oneday = strtotime($this->input->post('day'));
					$result['debug'] =2;
				}

				if(empty($cday)){
					$cday = $categoryDay;
				}

				if(empty($datetime)){
					$datetime = $oneday;
				}else{
					$datetime = strtotime($datetime);
				}
                
				$data = array('uid' => $this->uid,
                    'ncid' => $ncid,
                    'imgfile' => $imgurl,
                    'content' => $content,
                    'item_name' => $item_name,
                    'item_price' => $item_price,
                    'doctor' => $doctor,
                    'hospital' => $hospital,
                    'review' => $check,
                    'setting' => $setting,
                    'cday' => $cday,
                    'itemday' => $itemDay,
                    'oneday' => $datetime,
                    'pointX' => $pointX,
                    'pointY' => $pointY,
                    'views' => rand(10,50),
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

				$nid = $this->Diary_model->getLastID();
				$result['nid'] = $nid;
				for($i=1;$i <=3;$i++){

					$item_name = $this->input->post('item_name_'.$i) ? $this->input->post('item_name_'.$i) : '';
					$item_price = $this->input->post('item_price_'.$i) ? $this->input->post('item_price_'.$i): '';
					$pointX = $this->input->post('x_'.$i) ? $this->input->post('x_'.$i) : '0.00';
					$pointY = $this->input->post('y_'.$i) ? $this->input->post('y_'.$i): '0.00';
					if(empty($item_name))
					continue;
					$this->db->insert('note_item',array('nid'=> $nid, 'item_name'=>$item_name,'item_price'=>$item_price,'pointx'=>$pointX, 'pointy'=>$pointY ,'created_at'=>time()));
					//$result['data']['debug'][] =$this->db->last_query();
				}
				$result['notice'] = '发送成功！';
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
		//$this->diaryPublish();
	}
	#美人计发布
	public function diaryPublish($nid,$item_name){
		
		$array=array(
			'year'=>date('Y',time()),
			'month'=>date('m',time()),
			'week'=>date('w',time()),
			'day'=>date('d',time()),
			'hour'=>date('H',time()),
			'minute'=>date('i',time()),
			'second'=>date('s',time()),
			'user_id'=>$this->input->get('uid'),
			'diary_id'=>$nid,
			'diary_selection'=>$item_name,
			'type'=>1
		);
		$this->db->insert('addup_diary',$array);
	}
	#美人计评论
    public function diaryComment($nid,$item_name){
		
		$array=array(
			'year'=>date('Y',time()),
			'month'=>date('m',time()),
			'week'=>date('w',time()),
			'day'=>date('d',time()),
			'hour'=>date('H',time()),
			'minute'=>date('i',time()),
			'second'=>date('s',time()),
			'user_id'=>$this->input->get('uid'),
			'diary_id'=>$nid,
			'diary_selection'=>$item_name,
			'type'=>2
		);
		$this->db->insert('addup_diary',$array);
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

			$this->db->where('id',$this->uid);
			$this->db->where('banned',1);
			$num = $this->db->get('users')->num_rows();

			if(intval($num) > 0){
				$result['state'] = '012';
				$result['notice'] = '该用户被禁用或者已经被删除！';
				echo json_encode($result);
				exit;
			}

			/*if (isset ($_FILES['commentPic']['name']) && $_FILES['commentPic']['name'] != '') {
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
			 }*/

			$imgurl = $this->input->post('key1');
			$this->db->where('touid', $touid);
			$num = $this->db->get('note_comment')->num_rows();
			if($num == 10){
				$result['score'] = $this->Score_model->addScore(57,$touid);
			}else if($num == 50){
				$result['score'] = $this->Score_model->addScore(58,$touid);
			}else if($num == 100){
				$result['score'] =  $this->Score_model->addScore(59,$touid);
			}
			$result['score'] = $this->Score_model->addScore(64,$this->uid);

			$data = array(
                'nid'=>$nid,
                'fromusername'=>$fromusername,
                'fromuid'=>$fromuid,
                'tousername'=>$tousername,
                'touid'=>$touid,
                'content'=>$content,
                'imgfile'=>$imgurl,
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
			$cid = $this->Diary_model->getLastID();


			//send IGTTUI push
			$this->load->model('Users_model');
			$clientid = $this->Users_model->getClientID($nid, 1);

			$result['debug'] = $clientid[0]['clientid'];
			$this->db->where('nid',$nid);
			$tmpnote = $this->db->get('note')->row_array();
			if($this->uid != $tmpnote['uid']) {
				if (!empty($clientid)) {
					$this->load->library('igttui');
					$d = $this->igttui->sendMessage($clientid[0]['clientid'], "diary:" . $nid . ":" . $result['page'] . ':' . $content);
					$result['d'] = $d;
				} else {
					$this->load->model('push');
					$push = array('type' => 'diary', 'id' => $nid, 'page' => $result['page']);
					$this->push->sendUser('[美人计]新回复:' . $content, $touid, $push);
				}
			}
			$result['notice'] = '发送成功';
		}else{
			$result['state'] = '012';
			$result['notice'] = '用户未登录';
		}
		
		echo json_encode($result);
		$this->diaryComment($nid,$item_name);
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
		$type = $this->input->get('type')?$this->input->get('type'):0; //type为兼容叠楼功能1为叠楼
		$result['state'] = '000';
		$result['data'] = array();

		if(intval($nid) > 0){
			if($type == 1) {
				$tmp = $this->Diary_model->getCommentList($nid, $offset, 10, $type);

			}else{
				$tmp = $this->Diary_model->getCommentList($nid, $offset);
			}
			if(!empty($tmp)){

				foreach($tmp as $item){

					$item['thumb'] = $this->remote->thumb($item['fromuid'],105);
					$temp = $this->Diary_model->get_user_by_username($item['fromuid']);
					$username = !empty($temp[0]['alias'])?$temp[0]['alias']:$temp[0]['username'];
					$item['username'] = $username;
					if(preg_match("/^1[0-9]{10}$/",$item['username'])){

						$item['username'] = $this->getAlias($item['username']);//substr($item['username'],0,4).'****';
					}

					if(preg_match("/^1[0-9]{10}$/",$item['fromusername'])){

						$item['fromusername'] = $this->getAlias($item['fromusername']);//substr($item['fromusername'],0,4).'****';
					}
					$item ['basicinfo'] = $this->getBasicInfo($temp[0]);
					$item['level'] = $this->getLevel($temp[0]['jifen']);
					$item['age'] = $this->getAge($item['fromuid']);

					if(empty($item['imgfile'])) {
						$item['imgurl'] = !empty($item['imgurl']) ? $this->remote->getLocalImage($item['imgurl']) : '';
						$ptmp = getimagesize($item['imgurl']);
						$item['width'] = isset($ptmp[0]) ? $ptmp[0] : 0;
						$item['height'] = isset($ptmp[1]) ? $ptmp[1] : 0;
					}else{

						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
						$ptmp = getimagesize($item['imgurl']);
						$item['width'] = isset($ptmp[0]) ? $ptmp[0] : 0;
						$item['height'] = isset($ptmp[1]) ? $ptmp[1] : 0;
					}

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

					if($type == 1){
						$item['reply'] = $this->Diary_model->getChildReply($item['cid']);
						if(!empty($item['reply'])){
							$reply = array();
							foreach($item['reply'] as $it){
								//$temp = $this->Diary_model->get_user_by_username($it['fromuid']);
								//$username = !empty($temp[0]['alias'])?$temp[0]['alias']:$temp[0]['username'];
								//$it['username'] = $username;
								/*if(preg_match("/^1[0-9]{10}$/",$it['username'])){
								$it['username'] = $this->getAlias($it['username']);//substr($it['username'],0,4).'****';
								}*/
								if(preg_match("/^1[0-9]{10}$/",$it['fromusername'])){
									$it['fromusername'] = $this->getAlias($it['fromusername']);//substr($it['fromusername'],0,4).'****';
								}
								if(preg_match("/^1[0-9]{10}$/",$it['tousername'])){
									$it['tousername'] = $this->getAlias($it['tousername']);//substr($it['tousername'],0,4).'****';
								}
								if(empty($it['imgfile'])) {

									$it['imgurl'] = !empty($it['imgurl']) ? $this->remote->getLocalImage($it['imgurl']) : '';
									$ptmpc = getimagesize($it['imgurl']);
									$it['width'] = isset($ptmpc[0]) ? $ptmpc[0] : 0;
									$it['height'] = isset($ptmpc[1]) ? $ptmpc[1] : 0;
								}else{
									$it['imgurl'] = !empty($it['imgfile']) ? $this->remote->getQiniuImage($it['imgfile']): '';
									$ptmpc = getimagesize($it['imgurl']);
									$it['width'] = isset($ptmpc[0]) ? $ptmpc[0] : 0;
									$it['height'] = isset($ptmpc[1]) ? $ptmpc[1] : 0;
								}

								if ($it['created_at'] > time() - 3600) {
									$it['created_at'] = intval((time() - $it['created_at']) / 60) . '分钟前';
								} else {
									$it['created_at'] = date('Y-m-d', $it['created_at']);
								}
								$reply[] = $it;
							}
							$item['reply'] = array();
							$item['reply'] = $reply;
						}
						if(!empty($item['reply'])){
							$item['isreply'] = 1;
						}
					}else{
						$iscomment = $this->Diary_model->isComment($item['pcid']);
						if(intval($iscomment) > 0){
							// 判断是否有评论
							$item['reply'] = $this->Diary_model->getChildReply($item['cid']);
							$item['isreply'] = 1;

						}
					}
					$result['data'][] =$item;
				}
				$result['total_comments'] = $this->Diary_model->getCommentCount($nid);
				$result['page_size'] = ceil(intval($result['total_comments'])/10);
			}
		}
		echo json_encode($result);
	}

	private function getAlias($phone){

		if(preg_match("/^1[0-9]{10}$/", $phone)){

			$this->db->select('username, alias');
			$this->db->where('phone',$phone);
			$user = $this->db->get('users')->row_array();

			$alias = !empty($user['alias'])?$user['alias']:$user['username'];
			return $alias;
		}else{
			return ;
		}
	}
	/*
	 public function updateComment($id, $data = array()){


	 }
	 */
	/**
	 * @param string $param
	 */
	public function getMyNoteList($param = ''){
        $page = $this->input->get('page');
        $page = isset($page) ?$this->input->get('page'):1;
		$offset = (intval($page)-1)*10;

		$result['state'] = '000';
        if($this->input->get('uid')){
            $this->uid = $this->input->get('uid');
        }

		if($this->uid){
			$result['data'] = $this->Diary_model->getMyNoteList($this->uid,$offset);

			if(!empty($result['data'])){

				foreach($result['data'] as $key=>$item){

					if(empty($item['imgfile'])) {
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
					}else{
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
					}
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
					if(empty($item['imgfile'])){
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
					}else{
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
					}

					$item['diaryCount'] = $this->Diary_model->getDiaryCount($item['ncid']);
					$item['operation_time'] = date('Y-m-d',$item['operation_time']);
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

					if(empty($item['imgifle'])) {
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
					}else{
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
					}
					$item['diaryCount'] = $this->Diary_model->getDiaryCount($item['ncid']);
					$item['totalCount'] = $total;
					$diary_items = $this->Diary_model->getLastTagsForCategory($item['ncid']);
					$item['diary_items'] = isset($diary_items)?$diary_items:'';

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

			/*if (isset ($_FILES['frontPic']['name']) && $_FILES['frontPic']['name'] != '') {
			 $result['notice'] = '美人记发布成功！';
			 $imgurl = date('Y') . '/' . date('m') . '/' . date('d');
			 $ext = '.jpg';
			 $filename = uniqid().rand(1000,9999) . $ext;
			 $imgurl .= '/' . $filename;
			 $ptmp = getimagesize($_FILES['frontPic']['tmp_name']);
			 if (!$this->remote->cp($_FILES['frontPic']['tmp_name'], $filename, $imgurl, array (
			 'width' => 600,
			 'height' => 800
			 ), true)) {

			 $result['state'] = '001';
			 $result['notice'] = '图片上传失败！';
			 echo json_encode($result);
			 exit;
			 }
			 }
			 if (isset ($_FILES['rightPic']['name']) && $_FILES['rightPic']['name'] != '') {
			 $result['notice'] = '美人记发布成功！';
			 $rightimgurl = date('Y') . '/' . date('m') . '/' . date('d');
			 $ext = '.jpg';
			 $filename = uniqid().rand(1000,9999) . $ext;
			 $rightimgurl .= '/' . $filename;
			 $ptmp = getimagesize($_FILES['rightPic']['tmp_name']);
			 if (!$this->remote->cp($_FILES['rightPic']['tmp_name'], $filename, $rightimgurl, array (
			 'width' => 600,
			 'height' => 800
			 ), true)) {

			 $result['state'] = '002';
			 $result['notice'] = '图片上传失败！';
			 echo json_encode($result);
			 exit;
			 }
			 }
			 if (isset ($_FILES['leftPic']['name']) && $_FILES['leftPic']['name'] != '') {
			 $result['notice'] = '美人记发布成功！';
			 $leftimgurl = date('Y') . '/' . date('m') . '/' . date('d');
			 $ext = '.jpg';
			 $filename = uniqid().rand(1000,9999) . $ext;
			 $leftimgurl .= '/' . $filename;
			 $ptmp = getimagesize($_FILES['leftPic']['tmp_name']);
			 if (!$this->remote->cp($_FILES['leftPic']['tmp_name'], $filename, $leftimgurl, array (
			 'width' => 600,
			 'height' => 800
			 ), true)) {

			 $result['state'] = '003';
			 $result['notice'] = '图片上传失败！';
			 echo json_encode($result);
			 exit;
			 }
			 }*/

			$imgurl = $this->input->post('key1');
			$leftimgurl = $this->input->post('key2');
			$rightimgurl = $this->input->post('key3');

			if($imgurl != '') {
				$data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc, 'imgfile' => $imgurl,'rightimgfile'=> $rightimgurl, 'leftimgfile'=> $leftimgurl,'operation_time'=>$operation_time, 'created_at' => time(), 'updated_at' => time());
			}else{
				$data = array('uid' => $this->uid, 'is' => $is, 'title' => $title, 'desc' => $desc,'operation_time'=>$operation_time,'created_at' => time(), 'updated_at' => time());
				if($imgurl){
					$data['imgfile'] = $imgurl;
				}else{
					$data['imgfile'] = '';
				}

				if($leftimgurl){
					$data['leftimgfile'] = $leftimgurl;
				}else{
					$data['leftimgfile'] = '';
				}

				if($rightimgurl){
					$data['rightimgfile'] = $rightimgurl;
				}else{
					$data['rightimgfile'] = '';
				}
			}
			//$result['debug'] = $data;
			if($type == 1) {
				$isCategory = $this->Diary_model->isCategory($title, $this->uid);
				if (intval($isCategory) <= 0) {
					$result['data'][] = $this->Diary_model->addNoteCategory($data);
					$result['return'] = array('ncid' => $this->db->insert_id(), 'title' => $title, 'operation_time'=>$operation_time, 'desc' => $desc);
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

							if(empty($item['imgifle'])){
								$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
							}else{
								$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
							}

							if(empty($item['leftimgifle'])){
								$item['leftimgurl'] = $this->remote->getLocalImage($item['leftimgurl']);
							}else{
								$item['leftimgurl'] = $this->remote->getQiniuImage($item['leftimgfile']);
							}

							if(empty($item['rightimgifle'])){
								$item['rightimgurl'] = $this->remote->getQiniuImage($item['rightimgurl']);
							}else{
								$item['rightimgurl'] = $this->remote->getQiniuImage($item['rightimgfile']);
							}

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

	private function getMyTopicCount($uid){

		$sql = "SELECT w.comments,w.content, w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
		$sql .= ' FROM wen_weibo as w LEFT JOIN users as u ON w.uid=u.id';

		$ctime = time(); //set publish time
		if ($uid) {

			$sql .= ' WHERE w.type&25 AND w.uid = ' . $uid;
		}

		$sql .= " AND ctime<={$ctime}  and w.isdel=0 ORDER BY w.ctime DESC ";

		$query = $this->db->query($sql);
		//echo "<pre>";

		return $query->num_rows()?$query->num_rows():0;
	}
	/**
	 * 获取用户信息
	 * @param int lastiｄ　大于等于０
	 * @param string $param
	 */
	public function getMyNoteCategoryUserInfo($param = ''){

		$result['state'] = '000';
		$result['data'] = array();

		$uid = $this->input->get('uid')?$this->input->get('uid'):$this->input->post('uid');

		if($uid){
			$temp = $this->Diary_model->get_user_by_username($uid);
			if(!empty($temp)){
				foreach($temp as $item){
					$item['thumb'] = $this->remote->thumb($uid);
					$item['username'] = $item['alias'] ? $item['alias'] : $item['username'];
					if(preg_match("/^1[0-9]{10}$/",$item['username'])){
						$item['username'] = substr($item['username'],0,4).'****';
					}
					$item['funCount'] = $this->Diary_model->getFunCount($uid);
					$item['followerCount'] = $this->Diary_model->getFollowerCount($uid);

					$topicCommentMyNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and touid={$uid}")->num_rows();
					//$topicMyCommentNotReadCount = $this->db->query("select *from wen_comment where is_read = 0 and fuid={$this->uid}")->num_rows();
					$diaryCommentMyNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and touid={$uid}")->num_rows();
					//$diaryMyCommentNotReadCount = $this->db->query("select *from note_comment where is_read = 0 and fromuid={$this->uid}")->num_rows();
					$commnetTotal = $topicCommentMyNotReadCount + $diaryCommentMyNotReadCount;
					$zanMyNotReadCount = $this->db->query("select *from wen_zan where (type='diary' or type='topic') and  is_read = 0 and touid={$uid}")->num_rows();
					//$myZanNotReadCount =$this->db->query("select *from wen_zan where is_read = 0 and uid={$this->uid}")->num_rows();
					$zanNotReadTotal = $zanMyNotReadCount;// + $myZanNotReadCount;
					$zixunNotReadCount =$this->db->query("select *from wen_questions left join wen_answer ON wen_questions.id=wen_answer.qid where wen_answer.new_comment = 1 and wen_questions.fUid={$uid}")->num_rows();
					//$messageNotReadCount =  $zixunNotReadCount + $zanNotReadTotal + $commnetTotal;

					$zixunNotReviewCount =$this->db->query("select *from wen_questions left join reviews ON wen_questions.id=reviews.qid where reviews.userto={$uid}")->num_rows();

					$this->tehuiDB->where('order.state', 'unpay');
					$this->tehuiDB->where('create_time > ',time() - 86400 );
					$this->tehuiDB->where('order.user_id', $uid);
					$this->tehuiDB->order_by('order.id', 'DESC');
					$this->tehuiDB->join('team', 'order.team_id = team.id');
					$this->tehuiDB->select('order.id, order.express,order.realname,order.express_no,order.quantity, team.id as tid, order.state,team.team_price, team.image,team.title,order.address,order.realname, order.mobile,order.create_time,team.delivery, order.is_refund');
					$tmp = $this->tehuiDB->get('order')->num_rows();

					$orderCount = $tmp;
					$topicCount = $this->getMyTopicCount($uid);
					$diaryCount = $this->db->query("select *From note where uid='{$uid}' and `created_at` <= ".time())->num_rows();
					$item['debug'] = $this->Diary_model->getstate($uid, $this->uid);
					if ($this->Diary_model->getstate($uid, $this->uid)) {
						$item['follow'] = 1;
					} else {
						$item['follow'] = 0;
					}
					$item['signin'] = $this->db->query("select *From user_signin where uid={$uid} and calDate='".date('Y-m-d')."'")->num_rows();
					$item['signin_jifen'] = 30;
					$item['commnetTotal'] = $commnetTotal;
					$item['age'] = $this->getAge(intval($item['age']));
					$item['topicCount'] = $topicCount;
					$item['diaryCount'] = $diaryCount;
					$item['orderCount'] = $orderCount;
					$this->db->where('vuid', $uid);
					$item['visitCount'] = $this->db->get('visit')->num_rows();
					$item['zanNotReadTotal'] = $zanNotReadTotal;
					$item['zixunNotReadCount'] = $zixunNotReadCount;
					$item['zixunNotReviewCount'] = $zixunNotReviewCount;
					$item['jifen']= isset($item['jifen']) && is_numeric($item['jifen'])?intval($item['jifen']):0;
					$item['level'] = $this->getLevel($item['jifen']);
					$this->load->model('Background_Model');

					$item['userBackgroundImg'] = $this->remote->getLocalImage($this->Background_Model->getUserBackground($uid,1));

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

	public function getVisitor(){

		$result['state'] = '000';
		$result['data'] = array();
		$touid = $this->input->get('touid');
		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = intval($this->input->get('page')-1)*10;

		if(intval($touid) > 0){
			$this->db->select('users.id, visit.uid,users.username, users.age, users.alias, users.jifen, user_profile.sex, user_profile.city,visit.created_at');
			$this->db->from('users');
			$this->db->join('user_profile', 'users.id = user_profile.user_id');
			$this->db->join('visit', 'users.id = visit.uid');
			$this->db->where('visit.vuid', $touid);
			$this->db->limit(10, $offset);
			$this->db->order_by('visit.created_at desc');
			$tmp = $this->db->get()->result_array();
			if(!empty($tmp)){
				foreach($tmp as $item){
					$res = $this->Diary_model->get_user_by_username($item['uid']);
					$item['username'] = $res[0]['alias'] ? $res[0]['alias'] : $res[0]['username'];
					if(preg_match("/^1[0-9]{10}$/",$item['username'])){
						$item['username'] = substr($item['username'],0,4).'****';
					}
					$item['thumb'] =  $this->remote->thumb($item['uid']);
					$item['level'] = $this->getLevel($item['jifen']);
					if(intval($item['created_at']) > strtotime(date('Y-m-d'))){
						$item['type'] = 1;
					}else{
						$item['type'] = 0;
					}
					$item['city'] = empty($item['city'])?'':$item['city'];
					$item['age'] = $this->getAge(intval($item['uid']));
					$result['data'][] = $item;
				}
			}

			$this->db->where('vuid', $touid);
			$this->db->where('created_at >', strtotime(date('Y-m-d')));
			$tmpnum = $this->db->get('visit')->num_rows();
			$result['todayNum'] = $tmpnum;
			$this->db->where('vuid', $touid);
			$result['Num'] = $this->db->get('visit')->num_rows();
		}else{
			$result['state'] = '012';
			$result['notice'] = '请传入用户ID!';
		}
		echo json_encode($result);
	}
	public function getVisitorV2(){

		$result['state'] = '000';
		$result['data'] = array();
		$touid = $this->input->get('touid');
		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = intval($this->input->get('page')-1)*10000;

		if(intval($touid) > 0){
			$this->db->select('users.id, visit.uid,users.username, users.age, users.alias, users.jifen, user_profile.sex, user_profile.city,visit.created_at');
			$this->db->from('users');
			$this->db->join('user_profile', 'users.id = user_profile.user_id');
			$this->db->join('visit', 'users.id = visit.vuid');
			$this->db->where('visit.vuid', $touid);
			$this->db->limit(10000, $offset);
			$this->db->order_by('visit.created_at desc');
			$tmp = $this->db->get()->result_array();
			if(!empty($tmp)){
				foreach($tmp as $item){
					$res = $this->Diary_model->get_user_by_username($item['uid']);
					$item['username'] = $res[0]['alias'] ? $res[0]['alias'] : $res[0]['username'];
					if(preg_match("/^1[0-9]{10}$/",$item['username'])){
						$item['username'] = substr($item['username'],0,4).'****';
					}
					$item['thumb'] =  $this->remote->thumb($item['uid']);
					$item['level'] = $this->getLevel($item['jifen']);
					if(intval($item['created_at']) > strtotime(date('Y-m-d'))){
						$item['type'] = 1;
					}else{
						$item['type'] = 0;
					}
					$item['city'] = empty($item['city'])?'':$item['city'];
					$item['age'] = $this->getAge(intval($item['uid']));
					$result['data'][] = $item;
				}
			}

			$this->db->where('vuid', $touid);
			$this->db->where('created_at >', strtotime(date('Y-m-d')));
			$tmpnum = $this->db->get('visit')->num_rows();
			$result['todayNum'] = $tmpnum;
			$this->db->where('vuid', $touid);
			$result['Num'] = $this->db->get('visit')->num_rows();
		}else{
			$result['state'] = '012';
			$result['notice'] = '请传入用户ID!';
		}
		echo json_encode($result);
	}
	public function visitor(){
		$result['state'] = '000';
		$result['data'] = array();
		$fromuid = $this->input->get('fromuid');
		$touid = $this->input->get('touid');
		if((intval($fromuid) > 0 && intval($touid) > 0) && ($fromuid != $touid)){
			$this->db->where('uid', $fromuid);
			$this->db->where('vuid', $touid);
			$tmp = $this->db->get('visit')->result_array();

			if(empty($tmp)) {
				$result['data']['insert'] = $this->db->insert('visit', array('uid' => $fromuid, 'vuid' => $touid, 'created_at' => time()));
			}else{
				$this->db->where('uid', $fromuid);
				$result['data']['update'] = $this->db->update('visit', array('created_at'=>time()));
			}
		}else{
			$result['state'] = '012';
			$result['notice'] = '请传入用户ID!';
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
					//$result['data'][] = $item;
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
							if(empty($rs[0]['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
							}
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
								if(empty($it['imgfile'])) {
									$it['imgurl'] = $this->remote->getLocalImage($it['imgurl']);
								}else{
									$it['imgfile'] = $this->remote->getQiniuImage($it['imgfile']);
								}

								if(isset($this->uid)) {
									$is = $this->Diary_model->isZan($this->uid, $it['nid']);
									$it['isZan'] = $is?1:0;
								}else{
									$it['isZan'] = 0;
								}
								$item['detail'] = $it;
							}
						}
						$item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}

					}
					//$item['fromusername'] = $userInfo[0]['username']?$userInfo[0]['username']:$userInfo[0]['alias'];
					//$item['thumb'] = $this->remote->thumb($this->uid);
					$user = $this->Diary_model->get_user_by_username($rs[0]['uid']);

					$item['username'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
					$item['username'] = isset($item['username'])?$item['username']:'';
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
							if(empty($rs[0]['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
							}
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
								if(empty($it['imgfile'])) {
									$it['imgurl'] = $this->remote->getLocalImage($it['imgurl']);
								}else{
									$it['imgurl'] = $this->remote->getQiniuImage($it['imgfile']);
								}
								if(isset($this->uid)) {
									$is = $this->Diary_model->isZan($this->uid, $it['nid']);
									$it['isZan'] = $is?1:0;
								}else{
									$it['isZan'] = 0;
								}
								$item['detail'] = $it;
							}
						}
						$item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}

					}
					$item['myname'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
					$item['thumb'] = $this->remote->thumb($item['uid']);
					$user = $this->Diary_model->get_user_by_username($item['uid']);
					$item['username'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
					$item['username'] = isset($item['username'])?$item['username']:'';
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
				$result['data']['score'] = $this->Score_model->addScore(60,$rs[0]['touid']);
			}else if($num == 50){
				$result['data']['score'] = $this->Score_model->addScore(61,$rs[0]['touid']);
			}else if($num == 100){
				$result['data']['score'] = $this->Score_model->addScore(62,$rs[0]['touid']);
			}

			$isZan = $this->Diary_model->isZan($this->uid, $contentid,$type);
			$result['debug'] = $this->uid."==".$contentid."==".$type."==".$isZan;
			if(!$isZan) {
				$result['data']['zan'] = $this->Diary_model->addZan($contentid, $this->uid,$type);
				$result['data']['flag'] = 1;
			}else{
				$result['data']['zan'] = $this->Diary_model->getZan($contentid,$type);
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
							if(empty($rs[0]['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
							}
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
							if(empty($rs[0]['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
							}
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

	public function onReadTopicComments(){
		$result['state'] = '000';
		$result['data'] = array();
		if($this->uid) {
			$this->db->where('touid', $this->uid);
			$this->db->where('is_read', 0);
			$this->db->update("wen_comment", array('is_read' => 1));
		}else{
			$result['state'] = '012';
			$result['notice'] = '用户未登录!';
		}

		echo json_encode($result);
	}

	public function onReadDiaryComments(){
		$result['state'] = '000';
		$result['data'] = array();
		if($this->uid) {
			$this->db->where('touid', $this->uid);
			$this->db->where('is_read', 0);
			$this->db->update("note_comment",array('is_read'=>1));
		}else{
			$result['state'] = '012';
			$result['notice'] = '用户未登录!';
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

        if($this->input->get('uid')){
            $this->uid = $this->input->get('uid');
        }
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
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}
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
						$item['is_send'] = 1;//我评论的
						$item['mycomment'] = $item['content'];
						$item['myname'] = $userInfo[0]['username'] ? $userInfo[0]['username'] : $userInfo[0]['alias'];
						$item['mythumb'] = $this->remote->thumb($this->uid);
						$rs = $this->Diary_model->getDiaryDetail($item['nid']);
						$item['content'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}
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
								if(empty($it['imgfile'])) {
									$it['imgurl'] = $this->remote->getLocalImage($it['imgurl']);
								}else{
									$it['imgurl'] = $this->remote->getQiniuImage($it['imgfile']);
								}
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
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}

						$rstmp = $this->Diary_model->getDiaryDetail($item['nid']);
						if (!empty($rstmp)) {

							foreach ($rstmp as $it) {
								$temp = $this->Diary_model->get_user_by_username($it['uid']);

								$it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
								$it['username'] = $it['username'] ? $it['username'] : '';
								$it['zanNum'] = $this->Diary_model->getZan($it['nid']);
								if(empty($it['imgfile'])) {
									$it['imgurl'] = $this->remote->getLocalImage($it['imgurl']);
								}else{
									$it['imgurl'] = $this->remote->getQiniuImage($it['imgfile']);
								}
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
					if(preg_match("/^1[0-9]{10}$/",$item['myname'])){
						$item['myname'] = $this->getAlias($item['myname']);
					}
					$item['yourthumb'] = $this->remote->thumb($item['fromuid']);
					$rs = $this->Diary_model->getDiaryDetail($item['nid']);
					$item['content'] = isset($rs[0]['content'])?$rs[0]['content']:'';
					if(empty($rs[0]['imgfile'])) {
						$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
					}else{
						$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
					}

					$rstmp = $this->Diary_model->getDiaryDetail($item['nid']);
					if (!empty($rstmp)) {

						foreach ($rstmp as $it) {
							$temp = $this->Diary_model->get_user_by_username($it['uid']);

							$it['username'] = $temp[0]['username'] ? $temp[0]['username'] : $temp[0]['alias'];
							if(preg_match("/^1[0-9]{10}$/",$it['username'])){
								$it['username'] = $this->getAlias($it['username']);
							}
							$it['username'] = $it['username'] ? $it['username'] : '';
							$it['zanNum'] = $this->Diary_model->getZan($it['nid']);
							if(empty($it['imgfile'])) {
								$it['imgurl'] = $this->remote->getLocalImage($it['imgurl']);
							}else{
								$it['imgurl'] = $this->remote->getQiniuImage($it['imgfile']);
							}
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
					if(preg_match("/^1[0-9]{10}$/",$item['yourname'])){
						$item['yourname'] = $this->getAlias($item['yourname']);
					}
					if ($item['created_at'] > time() - 3600) {
						$item['created_at'] = intval((time() - $item['created_at']) / 60) . '分钟前';
					} else {
						$item['created_at'] = date('Y-m-d H:i', $item['created_at']);
					}
					$result['data'][] = $item;
				}
				$result['debug'] = $this->db->last_query();
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
			$result['data'] = $this->Diary_model->getDiaryNodeList($item_name,$doctor,$yiyuan, $offset);
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
					if(empty($item['imgfile'])) {
						$item['sImgUrl'] = $this->remote->getLocalImage($item['imgurl'], 360);
						$item['imgurl'] = $this->remote->getLocalImage($item['imgurl'], 640);
					}else{
						$item['sImgUrl'] = $this->remote->getQiniuImage($item['imgfile'], 360);
						$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile'], 640);
					}
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
	 * 获取消息动态
	 */
	public function getMessages($params = ''){
		$offset = 100;
		$result['state'] = '000';
		$result['data'] = array();
		//$this->uid = $this->input->get('uid');

		if($this->uid){

			if(1){
				$tmp = $this->Diary_model->getDiaryMyFollowCommentsListV2($this->uid,0,$offset);

				$userInfo = $this->Diary_model->get_user_by_username($this->uid);

				if(!empty($tmp)){

					foreach($tmp as $item){
						$item['fromcontent'] = $item['content'];
						$item['tousername'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
						$item['fromthumb'] = $this->remote->thumb($item['fromuid']);
						$rs = $this->Diary_model->getDiaryDetail($item['nid']);
						if(empty($rs[0]['imgfile'])) {
							$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
						}else{
							$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
						}
						$item['tothumb'] = $this->remote->thumb($this->uid);

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
						$item['is'] = 1; //is 1为美人计
						$result['data'][$item['ctime']] = $item;
					}
				}

				$tmp = $this->Diary_model->getMyFollowCommentsListV2($this->uid,0, $offset);
				$userInfo = $this->Diary_model->get_user_by_username($this->uid);

				if(!empty($tmp)){
					foreach($tmp as $item){
						$rs = $this->Diary_model->getMyZanTopic($item['contentid']);

						$item['tocontent'] = isset($rs[0]['content'])?$rs[0]['content']:'';
						if(empty($item['tocontent'])){
							$r = unserialize($rs[0]['type_data']);
							$item['tocontent'] = isset($r['title'])?$r['title']:'';

							if(empty($item['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($item['imgfile']);
							}
						}
						$item['fromcontent'] = $item['comment'];
						$user = $this->Diary_model->get_user_by_username($item['fuid']);
						$item['fromuid'] = $item['fuid'];
						$item['tousername'] = isset($userInfo[0]['alias'])?$userInfo[0]['alias']:$userInfo[0]['username'];
						$item['yourname'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
						$item['fromusername'] = $item['yourname']?$item['yourname']:'';
						$item['fromthumb'] = $this->remote->thumb($item['fuid']);
						$item['tothumb'] = $item['pic']?$item['pic']:$this->remote->thumb($this->uid);

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
						$item['type'] = 1;
						$item['is'] = 0; //is 0为帖子
						$result['data'][$item['cTime']] = $item;
					}

				}
			}
			if(2){
				$tmp = $this->Diary_model->getNotZanMyList($this->uid, 0,100);
				$userInfo = $this->Diary_model->get_user_by_username($this->uid);

				if(!empty($tmp)){
					foreach($tmp as $item){
						if(isset($item['type']) && $item['type'] == 'topic') {
							$rs = $this->Diary_model->getMyZanTopic($item['contentid']);
							$item['tocontent'] = isset($rs[0]['content']) ? $rs[0]['content'] : '';
							if (empty($item['tocontent'])) {
								$r = unserialize($rs[0]['type_data']);
								$item['tocontent'] = isset($r['title']) ? $r['title'] : '';
								if(empty($item['imgfile'])) {
									$item['pic'] = $this->remote->getLocalImage($r['pic']['savepath']);
								}else{
									$item['pic'] = $this->remote->getQiniuImage($item['imgfile']);
								}
							}
							$item['fromcontent'] = '赞了这个帖子';
							$item['is'] = 0; // 1为帖子
						}else{
							$item['is'] = 1; // 0为美人计
							$rs = $this->Diary_model->getMyZanDiary($item['contentid']);
							$item['fromcontent'] = '赞了这个美人记';
							$item['tocontent'] = $rs[0]['content'];
							if(empty($rs[0]['imgfile'])) {
								$item['pic'] = $this->remote->getLocalImage($rs[0]['imgurl']);
							}else{
								$item['pic'] = $this->remote->getQiniuImage($rs[0]['imgfile']);
							}

						}
						$item['itemid'] = $item['id'];
						$item['id'] = $item['contentid'];
						unset($item['contentid']);
						$item['tousername'] = $userInfo[0]['alias']?$userInfo[0]['alias']:$userInfo[0]['username'];
						$item['touid'] = $this->uid;
						$item['tothumb'] = $item['pic']?$item['pic']:$this->remote->thumb($this->uid);
						$item['fromthumb'] = $this->remote->thumb($item['uid']);
						$user = $this->Diary_model->get_user_by_username($item['uid']);
						$item['fromusername'] = isset($user[0]['alias'])?$user[0]['alias']:$user[0]['username'];
						$item['fromuid'] = $item['uid'];

						if ($item['cTime'] > time() - 3600) {
							$item['fromcreated_at'] = intval((time() - $item['cTime']) / 60) . '分钟前';
						} else {
							$item['fromcreated_at'] = date('Y-m-d H:i', $item['cTime']);
						}
						$item['type'] = 2;
						unset($item['touid']);
						unset($item['uid']);
						//unset($item['id']);
						$result['data'][$item['cTime']] = $item;

					}

				}
			}
			if(3){

				$this->db->where('wen_questions.fUid', $this->uid);
				$this->db->where('wen_answer.new_comment', 1);
				$this->db->select('wen_questions.title,wen_questions.id,wen_questions.cdate,wen_questions.is_read,wen_answer.new_comment');
				$this->db->order_by("wen_questions.id", "desc");
				$this->db->from('wen_questions');
				$this->db->join('wen_answer','wen_questions.id=wen_answer.qid');

				$this->db->limit(100);
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
					$row['ansid'] = $row['ans'][0]['id'];
					$row['itemid'] = $row['ansid'];
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
		$result = array();
		$result['state'] = '000';
		$result['data'] = array();
		$type = $this->input->get('type');
		$id = $this->input->get('itemid');
		$is = $this->input->get('is');

		if($this->uid){
			if($type == 4){  //全部未读消息清除
				$this->db->where('touid', $this->uid);
				$this->db->where('is_read', 0);
				$result['data']['note_comment'] = $this->db->update("note_comment",array('is_read'=>1));


				$this->db->where('touid', $this->uid);
				$this->db->where('is_read', 0);
				$result['data']['wen_comment'] = $this->db->update("wen_comment",array('is_read'=>1));

				$this->db->where('fUid', $this->uid);
				$this->db->where('is_read', 0);
				$result['data']['wen_questions'] = $this->db->update("wen_questions",array('is_read'=>1));

				$this->db->where('touid', $this->uid);
				$this->db->where('is_read', 0);
				$result['data']['wen_zan'] = $this->db->update("wen_zan",array('is_read'=>1));

			}elseif($type == 3){ //未读咨询清除
				$this->db->where('uid', $this->uid);
				$this->db->where('new_comment', 1);
				$this->db->where('id', $id);
				$result['data']['wen_answer1'] =$this->db->update("wen_answer",array('new_comment'=>0));
			}elseif($type == 2){//未读点赞清除
				if($is == 0) {
					$this->db->where('touid', $this->uid);
					$this->db->where('is_read', 0);
					$this->db->where('id', $id);
					$result['data']['wen_zan2']=$this->db->update("wen_zan", array('is_read' => 1));
				}else{
					$this->db->where('touid', $this->uid);
					$this->db->where('is_read', 0);
					$this->db->where('id', $id);
					$result['data']['wen_zan3']=$this->db->update("wen_zan", array('is_read' => 1));
				}
			}elseif($type == 1){ //清凉未读美人计和未读帖子评论
				if($is == 0) {
					$this->db->where('touid', $this->uid);
					$this->db->where('is_read', 0);
					$this->db->where('id', $id);
					$result['data']['wen_comment']=$this->db->update("wen_comment", array('is_read' => 1));
				}else{
					$this->db->where('touid', $this->uid);
					$this->db->where('is_read', 0);
					$this->db->where('cid', $id);
					$result['data']['wen_comment5']=$this->db->update("note_comment", array('is_read' => 1));
				}
			}
		}else{
			$result['state'] = '012';
			$result['notice'] = '用户未登录!';
		}
		echo json_encode($result);
	}

	/**
	 * @param int $pageSize
	 * 获取我的美人计列表
	 */
	public function getUserDiary($pageSize=5) {

		$result = array();
		$result['state'] = '000';
		$result['data'] = array();

		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = ($page - 1) * $pageSize;
		$this->uid = $this->input->get('uid');
		$tmp = $this->Diary_model->getUserDiaryList($offset, $this->uid);

		if(!empty($tmp)){
			$i = 1;
			foreach($tmp as $item){

				if(empty($item['imgfile'])) {
					$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
				}else{
					$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
				}

				$rs = $this->Diary_model->get_user_by_username($item['uid']);
				$item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$item['username'] = isset($item['username'])?$item['username']:'';
				$item ['basicinfo'] = $this->getBasicInfo($rs[0]);
				$item['level'] = $this->getLevel($rs[0]['jifen']);
				$item['sex'] = isset($rs[0]['sex'])?$rs[0]['sex']:0;
				if(preg_match("/^1[0-9]{10}$/",$item['username'])){
					$item['username'] = substr($item['username'],0,4).'****';
				}
				$item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
				$item['pageview'] = intval($item['views']);

				/*if(is_null($item['doctor']) || empty($item['doctor'])){
				 $item['doctor'] = '';
				 }

				 if(is_null($item['hospital']) || empty($item['hospital'])){
				 $item['hospital'] = '';
				 }*/

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

				$result['data'][] = $item;
				$i++;
			}
		}

		echo json_encode($result);
	}


    public function getMyUserDiary($pageSize=5) {

        $result = array();
        $result['state'] = '000';
        $result['data'] = array();

        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = ($page - 1) * $pageSize;
        $this->uid = $this->input->get('uid');
        $tmp = $this->Diary_model->getUserDiaryList($offset, $this->uid);

        if(!empty($tmp)){
            $i = 1;
            foreach($tmp as $item){
                $r = array();

                $r['ncid'] = $item['ncid'];
                $r['content'] = $item['content'];
                $result['data'][] = $r;
                $i++;
            }
        }

        echo json_encode($result);
    }

	public function diaryList($pageSize=10) {

		$result = array();
		$result['state'] = '000';
		$result['data'] = array();

		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = ($page - 1) * $pageSize;
		$tmp = $this->Diary_model->getDiaryFrontListV2($offset);

		if(!empty($tmp)){
			$i = 1;
			foreach($tmp as $item){

				if(empty($item['imgfile'])) {
					$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
				}else{
					$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
				}
				$rs = $this->Diary_model->get_user_by_username($item['uid']);
				$item['username'] = !empty($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$item['username'] = !empty($item['username'])?$item['username']:'';
				$item ['basicinfo'] = $this->getBasicInfo($rs[0]);
				$item['level'] = $this->getLevel($rs[0]['jifen']);
				$item['sex'] = isset($rs[0]['sex'])?$rs[0]['sex']:0;
				if(preg_match("/^1[0-9]{10}$/",$item['username'])){
					$item['username'] = substr($item['username'],0,4).'****';
				}
				$item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
				$item['pageview'] = intval($item['views']);

				/*if(is_null($item['doctor']) || empty($item['doctor'])){
					$item['doctor'] = '';
					}

					if(is_null($item['hospital']) || empty($item['hospital'])){
					$item['hospital'] = '';
					}*/

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

				$result['data'][] = $item;
				$i++;
			}
		}

		echo json_encode($result);
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

	private function topicList() {

		$type = mysql_real_escape_string($this->input->get('type'));
		$pid = $this->getChild($type);
		$tmpitem = array();
		if($pid){
			$sqlItem = "select name from items where pid = '{$pid}'";
			$citems = $this->db->query($sqlItem)->result_array();
			$typeSql = '';

			if(!empty($citems)){
				foreach($citems as $item){
					$typeSql .= " w.dataType like '%".$item['name']."%' OR w.tags like '%".$item['name']."%' OR";
					$tmpitem[] = $item['name'];
				}
			}
		}

		$sqltmp = substr($typeSql,0,strlen($typeSql)-2);

		$sql = "SELECT u.jifen,u.age,u.city, u.daren,w.group_start,w.group_end,w.comments,w.content,w.newtime,w.pageview,w.hot,w.hot_start,w.hot_end,w.top,w.top_start,w.top_end,w.chosen,w.chosen_start,w.chosen_end,w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
		$sql .= ' FROM wen_weibo as w LEFT JOIN users as u ON w.uid=u.id';

		$ctime = time(); //set publish time
		if ($this->input->get('uid')) {
			if ($sqltmp || $type) {
				if($sqltmp){
					$sql .= ' WHERE w.uid = ' . $this->input->get('uid') . " AND w.type=1 AND (".$sqltmp." and (w.dataType like '%".$type."%' OR w.tags like '%".$type."%'))";
				}else{
					$sql .= ' WHERE w.uid = ' . $this->input->get('uid') . " AND w.type=1 AND (w.dataType like '%".$type."%' OR w.tags like '%".$type."%')";
				}
			} else {
				$sql .= ' WHERE w.type&25 AND w.uid = ' . $this->input->get('uid');
			}
		} else {
			if ($sqltmp || $type) {
				if($sqltmp){
					$sql .= " WHERE w.type&25 AND (".$sqltmp." and (w.dataType like '%".$type."%' OR w.tags like '%".$type."%'))";
				}else{
					$sql .= " WHERE w.type&25 AND (w.dataType like '%".$type."%' OR w.tags like '%".$type."%')";
				}
			} else {
				$sql .= " WHERE w.type&25 ";
			}
		}
		$sql .=" and w.uid != 0 ";

		$sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.newtime DESC ";

		if($this->input->get('limit')){
			$limit = $this->input->get('limit');
		}else{
			$limit = 3;
		}
		if ($this->input->get('page')) {
			$start = ($this->input->get('page') - 1) * 3;
			$sql .= " LIMIT $start,$limit ";
		} else {
			$sql .= " LIMIT 0,$limit ";
		}
		$tmp = $this->db->query($sql)->result_array();


		//$totalCount = $this->getMyTopicCount($this->input->get('uid'));

		$res = array ();
		if (!empty ($tmp)) {
			foreach ($tmp as $row) {

				if($row['top_start'] <= time() && $row['top_end'] >= time()){
					$row['top'] = 1;
				}else{
					$row['top'] = 0;
				}
				$row['pageview'] = intval($row['pageview']);
				$row['age'] = $this->getAge(intval($row['age']));
				$row['city'] = empty($row['city'])?$row['city']:'';
				if($row['chosen_start'] <= time() && $row['chosen_end'] >= time()){
					$row['chosen'] = 1;
				}else{
					$row['chosen'] = 0;
				}
				$row['istopic'] = 1;
				if($row['hot_start'] <= time() && $row['hot_end'] >= time()){
					$row['hot'] = 1;
				}else{
					$row['hot'] = 0;
				}
				if($row['group_start'] <= time() && $row['group_end'] >= time()){
					$row['top'] = 1;
				}else{
					$row['top'] = 0;
				}

				$rs = $this->Diary_model->get_user_by_username($row['uid']);
				$item ['basicinfo'] = $this->getBasicInfo($rs[0]);

				$info = unserialize($row['type_data']);
				isset ($info['title']) && $row['content'] = $info['title'];
				unset ($row['type_data']);
				$row['title'] = $info['title'];
				$row['thumb'] = $this->profilepic($row['uid'], 2);
				$row['zanNum'] = ($this->getZan($row['weibo_id']) > 0) ? $this->getZan($row['weibo_id']) : 0;
				if(intval($this->uid) > 0) {
					$iszan = $this->isAtZan($this->uid, $row['weibo_id']);
					if ($iszan) {
						$row['isZan'] = 1;
					} else {
						$row['isZan'] = 0;
					}
				}else{
					$row['isZan'] = 0;
				}
				$row['level'] = $this->getLevel($row['jifen']);
				$row['content'] = $row['content'];
				$row['hasnew'] = $row['commentnums'];
				if(!empty($row['tags'])){
					$row['tag'] = explode(',',$row['tags']);

					foreach($row['tag'] as $item){
						if(!empty($item)){
							$arr = array();
							$arr['tag'] = $item;
							$itemid = $this->Diary_model->getItemId($item);
							$arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
							$arr['tagid'] = $itemid;
							$row['tagss'][] = $arr;
							$row['tag1'][] = $item;
						}
					}
				}
				$row['uname'] = $row['alias']?$row['alias']:'';
				if(empty($row['tagss'])){
					$row['tagss'] = array();
				}
				$row['tags'] = $row['tag1'];

				if(empty($row['tags'])){
					$row['tags'] = array();
				}
				unset($row['tag']);
				//$row['tags'] = explode(',',$row['tags']);
				$rs = $this->Diary_model->get_user_by_username($row['uid']);
				$row['username'] = !empty($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$row['username'] = !empty($row['username'])?$row['username']:'';
				if(preg_match("/^1[0-9]{10}$/",$row['username'])){
					$row['username'] = substr($row['username'],0,4).'****';
				}
				$row['showname'] = $row['username'];
				//$row['showname'] = $this->GName($row['alias'],$row['phone']);
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
		//$result['topicTotal'] = $this->Diary_model->getTopicCount($type)?$this->Diary_model->getTopicCount($type):0;
		//$result['diaryTotal'] = $this->Diary_model->getDiaryTotal($type)?$this->Diary_model->getDiaryTotal($type):0;
		//$s = $this->GroupTopicListTop();

		//$r = array_merge($s['data'],$result['data']);
		//$result['data'] = array();
		//$result['data'] = $r;
		//echo json_encode($result);
		return $res;
	}


	//get points data
	private function Gpoint($picid){
		$this->db->where('pic_id',$picid);
		return $this->db->get('topic_pics_extra')->result_array();
	}
	/**  发起的话题
	 * @param string $param,9=>Q+add topic
	 */
	private function getChild($type = 0){
		$tmp = array();
		$data = array();

		$sqlItem = "select id from items where name like '%".$type."%'";
		$citems = $this->db->query($sqlItem)->result_array();

		return $citems[0]['id'];
	}
	public function getItemDiaryList($pageSize=9) {
		//error_reporting(E_ALL);
		//ini_set('display_errors', 'On');

		$result = array();
		$result['state'] = '000';
		$result['data'] = array();
		$pageSize=9;
		$page = $this->input->get('page')?$this->input->get('page'):1;
		$offset = ($page - 1) * $pageSize;
		$tmp = $this->Diary_model->getDiaryFrontList($offset,2, $pageSize);
		$item_name = $this->input->get('item_name');
		$doctor = $this->input->get('doctor');
		$yiyuan = $this->input->get('yiyuan');
		//$uid = $this->input->get('uid');

		$tmp = $this->Diary_model->getDiaryNodeList($item_name,$doctor,$yiyuan, $offset,$pageSize);

		$result['data2'] = $this->topicList();
		$k = 0;
		if(!empty($tmp)){
			$n = 1;
			foreach($tmp as $item){
				if(empty($item['imgfile'])) {
					$item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
				}else{
					$item['imgurl'] = $this->remote->getQiniuImage($item['imgfile']);
				}
				$rs = $this->Diary_model->get_user_by_username($item['uid']);
				$item['username'] = isset($rs[0]['alias']) ? $rs[0]['alias'] : $rs[0]['username'];
				$item['username'] = isset($item['username'])?$item['username']:'';
				$item ['basicinfo'] = $this->getBasicInfo($rs[0]);
				$item['level'] = $this->getLevel($rs[0]['jifen']);
				$item['sex'] = isset($rs[0]['sex'])?$rs[0]['sex']:0;
				if(preg_match("/^1[0-9]{10}$/",$item['username'])){
					$item['username'] = substr($item['username'],0,4).'****';
				}
				$item['city'] = isset($rs[0]['city'])?$rs[0]['city']:'';
				$item['pageview'] = intval($item['views']);
				$item['views'] = intval($item['views']);
				$item['zan'] = intval($item['zan']);
				/*if(is_null($item['doctor']) || empty($item['doctor'])){
				 $item['doctor'] = '';
				 }

				 if(is_null($item['hospital']) || empty($item['hospital'])){
				 $item['hospital'] = '';
				 }*/
				$item['istopic'] = 0;
				if(isset($rs[0]['age'])){
					$item['age'] = $this->getAge($item['uid']);
				}else{
					$item['age'] = '';
				}
				$item['thumb'] = $this->profilepic($item['uid']);
				$item['zanNum'] = ($this->Diary_model->getZan($item['nid'])>0)?intval($this->Diary_model->getZan($item['nid'])):0;
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
				$item['istopic'] =0; //美人计
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

				if($n%3 == 0){

					$result['data'][] = $result['data2'][$k];
					$k ++;
				}else{
					$result['data'][] = $item;
				}
				$n++;
			}
		}
		unset($result['data2']);
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
	/*
	 *
	 * @content 评论内容
	 * @uid 医生id
	 * @doctor 医生名字
	 * @hospital 医院评分
	 * @skilled 专业评分
	 * @satisfied 满意度评分
	 * @evalution_content 评论内容
	 */
	public function Evaluation($param = 0){

		$result['state'] = '000';
		$result['data'] = array();
		$uid = $this->input->post('uid');
		$doctor = $this->input->post('doctor');
		$hospital = $this->input->post('hospital');
		$skilled = $this->input->post('skilled');
		$satisfied = $this->input->post('satisfied');
		$evaluation_content = $this->input->post('evaluation_content');

		if($this->uid){
			$this->Diary_model->addEvalution(array('uid'=>$uid, 'doctor'=>$doctor, 'hospital'=>$hospital, 'skilled'=>$skilled, 'satisfied'=>$satisfied, 'evalution_content'=>$evaluation_content));
			$result['notice'] = '评论成功!';
		}else{
			$result['state'] = '012';
			$result['notice'] = '用户未登录!';
		}
		echo json_encode($result);
	}
}
?>
