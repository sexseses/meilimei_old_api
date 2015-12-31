<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class topic extends MY_Controller {
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
        $this->load->model('Diary_model');
        $this->load->model('Score_model');

	}

	// topic detail info api
	function view($param = '') {

		$result['state'] = '000';
		if ($wid = $this->input->get('weibo_id')) {
			//if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
				$this->aplus($wid, 0);
				$this->db->where('wen_weibo.weibo_id', $wid);
				$this->db->where('wen_weibo.type & ', 25);
				$this->db->from('wen_weibo');
				$this->db->join('users', 'users.id = wen_weibo.uid');
				$this->db->join('user_profile', 'user_profile.user_id = wen_weibo.uid');
				$this->db->select('user_profile.city,users.alias,users.jifen, users.daren, users.phone,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.weibo_id,wen_weibo.extra_ids,wen_weibo.tags,wen_weibo.product_data,wen_weibo.uid,wen_weibo.type,wen_weibo.ctime,wen_weibo.q_id,wen_weibo.content,wen_weibo.type_data,wen_weibo.commentnums,wen_weibo.favnum,wen_weibo.views,wen_weibo.zan,wen_weibo.video,wen_weibo.videoHeight,wen_weibo.tehui_ids,wen_weibo.comments as commentnum');
				$tmp = $this->db->get()->result_array();
				//get pic
				$this->db->where('wen_weibo.q_id', $tmp[0]['q_id']);
				$this->db->where('wen_weibo.type', 4);
				$this->db->from('wen_weibo');
				$this->db->select('wen_weibo.type_data,wen_weibo.favnum');
				$ptmp = $this->db->get()->result_array();
				$result['data'] = array ();
				
				if (!empty ($tmp)) {

                    if($tmp[0]['top_start'] <= time() && $tmp[0]['top_end'] >= time()){
                        $result['data']['top'] = 1;
                    }else{
                        $result['data']['top'] = 0;
                    }

                    if($tmp[0]['chosen_start'] <= time() && $tmp[0]['chosen_end'] >= time()){
                        $result['data']['chosen'] = 1;
                    }else{
                        $result['data']['chosen'] = 0;
                    }

                    if($tmp[0]['hot_start'] <= time() && $tmp[0]['hot_end'] >= time()){
                        $result['data']['hot'] = 1;
                    }else{
                        $result['data']['hot'] = 0;
                    }
					$result['data']['zhenrenxiiu'] = strpos($tmp[0]['tags'], '人秀') ? 1 : 0;
					$result['data']['qid'] = $tmp[0]['q_id'];
					//$result['data']['videoHeight'] = $tmp[0]['videoHeight'];
					//$result['data']['video'] = $tmp[0]['video']?$this->showVideo($wid):'';
                    $result['data']['daren'] = $tmp[0]['daren'];
                    $result['data']['jifen'] = isset($tmp[0]['jifen'])?$tmp[0]['jifen']:0;
					$result['data']['city'] = $tmp[0]['city'];
                    $result['data']['sex'] = 1;
                    $result['data']['level'] = $this->getLevel($tmp[0]['jifen']);
					$result['data']['uid'] = $tmp[0]['uid'];
                    $result['data']['video'] = $tmp[0]['video'];
                    $result['data']['uname'] = $this->GName($tmp[0]['alias'],$tmp[0]['phone']);
					$result['data']['thumb'] = $this->profilepic($tmp[0]['uid'], 2);
                    $result['data']['content'] = '';
                    $rs = $this->Diary_model->get_user_by_username($tmp[0]['uid']);
                    $item ['basicinfo'] = $this->getBasicInfo($rs[0]);
					//$result['data']['isfav']  = $this->isfav($tmp[0]['weibo_id'],'topic');
					$result['data']['ctime'] = date('Y-m-d', $tmp[0]['ctime']);
					if ($tmp[0]['type_data']) {
						$info = unserialize($tmp[0]['type_data']);
						$result['data']['title'] = empty($info['title'])?'':$info['title'];
					}else{
						$result['data']['title'] = empty($info['title'])?'':$info['title'];
					}
					$num = ($this->getZan($tmp[0]['weibo_id'])>0)?$this->getZan($tmp[0]['weibo_id']):0;
					
					$result['data']['zan'] = $num;
					//$result['data']['favnum'] = $tmp[0]['favnum'];
					//$result['data']['views'] = $tmp[0]['views'] ;
					$result['data']['commentnum'] = $tmp[0]['commentnum'];
                    $result['data']['pageview'] = intval($tmp[0]['views']);
					if(!empty($tmp[0]['tags'])){
						$row['tag'] = explode(',',$tmp[0]['tags']);
						unset($row['tags']);
						foreach($row['tag'] as $item){
							if(!empty($item)){
								$result['data']['tags'][] = $item;
                                $arr = array();
                                $arr['tag'] = $item;
                                $itemid = $this->Diary_model->getItemId($item);
                                $arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
                                $result['data']['tagss'][] = $arr;
							}
						}
					}
                    if(empty($result['data']['tagss'])){
                        $result['data']['tagss'] = array();
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

                            if(intval($arr_url[1]) >= 3 && intval(date('Y')) <= $arr_url[0]){
                                if(isset($arr_url[1])){
                                    $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x1080/',$ptmp['0']['savepath']);
                                }
                                //$t['url'] = $this->remote->show320($url, $width);

                                if(empty($ptmp[0]['imgfile'])) {

                                    $result['data']['url'] = $this->remote->getLocalImage($arr_url[1], 640);
                                }else{
                                    $result['data']['url'] = $this->remote->getQiniuImage($ptmp[0]['imgfile'], 640);
                                }
                            }else{
                                if(isset($arr_url[1])){
                                    $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x640/',$ptmp['0']['savepath']);
                                }
                                //$t['url'] = $this->remote->show320($url, $width);

                                $result['data']['url'] = $this->fileUrl.$ptmp[0]['imgfile'];//$this->remote->show($ptmp['0']['savepath'], $width);
                            }
                        }
					}
					elseif ($tmp[0]['type'] == 8) {
						$pictmp = unserialize($tmp[0]['type_data']);
						
						if (isset ($pictmp['pic'])) {
							$result['data']['haspic'] = "1";
							$arr_url = explode('/',$pictmp['pic']['savepath']);

                            if(intval($arr_url[1]) >= 3 && intval(date('Y')) <= $arr_url[0]){
                                if(isset($arr_url[1])){
                                    $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x1080/',$pictmp['pic']['savepath']);
                                }

                                if(empty($ptmp[0]['imgfile'])) {

                                    $result['data']['url'] = $this->remote->getLocalImage($arr_url[1], 640);
                                }else{
                                    $result['data']['url'] = $this->remote->getQiniuImage($ptmp[0]['imgfile'], 640);
                                }
                            }else{
                                if(isset($arr_url[1])){
                                    $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x640/',$pictmp['pic']['savepath']);
                                }

                                $result['data']['url'] = $this->remote->getQiniuImage($ptmp[0]['imgfile'], 640);//$this->remote->show($pictmp['pic']['savepath'], $width);
                            }
                        }
					}
					elseif ($tmp[0]['type'] == 16) {
						$pictmp = unserialize($tmp[0]['type_data']);
                        $result['data']['haspic'] = "1";
                        $result['data']['mutilPic'] = "1";
                        $result['data']['images'] = $this->Plist($wid);
						/*if (isset ($pictmp['pic']) OR isset ($pictmp['savepath'])) {
							$result['data']['haspic'] = "1";
							$result['data']['mutilPic'] = "1";
							$result['data']['images'] = $this->Plist($wid);
						}*/
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

					$result['data']['doctor']= $this->getAns($result['data']['qid'])?$this->getAns($result['data']['qid']):array();
					$this->db->query("update wen_weibo set views=views+1 where weibo_id = '$wid'");
                    $this->db->query("update wen_weibo set pageview=pageview+1 where weibo_id = '$wid'");
				}
			/*	$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
			}else{
                $this->db->query("update wen_weibo set pageview=pageview+1 where weibo_id = '$wid'");
				$result = array();
				$result = unserialize($rs);	
			}*/
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}
	private function isfav($param = '') {


		if (($contentid) and $this->uid) {
			$condition = array (
				'type' => $type, 'uid' => $this->uid, 'contentid' => $contentid);

			if ($this->db->get_where('wen_favrite', $condition)->num_rows() > 0) {
				$result['isfav'] = '0';
			} else {
				$result['isfav'] = '1';
			}
		}else{
			$result['isfav'] = 0;
		}

		return $result['isfav'];
	}

	//add favorite info
	function addzan($param = '') {

        $contentid = $this->input->get('contentid');
        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';

        if($contentid && $this->uid){

            $rs =  $this->Diary_model->getMyZanTopic($contentid);
            $this->db->where('touid', $rs[0]['touid']);
            $this->db->where('type','topic');
            $num = $this->db->get('wen_zan')->num_rows();

            if($num == 10){
                $result['data']['score'] = $this->Score_model->addScore(60,$rs[0]['touid']);
            }else if($num == 50){
                $result['data']['score'] = $this->Score_model->addScore(61, $rs[0]['touid']);
            }else if($num == 100){
                $result['data']['score'] = $this->Score_model->addScore(62, $rs[0]['touid']);
            }

            $isZan = $this->Diary_model->isZan($this->uid, $contentid);
            if(!$isZan) {
                $result['data']['zan'] = $this->Zan($contentid, $this->uid);
            }else{
                $result['data']['zan'] = $this->getZan($contentid);
            }
        }else{
            $result['state'] = '012';
            $result['notice'] = '未登录！';
        }

        echo json_encode($result);
	}

	function unzan($param = '') {
        $contentid = $this->input->get('contentid');

        $contentid = $contentid ? $contentid :0;
        $result['state'] = '000';

        if($contentid && $this->uid){
            $result['data']['zan'] = $this->cancelZan($contentid, $this->uid);
        }else{
            $result['state'] = '012';
        }

		echo json_encode($result);
	}

    public function cancelZan($contentid, $uid=0){

        if(intval($contentid) < 0){
            return ;
        }

        if(intval($uid) < 0){
            return ;
        }

        $this->db->where('weibo_id', $contentid);
        $tmp = $this->db->get('wen_weibo')->result_array();


        $this->db->query("delete from wen_zan where type='topic' and contentid='{$contentid}' and uid='{$uid}' limit 1");

        $where_condition = array (
            'type' => 'topic',
            'contentid' => $contentid);

        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;
        return $num + intval($tmp[0]['zan']);
    }

    private function Zan($contentid, $uid){

        $this->db->where('weibo_id', $contentid);
        $tmp = $this->db->get('wen_weibo')->result_array();

        $rs = $this->getMyZanTopic($contentid);
        $touid = 0;
        if(!empty($rs)){
            $touid = $rs[0]['uid'];
        }else{
            $rs = array();
            $rs = $this->getDiaryUser($contentid);
            $touid = $rs[0]['uid'];
        }
        $data = array (
            'type' => 'topic',
            'contentid' => $contentid,
            'uid' => $uid,
            'touid' => $touid,
            'cTime' => time());
        $this->db->insert('wen_zan', $data);
        $where_condition = array (
            'type' => 'topic',
            'contentid' => $contentid);

        $num = $this->db->get_where('wen_zan', $where_condition)->num_rows();

        $num = $num?$num:0;
        return $num + intval($tmp[0]['zan']);
    }

    private function getDiaryUser($contentid){

        if(intval($contentid) < 0)
            return ;

        $this->db->select('uid');
        $this->db->where('nid',$contentid);
        return $this->db->get('note')->result_array();
    }
    private function getMyZanTopic($tid, $offset=0, $limit=10){
        if(intval($tid) < 0){
            return;
        }

        $this->db->select('content, uid, type_data');
        $this->db->where('weibo_id', $tid);
        $this->db->limit($limit, $offset);
        return $this->db->get('wen_weibo')->result_array();
    }


	function iszan($param = '') {
		$result['state'] = '000';

		if (($contentid = $this->input->get('contentid')) and $this->uid) {
			$condition = array (
				'type' => $this->input->get('type'
			), 'uid' => $this->uid, 'contentid' => $contentid);

			if ($this->db->get_where('wen_zan', $condition)->num_rows() > 0) {
				$result['iszan'] = '1';
			} else {
				$result['iszan'] = '0';
			};
		} else {
			$result['ustate'] = '001';
			$result['state'] = '012';
		}

		echo json_encode($result);
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
	//is repeate

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
				$tmp[0]['fuid'] = $tmp[0]['uid'];

                $result[] = $tmp[0];
				$id = $tmp[0]['id'];
				$this->db->query("UPDATE `wen_answer` SET `new_comment` = 0   WHERE `id` ={$id}");
			} else {
				$tmp = $this->db->query("SELECT {$fields} FROM wen_answer LEFT JOIN users  ON users.id = wen_answer.uid LEFT JOIN user_profile ON user_profile.user_id = wen_answer.uid WHERE wen_answer.qid = {$qid} AND is_talk=0 GROUP BY wen_answer.uid  order by wen_answer.id DESC")->result_array();

				foreach ($tmp as $row) {
					$row['cdate'] = date('Y-m-d', $row['cdate']);
					$row['thumb'] = $this->profilepic($row['uid'], 1);
                    $row['fuid'] = $row['uid'];
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
			$arr_url = explode('/',$r['savepath']);
			$url = '';
            if(substr($r['savepath'],strlen($r['savepath'])-4) == '.mp4'){
                $r['vedio'] = $r['savepath'];
                $r['savepath'] = '';
            }else {
                if (intval($arr_url[1]) >= 3 && intval(date('Y')) <= $arr_url[0]) {

                    if (isset($arr_url[1])) {

                        $url = str_replace('/' . $arr_url[1] . '/', '/' . $arr_url[1] . 'x1080/', $r['savepath']);
                    }

                    //echo $this->remote->show320($url, $width);
                    $r['savepath'] = $this->remote->show800($url, $width);
                    $r['vedio'] = '';
                } else {
                    if (isset($arr_url[1])) {

                        $url = str_replace('/' . $arr_url[1] . '/', '/' . $arr_url[1] . 'x640/', $r['savepath']);
                    }

                    $r['savepath'] = $this->remote->show320($url, $width);
                    $r['vedio'] = '';
                }
            }
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

    private function isItem($p){

        if(empty($p)){
            return 0;
        }

        $this->db->where('name',$p);
        $this->db->from('new_items');
        $num = $this->db->count_all_results();

        if($num > 0){
            return $num;
        }else{
            return 0;
        }
    }
	//add topic with pictrues V2
	public function addTopicWithPics($param = '') {
		$result['state'] = '000';

		if ($this->uid) {
			$result['ustate'] = '000';

            $ttag = str_replace('热门', '', trim($this->input->post('items')));
            $ttag = str_replace('null', '', $ttag);
            $isItem = $this->isItem($ttag);
            $d = 0;
            if(!$isItem){
                $d = $this->db->insert('new_items',array('pid'=>362,'name'=>$ttag));
            }
            $result['d']=$d;
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
				$datas['isdel'] = 0;
				$datas['vaoc'] = $datas['ctime'];
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
                    $result['data']['score'] = $this->Score_model->addScore(55,$this->uid);

				}else{
                    $result['data']['score'] = $this->Score_model->addScore(54,$this->uid);
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
				$datas['views'] = rand(10,50);
				$this->db->insert('wen_weibo', $datas);
				$result['weibo_id'] = $this->db->insert_id();
				$this->wen_auth->set_weibo_jifen($datas['uid']);
				$this->addTnum($ttag);
				$result['data']['weibo_id']=$result['weibo_id'];
				$result['notice'] = '话题发送成功！';
			} else {
				$result['notice'] = '发送内容太短！';
				$result['state'] = '012';
			}
		} else {
			$result['notice'] = '账户未登入！';
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}
	//check home
	private function checkhome($tags = '') {
		if ($tags) {
			$tags = trim(str_replace(',', '', $tags));
			$this->db->where('name', $tags);
			$this->db->where('type = ', 2);
			$this->db->from('new_items');
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

		if ($this->input->get('page') and $kw = mysql_real_escape_string(trim($this->input->get('kw')))) {
			$result['state'] = '000';
			$result['data'] = array ();
			$this->db->where('wen_weibo.isdel', 0);
			$this->db->like('wen_weibo.content', $kw);
			$this->db->like('wen_weibo.tags', $kw);
			$this->db->or_like('wen_weibo.type_data', $kw);
			$this->db->select('users.jifen, users.age, users.city , users.alias as showname,wen_weibo.tags as tags,users.phone,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments as commentnums,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');
			$this->db->from('wen_weibo');
			$offset = ($this->input->get('page') - 1) * 8;
			$this->db->limit(8, $offset);
			$this->db->join('users', 'users.id = wen_weibo.uid');
			$this->db->order_by("wen_weibo.weibo_id", "desc");
			$tmp = $this->db->get()->result_array();

			foreach ($tmp as $r) {

                if($r['top_start'] <= time() && $r['top_end'] >= time()){
                    $r['top'] = 1;
                }else{
                    $r['top'] = 0;
                }

                if($r['chosen_start'] <= time() && $r['chosen_end'] >= time()){
                    $r['chosen'] = 1;
                }else{
                    $r['chosen'] = 0;
                }

                if($r['hot_start'] <= time() && $r['hot_end'] >= time()){
                    $r['hot'] = 1;
                }else{
                    $r['hot'] = 0;
                }
                if($this->uid) {
                    if ($this->Diary_model->getstate($r['uid'],$this->uid)) {
                        $r['follow'] = 1;
                    } else {
                        $r['follow'] = 0;
                    }

                }else{
                    $r['follow'] = 0;
                }
                $r['age'] = $this->getAge(intval($r['age']));
                $r['city'] = empty($r['city'])?$r['city']:'';
				$r['showname'] == '' && $r['showname'] = substr($r['phone'], 0, 4) . '***';
				$r['ctime'] = date('Y-m-d', $r['ctime']);
                $r['zanNum'] = ($this->getZan($r['weibo_id']) > 0) ? $this->getZan($r['weibo_id']) : 0;
                if(intval($this->uid) > 0) {
                    $iszan = $this->isAtZan($this->uid, $r['weibo_id']);
                    if ($iszan) {
                        $r['isZan'] = 1;
                    } else {
                        $r['isZan'] = 0;
                    }
                }else{
                    $r['isZan'] = 0;
                }
                $r['level'] = $this->getLevel($r['jifen']);
				$dtypd = unserialize($r['type_data']);
				isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
				$r['haspic'] = 0;
				if (!empty ($dtypd) and isset ($dtypd['pic'])) {
					$r['haspic'] = 1;
				}
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
				$r['thumb'] = $this->profilepic($r['uid'], 2);
                //if($this->input->get('width')){
		        $r['images'] = $this->Plist($r['weibo_id']);
		       //}
                unset($r['type_data']);
				$result['data'][] = $r;
			}

		} else {
			$result['state'] = '012';
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
		if (!$this->notlogin) {
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
                $result['debug2'] = 1;
				$this->db->insert('wen_comment', $data);
                $result['debug3'] = 1;

				$this->aplus($wid, 1);
                $result['debug1'] = $touid;

                $this->db->where('wen_comment.touid', $touid);
                $num = $this->db->get('wen_comment')->num_rows();

                if($num == 10){
                    $result['data']['score'] = $this->Score_model->addScore(57,$touid);
                }else if($num == 50){
                    $result['data']['score'] = $this->Score_model->addScore(58,$touid);
                }else if($num == 100){
                    $result['data']['score'] = $this->Score_model->addScore(59,$touid);
                }
                $result['data']['score'] = $this->Score_model->addScore(64,$this->uid);

				//update topic
				if ($data['type'] == 'topic') {
                    $result['debug4'] = 1;
					$updata = array ();
					$updata['newtime'] = time();
					$this->db->query("UPDATE  `wen_weibo` SET  `newtime` =  '{$updata['newtime']}',`commentnums` =  `commentnums`+1,comments=comments+1 WHERE `weibo_id` = {$wid} AND `type` = 1");
                    $result['debug5'] = 1;
                }
                $result['debug6'] = 1;
			} else {
				$result['state'] = '012';
			}

		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function topicListWithOrder($param = '') {

		if ($this->input->get('page')) {
                $uid = $this->uid?$this->uid:0;
				$result['state'] = '000';
            //order等于3是推荐
            if($this->input->get('order') == 3){
               // if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
                    $result['data'] = $this->getHN3($this->input->get('order'), $uid);
               //     $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
               /* }else{
                    $result = array();
                    $result = unserialize($rs);
                }*/
            }else if($this->input->get('order') == 2) {
                //order等于2是精选
                if($this->input->get('page') == 1)
                    /*if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {*/
                        $result['data'] = array_merge($this->getHN4(2,10,$uid),$this->getHN(1, $uid));
                    /*    $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
                    }else{
                        $result = array();
                        $result = unserialize($rs);
                    }*/
                else{
                    //if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
                        $result['data'] = $this->getHN(1, $uid);
                        //$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
                    /*}else{
                        $result = array();
                        $result = unserialize($rs);
                    }*/
                }
            }else if($this->input->get('order') == 'web'){
                if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))) {
                    $result['data'] = $this->getHNWeb(3, $uid);
                    $this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
                }else{
                    $result = array();
                    $result = unserialize($rs);
                }
            }
		} else {
			$result['state'] = '012';
		}
        if(empty($result['data'])){
            $result['data'] = array();
        }
		echo json_encode($result);
	}
    //get new and hot topics
    private function getHNWeb($type = 1, $uid) {
        $this->db->from('wen_weibo');
        $this->db->where('wen_weibo.type != ', 4);
        $this->db->where('wen_weibo.piazza',1);
        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.nstime <', time());
        $offset = ($this->input->get('page') - 1) * 15;
        $this->db->limit(15, $offset);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        //	$this->db->join('wen_comment', 'wen_comment.contentid = wen_weibo.weibo_id');
        $this->db->select('wen_weibo.weibo_id,wen_weibo.front_pic,wen_weibo.type_pic,wen_weibo.nstime,wen_weibo.front_title,users.alias as uname,wen_weibo.views,wen_weibo.tags,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.newtime,wen_weibo.type_data');

        if ($type == 3) {
            $this->db->order_by('wen_weibo.nstime desc');
        } else {
            //$this->db->select('users.alias as uname,users.phone,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

            $this->db->order_by("wen_weibo.hots", "desc");
            $this->db->order_by("wen_weibo.comments", "desc");
        }

        $tmp = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $res = array ();
        $this->load->model('Diary_model');
        $arr_temp =array();
        $i = 1;
        foreach ($tmp as $r) {
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id']) > 0) ? $this->getZan($r['weibo_id']) : 0;
            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item){
                        if($item == '')
                            continue;
                        $r['tagss'][] = $item;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }
            unset($r['tag']);

            $r['ctime'] = date('Y-m-d', $r['ctime']);
            $dtypd = unserialize($r['type_data']);
            isset ($dtypd['title']) && $r['content'] = $dtypd['title'];

            if(!$r['front_title']){
                isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
            } else {
                $r['content'] = $r['front_title'];
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
            $r['ntime'] = $r['newtime'];
            $r['newtime'] = date('Y-m-d H:i',$r['newtime']);

            $r['front_pic'] = $this->remote->show320($r['front_pic']);
            if ($this->input->get('width')) {
                $r['images'] = $this->Plist($r['weibo_id']);
            }
            if(!$r['front_pic']) {
                $r['pic'] = isset($r['images'][0]['savepath']) ? $r['images'][0]['savepath'] : '';
            }else{
                $r['pic'] = $r['front_pic'];
            }
            $r['width'] = isset($r['images'][0]['width'])?$r['images'][0]['width'] : 0;
            $r['height'] = isset($r['images'][0]['height'])?$r['images'][0]['height']:0;

            unset($r['comments']);
            unset($r['uname']);
            unset($r['views']);
            unset($r['images']);
            unset($r['tags']);
            //unset($r['uid']);
            //unset($r['ctime']);
            unset($r['zanNum']);
            unset($r['haspic']);
            unset($r['thumb']);
            unset ($r['type_data']);
            $res[] = $r;
            $rr[strtotime(date('Y-m-d',$r['nstime']))][] = $r;

            if($i%5 == 0) {
                $arr_temp[] = $res;
                $res = array();
            }
            $i ++;
        }
//echo '<pre>';
//        print_r($rr);
        if(!empty($rr)){
            $arr_r = array();

            $j = 0;
            foreach($rr as $kk=>$item){
                $res = array();
                $rs = array();
                $i = 1;
                foreach($item as $k=>$it){
                    $rs[] = $it;
                    if($i%5 == 0) {
                        $res[] = $rs;
                        $rs = array();
                    }
                    if(count($item) == $i){
                        $res[] = $rs;
                        $rs = array();
                    }
                    $i++;
                }

                $arr_r[$j] = $res;
                $j ++;
            }
        }

        //echo '<pre>';
        //print_r($arr_r);
        if(!empty($arr_r)){
            $r = array();

            foreach($arr_r as $item){
                foreach($item as $k=>$it){
                    $r1 = array();
                    $r2 = array();
                    $r3 = array();
                    if(!empty($item)){
                        $r4 = array();
                        $r5 = array();
                        $r6 = array();
                        foreach($it as $i){

                            if($i['type_pic'] == "1"){
                                $r1 = $i;
                            }

                            if($i['type_pic'] == "2"){
                                $r2[] = $i;
                            }

                            if($i['type_pic'] == "3" or $i['type_pic'] == "0"){
                                $r3[] = $i;
                            }
                        }

                        $r5['type_pic'] = 2;
                        $r5['split'] = $r2;
                        $r6['type_pic'] = 3;
                        $r6['split'] = $r3;
                        if(!empty($r1)) {
                            $r4['banner'] = $r1;
                        }
                        if(!empty($r2)) {
                            $r4['smallimg'] = $r5;
                        }

                        if(!empty($r3)) {
                            $r4['list'] = $r6;
                        }
                    }

                    $r[] = $r4;

                }
            }
        }
        array_pop($r);
        return $r;
    }
    //get new and hot topics
    private function getHN3($type = 1, $uid) {
        $this->db->from('wen_weibo');
        $this->db->where('wen_weibo.type != ', 4);
        $this->db->where('wen_weibo.piazza',1);
        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.nstime <', time());
        $offset = ($this->input->get('page') - 1) * 15;
        $this->db->limit(15, $offset);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        //	$this->db->join('wen_comment', 'wen_comment.contentid = wen_weibo.weibo_id');
        $this->db->select('wen_weibo.weibo_id,wen_weibo.front_pic,wen_weibo.type_pic, wen_weibo.nstime,wen_weibo.front_title,users.alias as uname,wen_weibo.views,wen_weibo.tags,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.newtime,wen_weibo.type_data');

        if ($type == 3) {
            $this->db->order_by('wen_weibo.nstime desc');
        } else {
            //$this->db->select('users.alias as uname,users.phone,wen_weibo.weibo_id,wen_weibo.views,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

            $this->db->order_by("wen_weibo.hots", "desc");
            $this->db->order_by("wen_weibo.comments", "desc");
        }

        $tmp = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $res = array ();
        $this->load->model('Diary_model');
        $arr_temp =array();
        $i = 1;
        foreach ($tmp as $r) {
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id']) > 0) ? $this->getZan($r['weibo_id']) : 0;
            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item){
                        if($item == '')
                            continue;
                        $r['tagss'][] = $item;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }
            unset($r['tag']);

            $r['ctime'] = date('Y-m-d', $r['ctime']);
            $dtypd = unserialize($r['type_data']);
            isset ($dtypd['title']) && $r['content'] = $dtypd['title'];

            if(!$r['front_title']){
                isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
            } else {
                $r['content'] = $r['front_title'];
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
            $r['ntime'] = $r['newtime'];
            $r['newtime'] = date('Y-m-d H:i',$r['newtime']);

            $r['front_pic'] = $this->remote->show320($r['front_pic']);
            if ($this->input->get('width')) {
                $r['images'] = $this->Plist($r['weibo_id']);
            }
            if(!$r['front_pic']) {
                $r['pic'] = isset($r['images'][0]['savepath']) ? $r['images'][0]['savepath'] : '';
            }else{
                $r['pic'] = $r['front_pic'];
            }
            $r['width'] = isset($r['images'][0]['width'])?$r['images'][0]['width'] : 0;
            $r['height'] = isset($r['images'][0]['height'])?$r['images'][0]['height']:0;

            unset($r['comments']);
            unset($r['uname']);
            unset($r['views']);
            unset($r['images']);
            unset($r['tags']);
            unset($r['uid']);
            //unset($r['ctime']);
            unset($r['zanNum']);
            unset($r['haspic']);
            unset($r['thumb']);
            unset ($r['type_data']);
            $res[] = $r;
            $rr[date('Y-m-d',$r['nstime'])][] = $r;

            if($i%5 == 0) {
                $arr_temp[] = $res;
                $res = array();
            }
            $i ++;
        }
//echo '<pre>';
//        print_r($rr);
        if(!empty($rr)){
            $arr_r = array();

            $j = 0;
            foreach($rr as $kk=>$item){
                $res = array();
                $rs = array();
                $i = 1;
                foreach($item as $k=>$it){
                    $rs[] = $it;
                    if($i%5 == 0) {
                        $res[] = $rs;
                        $rs = array();
                    }
                    if(count($item) == $i){
                        $res[] = $rs;
                        $rs = array();
                    }
                    $i++;
                }

                $arr_r[$j] = $res;
                $j ++;
            }
        }

        //echo '<pre>';
        //print_r($arr_r);
        if(!empty($arr_r)){
            $r = array();

            foreach($arr_r as $item){
                foreach($item as $k=>$it){
                    $r1 = array();
                    $r2 = array();
                    $r3 = array();
                    if(!empty($item)){
                        $r4 = array();
                        $r5 = array();
                        $r6 = array();
                        if(empty($it))
                            continue;
                        foreach($it as $i){

                            if($i['type_pic'] == "1"){
                                $r1 = $i;
                            }

                            if($i['type_pic'] == "2"){
                                $r2[] = $i;
                            }

                            if($i['type_pic'] == "3" or $i['type_pic'] == "0"){
                                $r3[] = $i;
                            }
                        }

                        $r5['type_pic'] = 2;
                        $r5['split'] = $r2;
                        $r6['type_pic'] = 3;
                        $r6['split'] = $r3;
                        if(!empty($r1)) {
                            $r4[] = $r1;
                        }
                        if(!empty($r2)) {
                            $r4[] = $r5;
                        }

                        if(!empty($r3)) {
                            $r4[] = $r6;
                        }
                    }

                    $r[] = $r4;

                }
            }
        }
        return $r;
    }
// 获取圈中间置顶部分
    public function getTop($pageSize=10) {

        $res = array();
        $res['data'] = array();
        $res['state'] = '000';
        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.top', 1);

        $this->db->where('wen_weibo.top_start <', time());
        $this->db->where('wen_weibo.top_end >', time());


        $this->db->select('wen_weibo.weibo_id,wen_weibo.content,wen_weibo.type_data');

        $this->db->order_by('wen_weibo.toptime desc');
        $this->db->limit($pageSize);
        $tmp = $this->db->get('wen_weibo')->result_array();
        if(!empty($tmp)){

            foreach ($tmp as $r) {
                $dtypd = unserialize($r['type_data']);
                isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
                unset($r['type_data']);
                $res['data'][] = $r;
            }
        }

        echo json_encode($res);
    }
    private function getHN4($type = 1,$pageSize=10,$uid=0) {
        $this->db->from('wen_weibo');

        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.chosen', 1);

        $this->db->where('wen_weibo.chosen_start <', time());
        $this->db->where('wen_weibo.chosen_end >', time());

        $this->db->limit($pageSize);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        $this->db->select('users.age, users.city,wen_weibo.weibo_id, wen_weibo.views,wen_weibo.tags,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,users.alias as uname,wen_weibo.views as vote,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.newtime, wen_weibo.type_data');
        if($pageSize == 2) {
            $this->db->order_by('wen_weibo.toptime desc');
        }else{
            $this->db->order_by('wen_weibo.newtime desc');
        }
        $tmp = $this->db->get()->result_array();
        $res = array ();

        foreach ($tmp as $r) {

            if($r['top_start'] <= time() && $r['top_end'] >= time()){
                $r['top'] = 1;
            }else{
                $r['top'] = 0;
            }
            $r['city'] = '';
            $r['age'] = '';
            if($r['chosen_start'] <= time() && $r['chosen_end'] >= time()){
                $r['chosen'] = 1;
            }else{
                $r['chosen'] = 0;
            }

            if($r['hot_start'] <= time() && $r['hot_end'] >= time()){
                $r['hot'] = 1;
            }else{
                $r['hot'] = 0;
            }

            if($type == 1){
                $tags = $this->getTags();
                if(!empty($tags)){
                    foreach($tags as $item){
                        $res[] = $item;
                    }
                }
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id'])>0)?$this->getZan($r['weibo_id']):0;

            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            $r['pageview'] = $r['views'];
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

            $r['ishot'] = 1;
            $dtypd = unserialize($r['type_data']);
            $url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);

            $arr_url = explode('/',$url);
            if(isset($arr_url[1])){
                $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x320/',$url);
            }
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
            if(time()-$r['newtime']<3600*10){
                if(time()-$r['newtime']<3600){
                    $r['newtime'] = intval((time()-$r['newtime'])/60).'分钟前';
                }else{
                    $r['newtime'] = intval((time()-$r['newtime'])/3600).'小时前';
                }
            }else{
                $r['newtime'] = date('Y-m-d',$r['newtime']);
            }
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
                //unset($r["uname"]);
                unset($r["vote"]);
                //unset($r["comments"]);
                //unset($r["uid"]);
               // unset($r["thumb"]);
                //unset($r["ctime"]);
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


    public function getItemDiaryList($item_name, $pageSize=1) {


        $res = array();
        $pageSize=1;
        $page = $this->input->get('page')?$this->input->get('page'):1;
        $offset = ($page - 1) * $pageSize;
        $tmp = $this->Diary_model->getDiaryFrontList($offset,2, $pageSize);

        $doctor = '';
        $yiyuan = '';
        //$uid = $this->input->get('uid');

        $tmp = $this->Diary_model->getDiaryNodeList($item_name,$doctor,$yiyuan, $offset,$pageSize);

        //$result['data2'] = $this->topicList();
        $k = 0;
        if(!empty($tmp)){
            $n = 1;
            foreach($tmp as $item){

                if(empty($item['imgfile'])) {
                    $item['imgurl'] = $this->remote->getLocalImage($item['imgurl']);
                }else{
                    $item['imgurl'] = $this->remote->getQiniuImage($item['imgurl']);
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
                $item['item'] = $item_name;
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

                /*if($n%3 == 0){

                    $result['data'][] = $result['data2'][$k];
                    $k ++;
                }else{
                    $result['data'][] = $item;
                }*/
                $res = $item;
                $n++;
            }
        }
        return $res;
        //unset($result['data2']);
        //echo json_encode($result);
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
    public function getList(){

        $id = $result['data'] = array();
        if($name = $this->input->get('name')){
            $id = $this->getId($name);
        }
        if ($id OR ($id = intval($this->input->get('id')))) {
            $result['state'] = '000';

            $this->db->select('name,des');
            $this->db->where('pid', $id);
            $this->db->order_by("id", "desc");

            $tmp = $this->db->get('new_items')->result_array();
            $result['data'] = array ();

            if(!empty($tmp)) {
                foreach($tmp as $key=>$item) {

                    $tmp = $this->getHN5($item['name'],1,1,$this->uid);
                    $tmp1 = $this->getItemDiaryList($item['name']);
                    if($tmp[0]['toptime'] < $tmp1[0]['created_at']) {
                        $itemTopic = $this->getHN5($item['name'], 1, 1, $this->uid);
                        if($itemTopic){
                            $result['data'][] = $this->getHN5($item['name'], 1, 1, $this->uid);
                        }

                    }else {
                        $itemDiary = $this->getItemDiaryList($item['name']);
                        if($itemDiary) {
                            $result['data'][] = $itemDiary;
                        }
                    }
                }
            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    //get new and hot topics
    private function getHN5($item_name,$type = 1, $pageSize=1, $uid) {

        $this->db->from('wen_weibo');

        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.chosen', 1);

        $this->db->where('wen_weibo.chosen_start <', time());
        $this->db->where('wen_weibo.chosen_end >', time());
        $this->db->like('wen_weibo.datatype',$item_name);
        $this->db->or_like('wen_weibo.tags', $item_name);

        $this->db->limit($pageSize);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        $this->db->select('wen_weibo.toptime,users.city, users.age,wen_weibo.weibo_id,wen_weibo.pageview,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.newtime,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,users.alias as uname,users.jifen, wen_weibo.views,wen_weibo.tags,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');
        if($pageSize == 1) {
            $this->db->order_by('wen_weibo.toptime desc');
        }else{
            $this->db->order_by('wen_weibo.newtime desc');
        }

        $tmp = $this->db->get()->result_array();

        $res = array ();

        foreach ($tmp as $r) {
            if($this->uid) {
                if ($this->Diary_model->getstate($r['uid'],$this->uid)) {
                    $r['follow'] = 1;
                } else {
                    $r['follow'] = 0;
                }

            }else{
                $item['follow'] = 0;
            }
            if($r['top_start'] <= time() && $r['top_end'] >= time()){
                $r['top'] = 1;
            }else{
                $r['top'] = 0;
            }

            if($r['chosen_start'] <= time() && $r['chosen_end'] >= time()){
                $r['chosen'] = 1;
            }else{
                $r['chosen'] = 0;
            }
            $age = $this->getAge($r['uid']);
            $r['age'] = isset($age)?$this->getAge($r['uid']):'';
            $r['sex'] = 1;
            if($r['hot_start'] <= time() && $r['hot_end'] >= time()){
                $r['hot'] = 1;
            }else{
                $r['hot'] = 0;
            }
            $r['level'] = $this->getLevel($r['jifen']);
            $r['pageview'] = intval($r['views']);
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            $r['item'] = $item_name;
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id'])>0)?$this->getZan($r['weibo_id']):0;
            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item && !is_null($item)){
                        if($item == '' || is_null($item))
                            continue;
                        $arr = array();
                        $arr['tag'] = str_replace('null','',$item);
                        $itemid = $this->Diary_model->getItemId($item);
                        $arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        if(!empty($arr['tag']))
                            $r['tagss'][] = $arr;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }
            $rs = $this->Diary_model->get_user_by_username($r['uid']);
            $r['ishot'] = 0;
            $r['ctime'] = date('Y-m-d', $r['ctime']);
            $r['newtime'] = date('Y-m-d', $r['newtime']);
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

    //get new and hot topics
    private function getHN($type = 1, $uid) {
        $this->db->from('wen_weibo');
        $this->db->where('wen_weibo.type != ', 4);
        $this->db->where('wen_weibo.isdel', 0);
        $this->db->where('wen_weibo.chosen', 0);
        $this->db->where('wen_weibo.ctime <', time());
        $this->db->where('wen_weibo.wsource', 'IOS');
        $this->db->or_where('wen_weibo.wsource', 'android');

        $offset = ($this->input->get('page') - 1) * 8;
        $this->db->limit(8, $offset);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        //	$this->db->join('wen_comment', 'wen_comment.contentid = wen_weibo.weibo_id');
        $this->db->select('users.city, users.age,wen_weibo.weibo_id,wen_weibo.pageview,wen_weibo.top,wen_weibo.top_start,wen_weibo.top_end,wen_weibo.newtime,wen_weibo.chosen,wen_weibo.chosen_start,wen_weibo.chosen_end,wen_weibo.hot,wen_weibo.hot_start,wen_weibo.hot_end,users.alias as uname,users.jifen, wen_weibo.views,wen_weibo.tags,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

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
            if($this->uid) {
                if ($this->Diary_model->getstate($r['uid'],$this->uid)) {
                    $r['follow'] = 1;
                } else {
                    $r['follow'] = 0;
                }

            }else{
                $item['follow'] = 0;
            }
            if($r['top_start'] <= time() && $r['top_end'] >= time()){
                $r['top'] = 1;
            }else{
                $r['top'] = 0;
            }

            if($r['chosen_start'] <= time() && $r['chosen_end'] >= time()){
                $r['chosen'] = 1;
            }else{
                $r['chosen'] = 0;
            }
            $age = $this->getAge($r['uid']);
            $r['age'] = isset($age)?$this->getAge($r['uid']):'';
            $r['sex'] = 1;
            if($r['hot_start'] <= time() && $r['hot_end'] >= time()){
                $r['hot'] = 1;
            }else{
                $r['hot'] = 0;
            }
            $r['level'] = $this->getLevel($r['jifen']);
            $r['pageview'] = intval($r['views']);
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }
            $r['zanNum'] = ($this->getZan($r['weibo_id'])>0)?$this->getZan($r['weibo_id']):0;
            if(intval($uid) > 0) {
                $iszan = $this->isAtZan($uid, $r['weibo_id']);
                if ($iszan) {
                    $r['isZan'] = 1;
                } else {
                    $r['isZan'] = 0;
                }
            }else{
                $r['isZan'] = 0;
            }
            if(!empty($r['tags'])){
                $r['tag'] = explode(',',$r['tags']);

                foreach($r['tag'] as $item){
                    if($item && !is_null($item)){
                        if($item == '' || is_null($item))
                            continue;
                        $arr = array();
                        $arr['tag'] = str_replace('null','',$item);
                        $itemid = $this->Diary_model->getItemId($item);
                        $arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
                        if(!empty($arr['tag']))
                            $r['tagss'][] = $arr;
                    }
                }
            }

            if(empty($r['tagss'])){
                $r['tagss'] = array();
            }
            $rs = $this->Diary_model->get_user_by_username($r['uid']);
            $item ['basicinfo'] = $this->getBasicInfo($rs[0]);
            $r['ishot'] = 0;
            $r['ctime'] = date('Y-m-d', $r['ctime']);
            $r['newtime'] = date('Y-m-d', $r['newtime']);
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
	private function getChild($type = 0){
		$tmp = array();
		$data = array();

		$sqlItem = "select id from items where name like '%".$type."%'";
		$citems = $this->db->query($sqlItem)->result_array();
		
		return $citems[0]['id'];
	}
    public function GroupTopicListTop($param = '') {
        //error_reporting(E_ALL);
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

        $sql = "SELECT w.group_start,w.group_end,w.comments,w.content, w.hot,w.hot_start,w.hot_end,w.top,w.top_start,w.top_end,w.chosen,w.chosen_start,w.chosen_end,w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
        $sql .= ' FROM wen_weibo as w LEFT JOIN users as u ON w.uid=u.id';

        $ctime = time(); //set publish time
        if ($this->input->get('uid')) {
            if ($sqltmp || $type) {
                if($sqltmp){
                    $sql .= ' WHERE w.uid = ' . $this->input->get('uid') . " AND w.type=1 AND (".$sqltmp." or (w.dataType like '%".$type."%' OR w.tags like '%".$type."%'))";
                }else{
                    $sql .= ' WHERE w.uid = ' . $this->input->get('uid') . " AND w.type=1 AND (w.dataType like '%".$type."%' OR w.tags like '%".$type."%')";
                }
            } else {
                $sql .= ' WHERE w.type&25 AND w.uid = ' . $this->input->get('uid');
            }
        } else {
            if ($sqltmp || $type) {
                if($sqltmp){
                    $sql .= " WHERE w.type&25 AND (".$sqltmp." or (w.dataType like '%".$type."%' OR w.tags like '%".$type."%'))";
                }else{
                    $sql .= " WHERE w.type&25 AND (w.dataType like '%".$type."%' OR w.tags like '%".$type."%')";
                }
            } else {
                $sql .= " WHERE w.type&25 ";
            }
        }
        $sql .=" and w.uid != 0 ";
        $sql .= " AND ctime<={$ctime} and w.grouptop=1 and w.isdel=0 ORDER BY w.ctime DESC ";

        $tmp = $this->db->query($sql)->result_array();



        //$totalCount = $this->getMyTopicCount($this->input->get('uid'));

        $result['data'] = array ();
        if (!empty ($tmp)) {
            foreach ($tmp as $row) {

                if($row['top_start'] <= time() && $row['top_end'] >= time()){
                    $row['top'] = 1;
                }else{
                    $row['top'] = 0;
                }

                if($row['chosen_start'] <= time() && $row['chosen_end'] >= time()){
                    $row['chosen'] = 1;
                }else{
                    $row['chosen'] = 0;
                }

                if($row['hot_start'] <= time() && $row['hot_end'] >= time()){
                    $row['hot'] = 1;
                }else{
                    $row['hot'] = 0;
                }
                if($row['group_start'] <= time() && $row['group_end'] >= time()){
                    $row['group'] = 1;
                }else{
                    $row['group'] = 0;
                }
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
                if(empty($row['tagss'])){
                    $row['tagss'] = array();
                }
                $row['tags'] = $row['tag1'];

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
                //$row['totalCount'] = $totalCount;
                //if($this->input->get('width')){
                $row['images'] = $this->Plist($row['weibo_id']);
                //}
                $result['data'][] = $row;
            }
        }
        return $result;
    }

    public function topicList1($param = '') {
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
                    $typeSql .= " w.dataType like '%".$item['name']."%' OR w.tags like '%".$item['name']."%' OR";
                    $tmpitem[] = $item['name'];
                }
            }
        }

        $sqltmp = substr($typeSql,0,strlen($typeSql)-2);

        $sql = "SELECT w.group_start,w.group_end,w.comments,w.content, w.hot,w.hot_start,w.hot_end,w.top,w.top_start,w.top_end,w.chosen,w.chosen_start,w.chosen_end,w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
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
        $lastid = intval($this->input->get('lastid'))?intval($this->input->get('lastid')):0;
        if($lastid > 0){
            if($this->input->get('direction') == 'down'){
                $sql .= " AND w.weibo_id < {$lastid}";
            }else{
                $sql .= " AND w.weibo_id > {$lastid}";
            }
        }
        $sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.ctime DESC ";
        if($this->input->get('limit')){
            $limit = $this->input->get('limit');
        }else{
            $limit = 10;
        }
        if ($this->input->get('page')) {
            $start = ($this->input->get('page') - 1) * 10;
            $sql .= " LIMIT $start,$limit ";
        } else {
            $sql .= " LIMIT 0,$limit ";
        }
        $tmp = $this->db->query($sql)->result_array();


        //$totalCount = $this->getMyTopicCount($this->input->get('uid'));

        $result['data'] = array ();
        if (!empty ($tmp)) {
            foreach ($tmp as $row) {

                if($row['top_start'] <= time() && $row['top_end'] >= time()){
                    $row['top'] = 1;
                }else{
                    $row['top'] = 0;
                }

                if($row['chosen_start'] <= time() && $row['chosen_end'] >= time()){
                    $row['chosen'] = 1;
                }else{
                    $row['chosen'] = 0;
                }

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
                if(empty($row['tagss'])){
                    $row['tagss'] = array();
                }
                $row['tags'] = $row['tag1'];

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
                //$row['totalCount'] = $totalCount;
                //if($this->input->get('width')){
                $row['images'] = $this->Plist($row['weibo_id']);
                //}
                $result['data'][] = $row;
            }
            $result['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
        }
        $result['topicTotal'] = $this->Diary_model->getTopicCount($type)?$this->Diary_model->getTopicCount($type):0;
        $result['diaryTotal'] = $this->Diary_model->getDiaryTotal($type)?$this->Diary_model->getDiaryTotal($type):0;
        $s = $this->GroupTopicListTop();

        $r = array_merge($s['data'],$result['data']);
        $result['data'] = array();
        $result['data'] = $r;
        echo json_encode($r);
    }


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
					$typeSql .= " w.dataType like '%".$item['name']."%' OR w.tags like '%".$item['name']."%' OR";
					$tmpitem[] = $item['name'];
				}
			}
		}

		$sqltmp = substr($typeSql,0,strlen($typeSql)-2);
		
		$sql = "SELECT u.jifen, u.daren,w.group_start,w.group_end,w.comments,w.content,w.newtime, w.hot,w.hot_start,w.hot_end,w.top,w.top_start,w.top_end,w.chosen,w.chosen_start,w.chosen_end,w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags ";
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
		$lastid = intval($this->input->get('lastid'))?intval($this->input->get('lastid')):0;
		if($lastid > 0){
			if($this->input->get('direction') == 'down'){
				$sql .= " AND w.weibo_id < {$lastid}";
			}else{
				$sql .= " AND w.weibo_id > {$lastid}";
			}
		}
		$sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.newtime DESC ";
        if($this->input->get('limit')){
            $limit = $this->input->get('limit');
        }else{
            $limit = 10;
        }
		if ($this->input->get('page')) {
			$start = ($this->input->get('page') - 1) * 10;
			$sql .= " LIMIT $start,$limit ";
		} else {
			$sql .= " LIMIT 0,$limit ";
		}
		$tmp = $this->db->query($sql)->result_array();


        //$totalCount = $this->getMyTopicCount($this->input->get('uid'));

		$result['data'] = array ();
		if (!empty ($tmp)) {
			foreach ($tmp as $row) {

                if($row['top_start'] <= time() && $row['top_end'] >= time()){
                    $row['top'] = 1;
                }else{
                    $row['top'] = 0;
                }

                if($row['chosen_start'] <= time() && $row['chosen_end'] >= time()){
                    $row['chosen'] = 1;
                }else{
                    $row['chosen'] = 0;
                }

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
                $row['pageview'] = intval($row['pageview']);
                $row['age'] = $this->getAge(intval($row['age']));
                $row['city'] = !empty($row['city'])?$row['city']:'';
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
                if(empty($row['tagss'])){
                    $row['tagss'] = array();
                }
                $row['tags'] = $row['tag1'];

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
                $row['newtime'] = date('Y-m-d', $row['newtime']);
				if ($this->uid != $row['uid']) {
					$row['hasnew'] = 0;
				}
                //$row['totalCount'] = $totalCount;
				//if($this->input->get('width')){
				$row['images'] = $this->Plist($row['weibo_id']);
				//}
				$result['data'][] = $row;
			}
			$result['weiboCommentSum'] = $this->common->weiboCommentSum($this->uid);
		}
        $result['topicTotal'] = $this->Diary_model->getTopicCount($type)?$this->Diary_model->getTopicCount($type):0;
        $result['diaryTotal'] = $this->Diary_model->getDiaryTotal($type)?$this->Diary_model->getDiaryTotal($type):0;
        $s = $this->GroupTopicListTop();

        $r = array_merge($s['data'],$result['data']);
        $result['data'] = array();
        $result['data'] = $r;
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
    // 我的帖子
    public function getMyTopicList($param = '') {
        //error_reporting(E_ALL);
        $result['state'] = '000';
        $sql = "SELECT w.comments,w.content, w.uid, w.type_data,w.weibo_id,w.ctime,u.phone,u.email,u.alias,w.commentnums,w.tags,u.city, u.age, w.pageview ";
        $sql .= ' FROM wen_weibo as w LEFT JOIN users as u ON w.uid=u.id';
        $count = 0;
        $ctime = time(); //set publish time
        if ($this->input->get('uid')) {
            $count = $this->getMyTopicCount($this->input->get('uid'));

            $result['page'] = ceil($count/10);
            $sql .= ' WHERE w.type&25 AND w.uid = ' . $this->input->get('uid');
        }

        $sql .= " AND ctime<={$ctime} and w.isdel=0 ORDER BY w.ctime DESC ";
        if($this->input->get('limit')){
            $limit = $this->input->get('limit');
        }else{
            $limit = 10;

        }
        if ($this->input->get('page')) {
            $start = ($this->input->get('page') - 1) * 10;
            $sql .= " LIMIT $start,$limit ";
        } else {
            $sql .= " LIMIT 0,10 ";
        }
        $tmp = $this->db->query($sql)->result_array();

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
                $row['pageview'] = intval($row['pageview']);
                $row['age'] = $this->getAge(intval($row['age']));
                $row['city'] = empty($row['city'])?$row['city']:'';
                $row['zanNum'] = ($this->getZan($row['weibo_id']) > 0) ? $this->getZan($row['weibo_id']) : 0;
                if(!empty($row['tags'])){
                    $row['tag'] = explode(',',$row['tags']);

                    foreach($row['tag'] as $item){
                        if(!empty($item)){
                            $arr = array();
                            $arr['tag'] = $item;
                            $itemid = $this->Diary_model->getItemId($item);
                            $arr['other'] = $this->Diary_model->isItemLevel($itemid,1);
                            $row['tagss'][] = $arr;
                            $row['tag1'][] = $item;
                        }
                    }
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

                if(empty($row['tagss'])){
                    $row['tagss'] = array();
                }
                $row['tags'] = $row['tag1'];
                if(empty($row['tags'])){
                    $row['tags'] = array();
                }
                if(empty($row['tagss'])){
                    $row['tagss'] = array();
                }
                $row['tag'] = "全部";
                //unset($row['tag']);
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
                $row['totalCount'] = $count;

                //if($this->input->get('width')){
                $row['images'] = $this->Plist($row['weibo_id']);
                //}
                $result['data'][] = $row;
            }
            $result['weiboCommentSum'] = $this->common->weiboCommentSum($this->input->get('uid'));
        }

        echo json_encode($result);
    }
    //deltete topic
	function del($param = '') {
		$result['state'] = '000';
		$result['notice'] = '话题删除成功！';

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

		echo json_encode($result);

	}

	//回复的话题列表
	public function rtopicList($param = '') {
		$result['state'] = '000';

		$sql = "SELECT wen_weibo.content,wen_comment.new_reply,wen_weibo.comments,wen_weibo.commentnums,wen_weibo.favnum,wen_weibo.type_data,wen_weibo.weibo_id,wen_weibo.ctime";
		$sql .= ' FROM wen_comment LEFT JOIN wen_weibo ON wen_comment.contentid=wen_weibo.weibo_id';

		if ($uid = intval($this->input->get('uid'))) {
			$sql .= ' WHERE wen_comment.type="topic" and wen_weibo.isdel=0 AND wen_comment.fuid = ' . $uid;
		}

		$sql .= "  GROUP BY wen_comment.contentid ORDER BY wen_comment.contentid DESC ";
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

		echo json_encode($result);
	}


	public function flow($param = '') {
		$result['state'] = '000';

			
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
                   $SQL = "SELECT  zan,weibo_id,type_data,content,ctime FROM wen_weibo INNER JOIN( SELECT weibo_id,vaoc FROM wen_weibo WHERE INSTR(tags,'{$tags}')";
				   $SQL .= " AND type&25 AND INSTR(type_data,'savepath') AND ctime<={$ctime} AND isdel=0 {$fixcondition} ORDER BY vaoc DESC LIMIT $start,15) as lim USING(weibo_id)";
				}else{
					$SQL = "SELECT vaoc, zan,weibo_id,type_data,content,ctime FROM wen_weibo WHERE INSTR(tags,'{$tags}')";
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
                    if(empty($r['imgfile'])) {
                        $t['url'] = $this->remote->getLocalImage($url);
                    }else{
                        $t['url'] = $this->remote->getQiniuImage($r['imgfile']);
                    }
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
					$t['content'] = $dtypd['title'];
					$t['desc'] = $r['content'] ;
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

		if ($type = trim($this->input->get('type'))) {
			$this->db->where('type = ', $type);
			$this->db->select('name, id');
			$this->db->order_by("order", "desc");
			$result['data'] = $this->db->get('new_items')->result_array();
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);

	}
	public function comments($param = '') {
		$result['state'] = '000';

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

    public function pageview(){
        error_reporting(E_ALL);
        ini_set('display_errors','On');
        $threeday = time() - 3*86400;

        $this->db->query("update wen_weibo set views=views + ? where ctime < (? - 3600) and ctime > (? - 24*3600)", array(rand(500,1000), time(), time()))->result_array();
        echo $this->db->last_query();
        $this->db->query("update wen_weibo set views=views + ? where ctime < (? - 86400) and ctime > (? - 3*24*3600)", array(rand(1000,5000) ,time(), time()))->result_array();
        echo $this->db->last_query();
        $this->db->query("update wen_weibo set views=views + ? where ctime < {$threeday} limit 100", array(rand(5000,20000)))->result_array();
        echo $this->db->last_query();
    }
}
?>
