<?php
/**
 * 阿里云短信验证码发送类
 */
namespace api;

class AliyunSms {
    // 保存错误信息
    public $error;
    // Access Key ID
    private $accessKeyId = '';
    // Access Access Key Secret
    private $accessKeySecret = '';
    // 签名
    private $signName = '';
    // 模版ID
    private $templateCode = '';
    //短信模板中参数
    private $templatePara = '';
    
    public function __construct($config = array()) {
		
        $this->accessKeyId = $config ['accessKeyId'];
        $this->accessKeySecret = $config ['accessKeySecret'];
        $this->signName = $config ['signName'];
        $this->templateCode = $config ['templateCode'];
        $this->templatePara = $config ['templatePara'];
    }
    
    private function percentEncode($string) {
        $string = urlencode ( $string );
        $string = preg_replace ( '/\+/', '%20', $string );
        $string = preg_replace ( '/\*/', '%2A', $string );
        $string = preg_replace ( '/%7E/', '~', $string );
        return $string;
    }
    /**
     * 签名
     *
     * @param  $parameters            
     * @param  $accessKeySecret            
     * @return string
     */
    private function computeSignature($parameters, $accessKeySecret) {
        ksort ( $parameters );
        $canonicalizedQueryString = '';
        foreach ( $parameters as $key => $value ) {
            $canonicalizedQueryString .= '&' . $this->percentEncode ( $key ) . '=' . $this->percentEncode ( $value );
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode ( substr ( $canonicalizedQueryString, 1 ) );
        $signature = base64_encode ( hash_hmac ( 'sha1', $stringToSign, $accessKeySecret . '&', true ) );
        return $signature;
    }
    /**
     * 发送验证码
     * 
     * @param unknown $mobile            
     * @param unknown $verify_code            
     *
     */
    public function send_verify($mobile, $verify_code) {
        $params = array (
                // 公共参数
                'SignName' => $this->signName,
                'Format' => 'JSON',
                'Version' => '2016-09-27',
                'AccessKeyId' => $this->accessKeyId,
                'SignatureVersion' => '1.0',
                'SignatureMethod' => 'HMAC-SHA1',
                'SignatureNonce' => uniqid (),
                'Timestamp' => gmdate ( 'Y-m-d\TH:i:s\Z' ),
                // 接口参数
                'Action' => 'SingleSendSms',
                'TemplateCode' => $this->templateCode,
                'RecNum' => $mobile,
                'ParamString' => '{"'.$this->templatePara.'":"' . $verify_code . '"}' 
        );
        // 计算签名并把签名结果加入请求参数
        $params ['Signature'] = $this->computeSignature ( $params, $this->accessKeySecret );
        // 发送请求
        $url = 'https://sms.aliyuncs.com/?' . http_build_query ( $params );
        
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        $result = json_decode ( $result, true );
        if (isset ( $result ['Code'] )) {
			//测试阶段可以开启，然后调取getErrorMessage方法获取具体错误信息
            //$this->error = $this->getErrorMessage ( $result ['Code'] );
            return false;
        }
        return true;
    }
     /**
      * 获取详细错误信息
      *
      * @param unknown $status            
      */
     public function getErrorMessage($status) {
        
         $message = array (
                 'InvalidDayuStatus.Malformed' => '账户短信开通状态不正确',
                 'InvalidSignName.Malformed' => '短信签名不正确或签名状态不正确',
                 'InvalidTemplateCode.MalFormed' => '短信模板Code不正确或者模板状态不正确',
                 'InvalidRecNum.Malformed' => '目标手机号不正确，单次发送数量不能超过100',
                 'InvalidParamString.MalFormed' => '短信模板中变量不是json格式',
                 'InvalidParamStringTemplate.Malformed' => '短信模板中变量与模板内容不匹配',
                 'InvalidSendSms' => '触发业务流控',
                 'InvalidDayu.Malformed' => '变量不能是url，可以将变量固化在模板中' 
         );
         if (isset ( $message [$status] )) {
             return $message [$status];
         }
         return $status;
     }
}
