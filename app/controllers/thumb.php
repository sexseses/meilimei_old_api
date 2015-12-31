<?php
class thumb extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('user/login');
		}
        $this->load->model('remote');
		$this->load->helper('file');
	}

	public function index() {
		if($_FILES['image']['tmp_name']){
			$this->remote->uputhumb($_FILES['image']['tmp_name'],$this->uid);
			$this->session->set_flashdata('msg', $this->common->flash_message('error', '头像已更新！'));
		    redirect('user/info');
		}
        $data['notlogin'] = $this->notlogin;
		$data['message_element'] = "uploadthumb";
		$this->load->view('template', $data);
	}

	private function resizeImage($image, $width, $height, $scale) {
		list ($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
		switch ($imageType) {
			case "image/gif" :
				$source = imagecreatefromgif($image);
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				$source = imagecreatefromjpeg($image);
				break;
			case "image/png" :
			case "image/x-png" :
				$source = imagecreatefrompng($image);
				break;
		}
		imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);

		switch ($imageType) {
			case "image/gif" :
				imagegif($newImage, $image);
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				imagejpeg($newImage, $image, 90);
				break;
			case "image/png" :
			case "image/x-png" :
				imagepng($newImage, $image);
				break;
		}

		chmod($image, 0777);
		return $image;
	}

	//You do not need to alter these functions
	private function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale) {
		list ($imagewidth, $imageheight, $imageType) = getimagesize($image);
		$imageType = image_type_to_mime_type($imageType);

		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
		switch ($imageType) {
			case "image/gif" :
				$source = imagecreatefromgif($image);
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				$source = imagecreatefromjpeg($image);
				break;
			case "image/png" :
			case "image/x-png" :
				$source = imagecreatefrompng($image);
				break;
		}
		imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
		switch ($imageType) {
			case "image/gif" :
				imagegif($newImage, $thumb_image_name);
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				imagejpeg($newImage, $thumb_image_name, 90);
				break;
			case "image/png" :
			case "image/x-png" :
				imagepng($newImage, $thumb_image_name);
				break;
		}
		chmod($thumb_image_name, 0777);
		return $thumb_image_name;
	}
	//You do not need to alter these functions
	private function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}
	//You do not need to alter these functions
	private function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}
  	private function thumb($uid) {
		$target_path = realpath(APPPATH . '../images/users');
		if (!is_writable($target_path)) {
			return false;
		} else {
			if (!is_dir($target_path . '/' . $uid)) {
				mkdir($target_path . '/' . $uid, 0777, true);
			}
			$target_path = $target_path . '/' . $uid . '/';
		  GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_thumb.jpg', 36, 36);
			 GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_profile.jpg', 120, 120);
		   return true;
		}
	}
}
?>






