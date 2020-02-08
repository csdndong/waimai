<?php


class 	snsclass{

   private $accesskey ='Of1WI4RxSIIQG4KN';//Of1WI4RxSIIQG4KN
   private $secret='eodU7okmdUfxNRtt2vYehBNfomj5GXDO'; //eodU7okmdUfxNRtt2vYehBNfomj5GXDO 
   private $templateId=	269; //
   private $sign ='【MI外卖】';
   private $sendurl ='http://api.1cloudsp.com/intl/api/v2/send';
   private $expire =1;

   // function __construct()
   // {
   //     $this->accesskey = $accesskey;
   //     $this->secret = $secret;
   //     $this->templateId = $templateId;
   //     $this->sign = $sign;
   //     $this->sendurl = $sendurl;
   //     $this->expire = $expire;

   //  }

    function SendCode($phone,&$code){
   
  	$ret = 1;
  	$msg ='发送失败';
    $code = rand(100000,999999);

    $params = array(
      'accesskey'=>$this->accesskey,
      'secret'=>$this->secret,
      'sign'=> $this->sign,
      'templateId'=>$this->templateId,
      'mobile'=>$phone,
      'content'=>$code
      // 'code'=>$code
      );

    $content = $this->httped($this->sendurl,$params);
   
  

    if($content){
    	$result = json_decode($content,true);
    	$error_code =$result['error_code'];
    	if($error_code ==0){
    		//状态为零短信发送成功
    		$ret =0;
    		$msg ='发送成功';
    	}else{
    		$msg = $result['reason'];
    	}

    }else{
    	$msg ='请求发送短信失败';
    }
    return array('ret'=>$ret,'msg'=>$msg);
  }

  function httped($url,$params,$ispost=1){
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$this->sendurl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
       return $data ;
  }
}