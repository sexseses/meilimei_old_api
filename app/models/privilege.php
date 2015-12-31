<?php
/*26490
 * WENRAN privilege manage
 */
class privilege extends CI_Model{
	public $privilege,$table = 'privilege',$uid,$special = array('0','6082','6105');
	public function __construct()
	{
		parent::__construct();
	}
	public function init($uid=''){
	   $this->uid = $uid;
       $this->db->where('uid', $this->uid);
       $tmp = $this->db->get($this->table)->result_array();
       
       foreach($tmp as $r){
          $this->privilege[$r['funs']] = true;
       }
	}
	//judege whether have privilege
	function judge($str) {
		if(array_search($this->uid,$this->special)){
           return true;
		}else{
			return isset($this->privilege[$str]);
		}
	}
	//get block privilege
	function getPri($str){
	   $this->db->where('uid', $this->uid);
	   $this->db->where('type', $str);
       return $this->db->get('pfunction')->result_array();
	}
	//get block privilege
	function setPri($str,$data){
	   $this->db->where('uid', $this->uid);
	   $this->db->where('type', $str);
       $tmp = $this->db->get('pfunction')->result_array();
       if(!empty($tmp)){
          $this->db->where('uid', $this->uid);
	      $this->db->where('type', $str);
          $this->db->update('pfunction', $data);
       }else{
         $this->db->insert('pfunction', $data);
       }
	}
}
?>
