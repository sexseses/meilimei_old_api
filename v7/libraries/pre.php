<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pre1 {

    const TBL = 'tab2';
    const TBL2 = 'tab1';

    public function __construct(){
        
		$this->event1 = $this->load->database('meilimei', TRUE);
    }

    public function list_pre(){
        $query=$this->event1->get(self::TBL);
        return $query->result_array();
    }

    public function update($id=0){

        if(intval($id) > 0) {
            $num = $this->maxnum($id);
            if ($num > 0) {
                return $this->event1->query("UPDATE ".self::TBL." SET num=num-1,fornum=fornum+1 WHERE id={$id}");
            } else {
                return 0;
            }
        }else{
            return false;
        }

    }

    public function maxnum($id){
        if(intval($id)>0) {
            $this->event1->select('num');
            $this->event1->where('id',$id);
            return $this->event1->get(self::TBL)->row_array();
        }else{
            return 0;
        }
    }

    public function add_pre($data){
        return $this->event1->insert(self::TBL2,$data);
    }

}
?>
