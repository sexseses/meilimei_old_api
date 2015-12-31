<?php
if (!defined('BASEPATH'))
    exit ('No direct script access allowed');
/**
 * app首页
 * @package        WENRAN
 * @subpackage    Controllers
 */
require_once(__DIR__."/MyController.php");
class index extends MY_Controller
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

      if($this->input->get('tags') ){
         $this->db->like('tags',$this->input->get('tags'));
           //   $this->db->where('spcid', $this->input->get('id'));
         $this->db->order_by("id", "desc");
         $this->db->select('title, id,spcid,picture,sharepic,area');
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

        echo json_encode($result);
    }

    public function getHomeBody(){

        $tags = "middleBanner";

        $this->db->like('tags',$tags);

        $this->db->order_by("id", "asc");
        $this->db->select('title, picture');
        $tmp = $this->db->get('apple')->result_array();

        $result['data'] =  array();
        foreach($tmp as $r){
            if($r['picture']){
                $r['picture'] = $this->remote->show(str_replace('upload/','',$r['picture']));
                unset($r['title']);
            }else{
                unset($r['picture']);
            }
            $temp[] = $r;
        }

        $ad = array();
        if(!empty($temp)){
            foreach($temp as $key=>$item){
                if(!isset($item['picture'])){
                    $arrtmp = array();
                    $arrtmp = explode('|', $item['title']);
                    $tmp = array();
                    if(!empty($arrtmp)){
                    	$tmp['title'] = isset($arrtmp[0])?$arrtmp[0]:'';
                    	$tmp['color'] = isset($arrtmp[1])?$arrtmp[1]:'';
                    	$tmp['Transparent'] = isset($arrtmp[2])?$arrtmp[2]:'';
                    }
                    $ad['tags'][] = $tmp;
                }else{
                    $ad['picture'] = $item['picture'];
                }
            }
        }

        $result['data']['ad'] = $ad;
        $result['data']['piazza'] = $this->getHN(2,2);
        $result['data']['activities'] = $this->getTags("activities",3);
        $result['data']['reality'] = $this->getHN();
        
        $result['state'] = '000';
        echo json_encode($result);    
    }

    private function getTags($tags = "middleBanner",$pageSize = 10){

        $this->db->like('tags',$tags);

        $this->db->order_by("id", "asc");
        $this->db->limit($pageSize);
        $this->db->select('title,id,spcid,tehuiid,picture,sharepic');
        $tmp = $this->db->get('apple')->result_array();

        $result['data'] =  array();
        $temp = array();
        foreach($tmp as $r){
         
            $r['picture'] = $this->remote->show(str_replace('upload/','',$r['picture']));
            $r['sharepic'] = $r['sharepic']==''?$r['picture']:$this->remote->show(str_replace('upload/','',$r['sharepic']));
            if($tags != "activities"){
                $r['type'] = 1;
            }else{
                unset($r['tehuiid']);
                unset($r['spcid']);
            }
            
            $temp[] = $r;
            
        }
        return $temp;        
    }
    //get new and hot topics
    private function getHN($type = 1,$pageSize=10) {
        $this->db->from('wen_weibo');

        $this->db->where('wen_weibo.isdel', 0);
        //$this->db->where('wen_weibo.chosen', 0);
        //$this->db->where('wen_weibo.chosentime >=', time());
        
        $this->db->limit($pageSize);
        $this->db->join('users', 'users.id = wen_weibo.uid');
        $this->db->join('topic_pics_extra', 'wen_weibo.weibo_id = topic_pics_extra.weibo_id');
        $this->db->select('topic_pics_extra.items,topic_pics_extra.points_x,topic_pics_extra.points_y,topic_pics_extra.price,topic_pics_extra.doctor,topic_pics_extra.yiyuan,wen_weibo.weibo_id,wen_weibo.tags,users.alias as uname,wen_weibo.views as vote,wen_weibo.comments,wen_weibo.uid,wen_weibo.uid,wen_weibo.content,wen_weibo.ctime, wen_weibo.type_data');

        $this->db->order_by('wen_weibo.newtime desc');

        $tmp = $this->db->get()->result_array();

        $res = array ();

        foreach ($tmp as $r) {
            if($type == 1){
                $tags = $this->getTags();
                if(!empty($tags)){
                    foreach($tags as $item){
                        $res[] = $item;
                    }
                }
            }

            $dtypd = unserialize($r['type_data']);
            $url = (isset ($dtypd['pic']['savepath']) ? $dtypd['pic']['savepath'] : $dtypd['savepath']);
                    
            $arr_url = explode('/',$url);
            if(isset($arr_url[1])){
                $url = str_replace('/'.$arr_url[1].'/','/'.$arr_url[1].'x320/',$url);
            }
            $r['url'] = $this->remote->show320($url, $width);
            if (!isset ($dtypd['pic']['height'])) {
                $psize = getimagesize($r['url']);
                if($r['height'] = $psize[1]){
                    $r['width'] = $psize[0];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }
                        
                $this->UdatePic($r['weibo_id'], $psize);
            } else {
                if($r['height'] = $dtypd['pic']['height']){
                        $r['width'] = $dtypd['pic']['width'];
                }else{
                    $r['height'] = 260;
                    $r['width'] = 200;
                }
            }
            $r['uname'] == '' && $r['uname'] = substr($r['phone'], 0, 4) . '***';
            if (preg_match('/^\\d+$/', $r['uname'])) {
                $r['uname'] = substr($r['uname'], 0, 4) . '***';
            }

            if(time()-$r['ctime']<3600*10){
                if(time()-$r['ctime']<3600){
                    $r['ctime'] = intval((time()-$r['ctime'])/60).'分钟前';
                }else{
                    $r['ctime'] = intval((time()-$r['ctime'])/3600).'小时前';
                }
            }else{
                $r['ctime'] = date('Y年m月d日',$r['ctime']);
            }
            $dtypd = unserialize($r['type_data']);
            isset ($dtypd['title']) && $r['content'] = $dtypd['title'];
            $r['haspic'] = 0;
            if (!empty ($dtypd) and isset ($dtypd['pic'])) {
                $r['haspic'] = 1;
            }
            if ($this->input->get('thumbsize')) {
                $r['thumb'] = $this->remote->thumb($r['uid'], intval($this->input->get('thumbsize')));
            } else {
                $r['thumb'] = $this->profilepic($r['uid'], 2);
            }
            if($this->input->get('width')){
               $r['images'] = $this->Plist($r['weibo_id']);
             }
            $r['type'] = 2;
            if($type ==1){
                $r['points'][] = array('points_x'=>$r['points_x'], 'points_y'=>$r['points_y'], 'items'=>$r['items'], 'doctor'=>$r['doctor'], 'yiyuan'=>$r['yiyuan'], 'price'=>intval($r['price']));
                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            if($type != 1 ){
                
                unset($r["items"]);
                unset($r["price"]);
                unset($r["doctor"]);
                unset($r["yiyuan"]);
                unset($r["uname"]);
                unset($r["vote"]);
                unset($r["comments"]);
                unset($r["uid"]);
                unset($r["thumb"]);
                unset($r["ctime"]);
                unset($r["type"]);
                unset($r["points_y"]);
                unset($r["points_x"]);
            }
            unset($r["haspic"]);
            unset ($r['type_data']);
            $res[] = $r;
        }
        if($type == 1){
            $tags = $this->getTags();
            if(!empty($tags)){
                foreach($tags as $item){
                    $res[] = $item;
                }
            }
        }

        return $res;
    }
    //profile pic
    private function profilepic($id, $pos = 0) {
        switch ($pos) {
            case 1 :
                return $this->remote->thumb($id, '36');
            case 0 :
                return $this->remote->thumb($id, '250');
            case 2 :
                return $this->remote->thumb($id, '120');
            default :
                return $this->remote->thumb($id, '120');
                break;
        }
    }
    // bannner detail
    public function info($param = ''){

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
