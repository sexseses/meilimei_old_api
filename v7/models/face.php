<?php
/*
 * WENRAN face detect
 * @Author 5lulu.com
 */
class face extends CI_Model {
	private $pic = '', $extpoint = array (), $facePoint = array ();
	public function __construct() {
		parent :: __construct();
	}
	function init($file, $extpoint) {
		$this->pic = $file;
		$this->extpoint = $extpoint;
	}
	function res() {
		$size = getimagesize($this->pic); //0w 1h
		$imgType = strtolower(substr(image_type_to_extension($size[2]), 1));
		$imageFun = 'imagecreatefrom' . ($imgType == 'jpg' ? 'jpeg' : $imgType);
		$i = $imageFun ($this->pic);
		$skin = $this->colorc($this->getRgb(array (
			'x' => $size[0] * $this->extpoint->mouth_left->x,
			'y' => $size[1] * $this->extpoint->mouth_left->y
		), $i));
		$skin += $this->colorc($this->getRgb(array (
			'x' => $size[0] * $this->extpoint->mouth_right->x,
			'y' => $size[1] * $this->extpoint->mouth_right->y
		), $i));
		$skin += $this->colorc($this->getRgb(array (
			'x' => $size[0] * $this->extpoint->nose->x,
			'y' => $size[1] * $this->extpoint->nose->y
		), $i));
		$skin /= 3;
		$skin++;
		$skin = 100 - log($skin, 6) * 10;
		if ($skin < 0) {
			$skin = 0;
		}
		$res['skin'] = round($skin);

		$this->faceArea();
		$TV = $this->getTV($size, $i);
		$TV = $this->toT($TV);
        if(!empty($TV)){
		if($this->input->post('score')){
			$this->GScore(true,$TV);
			echo '完成,后退继续!';
			exit;
		}
		$judge = $this->GScore(false);;
		$res['score'] = 1;
		foreach ($TV as $k => $v) {
           $res['score']+=isset($judge[$k][$v])?$judge[$k][$v]:0.1;
		}
		$res['score'] = intval($res['score']/1.5);
        }else{
        	$res['score'] = $res['skin']+rand(1,5);
        }
		return $res;

	}
	//change to two value
	private function toT($TV) {
		foreach ($TV as $v) {
			foreach ($v as $r) {
				$sum += $r;
			}
		}
		$sum = $sum / 100;
		$res = array ();
		foreach ($TV as $k => $v) {
			foreach ($v as $j => $r) {
				$res[] = $r > $sum ? 1 : 0;
			}
		}
		return $res;
	}
	//get tow value poion
	private function getTV($size, & $im) {
		$TV = array ();
		$x = $y = 0;
		$xplun = intval($size[0] * ($this->facePoint['R']['X'] - $this->facePoint['L']['X']) / 1000);
		$yplun = intval($size[1] * ($this->facePoint['R']['Y'] - $this->facePoint['L']['Y']) / 1000);
		$xstart = intval($this->facePoint['L']['X'] * $size[0] / 100);
		$xend = intval($this->facePoint['R']['X'] * $size[0] / 100);
		$Ystart = intval($this->facePoint['L']['Y'] * $size[1] / 100);
		$Yend = intval($this->facePoint['R']['Y'] * $size[1] / 100);
        if($xplun==0 OR $yplun==0){
			return array();
		}
		for ($i = $xstart; $i <= $xend; $i += $xplun) {
			$y = 0;
			for ($j = $Ystart; $j <= $Yend; $j += $yplun) {
				$TV[$x][$y] = $this->avgHV($i, $j, $xplun, $yplun, $im);
				$y++;
			}
			$x++;
		}
		return $TV;
	}
	//avg light
	private function avgHV($x, $y, $xPLUS, $yPLUS, & $im) {
		$v = 0;

		for (; $xPLUS >= 0; $xPLUS--) {
			for (; $yPLUS >= 0; $yPLUS--) {
				$v += $this->RGB_TO_HSV($this->getRgb(array (
					'x' => $x,
					'y' => $y
				), $im));
				$y++;
			}
			$x++;
		}

		return intval($v / ($xPLUS * $yPLUS));
	}

	//generate face areas
	private function faceArea() {
		$this->facePoint['L']['Y'] = $this->extpoint->position->eye_right->y - ($this->extpoint->position->nose->y - $this->extpoint->position->eye_right->y);
		$this->facePoint['L']['Y'] < 0 && $this->facePoint['L']['Y'] = 0;
		$this->facePoint['R']['Y'] = $this->extpoint->position->nose->y + ($this->extpoint->position->nose->y - $this->extpoint->position->eye_right->y);
		$this->facePoint['R']['Y'] < 0 && $this->facePoint['R']['Y'] = 0;

		$this->facePoint['L']['X'] = $this->extpoint->position->eye_left->x - ($this->extpoint->position->eye_right->x - $this->extpoint->position->eye_left->x);
		$this->facePoint['L']['X'] < 0 && $this->facePoint['L']['X'] = 0;
		$this->facePoint['R']['X'] = $this->extpoint->position->eye_right->x + ($this->extpoint->position->eye_right->x - $this->extpoint->position->eye_left->x);
		$this->facePoint['R']['X'] < 0 && $this->facePoint['R']['X'] = 0;
	}

	private function getRgb($pos, & $im) {
		$rgb = imagecolorat($im, $pos['x'], $pos['y']);
		$point['r'] = ($rgb >> 16) & 0xFF;
		$point['g'] = ($rgb >> 8) & 0xFF;
		$point['b'] = $rgb & 0xFF;
		return $point;
	}
	private function colorc($point = array (
		'r' => 0,
		'g' => 0,
		'b' => 0
	)) {
		return sqrt(pow(235 - $point['r'], 2) + pow(210 - $point['g'], 2) + pow(197 - $point['b'], 2));
	}

	private function GScore($state = true,$Tv=array()) {
		if ($state) {
			$inseart['data'] = serialize($Tv);
			$inseart['score'] = intval($this->input->post('score'));
            $this->db->insert('AI_faces', $inseart);

			$tmp = $this->db->get('AI_faces')->result_array();
			$save = array ();
			$num = count($tmp);
			foreach ($tmp as $r) {
				$fdata = unserialize($r['data']);
				$add = $r['score'] / $num;
				$add /= 50;
				foreach ($fdata as $k => $v) {
					if (isset ($save[$k][$v])) {
						$save[$k][$v] += $add;
					} else {
						$save[$k][$v] = $add;
					}
				}
			}
			$idata = array (
				'data' => serialize($save
			));
			$this->db->where('id', 100);
			$this->db->update('AI_faces', $idata);

		} else {
			$this->db->where('id', 100);
			$tmp = $this->db->get('AI_faces')->result_array();
			return unserialize($tmp[0]['data']);
		}
	}
	function RGB_TO_HSV($arr) // RGB Values:Number 0-255
	{
		$HSL = array ();
		$var_R = ($arr['r'] / 255);
		$var_G = ($arr['g'] / 255);
		$var_B = ($arr['b'] / 255);

		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;

		$V = $var_Max;

		if ($del_Max == 0) {
			$H = 0;
			$S = 0;
		} else {
			$S = $del_Max / $var_Max;

			$del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
			$del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
			$del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;

			if ($var_R == $var_Max)
				$H = $del_B - $del_G;
			else
				if ($var_G == $var_Max)
					$H = (1 / 3) + $del_R - $del_B;
				else
					if ($var_B == $var_Max)
						$H = (2 / 3) + $del_G - $del_R;

			if ($H < 0)
				$H++;
			if ($H > 1)
				$H--;
		}

		$HSL['H'] = $H;
		$HSL['S'] = $S;
		$HSL['V'] = $V;
		return $HSL['V'];
	}
}
?>
