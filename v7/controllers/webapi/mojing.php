<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mojing extends CI_Controller {
	
	public function __construct() {
		parent :: __construct();
		$this->load->helper('url');
		$this->load->helper('cookie');
	}

	public function index()
	{
		header("Content-type:text/html;charset=utf-8");
		$data['page_title'] = '魔镜';
		$this->load->view('header.php',$data);
		$this->load->view('mojing/mojing.php');
		$this->load->view('footer.php');
	}
	private function returnpicmr($picName='',$target='')
	{
	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, 'http://www.meilimei.com/webapi/info/picmrweb');
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($ch, CURLOPT_POSTFIELDS,array('file'=>$picName,'target'=>$target));
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	 $info = curl_exec($ch);
	 curl_close($ch);
	//$info = json_decode($info,true);
	 return $info; 
	}
		

	public function setimg($img,$age,$gender,$score,$infos,$res,$skin,$huitou,$target,$types) {
		$dst = "/mnt/meilimei/m/upload/mojokbg.png";

		//得到原始图片信息
		$dst_im = imagecreatefrompng($dst);
		$dst_info = getimagesize($dst);

		//水印图像
		$src = "/mnt/meilimei/m/upload/mojokbg2.png";
		$src_im = imagecreatefrompng($src);
		$src_info = getimagesize($src); 
		if($types=='image/png'){
			$im = imagecreatefrompng($img);//imagecreatefrompng
		}elseif($types=='image/jpeg'){
			$im = imagecreatefromjpeg($img);
		}elseif($types=='image/gif'){
			$im = imagecreatefromgif($img);
		}elseif($types=='image/bmp'){
			$im = imagecreatefromwbmp($img);
		}
		
		$im_src = getimagesize($img);

	    $temp_img=imagecreatetruecolor(320,480);//创建画布     
	    //$im=create($src);     
	    imagecopyresampled($temp_img,$im,0,0,0,0,226,313,$im_src[0],$im_src[1]); //人脸


		$alpha = 100;
		imagecopy($dst_im,$temp_img,48,75,0,0,226,313);//后人脸

		imagecopy($dst_im,$src_im,45,72,0,0,237,323);//背景
    
		//$im = imagecreate(400 , 300);
		$gray = ImageColorAllocate($dst_im , 255 , 255 , 255);
		$pink = ImageColorAllocate($dst_im, 246 , 109 , 152);
		$fontfilebold = "/mnt/meilimei/m/upload/msyhbd.ttf";
		$fontfile = "/mnt/meilimei/m/upload/msyh.ttf";

		$str = iconv('GB2312','UTF-8',$huitou.'%');
		$str1 = $infos;
		$str2 = iconv('GB2312','UTF-8',$skin);
		$str3 = iconv('GB2312','UTF-8',$score);
		$str4 = iconv('GB2312','UTF-8',$age);

		ImageTTFText($dst_im, 22, 0, 175, 40, $gray , $fontfilebold , $str);
		ImageTTFText($dst_im, 11, 0, 50, 65, $gray , $fontfile , $str1);
		ImageTTFText($dst_im, 19, 0, 67, 225, $pink , $fontfilebold , $str2);
		ImageTTFText($dst_im, 19, 0, 67, 273, $pink , $fontfilebold , $str3);
		ImageTTFText($dst_im, 19, 0, 67, 320, $pink , $fontfilebold , $str4);
		

		imagesavealpha($src_im, true);
		imagesavealpha($dst_im, true);
		
		Imagepng($dst_im,"/mnt/meilimei/m/upload/webmojing/".date('Y') . '/' . date('m').'/' .$target.".jpg");
		return ImageDestroy($dst_im); 
	}
		
		
	public function mset(){
		setcookie("mojingid","1",time()+3600);
		date_default_timezone_set("Asia/Chongqing");
		$target= date('Y').date('m').rand(000001,999999);
		$img = $_POST['imgurl'];
				if (isset($img)){
					$targetc=  '/mnt/meilimei/m/upload/webmojing/';
					$targets = $targetc.$target.'.jpg';
					
							if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)){
							
							$img = base64_decode($matches[2]);
							file_put_contents($targets, $img);
							$send =  str_replace('/mnt/meilimei/m/upload','http://m.meilimei.com/upload',$targets);
							$targetc2=  '/mnt/meilimei/m/upload/webmojing/'.date('Y').'/'.date('m').'/';
								if (file_exists($targetc2)){
									$infoall = json_decode($this->returnpicmr($send,$target),true);
									if(isset($infoall['res'])){
										if($this->setimg($send,$infoall['age'],$infoall['gender'],$infoall['score'],$infoall['infos'],$infoall['res'],$infoall['skin'],$infoall['huitou'],$infoall['target'],$matches[1])){
											echo  $target;
										}else{
											echo '0'; 
										}
									}else{
										echo '1'; 
									}
								}else {
									mkdir($targetc2,0777,true);
									$infoall = json_decode($this->returnpicmr($send,$target),true);
									if(isset($infoall['res'])){
										if($this->setimg($send,$infoall['age'],$infoall['gender'],$infoall['score'],$infoall['infos'],$infoall['res'],$infoall['skin'],$infoall['huitou'],$infoall['target'],$matches[1])){
											echo  $target;
										}else{
											echo '0'; 
										}
									}else{
										echo 1; 
									}
							unlink('/mnt/meilimei/m/upload/webmojing/'.$target.'.jpg');
						}
							
						}else {
							echo '这张图来自外星嘛？换张试试哦'; 
						}
				}else{
					echo '这张图来自外星嘛？换张试试哦'; 
				}
	}
	
	public function mjget($nums=''){
			header("Content-type:text/html;charset=utf-8");
			$data['page_title'] = '魔镜-测今日回头率？测年龄和皮肤健康指数';
			$data['nums'] =  $nums;
			$this->load->view('header.php',$data);
			$this->load->view('mojing/mjget.php');
			$this->load->view('footer.php');
	}
	
}