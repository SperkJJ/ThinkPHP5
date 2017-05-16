<?php
/********************
	极光推送
********************/

namespace app/index/controller:

Class Jpush()
 
    public function Push{
        //获取极光推送相关配置信息
        $config  = config('jpush');
        $Jpush = new \api\Jpush($config);
		$param = array();
		
	    //数据案例
	    $param['receiver'] =  array('alias' => '1');                      //array    目标人群：tag 标签；tag_and 标签AND；alias 别名；registration_id 注册ID
		/*
		以上几种类型至少需要有其一。如果值数组长度为 0，表示该类型不存在。
		这几种类型可以并存，多项的隐含关系是 AND，即取几种类型结果的交集。
		例如：
		先计算 tag 中字段 tag1 和 tag2 的结果 tag1或tag2=A;
		再计算 tag_and 中字段 tag3 和 tag4 的结果 tag3且tag4=B;
		再计算 tag_not 中字段 tag5 和 tag6 的结果 非(tag5或tag6)=C 。
		最终的结果为 A且B且C 。 
		*/
		$param['title'] = '淘宝';                                                      //string   通知栏展示的App名称
		$param['content'] = '你有一条新订单'                                           //string   通知栏展示的内容
		$param['extras'] = array('code' => '1','content' => '你有一条新订单')          //array    附加字段
		$param['m_time'] = '3600'                                                      //string   保存离线时间的秒数默认为一天(可不传)单位为秒 
	
        $res = $Jpush ->push($param);
        if($res)
            echo '推送成功！';
        else 
            echo '推送失败！';
    };
	
}   
	
