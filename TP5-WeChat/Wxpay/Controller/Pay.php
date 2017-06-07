<?php

/**
 * Created by Sperk.
 * 微信支付控制器
 */
 
namespace app\index\controller;
header('Content-type:text/html;charset=utf-8');


class Pay{

    private $app_id = '';                                                      // Your appid
    private $mch_id = '';                                                      // Your 商户号
    private $makesign = '';                                                    // Your API支付的签名(在商户平台API安全按钮中获取)
    private $parameters=NULL;
    private $notify='http://www.ctz.cn/wxpay.php';                             //配置回调地址(给pays中转文件上传到根目录下面)
    private $app_secret='';                                                    //Your appSecret 微信官方获取
    public $error = 0;
    public $orderid = null;
    public $openid = '';
	
    //进行微信支付
    public function wxPay(){

        $reannumb = $this->randomkeys(6);  //生成随机数 以后可以当做 订单号
        $pays ='1';                        //获取需要支付的价格·
		
        #插入语句书写的地方
        $conf = $this->payconfig('Bm'.$reannumb,$pays * 100, '报名费用支付');
        if (!$conf || $conf['return_code'] == 'FAIL') exit("<script>alert('对不起，微信支付接口调用错误!" . $conf['return_msg'] . "');history.go(-1);</script>");
		$this->orderid = $conf['prepay_id'];
        //微信相关配置如果不正的话，进入支付页面会出现错误信息

	   //生成页面调用参数
        $jsApiObj["appId"] = $conf['appid'];
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=" . $conf['prepay_id'];
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->MakeSign($jsApiObj);
        
        $json = json_encode($jsApiObj);
	//返回支付页面，并将相关参数返回给JS
	return view('Pay',['parameters' => $json]);
    }


    //订单管理
    #微信JS支付参数获取#
    protected function payconfig($no, $fee, $body)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $data['appid'] = $this->app_id;
        $data['mch_id'] = $this->mch_id;                       //商户号
        $data['device_info'] = 'WEB';
        $data['body'] = $body;
        $data['out_trade_no'] = $no;                           //订单号
        $data['total_fee'] = $fee;                             //金额
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];   //ip地址
        $data['notify_url'] = $this->notify;
        $data['trade_type'] = 'JSAPI';
        $data['openid'] = $_SESSION['openid'];                 //获取保存用户的openid
        $data['nonce_str'] = $this->createNoncestr();
        $data['sign'] = $this->MakeSign($data);
		
		
        $xml = $this->ToXml($data);
        $curl = curl_init(); // 启动一个CURL会话
		
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		
        //设置header
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
		
        //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        $arr = $this->FromXml($tmpInfo);
        return $arr;
    }

    /**
     *    作用：产生随机字符串，不长于32位
     */
    public function createNoncestr($length = 32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *    作用：产生随机字符串，不长于32位
     */
    public function randomkeys($length)
    {
        $pattern = '1234567890123456789012345678905678901234';
        $key = null;
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 30)};    //生成php随机数
        }
        return $key;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        //将XML转为array
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    protected function MakeSign($arr)
    {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        $string = $this->ToUrlParams($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->makesign;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    protected function ToUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

}
