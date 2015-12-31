<?php
class getdata extends CI_Controller {
	private $notlogin = true;
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->get_role_id() == 16) {
			$this->notlogin = false;
		} else {
			redirect('');
		}
		$this->load->model('privilege');
	}
	public function yiyuan() {
       if( $province = $this->input->get('province') ){
       	$city = $this->input->get('city');
       	$tmp = $this->GCOP($province,$city);
       	foreach($tmp as $r){
            echo '<option value="'.$r['userid'].'">'.$r['name'].'</option>';
       	}
       }
	}
	private function GCOP($province,$city){
       $this->db->where('state', 1);
       if($province!='国外'){
       	if($city){
       	   $this->db->where('city', $city);
       	   $this->db->where('province', $province);
       }else{
       	  $this->db->where('city', $province);
       }
       }else{
       	 $this->db->where('country', $province);
       }

       $this->db->select('userid, name,city, id');
       $this->db->from('company');
      return   $this->db->get()->result_array();
	}
}
?>
