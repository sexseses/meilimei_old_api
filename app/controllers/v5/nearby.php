<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * 附近的机构
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class nearby extends MY_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        error_reporting(E_ALL);
        parent :: __construct();
        $this->load->model('Gallery');
        $this->load->model('auth');

    }

    /**
     * 附近的机构
     */
    public function index($param = ''){

        $search = $this->input->post("search"); //搜索
        $page = $this->input->post("page")?$this->input->post("page"):1; //当前页

        if ($this->auth->checktoken($param)) { //验证令牌
            $curLat = $this->input->post("currLat")?$this->input->post("currLat"):31.249162;//当前坐标
            $curLng = $this->input->post("currLng")?$this->input->post("currLng"):121.487899;//当前坐标

            $range = $this->input->post("range")?$this->input->post("range"):2000; //所选距离范围
            $wheres = '';
            if($search){
                $wheres .= " and name like '%{$search}%'";
            }

            $pers = 10;
            $sql = "SELECT company.id,company.userid,company.name,company.contactN,company.tel,company.phone,company.email,company.web,company.weibo,company.picture,company.province,company.city,company.district,company.address,company.department,company.users,company.shophours,company.votenum,company.grade,company.cdate,company.click,company.is_show,lat,lng,
    ( 6371 * acos( cos( radians( $curLat ) ) * cos( radians( lat ) )
    * cos( radians( lng ) - radians( $curLng ) ) + sin( radians( $curLat ) )
     * sin( radians( lat ) ) ) ) AS distance FROM map join company on company.userid=map.userid
    where lat > ".($curLat-0.1*$range/10000)." and lat < ".($curLat+0.1*$range/10000)." and lng > ".($curLng-0.1*$range/10000)." and lng < ".($curLng+0.1*$range/10000)." $wheres
    having distance<".($range/1000)."  ORDER BY distance  limit ".(($page-1)*$pers).",{$pers}";

            $cooList = $this->db->query($sql)->result_array();

            foreach ($cooList as &$row) {
                $row['thumb'] = $this->Gallery->profilepic($row['userid'], 2);
            }
            $result['status'] = 000;
            $result['data'] = $cooList;
        }else{
            $result['status'] = 001;
        }

        echo json_encode($result);
    }


}

?>
