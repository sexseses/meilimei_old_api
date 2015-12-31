<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api info Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class info extends CI_Controller {
	private $notlogin = true;
	private $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('form_validation');
		$this->load->model('auth');
		$this->load->model('Email_model');
	}
	function suggest($param = '') {
		if ($this->auth->checktoken($param)) {
			$this->form_validation->set_rules('name', 'Name', 'trim|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|xss_clean');
			$this->form_validation->set_rules('message', 'Message', 'required|trim|xss_clean');
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$message = $this->input->post('message');
			$result['state'] = '000';
			if ($this->form_validation->run()) {
				$admin_email = $this->wen_auth->get_site_sadmin();
				$admin_name = $this->wen_auth->get_site_title();

				$email_name = 'contact_form';
				$splVars = array (
				"{site_name}" => $this->wen_auth->get_site_title(), "{email}" => $email, "{name}" => $name, "{message}" => $message);

				$this->Email_model->sendMail($admin_email, $email, ucfirst($name), $email_name, $splVars);
				$result['updatestate'] = '000';
			} else {
				$result['updatestate'] = '001';
				$result['name'] = $name;
				$result['email'] = $email;
				$result['message'] = $message;
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//user reports
	function report($param = '') {
		$result['state'] = '000';
		$result['ustate'] = '000';
		if ($this->auth->checktoken($param)) {
			if ($this->uid) {
				if ($id = $this->input->post('contentid')) {
					if (!$this->session->userdata('report_ctime')) {
						$this->session->set_userdata('report_ctime', time());
					}
					elseif (time() - $this->session->userdata('report_ctime') < 10) {
						$result['state'] = '012';
						$result['notice'] = '重复举报！';
						echo json_encode($result);
						exit;
					}
					elseif (time() - $this->session->userdata('report_ctime') < 120) {
						$result['state'] = '012';
						$result['notice'] = '举报发送间隔2分钟！';
						echo json_encode($result);
						exit;
					}
					$result['notice'] = '举报成功！';
					$str = $this->input->post('type');
					$str .= '标题：' . $this->input->post('title');
					$str .= '<br>内容ID：' . $this->input->post('contentid');
					$str .= '<br>举报人：' . $this->input->post('name');
					$str .= '<br>举报人UID：' . $this->uid;
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: meilimei <system@meilimei.com>' . "\r\n";
					mail('muzhuquan@126.com', "=?utf-8?B?" . base64_encode('美丽美信息反馈') . "?=", $str, $headers);
				} else {
					$result['notice'] = '参数不全！';
					$result['state'] = '012';
				}
			} else {
				$result['notice'] = '账户未登入！';
				$result['ustate'] = '001';
			}
		} else {
			$result['notice'] = 'Toke错误！';
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function item($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($this->input->get('pid') >= 0) {
				$this->db->where('pid', $this->input->get('pid'));
				$res = array (
					1 => '除皱',
					2 => '面部轮廓',
					3 => '减肥塑形',
					4 => '皮肤美容',
					5 => '眼部',
					6 => '鼻部',
					7 => '胸部',
					8 => '口唇',
					9 => '私密整形',
					117 => '牙齿'
				);
				foreach ($res as $k => $v) {
					$r = array ();
					$r['id'] = $k;
					$r['name'] = $v;
					$r['pid'] = 0;
					$result['datas'][] = $r;
				}

			} else {
				$result['paramState'] = '001';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function getnewscat($param = '') {
		if ($this->auth->checktoken($param)) {
			$this->db->order_by("weigh", "ASC");
			$tmp = $this->db->get('wp_terms')->result_array();
			foreach ($tmp as $row) {
				if ($row['term_id'] != 2) {
					$result['datas'][] = $row;
				}
			}

		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function newsdetail($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($ID = $this->input->get('ID')) {
				$this->db->where('ID', $ID);
				$this->db->from('wp_posts');
				$this->db->select('post_title,post_date,post_content');
				$tmp = $this->db->get()->result_array();
				$tmp[0]['post_content'] = '<style type="text/css">
																								pre{word-wrap: break-word;
																								word-break: normal;font-size:1.3em ; }
																								</style><pre>' . $tmp[0]['post_content'] . '</pre>';
				$result['data'] = $tmp[0];
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function newsLists($param = '') {
		if ($this->auth->checktoken($param)) {
			if ($term_taxonomy_id = $this->input->get('taxonomy')) {
				$offset = (intval($this->input->get('page')) - 1) * 10;
				$this->db->limit(10, $offset);
				$this->db->where('wp_term_relationships.term_taxonomy_id', $term_taxonomy_id);
				$this->db->from('wp_term_relationships');
				$this->db->select('wp_posts.post_title,wp_posts.post_date,wp_posts.ID');
				$this->db->join('wp_posts', 'wp_posts.ID = wp_term_relationships.object_id', 'left');
				$this->db->order_by("wp_posts.ID", "desc");
				$tmp = $this->db->get()->result_array();

				$this->db->where('wp_term_relationships.term_taxonomy_id', $term_taxonomy_id);
				$this->db->from('wp_term_relationships');
				$this->db->select('wp_term_relationships.object_id');
				$num = $this->db->get()->num_rows();
				$result['data'] = $tmp;
				$result['nums'] = $num;
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	function softInfo($param) {
		if ($this->auth->checktoken($param)) {
			if ($type = $this->input->get('type')) {
				$this->db->where('type', $type);
				if ($extra = $this->input->get('extra')) {
					$this->db->where('extra', $extra);
				} else {
					$this->db->where('extra', '');
				}
				//$this->db->select('type','name','version','effectver','needupdate','content');
				$this->db->limit(1);
				$this->db->order_by("id", "DESC");
				$query = $this->db->get('softinfo')->result_array();

				if (!empty ($query)) {
					$result['name'] = $query[0]['name'];
					$result['version'] = $query[0]['version'];
					$result['effectver'] = $query[0]['effectver'];
					$result['needupdate'] = $query[0]['needupdate'];
					$result['content'] = $query[0]['content'];
					$result['size'] = $query[0]['size'];
					$result['type'] = $query[0]['type'];
					$result['downurl'] = site_url() . $query[0]['downurl'];
				}
			} else {
				$result['state'] = '012';
			}
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
	//scan folder
	private function getAllFiles($dir) {
		$result = array ();
		if (is_dir($dir)) {
			$handle = opendir($dir);
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..")
					continue;
				$result[] = $file;
			}
			closedir($handle);
		} else {
			exit ("没有这个文件夹!");
		}
		return $result;

	}
	//get faces url and info
	function faces($param = '') {
		$result['version'] = FACES_VERSION;
		$files = $this->getAllFiles('/mnt/meilimei/images/faces');
		$result['nums'] = count($files);
		foreach ($files as $r) {
			$result['data'][] = array (
				'name' => str_replace('.png',
				'',
				$r
			), 'url' => 'http://www.meilimei.com/images/faces/' . urlencode($r));
		}
		echo json_encode($result);
	}
	//user leave message api
	function leave($param = '') {
		$result['state'] = '001';
		if ($this->auth->checktoken($param)) {
			$result['state'] = '000';
			if ($content = $this->input->post('content')) {
				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: meilimei <system@meilimei.com>' . "\r\n";
				$str = $this->input->post('title') . '<br>' . $content;
				mail('', "=?utf-8?B?" . base64_encode('美丽美信息反馈') . "?=", $str, $headers);
				$result['notice'] = '发送成功';
			} else {
				$result['notice'] = '参数不全';
				$result['state'] = '012';
			}
		}
		echo json_encode($result);
	}
	 //delete face upload pic
	function delFace($param = '') {
		$result['state'] = '001';
		if ($this->auth->checktoken($param)) {
			$result['state'] = '000';
			if ($this->uid) {
				$result['notice'] = '信息成功删除！';
                $this->load->model('remote');
                $this->db->where('uid', $this->uid);
                $tmp = $this->db->get('faces')->result_array();
                if(!empty($tmp)){
                	$this->remote->del($tmp[0]['pic']);
                	$this->db->where('uid', $this->uid);
                	$this->db->delete('faces');
                }
			} else {
				$result['notice'] = '账户未登入！';
				$result['state'] = '012';
			}
		}
		echo json_encode($result);
	}
	//specif user location
	private function getUloc(){
		$sql = 'select id,score,uid,pic,paiming from(select id,pic,uid,score,(@rowno:=@rowno+1) as paiming from faces ,(select (@rowno:=0)) b order by score desc)as res where uid = '.$this->uid;
	    $tmp = $this->db->query($sql)->result_array();
	    if(!empty($tmp)){
	    	return $tmp[0];
	    }else{
	    	return '';
	    }
	}
	//get faces scroe top 100 lists
	function facesTop($param = '') {
		$result['state'] = '001';
		$result['data'] = array ();
		if ($this->auth->checktoken($param)) {
			$result['state'] = '000';
			$result['myScore'] = "";
			$page = intval($this->input->get('page') - 1);
			$width = intval($this->input->get('width'));
			$width == 0 && $width = 60;

			$this->load->model('remote');
			if($this->uid and ($page==0 OR $page==1) and $result['myScore'] = $this->getUloc()){
                  $result['myScore']['thumb'] = $this->remote->thumb($result['myScore']['uid'], $width);
                  $result['myScore']['pic'] = $this->remote->show($result['myScore']['pic']);
                  $result['myScore']['alias'] = $this->wen_auth->get_username();;
			}
			$this->db->limit(20, 20 * $page);
			$this->db->order_by("faces.score", "DESC");
			$this->db->select('faces.id, faces.uid,faces.score,faces.pic,users.alias');
			$this->db->join('users', 'users.id = faces.uid','left');
			$res = $this->db->get('faces')->result_array();

			foreach ($res as $k=>$r) {
				$r['thumb'] = $this->remote->thumb($r['uid'], $width);
				$r['pic'] = $this->remote->show($r['pic']);
				$res[$k] = $r;
			}
			 //$result['data']  = $res;

		}
		echo json_encode($result);
	}
	
	
function facesTheDayTop($param = '') {

		$result['data'] = array ();

			$result['state'] = '000';
			$result['myScore'] = "";
			$page = intval(abs($this->input->get('page')) - 1);
			$width = intval($this->input->get('width'));
			$width == 0 && $width = 60;

			$this->load->model('remote');
			if($this->uid and ($page==0 OR $page==1) and $result['myScore'] = $this->getUloc()){
                  $result['myScore']['thumb'] = $this->remote->thumb($result['myScore']['uid'], $width);
                  $result['myScore']['pic'] = $this->remote->show($result['myScore']['pic']);
                  $result['myScore']['alias'] = $this->wen_auth->get_username();;
			}
			$this->db->limit(20, 20 * $page);
			$this->db->order_by("faces.score", "DESC");
			$this->db->select('faces.id, faces.uid,faces.score,faces.pic,users.phone,users.alias');
			$this->db->join('users', 'users.id = faces.uid','left');
			
			$yestoday = strtotime(date('Y-m-d',strtotime('-1 day')));
			$today = strtotime(date('Y-m-d'));
			if($this->input->get('type') == 1){
				$this->db->where('cdate > ', strtotime('2014-10-31'));
				$this->db->where('cdate  <', strtotime('2014-11-07'));
			}else{
				$this->db->where('cdate > ', $yestoday);
				$this->db->where('cdate  <', $today);
			}
			$res = $this->db->get('faces')->result_array();
			//$result['sql'] = $this->db->last_query();
			foreach ($res as $k=>$r) {
				if(empty($r['alias'])){
					$r['alias'] = strlen($r['phone']) > 4 ? substr($r['phone'],0,4)."***":$r['phone'];
				}
				$r['thumb'] = $this->remote->thumb($r['uid'], $width);
				$r['pic'] = $this->remote->show($r['pic']);
				$r['score'] = (isset($r['score']) && $r['score'] > 100) ? 100: $r['score'];
				$res[$k] = $r;
			}
			$result['data']  = $res;

		echo json_encode($result);
	}
	//pictrue compare for faces
	function picmr($param = '') {
		$result = array ();

		if ($_FILES['pic']['tmp_name']) {
			$result['notice'] = '成功!';
			$result['ustate'] = '000';
			//save result
			if ($this->input->post('save') and $_FILES['pic']['tmp_name']) {
				if (!$this->uid) {
					$result['state'] = '001';
					$result['ustate'] = '001';
					$result['notice'] = '图片上传失败！';
					echo json_encode($result);
					exit;
				}
				$this->load->model('remote');
				$datas['savepath'] = date('Y') . '/' . date('m') . '/' . date('d');
				$ext = '.jpg';
				$datas['name'] = uniqid() . rand(1000, 9999) . $ext;
				$datas['savepath'] .= '/' . $datas['name'];
				$ptmp = getimagesize($_FILES['pic']['tmp_name']);
				if (!$this->remote->cp($_FILES['pic']['tmp_name'], $datas['name'], $datas['savepath'], array (
						'width' => 600,
						'height' => 800
					), true)) {
					$result['state'] = '001';
					$result['notice'] = '图片上传失败！';
					echo json_encode($result);
					exit;
				} else {
					$this->db->where('uid', $this->uid);
					$tmp = $this->db->get('faces')->result_array();
					if (!empty ($tmp)) {
						if ($this->remote->del($tmp[0]['pic'], true)) {
							$data = array (
								'uid' => $this->uid,
								'pic' => $datas['savepath'],
								'sex' => $this->input->post('sex'),
							'skins' =>intval($this->input->post('skins')),
							'age' =>intval($this->input->post('age')),
								'score' => intval($this->input->post('score')),
							'cdate' => time());
							$this->db->where('uid', $this->uid);
							$this->db->update('faces', $data);
						}
					} else {
						$data = array (
							'uid' => $this->uid,
							'pic' => $datas['savepath'],
							'sex' => $this->input->post('sex'),
							'skins' =>intval($this->input->post('skins')),
							'age' =>intval($this->input->post('age')),
							'score' => intval($this->input->post('score')),
						'cdate' => time());
						$this->db->insert('faces', $data);
					}
				}
			}else{
               $send = array ();
			$url = 'http://apicn.faceplusplus.com/v2';
			$send['api_key'] = 'f7c63d17b7b5723f52529392e9c9f7ec';
			$send['api_secret'] = 'qO95EFi__RJUvBjlWScR2X8Pa_Q4nfqz';
			$send['img'] = '@' . $_FILES['pic']['tmp_name'];
			$send['attribute'] = 'glass,pose,gender,age,race,smiling';
			$info = json_decode($this->request($url . '/detection/detect', $send));

			$info = $info->face[0];
			$hasBackData = true;
			if (isset ($info->attribute->age->value)) {
				$result['age'] = $info->attribute->age->value;
			} else {
				$hasBackData = false;
				$result['age'] = '分析中';
			}
			if (isset ($info->attribute->gender->value)) {
				$result['gender'] = $info->attribute->gender->value;
			} else {
				$result['gender'] = '分析中';
			}
			$result['score'] = 0;
			$result['infos'] = '';
			if ($hasBackData) {
				$this->load->model('face');
				$this->face->init($_FILES['pic']['tmp_name'], $info);
				$res = $this->face->res();
				$result['skin'] = intval($res['skin']*1.1);
				$result['score'] = $res['score'];
				$result['huitou'] = intval(($result['score']+$res['skin'])/2*1.1);
				$result['infos'] = $this->gRview($result['huitou']);
			} else {
				$result['skin'] = '分析中';
				$result['score'] = '分析中';
			}
			}
		}
		echo json_encode($result);
	}
	
	function picmrwebsave() {
		if ($this->input->post('save')){
			$data = array (
				'uid' => $this->input->post('uid'),
				'pic' => $this->input->post('pic'),
				'sex' => $this->input->post('sex'),
				'skins' =>intval($this->input->post('skins')),
				'age' =>intval($this->input->post('age')),
				'score' => intval($this->input->post('score')),
				'cdate' => time());
				$query = $this->db->insert('faces', $data);
				 echo $query ;
		}
	}
	function picmrweb() {
		$pimg = $this->input->post('file');
		$target = $this->input->post('target');
		$result = array ();
		$send = array ();
		
			$url = 'http://apicn.faceplusplus.com/v2';
			$send['api_key'] = 'f7c63d17b7b5723f52529392e9c9f7ec';
			$send['api_secret'] = 'qO95EFi__RJUvBjlWScR2X8Pa_Q4nfqz';
			//$send['img'] = '@' . $_FILES['pic']['tmp_name'];
			$send['url'] =  $pimg;
			$send['attribute'] = 'glass,pose,gender,age,race,smiling';
			$info = json_decode($this->request($url . '/detection/detect', $send));
			$info = $info->face[0];
			$hasBackData = true;
			if (isset ($info->attribute->age->value)) {
				$result['age'] = $info->attribute->age->value;
			} else {
				$hasBackData = false;
				$result['age'] = '分析中';
			}
			if (isset ($info->attribute->gender->value)) {
				$result['gender'] = $info->attribute->gender->value;
			} else {
				$result['gender'] = '分析中';
			}
			$result['score'] = 0;
			$result['infos'] = '';
			if ($hasBackData) {
				$this->load->model('face');
				$this->face->init($pimg, $info);
				$res = $this->face->res();
				$result['res'] =1;
				$result['skin'] = intval($res['skin']*1.1);
				$result['score'] = $res['score'];
				$result['huitou'] = intval(($result['score']+$res['skin'])/2*1.1);
				$result['infos'] = $this->gRview($result['huitou']);
				$result['target']=$target;
			} else {
				$result['skin'] = '分析中';
				$result['score'] = '分析中';
			}
			echo json_encode($result);
	
	}
	
	// push get device info
	function guid($param = '') {
		$result['state'] = '000';
		if (($type = $this->input->post('type')) and $idt = trim($this->input->post('idt'))) {
			$idt = trim($idt);
			$idt = str_replace(' ', '', $idt);
			$idt = trim(substr($idt, 1, strlen($idt) - 2));
			$this->db->where('devicetoken', $idt);
			$this->db->from('apns_devices');
			$n = $this->db->count_all_results();
			//$result['sql'] = $this->db->last_query();
			$data['type'] = $type;
			$data['devicetoken'] = $idt;
			$data['type'] = $this->input->post('version');
			$data['appversion'] = $this->input->post('appversion');
			$data['appname'] = $this->input->post('appname');
			$data['devicename'] = $this->input->post('devicename');
			$data['pushalert'] = $this->input->post('pushalert');
			$data['pushsound'] = $this->input->post('pushsound');
			$data['status'] = $this->input->post('status');
			$data['uid'] = $this->uid;
			if ($n) {
				$data['modified'] = time();
				$this->db->where('id', $tmp[0]->id);
				$this->db->update('apns_devices', $data);
			} else {
				$data['created'] = $data['modified'] = time();
				$this->db->insert('apns_devices', $data);
			}
			$result['notice'] = '成功记录设备!';
		} else {
			$result['notice'] = '参数不全';
			$result['state'] = '012';
		}
		echo json_encode($result);
	}
	private function request($request_url, $request_body) {
		$useragent = 'Faceplusplus PHP SDK/1.0';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	//get review info with face scroe
	private function gRview($score = 0) {
		$tm = array ();
		switch ($score) {
			case $score >= 90 :
				$tm = array ('在你面前，我早已丢失了节操','你已击败全国99.99%的用户，获得出街使者称号！（100%）','要清凉不要色狼！','今天被搭讪的几率是98%哦','出街后，路人都会喷鼻血哒','再朴素的着装也遮不住你迷人的外貌','你击败林志玲97%的回头率，有什么想说的？','今天有人为你承包了全地球的鱼塘');
				break;
			case $score >= 80 :
				$tm = array ('看脸的社会，你赢得很彻底','今天被搭讪率很高哦，值得期待','你这磨人的小妖精，回头率好高！','不做个普通人就做个美人','美则美矣，出挑不出众','今天你绝对是万众瞩目的焦点','准备好给心仪的他|她告白了吗？');
				break;
			case $score >= 70 :
				$tm = array ('你的背影很美，我的视力很好','敢晒你就是达人，给你320个赞','镜头感很强，美感还差了那么点儿','至今为止，难道没有想过整容吗？','寄在茫茫人海，连你的影子都看不到的背影很美，我的视力很好','敢晒你就是达人，给你320个赞','镜头感很强，美感还差了那么点儿','至今为止，难道没有想过整容吗？','寄在茫茫人海，连你的影子都看不到');
				break;
			case $score >= 60 :
				$tm = array ('镜头感很强，美感还差了那么点儿','只需要一步，你就可以美到没朋友','你明亮的双眼点燃了我澎湃的激情','这人气，一般人都是没有的','谢天谢地，谢谢你点开我');
				break;
			default :
				$tm = array ('纳尼？一定是我打开的方式不对','系统提示出错，要不要再试一次？','这不公平，让大家来评评理吧','芙蓉如面柳如眉，你的美丽我知道','偷偷告诉你，你真棒！','如果觉得我是错的，你就分享我','今天的你够美腻，但不适合约会');
				break;
				break;
		}
       return  $tm[array_rand($tm)];
	}
}
?>
