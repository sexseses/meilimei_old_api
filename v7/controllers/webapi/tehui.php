<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api tehui => 团购 Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class tehui extends MY_Controller {
	private $notlogin = true, $uid = 0,$result=array();
	public function __construct() {
		parent :: __construct();
		$this->tehuiDB = $this->load->database('tehui', TRUE);
		session_start();
		//error_reporting(E_ALL);
		ini_set("display_errors","On");
 	    if ($this->wen_auth->is_logged_in()) {
 			$this->notlogin = false;
 			$this->uid = $this->wen_auth->get_user_id();
 		} else {
 			$this->notlogin = true;
 		}
 		//$this->load->model('auth');
 		$this->load->model('remote');
 		$this->load->model('Diary_model');
 		
		//$this->uid = 53604;
		
		$this->result['state'] = '000';
		//$this->result['notic'] = '数据错误';
		//$result['thumb'] = $this->profilepic($uid, 2);
	}
	
	/*
	 * 获取图片相关医院
	 * */
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
	
	private function ablum($abid, &$state = false)
	{
	    $sql = "SELECT savepath,id FROM c_photo WHERE userId = {$abid} AND isDel=0";
	    $tmp = $this->db->query($sql)->row_array();
	    $result = array();
	    foreach ($tmp as $r) {
	        $result[] = $this->remote->show(str_replace('upload/', '', $r['savepath']));
	    }
	    (!empty($tmp) && file_exists('../' . $tmp[0]['savepath'])) && $state = true;
	    return $result;
	}

	/**
	 * 获取特惠首页频道图
	 * @param $int type 首页banner类型 
	 * http://www.meilimei.com/v2/tehui/getIndexBanner?type=1
	 *
	 * */
	public function getIndexTopBanner(){
		$rs = $this->db->get('tehui_index_ad');
		$this->result['data'] = "";
	    $this->result['data'] = $rs->result_array();
        foreach ($this->result['data'] as &$v){
            $v['banner_pic'] = "http://pic.meilimei.com.cn/upload/".$v['banner_pic'];
        }
	    
	    echo json_encode($this->result);
	}
	
	/**
	 * 获取特惠首页banner
	 * @param $int type 首页banner类型
	 * http://www.meilimei.com/v2/tehui/getIndexBanner?type=1
	 *
	 * */
	public function getTopBanner(){
	    $falsh = $this->input->get('falsh');
	    $tehui = $this->input->get('tehui');
	    $city = $this->input->get('city');
	    $city = '@'.$city.'@';
	    
	    if($falsh == 1){
        	    $this->db->where('falsh_shop',$falsh);  
        	    $this->db->where('display','1');
	    }
	    if($tehui == 1){
        	    $this->db->where('tehui',$tehui);
        	    $this->db->where('display','1');	    
	    }
	    
	    $rs = $this->db->get('tehui_top_banner')->row_array();
	    $this->result['data'] = "";
	    if(in_array($city,unserialize($rs['dizhiid']))){
	        $this->result['data'] = $rs;
	       
	        
	        if($this->result['data']){
	            $this->result['data']['banner_pic'] = "http://pic.meilimei.com.cn/upload/".$this->result['data']['bimg'];
	        }else{
	            $this->result['data'] = "";
	        }
	        
	        //美人记
	        if($this->result['data']['meirenji'] != '' || $this->result['data']['meirenji'] != 0){
	            $this->result['data']['ziduan'] = 1;
	        }
	        //社区帖子
	        if($this->result['data']['teizi'] != ''  || $this->result['data']['teizi'] != 0){
	            $this->result['data']['ziduan'] = 2;
	        }
	        //特惠
	        if($this->result['data']['prodid'] != '' || $this->result['data']['prodid'] != 0){
	            $this->result['data']['ziduan'] = 3;
	        }
	        //闪购
	        if($this->result['data']['shangou'] != '' || $this->result['data']['shangou'] != 0){
	            $this->result['data']['ziduan'] = 4;
	        }
	        //http网页
	        if($this->result['data']['burl'] != '' || $this->result['data']['burl'] != 0){
	        	$this->result['data']['ziduan'] = 5;
	        }
	        
	    }

	   
	    
// 	    foreach ($this->result['data'] as &$v){
// 	        $v['banner_pic'] = "http://pic.meilimei.com.cn/upload/".$v['bimg'];
// 	    }

	       
	     
	    echo json_encode($this->result);
	}
	
	/**
	 * 获取特惠首页
	 * @param $int type 首页banner类型 
	 * http://www.meilimei.com/v2/tehui/getIndexBanner?type=1
	 *
	 * */
	public function getIndexBanner(){
	    //$type = $this->input->get('type');
	    //$this->db->where('type',$type);
	    $rs = $this->db->get('tehui_index_banner');
	    $this->result['data'] = "";
	    $this->result['data'] = $rs->result_array();
        foreach ($this->result['data'] as &$v){
            $v['banner_pic'] = "http://pic.meilimei.com.cn/upload/".$v['banner_pic'];
        }
	    
	    echo json_encode($this->result);
	}
    
		/**
	 * 获取特惠详情根据id
	 * @param $int t_id 首页特惠类型 
	 * http://www.meilimei.com/v2/tehui/getTehuiDetailById
	 *
	 * */
	public function getTehuiDetailById(){
	    $t_id = $this->input->get('t_id');
        $this->db->where('tehui_id', $t_id);
        $rs = $this->db->get('tehui_relation');
        $rs = $rs->result_array();
        
        $this->result['tehui'] = array();
        $this->result['data'] = array();
        
        
        if(count($rs)<=0){
            //$t_id
            $tehui_id = $t_id;
            $this->tehuiDB->where('team.id', $tehui_id);
            $this->tehuiDB->where('team.group_id', 1);
            $this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
            $this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
            $tmp = $this->tehuiDB->get('team')->row_array();

            if (!empty ($tmp)) {
                $this->result['data'] = $tmp;
                if (isset ($this->result['data']['longlat'])) {
                    $this->result['data']['haspartner'] = 1;
                    $this->result['data']['partner_score'] = intval(($this->result['data']['comment_good'] * 5 + $this->result['data']['comment_none'] * 3 + $this->result['data']['comment_bad'] * 1) / ($this->result['data']['comment_good'] + $this->result['data']['comment_none'] + $this->result['data']['comment_bad'] + 0.1));
                    $usercor = explode(',', $this->result['data']['longlat']);
                    $this->result['data']['distance'] = $this->getDistance($this->input->get('Lat'), $this->input->get('Lng'), $usercor[0], $usercor[1]);
                } else {
                    $this->result['data']['haspartner'] = 0;
                }
                
                $this->result['data']['items']=unserialize($this->result['data']['items']);
                $items_tmp = array();
                $i = 0;
                foreach ($this->result['data']['items'] as &$item){
                    $itemid = $this->Diary_model->getItemId($item);
                    $items_tmp[$i]['itemid'] = $itemid;
                    $items_tmp[$i]['itemlevel'] = $this->Diary_model->isItemLevel($itemid,1);
                    $items_tmp[$i]['name'] = $item;
                    $i++;
                }
                $this->result['data']['items'] = $items_tmp;
                
                $this->result['data']['txtDetail'] = mb_substr(strip_tags($this->result['data']['detail']),0,120);
                $this->result['data']['txtDetail'] ='<div style="font-size:14px;line-height:160%;color:#666666">'. $this->result['data']['txtDetail'].'</div>';
                $this->result['data']['detail'] = $this->gdetail($this->result['data']['detail'],$this->result['data']['title']);
                $this->result['data']['lastDays'] = $this->result['data']['end_time'] - time();
                
                 
                
            
                if ($this->result['data']['lastDays'] > 0) {
                    if ($this->result['data']['lastDays'] > 3600 * 24) {
                        $this->result['data']['lastDays'] = intval($this->result['data']['lastDays']/(3600 * 24)).'天';
                    } else {
                        $this->result['data']['lastDays'] = date('H时i分s秒', $this->result['data']['lastDays']);
                    }
                } else {
                    $this->result['data']['lastDays'] = '过期';
                }
            
                $images = array ();
                if ($this->result['data']['image'] != '') {
                    $images[] = $this->result['data']['image'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image'];
                }
                if ($this->result['data']['image1'] != '') {
                    $images[] = $this->result['data']['image1'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image1'];
                }
                if ($this->result['data']['image2'] != '') {
                    $images[] = $this->result['data']['image2'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image2'];
                }
                $this->result['data']['images'] = $images;
            
                $this->result['data']['expire_time'] = date('Y-m-d', $this->result['data']['expire_time']);
                //$pat = "/<(\/?)(script|i?frame|style|html|body|title|link|u|font|meta|\?|\%)([^>]*?)>/isU";
                $this->result['data']['notice'] = preg_replace('~<([a-z]+?)\s+?.*?>~i','<$1>',$this->result['data']['notice']);
                $this->result['data']['notice'] = '<div style="font-size:12px;line-height:160%;color:#666666"><b>有效期:</b><br>' . $this->result['data']['expire_time'] . '<br>' . $this->result['data']['notice'].'</div>';
                
                
                
               
            
                $this->tehuiDB->where('team_id', $tehui_id);
                $this->tehuiDB->where('comment_time > ', 0);
                $this->tehuiDB->from('order');
            
                $this->result['data']['teamScoreNum'] = $this->tehuiDB->count_all_results();
                $this->result['data']['teamScore'] = 0;
            
                if ($this->result['data']['teamScoreNum']) {
                    $sql = "SELECT sum(case when `comment_grade` = 'good' Then 5 when `comment_grade` = 'none' then 3 else 1 end ) as v FROM `order` WHERE `team_id` = {$tehui_id} and `comment_time` >0";
                    $tmp = $this->tehuiDB->query($sql)->result_array();
            
                    $this->result['data']['teamScore'] = round($tmp[0]['v'] / $this->result['data']['teamScoreNum'], 1);
                }
            
                $this->result['data']['buynums'] = $this->result['data']['now_number'];
            
            }
        }else{
            //有关联机构和医师的数据

            foreach($rs as &$r){
                $m_id  = $r['mechanism'];
                $r['items'] = unserialize($r['items']);
                $this->db->where('id',$m_id);
                $company = $this->db->get('company')->result_array();
                //$this->db->where('id',$company[false]['userid']);
                //$com_user = $this->db->get('user')->row_array();
                
                if($company){
                    $r['mechanism'] = $company;
                    //$r['mechanism'][false]['grade'] = $com_user['grade'];
                    $r['mechanism'][false]['thumb'] = $this->profilepic($m_id, 2);
                    $abc = false;
                    $r['mechanism'][false]['ablum'] = $this->ablum($m_id, $abc);
                    
                }else{
                    $r['mechanism'] = array();
                }
                
                
                $user_id_arr = unserialize($r['physician']);
                $this->db->where_in('user_id',$user_id_arr);
            
                $r['physician'] = array();
                $physician = $this->db->get('user_profile')->result_array();
                if($physician){
                    $r['physician'] = $physician;
                    
                   
                    
                    foreach ($r['physician'] as &$v){
                        $v['thumb'] = $this->profilepic($v['user_id'],2);
                        $this->db->where('id',$v['user_id']);
                        $tmp_user = $this->db->get('users')->result_array();
                        $v['username'] = $tmp_user[false]['alias'] ?$tmp_user[false]['alias'] : $tmp_user[false]['username'];
                        if(preg_match("/^1[0-9]{10}$/",$v['username'])){
                            $v['username'] = substr($v['username'],0,4).'****';
                        }
                        $v['alias'] = $tmp_user[false]['alias'];
                        $v['grade'] = $tmp_user[false]['grade'];
                        $v['position'] = trim($v['position']);
                        $v['position'] = str_replace("&nbsp;"," ",$v['position']);
                        $v['casenum'] = rand(50,1200);
                    }
                }
                
                
                $note_id = unserialize($r['relation_note']);
                $this->result['note'] = array();
                
                if($note_id){
                    foreach ($note_id as $id){
                        $note_rs= $this->Diary_model->getDiaryDetail($id);
                        if(!empty($note_rs)){
                            $note_rs[false]['thumb_url'] = $this->remote->thumb($note_rs[false]['uid'], '36');
                            $note_rs[false]['imgurl_url'] =$this->remote->show320($note_rs[false]['imgurl']);
                            $userInfo = $this->Diary_model->get_user_by_username($note_rs[false]['uid']);
                            $note_rs[false]['myname'] = $userInfo[false]['username']?$userInfo[false]['username']:$userInfo[false]['alias'];
                            $this->result['note'][] = $note_rs[false];
                        }
                    }
                }
                
                
                $product_id = unserialize($r['relation_product']);
                
                $this->result['product_data'] = array();
                
                
                if($product_id){
                    $this->db->where_in('id',$product_id);
                    $r['r_product'] = $this->db->get('tehui_relation')->result_array();

                    if($r['r_product']){
                        foreach($r['r_product'] as $rpv){
                            $tehui_id = $rpv['tehui_id'];
                            $this->tehuiDB->where('team.id', $tehui_id);
                            //$this->tehuiDB->where('team.group_id', 1);
                            //$this->tehuiDB->or_where('team.group_id', 77);
                            $this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
                            $this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
                            $r_product_tmp = $this->tehuiDB->get('team')->row_array();
                            $r_product_tmp['imgurl'] = 'http://tehui.meilimei.com/static/' . $r_product_tmp['image'];
                    
                             
                            if (!empty($r_product_tmp)) {
                                $this->result['product_data'][] = $r_product_tmp;
                            }
                        }
                    }
                }
                
            
                 
                $tehui_id = $r['tehui_id'];
                $this->tehuiDB->where('team.id', $tehui_id);
                //$this->tehuiDB->where('team.group_id', 1);
                //$this->tehuiDB->or_where('team.group_id', 77);
                $this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
                $this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
                $tmp = $this->tehuiDB->get('team')->row_array();
//echo $this->tehuiDB->last_query();die;
                $this->result['data'] = array();
                $this->result['tehui'] = array();
            
                if (!empty ($tmp)) {
                    $this->result['data'] = $tmp;
                    if (isset ($this->result['data']['longlat'])) {
                        $this->result['data']['haspartner'] = 1;
                        $this->result['data']['partner_score'] = intval(($this->result['data']['comment_good'] * 5 + $this->result['data']['comment_none'] * 3 + $this->result['data']['comment_bad'] * 1) / ($this->result['data']['comment_good'] + $this->result['data']['comment_none'] + $this->result['data']['comment_bad'] + 0.1));
                        $usercor = explode(',', $this->result['data']['longlat']);
                        $this->result['data']['distance'] = $this->getDistance($this->input->get('Lat'), $this->input->get('Lng'), $usercor[0], $usercor[1]);
                    } else {
                        $this->result['data']['haspartner'] = 0;
                    }
                    //print_r($this->result['data']);die;
                    $this->result['data']['items'] =  unserialize($this->result['data']['items']);
                    
                    $items_tmp = array();
                    $i = 0;
                    foreach ($this->result['data']['items'] as &$item){
                        $itemid = $this->Diary_model->getItemId($item);
                        $items_tmp[$i]['itemid'] = $itemid;
                        $items_tmp[$i]['itemlevel'] = $this->Diary_model->isItemLevel($itemid,1);
                        $items_tmp[$i]['name'] = $item;
                        $i++;
                    }

                    $this->result['data']['items'] = $items_tmp;
                   
                    $this->result['data']['txtDetail'] = mb_substr(strip_tags($this->result['data']['detail']),0,120);
                    $this->result['data']['txtDetail'] ='<div style="font-size:14px;line-height:160%;color:#666666">'. $this->result['data']['txtDetail'].'</div>';
                    $this->result['data']['detail'] = $this->gdetail($this->result['data']['detail'],$this->result['data']['title']);
                    $this->result['data']['lastDays'] = $this->result['data']['end_time'] - time();
            
                    if ($this->result['data']['lastDays'] > 0) {
                        if ($this->result['data']['lastDays'] > 3600 * 24) {
                            $this->result['data']['lastDays'] = intval($this->result['data']['lastDays']/(3600 * 24)).'天';
                        } else {
                            $this->result['data']['lastDays'] = date('H时i分s秒', $this->result['data']['lastDays']);
                        }
                    } else {
                        $this->result['data']['lastDays'] = '过期';
                    }
            
                    $images = array ();
                    if ($this->result['data']['image'] != '') {
                        $images[] = $this->result['data']['image'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image'];
                    }
                    if ($this->result['data']['image1'] != '') {
                        $images[] = $this->result['data']['image1'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image1'];
                    }
                    if ($this->result['data']['image2'] != '') {
                        $images[] = $this->result['data']['image2'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image2'];
                    }
                    $this->result['data']['images'] = $images;
            
                    $this->result['data']['expire_time'] = date('Y-m-d', $this->result['data']['expire_time']);
                    $this->result['data']['notice'] = preg_replace('~<([a-z]+?)\s+?.*?>~i','<$1>',$this->result['data']['notice']);
                    $this->result['data']['notice'] = '<div style="font-size:14px;line-height:160%;color:#666666"><b>有效期:</b><br>' . $this->result['data']['expire_time'] . '<br>' . $this->result['data']['notice'].'</div>';
                     
            
                    $this->tehuiDB->where('team_id', $tehui_id);
                    $this->tehuiDB->where('comment_time > ', 0);
                    $this->tehuiDB->from('order');
            
                    $this->result['data']['teamScoreNum'] = $this->tehuiDB->count_all_results();
                    $this->result['data']['teamScore'] = 0;
            
                    if ($this->result['data']['teamScoreNum']) {
                        $sql = "SELECT sum(case when `comment_grade` = 'good' Then 5 when `comment_grade` = 'none' then 3 else 1 end ) as v FROM `order` WHERE `team_id` = {$tehui_id} and `comment_time` >0";
                        $tmp = $this->tehuiDB->query($sql)->result_array();
            
                        $this->result['data']['teamScore'] = round($tmp[0]['v'] / $this->result['data']['teamScoreNum'], 1);
                    }
            
                    $this->result['data']['buynums'] = $this->result['data']['now_number'];
            
                }
            }
            if($rs){
                $this->result['tehui'] = $rs[false];
            }
        }

        echo json_encode($this->result);
	}
    
	/**
	 * 获取用户所有的优惠券
	 * @param int page
	 * http://www.meilimei.com/v2/tehui/getUserCoupons?page=1&state=1
	 *
	 * */
	public function getUserCoupons($param = '') {
	    $result['state'] = '000';
	    $result['ustate'] = '000';
 
	    if ($this->uid) {
	        if ($this->input->get('state')) {
	            switch ($this->input->get('state')) {
	                case '1' :
	                    $this->tehuiDB->where('coupon.consume', 'N');
	                    break;
	                case '2' :
	                    $this->tehuiDB->where('coupon.consume', 'Y');
	                    break;
	                default :
	                    $time = time();
					    $this->tehuiDB->where('coupon.expire_time < ', $time);
	                    break;
	            }
	        }
	        
	        $page = $this->input->get('page');
	        
	        if ($page) {
	            $start = ($page -1) * 10;
	            $this->tehuiDB->limit(10, $start);
	        }
	        $this->tehuiDB->where('coupon.user_id', $this->uid);
	        $this->tehuiDB->order_by('coupon.id', 'DESC');
	        $this->tehuiDB->join('team', 'coupon.team_id = team.id');
	        $this->tehuiDB->join('order', 'order.id = coupon.order_id');
	        $this->tehuiDB->select(' team.team_price,team.image,team.title,team.summary,team.now_number,team.id,order.quantity,order.id');
	        
	        
	        //$this->tehuiDB->select('order.id,order.express,order.express_no,order.comment_time,order.quantity, order.state,team.team_price, team.image,team.title,team.summary,order.create_time');
	        //$this->tehuiDB->join('order', 'order.team_id = team.id');
	        
	        $tmp = $this->tehuiDB->get('coupon')->result_array();
            //echo $this->tehuiDB->last_query();
	        $result['data'] = array ();
	        foreach ($tmp as $r) {
	            //$r['hasComment'] = $r['consume_time'] > 0 ? 'Y' : 'N';
	            $r['image'] && $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
	            $r['create_time'] = date('Y年m月d日', $r['create_time']);
	            $result['data'][] = $r;
	        }
	    } else {
	        $result['ustate'] = '001';
	        $result['notice'] = '参数不全！';
	        $result['state'] = '012';
	    }
	
	    echo json_encode($result);
	}
	
	/**
	 *  tmp 
	 * 
	 * */
	public function getUserCouponstmp(){
		$this->uid = $this->input->get('uid');
	    if ($this->uid) {
	        $start = intval($this->input->get('page') - 1) * 10;
	        $this->tehuiDB->limit(10, $start);
	        $this->tehuiDB->select('team.title,team.id as team_id,coupon.id as coupon_id,coupon.expire_time,coupon.consume_time');
	        $this->tehuiDB->where('coupon.user_id', $this->uid);
	        switch ($this->input->get('state')) {
				case 1 :
					$time = time();
					$this->tehuiDB->where('coupon.consume', 'N');
					$this->tehuiDB->where('coupon.expire_time > ', $time);
					break;
				case 2 :
					$time = time();
					$this->tehuiDB->where('coupon.consume', 'Y');
					break;
				case 3 :
				    $time = time();
					$this->tehuiDB->where('coupon.expire_time < ', $time);
					break;
				default :
				    $this->tehuiDB->where('coupon.consume', 'Y');
					$this->tehuiDB->where('order.comment_time', null);
					break;
			}
	        $this->tehuiDB->join('team', 'team.id = coupon.team_id');
	        $res = $this->tehuiDB->get('coupon')->result_array();

            $this->result['data'] = array();

	        if($res){
	            foreach ($res as $r) {
	                $lasttime = $r['expire_time'] - time();
	                if ($lasttime <= 0) {
	                    $r['last_day'] = 0;
	                } else {
	                    $r['last_day'] = date('d', $lasttime);
	                }
	                 
	                $r['expire_time'] = date('Y年m月d日', $r['expire_time']);
	                $this->result['state'] = '100';
	                $this->result['data'][] = $r;
	            }
	        }
	    } else {
	        $this->result['notice'] = '账户未登入！';
	        $this->result['ustate'] = '001';
	    }
	    echo json_encode($this->result);
	}
	
	/**
	 * 获取用户所有的优惠券（美丽券）详细信息
	 * @param int page
	 * http://www.meilimei.com/v2/tehui/getUserCouponDetailInfo?id=?&sn=?&uid=?
	 * */
	public function getUserCouponDetailInfo(){
	    $id = $this->input->get('id');
	    $sn = $this->input->get('sn');

	    
	    if ($this->uid AND ($id OR $sn)) {
	        $this->tehuiDB->select('coupon.id,order.comment_grade,order.comment_time,coupon.secret,coupon.consume,coupon.expire_time,coupon.consume_time,team.title,team.id as tid,team.image,team.summary,order.id as sn,team.outdatefun,team.allowrefund,order.mobile,order.origin,order.pay_time,order.quantity,team.team_price,order.card');
	        if($this->input->get('sn')){
	            $this->tehuiDB->where('order.id', $sn);
	        }else{
	            $this->tehuiDB->where('coupon.id', $id);
	            
	        }

	        $this->tehuiDB->where('coupon.user_id', $this->uid);
	        $this->tehuiDB->join('team', 'team.id = coupon.team_id');
	        $this->tehuiDB->join('order', 'order.id = coupon.order_id');
	        $this->tehuiDB->order_by("coupon.id", "desc");
	        $tmp = $this->tehuiDB->get('coupon')->result_array();
	        $expire_time = $tmp[0]['expire_time'];
            
	        //echo $this->tehuiDB->last_query();
	        
	        $tmp[0]['pay_time'] = date('Y/m/d', $tmp[0]['pay_time']);
	        $tmp[0]['expire_time'] = date('Y/m/d', $tmp[0]['expire_time']);
	        $tmp[0]['consume_time'] = date('Y/m/d', $tmp[0]['consume_time']);
	        $tmp[0]['image'] = 'http://tehui.meilimei.com/static/' . $tmp[0]['image'];

	        if ($tmp[0]['comment_time']) {
	            switch ($tmp[0]['comment_grade']) {
	                case 'good':
	                    $tmp[0]['comment_grade'] = 5;
	                    break;
	                case 'none':
	                    $tmp[0]['comment_grade'] = 3;
	                    break;
	                case 'bad':
	                    $tmp[0]['comment_grade'] = 1;
	                    break;
	                default:
	                    $tmp[0]['comment_grade'] = 0;
	                    break;
	            }
	        } else {
	            $tmp[0]['comment_grade'] = 0;
	        }

	        $result['data'] = array();

	        $result['data'] = $tmp[0];
	        $result['notice'] = 'success';
	        if ($result['data']['consume'] == 'Y') {
	            $result['data']['state'] = 1; //'已消费';
	        } else {
	            if ($expire_time < time()) {
	                $result['data']['state'] = 2; //'已过期';
	            } else {
	                $result['data']['state'] = 3; //'未使用';
	            }
	        }
	    } else {
	        $result['notice'] = '账户未登入！';
	        $result['ustate'] = '001';
	    }
	    echo json_encode($result);
	}

	/**
	 * 获得用户订单列表
	 * @param int page
	 * @param string state
	 * http://www.meilimei.com/v2/tehui/getUserOrderList?page=1&state=pay/compay/unpay
	 */
	public function getUserOrderList() {
	    $result['ustate'] = '000';
	    $page = 1;

	    if ($this->uid) { 
	        if ($this->input->get('state')) {
	            switch ($this->input->get('state')) {
	                case 'pay' :
	                    $this->tehuiDB->where('order.state', 'pay');
	                    break;
	                case 'compay' :
	                    $this->tehuiDB->where('order.state', 'compay');
	                    //$this->tehuiDB->where('order.comment_time', null);
	                    break;
	                default :
	                    $this->tehuiDB->where('order.state', 'unpay');
	                    break;
	            }
	        }
	        
	        $this->page = $this->input->get('page');
	        
	        if ($this->page) {
	            $start = ($this->page -1) * 10;
	            $this->tehuiDB->limit(10, $start);
	        }
	        
	        $this->tehuiDB->where('order.user_id', $this->uid);
	        $this->tehuiDB->order_by('order.id', 'DESC');
	        $this->tehuiDB->join('team', 'order.team_id = team.id');
	        $this->tehuiDB->select('order.id,order.express,order.express_no,order.comment_time,order.quantity, order.state,team.team_price, team.image,team.title,team.summary,order.create_time,team.delivery');
	        $tmp = $this->tehuiDB->get('order')->result_array();
	        //echo $this->tehuiDB->last_query();die;
	        $this->result['data'] = array ();
	        foreach ($tmp as $r) {
	            $r['hasComment'] = $r['comment_time'] > 0 ? 'Y' : 'N';
	            $r['image'] && $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
	            $r['create_time'] = date('Y年m月d日', $r['create_time']);
	            $this->result['data'][] = $r;
	        }
	    } else {
	        $this->result['ustate'] = '001';
	        $this->result['notice'] = '参数不全！';
	        $this->result['state'] = '012';
	    }
	    //$this->result['sql'] =  $this->tehuiDB->last_query();

	    echo json_encode($this->result);
	}
	
	
	/*
	 * 获得用户订单（非美丽券）详细信息
	 * @param int page
	 * @param string state
	 * http://www.meilimei.com/v2/tehui/getUserOrderDetailInfo?id=id_num
	 * */
	public function getUserOrderDetailInfo(){
	    $result['ustate'] = '000';
	    // and ($id = $this->input->get('id'))
	    
	    $id = $this->input->get('id');

	    //($uid = $this->uid) OR $uid = $this->input->get('uid')
// 	    $uid = $this->input->get('uid');
// 	    $this->uid = $uid;
	    if ($this->uid) {
	        $this->tehuiDB->where('order.user_id',$this->uid);
	        $this->tehuiDB->where('order.id', $id);
	        $this->tehuiDB->order_by('order.id', 'DESC');
	        $this->tehuiDB->join('team', 'order.team_id = team.id');
	        $this->tehuiDB->select('team.id as tid,order.id,order.express,order.express_no,order.comment_time,order.quantity, order.state,team.team_price,team.image,team.title,team.summary,order.create_time,order.card,order.pay_time');
	        //$this->tehuiDB->select('order.*,team.*');
	        $tmp = $this->tehuiDB->get('order')->result_array();
	         
	        //echo $this->tehuiDB->last_query();die;
	        
	        $this->result['data'] = array ();
	        foreach ($tmp as $r) {
	            //$r['hasComment'] = $r['comment_time'] > 0 ? 'Y' : 'N';
	            $r['image'] && $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
	            $r['create_time'] = date('Y年m月d日', $r['create_time']);
	            $this->result['data'][] = $r;
	        }
	    } else {
	        $this->result['ustate'] = '001';
	        $this->result['notice'] = '参数不全！';
	        $this->result['state'] = '012';
	    }
	    echo json_encode($this->result);
	}
	
	
	
	/*
	 * 获得用户代金券
	 * @param int page
	 * @param string state
	 * http://www.meilimei.com/v2/tehui/getUserVoucher?voucher=""
	 * */
	public function getUserVoucher($param = '') {
	    $result['state'] = '000';
	    $result['ustate'] = '000';
	    
	    $this->uid = $this->input->get('uid');
	    
	    if ($this->uid) {
	        $voucher = trim($this->input->get('voucher'));
	        if ($voucher) {
	            $this->tehuiDB->where('id', $voucher);
	            //$this->tehuiDB->select('id, end_time, begin_time,consume,credit');
	            $tmp = $this->tehuiDB->get('card')->result_array();
	            //$result['sql'] = $this->tehuiDB->last_query();
	            $result['data'] = array();
	            if (!empty ($tmp)) {
	                $result['data'] = $tmp[0];
	                if ($result['data']['consume'] == 'Y') {
	                    $result['state'] = '400';
	                    $result['notice'] = '代金券已被使用过！';
	                }
	                elseif ($result['data']['begin_time'] > time() or $result['data']['end_time'] < time()) {
	                    $result['state'] = '400';
	                    $result['notice'] = '代金券已过期！';
	                } else {
	                    $result['notice'] = '代金券可使用！';
	                }
	            } else {
	                $result['state'] = '400';
	                $result['notice'] = '代金券不存在！';
	                $result['data'] = array ();
	            }
	        }
	    } else {
	        $result['ustate'] = '001';
	        $result['notice'] = '参数不全！';
	        $result['state'] = '012';
	    }
	    echo json_encode($result);
	}
	
	
	
	/*
	 * 获得特惠的列表
	 * @param int page
	 * @param string state
	 * http://www.meilimei.com/v2/tehui/getTehuiList?page&tag_id&city_ids&reser_price&order_num&sort_order"
	 * @return  delivery = coupon/express (coupon优惠券/express实物)
	 * */
	public function getTehuiList($param = '') {
	    $result['state'] = '000';
	    
	    $page = intval($this->input->get('page'));
	    $tag_id = $this->input->get('tag_id');
	    $city_ids = $this->input->get('city_ids');
	    $endselect = $this->input->get('endselect');

	    if ($page) {
	        $time = time();
	        $start = ($page -1) * 10;
	        $fields = 't.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';
	        $condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}' and flashSale = 0";
	        if($city_ids == "全部地区"){
	            $city_ids = 0;
	        }
	        
	        if ($city_ids) {
	            $city_id = intval($this->input->get('city_ids'));
	            if ($city_id) {
	                $condition .= " AND ((t.city_ids like '%@{$city_id}@%' or t.city_ids like '%@0@%') or t.city_id in(0,{$city_id})  OR t.areatype=1)";
	            } else {
	                $city_id = trim($this->input->get('city_ids'));
	                $tmp = $this->tehuiDB->query("SELECT id FROM category WHERE name = '{$city_id}'")->result_array();
	
	                if (!empty ($tmp)) {
	                    $city_id = $tmp[0]['id'];
	                    $condition .= " AND ((t.city_ids like '%@{$city_id}@%' or t.city_ids like '%@0@%') or t.city_id in(0,{$city_id}) OR t.areatype=1)";
	                } else {
	                    $condition .= " AND t.areatype=1 ";
	                }
	                
	                
	            }
	        }

	         

	        if ($tag_id) {
	            $condition .= " AND (t.sub_id = {$tag_id} OR t.sub_ids like '%@{$tag_id}@%') ";
	        }
	        
	        $order = ' t.sort_order DESC ';
 
	        switch ($endselect){
	            case 1:
	                $order .= ' ,t.pre_number DESC ';
	                break;
	            case 2:
	                $order .= ' ,t.pre_number ASC ';
	                break;
	            case 3:
	                $order .= ' ,t.team_price DESC ';
	                break;
	            case 4:
	                $order .= ' ,t.team_price ASC ';
	        }
	    
	        	
 
// 	        $order .= ' t.begin_time DESC, t.id DESC';
	        $limit = "{$start},10";
	        $result['data'] = array ();
	        $tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} limit {$limit} ")->result_array();
	        
	        $randpic = date('Ymdhi',time());
	        foreach ($tmpinfo as $r) {
	            $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
	            $tehui_sql = "select * from tehui_relation where tehui_id = ?";
	            $tehui_rs = $this->db->query($tehui_sql,array($r['id']))->row_array();
	            //$r['tehui'] = $tehui_rs;
	            //$r['items'] = $tehui_rs['items'];
	            $m_id  = $tehui_rs['mechanism'];
	            $this->db->where('id',$m_id);
	            
	            $r['mechanism'] = "";
	            $company = $this->db->get('company')->row_array();
	            if($company){
	               $r['mechanism'] = $company;
	               $r['mechanism'] = $r['mechanism']['name'];
	            }

// 	            $r['order_num'] = "";
// 	            if($tehui_rs['num']){
// 	               $r['order_num'] = $tehui_rs['num'];
// 	            }
	            
	            
	            //session 唯一标示付
	            $sid = $r['id'];
	             
	            //print_r($_SESSION);
	         
	            if(!empty($_SESSION[$sid])){
	                $r['order_num'] = $_SESSION[$sid];
	            }else{
	                $_SESSION[$sid] = rand(66, 88);
	                $r['order_num'] = $_SESSION[$sid];
	            }
	            //print_r($_SESSION);
	            $r['case_num'] = rand(50,66);
	            
	            $r['reser_price'] = 0;
	            $r['deposit'] = 0;
	            
	            if($tehui_rs['reser_price']){
	                $r['reser_price'] = $tehui_rs['reser_price'];
	            }
	            
	            if($tehui_rs['deposit']){
	                $r['deposit'] = $tehui_rs['deposit'];
	            }
	            
	            $result['data'][] = $r;
	        }
	        

	        $result['notice'] = '成功获取！';
	    } else {
	        $result['notice'] = '参数不全！';
	        $result['state'] = '012';
	    }
	    echo json_encode($result);
	}
	
	
	/*
	 * 获得特惠的列表
	 * @param int page
	 * @param string state
	 * http://www.meilimei.com/v2/tehui/getTehuiList?page&tag_id&city_ids&reser_price&order_num&sort_order"
	 * @return  delivery = coupon/express (coupon优惠券/express实物)
	 * */
	public function getNewTehuiList($param = '') {
	    $result['state'] = '000';
	     
	    $page = intval($this->input->get('page'));
	    $tag_id = $this->input->get('tag_id');
	    $czone = $this->input->get('czone');
	    $endselect = $this->input->get('endselect');
	   
 
	    
	    
        
	    
	    
	    //print_r($city_id_tmp);die;
	    if ($page) {
	        $time = time();
	        $start = ($page -1) * 10;
	        $fields = 't.city_ids,t.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';
	        $condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}' and flashSale = 0 ";
	        
	        if($czone == '全部地区'){
	            //$condition .= " and areatype = 1";
	        }
	        
	        if($czone != '全部地区'){
	            $city_sql = "select id from category where czone = ? and zone = ?";
	            $city_id_rs = $this->tehuiDB->query($city_sql,array($czone,'city'))->result_array();
	            foreach ($city_id_rs as $vids){
	                $city_id_tmp[] = $vids['id'];
	            }
	            $city_id_string = implode($city_id_tmp,',');
	            $condition .= " and (city_id in ({$city_id_string}) or city_id = 0)";
	        }
	        
	        if ($tag_id) {
	            $condition .= " AND (t.sub_id = {$tag_id} OR t.sub_ids like '%@{$tag_id}@%') ";
	        }
	         
	        $order = ' t.sort_order DESC ';
	
	        switch ($endselect){
	            case 1:
	                $order .= ' ,t.pre_number DESC ';
	                break;
	            case 2:
	                $order .= ' ,t.pre_number ASC ';
	                break;
	            case 3:
	                $order .= ' ,t.team_price DESC ';
	                break;
	            case 4:
	                $order .= ' ,t.team_price ASC ';
	        }
	         
	
	
	        // 	        $order .= ' t.begin_time DESC, t.id DESC';
	        $limit = "{$start},10";
	        $result['data'] = array ();
	        $tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} limit {$limit} ")->result_array();
    
	        //echo $this->tehuiDB->last_query();
	        $randpic = date('Ymdhi',time());
	        foreach ($tmpinfo as $r) {
	            $r['city_ids'] = trim($r['city_ids'],'@');
	            $r['city_ids'] = explode('@', $r['city_ids']);

	            if(count($city_id_tmp)>0){
	                foreach ($city_id_tmp as $civ){
	                    if(in_array($civ, $r['city_ids'])){
	                        $tmpr = $r;
	                        $tmpr['image'] = 'http://tehui.meilimei.com/static/' . $tmpr['image'].'?'.$randpic;
	                        $tehui_sql = "select * from tehui_relation where tehui_id = ?";
	                        $tehui_rs = $this->db->query($tehui_sql,array($tmpr['id']))->row_array();
	                        
	                        $m_id  = $tehui_rs['mechanism'];
	                        $this->db->where('id',$m_id);
	                        $tmpr['mechanism'] = "";
	                        $company = $this->db->get('company')->row_array();
	                        if($company){
	                            $tmpr['mechanism'] = $company;
	                            $tmpr['mechanism'] = $tmpr['mechanism']['name'];
	                        }
	                        $sid = $tmpr['id'];
	                         
	                        //print_r($_SESSION);
	                         
	                        if(!empty($_SESSION[$sid])){
	                            $r['order_num'] = $_SESSION[$sid];
	                        }else{
	                            $_SESSION[$sid] = rand(66, 88);
	                            $tmpr['order_num'] = $_SESSION[$sid];
	                        }
	                        //print_r($_SESSION);
	                        $tmpr['case_num'] = rand(50,66);
	                         
	                        $tmpr['reser_price'] = 0;
	                        $tmpr['deposit'] = 0;
	                         
	                        if($tehui_rs['reser_price']){
	                            $tmpr['reser_price'] = $tehui_rs['reser_price'];
	                        }
	                         
	                        if($tehui_rs['deposit']){
	                            $tmpr['deposit'] = $tehui_rs['deposit'];
	                        }
	                         
	                        $result['data'][] = $tmpr;
	                    }
	                    
	                    
	                }
	            }else{
	                $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
	                $tehui_sql = "select * from tehui_relation where tehui_id = ?";
	                $tehui_rs = $this->db->query($tehui_sql,array($r['id']))->row_array();
	                 
	                $m_id  = $tehui_rs['mechanism'];
	                $this->db->where('id',$m_id);
	                $r['mechanism'] = "";
	                $company = $this->db->get('company')->row_array();
	                if($company){
	                    $r['mechanism'] = $company;
	                    $r['mechanism'] = $r['mechanism']['name'];
	                }
	                $sid = $r['id'];
	                
	                //print_r($_SESSION);
	                
	                if(!empty($_SESSION[$sid])){
	                    $r['order_num'] = $_SESSION[$sid];
	                }else{
	                    $_SESSION[$sid] = rand(66, 88);
	                    $r['order_num'] = $_SESSION[$sid];
	                }
	                //print_r($_SESSION);
	                $r['case_num'] = rand(50,66);
	                
	                $r['reser_price'] = 0;
	                $r['deposit'] = 0;
	                
	                if($tehui_rs['reser_price']){
	                    $r['reser_price'] = $tehui_rs['reser_price'];
	                }
	                
	                if($tehui_rs['deposit']){
	                    $r['deposit'] = $tehui_rs['deposit'];
	                }
	                $result['data'][] = $r;
	            }
	        }
	         
	
	        $result['notice'] = '成功获取！';
	    } else {
	        $result['notice'] = '参数不全！';
	        $result['state'] = '012';
	    }
	    echo json_encode($result);
	}
	
	
	
	public function getnewcate($param = '') {
	    $result['state'] = '000';
	    
	    $this->tehuiDB->select('name, id');
	    $this->tehuiDB->where('fid', 77);
	    //$this->tehuiDB->where('czone !=','');
	    $this->tehuiDB->order_by("sort_order", "desc");
	    $result['cates'] = $this->tehuiDB->get('category')->result_array();
	    
	    array_unshift($result['cates'],array (
	    'name' => '全部项目',
	    'id' => 0
	    ));
	    //$this->tehuiDB->select('name, id');
	    //$this->tehuiDB->where('zone', 'city');
	    
	    $citys_sql = "select DISTINCT (czone) from category where 1=1 and zone = ? order by sort_order DESC";
	    $result['czones'] = $this->tehuiDB->query($citys_sql,array('city'))->result_array();
	    array_unshift($result['czones'],array (
	    'czone' => '全部地区',
	    'id' => 0
	    ));
	    $result['notice'] = '成功获取！';
	    
	    $tehuisort = array(
	        array(
	            id => 0,
	            name => '默认排序'
	        ),
	        array(
	            id => 1,
	            name => '购买数高到低'
	        ),
	        array(
	            id => 2,
	            name => '购买数低到高'
	        ),
	        array(
	            id => 3,
	            name => '预约价高到低'
	        ),
	        array(
	            id => 4,
	            name => '预约价低到高'
	        )
	    );
	    
	    $result['sort'] = $tehuisort;
	    echo json_encode($result);
	}
	
	
	//get Super Sale category
	public function getwebcate($param = '') {
	     
	   $result['state'] = '000';
	    
	    $this->tehuiDB->select('name, id');
	    $this->tehuiDB->where('fid', 77);
	    //$this->tehuiDB->where('czone !=','');
	    $this->tehuiDB->order_by("sort_order", "desc");
	    $result['data']['cates'] = $this->tehuiDB->get('category')->result_array();
	    
	    array_unshift($result['data']['cates'],array (
	    'name' => '全部项目',
	    'id' => 0
	    ));
	    //$this->tehuiDB->select('name, id');
	    //$this->tehuiDB->where('zone', 'city');
	    
	    $citys_sql = "select DISTINCT (czone) from category where 1=1 and zone = ? order by sort_order DESC";
	    $result['data']['czones'] = $this->tehuiDB->query($citys_sql,array('city'))->result_array();
	    array_unshift($result['data']['czones'],array (
	    'czone' => '全部地区',
	    'id' => 0
	    ));
	    $result['data']['notice'] = '成功获取！';
	    
	    $tehuisort = array(
	        array(
	            id => 0,
	            name => '默认排序'
	        ),
	        array(
	            id => 1,
	            name => '购买数高到低'
	        ),
	        array(
	            id => 2,
	            name => '购买数低到高'
	        ),
	        array(
	            id => 3,
	            name => '预约价高到低'
	        ),
	        array(
	            id => 4,
	            name => '预约价低到高'
	        )
	    );
	    
	    $result['data']['sort'] = $tehuisort;
	    echo json_encode($result);
	}
	
	
	//get Super Sale category
	public function getcate($param = '') {
	    
		$result['state'] = '000';

		$this->tehuiDB->select('name, id');
		$this->tehuiDB->where('fid', 1);
		$this->tehuiDB->order_by("sort_order", "desc");
		$result['cates'] = $this->tehuiDB->get('category')->result_array();
		
		array_unshift($result['cates'],array (
			'name' => '全部项目',
			'id' => 0
		));
		//$this->tehuiDB->select('name, id');
		//$this->tehuiDB->where('zone', 'city');
		
		$citys_sql = "select id,name from category where 1=1 and zone = ? order by sort_order DESC";
		$result['citys'] = $this->tehuiDB->query($citys_sql,array('city'))->result_array();
		array_unshift($result['citys'],array (
        		'name' => '全部地区',
        		'id' => 0
		));
		$result['notice'] = '成功获取！';
		
		$tehuisort = array(
		    array(
		        id => 0,
		        name => '默认排序'
		    ),
		    array(  
		        id => 1,
		        name => '购买数高到低'
		    ),
		    array(
		        id => 2,
		        name => '购买数低到高'
		    ),
		    array(
		        id => 3,
		        name => '预约价高到低'
		    ),
		    array(
		        id => 4,
		        name => '预约价低到高'
		    )
		);

		$result['sort'] = $tehuisort;
		echo json_encode($result);
	}
	//get Super Sale lists
	public function getSales($param = '') {
		$result['state'] = '000';
		$page = intval($this->input->get('page'));

		if ($page) {
			$time = time();
			$start = ($page -1) * 10;
			$fields = 't.id,t.user_id,t.title,t.summary,t.image,t.team_price, t.now_number,t.market_price';
			$condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}'";
			if ($this->input->get('city_ids')) {
			    $city_id = intval($this->input->get('city_ids'));
				if ($city_id) {
					$condition .= " AND ((t.city_ids like '%@{$city_id}@%' or t.city_ids like '%@0@%') or t.city_id in(0,{$city_id})  OR t.areatype=1)";
				} else {
					$city_id = trim($this->input->get('city_ids'));
					$tmp = $this->tehuiDB->query("SELECT id FROM category WHERE name = '{$city_id}'")->result_array();

					if (!empty ($tmp)) {
						$city_id = $tmp[0]['id'];
						$condition .= " AND ((t.city_ids like '%@{$city_id}@%' or t.city_ids like '%@0@%') or t.city_id in(0,{$city_id}) OR t.areatype=1)";
					} else {
						$condition .= " AND t.areatype=1 ";
					}
				}
			}
            
			
			$tag_id = $this->input->get('tag_id');
			if ($tag_id) {
				$condition .= " AND (t.sub_id = {$tag_id} OR t.sub_ids like '%{$tag_id}%')";
			}
			
			$order = ' t.sort_order DESC,t.begin_time DESC, t.id DESC';
			$limit = "{$start},10";
			$result['data'] = array ();
			$tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} limit {$limit} ")->result_array();
            $randpic = date('Ymdhi',time());
			foreach ($tmpinfo as $r) {
				$r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
				$result['data'][] = $r;
			}
			$result['notice'] = '成功获取！';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//get suggest Sale lists
	public function getSugSales($param = '') {
		$result['state'] = '000';

		if ($this->input->get('tehui_ids')) {

		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//get Super Sale detail
	public function detail($param = '') {
		$result['state'] = '000';
		$id = intval($this->input->get('id'));
		if ($id) {
			$this->tehuiDB->where('team.id', $id);
			$this->tehuiDB->where('team.group_id', 1);
			$this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
			$this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
			$tmp = $this->tehuiDB->get('team')->result_array();

			if (!empty ($tmp)) {
				$result['data'] = $tmp[0];
				if (isset ($result['data']['longlat']) and $this->input->get('Lat')) {
					$result['data']['haspartner'] = 1;
					$result['data']['partner_score'] = intval(($result['data']['comment_good'] * 5 + $result['data']['comment_none'] * 3 + $result['data']['comment_bad'] * 1) / ($result['data']['comment_good'] + $result['data']['comment_none'] + $result['data']['comment_bad'] + 0.1));
					$usercor = explode(',', $result['data']['longlat']);
					$result['data']['distance'] = $this->getDistance($this->input->get('Lat'), $this->input->get('Lng'), $usercor[0], $usercor[1]);
				} else {
					$result['data']['haspartner'] = 0;
				}
				//$result['data']['team_price'] = number_format($result['data']['team_price']);
				//$result['data']['market_price'] = number_format($result['data']['market_price']);
				$result['data']['txtDetail'] = mb_substr(strip_tags($result['data']['detail']),0,120);
                $this->result['data']['txtDetail'] ='<div style="font-size:14px;line-height:160%;color:#666666">'. $this->result['data']['txtDetail'].'</div>';
			  	$result['data']['detail'] = $this->gdetail($result['data']['detail'],$result['data']['title']);
				$result['data']['lastDays'] = $result['data']['end_time'] - time();
				if ($result['data']['lastDays'] > 0) {
					if ($result['data']['lastDays'] > 3600 * 24) {
						$result['data']['lastDays'] = intval($result['data']['lastDays']/(3600 * 24)).'天';
					} else {
						$result['data']['lastDays'] = date('H时i分s秒', $result['data']['lastDays']);
					}
				} else {
					$result['data']['lastDays'] = '过期';
				}
				$images = array ();
				if ($result['data']['image'] != '') {
					$images[] = $result['data']['image'] = 'http://tehui.meilimei.com/static/' . $result['data']['image'];
				}
				if ($result['data']['image1'] != '') {
					$images[] = $result['data']['image1'] = 'http://tehui.meilimei.com/static/' . $result['data']['image1'];
				}
				if ($result['data']['image2'] != '') {
					$images[] = $result['data']['image2'] = 'http://tehui.meilimei.com/static/' . $result['data']['image2'];
				}
                $result['data']['images'] = $images;
				$result['data']['expire_time'] = date('Y-m-d', $result['data']['expire_time']);
				$result['data']['notice'] = '<div style="font-size:12px"><b>有效期:</b><br>' . $result['data']['expire_time'] . '<br>' . $result['data']['notice'].'</div>';
				$this->tehuiDB->where('team_id', $id);
				$this->tehuiDB->where('comment_time > ', 0);
				$this->tehuiDB->from('order');
				$result['data']['teamScoreNum'] = $this->tehuiDB->count_all_results();
				$result['data']['teamScore'] = 0;
				if ($result['data']['teamScoreNum']) {
					$sql = "SELECT sum(case when `comment_grade` = 'good' Then 5 when `comment_grade` = 'none' then 3 else 1 end ) as v FROM `order` WHERE `team_id` = {$id} and `comment_time` >0";
					$tmp = $this->tehuiDB->query($sql)->result_array();

					$result['data']['teamScore'] = round($tmp[0]['v'] / $result['data']['teamScoreNum'], 1);
				}

				$result['data']['buynums'] = $result['data']['now_number'];

			}
			$result['notice'] = '成功获取！';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
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

	//order product
	public function order($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		
		if (($quantity = $this->input->post('quantity')) and $id = intval($this->input->post('id'))) {
			if (!$this->uid) {
				$result['ustate'] = '001';
				$result['notice'] = '账户未登入！';
				echo json_encode($result);
				exit;
			}
			$this->tehuiDB->where('id', $id);
			$team = $this->tehuiDB->get('team')->result_array();
			if (!empty ($team)) {
				$team = $team[0];
			} else {
				$result['state'] = '001';
				$result['notice'] = '非法请求';
				echo json_encode($result);
				exit;
			}
			$result['title'] = $team['title'];
			$result['team_id'] = $team['id'];
			$express_id = $this->input->post('express_id');

			if ($team['delivery'] == 'express') {
				$express_ralate = unserialize($team['express_relate']);
				foreach ($express_ralate as $k => $v) {
					$exp_id[] = $v['id'];
					$ex[$v['id']]['price'] = $v['price'];
				}
				if (!in_array($express_id, $exp_id) && !empty ($exp_id)) {
					$result['notice'] = '非法请求';
					$result['state'] = '001';
					echo json_encode($result);
					exit;
				}
				$express_price = abs($ex[$express_id]['price']);
			}
			$condbuy = implode('@', $this->input->post('condbuy'));

			if ($quantity == 0) {
				$result['state'] = '001';
				$result['notice'] = '购买数量不能小于1份';
				echo json_encode($result);
				exit;
			}
			elseif ($team['per_number'] > 0 && $quantity > $team['per_number']) {
				$result['notice'] = '您本次购买本单产品已超出限额！';
				$result['state'] = '001';
				echo json_encode($result);
				exit;
			}

			$this->tehuiDB->where('user_id', $this->uid);
			$this->tehuiDB->where('team_id', $team['id']);
			$order = $this->tehuiDB->count_all_results('order');
			if ($order && $team['buyonce'] == 'Y') {
				$result['notice'] = '本团不能多次购买！';
				$result['state'] = '001';
				echo json_encode($result);
				exit;
			}
			$data = array ();
			$data['user_id'] = $this->uid;
			$data['state'] = 'unpay';
			$data['allowrefund'] = $team['allowrefund'];
			$data['team_id'] = $team['id'];
			$data['city_id'] = $team['city_id'];
			$data['express'] = ($team['delivery'] == 'express') ? 'Y' : 'N';
			$data['fare'] = $data['express'] == 'Y' ? $express_price : 0;
			$data['express_id'] = $data['express'] == 'Y' ? $express_id : 0;
			$data['price'] = $team['team_price'];
			$data['credit'] = 0;
			$data['condbuy'] = $condbuy;
			$data['card_id'] = $this->input->post('card_id');
			$data['remark'] = $this->input->post('remark');
			$data['jifen'] = intval($this->input->post('jifen'));
			$data['express_xx'] = $this->input->post('express_xx');
			$data['mobile'] = $this->input->post('mobile');
            //get system info
		    $head = $_SERVER['HTTP_USER_AGENT'];
		   if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
				$data['device'] = 'IOS';
			} else {
				$data['device'] = 'Android';
			}
			//check jifen
			$this->db->where('id', $this->uid);
			$this->db->limit(1);
			$this->db->select('jifen');
            $jifen = $this->db->get('users')->result_array();
            if($data['jifen']>0){
            	if($jifen[0]['jifen']-$data['jifen']<0){
                        $result['notice'] = '积分不够';
						$result['state'] = '001';
						echo json_encode($result);
						exit;
            	}
            }
			// user address
			if ($team['delivery'] == 'express') {
				if ($this->input->post('address-list') != '0') {
					//$this->tehuiDB->where('user_id', $this->uid);
					$this->tehuiDB->where('id', $this->input->post('address-list'));
					$address = $this->tehuiDB->get('address')->result_array();
					if (empty ($address)) {
						$result['notice'] = '收货地址信息有误';
						$result['state'] = '001';
						echo json_encode($result);
						exit;
					} else {
						$address = $address[0];
					}
					$data['realname'] = $address['name'];
					$data['zipcode'] = $address['zipcode'];
					$data['mobile'] = $address['mobile'];
					$data['address'] = $address['province'] . $address['area'] . $address['city'] . $address['street'];
				}
			}else{
				$this->db->where('id', $this->uid);
				$uinfo = $this->db->get('users')->result_array();
				$data['realname'] = $uinfo[0]['alias'];
			}
			$data['quantity'] = $quantity;
			$data['origin'] = $this->team_origin($team, $quantity, $express_price) - $data['jifen'] / 100;

			$result['quantity'] = $data['quantity'];
			//check card
			if ($data['card_id']) {
				$this->tehuiDB->where('consume', 'N');
				$this->tehuiDB->where('id', $data['card_id']);
				$cards = $this->tehuiDB->get('card')->result_array();
				if (empty ($cards)) {
					$result['notice'] = '代金券不存在或已使用';
					$result['state'] = '001';
					echo json_encode($result);
					exit;
				} else {
					$data['credit'] = $cards[0]['credit'];
					$data['origin'] -= $cards[0]['credit'];
					$SQL = "UPDATE card set consume = 'Y'  WHERE id = {$data['card_id']} limit 1";
					$this->tehuiDB->query($SQL);
				}
			}
			$data['origin']<0&&$data['origin']=0;
			$result['origin'] = $data['origin'];
			if ($team['allowrefund'] == 'Y')
				$data['allowrefund'] = 'Y';

			$data['resource'] = 1;
			$data['user_id'] = $this->uid;
			$data['create_time'] = time();

			if (($team['p_store'] > $team['p_warnning'])) {
				//var_dump($team['id'] && $team['id'] == $id);die;
				$p_store = $team['p_store'] - $quantity;
				if (($team['id'] && $team['id'] == $id) && (!empty ($quantity) && $p_store > $team['p_warnning'])) {
					if ($this->tehuiDB->insert('order', $data)) {
						$randid = strtolower($this->GenSecret(4, 2));
						$updata = array ();
						$insid = $this->tehuiDB->insert_id();
						$updata['pay_id'] = "go-{$insid}-{$quantity}-{$randid}";
						$this->tehuiDB->where('id', $insid);
						$this->tehuiDB->update('order', $updata);
						$result['pay_id'] = $updata['pay_id'];
						$updata = array ();
						$updata['p_store'] = $p_store;
						$this->tehuiDB->where('id', $id);
						$this->tehuiDB->update('team', $updata);
						//deal jifen
						if ($data['jifen']) {
							$SQL = "UPDATE users set jifen = jifen-{$data['jifen']}  WHERE id = {$this->uid} limit 1";
							$this->db->query($SQL);
						}
						if ($data['origin'] == 0) {
							$this->pay($result['pay_id']);
							$result['notice'] = '已付款成功！';
						}
						$result['sn'] = $insid;
					}
				} else
					if ($p_store <= $team['p_warnning']) {
					    $result['state'] = '101';
						$result['notice'] = '您购买的商品的库存不足';
						echo json_encode($result);
						exit;
					} else
						if (empty ($quantity)) {
							$result['notice'] = '请输入您要购买的商品的数量';
							echo json_encode($result);
							exit;
						}
				$result['notice'] = '成功下单！';
				$result['state'] = '000';


			} else {
				$result['state'] = '001';
				$result['notice'] = '您购买的产品已无库存，快去关注一下其他产品吧！' . $team['p_store'] . 'n:' . $team['p_warnning'];
				echo json_encode($result);
				exit;
			}
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	public function shopsDetail($param = '') {
		$result['state'] = '000';

		if ($id = intval($this->input->get('id'))) {
			$this->tehuiDB->where('id', $id);
			$tmp = $this->tehuiDB->get('partner')->result_array();
			if (!empty ($tmp)) {
				$result['data'] = $tmp[0];
				if ($result['data']['mage']) {
					$result['data']['mage'] = 'http://tehui.meilimei.com/' . $result['data']['mage'];
				}
				if ($result['data']['mage1']) {
					$result['data']['mage1'] = 'http://tehui.meilimei.com/' . $result['data']['mage1'];
				}
				if ($result['data']['mage2']) {
					$result['data']['mage2'] = 'http://tehui.meilimei.com/' . $result['data']['mage2'];
				}
				$result['data']['partner_score'] = intval(($result['data']['comment_good'] * 5 + $result['data']['comment_none'] * 3 + $result['data']['comment_bad'] * 1) / ($result['data']['comment_good'] + $result['data']['comment_none'] + $result['data']['comment_bad'] + 0.1));
			}
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//get order comments
	public function Gcomments($param = '') {
		$result['state'] = '000';

		$this->tehuiDB->select('id, realname,comment_grade,comment_display,comment_content,comment_time');
		$this->tehuiDB->where('comment_time is not NULL');
		$this->tehuiDB->order_by('comment_time DESC');
		$this->tehuiDB->where('team_id', $this->input->get('id'));
		if ($this->input->get('page')) {
			$start = ($this->input->get('page') - 1) * 10;
		} else {
			$start = 0;
		}
		$this->tehuiDB->limit(10, $start);
		$tmp = $this->tehuiDB->get('order')->result_array();
		//echo $this->tehuiDB->last_query();
        $result['data']= array();
		foreach ($tmp as $r) {
			$r['comment_time'] = date('Y年m月d日', $r['comment_time']);
			switch ($r['comment_grade']) {
				case 'good' :
					$r['comment_grade'] = 5;
					break;
				case 'none' :
					$r['comment_grade'] = 3;
					break;
				case 'bad' :
					$r['comment_grade'] = 1;
					break;
				default :
					$r['comment_grade'] = 0;
					break;
			}
			$result['data'][] = $r;
		}
		$result['notice'] = '成功获取！';

		echo json_encode($result);
	}
	//add user address
	public function addAddress($param = '') {
		$result['state'] = '000';

		if ($this->uid and $this->input->post('mobile') and $this->input->post('street')) {
			if ($this->input->post('default') == "Y") {
				$this->db->where('user_id', $this->uid);
				$this->tehuiDB->update('address', array (
					'default' => 'N'
				));
			}
			$data = array (
				'user_id' => $this->uid,
				'province' => $this->input->post('province'
			), 'city' => $this->input->post('city'), 'street' => $this->input->post('street'), 'zipcode' => $this->input->post('zipcode'), 'name' => $this->input->post('name'), 'mobile' => $this->input->post('mobile'), 'default' => $this->input->post('default'), 'create_time' => time(), 'area' => $this->input->post('area'));
			$this->tehuiDB->insert('address', $data);
			$result['address_id'] = $this->tehuiDB->insert_id();

			$result['notice'] = '成功添加！';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//delete user address
	public function delAddress($param = '') {
		$result['state'] = '000';

		if ($this->uid and $this->input->post('id')) {
			$this->tehuiDB->where('id', intval($this->input->post('id')));
			$this->tehuiDB->where('user_id', $this->uid);
			$this->tehuiDB->delete('address');
			$result['notice'] = '成功删除！';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//get user address
	public function getAddress($param = '') {
		$result['state'] = '000';

		if ($this->uid) {
			$this->tehuiDB->where('user_id', $this->uid);
			$result['data'] = $this->tehuiDB->get('address')->result_array();
			foreach ($result['data'] as &$vdata){
			    $vdata['default_android'] = $vdata['default'];
			}
			//$result['data']['default_android'] = $result['data']['default'];
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}
	//get user coupon
	public function getCoupon($param = '') {
		$result['state'] = '000';
		if (($uid = $this->uid)) {
			$start = ($this->input->get('page') - 1) * 10;
			$this->tehuiDB->select('coupon.id,coupon.secret,coupon.consume_time,coupon.expire_time,team.title,team.image,order.price,order.quantity');
			$this->tehuiDB->limit(10, $start);
			switch ($this->input->get('state')) {
				case 1 :
					$time = time();
					$this->tehuiDB->where('coupon.consume', 'N');
					$this->tehuiDB->where('coupon.expire_time > ', $time);
					break;
				case 2 :
					$time = time();
					$this->tehuiDB->where('coupon.consume', 'Y');
					break;
				case 3 :
				    $time = time();
					$this->tehuiDB->where('coupon.expire_time < ', $time);
					break;
				default :
				    $this->tehuiDB->where('coupon.consume', 'Y');
					$this->tehuiDB->where('order.comment_time', null);
					break;
			}
			$this->tehuiDB->where('coupon.user_id', $uid);
			$this->tehuiDB->join('order', 'coupon.order_id = order.id');
			$this->tehuiDB->join('team', 'team.id = coupon.team_id', 'left');
			$this->tehuiDB->order_by("coupon.id", "desc");
			$tmp = $this->tehuiDB->get('coupon')->result_array();

			$result['data'] = array ();
			foreach ($tmp as $r) {
				$r['image'] && $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
				if ($this->input->get('state') == 2) {
					$r['time'] = date('Y-m-d', $r['consume_time']);
				} else {
					$r['time'] = date('Y-m-d', $r['expire_time']);
				}

				$result['data'][] = $r;
			}
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}
		echo json_encode($result);
	}
	//get coupon detail info
	public function couponDetail($param = '') {
		$result['state'] = '000';
		if (($uid = $this->uid) AND ($id = $this->input->get('id') OR $sn = $this->input->get('sn'))) {
			$this->tehuiDB->select('coupon.id,order.comment_grade,order.comment_time,coupon.secret,coupon.consume,coupon.expire_time,coupon.consume_time,team.title,team.id as team_id,team.image,team.summary,order.id as sn,team.outdatefun,team.allowrefund,order.mobile,order.origin,order.pay_time');
			if($this->input->get('sn')){
               $this->tehuiDB->where('order.id', $sn);
			}else{
				$this->tehuiDB->where('coupon.id', $id);
			}
			$this->tehuiDB->where('coupon.user_id', $uid);
			$this->tehuiDB->join('team', 'team.id = coupon.team_id', 'left');
			$this->tehuiDB->join('order', 'order.id = coupon.order_id', 'left');
			$this->tehuiDB->order_by("coupon.id", "desc");
			$tmp = $this->tehuiDB->get('coupon')->result_array();
			$expire_time = $tmp[0]['expire_time'];
			$tmp[0]['pay_time'] = date('Y/m/d', $tmp[0]['pay_time']);
			$tmp[0]['expire_time'] = date('Y/m/d', $tmp[0]['expire_time']);
			$tmp[0]['consume_time'] = date('Y/m/d', $tmp[0]['consume_time']);
			$tmp[0]['image'] = 'http://tehui.meilimei.com/static/' . $tmp[0]['image'];



			if ($tmp[0]['comment_time']) {
			 switch ($tmp[0]['comment_grade']) {
				case 'good':
					$tmp[0]['comment_grade'] = 5;
					break;
			    case 'none':
					$tmp[0]['comment_grade'] = 3;
					break;
				case 'bad':
					$tmp[0]['comment_grade'] = 1;
					break;
				default:
				   $tmp[0]['comment_grade'] = 0;
					break;
			}
			} else {
				$tmp[0]['comment_grade'] = 0;
			}
			$result['data'] = $tmp[0];
			$result['notice'] = 'success';
			if ($result['data']['consume'] == 'Y') {
				$result['data']['state'] = 1; //'已消费';
			} else {
				if ($expire_time < time()) {
					$result['data']['state'] = 2; //'已过期';
				} else {
					$result['data']['state'] = 3; //'未使用';
				}
			}
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}
		echo json_encode($result);
	}
	//order check is ok?
	public function bookCheck($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($id = intval($this->input->get('id'))) {
			$this->tehuiDB->where('id', $id);
			$team = $this->tehuiDB->get('team')->result_array();

			if (!empty ($team)) {
				$team = $team[0];
			} else {
				$result['notice'] = '团购项目不存在！';
				$result['state'] = '400';
				echo json_encode($result);
				exit;
			}
			unset ($team['seo_title']);
			unset ($team['seo_keyword']);
			unset ($team['seo_description']);

			if ($team['begin_time'] > time()) {
				$result['notice'] = '团购项目过期！';
				$result['state'] = '400';
				echo json_encode($result);
				exit;
			}

			//whether buy
			$this->tehuiDB->where('user_id', $this->uid);
			$this->tehuiDB->where('team_id', $team['id']);
			$this->tehuiDB->where('state', 'unpay');
			$tmp = $this->tehuiDB->get('order')->result_array();
			$order = empty ($tmp) ? array () : $tmp[0];

			//buyonce
			if (strtoupper($team['buyonce']) == 'Y') {
				$this->tehuiDB->where('user_id', $this->uid);
				$this->tehuiDB->where('team_id', $team['id']);
				$this->tehuiDB->where('state', 'pay');
				$tmp = $this->tehuiDB->get('order')->result_array();
				if (!empty ($tmp)) {
					$result['notice'] = '您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！';
					$result['state'] = '400';
					echo json_encode($result);
					exit;
				}
			}

			//bind mobile can buy
			if (!$this->uid) {
				$result['notice'] = '登录后绑定手机的用户才能参团,赶快登录吧！';
				$result['state'] = '400';
				$result['ustate'] = '001';
				echo json_encode($result);
				exit;
			}
			$sql = "select mobile FROM user where id = {$this->uid}";
			$phonetmp = $this->tehuiDB->query($sql)->result_array();
			if (!($result['other']['phone'] = $phonetmp[0]['mobile'])) {
				$result['ustate'] = '403';
			}

			//peruser buy count
			if ($team['p_store'] <= $team['p_warnning']) {
				$result['notice'] = '您购买本单产品已无库存，快去关注一下其他产品吧！';
				$result['state'] = '400';
				echo json_encode($result);
				exit;
			} else {
				if ($team['per_number'] > 0) {
					$this->tehuiDB->where('user_id', $this->uid);
					$this->tehuiDB->where('team_id', $id);
					$this->tehuiDB->where('state', 'pay');
					$this->tehuiDB->select('count(quantity) as num');
					$now_count = $this->tehuiDB->get('order')->result_array();

					$team['per_number'] -= $now_count[0]['num'];

					if ($team['per_number'] <= 0) {
						$result['notice'] = '您购买本单产品的数量已经达到上限，快去关注一下其他产品吧！';
						$result['state'] = '400';
						echo json_encode($result);
						exit;
					}
				} else {
					if ($team['max_number'] > 0)
						$team['per_number'] = $team['max_number'] - $team['now_number'];
				}
			}
			$team['per_number'] == 0 && $team['per_number'] = -1;
			unset ($team['notice']);
			unset ($team['max_number']);
			unset ($team['now_number']);
			$result['notice'] = '可以使用！';
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//get book info
	public function getBookInfo($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($id = intval($this->input->get('id'))) {
			$this->tehuiDB->where('id', $id);
			$team = $this->tehuiDB->get('team')->result_array();

			$team = $team[0];
			unset ($team['seo_title']);
			unset ($team['seo_keyword']);
			unset ($team['seo_description']);

			/* 查询快递清单 */
			$result['express'] = $express = array ();
			if ($team['delivery'] == 'express') {
				$express_ralate = unserialize($team['express_relate']);
				foreach ($express_ralate as $k => $v) {
					$this->tehuiDB->where('id', $v['id']);
					$tmp = $this->tehuiDB->get('category')->result_array();
					$express[$k] = $tmp[0];
					$express[$k]['relate_data'] = $v['price'];
				}
				$result['other']['express'] = $express;
			}

			/* 查询用户收货地址*/
			if ($team['delivery'] == 'express') {
				$this->tehuiDB->where('user_id', $this->uid);
				$this->tehuiDB->order_by("id", "DESC");
				$result['other']['address'] = $this->tehuiDB->get('address')->result_array();
				$result['other']['sql'] = $this->tehuiDB->last_query();

				$this->tehuiDB->where('user_id', $this->uid);
				$this->tehuiDB->where('default', 'Y');
				$tmp = $this->tehuiDB->get('address')->result_array();
				if (!empty ($tmp)) {
					$result['other']['def'] = $tmp;
				}
				elseif (!empty ($result['other']['address'])) {
					$result['other']['def'][] = $result['other']['address'][0];
				} else {
					$result['other']['def'] = array ();
				}

			}
			//whether buy
			$this->tehuiDB->where('user_id', $this->uid);
			$this->tehuiDB->where('team_id', $team['id']);
			$this->tehuiDB->where('state', 'unpay');
			$tmp = $this->tehuiDB->get('order')->result_array();
			$order = empty ($tmp) ? array () : $tmp[0];
			//bind mobile can buy
			if (!$this->uid) {
				$result['notice'] = '登录后绑定手机的用户才能参团,赶快登录吧！';
				$result['state'] = '400';
				$result['ustate'] = '001';
				echo json_encode($result);
				exit;
			}
			$sql = "select phone FROM users where id = {$this->uid}";
			$phonetmp = $this->db->query($sql)->result_array();
			if (!($result['other']['phone'] = $phonetmp[0]['phone'])) {
				$result['ustate'] = '403';
			}
			/*
							$this->tehuiDB->where('user_id', $this->uid);
							$this->tehuiDB->where('enable', 'Y');
							$havebind = $this->tehuiDB->get('toolsbind')->result_array();
							if (!empty ($havebind)) {
								$result['notice'] = '绑定手机的用户才能参团,赶快在账户信息里绑定手机吧！';
								$result['state'] = '403';
								echo json_encode($result);
								exit;
							}*/

			//peruser buy count

			if ($team['per_number'] > 0) {
				$this->tehuiDB->where('user_id', $this->uid);
				$this->tehuiDB->where('team_id', $id);
				$this->tehuiDB->where('state', 'pay');
				$this->tehuiDB->select('count(quantity) as num');
				$now_count = $this->tehuiDB->get('order')->result_array();

				$team['per_number'] -= $now_count[0]['num'];

				if ($team['per_number'] <= 0) {
					$result['notice'] = '您购买本单产品的数量已经达到上限，快去关注一下其他产品吧！';
					$result['state'] = '400';
					echo json_encode($result);
					exit;
				}
			} else {
				if ($team['max_number'] > 0)
					$team['per_number'] = $team['max_number'] - $team['now_number'];
			}

            $team['min_number'] = $team['permin_number'];
			$team['per_number'] == 0 && $team['per_number'] = -1;
			unset ($team['notice']);
			unset ($team['max_number']);
			unset ($team['now_number']);
			$result['data'] = $team;
			$tmp = $this->db->get_where('users', array (
				'id' => $this->uid
			), 1)->result_array();
			$result['data']['jifen'] = $tmp[0]['jifen'];
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}

	//get coupon list
	public function usecoupon($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($this->uid) {
			if (($pass = $this->input->post('pass')) and $cid = $this->input->post('coupon_id')) {
				$this->tehuiDB->where('id', $cid);
				$coupon = $this->tehuiDB->get('coupon')->result_array();

				if (empty ($coupon)) {
					$result['state'] = '400';
					$result['notice'] = '本次消费失败';
				} else
					if ($coupon[0]['secret'] != $pass) {
						$result['state'] = '400';
						$result['notice'] = $cid . '编号密码不正确';
					} else
						if ($coupon[0]['expire_time'] < strtotime(date('Y-m-d'))) {
							$result['state'] = '400';
							$result['notice'] = "{$cid}&nbsp;已过期";
						} else
							if ($coupon[0]['consume'] == 'Y') {
								$result['state'] = '400';
								$result['notice'] = "{$cid}&nbsp;已用过";
							} else {
								$this->Consume($coupon[0]);
								$result['notice'] = '本次消费成功';
							}

			} else {
				$result['notice'] = '信息不完整！';
				$result['ustate'] = '012';
			}
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}
	//get coupon list
	public function coupon($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($this->uid) {
			$start = intval($this->input->get('page') - 1) * 10;
			$this->tehuiDB->limit(10, $start);
			$this->tehuiDB->select('team.title,team.id as team_id,coupon.id as coupon_id,coupon.expire_time,coupon.consume_time');
			$this->tehuiDB->where('user_id', $this->uid);
			$this->tehuiDB->join('team', 'team.id = coupon.team_id');
			$res = $this->tehuiDB->get('coupon')->result_array();
			foreach ($res as $r) {
				$lasttime = $r['expire_time'] - time();
				if ($lasttime <= 0) {
					$r['last_day'] = 0;
				} else {
					$r['last_day'] = date('d', $lasttime);
				}

				$r['expire_time'] = date('Y年m月d日', $r['expire_time']);
				$result['data'][] = $r;
			}

		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}
	//set user phone
	public function setPhone($param = '') {
		$result['state'] = '000';

		if ($this->uid and $phone = trim($this->input->post('phone'))) {
			if (!preg_match("/^1[0-9]{2}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $phone)) {
				$result['notice'] = '手机号不正确！';
				$result['state'] = '066';
				echo json_encode($result);
				exit;
			}
			if ($this->session->userdata('veryCode') != strtolower($this->input->post('code'))) {
				$result['state'] = '066';
				$result['notice'] = '验证码不正确！';
				echo json_encode($result);
				exit;
			}
			if (!$this->_check_phone_no($phone)) {
				$result['notice'] = '手机号已被使用！';
				$result['state'] = '066';
				echo json_encode($result);
				exit;
			}
			$data = array (
				'phone' => $phone
			);
			$result['notice'] = '已经成功修改！';
			$this->db->where('id', $this->uid);
			$this->db->update('users', $data);

			$data = array (
				'mobile' => $phone
			);
			$this->tehuiDB->where('id', $this->uid);
			$this->tehuiDB->update('user', $data);
		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	
	//check voucher
	public function voucher($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($this->uid) {
			if ($voucher = trim($this->input->post('voucher'))) {
				$this->tehuiDB->where('id', $voucher);
				$this->tehuiDB->select('id, end_time, begin_time,consume,credit');
				$tmp = $this->tehuiDB->get('card')->result_array();
				//$result['sql'] = $this->tehuiDB->last_query();
				if (!empty ($tmp)) {
					$result['data'] = $tmp[0];
					if ($result['data']['consume'] == 'Y') {
						$result['state'] = '400';
						$result['notice'] = '代金券已被使用过！';
					}
					elseif ($result['data']['begin_time'] > time() or $result['data']['end_time'] < time()) {
						$result['state'] = '400';
						$result['notice'] = '代金券已过期！';
					} else {
						$result['notice'] = '代金券可使用！';
					}
				} else {
					$result['state'] = '400';
					$result['notice'] = '代金券不存在！';
					$result['data'] = array ();
				}
			}
		} else {
			$result['ustate'] = '001';
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	//cancel order
	public function cancelOrder($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		if ($this->uid) {
			if ($this->input->post('id')) {
				$this->rollOrder();
			}
		} else {
			$result['ustate'] = '001';
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	public function myOrder($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if (($uid = $this->uid) OR $uid = $this->input->get('uid')) {
			if ($this->input->get('state')) {
				switch ($this->input->get('state')) {
					case 'pay' :
						$this->tehuiDB->where('order.state', 'pay');
						break;
					case 'compay' :
						$this->tehuiDB->where('order.state', 'pay');
						$this->tehuiDB->where('order.comment_time', null);
						break;
					default :
						$this->tehuiDB->where('order.state', 'unpay');
						break;
				}
			}
			if ($page = $this->input->get('page')) {
				$start = ($page -1) * 10;
				$this->tehuiDB->limit(10, $start);
			}
			$this->tehuiDB->where('order.user_id', $uid);
			$this->tehuiDB->order_by('order.id', 'DESC');
			$this->tehuiDB->join('team', 'order.team_id = team.id');
			$this->tehuiDB->select('order.id,order.express,order.express_no,order.comment_time,order.quantity, order.state,team.team_price, team.image,team.title,team.summary,order.create_time');
			$tmp = $this->tehuiDB->get('order')->result_array();
			//$result['sql'] = $this->tehuiDB->last_query();
			$result['data'] = array ();
			foreach ($tmp as $r) {
				$r['hasComment'] = $r['comment_time'] > 0 ? 'Y' : 'N';
				$r['image'] && $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
				$r['create_time'] = date('Y年m月d日', $r['create_time']);
				$result['data'][] = $r;
			}
		} else {
			$result['ustate'] = '001';
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	
	
	
	//get order pay info
	public function payInfo($param) {
		$result['state'] = '000';
		$result['ustate'] = '000';
        //$this->uid = $this->input->get('uid');
        
		if ($this->uid) {
		    $id = $this->input->get('id');
			if ($id) {
				$this->tehuiDB->select('order.origin,category.name as express_name,order.price,order.pay_id,order.pay_id,order.jifen,order.mobile,order.fare,order.credit,order.state,order.quantity,team.title');
				$this->tehuiDB->where('order.id', $id);
				$this->tehuiDB->join('team', 'team.id = order.team_id', 'left');
				$this->tehuiDB->join('category', 'category.id = order.express_id', 'left');
				$info = $this->tehuiDB->get('order')->result_array();
				$result['data'] = array ();
				if (!empty ($info)) {
					$info[0]['total'] = $info[0]['price'] * $info[0]['quantity'] + $info[0]['fare'];
					$result['data'] = $info[0];
				}
			} else {
				$result['ustate'] = '001';
				$result['notice'] = '参数不全！';
				$result['state'] = '012';
			}
		} else {
			$result['ustate'] = '001';
			$result['notice'] = '未登入！';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}

	//notify
	public function notify() {
	    $alipay_config['cacert'] = getcwd() . '\\cacert.pem';
	    echo $alipay_config['cacert'];die;
		if (!empty ($_POST)) {

			$alipay_config = array ();
			$alipay_config['partner'] = '2088111063773467';
			$alipay_config['private_key_path'] = '/mnt/meilimei/alipay_key/rsa_private_key.pem';
			$alipay_config['ali_public_key_path'] = '/mnt/meilimei/alipay_key/alipay_public_key.pem';
			$alipay_config['sign_type'] = strtoupper('RSA');
			$alipay_config['input_charset'] = strtolower('utf-8');
			$alipay_config['cacert'] = getcwd() . '\\cacert.pem';
			echo $alipay_config['cacert'];die;
			$alipay_config['transport'] = 'http';
			$this->load->library('alipay/notify');

			$this->notify->init($alipay_config);
			$verify_result = $this->notify->verifyNotify();
			if ($verify_result) {
				$out_trade_no = $_POST['out_trade_no'];
				$trade_no = $_POST['trade_no'];
				$trade_status = $_POST['trade_status'];
				if ($_POST['trade_status'] == 'TRADE_FINISHED') {
					$this->tehuiDB->where('pay_id', $out_trade_no);
					$order = $this->tehuiDB->get('order')->result_array();
					if ($order[0]['origin'] != $_POST['total_fee']) {
						echo 'error';
						exit;
					}
					$this->tehuiDB->where('pay_id', $out_trade_no);
					$this->tehuiDB->update('order', array (
						'state' => 'pay',
						'money' => $_POST['total_fee'],
						'service' => 'alipay',
						'trade_no' => $trade_no,
					'pay_time' => time()));
					$this->payCall($order[0]);
					echo "success";
				}
			} else {
				echo "fail";
			}
		} else {
			echo "fail";
		}
	}
	//callback pay order
	private function pay($pay_id) {
		$this->tehuiDB->where('pay_id', $pay_id);
		$order = $this->tehuiDB->get('order')->result_array();

		$this->tehuiDB->where('pay_id', $pay_id);
		$this->tehuiDB->update('order', array (
			'state' => 'pay',
			'service' => 'alipay',
			'trade_no' => '',
		'pay_time' => time()));
		$this->payCall($order[0]);
	}
	//total user orders
	public function total($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if (($uid = $this->uid) OR $uid = $this->input->get('uid')) {
			$this->tehuiDB->where('user_id', $uid);
			$this->tehuiDB->where('consume', 'Y');
			$result['data']['tuangouUse'] = $this->tehuiDB->count_all_results('coupon');

			$this->tehuiDB->where('user_id', $uid);
			$this->tehuiDB->where('consume', 'N');
			$time = time();
			$this->tehuiDB->where('coupon.expire_time > ', $time);
			$result['data']['tuangouNoUse'] = $this->tehuiDB->count_all_results('coupon');

			$this->tehuiDB->where('user_id', $uid);
			$this->tehuiDB->where('state', 'pay');
			$result['data']['orderPay'] = $this->tehuiDB->count_all_results('order');

			$this->tehuiDB->where('user_id', $uid);
			$this->tehuiDB->where('state', 'unpay');
			$result['data']['orderUnpay'] = $this->tehuiDB->count_all_results('order');

			$tmp = $this->tehuiDB->query("SELECT COUNT(*) AS `numrows` FROM (`order`) WHERE  state = 'pay' and user_id = {$uid} and `comment_time` is null ")->result_array();
			$result['data']['orderUnComment'] = $tmp[0]['numrows'];

			$result['data']['daijin'] = 0;

		} else {
			$result['ustate'] = '001';
			$result['notice'] = '参数不全！';
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	//comment success coder
	function commnetOrder($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		if ($this->uid and $id = $this->input->post('id')) {
			$this->tehuiDB->select('team.partner_id');
			$this->tehuiDB->where('order.id', $id);
			$this->tehuiDB->join('team', 'team.id = order.team_id');
			$info = $this->tehuiDB->get('order')->result_array();
            if(empty($info)){
              $result['notice'] = '订单不存在！';
			   $result['state'] = '403';
            }

			$updata = array (
				'comment_grade' => trim($this->input->post('commnet_grade')),
                'comment_content' => trim($this->input->post('comment_content')),
                 'comment_wantmore' => trim($this->input->post('commnet_wantmore')),
                  'partner_id' => intval($info[0]['partner_id']),
                   'comment_time' => time());
		 	$this->tehuiDB->where('id', $id);
		    $this->tehuiDB->update('order', $updata);
          // $result['sql'] = $this->tehuiDB->last_query();
			/* update partner */
			$apls = '';
			switch($this->input->post('comment_grade')){
				case 'good':
				  $apls = 'comment_good';
				  break;
				case 'none':
				 $apls = 'comment_none';
				  break;
				case 'bad':
				 $apls = 'comment_bad';
				  break;
			}
			$result['notice'] = '评论成功！';
			if($apls){
$sql = "update partner SET {$apls} = {$apls}+1 where id = {$info[0]['partner_id']} LIMIT 1";
           $this->tehuiDB->query($sql);
			}

		} else {
			$result['notice'] = '参数不全！';
			$result['state'] = '001';
		}

		echo json_encode($result);
	}
	/**
	 * RSA签名
	 */
	function rsaSign($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';

		$priKey = file_get_contents('/mnt/meilimei/alipay_key/rsa_private_key.pem');
		$res = openssl_get_privatekey($priKey);
		$sign = '';
		openssl_sign($this->input->post('str'), $sign, $res);
		openssl_free_key($res);
		$result['data'] = base64_encode($sign);
		echo json_encode($result);
	}

	private function GenSecret($len = 6, $type = 2) {
		$secret = '';
		for ($i = 0; $i < $len; $i++) {
			if (1 == $type) {
				if (0 == $i) {
					$secret .= chr(rand(49, 57));
				} else {
					$secret .= chr(rand(48, 57));
				}
			} else
				if (2 == $type) {
					$secret .= chr(rand(65, 90));
				} else {
					if (0 == $i) {
						$secret .= chr(rand(65, 90));
					} else {
						$secret .= (0 == rand(0, 1)) ? chr(rand(65, 90)) : chr(rand(48, 57));
					}
				}
		}
		return $secret;
	}
	//pay callback deals
	private function payCall($order) {
		$updata = array ();
		$this->tehuiDB->where('id', $order['team_id']);
		$tmp = $this->tehuiDB->get('team')->result_array();

		$team = $tmp[0];
		$order['title'] = $team['title'];
		$plus = $team['conduser'] == 'Y' ? 1 : $order['quantity'];
		$team['now_number'] += $plus;

		/* close time */
		if ($team['max_number'] > 0 && $team['now_number'] >= $team['max_number']) {
			$team['close_time'] = time();
		}
		/* reach time */
		if ($team['now_number'] >= $team['min_number'] && $team['reach_time'] == 0) {
			$team['reach_time'] = time();
		}
		$this->tehuiDB->where('id', $team['id']);
		$this->tehuiDB->update('team', array (
			'close_time' => $team['close_time'],
			'reach_time' => $team['reach_time'],
			'now_number' => $team['now_number']
		));
		//UPDATE buy_id
		$SQL = "UPDATE `order` o,(SELECT max(buy_id)+1 AS c FROM `order` WHERE state = 'pay' and team_id = '{$team_id}') AS c SET o.buy_id = c.c, o.luky_id = 100000 + floor(rand()*100000) WHERE o.id = '{$order_id}' AND buy_id = 0;";
		$this->tehuiDB->query($SQL);
		$this->CreateFromOrder($order);
		if ($order['express'] == 'N') {
			$this->CreateCoupon($order);
		}
	}

	private function CreateCoupon($order) {
		$this->tehuiDB->where('id', $order['team_id']);
		$tmp = $this->tehuiDB->get('team')->result_array();
		$team = $tmp[0];
		for ($i = 0; $i < $order['quantity']; $i++) {
			$id = (ceil(time() / 100) + rand(10000000, 40000000)) . rand(1000, 9999);
			$id = $this->VerifyCode($id);
			$this->tehuiDB->where('id', $id);
			$tmp = $this->tehuiDB->get('coupon')->result_array();
			if (!empty ($tmp))
				continue;
			$pass = $this->VerifyCode($this->GenSecret(6, 1));
			$coupon = array (
				'id' => $id,
				'user_id' => $order['user_id'],
				'buy_id' => $order['buy_id'],
				'partner_id' => $team['partner_id'],
				'order_id' => $order['id'],
				'credit' => $team['credit'],
				'team_id' => $order['team_id'],
				'secret' => $pass,
				'expire_time' => $team['expire_time'],
			'create_time' => time(),);
			$this->tehuiDB->insert('coupon', $coupon);
			$expire_time = date('Y-m-d',$team['expire_time']);
			$message = "亲爱的美粉，您的订单已经成功下单:{$order['title']},特惠券号:{$id},有效期至{$expire_time}，需提前3天预约，预约咨询电话：400-667-7245。";

			if($order['mobile']){
               $this->sms->sendSMS(array(
                            "{$order['mobile']}"
                           ), $message);
            }
		}
	}

	private function VerifyCode($code = 0) {
		$verifycode = $code ? $code : rand(100000, 999999);
		$verifycode = str_replace('1989', '9819', $verifycode);
		$verifycode = str_replace('1259', '9521', $verifycode);
		$verifycode = str_replace('12590', '95210', $verifycode);
		$verifycode = str_replace('10086', '68001', $verifycode);
		return $verifycode;
	}
	private function CreateFromOrder($order) {
		//update user money;
		//$user = Table::Fetch('user', $order['user_id']);
		/*	Table::UpdateCache('user', $order['user_id'], array(
						'money' => array( "money - {$order['origin']}" ),
						));
		*/
		$u = array (
			'user_id' => $order['user_id'],
			'money' => $order['origin'],
			'direction' => 'expense',
			'action' => 'buy',
			'detail_id' => $order['team_id'],
		'create_time' => time(),);
		$this->tehuiDB->insert('flow', $u);
	}
	//order rollback
	public function rollOrder($order_id = '') {

		$order_id=$this->input->get('order_id');
		$this->tehuiDB->where('id',$order_id);
		$rs = $this->tehuiDB->get('order')->result_array();
		if(isset($rs[0])){
			$jifen = $rs[0]['jifen'] ? $rs[0]['jifen']:0;
			$jifen_query = $this->db->query('update users set jifen=jifen + ' .$rs[0]['jifen'] .' where id= ?',array($rs[0]['user_id']));
			$qty_query = $this->tehuiDB->query('update team set p_store = p_store + ' .$rs[0]['quantity'] .' where id= ?',array($rs[0]['team_id']));
			$card_query = $this->tehuiDB->query("update card set consume = 'N' where id= ?",array($rs[0]['card_id']));
			if(($jifen_query->num_rows()>0 && $qty_query->num_rows()>0 && $card_query->num_rows()>0 ) 
				|| ($jifen_query->num_rows()>0 && $qty_query->num_rows()>0)
				|| ($qty_query->num_rows()>0 && $card_query->num_rows()>0) || ($card_query->num_rows()>0)){
				$this->tehuiDB->delete('order', array('id' => $order_id)); 
			}
		}
	}
	private function team_origin($team, $quantity = 0, $express_price = 0) {
		$origin = $quantity * $team['team_price'];
		if ($team['delivery'] == 'express' && ($team['farefree'] == 0 || $quantity < $team['farefree'])) {
			$origin += $express_price;
		}
		return $origin;
	}

	private function _check_phone_no($value) {
		$value = trim($value);
		if (true) {
			if ($this->wen_auth->is_phone_available($value)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	//coupon Consume
	private function Consume($coupon) {
		if (!$coupon['consume'] == 'N')
			return false;
		$u = array (
			'ip' => $_SERVER['REMOTE_ADDR'],
		'consume_time' => time(), 'consume' => 'Y',);
		$this->tehuiDB->where('id', $coupon['id']);
		$this->tehuiDB->update('coupon', $u);
		$this->CreateFromCoupon($coupon);
		return true;
	}
	private function CreateFromCoupon($coupon) {
		if ($coupon['credit'] <= 0)
			return 0;
		//update user money;
		$this->tehuiDB->where('id', $coupon['user_id']);
		$this->tehuiDB->update('user', array (
			'money' => array (
				"money + {$coupon['credit']}"
			),


		));
		$u = array (
			'user_id' => $coupon['user_id'],
			'money' => $coupon['credit'],
			'direction' => 'income',
			'action' => 'coupon',
			'detail_id' => $coupon['id'],
		'create_time' => time(),);
		$this->tehuiDB->insert('flow', $u);
		return true;
	}
	//calculate distance between coordinate point
	private function rad($d) {
		return $d * 3.1415926535898 / 180.0;
	}
	private function getDistance($lat1, $lng1, $lat2, $lng2) {

		$EARTH_RADIUS = 6378.137;
		$radLat1 = $this->rad($lat1);
		$radLat2 = $this->rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = $this->rad($lng1) - $this->rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$s = $s * $EARTH_RADIUS;
		$s = round($s * 10000);
		return $s;
	}

}
?>