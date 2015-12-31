<?php
/**
 * Created by PhpStorm.
 * User: zhangyi
 * Date: 14-10-30
 * Time: 下午4:27
 */
require_once(dirname(__FILE__) . '/' . 'TopSdk.php');
require_once(dirname(__FILE__) . '/' . 'top/RequestCheckUtil.php');
require_once(dirname(__FILE__) . '/' . 'top/ResultSet.php');
require_once(dirname(__FILE__) . '/' . 'top/TopClient.php');
require_once(dirname(__FILE__) . '/' . 'top/request/OpenimUsersAddRequest.php');
require_once(dirname(__FILE__) . '/' . 'top/request/OpenimUsersGetRequest.php');
require_once(dirname(__FILE__) . '/' . 'top/request/OpenimUsersUpdateRequest.php');
require_once(dirname(__FILE__) . '/' . 'top/request/OpenimUsersDeleteRequest.php');

class top_sdk extends CI_Model
{

    const APPKEY = '23000538';
    const SECRETKEY = '9a7c89eb953701f8cdebf1832fcd48d9';

    private $db = '';
    private $email = '';
    private $mobile = '';
    private $taobaoid = '快问小美';
    private $userid = '';
    private $password = '';
    private $groupid = '157099046'; //'157099046';

    private $c = '';

    public function __construct()
    {
        $this->db = $this->load->database('default', TRUE);
        $this->c = new TopClient;
        $this->c->appkey = self::APPKEY;
        $this->c->secretKey = self::SECRETKEY;
    }

    /**
     * @return string
     */
    public function getGroupid()
    {
        return $this->groupid;
    }

    /**
     * @param string $groupid
     */
    public function setGroupid($groupid)
    {
        $this->groupid = $groupid;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getTaobaoid()
    {
        return $this->taobaoid;
    }

    /**
     * @param string $taobaoid
     */
    public function setTaobaoid($taobaoid)
    {
        $this->taobaoid = $taobaoid;
    }

    /**
     * @return string
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param string $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return Array
     */
    public function getOpenIM($uid = 0)
    {

        if (strlen($uid) != 32) {
            //echo intval($uid);
            if ($this->isOpenIMUser($uid) > 0) {

                $user = $this->getDBUser($uid);

                $this->setUserid($user[0]['id']);
                $this->setPassword(md5($user[0]['id']));
                //echo '1';
                return array('openid' => $this->getUserid(), 'password' => $this->getPassword(), 'groupid' => $this->getGroupid(), 'taobaoid'=>$this->getTaobaoid(), 'debug'=>1);
            } else {
                //echo '2';
                $r = $this->addUser($uid);
                if (!isset($r->code)) {
                    //echo '3';
                    if ($this->updateOpenIMUser($uid)) {
                        //echo '4';
                        return array('openid' => $this->getUserid(), 'password' => $this->getPassword(), 'groupid' => $this->getGroupid(), 'taobaoid'=>$this->getTaobaoid(), 'debug'=>4);
                    }
                } else {
                    //echo '5';
                    return array();
                }
            }
        } else {

            if ($this->isOpenIMUser($uid, 1) > 0) {

                $user = $this->getDBUser($uid, 1);

                $this->setUserid($user[0]['id']);
                $this->setPassword(md5($user[0]['id']));
                //echo '12';
                return array('openid' => $this->getUserid(), 'password' => $this->getPassword(), 'groupid' => $this->getGroupid(), 'taobaoid'=>$this->getTaobaoid(), 'debug'=>12);
            } else {
                //echo '6';
                $r = $this->getAnonymousUser($uid);

                if (!isset($r->code)) {
                    //echo '7';
                    if ($this->db->insert('anonymous_user', array('id' => $uid))) {
                        return array('openid' => $this->getUserid(), 'password' => $this->getPassword(), 'groupid' => $this->getGroupid(), 'taobaoid'=>$this->getTaobaoid(), 'debug'=>7);
                    }
                } else {
                    //echo '8';
                    return array();
                }

            }
        }
    }


    public function addUser($uid = 0)
    {
        $userinfos = array();
        $req = new OpenimUsersAddRequest();

        $user = $this->getDBUser($uid);
        $this->setUserid($user[0]['id']);
        $this->setPassword(md5($user[0]['id']));

        $userinfos['email'] = $this->getEmail();
        $userinfos['mobile'] = $this->getMobile();
        $userinfos['taobaoid'] = $this->getTaobaoid();
        $userinfos['userid'] = $this->getUserid();
        $userinfos['password'] = $this->getPassword();

        $req->setUserinfos(json_encode($userinfos));

        return $this->c->execute($req);
    }

    public function delUser($uid = 0)
    {
        $req = new OpenimUsersDeleteRequest();
        $req->setUserids($uid);
        return $this->c->execute($req);
    }

    public function getUser($uid){

        $req = new OpenimUsersGetRequest();
        $req->setUserids($uid);
        return $this->c->execute($req);
    }

    public function getAnonymousUser($uid = 0){

        $req = new OpenimUsersAddRequest();

        $this->setUserid($uid);
        $this->setPassword(md5($uid));

        $userinfos['email'] = $this->getEmail();
        $userinfos['mobile'] = $this->getMobile();
        $userinfos['taobaoid'] = $this->getTaobaoid();
        $userinfos['userid'] = $this->getUserid();
        $userinfos['password'] = $this->getPassword();

        $req->setUserinfos(json_encode($userinfos));

        return $this->c->execute($req);
    }

    public function getDBUser($uid = 0, $type=0)
    {
        if($type == 1){
            $this->db->select('id', 'password');
            $this->db->where('id', $uid);
            return $this->db->get('anonymous_user')->result_array();
        }else{
            $this->db->select('id', 'password');
            $this->db->where('id', $uid);
            return $this->db->get('users')->result_array();
        }
    }

    public function isOpenIMUser($uid = 0, $type = 0)
    {
        if($type == 1){
            $this->db->where('id', $uid);
            $this->db->where('openim', 1);
            return $this->db->get('anonymous_user')->num_rows();
        }else{
            $this->db->where('id', $uid);
            $this->db->where('openim', 1);
            return $this->db->get('users')->num_rows();
        }
    }

    public function updateOpenIMUser($uid = 0)
    {
        $this->db->where('id', $uid);
        return $this->db->update('users', array('openim' => 1));
    }

    public function updateUser()
    {

    }


}