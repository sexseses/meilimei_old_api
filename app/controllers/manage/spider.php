<?php
class spider extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		$this->load->library('httpdown');
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->library('yisheng');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('remote');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('spider')){
          die('Not Allow');
       }
	}
	public function index() {
		$per_page = 16;
		$start = intval($this->input->get('page'));
		$start == 0 && $start = 1;
        $this->load->library('pager');
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->limit(16, $offset);
		$this->db->select('title, cdate, id,url');
		$this->db->where('state', 0);
		$this->db->where('uid', $this->uid);
		$data['res'] = $this->db->get('temp_jigou')->result_array();
		$data['total_rows'] = $this->db->query("SELECT id FROM temp_jigou where state=0 and uid={$this->uid} ORDER BY id DESC")->num_rows();
        $data['ac_rows'] = $this->db->query("SELECT id FROM temp_jigou where  uid={$this->uid} ORDER BY id DESC")->num_rows();
		$data['offset'] = $offset +1;
		$config =array(
                "record_count"=>$data['total_rows'],
                "pager_size"=>$per_page,
                "show_jump"=>true,
                'base_url'=>'manage/spider/index',
                "pager_index"=>$start
          );
        $this->pager->init($config);
        $data['pagelink'] = $this->pager->builder_pager();
	    $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "spider_jigou";
		$this->session->set_userdata('history_url', 'manage/spider/index?page=' . $start);
		$this->load->view('manage', $data);
	}
	public function topic($page = '') {
		$per_page = 16;
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->limit(16, $offset);
		$this->db->where('state', 0);
		$data['res'] = $this->db->get('temp_topic')->result_array();
		$data['total_rows'] = $this->db->query("SELECT id FROM temp_topic where state=0  ORDER BY id DESC")->num_rows();

		$data['offset'] = $offset +1;
		$data['preview'] = $start > 2 ? site_url('manage/spider/topic/' . ($start -1)) : site_url('manage/spider/topic');
		$data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/spider/topic/' . ($start +1)) : '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "spider_topic";
		$this->load->view('manage', $data);
	}
	public function gjigou() {
		if ($url = $this->input->post('url')) {
			$start = intval($this->input->post('starts'));
			$ends = intval($this->input->post('ends'));
			for ($fix = $start; $fix <= $ends; $fix++) {
				$this->httpdown->OpenUrl($url . $fix);
				$html = $this->httpdown->GetHtml();
				preg_match_all("/<li class=\"shopname\">([^`]*?)<\/li>/is", $html, $res);
				$pat = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/is';
				$nres = array ();
				foreach ($res as $r) {
					foreach ($r as $k) {
						preg_match_all($pat, $k, $t);
						if (strpos($t[0][0], 'shop')) {
							$tmp['title'] = strip_tags($t[0][0]);
							preg_match_all($pat, $t[0][0], $t);
							$tmp['url'] = $t[2][0];
							$nres[] = $tmp;
						}
					}
				}
				foreach ($nres as $r) {
					$this->Gdes($r);
				}
			}
			$this->httpdown->Close();
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "spider_gjigou";
		$this->load->view('manage', $data);
	}
	//get topic infos
	public function gtopic() {
		if ($url = $this->input->post('url')) {
			$start = intval($this->input->post('starts'));
			$ends = intval($this->input->post('ends'));
			for ($fix = $start; $fix <= $ends; $fix++) {
				$this->httpdown->OpenUrl($url . $fix);
				$html = $this->httpdown->GetHtml();
				$html = iconv('gbk', 'utf8', $html);
				preg_match_all("/<div class=\"threadlist_text threadlist_title j_th_tit  notStarList \">([^`]*?)<\/div>/is", $html, $res);
				$pat = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/is';

				$nres = array ();
				foreach ($res[0] as $r) {
					preg_match_all($pat, $r, $tmp);
					$t['title'] = trim($tmp[4][0]);
					$t['url'] = trim($tmp[2][0]);
					$this->Gdes($t, 'topic');
				}
			}
			$this->httpdown->Close();
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "spider_gtopic";
		$this->load->view('manage', $data);
	}

	//private get detail page
	private function Gdes($arr, $type = 'jigou') {
		if (!$this->CK($arr['url'], $type)) {
			if ($type == 'jigou') {
				$this->httpdown->OpenUrl($arr['url']);
				$this->httpdown->OpenUrl('http://www.dianping.com' . $arr['url']);
				$data['url'] = $arr['url'];
				$data['title'] = $arr['title'];
				$data['uid'] = $this->uid;
				$data['content'] = $this->httpdown->GetHtml();
				$data['cdate'] = time();
				$this->db->insert('temp_jigou', $data);
			} else {
				$this->httpdown->OpenUrl($arr['url']);
				$this->httpdown->OpenUrl('http://tieba.baidu.com' . $arr['url']);
				$data['url'] = $arr['url'];
				$data['title'] = $arr['title'];
				$html = $this->httpdown->GetHtml();
				$data['content'] = iconv('gbk', 'utf8', $html);
				$data['cdate'] = time();
				$this->db->insert('temp_topic', $data);
			}
		}
	}
	//check exsits
	private function CK($id, $type = 'jigou') {
		$this->db->select('id');
		if ($type == 'jigou') {
			$this->db->where('url', $id);
			return $this->db->get('temp_jigou')->num_rows();
		} else {
			$this->db->where('url', $id);
			return $this->db->get('temp_topic')->num_rows();
		}
	}
	//edit topic
	function topicEdit($id = '') {
		if ($this->input->post('submit')) {
			$this->initTopic($id);
			$this->addTopic();
		}
		$data['contentid'] = $id;
		$this->db->where('id', $id);
		$this->db->where('state', 0);
		$data['res'] = $this->db->get('temp_topic')->result_array();
		$part = '/<cc>([^`]*?)<\/cc>/is';
		preg_match_all($part, $data['res'][0]['content'], $res);
		$i = 0;
		// get type
		$part = '/<a(.*?)tab_forumname(.*?)>([^`]*?)<\/a>/is';
		preg_match_all($part, $data['res'][0]['content'], $tags);
		$data['tags'] = str_replace('吧', '', $tags[3][0]);
		foreach ($res[0] as $r) {
			if ($i) {
				$data['replys'][] = strip_tags($r);
			} else {
				$preg = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
				preg_match_all($preg, $r, $tmp);
				$data['topic'] = strip_tags($r);
				if (!empty ($tmp)) {
					preg_match('/<[img|IMG].*?width=[\'|\"](.*?)[\'|\"].*?[\/]?>/i', $tmp[0][0], $match);
					if ($match[1] > 200) {
						$data['topic_pics'] = $tmp;
					}
				}
			}
			$i++;
		}
		$data['res'][0]['content'] = '';
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "spider_topic_edit";
		$this->load->view('manage', $data);
	}

	//edit jigou
	function jigouEdit($id = '') {
		if ($this->input->post('submit')) {
			$this->initJigou($id);
			if($this->input->post('is_publish')){
				$this->addJigou();
			}else{
				redirect('manage/spider');
			}

		}
		$data['contentid'] = $id;
		$this->db->where('state', 0);
		$this->db->where('id', $id);
		$data['res'] = $this->db->get('temp_jigou')->result_array();
		if(empty($data['res'])){
			redirect('manage/spider');
		}
		preg_match_all("/<div class=\"shop-location\">([^`]*?)<\/div>/is", $data['res'][0]['content'], $res);
		preg_match_all("/<li>([^`]*?)<\/li>/is", $res[0][0], $res);
		$data['addr'] = trim(strip_tags(str_replace('地址：', '', $res[0][0])));
		$data['tel'] = trim(strip_tags(str_replace('电话：', '', $res[0][1])));

		//get contact
		preg_match_all("/<span class=\"txt\">([^`]*?)<\/a>/is", $data['res'][0]['content'], $res);
		$data['city'] = strip_tags(str_replace('站', '', $res[0][0]));

		preg_match_all("/<div class=\"desc-list Hide\">([^`]*?)<\/a>/is", $data['res'][0]['content'], $res);
		$res = explode('</em>', $res[0][0]);
		$data['contactName'] = $data['shoptime'] = '';
		foreach ($res as $rt) {
			if (strpos($rt, '商户简介：') and $data['contactName'] == '') {
				$data['contactName'] = trim(strip_tags(str_replace('商户简介：', '', $rt)));
			}
			if (strpos($rt, '营业时间：') and $data['contactName'] == '') {
				$data['contactName'] = trim(strip_tags(str_replace('营业时间：', '', $rt)));
			}
			if (strpos($rt, '修改')) {
				$data['shoptime'] = trim(strip_tags(str_replace('修改', '', $rt)));
				continue;
			}
		}

		//  $data['city'] = strip_tags(str_replace('站','',$res[0][0]));

		preg_match_all("/<div class=\"breadcrumb\">([^`]*?)<\/div>/is", $data['res'][0]['content'], $res);
		$pat = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/i';
		preg_match_all($pat, $res[0][0], $res);
		$data['dist'] = strip_tags($res[0][1]);
		$data['deparmtent'] = strip_tags($res[0][3]);

		$preg = '/<ul>([^`]*?)<\/ul>/is';
		preg_match_all($preg, $data['res'][0]['content'], $res);
		foreach ($res[0] as $k => $r) {
			if (strpos($r, 'class="img"')) {
				$plist = $r;
			}
		}
		$preg = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
		preg_match_all($preg, $plist, $res);
		$data['plist']['show'] = $res[0];
		$data['plist']['list'] = $res[1];
		$data['notlogin'] = $this->notlogin;
		$data['keshi'] = $this->yisheng->getKeShi();
		$data['message_element'] = "spider_jigou_edit";
		$this->load->view('manage', $data);
	}

	public function jigouidel($param = '') {
		if ($param) {
			$this->initJigou(intval($param));
			redirect('manage/spider');
		}
	}
	public function topicdel($param = '') {
		if ($param) {
			$this->initTopic(intval($param));
			redirect('manage/spider/topic');
		}
	}
	//initial topic temp table data
	private function initTopic($id) {
		$data = array (
			'state' => 1
		);
		$this->db->where('id', $id);
		$this->db->update('temp_topic', $data);
	}
	//initial jigoui temp table data
	private function initJigou($id) {
		$data = array (
			'state' => 1,
			'content' => ''
		);
		$this->db->where('id', $id);
		$this->db->update('temp_jigou', $data);
	}
	// add topic info
	private function addTopic() {
		if ($this->input->post() && !$this->notlogin and $this->input->post('title')) {
			$info['title'] = $this->input->post('title');
			if ($_FILES['uppics']['tmp_name']) {
				$target_path = realpath(APPPATH . '../upload') . '/';
				if (is_writable($target_path)) {
					$tmpdir = date('Y');
					if (!is_dir($target_path . $tmpdir)) {
						mkdir($target_path . $tmpdir, 0777, true);
					}
					$tmpdir .= '/' . date('m');
					if (!is_dir($target_path . $tmpdir)) {
						mkdir($target_path . $tmpdir, 0777, true);
					}
					$tmpdir .= '/';
					$addpl = array ();
					$datas['name'] = time() . '.jpg';
					$datas['savepath'] = $tmpdir . $datas['name'];
					@ move_uploaded_file($_FILES['uppics']['tmp_name'], $target_path . $datas['savepath']);

					$info['pic']['type'] = 'jpg';
					$info['pic']['savepath'] = $datas['savepath'];
					//insert to db
					$addpl['name'] = $datas['name'];
					$addpl['savepath'] = $datas['savepath'];
					$addpl['userId'] = $this->uid;
					$ptmp = getimagesize($target_path . $datas['savepath']);

					$info['pic']['width'] = $addpl['width'] = $ptmp[0];
					$info['pic']['height'] = $addpl['height'] = $ptmp[1];
					$addpl['cTime'] = time();
					$addpl['type'] = 'jpg';
					$addpl['privacy'] = 0;

				}
			} else
				if ($this->input->post('topics')) { //上传图片
					$target_path = realpath(APPPATH . '../upload') . '/';
					if (is_writable($target_path)) {
						$tmpdir = date('Y');
						if (!is_dir($target_path . $tmpdir)) {
							mkdir($target_path . $tmpdir, 0777, true);
						}
						$tmpdir .= '/' . date('m');
						if (!is_dir($target_path . $tmpdir)) {
							mkdir($target_path . $tmpdir, 0777, true);
						}
						$tmpdir .= '/';
						$addpl = array ();
						$datas['name'] = time() . '.jpg';
						$datas['savepath'] = $tmpdir . $datas['name'];
						$get_content = file_get_contents($this->input->post('topics'));
						@ file_put_contents($target_path . $datas['savepath'], $get_content);
						$info['pic']['type'] = 'jpg';
						$info['pic']['savepath'] = $datas['savepath'];
						//insert to db
						$addpl['name'] = $datas['name'];
						$addpl['savepath'] = $datas['savepath'];
						$addpl['userId'] = $this->uid;
						$ptmp = getimagesize($target_path . $datas['savepath']);

						$info['pic']['width'] = $addpl['width'] = $ptmp[0];
						$info['pic']['height'] = $addpl['height'] = $ptmp[1];
						$addpl['cTime'] = time();
						$addpl['type'] = 'jpg';
						$addpl['privacy'] = 0;

					}
				}
			$datas = array ();
			$info['toUid'] = 0;
			$datas['type'] = 8;
			$datas['q_id'] = 0;
			if ($this->input->post('suser_id')) {
				$datas['uid'] = $this->input->post('suser_id');
			} else {
				$datas['uid'] = $this->uid;
			}
            $datas['tags'] = ',';
			$datas['ctime'] = time();
			if($tagls = $this->input->post('positions')){
				foreach($tagls as $r){
                  $datas['tags'] .=  $r . ',';
				}
			}else
			if ($ttag = $this->input->post('position')) {
				$datas['tags'] =  $ttag . ',';
			}
			$datas['newtime'] = time();
			$datas['type_data'] = serialize($info);
			$datas['content'] = $this->input->post('description');
			$weibo_id = $this->common->insertData('wen_weibo', $datas);

			if ($rply = $this->input->post('replys')) {
				$uids = explode(',', $this->input->post('uids'));
				$tongji = 0;
				foreach ($rply as $r) {
					if ($rply != '') {
						$Idata = array ();
						$Idata['type'] = 'topic';
						$Idata['pid'] = 0;
						$Idata['pcid'] = 0;
						$Idata['contentid'] = $weibo_id;
						$Idata['fuid'] = $uids[array_rand($uids)];
						$Idata['cTime'] = time();
						$Idata['comment'] = $r;
						$this->db->insert('wen_comment', $Idata);
						$tongji++;
					}
					$this->db->query("update wen_weibo set comments=comments+{$tongji},commentnums=commentnums+{$tongji} where weibo_id = '$weibo_id' limit 1 ");
				}
			}
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息添加成功！'));
			redirect('manage/spider/topic');
		}
	}
	// add Jigou info
	private function addJigou() {
		if ($this->input->post() && !$this->notlogin and $this->input->post('name')) {
            $this->db->select("id");
            $this->db->like('alias', trim($this->input->post('name')));
            $this->db->from('users');
            $nums = $this->db->count_all_results();
			$this->form_validation->set_rules('email', '邮箱', 'trim|xss_clean|callback__check_user_email');
			//$this->form_validation->set_rules('phone', '手机', 'trim|xss_clean|callback__check_phone_no');
			if ($this->form_validation->run() == TRUE and $nums==0) {
				$this->wen_auth->_setRegFrom(1);

				$udata = $this->wen_auth->register($this->input->post('name'), $this->input->post('password'), $this->input->post('email'), '', '', '', 3, '', '', '', false, false, false);

				$updateData['name'] = trim($this->input->post('name'));
				$updateData['contactN'] = trim($this->input->post('contactN'));
				$updateData['tel'] = trim($this->input->post('tel'));
				$updateData['shophours'] = $this->input->post('shophours');

				$updateData['province'] = $this->input->post('province');
				$updateData['city'] = $this->input->post('city');
				$updateData['district'] = $this->input->post('district');
				if ($updateData['district'] == '') {
					$updateData['district'] = $updateData['city'];
					$updateData['city'] = $updateData['province'];
				}
				$department = $this->input->post('department');
				if ($department) {
					$updateData['department'] = ',';
					foreach ($department as $k => $val) {
						$updateData['department'] .= $k . ',';
					}
				}
				$updateData['address'] = $this->input->post('address');
				$updateData['descrition'] = $this->input->post('descrition');
				$updateData['userid'] = $udata['user_id'];
				$updateData['cdate'] = time();

				if ($updateData['district'] != '' && $updateData['name'] != '' && $updateData['tel'] != '') {
					//upload certificate pictures

					$str = "SELECT userid,id FROM `company` WHERE `userid` ={$udata['user_id']} LIMIT 1";
					$tjudge = $this->db->query($str)->result_array();
					if (empty ($tjudge)) {
						$this->db->insert('company', $updateData);
						$companyid = $this->db->insert_id();
					} else {
						$companyid = $tjudge[0]['id'];
					}

					//init
					$updateData = array ();
					$this->input->post('thumburl') && $this->thumb($udata['user_id'], $this->input->post('thumburl'));
					$updateData = array ();
					$updateData['alias'] = $this->input->post('name');
					$updateData['state'] = 1;
					$updateData['banned'] = 0;
					if (!$this->wen_auth->get_emailId()) {
						$updateData['email'] = $this->input->post('email');
					}
					elseif (!$this->wen_auth->get_phone()) {
						$updateData['phone'] = $this->input->post('phone');
					}
					$updateData['utags']  = ',';
                    if($tagls = $this->input->post('positions')){
				     foreach($tagls as $r){
                        $updateData['utags'] .=  $r . ',';
				    }
			      }else{
			      	$updateData['utags']  .= $this->input->post('tags');
			      }

					$this->db->where('id', $udata['user_id']);
					$this->db->update('users', $updateData);
					$this->wen_auth->complete = true;
					//upload picture set
					if ($this->input->post('picurls')) {
						$this->picSet($udata['user_id'], unserialize($this->input->post('picurls')));
					}
					$this->session->set_flashdata('msg', $this->common->flash_message('success', '信息添加成功！'));
					$this->session->set_userdata('uinfonotcomplete', false);
					redirect('manage/spider');
				} else {
					$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息不完整或者信息重复！'));
					redirect('manage/spider');
				}

			}
		} else {
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '信息不完整,更新失败！'));
			redirect('manage/spider');
		}
	}
	//user thumb
	private function thumb($uid, $file) {
		if ($file != '') {
				if (isset ($_FILES['upthumburl']['tmp_name']) and $_FILES['upthumburl']['tmp_name']) {
					$this->remote->uputhumb($_FILES['upthumburl']['tmp_name'],$uid);
				} else {
					$this->remote->uputhumb($file,$uid);
				}
				return true;
			} else {
				return false;
			}
	}
	//upload set pic
	private function picSet($userid = '', $tmp_name = array ()) {
		$datas['albumId'] = time();
		$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
		$basedir .= '/' . date('m');
        $watermark = realpath(APPPATH.'../').'/images/waterprint.png';
        if(!file_exists($watermark)){
        	die(' water mark picture not exists'.$watermark);
        }
		if (!is_dir($basedir)) {
			mkdir($basedir, 0777);
		}
        $basedir .= '/' . date('d');
        if (!is_dir($basedir)) {
			mkdir($basedir, 0777);
		}
		$datas['userId'] = $userid;
		if($_FILES['upintures']['tmp_name'][0]){
          foreach($_FILES['upintures']['tmp_name'] as $k){
          	   if($k!=''){
          	   	$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = $basedir . '/' . $filename;
				@ move_uploaded_file($k, $datas['savepath']);
				//GenerateThumbFile($datas['savepath'], $datas['savepath'], 600, 600);
				$datas['cTime'] = time();
				$datas['savepath'] = 'upload/' . date('Y') . '/' . date('m') . '/'. date('d') . '/' . $filename;
				$this->common->insertData('c_photo', $datas);
          	   }
          }
		}else{
		  $this->load->library('watermark');
          foreach ($tmp_name as $row) {
			if ($get_content = file_get_contents($row)) {
				$filename = uniqid(time(), false) . '.jpg';
				$datas['savepath'] = $basedir . '/' . $filename;
				@ file_put_contents($datas['savepath'], $get_content);
				$this->watermark->setImSrc($datas['savepath']);
                $this->watermark->setImWater($watermark);
                if($this->input->post('shuiyinpos')==2){
                   $this->watermark->mark(1,10, 0, 0);
                }else{
                	$this->watermark->mark(1,9, 0, 0);
                }

				//GenerateThumbFile($datas['savepath'], $datas['savepath'], 600, 600);
				$datas['cTime'] = time();
				$datas['savepath'] = 'upload/' . date('Y') . '/' . date('m') . '/'. date('d') . '/'. $filename;
				$this->common->insertData('c_photo', $datas);
			}

		}
		}
	}
}
?>
