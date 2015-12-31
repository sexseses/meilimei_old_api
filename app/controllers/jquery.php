<?php
class jquery extends CI_Controller {
	private $uid = '';
	public function __construct() {
		parent :: __construct();
		$this->load->library('yisheng');
		$this->load->model('Yuyue_model');
		$this->load->model('Email_model');
		$this->load->model('Users_model');
		$this->load->model('remote');
		//error_reporting(E_ALL);
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('user/login');
		}
		$this->path = realpath(APPPATH . '../images');
	}
	
	public function upImage(){
	    $picname = $_FILES['image']['name'];
	    $picsize = $_FILES['image']['size'];
	    if ($picname != "") {
	        if ($picsize > 102400*30) {
	            echo '图片大小不能超过30M';
	            $result['msg'] = " 图片上传失败 ";
	            exit;
	        }
	        $type = strstr($picname, '.');
	        if ($type != ".gif" && $type != ".jpg" && $type != ".png") {
	            echo '图片格式不对！';
	            $result['msg'] = " 图片上传失败 ";
	            exit;
	        }
	        $upload_path = "2015/07/";
	        $htp_ads="http://pic.meilimei.com.cn/upload/";
	        if ($_FILES['image']['tmp_name']) {
	            $file_name = time() . rand(1000, 9999). '.jpg';
	            if (!$this->remote->cp($_FILES['image']['tmp_name'], $file_name,$upload_path.$file_name)) {
	                $result['msg'] = " 图片上传失败 ";
	            }else{
	                $result['code'] = 1;
	                $result['msg'] = " 图片上传成功 ";
	                $result['banner_pic'] = $upload_path . $file_name;
	            }
	        }
	        echo json_encode($result);
	    }
	}
	
	
	public function enyuyue($param = '') {
		$this->load->library('sms');
		//$this->load->library('Email');
		$condition = array (
			'id' => $this->input->get('acid'
		));
		$state['phone'] = '';
		$phone = $this->Yuyue_model->get_phone_by_id($condition['id']);
	    if($phone->num_rows > 0) {
			$ophone = $phone->result_array();
			$state['phone'] = $ophone[0]['phone'];
		}
		$udate = array (
			'state' => 1
		);
		$state['state'] = '000';
		$state['ac'] = 'yuyue';
		$tmp = $this->db->query("SELECT jifen FROM `users` WHERE `id` ={$this->uid}")->result_array();
		if($tmp[0]['jifen']>=5){
           $this->common->updateTableData('yuyue', '', $condition, $udate);
           if(isset($state['phone']) && !empty($state['phone'])) {
           	   $message = "您已成功预约。 退订回复TD";
               $this->sms->sendSMS(array ($state['phone']), $message);
               $uinfo = $this->Users_model->get_user_by_phone($state['phone']);
               $new_user = array();
               if($uinfo->num_rows > 0) {
                   $ures = $uinfo->result_array();
                   $new_user['email'] =  $ures[0]['email'];
                   $new_user['password'] =  $ures[0]['password'];
               }
			   $splVars = array (
				"{site_name}" => '美丽诊所', "{email}" => $new_user['email'], "{password}" => $password);
               $this->Email_model->sendMail('sunbiao@rolaner.com', 'muzhuquan@126.com', ucfirst('美丽诊所'), 'users_signin',$splVars);
               //$from = $this->auth->config->item('WEN_webmaster_email');
               /*$from = 'big';
               $to = "";
               $subject = "美丽诊所预约";
			   $email = $this->email;
		       $email->from($from);
			   $email->to($to);
			   $email->subject($subject);
			   $email->message($message);*/
           }

		   $this->db->query("UPDATE `users` SET `jifen` = `jifen`-5   WHERE `id` ={$this->uid}");
		}else{
          $state['state'] = '001';
          $state['reson'] = '积分不足！';
		}

		echo json_encode($state);
	}
	public function closequestion() {
		if ($qid = intval($this->input->get('data_id'))) {
			$condition = array (
				'id' => $qid,
				'fUid' => $this->uid
			);
			$udate = array (
				'state' => 8
			);
			$state['state'] = '000';
			$state['ac'] = 'closequestion';
			$this->common->updateTableData('wen_questions', '', $condition, $udate);
			echo json_encode($state);
		}
	}
	public function yueyueset($param = '') {
		if ($this->input->get('dataid') && $this->input->get('amount')) {
			$condition = array (
				'id' => $this->input->get('dataid'
			), 'userto' => $this->uid);
			$udate = array (
				'amout' => intval($this->input->get('amount'
			)));
			$state['state'] = '000';
			$state['ac'] = 'yuyue';
			$this->common->updateTableData('yuyue', '', $condition, $udate);
			echo json_encode($state);
		}
	}
	public function tuijianset($param = '') {
		if ($this->input->get('dataid') && $this->input->get('weight') && $this->wen_auth->get_role_id() == 16) {
			$condition = array (
				'id' => $this->input->get('dataid'
			));
			$udate = array (
				'rank_search' => intval($this->input->get('weight'
			)));
			$state['state'] = '000';
			$state['ac'] = 'tuijian';
			$this->common->updateTableData('users', '', $condition, $udate);
			echo json_encode($state);
		}
	}
	public function myueyueset($param = '') {
		if ($this->input->get('dataid') && $this->input->get('amount') && $this->wen_auth->get_role_id() == 16) {
			$condition = array (
				'id' => $this->input->get('dataid'
			));
			$udate = array (
				'amout' => trim($this->input->get('amount'
			)));
			$state['state'] = '000';
			$state['ac'] = 'yuyue';
			$this->common->updateTableData('yuyue', '', $condition, $udate);
			echo json_encode($state);
		}
	}
	public function unyuyue($param = '') {
		$condition = array (
			'id' => $this->input->get('acid'
		));
		$udate = array (
			'state' => 2
		);
		$this->common->updateTableData('yuyue', '', $condition, $udate);
	    if(isset($state['phone']) && !empty($state['phone'])) {
           $message = "您预约美丽诊所被拒绝";
           $this->sms->sendSMS(array ($state['phone']), $message);
           $uinfo = $this->Users_model->get_user_by_phone($state['phone']);
           $new_user = array();
           if($uinfo->num_rows > 0) {
               $ures = $uinfo->result_array();
               $new_user['email'] =  $ures[0]['email'];
               $new_user['password'] =  $ures[0]['password'];
           }
		   $splVars = array (
			"{site_name}" => '美丽诊所', "{email}" => $new_user['email'], "{password}" => $password);
           $this->Email_model->sendMail('sunbiao@rolaner.com', 'muzhuquan@126.com', ucfirst('美丽诊所'), 'users_signin',$splVars);
               //$from = $this->auth->config->item('WEN_webmaster_email');
               /*$from = 'big';
               $to = "";
               $subject = "美丽诊所预约";
			   $email = $this->email;
		       $email->from($from);
			   $email->to($to);
			   $email->subject($subject);
			   $email->message($message);*/
        }
		$result['state'] = '000';
		$result['ac'] = 'unyuyue';
		echo json_encode($result);
	}
	//del hetong pic
	public function delhetong() {
		if ($this->uid) {
			$info = $this->db->query("SELECT picture FROM company WHERE userid={$this->uid}")->result();
			$pic = unserialize($info[0]->picture);
			$tmpstr = '1,' . $this->input->get('str');
			$newPic = array ();
			foreach ($pic['CI'] as $row) {
				if (!strpos($tmpstr, $row)) {
					$newPic['CI'][] = $row;
				} else {
					unlink($row);
				}
			}
			$updateData['picture'] = serialize($newPic);

			$this->db->where('userid', $this->uid);
			$this->db->update('company', $updateData);
		}
	}
		//del hetong pic
	public function deljigoupic() {
		if ($this->uid && $this->input->get('id')) {
            $id = substr($this->input->get('id'),0,strlen($this->input->get('id'))-1);
			$this->db->query("UPDATE `c_photo` SET `isDel` = 1 WHERE `id` in ({$id}) AND `userid` =  $this->uid");
		}
	}
    public function delsaomiao() {
		if ($this->uid && $this->input->get('id')) {
            $id = substr($this->input->get('id'),0,strlen($this->input->get('id'))-1);
            $tmp = $this->db->query("SELECT savepath FROM `c_photo` WHERE `id` in ({$id}) AND `userid` =  $this->uid")->result();
            foreach($tmp as $row){
               unlink($row->savepath);
            }
			$this->db->query("DELETE FROM  `c_photo` WHERE `id` in ({$id}) AND `userid` =  $this->uid");
		}
	}
	//del hetong pic
	public function uphetong() {
		if ($this->uid && !empty ($_FILES)) {
			$info = $this->db->query("SELECT picture FROM company WHERE userid={$this->uid}")->result();
			$pic = unserialize($info[0]->picture);
            $count = isset($pic['CI'])?count($pic['CI']):0;
			$tempFile = $_FILES['picfile']['tmp_name'];
			$fileTypes = array (
				'jpg',
				'jpeg',
				'gif',
				'png'
			);
			$fileParts = pathinfo($_FILES['picfile']['name']);
			if (in_array($fileParts['extension'], $fileTypes) && $count<6) {
				$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
				$basedir .= '/' . date('m');
				if (!is_dir($basedir)) {
					mkdir($basedir, 0777);
				}
				$filename = uniqid(time(), false) . '.jpg';
				$tmppath = $basedir . '/' . $filename;
				move_uploaded_file($_FILES['picfile']['tmp_name'], $tmppath);
				$pic['CI'][] = $TMP = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
				$updateData['picture'] = serialize($pic);
				$this->db->where('userid', $this->uid);
				$this->db->update('company', $updateData);
				echo "{";
				echo "error: '',\n";
				echo "msg: '" . site_url() .$TMP, "'\n";
				echo "}";
			} else {
				echo "{";
				echo "error: '文件格式不正确或者太大，或者图片超过6张',\n";
				echo "msg: ''\n";
				echo "}";
			}

		}
	}
	//del ablum jigou pic
	public function upjigouablum() {
	    $this->load->model('remote');
		if ($this->uid && !empty ($_FILES)) {
		    
			$info = $this->db->query("SELECT id,albumId FROM c_photo WHERE userId ={$this->uid} AND isDel=0 AND type=0")->result();
            $count = count($info);
			$tempFile = $_FILES['picfile']['tmp_name'];
			$fileTypes = array (
				'jpg',
				'jpeg',
				'gif',
				'png'
			);
			
			$fileParts = pathinfo($_FILES['picfile']['name']);
			if (in_array($fileParts['extension'], $fileTypes) && $count<12) {
				//$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
				//$basedir .= '/' . date('m');
// 				if (!is_dir($basedir)) {
// 					mkdir($basedir, 0777);
// 				}		
				$basedir = date('Y') . '/' . date('m');		
				$filename = uniqid(time(), false) . '.jpg';
				$tmppath = $basedir .'/'. $filename;
				//move_uploaded_file($_FILES['picfile']['tmp_name'], $tmppath);
				//$sice_s = array('width'=>40,'height'=>60);
				if($this->remote->cp($_FILES['picfile']['tmp_name'],$filename,$tmppath)){			
				//copy($tmppath,$tmppath.'_2.jpg');
			 	//$this->imageresize(array('height'=>'40','width'=>'60'),$tmppath.'_2.jpg');
				   
				$idata["savepath"] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
                $idata["userId"] = $this->uid;
                $idata["isDel"] = 0;
                $idata["cTime"] = time();
                $idata["albumId"] = isset($info[0]->albumId)?$info[0]->albumId:$idata["cTime"];
                $idata["privacy"] = 0;
				$this->common->insertData('c_photo',$idata);
				}
				$data['info'] = $this->db->query("SELECT albumId,savepath,id FROM c_photo WHERE userId={$this->uid} AND isDel=0 AND type=0")->result();
				$data['notlogin'] = $this->notlogin;
				$data['message_element'] = "ablum";
				$this->load->view('template', $data);
				echo "{";
				echo "error: '',\n";
				echo "msg: '" . site_url() .$idata["savepath"], "'\n";
				echo "}";
			} else {
				echo "{";
				echo "error: '文件格式不正确或者太大，或者图片超过6张',\n";
				echo "msg: ''\n";
				echo "}";
			}

		}
	}
	//upyishiablum pic
	public function upyishiablum() {
		if ($this->uid && !empty ($_FILES)) {
			$info = $this->db->query("SELECT id,albumId FROM c_photo WHERE userId ={$this->uid} AND isDel=0 AND type=1")->result();
            $count = count($info);
			$tempFile = $_FILES['picfile']['tmp_name'];
			$fileTypes = array (
				'jpg',
				'jpeg',
				'gif',
				'png'
			);
			$fileParts = pathinfo($_FILES['picfile']['name']);
			if (in_array($fileParts['extension'], $fileTypes) && $count<12) {
				$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
				$basedir .= '/' . date('m');
				if (!is_dir($basedir)) {
					mkdir($basedir, 0777);
				}
				$filename = uniqid(time(), false) . '.jpg';
				$tmppath = $basedir . '/' . $filename;
				move_uploaded_file($_FILES['picfile']['tmp_name'], $tmppath);
				copy($tmppath,$tmppath.'_2.jpg');

			 	$this->imageresize(array('height'=>'40','width'=>'60'),$tmppath.'_2.jpg');
				$idata["savepath"] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
                $idata["userId"] = $this->uid;
                $idata["isDel"] = 0;
                $idata["type"] = 1;
                $idata["cTime"] = time();
                $idata["albumId"] = isset($info[0]->albumId)?$info[0]->albumId:$idata["cTime"];
                $idata["privacy"] = 0;
				$this->common->insertData('c_photo',$idata);
				
				echo "{";
				echo "error: 'zhengque',\n";
				echo "msg: '" . site_url() .$idata["savepath"], "'\n";
				echo "}";
			} else {
				echo "{";
				echo "error: '文件格式不正确或者太大，或者图片超过6张',\n";
				echo "msg: ''\n";
				echo "}";
			}

		}
	}
	//del ablum jigou pic
	public function upsaomiao() {
		if ($this->uid && !empty ($_FILES)) {
			$info = $this->db->query("SELECT id,albumId FROM c_photo WHERE userId ={$this->uid} AND isDel=0 AND type=0")->result();
            $count = count($info);
			$tempFile = $_FILES['picfile']['tmp_name'];
			$fileTypes = array (
				'jpg',
				'jpeg',
				'gif',
				'png'
			);
			$fileParts = pathinfo($_FILES['picfile']['name']);
			if (in_array($fileParts['extension'], $fileTypes) && $count<12) {
				$basedir = realpath(APPPATH . '../upload/' . date('Y') . '/');
				$basedir .= '/' . date('m');
				if (!is_dir($basedir)) {
					mkdir($basedir, 0777);
				}
				$filename = uniqid(time(), false) . '.jpg';
				$tmppath = $basedir . '/' . $filename;
				move_uploaded_file($_FILES['picfile']['tmp_name'], $tmppath);
				$idata["savepath"] = 'upload/' . date('Y') . '/' . date('m') . '/' . $filename;
                $idata["userId"] = $this->uid;
                $idata["isDel"] = 0;
                $idata["cTime"] = time();
                $idata["albumId"] = isset($info[0]->albumId)?$info[0]->albumId:$idata["cTime"];
                $idata["privacy"] = 0;

				$this->common->insertData('c_photo',$idata);
				echo "{";
				echo "error: '',\n";
				echo "msg: '" . site_url() .$idata["savepath"], "'\n";
				echo "}";
			} else {
				echo "{";
				echo "error: '文件格式不正确或者太大，或者图片超过6张',\n";
				echo "msg: ''\n";
				echo "}";
			}

		}
	}
	public function topicOrder() {
         if($this->input->get('dataid') and $dataid = intval($this->input->get('dataid'))){
             $data = array(
               'weight' => intval($this->input->get('weight'))
            );
           $this->db->where('weibo_id', $dataid);
           $this->db->update('wen_weibo', $data);
         }
	}
	public function getsuggest() {

		$input = trim(strtolower($_GET['input']));
		$len = strlen($input);
		$limit = isset ($_GET['limit']) ? (int) $_GET['limit'] : 0;
		$aResults = array ();
		$count = 0;

		if ($len) {
			$this->db->like('name', $input);
			$this->db->limit(12);
			$this->db->select('id,name');
			$query = $this->db->get('company')->result();
			foreach ($query as $row) {
				$aResults[] = array (
					"id" => $row->id,
					"value" => htmlspecialchars($row->name
				), "info" => htmlspecialchars($row->name));
			}
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0

		if (isset ($_REQUEST['json'])) {
			header("Content-Type: application/json");

			echo "{\"results\": [";
			$arr = array ();
			for ($i = 0; $i < count($aResults); $i++) {
				$arr[] = "{\"id\": \"" . $aResults[$i]['id'] . "\", \"value\": \"" . $aResults[$i]['value'] . "\", \"info\": \"\"}";
			}
			echo implode(", ", $arr);
			echo "]}";
		} else {
			header("Content-Type: text/xml");

			echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
			for ($i = 0; $i < count($aResults); $i++) {
				echo "<rs id=\"" . $aResults[$i]['id'] . "\" info=\"" . $aResults[$i]['info'] . "\">" . $aResults[$i]['value'] . "</rs>";
			}
			echo "</results>";
		}
	}
	public function test(){
		$data['info'] = 'test';
		echo $_GET['callback'].json_encode($data);exit;
	}
 protected function imageresize($needsize=array('height'=>'230','width'=>'230'),$fsource='') {
		$info = "";
		$data = getimagesize($fsource, $info);
		$maxWidth = $needsize['width'];
		$maxHeight = $needsize['height'];
		switch ($data[2]) {
			case 1 :
				if (!function_exists("imagecreatefromgif")) {
					echo 'please use jpg png picture';
					exit ();
				}
				$picflag = 1;
				$im = @ imagecreatefromgif($fsource);
				break;
			case 2 :
				if (!function_exists("imagecreatefromjpeg")) {
					echo 'please use gif png picture';
					exit ();

				}
				$picflag = 2;
				$im = @ imagecreatefromjpeg($fsource);
				break;

			case 3 :
				if (!function_exists("imagecreatefromjpeg")) {
					echo 'please use jpg gif picture';
					exit ();
				}
				$picflag = 3;
				$im = @ imagecreatefrompng($fsource);
				break;

		}

		$srcw = imagesx($im);
		$srch = imagesy($im);
		$scale = max($maxWidth / $srcw, $maxHeight / $srch);
		$picpos['x'] = ($srcw-$maxWidth)/2;
		$picpos['y'] = ($srch-$maxHeight)/2;
		if ($scale < 1) {
			$newWidth = floor($scale * $srcw);
			$newHeight = floor($scale * $srch);

		} else {

			$newWidth = $srcw;
			$newHeight = $srch;
		}
		if (function_exists("imagecreatetruecolor")) {

			$ni = imagecreatetruecolor($newWidth, $newHeight);
			if ($ni)
				imagecopyresampled($ni, $im, 0, 0, 0, 0, $newWidth, $newHeight, $srcw, $srch);
			else {
				$ni = imagecreate($newWidth, $newHeight);
				imagecopyresized($ni, $im, 0, 0, 0, 0, $newWidth, $newHeight, $srcw, $srch);
			}
		} else {
			$ni = imagecreate($newWidth, $newHeight);
			imagecopyresized($ni, $im, 0, 0, 0, 0, $newWidth, $newHeight, $srcw, $srch);
		}
		switch ($picflag) {
			case 1 :
				imagegif($ni, $fsource);
				break;

			case 2 :
				imagejpeg($ni, $fsource);
				break;

			case 3 :
				imagepng($ni, $fsource);
				break;

			case 6 :
				imagewbmp($ni, $fsource);
				break;

		}

		imagedestroy($ni);
		imagedestroy($im);
	}
}
?>
