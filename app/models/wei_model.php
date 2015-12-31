<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class wei_model extends CI_Model{

    const TBL = 'we';

    public function __construct(){
        parent::__construct();
    }

    public function add_wei($data){
        return $this->db->insert(self::TBL,$data);
    }

    public function update(){
        $data = array(
            'score' => $_GET['score'],
            'time' => time()
        );
        $this->db->where('openid',$_GET['openid']);
        return $this->db->update(self::TBL,$data);
    }

    public function select($openid){
        $this->db->where('openid',$openid);
        return $this->db->get(self::TBL)->row();
    }

    public function count(){
        return $this->db->get(self::TBL)->num_rows();
    }

    public function ran($id){

        $num = $this->db->query("SELECT e.place,d.weName,d.score FROM (select a.id,count(b.id)+1 as place from we a left join we b on a.score < b.score group by a.id) AS e INNER JOIN we AS d ON e.id = d.id where openid='{$id}' ORDER BY e.place ASC")->result_array();
	
        $ran = $num[0]['place'];
        return $ran;
    }

    public function sel($openid){
        $this->db->where('openid',$openid);
        return $this->db->get(self::TBL)->result_array();
    }
    public function sel1($openid1){
        $this->db->where('openid',$openid1);
        return $this->db->get(self::TBL)->result_array();
    }
}


?>