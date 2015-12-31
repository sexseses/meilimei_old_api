<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api info Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class items extends MY_Controller {
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

		echo json_encode($result);
	}
	function allItems($param = '') {

		$result['state'] = '000';

		$result['data'] = $this->getChild(0);

        echo json_encode($result);
	}

    private function compareCategory($version){

    }

    function getAllItems($param = '') {

        $result['state'] = '000';
        $version = $this->input->get('category_version');
        $result['data'] = $this->getChild(0);
        foreach($result['data'] as $key=>$item){
            foreach($item['child'] as $k=>$ii){

                $ii['child'] = $this->getChild($ii['id']) ? $this->getChild($ii['id']): array();
                $item['child'][$k] = $ii;

            }
            $result['data'][$key] = $item;
        }
        $result['hot'] = $this->getHot();
        $result['category_version'] = '1.0';
        echo json_encode($result);
    }

    private function getHot($pid = 261){
        $tmp = array();
        $this->db->where('is_hot',1);
        $this->db->select('id, pid, name,surl,burl,colors,is_hot as num');
        $tmp = $this->db->get('items')->result_array();
        $data = array();

        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show($item['burl']);
            $item['surl'] = $this->remote->show($item['surl']);
            
            $data[] = $item;
        }
        return $data;

        return $data;
    }

	private function getChild($pid = 0){
			$tmp = array();
			$this->db->where('pid',$pid);
			$this->db->select('id, pid, name,surl,burl,colors,is_hot as num');
			$tmp = $this->db->get('items')->result_array();

			$data = array();
			$key = array('261');
			foreach($tmp as $key=>$item){
				$item['burl'] = $this->remote->show($item['burl']);
				$item['surl'] = $this->remote->show($item['surl']);

                if ($item['id'] == 261)
                    continue;

				$itemtmp = array();
				$this->db->where('pid',$item['id']);
				$this->db->select("id, pid, name,surl,burl,colors,is_hot as num");
				$itemtmp = $this->db->get('items')->result_array();

				foreach($itemtmp as $k=>$i){
                    if($this->uid) {

                        if ($this->isstate($itemtmp[$k]['id'], 9)) {

                            $itemtmp[$k]['follow'] = 1;//$this->remote->show($itemtmp[$k]['burl']);
                        } else {

                            $itemtmp[$k]['follow'] = 0;//$this->remote->show($itemtmp[$k]['burl']);
                        }
                    }

					$itemtmp[$k]['burl'] = $this->remote->show($itemtmp[$k]['burl']);
					$itemtmp[$k]['surl'] = $this->remote->show($itemtmp[$k]['surl']);
					$tmpitem = $itemtmp;
				}
				$item['child'] = $tmpitem ? $tmpitem : array();
				$data[] = $item;
			}
			return $data;
	}


    private function isstate($followuser,$type) {

        if ($this->uid) {

            if ($followuser) {
                $result['follow'] = '0';
                $condition = array (
                    'uid' => $followuser,
                    'fid' => $this->uid,
                    'type'=> $type
                );

                $tmp = $this->common->getTableData('wen_follow', $condition)->num_rows();

                if ($tmp > 0) {
                    return 1;
                }

            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 0;
    }

	//new get item info width more info api
	function getIMtemInfo($param = '') {

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
	public function getItemChildList($param = '') {

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

			$result['data']['price'] = intval($tmp[0]['price']);
			$result['data']['des'] = $tmp[0]['des'];

			$result['data']['child'] = $this->getItemChild($tmp[0]['id'])?$this->getItemChild($tmp[0]['id']):array();
		} else {
			$result['state'] = '012';
		}

		echo json_encode($result);
	}

    public function getItemInfo(){

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
            $result['data'] = $tmp[0];


        } else {
            $result['state'] = '012';
        }

        echo json_encode($result);
    }

    private function getItemChild($pid = 0){
        $tmp = array();
        $this->db->where('pid',$pid);
        $this->db->select('id, pid, name,surl,burl,colors,is_hot as num');
        $tmp = $this->db->get('items')->result_array();

        $data = array();
        $key = array('261');
        foreach($tmp as $key=>$item){
            $item['burl'] = $this->remote->show($item['burl']);
            $item['surl'] = $this->remote->show($item['surl']);

            $data[] = $item ? $item : array();

        }
        return $data;
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

		if ($this->uid) {
			$result['state'] = '000';
			$this->db->where('uid', $this->uid);
			$this->db->order_by("id", "desc");
			$result['data'] = $this->db->get('myTags')->result_array();
		} else {
			$result['ustate'] = '001';
		}

		echo json_encode($result);
	}

	function getItemsWithUser($param = '') {

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
					$r['burl'] = $this->remote->show($r['burl']);
					$r['surl'] = $this->remote->show($r['surl']);
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
		echo json_encode($result);
	}

    private function addFollow($followuser,$type) {
        $result['state'] = '000';
        $result['updateState'] = '001';
        $data['uid'] = $followuser;
        //$this->uid = 67868;
        if ($data['uid']) {
            $this->db->where('fid',$this->uid);
            $this->db->where('uid',$data['uid']);
            $this->db->where('type',$type);
            $this->db->from('wen_follow');
            if($this->db->count_all_results()){
                return 0;
            }else{
                $data['fid'] = $this->uid;
                $data['type'] = $type;
                $result['updateState'] = '000';
                $this->common->insertData('wen_follow', $data);

            }
            return 1;
        } else {

            return 0;
        }

    }

    public function getFollow($params = ''){
        $result['state'] = '000';

        $type = 9;

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
                $this->db->select('id,name,surl,burl');
                $ires=$this->db->get('items')->result_array();

                if(!empty($ires)){
                    $this->db->where('uid',$this->uid);
                    $this->db->select('cid');
                    $q = $this->db->get('user_fav')->result_array();
                    foreach($ires as $r){
                        if(in_array($r['id'],$p[0])){
                            continue;
                        }
                        $r['burl'] = $this->remote->show($r['burl']);
                        $r['surl'] = $this->remote->show($r['surl']);
                        $res_items[] = $r;
                    }

                }
            }
            $result['data'] = $res_items?$res_items:array();
        } else {
            $result['state'] = '012';
        }
        echo json_encode($result);
    }
    public function updateUserItem($param = '')
    {

        $tags = $this->input->post('tags');

        $result['state'] = '000';
        $result['data'] = array();
        //$this->uid=67868;

        if ($this->uid) {
            if ($tags) {

                $this->db->delete("user_fav", array('uid' => $this->uid));
                $arr_tags = explode(',', $tags);

                if (count($arr_tags) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach ($arr_tags as $item) {
                        $sql .= " name='" . $item . "' or";
                    }
                    $sqltmp = " and (" . substr($sql, 0, strlen($sql) - 2) . ")";

                    $query = $this->db->query("select id,name,surl from items where 1=1" . $sqltmp);
                    $res = $query->result_array();
                    if (!empty($res)) {

                        foreach ($res as $item) {
                            $user_fav = array('cid' => $item['id'], 'uid' => $this->uid, 'tag_img' => $item['surl'], 'tag' => $item['name'], 'created_at' => time(), 'updated_at' => time());
                            $this->db->insert('user_fav', $user_fav);
                        }
                    }
                }

            }
        }else{
            $result['state'] = '012';
        }

        echo json_encode($result);
    }
	public function addUserItem($param = ''){

        $tags = $this->input->post('tags');
        $type = $this->input->post('type');
        $follow = $this->input->post('follow');

        $result['state'] = '000';
        //$this->uid=67868;

		if($this->uid){
            if($tags) {

                $this->db->delete("user_fav", array('uid' => $this->uid));
                $arr_tags = explode(',',$tags);

                if(count($arr_tags) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach ($arr_tags as $item) {
                        $sql .= " name='" . $item . "' or";
                    }
                    $sqltmp = " and (" . substr($sql, 0, strlen($sql) - 2) . ")";

                    $query = $this->db->query("select id,name,surl from items where 1=1".$sqltmp);
                    $res = $query->result_array();
                    if (!empty($res)) {

                        foreach ($res as $item) {
                            $user_fav = array('cid' => $item['id'], 'uid' => $this->uid, 'tag_img' => $item['surl'], 'tag' => $item['name'], 'created_at' => time(), 'updated_at' => time());
                            $this->db->insert('user_fav', $user_fav);
                        }
                    }
                }

            }
            if($follow){
                $arr_follow = explode(',',$follow);

                if(count($arr_follow) > 0) {
                    $sql = '';
                    $sqltmp = '';
                    foreach($arr_follow as $item){
                        $sql .= " name='".$item."' or";
                    }
                    $sqltmp = " and (".substr($sql,0,strlen($sql)-2).")";

                    $query = $this->db->query("select id,name,surl from items where 1=1".$sqltmp);

                    $res = $query->result_array();

                    //$result['sql'] = $this->db->last_query();
                    if (!empty($res)) {
                        foreach ($res as $item) {
                            $result['r'] = $this->addFollow($item['id'],$type);
                        }
                    }
                }

            }
		}else{
			$result['state'] = '012';
		}
		echo json_encode($result);
	}



	public function getHotTags(){

        $this->db->where('is_hot',1);
        $this->db->select('id,name,surl,colors');
        $this->db->limit(9);
        $res = $this->db->get('items')->result_array();
		$result['data'] = array();
		$result['state'] = '000';
		if(!empty($res)){
			foreach($res as $item){

                $item['surl'] =$this->remote->show($item['surl']);;
				$result['data']['hot_tags'][] = $item;
			}
		}
		$this->db->where('is_default',1);
		$this->db->select('id,name,surl,colors');
		$rs_default = $this->db->get('items')->result_array();
		if(!empty($rs_default)){
			foreach($rs_default as $item){
				$item['surl'] =$this->remote->show($item['surl']);;
				$result['data']['default_tags'][] = $item;
			}
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
