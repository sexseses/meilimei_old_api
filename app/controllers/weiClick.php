<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weiclick extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('wei_model');
        $this->load->helper('url_helper');
        $this->load->database();
    }

    public function info(){
        $this->load->view('into.html');
    }

    public function graded(){
        if(!empty($_GET['callback'])){
            $rs = $this->insert();
            $data['code'] = 200;
            $data['data'] = $rs;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function select(){
        if(!empty($_GET['callback'])){
            $rs = $this->sel();
            $data['code'] = 200;
            $data['data'] = $rs;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function count(){
        if(!empty($_GET['callback'])){
            $rs = $this->num();
            $data['code'] = 200;
            $data['data'] = $rs;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }
	public function clickTotal(){
	    
		if(!empty($_GET['callback'])){
            
            $data['code'] = 200;
            $data['data'] = $this->db->query('update we set score= score + 1 where id=16');
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
	}
    public function halloween(){

        if(!empty($_GET['callback'])){
            $content = $this->input->get('content');
            $type = "halloween";
            $data['code'] = 200;
            $data['data'] = $this->db->query("insert into events (type, content) values ('$type','$content')");
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }
	//选手投
	public function clickPlayer(){

		if(!empty($_GET['callback'])){

			$openid=$this->input->get('openid');
			$playerid=$this->input->get('playerid');
		    $array=explode(',',$playerid);
		    foreach($array as $item)
		    {	
		    	$this->db->where('openid',$openid);
		    	$this->db->where('playerid',$item['playerid']);
		    	$row=$this->db->get('click')->num_rows();
		    	
		    	if($row == 0)
		        {
			    	$data=array(
			    		'openid'=>$openid,
			    		'playerid'=>$item['playerid'],
			    		'clickTime'=>time(),
			    		'date'=>date('Y:m:d',time())
			    	);
			    	$this->db->insert('click',$data);
		        }
		       
		    }
		    
		    
            $data['code'] = 200;
            $data['data'] = $this->db->query('SELECT count(*) as num FROM click ')->num_rows();
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
	}
		//选手投
	public function backNow(){

		if(!empty($_GET['callback'])){

			$uid=$this->input->get('uid');
			$ncid=$this->input->get('ncid');
			$realname=$this->input->get('realname');
			$alipay=$this->input->get('alipay');

		    if(!empty($uid) && !empty($ncid) && !empty($alipay))
		    {	

				$this->db->where('ncid',$ncid);
				$this->db->where('uid', $uid);
				$num = $this->db->get('diary_event')->num_rows();
				if($num == 0){
					$data=array(
						'realname'=>$realname,
						'uid'=>$uid,
						'ncid'=>$ncid,
						'alipay'=>$alipay
					);
					$data['data'] = $this->db->insert('diary_event',$data);
				}else{
					$data['data'] = '';
				}
		    }
		    
		    
            $data['code'] = 200;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
	}
	public function clickNum()
	{

		if(!empty($_GET['callback'])){
            
            $data['code'] = 200;
			if(empty($_GET['id'])){
				$data['data'] = $this->db->query("select distinct openid from click ")->num_rows();
			}else{
				$data['data'] = $this->db->query("select * from click where playerid='".$_GET['id']."'")->num_rows();
			}
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
	}

    public function addSign()
    {

        if(!empty($_GET['callback'])){


            $data['code'] = 200;
            if(!empty($_GET['uid'])){
                //当前日期时间

                $sign = $this->db->query("select * from user_signin where points =1 and uid='".$_GET['uid']."' and calDate='".date('Y-m-d')."'")->num_rows();
                if(empty($sign)) {
                    $currentDateTime = date('Y-m-d H:i:s');

                    //记录签到日志
                    $signin_arg_array = array(
                        'createTime' => $currentDateTime,
                        'timeStamp' => $currentDateTime,
                        'uid' => $_GET['uid'],
                        'calDate' => date('Y-m-d'),
                        'points' => 1
                    );
                    $this->db->insert('user_signin', $signin_arg_array);
                    $data['data'] = $this->db->query("select * from user_signin where points =1 and uid='" . $_GET['uid'] . "'")->num_rows();
                }else{
                    $data['code'] = '201';
                    $data['data'] = $this->db->query("select * from user_signin where points =1 and uid='" . $_GET['uid'] . "'")->num_rows();
                }
            }else{
                $data['data'] = 0;
            }
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function getSignTotal()
    {

        if(!empty($_GET['callback'])){

            $data['code'] = 200;
            if(!empty($_GET['uid'])){
                $data['data'] = $this->db->query("select * from user_signin where points =1 and uid='".$_GET['uid']."'")->num_rows();
            }else{
                $data['data'] = 0;
            }
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function ranking(){
        if(!empty($_GET['callback'])){
            $rs = $this->rank();
            $data['code'] = 200;
            $data['data'] = $rs;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function being(){
        if(!empty($_GET['callback'])){
            $rs = $this->be();
            $data['code'] = 200;
            $data['data'] = $rs;
        }else{
            $data['code'] = 403;
            $data['data'] = '';
        }
        $callback = !empty($_GET['callback'])?$_GET['callback']:'';
        echo $callback.'('.json_encode($data).')';
    }

    public function insert(){
        $data = array(
            'openid' => isset($_GET['openid'])?$_GET['openid']:'',
            'img' => isset($_GET['img'])?$_GET['img']:'',
            'weName' => isset($_GET['weName'])?$_GET['weName']:'',
            'score' => isset($_GET['score'])?$_GET['score']:'',
            'time' => time()
        );

        $id = isset($_GET['openid'])?$_GET['openid']:'';
        if(!empty($id))
        {
            $sql = $this->db->query("select * from we where openid = '$id'")->result_array();
        }else{
            return false;
        }

        if($sql){
            if($this->wei_model->update($data)){
                return true;
            }else{
                return false;
            }
        }else{
            if($this->wei_model->add_wei($data)){
                return true;
            }else{
                return false;
            }
        }
    }

    public function sel(){
        $id = isset($_GET['openid']) ? $_GET['openid'] : '';
        if (!empty($id)) {
            $openid = $_GET['openid'];
            $rs = $this->wei_model->select($openid);
            if(!empty($rs)){
                return $rs;
            }else{
                return '没有该ID信息';
            }
        } else {
            return '403';
        }
    }

    public function num(){
        $max = $this->wei_model->count();
        if($max > 0){
            return $max;
        }else{
            return '没有数据';
        }
    }

    public function rank(){
        $id = isset($_GET['openid']) ? $_GET['openid'] : '';
        if(!empty($id)) {
            return $this->wei_model->ran($id);
        }else{
            return '403';
        }
    }
	
    public function be(){
        $id = isset($_GET['openid']) ? $_GET['openid'] : '';
        $id1 = isset($_GET['openid1']) ? $_GET['openid1'] : '';
        if (!empty($id)) {
            $openid = $_GET['openid'];
            $rs = $this->wei_model->sel($openid);
            if(!empty($rs)){
                $user = $rs[0]['score'];
            }else{
                return '用户错误';
            }
        }else{
            return 'false';
        }
        if (!empty($id1)) {
            $openid1 = $_GET['openid1'];
            $rs1 = $this->wei_model->sel1($openid1);
            if(!empty($rs1)){
                $user1 = $rs1[0]['score'];
            }else{
                return '用户错误';
            }
        }else{
            return 'false';
        }
        if($user <= $user1){
            return '002';
        }else{
            return '001';
        }
    }

}
?>