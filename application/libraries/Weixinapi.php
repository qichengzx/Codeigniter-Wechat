<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixinapi{

    public $token           = '';
    public $appid           = '';
    public $secret          = '';
    public $access_token    = '';

    public $request;
    public $debug           = TRUE;
    public $CI;

    public function __construct() {
        //error_reporting(E_ALL);
        log_message('debug', "Weixinapi Class Initialized.");
        $this->CI = &get_instance();
        $this->valid();
    }

    // 用于接入验证
    public function _valid(){

        $signature  = $this->CI->input->get('signature');
        $timestamp  = $this->CI->input->get('timestamp');
        $nonce      = $this->CI->input->get('nonce');

        $tmp_arr = array($this->token, $timestamp, $nonce);
        sort($tmp_arr);
        $tmp_str = implode($tmp_arr);
        $tmp_str = sha1($tmp_str);

        if( $tmp_str == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function valid(){
        $echostr = $this->CI->input->get('echostr');
        if (!empty($echostr)){
            echo $echostr;
        //普通消息
        }else{
            return;
        }
    }


    /**
     * 获取本次请求中的参数，不区分大小
     *
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    public function getRequest($param = FALSE) {
        if ($param === FALSE) {
            return $this->request;
        }

        $param = strtolower($param);

        if (isset($this->request[$param])) {
            return $this->request[$param];
        }

        return NULL;
    }

    /**
     * 获取用户信息
     * @return string
     */
    public function _userinfo( $openid ){
        
        $access = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->token."&openid=".$openid."&lang=zh_CN");
        $result = json_decode($access,true);
        //print_r($result);
        return $result;   
       
        
    }

    /**
     * 创建分组
     * @return string
     */
    public function _creat_user_group( $name ){

    }   


    /**
     * 获取所有分组
     * @return string
     */
    public function _get_group_list(){
        $this -> access_token = $this -> _access_token();
        $access = file_get_contents("https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$this -> access_token);
        $result = json_decode($access,true);
        //print_r($result);
        return $result;
    }


    /**
     * 查询用户所在分组
     * @param   string  $openid
     * @return  string
     */
    public function _get_group_byid( $openid ){
        $this -> access_token = $this -> _access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=".$this -> access_token;
        $post_data = json_encode(array("openid" => $openid));
        //print_r($post_data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);

        //打印获得的数据
        //print_r($output);
        $result = json_encode($output,true);
        return $output;
    }

    //移动用户到指定分组
    //102，活动分组
    public function _update_user_group( $openid , $groupid ){

        $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$this -> access_token;
        $post_data = json_encode(array("openid" => $openid,'to_groupid'=>$groupid));
        //print_r($post_data);
       
        $ch = curl_init($url);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($post_data))                                                                       
        );     
        $output = curl_exec($ch);
        $result = json_decode($output,true);
        curl_close($ch);

        if ( $result['errmsg'] == "ok" ) {
            return true;
        }else{
            return false;
        }
    }



    /**
     * 获取access_token
     * @return string
     */
    public function _access_token(){
        
        $access = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret);
        $result = json_decode($access,true);
        //print_r($result);
        if( $result['access_token'] ){
            return $result['access_token'];    
        }else{
            return NULL;
        }
        
    }





}