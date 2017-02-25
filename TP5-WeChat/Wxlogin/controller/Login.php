<?php
/*
 **
 ***********************************
	微信登录，用户授权成功回调地址
 ***********************************
 **
 */
namespace app\index\controller;

class  Weixin
{
	
	
	//用户授权成功的操作
	public function wxlogin()
	
    {    //当你设置微信回调网址设置为http://your domain/index/weixin/wxlogin时
	     //用户同意授权后，微信后台会访问该网站同时返回code参数
		 
		 $config = config('weixin');//获取微信相关配置
		 $wechat = new \api\WxLogin($config);
		 
		 if(isset($_GET['code'])){
			 
			 //通过code参数获取Access_Token
			 $token = $wechat ->get_access_token($_GET['code']);
			 //通过code参数获取用户信息
			 $info = $wechat ->get_userinfo($token->access_token, $token->openid);
			 //$info即为已经获得的用户的信息，数据格式为对象形式。如获取用户的openid,获取方式为$info->openid。
			 
			 //你的操作
			 ...
			 ...
			 ..
			 
		 }
    }
	
	//生成用户登录需要访问的网址
	//只有进入这个网址，用户才能进行授权
	public function get_authorize_url()
    {
         $config = config('weixin');//获取微信相关配置
		 $wechat = new \api\WxLogin($config);
		 $url  = $wechat->get_authorize_url($state);//state为你设置的参数，随便填
		 return $url;
    }
}


	
