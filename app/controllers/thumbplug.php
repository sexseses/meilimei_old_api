<?php
class thumbplug extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('user/login');
		}

		$this->load->helper('file');
	}

	public function index() {
		if (!$this->session->userdata('random_key')) {
			$this->session->set_userdata('random_key',strtotime(date('Y-m-d H:i:s')));
			$this->session->set_userdata('user_file_ext','');
		}
        $data=array();
        $data['error'] = '';
        $tmpext = '.jpg';
        $data['current_large_image_width'] = 0;
	    $data['current_large_image_height'] = 0;
		$data['upload_dir'] = 'tmp' ; // The directory for the images to be saved in
		$data['upload_path'] = $data['upload_dir'] . "/"; // The path to where the image will be saved
		$large_image_prefix = "resize_"; // The prefix name to large image
		$thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb image
		$data['large_image_name'] = $this->uid; // New name of the large image (append the timestamp to the filename)
		$thumb_image_name = $this->uid.'_userpic'; // New name of the thumbnail image (append the timestamp to the filename)
		$max_file = "3"; // Maximum file size in MB
		$max_width = "1200"; // Max width allowed for the large image
		$data['thumb_width'] = "250"; // Width of thumbnail image
		$data['thumb_height'] = "250"; // Height of thumbnail image
		// Only one of these image types should be allowed for upload
		$allowed_image_types = array (
			'image/pjpeg' => "jpg",
			'image/jpeg' => "jpg",
			'image/jpg' => "jpg",
			'image/png' => "png",
			'image/x-png' => "png",
			'image/gif' => "gif"
		);
		$allowed_image_ext = array_unique($allowed_image_types); // do not change this
		$image_ext = ""; // initialise variable, do not change this.
		foreach ($allowed_image_ext as $mime_type => $ext) {
			$image_ext .= strtoupper($ext) . " ";
		}
		//Image Locations
		$large_image_location = $data['upload_path'] . $data['large_image_name'] . $tmpext;
		$thumb_image_location = $data['upload_path'] . $thumb_image_name . $tmpext;
        if(file_exists($large_image_location)){
        	$data['current_large_image_width'] = $this->getWidth($large_image_location);
		$data['current_large_image_height'] =$this->getHeight($large_image_location);
        }

		//Create the upload directory with the right permissions if it doesn't exist
		if (!is_dir($data['upload_dir'])) {
			mkdir($data['upload_dir'], 0777);
			chmod($data['upload_dir'], 0777);
		}
		//Check to see if any images with the same name already exist
		if (file_exists($large_image_location)) {
			if (file_exists($thumb_image_location)) {
				$data['thumb_photo_exists'] = "<img src=\"" .site_url(). $data['upload_path'] . $thumb_image_name . $tmpext .'?'.time(). "\" alt=\"Thumbnail Image\"/>";
			} else {
				$data['thumb_photo_exists'] = "";
			}
			$data['large_photo_exists'] = "<img src=\"" .site_url(). $data['upload_path'] . $data['large_image_name'] . $tmpext .'?'.time(). "\" alt=\"Large Image\"/>";
		} else {
			$data['large_photo_exists'] = "";
			$data['thumb_photo_exists'] = "";
		}

		if (isset ($_POST["upload"])) {
			//Get the file information
			$userfile_name = $_FILES['image']['name'];
			$userfile_tmp = $_FILES['image']['tmp_name'];
			$userfile_size = $_FILES['image']['size'];
			$userfile_type = $_FILES['image']['type'];
			$filename = basename($_FILES['image']['name']);
			$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

			//Only process if the file is a JPG, PNG or GIF and below the allowed limit
			if ((!empty ($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

				foreach ($allowed_image_types as $mime_type => $ext) {
					//loop through the specified image types and if they match the extension then break out
					//everything is ok so go and check file size
					if ($file_ext == $ext && $userfile_type == $mime_type) {
						$data['error'] = "";
						break;
					} else {
						$data['error'] = "Only <strong>" . $image_ext . "</strong> images accepted for upload<br />";
					}
				}
				//check if the file size is above the allowed limit
				if ($userfile_size > ($max_file * 1048576)) {
					$data['error'] .= "Images must be under " . $max_file . "MB in size";
				}

			} else {
				$data['error'] = "Select an image for upload";
			}
			//Everything is ok, so we can upload the image.
			if (strlen($data['error']) == 0) {

				if (isset ($_FILES['image']['name'])) {
					//this file could now has an unknown file extension (we hope it's one of the ones set above!)
					$large_image_location = $large_image_location  ;
					$thumb_image_location = $thumb_image_location ;

					//put the file ext in the session so we know what file to look for once its uploaded

                    $this->session->set_userdata('user_file_ext',"." . $file_ext);
					move_uploaded_file($userfile_tmp, $large_image_location);
					GenerateThumbFile($large_image_location, $large_image_location,370, 650);
					chmod($large_image_location, 0777);

					 $width = $this->getWidth($large_image_location);
					 $height = $this->getHeight($large_image_location);
					//Scale the image if it is greater than the width set above
					if ($width > $max_width) {
						$scale = $max_width / $width;
						$uploaded = $this->resizeImage($large_image_location, $width, $height, $scale);
					} else {
						$scale = 1;
						$uploaded = $this->resizeImage($large_image_location, $width, $height, $scale);
					}
					//Delete the thumbnail file so the user can create a new one
					if (file_exists($thumb_image_location)) {
						unlink($thumb_image_location);
					}
				}
				//Refresh the page to show the new uploaded image
				header("location:" . $_SERVER["PHP_SELF"]);
				exit ();
			}
		}

		if (isset ($_POST["upload_thumbnail"]) && strlen($data['large_photo_exists']) > 0) {
			//Get the new coordinates to crop the image.
			$x1 = $_POST["x1"];
			$y1 = $_POST["y1"];
			$x2 = $_POST["x2"];
			$y2 = $_POST["y2"];
			$w = $_POST["w"];
			$h = $_POST["h"];
			//Scale the image to the thumb_width set above
			$scale = $data['thumb_width'] / $w;
			$cropped = $this->resizeThumbnailImage($thumb_image_location, $large_image_location, $w, $h, $x1, $y1, $scale);
			$this->thumb($thumb_image_location,$large_image_location,$this->uid);
			//Reload the page again to view the thumbnail
			//redirect('thumbplug');
		}

        $data['notlogin'] = $this->notlogin;
		$this->load->view('theme/include/thumbplug', $data);
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
  	private function thumb($file='',$large_image_location,$uid) {
  	   unlink($large_image_location);
       $this->load->model('remote');
       $this->remote->upThumbAndDel($file,$uid);
       echo '图片已经成功上传!';
       exit;
	}
}
?>






