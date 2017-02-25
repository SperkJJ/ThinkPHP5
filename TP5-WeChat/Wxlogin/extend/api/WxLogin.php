<?php
/**
 * 微信登录类
 */
namespace api;

class WxLogin {
    
    //appId
    private $appId = '';
    //appSecret
    private $appSecret = '';
    //redirect_uri
    private $redirect_uri = '';
    
    public function __construct($config = array())
    {
        $this->appId = $config['appId'];
        $this->appSecret = $config['appSecret'];
        $this->redirect_uri = $config['redirect_uri'];
    }
    
    /**
     * 获取微信授权网址
     *
     * @param  $state 参数  
     */
    public function get_authorize_url($state)
    {
        $authorize_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appId.'&redirect_uri='.$this->redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
        return $authorize_url;
    }
    
    /**
     * 根据code获取授权toke
     *
     * @param  $parameters
     */
    public function get_access_token($code)
    {
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appId.'&secret='.$this->appSecret.'&code='.$code.'&grant_type=authorization_code';  
        $res = $this->https_request($token_url);
        return json_decode($res);
    }
    
    /**
     * 根据access_token以及oppenid获取用户信息
     *
     * @param  $access_token
     * @param  $oppenid
     */
    public function get_userinfo($access_token,$oppenid)
    {
        $info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$oppenid;  
        $res = $this->https_request($info_url);
        return json_decode($res);
    }
    
    /**
     * https请求
     * @param  $url  请求网址
     */
    public function https_request($url , $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
