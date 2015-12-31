<?php
/**   消费记录     按照时间倒叙排序
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-16
 * Time: 下午2:28
 * To change this template use File | Settings | File Templates.
 */
class fanli extends CI_Controller {
    private $notlogin = true,$uid='';

    public function __construct() {
        parent :: __construct();
        if ($this->wen_auth->get_role_id()==16) {
            $this->notlogin = false;
            $this->uid = $this->wen_auth->get_user_id();
        }else{
            redirect('');
        }
        $this->load->library('yisheng');
        $this->load->library('tehui');
        $this->load->model('privilege');
        $this->privilege->init($this->uid);
       if(!$this->privilege->judge('fanli')){
          die('Not Allow');
       }
    }

    public function index($param=''){
        error_reporting(E_ALL);

        $per_page = 16;
        $start = intval($param);
        $start == 0 && $start = 1;

        if ($start > 0)
           $offset = ($start -1) * $per_page;
        else
           $offset = $start * $per_page;

        $wheres = " where 1 ";
        if($this->input->get('mobile'))
            $wheres .= " and fanli.mobile ='".$this->input->get('mobile')."'";

        $data['data'] = $this->db->query("select fanli.*,users.username from fanli left join users on users.id=fanli.user_id $wheres order by fanli.id desc  LIMIT $offset, $per_page ")->result_array();
        $data['total_rows'] = $this->db->query("SELECT id FROM (`fanli`) ")->num_rows();
        $data['offset'] = $offset + 1;
        $data['preview'] = $start > 2 ? site_url('manage/fanli/index/' . ($start -1)) : site_url('manage/fanli/');
        $data['next'] = $offset + $per_page < $data['total_rows'] ? site_url('manage/fanli/index/' . ($start +1)) : site_url('manage/fanli/index/'.$start);

        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "fanli";
        $this->load->view('manage', $data);
    }
    //set rate
    public function Gfanli($param){
         if($this->input->post('submit')){
         	$id = intval($param);
            $row = $this->db->query("select user_id,amount,fan_status from fanli where id = $id")->row_array();
            $uid = $row['user_id'];   //获取uid
            $amount=$row['amount'];

            if($row['deal_state']==1){
                //已经审核过了
                echo json_encode(array('status'=>0,'msg'=>'不要重复审核'));  exit;
            }
            $this->db->query("update fanli set `fan_status` = 1,`deal_state` = 1 where id = $id");  //更改状态

            //获取利率
            $score = intval($this->input->post('jifen'))*$amount;
            $money = $amount*intval($this->input->post('xianjin'));

            //增加积分和余额
            $this->db->query("update users set amount=amount+$money where id=$uid");
            $this->db->query("update users set score=score+$score where id=$uid");
            redirect('manage/fanli');
         }else{
         	$data = array();
         	$data['id'] = $param;
         	$rate = $this->db->query("select * from fanli_rate where cur_type ='rmb'")->row_array();
            $cur_rate=explode(':',$rate['cur_rate']);
            $cur_money=explode(':',$rate['cur_money']);
            $data['rate'] = $cur_rate[1]/$cur_money[0];
            $data['moneyrate'] = $cur_money[1]/$cur_money[0];
         	$this->load->view("manage/fanli_G",$data);
         }
    }
    /**
     *  消费审核
     */
    public function ajax_audit(){
        $id = $this->input->post('id');
        //审核通过的时候
        if($this->input->post('type')==1) {     //审核不通过的时候
            $row = $this->db->query("select fan_status from fanli where id = $id")->row_array();

            if($row['fan_status']==1){
                //已经审核过了
                echo json_encode(array('status'=>0,'msg'=>'审核已通过不能再退回'));  die;
            }elseif($row['fan_status']==2){
                echo json_encode(array('status'=>0,'msg'=>'请不要重复退回'));  die;
            }

            $this->db->query("update fanli set fan_status = 2,`deal_state` = 1 where id = $id");
            echo json_encode(array('status'=>1,'msg'=>'退回成功'));
        }

    }


}
