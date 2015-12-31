<?php
class notice extends CI_Controller {
	public function __construct() {
		parent :: __construct();
	}
	public function p404() {
        $this->load->view('404.php');
	}
}
?>
