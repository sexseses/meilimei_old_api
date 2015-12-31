<?php
class m extends CI_Controller {
	public function __construct() {
		parent :: __construct();
	}
	
	public function index(){
		//重定向浏览器
		header("Location: http://pic.meilimei.com.cn/upload/apk/meilishenqi.apk");
		//确保重定向后，后续代码不会被执行
		exit;
	}
	
	public function meilishenqi(){
		echo "正在跳转下载页面";
		//重定向浏览器
		header("Location: http://pic.meilimei.com.cn/upload/apk/meilishenqi.apk");
		//确保重定向后，后续代码不会被执行
		exit;
	}
}