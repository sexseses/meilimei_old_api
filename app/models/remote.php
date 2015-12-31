<?php
set_time_limit(60);
class remote extends CI_Model {

	private $url = 'http://115.29.167.43/',$curl = 'http://www.meilimei.com/',$vars=array();

    private $fileUrl = 'http://7xkdi8.com1.z0.glb.clouddn.com/';
	public function __construct() {
		parent :: __construct();
	}
	
	//copy picture
	public function upload_qiniu($file="",$name = "") {
		$path = $_SERVER['DOCUMENT_ROOT'];

		require_once($path."/app/libraries/qiniu/rs.php");
		require_once($path."/app/libraries/qiniu/io.php");
		$bucket = "meilimei";
		$local_file = "upload/".$name;
		$key = $local_file;
		$accessKey = 'tjtiZoMyJa9ggnNisrdocCCGvTlNLFvYxGMMQ6LF';
		$secretKey = '6zQXHont_jN-QFznn96H4oKA7IUqjibPeC-Yto62';
		Qiniu_SetKeys($accessKey, $secretKey);
		$putPolicy = new Qiniu_RS_PutPolicy($bucket);
		$upToken = $putPolicy->Token(null);
		
		
		
		if(move_uploaded_file($file,$local_file)){
			
			$upload_file = $path."/upload/".$name;

			$putExtra = new Qiniu_PutExtra();
			$putExtra->Crc32 = 1;
			list($ret, $err) = Qiniu_PutFile($upToken,$key,$upload_file,$putExtra);
 
			if ($err !== null) {
				return false;
			} else {
				//var_dump($ret);
				return $ret;
			}
		}else{
			return false;
// 			echo "move_uploaded错误！";
// 			die;
		}
		
		return false;
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
		//$this->curl = $_SERVER['DOCUMENT_ROOT'];
		if(move_uploaded_file($file, '/mnt/meilimei/up_tmp/'.$name)){
			$this->vars['WEN_UPLOAD_FILE'] = 'http://www.meilimei.com/up_tmp/'.$name;
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
              if(!$this->Npost('cp')){
			   if($checkfile){
			   	$i = 1;
			   	    while($i<12 and $fileFail){
			   	    	if($this->Npost('cp')){
			   	    		unlink('/mnt/meilimei/up_tmp/'.$name);
			   	    		$fileFail = false;
			        	    return true;
			   	    	}
			   	    	usleep(rand(1000,5000));
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

    //copy picture
    public function cp2($file='',$name = '',$storepath='',$size=array(),$checkfile=true,$DIR='upload') {
        if(copy($file, '/mnt/meilimei/up_tmp/'.$name)){
            $this->vars['WEN_UPLOAD_FILE'] = 'http://www.meilimei.com/up_tmp/'.$name;
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
                if(!$this->Npost('cp')){
                    if($checkfile){
                        $i = 1;
                        while($i<12 and $fileFail){
                            if($this->Npost('cp')){
                                unlink('/mnt/meilimei/up_tmp/'.$name);
                                $fileFail = false;
                                return true;
                            }
                            usleep(rand(1000,5000));
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
      $ENINFO['WEN_AC_FROM'] = 'www.meilimei.com';
      $this->vars['WEN_AC_TOKEN'] = rand(100000,999999);
      $this->vars['WEN_ENC'] = crypt(md5(serialize($ENINFO)),$this->vars['WEN_AC_TOKEN'].$this->vars['WEN_STORE_PATH']);
	}
	//Encapsulation data
	private function enc2data($file='') {
      $ENINFO = array();
      $this->vars['WEN_STORE_PATH'] = $ENINFO['WEN_STORE_PATH'] = $file;
      $ENINFO['WEN_AC_TIME'] = time();
      $ENINFO['WEN_AC_FROM'] = 'www.meilimei.com';
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
	
	//show data
	public function showup($file,$width="auto" ){
	    return 'http://pic.meilimei.com.cn/upload/'.urlencode($file).'_'.$width ;
	}

	public function show320($file,$width="auto" ){
		return 'http://pic.meilimei.com.cn/upload/'.urlencode($file);
	}
    public function show800($file,$width="auto" ){
        return 'http://pic.meilimei.com.cn/upload/'.urlencode($file);
    }
    
    /**
     * @param $file  获取图片地址
     * @param int $width 获取图片的宽度
     * @param string $os  操作系统
     * @return string
     */
    public function getQiniuImageWatermark($file, $width = 360, $os = 'android'){
        switch ($width) {
            case 150:
                return $this->fileUrl . $file . "?imageView2/1/w/150/q/75/format/jpg";
                break;
            case 160:
    
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/75/format/jpg";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/100/format/jpg";
                }
                break;
            case 360:
    
                if ($os == 'android') {
                    return $this->fileUrl . $file ."?imageView2/2/w/360/q/75/format/jpg|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center"; //
                } else {
                    return $this->fileUrl . $file ."?imageView2/2/w/360/q/75/format/jpg|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center";
                }
                break;
            case 640:
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center";
                   
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/100/format/jpg|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center";
                }
            case 1242:
    
                return $this->fileUrl . $file . "?imageView2/2/w/1242/q/100/format/jpg";
                break;
    
            default:
    
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/100/format/jpg";
                }
        }
    
    }

	/**
	 * @param $file  获取图片地址
	 * @param int $width 获取图片的宽度
	 * @param string $os  操作系统
	 * @return string
	 */
	public function getQiniuImagewatertopic($file, $width = 360, $os = 'android'){
		switch ($width) {
			case 150:
				return $this->fileUrl . $file . "?imageView2/1/w/150/q/75/format/jpg";
				break;
			case 160:
				if ($os == 'android') {
					return $this->fileUrl . $file . "?imageView2/2/w/160/q/75/format/jpg";
				} else {
					return $this->fileUrl . $file . "?imageView2/2/w/160/q/100/format/jpg";
				}
				break;
			case 360:
				if ($os == 'android') {
					return $this->fileUrl . $file . "?imageView2/2/w/360/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/25/gravity/Center";
				} else {
					return $this->fileUrl . $file . "?imageView2/2/w/360/q/100/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/25/gravity/Center";
				}
				break;
			case 640:
				if ($os == 'android') {
					return $this->fileUrl.urlencode($file) ."?imageView2/2/w/640/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/25/gravity/Center";
				} else {
					return $this->fileUrl.urlencode($file)."?imageView2/2/w/640/q/100/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/25/gravity/Center";
				}
			case 1242:
				return $this->fileUrl . $file . "?imageView2/2/w/1242/q/100/format/jpg";
				break;

			default:
				if ($os == 'android') {
					return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/40/gravity/Center";
				} else {
					return $this->fileUrl . $file . "?imageView2/2/w/640/q/100/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/40/gravity/Center";
				}
		}

	}
    
    /**
     * @param $file  获取图片地址
     * @param int $width 获取图片的宽度
     * @param string $os  操作系统
     * @return string
     */
    public function getQiniuImagewater($file, $width = 360, $os = 'android'){
        switch ($width) {
            case 150:
                return $this->fileUrl . $file . "?imageView2/1/w/150/q/75/format/jpg";
                break;
            case 160:
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/75/format/jpg";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/100/format/jpg";
                }
                break;
            case 360:
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/360/q/75/format/jpg";//%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/40/gravity/Center";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/360/q/100/format/jpg";//%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/40/gravity/Center";
                }
                break;
			case 480:
				if ($os == 'android') {
					return $this->fileUrl . $file . "?imageView2/3/w/480/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0MjAucG5n/dissolve/20/gravity/Center/ws/1";
				} else {
					return $this->fileUrl . $file . "?imageView2/3/w/480/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0MjAucG5n/dissolve/20/gravity/Center/ws/1";
				}
				break;
            case 640:
                if ($os == 'android') {
                    return $this->fileUrl.$file."?imageView2/2/w/640/q/75/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM2NC5wbmc=/dissolve/20/gravity/Center/ws/1";
                } else {
                    return $this->fileUrl.$file."?imageView2/2/w/640/q/100/format/jpg%7Cwatermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM2NC5wbmc=/dissolve/20/gravity/Center/ws/1";
                }
            case 1242:
                return $this->fileUrl . $file . "?imageView2/2/w/1242/q/100/format/jpg";
                break;
    
            default: 
                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg%7watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0Mi5wbmc=/dissolve/20/gravity/Center/ws/1";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg%7watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0Mi5wbmc=/dissolve/20/gravity/Center/ws/1";
                }
        }
    
    }

    /**
     * @param $file  获取图片地址
     * @param int $width 获取图片的宽度
     * @param string $os  操作系统
     * @return string
     */
    public function getQiniuImage($file, $width = 360, $os = 'android'){
        switch ($width) {
            case 150:
                return $this->fileUrl . $file . "?imageView2/1/w/150/q/75/format/jpg";
                break;
            case 160:

                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/75/format/jpg";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/160/q/100/format/jpg";
                }
                break;
            case 360:

                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/360/q/75/format/jpg";
                    //|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/360/q/100/format/jpg";
                    //|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM3LnBuZw==/dissolve/40/gravity/Center
                }
                break;
            case 640:

                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg";
                    //|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/100/format/jpg";
                    //|watermark/1/image/aHR0cDovLzd4a2RpOC5jb20xLnowLmdsYi5jbG91ZGRuLmNvbS93aXM0LnBuZw==/dissolve/40/gravity/Center
                }
            case 1242:

                return $this->fileUrl . $file . "?imageView2/2/w/1242/q/100/format/jpg";
                break;

            default:

                if ($os == 'android') {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/75/format/jpg";
                } else {
                    return $this->fileUrl . $file . "?imageView2/2/w/640/q/100/format/jpg";
                }
        }

    }

    /**
     * @param $file  获取图片地址
     * @param int $width 获取图片的宽度
     * @param string $os  操作系统
     * @return string
     */
    public function getLocalImage($file, $width = 360, $os = 'android'){


        $arr_url = array();
        $arr_url = explode('/', $file);
        $url = $file;

        switch ($width) {
            case '160':
                return 'http://pic.meilimei.com.cn/upload/' . urlencode(str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x160/',$url));
                break;
            case '360':
                return 'http://pic.meilimei.com.cn/upload/' . urlencode($url);
                break;
            case '640':
                return 'http://pic.meilimei.com.cn/upload/' . urlencode($url);
            case '1242':
                return 'http://pic.meilimei.com.cn/upload/' . urlencode($url);
                break;
            default:
                return 'http://pic.meilimei.com.cn/upload/' . urlencode($url);
        }

    }
    //show thumb
	public function thumb($uid,$width=250,$type=0){
        if(intval($uid) < 0)
            return;
        $this->db->where('id',$uid);
        $tmp = $this->db->get('users')->result_array();

        if(isset($tmp[0]['icon']) &&!empty($tmp[0]['icon'])){
            return $tmp[0]['icon'];
        }else {
            return 'http://pic.meilimei.com.cn/thumb/' . urlencode($uid) . '_' . $width . '_' . $type . '?' . time();
        }
	}
    public function showmeilimei($file){
        return 'http://www.meilimei.com/upload/'.$file;
    }
	// get pic info
	public function info($file){
		 $tmp = file_get_contents($this->url.'info.php?info='.urlencode($file));
        return unserialize($tmp);
	}
}

?>
