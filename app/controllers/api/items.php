<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api info Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class items extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->model('auth');
		$this->load->model('remote');
		$this->load->library('alicache');
	}
	function getItems($param = '') {
		if ($this->auth->checktoken($param)) {
			$pid = intval($this->input->get('pid'));
			if ($pid == 261) {
				$this->db->where('is_hot', 1);
			} else {
				$this->db->where('pid', $pid);
			}
			$this->db->select('id, pid, name,surl,burl');
			$this->db->order_by("order", "desc");
			$tmp = $this->db->get('items')->result_array();
			$result['data'] = array ();
			foreach ($tmp as $r) {
				$r['burl'] = site_url() . 'upload/' . $r['burl'];
				$r['surl'] = site_url() . 'upload/' . $r['surl'];
				$result['data'][] = $r;
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//auto complte jquery search
	public function jsearch() {
		$result = array ();
		$c = strip_tags(trim($this->input->get('xm')));
		$SQL = "select name from items where name like '{$c}%' limit 10";
		if ($c) {
			$mec = new Memcache();
			$mec->connect('127.0.0.1', 11211);
			if ($result = $mec->get('itm_' . $c)) {
			} else {
				$result = $this->db->query($SQL)->result_array();
				if (!empty ($result)) {
					$mec->set('itm_' . $c, $result, 0, 1800);
				}
			}
			$mec->close();
		}
		echo json_encode($result);
	}
	function cItems($param = '') {
		if ($this->auth->checktoken($param)) {
			$result['state'] = '000';
			$this->db->select('id, pid, name,surl,burl');
			$this->db->order_by("order", "desc");
			$this->db->where("app", 1);
			$tmp = $this->db->get('items')->result_array();
			$this->db->last_query();
			$result['data'] = array ();

			foreach ($tmp as $r) {
				$r['burl'] = $this->remote->show($r['burl']);
				$r['surl'] = $this->remote->show($r['surl']);
				$result['data'][] = $r;
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function allItems($param = '') {
		if ($this->auth->checktoken($param)) {
			$result['state'] = '000';
			$this->db->select('id, pid, name,surl,burl,is_hot');
			$this->db->order_by("order", "desc");
			/*$mec = new Memcache();
			$mec->connect('127.0.0.1', 11211);
			if ($result['data'] = $mec->get('api_allItems') and !empty ($result['data'])) {
			} else {*/
				$tmp = $this->db->get('items')->result_array();
				$result['data'] = array ();
				foreach ($tmp as $r) {
					$r['burl'] = $this->remote->noDNSCUrl($r['burl']);
					$r['surl'] = $this->remote->noDNSCUrl($r['surl']);
					if ($r['is_hot'] == 1) {
						unset ($r['is_hot']);
						$result['data'][] = $r;
						$r['pid'] = '261';
						$result['data'][] = $r;
					} else {
						unset ($r['is_hot']);
						$result['data'][] = $r;
					}

				}
			/*	$mec->set('api_allItems', $result['data'], 0, 7200);
			}*/
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
		//$this->mec->close();
	}
	//new get item info width more info api
	function getIMtemInfo($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($id = intval($this->input->get('id'))) {
				$result['state'] = '000';
				$this->db->where('id', $id);
				$this->db->order_by("id", "desc");
				$tmp = $this->db->get('items')->result_array();
				$result['iteminfo'] = $tmp[0];
				$result['iteminfo']['surl'] = $this->remote->show($tmp[0]['surl']);
				$result['iteminfo']['burl'] = $this->remote->show($tmp[0]['burl']);
				$result['iteminfo']['items'] = $this->getTags();
				$result['tehui'] = $this->getSales($tmp[0]['name']);
				$result['price'] = $this->getPrice($tmp[0]['id']);
				$result['doctor'] = $this->getDoctor($tmp[0]['name']);
			} else {
				$result['notice'] = '参数不全';
				$result['state'] = '012';
			}
		} else {
			$result['notice'] = 'Token错误';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	// get Doctor with tags
	private function getDoctor($tag = ''){
		$sql = "select p.company,u.alias,u.id,u.suggested,u.grade,u.verify from users as u ";
        $sql .= "LEFT JOIN user_profile as p ON p.user_id = u.id where u.utags = '{$tag}' limit 5";
        $tmpinfo = $this->db->query($sql)->result_array();
        $res = array();
        foreach($tmpinfo as $r){
           $r['thumb'] = $this->profilepic($r['id']);
           $res[] = $r;
        }
        return $res;
	}
	// get price fo jigou
	private function getPrice($id = ''){
		$sql = "select p.price,c.userid,c.name FROM price as p";
        $sql .= " LEFT JOIN company as c ON c.id = p.company_id where p.item_id = '{$id}' limit 5";
        $tmpinfo = $this->db->query($sql)->result_array();
        return $tmpinfo;
	}
	// get tehui product with tags
	private function getSales($tag = '') {
		$time = time();
		$this->tehuiDB = $this->load->database('tehui', TRUE);
		$fields = 't.id,t.user_id,t.title,t.summary,t.image,t.team_price, t.now_number,t.market_price';
		$condition = "t.team_type='normal' and t.begin_time <= '{$time}' and t.end_time >= '{$time}'";
		$condition .= " AND INSTR(t.tags,'{$tag}')";
		$order = ' t.sort_order DESC,t.begin_time DESC, t.id DESC';
		$res = array ();
		$tmpinfo = $this->tehuiDB->query("SELECT {$fields} FROM team as t  WHERE {$condition} ORDER by {$order} limit 5 ")->result_array();

        $randpic = date('Ymdhi', time());
		foreach ($tmpinfo as $r) {
			$r['team_price'] = intval($r['team_price']);
			$r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'] . '?' . $randpic;
			$res[] = $r;
		}
		return $res;
	}
	//get sumarize intem info api
	function getItemInfo($param = '') {
		//if ($this->auth->checktoken($param)) {
			$id = $result['data'] = '';
			if($name = $this->input->get('name')){
				$id = $this->getId($name);
			}
			if ($id OR ($id = intval($this->input->get('id')))) {
				$result['state'] = '000';
				$this->db->where('id', $id);
				$this->db->order_by("id", "desc");
				$tmp = $this->db->get('items')->result_array();
				$result['data'] = array ();
				$result['data']['id'] = $tmp[0]['id'];
				$result['data']['pid'] = $tmp[0]['pid'];
				$result['data']['name'] = $tmp[0]['name'];
				$result['data']['safety'] = $tmp[0]['safety']*20;
				$result['data']['satisfaction'] = $tmp[0]['satisfaction']*20;
				$result['data']['attention'] = $tmp[0]['attention']*20;

				$result['data']['surl'] = $this->remote->noDNSCUrl($tmp[0]['surl']);
				$result['data']['burl'] = $this->remote->noDNSCUrl($tmp[0]['burl']);
				$result['data']['price'] = intval($tmp[0]['price']);
				$result['data']['des'] = $tmp[0]['des'];
				$result['data']['is_hot'] = $tmp[0]['is_hot'];
				$result['data']['is_fav'] = 0;
				if($this->uid){
					$this->db->where('tagid', $id);
					$this->db->where('uid', $this->uid);
					$this->db->from('myTags');
					if($this->db->count_all_results()){
                       $result['data']['is_fav'] = 1;
					}
				}
			} else {
				$result['state'] = '012';
			}
		/*} else {
			$result['state'] = '001';
		}*/
		echo json_encode($result);
	}
    //use jigou name get its id
    private function getId($name=''){
       if($name){
       	  $this->db->select('id');
          $this->db->where('name', $name);
          $this->db->limit(1);
          $tmp = $this->db->get('items')->result_array();
          if(!empty($tmp)){
             return $tmp[0]['id'];
          }else{
          	return 0;
          }
       }
    }
	//get tags of item
	private function getTags() {
		$res = array ();
		$res['DStreatments'] = '治疗方法';
		$res['XGtreatment'] = '治疗效果';
		$res['notice'] = '注意事项';
		$res['crowd'] = '适合人群';
		$res['treatment_time'] = '治疗次数';
		$res['recovery_process'] = '恢复过程';
		return $res;
	}

	// get user favorite tags
	function getMyTag($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($this->uid) {
				$result['state'] = '000';
				$this->db->where('uid', $this->uid);
				$this->db->order_by("id", "desc");
				$result['data'] = $this->db->get('myTags')->result_array();
			} else {
				$result['ustate'] = '001';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}

	function getItemsWithUser($param = '') {
		if ($this->auth->checktoken($param)) {
		
			if ($this->input->get('page')) {
				if(!($rs = $this->alicache->get($_SERVER['REQUEST_URI']))){
					$result['state'] = '000';
					$pid = intval($this->input->get('pid'));
					if ($pid == 261) {
						$this->db->where('is_hot', 1);
					}
					elseif ($pid == -1) {

					} else {
						$this->db->where('pid', $pid);
					}
					$page = intval($this->input->get('page') - 1);
					$this->db->limit(8, 8 * $page);
					if ($this->input->get('type') == 1) {
						$this->db->join('myTags', 'myTags.tagid = items.id');
						$this->db->where('myTags.uid', $this->uid);
					}
					$this->db->select('items.id, items.pid, items.name,items.surl,items.burl,items.des');
					$this->db->order_by("order", "DESC");
					$tmp = $this->db->get('items')->result_array();
					$result['data'] = array ();
					foreach ($tmp as $r) {
						$r['burl'] = $this->remote->noDNSCUrl($r['burl']);
						$r['surl'] = $this->remote->noDNSCUrl($r['surl']);
						;
						$r['users'] = $this->getUser($r['name']);
						$result['data'][] = $r;
					}
					$this->alicache->set($_SERVER['REQUEST_URI'],serialize($result));
					//print_r($this->alicache->get($_SERVER['REQUEST_URI']));
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

	private function getUser($item) {
		$this->db->like('tags', $item);
		$this->db->select('uid');
		//$this->db->distinct('uid');
		$this->db->limit(8);
		$this->db->group_by(array('uid','weibo_id'));
		$this->db->order_by("weibo_id", "desc");
		$tmp = $this->db->get('wen_weibo')->result_array();
		$result = array ();
		foreach ($tmp as $r) {
			$r['picture'] = $this->profilepic($r['uid'], 1);
			$result[] = $r;
		}
		return $result;
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		switch ($pos) {
			case 1 :
				return $this->remote->thumb($id, '44');
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
