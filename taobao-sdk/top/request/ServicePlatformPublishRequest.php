<?php
/**
 * TOP API: taobao.service.platform.publish request
 * 
 * @author auto create
 * @since 1.0, 2015.06.08
 */
class ServicePlatformPublishRequest
{
	/** 
	 * 用于明确具体的schema
	 **/
	private $keys;
	
	/** 
	 * 具体业务数据
	 **/
	private $payload;
	
	private $apiParas = array();
	
	public function setKeys($keys)
	{
		$this->keys = $keys;
		$this->apiParas["keys"] = $keys;
	}

	public function getKeys()
	{
		return $this->keys;
	}

	public function setPayload($payload)
	{
		$this->payload = $payload;
		$this->apiParas["payload"] = $payload;
	}

	public function getPayload()
	{
		return $this->payload;
	}

	public function getApiMethodName()
	{
		return "taobao.service.platform.publish";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->keys,"keys");
		RequestCheckUtil::checkNotNull($this->payload,"payload");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
