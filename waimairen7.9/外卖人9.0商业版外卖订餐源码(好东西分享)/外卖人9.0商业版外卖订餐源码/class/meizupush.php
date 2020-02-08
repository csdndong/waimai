<?php  
/*** 
*    多通道推送
*    使用到的接口
*    
**/
class meizuPush  implements pushinterface
{  
	private $title;
	private $content;
	private $userlist;
	private $sound;
	private $extdata; 
	private $messageUrl   = "http://server-api-mzups.meizu.com/ups/api/server/push/unvarnished/pushByPushId"; //根据pushid透传消息
	private $notifyUrl = "http://server-api-mzups.meizu.com/ups/api/server/push/varnished/pushByPushId";//根据pushid 发布通知
	private $notifyAllUrl= "http://server-api-mzups.meizu.com/ups/api/server/push/pushTask/pushToApp";
	

	public function __construct(){   
		$this->title = '';
		$this->content = '';
		$this->userlist = array();
		$this->sound = '';
		$this->extdata = ''; 
		$this->appSecret =  Mysite::$app->config['meizu_tsshop_secret'];//'4c91a270e1bfd3430c5a08e02b613dfe';
		$this->appId =  Mysite::$app->config['meizu_tsshop_appid'];//'100407811';
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
	//发送透传
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
		$messageData = array();
		$messageData['appId'] = $this->appId;
		$messageData['pushIds'] =join(',',$this->userlist);//推送设备，⼀批最多不能超过1000个 多个英⽂逗号分割必填
		$messageData['messageJson'] =json_encode(array('content'=>json_encode(array('title'=>$this->title,'content'=>$this->content,'sound'=>$this->sound,'task_extra'=>$this->extdata)),'pushTimeInfo'=>array('offLine'=>0,'validTime'=>1)));//json序列化 
		//序列化message 
		$messageData['sign'] = $this->getsign($messageData);//签名 
		$info = $this->postxm($this->messageUrl,$messageData);
		$info = trim($info); 
		$tempinfo = json_decode($info,true);
	
		if(isset($tempinfo['code'])){
			if($tempinfo['code'] == 200){
				
			}else{
				$this->error = $tempinfo['message'];
				return false;
			} 
		}else{
			$this->error =$info;
			return false;
		} 
	}
	private function getsign($message){
		ksort($message);//升序
		$chckstr = '';
		foreach($message as $key=>$value){
			$chckstr .=$key.'='.$value;
		}
		$chckstr = $chckstr.$this->appSecret;
		$newstr = strtolower(md5($chckstr));
		return $newstr; 
	}
	//发送通知栏
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
		$messageData = array();
		$messageData['appId'] = $this->appId;
		$messageData['pushIds'] =join(',',$this->userlist);//推送设备，⼀批最多不能超过1000个 多个英⽂逗号分割必填
		
		
		$messageJson  = array();//构造信息格式
		$messageJson['noticeBarInfo'] = array(
					'title'=>$this->title,//推送标题
					'content'=>$this->content,//推送内容
					);
		/*
		$messageJson['clickTypeInfo'] = array(
					'clickType'=>0,//(0,"打开应用"),(1,"打开应用页面"),(2,"打开URI页面");【int 非必填,默认为0】
					'url'=>'',//URI页面地址, 【clickType=2，必填】
					'parameters'=>json_encode(array()),//参数 【JSON格式】【非必填】
					'activity'=>'',//应用页面地址 应用页面地址【clickType=1，必填 格式 pkg.activityeg: com.meizu.upspushdemo.TestActivity】
					);//
		*/
		$messageJson['pushTimeInfo'] = array(
					'offLine'=>1,//是否进离线消息(0 否 1 是[validTime]) 【int 非必填，默认值为1】
					'validTime'=>1,//有效时长 (1到72 小时内的正整数) 【int offLine值为1时，必填，默认24】
		);
		$messageJson['advanceInfo'] = array(
					'suspend'=>1,//是否通知栏悬浮窗显示 (1 显示 0 不显示) 【int 非必填，默认1】
					'clearNoticeBar'=>1,//是否可清除通知栏 (1 可以 0 不可以) 【int 非必填，默认1】
					'notificationType'=>array(
						'vibrate'=>1,//震动 (0关闭 1 开启) , 【int 非必填，默认1】
						'lights'=>1,//闪光 (0关闭 1 开启), 【int 非必填，默认1】
						'sound'=>1,//声音 (0关闭 1 开启), 【int 非必填，默认1】
					),
		
		);  
		$messageData['messageJson'] =json_encode($messageJson);//json序列化  
		//序列化message 
		$messageData['sign'] = $this->getsign($messageData);//签名 
		$info = $this->postxm($this->notifyUrl,$messageData);
		$info = trim($info); 
		$tempinfo = json_decode($info,true);  
		if(isset($tempinfo['code'])){
			if($tempinfo['code'] == 200){
				
			}else{
				$this->error = $tempinfo['message'];
				return false;
			} 
		}else{
			$this->error =$info;
			return false;
		}  
	}
	
	//全网推送
	public function notifyall(){
		$this->notifyUrl = $this->notifyAllUrl;//重定义发送链接
		$this->SendNotify(); 
	}
	public function error(){
		return $this->error;
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