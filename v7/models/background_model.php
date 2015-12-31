<?php

class Background_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}

    /**
     * @param type 1 为普通用户，２为医生，机构
     */
    public function getBackgroundList($type = 1){

        $this->db->where('type', $type);
        return $this->db->get('backimg')->result_array();
    }

    /**
     * @param  int typeid 如果type=1 #uid 如果是２的话是如果是医生的＃uid 如果是机构传ｃｏｍｐａｎｙｉｄ
     * @param type 1 为普通用户，２为医生，机构
     */
    public function addUserBackground($typeid = 0, $type = 1, $imgid){

        if(intval($typeid) < 0){
            return;
        }

        if(intval($imgid) < 0){
            return;
        }

        $img = $this->getBackgroundImg($imgid);
        $imgurl = isset($img[0]['imgurl']) ? $img[0]['imgurl'] : '';

        return $this->db->insert('background',array('typeid'=>$typeid, 'type'=>$type, 'imgurl'=>$imgurl));
    }

    /**
     * @param  int typeid 如果type=1 #uid 如果是２的话是如果是医生的＃uid 如果是机构传ｃｏｍｐａｎｙｉｄ
     * @param type 1 为普通用户，２为医生，机构
     */
    public function updateUserBackground($typeid, $imgid, $type){
        if(intval($typeid) < 0){
            return;
        }

        if(intval($imgid) < 0){
            return;
        }
        $img = $this->getBackgroundImg($imgid);
        $imgurl = isset($img[0]['imgurl']) ? $img[0]['imgurl'] : '';
        $this->db->where('typeid',$typeid);
        $this->db->where('type',$type);
        return $this->db->update('background', array('imgurl'=>$imgurl));
    }

    /**
     * @param  int uid 如果type=1 #uid 如果是２的话是如果是医生的＃uid 如果是机构传ｃｏｍｐａｎｙｉｄ
     * @param type 1 为普通用户，２为医生，机构
     */
    public function getUserBackground($typeid = 0, $type = 1){

        if(intval($typeid) < 0){
            return ;
        }

        $this->db->where('typeid', $typeid);
        $this->db->where('type', $type);
        $rs = $this->db->get('background')->result_array();
        return isset($rs[0]['imgurl']) ? $rs[0]['imgurl'] : '';
    }

    /**
     * @param int $imgid
     */
    public function getBackgroundImg($imgid = 0){

        if(intval($imgid) < 0){
            return;
        }

        $this->db->where('id',$imgid);
        return $this->db->get('backimg')->result_array();
    }
}
?>