<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * WERAN Api auth Controller Class
 * @package		WENRAN
 * @subpackage	Controllers
 */
class H5v1 extends CI_Controller {

    const H5URL = 'http://meilishenqi.meiriq.com';
    const H5_KEY = '4446547228ad70e806c8f95dd043b4c1';

    public function __construct() {
        parent::__construct();
        $this->load->model('remote');
    }

    public function getUser() {

        $uid = urldecode($this->input->get('uid'));
        $url = urldecode($this->input->get('url'));
        $sign = urldecode($this->input->get('sign'));
        //echo $uid.$url.self::H5_KEY."====".md5($uid.$url.self::H5_KEY)."====".strtolower($sign);
        if (intval($uid) > 0 && strtolower($url) == self::H5URL && md5($uid.$url.self::H5_KEY) == strtolower($sign)) {

            $query = $this->db->query('select id, username from users where id=?', array($uid));

            if ($query->num_rows() > 0) {

                $r = $query->result_array();
                
                $result['uid'] = $r[0]['id'];
                $result['username'] = $r[0]['username'];
                $result['thumb'] = $this->remote->thumb($r[0]['id'], '36');
            } else {

                $result['state'] = array('201' => '此而用户不存在');
            }
        } else {
            $result['state'] = array('202' => '参数有误');
        }
        echo json_encode($result);
    }

}
