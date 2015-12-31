<?php
class banner extends CI_Controller {
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
		$this->load->model('remote');
	}
	public function index() {
		if ($tag = $this->input->get('param')) {
			$data = array ();
			$this->db->like('tags', $tag, 'both');
			$this->db->order_by("id", "desc");
			$this->db->limit(10);
			$data['infos'] = $this->db->get('banner')->result_array();
			$this->load->view('theme/include/articles', $data);

		}
	}
	//get from apple banner
	public function mobile($param = '') {

        $head = strtolower($_SERVER['HTTP_USER_AGENT']);

        if(strpos($head, 'android') !== false && intval($param) == 255){
            //header('HTTP/1.1 301 Moved Permanently');
            header("Location:http://m.meilimei.com/zt/show_200/");
            exit();
        }


        ///

		if ($id = intval($param)) {
			$data = array ();
			$this->db->where('id', $id);
			$this->db->order_by("id", "desc");
			$this->db->limit(1);
			$data['infos'] = $this->db->get('apple')->result_array();
			$this->db->where('banner_id', $id);
			$this->db->order_by("id", "desc");
			$data['survey'] = $this->db->get('survey')->result_array();

            $this->load->view('theme/include/mobile_banner', $data);


		}

	}
    //get from apple banner
    public function test($param = '') {
        echo strtolower($_SERVER['HTTP_USER_AGENT']);

        if(intval($param) == 255 && strtolower($_SERVER['HTTP_USER_AGENT']) == 'android'){
            header("location:http://m.meilimei.com/zt/show_200/");
        }
        if ($id = intval($param)) {
            $data = array ();
            $this->db->where('id', $id);
            $this->db->order_by("id", "desc");
            $this->db->limit(1);
            $data['infos'] = $this->db->get('apple')->result_array();
            $this->db->where('banner_id', $id);
            $this->db->order_by("id", "desc");
            $data['survey'] = $this->db->get('survey')->result_array();
            $this->load->view('theme/include/mobile_banner', $data);
        }

    }
	public function bannerimg(){
		$error = "";
		$msg = "";
		$fileElementName = $_POST['name'];
		$uptypes=array('image/jpg','image/jpeg','image/png','image/pjpeg','image/gif','image/bmp','image/x-png'); 
	
		
		if(!empty($_FILES[$fileElementName]['error']))
		{
			$error = '这张图来自外星嘛？换张试试哦！';
		}elseif(!in_array($_FILES[$fileElementName]['type'],$uptypes)){
			$error = '这张图来自外星嘛？换张试试哦！';
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
		{
			$error = '这张图来自外星嘛？换张试试哦！';
		}else 
		{
			$name = uniqid(time() . rand(1000, 99999), false) . $this->extendName($_FILES[$fileElementName]['name']);
			$savepath = date('Y') . '/' . date('m') . '/' . date('d') . '/' . $name;
			
			if ($this->remote->cp($_FILES[$fileElementName]['tmp_name'], $name, $savepath, array (
					'width' => 600,
					'height' => 800
				), true)) {
				$msg .= $savepath;
			}else{
				$error = '图片上传失败，请再试一次';
			}
				@unlink($_FILES[$fileElementName]);		
		}		
		echo "{";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "'\n";
		echo "}";
	}
	
	
	//gather info from survey
	public function survey($param='') {
		if ($this->input->post('survey')) {
			$this->db->where('id', $param);
			$this->db->order_by("id", "desc");
			$this->db->limit(1);
			$infos = $this->db->get('apple')->result_array();
			//get data
			$str = '<ul>';
			$survey_name = $this->input->post('survey_name');
			$vals  = $this->input->post('survey');
			$phone = '';
			foreach ($vals as $k => $v) {
				$str .= '<li>' . $survey_name[$k] . ': ' . $v . '</li>';
				if($survey_name[$k]=='手机'){
					$phone = trim($v);
				}
			}
			$str .= '</ul>';
			if($infos[0]['sms'] and $phone and preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone)){
               $this->load->library('sms');
               $this->sms->sendSMS(array(
                            "{$phone}"
                        ), $infos[0]['sms']);
			}

			$indata['data'] = $str;
			$indata['banner_id'] = $this->input->post('banner_id');
			$indata['cdate'] = time();
			$this->db->insert('survey_log', $indata);
			$emails = explode(',', $this->input->post('emails'));
			$str  = $infos[0]['title'].$str;

			$config = array(
				'protocol' => 'smtp',
				'smtp_host'=> 'smtp.163.com',
				'smtp_port' =>'25',
				'smtp_user' =>'yyyyy210@163.com',
				'smtp_pass' =>'wjj2flh',
				'newline'   =>'\r\n'
				);
			$this->load->library('email',$config);
			//$this->email->from('system@meilimei.com');

			$this->email->to($emails);
			$this->email->from('yyyyy210@163.com');
			$this->email->subject('美丽美信息反馈');
			$this->email->message($str);
			$this->email->send();
			redirect('banner/mobile/'.$param.'?state=success');
		}
	}
	public function send(){

			$config = array(
				'protocol' => 'smtp',
				'smtp_host'=> 'smtp.163.com',
				'smtp_port' =>'25',
				'smtp_user' =>'yyyyy210@163.com',
				'smtp_pass' =>'wjj2flh',
				'newline'   =>'\r\n'
				);
			$this->load->library('email',$config);
			//$this->email->from('system@meilimei.com');

			$this->email->to(array('ken@rolaner.com'));

			$this->email->from('yyyyy210@163.com');
			$this->email->subject('美丽美信息反馈');
			$this->email->message("美丽美信息反馈美丽美信息反馈美丽美信息反馈美丽美信息反馈");

			var_dump($this->email->send());

	}
	private function extendName($file_name) {
		$extend = pathinfo($file_name);
		$extend = strtolower($extend["extension"]);
		return '.' . $extend;
	}
}
?>
