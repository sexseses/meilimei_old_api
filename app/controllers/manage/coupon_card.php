<?php
class coupon_card extends CI_Controller {
    
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
  
        //报告所有错误
        //error_reporting(E_ALL);
        ini_set("display_errors","On");
        header("Content-type: text/html; charset=utf-8");
        $this->eventDB = $this->load->database('event', TRUE);
        
        if ($this->wen_auth->get_role_id() == 16) {
            $this->notlogin = false;
            $this->uid=$this->wen_auth->get_user_id();
        } else {
            redirect('');
        }
    
        $this->load->library('form_validation');
        $this->load->helper('file');
        $this->load->model('users_model');
        $this->load->model('privilege');
        $this->load->model('remote');
        $this->privilege->init($this->uid);
	}
	
	public function index() {

		$coupon_sql = "select distinct batch,coupon_card.begin_time,coupon_card.end_time,coupon_card.credit,coupon_card.quota from coupon_card where 1=1 ";
	    $coupon_rs = $this->db->query($coupon_sql)->result_array();

	    $data['coupon_rs'] = $coupon_rs;

	    $data['message_element'] = "coupon_card";
	    $this->load->view('manage', $data);
	}
	
	
	
	public function coupon_card_list($batch = ''){
	    $coupon_sql = "select * from coupon_card where 1=1 and batch = ? ";
	    $coupon_rs = $this->db->query($coupon_sql,array($batch))->result_array();
	    
	    if(empty($coupon_rs)){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"访问的页面不存在！"));
	        redirect("coupon_card", 'refresh');
	    }
	    
	    foreach ($coupon_rs as &$v){
	        $content_sql = "select * from coupons_sn where 1=1 and  batch = ? and sn = ? ";
	        $v['content'] = $this->eventDB->query($content_sql,array($v['batch'],$v['sn']))->row_array();
	    }
	    
	    $data['coupon_rs'] = $coupon_rs;
	    $data['message_element'] = "coupon_card_list";
	    $this->load->view('manage', $data);
	}
	
	public function coupon_card_add(){
	    if(isset($_POST['act']) && $this->input->post("act") == 'add'){
	        $card = $_POST;
	        $card['quantity'] = abs(intval($card['quantity']));
	        $card['credit'] = abs(intval($card['money']));
	        $card['begin_time'] = strtotime($card['begin']);
	        $card['end_time'] = strtotime($card['end']);
	         
	        $csql = "select * from coupon_card where batch = ?";
	        $coupon_rs = $this->db->query($csql,array($card['batch']))->row_array();
	        
	        if(!empty($coupon_rs)){
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "批次不能相同！"));
	            redirect("manage/coupon_card/coupon_card_add", 'refresh');
	        }
	        
	        $error = array();
	        if ( $card['credit'] < 1 ) {
	            $error[] = "代金券面额不能小于1元";
	        }
	        if ( $card['quantity'] < 1 || $card['quantity'] > 3000 ) {
	            $error[] = "代金券每次只能生产1-100枚";
	        }
	
	        $today = strtotime(date('Y-m-d'));
	        if ( $card['begin_time'] < $today ) {
	            $error[] = "开始时间不能小于当天";
	        }
	        elseif ( $card['end_time'] < $card['begin_time'] ) {
	            $error[] = "结束时间不能小于开始时间";
	        }

	        if (!$error && $this->coupon_card_create($card)) {
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "代金券生成成功！"));
	            redirect("manage/coupon_card", 'refresh');
	        }else{
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"代金券生成不成功！"));
	            redirect("manage/coupon_card", 'refresh');
	        }
	    }
	    $data['message_element'] = "coupon_card_add";
	    $this->load->view('manage', $data);
	}
	
	public function coupon_card_edit($batch = ''){
	    if(empty($batch)){
	        $batch = $this->input->post('batch');
	    }
	    
	    if(empty($batch)){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误，批次不能为空！'));
	        redirect('manage/coupon_card', 'refresh');
	    }
	    $coupon_sql = "select distinct batch,coupon_card.begin_time,coupon_card.end_time,coupon_card.credit,coupon_card.quota from coupon_card where 1=1 and batch = ?";
	    $coupon_rs = $this->db->query($coupon_sql,array($batch))->row_array();
	    
	    if(isset($_POST['act']) && $this->input->post("act") == 'edit'){
	        $card = $_POST;
	        $card['quantity'] = abs(intval($card['quantity']));
	        $card['credit'] = abs(intval($card['money']));
	        $card['begin_time'] = strtotime($card['begin']);
	        $card['end_time'] = strtotime($card['end']);
	    
	        
	        $error = array();
	        if ( $card['credit'] < 1 ) {
	            $error[] = "代金券面额不能小于1元";
	        }
	        if ( $card['quantity'] < 1 || $card['quantity'] > 3000 ) {
	            $error[] = "代金券每次只能生产1-100枚";
	        }
	    
	        $today = strtotime(date('Y-m-d'));
	        if ( $card['begin_time'] < $today ) {
	            $error[] = "开始时间不能小于当天";
	        }
	        elseif ( $card['end_time'] < $card['begin_time'] ) {
	            $error[] = "结束时间不能小于开始时间";
	        }
	        
	        
	        
	        unset($card['act'],$card['money'],$card['quantity'],$card['begin'],$card['end']);

	        $this->db->where('batch',$batch);
	        $this->db->update('coupon_card', $card);
	        
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "代金券修改成功！"));
	        redirect("manage/coupon_card", 'refresh');
	        
// 	        if (!$error && $this->coupon_card_create($card)) {
// 	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error', "代金券生成成功！"));
// 	            redirect("coupon_card", 'refresh');
// 	        }else{
// 	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"代金券生成不成功！"));
// 	            redirect("coupon_card", 'refresh');
// 	        }
	    }
	    
	    $data['coupon_rs'] = $coupon_rs;
	    $data['coupon_num'] = $this->db->query('select * from coupon_card where batch = ?',array($batch))->num_rows();;
	    $data['batch'] = $batch;
	    $data['message_element'] = "coupon_card_edit";
	    $this->load->view('manage', $data);
	}
	
 
	private function coupon_card_create($query){
	    $need = $query['quantity'];
	    for($i=1; $i<=$need; $i++){
	        $card = array(
	            'uid' => $this->uid,
	            'sn' => $this->GenSecret(8),
	            'batch'=> $query['batch'],
	            //'partner_id' => $query['partner_id'],
	            //'team' => $query['team'],
	            //'order_id' => 0,
	            'credit' => $query['credit'],
	            'quota' => $query['quota'],
	            'consume' => 'N',
	            'begin_time' => $query['begin_time'],
	            'end_time' => $query['end_time']
	        );
	        $this->db->insert('coupon_card', $card);
	        //$need = ($this->db->insert('coupon_card', $card)) ? 1 : 0;
	        //if ( $need <= 0 ) return true;
	    }
	     
	    return true;
	}
	
	private function GenSecret($len=6, $type=2){
	    $secret = '';
	    for ($i = 0; $i < $len;  $i++) {
	        if ( 2==$type ){
	            if (0==$i) {
	                $secret .= chr(rand(49, 57));
	            } else {
	                $secret .= chr(rand(48, 57));
	            }
	        }else if ( 1==$type ){
	            $secret .= chr(rand(65, 90));
	        }else{
	            if ( 0==$i ){
	                $secret .= chr(rand(65, 90));
	            } else {
	                $secret .= (0==rand(0,1))?chr(rand(65, 90)):chr(rand(48,57));
	            }
	        }
	    }
	    return $secret;
	}
	
}
?>
