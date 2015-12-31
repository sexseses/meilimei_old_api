<?php
class searchJigou extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('yisheng');
		$this->load->model('Gallery');
		$this->path = realpath(APPPATH . '../images');
	}
	public function index($param = '') {
		$data['notlogin'] = $this->notlogin;
        $flink = site_url('searchJigou').'?';
		$city = addslashes($this->input->get('city'));
		if($city){
			$data['itemlink'] = site_url('searchJigou').'?city='.$city.'&item=';
			$flink .= 'city='.$city;
			$data['city'] = $city;
		}else{
			$data['itemlink'] = site_url('searchJigou').'?item=';
			$data['city'] = '不限';
		}

		if($this->input->get('item')){
			$data['citylink'] = site_url('searchJigou').'?item='.$this->input->get('item').'&city=';
			$flink .= '&item='.$this->input->get('item');
			$data['item'] = $this->yisheng->search($this->input->get('item'));
		}else{
			$data['citylink'] = site_url('searchJigou').'?city=';
            $data['item'] = '不限';
		}
        $data['WEN_PAGE_TITLE'] = '找'.$this->input->get('city').'整形医院';
        $data['result'] = $this->search(3,$city,$this->input->get('item'),'','',$this->input->get('per_page'),$flink);
		$data['message_element'] = "searchJigou";
		$this->load->view('template', $data);
	}

	private function search($utype='',$city='',$department='',$company='',$keys='',$page=1,$url='') {
		    $result['state'] = '000';
		    $this->load->library('pagination');
            $config['base_url'] = $url;
            $config['per_page'] = 10;
            $config['enable_query_strings'] = true;
            $config['page_query_string'] = true;
            $config['first_link'] = "第一页";
            $config['last_link'] = "末页";
			$sql = "SELECT users.id as user_id,users.tconsult,users.systconsult,users.replys,users.sysreplys,users.alias as username,users.created,users.verify,users.suggested,users.voteNum,users.grade,users.sysgrade,users.sysvotenum,users.suggested,company.province,company.address,company.tel,company.contactN,company.name";

			$csql = ' FROM users LEFT JOIN company ';
			$csql .= ' ON company.userid = users.id  WHERE ';

			if ($utype) {
				$csql .= ' users.role_id = ' . $utype . ' AND ';
			}

			if ($city) {
				$csql .= " company.city = '" . $city . "' AND ";
			}
			if ($department) {
				$csql .= " company.department LIKE '%," . $department . ",%' AND ";
			}
			if ($company) {
				$csql .= " company.name = '" . $company . "' AND ";
			}
			if (strstr($csql, 'AND')) {
				$csql = substr($csql, 0, strlen($csql) - 4);
			} else {
				$csql = substr($csql, 0, strlen($csql) - 7);
			}

			if ($keys) {
				$csql .= " AND (company.name LIKE '%" . $keys . "%' OR ";
				$csql .= " users.alias LIKE '%" . $keys . "%' OR ";
				$csql .= " users.phone LIKE '%" . $keys . "%')";

			}
			$csql .= " AND users.banned = 0";
			$csql .= ' ORDER BY company.userid DESC';
			//users.rank_search DESC, users.grade DESC,
			$config['total_rows'] = $result['total_rows'] = $this->db->query("SELECT users.id".$csql)->num_rows();

			if ($page) {
				$csql .= " LIMIT $page,10 ";
			} else {
				$csql .= " LIMIT 0,10 ";
			}
			$tmp = $this->db->query($sql.$csql)->result_array();
			$result['data'] = array();

			if (!empty ($tmp)) {
				foreach ($tmp as $row) {
					$row['thumbUrl'] = 'http://pic.meilimei.com.cn/thumb/'.$row['user_id'].'_90'; 

					$row['tconsult'] = $row['systconsult'] > 0 ? $row['systconsult'] : $row['tconsult'];
					$row['replys'] = $row['sysreplys'] > 0 ? $row['sysreplys'] : $row['replys'];
					$row['voteNum'] = $row['sysvotenum'] > 0 ? $row['sysvotenum'] : $row['voteNum'];
					$row['grade'] = $row['sysgrade'] > 0 ? $row['sysgrade'] : $row['grade'];
					unset ($row['sysvotenum']);
					unset ($row['sysgrade']);
					unset ($row['systconsult']);
					;
					unset ($row['sysreplys']);

					$row['created'] = date('Y-m-d', $row['created']);
					$result['data'][] = $row;
				}
			}
			$this->pagination->initialize($config);
            $result['pagelink'] = $this->pagination->create_links();

			return $result;

	}

}
?>
