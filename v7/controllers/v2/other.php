<?php
require_once(__DIR__."/MyController.php");
class other extends MY_Controller {
	public function __construct() {
		parent :: __construct();
		$this->load->model('auth');
		$this->load->model('Email_model');
	}
	function banner($param = '') {

		$data = array ();
		$this->db->where('machine', 'phone');
		$this->db->or_where('machine', '');
		$this->db->order_by("id", "desc");
		$this->db->limit(10);
		$tmp = $this->db->get('banner')->result_array();
		$result['version'] = 1.1;
		foreach ($tmp as $r) {
			unset ($r['machine']);
			unset ($r['is_show']);
			unset ($r['tags']);
			unset ($r['pos']);
			unset ($r['weigh']);
			$r['picture'] = site_url() . $r['picture'];
			$r['cdate'] = date('Y-m-d', $r['cdate']);
			$result['data'][] = $r;
		}

		echo json_encode($result);
	}
	function contractPic($param = ''){

        $page = intval($this->input->get('page'))-1;
        $perpage = 18;
        $tmp = $this->getAllFiles();
        $tmp1 = $this->getAllFiles1();
        $result['total'] = count($tmp);
        $i=$page*$perpage;
        $result['data'] = array();
        $baseurl = site_url();
        for($j=0;$j<$perpage;$j++){
          isset($tmp1[$j+$i])&&$result['data1'][] = $baseurl.$tmp1[$j+$i];
          $result['data1'][] = str_replace('zhengrong_2','zhengrong_1',$baseurl.$tmp1[$j+$i]);
          isset($tmp[$j+$i])&&$result['data'][] = $baseurl.$tmp[$j+$i];
          $result['data'][] = str_replace('zhengrong_1','zhengrong_2',$baseurl.$tmp[$j+$i]);
        }
	
		echo json_encode($result);
	}
	 function contractBPic($param = ''){

        $picstr = $this->input->get('picstr');
        $result['data'] = str_replace('zhengrong_2','zhengrong_1',$picstr);
		echo json_encode($result);
	}
	private function getAllFiles() {
		$path = 'upload/zhengrong_2/';
		$result = array ();
		if (is_dir($path)) {
			$handle = opendir($path);
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..")
					continue;
				$result[] =  $path . $file;
			}
			closedir($handle);
		} else {
			exit ("没有这个文件夹!");
		}
		return $result;
	}
	private function getAllFiles1() {
		$path = 'upload/zhengrong_1/';
		$result = array ();
		if (is_dir($path)) {
			$handle = opendir($path);
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..")
					continue;
				$result[] =  $path . $file;
			}
			closedir($handle);
		} else {
			exit ("没有这个文件夹!");
		}
		return $result;
	}
}
?>
