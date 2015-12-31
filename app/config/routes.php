<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['manage']  	       = 'manage/index';
$route['default_controller'] = "meili";
$route['promotion/([\w]+)']          = "promotion/download/$1";
$route['question/(:num)']          = "question/index/$1";
$route['page/(:any)']         = "page/index/$1";
$route['question/new']             = "question/ask";
$route['404_override']          = 'notice/p404';
$route['yishengDetail/(:num)']        = "yishengDetail/index/$1";
$route['jigouDetail/(:num)']        = "jigouDetail/index/$1";
$route['manage/syncUserInfo'] = 'manage/home/syncUserInfo';
$route['manage/search'] = 'manage/home/search';

$route['m/meilishenqi.apk'] = 'm/meilishenqi';
