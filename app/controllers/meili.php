<?php
class meili extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		$this->load->helper('form');
		if ($this->wen_auth->is_logged_in()) {
			$this->uid = $this->wen_auth->get_user_id();
			$this->load->helper('file');
			$this->notlogin = false;
		} else {
			$this->notlogin = true;
		}
	}
	public function index($param = '') {
	//	$this->output->cache(3600);
        $title = $this->input->post('qtitle');
        $this->load->library('filter');$this->filter->filts($title,true);
		if ($title) {
			if ($this->uid) {
				$data['fUid'] = $this->uid;
				$data['title'] = $title;
				//$data['position'] = $this->input->post('position');
				$data['description'] = $this->input->post('qdes'); $this->filter->filts($data['description'],true);
				//$data['sex'] = $this->input->post('sex');
				//$data['address'] = $this->input->post('address');
				//	$data['city'] = $this->input->post('city');
				//$data['toUid'] = "";
				$data['state'] = 1;$data['has_answer'] = 0;
				$data['cdate'] = time();
				$id = $this->common->insertData('wen_questions', $data);
				if (isset ($_FILES['attachPic']['tmp_name']) && $_FILES['attachPic']['tmp_name'] && $id != 0) {

				$target_path = realpath(APPPATH . '../upload');
//var_dump($target_path);die;
				if (is_writable($target_path)) {
					if (!is_dir($target_path . '/' . round($id / 1000))) {
						mkdir($target_path . '/' . round($id / 1000), 0777, true);
					}

					$datas['name'] = time() . '.jpg';
					$datas['savepath'] = round($id / 1000) . '/' . $datas['name'];
					$target_path = $target_path . '/' . $datas['savepath'];
					move_uploaded_file($_FILES['attachPic']['tmp_name'], $target_path);
					GenerateThumbFile($target_path, $target_path, 550, 650);
					$datas['userId'] = $this->uid;
					$datas['uploadTime'] = time();
					$datas['type'] = 'jpg';
					$datas['private'] = 1;
					$pictureid = $this->common->insertData('wen_attach', $datas);
					$result['updatePictureState'] = '000';
					$result['postState'] = '000';

					$upicArr = array ();
					$upicArr[]['type'] = 'jpg';
					$upicArr[]['id'] = $pictureid;
					$wdata['uid'] = $this->uid;
					$wdata['content'] = '';
					$wdata['q_id'] = $id;
					$wdata['type_data'] = serialize($upicArr);
					$wdata['type'] = 4;
					$wdata['ctime'] = time();
					$this->common->insertData('wen_weibo', $wdata);
				}
			}
				$this->session->set_flashdata('msg', $this->common->flash_message('sucess', '咨询已经发布！'));
				redirect('user/dashboard');
			} else {
				$this->session->set_flashdata('msg', $this->common->flash_message('error', '登入后才能发布咨询！'));
				redirect('user/login');
			}

		} else {
			$this->db->where('wen_questions.state', 1);
			$this->db->where('wen_answer.uid != ', "");
			$this->db->where('users.banned',0);
			$this->db->select('wen_questions.title,wen_questions.id,wen_answer.uid,users.alias,users.alias,wen_answer.cdate,wen_questions.cdate,user_profile.company');
			$this->db->from('wen_questions');
			$this->db->join('wen_answer', 'wen_answer.qid = wen_questions.id', 'left');
			$this->db->join('users', 'wen_answer.uid = users.id', 'left');
			$this->db->join('user_profile', 'wen_answer.uid = user_profile.user_id', 'left');
			$this->db->order_by("wen_questions.id", "desc");
			$this->db->limit(5);
         //   Memcached::add('home','test');

           	//  $data['results'] = $this->db->get()->result();
            if(!$this->uid){
            	foreach ($_COOKIE as $c_id => $c_value)
               {
                  setcookie($c_id, NULL, 1, "/", ".meilimei.name");
                }
            }

			$data['notlogin'] = $this->notlogin;
			$this->load->view('meili', $data);
		}


		
	}

	public function nocap($param = '') {
		    $data['notlogin'] = $this->notlogin;
			$this->load->view('mlmnocap',$data);
	}
}	
?>
