<?php
class yuemei extends CI_Controller {
	private $notlogin = true;
	public function __construct() {
        header("Content-type:text/html;charset=utf-8");
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('yuemei')){
          die('Not Allow');
       }
	}

    /**
     * 医生列表页采集
     */
    public function index() {

        $id=isset($_GET['p'])?$_GET['p']:0;

        if($this->input->post()){
            $city_url = $this->input->post('city_url');
            $html = file_get_contents($city_url);
            preg_match('/<a href=".*">([0-9]+)<\/a><a class="next" href=".*">下一页<\/a>/uiUs',$html,$matches);
            $totalPages = trim(@$matches[1])?trim(@$matches[1]):1;
            $this->session->set_userdata('totalPages',$totalPages);
            $this->session->set_userdata('city_url',$city_url);
            //根据cityurl查询出城市名称
            $city_name = $this->db->query("select city from caiji_doctor_city_list where url='{$city_url}'")->row_array();
            $city_name = $city_name['city'];
            $this->session->set_userdata('doctor_city',$city_name);
        }
        if(!($totalPages = $this->session->userdata('totalPages'))){
            echo "参数错误"; die;
        }
        if(!($city_url = $this->session->userdata('city_url'))){
            echo "参数错误"; die;
        }
        if(!($doctor_city = $this->session->userdata('doctor_city'))){
            echo "参数错误"; die;
        }
        if($id){
            $target_url = $city_url."p_".$id;
        }else{
            $target_url = $city_url;
            $id = 1;
        }
        $html = file_get_contents($target_url);

        preg_match_all('/<a\s+class="f14 f700"\s+target="_blank"\s+href="(.*)">(.*)<\/a>/U',$html,$matches);

        preg_match_all('/<img\s+class="border"\s+src="(.*)"\s+width="84"\s+height="84"/',$html,$matches_picture);
        preg_match_all('/<dd><em\s+class="gray">机构：<\/em>(.*)<\/dd>/',$html,$matches_jigou);
        $data = array();
        foreach($matches[2] as $k=>$v){
            $rs = $this->db->query("select * from caiji_doctor where name ='{$v}'")->result_array();
            if(count($rs)>0) continue;

            $data['name'] = $v;
            $data['picture'] = $matches_picture[1][$k];
            $data['url'] = $matches[1][$k];
            $data['jigou'] = $matches_jigou[1][$k];
            $data['city'] = $doctor_city;

            $this->common->insertData("caiji_doctor",$data);
        }



        $id++;
        if($id<=$totalPages){
            echo "采集中...";
            echo "<script>
                location.href='?p={$id}&pages={$totalPages}';
            </script>";
        }else{
            echo "采集结束!";
            $this->session->unset_userdata('totalPages');
            $this->session->unset_userdata('city_url');
            $this->session->unset_userdata('doctor_city');
        }

	}

    /**
     * 获取医生城市列表
     */
    public function getDoctorCityList(){

        //获取城市和城市链接
        $html = file_get_contents("http://doctor.yuemei.com");
        preg_match('/<dd class="city_list">(.*)<\/dd>/Usi',$html,$matches);
        $aaa = $matches[1];
        preg_match_all('/<a.*href\s*=\s*"(.*)"\s*>(.*)<\/a>/iUs',$aaa,$matches2);

        //插入数据库
        foreach($matches2[1] as $k=>$v){
            $data=array();
            $data['city']=trim($matches2[2][$k]);
            $data['url']=$v;
            $result = $this->db->query("select * from caiji_doctor_city_list where city ='{$data['city']}'")->result_array();
            if(count($result)>1){
                continue;
            }
            $this->common->insertData('caiji_doctor_city_list',$data);

        }

        //组织select控件
        echo "<select name='city_url'>";
        $rs = $this->db->query("select * from caiji_doctor_city_list")->result_array();
        foreach($rs as $k=>$v){
            echo "<option value=\"{$v['url']}\">{$v['city']}</option>";
        }
        echo "</select>";

    }

    /**
     * 医生详细页采集
     * 从caiji_doctor中把医生的url查出来，然后进入此url进行信息采集，
     */
    public function detail(){

        try{
            $id = $this->input->get('id');
            if($id){
                $row = $this->db->query("select url,id from caiji_doctor where id = $id")->row_array();
            }else{
                $row = $this->db->query("select url,id from caiji_doctor order by id asc limit 1")->row_array();
            }

            if(strstr($row['url'],'www.yuemei.com/u')){  //http://www.yuemei.com/u/00038191/ 这种形式
                $html = file_get_contents($row['url'].'data/#tables');

                preg_match('/<!--female:女；male:男-->
                    <dd class="doc_cont">
                    	                    	<em>(.*)<\/em>/Uis',$html,$matches);  //匹配职称
                @$data['zhiCheng']=$matches[1];

                preg_match('/<dt>擅长<\/dt>
                    <dd>(.*)<\/dd>/uUis',$html,$matches);   //匹配擅长
                preg_match_all('/<em>(.*)<\/em>/Uis',@$matches[1],$matches2);
                @$data['skilled'] = implode(',',$matches2[1]);

                preg_match('/<dd class="doc_cont"><em>(.*)<\/em><i class="iline">\|<\/i>/iUs',$html,$matches);//匹配省份
                @$data['province'] = str_replace('省','',$matches[1]);

                preg_match('/<em>执业医师编号<\/em><p>(.*)<\/p>/iuUs',$html,$matches); //匹配执业医师编号、
                @$data['zigeCode'] = $matches[1];

                preg_match('/<dd><em>介绍<\/em>(.*)<\/dd>/iuUs',$html,$matches);//匹配个人介绍
                @$data['desc'] = strip_tags($matches[1]);

                $this->common->updateTableData('caiji_doctor',$row['id'],array (),$data);


            }else{   //http://doctor.yuemei.com/doctor/5907/ 这种形式
                $html = file_get_contents($row['url']);
                preg_match('/<em class="title">(.*)<\/em>/iuUs',$html,$matches);
                @$data['zhiCheng'] = $matches[1];

                preg_match('/<h3><a name="gaikuang"><\/a>擅长<\/h3>
            <p class="f14 indent mar_t14">(.*)<\/p>/iuUs',$html,$matches);
                @$data['skilled'] = $matches[1];

                preg_match('/<em class="gray14">执业医师编号：<\/em><em>(.*)<\/em>/iuUs',$html,$matches);
                @$data['zigeCode'] = $matches[1];

                $html2 = file_get_contents($row['url'].'introduction');
                preg_match('/<h3><a name="jieshao"><\/a>医生介绍<\/h3>
            <p class="f14 indent mar_t10">(.*)<\/p>/iuUs',$html2,$matches);//匹配个人介绍
                @$data['desc'] = $matches[1];

                $this->common->updateTableData('caiji_doctor',$row['id'],array(),$data);

            }

            //print_r($data);
            $max_id = $this->db->query("select url,max(id) as id from caiji_doctor")->row_array();
            if($row['id'] < $max_id['id']){
                echo "采集中...id：{$row['id']}";
                $next = $this->db->query("select url,id from caiji_doctor where id>{$row['id']} order by id asc  limit 1")->row_array();
                echo "<script>location.href='".site_url('manage/yuemei/detail')."?id={$next['id']}';</script>";
            }else{
                echo "采集结束";
            }

        }catch (Exception $e){
           echo $e;
        }

    }

    /**
     * 医院列表页采集
     */
    public function hospitalList() {

        $id=isset($_GET['p'])?$_GET['p']:0;

        if($this->input->post()){
            $city_url = $this->input->post('city_url');
            $html = file_get_contents($city_url);
            preg_match('/<a href=".*">([0-9]+)<\/a><a class="next" href=".*">下一页<\/a>/uiUs',$html,$matches);
            $totalPages = trim(@$matches[1])?trim(@$matches[1]):1;  //获取总页数
            $this->session->set_userdata('totalPages',$totalPages);   //将总页数存到session中
            $this->session->set_userdata('city_url',$city_url);       //将城市url存到session中
            //根据cityurl查询出城市名称
            $city_name = $this->db->query("select city from caiji_hospital_city_list where url='{$city_url}'")->row_array();
            $city_name = $city_name['city'];
            $this->session->set_userdata('hospital_city',$city_name);   //存储医院所在城市到session中
        }
        if(!($totalPages = $this->session->userdata('totalPages'))){   //总页数
            echo "参数错误"; die;
        }
        if(!($city_url = $this->session->userdata('city_url'))){    //城市url
            echo "参数错误"; die;
        }
        if(!($hospital_city = $this->session->userdata('hospital_city'))){       //医院所在城市
            echo "参数错误"; die;
        }
        if($id){
            $target_url = $city_url."p_".$id;

        }else{
            $target_url = $city_url;
            $id = 1;
        }
        $html = file_get_contents($target_url);

        preg_match_all('/<dl><dt><div class="hostit"><a target="_blank" href="(.*)">(.*)<\/a><\/div><em class="btnhosweb"><a target="_blank" href=".*">查看医院主页<\/a><\/em><\/dt>\s*<dd class="hos-l">\s*<div><em>类 型：<\/em>.*<\/div>\s*<div><em>资 质：<\/em>(.*)<\/div>\s*<div><em>业 务：<\/em><span class="green"><a target="_blank" href=".*">.*<\/a><\/span><\/div>\s*<\/dd>\s*<dd class="hos-r">\s*(<div><em>地 址：<\/em>(.*)<\/div>\s*){0,1}(<div><em>电 话：<\/em>(.*)<\/div>\s*){0,1}(<div><em>营 业：<\/em>(.*)<\/div>\s*){0,1}<\/dd>\s*(<dd class="hos-pic">\s*(<a target="_blank" href=".*"><img src="(.*)" width="50" height="50"><\/a>\s*)*<\/dd>\s*){0,1}<\/dl>
/iuUs',$html,$matches); //匹配url，名称

        $matches[1]; //url
        $matches[2]; //名称
        $matches[5]; //地址
        $matches[7]; //电话
        $matches[9]; //营业时间
        $matches[12]; //图片

        $data = array();
        foreach($matches[2] as $k=>$v){
            $rs = $this->db->query("select * from caiji_hospital where name ='{$v}'")->result_array();
            if(count($rs)>0) continue;

            $data['name'] = $v;   //医院名称
            $data['picture'] = $matches[12][$k];  //医院图片
            $data['url'] = $matches[1][$k];    //医院url地址
            $data['phone'] = $matches[7][$k];     //电话
            $data['zizhi'] = $matches[3][$k];     //资质
            $data['address'] = $matches[5][$k];     //地址
            $data['yingyeTime'] = $matches[9][$k];     //营业时间
            $data['city'] = $hospital_city;  //城市

            $this->common->insertData("caiji_hospital",$data);
        }

        $id++;
        if($id<=$totalPages){
            echo "采集中...";
            echo "<script>
                location.href='?p={$id}&pages={$totalPages}';
            </script>";
        }else{
            echo "采集结束!";
            $this->session->unset_userdata('totalPages');
            $this->session->unset_userdata('city_url');
            $this->session->unset_userdata('hospital_city');
        }

    }

    /**
     * 获取医院城市列表
     */
    public function getHospitalCityList(){

        //获取城市和城市链接
        $html = file_get_contents("http://hospital.yuemei.com");
        preg_match('/<dd class="city_list all">(.*)<\/dd>/Usi',$html,$matches);
        $aaa = $matches[1];
        preg_match_all('/<a.*href\s*=\s*"(.*)".*>(.*)<\/a>/iUs',$aaa,$matches2);

        //插入数据库
        foreach($matches2[1] as $k=>$v){
            if(trim($matches2[2][$k]) == '收起'){
                continue;
            }
            $data=array();
            $data['city']=trim($matches2[2][$k]);
            $data['url']=$v;
            $result = $this->db->query("select * from caiji_hospital_city_list where city ='{$data['city']}'")->result_array();
            if(count($result)>1){
                continue;
            }

            $this->common->insertData('caiji_hospital_city_list',$data);

        }

        //组织select控件
        echo "<select name='city_url'>";
        $rs = $this->db->query("select * from caiji_hospital_city_list")->result_array();
        foreach($rs as $k=>$v){
            echo "<option value=\"{$v['url']}\">{$v['city']}</option>";
        }
        echo "</select>";

    }

    /**
     * 医院详细页采集
     * 从caiji_doctor中把医生的url查出来，然后进入此url进行信息采集，
     */
    public function hospitalDetail(){

        try{
            $id = $this->input->get('id');
            if($id){
                $row = $this->db->query("select url,id from caiji_hospital where id = $id")->row_array();
            }else{
                $row = $this->db->query("select url,id from caiji_hospital order by id asc limit 1")->row_array();
            }
            if(strstr($row['url'],'http://hospital.yuemei.com/hospital')){

                $html = file_get_contents($row['url'].'introduction/');

                preg_match('/<div class="content">(.*)<\/div>/iUus',$html,$matches);  //匹配详细描述

            }else{
                $html = file_get_contents($row['url']);

                preg_match('/<div class="border brandidea">\s*<p>(.*)<\/p>\s*<\/div>/iUus',$html,$matches);  //匹配详细描述

            }
            @$data['description']=$matches[1];

            $this->common->updateTableData('caiji_hospital',$row['id'],array (),$data);


            //print_r($data);
            $max_id = $this->db->query("select url,max(id) as id from caiji_hospital")->row_array();
            if($row['id'] < $max_id['id']){
                echo "采集中...id：{$row['id']}";
                $next = $this->db->query("select url,id from caiji_hospital where id>{$row['id']} order by id asc  limit 1")->row_array();
                echo "<script>location.href='".site_url('manage/yuemei/hospitalDetail')."?id={$next['id']}';</script>";
            }else{
                echo "采集结束";
            }

        }catch (Exception $e){
            echo $e;
        }

    }

    /**
     * 导入医生
     */
    public function importYisheng(){
        $data = $this->db->query("select * from caiji_doctor")->result_array();
        foreach($data as $v){
            $result = $this->db->query("select * from users where username = '{$v['name']}' and role_id = 2 ")->result_array();

            if(count($result)>0){
                continue;
            }
            $insert = array();
            $insert['role_id'] = 2;
            $insert['username'] = $v['name'];
            $insert['alias'] = $v['name'];
            $insert['created'] = time();
            $insertId = $this->common->insertData('users',$insert);

            $insert = array();
            $insert['user_id'] = $insertId;
            $insert['skilled'] = $v['skilled'];
            $insert['company'] = $v['jigou'];
            $insert['position'] = $v['zhiCheng'];
            $insert['province'] = $v['province'];
            $insert['city'] = $v['city'];
            $insert['introduce'] = $v['desc'];
            $insert['zigeCode'] = $v['zigeCode'];
            $this->common->insertData('user_profile',$insert);
            $this->thumb($insertId, $v['picture']); //生成缩略图


        }

        $this->db->query("TRUNCATE TABLE  `caiji_doctor_city_list`");  //清空临时表
        $this->db->query("TRUNCATE TABLE  `caiji_doctor`");

        echo "导入结束";
    }

    private function thumb($uid, $file) {
        $target_path = realpath(APPPATH . '../images/users');
        if (!is_writable($target_path)) {
            return false;
        } else {
            if (!is_dir($target_path . '/' . $uid)) {
                mkdir($target_path . '/' . $uid, 0777, true);
            }
            $target_path = $target_path . '/' . $uid . '/';
            if ($file != '') {
            	$ImgInfo = getimagesize($file);
            	if($ImgInfo[2]==1 OR $ImgInfo[2]==2 OR $ImgInfo[2]==3){
                $get_content = file_get_contents($file);
                @ file_put_contents($target_path . 'userpic.jpg', $get_content);
                $this->load->helper('file');
                $this->load->library('upload');
                GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_thumb.jpg', 36, 36);
                GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic_profile.jpg', 120, 120);
                GenerateThumbFile($target_path . 'userpic.jpg', $target_path . 'userpic.jpg', 250, 250);
                return true;
            	}else{
            		return false;
            	}
            } else {
                return false;
            }
        }
    }

    /**
     * 导入医院
     */
    public function importJigou(){
        $data = $this->db->query("select * from caiji_hospital")->result_array();
        foreach($data as $v){
            $result = $this->db->query("select * from users where username = '{$v['name']}' and role_id = 3 ")->result_array();
            if(count($result)>0){
                continue;
            }
            $insert = array();
            $insert['role_id'] = 3;
            $insert['username'] = $v['name'];
            $insert['alias'] = $v['name'];
            $insert['created'] = time();
            $insertId = $this->common->insertData('users',$insert);

            $insert = array();
            $insert['user_id'] = $insertId;
            $this->common->insertData('user_profile',$insert);

            $insert = array();
            $insert['userid'] = $insertId;
            $insert['name'] = $v['name'];
            $insert['tel'] = $v['phone'];
            $insert['web'] = ''; //官网地址
            $insert['descrition'] = $v['description'];
            //$insert['province'] = $v['province'];
            $insert['city'] = $v['city'];
            $insert['address'] = $v['address'];
            $insert['shophours'] = $v['yingyeTime'];
            $insert['cdate'] = time();
            $insert['zizhi'] = $v['zizhi'];
            $this->common->insertData('company',$insert);

            $this->thumb($insertId, $v['picture']); //生成缩略图

        }

        $this->db->query("TRUNCATE TABLE  `caiji_hospital_city_list`");  //清空临时表
        $this->db->query("TRUNCATE TABLE  `caiji_hospital`");

        echo "导入结束";
    }

}
?>
