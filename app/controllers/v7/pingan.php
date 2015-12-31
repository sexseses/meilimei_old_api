<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
 
 
class Pingan extends CI_Controller {
	public function __construct() {  
		parent :: __construct();
		//报告所有错误
		error_reporting(E_ALL);
		ini_set("display_errors","On");
		header("Content-type: text/html; charset=utf-8");
	} 
	
	public function index(){
		echo "aadb";
	}
	/** 验证码输入页面
	 * @param string $vars 提交的数据
	 * @return string
	 */	
	public function captcha(){
		$captcha = $this->input->post('captcha');
		if($captcha == '123456'){
			echo true;
		}else{
			echo false;
		}

	}	
	/** 保单输入页面
	 * 
	 */
	public function addpolicy(){
		print_r($this->input->post());
	}
	/**
	 * @param string $vars 提交的数据
	 * @return string 
	 */
	public function makeXml($vars){
		$relativefilepath= realpath(APPPATH.'../').'/pingan/'. date ( 'Ymd' ) .'/';exit;
		$filename = time().".xml";
		define ( "XML_NEW_SAVE_PATH", $relativefilepath . $filename );
		if (! file_exists ( $relativefilepath )) {
			if (!mkdir ( $relativefilepath, 0777, true )) {
				echo "mkdir failed";
			}
		}
		if($handle=fopen(XML_NEW_SAVE_PATH,'wr')){
			fwrite($handle, $vars);
			fclose($handle);
		};

	}
	public function test(){
		var_dump($this->curl_post_ssl(1,1,30,1));exit;
		$vars= '<?xml version="1.0" encoding="GBK"?>
<abbsParamXml>
	<Header>
		<documentId>001</documentId>
		<profileRequest>01</profileRequest>
		<function>policyIssuing</function>
		<from>11-24-0001</from>
		<to>11-11-1111</to>
	</Header>
	<Request>
		<policyIssuing>
			<paramList>
				<parameter>
					<businessID>P0130G1002012041600006</businessID>
					<billNo>YLLC0200000000010014</billNo>
					<invoiceNo>YLLC0200000000010015</invoiceNo>
					<policyNo>GP02000000630404</policyNo>
					<name>单证正确</name>
					<productNo>P0130B13</productNo>
					<agencyNo>SHAA-00001</agencyNo>
					<idType>9</idType>
					<idNo>12340272021306</idNo>
					<birthDate>19701104</birthDate>
					<gender>9</gender>
					<units>1</units>
					<paymentType>C</paymentType>
					<effDate>20130501000000</effDate>
					<matuDate>20130510000000</matuDate>
				</parameter>
			</paramList>
		</policyIssuing>
	</Request>
</abbsParamXml>';
		$this->makeXml($vars);
	}
	/** 
	 * @name ssl Curl Post数据 
	 * @param string $url 接收数据的api 
	 * @param string $vars 提交的数据 
	 * @param int $second 要求程序必须在$second秒内完成,负责到$second秒后放到后台执行 
	 * @return string or boolean 成功且对方有返回值则返回 
	 */
	function curl_post_ssl($url, $vars, $second=30,$header=array()) 
	{ 
		//https://eairiis-stgdmz.paic.com.cn/invoke/wm.tn/receive
		$url = "https://eairiis-stgdmz.paic.com.cn/invoke/wm.tn/receive";
		//$vars =getcwd().'/pingan/'.'20141010'.'/'.'P0130G1.xml';								
		$vars = <<<EOF
		<?xml version="1.0" encoding="GBK"?>
<abbsParamXml>
	<Header>
		<documentId>001</documentId>
		<profileRequest>01</profileRequest>
		<function>policyIssuing</function>
		<from>11-24-0001</from>
		<to>11-11-1111</to>
	</Header>
	<Request>
		<policyIssuing>
			<paramList>
				<parameter>
					<businessID>P0130G1002012041600006</businessID>
					<billNo>YLLC0200000000010014</billNo>
					<invoiceNo>YLLC0200000000010015</invoiceNo>
					<policyNo>GP02000000630404</policyNo>
					<name>单证正确</name>
					<productNo>P0130B13</productNo>
					<agencyNo>SHAA-00001</agencyNo>
					<idType>9</idType>
					<idNo>12340272021306</idNo>
					<birthDate>19701104</birthDate>
					<gender>9</gender>
					<units>1</units>
					<paymentType>C</paymentType>
					<effDate>20130501000000</effDate>
					<matuDate>20130510000000</matuDate>
				</parameter>
			</paramList>
		</policyIssuing>
	</Request>
</abbsParamXml>
EOF;

		$ch = curl_init();
		$header = array("Content-type: text/xml");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义请求类型	
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        
        curl_setopt($ch, CURLOPT_CAINFO, getcwd().'/pingan_lce/'.'ABBS-NET-TEST.pem');
//         curl_setopt($ch, CURLOPT_SSLCERT, getcwd().'/pingan_lce/'.'PAICSTG.pem');
//         curl_setopt($ch, CURLOPT_SSLCERTPASSWD, '123456');
        
//         curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
//         curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/pingan_lce/'.'ABBS-NET-TEST.pem');
//         curl_setopt($ch,CURLOPT_SSLCERTPASSWD,'123456');
//         curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
//         curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/pingan_lce/'.'PAICSTG.pem');
//         curl_setopt($ch,CURLOPT_SSLCERTPASSWD,'');
		$data = curl_exec($ch); 
		
		if(curl_errno($ch)){//出错则显示错误信息
			echo curl_errno($ch);
			var_dump(curl_error($ch));	
		}
		curl_close($ch);
	}

	/**
	 * @name 拼凑投保报文toubao
	 * @param array $arr
	 * @return string $str
	 */
	public function toubao($arr){
		$str = '<?xml version="1.0" encoding="GBK"?>'."
<abbsParamXml>
	<Header>
		<TRAN_CODE>ABBS01</TRAN_CODE>
		<documentId>001</documentId>
		<profileRequest>01</profileRequest>
		<function>policyIssuing</function>
		<from>11-24-0001</from>
		<to>11-11-1111</to>
	</Header>
	<Request>
		<policyIssuing>
			<paramList>
				<parameter>
					<policyNo>GP02009990383187</policyNo>
					<name>测试姓名1wj</name>
					<productNo>246A2</productNo>
					<agencyNo>SHAA-00009</agencyNo>
					<idType>3</idType>
					<idNo>620523198411041697</idNo>
					<birthDate>19801104</birthDate>
					<gender>M</gender>
					<occupation>00</occupation>
					<!--被保人职业-->
					<nationality>156</nationality>
					<!--被保人国藉-->
					<idTermDate>20201231</idTermDate>
					<!--证件有效期-->
					<applicantName>和平</applicantName>
					<applicantIdType>1</applicantIdType>
					<applicantIdNo>140102560602067</applicantIdNo>
					<applicantBirthDate>19560602</applicantBirthDate>
					<applicantGender>M</applicantGender>
					<applicantOccupation>00</applicantOccupation>
					<applicantNationality>156</applicantNationality>
					<applicantIdTermDate>20201231</applicantIdTermDate>
					<units>1</units>
					<effDate>20090223</effDate>
					<matuDate>20090303</matuDate>
					<contractNo>20090303</contractNo>
					<flightNo>CA002</flightNo>
					<sumIns>100000.00</sumIns>
					<benLevel>11</benLevel>
					<destination>美国 </destination>
					<businessID>000001</businessID>
					<operatorNo>操作员工号</operatorNo>
					<issuingMode>S</issuingMode>
                    <certificateNo>PP16A000004444</certificateNo>
					<takeoffTime> 19801104132400</takeoffTime>
					<seatNo>2001</seatNo>
					<remark/>
					<mobile>13817650067</mobile>
					<isSendSMS>1</isSendSMS>
					<paymentType>K</paymentType>
					<paymentAcctId>agency_account@beijing.com</paymentAcctId>
					<paymentVerifyId>123</paymentVerifyId>
					< groupNo>123</groupNo>
<invoiceNo>A3200100741100054500</invoiceNo>
				</parameter>
			</paramList>
		</policyIssuing>
	</Request>
</abbsParamXml>";
	}
	public function addEvent(){
		$event_name = $this->input->post('event_name');
		$event_content = $this->input->post('event_content');
		$event_mobile = $this->input->post('event_mobile');
		$user_name = $this->input->post('user_name');
		$event_time = time();

		$data_arr = array(
			'event_name' => $event_name,
			'event_content' => $event_content,
			'event_mobile' => $event_mobile,
			'user_name' => $user_name,
			'event_time' => $event_time
		);
		

		if($this->db->insert('mlm_event', $data_arr)){
			echo TRUE;
		}else{
			echo FALSE;
		}
		
	}

}
?>