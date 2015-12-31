<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
 
 
class Tehuibanner extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(0);
		//ini_set("display_errors","On");
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
			$this->uid=$this->wen_auth->get_user_id();
		} else {
			redirect('');
		}
		$this->path = realpath(APPPATH . '../images');
		$this->load->helper('file');
		$this->load->model('privilege');
		$this->load->model('user_visit');
		$this->privilege->init($this->uid);
		$this->tehuiDB = $this->load->database('tehui', TRUE);
		$this->load->model('remote');
       if(!$this->privilege->judge('users')){
          die('Not Allow');
       }
       
	}

	public function index() {
		
		$sname = $this->input->post('sname');
		$sql = "select * from category where zone = 'city' ";
		$data['results3'] = $this->tehuiDB->query($sql)->result_array();
		
		if($sname == null){
			$this->db->select('*');
			$this->db->from('tehui_index_banner');
		    $data['results'] = $this->db->get()->result_array();
		    $this->db->select('*');
		    $this->db->from('tehui_top_banner');
		    $this->db->where('display','1');
		    $data['results2'] = $this->db->get()->result_array();
		    $data['message_element'] = "tehuibanner";
		    $this->load->view('manage', $data);
		}else{
			
			$sql = "select * from tehui_top_banner where display='1' and dizhiid regexp '$sname'";
			$data['results2'] = $this->db->query($sql)->result_array();
			$data['message_element'] = "tehuibanner";
			$this->load->view('manage', $data);
		
		}
	}
	public function addimg(){
	    if($_GET['id']){
	        $this->db->select('*');
	        $this->db->from('tehui_index_banner');
	        $this->db->where('id',$_GET['id']);
	        $rs = $this->db->get()->result_array();
	        $data = $rs['0'];
	    }else{
	    	
	    }
	    $data['message_element'] = "addimg";
	    $this->load->view('manage', $data);
	}
	/** update usre detail info
	 * @param string $id
	 *
	 */
	public function update() {
	    $url = $this->input->post('url');
	    $type = $this->input->post('type');
	    $arr = array(
	            'url' => $url,
	            'type' => $type
	    );
	    if ($_FILES['file']['tmp_name']) {
	        $upload_path = 'banner/'.date('Y').'/'.date('m').'/';
	        $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
	        $banner_pic = $upload_path.$file_name;
	        if (!$this->remote->cp($_FILES['file']['tmp_name'], $file_name, $banner_pic,array (), true)) {
	            $this->session->set_flashdata('flash_message',
	                    $this->common->flash_message('error', $this->upload->display_errors())
	            );
	        }else{
	            $arr['banner_pic'] = $banner_pic;
	        }
	    }
	    $sql = 'select * from tehui_index_banner where type = '.$type;
		$rs = $this->db->query($sql)->result_array();
		if(count($rs)>0){
		    $this->db->where('type',$type);
		    $this->db->update('tehui_index_banner',$arr);
		}else{
		    $this->db->insert('tehui_index_banner',$arr);
		}
	    redirect('manage/tehuibanner/index');
	}
	
	//添加banner
	public function topadd() {
		$sql = "select DISTINCT czone from category where zone = 'city'";
		$data['results'] = $this->tehuiDB->query($sql)->result_array();
		$data['message_element'] = "tehuibannertopadd";
		$this->load->view('manage', $data);
	}
	
	//添加banner的方法
	public function topinset(){		
		$title = $this->input->post('title'); //标题
		$conn = $this->input->post('conn'); //内容
		$weigt = $this->input->post('weigt'); // 权重
		$link = $this->input->post('link'); // 链接
		$weizhi = $this->input->post('weizhi');   //  显示位置
		$showtype = $this->input->post('showtype');  //闪或特位置显示
		$content = $this->input->post('content');  //内容
		$trme = $this->input->post('trme');  //特惠
		$meirenji = $this->input->post('meirenji');  //美人记
		$tiezi = $this->input->post('tiezi'); //社区帖子
		$shangou = $this->input->post('shangou');  //闪购
		$upload_path = 'banner/'.date('Y').'/'.date('m').'/';
		$banner_pic = '';
		$dizhi = $this->input->post('dizhi');  //地址
		$falsh = in_array("falsh",$weizhi)?"1":"2";
		$tehui = in_array("tehui",$weizhi)?"1":"2";
		$ios = in_array("ios",$showtype)?"1":"2";
		$android = in_array("android",$showtype)?"1":"2";
		if(empty($weizhi)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','位置没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		if(empty($showtype)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','显示没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		if(empty($dizhi)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','地址没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		
		if ($_FILES['lbanner']['tmp_name']) {
                $file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
                if (!$this->remote->cp($_FILES['lbanner']['tmp_name'], $file_name, $upload_path . $file_name)) {
                    $this->session->set_flashdata('flash_message',$this->common->flash_message('error', '上传失败！'));
                    //redirect('manage/flashSale/edit_banner/$id', 'refresh');
                }
		        $banner_pic = $upload_path . $file_name;
		        if( (!empty($link) && !empty($trme) && !empty($meirenji) && !empty($tiezi) && !empty($shangou)) || (empty($link) && empty($trme)) && empty($meirenji) && empty($tiezi) && empty($shangou)){
		        	$this->session->set_flashdata('flash_message',$this->common->flash_message('error','不可同时填写多项！填写出错！'));
		        	//redirect('manage/tehuibanner/topadd', 'refresh');
		        	}
		        	
			   $bannerarr = array(
			   			'prodid' => intval($trme),
			   			'btitle' => $title,
			   			'bcontent' => $conn,
			   			'burl' => $link,
			   			'bimg' => $banner_pic,
			   			'falsh_shop' => $falsh,
			   			'tehui' => $tehui,
			   			'iossystem' => $ios,
			   			'androidsystem' =>$android,
			   			'bweights' => $weigt,
			   			'stday' => time(),
			   			'counton' => $content,
			   			'dizhiid' => serialize($dizhi),
			   			'meirenji' => intval($meirenji),
			   			'teizi' => intval($tiezi),
			   			'shangou' => intval($shangou)	   			
			   		);
			   $this->db->insert('tehui_top_banner',$bannerarr);
			   redirect('manage/tehuibanner', 'refresh');
			   return;
				}else{
					$this->session->set_flashdata('flash_message',$this->common->flash_message('error','图片为上传！'));
					redirect('manage/tehuibanner/topadd', 'refresh');
			}
	}
	
	public function topedit(){
		if($_GET['id']){
			$this->db->select('*');
			$this->db->from('tehui_top_banner');
			$this->db->where('id',$_GET['id']);
			$data['results'] = $this->db->get()->row_array();
			$data['didizhi'] = unserialize($data['results']['dizhiid']);
			$sql = "select DISTINCT czone from category where zone = 'city' ";
			$data['results3'] = $this->tehuiDB->query($sql)->result_array();
		}
		$data['message_element'] = "tehuibanneredit";
		$this->load->view('manage', $data);	
	}
	
	public function topupdate(){
		$uid = $this->input->post('uid');
		$title = $this->input->post('title');
		$conn = $this->input->post('conn');
		$weigt = $this->input->post('weigt');
		$link = $this->input->post('link');
		$weizhi = $this->input->post('weizhi');
		$content = $this->input->post('content');
		$showtype = $this->input->post('showtype');
		$trme = $this->input->post('trme');
		$meirenji = $this->input->post('meirenji');  //美人记
		$tiezi = $this->input->post('tiezi'); //社区帖子
		$shangou = $this->input->post('shangou');  //闪购
		$oldimg = $this->input->post('oldimg');
		$upload_path = 'banner/'.date('Y').'/'.date('m').'/';
		$banner_pic = '';
		$dizhi = $this->input->post('dizhi');
		$falsh = in_array("falsh",$weizhi)?"1":"2";
		$tehui = in_array("tehui",$weizhi)?"1":"2";
		$ios = in_array("ios",$showtype)?"1":"2";
		$android = in_array("android",$showtype)?"1":"2";
		if(empty($weizhi)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','位置没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		if(empty($showtype)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','显示没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		if(empty($dizhi)){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','地址没有选择！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		if ($_FILES['lbanner']['tmp_name']) {
			$file_name = uniqid(time() . rand(1000, 9999), false) . '.jpg';
			if (!$this->remote->cp($_FILES['lbanner']['tmp_name'], $file_name, $upload_path . $file_name)) {
				$this->session->set_flashdata('flash_message',$this->common->flash_message('error', '上传失败！'));
				//redirect('manage/flashSale/edit_banner/$id', 'refresh');
			}
			$banner_pic = $upload_path . $file_name;
			if( (!empty($link) && !empty($trme) && !empty($meirenji) && !empty($tiezi) && !empty($shangou)) || (empty($link) && empty($trme)) && empty($meirenji) && empty($tiezi) && empty($shangou)){
				$this->session->set_flashdata('flash_message',$this->common->flash_message('error','不可同时填写多项！填写出错！'));
				redirect('manage/tehuibanner/topadd', 'refresh');
			}
			$bannerarr = array(
					'prodid' => $trme,
					'btitle' => $title,
					'bcontent' => $conn,
					'burl' => $link,
					'falsh_shop' => $falsh,
					'tehui' => $tehui,
					'iossystem' => $ios,
					'androidsystem' =>$android,
					'bimg' => $banner_pic,
					'bweights' => $weigt,
					'counton' => $content,
					'stday' => time(),
					'dizhiid' => serialize($dizhi),
					'meirenji' => $meirenji,
					'teizi' => $tiezi,
					'shangou' => $shangou
			);
			$this->db->where('id',$uid);
			$this->db->update('tehui_top_banner',$bannerarr);
			redirect('manage/tehuibanner', 'refresh');
			return true;
		}else{		
			//$this->session->set_flashdata('flash_message',$this->common->flash_message('error','图片为上传！'));
			//redirect('manage/tehuibanner/topadd', 'refresh');
		
		if( (!empty($link) && !empty($trme)) && (empty($link) && empty($trme)) ){
			$this->session->set_flashdata('flash_message',$this->common->flash_message('error','链接或产品填写出错！'));
			redirect('manage/tehuibanner/topadd', 'refresh');
		}
		$bannerarr = array(
				'prodid' => $trme,
				'btitle' => $title,
				'bcontent' => $conn,
				'burl' => $link,
				'falsh_shop' => $falsh,
				'tehui' => $tehui,
				'iossystem' => $ios,
				'androidsystem' =>$android,
				'bimg' => $oldimg,
				'bweights' => $weigt,
				'counton' => $content,
				'stday' => time(),
				'dizhiid' => serialize($dizhi),
				'meirenji' => $meirenji,
				'teizi' => $tiezi,
				'shangou' => $shangou
		);
		$this->db->where('id',$uid);
		$this->db->update('tehui_top_banner',$bannerarr);
		redirect('manage/tehuibanner', 'refresh');
		}
	}
	
	public function topdel(){
		if($_GET['id']){
			$arr = array(
				'display' => '2'
			);
			$this->db->where('id',$_GET['id']);
			$this->db->update('tehui_top_banner',$arr);
			redirect('manage/tehuibanner', 'refresh');
		}
	}
}
?>