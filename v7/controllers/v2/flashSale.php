<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * Api flashSale => 闪购 Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
*/
require_once(__DIR__."/MyController.php");

class flashSale extends MY_Controller {
    private $notlogin = true, $uid = 0,$result=array();
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        session_start();
        //error_reporting(E_ALL);
        ini_set("display_errors","On");
        if ($this->wen_auth->is_logged_in()) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        } else {
            $this->notlogin = true;
        }
        $this->load->model('remote');
        $this->load->model('Diary_model');

        $this->result['state'] = '001';
        //$this->result['notic'] = '数据错误';
        //$result['thumb'] = $this->profilepic($uid, 2);
    }
    
    /*
     * 获取图片相关医院
     * */
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1 :
                return $this->remote->thumb($id, '36');  //$this->remote->thumb($note_rs[false]['uid'], '36');
            case 0 :
                return $this->remote->thumb($id, '250');
            case 2 :
                return $this->remote->thumb($id, '120');
            default :
                return $this->remote->thumb($id, '120');
                break;
        }
    }
    
    private function gdetail($content,$title){
        $content = preg_replace('/ style=\".*?\"/','',$content);
        //padding:10px;
        //.mainc p { text-indent:2em;}
        return '
        <style>
            .mainc{
                    font-size:14px;
                    line-height:160%;
                    max-width:600px;
                    
                    color:#666666;
                    margin:auto;
            }
            .mainc p {margin:0px 10px;}
        
            .mainc img{
                    max-width:350px;
                     
            }
            .mainc img {
                    width:100%;
	        }
            
            
            
            .wapper_form{
                    width:95%;
                    margin:0 auto;
	        }
            a:link{
                text-decoration:none;
                color:#fc85b6;
	        }
        </style>
        <div id="content" class="mainc">'.$content.'</div> ';
    }
    
    public function getflashSaleList(){
        $page = intval($this->input->get('page'));
        $web = $this->input->get('web');
        $state = $this->input->get('state');
        $page = $page==0 ? 1 : $page;
        
        $thistime= time();
        $start = ($page -1) * 10;
        $limit = "{$start},10";
        
        
        $fields = " id,city,type,type_id,begin,end,lbanner,title,discount,vbuy ";
        

        //state = 1  正常/结束，=2 未开始
        if($state == 1){
            $condition = " and fs.begin <= {$thistime}";
        }elseif($state == 2){
            $condition = " and fs.begin > {$thistime}";
        }
        
        $order = ' fs.level DESC,fs.begin DESC';
        
        

        $fs_sql = "select $fields from flash_Sale as fs where 1=1 and display = 1 {$condition} order by {$order} limit {$limit} ";
        
        $fs_rs = $this->db->query($fs_sql)->result_array();
        
        $this->result['fs_rs'] = array();
        $this->result['data'] = array();
       
        if(!empty($fs_rs)){
            foreach($fs_rs as &$v){
                //$v['begin'] = 1423584000;
               // $v['end'] =1423670400;
                $v['begin'] = intval($v['begin'] - time());
                $v['end'] =  intval($v['end'] - time());
              
                $v['flag'] = 4;
                //$flag 1即将结束(下架)2即将开始(上架)3全新上架4正常5已结束
                if($state == 1){
                    if($v['end'] < 0){
                        $v['flag'] = 5;
                    }elseif($v['end'] <= 259200 && $v['end']>=0){
                        $v['flag'] = 1;
                    }elseif($v['begin'] >= -259200 && $v['begin'] <= 0){
                        $v['flag'] = 3;
                    }
                }elseif($state == 2){
                       $v['flag'] = 2;
                }
                
                $v['begin'] = abs($v['begin']);
                $v['end'] =  abs($v['end']);
                
                $bday = floor($v['begin']/(3600*24));
	            $bsecond = $v['begin']%(3600*24);//除去整天之后剩余的时间
	            $bhour = floor($bsecond/3600);
	            $bsecond = $bsecond%3600;//除去整小时之后剩余的时间 
	            $bminute = floor($bsecond/60);
	            $bsecond = $bsecond%60;//除去整分钟之后剩余的时间 
	            if($bday >0){
	                //返回字符串
	                $v['begin']  = $bday.'天';
	            }else{
	                $v['begin']  = $bhour.'小时';
	            }
	            
	            
	            $day = floor($v['end']/(3600*24));
	            $second = $v['end']%(3600*24);//除去整天之后剩余的时间
	            $hour = floor($second/3600);
	            $second = $second%3600;//除去整小时之后剩余的时间
	            $minute = floor($second/60);
	            $second = $second%60;//除去整分钟之后剩余的时间

                //$v['begin'] = date("d天h时i分",$v['begin']);
                $v['end'] = $day.'天'.$hour.'小时';
                if($day >0){
                    //返回字符串
                    $v['end']  = $day.'天';
                }else{
                    $v['end']  = $hour.'小时';
                }
                
              	if(empty($v['lbanner_key'])) {
                    $v['lbanner'] = $this->remote->show($v['lbanner']);
                }else{
                    $v['lbanner'] = 'http://7xkdi8.com1.z0.glb.clouddn.com/'.$v['lbanner_key'].'?imageView2/2/w/640/q/100/format/png';
                }
                //print_r($v['flag']);die;
            }
            $this->result['state'] = '000';
            
            if($web == 'web'){
                $this->result['data'] = $fs_rs;
            }else{
                $this->result['fs_rs'] = $fs_rs;
            }
            
            //$this->result['sql'] = $this->db->last_query();
        }
        
        echo json_encode($this->result);
    }
    
    public function getflashSaleByid(){
        $id = intval($this->input->get('s_id'));
        $web = $this->input->get('web');
        $fs_sql = "select * from flash_Sale where 1=1 and id = ?";
        $fs_rs = $this->db->query($fs_sql,array($id))->row_array();
        $this->result['fs_detail'] = array();
        $this->result['data'] = array();
        $this->result['data']['tehui_list'] = array();
        $this->result['fs_detail']['tehui_list'] = array();
        
        if(!empty($fs_rs)){
            if(!empty($fs_rs)){
                $fs_rs['pro_arr'] = unserialize($fs_rs['product']);

                $tehui_fields = 't.newversion,t.pre_number,t.p_store,t.id,t.user_id,t.summary,t.title,t.image,t.team_price, t.now_number,t.market_price,t.delivery,t.reser_price,t.deposit';
                
                if(!empty($fs_rs['pro_arr'])){
                    $fs_rs['pro_arr'] = implode(',', $fs_rs['pro_arr']);
                    $tehui_condition = "and t.id in ({$fs_rs['pro_arr']})";
                    $tehui_info = $this->tehuiDB->query("SELECT {$tehui_fields} FROM team as t WHERE 1=1 {$tehui_condition}")->result_array();
                    $randpic = date('Ymdhi',time());
                    foreach ($tehui_info as &$r) {
                        if(strpos($r['image'],'pic.meilimei') == 0){
                            $r['image'] = 'http://tehui.meilimei.com/static/' . $r['image'].'?'.$randpic;
                        }
                        $tehui_sql = "select * from tehui_relation where tehui_id = ?";
                        $tehui_rs = $this->db->query($tehui_sql,array($r['id']))->row_array();
                        $m_id  = $tehui_rs['mechanism'];
                        $this->db->where('id',$m_id);
                         
                        $r['mechanism'] = "";
                        $company = $this->db->get('company')->row_array();
                        if($company){
                            $r['mechanism'] = $company;
                            $r['mechanism'] = $r['mechanism']['name'];
                        }
                    
                    
                        //session 唯一标示付
                        $sid = $r['id'];
                    
                        if(!empty($_SESSION[$sid])){
                            $r['order_num'] = $_SESSION[$sid];
                        }else{
                            $_SESSION[$sid] = rand(66, 88);
                            $r['order_num'] = $_SESSION[$sid];
                        }
                        $r['case_num'] = rand(50,66);
                         
                        $r['reser_price'] = 0;
                        $r['deposit'] = 0;
                         
                        if($tehui_rs['reser_price']){
                            $r['reser_price'] = $tehui_rs['reser_price'];
                        }
                         
                        if($tehui_rs['deposit']){
                            $r['deposit'] = $tehui_rs['deposit'];
                        }
                    
                    }
                    
                    
                    $fs_rs['fm_end_1'] = date("Y年m月d日",$fs_rs['end']);
                    $fs_rs['fm_begin_1'] = date("Y年m月d日 ",$fs_rs['begin']);
                    
                    $fs_rs['i_end'] =  $fs_rs['end'];
                    $fs_rs['i_begin'] =  $fs_rs['begin'];
                    
                    //1 开始 2 未开始
                    $fs_rs['nobegin'] = '1';
                    if($fs_rs['begin'] > time()){
                        $fs_rs['nobegin'] = '2';
                    }
                    
                    $fs_rs['end'] =  abs($fs_rs['end']-time());
                    $fs_rs['begin'] =   abs($fs_rs['begin']-time());
                    
                     
                     
                    $day = floor($fs_rs['end']/(3600*24));
                    $second = $fs_rs['end']%(3600*24);//除去整天之后剩余的时间
                    $hour = floor($second/3600);
                    $second = $second%3600;//除去整小时之后剩余的时间
                    $minute = floor($second/60);
                    $second = $second%60;//除去整分钟之后剩余的时间
                    
                    //$v['begin'] = date("d天h时i分",$v['begin']);
                    $fs_rs['fm_end_2'] = $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
                    
                    //$fs_rs['fm_end_2'] = date("d天h时i分",$fs_rs['end']);
                    //$fs_rs['fm_begin_2'] = date("d天h时i分",$fs_rs['begin']);
                    
                    if(empty($fs_rs['banner_key']) && !is_null($fs_rs['banner_key'])) {
                    	$fs_rs['lbanner'] = $this->remote->show($fs_rs['lbanner']);
                    }else{
                    	$fs_rs['lbanner'] = 'http://7xkdi8.com1.z0.glb.clouddn.com/'.$fs_rs['lbanner_key']."?imageView2/2/w/640/q/100/format/png";
                    }
                    
                    if(empty($fs_rs['banner_key']) && !is_null($fs_rs['banner_key'])) {
                    	$fs_rs['banner'] = $this->remote->show($fs_rs['banner']);
                    }else{
                    	$fs_rs['banner'] = 'http://7xkdi8.com1.z0.glb.clouddn.com/'.$fs_rs['banner_key']."?imageView2/2/w/640/q/100/format/png";
                    }
                    
//                     $fs_rs['banner'] = $this->remote->show($fs_rs['banner']);
//                     $fs_rs['lbanner'] = $this->remote->show($fs_rs['lbanner']);
                    $fs_rs['type_thumb'] = $this->profilepic($fs_rs['type_id'], 1);
                    $fs_rs['context'] = str_replace('src="','src="http://www.meilimei.com/',$fs_rs['context']);
                    
                    $fs_rs['context'] = $this->gdetail($fs_rs['context'], 'tmp');
                    //             echo "<pre>";
                    //             print_r($tehui_info);
                    //             print_r($fs_rs);die;
                    
                    $this->result['state'] = '000';
                    
                    if($web == 'web'){
                        $this->result['data'] = $fs_rs;
                        $this->result['data']['tehui_list'] = $tehui_info;
                    }else{
                        $this->result['fs_detail'] = $fs_rs;
                        $this->result['fs_detail']['tehui_list'] = $tehui_info;
                    }
                }
            }
        }

        echo json_encode($this->result);
    }
    
}