<?php
class coupon extends CI_Controller {
	private $notlogin = true,$uid='';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('coupon')){
          die('Not Allow');
       }
	}
	public function index() {
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "coupon";
		$this->load->view('manage', $data);
	}
	
	public function coupon_card(){
	    $coupon_sql = "select * from coupon_card where 1=1 and uid = ? ";
	    $coupon_rs = $this->db->query($coupon_sql,array($this->uid))->row_array();
	     
	    $data['coupon_rs'] = $coupon_rs;
	
	    $data['message_element'] = "coupon_card";
	    $this->load->view('template', $data);
	}
	
	public function coupon_card_add(){
	    if(isset($_POST['act']) && $this->input->post("act") == 'add'){
	        $card = $_POST;
	        $card['quantity'] = abs(intval($card['quantity']));
	        $card['credit'] = abs(intval($card['money']));
	        $card['begin_time'] = strtotime($card['begin']);
	        $card['end_time'] = strtotime($card['end']);
	         
	         
	        $error = array();
	        if ( $card['credit'] < 1 ) {
	            $error[] = "代金券面额不能小于1元";
	        }
	        if ( $card['quantity'] < 1 || $card['quantity'] > 100 ) {
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
	            redirect("counselor/coupon_card", 'refresh');
	        }else{
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"代金券生成不成功！"));
	            redirect("counselor/coupon_card", 'refresh');
	        }
	    }
	    $data['message_element'] = "coupon_card_add";
	    $this->load->view('template', $data);
	}
	
	public function coupon_card_edit($id = ""){
	    if(empty($id)){
	        $event_id = $this->input->post("id");
	    }
	    if(empty($id)){
	        $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '参数错误，特惠id不能为空！'));
	        redirect('manage/tehui', 'refresh');
	    }
	    $coupon_sql = "select * from coupon_card where 1=1  and id = ?";
	    $coupon_rs = $this->db->query($coupon_sql,array($id))->row_array();
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
	        if ( $card['quantity'] < 1 || $card['quantity'] > 100 ) {
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
	            redirect("counselor/coupon_card", 'refresh');
	        }else{
	            $this->session->set_flashdata('flash_message', $this->common->flash_message('error',"代金券生成不成功！"));
	            redirect("counselor/coupon_card", 'refresh');
	        }
	    }
	    $data['message_element'] = "coupon_card_add";
	    $this->load->view('template', $data);
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
