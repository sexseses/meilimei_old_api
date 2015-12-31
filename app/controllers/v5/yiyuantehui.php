<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * Api tehui => 团购 Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
*/
//require_once(__DIR__."/MyController.php");
class yiyuantehui extends CI_Controller {
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
         
        
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        $this->result['state'] = '000';
    }
    
    public function getNewExclusive(){
        $this->db->limit(1,0);
        $rs = $this->db->get('new_exclusive');
        $this->result['data'] = $rs->result_array();
        echo json_encode($this->result); 
    }
    
    public function getyiyuantehuilist(){
       $start = intval($this->input->get('page') - 1) * 10;
       $this->db->limit(10, $start);
       $rs = $this->db->get('ml_one_event');
       $this->result['data'] = $rs->result_array();
       echo json_encode($this->result);
    }
    
    public function getyiyuantehuidetailbyid(){
        $t_id = $this->input->get('t_id');
        $this->db->where('id', $t_id);
        $rs = $this->db->get('ml_one_event');
        $rs = $rs->result_array();
        
        foreach($rs as &$r){
            $tehui_id = $r['tehui_id'];
            $this->tehuiDB->where('team.id', $tehui_id);
            $this->tehuiDB->where('team.group_id', 1);
            $this->tehuiDB->join('partner', 'partner.id=team.partner_id', 'left');
            $this->tehuiDB->select('team.*,partner.comment_good,partner.comment_none,partner.comment_bad,partner.address, partner.longlat,partner.phone as partner_phone,partner.title as partner_name');
            $tmp = $this->tehuiDB->get('team')->result_array();
            
            if (!empty ($tmp)) { 
                $this->result['data'] = $tmp[false];
                if (isset ($this->result['data']['longlat'])) {
                    $this->result['data']['haspartner'] = 1;
                    $this->result['data']['partner_score'] = intval(($this->result['data']['comment_good'] * 5 + $this->result['data']['comment_none'] * 3 + $this->result['data']['comment_bad'] * 1) / ($this->result['data']['comment_good'] + $this->result['data']['comment_none'] + $this->result['data']['comment_bad'] + 0.1));
                    $usercor = explode(',', $this->result['data']['longlat']);
                    $this->result['data']['distance'] = $this->getDistance($this->input->get('Lat'), $this->input->get('Lng'), $usercor[0], $usercor[1]);
                } else {
                    $this->result['data']['haspartner'] = 0;
                }
                
                $this->result['data']['txtDetail'] = mb_substr(strip_tags($this->result['data']['detail']),0,120);
                //$this->result['data']['detail'] = $this->gdetail($this->result['data']['detail'],$this->result['data']['title']);
                $this->result['data']['lastDays'] = $this->result['data']['end_time'] - time();
                
                if ($this->result['data']['lastDays'] > 0) {
                    if ($this->result['data']['lastDays'] > 3600 * 24) {
                        $this->result['data']['lastDays'] = intval($this->result['data']['lastDays']/(3600 * 24)).'天';
                    } else {
                        $this->result['data']['lastDays'] = date('H时i分s秒', $this->result['data']['lastDays']);
                    }
                } else {
                    $this->result['data']['lastDays'] = '过期';
                }
                
                $images = array ();
                if ($this->result['data']['image'] != '') {
                    $images[] = $this->result['data']['image'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image'];
                }
                if ($this->result['data']['image1'] != '') {
                    $images[] = $this->result['data']['image1'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image1'];
                }
                if ($this->result['data']['image2'] != '') {
                    $images[] = $this->result['data']['image2'] = 'http://tehui.meilimei.com/static/' . $this->result['data']['image2'];
                }
                $this->result['data']['images'] = $images;
                
                $this->result['data']['expire_time'] = date('Y-m-d', $this->result['data']['expire_time']);
                $this->result['data']['notice'] = '<div style="font-size:12px"><b>有效期:</b><br>' . $this->result['data']['expire_time'] . '<br>' . $this->result['data']['notice'].'</div>';
                
                $this->tehuiDB->where('team_id', $tehui_id);
                $this->tehuiDB->where('comment_time > ', 0);
                $this->tehuiDB->from('order');
                
                $this->result['data']['teamScoreNum'] = $this->tehuiDB->count_all_results();
                $this->result['data']['teamScore'] = 0;
                
                if ($this->result['data']['teamScoreNum']) {
                    $sql = "SELECT sum(case when `comment_grade` = 'good' Then 5 when `comment_grade` = 'none' then 3 else 1 end ) as v FROM `order` WHERE `team_id` = {$tehui_id} and `comment_time` >0";
                    $tmp = $this->tehuiDB->query($sql)->result_array();
            
                    $this->result['data']['teamScore'] = round($tmp[0]['v'] / $this->result['data']['teamScoreNum'], 1);
                }
            
                $this->result['data']['buynums'] = $this->result['data']['now_number'];
            
            }
        }
        
        $this->result['tehui'] = $rs[false];
        echo json_encode($this->result);
    }
    
    
    
    private function gdetail($content,$title){
        return ' <style>
                .mainc{
                font-size:16px;
                line-height:180%;max-width:600px;
                padding:10px;color:#333;margin:auto;
                }
                 .mainc img{
                max-width:350px;
                }
                .mainc img { width:100%; }
                .wapper_form{ width:95%; margin:0 auto;  }  </style>
                <div id="content" class="mainc">'.$content.'</div> ';
    }
    
    
    private function getDistance($lat1, $lng1, $lat2, $lng2) {
    
        $EARTH_RADIUS = 6378.137;
        $radLat1 = $this->rad($lat1);
        $radLat2 = $this->rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = $this->rad($lng1) - $this->rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $EARTH_RADIUS;
        $s = round($s * 10000);
        return $s;
    }
    
    private function rad($d) {
        return $d * 3.1415926535898 / 180.0;
    }
}