<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class sns extends MY_Controller {
	private $notlogin = true;
	private $uid = 0, $path = '';
	public function __construct() {
		parent :: __construct();
		$this->load->library('yisheng');
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->model('auth');
		$this->load->model('remote');
        $this->load->model('Score_model');
	}
    //uid 可以分为项目id,用户id,机构id，$type = 8为普通用户$type=9项目关注$type=10机构关注
	function add($param = '') {
		$result['state'] = '000';
		$result['updateState'] = '001';
        $type= $this->input->post('type');
		if ($this->uid) {
            $result['debug'] = $this->input->post('followuser');
			if ($data['uid'] = $this->input->post('followuser')) {
                $condition = array (
                    'uid' => $data['uid'],
                    'fid' => $this->uid,
                    'type'=> $type
                );

                $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
                if($tmp > 0){

                    $this->db->select('wen_follow.fid as fid');
                    $this->db->from('wen_follow');
                    $this->db->where('wen_follow.uid', $data['uid']);
                    $this->db->where('wen_follow.type', 8);
                    $num = $this->db->get()->num_rows();

                    if($num == 100){
                        $this->Score_model->addScore(69,$data['uid']);
                    }else if($num == 1000){
                        $this->Score_model->addScore(70,$data['uid']);
                    }else if($num == 10000){
                        $this->Score_model->addScore(71,$data['uid']);
                    }else if($num == 100000){
                        $this->Score_model->addScore(72,$data['uid']);
                    }
                    $this->Score_model->addScore(48,$this->uid);


                    $result['state'] = '006';
                    $result['notice'] = '已经关注!';
                }else{
                	$data['fid'] = $this->uid;
					$data['type'] = $type;
					$result['updateState'] = '000';
					$this->common->insertData('wen_follow', $data);
                }
			} else {
				$result['state'] = '012';
                $result['notice'] = '用户未登陆!';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function plus($param = '') {

		$result['state'] = '000';
		$result['updateState'] = '001';
        $result['data'] = array();
        $type= $this->input->post('type');

		if ($this->uid) {
            $this->db->trans_start();
                if ($data['uid'] = $this->input->post('followuser')) {
                    $condition = array (
                        'uid' => $data['uid'],
                        'fid' => $this->uid,
                        'type'=> $type
                    );
                    $this->common->deleteTableData('wen_follow', $condition);
                    $result['updateState'] = '000';
                } else {
                    $result['state'] = '012';
                }
                $tags = $this->input->post('tag');

                if(!empty($tags)){
                    $isUser = $this->isUser($tags, $this->uid);
                    $result['debug'] = $isUser;
                    if(!empty($isUser)){

                        $this->delete($tags);
                        $this->addFollowUser($this->uid);
                        $this->db->where('uid',$this->uid);
                        $this->db->select('tag as name, tag_img as surl, colors');

                        $q = $this->db->get('user_fav')->result_array();
                        $result['data']['items'] = !empty($q) ? $q : array();
                        $p = $this->getFollow($this->uid);
                        $result['data']['follows'] = !empty($p)? $p : array();
                        $result['flag'] = 1; //取消推荐当中的
                    }else{
                        $result['data'] = array();
                        $result['flag'] = 0; //普通取消
                    }
                }
            $this->db->trans_complete();
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}

    private function delete($tag){
        if(empty($tag)){
            return ;
        }

        return $this->db->delete("user_fav", array('tag'=>$tag));
    }

    private function isUser($tag, $uid){
        if(empty($tag)){
            return;
        }
        $this->db->where('tag',$tag);
        $this->db->where('uid',$uid);
        return $this->db->get('user_fav')->result_array();
    }

    public function addFollowUser($uid){

        $rs = $this->getChildItem(0,$uid);

        $data = $rs[rand(0,count($rs)-1)];

        $user_fav = array('cid' => $data['id'], 'uid' => $this->uid, 'tag_img' => $data['surl'], 'tag' => $data['name'], 'colors'=>$data['colors'],'created_at' => time(), 'updated_at' => time());
        return $this->db->insert('user_fav', $user_fav);
    }

    private function getDefualtFrontTags($uid){

        $this->db->where('is_default',1);
        $this->db->limit(3);
        return $this->db->get('new_items')->result_array();
    }

    public function getChildItem($pid='',$uid = 0){


        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,burl,colors,is_hot as num,img_png as surl');
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

                $itemtmp[$k]['burl'] = $this->remote->show($itemtmp[$k]['surl']);
                $itemtmp[$k]['surl'] = $this->remote->show320($itemtmp[$k]['surl']);
                $tmpitem = $itemtmp;
            }
            $item['child'] = $tmpitem ? $tmpitem : array();

            $data[] = $item['child'];
        }
        $res = array();
        if(!empty($data)){
            $this->db->where('uid',$uid);
            $this->db->select('tag');
            $q = $this->db->get('user_fav')->result_array();
            $arr_user = array();

            if(!empty($q)){
                foreach($q as $qitem){
                    $arr_user[] = $qitem['tag'];
                }
            }
            foreach($data as $item){
                if(!empty($item)) {
                    foreach ($item as $it) {
                        if(in_array($it['name'],$arr_user)){
                            continue;
                        }
                        $res[] = $it;
                    }
                }
            }
        }
        $follows = $this->getFollow($uid);
        if(count($follows) > 0){
            return $follows;
        }else {
            return $res;
        }
    }

	function getstate($param = '') {
		$result['state'] = '000';
        $type= $this->input->post('type');

		if ($this->uid) {

			if ($followuser = $this->input->post('followuser')) {
				$result['follow'] = '0';
				$condition = array (
					'uid' => $followuser,
					'fid' => $this->uid,
                    'type'=> $type
				);

				$tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
				if ($tmp > 0) {
					$result['follow'] = '1';
				}

			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function myfavorites($param = '') {
		//file_put_contents("/var/www/test/logres2",var_export($this->session->userdata('session_id'),true).'----time='.date("Y-m-d h:i:s",time()));
		$result['state'] = '000';
		if (!$this->notlogin) {
			if ($type = $this->input->get('type')) {
				$this->db->where('wen_follow.fid', $this->uid);
				$this->db->where('users.role_id', $type);
				$offset = ($this->input->get('page') - 1) * 10;
				$this->db->limit(10, $offset);
				$result['data'] = array();
				if ($type == 2) {
					$this->db->select('users.tconsult,users.verify,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				} elseif($type == 1) {
					$this->db->select('user_profile.address,user_profile.city,user_profile.user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
				}else {
					$this->db->select('company.name,users.verify,company.tel,company.shophours,company.department,company.address,company.city,company.userid as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
					$this->db->from('wen_follow');
					$this->db->join('users', 'users.id = wen_follow.uid', 'left');
					$this->db->join('company', 'company.userid = wen_follow.uid', 'left');
				}

				$tmp = $this->db->get()->result_array();
                //$result['sql'] = $this->db->last_query();
				foreach ($tmp as $row) {
					$row['tconsult'] = $row['systconsult'] > 0 ? $row['systconsult'] : $row['tconsult'];
					$row['replys'] = $row['sysreplys'] > 0 ? $row['sysreplys'] : $row['replys'];
					$row['voteNum'] = $row['sysvotenum'] > 0 ? $row['sysvotenum'] : $row['voteNum'];
					$row['grade'] = $row['sysgrade'] > 0 ? $row['sysgrade'] : $row['grade'];
                    $row['message'] = '';
					unset ($row['sysvotenum']);
					unset ($row['sysgrade']);
					unset ($row['systconsult']);
					unset ($row['sysreplys']);

					$row['department'] = $this->yisheng->search($row['department']);
					$row['thumb'] = $this->profilepic($row['user_id'], 2);
					$result['data'][] = $row;
				}

			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function getfavorites($param = '') {
 		$result['state'] = '000';

		if (($type = $this->input->get('type')) && $uid = intval($this->input->get('uid'))) {
			$this->db->where('wen_follow.fid', $uid);
			$this->db->where('users.role_id', $type);
			$offset = ($this->input->get('page') - 1) * 10;
			$this->db->limit(10, $offset);
			$result['data'] = array();
			if ($type == 2) {
				$this->db->select('users.id as uid, users.jifen as jifen,users.daren as daren,users.tconsult,users.verify,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,user_profile.user_id,user_profile.user_id,user_profile.sex,user_profile.company,user_profile.position,user_profile.department,user_profile.city,user_profile.introduce,user_profile.skilled');
				$this->db->from('wen_follow');
				$this->db->join('users', 'users.id = wen_follow.uid', 'left');
				$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
			} elseif($type == 1) {
				$this->db->select('users.id as uid, users.jifen as jifen,users.daren as daren,user_profile.address,user_profile.city,user_profile.user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
				$this->db->from('wen_follow');
				$this->db->join('users', 'users.id = wen_follow.uid', 'left');
				$this->db->join('user_profile', 'user_profile.user_id = wen_follow.uid', 'left');
			}else {
				$this->db->select('company.name,users.verify,company.tel,company.shophours,company.department,company.address,company.city,company.userid as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested');
				$this->db->from('wen_follow');
				$this->db->join('users', 'users.id = wen_follow.uid', 'left');
				$this->db->join('company', 'company.userid = wen_follow.uid', 'left');
			}

			$tmp = $this->db->get()->result_array();

			foreach ($tmp as $row) {
				$row['tconsult'] = $row['systconsult'] > 0 ? $row['systconsult'] : $row['tconsult'];
				$row['replys'] = $row['sysreplys'] > 0 ? $row['sysreplys'] : $row['replys'];
				$row['voteNum'] = $row['sysvotenum'] > 0 ? $row['sysvotenum'] : $row['voteNum'];
				$row['grade'] = $row['sysgrade'] > 0 ? $row['sysgrade'] : $row['grade'];

                $rs['state'] = $this->isstate(8,$row['uid']);
                $rs['level'] = $this->isLevel($row['jifen']);
				unset ($row['sysvotenum']);
				unset ($row['sysgrade']);
				unset ($row['systconsult']);
				unset ($row['sysreplys']);
				if($type == 1){
					$row['guangzhu'] = $this->Cfensi($row['user_id']);
				    $row['fensi'] = $this->Cgz($row['user_id']);
				    $row['shoucang'] = $this->Cfavr($row['user_id']);
				}
				$type!=1&&$row['department'] = $this->yisheng->search($row['department']);
				$row['thumb'] = $this->profilepic($row['user_id'], 2);
				$result['data'][] = $row;
			}

		} else {
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	public function myfans($param = ''){
		$result['state'] = '000';

		if($this->notlogin){
          $result['ustate'] = '002';
		}else{
		  if ($this->input->get('page')) {
			$page = intval($this->input->get('page')-1);
			$start = $page * 15;
			$this->db->select('users.alias as uname, users.id as uid, users.jifen as jifen');
			$this->db->from('wen_follow');
			$this->db->where('uid', $this->uid);
			$this->db->limit(15, $start);
			$this->db->join('users', 'users.id = wen_follow.fid');
			$tmp = $this->db->get()->result_array();
			$result['data'] =  array();
			foreach($tmp as $r){
				$r['thumb'] = $this->profilepic($r['uid'],1);

                $result['data'][] = $r;
			}
		  } else {
			$result['state'] = '012';
		  }
	   }

		echo json_encode($result);
	}

    private function isLevel($jifen){

        if($jifen < 1000){
            return 1;
        }elseif($jifen >=1000 && $jifen < 8000){
            return 2;
        }elseif($jifen >=8000 && $jifen < 36000){
            return 3;
        }elseif($jifen >=96000 && $jifen < 250000){
            return 4;
        }elseif($jifen >=250000){
            return 5;
        }
    }

    private function isstate($type = 8, $fid=0) {



        if ($this->uid) {

            if ($followuser = $fid) {
                $result['follow'] = '0';
                $condition = array (
                    'uid' => $followuser,
                    'fid' => $this->uid,
                    'type'=> $type
                );

                $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();
                if ($tmp > 0) {
                    return 1;
                }else{
                    return 0;
                }

            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 0;
    }
    public function getfansv2($param = ''){

        $result['state'] = '000';
        //ini_set('display_errors','On');
        //error_reporting(-1);
        $type = $this->input->get('type')?$this->input->get('type'):0;
        if ($this->input->get('page') and $uid = $this->input->get('uid')) {
            $page = intval($this->input->get('page')-1);
            $start = $page * 50;
            $this->db->select('wen_follow.fid as fid,wen_follow.uid as uid');
            $this->db->from('wen_follow');
            if($type == 1) {
                $this->db->where('wen_follow.fid', $uid);
            }else{
                $this->db->where('wen_follow.uid', $uid);
            }
            $this->db->where('wen_follow.type', 8);
            $this->db->order_by('follow_id desc');
            $this->db->limit(50, $start);
            $tmp = $this->db->get()->result_array();
            //$result['debug'] = $this->db->last_query();

            $result['data'] =  array();
            foreach($tmp as $r){

                $this->db->select('users.alias as uname, users.username as username,users.id as uid, users.jifen as jifen');
                $this->db->from('wen_follow');
                if($type == 1) {
                    //$r['thumb'] = $this->profilepic($r['uid'],0);
                    $this->db->where('wen_follow.uid', $r['uid']);
                    $this->db->join('users', 'users.id = wen_follow.uid');
                }else{
                    //$r['thumb'] = $this->profilepic($r['fid'],0);
                    $this->db->where('wen_follow.fid', $r['fid']);
                    $this->db->join('users', 'users.id = wen_follow.fid');
                }
                $this->db->limit(1);
                $this->db->order_by('wen_follow.follow_id desc');
                $retmp = $this->db->get()->result_array();
                if($type == 1){
                    $r['thumb'] = $this->profilepic($r['uid'],0);
                }else{
                    $r['thumb'] = $this->profilepic($r['fid'],0);
                }
                if(!empty($retmp)){

                    //$rs['daren'] = 1;
                    $rs['state'] = $this->isstate(8,$retmp[0]['uid']);
                    $rs['level'] = $this->isLevel($retmp[0]['jifen']);
                    unset($retmp[0]['jifen']);
                    $rs['thumb'] = $r['thumb'];
                    $this->db->where('uid',$retmp[0]['uid']);
                    $this->db->order_by('newtime','desc');
                    $res = $this->db->get('wen_weibo')->result_array();
                    if(!empty($res) && isset($res[0]['message'])) {
                        $rs['message'] = $res[0]['message'];
                    }else{
                        $rs['message'] = '';
                    }
                    $rs['alias'] = $retmp[0]['uname'] ? $retmp[0]['uname']:$retmp[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$rs['alias'] )){
                        $rs['alias']  = substr($rs['alias'] ,0,4).'****';
                    }
                    $rs['uname'] =  $rs['alias'];
                    unset($rs['alias']);
                    $rs['uid'] = $retmp[0]['uid'];
                    $result['data'][] = $rs;

                }
            }
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
	public function getfans($param = ''){
        //ini_set('display_errors','on');
        $result['state'] = '000';
        //error_reporting(E_ALL);
		//$result['state'] = '000';
        $type = $this->input->get('type')?$this->input->get('type'):0;
	    if ($this->input->get('page') and $uid = $this->input->get('uid')) {
			$page = intval($this->input->get('page')-1);
            $limit = (intval($this->input->get('limit')) > 0)?intval($this->input->get('limit')):50;
			$start = $page * $limit;
            $this->db->select('wen_follow.fid as fid,wen_follow.uid as uid');
			$this->db->from('wen_follow');
            if($type == 1) {
                $this->db->where('wen_follow.fid', $uid);
            }else{
                $this->db->where('wen_follow.uid', $uid);
            }
            $this->db->where('wen_follow.type', 8); //普通用户 8
            $this->db->order_by('follow_id desc');
			$this->db->limit(50, $start);
			$fanstmp = $this->db->get()->result_array();

            $result['debug'] = $this->db->last_query();
			$result['data'] =  array();
		    foreach($fanstmp as $r){
				$r['thumb'] = $this->profilepic($r['fid'],0);

                $this->db->select('users.alias as uname, users.username as username,users.id as uid, users.jifen as jifen, users.city, users.age');
                $this->db->from('wen_follow');
                if($type == 1) {
                    $this->db->where('wen_follow.uid', $r['uid']);
                    $this->db->join('users', 'users.id = wen_follow.uid');
                }else{
                    $this->db->where('wen_follow.fid', $r['fid']);
                    $this->db->join('users', 'users.id = wen_follow.fid');
                }
                $this->db->limit(1);

                $retmp = $this->db->get()->result_array();
                //echo $this->db->last_query();
                if($type == 1) {
                    $r['thumb'] = $this->profilepic($r['uid'], 0);
                }else{
                    $r['thumb'] = $this->profilepic($r['fid'], 0);
                }
                if(!empty($retmp)){

                    $rs['daren'] = 1;
                    $rs['state'] = $this->isstate(8,$retmp[0]['uid']);
                    $rs['level'] = $this->isLevel($retmp[0]['jifen']);
                    unset($retmp[0]['jifen']);
                    $rs['thumb'] = $r['thumb'];
                    $this->db->where('uid',$retmp[0]['uid']);
                    $this->db->order_by('newtime','desc');
                    $res = $this->db->get('wen_weibo')->result_array();
                    $rs['age'] = $this->getAge(intval($retmp[0]['age']));
                    $rs['city'] = '';
                    $rs['sex'] = 1;
                    if(!empty($res) && isset($res[0]['message'])) {
                        $rs['message'] = $res[0]['message'];
                    }else{
                        $rs['message'] = '';
                    }

                    $rs['alias'] = !empty($retmp[0]['uname']) ? $retmp[0]['uname']:$retmp[0]['username'];
                    if(preg_match("/^1[0-9]{10}$/",$rs['alias'] )){
                        $rs['alias']  = substr($rs['alias'] ,0,4).'****';
                    }
                    $rs['uname'] =  $rs['alias'];
                    $rs['username'] = $rs['uname'];
                    unset($rs['alias']);
                    $rs['uid'] = $retmp[0]['uid'];
                    $result['data'][] = $rs;

                }
		    }
	    } else {
		    $result['state'] = '012';
	    }
		echo json_encode($result);
	}
    //guangzhu
	private function Cgz($uid){
       $this->db->where('uid', $uid);
       $this->db->from('wen_follow');
       return $this->db->count_all_results();
	}
	//favrite
	private function Cfavr($uid){
       $this->db->where('uid', $uid);
       $this->db->from('wen_favrite');
       return $this->db->count_all_results();
	}
	private function Cfensi($uid){
       $this->db->where('fid', $uid);
       $this->db->from('wen_follow');
       return $this->db->count_all_results();
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		switch ($pos) {
			case 1:
			    return $this->remote->thumb($id,'36');
			case 0:
			    return $this->remote->thumb($id,'250');
		    case 2:
                return $this->remote->thumb($id,'120');
			default:
			    return $this->remote->thumb($id,'120');
				break;
		}
	}

    private function getFollow($uid = 0){

        $type = 9;
        $result['data'] = array();

        $this->uid = $uid;

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
                    $this->db->select('cid, tag_img as surl, tag');
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
            $result['data'] = $res_items?$res_items:array();
        }
        return $result['data'];
    }
}
?>
