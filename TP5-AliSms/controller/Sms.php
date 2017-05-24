<?php
namespace app/index/controller:
class Sms{
    public function send(){
    	// 读取阿里云短息相关配置信息
    	$config = config('aliyun_sms');
    	$data = [
        	'accessKeyId' => $config ['accessKeyId'],                 //your appid
        	'accessKeySecret' => $config ['accessKeySecret'],         //your app_secret
        	'signName'    => $config ['signName'],                    //your 签名
        	'templateCode' => $config ['templateCode'.$type],         //your 模板编号
        	'templatePara' => $config ['templatePara'.$type]          //your 模板中的变量
    	];
    	$sms = new \api\AliyunSms($data);
    	$status = $sms->send_verify($mobile, $veryify_code);
    	if(!$status)
		echo "短信发送失败！";
    	else
		echo "短信发送成功！";
    }	
}   
	
