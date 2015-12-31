<?php
header("Content-type: text/html; charset=utf-8");
include "TopSdk.php";
//将下载SDK解压后top里的TopClient.php第8行$gatewayUrl的值改为沙箱地址:http://gw.api.tbsandbox.com/router/rest,
//正式环境时需要将该地址设置为:http://gw.api.taobao.com/router/rest
error_reporting(2047);
ini_set("display_errors","On");

 

//实例化TopClient类
$c = new TopClient;

/*
沙箱
*/
// $c->appkey = "1023187000";
// $c->secretKey = "sandbox5105a7935718a8c3452825568";
// $sessionKey= "61012213cb04eff8cc129f5bb56e514a6c528f53b9bfddb3651882532";   //如沙箱测试帐号sandbox_c_1授权后得到的sessionkey
//实例化具体API对应的Request类

/*
正式
*/
$c->appkey = "23187000";
$c->secretKey = "83afdc24a7d90013cdfdf8e1873566ab";
$sessionKey= "6201b118d7870f0d602ZZ2d0778d43ccce4ba100febf2212011261288"; 

$req = new PictureUploadRequest;
$req->setPictureCategoryId(123);
//附件上传的机制参见PHP CURL文档，在文件路径前加@符号即可
$req->setImg("@/Users/kingsley/Pictures/pic/B.jpg");
$req->setImageInputTitle("B.jpg");
$req->setTitle("美丽少女");
$req->setClientType("client:computer");


//发布服务者
/*
$req = new ServicePlatformPublishRequest;
$keyarr = array('scene' => 'server.service_life.importServers');
 
$server = array(
			"outerId" => "id001",
			"name" => "蒋中川",
			"checkStatus" => 0,
			"photo" => "abc.jpg",
			"phone" => "13566778899,13322994455",
			"organization" => "美丽神器",
			"email" => "caspershi@126.com",
			"service_time" => "8:00-9:00", // 可服务时间描述
            "workingLife" => 5, // 工作年限
            "properties" => "水平高，韩国行业认证"// 个性特征，逗号分隔。
	);

 $playlodarr = array(
	"servers" => array($server);
 );

$playlodjson = json_encode($playlodarr);
//echo $playlodjson;
*/

//上架产品
// $req = new ServicePlatformPublishRequest;
// $keyarr = array('scene' => 'model-service.lightservice.publish');


// // $playlodarr = array(
// // 	"serviceId" => "service0001",
// // 	"serviceTitle" => "水光针",
// // 	"status" => -5,
// // 	"workerIds" => array("2207"),
// // 	"categoryId" => 123,
// // 	"description" => "xxxxdfdsafdsxcvc"
// // 	"image" => array("b.jpg"),
// // 	"price" => array("fixedPrice" => "1280.00"),
// // 	"quantity" => 12
// //  );




// $keyarrjson = json_encode($keyarr);
// $playlodjson = json_encode($playlodarr);
// $req->setKeys($keyarrjson);
// $req->setPayload($playlodjson);
$resp = $c->execute($req, $sessionKey);

echo "result:";
print_r($resp);
 

?>
  