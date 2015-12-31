<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * 附近的机构
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class recomAPP extends MY_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent :: __construct();
        $this->load->model('auth');
    }

    /**
     * 应用推荐
     */
    public function index($param = ''){

        $page = $this->input->post("page")?$this->input->post("page"):1; //当前页

        if ($this->auth->checktoken($param)) { //验证令牌
            $pers = 10;
            $sql = "select * from recom_app order by ctime desc  limit ".(($page-1)*$pers).",{$pers}";

            $cooList = $this->db->query($sql)->result_array();
            foreach($cooList as $k=>$v){
                $cooList[$k]['picture'] = base_url().$v['picture'];
            }

            $result['status'] = '000';
            $result['data'] = $cooList;
        }else{
            $result['status'] = '001';
        }

        echo json_encode($result);
    }


}

?>
