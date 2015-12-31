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
	}
	public function sendcomment($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->notlogin) {
				$result['ustate'] = '002';
			} else {
				if (($type = $this->filter->filts($this->input->post('type')))  and ($contentid = intval($this->input->post('contentid')))) {
					$pid = intval($this->input->post('commentTo'));
					if($pid>0){
                       $PCID = $this->GPCID($pid);
					}else{
						$PCID = 0;
					}
                    if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name']) {
					$target_path = realpath(APPPATH . '../upload');
					$Idata = array();
					if (is_writable($target_path)) {
						$tmpdir = date('Y');
						if (!is_dir($target_path . '/' . $tmpdir)) {
							mkdir($target_path . '/' . $tmpdir, 0777, true);
						}
                        $tmpdir .= '/'.date('m');
						if (!is_dir($target_path . '/' . $tmpdir)) {
							mkdir($target_path . '/' . $tmpdir, 0777, true);
						}
						$datas['name'] = uniqid() . '.jpg';
						$picturesave = $tmpdir . '/' . $datas['name'];
						$target_path = $target_path . '/' . $picturesave;
						move_uploaded_file($_FILES['attachPic']['tmp_name'], $target_path);
                        $tmpinfo = getimagesize($target_path);
						$result['updatePictureState'] = '000';
						$upicArr[0]['type'] = 'jpg';
						$upicArr[0]['height'] = $tmpinfo[1];
						$upicArr[0]['width'] = $tmpinfo[0];
						$upicArr[0]['path'] = $picturesave;
						$upicArr[0]['uploadTime'] = time();
						$Idata['data'] = serialize($upicArr);
					}
				  }
					$Idata['type'] = $type;
					$Idata['pid'] = $pid ;
					$Idata['pcid'] =  $PCID;
					$Idata['contentid'] =  $contentid;
					$Idata['fuid'] = $this->uid;
					$Idata['cTime'] =  time();
					$Idata['comment'] =  strip_tags($this->input->post('comment')) ;
					$this->db->insert('wen_comment', $Idata);
					$this->wen_auth->set_weibo_rjifen($this->uid);
					$this->db->query("update wen_weibo set comments=comments+1,commentnums=commentnums+1 where weibo_id = '$contentid' limit 1 ");
					//$this->db->query("update wen_notify set comments=comments+1,commentnums=commentnums+1 where  user_id = '$contentid' limit 1 ");
				} else {
					$result['state'] = '012';
				}
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//get top parent comment id
	private function GPCID($pid){
        $this->db->where('id', $pid);
        $this->db->select('pid');
        $query = $this->db->get('wen_comment')->result_array();
        if($query[0]['pid']==0){
        	return $pid;
        }else{
        	$this->GPCID($query[0]['pid']);
        }
	}
	public function Gcomments($param = '') {
		$result['state'] = '000';
		if ($this->auth->checktoken($param)) {
			if (($type = trim($this->input->get('type'))) and ($contentid = intval($this->input->get('contentid'))) ) {
				$page = intval($this->input->get('page')) - 1;
				$start = $page<1?0:$page*5;
				$fields = 'users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.pid,c.pcid,c.data,c.is_delete';
				$sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
				$sql .= "WHERE type = '{$type}' and contentid={$contentid}  order by c.id ASC limit $start,5";
				$tmp = $this->db->query($sql)->result_array();
				$result['data'] = array();
				$i=1+$start;
				foreach($tmp as $r){
					if(time()-$r['cTime']<3600*10){
						if(time()-$r['cTime']<3600){
                           $r['cTime'] = intval((time()-$r['cTime'])/60).'分钟前';
						}else{
                           $r['cTime'] = intval((time()-$r['cTime'])/3600).'小时前';
						}
					}else{
						$r['cTime'] = date('Y-m-d H:i',$r['cTime']);
					}
					if($r['uname']!='' and preg_match('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/', $r['uname'])){
						$r['uname'] = substr($r['uname'],0,4).'***';
					}elseif($r['uname']=='') {
						$r['uname'] = substr($r['phone'],0,4).'***' ;
					}

					unset($r['phone']);
                    $r['floor'] = $i;
                    $r['thumb'] = $this->profilepic($r['uid'],1);
                    $r['haspic'] = '0';
                    $tmp = unserialize($r['data']);
					if(isset($tmp[0]['path']) and $tmp[0]['path']){
						$r['haspic'] = '1';
						$r['picture'] = site_url().'upload/'.$tmp[0]['path'];
						$r['height'] = isset($tmp[0]['height'])?$tmp[0]['height']:200;
						$r['width'] =  isset($tmp[0]['width'])?$tmp[0]['width']:200;
					}
                    unset($r['data']);
                    $r['is_reply'] = '0';
                    if($r['pid']>0){
                    	$r['is_reply'] = '1';
                      $fields = 'users.email,users.alias as uname,users.phone,users.id as uid,c.id,c.contentid,c.comment,c.cTime,c.data,c.is_delete';
				      $sql = "SELECT {$fields} FROM wen_comment as c LEFT JOIN users ON users.id = c.fuid ";
				      $sql .= "WHERE type = '{$type}' and c.id={$r['pid']}  order by c.id ASC limit 1";
				      $tmps = $this->db->query($sql)->result_array();
				      (is_int($tmps[0]['uname']) and $tmps[0]['uname']!='')&&$r['replay']['uname'] = substr($tmps[0]['uname'],0,4).'***';
				      $r['replay']['uname']==''&& $r['replay']['uname'] = substr($tmps[0]['phone'],0,4).'***' ;
				      $r['replay']['uname']=='***'&& $r['replay']['uname'] = substr($tmps[0]['cTime'],0,4).'***';
				      $r['replay']['comment'] = $tmps[0]['comment'];
				      $tmps = null;
                    }
                    $result['data'][] = $r;
                    $i++;
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		if (is_dir($this->path . '/users/' . $id)) {
			$files = scandir($this->path . '/users/' . $id);
			$files = array_diff($files, array (
				'.',
				'..'
			));
			if (count($files) > 1) {
				if ($pos == 1) {
					$url = base_url() . '/images/users/' . $id . '/userpic_thumb.jpg';
				} else
					if ($pos == 2) {
						$url = base_url() . 'images/users/' . $id . '/userpic_profile.jpg';
					} else {
						$url = base_url() . 'images/users/' . $id . '/userpic.jpg';
					}
			} else {
				if ($pos == 1) {
					$url = base_url() . 'images/no_avatar_thumb.jpg';
				} else
					if ($pos == 2) {
						$url = base_url() . 'images/no_avatar-xlarge.jpg';
					} else {
						$url = base_url() . 'images/no_avatar.jpg';
					}

			}
		} else {
			if ($pos == 1) {
				$url = base_url() . 'images/no_avatar_thumb.jpg';
			} else
				if ($pos == 2) {
					$url = base_url() . 'images/no_avatar-xlarge.jpg';
				} else {
					$url = base_url() . 'images/no_avatar.jpg';
				}
		}
		return $url;
	}
}
?>