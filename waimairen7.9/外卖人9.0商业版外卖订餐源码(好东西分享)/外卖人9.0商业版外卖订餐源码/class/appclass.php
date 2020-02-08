<?php 
/*
app消息通知类
*/ 
class appclass
{
	private $gwUrl = 'http://app.waimairen.com/gt.php?';//访问地址
	private $user;//用户名
	private $secret;//密匙
	private $actime;//时间搓
	private $sign;//签名 
	private $otherlink;   
	function __construct(){  
		$this->tskey = Mysite::$app->config['ghtskey'];
		$this->tsurl = 'http://ts.ghwmr.com'; 
		
		$this->huaweiuserlist = array();
		$this->oppouserlist = array();
		$this->xiaomiuserlist = array();
		$this->otheruserlist = array();
		$this->meizuuserlist = array();
		
		$this->extra = null;
		$this->sendtime = null; 
		$this->opentype = null;
		$this->openurl = "";
		$this->needread = 1;
    } 
	function SetUid($shopuid){
		$this->shopuid = $shopuid;
		return $this;
	}
	//设置苹果播放语音表示--- 只要  $this->needread 不为空苹果会播放发送的content
	function setRead($readtype){ 
		$this->needread = $readtype;
		return $this;
	}
	public function SetExtra($data){//附加字段
		//附加数据格式: activity|字段1:值,字段2:值 ----可与app约定
		//当作为根据推送距离推送时：格式 locat|距离|lng坐标,lat坐标
		$this->extra = $data;
		return $this;
	} 
	//设置用户所有数据
	function SetUserlist($userlist = array()){
		$newarray = array(); 
		if(is_array($userlist) && count($userlist) > 0){
			foreach($userlist as $key=>$value){
				if(!empty($value['userid'])){
					if($value['devicetype'] == 'oppo'){ 
						$this->oppouserlist[] = $value['userid'];
					}elseif($value['devicetype'] == 'huawei'){
						$this->huaweiuserlist[] = $value['userid'];
					}elseif($value['devicetype'] == 'xiaomi'){
						$this->xiaomiuserlist[] = $value['userid'];
					}elseif($value['devicetype'] == 'meizu'){
						$this->meizuuserlist[] = $value['userid'];
					}else{
						$this->otheruserlist[] = $value['userid'];
					}
				}  
			}
		} 
		return $this;
	}
	//新增函数  
	public function openUrl($openurl){
		if(!empty($openurl)){
			$this->opentype = "URL";
			$this->openurl = $openurl;
		} 
		return $this;
	}
	 
    function sendNewmsg($title,$msgcontent='',$sendtime=''){ 
		// print_r($this->huaweiuserlist);
		if(!empty($this->huaweiuserlist) && count($this->huaweiuserlist) > 0){
			$huaweiPush = new huaweiPush();
			$huaweiPush->Title($title)
			->Content($msgcontent)
			->UserList($this->huaweiuserlist)
			->Sound('')
			->ExtData($this->extra)
			->SendMsg();
			// echo $huaweiPush->error();
		}
		if(!empty($this->oppouserlist) && count($this->oppouserlist) > 0){		 
			
  
			$oppoPush = new oppoPush();
			$oppoPush->Title($title)
			->Content($msgcontent)
			->UserList($this->oppouserlist)
			->Sound('')
			->ExtData($this->extra)
			->SendMsg();
			 
		}
		if(!empty($this->xiaomiuserlist) && count($this->xiaomiuserlist) > 0){
			$xiaomiPush = new xiaomiPush();
			$xiaomiPush->Title($title)
			->Content($msgcontent)
			->UserList($this->xiaomiuserlist)
			->Sound('')
			->ExtData($this->extra)
			->SendMsg();
		}
		if(!empty($this->meizuuserlist) && count($this->meizuuserlist) > 0){
			$meizuPush = new meizuPush();
			$meizuPush->Title($title)
			->Content($msgcontent)
			->UserList($this->meizuuserlist)
			->Sound('')
			->ExtData($this->extra)
			->SendMsg();
		}
		
		
		if(!empty($this->otheruserlist) && count($this->otheruserlist) > 0){
		
			$wmrpush = new wmrPush();
			$wmrpush->Title($title)
			->Content($msgcontent)
			->UserList(array())
			->ShopUid($this->shopuid)
			->Sound('')
			->ExtData($this->extra)
			->SendMsg();
		
		} 
		return "ok";
	}
	private function alinint($title,$msgcontent=''){ 
		 return true;
	}
   
	function checkcode($code){
		if($code == 'ok'){
			return 'ok';//发送信息成功 
		}else{
			return $code;//其他错误
		} 
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
?>