<?php  
/*** 
*    多通道推送
*    使用到的接口
*    
**/
class oppoPush  implements pushinterface
{  
	private $title;
	private $content;
	private $userlist;
	private $sound;
	private $extdata;
	private $tokenUrl = "https://api.push.oppomobile.com/server/v1/auth";
	private $SaveMessageUrl   = "https://api.push.oppomobile.com/server/v1/message/notification/save_message_content"; 
	private $notifyUrl = 'https://api.push.oppomobile.com/server/v1/message/notification/broadcast';
	

	public function __construct(){   
		$this->title = '';
		$this->content = '';
		$this->userlist = array();
		$this->sound = '';
		$this->extdata = ''; 
		$this->appKey = Mysite::$app->config['oppo_tsshop_key'];//'1b4500a972fb403f9cd2c34dfb675958';
		$this->mastersecret =Mysite::$app->config['oppo_tsshop_secret'];// '1868477294ce472a92cdcf640a55f4fc';
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
		if(empty($this->appKey)){
			$this->error = '未来设置推送key';
			return false;
		}
		if(empty($this->mastersecret)){
			$this->error = '未来设置推送secret';
			return false;
		}
		if(empty($this->userlist) || count($this->userlist) == 0){
			$this->error = '未设置用户ID集';
			return false;
		}
		$this->userlist = array_unique($this->userlist);
		if($this->checktoken()){
			//创建通知栏  /server/v1/message/notification/save_message_content
			$messagedata = array();
			$messagedata['title'] = $this->title;
			$messagedata['sub_title'] = $this->title;
			$messagedata['content'] = $this->content;
			$messagedata['clickactiontyp'] = 0;
			$messagedata['action_parameters'] = array('extdata'=>$this->extdata);
			$messagedata['show_time_type'] = 0;
			$messagedata['off_line_ttl'] = 1200;
			$messagedata['auth_token'] =$this->token;
			
			$info = $this->postxm($this->SaveMessageUrl,$messagedata);
			
			$tempinfo = trim($info);
			$tempinfo = json_decode($tempinfo,true); 
			if(isset($tempinfo['code']) && ($tempinfo['code'] ==0||$tempinfo['code'] =='0')){//将值存放起来
				$this->messageID = $tempinfo['data']['message_id']; 
			}else{
				$this->error = isset($tempinfo['code'])?$tempinfo['message']:$info; 
				return false;
			} 
			 
			 
			$notifydata = array();
			$notifydata['message_id'] = $this->messageID;
			$notifydata['target_type'] = 2;
			$notifydata['target_value'] = join(';',$this->userlist);//多个以英文分号(;)分隔，最大 1000 个
			$notifydata['notification'] = json_encode($messagedata);
			$notifydata['auth_token'] =$this->token;
			$info = $this->postxm($this->notifyUrl,$notifydata);
			
			$tempinfo = trim($info);
			$tempinfo = json_decode($tempinfo,true); 
			if(isset($tempinfo['code']) && ($tempinfo['code'] ==0||$tempinfo['code'] =='0')){//将值存放起来
				return true;
			}else{
				$this->error = isset($tempinfo['code'])?$tempinfo['message']:$info; 
				return false;
			}  
		}  
	}
	public function SendNotify(){//发送通知  可以activity可以web
		 $this->SendMsg();
		
	}
	public function error(){
		return $this->error;
	} 
	//获取token
	private function checktoken(){
		$freshdata = array();
		// $timestamp = time();
		$millisecond = $this->get_millisecond();
		$millisecond = str_pad($millisecond,3,'0',STR_PAD_RIGHT);
		$timestamp = time().$millisecond;
		$freshdata['app_key'] =$this->appKey;
		$freshdata['sign'] =hash('sha256', $this->appKey.$timestamp.$this->mastersecret);
		$freshdata['timestamp'] = $timestamp;
		$info = $this->postxm($this->tokenUrl,$freshdata);
		$tempinfo = trim($info);
		$tempinfo = json_decode($tempinfo,true); 
		if(isset($tempinfo['code']) && ($tempinfo['code'] ==0||$tempinfo['code'] =='0')){//将值存放起来
			$this->token = $tempinfo['data']['authtoken'];
			$this->tokentime = time()+$tempinfo['data']['create_time'];//创建时间戳  可判断失效时间 
			return true;
		}else{
			$this->error = isset($tempinfo['code'])?$tempinfo['message']:$info; 
			return false;
		} 
	} 
	function get_millisecond()  
    {  
            list($usec, $sec) = explode(" ", microtime());  
            $msec=round($usec*1000);  
            return $msec;  
               
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