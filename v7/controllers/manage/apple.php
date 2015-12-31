<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-23
 * Time: 上午10:41
 * To change this template use File | Settings | File Templates.
 */
class apple extends CI_Controller { //苹果app端广告管理
	private $adPos = array (
		'1' => 'app主页上端轮播图片',
		'2' => 'app主页下端文章列表'
	);
	private $notlogin = true, $uid = '',$imgurl = "http://pic.meilimei.com.cn/upload/",$qiniuimgurl= "http://7xkdi8.com1.z0.glb.clouddn.com/";
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		//error_reporting(E_ALL);
		//ini_set("display_errors","On");
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
		$this->load->model('remote');
		if (!$this->privilege->judge('apple')) {
			die('Not Allow');
		}

	}

	/**
	 * 广告管理
	 */
	public function index() {
		$page = intval($this->input->get('page'));

		$data = array ();
		$data['adPosArr'] = $this->adPos;
		//$page = $this->input->get("page");
		$title = $this->input->post('sname');
		$where = " where 1 ";
		if ($title) {
			$where .= " and title like '%$title%'";
		}
		$this->load->library('pager');

		$config['base_url'] = base_url("manage/apple/index");
		$tmp =  $this->db->query("select count(*) as num from apple $where")->result_array() ;

		$config['per_page'] = $per = 15;
		$config['first_link'] = '首页';
		$config['last_link'] = '尾页';
		;
		$config['uri_segment'] = '4';
		$config['num_links'] = 2;

		$page = $page ? $page : 1;
		$sql = "select * from apple $where order by cdate desc limit " . ($page -1) * $per . ",$per";
		$results = $this->db->query($sql)->result_array();
		$data['results'] = $results;

		$data['message_element'] = "apple";
         $config =array(
                "record_count"=>$tmp[0]['num'],
                "pager_size"=>$per,
              //  "show_jump"=>true,
               // 'querystring_name'=>$fixurl.'page',
                'base_url'=>'manage/apple/index',
                "pager_index"=>$page
            );
        $this->pager->init($config);

		$data['pages'] = $this->pager->builder_pager();

		$this->load->view('manage', $data);
	}
	//extra product links
	public function linksproduct() {
		$data['results'] = array ();
		$this->load->library('pager');
		$fix = '';
		$data['total_rows'] = $this->db->count_all_results('product_promotion');
		$per_page = 16;
		$page = intval($this->input->get('page'));
		$start = intval($page);
		$start == 0 && $start = 1;

		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = 0;
        $this->db->order_by("id", "desc");
		$this->db->limit(16, $offset);
		$data['results'] = $this->db->get('product_promotion')->result_array();

		$config = array (
			"record_count" => $data['total_rows'],
			"pager_size" => $per_page,
			"show_jump" => true,
			'querystring_name' => $fix . '&page',
			'base_url' => 'manage/topic/index',
			"pager_index" => $page
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		$query = $this->db->get('product_promotion');
		$data['message_element'] = "appleProduct";
		$this->load->view('manage', $data);
	}
	//edit produc link
	public function editlink($id = '') {
		if ($id) {
			if ($this->input->post('taobao_title')) {
				$other['title'] = $this->input->post('taobao_title');
				$other['url'] = $this->input->post('taobao_web');
				$other['price'] = $this->input->post('taobao_price');
				$other['market_price'] = $this->input->post('taobao_mkprice');
				$name = uniqid(time() . rand(1000, 99999), false) . $this->extendName($_FILES['taobao_pic']['name']);
				$savepath = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $name;

				if (isset ($_FILES['taobao_pic']['tmp_name']) and $_FILES['taobao_pic']['tmp_name']) {
					if ($this->remote->cp($_FILES['taobao_pic']['tmp_name'], $name, $savepath, array (
							'width' => 600,
							'height' => 800
						), true)) {
						$other['image'] = $savepath;
						$this->remote->del($this->input->post('sourcefile'));
					}
				}
				$this->db->where('id', $id);
				$this->db->update('product_promotion', $other);
				redirect('manage/apple/linksproduct');
			}
			$this->db->where('id', $id);
			$data['res'] = $this->db->get('product_promotion')->result_array();
            $data['id'] = $id;
			$data['message_element'] = "appleEditLink";
			$this->load->view('manage', $data);
		}
	}
	//add produt link
	public function addlink() {
		if ($this->input->post('taobao_title')) {
			$name = uniqid(time() . rand(1000, 99999), false) . $this->extendName($_FILES['taobao_pic']['name']);
			$savepath = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $name;
			if ($this->remote->cp($_FILES['taobao_pic']['tmp_name'], $name, $savepath, array (
					'width' => 600,
					'height' => 800
				), true)) {
				$other['uid'] = $this->uid;
				$other['title'] = $this->input->post('taobao_title');
				$other['url'] = $this->input->post('taobao_web');
				$other['price'] = $this->input->post('taobao_price');
				$other['market_price'] = $this->input->post('taobao_mkprice');
				$other['image'] = $savepath;
				$other['cdate'] = time();
				$this->db->insert('product_promotion', $other);
			}
		}
		$data['message_element'] = "appleAddlink";
		$this->load->view('manage', $data);
	}
	/**
	 * 添加广告
	 */
	public function add() {
		if ($this->input->post('title')) { //增加
			$adPos = $this->input->post("adPos");
			$adPos = implode('$', $adPos);
			$adPos = '$' . $adPos . '$';

			$upload_path = 'banner/';
			$picure = '';

			//没选择图片，并且是处于修改状态,令图片等于老图片地址
			if ($_FILES['picture']['size'] == 0 && $this->input->post('id')) {
				$picure = $this->input->post('oldpicture');
			}elseif ($_FILES['picture']['tmp_name']) {
				$file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
				$upload_rs = $this->remote->upload_qiniu($_FILES['picture']['tmp_name'], $file_name);
				if (empty($upload_rs['key'])) {
					$this->session->set_flashdata('flash_message', $this->common->flash_message('error', $this->upload->display_errors()));
					redirect('manage/apple', 'refresh');
				}
				$picure = $upload_rs['key'];
			}
			
			$spicure = '';
			if ($_FILES['sharepic']['size'] == 0 && $this->input->post('id')) {
				$spicure = $this->input->post('oldsharepic');
			}elseif ($_FILES['sharepic']['tmp_name']) {
				$file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
				$upload_rs = $this->remote->upload_qiniu($_FILES['sharepic']['tmp_name'], $file_name);
				if (empty($upload_rs['key'])) {
					$this->session->set_flashdata('flash_message', $this->common->flash_message('error', $this->upload->display_errors()));
					redirect('manage/apple', 'refresh');
				}
				$spicure = $upload_rs['key'];
			}
			
			$datas = array (
				'author' => $this->uid,
				'email' => $this->input->post('emails'), 
				'spcid' => $this->input->post('bingid'), 
				'tags' => $this->input->post('tags'), 
				'subtype'=>$this->input->post('subtype'),
				'url' => $this->input->post('url'), 
				'picture' => $picure,
				'picture_key' => $picure,
				'sharepic' => $spicure,
				'sharepic_key' => $spicure,
			    'title' => strip_tags($this->input->post('title')),
			    'sur_title' => strip_tags($this->input->post('sur_title')),
				'content' => $this->input->post('content'), 
				'success_content' => $this->input->post('success_content'), 
				'sms' => $this->input->post('sms'), 
				'cdate' => time(), 
				'adPos' => $adPos,
				'area'=>serialize($this->input->post("city")),
				'tehuiid'=>$this->input->post('tehuiid'),
				'flashid'=>$this->input->post('flashid'),
				'order'=>$this->input->post('order'),
				'event_id'=>$this->input->post('event_id')
			);
			
			if (!$this->input->post('id')) { //add
				$aid = $this->common->insertData('apple', $datas);
				if ($this->input->post('opensur')) {

					foreach ($this->input->post('surver') as $i) {
						$indata = array ();
						$indata['banner_id'] = $aid;
						$indata['title'] = $i;
						$indata['type'] = 1;
						$indata['cdate'] = time();
						$this->db->insert('survey', $indata);
					}
				}
				$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加成功！'));
			} else {
				if (!$this->input->post('opensur') and $this->input->post('hasopensur')) {
					$this->db->delete('survey', array (
						'banner_id' => $this->input->post('id'
					)));
				}elseif ($this->input->post('opensur')) {
					$this->db->delete('survey', array (
						'banner_id' => $this->input->post('id'
					)));
					$aa=1;
					foreach ($this->input->post('surver') as $i) {
						
						if ($i) {
							$indata = array ();
							$indata['banner_id'] = $this->input->post('id');
							$indata['title'] = $i;
							$indata['type'] = 1;
							$indata['cdate'] = time();
							
							//foreach ($this->input->post('sort') as $s) {
								$indata['sort'] = $aa;
							//}
							
							$this->db->insert('survey', $indata);
						}
						$aa++;
					}
				}
				
			}
			$this->common->updateTableData('apple', $this->input->post('id'), $conditions = array (), $datas);
			$this->session->set_flashdata('flash_message', $this->common->flash_message('error', '修改成功！'));
			redirect('manage/apple/index', 'refresh');
		}

		$id = $this->uri->segment(4);
		if ($id) {
			$data['row'] = $this->db->query("select * from apple where id = $id ")->row_array();
			if(!empty($data['row']['picture_key']) || $data['row']['picture_key'] != 0){
				$data['row']['hidden_pic'] = $data['row']['picture_key'];
				$data['row']['picture'] = $this->qiniuimgurl.$data['row']['picture_key'];
			}else{
				$data['row']['hidden_pic'] = $data['row']['picture'];
				$data['row']['picture'] = $this->imgurl.$data['row']['picture'];
			}
				
				if(!empty($data['row']['sharepic_key']) || $data['row']['sharepic_key'] != 0 ){
					$data['row']['hidden_sharepic'] = $data['row']['sharepic_key'];
					$data['row']['sharepic'] = $this->qiniuimgurl.$data['row']['sharepic_key'];
				}else{
					$data['row']['hidden_sharepic'] = $data['row']['sharepic'];
					$data['row']['sharepic'] = $this->imgurl.$data['row']['sharepic'];
				}

			$data['survey'] = $this->db->query("select * from survey where banner_id = $id ")->result_array();
		}
		$data['city'] = $this->db->query("select * from city")->result_array();
		$data['adPos'] = $this->adPos;
		
		$data['message_element'] = "appleAdd";
		$this->load->view('manage', $data);
	}
	

	
	//track user feedback
	public function track($param = '') {
		$this->load->library('pager');
		$per_page = 16;
		$start = $page = intval($this->input->get('page'));
		$start == 0 && $start = 1;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->from('survey_log');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['total_rows'] = $this->db->count_all_results();
		$config = array (
			"record_count" => $data['total_rows'],
			"pager_size" => $per_page,
			"show_jump" => true,
			'base_url' => 'manage/apple/track',
			"pager_index" => $page
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		$this->db->limit($per_page, $offset);
		$this->db->order_by("survey_log.id", "desc");
		$this->db->select('survey_log.*, apple.title');
		$this->db->join('apple', 'survey_log.banner_id = apple.id');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['results'] = $this->db->get('survey_log')->result();
		$data['param']= $param;
		$data['message_element'] = "appleTrack";
		$this->load->view('manage', $data);
	}
	
	
	public function trackexcel($param = '') {
		$this->load->library('pager');
		$per_page = 16;
		$start = $page = intval($this->input->get('page'));
		$start == 0 && $start = 1;
		if ($start > 0)
			$offset = ($start -1) * $per_page;
		else
			$offset = $start * $per_page;
		$this->db->from('survey_log');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['total_rows'] = $this->db->count_all_results();
		$config = array (
			"record_count" => $data['total_rows'],
			"pager_size" => $per_page,
			"show_jump" => true,
			'base_url' => 'manage/apple/track',
			"pager_index" => $page
		);
		$this->pager->init($config);
		$data['pagelink'] = $this->pager->builder_pager();
		//$this->db->limit($per_page, $offset);
		$this->db->order_by("survey_log.id", "desc");
		$this->db->select('survey_log.*, apple.title');
		$this->db->join('apple', 'survey_log.banner_id = apple.id');
		if ($param) {
			$this->db->where('banner_id', $param);
		}
		$data['results'] = $this->db->get('survey_log')->result();
		//print_r($data['results']);exit;
		header("Content-Type: application/vnd.ms-execl");  
		header("Content-Disposition: attachment; filename=myExcel.xls");  
		header("Pragma: no-cache");  
		header("Expires: 0");  
		
		echo "ID"."\t";  
		echo "主题"."\t";  
		echo "内容"."\t";  
		echo "时间"."\t";  
		echo "\t\n";
		
		foreach($data['results'] as $exl){
			$string = preg_replace ( "/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $exl->data );
			echo $exl->id."\t";
			echo $exl->title."\t";
			echo str_replace('}}','   ',str_replace('{{','http://pic.meilimei.com.cn/upload/',$string));
			echo "\t";
			echo date('Y-m-d',$exl->cdate)."\t";
			echo "\t\n";
		}
		
		//print_r($data['results']);
	}
	
	
	public function del($id = '') {
		$condition = array (
			'id' => $id
		);
		$this->common->deleteTableData('apple', $condition);
		$this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
		redirect('manage/apple', 'refresh');
	}
	public function dellink($id = '') {
		$condition = array (
			'id' => $id
		);
		$this->common->deleteTableData('product_promotion', $condition);
		$this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
		redirect('manage/apple/linksproduct', 'refresh');
	}
	private function extendName($file_name) {
		$extend = pathinfo($file_name);
		$extend = strtolower($extend["extension"]);
		return '.' . $extend;
	}

}