<?php 
/** @class paytowechat 一键打款到微信零钱*/
class paytowechat{
	function __construct(){
        $this->sitename =  Mysite::$app->config['sitename']; 		
		$this->mysql =new mysql_class();
		$this->domain = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';		
    }
	function getconfig(){
		$logindir = plugdir.'/pay';
		if(!file_exists($logindir.'/weixin/set.php')){
			logwrite('set.php配置文件不存在');
			return false;
		}else{
			include_once($logindir.'/weixin/set.php');
      		$info = $plugsdata;
			return $info;
		}
	}
	//获取ip
	 function get_ip(){
		//strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$real_ip =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
		return $real_ip;
	}
	function curl_post_content($url,$datas,$cookie=''){
		#print_r($url);exit;
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT,8080);
		// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $datas); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		//以下两种方式需选择一种
		/******* 此处必须为文件服务器根目录绝对路径 不可使用变量代替*********/
		//第一种方法，cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		//curl_setopt($curl,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($curl,CURLOPT_SSLCERT,$_SERVER['DOCUMENT_ROOT']."/plug/pay/weixin/cert/apiclient_cert.pem");
		//默认格式为PEM，可以注释
		//curl_setopt($curl,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($curl,CURLOPT_SSLKEY,$_SERVER['DOCUMENT_ROOT']."/plug/pay/weixin/cert/apiclient_key.pem");
		
		//第二种方式，两个文件合成一个.pem文件
		//curl_setopt($curl,CURLOPT_SSLCERT,getcwd().$_SERVER['DOCUMENT_ROOT'].'/plug/pay/weixin/cert/ssss.pem');
		$tmpInfo = curl_exec($curl); // 执行操作
		#echo '1111';exit;
		#print_r($tmpInfo);exit;
		if (curl_errno($curl)) {
		echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo;
	}
	function ArrToXml($arr){
		#print_r($arr);
        if(!is_array($arr) || count($arr) == 0) return '';
        $xml = "<xml>";
        foreach ($arr as $key=>$val){           
           if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">"; 
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
			} 
        }
        $xml.="</xml>";
        return $xml;
	}
	function XmlToArr($xml){
		$array_data = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
	}
	//格式化参数
	function formatBizQueryParaMap($paraMap,$urlencode){
        $buff = "";
        ksort($paraMap);
        foreach($paraMap as $k => $v){
            if($urlencode){
               $v = urlencode($v);
            }
            $buff .=$k."=".$v."&";
        }
        if(strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
	function getSign($params,$key){
		foreach ($params as $k => $v){
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);//方法如下
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$key;
		#print_r($String);
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		return $result_;
	}
	//企业向个人付款
	 function payToUser($openid,$txid){
		#print_r(1111);exit;
		$txinfo = $this->mysql->select_one("select a.cost,a.shopid,b.shopname from ".Mysite::$app->config['tablepre']."shoptx as a left join ".Mysite::$app->config['tablepre']."shop as b on b.id=a.shopid where a.id= '".$txid."'");
		$config = $this->getconfig();
		if($config){
			//$txinfo['cost'] = 1;
			$params["mch_appid"]        = $config[0]['values'];   //公众账号appid
			$params["mchid"]            = $config[1]['values'];   //商户号 微信支付平台账号
			$params["nonce_str"]        = 'success'.mt_rand(100,999);   //随机字符串
			$params["partner_trade_no"] = mt_rand(100,999).date('YmdHis');           //商户订单号
			$params["amount"]           = round($txinfo['cost'],2)*100;          //金额
			$params["desc"]             = $this->sitename.'转账到'.$txinfo['shopname'];            //企业付款描述
			$params["openid"]           = $openid;          //用户openid
			$params["check_name"]       = 'NO_CHECK';       //不检验用户姓名
			$params['spbill_create_ip'] = $this->get_ip();   //获取IP
			#print_r($config);exit;
			$sign = $this->getSign($params,$config[3]['values']);
			$params["sign"] = $sign;//签名
			$xml = $this->ArrToXml($params);
			$return = $this->curl_post_content($this->domain,$xml);
			#print_r($return);
			$info = $this->XmlToArr($return);
			#print_r($info);exit;
			if($info['return_code']=='SUCCESS'){
				if($info['result_code']=='SUCCESS'){
					return true ;
				}else{
					$back = $this->curl_post_content($this->domain,$xml);
					$info = $this->XmlToArr($back);
					if($info['result_code']=='SUCCESS'&& $info['result_code']=='SUCCESS'){
						return true ;
					}else{
						logwrite($txid.'--'.$info['err_code'].$info['err_code_des']);
						return false;
					}
				}			
			}else{
				logwrite($txid.'--一键打款失败');
				return false; 
			}
		}
	}
}
?>