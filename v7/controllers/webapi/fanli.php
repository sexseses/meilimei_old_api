<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/**
 * WERAN Api doctor Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */

class fanli extends CI_Controller {
	private $notlogin = true;
	private $max_money = 0;
	public function __construct() {
		parent :: __construct();
		$this->max_money = 500;
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		} else {
			$this->notlogin = true;
		}
		$this->load->library('tehui');
		$this->load->model('fanli_consume');
		$this->load->model('auth');
	}
	public function index() {

		$data = $this->fanli_consume->get_list();
		return $data;

	}

	public function upload_process() {   //添加返利信息
        if(0==$_FILES['image']['size']){    //上传文件大小 $_FILES['image']['size']
            $code['state'] = "003";  //请选择图片
            $code['data'] = " 请选择文件!";
            echo json_encode($code);die;
        }

        if(5242880<$_FILES['image']['size']){    //上传文件大小 $_FILES['image']['size']
            $code['state'] = "004";  //请选择图片
            $code['data'] = " 上传文件大小不能超过5MB";
            echo json_encode($code);die;
        }

        $exif   = getimagesize($_FILES['image']['tmp_name']);//$_FILES['image']['tmp_name']文件上传后服务器存储的临时文件名 getimagesize获得大小
        $formats= array('image/png','image/jpeg','image/gif','image/x-ms-bmp');

        if(!in_array($exif['mime'], $formats) OR $_FILES['image']['tmp_name']==''){ //$exif['mime']格式类型 也可以用$_FILES['image']['type']不过这个就不是临时文件了。
            $code['state'] = "005";  //请选择图片
            $code['data'] = "只支持 jpg、gif、png、bmp 格式!";
            echo json_encode($code);exit;
        };

        $uploads_dir = realpath(APPPATH . '../upload/fanli');  //上传路径
		$file = $this->fanli_consume->upload_process($uploads_dir);  // 上传消费凭证图片
		//echo json_encode($file);die;
		$code['state'] = "000";   //状态设为成功
        $code['data'] = " file upload successfully!";
		if ($file) {
			$data['mobile'] = $this->input->post('mobile');
			$data['consum_time'] = $this->input->post('consum_time');
			$data['amount'] = $this->input->post('amount');
			$data['remark'] = $this->input->post('remark');
			$data['user_name'] = $this->input->post('user_name');
			$data['image'] = $file;
			$data['user_id'] = $this->input->post('uid');
			//echo json_encode($data);die;
			if (!$this->fanli_consume->putContents($data)) {  //数据插入失败
				$code['state'] = "002";
				$code['data'] = " data put  failed!";
			}
		} else {
			$code['state'] = "001";  //错误
			$code['data'] = " file upload failed!";
		}
		//
		echo json_encode($code);

	}
	public function get_fanli_list($param) {        //获取用户账户信息和返利列表
		$res['state'] = '001';
		if ($this->auth->checktoken($param) && !$this->notlogin) { //验证令牌通过并且用户已经登录
			$res['state'] = '000';  //成功标志
			$res['data'] = '';

            $row  =$this->db->get_where('users',array('id'=>$this->uid))->row_array();
            $res['money'] = $row['amount'];   //用户目前的余额
            $res['score'] = $row['jifen'];   //用户目前的积分

			$fan_rate = $this->db->get_where('fanli_rate', array (
				'cur_type' => 'rmb'
			))->result_array();       //获取比率，包括返积分比率，消费返现设置。
			$page = $this->input->post('page') ? $this->input->post('page') : 1;     //当前页
			$perpage = 5;   //每页记录数
			//$tehui_score = $this->tehui->getUserInfo($this->uid);
			$sql = "select u.*,f.consum_time,f.fan_status,f.amount,f.id as fanli_id from fanli as f left join users as u  on f.user_id=u.id where u.id=" . $this->uid . "  order by fanli_id desc limit " . ($page-1)*$perpage . " ," . $perpage . " ";
			$tmp = $this->db->query($sql)->result_array();

			$result = array();

				foreach ($tmp as $v) {
					//$v['score'] = $v['score'] + $tehui_score[0]['score'];
					if (!empty ($fan_rate[0]['cur_rate'])) {
						$rate = explode(":", $fan_rate[0]['cur_rate']);
						$fan_score = $v['amount'] * $rate[1];
						$v['fan_score'] = $fan_score;

					}
					if (!empty ($fan_rate[0]['cur_money'])) {
						$rate = explode(":", $fan_rate[0]['cur_money']);
						$fan_money = intval($v['amount'] / $rate[0]);
						$v['fanli'] = $fan_money;
					}
					$result[] = $v;
				}
			// $result = array();
			$res['data'] = $result;

		}
 		echo json_encode($res);
	}
	public function get_account_info($param) {
		$res['state'] = '000';
		$res['data'] = '';
		if ($this->auth->checktoken($param) && !$this->notlogin) {
			if (!($user_id = $this->input->post('uid'))) {
				$res['state'] = '001';
				$res['data'] = 'uid is required!';
			} else {
				$type = $this->input->post('type');
				$res['data'] = $this->db->query("select * from  `account` where user_id = " . $user_id . " and ac_type='" . $type . "'")->result_array();
				$res['max_money'] = $this->max_money;
				$res['state'] = '000';
			}

		} else {
			$res['state'] = '003';
			$res['data'] = 'Please login!';
		}
		echo json_encode($res);
	}
	public function putRecords() {

		$res['state'] = '000';
		$res['data'] = '';
		$card_num = $this->input->post('card_num', true);
		$bank = $this->input->post('bank', true);
		$ac_name = $this->input->post('ac_name', true);
		$amount = $this->input->post('amount', true);
		$type = $this->input->post('type', true);
        $user_id = $this->input->post('uid', true);

        $row = $this->db->query("select amount from users where id = $user_id")->row_array();
        if($amount > $row['amount'] ){
            $res['state'] = '008';
            $res['data'] = '';
            echo json_encode($res);die;
        }
		if (!$amount) {   //提现金额不能为空
			$res['state'] = '001';
			$res['data'] = 'amount is required!';
		} else
			if (!$card_num) {  //银行卡号不能为空
				$res['state'] = '001';
				$res['data'] = 'card_num is required!';
			} else
				if (!$bank && $type == 1) {  //银行名称不能为空
					$res['state'] = '001';
					$res['data'] = 'bank is required!';
				} else
					if (!$ac_name && $type == 1) {  //开户名不能为空
						$res['state'] = '001';
						$res['data'] = 'ac_name is required!';
					} else {
						$data['acc_id'] = 0;
						$data['amount'] = $amount;
						//$this->db->trans_begin();
						$this->db->where('user_id', $user_id);
						$this->db->where('card_num', $card_num);
						if (intval($type) == 1) {
							$this->db->where('bank', $bank);
						}
						$account = $this->db->count_all_results('account');
						$data1['card_num'] = $card_num;
						$data1['user_id'] = $user_id;
						if (intval($type) == 1) {
							$data1['bank'] = $bank;
							$data1['ac_name'] = $ac_name;
						}

						$data1['ac_type'] = $type;
						$ac_insert = 0;
						$ac_rec_insert = 0;
                        //var_dump($account);

						if ($account <= 0) {
							$this->db->insert('account', $data1);
							$ac_insert = $this->db->insert_id();
							$data['acc_id'] = $ac_insert;
						} else {
                            $this->db->select('id');
                            $rs = $this->db->get("account")->result_array();
							$data['acc_id'] = $rs[0]['id'];
						}
						$this->db->insert('account_records', $data);
						$ac_rec_insert = $this->db->insert_id();
						if ($ac_rec_insert && $ac_insert) {
							//扣除用户余额
							$money = "money - $amount";
							$this->db->query("UPDATE `users` SET amount = " . $money . " where id=" . $user_id);
						}

						$res['data'] = 'success';
						$res['state'] = '000';
					}
		echo json_encode($res);

	}
}
?>