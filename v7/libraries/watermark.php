<?php
class watermark {
	private $im_src; // 背景图文件
	private $im_src_width; // 背景图宽度
	private $im_src_height; // 背景图高度
	private $src_im; // 由背景图创建的新图
	private $im_water; // 水印图文件
	private $im_water_width; // 水印图宽度
	private $im_water_height; // 水印图高度
	private $water_im; // 由水印图创建的新图
	private $font; // 字体库
	private $font_text; // 文本
	private $font_size; // 字体大小
	private $font_color; // 字体颜色
	function setImSrc($img) {
		$this->im_src = $img;
		$srcInfo = @ getimagesize($this->im_src);
		$this->im_src_width = $srcInfo[0];
		$this->im_src_height = $srcInfo[1];
		$this->src_im = $this->getType($this->im_src, $srcInfo[2]);
	}

	function setImWater($img) {
		$this->im_water = $img;
		$waterInfo = @ getimagesize($this->im_water);
		$this->im_water_width = $waterInfo[0];
		$this->im_water_height = $waterInfo[1];
		$this->water_im = $this->getType($this->im_water, $waterInfo[2]);
	}

	function setFont($font, $text, $size, $color) {
		$this->font = $font;
		$this->font_text = $text;
		$this->font_size = $size;
		//水印文字颜色（'255,255,255'）
		$this->font_color = $color;
	}

	/**
	 * 根据文件或URL创建一个新图象
	 * @param $img
	 * @param $type
	 * @return resource
	 */
	function getType($img, $type) {
		switch ($type) {
			case 1 :
				$im = imagecreatefromgif($img);
				break;
			case 2 :
				$im = imagecreatefromjpeg($img);
				break;
			case 3 :
				$im = imagecreatefrompng($img);
				break;
			default :
				break;
		}
		return $im;
	}

	/**
	 * 根据位置及水印宽高，获取打印的x/y坐标
	 * @param $pos
	 * @param $w
	 * @param $h
	 */
	function getPos($pos, $w, $h) {
		switch ($pos) {
			case 0 : //随机
				$posX = rand(0, ($this->im_src_width - $w));
				$posY = rand(0, ($this->im_src_height - $h));
				break;
			case 1 : //1为顶端居左
				$posX = 0;
				$posY = 0;
				break;
			case 2 : //2为顶端居中
				$posX = ceil($this->im_src_width - $w) / 2;
				$posY = 0;
				break;
			case 3 : //3为顶端居右
				$posX = $this->im_src_width - $w;
				$posY = 0;
				break;
			case 4 : //4为中部居左
				$posX = 0;
				$posY = ceil($this->im_src_height - $h) / 2;
				break;
			case 5 : //5为中部居中
				$posX = ceil($this->im_src_width - $w) / 2;
				$posY = ceil($this->im_src_height - $h) / 2;
				break;
			case 6 : //6为中部居右
				$posX = $this->im_src_width - $w;
				$posY = ceil($this->im_src_height - $h) / 2;
				break;
			case 7 : //7为底端居左
				$posX = 0;
				$posY = $this->im_src_height - $h;
				break;
			case 8 : //8为底端居中
				$posX = ceil($this->im_src_width - $w) / 2;
				$posY = $this->im_src_height - $h;
				break;
			case 9 : //9为底端居右
				$posX = $this->im_src_width - $w-20;
				$posY = $this->im_src_height - $h-20;
				break;
			case 10 : //9为底端居右
				$posX = $this->im_src_width - $w;
				$posY = $this->im_src_height - $h;
				break;
			default : //随机
				$posX = rand(0, ($this->im_src_width - $w));
				$posY = rand(0, ($this->im_src_height - $h));
				break;
		}
		return array (
			$posX,
			$posY
		);
	}

	/**
	 * 校验尺寸
	 * @param $w
	 * @param $h
	 * @return boolean
	 */
	function check_range($w, $h) {
		if (($this->im_src_width < $w) || ($this->im_src_height < $h)) {
			return false;
		}
		return true;
	}

	/**
	 * 打水印操作
	 * @param $is_image   是1否0水印图片
	 * @param $image_pos  水印图片位置（0~9）
	 * @param $is_text    是1否0水印文字
	 * @param $text_pos   水印文字位置（0~9）
	 */
	function mark($is_image = 0, $image_pos = 0, $is_text = 0, $text_pos = 0,$over = 1) {
		// 水印图片情况
		if ($is_image) {
			$label = '图片的';
			if (!$this->check_range($this->im_water_width, $this->im_water_height)) {
				echo "需要加水印的图片的长度或宽度比水印" . $label . "还小，无法生成水印！";
				return;
			}
			$posArr = $this->getPos($image_pos, $this->im_water_width, $this->im_water_height);
			$posX = $posArr[0];
			$posY = $posArr[1];
			// 拷贝水印到目标文件
			imagecopy($this->src_im, $this->water_im, $posX, $posY, 0, 0, $this->im_water_width, $this->im_water_height);
		}
		// 水印文字情况
		if ($is_text) {
			$label = '文字区域';
			//取得此字体的文本的范围
			$temp = imagettfbbox($this->font_size, 0, $this->font, $this->font_text);
			$w = $temp[2] - $temp[0];
			$h = $temp[1] - $temp[7];
			unset ($temp);
			// 校验
			if (!$this->check_range($w, $h)) {
				echo "需要加水印的图片的长度或宽度比水印" . $label . "还小，无法生成水印！";
				return;
			}
			$posArr = $this->getPos($text_pos, $w, $h);
			$posX = $posArr[0];
			$posY = $posArr[1];
			// 打印文本
			$red = imagecolorallocate($this->src_im, 255, 0, 0);
			imagettftext($this->src_im, $this->font_size, 0, $posX, $posY, $this->font_color, $this->font, $this->font_text);
		}
		if($over){
			imagejpeg($this->src_im,$this->im_src);
			//copy($this->src_im,$this->im_src);
		}
		// 输出
	//	$this->show();
		// 清理
		$this->clean();
	}

	/**
	 * 输出图像
	 */
	function show() {
		ob_clean();
		header("Content-type: image/png; charset=UTF-8");
		imagepng($this->src_im);
	}

	/**
	 * 清理
	 */
	function clean() {
		imagedestroy($this->water_im);
		imagedestroy($this->src_im);
	}

}
?>