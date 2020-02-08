<?php  
/*** 
*    多通道推送
*    使用到的接口
*    
**/
class huaweiPush  implements pushinterface
{  
	private $title;
	private $content;
	private $userlist;
	private $sound;
	private $extdata;
	private $tokenUrl = "https://login.cloud.huawei.com/oauth2/v2/token";
	private $apiUrl   = "https://api.push.hicloud.com/pushsend.do?"; 
	

	public function __construct(){   
		$this->title = '';
		$this->content = '';
		$this->userlist = array();
		$this->sound = '';
		$this->extdata = ''; 
		$this->appSecret =  Mysite::$app->config['huawei_tsshop_appkey'];//'4c91a270e1bfd3430c5a08e02b613dfe';
		$this->appId =  Mysite::$app->config['huawei_tsshop_appid'];//'100407811';
		$this->token = '';
		$this->tokentime = '';
		$this->error = '';
	}  
    public function Title($Title){
		$this->title = $Title;
		return $this; 
	}
    public function Content($Content){
		$this->content = $Content;
		return $this;
	}
	public function UserList($userlist){
		$this->userlist = $userlist;
		return $this;
	}
	public function Sound($sound){
		$this->sound = $sound;
		return $this;
	}
	public function ExtData($extdata){
		$this->extdata = $extdata;
		return $this; 
	}
	public function ShopUid($uid){
		return $this;
	}
	public function SendMsg(){
		if(empty($this->appId)){
			$this->error = '未来设置推送key';
			return false;
		}
		if(empty($this->appSecret)){
			$this->error = '未设置推送Secret';
			return false;
		}
		if(empty($this->userlist) || count($this->userlist) == 0){
			$this->error = '接收用户未设置';
			return false;
		}
		if($this->checktoken()){
			$msgdata = array();
			$nsp_ctx='{"ver":"1", "appId":"'.$this->appId.'"}';
			$msgdata['access_token'] = $this->token;
			$msgdata['nsp_ts'] = time()+60;
			$msgdata['nsp_svc'] = 'openpush.message.api.send';
			$msgdata['device_token_list'] = json_encode($this->userlist);
			$payload = array();
			$payload['hps']['msg'] = array('type'=>1,'body'=>array('title'=>$this->title,'content'=>$this->content,'sound'=>$this->sound,'task_extra'=>$this->extdata));
			$msgdata['payload'] = json_encode($payload);
			// echo var_export($msgdata,true);
			$info = $this->postxm($this->apiUrl.'nsp_ctx='.urlencode($nsp_ctx),$msgdata);
			$info = trim($info);
			$tempinfo = json_encode($info,true);
			logwrite('huaweisend'.$info);
			if(isset($tempinfo['code']) && $tempinfo['code'] =='80000000'){
				
				return true;
			}else{
				$this->error = isset($tempinfo['msg'])?$tempinfo['msg']:$info; 
				return false;
			} 
		}  
	}
	public function SendNotify(){//发送通知  可以activity可以web
		if(empty($this->appId)){
			$this->error = '未来设置推送key';
			return false;
		}
		if(empty($this->appSecret)){
			$this->error = '未设置推送Secret';
			return false;
		}
		if(empty($this->userlist) || count($this->userlist) == 0){
			$this->error = '接收用户未设置';
			return false;
		}
		if($this->checktoken()){
			$msgdata = array();
			$nsp_ctx='{"ver":"1", "appId":"'.$this->appId.'"}';
			$msgdata['access_token'] = $this->token;
			$msgdata['nsp_ts'] = time()+60;
			$msgdata['nsp_svc'] = 'openpush.message.api.send';
			$msgdata['device_token_list'] = json_encode($this->userlist);
			$payload = array();
			$webarray = array('type'=>2,'param'=>array('url'=>'http://www.baidu.com'));//打开网页跳转
			$activityarray = array('type'=>3,'param'=>array('appPkgName'=>'com.example.m6wmr'));//打开app
			$payload['hps']['msg'] = array('type'=>3,'body'=>array('content'=>$this->title,'title'=>$this->content),'action'=>$activityarray);
			$msgdata['payload'] = json_encode($payload); 
			$info = $this->postxm($this->apiUrl.'nsp_ctx='.urlencode($nsp_ctx),$msgdata); 
			$info = trim($info);
			$tempinfo = json_encode($info,true);
			if(isset($tempinfo['code']) && $tempinfo['code'] =='80000000'){
				
				return true;
			}else{
				$this->error = isset($tempinfo['msg'])?$tempinfo['msg']:$info; 
				return false;
			}
			
		}  
		return false;
		
	}
	public function error(){
		return $this->error;
	} 
	//获取token
	private function checktoken(){
		$config = new config('autorun.php',hopedir);   
	   	$tempinfo_save = $config->getInfo();
	    
	   	if(isset($tempinfo_save['huawei_access_token']) && isset($tempinfo_save['huawei_expires_time'])){
	   		 $btime =  $tempinfo_save['huawei_expires_time'] -time();
	   		 if($btime > 0){
	   		 	 $this->token = $tempinfo_save['huawei_access_token'];
				 $this->tokentime = $tempinfo_save['huawei_expires_time']; 
	   		 	 return true;
	   		}
	   	   
	   	}  
		
		
		
		$freshdata = array();
		$freshdata['grant_type'] ='client_credentials';
		$freshdata['client_id'] =$this->appId;
		$freshdata['client_secret'] = $this->appSecret;
		$info = $this->postxm($this->tokenUrl,$freshdata);
		// print_r($freshdata);
		$tempinfo = trim($info);
		$tempinfo = json_decode($tempinfo,true); 
		if(isset($tempinfo['access_token'])){//将值存放起来
			$this->token = $tempinfo['access_token'];
			$this->tokentime = time()+$tempinfo['expires_in'];
			$test['huawei_access_token'] = $this->token; 
	   		$test['huawei_expires_time'] = $this->tokentime;
	   		$config->write($test); 
			return true;
		}else{
			$this->error = isset($tempinfo['error_description'])?$tempinfo['error_description']:$info; 
			return false;
		} 
	} 
	private function postxm($url,$data='',$cookie=''){  
					$data = http_build_query($data);
					$curl = curl_init(); // 启动一个CURL会话
					curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
					 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
					// curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
					// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
					//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
					curl_setopt($curl, CURLOPT_COOKIE, $cookie);
					curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
					curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
					curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
					curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
					$tmpInfo = curl_exec($curl); // 执行操作
					if (curl_errno($curl)) {
						 $tmpInfo = 'Errno';//捕抓异常.curl_error($curl)
					}
					curl_close($curl); // 关闭CURL会话  					
					return $tmpInfo; // 返回数据  
					
	}
}