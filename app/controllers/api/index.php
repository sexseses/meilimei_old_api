<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * app首页
 * @package        WENRAN
 * @subpackage    Controllers
 */

class index extends CI_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {

        parent :: __construct();
        $this->load->model('auth');
        $this->load->model('remote');

    }

    /**
     * 广告
     */
    public function index($param = ''){

            //调取上面的广告
            $topAds = $this->db->query("select * from apple where adPos like '$1$'  order by cdate")->result_array();
            foreach($topAds as $k => $v){
                $topAds[$k]['picture']=base_url().$v['picture'];
            }

            //调去下面的文章列表
            $bottomAds = $this->db->query("select * from apple where adPos like '$2$' order by cdate desc ")->result_array();
            foreach($bottomAds as $k => $v){
                $bottomAds[$k]['picture']=base_url().$v['picture'];
            }
            $data = array();
            $data['status']='000';
            $data['topAds'] =$topAds;
            $data['bottomAds'] = $bottomAds;

        echo json_encode($data);
    }
    /**
     * 广告
     */
    public function getBanner($param = ''){
      //if ($this->auth->checktoken($param)) {
      	   if($this->input->get('tags') ){
      	   	$this->db->like('tags',$this->input->get('tags'));
         //   $this->db->where('spcid', $this->input->get('id'));
            $this->db->order_by("id", "desc");
            $this->db->select('title, id,spcid,picture,sharepic,area,url');
            if($this->input->get('need') !="tae"){
                 $this->db->where("spcid !=","-10");
            }
            $tmp = $this->db->get('apple')->result_array();
            $city = $this->input->get('city');
            
            $result['data'] =  array();
            foreach($tmp as $r){
              if(in_array($city,unserialize($r['area']))){
              	$r['picture'] = $this->remote->show(str_replace('upload/','',$r['picture']));
              	$r['sharepic'] = $r['sharepic']==''?$r['picture']:$this->remote->show(str_replace('upload/','',$r['sharepic']));;
                $result['data'][] = $r;
              }else{
                $r['picture'] = $this->remote->show(str_replace('upload/','',$r['picture']));
                $r['sharepic'] = $r['sharepic']==''?$r['picture']:$this->remote->show(str_replace('upload/','',$r['sharepic']));;
                $result['data'][] = $r;
              }
            }
            $result['state'] = '000';
      	   }else{
      	   	  $result['state'] = '012';
      	   }
      /*} else {
			$result['state'] = '001';
	  }*/
	  echo json_encode($result);
    }
    // bannner detail
    public function info($param = ''){
      if ($this->auth->checktoken($param)) {
      	   if($id = intval($this->input->get('id'))){
      	   	$this->db->where('id', $id);
            $this->db->order_by("id", "desc");
            $this->db->select('title, content,cdate,url');
            $tmp = $this->db->get('apple')->result_array();
            $result['data'] =  array();
            $result['data']['url'] = $tmp[0]['url']?$tmp[0]['url']:site_url().'banner/mobile/'.$id;
            $result['data']['title'] = $tmp[0]['title'];
            $result['data']['content'] = '<meta name="viewport" content="initial-scale=1, width=device-width,  user-scalable=no"  /><style>img{max-width:100%;} #content{font-size:16px; line-height:180%; padding:10px;color:#333;margin:0 auto}   </style><div id="content">'.preg_replace('/(?<=img src=")(\W+)attached?\//i', base_url()."attached/", $tmp[0]['content']).'</div>';
            $result['data']['cdate'] = date('Y-m-d',$tmp[0]['cdate']);
            $result['state'] = '000';
      	   }else{
      	   	  $result['state'] = '012';
      	   }

      } else {
			$result['state'] = '001';
	  }
	  echo json_encode($result);
    }
   /**
     * 广告detail
     */
    public function detail($param = ''){

        $id = $this->input->post('id');
        if($id){
            $detail = $this->db->query("select * from apple where id = $id ")->row_array();
            $data['status'] = '000';
            $data['detail'] = $detail;
        }else{
            $data['status'] = '001';
        }
        echo "<style>
        *{
        margin:0px;
        padding:0px;
        border:0px;
        }
        #content{
        font-size:16px;
        line-height:180%;
        padding:10px;color:#333;
        }
        #content img{
        max-width:300px;
        }

        </style>
        <div id='content'>".preg_replace('/(?<=img src=")(\W+)attached?\//i', base_url()."attached/", $detail['content'])."
        </div>";
    }


}

?>
