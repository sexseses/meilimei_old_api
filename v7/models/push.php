<?php
class push extends CI_Model {
	private $passphrase = '123456';
	private $cert = '/usr/cert/online.pem';
	private $push = 'ssl://gateway.push.apple.com:2195';
	public $error = '', $error_code,$mec;
	public function __construct() {
		parent :: __construct();
		$this->mec = new Memcache();
		$this->mec->connect('127.0.0.1', 11211);
	}
	public function setTest(){
		$this->cert= '/usr/cert/test.pem';
		$this->push='ssl://gateway.sandbox.push.apple.com:2195';
	}

	public function send($message = '',$extra = array(), $token = '', $pushmusic = 'whiz') {
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $this->cert);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
		stream_context_set_option($ctx, 'ssl', 'cafile', '/usr/cert/entrust_2048_ca.cer');

		$fp = stream_socket_client($this->push, $this->error_code, $this->error, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

		$body['aps'] = array (
			'alert' => $message,
			//s'badge'=>1,
			'sound' => $pushmusic
		);
		$body['aps'] = array_merge($body['aps'],$extra);
        //echo '<pre>';
        //print_r($body);
		$payload = json_encode($body);
		$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
		$result = fwrite($fp, $msg, strlen($msg));
        //echo '<pre>';
        //print_r($result);
		if (!$result)
			return false;
		else
			return true;
	}

	public function sendUser($message = '', $uid = '',$extra = array()) {
	 	$this->setTest();
	    $tmp =  $extra;
	    $tmp['message'] = $message;
	    $res = array();
	    $res[] = $tmp;
	    if($past = $this->mec->get('push_'.$uid)){
	    	$res[] = $past;
	    }
	    $this->mec->set('push_'.$uid,$res,MEMCACHE_COMPRESSED, 30);
		$this->db->where('uid', $uid);
		$this->db->where('pushalert', 'enabled');
        $this->db->order_by('id desc');
        $this->db->limit(3);
		$tmp = $this->db->get('apns_devices')->result_array();
        //echo $this->db->last_query();
		if (!empty ($tmp)) {
			$state = true;
			foreach($tmp as $r){
				$state = $state&$this->send($message,$extra, $r['devicetoken'], $r['pushmusic']);
			}
			return  $state;
		}
		return false;
	}
	public function __destruct(){
       $this->mec->close();
	}
}
?>
