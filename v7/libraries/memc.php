<?php
/*
 * WENRAN Filter
 */
class memc {
	private $C=null;
	function __construct() {
        $this->C = new Memcache;
        $this->C->connect('127.0.0.1', 11211) or die ("Could not connect");
    }
    public function S($k,$v,$time = 86400){
       $key = md5($k);
       $this->C->set($key, $v, MEMCACHE_COMPRESSED, $time);
    }
    public function G($k){
    	$key = md5($k);
        return $this->C->get($key);
    }
    function __destruct(){
		$this->C->close();
	}
}

?>
