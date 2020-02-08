<?php
class taskMethod    
{  
	private $taskdb = null;
	private $Chek_USER_AGENT = false;
	private $autoCallPhone_accessKeyId;//Access Key Id
	private $autoCallPhone_accessKeySecret;//Access Key Secret
	private $autoCallPhone_tel;//被叫显号
	private $autoCallPhone_TelCode;//模板ID	
	public function init(){
		 if(strpos($_SERVER["HTTP_USER_AGENT"],'gh/1.0') > -1){
			$this->Chek_USER_AGENT = true;  
		}  
		$this->Chek_USER_AGENT = true;
		$this->autoCallPhone_accessKeyId = trim(Mysite::$app->config['autoCallPhone_accessKeyId']); 
		$this->autoCallPhone_accessKeySecret = trim(Mysite::$app->config['autoCallPhone_accessKeySecret']); 
		$this->autoCallPhone_tel = trim(Mysite::$app->config['autoCallPhone_tel']); 
		$this->autoCallPhone_TelCode = trim(Mysite::$app->config['autoCallPhone_TelCode']); 
		
	}
	//检测权限
	private function checkRight(){
		if($this->Chek_USER_AGENT){
			
		}else{
			echo 'error';
			exit;
		} 
	} 
	//初始化数据库
	private function initSql(){
		if($this->taskdb == null){
			$this->taskdb = new mysql_class();
		}
	}
	
	//失败函数
	public function defaultdo(){
		echo '默认函数';
		exit;
		
	}
	//打电话任务--通知商家确认制作
	public function callShopMake(){ 
		$this->checkRight();
		$this->initSql();
		$orderid = intval(IFilter::act(IReq::get('orderid'))); 
		 
		if($orderid > 0){
			$orderinfo =  $this->taskdb->select_one("select  shopphone,is_make,shoptype,status from ".Mysite::$app->config['tablepre']."order  where id = '".$orderid."' "); 
			if( !empty($orderinfo['shopphone']) && $orderinfo['is_make'] == 0 && $orderinfo['shoptype'] != 100  && $orderinfo['status'] < 4   ){ // 
				// $sendphone = '18638005332';
				echo $orderid.'call'.$orderinfo['shopphone'];
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
				print_r("callback".var_export($resp,true));
			}else{
				echo '店铺制作状态'.$orderinfo['is_make'].'phone'.$orderinfo['shopphone'];
			}			
		} 
		echo 'callShop';
		exit; 
	}
	//类抛出异常可能数据连接不上等重发任务默认2分钟 重发任务最多2次.
	public function resendTask(){
		$this->checkRight();
		$queryData = $_SERVER["QUERY_STRING"];
		parse_str($queryData,$queryArray);  
		if(isset($queryArray['SendNum'])){//重复执行2次
			if($queryArray['SendNum'] >=2){
				return;
			}
			$SendNum = intval($queryArray['SendNum']);
			$SendNum = $SendNum+1;
			unset($queryArray['SendNum']); 
		}else{
			$SendNum  =1;
		}		
		
		$newurl = Mysite::$app->config['siteurl'].'/taskCallBack.php?'.http_build_query($queryArray); 
		$mtime = time()+120;//2分钟后继续执行
		$TaskTimer = new TaskTimer();
		$TaskTimer->setUrl($newurl)->setTime($mtime)->SetNum($SendNum)->sendTask(); 
	}
	
	
	
 
	
	
}



?>