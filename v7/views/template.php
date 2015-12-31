<?php
if(!isset($WEN_PAGE_TITLE)){
	$GDATA['WEN_PAGE_TITLE'] = '美丽美 : 美丽神器,美丽助手,美丽诊所. 最好的整形美容和微整形手机APP_医疗美容行业网站平台';
}else{
	$GDATA['WEN_PAGE_TITLE'] =$WEN_PAGE_TITLE.'_美丽美：美丽神器，美丽助手';
}
if(!isset($WEN_PAGE_KEYWORDS)){
	$GDATA['WEN_PAGE_KEYWORDS'] = '美丽美,美丽神器,美丽助手,美丽诊所,整形美容,美丽神器app,美丽助手app,美丽美app,微整形,整形,整容,整形医院,整形医生,整形app,哪里整形好,哪家整形医院好,美容app,整形对比图,美丽诊所app,哪个整形医生好,美容预约app,整形预约,美白针,瘦脸针,隆鼻,双眼皮,开眼角,激光脱毛,无痛脱毛,玻尿酸,黑脸娃娃,上海整形医院,北京整形医院,广州整形医院,深圳整形医院,抽脂,胶原蛋白注射,牙齿纠正,牙齿美白,除皱,隆胸';
}else{
	$GDATA['WEN_PAGE_KEYWORDS'] =$WEN_PAGE_KEYWORDS.',美丽神器，美丽助手';
}
if(!isset($WEN_PAGE_DESCRIPTION)){
	$GDATA['WEN_PAGE_DESCRIPTION'] = '美丽美,旗下APP:美丽神器,美丽助手,美丽诊所,美丽美. 最好的整形美容网站和手机APP. 汇集全国知名的微整形医院和整形医生,免费咨询微整形问题并预约整形医师.提供最新的整形服务价格和限时整形特惠信息. 在美丽社区大家还能讨论整形美容的心得,告诉你哪里的整形医院好,哪里的整形医生好.美丽热线:4006677245,欢迎咨询.';
}else{
	$GDATA['WEN_PAGE_DESCRIPTION'] =$WEN_PAGE_DESCRIPTION;
}
$this->load->view('theme/menu.php',$GDATA);
 if ($wen_msg =$this->session->flashdata('msg')) {
	 echo $wen_msg;
}
//print_r($phone);

if(isset($phone) && $phone){
	$data['phone'] = $phone;
	$this->load->view('theme/' . $message_element,$data['phone']);
}else{
	$this->load->view('theme/' . $message_element);
}

$this->load->view('theme/footer.php');
?>