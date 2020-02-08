<?php  
/*** 
*    多通道推送
*    使用到的接口
*    
**/
use xmpush\Builder;
use xmpush\HttpBase;
use xmpush\Sender;
use xmpush\Constants;
use xmpush\Stats;
use xmpush\Tracer;
use xmpush\Feedback;
use xmpush\DevTools;
use xmpush\Subscription;
use xmpush\TargetedMessage; 
include_once(hopedir . '/plug/push/xiaomi/autoload.php');
class xiaomiPush  implements pushinterface
{  
	private $title;
	private $content;
	private $userlist;
	private $sound;
	private $extdata; 
	

	public function __construct(){   
		$this->title = '';
		$this->content = '';
		$this->userlist = array();
		$this->sound = '';
		$this->extdata = ''; 
		$this->appSecret = Mysite::$app->config['xiaomi_tsshop_secret'];//'pamTdc4U99w4TaMb64aBXg==';
		$this->package =  Mysite::$app->config['xiaomi_tsshop_packpage'];//'com.example.m6shopa'; 
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
		// 常量设置必须在new Sender()方法之前调用
		if(empty($this->package)){
			$this->error = '未来设置推送包名';
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
		
		
		
		Constants::setPackage($this->package);
		Constants::setSecret($this->appSecret); 
		$aliasList = $this->userlist; 
		$payload = json_encode(array('title'=>$this->title,'content'=>$this->content,'sound'=>$this->sound,'task_extra'=>$this->extdata)); 
		// print_r($payload);
		$sender = new Sender(); 
		$message1 = new Builder(); 
		$message1->passThrough(1);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
		$message1->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
		// $message1->extra(Builder::notifyForeground, 0); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
		$message1->notifyId(0); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
		$message1->build(); 
		$info = $sender->sendToIds($message1, $aliasList)->getRaw();
		// print_r($info);
		if($info['result'] == 'ok'){
			
			return true;
		}else{
			$this->error = $info['reason'];
			return false;
		} 
		//Array ( [result] => error [reason] => No valid targets! [trace_id] => Xcm52413536402466473IQ [code] => 20301 [description] => 发送消息失败 ) a
		//Array ( [result] => ok [trace_id] => Xcm50316536406639709z2 [code] => 0 [data] => Array ( [id] => scm50316536406639711qL ) [description] => 成功 [info] => Received push messages for 1 REGID ) a
	}
	public function SendNotify(){//发送通知  可以activity可以web
		if(empty($this->package)){
			$this->error = '未来设置推送包名';
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
		Constants::setPackage($this->package);
		Constants::setSecret($this->appSecret); 
		$aliasList = $this->userlist; 
		$payload =  json_encode(array('title'=>$this->title,'content'=>$this->content,'sound'=>$this->sound,'task_extra'=>$this->extdata));
		
		$sender = new Sender();

	 
		$message1 = new Builder();
		$message1->title($this->title);  // 通知栏的title
		$message1->description($this->content); // 通知栏的descption
		$message1->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
		$message1->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
		$message1->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
		$message1->notifyId(2); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
		$message1->build(); 
		$info = $sender->sendToIds($message1, $aliasList)->getRaw();
		if($info['result'] == 'ok'){
			
			return true;
		}else{
			$this->error = $info['reason'];
			return false;
		} 
		
	}
	public function error(){
		return $this->error;
	}  
}