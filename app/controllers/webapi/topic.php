<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class topic extends CI_Controller {
	private $notlogin = true;
	private $uid = 0;
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('filter');
		$this->load->library('alicache');
		$this->load->model('auth');
		$this->load->model('remote');
		$this->load->helper('file');
	}
	// topic detail info api
	
	
	function view($param = '') {

		$result['state'] = '000';
		if ($wid = $this->input->get('weibo_id')) {
			/*if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){*/
				$this->aplus($wid, 0);
				$this->db->where('wen_weibo.weibo_id', $wid);
				$this->db->where('wen_weibo.type & ', 25);
				$this->db->from('wen_weibo');
				$this->db->join('users', 'users.id = wen_weibo.uid');
				$this->db->join('user_profile', 'user_profile.user_id = wen_weibo.uid');
				$this->db->select('user_profile.city,users.alias,users.phone,wen_weibo.extra_ids,wen_weibo.tags,wen_weibo.product_data,wen_weibo.uid,wen_weibo.type,wen_weibo.ctime,wen_weibo.q_id,wen_weibo.content,wen_weibo.type_data,wen_weibo.commentnums,wen_weibo.favnum,wen_weibo.views,wen_weibo.zan,wen_weibo.video,wen_weibo.videoHeight,wen_weibo.tehui_ids,wen_weibo.comments as commentnum');
				$tmp = $this->db->get()->result_array();
				//get pic
				$this->db->where('wen_weibo.q_id', $tmp[0]['q_id']);
				$this->db->where('wen_weibo.type', 4);
				$this->db->from('wen_weibo');
				$this->db->select('wen_weibo.type_data,wen_weibo.favnum');
				$ptmp = $this->db->get()->result_array();
				$result['data'] = array ();
				
				if (!empty ($tmp)) {
					$result['data']['zhenrenxiiu'] = strpos($tmp[0]['tags'], '人秀') ? 1 : 0;
					$result['data']['qid'] = $tmp[0]['q_id'];
					//$result['data']['videoHeight'] = $tmp[0]['videoHeight'];
					//$result['data']['video'] = $tmp[0]['video']?$this->showVideo($wid):'';
					$result['data']['city'] = $tmp[0]['city'];
					$result['data']['uid'] = $tmp[0]['uid'];
                    $result['data']['uname'] = $this->GName($tmp[0]['alias'],$tmp[0]['phone']);
					$result['data']['thumb'] = $this->profilepic($tmp[0]['uid'], 2);

					$result['data']['ctime'] = date('Y-m-d', $tmp[0]['ctime']);
					if ($tmp[0]['type_data']) {
						$info = unserialize($tmp[0]['type_data']);
						$result['data']['content'] = $info['title'];
					}else{
						$result['data']['content'] = $info['title'];
					}
					$result['data']['zan'] = $tmp[0]['zan'];
					//$result['data']['favnum'] = $tmp[0]['favnum'];
					//$result['data']['views'] = $tmp[0]['views'] ;
					$result['data']['commentnum'] = $tmp[0]['commentnum'];
					if(!empty($tmp[0]['tags'])){
						$row['tag'] = explode(',',$tmp[0]['tags']);
						unset($row['tags']);
						foreach($row['tag'] as $item){
							if(!empty($item)){
								$result['data']['tags'][] = $item;	
							}
						}
					}
					//$result['data']['tags'] = 
					if ($tmp[0]['type_data']) {
						$info = unserialize($tmp[0]['type_data']);
					}
					/*
					if (!isset ($info['title'])) {
						$info['title'] = $result['data']['title'] = $result['data']['content'];
					} else {
						$result['data']['title'] = $info['title'];
						if ($result['data']['content'] == '') {
							//$result['data']['content'] =$info['title'];
						}

					}*/
					$result['data']['mutilPic'] = "0";
					$result['data']['haspic'] = "0";
					$result['data']['extra'] = $info;
					$result['data']['height'] = isset ($result['data']['extra']['pic']['height']) ? $result['data']['extra']['pic']['height'] : 300;
					$result['data']['width'] = isset ($result['data']['extra']['pic']['width']) ? $result['data']['extra']['pic']['width'] : 200;
					//show pic width
					$width = 'auto';
					if ($this->input->get('width')) {
						$width = intval($this->input->get('width'));
					}
					if ($tmp[0]['type'] == 1 and !empty ($ptmp)) {
						$pictmp = unserialize($ptmp[0]['type_data']);
						$this->db->where('id', $pictmp[1]['id']);
						$this->db->from('wen_attach');
						$this->db->select('savepath');
						$ptmp = $this->db->get()->result_array();
						if (!empty ($ptmp)) {
							$result['data']['haspic'] = "1";

							$arr_url = explode('/',$ptmp['0']['savepath']);
							if(isset($arr_url[1])){
								$url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x640/',$ptmp['0']['savepath']);
							}
							//$t['url'] = $this->remote->show320($url, $width);
							
							$result['data']['url'] = $this->remote->show320($url, $width);//$this->remote->show($ptmp['0']['savepath'], $width);
						}
					}
					elseif ($tmp[0]['type'] == 8) {
						$pictmp = unserialize($tmp[0]['type_data']);
						
						if (isset ($pictmp['pic'])) {
							$result['data']['haspic'] = "1";
							$arr_url = explode('/',$pictmp['pic']['savepath']);

							if(isset($arr_url[1])){
								$url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x640/',$pictmp['pic']['savepath']);
							}
							
							$result['data']['url'] = $this->remote->show320($url, $width);//$this->remote->show($pictmp['pic']['savepath'], $width);
						}
					}
					elseif ($tmp[0]['type'] == 16) {
						$pictmp = unserialize($tmp[0]['type_data']);
						if (isset ($pictmp['pic']) OR isset ($pictmp['savepath'])) {
							$result['data']['haspic'] = "1";
							$result['data']['mutilPic'] = "1";
							$result['data']['images'] = $this->Plist($wid);
						}
					}
					//sumarize
					if ($result['data']['uid'] == $this->uid and $tmp[0]['commentnums'] > 0) {
						$this->db->query("update wen_weibo set commentnums = 0 where weibo_id = {$wid} AND uid = {$this->uid}");

						$mec = new Memcache();
						$mec->connect('127.0.0.1', 11211);
						if (($res = $mec->get('state' . $this->uid)) and !empty ($res)) {
							$res['weiboCommentSum'] -= $tmp[0]['commentnums'];
							$res['weiboCommentSum'] < 0 && $res['weiboCommentSum'] = 0;
							$mec->set('state' . $this->uid, $res, 0, 3600);
						}
						$mec->close();
					}
					//get tehui
					$result['tehui'] = array();
					if($tmp[0]['tehui_ids']){
						$result['tehui'] = $this->getTehui($tmp[0]['tehui_ids']);
					}
					//get extran links
					$result['extras'] = array();
					if($tmp[0]['extra_ids'] ){
						$result['extras'] = $this->getExtras($tmp[0]['extra_ids']);
					}else{
						$tmp = unserialize($tmp[0]['product_data']);
						foreach($tmp as $r){
							$r['image'] = $this->remote->show($r['image'], 120);
							$result['extras'][] = $r;
						}
					}

					$result['data']['doctor']= $this->getAns($result['data']['qid']);
					$this->db->query("update wen_weibo set views=views+1 where weibo_id = '$wid'");
				}
			/*	$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
			}else{
				$result = array();
				$result = unserialize($rs);	
			}*/
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	
	private function getAns($qid,$rid) {
		if ($qid) {
			$result = array ();
			$fields = 'wen_answer.uid,wen_answer.id,wen_answer.content,wen_answer.new_comment,wen_answer.is_talk,wen_answer.cdate,users.alias as Uname,users.verify,users.suggested,users.grade,user_profile.company,user_profile.department,users.sysgrade';
			if ($rid) {
				$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users ON users.id = wen_answer.uid LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND wen_answer.uid = {$rid} order by wen_answer.id ASC")->result_array();

				$tmp[0]['sysgrade'] != '' && $tmp[0]['grade'] = $tmp[0]['sysgrade'];
				$insertd['question'] = $this->qdetail($qid);
				$insertd['mainans']['content'] = $tmp[0]['content'];
				$insertd['mainans']['cTime'] = date('Y-m-d H:i:s', $tmp[0]['cdate']);
				$insertd['mainans']['fuid'] = $tmp[0]['uid'];

				$result['talks'] = $this->getcomment($tmp[0]['id'], $rid, $insertd);
				$tmp[0]['thumb'] = $this->profilepic($tmp[0]['uid'], 2);
				if ($tmp[0]['department']) {
					$tmp[0]['department'] = $this->yisheng->search($tmp[0]['department']);
				}
				$tmp[0]['cdate'] = date('Y-m-d H:i:s', $tmp[0]['cdate']);
				$result[] = $tmp[0];
				$id = $tmp[0]['id'];
				$this->db->query("UPDATE `wen_answer` SET `new_comment` = 0   WHERE `id` ={$id}");
			} else {
				$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users  ON users.id = wen_answer.uid LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC")->result_array();

				foreach ($tmp as $row) {
					$row['cdate'] = date('Y-m-d', $row['cdate']);
					$row['thumb'] = $this->profilepic($row['uid'], 1);
					$result[] = $row;
				}

			}
		} 

		return $result;
	}	
	
	private function showVideo($content){
       return site_url().'video/index/'.urlencode($content);
	}
	//get set pic lists
	private function Plist($id) {
		$this->db->select('id,savepath,height,width,info');
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
			$r['savepath'] = $this->remote->show($r['savepath'], $width);
			$rt[] = $r;
		}
		return $rt;
	}
	//get points data
	private function Gpoint($picid){
		$this->db->where('pic_id',$picid);
        return $this->db->get('topic_pics_extra')->result_array();
	}
	public function addPicBits(){

	}
	//add topic with pictrues V2
	public function addTopicWithPics($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->uid) {
				$result['ustate'] = '000';
				if (strlen(strip_tags(trim($this->input->post('content')))) > 2) {
					$datas = array ();
					$datas['type'] = 16;
					$datas['q_id'] = 0;
					$datas['uid'] = $this->uid;
					$datas['ctime'] = time();
					$enc = md5(trim($this->input->post('content')));
					if (isset($_COOKIE['topic_senddata']) and $_COOKIE['topic_senddata'] == $enc) {
						$result['state'] = '012';
						$result['notice'] = '话题重复发送！';
						echo json_encode($result);
						exit;
					} else {
						setcookie('topic_senddata', $enc);
					}
					//check illegal word
					if(strlen(strip_tags(trim($this->input->post('content'))))<5){
						$result['state'] = '012';
						$result['notice'] = '内容太短！';
						echo json_encode($result);
						exit;
					}
					//check illegal word
					if(!$this->filter->judge($this->input->post('content'))){
						$result['state'] = '012';
						$result['notice'] = '含有广告等信息！';
						echo json_encode($result);
						exit;
					}
					if (!$this->session->userdata('topic_ctime')) {
						$this->session->set_userdata('topic_ctime', $datas['ctime']);
					}
					elseif (time() - $this->session->userdata('topic_ctime') < 3) {
						$result['state'] = '012';
						$result['notice'] = '话题重复发送！';
						echo json_encode($result);
						exit;
					}
					elseif (time() - $this->session->userdata('topic_ctime') < 10) {
						$result['state'] = '012';
						$result['notice'] = '信息发送间隔2分钟！';
						echo json_encode($result);
						exit;
					}
					$content  = $this->input->post('content');
					$this->filter->filts($content);
					$ttag = str_replace('热门', '', trim($this->input->post('items')));
					$ttag = str_replace('null', '', $ttag);
					$datas['newtime'] = time();
					$datas['tags'] = $ttag;
					$info = array();
					$info['title'] = $content;
					$datas['type_data'] = $type_data =  serialize($info);
					//get system info
					$head = $_SERVER['HTTP_USER_AGENT'];
					if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
						$datas['wsource'] = 'IOS';
					} else {
						$datas['wsource'] = 'Android';
					}
					$datas['isdel'] = 1;
					$this->db->insert('wen_weibo', $datas);
					$result['weibo_id'] = $this->db->insert_id();
					$result['notice'] = '话题发送成功！';
					$points = $this->input->post('attachPicInfo');
					if($picnum = intval($this->input->post('has_pics'))){
						$upstate = false;
						for($i=1;$i<=$picnum;$i++){
							//$result['infos'][$i] = $_FILES['attachPic'.$i]['tmp_name'];
							if($i==1){
								$upstate = $this->addTopicPicAC($result['weibo_id'],$_FILES['attachPic'.$i]['tmp_name'],$this->input->post('upPicInfo'.$i),$type_data,true);
							}else{
								$upstate = $this->addTopicPicAC($result['weibo_id'],$_FILES['attachPic'.$i]['tmp_name'],$this->input->post('upPicInfo'.$i),$type_data);
							}
							if(!$upstate){
								break;
							}else{
								isset($points[$i])&&$this->addpoints($points[$i],$result['weibo_id'],$upstate);
							}
						}
						if($upstate){
							if (!$this->checkhome($datas['tags'])) {
						      //  $this->openweibo($result['weibo_id']);
					        }else{
					        	$result['notice'] = '话题等待审核中！';
					        }
						}else{
							setcookie('topic_senddata', '');
							$this->delweibo($result['weibo_id']);
							$this->session->set_userdata('topic_ctime', 0);
							$result['notice'] = '话题发送失败！';
							$result['state'] = '006';
						}
					}else{
                    // $this->openweibo($result['weibo_id']);
					}
					//deal extra chain data
                    $this->load->model('user_sum');
					$this->user_sum->addJifen($this->uid,'JIFEN_WEIBO');
					$this->user_sum->addGrowth($this->uid,'GROW_TOPIC');
					$this->addTnum($ttag);
				} else {
					$result['notice'] = '发送内容太短！';
					$result['state'] = '012';
				}
			} else {
				$result['notice'] = '账户未登入！';
				$result['ustate'] = '001';
			}
		} else {
			$result['notice'] = 'Token错误！';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//deal topic picture points
	private function addpoints($point,$weibo_id,$pic_id){
		foreach($point as $r){
       $inseart = array();
       $inseart['weibo_id'] = $weibo_id;
       $inseart['pic_id'] = $pic_id;
       $inseart['items'] = $r['items'];
       $inseart['price'] = $r['price'];
       $inseart['doctor'] = $r['doctor'];
       $inseart['yiyuan'] = $r['yiyuan'];
       $inseart['remark'] = $r['remark'];
       $inseart['points_x'] = $r['points_x'];
       $inseart['points_y'] = $r['points_y'];
       $inseart['cdate'] = strtotime($r['cdate']);
       $this->db->insert('topic_pics_extra', $inseart);
		}

	}
	//add topic
	public function addTopic($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->uid) {
				$result['ustate'] = '000';
				if (strlen(strip_tags(trim($this->input->post('content')))) > 2) {
					$datas = array ();
					$datas['type'] = 16;
					$datas['q_id'] = 0;
					$datas['uid'] = $this->uid;
					//newtime store add time ,ctime publish time
					$datas['ctime'] = $datas['newtime'] = time();
					$enc = md5(trim($this->input->post('content')));
					$result['data'] = $_COOKIE['topic_senddata'];
					if ($_COOKIE['topic_senddata'] == $enc) {
						$result['state'] = '012';
						$result['notice'] = '话题重复发送！';
						echo json_encode($result);
						exit;
					} else {
						setcookie('topic_senddata', $enc);
					}
                    //check illegal word
					if(!$this->filter->judge($this->input->post('content'))){
						$result['state'] = '012';
						$result['notice'] = '含有广告等信息！';
						echo json_encode($result);
						exit;
					}
					if (!$this->session->userdata('topic_ctime')) {
						$this->session->set_userdata('topic_ctime', $datas['ctime']);
					}
					elseif (time() - $this->session->userdata('topic_ctime') < 10) {
						$result['state'] = '012';
						$result['notice'] = '话题重复发送！';
						echo json_encode($result);
						exit;
					}
					elseif (time() - $this->session->userdata('topic_ctime') < 120) {
						$result['state'] = '012';
						$result['notice'] = '信息发送间隔2分钟！';
						echo json_encode($result);
						exit;
					}
					//check illegal word
					if(strlen(trim($this->input->post('content')))<5){
						$result['state'] = '012';
						$result['notice'] = '内容太短！';
						echo json_encode($result);
						exit;
					}
					$ttag = str_replace('热门', '', trim($this->input->post('items')));
					$ttag = str_replace('null', '', $ttag);
					$datas['newtime'] = time();
					$datas['tags'] = $ttag;
					$info['title'] = $this->input->post('content');
					$datas['type_data'] = serialize($info);
					//get system info
					$head = $_SERVER['HTTP_USER_AGENT'];
					if ((stristr($head, 'iPhone') and !stristr($head, 'U;')) OR stristr($head, 'ipod')) {
						$datas['wsource'] = 'IOS';
					} else {
						$datas['wsource'] = 'Android';
					}
					if ($this->input->post('opencheck')) {
						$datas['isdel'] = 1;
					}
					if ($this->checkhome($datas['tags'])) {
						$datas['isdel'] = 1;
					}
					$this->db->insert('wen_weibo', $datas);
					$result['weibo_id'] = $this->db->insert_id();
					$this->wen_auth->set_weibo_jifen($datas['uid']);
					$this->addTnum($ttag);
					$result['notice'] = '话题发送成功！';
				} else {
					$result['notice'] = '发送内容太短！';
					$result['state'] = '012';
				}
			} else {
				$result['notice'] = '账户未登入！';
				$result['ustate'] = '001';
			}
		} else {
			$result['notice'] = 'Token错误！';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//check home
	private function checkhome($tags = '') {
		if ($tags) {
			$tags = trim(str_replace(',', '', $tags));
			$this->db->where('name', $tags);
			$this->db->where('type & ', 2);
			$this->db->from('items');
			return $this->db->count_all_results();
		}
		return false;
	}
	//delete topic
	private function delweibo($id = '') {
		if ($id) {
			$this->db->limit(1);
			$this->db->where('weibo_id', $id);
			$this->db->delete('wen_weibo');
		}
	}
	//set topic is on
	private function openweibo($id = '') {
		if ($id) {
			$data = array (
				'isdel' => 0
			);
			$this->db->limit(1);
			$this->db->where('weibo_id', $id);
			$this->db->update('wen_weibo', $data);
		}
	}
	//search with keywords in topic
	public function topicSearch($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($this->input->get('page') and $kw = mysql_real_escape_string(trim($this->input->get('kw')))) {
				$result['state'] = '000';
				$result['data'] = array ();
				$this->db->where('wen_weibo.isdel', 0);
				$this->db->like('wen_weibo.content', $kw);
				$this->db->or_like('wen_weibo.tags', $kw);
				$this->db->or_like('wen_weibo.type_data', $kw);
				$this->db->select('users.alias as showname,users.phone,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments as commentnums,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');
				$this->db->from('wen_weibo');
				$offset = ($this->input->get('page') - 1) * 8;
				$this->db->limit(8, $offset);
				$this->db->join('users', 'users.id = wen_weibo.uid');
				$this->db->order_by("wen_weibo.weibo_id", "desc");
				$tmp = $this->db->get()->result_array();

				foreach ($tmp as $r) {
					$r['showname'] == '' && $r['showname'] = substr($r['phone'], 0, 4) . '***';
					$r['ctime'] = date('Y-m-d', $r['ctime']);
					$dtypd = unserialize($r['type_data']);
					isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
					$r['haspic'] = 0;
					if (!empty ($dtypd) and isset ($dtypd['pic'])) {
						$r['haspic'] = 1;
					}
					$r['thumb'] = $this->profilepic($r['uid'], 2);
					if($this->input->get('width')){
			              $r['images'] = $this->Plist($r['weibo_id']);
			       }
					$result['data'][] = $r;
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//deal item
	private function addTnum($itmes) {
		if (substr($itmes, 0, 1) == ',') {
			$itmes = substr($itmes, 1);
		}
		if (substr($itmes, strlen($itmes) - 1) == ',') {
			$itmes = substr($itmes, 0, strlen($itmes) - 1);
		}
		$sql = "UPDATE items SET  topicnum = topicnum +1 WHERE name in('{$itmes}')";
		$this->db->query($sql);
	}

	//topic add pic
	public function addTopicPic($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->uid) {
				$datas['attachId'] = intval($this->input->post('weibo_id'));
				if (isset ($_FILES['attachPic']['name']) && $_FILES['attachPic']['name'] != '' && $datas['attachId'] != 0) {
					$result['notice'] = '图片成功上传！';
					$datas['savepath'] = date('Y') . '/' . date('m') . '/' . date('d');
					/*switch ($_FILES['attachPic']['type']) {
						case 'image/pjpeg':
						case 'application/octet-stream':
						case 'image/jpeg':
							$ext = '.jpg';
							break;
					    case 'image/x-png':
							$ext = '.png';
							break;
						case 'image/gif':
						    $ext = '.gif';
							break;
						default:
						   $result['state'] = '001';
					       echo json_encode($result);
					       exit;
					}*/
					$ext = '.jpg';
					$datas['name'] = uniqid().rand(1000,9999) . $ext;
					$datas['savepath'] .= '/' . $datas['name'];
					$ptmp = getimagesize($_FILES['attachPic']['tmp_name']);
					if (!$this->remote->cp($_FILES['attachPic']['tmp_name'], $datas['name'], $datas['savepath'], array (
							'width' => 600,
							'height' => 800
						), true)) {
						$this->delweibo($datas['attachId']);
						$result['state'] = '001';
						$result['notice'] = '图片上传失败！';
						echo json_encode($result);
						exit;
					}

					$datas['userId'] = $this->uid;
					$datas['width'] = $ptmp[0];
					$datas['height'] = $ptmp[1];
					$b1 = $datas['width'] / 600;
					$b2 = $datas['height'] / 800;
					if ($b1 >= $b2 and $b1 > 1) {
						$datas['width'] = 600;
						$datas['height'] = intval($datas['height'] / $b1);
					}
					elseif ($b2 >= $b1 and $b2 > 1) {
						$datas['height'] = 800;
						$datas['width'] = intval($datas['width'] / $b2);
					}
					if ($this->input->post('checked')) {
						if ($this->checkhome($datas['tags'])) {
							$result['notice'] = '话题等待审核中';
						} else {
							$this->openweibo($datas['attachId']);
						}
					}
					$datas['info'] = strip_tags($this->input->post('info'));
					$datas['cTime'] = time();
					$datas['type'] = substr($ext, 1);
					$datas['privacy'] = 0;
					$result['pictureid'] = $this->common->insertData('topic_pics', $datas);
					//update
					$udata = array ();
					$this->db->where('weibo_id', $datas['attachId']);
					$tmps = $this->db->get('wen_weibo')->result_array();
					if ($info = unserialize($tmps[0]['type_data'])) {
						if (!isset ($info['pic']['savepath'])) {
							$info['pic']['height'] = $datas['height'];
							$info['pic']['width'] = $datas['width'];
							$info['pic']['savepath'] = $datas['savepath'];
							$udata['type_data'] = serialize($info);
							$this->common->updateTableData('wen_weibo', '', array (
								'weibo_id' => $this->input->post('weibo_id'
							)), $udata);
						}
					}

				} else {
					$result['state'] = '012';
				}
			} else {
				$result['ustate'] = '001';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//add topic pic action,file is file url
	private function addTopicPicAC($weibo_id = '',$picsrc='',$picinfo='',$type_data='',$is_main=false) {
		if ($weibo_id and $picsrc ) {
			$datas['attachId'] = $weibo_id;
			$datas['savepath'] = date('Y') . '/' . date('m') . '/' . date('d');
			$ext = '.jpg';
			$datas['name'] = uniqid().rand(1000,9999). $ext;
			$datas['savepath'] .= '/' . $datas['name'];
			$ptmp = getimagesize($picsrc);
			if (!$this->remote->cp($picsrc, $datas['name'], $datas['savepath'], array (
					'width' => 600,
					'height' => 800
				), true)) {
				return false;
			}

			$datas['userId'] = $this->uid;
			$datas['width'] = $ptmp[0];
			$datas['height'] = $ptmp[1];
			$b1 = $datas['width'] / 600;
			$b2 = $datas['height'] / 800;
			if ($b1 >= $b2 and $b1 > 1) {
				$datas['width'] = 600;
				$datas['height'] = intval($datas['height'] / $b1);
			}
			elseif ($b2 >= $b1 and $b2 > 1) {
				$datas['height'] = 800;
				$datas['width'] = intval($datas['width'] / $b2);
			}

			$datas['info'] = strip_tags($picinfo);
			$datas['cTime'] = time();
			$datas['type'] = substr($ext, 1);
			$datas['privacy'] = 0;
			$result['pictureid'] = $this->common->insertData('topic_pics', $datas);
			//update
			if ($is_main and $datas['width'] and $datas['savepath']) {
				//update
					if ($info = unserialize($type_data)) {
						if (!isset ($info['pic']['savepath'])) {
							$info['pic']['height'] = $datas['height'];
							$info['pic']['width'] = $datas['width'];
							$info['pic']['savepath'] = $datas['savepath'];
							$udata['type_data'] = serialize($info);
							$this->common->updateTableData('wen_weibo', '', array (
								'weibo_id' => $weibo_id), $udata);
						}
					}
			}
			return $result['pictureid'] ;
		}
		return false;
	}
	//comment topic
	public function sendcomment($param = '') {
		$result['state'] = '000';
		error_reporting(E_ALL);
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			$touid = $this->input->post('touid');
			if ($wid = $this->input->post('weibo_id')) {
				if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {
					$target_path = realpath(APPPATH . '../upload');
					if (is_writable($target_path)) {
						if (!is_dir($target_path . '/' . round($wid / 1000))) {
							mkdir($target_path . '/' . round($wid / 1000), 0777, true);
						}

						$datas['name'] = uniqid() . '.jpg';
						$picturesave = round($wid / 1000) . '/' . $datas['name'];
						$target_path = $target_path . '/' . $picturesave;
						move_uploaded_file($_FILES['attachPic']['tmp_name'], $target_path);

						$result['updatePictureState'] = '000';
						$upicArr[0]['type'] = 'jpg';
						$upicArr[0]['path'] = $picturesave;
						$upicArr[0]['uploadTime'] = time();
						$data['data'] = serialize($upicArr);
					}
				}
				$data['type'] = 'topic';
				$data['status'] = 1;
				$data['contentid'] = $wid;
				$data['comment'] = $this->filter->filts($this->input->post('comment'));
				$data['fuid'] = $this->uid;
				$data['touid'] = $touid;
				$data['cTime'] = time();
				$this->db->insert('wen_comment', $data);
				$this->aplus($wid, 1);
				//update topic
				if ($data['type'] == 'topic') {
					$updata = array ();
					$updata['newtime'] = time();
					$this->db->query("UPDATE  `wen_weibo` SET  `newtime` =  '{$updata['newtime']}',`commentnums` =  `commentnums`+1,comments=comments+1 WHERE `weibo_id` = {$wid} AND `type` = 1");
				}
			} else {
				$result['state'] = '012';
			}

		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function topicListWithOrder($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($this->input->get('page')) {
				if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
					$result['state'] = '000';
					$result['data'] = $this->getHN($this->input->get('order'));
					$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
				}else{
					$result = array();
					$result = unserialize($rs);
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//get new and hot topics
	private function getHN($type = 1) {
	    $this->db->from('wen_weibo');
		$this->db->where('wen_weibo.type != ', 4);
		$this->db->where('wen_weibo.isdel', 0);
		$offset = ($this->input->get('page') - 1) * 8;
		$this->db->limit(8, $offset);
		$this->db->join('users', 'users.id = wen_weibo.uid');
	//	$this->db->join('wen_comment', 'wen_comment.contentid = wen_weibo.weibo_id');
		$this->db->select('wen_weibo.weibo_id,users.alias as uname,users.phone,wen_weibo.views,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

		if ($type == 1) {
			  $this->db->order_by('wen_weibo.newtime desc');
		} else {
			//$this->db->select('users.alias as uname,users.phone,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

            $this->db->order_by("wen_weibo.hots", "desc");
            $this->db->order_by("wen_weibo.comments", "desc");
		}

		$tmp = $this->db->get()->result_array();
		$res = array ();

		foreach ($tmp as $r) {
			$r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
			if (preg_match('/^\\d+$/', $r['uname'])) {
				$r['uname'] = substr($r['uname'], 0, 4) . '***';
			}
			$r['ctime'] = date('Y-m-d', $r['ctime']);
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
			unset ($r['type_data']);
			$res[] = $r;
		}
		return $res;

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
	/**  发起的话题
	 * @param string $param,9=>Q+add topic
	 */
	public function topicList($param = '') {
		//error_reporting(E_ALL);
		$result['state'] = '000';
		$type = mysql_real_escape_string($this->input->get('type'));
		$pid = $this->getChild($type);
		$tmpitem = array();
		if($pid){
			$sqlItem = "select name from items where pid = '{$pid}'";
			$citems = $this->db->query($sqlItem)->result_array();
			$typeSql = '';

			if(!empty($citems)){
				foreach($citems as $item){
					$typeSql .= " (w.dataType like '%".$item['name']."%' OR w.tags like '%".$item['name']."%') OR";
					$tmpitem[] = $item['name'];
				}
			}
		}

		$sqltmp = substr($typeSql,0,strlen($typeSql)-2);
		
		$sql = "SELECT w.comments,w.content, w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
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
		$lastid = intval($this->input->get('lastid'))?intval($this->input->get('lastid')):0;
		if($lastid > 0){
			if($this->input->get('direction') == 'down'){
				$sql .= " AND w.weibo_id < {$lastid}";
			}else{
				$sql .= " AND w.weibo_id > {$lastid}";
			}
		}
		$sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.ctime DESC ";
		if ($this->input->get('page')) {
			$start = ($this->input->get('page') - 1) * 10;
			$sql .= " LIMIT 10 ";
		} else {
			$sql .= " LIMIT 0,10 ";
		}
		$tmp = $this->db->query($sql)->result_array();
		//echo $this->db->last_query();exit();
		$result['data'] = array ();
		if (!empty ($tmp)) {
			foreach ($tmp as $row) {
				$info = unserialize($row['type_data']);
				isset ($info['title']) && $row['content'] = $info['title'];
				unset ($row['type_data']);
				$row['title'] = $info['title'];
				$row['thumb'] = $this->profilepic($row['uid'], 2);
				$row['content'] = $row['content'];
				$row['hasnew'] = $row['commentnums'];
				if(!empty($row['tags'])){
					$row['tag'] = explode(',',$row['tags']);
					unset($row['tags']);
					foreach($row['tag'] as $item){
						if($item && in_array($item,$tmpitem)){
							$row['tags'][] = $item;
						}
					}
				}
				if(empty($row['tags'])){
					$row['tags'] = array();
				}
				unset($row['tag']);
				//$row['tags'] = explode(',',$row['tags']);
				$row['showname'] = $this->GName($row['alias'],$row['phone']);
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
				if ($this->uid != $row['uid']) {
					$row['hasnew'] = 0;
				}
				//if($this->input->get('width')){
				$row['images'] = $this->Plist($row['weibo_id']);
				//}
				$result['data'][] = $row;
			}
			$result['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
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
    //deltete topic
	function del($param = '') {
		$result['state'] = '000';
		$result['notice'] = '话题删除成功！';
		if ($this->auth->checktoken($param)) {
			$result['delState'] = '001';
			if ($this->uid and $wid = intval($this->input->post('weibo_id'))) {
				$condition = array (
					'uid' => $this->uid,
					'weibo_id' => $wid
				);
				$this->common->deleteTableData('wen_weibo', $condition);

				$condition = array (
					'contentid' => $wid,
					'type' => 'topic'
				);
				$this->common->deleteTableData('wen_comment', $condition);
				$this->db->where('attachId', $wid);
				$tmp = $this->db->get('topic_pics')->result_array();

				foreach ($tmp as $r) {
					$this->remote->del($r['savepath']);
				}
				$condition = array (
					'attachId' => $wid
				);
				$this->common->deleteTableData('topic_pics', $condition);

				$result['delState'] = '000';

			} else {
				$result['notice'] = '信息不全！';
				$result['state'] = '012';
			}
		} else {
			$result['notice'] = 'Token错误！';
			$result['state'] = '001';
		}
		echo json_encode($result);

	}

	//回复的话题列表
	public function rtopicList($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			$sql = "SELECT wen_weibo.content,wen_comment.new_reply,wen_weibo.comments,wen_weibo.commentnums,wen_weibo.favnum,wen_weibo.type_data,wen_weibo.weibo_id,wen_weibo.ctime";
			$sql .= ' FROM wen_comment LEFT JOIN wen_weibo ON wen_comment.contentid=wen_weibo.weibo_id';

			if ($uid = intval($this->input->get('uid'))) {
				$sql .= ' WHERE wen_comment.type="topic" and wen_weibo.isdel=0 AND wen_comment.fuid = ' . $uid;
			}

			$sql .= "  GROUP BY wen_weibo.weibo_id ORDER BY wen_weibo.weibo_id DESC ";
			if ($this->input->get('page')) {
				$start = ($this->input->get('page') - 1) * 10;
				$sql .= " LIMIT $start,10 ";
			} else {
				$sql .= " LIMIT 0,10";
			}
			$tmp = $this->db->query($sql)->result_array();
			$result['data'] = array ();
			if (!empty ($tmp)) {
				foreach ($tmp as $row) {
					$info = unserialize($row['type_data']);
					if (isset ($info[1]['id']) OR isset ($info['pic'])) {
						$row['haspic'] = 1;
					} else {
						$row['haspic'] = 0;
					}
					unset ($row['type_data']);
					$row['title'] = $info['title'];
					$row['ctime'] = date('Y-m-d', $row['ctime']);
					$result['data'][] = $row;
				}
				$result['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	public function zan($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($wid = $this->input->post('weibo_id')) {
				$this->db->query("UPDATE wen_weibo SET zan=zan+1 WHERE weibo_id = {$wid} LIMIT 1");
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);

	}

	public function flow($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			
			if (($tags = trim(strip_tags($this->input->get('tags')))) and $page = intval($this->input->get('page'))) {
				if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
					$start = $page -1 <= 0 ? 0 : ($page -1) * 15;
					$ctime = time();
					$fixcondition = '';
					$width = $this->input->get('width') ? $this->input->get('width') : 200;

					if ($this->input->get('ctime') and $page > 1) {
						$fixcondition = ' AND ctime <= ' . intval($this->input->get('ctime'));
					}
					if(false and  $start>0){
	                   $SQL = "SELECT  zan,weibo_id,type_data,content,ctime FROM wen_weibo INNER JOIN( SELECT weibo_id,(ctime+weight) as vaoc FROM wen_weibo WHERE INSTR(tags,'{$tags}')";
					   $SQL .= " AND type&25 AND INSTR(type_data,'savepath') AND ctime<={$ctime} AND isdel=0 {$fixcondition} ORDER BY vaoc DESC LIMIT $start,15) as lim USING(weibo_id)";
					}else{
						$SQL = "SELECT (ctime+weight) as vaoc, zan,weibo_id,type_data,content,ctime FROM wen_weibo WHERE INSTR(tags,'{$tags}')";
					    $SQL .= " AND type&25 AND INSTR(type_data,'savepath') AND ctime<={$ctime} AND isdel=0 {$fixcondition} ORDER BY vaoc DESC LIMIT $start,15";
					}
					//$result['sql'] = $$SQL;
					$tmps = $this->db->query($SQL)->result_array();
	            //    $result['SQL'] = $SQL;
					$result['data'] = array ();
					foreach ($tmps as $r) {
						$dtypd = unserialize($r['type_data']);
						$url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);
						
						$arr_url = explode('/',$url);
						if(isset($arr_url[1])){
							$url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x320/',$url);
						}
						$t['url'] = $this->remote->show320($url, $width);
						if (!isset ($dtypd['pic']['height'])) {
							$psize = getimagesize($t['url']);
							if($t['height'] = $psize[1]){
								$t['width'] = $psize[0];
							}else{
								$t['height'] = 260;
								$t['width'] = 200;
							}

							$this->UdatePic($r['weibo_id'], $psize);
						} else {
							if($t['height'] = $dtypd['pic']['height']){
								$t['width'] = $dtypd['pic']['width'];
							}else{
								$t['height'] = 260;
								$t['width'] = 200;
							}
						}
						$t['ctime'] = time();
						$t['zan'] = $r['zan'];
						$t['weibo_id'] = $r['weibo_id'];
						$t['content'] = $r['content'] . $dtypd['title'];
						unset ($r['type_data']);
						$result['data'][] = $t;
					}
					$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
				}else{
					$result = array();
					$result = unserialize($rs);
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}

		echo json_encode($result);

	}
	private function UdatePic($id, $size = array ()) {
		$this->db->limit(1);
		$this->db->where('weibo_id', $id);
		$tmp = $this->db->get('wen_weibo')->result_array();
		$type_data = unserialize($tmp[0]['type_data']);
		if ($type_data['pic']['savepath']) {
			$type_data['pic']['height'] = $size[1];
			$type_data['pic']['width'] = $size[0];
			$data['type_data'] = serialize($type_data);
			$this->db->where('weibo_id', $id);
			$this->db->limit(1);
			$this->db->update('wen_weibo', $data);

		}
	}
	public function tags($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($type = trim($this->input->get('type'))) {
				$this->db->where('type & ', $type);
				$this->db->select('name, id');
				$this->db->order_by("order", "desc");
				$result['data'] = $this->db->get('items')->result_array();
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);

	}
	public function comments($param = '') {
		$result['state'] = '000';
		//if ($this->auth->checktoken($param)) {
			if ($wid = $this->input->get('weibo_id')) {
				if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
					$offset = (intval($this->input->get('page')) - 1) * 10;
					$this->db->limit(10, $offset);
					$this->db->where('wen_comment.contentid', $wid);
					$this->db->where('wen_comment.type', 'topic');
					$this->db->from('wen_comment');
					$this->db->select('wen_comment.comment,wen_comment.alias,wen_comment.cTime,wen_comment.data,users.email,users.phone');
					$this->db->join('users', 'users.id = wen_comment.fuid', 'left');
					$tmp = $this->db->get()->result_array();

					$this->db->where('wen_comment.contentid', $wid);
					$this->db->where('wen_comment.type', 'topic');
					$this->db->from('wen_comment');
					$this->db->select('wen_comment.id');
					$this->db->join('user_profile', 'user_profile.user_id = wen_comment.fuid', 'left');
					$num = $this->db->get()->num_rows();
					$result['comments'] = array ();
					if (!empty ($tmp)) {
						foreach ($tmp as $row) {
							if ($row['data'] != '') {
								$extra = unserialize($row['data']);
								$row['picture'] = site_url() . 'upload/' . $extra[0]['path'];
								$row['haspic'] = '1';
							} else {
								$row['haspic'] = '0';
							}
							$row['Fname'] = !empty ($row['email']) ? substr($row['email'], 0, 4) . '***' : (!empty ($row['phone']) ? substr($row['phone'], 0, 4) . '***' : '');
							$row['Fname'] == '' && $row['Fname'] = $row['alias'];
							unset ($row['phone']);
							unset ($row['email']);
							$row['cTime'] = date('Y-m-d', $row['cTime']);
							$result['comments'][] = $row;
						}
					}
					$result['total'] = $num;
					$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
				}else{
					$result = array();
					$result = unserialize($rs);
				}
			} else {
				$result['state'] = '012';
			}
		//} else {
		//	$result['state'] = '001';
	//	}
		echo json_encode($result);

	}
  // get getExtras links
   private function getExtras($ids=''){
   	  $idarr = explode(',',$ids);
   	  $res = array();
   	  if(!empty($idarr)){
   	  	$sec = '';
   	  	 foreach($idarr as $r){
   	  	 	if($r){
               $sec.=','.$r;
   	  	 	}
   	  	 }
   	  	 $sec = substr($sec,1);
   	  	 $sql = "select * from product_promotion where id in($sec)";
   	  	 $tmp = $this->db->query($sql)->result_array();
   	  	 foreach($tmp as $r){
   	  	 	$r['price'] = number_format($r['price']);
   	  	 	$r['market_price'] = number_format($r['market_price']);
   	  	 	$r['image'] = $this->remote->show($r['image'], 300);
            $res[] = $r;
   	  	 }
   	  }
   	  return $res;
   }
	//get tehui lists
   private function getTehui($ids=''){
   	  $idarr = explode(',',$ids);
   	  $res = array();
   	  if(!empty($idarr)){
   	  	$sec = '';
   	  	$this->load->library('tehui');
   	  	 foreach($idarr as $r){
   	  	 	if($r){
               $sec.=','.$r;
   	  	 	}
   	  	 }
   	  	 $sec = substr($sec,1);
   	  	 $sql = "select id,title,team_price as price,market_price ,summary,image from team where id in($sec)";
   	  	 $tmp = $this->tehui->q($sql);
   	  	 foreach($tmp as $r){
   	  	 	$r['price'] = number_format($r['price']);
   	  	 	$r['market_price'] = number_format($r['market_price']);
   	  	 	$r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'];
            $res[] = $r;
   	  	 }
   	  }
   	  return $res;
   }
	//add and plus comment num
	private function aplus($contentid, $pls) {
		if ($pls > 0) {
			$sql = "UPDATE wen_weibo SET hasnew=hasnew+1 WHERE weibo_id = {$contentid} LIMIT 1";
			$this->db->query($sql);
		}
		elseif ($pls < 0) {
			$this->db->where('weibo_id', $contentid);
			$this->db->select('title, content, date');
			$this->db->limit(1);
			$res = $this->db->get('wen_weibo')->result_array();
			if ($res[0]['hasnew'] > 0) {
				$sql = "UPDATE wen_weibo SET hasnew=hasnew-1 WHERE weibo_id = {$contentid} LIMIT 1";
				$this->db->query($sql);
			}
		} else {
			$sql = "UPDATE wen_weibo SET hasnew=0 WHERE weibo_id = {$contentid} LIMIT 1";
			$this->db->query($sql);
		}
		return true;
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
}
?>
