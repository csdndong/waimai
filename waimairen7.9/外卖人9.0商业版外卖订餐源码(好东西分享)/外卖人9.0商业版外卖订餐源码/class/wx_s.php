<?php 

/**
 * @class wx_s  微信menu 和客服信管理类
 xxxx
 
 */
class wx_s
{
	private $wxtoken;//微信自定义 token
	private $wxappid; //微信  appid
	private $wxsecret;//微信  secret
	public $access_token; //操作令牌 
	private $errId; //错误号
	private $menulist;//菜单信息
	private $ticket;
	private $fxcode;
	private $userlist;
	private $lookuser;
	private $proxypassword = false; 
	private $sessionKey = '123456';//登录后所持有的SESSION KEY，即可通过login方法时创建 
	private $client;
	private  $errorcode = array(
	'-1'=>'系统繁忙',
   '0'=>'请求成功',
   '40001'=>'获取access_token时AppSecret错误，或者access_token无效',
   '40002'=>'不合法的凭证类型',
   '40003'=>'不合法的OpenID',
   '40004'=>'不合法的媒体文件类型',
   '40005'=>'不合法的文件类型',
   '40006'=>'不合法的文件大小',
   '40007'=>'不合法的媒体文件id',
   '40008'=>'不合法的消息类型',
   '40009'=>'不合法的图片文件大小',
   '40010'=>'不合法的语音文件大小',
   '40011'=>'不合法的视频文件大小',
   '40012'=>'不合法的缩略图文件大小',
   '40013'=>'不合法的APPID',
   '40014'=>'不合法的access_token',
   '40015'=>'不合法的菜单类型',
   '40016'=>'不合法的按钮个数',
   '40017'=>'不合法的按钮个数',
   '40018'=>'不合法的按钮名字长度',
   '40019'=>'不合法的按钮KEY长度',
   '40020'=>'不合法的按钮URL长度',
   '40021'=>'不合法的菜单版本号',
   '40022'=>'不合法的子菜单级数',
   '40023'=>'不合法的子菜单按钮个数',
   '40024'=>'不合法的子菜单按钮类型',
   '40025'=>'不合法的子菜单按钮名字长度',
   '40026'=>'不合法的子菜单按钮KEY长度',
   '40027'=>'不合法的子菜单按钮URL长度',
   '40028'=>'不合法的自定义菜单使用用户',
   '40029'=>'不合法的oauth_code',
   '40030'=>'不合法的refresh_token',
   '40031'=>'不合法的openid列表',
   '40032'=>'不合法的openid列表长度',
   '40033'=>'不合法的请求字符，不能包含\uxxxx格式的字符',
   '40035'=>'不合法的参数',
   '40038'=>'不合法的请求格式',
   '40039'=>'不合法的URL长度',
   '40050'=>'不合法的分组id',
   '40051'=>'分组名字不合法',
   '41001'=>'缺少access_token参数',
   '41002'=>'缺少appid参数',
   '41003'=>'缺少refresh_token参数',
   '41004'=>'缺少secret参数',
   '41005'=>'缺少多媒体文件数据',
   '41006'=>'缺少media_id参数',
   '41007'=>'缺少子菜单数据',
   '41008'=>'缺少oauthcode',
   '41009'=>'缺少openid',
   '42001'=>'access_token超时',
   '42002'=>'refresh_token超时',
   '42003'=>'oauth_code超时',
   '43001'=>'需要GET请求',
   '43002'=>'需要POST请求',
   '43003'=>'需要HTTPS请求',
   '43004'=>'需要接收者关注',
   '43005'=>'需要好友关系',
   '44001'=>'多媒体文件为空',
   '44002'=>'POST的数据包为空',
   '44003'=>'图文消息内容为空',
   '44004'=>'文本消息内容为空',
   '45001'=>'多媒体文件大小超过限制',
   '45002'=>'消息内容超过限制',
   '45003'=>'标题字段超过限制',
   '45004'=>'描述字段超过限制',
   '45005'=>'链接字段超过限制',
   '45006'=>'图片链接字段超过限制',
   '45007'=>'语音播放时间超过限制',
   '45008'=>'图文消息超过限制',
   '45009'=>'接口调用超过限制',
   '45010'=>'创建菜单个数超过限制',
   '45015'=>'回复时间超过限制',
   '45016'=>'系统分组，不允许修改',
   '45017'=>'分组名字过长',
   '45018'=>'分组数量超过上限',
   '46001'=>'不存在媒体数据',
   '46002'=>'不存在的菜单版本',
   '46003'=>'不存在的菜单数据',
   '46004'=>'不存在的用户',
   '47001'=>'解析JSON/XML内容错误',
   '48001'=>'api功能未授权',
   '50001'=>'用户未授权该api',
   '40054'=>'invalid sub button url domain');
	 //  微信access_token  服务令牌
	 //https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN  微信发送信息  body
	 //https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN  微信 创建菜单 
	 //https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN  微信  删除菜单
	 //  返回信息    
	 //  成功    {"errcode":0,"errmsg":"ok"} 
	 //  失败     {"errcode":40018,"errmsg":"invalid button name size"} 
	  function __construct(){ 	  
	  	$this->wxtoken =  Mysite::$app->config['wxtoken'];
	  	$this->wxappid =  Mysite::$app->config['wxappid']; 
	  	$this->wxsecret =  Mysite::$app->config['wxsecret']; 
    }
    //获取token
   function checktoken(){
      $config = new config('autorun.php',hopedir);   
	   	$tempinfo = $config->getInfo();
	    
	   	if(isset($tempinfo['access_token']) && isset($tempinfo['wx_time'])){
	   		 $btime = time() - $tempinfo['wx_time'];
	   		 if($btime < 7000){
	   		 	 $this->access_token = $tempinfo['access_token'];
	   		 	 return true;
	   		}
	   	   
	   	}  
	   	//通过post方法获取  当前token;
	   	$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wxappid.'&secret='.$this->wxsecret);
	   
	   	$info = json_decode($info,true);
	   	 
	   	if(isset($info['access_token'])){
	   		$test['access_token'] = $info['access_token'];
	   		$this->access_token = $info['access_token'];
	   		$test['wx_time'] = time();
	   		$config->write($test);
	   		return true;
	   	}else{
	   		$this->errId=$info['errcode'];
	   	   return false;
	   	} 
   }
   function gettoken(){
   	   if($this->checktoken()){
   	     return  $this->access_token;
   	   }else{
   	      return '获取失败';
   	   }
   }
   function menu(){
   	 if($this->checktoken()){
   	 	 $info = $this->vpost('https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->access_token); 
   	 	 
   	 	 $info = json_decode($info,true);
   	 	 if(isset($info['errcode'])){
   	 	   
   	 	    if($info['errcode'] == 0){
   	 	    	return true;
   	 	    }else{
   	 	    	 $this->errId = $info['errcode'];
   	 	        return false;
   	 	    }
   	 	 }
   	 	 
   	 	 $this->menulist = $info;
   	 	 return true; 
     }
     return false;
   }
   function savemenu($info){
      	if($this->checktoken()){
      	//	$data['body'] = json_encode($info);
      	//	echo $str;
      	/*
      	   $strpost = json_encode($info);
      	   logwrite($strpost);
      	   $strpost= preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $strpost);
          */
          logwrite($info);
      	   $info = $this->vpost('https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token,$info);
      	   
      	   $info = json_decode($info,true);
      	   if(isset($info['errcode'])){
      	     if($info['errcode'] == 0){
      	 	    	return true;
      	 	     }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	        return false;
      	 	    }
      	  }
      	  $this->errId('-1');
      	  return false;
      }else{
      	  return false;
      } 
   }
   function  tickets(){
   
   		if($this->checktoken()){
   		 
   			 $posttr = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
   		 
   				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->access_token,$posttr); 
   			 
	       	$info = json_decode($info,true);
	        
	        if(isset($info['errcode'])){
      	      if($info['errcode'] == 0){
      	 	    	return false;
      	 	     }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	      return false;
      	 	    }
      	  }
      	  $this->ticket = $info['ticket'];
      	  return true;
      
      }else{
       
      	return false;
      }
   }
   
   //上传永久店铺扫描地址
   function makeforever($shopid){
	   if($this->checktoken()){ 
				$posttr = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "sp_'.$shopid.'"}}}';
				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->access_token,$posttr);  
				 
				$info = json_decode($info,true); 
				if(isset($info['errcode'])){
					if($info['errcode'] == 0){
						return false;
					}else{
						$this->errId = $info['errcode'];
						return false;
					}
				} 
//{"ticket":"gQH47joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2taZ2Z3TVRtNzJXV1Brb3ZhYmJJAAIEZ23sUwMEmm3sUw==","expire_seconds":60,"url":"http:\/\/weixin.qq.com\/q\/kZgfwMTm72WWPkovabbI"}
				$this->makeurl = $info['url'];
				$this->ticket = $info['ticket'];
				return true; 
		}else{ 
			return false;
		}
   }
   //根据shopid创建临时二维码
   function creatPassEwm($shopid){
	   if($this->checktoken()){ 
				$posttr = '{"expire_seconds":"1800","action_name": "QR_STR_SCENE", "action_info":{"scene": {"scene_str": "bd_'.$shopid.'"}}}';
				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->access_token,$posttr); 				
				$info = json_decode($info,true); 
				$imgurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.UrlEncode($info['ticket']);
				return $imgurl;				
		}else{ 
			return false;
		}
   }	
public function curl_get_content($url,$data='',$cookie=''){
	
	 $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
   // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
 	
    if (curl_errno($curl)) {
       echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo;
	}
	
	   function vpost($url,$data='',$cookie=''){ // 模拟提交数据函数
      /*1方案*/
   	      
              $options = array(  
                   'http' => array(  
                       'method' => 'POST',  
                       // 'content' => 'name=caiknife&email=caiknife@gmail.com',  
                       'content' => $data,  
                   ),  
               );  
             
               $result = file_get_contents($url, false, stream_context_create($options));  
             
               return $result;  
			   /*2方案
			   
			   
			     $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
   // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
       echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
	
	
	   */
 }

    //上传永久店铺扫描地址
   function makefxcode($uid){
	   if($this->checktoken()){ 
			$posttr = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "fx_'.$uid.'"}}}';
			$info = $this->curl_get_content('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->access_token,$posttr);   
			$info = json_decode($info,true); 
			if(isset($info['errcode'])){
				if($info['errcode'] == 0){
					return false;
				}else{
					$this->errId = $info['errcode'];
					return false;
				}
			} 				 
			$this->fxcode = $info['url'];
			$this->ticket = $info['ticket'];		 
			return true; 
		}else{ 
			return false;
		}
   }
    
   
   function get_fxcodeurl($uid){
	   $this->mysql = new mysql_class();
	   $member = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid = '".$uid."'");
	  
	   if( isset($this->fxcode)  && !empty($this->fxcode)  && $uid > 0  && !empty($member)  && empty($member['fxcode']) ){
  		    if(!empty($this->fxcode)){	 
				include_once(hopedir.'/plug/tool/phpqrcode.php');  
				$value = urldecode($this->fxcode);
				$errorCorrectionLevel = 'M'; //容错级别  
				$matrixPointSize = 10; //生成图片大小  
 			 
				//生成二维码图片  
				QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 
				if(empty($member['logo'])){
					$logo = getImgQuanDir(Mysite::$app->config['userlogo']);
				}else{
 					 $logo = getImgQuanDir($member['logo']); 
				} 
				 
 				$logo = $logo; //准备好的logo图片  
				$QR = 'qrcode.png'; //已经生成的原始二维码图  
				$flag = time(); 
				$logo_rs = hopedir."/images/user/wxcode/".$flag.".png";//文件存放路径  
 			   $QR = imagecreatefromstring(file_get_contents($QR));  
 				if( !empty($logo) ){  
 					$logo = imagecreatefromstring($this->curl_file_get_contents($logo));  
					$QR_width = imagesx($QR); //二维码图片宽度  
					$QR_height = imagesy($QR); //二维码图片高度  
					$logo_width = imagesx($logo); //logo图片宽度  
					$logo_height = imagesy($logo); //logo图片高度  
					$logo_qr_width = $QR_width / 5; 
					$scale = $logo_width / $logo_qr_width; 
					$logo_qr_height = $logo_height / $scale; 
					$from_width = ($QR_width - $logo_qr_width) / 2; 
					imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height); 
				} 
				//输出图片  
				imagepng($QR, $logo_rs); 
				//重新组合图片并调整大小  
                 $wxcodeimgurl = Mysite::$app->config['siteurl'].'/images/user/wxcode/'.$flag.'.png';    
 				if(empty($member['fxcode'])){
				    $this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcode'=>$wxcodeimgurl),"uid='".$uid."'");	
				}
 				logwrite("生成二维码数据成功UID:".$uid);
			}else{
			    logwrite("phpqrcode生成二维码数据失败UID".$uid);
				return false; 
			}
	   }
	   
        return true; 
   }
   
   function curl_file_get_contents($durl){   
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $durl);
   curl_setopt($ch, CURLOPT_TIMEOUT, 2);
   curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
   curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
   $r = curl_exec($ch);
   curl_close($ch);   return $r;
 }
   
   function get_shopurl(){
        return $this->makeurl;//返回店铺二维码生成url
   }
   function get_img(){
   	 if($this->tickets()){
   		  
   		  return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.UrlEncode($this->ticket);
   	   
   	 }else{
   	    return '';
   	 }
   }
   
   
   function get_user($newxid = ''){
      
     if($this->checktoken()){
   			 
   				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->access_token.'&next_openid='.$newxid); 
	       	$info = json_decode($info,true);
	        if(isset($info['errcode'])){
      	     if($info['errcode'] == 0){
      	 	    	return false;
      	 	     }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	        return false;
      	 	    }
      	  }
      	  $this->userlist = $info;
      	  return true;
      
      }else{
      	return false;
      }
   }
   function showuserinfo($openid){
   	  if($this->checktoken()){ 
           
		   $info = $this->vpost('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN');   
           $info = json_decode($info,true);
           //logwrite("info*************".var_export($info,true));
	        if(isset($info['errcode'])){
      	     if($info['errcode'] == 0){
      	 	    	return true;
      	 	    }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	        return false;
      	 	    }
      	  }else{
             $this->lookuser =$info;
             return true;
          }
      	 
     }else{
		 
        return false;
     }
   }
   function getone(){
      return  $this->lookuser;
   }
   function userlist(){
     	return  $this->userlist; 
   }
   //{"ticket":"gQG28DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0FuWC1DNmZuVEhvMVp4NDNMRnNRAAIEesLvUQMECAcAAA==","expire_seconds":1800}
   function returnmenu(){
   	
      return $this->menulist;
   }
   function sendmsg($msg,$useropenid){ 
   
      if($this->checktoken()){ 
      	  $poststr = '{"touser":"'.$useropenid.'","msgtype":"text","text":{"content":"'.$msg.'"}}'; 
   				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->access_token,$poststr); 
   			 
	       	$info = json_decode($info,true); 
	        if(isset($info['errcode'])){
      	     if($info['errcode'] == 0){
      	 	    	return true;
      	 	     }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	        return false;
      	 	    }
      	  }
      	  
      	  return true;
      
      }else{
      	return false;
      }
   }
   //发送微信模板消息
	function send_tem_msg($orderid,$useropenid,$type,$parent_type){
		$temp = new templateclass();
		$data = $temp->get_template($orderid,$useropenid,$type,$parent_type);
		if($this->checktoken()){
			$info = $this->curl_get_content('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->access_token,$data); 
			$info = json_decode($info,true);
			#print_r($info);exit;
			if(isset($info['errcode'])){
				if($info['errcode'] == 0){
					return true;
				}else{
					$this->errId = $info['errcode'];
					return false;
				}
			}
		}else{
			return false;
		}
	}
   /* 
   根据OpenID列表群发【订阅号不可用，服务号认证后可用】
接口调用请求说明
http请求方式: POST
https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=ACCESS_TOKEN 	

文本：

{
   "touser":[
    "OPENID1",
    "OPENID2"
   ],
    "msgtype": "text",
    "text": { "content": "hello from boxer."}
}

	*/
   
    function qunsendmsg($msg,$useropenidaarr){ 		//高级群发接口
      if($this->checktoken()){ 
      	  $poststr = '{"touser":['.$useropenidaarr.'],"msgtype":"text","text":{"content":"'.$msg.'"}}'; 
   				$info = $this->vpost('https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->access_token,$poststr); 
   			 
	       	$info = json_decode($info,true); 
	        if(isset($info['errcode'])){
      	     if($info['errcode'] == 0){
      	 	    	return true;
      	 	     }else{
      	 	     	 $this->errId = $info['errcode'];
      	 	        return false;
      	 	    }
      	  }
      	  
      	  return true;
      
      }else{
      	return false;
      }
   }
   
   
   
   function err(){ 
   	  
      return  $this->errorcode[$this->errId]; 
   }
   
   
   
   
   
   
   // JSSDK
   
  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
	
    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol =    Mysite::$app->config['map_comment_link'];//(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);
  #  print_r($signature);
    $signPackage = array(
      "appId"     => $this->wxappid,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  //获取ticket
   function getJsApiTicket(){
	   
        $config = new config('autorun.php',hopedir);   
	   	$tempinfo = $config->getInfo();
	   	if(isset($tempinfo['ticket']) && isset($tempinfo['wcx_time'])){
	   		 $btime = time() - $tempinfo['wcx_time'];
			 if($btime < 7000){
	   		 	 $ticket= $tempinfo['ticket'];
				
	   		 	 return $ticket;
	   		}
	   	   
	   	}  
		
 	 	 $accessToken = $this->gettoken();
		
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
  	/*	    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
	  
	   */
	   	$url = $this->vpost('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi');
	
		$info = json_decode($url,true);	 
	   	if(isset($info['ticket'])){
	   		$test['ticket'] = $info['ticket'];
	   		$ticket = $info['ticket'];
	   		$test['wcx_time'] = time();
	   		$config->write($test);
	   		 return $ticket;
	   	}else{
	   		$this->errId=$info['errcode'];
	   	   return false;
	   	} 
   }
   /* http请求方式: GET
			http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID
 */
	//下载多媒体文件
    function saveMedia($url){
		
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //对body进行输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
       
        curl_close($ch);
        $media = array_merge(array('mediaBody' => $package), $httpinfo);
        
        //求出文件格式
        preg_match('/\w\/(\w+)/i', $media["content_type"], $extmatches);
        $fileExt = $extmatches[1];
		
        $filename = time().rand(100,999).".{$fileExt}";
        $dirname = "./upload/wximages/";
        if(!file_exists($dirname)){
            mkdir($dirname,0777,true);
        }
		
	#	logwrite(var_export($media['mediaBody'],true),1);
		
        file_put_contents($dirname.$filename,$media['mediaBody']);
        return Mysite::$app->config['siteurl'].'/upload/wximages/'.$filename;
    }
	
	 
   

}


?>