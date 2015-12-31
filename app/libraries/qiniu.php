<?php
/*
 * WENRAN Filter
 */
require_once('qiniu/io.php');
require_once('qiniu/rs.php');

class qiniu {

    private $bucket = "";
    private $deadline = 0;


	function __construct() {
        $this->bucket = "meilimei";
        $this->deadline = time() + 86400*1500;
    }

    public function getToken(){

        $client = new Qiniu_MacHttpClient(null);
        $putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
        $putPolicy->Expires = $this->deadline;
        $putPolicy->CallbackBody = 'key=$(key)&hash=$(etag)';
        $upToken = $putPolicy->Token(null);
        return $upToken;
    }

}

?>
