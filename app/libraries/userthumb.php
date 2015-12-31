<?php
class userthumb {
	private $url = 'http://static.meilimei.com.cn/';
	//get thumb
	function get($id, $pos = 0) {
		if ($id <= 22541) {
			return $this->profilepic($id, $pos);
		} else {
           return $this->profilepic2($id, $pos);
		}
	}
	//generate thumb url
	function gset($id) {
		return 'users/' . round($id / 1000000) . '/' . round($id / 3000) . '/'.$id;
	}
	//profile pic type with dif folder
	private function profilepic2($id, $pos = 0) {
		$path = '/mnt/meilimei/';
		$dir = 'users/' . round($id / 1000000) . '/' . round($id / 3000) . '/';
		if (is_dir($path . $dir . $id)) {
			$files = scandir($path . $dir . $id);
			$files = array_diff($files, array (
				'.',
				'..'
			));
			if (count($files) > 1) {
				if ($pos == 1) {
					return $this->url . $dir . $id . '/userpic_thumb.jpg';
				} else
					if ($pos == 2) {
						return $this->url . $dir . $id . '/userpic_profile.jpg';
					} else {
						return $this->url . $dir . $id . '/userpic.jpg';
					}
			}
		}
		if ($pos == 1) {
			return $this->url . 'images/no_avatar_thumb.jpg';
		} else
			if ($pos == 2) {
				return $this->url . 'images/no_avatar-xlarge.jpg';
			} else {
				return $this->url . 'images/no_avatar.jpg';
			}
	}
	//profile pic
	private function profilepic($id, $pos = 0) {
		$path = '/mnt/meilimei';
		if (is_dir($path . '/users/' . $id)) {
			$files = scandir($path . '/users/' . $id);
			$files = array_diff($files, array (
				'.',
				'..'
			));
			if (count($files) > 1) {
				if ($pos == 1) {
					$url = $this->url . 'users/' . $id . '/userpic_thumb.jpg';
				} else
					if ($pos == 2) {
						$url = $this->url . 'users/' . $id . '/userpic_profile.jpg';
					} else {
						$url = $this->url . 'users/' . $id . '/userpic.jpg';
					}
			} else {
				if ($pos == 1) {
					$url = $this->url . 'images/no_avatar_thumb.jpg';
				} else
					if ($pos == 2) {
						$url = $this->url . 'images/no_avatar-xlarge.jpg';
					} else {
						$url = $this->url . 'images/no_avatar.jpg';
					}

			}
		} else {
			if ($pos == 1) {
				$url = $this->url . 'images/no_avatar_thumb.jpg';
			} else
				if ($pos == 2) {
					$url = $this->url . 'images/no_avatar-xlarge.jpg';
				} else {
					$url = $this->url . 'images/no_avatar.jpg';
				}
		}
		return $url;
	}
}
?>
