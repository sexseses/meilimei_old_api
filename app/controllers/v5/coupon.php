<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * Api coupon => 优惠券 Controller Class
 * @package        WENRAN
 * @subpackage    Controllers
*/
require_once(__DIR__."/MyController.php");

class coupon extends MY_Controller {
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
    
    
    
    public function getCouponBySn(){
        $id = intval($this->input->get('s_id'));
        $uid = intval($this->input->get('u_id'));
        $web = $this->input->get('web');
        

        $cc_sql = "select * from coupon_card where 1=1 and sn = ?";
        $cc_rs = $this->db->query($cc_sql,array($id))->row_array();
        $this->result['cc_detail'] = array();

        if(count($cc_rs)>0){
            if($cc_rs['useid'] <> 0){
                $this->result['ustate'] = '101';
                $this->result['notice'] = '已经兑换过了！';
                $this->result['state'] = '112';
                echo json_encode($this->result);
                exit;
            }else{
                if(!empty($uid)){
                    $this->result['state'] = '000';
                    $updata['useid'] = $uid;
                    $this->db->where('sn',$id);
                    $this->db->update('coupon_card',$updata);
                    $cc_rs['begin_time'] = date('Y-m-d',$cc_rs['begin_time']);
                    $cc_rs['end_time'] = date('Y-m-d',$cc_rs['end_time']);
                    $this->result['cc_detail'] = $cc_rs;
                }else{
                    $this->result['ustate'] = '001';
                    $this->result['notice'] = '兑换失败，请再试试！';
                    $this->result['state'] = '012';
                }
            }
        }else{
            $this->result['ustate'] = '001';
            $this->result['notice'] = '兑换失败，请再试试！';
            $this->result['state'] = '012';
        }


        echo json_encode($this->result);
    }
    
    public function getUserCouponList(){
        $time = time();
    		$uid = intval($this->input->get('u_id'));
    		$cc_sql = "select * from coupon_card where 1=1 and useid = ? and begin_time <= '{$time}' and end_time >= '{$time}' order by consume DESC,begin_time DESC";
    		$cc_rs = $this->db->query($cc_sql,array($uid))->result_array();
    		$this->result['cc_list'] = array();

    		if(!empty($uid) || $uid == 0){
    			$this->result['state'] = '000';
    			foreach ($cc_rs as &$v){
    			    $v['begin_time'] = date('Y-m-d',$v['begin_time']);
    			    $v['end_time'] = date('Y-m-d',$v['end_time']);
    			    $com_sql = "select * from company where userid = ?";
                $com_rs = $this->db->query($com_sql,array($v['uid']))->row_array();
                
                if(empty($com_rs) || count($com_rs) > 1){
                    $v['company_name'] = "美丽神器APP";
                }else{
                    $v['company_name'] = $com_rs['name'];
                }
    			}
    			
    			$this->result['cc_list'] = $cc_rs;
    		}else{
    			$this->result['ustate'] = '001';
    			$this->result['notice'] = '参数不全！';
    			$this->result['state'] = '012';
    		}
    		
    		echo json_encode($this->result);
    }
    
    public function getListbyCondition(){
        $uid = intval($this->input->get('u_id'));
        $tid = intval($this->input->get('t_id'));

        $t_sql = "select * from team where id = $tid";
        $t_rs = $this->tehuiDB->query($t_sql,array($tid))->row_array();
        
        $nowtime = time(); 
        
        
        $cc_sql = "select * from coupon_card where 1=1 and useid = ? and credit < ? and quota < ? and ( team = ? or team = 0 ) and consume = 'N' and end_time > {$nowtime} and begin_time < {$nowtime}";
        $cc_rs = $this->db->query($cc_sql,array($uid,$t_rs['deposit'],$t_rs['reser_price'],$tid))->result_array();
        
        $this->result['cc_list'] = array();

        if(!empty($uid) || $uid == 0 || !empty($tid)){
            $this->result['state'] = '000';
            foreach ($cc_rs as &$v){
                $v['credit'] = intval($v['credit']);
                $v['quota'] = intval($v['quota']);
                $com_sql = "select * from company where userid = ?";
                $com_rs = $this->db->query($com_sql,array($v['uid']))->row_array();
                
                if(empty($com_rs) || count($com_rs) > 1){
                    $v['company_name'] = "美丽神器APP";
                }else{
                    $v['company_name'] = $com_rs['name'];
                }
                
                $v['begin_time'] = date('Y-m-d',$v['begin_time']);
                $v['end_time'] = date('Y-m-d',$v['end_time']);
            }
             
            $this->result['cc_list'] = $cc_rs;
        }else{
            $result['ustate'] = '001';
            $result['notice'] = '参数不全！';
            $result['state'] = '012';
        }
    
        echo json_encode($this->result);
    }
    
}