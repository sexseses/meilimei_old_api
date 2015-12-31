<?php
/**
 * 
 * @author 施耀靓
 * ajax 方法汇总
 */
class ajaxfun extends CI_Controller {
    protected  $jsonarr;
    public function __construct() {
        parent :: __construct();
        $this->tehuiDB = $this->load->database('tehui', TRUE);
        $this->eventonedb = $this->load->database('event1', TRUE);
        //报告所有错误
        //error_reporting(E_ALL);
        //ini_set("display_errors","On");
        header("Content-type: text/html; charset=utf-8");
    
        if ($this->wen_auth->get_role_id() == 16) {
            $this->notlogin = false;
            $this->uid=$this->wen_auth->get_user_id();
        } else {
            redirect('');
        }
    
        $this->load->helper('file');
        $this->load->model('privilege');
        $this->load->model('remote');
        $this->privilege->init($this->uid);
        $this->jsonarr['code'] = 0;
    }
    
    private function is_phone_num($str){
        return preg_match('/(1(?:3[4-9]|5[012789]|8[78])\d{8}|1(?:3[0-2]|5[56]|8[56])\d{8}|18[0-9]\d{8}|1[35]3\d{8})|14[57]\d{8}/s', $str);
    }
    
    
    /**
     * 社区活动发送短信报名
     */
    
    public function community_enter_sendSms() {
        $this->load->library('sms');
        $id = abs(intval($this->input->post('id')));
        $smstext = $this->input->post('smstext');
        if($id == 0 || empty($id)) {
            $this -> jsonarr['msg'] = '参数错误！';
            return;
        }
        
        $sql = "select * from event_topic_enter where id =$id where sms = 'Y'";
        $rs = $this->eventonedb->query($sql)->row_array();
        
        
        $status = $this->sms->sendSMS(array ($rs['mobile']), $smstext);
        if($status===false || $status =='' || $status <0){
            $this -> jsonarr['msg'] = '参数错误！';
            return;
        }
        
        $updata = array(
            'smscontext' => $smstext,
            'sms' => 'Y'
        );
        
        $this->eventonedb->where('id',$id);
	    $uprs = $this->eventonedb->update('event_topic_enter',$updata);
        
	    if($uprs){
	        $this -> jsonarr['code'] = 1;
	        $this -> jsonarr['msg'] = '发送成功！';
	    }

        echo json_encode($this->jsonarr);
    }

    
    /**
     *  社区活动置顶
     */
	public function community_detail_top(){
	    $id = $this->input->post('topic_id');
	    
	    if(empty($id)){
	        $this->jsonarr['msg'] = "设置置顶失败";
	    }else{
	        $updata = array(
	            'top' => 1
	        );
	         
	        $this->eventonedb->where('topic_id',$id);
	        $rs = $this->eventonedb->update('event_topic_detail',$updata);
	         
	        if($rs){
	            $this->jsonarr['code'] = 1;
	            $this->jsonarr['msg'] = "设置置顶成功";
	        }
	    }
	    
	    echo json_encode($this->jsonarr);
	}
	
	public function community_detail_notop(){
	    $id = $this->input->post('topic_id');
	     
	    if(empty($id)){
	        $this->jsonarr['msg'] = "设置置顶失败";
	    }else{
	        $updata = array(
	            'top' => 0
	        );
	
	        $this->eventonedb->where('topic_id',$id);
	        $rs = $this->eventonedb->update('event_topic_detail',$updata);
	
	        if($rs){
	            $this->jsonarr['code'] = 1;
	            $this->jsonarr['msg'] = "取消置顶成功";
	        }
	    }
	     
	    echo json_encode($this->jsonarr);
	}
	 
	
	/**
	 *  社区活动隐藏
	 */
	public function community_detail_display(){
	    $id = $this->input->post('topic_id');
	     
	    if(empty($id)){
	        $this->jsonarr['msg'] = "设置隐藏失败";
	    }else{
	        $updata = array(
	            'display' => 0
	        );
	
	        $this->eventonedb->where('topic_id',$id);
	        $rs = $this->eventonedb->update('event_topic_detail',$updata);
	
	        if($rs){
	            $this->jsonarr['code'] = 1;
	            $this->jsonarr['msg'] = "设置隐藏成功";
	        }
	    }
	     
	    echo json_encode($this->jsonarr);
	}
	
	public function community_detail_nodisplay(){
	    $id = $this->input->post('topic_id');
	
	     if(empty($id)){
	        $this->jsonarr['msg'] = "设置显示失败";
	    }else{
	        $updata = array(
	            'display' => 1
	        );
	
	        $this->eventonedb->where('topic_id',$id);
	        $rs = $this->eventonedb->update('event_topic_detail',$updata);
	
	        if($rs){
	            $this->jsonarr['code'] = 1;
	            $this->jsonarr['msg'] = "设置显示成功";
	        }
	    }
	     
	    echo json_encode($this->jsonarr);
	}
}