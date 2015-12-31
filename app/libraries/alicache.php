<?php
/*
 * WENRAN Filter
 */
class alicache {
	private $C=null;
	function __construct() {
        $this->C = new Memcached;  //声明一个新的memcached链接
        $this->C->setOption(Memcached::OPT_COMPRESSION, false); //关闭压缩功能
        $this->C->setOption(Memcached::OPT_BINARY_PROTOCOL, true); //使用binary二进制协议
        $this->C->addServer('2244ff762cf711e4.m.cnhzalicm10pub001.ocs.aliyuncs.com', 11211); //添加OCS实例地址及端口号
        $this->C->setSaslAuthData('2244ff762cf711e4', 'Oy2Xiv_YqvZo8_JlfmIy'); //设置OCS帐号密码进行鉴权
        
    }
    public function set($k,$v,$time = 600){;
       $key = md5($k);
       $this->C->set($key, $v,$time);
    }
    public function get($k){
    	$key = md5($k);
        return $this->C->get($key);
    }
    function __destruct(){
		$this->C->quit();
	}
}

?>
