<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api tehui => 团购 Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class onetehui extends MY_Controller {
	private $notlogin = true, $uid = 0,$result=array();
	public function __construct() {
		parent :: __construct();
		$this->tehuiDB = $this->load->database('tehui', TRUE);
		//error_reporting(E_ALL);
		//ini_set("display_errors","On");
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
	 * 获取特惠详情根据id
	 * @param $int t_id 首页特惠类型
	 * http://www.meilimei.com/v2/tehui/getTehuiDetailById
	 *
	 * */
	public function getTehuiList(){
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
	                $condition .= " AND t.sub_id = {$tag_id}";
	            }
	            $order = ' t.sort_order DESC,t.begin_time DESC, t.id DESC';
	            $limit = "{$start},10";
	            $result['tehui_data'] = array ();
	            $tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t WHERE {$condition} ORDER by {$order} limit {$limit} ")->result_array();
	    
	            $randpic = date('Ymdhi',time());
	            foreach ($tmpinfo as $r) {
	                $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
	                $result['tehui_data'][] = $r;
	            }
	            
	            $sql = "select tehuiy.*,cp.name from tehui_yiyuan as tehuiy join company as cp where tehuiy.mechanism = cp.id"; 
	            $list_info = $this->db->query($sql)->result_array();
	            
	            foreach ($list_info as $li) {
	                $li['image_url'] = $this->remote->getLocalImage($li['image']);
	                $result['yiyuan_data'][] = $li;
	            }
	            
	            
	            
	            if(count($result['tehui_data']) > 0 and count($result['yiyuan_data'])){
	                $result['notice'] = '成功获取！';
	            }
	            
	            
	        } else {
	            $result['notice'] = '参数不全！';
	            $result['state'] = '012';
	        }
	        echo json_encode($result);
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
        $rs = $this->db->get('tehui_yiyuan');
        $rs = $rs->result_array();

        foreach($rs as &$r){
            $m_id  = $r['mechanism'];
            	
            $this->db->where('id',$m_id);
            $r['mechanism'] = $this->db->get('company')->result_array();
            $r['mechanism'][false]['thumb'] = $this->profilepic($m_id, 2);
            $user_id_arr = unserialize($r['physician']);
            $this->db->where_in('user_id',$user_id_arr);

            $r['physician'] = array();
            $r['physician'] = $this->db->get('user_profile')->result_array();
            	
            foreach ($r['physician'] as &$v){
                $v['thumb'] = $this->profilepic($v['user_id'],2);
                $this->db->where('id',$v['user_id']);
                $tmp_user = $this->db->get('users')->result_array();
                $v['username'] = $tmp_user[false]['username'];
                $v['grade'] = $tmp_user[false]['grade'];
                $v['position'] = trim($v['position']);
                $v['position'] = str_replace("&nbsp;"," ",$v['position']);
                $v['casenum'] = rand(50,1200);
            }

            $note_id = unserialize($r['relation_note']);

            $this->result['note'] = array();
            foreach ($note_id as $id){
                $note_rs= $this->Diary_model->getDiaryDetail($id);
                if(!empty($note_rs)){
                    $note_rs[false]['thumb_url'] = $this->remote->thumb($note_rs[false]['uid'], '36');
                    $note_rs[false]['imgurl_url'] =$this->remote->getLocalImage($note_rs[false]['imgurl']);
                    $userInfo = $this->Diary_model->get_user_by_username($note_rs[false]['uid']);
                    $note_rs[false]['myname'] = $userInfo[0]['username']?$userInfo[0]['username']:$userInfo[0]['alias'];
                }
                $this->result['note'][] = $note_rs[false];
            }
                
            

             
            $product_id = unserialize($r['relation_product']);
		    $this->db->where_in('id',$product_id);
		    $r['r_product'] = $this->db->get('tehui_relation')->result_array();
 
		    $this->result['product_data'] = array();
            
            if($r['r_product']){
		        foreach($r['r_product'] as $rpv){
		            $tehui_id = $rpv['tehui_id'];
					$this->tehuiDB->where('team.id', $tehui_id);
					$this->tehuiDB->where('team.group_id', 1);
					$this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
					$this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
					$r_product_tmp = $this->tehuiDB->get('team')->result_array();
					if (!empty($r_product_tmp)) { 
						$this->result['product_data'][] = $r_product_tmp[false];
					}
		        }
			}
			 
             
          	$tehui_id = $r['tehui_id'];
            $this->tehuiDB->where('team.id', $tehui_id);
            $this->tehuiDB->where('team.group_id', 1);
            $this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
            $this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
            $tmp = $this->tehuiDB->get('team')->result_array();

            $this->result['data'] = array();
            $this->result['tehui'] = array();

            if (!empty ($tmp)) { 
                $this->result['data'] = $tmp[false];
                if (isset ($this->result['data']['longlat'])) {
                    $this->result['data']['haspartner'] = 1;
                    $this->result['data']['partner_score'] = intval(($this->result['data']['comment_good'] * 5 + $this->result['data']['comment_none'] * 3 + $this->result['data']['comment_bad'] * 1) / ($this->result['data']['comment_good'] + $this->result['data']['comment_none'] + $this->result['data']['comment_bad'] + 0.1));
                    $usercor = explode(',', $this->result['data']['longlat']);
                    $this->result['data']['distance'] = $this->getDistance($this->input->get('Lat'), $this->input->get('Lng'), $usercor[0], $usercor[1]);
                } else {
                    $this->result['data']['haspartner'] = 0;
                }
                
                $this->result['data']['txtDetail'] = mb_substr(strip_tags($this->result['data']['detail']),0,120);
                //$this->result['data']['detail'] = $this->gdetail($this->result['data']['detail'],$this->result['data']['title']);
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
                $this->result['data']['notice'] = '<div style="font-size:12px"><b>有效期:</b><br>' . $this->result['data']['expire_time'] . '<br>' . $this->result['data']['notice'].'</div>';
                
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
        echo json_encode($this->result);
	}
 
}
?>