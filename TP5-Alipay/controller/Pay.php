<?php
/*
 *支付接口控制器 作者Sperk
 */

namespace app\index\controller;

class Pay{
	/*
	**
	*********************************** 
	支付宝下单接口
	***********************************
	**
	*/
	 public function Alipay(){ 
	    //导入SDK文件
        vendor('alipay.alipay_core#function');
        vendor('alipay.alipay_md5#function');
        vendor('alipay.alipay_submit#class');
		$config = config('alipay');               
		$alipay_config['partner'] =  $config['partner'];                                //Your PID
		$alipay_config['key'] =  $config['key'];                                        //Your Key
		$alipay_config['seller_id']	= $alipay_config['partner'];
		$alipay_config['notify_url'] = SITE_URL."index/pay/aliPayNotify";              //支付成功回调地址。阿里后台会向该地址后发送支付订单信息，让你后台进行确认
		$alipay_config['sign_type']    = strtoupper('MD5');
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';
		$alipay_config['payment_type'] = "1";
		$alipay_config['return_url'] = SITE_URL.'index/auth/payreturn';                 //支付成功跳转地址
		$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";             //WAP支付方法
		//$alipay_config['service'] = "create_direct_pay_by_user";                      //PC即时到账支付方法
		$alipay_config['anti_phishing_key'] = "";                                       //防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
		$alipay_config['exter_invoke_ip'] = $_SERVER["REMOTE_ADDR"];                    //客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1

	   //订单信息生成
        $total_fee = '商品价格';                                                            //支付金额，以元为单位。$total_fee='100'代表100元
        $body = '商品描述';                                                                 //商品描述
        $subject = "在线付款";                                                              //商品名称，不要使用充值、支付宝等字眼，会报错。
        $out_trade_no = "订单编号";                                                         //订单号，自己生成
        //WAP版支付才需要配置该参数
	    $show_url = "http://".$_SERVER["HTTP_HOST"];                                        //商品展示的超链接
		
		//构造请求的参数数组
		$parameter = array(
		"service"       => $alipay_config['service'],
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> $alipay_config['notify_url'],
		"return_url"	=> $alipay_config['return_url'],
		"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
		"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"show_url"	=> $show_url,
		"body"	=> $body,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "OK");
	    //$html_text是支付表单，这个表单是会自动跳转到支付宝支付页面
		//可查看alipay_submit.class.php中buildRequestForm()方法
        echo $html_text;
    }
	
	
	/*
	**
	*********************************** 
	支付宝支付回调地址
	***********************************
	**
	*/
	public function aliPayNotify(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
 	    $ret = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
 	    //当你支付成功后，阿里后台就会将你刚才的支付订单信息通过POST方式将数据发送给这个控制方法
		//现在你需要做的就是对这个订单进行验证(验证方法有很多种)，验证通过后修改你数据库中的状态
		//最后给支付宝后台返回'SUCCESS',这样整个交易过程才算结束
		
		//验证过程
		...
		...
		...
		
		ECHO 'SUCCESS';
	}

	
}