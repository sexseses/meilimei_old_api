<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * app首页
 * @package        WENRAN
 * @subpackage    Controllers
 */

require_once(__DIR__ . "/MyController.php");
class advert extends MY_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {

        parent:: __construct();

    }

    /**
     * 广告
     */
    public function getGroupAdvert($param = '')
    {
        $result = array();
        $result['state'] = '1000';

        $groupid = intval($this->input->get('groupid'));

        if($groupid > 0) {
            //调取上面的广告
            $ads = $this->db->query("select id, banner, event_type, event_value,event_sort from crm_event_release where (event_position =5 or event_position=6) and crm_event_id=? and event_begin_time <=" . time() . " and event_end_time >= " . time() . " limit 2", array($groupid))->result_array();

            if (!empty($ads)) {
                foreach ($ads as $k => $v) {
                    $ads[$k]['banner'] = $v['banner'];
                }
                $result['data'] = $ads;
            } else {

                $result['state'] = "1016";
                echo json_encode($result);
                exit();
            }
        }else{
            $result['state'] = "1015";
            echo json_encode($result);
            exit();
        }
        echo json_encode($result);
    }

    /**
     * 广告
     */
    public function getAdvert($param = '')
    {
        $result = array();
        $result['state'] = '1000';

        $position = $this->input->get('position');//广告位
        if(empty($position)){
            $result['state'] = "1017";
            echo json_encode($result);
            exit();
        }
        //根据广告位获取广告
        $ads = $this->db->query("select id, banner, event_type, event_value from crm_event_release where event_position=? and event_begin_time <=" . time() . " and event_end_time >= " . time() . " limit 10", array($position))->result_array();
        if (!empty($ads)) {
            foreach ($ads as $k => $v) {
                $ads[$k]['banner'] = $v['banner'];
            }
            $result['data'] = $ads;
        } else {

            $result['state'] = "1016";
            echo json_encode($result);
            exit();
        }

        echo json_encode($result);
    }
}
?>
