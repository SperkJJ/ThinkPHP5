<?php
/********************
	极光推送
********************/

namespace app/index/controller:

Class Jpush()
 
    private function Push{
        //获取极光推送相关配置信息
        $config  = config('jpush');
        $Jpush = new \api\Jpush($config);
		$param = array();
		/**
		 * 
		 * $param['receiver']  array    目标人群：tag 标签；tag_and 标签AND；alias 别名；registration_id 注册ID
		 * $param['title']     string   通知栏展示的App名称
		 * $param['content']   string   通知栏展示的内容
		 * $param['extras']    array    附加字段
		 * $param['m_time']    string   保存离线时间的秒数默认为一天(可不传)单位为秒 
		 */
        $res = $Jpush ->push($param);
        if($res)
            return true;
        else 
            return false;
    };
	
}   
	
