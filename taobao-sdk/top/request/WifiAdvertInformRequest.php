<?php
/**
 * TOP API: taobao.wifi.advert.inform request
 * 
 * @author auto create
 * @since 1.0, 2015.06.08
 */
class WifiAdvertInformRequest
{
	/** 
	 * 广告号
	 **/
	private $advertId;
	
	/** 
	 * 广告名称
	 **/
	private $advertName;
	
	/** 
	 * 备注
	 **/
	private $comment;
	
	/** 
	 * 设备标识
	 **/
	private $equipId;
	
	/** 
	 * 订单号
	 **/
	private $orderId;
	
	/** 
	 * 奖励类型
	 **/
	private $payType;
	
	/** 
	 * 用户奖励
	 **/
	private $point;
	
	/** 
	 * 价格
	 **/
	private $price;
	
	/** 
	 * 时间戳
	 **/
	private $timeStamp;
	
	/** 
	 * 用户行为
	 **/
	private $userAction;
	
	/** 
	 * 用户标识
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setAdvertId($advertId)
	{
		$this->advertId = $advertId;
		$this->apiParas["advert_id"] = $advertId;
	}

	public function getAdvertId()
	{
		return $this->advertId;
	}

	public function setAdvertName($advertName)
	{
		$this->advertName = $advertName;
		$this->apiParas["advert_name"] = $advertName;
	}

	public function getAdvertName()
	{
		return $this->advertName;
	}

	public function setComment($comment)
	{
		$this->comment = $comment;
		$this->apiParas["comment"] = $comment;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setEquipId($equipId)
	{
		$this->equipId = $equipId;
		$this->apiParas["equip_id"] = $equipId;
	}

	public function getEquipId()
	{
		return $this->equipId;
	}

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function setPayType($payType)
	{
		$this->payType = $payType;
		$this->apiParas["pay_type"] = $payType;
	}

	public function getPayType()
	{
		return $this->payType;
	}

	public function setPoint($point)
	{
		$this->point = $point;
		$this->apiParas["point"] = $point;
	}

	public function getPoint()
	{
		return $this->point;
	}

	public function setPrice($price)
	{
		$this->price = $price;
		$this->apiParas["price"] = $price;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setTimeStamp($timeStamp)
	{
		$this->timeStamp = $timeStamp;
		$this->apiParas["time_stamp"] = $timeStamp;
	}

	public function getTimeStamp()
	{
		return $this->timeStamp;
	}

	public function setUserAction($userAction)
	{
		$this->userAction = $userAction;
		$this->apiParas["user_action"] = $userAction;
	}

	public function getUserAction()
	{
		return $this->userAction;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "taobao.wifi.advert.inform";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->advertId,"advertId");
		RequestCheckUtil::checkNotNull($this->equipId,"equipId");
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
		RequestCheckUtil::checkNotNull($this->payType,"payType");
		RequestCheckUtil::checkNotNull($this->point,"point");
		RequestCheckUtil::checkNotNull($this->timeStamp,"timeStamp");
		RequestCheckUtil::checkNotNull($this->userAction,"userAction");
		RequestCheckUtil::checkNotNull($this->userId,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
