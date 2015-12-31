<?php
$this->load->view('manage/menu.php');
 if ($wen_msg =$this->session->flashdata('msg')) {
	 echo $wen_msg;
}
$this->load->view('manage/' . $message_element);
$this->load->view('manage/footer.php');
?>