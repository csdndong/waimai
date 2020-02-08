<?php
/*  ini_set('display_errors',1);            //错误信息
  ini_set('display_startup_errors',1);    //php启动错误信息
 error_reporting(-1); */
  /**
 * @class alidayuClass
 * @brief 阿里大鱼开发服务
 */ 
class alidayuclass
{
    private $appkey;//必传参数 控制平台中申请的App Key
    private $secretKey;//必传参数 控制平台中申请的App Secret
    private $domain; 

    //初始化函数
    function __construct()
    {
        $this->appkey = Mysite::$app->config['aliid'];
        $this->secretKey = Mysite::$app->config['alisecret'];
        $this->domain = 'dysmsapi.aliyuncs.com';
    } 
    /**
     * 短信通知
     * String $signName 短信签名
     * Array $smsParams 要发送的短信内容（数组形式）
     * String $phone  短信接收号码
     * String $SMSID  短信模板ID
     * String $extend  在“消息返回”中会透传回该参数（包含在返回的信息中）
     */
    public function sendTextMessage($signName, $smsParams, $phone, $SMSID, $extend = '')
    {
		 $params = array (); 
		// *** 需用户填写部分 *** 
		// fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
		 
		// fixme 必填: 短信接收号码
		$params["PhoneNumbers"] = $phone; 
		// fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$params["SignName"] = $signName; 
		// fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$params["TemplateCode"] = $SMSID; 
		// fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
		$params['TemplateParam'] =  $smsParams; 
		// fixme 可选: 设置发送短信流水号
	    // $params['OutId'] = "12345"; 
		// fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
	    // $params['SmsUpExtendCode'] = "1234567";  
		// *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
		if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
			$params["TemplateParam"] = json_encode($params["TemplateParam"], true);
		}  
 		// 此处可能会抛出异常，注意catch
		$content = $this->request($params);  
		return $content;
	 
  }
 public function request( $params, $security=false) {
         $apiParams = array_merge(array (
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $this->appkey,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params,array(
				"RegionId" => "cn-hangzhou",
				"Action" => "SendSms",
				"Version" => "2017-05-25",
		));
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $this->secretKey . "&",true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http')."://{$this->domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
 
}
