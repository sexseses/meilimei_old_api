<?php
class chongzhi extends CI_Controller {
	private $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		}
		$this->load->model('banks_model');
	}

	public function process() {
		if($this->notlogin)redirect('user/login');
		$out_trade_no = date('Ymd').mt_rand() ;
		$subject = '账户充值';
		$body = '美丽诊所账户充值！';
		$total_fee = $this->input->post('total_fee');
		$billId = '';
		if($total_fee<=0 || $total_fee>10000){
		   $this->session->set_flashdata('msg', $this->common->flash_message('error', '无效充值金额!'));
           redirect('counselor/chongzhi');
		}else{
			//deal in web
           $datas['uid'] = $this->uid;
           $datas['amout'] = $total_fee;
           $datas['trade_no'] = $out_trade_no;
           $datas['state'] = 1;
           $datas['remark'] = '推广账户充值';
           $datas['cdate'] = time();
           $billId = $this->common->insertData('bill',$datas);
           if(!$billId){
           	$this->session->set_flashdata('msg', $this->common->flash_message('error', '系统繁忙!'));
           	 redirect('counselor/chongzhi');
           }
		}
		//扩展功能参数——默认支付方式//
		$defaultbank = '';
		if ($this->input->post('pay_bank') == 'directPay') {
			$paymethod = 'directPay';
		} else {
			$paymethod = 'bankPay';
			$defaultbank = $this->input->post('pay_bank');
		}

		//扩展功能参数——防钓鱼//

		//防钓鱼时间戳
		$anti_phishing_key = '';
		//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		$exter_invoke_ip = '';
		//注意：
		//1.请慎重选择是否开启防钓鱼功能
		//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
		//示例：
		//$exter_invoke_ip = '202.1.1.1';
		//$ali_service_timestamp = new AlipayService($aliapy_config);
		//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数

		//扩展功能参数——其他//


		$show_url = base_url();
		//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$extra_common_param = $billId . '@' . $this->uid . '@' . $out_trade_no;

		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type = ""; //提成类型，该值为固定值：10，不需要修改
		$royalty_parameters = "";
		//注意：
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		//示例：
		//royalty_type 		= "10"
		//royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"

		/************************************************************/

		//构造要请求的参数数组
$parameter = array(
		"service"			=> "create_direct_pay_by_user",
		"payment_type"		=> "1",

		"partner"			=> trim($this->banks_model->aliapy_config['partner']),
		"_input_charset"	=> trim(strtolower($this->banks_model->aliapy_config['input_charset'])),
        "seller_email"		=> trim($this->banks_model->aliapy_config['seller_email']),
        "return_url"		=> trim($this->banks_model->aliapy_config['return_url']),
        "notify_url"		=> trim($this->banks_model->aliapy_config['notify_url']),

		"out_trade_no"		=> $out_trade_no,
		"subject"			=> $subject,
		"body"				=> $body,
		"total_fee"			=> $total_fee,

		"paymethod"			=> $paymethod,
		"defaultbank"		=> $defaultbank,

		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,

		"show_url"			=> $show_url,
		"extra_common_param"=> $extra_common_param,

		"royalty_type"		=> $royalty_type,
		"royalty_parameters"=> $royalty_parameters
);
		//构造纯网关接口
		$html_text = $this->banks_model->create_direct_pay_by_user($parameter);
		echo $html_text;
	}
	//Alipay lib
	private function topay($data) {
		/**
		 * 从 Model 获取参数设置
		 */
		$partner = $this->Alipay_Model->partner;
		$security_code = $this->Alipay_Model->security_code;
		$seller_email = $this->Alipay_Model->seller_email;
		$_input_charset = $this->Alipay_Model->_input_charset;
		$transport = $this->Alipay_Model->transport;
		$notify_url = $this->Alipay_Model->notify_url;
		$return_url = $this->Alipay_Model->return_url;
		$show_url = $this->Alipay_Model->show_url;
		$sign_type = $this->Alipay_Model->sign_type;
		$mainname = $this->Alipay_Model->mainname;
		$antiphishing = $this->Alipay_Model->antiphishing;
		$gateway = $this->Alipay_Model->gateway;

		$out_trade_no = date('Ymd') . local_to_gmt(now()); // 唯一订单号，这里生成了一个 Unix 时间的例子
		$subject = $data['title']; // 订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$body = $data['description']; // 订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里。
		$total_fee = $data['amount']; // 订单总金额，显示在支付宝收银台里的“应付总额”里，可以有两位小数。

		/**
		 * 扩展参数
		 */
		$pay_mode = "bankPay";
		if ($pay_mode == "directPay") {
			$paymethod = "directPay"; //默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
			$defaultbank = "";
		} else {
			$paymethod = "bankPay"; //默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
			$defaultbank = $pay_mode; //默认网银代号，代号列表见http://club.alipay.com/read.php?tid=8681379
		}

		/**
		 * 防钓鱼
		 */
		$encrypt_key = ''; //防钓鱼时间戳，初始值
		$exter_invoke_ip = ''; //客户端的IP地址，初始值
		if ($antiphishing == 1) {
			$encrypt_key = $this->Alipay_Model->query_timestamp($partner);
			$exter_invoke_ip = ''; //获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		}

		/**
		 * 其它
		 */
		$extra_common_param = $data['custom']; //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$buyer_email = $data['buyer_email']; //默认买家支付宝账号

		/**
		 * 构造请求数组
		 */
		$parameter = array (
				"service" => "create_direct_pay_by_user", //接口名称，不需要修改
		"payment_type" => "1", //交易类型，不需要修改

	"partner" => $partner,
			"seller_email" => $seller_email,
			"return_url" => $return_url,
			"notify_url" => $notify_url,
			"_input_charset" => $_input_charset,
			"show_url" => $show_url,
			"out_trade_no" => $out_trade_no,
			"subject" => $subject,
			"body" => $body,
			"total_fee" => $total_fee,
			"paymethod" => $paymethod,
			"defaultbank" => $defaultbank,
				// 防钓鱼
	"anti_phishing_key" => $encrypt_key,
			"exter_invoke_ip" => $exter_invoke_ip,
				// 分润(若要使用，请取消下面两行注释)
		//"royalty_type"   => "10",	  //提成类型，不需要修改
		//"royalty_parameters" => "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二",
	/**
	 * 提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
	 * 提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
	 */
				// 自定义超时(若要使用，请取消下面一行注释)。该功能默认不开通，需联系客户经理咨询
		//"it_b_pay" => "1c", //超时时间，不填默认是15天。八个值可选：1h(1小时),2h(2小时),3h(3小时),1d(1天),3d(3天),7d(7天),15d(15天),1c(当天)
		// 自定义参数
	"buyer_email" => $buyer_email,
			"extra_common_param" => $extra_common_param
		);

		// GET 方式传递，默认使用
		//$url = $this->Alipay_Model->create_url($parameter);
		// echo "<script>window.location =\"$url\";</script>"; // 输出跳转链接
		//POST 方式传递，如需使用此方式，请去掉下列代码注释并注释 GET 传递方式
		$payform_html = $this->Alipay_Model->build_postform($parameter);
		echo $payform_html; // 输出支付表单
	}
	/**
	* 同步支付结果接收
	*/
	function return_page() {
		/**
		 * 从 Model 获取参数设置
		 */
		$partner = $this->Alipay_Model->partner;
		$security_code = $this->Alipay_Model->security_code;
		$_input_charset = $this->Alipay_Model->_input_charset;
		$transport = $this->Alipay_Model->transport;
		$sign_type = $this->Alipay_Model->sign_type;

		/**
		 * 验证接收内容
		 */
		$verify_result = $this->Alipay_Model->return_verify();
		if ($verify_result) {
			//验证成功，获取支付宝的反馈参数
			$config['uri_protocol'] = "PATH_INFO";
			parse_str($_SERVER['QUERY_STRING'], $_POST);
			$trade = $_POST['out_trade_no']; //获取支付宝传递过来的订单号
			$total = $_POST['total_fee']; //获取支付宝传递过来的总价格
			$You_trade_status = "1"; //获取商户数据库中查询得到该笔交易当前的交易状态
			/**
			 * 这里假设：
			 * You_trade_status="0";表示订单未处理；
			 * $You_trade_status="1";表示交易成功（TRADE_FINISHED/TRADE_SUCCESS）；
			 */
			if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') { //交易成功结束
				// 放入订单交易完成后的数据库更新程序代码，附带一些调试代码
				// 请务必保证echo输出的信息只有 success，以便支付宝记录结果
				// 为了保证不被重复发送通知，或重复执行数据库更新程序，请判断该笔交易状态是否是订单未处理状态
				if ($You_trade_status < 1) {
					//根据订单号更新订单，把商户数据库订单处理成交易成功
				}
				echo "success";
				// echo "支付成功！订单号：".$trade."支付金额：".$total;
			} else {
				//其他状态判断。普通即时到帐中，其他状态不用判断，直接打印 success。
				echo "success";
				// echo "支付成功！订单号：".$trade."支付金额：".$total;
			}
		} else {
			// 验证失败
			echo "fail";
		}
	}

	public function payreturn() {
		if ($this->banks_model->notify_verify()) {
			$custom = $_POST['extra_common_param'];
			$data = array ();
			$data = explode('@', $custom);
			$out_trade_no	= $_POST['out_trade_no'];	    //获取订单号
            $trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
            $total_fee		= $_POST['total_fee'];			//获取总价格
			$result = $this->common->getTableData('bill', array (
				'id' => $data['0'],
				'trade_no' => $out_trade_no,
				'uid' => $data['1']
			));
			if ($result->num_rows() == 0 or $result->row()->amout != $total_fee) {
				echo 'error';
			} else {
				$updata['trade_no'] = $trade_no;
				$updata['state'] = 8;
				$this->common->updateTableData('bill', $data['0'], '', $updata);
				switch ($total_fee) {
					case 100:
                        $total_fee+=10;
						break;
                    case 500:
                        $total_fee+=100;
						break;
					case 1000:
                        $total_fee+=300;
						break;
				}
				$sql = 'UPDATE users SET amount = amount+'.$total_fee.' WHERE id='.$data['1'];
		        $this->db->query($sql);
				echo 'success';
			}
		} else {
			echo 'error';
		}
	}
	public function success() {
		$this->session->set_flashdata('msg',$this->common->flash_message('success', '付款成功！') );
		redirect("user/dashboard");
	}

}
?>
