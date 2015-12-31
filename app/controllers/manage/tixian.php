<?php
/**   提现记录     按照时间倒叙排序
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-16
 * Time: 下午2:28
 * To change this template use File | Settings | File Templates.
 */
class tixian extends CI_Controller {
    private $notlogin = true,$uid='';

    public function __construct() {
        parent :: __construct();
        if ($this->wen_auth->get_role_id()==16) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        }else{
            redirect('');
        }
        $this->load->model('privilege');
        $this->privilege->init($this->uid);
       if(!$this->privilege->judge('tixian')){
          die('Not Allow');
       }
    }

    public function index($param=''){
       error_reporting(E_ALL);

       $per_page = 16;
       $start = intval($param);
       $start == 0 && $start = 1;

        $wheres = "where 1 ";
       if($this->input->get('card_num'))
           $wheres .= " and a.card_num = '".$this->input->get('card_num')."'";
       if($this->input->get('phone'))
           $wheres .= " and users.phone = '".$this->input->get('phone')."'";

       if ($start > 0)
           $offset = ($start -1) * $per_page;
       else
           $offset = $start * $per_page;

       $data['data'] = $this->db->query("select a.*,ar.amount,users.phone from account a left join  account_records ar on a.id = ar.acc_id left join users on users.id=a.user_id   $wheres order by a.id desc LIMIT $offset, $per_page ")->result_array();
       $data['total_rows'] = $this->db->query("SELECT a.id FROM (`account` a) left join account_records ar on a.id= ar.acc_id")->num_rows();
       $data['offset'] = $offset + 1;
       $data['preview'] = $start > 2 ? site_url('manage/tixian/index/' . ($start -1)) : site_url('manage/tixian/');
       $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/tixian/index/' . ($start +1)) : site_url('manage/tixian/index/'.$start);

       $data['notlogin'] = $this->notlogin;
       $data['message_element'] = "tixian";
       $this->load->view('manage', $data);
    }
}
