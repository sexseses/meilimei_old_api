<?php
/**
 * TOP API: taobao.openim.kefu.add request
 * 
 * @author auto create
 * @since 1.0, 2015.04.20
 */
class OpenimKefuAddRequest
{
	/** 
	 * openim客服账号的名称
	 **/
	private $opNick;
	
	private $apiParas = array();
	
	public function setOpNick($opNick)
	{
		$this->opNick = $opNick;
		$this->apiParas["op_nick"] = $opNick;
	}

	public function getOpNick()
	{
		return $this->opNick;
	}

	public function getApiMethodName()
	{
		return "taobao.openim.kefu.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->opNick,"opNick");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
