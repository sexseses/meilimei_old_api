<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class comments extends CI_Controller {
	private $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
	 	$this->load->library('filter');
		$this->load->model('auth');
		$this->load->model('remote');
		$this->load->model('track_error');
	}
	//send to topic
	public function sendcomment($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->notlogin OR !$this->uid) {
				$result['notice'] = '账户未登入！';
				$result['ustate'] = '001';
			} else {
				if (($type = strip_tags($this->input->post('type')))  and ($contentid = intval($this->input->post('contentid')))) {
					$pid = intval($this->input->post('commentTo'));
					if(strlen($this->input->post('comment'))<2){
						$result['state'] = '012';
						$result['notice'] = '评论内容过短！';
						$this->track_error->L($this->input->post('comment').$result['state']);
						echo json_encode($result);
						exit;
					}
					//check illegal word
					if(!$this->filter->judge($this->input->post('comment'))){
						$result['state'] = '012';
						$this->track_error->L($this->input->post('comment').$result['state']);
						$result['notice'] = '含有广告等信息！';
						echo json_encode($result);
						exit;
					}
					//check weibo
					if(!$this->cktopic($contentid)){
                        $result['state'] = '400';
                        $this->track_error->L($this->input->post('comment').$result['state']);
                        $result['notice'] = '该话题已经被删除！';
						echo json_encode($result);
						exit;
					}
					if($pid>0){
                       $PCID = $this->GPCID($pid);
                       if(!$PCID){
                       	 $this->track_error->L($this->input->post('comment').$pid.'PCID:'.$PCID);
                       	 $result['notice'] = '该评论已被删除！';
					     $result['state'] = '012';
						 echo json_encode($result);
						 exit;
                       }
					}else{
						$PCID = 0;
					}
                    if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {

					$Idata = array();

						$datas['name'] = uniqid(time(), false) . '.jpg';
						$picturesave = date('Y').'/' . date('m').'/' . date('d').'/' . $datas['name'];;
						$tmpinfo = getimagesize($_FILES['attachPic']['tmp_name']);
						if(!$this->remote->cp($_FILES['attachPic']['tmp_name'],$datas['name'],$picturesave,array('width'=>500,'height'=>500),true)){
                           $result['state'] = '001';
                           $result['notice'] = '图片上传失败！';
                           mail('muzhuquan@126.com','debug',serialize($datas));
                            echo json_encode($result);
                            exit;
						}
						$result['updatePictureState'] = '000';
						$upicArr[0]['type'] = 'jpg';
						$upicArr[0]['height'] = $tmpinfo[1];
						$upicArr[0]['width'] = $tmpinfo[0];
						$upicArr[0]['path'] = $picturesave;
						$upicArr[0]['uploadTime'] = time();
						$Idata['data'] = serialize($upicArr);

				  }
				    $head = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Android';
				    if ((stristr($head,'iPhone') and !stristr($head,'U;')) OR  stristr($head,'ipod')) {
		                $Idata['device'] = 'IOS';
		            } else {
			           $Idata['device'] = 'Android';
		            }
		            //special commment?
		            if($this->wen_auth->get_role_id()!=1){
                       $Idata['type'] = 'ans';
		            }
					$Idata['type'] = $type;
					$Idata['pid'] = $pid ;
					$Idata['pcid'] =  $PCID;
					$Idata['contentid'] =  $contentid;
					$Idata['fuid'] = $this->uid;
					$Idata['cTime'] =  time();
					$Idata['comment'] =  strip_tags($this->input->post('comment')) ;
                    $result['notice'] = '回复成功！';
					$this->db->insert('wen_comment', $Idata);
					if($PCID>0){
						$this->db->query("update wen_comment set new_reply = new_reply+1 where id = {$PCID} limit 1 ");
					}
					$this->wen_auth->set_weibo_rjifen($this->uid);
					$this->db->query("update wen_weibo set comments=comments+1,commentnums=commentnums+1 where weibo_id = '$contentid' limit 1 ");
					$judge = $this->db->query("select uid from  wen_weibo where weibo_id = '$contentid' limit 1 ")->result_array();

                    //get comment in page
                    $this->db->where('type', 'topic');
                    $this->db->where('contentid',$contentid);
                    $this->db->from('wen_comment');
                    $tmpage  = $this->db->count_all_results()/5;
                    $result['pagesize'] = 5;
                    if(is_int($tmpage)){
                    	$result['page'] = $tmpage;
                    }else{
                    	$result['page'] = intval($tmpage)+1;
                    }

                    if(count($judge) and $this->uid != $judge[0]['uid']){
                       //send apple push
                      $this->load->model('push');
                      $push = array('type'=>'topic','id'=>$contentid,'page'=>$result['page']);
                      $this->push->sendUser('[话题]新回复:'.$Idata['comment'],$judge[0]['uid'],$push);
                    }
                    //deal extra chain data
                    $this->load->model('user_sum');
					$this->user_sum->addJifen($this->uid,'JIFEN_RWEIBO');
					$this->user_sum->addGrowth($this->uid,'GROW_RTOPIC');
                 } else {
					$log['api'] = 'comments/sendcomment';
					$log['type'] = $this->input->post('type');
					$log['contentid'] = $this->input->post('contentid');
					$this->track_error->L($log);
					$result['notice'] = '信息不完整！';
					$result['state'] = '012';
				}
			}
		} else {
			$result['notice'] = 'Token无效！';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//check exists topic
	private function cktopic($wid){
        $this->db->where('weibo_id', $wid);
        $this->db->select('weibo_id');
        $this->db->from('wen_weibo');
        return $this->db->count_all_results();
	}
	//get top parent comment id
	private function GPCID($pid){
        $this->db->where('id', $pid);
        $this->db->select('pid,id,fuid');
        $query = $this->db->get('wen_comment')->result_array();
        if(!empty($query)){
        	 $this->load->model('push');
             $this->push->sendUser('[话题]你的评论有新回复',$query[0]['fuid']);
        	return $query[0]['id'];
        }else{
        	$this->GPCID($query[0]['pid']);
        }
	}

	//get comments
	public function Gcomments($param = '') {
		$result['state'] = '000';
		//if ($this->auth->checktoken($param)) {
			if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid'))) ) {
				$page = intval($this->input->get('page')) - 1;
				$start = $page<1?0:$page*5;
				$fields = 'users.banned,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.new_reply,c.data,c.is_delete';
				$sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
				$sql .= "WHERE type = '{$type}' and contentid={$contentid}  order by c.id ASC limit $start,5";
				$tmp = $this->db->query($sql)->result_array();
				$result['data'] = array();
				$i=1+$start;
				$result['ans'] = array();
				if($start==0){
					$result['ans'] = $this->Gans($contentid);
				}

				foreach($tmp as $r){
					if(time()-$r['cTime']<3600*10){
						if(time()-$r['cTime']<3600){
                           $r['cTime'] = intval((time()-$r['cTime'])/60).'分钟前';
						}else{
                           $r['cTime'] = intval((time()-$r['cTime'])/3600).'小时前';
						}
					}else{
						$r['cTime'] = date('Y年m月d日',$r['cTime']);
					}
					if($r['uname']!='' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])){
						$r['uname'] = substr($r['uname'],0,4).'***';
					}elseif($r['uname']=='') {
						$r['uname'] = substr($r['phone'],0,4).'***' ;
					}

					unset($r['phone']);
                    $r['floor'] = $i;
                    $r['banned']&&$r['is_delete']=1;
                    if(!$r['uid']){
                    	$r['banned'] = 1;
                    	$r['uid'] = 0;
                    	$r['is_delete']=1;
                    }
                    //clear user new_reply
                    $this->setnew($r['id'],$r['uid']);
                    $r['thumb'] = $this->profilepic($r['uid'],1);
                    $r['haspic'] = '0';
                    $tmp = unserialize($r['data']);
					if(isset($tmp[0]['path']) and $tmp[0]['path']){
						$r['haspic'] = '1';
						$r['picture'] = $this->remote->show($tmp[0]['path']);
						$r['height'] = isset($tmp[0]['height'])?$tmp[0]['height']:200;
						$r['width'] =  isset($tmp[0]['width'])?$tmp[0]['width']:200;
					}
                    unset($r['data']);
                    $r['is_reply'] = '0';
                    if($r['pid']>0){
                    	$r['is_reply'] = '1';
                      $fields = 'users.banned,users.email,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.data,c.is_delete';
				      $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
				      $sql .= "WHERE type = '{$type}' and c.id={$r['pid']}  order by c.id ASC limit 1";
				      $tmps = $this->db->query($sql)->result_array();
				      $r['replay']['uname'] = $tmps[0]['uname'];
				      if($tmps[0]['uname']!='' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $tmps[0]['uname'])){
						$r['replay']['uname'] = substr($tmps[0]['uname'],0,4).'***';
					  }elseif($tmps[0]['uname']=='') {
						$r['replay']['uname'] = substr($tmps[0]['phone'],0,4).'***' ;
					  }
				      $r['replay']['comment'] = $tmps[0]['comment'].'';
				      $r['replay']['is_delete'] = $tmps[0]['is_delete'].'';
				      $tmps[0]['banned']&&$r['replay']['is_delete']=1;
				      if(!$tmps[0]['uid']){
                        $tmps[0]['uid'] = 0;
				      }
				      $tmps = null;
                    }
                    $result['data'][] = $r;
                    $i++;
				}
			} else {
				$result['state'] = '012';
			}
		//} else {
		//	$result['state'] = '001';
		//}
		echo json_encode($result);
	}
	//get answer type commment
	private function Gans($id){
		$res = array();
		$fields = 'wen_comment.*,users.alias as uname,user_profile.company';
        $gtmp = $this->db->query("SELECT {$fields} FROM wen_comment LEFT JOIN users  ON users.id = wen_comment.fuid LEFT JOIN user_profile ON user_profile.user_id = wen_comment.fuid WHERE wen_comment.contentid = {$id} AND type='ans' AND wen_comment.is_delete = 0 order by wen_comment.id DESC")->result_array();
      //  print_r($tmp);
        foreach ($gtmp as $row) {
			 $row['cdate'] = date('Y-m-d', $row['cTime']);
			 $row['thumb'] = $this->profilepic($row['fuid'], 1);
			 $res[] = $row;
	  }
	  return $res;
	}
	//clear new_reply
	private function setnew($id=0,$cuid=0){
		if($id && $cuid && $cuid==$this->uid){
           $data = array(
               'new_reply' => 0
           );
           $this->db->limit(1);
           $this->db->where('id', $id);
           $this->db->where('fuid', $cuid);
           $this->db->update('wen_comment', $data);
           $mec = new Memcache();
           $mec->connect('127.0.0.1', 11211);
           $mec->set('state'.$this->uid,array(),0,3600);
           $mec->close();
		}
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
}
?>