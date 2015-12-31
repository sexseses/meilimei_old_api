<?php
/* *
 * 类名：user_score
 * 功能：用户积分计算
 * 详细：根据用户的操作增加积分
 * 版本：0.1
 * 日期：2014-10-14
 * 说明：
 * 以下代码根据产品文档规则制定
 */
class user_score extends CI_Model{
    protected  $num;
    function __construct() {
        $this->db = $this->load->database('default', TRUE);
    }
    
    /**
     * 类内部使用更新数据库数据(加分)
     * @param $user_id 用户id
     * @param $score_num 增加的分数
     * @return 返回数据 TRUE FALSE
     */
    function updat_score($user_id,$score_num){
        $sql = "SELECT * FROM user_score WHERE user_id = ?";
        $userquery = $this->db->query($sql,array($user_id));
        
        if($userquery->num_rows()>0){
            $this->db->where('user_id', $user_id);
            $this->db->set('user_score',"user_score + $score_num",FALSE);
            return $this->db->update('user_score');
        }else{
            $data = array('user_id' => $user_id,'user_score' => $score_num);
            return $this->db->insert('user_score', $data);
        }
    }
    
    /**
     * 类内部使用更新数据库数据(加分操作记录)
     * @param $user_id 用户id
     * @param $score_num 增加的分数
     * @return 返回数据 TRUE FALSE
     */
    function user_bonus_log($user_id,$score_num,$score_oper){
        $data = array( 
            'user_id' => $user_id,
            'user_score' => $score_num,
            'user_oper' => $score_oper,
            'lasttime' => time()
        );
        return $this->db->insert('user_score_log', $data);
    }
    
    /**
     * 登录天数登记
     * @param $user_id 用户id
     * @param $score_num 登录的天数
     * @return 返回数据 TRUE FALSE
     */
    function login_day($user_id,$day_num){
        if($day_num == 1){
            $this->num = 5;
        }elseif ($day_num == 2){
            $this->num = 10;
        }elseif ($day_num == 3){
            $this->num = 15;
        }elseif ($day_num >= 3 && $day_num < 10){
            $this->num = 15;
        }elseif ($day_num >= 10){
            $this->num = 20;
        }
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );    
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户注册
     * @param $user_id 用户id
     * @return 返回数据 TRUE FALSE
     */
    function user_reg($user_id){
        if($user_id == ""){
            return false;
        }
        $this->num = 20;
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    
    /**
     * 用户账户绑定
     * @param $type 账号的类型 （taobao,weibo,mobile,qq,weixin）
     * @return 返回数据 TRUE FALSE
     */
    function user_bind($user_id,$type){
        if($user_id == ""){
            return false;
        }
        if($type == "taobao" || $type == "mobile"){
            $this->num = 20;
        }else{
            $this->num = 10;
        }
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户上传头像
     * @return 返回数据 TRUE FALSE
     */
    function upload_user_img($user_id){
        $this->num = 30;
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 验证邮箱
     * @return 返回数据 TRUE FALSE
     */
    function check_user_email($user_id){
        $this->num = 30;
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户填写年龄
     * @return 返回数据 TRUE FALSE
     */
    function add_user_age($user_id){
        $this->num = 20;
        $rs =  $this->updat_score($user_id, $this->num);
        if($rs){
            echo "log";
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户填写兴趣爱好
     * @return 返回数据 TRUE FALSE
     */
    function add_user_fancy($user_id){
        $this->num = 20;
        $rs =  $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
    * 用户邀请好友
    * @return 返回数据 TRUE FALSE
    */
    function invitation_friend($user_id){
        $this->num = 20;
        
        $sql = "select * from user_score_log where user_id = ?";
        $userquery = $this->db->query($sql,array($user_id));
        if($userquery->num_rows()>20){
            echo "会员数量超过20人";
            return FALSE;
        }
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    function invitation_code($user_id){
        $this->num = 20;
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 圈子发帖
     * @param $type 文章,无标题  (1,2)
     * @return 返回数据 TRUE FALSE
     */
    function circle_post($user_id,$type){
        if($type){
            $this->num = 10;           
        }else{
            $this->num = 20;
        }
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户购买
     * @param $user_id;
     * @param $day_num 团购,机构整形,正常购物(1,2,3)
     * @return 返回数据 TRUE FALSE
     */
    function user_fans_num($user_id,$day_num){
        switch ($day_num) {
            case 100:
                $this->num = 10;
            break;
            case 1000:
                $this->num = 100;
            break;
            case 10000:
                $this->num = 1000;
            break;
            case 100000:
                $this->num = 20000;
            break;
            case 1000000:
                $this->num = 400000;
            break;
            case 10000000:
                $this->num = 8000000;
            break;
            case 100000000:
                $this->num = 16000000;
            break;
            default :
                $this->num = 0;
            break;
        }
        
        $rs =  $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户购买
     * @param $user_id;
     * @param $type 成功(1)
     * @return 返回数据 TRUE FALSE
     */
    function user_appointment($user_id,$type){
        if($type){
            $this->num = 15;
        }else{
            $this->num = 20;
        }
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户购买
     * @param $user_id;
     * @param $type 团购,机构整形,正常购物(1,2,3)
     * @return 返回数据 TRUE FALSE
     */
    function user_buy($user_id,$type){
        if($type == 1){
            $this->num = 1;
        }elseif($type == 2){
            $this->num = 100;
        }else{
            $this->num = 25;
        }
        
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 用户资讯
     * @param $user_id;
     * @param $type 保密 1,公开 0,
     * @return 返回数据 TRUE FALSE
     */
    function user_picinfo($user_id,$type){
        if($type){
            $this->num = 10;
        }else{
            $this->num = 20;
        }
    
        $rs =  $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
    /**
     * 消费评价
     * @param $user_id;
     * @param $type 图文并茂 1,文字 0
     * @return 返回数据 TRUE FALSE
     */
    function user_review($user_id,$type){
        if($type){
            $this->num = 20;
        }else{
            $this->num = 10;
        }
    
        $rs = $this->updat_score($user_id, $this->num);
        if($rs){
            return $this->user_bonus_log($user_id, $this->num, __FUNCTION__ );
        }else{
            return FALSE;
        }
    }
    
}