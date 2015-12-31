<?php
class Gallery extends CI_Model {
	var $path;
	var $gallery_path_url;
	var $logopath;
	function Gallery() {
		parent :: __construct();
		$this->path = realpath(APPPATH . '../images');
		$this->gallery_path_url = base_url() . 'images/';
		$this->logopath = realpath(APPPATH . '../');
	}

	function do_upload($id) {
		if (!is_dir($this->path . '/' . $id)) {
			//echo $this->path.'/'.$id;
			mkdir($this->path . '/' . $id, 0777, true);
		}
		$config = array (
			'allowed_types' => 'jpg|jpeg|gif|png',
			'upload_path' => $this->path . '/' . $id
		);
		//echo $this->path.'/'.$id;
		$this->load->library('upload', $config);
		$this->upload->do_upload();
	}

	function do_upload_logo() {

	}
	public function get_images($id, $conditions = array (), $limit = array ()) {
		$images = array ();
		if (is_dir($this->path . '/' . $id)) {
			$files = scandir($this->path . '/' . $id);
			$files = array_diff($files, array (
				'.',
				'..'
			));
			foreach ($files as $file) {
				if ($file != 'Thumbs.db') {
					$images[] = array (
						'url' => $this->gallery_path_url . $id . '/' . $file,
						'path' => $this->path . '/' . $id . '/' . $file
					);
				}
			}
		}
		return $images;
	}

	public function get_imagesG($id, $conditions = array (), $limit = array (), $orderby = array ()) {
		if ($id != '')
			$this->db->where('list_id', $id);

		//Check For Conditions
		if (is_array($conditions) and count($conditions) > 0)
			$this->db->where($conditions);

		//Check For Limit
		if (is_array($limit)) {
			if (count($limit) == 1)
				$this->db->limit($limit[0]);
			else
				if (count($limit) == 2)
					$this->db->limit($limit[0], $limit[1]);
		}

		//Check for Order by
		if (is_array($orderby) and count($orderby) > 0)
			$this->db->order_by($orderby[0], $orderby[1]);

		$this->db->from('list_photo');

		$result = $this->db->get();

		return $result;
	}

	public function helper_image($id) {
		$images = $this->get_images($id);
		if (count($images) == 0)
			$url = base_url() . 'images/no_image.jpg';
		else
			$url = $images[0]['url'];
		return $url;
	}

	function Udo_upload($id) {
		if (!is_dir($this->path . '/users/' . $id)) {
			//echo $this->path.'/'.$id;
			mkdir($this->path . '/users/' . $id, 0777, true);
		}
		$config = array (
			'allowed_types' => 'jpg|jpeg|gif|png',
			'upload_path' => $this->path . '/users/' . $id . '/'
		);
		//echo $this->path.'/users/'.$id;
		$this->load->library('upload', $config);
		$this->upload->do_upload();
	}

	public function Uget_images($id) {
		$images = array ();
		if (is_dir($this->path . '/users/' . $id)) {
			$files = scandir($this->path . '/users/' . $id);
			$files = array_diff($files, array (
				'.',
				'..'
			));
			foreach ($files as $file) {
				$images[] = array (
					'url' => $this->gallery_path_url . $id . '/users/' . $file
				);
			}
		}
		return $images;
	}

	public function profilepic($id, $pos = 0) {
		$this->load->model('remote');
		switch ($pos) {
			case 1:
			    return $this->remote->thumb($id,'36');
			case 0:
			    return $this->remote->thumb($id,'250');
		    case 2:
                return $this->remote->thumb($id,'120');
			default:
			    return $this->remote->thumb($id,'120');
				break;
		}
	}
}
?>