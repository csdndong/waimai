<?php  
class wmrPush  implements pushinterface
{  
	private $title;
	private $content;
	private $userlist;
	private $sound;
	private $extdata; 
	

	public function __construct(){   
		$this->tskey = Mysite::$app->config['ghtskey'];
		$this->tsurl = 'http://ts.ghwmr.com';   
		$this->needread = 1;
		$this->userlist = array();
		$this->sound = '';
		$this->extdata = '';  
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
		$this->shopuid = $uid;
		return $this;
	}
	public function SendMsg(){ 
		// 常量设置必须在new Sender()方法之前调用
		$tsdata['type'] = 'publish';
		$tsdata['title'] = $this->title;
		$tsdata['content'] = $this->content;
		$tsdata['uid'] = $this->tskey;
		$tsdata['sendtime'] = '';
		$tsdata['type'] = '2';
		$tsdata['keys'] = $this->shopuid;
		$tsdata['extra'] = $this->extdata == null?'':$this->extdata;
		if(empty($this->shopuid)){
			$this->error =  '店铺id为空';
			return false;
		}
		if(!empty($this->needread)){
			$tsdata['needread'] = $this->needread;
		}
		if(strpos($this->title,'退款通知') !== false){
			$tsdata['music'] = 'tuikuan.aiff';
		}
		if(strpos($this->title,'自动接单') !== false){
			$tsdata['music'] = 'autoOrders.aiff';
		} 
		$tempinfo = $this->postxm($this->tsurl,$tsdata); 
		if($tempinfo == 'ok'){
			return true;
		}else{
			$this->error = $tempinfo;
			return false;
		}
	}
	public function SendNotify(){//发送通知  可以activity可以web
		 $this->error = "不支持发送通知";
		return false;
	}
	public function error(){
		return $this->error;
	}  
	public function postxm($url,$data='',$cookie=''){ // 模拟提交数据函数  
					// $header = array( 
						// 'Authorization: key='.$this->xmkey
					// );   
					 
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