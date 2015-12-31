<?php
class category extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('category')){
          die('Not Allow');
       }
	}
	public function index($param = 0) {
		//$this->load->library('pagination');
		$this->db->from('new_items');
		$this->db->where('pid', $param);
		$this->db->select('id,pid,name,order');
		$page = $this->input->get('per_page');
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "category";
		$data['pid'] = $param;
		$this->load->view('manage', $data);
	}
	public function del($param = '') {
		if ($param) {
			$this->db->delete('new_items', array (
				'id' => $param
			));
			redirect('manage/category');
		}
	}
	public function sec() {

		//$this->load->library('pagination');
		$this->db->from('new_items');
		$this->db->where('is_hot', 1);
		$this->db->select('id,pid,name,order');
		$page = $this->input->get('per_page');
		$data['results'] = $this->db->get()->result();
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "category";
		$data['pid'] = 0;
		$this->load->view('manage', $data);
	}
	public function edit($param = '') {
		$this->db->where('id', $param);
		if ($this->input->post('name')) {
			$udata['name'] = trim($this->input->post('name'));
			$udata['price'] = $this->input->post('price');
			$udata['des'] = $this->input->post('des');
			$udata['order'] = $this->input->post('order');
			$udata['app'] = $this->input->post('app');
			$udata['pid'] = $this->input->post('position');
            $pid = $udata['pid'];
			$udata['is_hot'] = $this->input->post('is_hot');
			$udata['is_default'] = $this->input->post('is_default');
			$udata['attention'] = $this->input->post('attention');
			$udata['safety'] = $this->input->post('safety');
			$udata['complexity'] = $this->input->post('complexity');
			$udata['satisfaction'] = $this->input->post('satisfaction');
			$udata['treatment'] = $this->input->post('treatment');
			$udata['treatment_time'] = $this->input->post('treatment_time');
			$udata['tlasts'] = $this->input->post('tlasts');
			$udata['recovery_time'] = $this->input->post('recovery_time');
			$udata['DStreatments'] = $this->input->post('DStreatments');
			$udata['XGtreatment'] = $this->input->post('XGtreatment');
			$udata['crowd'] = $this->input->post('crowd');
			$udata['recovery_process'] = $this->input->post('recovery_process');
			$udata['notice'] = $this->input->post('notice');
			$udata['advantage'] = $this->input->post('advantage');
			$udata['shortcomings'] = $this->input->post('shortcomings');
			$udata['risk'] = $this->input->post('risk');
            $udata['other'] = $this->input->post('other');
            $udata['isrecommend'] = $this->input->post('isrecommend');

			if ($_FILES["surl"]['name']) {
				$udata['surl'] = $this->upload($_FILES["surl"]);
				$this->cropImg($udata['surl'],45,45);
				if ($this->input->post('ssurl')) {
					unlink('../upload/' . $this->input->post('ssurl'));
				}
			}
			if ($_FILES["burl"]['name']) {
				$udata['burl'] = $this->upload($_FILES["burl"]);
				$this->cropImg($udata['burl'],45,45);
				if ($this->input->post('sburl')) {
					unlink('../upload/' . $this->input->post('sburl'));
				}
			}
			$udata['type'] = 0;
           	if($tmpv = $this->input->post('type')){
              foreach($tmpv as $v){
                $udata['type'] +=$v;
              }
			}
			$this->db->update('new_items', $udata);
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '项目更新成功!'));
            if($pid){
                redirect('manage/category/index/'.$pid);
            }else{
                redirect('manage/category');
            }
		}
		$data['results'] = $this->db->get('new_items')->result();
		$data['clists'] = $this->GList(-1);

		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "categoryedit";
		$this->load->view('manage', $data);
	}
	//get list category
	private function GList($param=0){
		if($param>-1){
			$this->db->where('pid', $param);
		}
        return $this->db->get('new_items')->result_array();
	}
	public function add($pid = 0) {
		if ($this->input->post('name')) {

			$adata['name'] = trim($this->input->post('name'));
			$adata['pid'] = $pid;
			$adata['price'] = $this->input->post('price');
			$adata['des'] = $this->input->post('des');
			$adata['order'] = $this->input->post('order');
			$adata['is_hot'] = $this->input->post('is_hot');
			$udata['is_default'] = $this->input->post('is_default');
			if(isset($_FILES["surl"]['name']) and $_FILES["surl"]['name']){
				$adata['surl'] = $this->upload($_FILES["surl"]);
			    $this->cropImg($adata['surl'],45,45);
			}
           if(isset($_FILES["burl"]['name']) and $_FILES["burl"]['name']){
             	$adata['burl'] = $this->upload($_FILES["burl"]);
             	$this->cropImg($adata['burl'],45,45);
           }

			$this->common->insertData('new_items', $adata);
			$this->session->set_flashdata('msg', $this->common->flash_message('success', '项目添加成功!'));
            if($pid){
                redirect('manage/category/index/'.$pid);
            }else{
			    redirect('manage/category');
            }
		}
		$data['notlogin'] = $this->notlogin;
		$data['message_element'] = "categoryadd";
		$this->load->view('manage', $data);
	}
	private function upload($file) {
		$target_path = realpath(APPPATH . '../upload');
		if (!is_writable($target_path)) {
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '上传失败！'));
			redirect('users/edit', 'refresh');
		} else {
			if (!is_dir($target_path . '/' . date('Y'))) {
				mkdir($target_path . '/' . date('Y'), 0777, true);
			}
			$extend = explode(".", $file["name"]);
			$va = count($extend) - 1;
			$tmp = date('Y') . '/' . time() . '.' . $extend[$va];
			$target_path .= '/' . $tmp;
			move_uploaded_file($file["tmp_name"], $target_path);
			return  $tmp;
		}
		return false;
	}
	private function cropImg($Image, $Dw, $Dh, $Type = 2) {
	    if ($Type != 1) {
	    	$tmp = realpath(APPPATH . '../upload').'/'.str_replace('.', '_' . $Dw . '.', $Image);
	     	$Image = realpath(APPPATH . '../upload').'/'.$Image;

			copy($Image, $tmp) or die('error');
			$Image = $tmp;
		}
		if (!file_exists($Image)) {
			echo "不存在图片";
			return false;
		}
		$ImgInfo = getimagesize($Image);
		switch ($ImgInfo[2]) {
			case 1 :
			    $fn = 'imagegif';
				$im = @ imagecreatefromgif($Image);
				break;
			case 2 :
			     $fn = 'imagejpeg';
				$im = @ imagecreatefromjpeg($Image);
				break;
			case 3 :
			    $fn = 'imagepng';
				$im = @ imagecreatefrompng($Image);
				break;
			default :
				echo "格式不支持";
				return false;
		}

		$w = ImagesX($im);
		$h = ImagesY($im);
		$width = $w;
		$height = $h;
		if ($width > $Dw) {
			$Par = $Dw / $width;
			$width = $Dw;
			$height = $height * $Par;
			if ($height > $Dh) {
				$Par = $Dh / $height;
				$height = $Dh;
				$width = $width * $Par;
			}
		}
		elseif ($height > $Dh) {
			$Par = $Dh / $height;
			$height = $Dh;
			$width = $width * $Par;
			if ($width > $Dw) {
				$Par = $Dw / $width;
				$width = $Dw;
				$height = $height * $Par;
			}
		} else {
			$width = $width;
			$height = $height;
		}
		$nImg = imagecreatetruecolor($width, $height);
		imagealphablending($nImg, false);
		imagesavealpha($nImg, true);
		ImageCopyReSampled($nImg, $im, 0, 0, 0, 0, $width, $height, $w, $h);
		$fn($nImg, $Image);
		return true;
	}
}
?>
