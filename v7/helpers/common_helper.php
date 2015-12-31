<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_meta_details($meta_name = '', $type = '')
{
		$CI     =& get_instance();

		if($type == 'title')
		{
		return $CI->Common_model->getTableData('metas', array('name' => $meta_name))->row()->title;
		}
		else if($type == 'meta_keyword')
		{
		return $CI->Common_model->getTableData('metas', array('name' => $meta_name))->row()->meta_keyword;
		}
		else if($type == 'meta_description')
		{
		return $CI->Common_model->getTableData('metas', array('name' => $meta_name))->row()->meta_description;
		}

}

function get_price1($val='')
{
	$percent=($val)*(55/100);
	$finalvalue=($percent*10)+$val;
	return $finalvalue;
}

function get_price2($val='')
{
	$percent=(($val)*(58/100));
	$finalvalue=($percent*10)+$val;
	return $finalvalue;
}

function get_price3($val='')
{
	$percent=($val)*(130/100);
	$finalvalue=($percent*10)+$val;
	return $finalvalue;
}

function get_price4($val='')
{
	$percent=($val)*(170/100);
	$finalvalue=($percent*10)+$val;
	return $finalvalue;
}
function includeFile($type,$name=array())
{ $tmp = '';
  $baseurl = base_url();
  if($type =='js'){
  	array_filter($name);
  	foreach($name as $k){
  		$tmp.='<script src="'.$baseurl.$k.'" type="text/javascript"></script>';
  	}
  }
  return $tmp;
}


/**
 * 返回json格式的数据
 * @param $msg
 * @param string $code
 * @param array $data
 */
function returnJsonData( $msg, $code = '400', $data = array() ) {
    die(json_encode(array('state'=>$code, 'notice'=>$msg, 'data'=>$data)));
}



?>