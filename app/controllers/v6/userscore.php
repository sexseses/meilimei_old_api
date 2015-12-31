<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * Api Userscore Controller Class
 * @author		kingsley
 * @subpackage	Controllers
 */
require_once(__DIR__."/MyController.php");
class Userscore extends CI_Controller {
    public function __construct() {
        error_reporting(E_ALL);
        ini_set("display_errors","On");
        parent :: __construct();
//        if ($this->wen_auth->is_logged_in()) {
//             $this->notlogin = false;
//             $this->uid = $this->wen_auth->get_user_id();
//         } else {
//             $this->notlogin = true;
//         }
        //$this->load->helper('cookie');
        $this->load->library('user_score');
  
    }
    
    public function index(){
        echo "index";
    }
    
    public function testScore(){
        $user_id = 13;
        $user_score = new user_score();
        echo $user_score->invitation_friend($user_id);
        echo $user_score->invitation_code($user_id);
        
    }
}