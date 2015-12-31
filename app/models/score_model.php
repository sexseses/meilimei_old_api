<?php

class Score_model extends CI_Model
{
    public function Score_model()
    {
        parent::__construct();
    }

    private function getScore($id=0){

        if(intval($id) < 0)
            return;

        $this->db->where('sid',$id);
        $rs = $this->db->get('score')->result_array();

        if(empty($rs))
            return;
        return $rs[0];
    }

    public function addScore($id, $uid){

        if(intval($id) < 0)
            return;

        $score = $this->getScore($id);
        $this->addLogger($uid, $score['name'], $score['score']);
        $this->db->where('id',$uid);
        $t = $this->db->get('users')->result_array();
        if(intval($t[0]['daren']) == 2){
            $jifen = intval($t[0]['jifen']) + (intval($score['score']) *2);
        }else{
            $jifen = intval($t[0]['jifen']) +intval($score['score']);
        }

        $this->db->where('id',$uid);
        $isUpdateScore = $this->db->update('users',array('jifen'=>$jifen));
        return $score['score'];
        /*if($isUpdateScore){
            return $score['score'];
        }else{
            return;
        }*/
    }

    /**
     * @param int $uid
     */
    public function getScoreList($uid=0){

        if(intval($uid) < 0)
            return;

        $this->db->where('uid',$uid);
        $this->db->order_by('id','desc');
        return $this->db->get('score_logger')->result_array();
        return $this->db->last_query();
    }
    public function addLogger($uid, $name, $score){

        $this->db->insert('score_logger', array('uid'=>$uid, 'desc'=>$name, 'score'=>$score, 'created_at'=>time()));
        //return $this->db->insert_id();
    }
}

?>