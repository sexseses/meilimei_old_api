<?php
class checkcode extends CI_Controller {
	private $length, $number = 1;
	public function __construct() {
		parent :: __construct();
		$this->length = 4;
		$this->number = 0;
	}
	function C($code = '') {
		$result['state'] = 0;
		if ($code && $code == $this->session->userdata('authcode')) {
			$result['state'] = 1;
		}
		echo json_encode($result);
	}
	function G($width = 100, $heiht = 37) {
		mt_srand((double) microtime() * 1000000);
		if ($this->number) {
			$hash = sprintf('%0' . $this->length . 'd', mt_rand(0, pow(10, $this->length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEQRSTUVWXYZ23456FGHIJKLMNP789';
			$max = strlen($chars) - 1;
			for ($i = 0; $i < $this->length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		$this->session->set_userdata('validecode', strtolower($hash));
		$im = imagecreate($width, $heiht);
		$backgroundcolor = imagecolorallocate($im, 255, 255, 255);
		$numorder = array (
			1,
			2,
			3,
			4
		);
		shuffle($numorder);
		$numorder = array_flip($numorder);

		for ($i = 1; $i <= 4; $i++) {
			$x = $numorder[$i] * 20 + mt_rand(0, 4) - 2;
			$y = mt_rand(2, 12);
			$text_color = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 128), mt_rand(50, 255));
			imagechar($im,5, $x +12, $y, $hash[$numorder[$i]], $text_color);
		}

		$linenums = mt_rand(10, 32);
		for ($i = 0; $i < 4; $i++)
		 {
			$color1 = ImageColorAllocate($im, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255));
			ImageArc($im, mt_rand(-5, $width), mt_rand(-5, $heiht), mt_rand(30, $width), mt_rand(30, $heiht), 55, 44, $color1);
		}

		for ($i = 0; $i <= $linenums; $i++) {
			$linecolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			$linex = mt_rand(0, $width -2);
			$liney = mt_rand(0, $heiht -2);
			imageline($im, $linex, $liney, $linex +mt_rand(0, 4) - 2, $liney +mt_rand(0, 4) - 2, $linecolor);
		}

		for ($i = 0; $i <= $width -2; $i++) {
			$pointcolor = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 255), mt_rand(50, 255));
			imagesetpixel($im, mt_rand(0, $width -2), mt_rand(0, $heiht -2), $pointcolor);
		}

		$bordercolor = imagecolorallocate($im, 150, 150, 150);
		imagerectangle($im, 0, 0, $width -2, $heiht -2, $bordercolor);
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	private function upload($file) {
		$target_path = realpath(APPPATH . '../upload');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path .'/'. date('Y'))) {
				mkdir($target_path .'/'. date('Y'), 0777, true);
			}
			$extend =explode("." , $file["name"]);
            $va=count($extend)-1;
            $tmp = date('Y') . '/' . time().'.' . $extend[$va];
			$target_path .= '/' .$tmp;
			move_uploaded_file($file["tmp_name"], $target_path);
			return 'upload/'.$tmp;
		}
		return false;
	}
}
?>
