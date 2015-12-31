<?php
require_once(__DIR__."/MyController.php");
class test extends MY_Controller {
  public function __construct() {
		parent :: __construct();
		$this->load->model('auth_bk');
	}

  public function index(){
     echo '<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="PHPEclipse 1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>title</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">
'. form_open_multipart('api/customerV2/sendcomment/878').'
  <input type="text" name="commentTo" value="111" />  <input type="text" name="contentid" value="111" />
<input type="text" name="comment" value="test"/><input type="text" name="type" value="topic"/>
 <input type="submit" name="name" value="测试"/>
'.form_close().'</body></html>';
  }
  public function t($param){
		$result['state'] = '000';
		if ($this->auth_bk->checktoken($param)) {
             if(true){

             }else{

             }
		} else {
			$result['state'] = '001';
		}
		echo json_encode($result);
	}
}

?>

