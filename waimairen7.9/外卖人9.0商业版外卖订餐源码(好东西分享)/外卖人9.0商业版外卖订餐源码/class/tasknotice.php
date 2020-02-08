<?php 
/*  ini_set('display_errors',1);            //错误信息
   ini_set('display_startup_errors',1);    //php启动错误信息
    error_reporting(-1); */
class TaskNotice
{ 
    private $taskdb = null;
    private $is_auto_callphone;//平台是否允许通知---根据平台设置 
	private $taskUrl;//平台通知域名 tasknotice 
	private  $starttime;
	private  $url; 
	private $service = 'http://39.106.110.231:4589';
	private $autoCallPhone_accessKeyId;//Access Key Id
	private $autoCallPhone_accessKeySecret;//Access Key Secret
	private $autoCallPhone_tel;//被叫显号
	private $autoCallPhone_TelCode;//模板ID	
	private $is_make_auto_callphone;//是否开启未制作订单几分钟内自动电话通知
	private $autoCallPhone_Minute;//间隔分钟数
	
	
	 
    public function __construct()
    {   
		$this->is_auto_callphone = intval(Mysite::$app->config['is_auto_callphone']);//=1允许
		$this->taskUrl = Mysite::$app->config['siteurl'];
		
		$this->starttime = '';
		$this->url = '';
		$this->SendNum = 0;//发送次数
		$this->autoCallPhone_accessKeyId = trim(Mysite::$app->config['autoCallPhone_accessKeyId']); 
		$this->autoCallPhone_accessKeySecret = trim(Mysite::$app->config['autoCallPhone_accessKeySecret']); 
		$this->autoCallPhone_tel = trim(Mysite::$app->config['autoCallPhone_tel']); 
		$this->autoCallPhone_TelCode = trim(Mysite::$app->config['autoCallPhone_TelCode']); 
		$this->is_make_auto_callphone = intval(Mysite::$app->config['is_make_auto_callphone']); 
		$this->autoCallPhone_Minute = intval(Mysite::$app->config['autoCallPhone_Minute']); 
 		
    }   
	public function SetNum($num){
		$this->SendNum = $num;
		return $this;
	}
	private function limitedRight(){
		$is_allow = false;
		if( $this->is_auto_callphone == 1 && !empty($this->autoCallPhone_accessKeyId) &&  !empty($this->autoCallPhone_accessKeySecret)   &&  !empty($this->autoCallPhone_tel)   &&  !empty($this->autoCallPhone_TelCode)      ){
			$is_allow = true;
		} 
 		return $is_allow;
	}
	/**
	*  函数使用说明  30秒后 根据订单ID 是否确认制作  未制作则触发拨打电话通知
	*  @orderid  订单ID
	*  @longtime 秒数       
	*/
	public function callShopMake($orderid){
		if($this->limitedRight()){
			$this->url = $this->taskUrl.'/taskCallBack.php?action=callShopMake&orderid='.$orderid;
 			$mtime = time()+$this->autoCallPhone_Minute*60;
			$this->starttime = $mtime;
 			$this->sendTask($orderid); 
		}
	} 
	
	public function sendTask($orderid){
		//保存任务函数 
		$newdata = array();
		$newdata['url'] = $this->url.'&SendNum='.$this->SendNum;
	 
		if( $this->is_make_auto_callphone == 1 ){
			$newdata['delaytime'] = $this->starttime-time();//多少秒后执行
			if($newdata['delaytime'] < 1){
				$newdata['delaytime'] = 1;//时间少于1时默认延迟1秒执行
			}
			$info = $this->postxm($this->service,$newdata);
		}else{
			logwrite("go---taskMethod");
 			$this->goCallShopMake($orderid);
		}
	  
		
	} 
	private function initSql(){
		if($this->taskdb == null){
			$this->taskdb = new mysql_class();
		}
	}
	
	public function goCallShopMake($orderid){ 
 		$this->initSql();
 		if($orderid > 0){
			$orderinfo =  $this->taskdb->select_one("select  shopphone,is_make,shoptype,status from ".Mysite::$app->config['tablepre']."order  where id = '".$orderid."' "); 
			if( !empty($orderinfo['shopphone']) && $orderinfo['is_make'] == 0 && $orderinfo['shoptype'] != 100    && $orderinfo['status'] < 4 ){  
  				$mir = hopedir.'class/SignatureHelper.php'; 
				require_once($mir); 
				$params = array (); 
				// fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
				$accessKeyId = $this->autoCallPhone_accessKeyId;
				$accessKeySecret = $this->autoCallPhone_accessKeySecret;
				// fixme 必填: 被叫显号
				$params["CalledShowNumber"] = $this->autoCallPhone_tel; 
				// fixme 必填: 被叫显号
			 	 $params["CalledNumber"] = $orderinfo['shopphone'];  
 				// fixme 必填: Tts模板Code
				$params["TtsCode"] = $this->autoCallPhone_TelCode; 
				// fixme 选填: Tts模板中的变量替换JSON,假如Tts模板中存在变量，则此处必填
			   # $params["TtsParam"] = array("code" => "123456");
				$params["TtsParam"] = array(); 
				// fixme 选填: 音量, 取值范围 0~200
				$params["Volume"] = 200; 
				if(!empty($params["TtsParam"]) && is_array($params["TtsParam"])) {
					$params["TtsParam"] = json_encode($params["TtsParam"], JSON_UNESCAPED_UNICODE);
				} 
				// 初始化SignatureHelper实例用于设置参数，签名以及发送请求
				$helper = new SignatureHelper(); 
				// 此处可能会抛出异常，注意catch
				$resp = $helper->request(
					$accessKeyId,
					$accessKeySecret,
					"dyvmsapi.aliyuncs.com",
					 array_merge($params, array(
								"RegionId" => "cn-hangzhou",
								"Action" => "SingleCallByTts",
								"Version" => "2017-05-25",
							))
				); 
				logwrite("callback".var_export($resp,true),1);
			}else{
				logwrite('店铺制作状态'.$orderinfo['is_make'].'phone'.$orderinfo['shopphone'],1);
			}			
		} 
		return true; 
	}
	private function postxm($url,$data='',$cookie=''){ // 模拟提交数据函数  
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
