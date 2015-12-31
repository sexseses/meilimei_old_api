<?php
class importCoordinate extends CI_Controller {
	private $notlogin = true;
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		}else{
			redirect('');
		}
		$this->load->model('privilege');
	}
	public function index() {
        set_time_limit(0);
        $company = $this->db->query("select * from company")->result_array();

        foreach($company as $k => $v){
            $url = "http://api.map.baidu.com/place/v2/search?&q=".$v['address']."&region=".$v['city']."&output=json&ak=49f2ad787ba03fe0e52b94806b107ae3";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $json = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($json,true);
            $userid= $v['userid'];
            $lat=$json['results'][0]['location']['lat'];
            $lng=$json['results'][0]['location']['lng'];
            @$this->db->query("insert ignore into map values('$userid','$lat','$lng')");
        }


		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "importCoordinate";
		$this->load->view('manage', $data);
	}
}
?>
