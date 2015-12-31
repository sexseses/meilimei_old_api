<?php

/*
 * WENRAN privilege manage
 */
class track extends CI_Model {
	public $privilege, $table = 'privilege', $uid, $special = array (
		'0',
		'6082',
		'6105'
	);
	public function __construct() {
		parent :: __construct();
	}
	//1 eidt 2 add
	public function topic($info, $uid, $type = 2) {
		$data = array (
			'uid' => $uid,
			'type' => $type,
			'topic' => $info,
		'cdate' => time());
		$this->db->insert('user_track', $data);
	}
	public function reply($info, $uid, $type = 2) {
		$data = array (
			'uid' => $uid,
			'type' => $type,
			'reply' => $info,
		'cdate' => time());
		$this->db->insert('user_track', $data);
	}

    //1 eidt 2 add
    public function diary($info, $uid, $type = 2) {
        $data = array (
            'uid' => $uid,
            'type' => $type,
            'diary' => $info,
            'cdate' => time());
        $this->db->insert('user_track', $data);
    }
    public function comments($info, $uid, $type = 2) {
        $data = array (
            'uid' => $uid,
            'type' => $type,
            'comments' => $info,
            'cdate' => time());
        $this->db->insert('user_track', $data);
    }
        //删除reply日志 @$rid 评论ID
        public function delReply($rid){
            if(intval($rid) > 0)
                $this->db->delete('user_track',array('reply'=>$rid));
        }
        
	public function tags($info, $uid, $type = 2) {
		$data = array (
			'uid' => $uid,
			'type' => $type,
			'tags' => $info,
		'cdate' => time());
		$this->db->insert('user_track', $data);
	}
	public function total($uid, $type = 3, $tag = 'topic', $stime, $etime) {
		$this->db->where('uid', $uid);
		$this->db->where($tag . ' > ', 0);
		$this->db->where('type &', $type);
		$this->db->where('cdate <= ', $etime);
		$this->db->where('cdate >= ', $stime);
		$this->db->where('display =  ', 1);
		$this->db->from('user_track');

        return $this->db->count_all_results();
	}
	public function mtagSum($stime, $etime) {
		$sql = "SELECT tags FROM wen_weibo where isdel = 0 ";
		$sql .= "AND ctime <= {$etime} AND ctime >= {$stime}  ";
		$sql .= " order BY weibo_id";
		$tmp = $this->db->query($sql)->result_array();
		$res = array ();
		foreach ($tmp as $r) {
			$r = explode(',', $r['tags']);
			foreach ($r as $i) {
				if ($i) {
					if (isset ($res[$i])) {
						$res[$i]++;
					} else {
						$res[$i] = 1;
					}
				}
			}
		}
		return $res;
	}
	public function topicSum($stime, $etime) {
		$sql = "SELECT COUNT(weibo_id) as num FROM wen_weibo where isdel = 0 ";
		$sql .= "AND ctime <= {$etime} AND ctime >= {$stime}  ";
		$sql .= " order BY weibo_id";
		$tmp = $this->db->query($sql)->result_array();
		return $tmp[0]['num'];
	}

    public function diarySum($stime, $etime) {
        $sql = "SELECT COUNT(nid) as num FROM note where isdel = 0 ";
        $sql .= "AND created_at <= {$etime} AND created_at >= {$stime}  ";
        $sql .= " order BY nid";
        $tmp = $this->db->query($sql)->result_array();

        return $tmp[0]['num'];
    }
	public function tagSum($uid = 0, $type = 3, $stime, $etime) {
		$sql = "SELECT COUNT(id) as sum, tags FROM user_track where type & {$type} ";
		$sql .= "AND cdate <= {$etime} AND cdate >= {$stime} AND tags!='' ";
		$uid > 0 && $sql .= "AND uid  = {$uid}  ";
		$sql .= " GROUP BY tags";
		return $this->db->query($sql)->result_array();
	}
	/*
	 * 巨朋 - 广告主对接接口
	 * advLog 记录
	 * advNotify 通知对方
	 */
	public function advLog($param) {
		$data = array (
			'source' => 'jupeng',
			'mac' => strip_tags(trim($param['mac']
		)), 'sn' => strip_tags(trim($param['idfa'])), 'cdate' => time());
		$this->db->insert('advtrack', $data);
		return true;
	}

	public function advNotify($idfa = '', $mac = '') {
		$send = array ();
		$send['appid'] = 'meilishenqi';
		$send['idfa'] = $idfa;
		$send['mac'] = $mac;
		if ($idfa) {
			$this->db->where('sn', $idfa);
		}
		if ($mac) {
			$this->db->where('mac', $mac);
		}
		$this->db->where('status', 0);
		$this->db->from('advtrack');
		if ($this->db->count_all_results()) {
			$data = array (
				'status	' => 1
			);
			if ($idfa) {
				$this->db->where('sn', $idfa);
			}
			if ($mac) {
				$this->db->where('mac', $mac);
			}
			$this->db->where('status', 0);
			$this->db->limit(1);
			$this->db->update('advtrack', $data);
			for ($i = 0; $i < 6; $i++) {
				$str = $this->Npost('http://wall.jpmob.com/wall/iosAck.jsp', $send);
				$res = json_decode($str);
				if ($res->message == 'ok') {
					break;
				} else {
					usleep(rand(20000, 30000));
				}
			}
		}
	}
	// post data
	function Npost($ac, $vars) {
		$content = http_build_query($vars);
		$content_length = strlen($content);
		$options = array (
			'http' => array (
				'method' => 'POST',
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				"Content-length: $content_length\r\n",
				'content' => $content
			)
		);
		$context = stream_context_create($options);
		return file_get_contents($ac, false, $context);
	}
}
?>
