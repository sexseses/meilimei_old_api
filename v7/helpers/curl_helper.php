<?php
function curl_return($purl= '' )
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.meilimei.com/webapi/".$purl.'signapp=893F86E4DC4CE0905628AECFBF8F1D2F');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$info=curl_exec($ch);
	curl_close($ch);
	$info = json_decode($info,true);
	return $info['data'];
}

function curl_return2($purl= '' )
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.meilimei.com/webapi/".$purl.'signapp=893F86E4DC4CE0905628AECFBF8F1D2F');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$info=curl_exec($ch);
	curl_close($ch);
	$info = json_decode($info,true);
	return $info;
}

function curl_return_post($postlist='',$purl= '')
{
	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, "http://www.meilimei.com/webapi/".$purl.'signapp=893F86E4DC4CE0905628AECFBF8F1D2F');
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($ch, CURLOPT_POSTFIELDS,$postlist);
	 
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
	 $info = curl_exec($ch);
	 curl_close($ch);
	 return $info;
}
