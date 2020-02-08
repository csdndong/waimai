<?php 
/*
app消息通知类
*/
 
class apppsyclass
{
	private $gwUrl = 'http://app.waimairen.com/gt.php?';//访问地址
	private $user;//用户名
	private $secret;//密匙
	private $actime;//时间搓
	private $sign;//签名 
	private $otherlink; 
	private $xmmsg;
	private $xmuserlist;
	 
	function __construct(){ 	  
	  	$this->secret =  Mysite::$app->config['appsecret3'];
	  	$this->user =  Mysite::$app->config['appuser3'];  
	  	$this->actime = time();
	  	$checksign = md5(md5($this->user.$this->actime).$this->secret);
	  	$this->sign = $checksign; 
	  	$this->otherlink = '';
		$this->noticetype = 1;
		$this->xmbao = Mysite::$app->config['xmbao3'];//小米包名
		$this->xmtitle =  Mysite::$app->config['xmtitle3'];//小米推送标题
		$this->miuiKey =  Mysite::$app->config['miuiKey3'];;//小米的key值
		$this->xmapi = 'https://api.xmpush.xiaomi.com/v2/message/regid';
		$this->xmmsg = '';
		$this->xmerror = '';
		$this->xmuserlist = array();//小米推送的用户列表
		$this->gtuserids = '';//个推用户id集
    }
	//设置用户所有数据
	function SetUserlist($userlist = array()){
		$newarray = array(); 
		if(is_array($userlist) && count($userlist) > 0){
			foreach($userlist as $key=>$value){
					if(!empty($value['userid'])){
						$newarray[] = $value['userid'];
					}
					//可能存在未更新数据库的情况
					if(isset($value['xmuserid']) && !empty($value['xmuserid'])){
						$this->xmuserlist[] = $value['xmuserid'];
					}
			}
		}
		$this->gtuserids = join(',',$newarray);
		return $this;
	}
    
   function bklink(){ 
   	   $weblink = $this->gwUrl.'user='.$this->user.'&sign='.$this->sign.'&actime='.$this->actime.'&noticetype='.$this->noticetype.$this->otherlink;  
	    logwrite($weblink);
    	$contentcccc =  file_get_contents($weblink);   
    	return $contentcccc;
   }
   /*
   $userID       用户ID
   $channelid  设备ID
   $message  用户通知显示信息
   $othermsg='' //其他  可以为空
   */
	function sendbytag($message,$othermsg='',$tag='',$messagetype=1){
	   $this->otherlink = '&userID=5&channelid=m&message='.$message.'&othermsg='.$othermsg.'&messagetype='.$messagetype.'&sendall=2&tag='.$tag; 
	    $code = $this->bklink();
		$this->xmmsg = $message;
	    return $this->checkcode($code); 
    }
   function sendmsg($userID,$channelid,$message,$othermsg='',$messagetype=1)
   {   
   	  $this->otherlink = '&userID='.$userID.'&channelid='.$channelid.'&message='.$message.'&othermsg='.$othermsg.'&messagetype='.$messagetype; 
	    $code = $this->bklink();
		$this->xmmsg = $message;
	    return $this->checkcode($code);  
   } 
   function sendNewmsg($message,$othermsg){
	   if(!empty($this->gtuserids)){
			$this->otherlink = '&userID='.$this->gtuserids.'&channelid=&message='.$message.'&othermsg=1&messagetype=1'; 
			$code = $this->bklink();
			if(!empty($othermsg)){
				$this->xmmsg = $othermsg;
				$this->xmtitle = $message;
			}else{
				$this->xmmsg = $message;
			}
			if( $this->pushxm() == false ){
				  logwrite($this->xmerror);
			}
			return $this->checkcode($code);  
	   }else{
			$this->xmmsg = $othermsg;
			 if( $this->pushxm() == false ){
				  logwrite($this->xmerror);
			  }
			return $this->checkcode('ok'); 
	   } 
		
   }
   function sendall($message,$othermsg='',$messagetype=1){
   	   $this->otherlink = '&userID=5&channelid=m&message='.$message.'&othermsg='.$othermsg.'&messagetype='.$messagetype.'&sendall=3'; 
	    $code = $this->bklink();
		$this->xmmsg = $message;
	    return $this->checkcode($code); 
   }
	function checkcode($code){  
		  if($code == 'ok'){ 
			return 'ok';//发送信息成功 
	 	  }else{
	 	    return $code;//其他错误
	 	  } 
	} 
	//message
	function pushxm(){   
		if($this->xmmsg == null){
			$this->xmerror = '信息为空'; 
			return false;
		} 
		if(count($this->xmuserlist) < 1){ 
			$this->xmerror = '用户为空'; 
			return false;
		} 
		if(empty($this->xmbao)){
			$this->xmerror = '包名为空'; 
			return false;
		}
		if(empty($this->miuiKey)){
			$this->xmerror = 'key为空'; 
			return false;
		}
		if(empty($this->xmtitle)){
			$this->xmerror = '通知栏为空'; 
			return false;
		} 
		$checkinfo = $this->doxiaomi($this->xmtitle,$this->xmmsg);
		$backdata = json_decode($checkinfo,true);
		if(isset($backdata['result'])){
			if($backdata['result'] == 'ok'){
				return true;
			}else{ 
				$this->xmerror = $backdata['code']; 
			return false;
			}
		}else{
			$this->xmerror = $checkinfo;  
			return false;
		} 
		
	}
	
	private function doxiaomi($title,$content){ 
			$data['payload'] = $content;
			// 	App的包名。备注：V2版本支持一个包名，V3版本支持多包名（中间用逗号分割）。
			$data['restricted_package_name']  = $this->xmbao;
			/*
			pass_through的值可以为：

				0 表示通知栏消息
				1 表示透传消息 
			*/
			$data['pass_through'] =0;
			//通知栏展示的通知的标题。
			$data['title'] = $title;
			// 	通知栏展示的通知的描述。
			$data['description'] = $content; 
			if(is_array($this->xmuserlist)){ 
				$data['registration_id']=join(',',$this->xmuserlist);//注册的设备号
			}else{
				$data['registration_id']=$this->xmuserlist;//注册的设备号
			}

			/*
			notify_type的值可以是DEFAULT_ALL或者以下其他几种的OR组合： 
			DEFAULT_ALL = -1;
			DEFAULT_SOUND  = 1;  // 使用默认提示音提示；
			DEFAULT_VIBRATE = 2;  // 使用默认震动提示；
			DEFAULT_LIGHTS = 4;   // 使用默认led灯光提示；  
			*/
			$data['notify_type'] = 1;  
			$data['notify_id'] = 0;
			$data['time_to_live'] = 1000;
			$data['extra.notify_foreground'] = 0;
			$data['extra.notify_effect'] = 1;
			$data['extra.intent_uri']='intent:#Intent;component='.$this->xmbao.'/.MainActivity;end';
			/* intent:#Intent;component=com.xiaomi.mipushdemo/.NewsActivity;end
				com.malidiano.tcshop. 
				铃声只能使用当前app内的资源，URI格式满足 android.resource://your packagename/XXX/XXX。
				铃声文件放在Android app的raw目录下。
				只适用于通知栏消息。
				存储的声音文件需要有扩展名，但是不要把扩展名写在uri中 
			*/
			$data['extra.sound_uri'] = 'android.resource://'.$this->xmbao.'/raw/wave';

			// extra.key=value  服务器端还可以定义一些以”extra.”开头的POST参数，POST参数形式如：extra.key=value。注：至多可以设置10个key-value键值对。


			$temp = array_keys($data);
			  sort($temp); 
			// $data = array_sort($data,array_keys($data)); 
			$mdata = array();
			foreach($temp as $key=>$value){
				 // $mdata[$value] = urlencode($data[$value]);
				$mdata[$value] = $data[$value];
			} 
			$info = $this->postxm($this->xmapi,$mdata); 
			return $info;
	}
	public function postxm($url,$data='',$cookie=''){ // 模拟提交数据函数  
					$header = array( 
						'Authorization: key='.$this->miuiKey
					);   
					 
					$data = http_build_query($data);
					$curl = curl_init(); // 启动一个CURL会话
					curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
					 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
					curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
					// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
					//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
					curl_setopt($curl, CURLOPT_COOKIE, $cookie);
					curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
					curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
					curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
					curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
					$tmpInfo = curl_exec($curl); // 执行操作
					if (curl_errno($curl)) {
						logwrite('xm_Errno'.curl_error($curl));//捕抓异常
					}
					curl_close($curl); // 关闭CURL会话 
					return $tmpInfo; // 返回数据  
					
	}
	

}
?>