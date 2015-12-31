<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pre_model extends CI_Model{

    const TBL = 'tab2';
    const TBL2 = 'tab1';

    public function __construct(){
        parent::__construct();
    }

    public function list_pre(){
        $query=$this->db->get(self::TBL);
        return $query->result_array();
    }

    public function update($id=0){

        if(intval($id) > 0) {
            $num = $this->maxnum($id);
            if ($num > 0) {
                return $this->db->query("UPDATE ".self::TBL." SET num=num-1,fornum=fornum+1 WHERE id={$id}");
            } else {
                return 0;
            }
        }else{
            return false;
        }

    }

    public function maxnum($id){
        if(intval($id)>0) {
            $this->db->select('num');
            $this->db->where('id',$id);
            return $this->db->get(self::TBL)->row_array();
        }else{
            return 0;
        }
    }

    public function add_pre($data){
        return $this->db->insert(self::TBL2,$data);
    }


}
?>
