<?php
set_time_limit(60);
class remote_new extends CI_Model {
	private $url = 'http://115.29.167.43/',$curl = 'http://www.meilimei.com/',$vars=array(),$add='/Users/kingsley/Documents/develop/PHP/meilimei/up_tmp/';
	public function __construct() {
		parent :: __construct();
		//报告所有错误
		error_reporting(E_ALL);
		ini_set("display_errors","On");
	}
	//upload file
	public function upload($file = '',$storepath='',$size=array()) {
		if ($file and $storepath) {
			if(!empty($size)){
				$this->vars['WEN_STORE_WIDTH'] = $size['width'];
				$this->vars['WEN_STORE_HEIGHT'] = $size['height'];
			}else{
				$this->vars['WEN_STORE_WIDTH'] = 0;
				$this->vars['WEN_STORE_HEIGHT'] = 0;
			}
			$this->encdata($file,$storepath);
			return $this->post('add');
		}
		return false;
	}
    //delete file
	public function del($file = '',$ck=false) {
		if ($file) {
			$this->enc2data($file);
			$state = $this->post('del');
			if($ck){
				if($this->CkFile($this->noDNSUrl($file))){
				    return false;
				}else{
				    return true;
				}
		    }
		    return $state;
		}
		return false;
	}
    //modify file
	public function mod($file = '',$sorrce='') {
		if ($file) {
			$this->encdata($file,$sorrce);
			return $this->post('modf');
		}
		return false;
	}
	//debug copy file api
	public function dcp($name,$storepath='ss') {
		$this->vars['WEN_UPLOAD_FILE'] = $this->curl.'up_tmp/'.$name;
			$this->vars['WEN_STORE_PATH'] = $storepath;
			$this->vars['WEN_STORE_WIDTH'] = 0;
			$this->vars['WEN_STORE_HEIGHT'] = 0;
			$this->enc2data($storepath);
			if($this->Npost('testApi')){
				echo 'ss';
			}else{
				echo 'zz';
			};
	}
	//copy picture
	public function cp($file='',$name = '',$storepath='',$size=array(),$checkfile=true,$DIR='upload') {
		if(move_uploaded_file($file, $this->add.$name)){
 
			$this->vars['WEN_UPLOAD_FILE'] = $this->curl.'up_tmp/'.$name;
			$this->vars['WEN_STORE_PATH'] = $storepath;
			$this->vars['WEN_STORE_DIR'] = $DIR;
			if(!empty($size)){
				$this->vars['WEN_STORE_WIDTH'] = $size['width'];
				$this->vars['WEN_STORE_HEIGHT'] = $size['height'];
			}else{
				$this->vars['WEN_STORE_WIDTH'] = 0;
				$this->vars['WEN_STORE_HEIGHT'] = 0;
			}
			$this->enc2data($storepath);
			$fileFail = true;
            try{
                
              if(!$this->Npost('cpcp')){
			   if($checkfile){
			   	$i = 1;
			   	    while($i<12 and $fileFail){
			   	    	if($this->Npost('cpcp')){
			   	    		unlink($this->add.$name);
			   	    		$fileFail = false;
			        	    return true;
			   	    	}
			   	    	usleep(rand(1000,5000));
			   	    	$i++;
			   	    }
                   return false;
			   }
			  }else{
			  	 unlink($this->add.$name);
			     return true;
			  }
            }catch(Exception $e) {
            	unlink($this->add.$name);
           }
		}
		return false;
	}
	//copy video file
	public function cpf($file='',$name = '',$storepath='',$checkfile=true) {
		if(move_uploaded_file($file, '/mnt/meilimei/up_tmp/'.$name)){
			$this->vars['WEN_UPLOAD_FILE'] = $this->curl.'up_tmp/'.$name;
			$this->vars['WEN_STORE_PATH'] = $storepath;
			$this->enc2data($storepath);
			$fileFail = true;
            try{
              if(!$this->Npost('cpf')){
			   if($checkfile){
			   	$i = 1;
			   	    while($i<3 and $fileFail){
			   	    	if($this->Npost('cpf')){
			   	    		unlink('/mnt/meilimei/up_tmp/'.$name);
			   	    		$fileFail = false;
			        	    return true;
			   	    	}
			   	    	$i++;
			   	    }
                   return false;
			   }
			  }else{
			  	 unlink('/mnt/meilimei/up_tmp/'.$name);
			     return true;
			  }
            }catch(Exception $e) {
            	unlink('/mnt/meilimei/up_tmp/'.$name);
           }
		}
		return false;
	}

	//chiek url file exist?
   public function CkFile($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     if($code == 200){
       $status = true;
     }else{
      $status = false;
    }
     curl_close($ch);
     return $status;
   }
   //upload user file
	public function upThumbAndDel($file='',$uid=0) {
		$this->vars['WEN_STORE_UID'] = $uid;
		if(strpos($file,'ttp://')){
			$this->vars['WEN_UPLOAD_FILE'] = $file;
			$this->enc2data();
			$this->post('uputhumb');
		}else{
		  $name = uniqid(time(), false).'.jpg';
			$this->vars['WEN_UPLOAD_FILE'] = 'http://www.meilimei.com/'.$file;
			$this->enc2data();
			$this->post('uputhumb');
			unlink('/mnt/meilimei/'.$file) or die('error'.$file);
		}
		return false;
	}
    //upload user file
	public function uputhumb($file='',$uid=0) {
		$this->vars['WEN_STORE_UID'] = $uid;
		if(strpos($file,'ttp://')){
			$this->vars['WEN_UPLOAD_FILE'] = $file;
			$this->enc2data();
			$this->post('uputhumb');
		}else{
		  $name = uniqid(time(), false).'.jpg';
		  if (move_uploaded_file($file, '/mnt/meilimei/up_tmp/'.$name)) {
			$this->vars['WEN_UPLOAD_FILE'] = 'http://www.meilimei.com/up_tmp/'.$name;
			$this->enc2data();
			$this->post('uputhumb');
			unlink('/mnt/meilimei/up_tmp/'.$name) or die('error'.$name);
		  }
		}
		return false;
	}
	//post
	 private function post($ac) {
		set_time_limit(300);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url.$ac);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->vars);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		$state = curl_exec($ch);
		curl_errno($ch) && die(curl_error($ch));
		curl_close($ch);
		return $state;
	}
   //new post
   private function Npost($ac){
        $content = http_build_query($this->vars);
        $content_length = strlen($content);
        $options = array (
        	'http' => array (
        		'method' => 'POST',
        		'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
        		"Content-length: $content_length\r\n",
        		'content' => $content
        	)
        );
        $context = stream_context_create($options);
        return file_get_contents($this->url.$ac, false, $context);
   }
	//Encapsulation data
	private function encdata($file,$storepath) {
      $ENINFO = array();
      $this->vars['WEN_UPLOAD'] = '@'.$file;
      $this->vars['WEN_STORE_PATH'] = $ENINFO['WEN_STORE_PATH'] = $storepath;
      $ENINFO['WEN_AC_TIME'] = time();
      $ENINFO['WEN_AC_FROM'] = '115.29.225.75';
      $this->vars['WEN_AC_TOKEN'] = rand(100000,999999);
      $this->vars['WEN_ENC'] = crypt(md5(serialize($ENINFO)),$this->vars['WEN_AC_TOKEN'].$this->vars['WEN_STORE_PATH']);
	}
	//Encapsulation data
	private function enc2data($file='') {
      $ENINFO = array();
      $this->vars['WEN_STORE_PATH'] = $ENINFO['WEN_STORE_PATH'] = $file;
      $ENINFO['WEN_AC_TIME'] = time();
      $ENINFO['WEN_AC_FROM'] = '115.29.225.75';
      $this->vars['WEN_AC_TOKEN'] = rand(100000,999999);
      $this->vars['WEN_ENC'] = crypt(md5(serialize($ENINFO)),$this->vars['WEN_AC_TOKEN'].$this->vars['WEN_STORE_PATH']);
	}
	//no dns file real url---
	public function noDNSUrl($file){
		return $this->url.'upload/'.$file;
	}

	//no dns file real url---
	public function noDNSCUrl($file){
		return $this->curl.'upload/'.$file;
	}
	//show data
	public function show($file,$width="auto" ){
		return 'http://pic.meilimei.com.cn/picture/'.urlencode($file).'_'.$width ;
	}

	public function show320($file,$width="auto" ){
		return 'http://pic.meilimei.com.cn/upload/'.urlencode($file);
	}
    //show thumb
	public function thumb($uid,$width=250,$type=0){
		return 'http://pic.meilimei.com.cn/thumb/'.urlencode($uid).'_'.$width.'_'.$type.'?'.date('d');
	}
	// get pic info
	public function info($file){
		 $tmp = file_get_contents($this->url.'info.php?info='.urlencode($file));
        return unserialize($tmp);
	}
}

?>
