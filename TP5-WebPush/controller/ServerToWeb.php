<?php            
namespace app/index/controller:

class WebPush{

            public function push{

	    	//后台主动前台进行推送
	    	$postUrl = 'http://goeasy.io/goeasy/publish'; //推送网址
             
                $appkey = 'your appkey';       //您的app key
                $channel = 'your channel';     //您的目标channel
                $content = json_encode($data); //推送的消息内容,消息一定必须是字符串，如果你要推送的$data类型为数组的话，一定要转化成JSON字符串，然后在前端JSON解析一下
											   //当然如果$data本身就为字符变量,可以直接$content = $data;
                                                         
                //准备好POST数据
	    	$curlPost = array('appkey' => $appkey,'channel' => $channel,'content'=> $content);
         
                //开始推送
	    	$ch = curl_init();//初始化curl         
	    	curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页         
	    	curl_setopt($ch, CURLOPT_HEADER, 0);//设置header         
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上        
	    	curl_setopt($ch, CURLOPT_POST, 1);//post提交方式         
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);         
	    	$data = curl_exec($ch);//运行curl         
	    	curl_close($ch);
            }
}