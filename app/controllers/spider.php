<?php
set_time_limit(0);
/*
 * Created on 2012-10-30
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class spider extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->library('upload');
		$this->load->library('snoopy');
		$this->load->helper('form');$this->load->helper('file');
	}
	public function index() {
		$data['message_element'] = "spider";
		$this->load->view('template', $data);
	}
	public function submit() {
		if ($this->input->post('url') && $this->input->post('page') && $this->input->post('city')) {
			$this->getList($this->input->post('url'), $this->input->post('page'), $this->input->post('city'));
			redirect('spider');
		}
	}
	private function getList($url = '', $page = 18, $city) {
		$this->snoopy->fetchlinks($url);
			foreach ($this->snoopy->results as $k) {
				if (strpos($k, 'hospital/')) {
					$tmpList[] = $k;
				}
			}
		 $this->snoopy->results = null;

		foreach ($tmpList as $k) {
			$this->getDetail($k, $city);
		}
	}
	private function getDetail($url, $city) {
		$info['id'] = uniqid(rand(1000000, 9999999), false);
		$introduce = $url . '/introduction/';
		$this->snoopy->fetch($introduce);
		preg_match_all('/(.*?)<div class="bd_block">(.*?)<\/div>/', $this->snoopy->results, $tmp);
		if(isset($tmp[2][0])){
			$introduce = preg_replace("/<h3[^>]*>.*<\/h3>/", "", $tmp[2][0]);
		}


		$companyname = '';
		preg_match("/<div class=\"head_hos_title\"(.*?)<\/ul>/is", $this->snoopy->results, $tmp);
		preg_match("/<h1(.*?)<\/h1>/is", $tmp[0], $tmp);
		$companyname = trim(strip_tags($tmp[0]));

		$price = $url . '/price/';
		$this->snoopy->fetch($price);
		// preg_match_all('/(.*?)<dd>(.*?)<\/dd>/', $this->snoopy->results, $tmp);
		if($this->snoopy->results){
			preg_match_all("/<dd(.*?)<\/dd>/is", $this->snoopy->results, $tmp);
		$price = array ();
		foreach ($tmp[0] as $k) {
			preg_match_all("/<li(.*?)<\/li>/is", $k, $ptm);
			foreach ($ptm[0] as $r) {
				$pattern = "'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx";
				preg_match_all($pattern, $r, $name);
				preg_match_all("/<em(.*?)<\/em>/is", $r, $val);
				$price[$name[4][0]] = strip_tags($val[0][0]);
			}
		}
		$price = array_filter($price);
		}


		$extrainfo = array ();
		preg_match_all("/<ul class=\"con_detail\"(.*?)<\/ul>/is", $this->snoopy->results, $tmp);
		preg_match_all("/<li(.*?)<\/li>/is", $tmp[0][0], $tmp);
		$tmpinfo = array_filter($tmp[0]);
		$extrainfo['shophours'] = str_replace('营业时间：', '', strip_tags($tmpinfo[0]));
		$extrainfo['web'] = str_replace('官方网址：', '', strip_tags($tmpinfo[1]));
		$extrainfo['tel'] = str_replace('联系电话：', '', strip_tags($tmpinfo[2]));
		$extrainfo['address'] = str_replace('联系地址：', '', strip_tags($tmpinfo[4]));
		$extrainfo['city'] = trim($city);

		$photos = $url . '/photos/';
		$this->snoopy->fetch($photos);
		preg_match_all("/<div class=\"con_img con_img_h\"(.*?)<\/div>/is", $this->snoopy->results, $tmp);
		$pattern = "'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx";
		if(isset($tmp[0][0])){
			preg_match_all($pattern, $tmp[0][0], $tmp);
		}
		$photos = array ();
		if(isset($tmp[2])){
			foreach ($tmp[2] as $r) {
			$this->snoopy->fetch($r);
			preg_match("/<div class=\"pic-box\"(.*?)<\/div>/is", $this->snoopy->results, $pts);
			preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $pts[0], $pts);

			$photos[] = $pts[2];
		}
		$photos = array_filter($photos);
		}

		$team = '';
		$this->snoopy->fetch($url);
		preg_match("/<dl class=\"con_team\"(.*?)<\/dl>/is", $this->snoopy->results, $tmp);

		$pattern = "'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx";
		if(isset($tmp[0])){
			preg_match_all($pattern, $tmp[0], $tmp);
		foreach ($tmp[4] as $k) {
			$team .= ',' . $k;
			$team = substr($team, 1);
		}
		}
		$teamlinks = $tmp[2];

		$info['price'] = $price;
		$info['descrition'] = $introduce;
		$info['extrainfo'] = $extrainfo;
		$info['users'] = $team;
		$info['photos'] = $photos;
		$info['name'] = $info['username'] = $companyname;
		$this->jigouReg($info);
		foreach ($teamlinks as $tr) {
			$this->snoopy->fetch($tr);
            if(!$this->snoopy->results) continue;
			preg_match_all("/<div class=\"bd_block mar_t10\"(.*?)<\/p>/is", $this->snoopy->results, $tmp);
			preg_match("/<p class=\"f14 indent mar_t14\"(.*?)<\/p>/is", $tmp[0][1], $tmp);
			$uinfo['skilled'] = trim(strip_tags($tmp[0]));

			$eintroduce = $tr . '/introduction/';
			$this->snoopy->fetch($eintroduce);
			preg_match("/<div class=\"bd_block mar_t14\"(.*?)<\/p>/is", $this->snoopy->results, $tmp);
			preg_match("/<p class=\"f14 indent mar_t10\"(.*?)<\/p>/is", $tmp[0], $tmp);
			$uinfo['introduce'] = strip_tags($tmp[0]);

			preg_match("/<div class=\"con_peopel\"(.*?)<\/div>/is", $this->snoopy->results, $tmp);
		    preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $tmp[0], $thumburl);
			$name = '';
			preg_match("/<em class=\"name\"(.*?)<\/em>/is", $tmp[0], $name);
			$uinfo['username'] = trim(strip_tags($name[0]));
			preg_match_all("/<dd(.*?)<\/dd>/is", $tmp[0], $tmp);

			preg_match("/<em class=\"title\"(.*?)<\/em>/is", $tmp[0][0], $position);
			preg_match_all("/<em(.*?)<\/em>/is", $tmp[0][1], $sex);

			$uinfo['position'] = trim(strip_tags($position[0]));
			$uinfo['sex'] = trim(strip_tags($sex[0][1])) == '男' ? 2 : 1;
			if (isset ($tmp[0][2])) {
				preg_match_all("/<em(.*?)<\/em>/is", $tmp[0][2], $sn);
				$uinfo['sn'] = trim(strip_tags($sn[0][1]));
			} else {
				$uinfo['sn'] = '';
			}
			$uinfo['thumburl'] = $thumburl[2];
			$uinfo['city'] = $city;$uinfo['company'] = $info['name'];
			$uinfo['id'] = uniqid(rand(1000000, 9999999), false);
			$this->yishiReg($uinfo);
		}
	}
	private function yishiReg($info) {
		$info['password'] = rand(100000, 999999);
		$info['email'] = $info['id'] . '@meilizhensuo.com';
		$info['device_sn'] = uniqid(time(), false);
		$data = $this->wen_auth->register($info['username'], $info['password'], $info['email'], '', $info['device_sn'], '', 2);
		$uid = $data['user_id'];
		if ($uid) {
			$notification['user_id'] = $uid;
			$this->common->insertData('user_notification', $notification);
			$this->common->insertData('wen_notify', $notification);
		}
		$updateData['sn'] = $info['sn'];
		$updateData['sex'] = $info['sex'];
		$updateData['skilled'] = $info['skilled'];
		$updateData['position'] = $info['position'];
		$updateData['city'] = $info['city'];$updateData['company'] = $info['company'];
		$updateData['introduce'] = $info['introduce'];
		$this->db->where('user_id', $uid);
		$this->db->update('user_profile', $updateData);
		$this->thumb($uid,$info['thumburl']);
	}
	private function getTags($html, $tag) {
		$level = 0;
		$offset = 0;
		$return = "";
		$len = strlen($tag);
		$tag = strtolower($tag);
		$html2 = strtolower($html);
		if (strpos($tag, " ")) {
			$temp = explode(" ", $tag);
		}
		$tag_end = (isset ($temp[0])) ? $temp[0] : $tag;
		$i = 0;
		while (1) {
			$seat1 = strpos($html2, "<{$tag}", $offset);
			if (false === $seat1)
				return $return;
			$seat2 = strpos($html2, "</{$tag_end}>", $seat1 +strlen($tag) + 1);
			$seat3 = strpos($html2, "<{$tag}", $seat1 +strlen($tag) + 1);
			while ($seat3 != false && $seat3 < $seat2) {
				$seat2 = strpos($html2, "</{$tag_end}>", $seat2 +strlen($tag_end) + 3);
				$seat3 = strpos($html2, "<{$tag}", $seat3 +strlen($tag) + 1);
			}
			$offset = $seat1 + $len +1;
			$return[$i]['s'] = $seat1;
			$return[$i]['e'] = $seat2 + $len +3 - $seat1;
			$i++;
		}
	}
	private function jigouReg($info) {
		$info['password'] = rand(100000, 999999);
		$info['email'] = $info['id'] . '@meilizhensuo.com';
		$info['device_sn'] = uniqid(time(), false);
 	    $data = $this->wen_auth->register($info['username'], $info['password'], $info['email'], '', $info['device_sn'], '', 3);
 	    $uid = $data['user_id'];

		if ($uid) {
			$notification['user_id'] = $uid;
			$this->common->insertData('user_notification', $notification);
			$this->common->insertData('wen_notify', $notification);
		}
        if ($uid) {
		$updateData['name'] = $info['name'];
		$updateData['tel'] = $info['extrainfo']['tel'];

		$updateData['web'] = $info['extrainfo']['web'];
		$updateData['department'] = '';
		$items = $this->db->get('items');
		$arrItems = array ();
		foreach ($items->result_array() as $k) {
			$arrItems[$k['name']] = $k['id'];
		}

		foreach ($info['price'] as $k => $val) {
			if (isset ($arrItems[$k])) {
				$updateData['department'] .= ',' . $arrItems[$k];
			}
		}
		$updateData['shophours'] = $info['extrainfo']['shophours'];
		$updateData['department'] = substr($updateData['department'], 1);
		$updateData['city'] = $info['extrainfo']['city'];
		$updateData['address'] = $info['extrainfo']['address'];
		$updateData['descrition'] = $info['descrition'];
		$updateData['userid'] = $uid;
		$updateData['cdate'] = time();
		$updateData['users'] = $info['users'];
		$this->db->insert('company', $updateData);
		unset ($updateData);
		$companyid = $this->db->insert_id();
		foreach ($info['price'] as $k => $v) {
			if (isset ($arrItems[$k])) {
				$data = array (
					'userid' => $uid,
					'item_id' => $arrItems[$k],
					'price' => $v,
					'company_id' => $companyid,
				'cdate' => time());
				$this->db->insert('price', $data);
			}
		}
		$basedir = realpath(APPPATH . '../upload/2012/11/') ;
		$inserteData['cTime'] = time();
		$inserteData['userId'] = $uid;
		$inserteData['albumId'] = time();
        $this->thumb($uid,$info['photos'][0]);
		foreach ($info['photos'] as $pr) {
			$get_content = file_get_contents($pr);
			$fdir_tmp = $basedir .'/'. end(explode('/', $pr));
			 file_put_contents($fdir_tmp, $get_content);
			$inserteData['savepath'] = str_replace('/home4/rentwoco/public_html/meilizhensuo/', '', $fdir_tmp);
			$this->db->insert('c_photo', $inserteData);
		}}
	}
   private function thumb($uid, $file) {
		$target_path = realpath(APPPATH . '../images/users');
		if (!is_writable($target_path)) {
			return false;
		} else {
			if (!is_dir($target_path. '/' . $uid)) {
				mkdir($target_path . '/' . $uid, 0777, true);
			}
			$target_path = $target_path . '/' . $uid . '/';
			if ($file != '') {
                $get_content = file_get_contents($file);
				@ file_put_contents($target_path.'userpic.jpg',$get_content );
				GenerateThumbFile($target_path.'userpic.jpg', $target_path.'userpic_thumb.jpg', 36, 36);
				GenerateThumbFile($target_path.'userpic.jpg', $target_path.'userpic_profile.jpg', 120, 120);
				GenerateThumbFile($target_path.'userpic.jpg', $target_path.'userpic.jpg', 250, 250);
				return true;
			} else {
				return false;
			}
		}
	}
}
?>
