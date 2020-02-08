<?php 

/**
 * @class baseclass 
 * @描述   基础类
 */
class wmrclass
{
	public $mysql;  
	public $mysqlcache;  
	function __construct(){
		 
		$controller = Mysite::$app->getController();
		
		$action = Mysite::$app->getAction();   
		limitedIPCheck();
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Apache-HttpClient/UNAVAILABLE') > -1){
			updateLimitedIP();
			echo '';
			exit;
			
		}
		if($controller == 'app' && $action == 'sendregphone'  ){
			  	
			if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !empty($_SERVER['HTTP_REFERER']) || isset($_SERVER['HTTP_X_REQUESTED_WITH'])  ){
               
				 if(strpos($_SERVER["HTTP_USER_AGENT"],'gh') > -1){ 
				}else{
					logwrite('0n0n'.$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_CLIENT_IP'].$_SERVER['HTTP_X_FORWARDED_FOR']);				
					updateLimitedIP();
					echo '';
					exit;
				}
			}
			
		}else if($controller == 'member' &&   $action=='fastloginphone' ){
			if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ 
			}else{
				if( isset($_SERVER['HTTP_REFERER'])  && !empty($_SERVER['HTTP_REFERER']) ){
					 
					$checkurl = Mysite::$app->config['siteurl'];
					if(strpos($_SERVER['HTTP_REFERER'], $checkurl) > -1 ){ 
					
					}else{
						//logwrite('fasterror'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_CLIENT_IP'].$_SERVER['HTTP_X_FORWARDED_FOR'].'_href'.$_SERVER['HTTP_REFERER']);	
						updateLimitedIP();
						echo '';
						exit;
					}
				}else{
					//$_SERVER['HTTP_CLIENT_IP']; //代理端的（有可能存在，可伪造） $_SERVER['HTTP_X_FORWARDED_FOR']; //用户是在哪个IP使用的代理（有可能存在，也可以伪造）
					//logwrite('fasterror'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_CLIENT_IP'].$_SERVER['HTTP_X_FORWARDED_FOR']);		
					updateLimitedIP();
					 echo '';
					exit;
				} 
				$sendphonetime = ICookie::get('sendphonetime');
				if(empty($sendphonetime) && $sendphonetime < time()){
					logwrite('IP:'.$_SERVER['HTTP_CLIENT_IP'].$_SERVER['HTTP_X_FORWARDED_FOR']);	
					updateLimitedIP();
					echo '';
					exit;
					
				}
				 
			}
		}  
		$this->mysql =  new mysql_class(); 
		$this->mysqlcache = $this->mysql; 
	}
	//格式化价格数据，保留两位小数
	 public function formatnumber($cost){
	 	if($cost > 0){
			$cost = str_replace(',','',$cost);//先去掉','
			$cost = number_format($cost,2);//再格式化，保留两位小数
			$cost = str_replace(',','',$cost);//再去掉','
		}else{
			$cost = '0.00';
		}
		$cost = floatval($cost);
        return (string)$cost;		
	 } 
	//格式化价格数据，保留两位小数
	 public function formatcost($cost,$num){
	 	if($cost > 0){
			$cost = str_replace(',','',$cost);//先去掉','
			$cost = number_format($cost,$num);//再格式化，保留两位小数
			$cost = str_replace(',','',$cost);//再去掉','
		}else{
			$cost = '0.00';
		}
		$cost = floatval($cost);
        return (string)$cost;		
	 } 
	 
	 public function checkadminlogin(){
	 	 $link = IUrl::creatUrl('member/adminlogin'); 
	 	 if($this->admin['uid'] == 0) $this->message('未登录',$link); 
	 }
	 public function checkmemberlogin(){
                if(strpos($_SERVER["HTTP_USER_AGENT"],'MicroMessenger')){ //判断是微信浏览器不
                         $link = IUrl::creatUrl('wxsite/login');
                 }else{
                         $link = IUrl::creatUrl('member/login'); 

                 }
                if($this->member['uid'] == 0) $this->message('未登录',$link); 
	 }
	 public function checkshoplogin(){
	 	 $link = IUrl::creatUrl('member/shoplogin'); 
                 $agentadminuid = ICookie::get('agentadminuid');
	 	 if($this->member['uid'] == 0 && $this->admin['uid'] == 0 && $agentadminuid == 0)$this->message('未登录',$link);
	 	 $shopid = ICookie::get('adminshopid');
	 	 if(empty($shopid)) $this->message('未登录',$link); 
	 }
	 public static function sqllink($where,$sqlkey,$sqlvalue,$fuhao){
	 	  if(empty($sqlvalue)){
	 	     return  $where;
	 	  }else{
	 	  	 if(empty($where)){
	 	  	   return  '`'.$sqlkey.'`'.$fuhao.'\''.$sqlvalue.'\'';
	 	  	 }else{
	 	  	   return $where.' and `'.$sqlkey.'`'.$fuhao.'\''.$sqlvalue.'\'';
	 	  	 }
	 	  }
	 }
	 public static function message($msg,$link=''){
	 		$datatype = IFilter::act(IReq::get('datatype')); 
	 		if($datatype == 'json'){
	 			 //languagecls
	 			 $lngcls = new languagecls();
	 			 $msg = $lngcls->show($msg);
	 			 echo json_encode(array('error'=>true,'msg'=>$msg));  
	       exit; 
	 		}else{
                self::refunction($msg,$link);
	 	   }
   }
   public static function refunction($msg,$info=''){
   	  $newrul = empty($info)?Mysite::$app->config['siteurl']:$info;
	    header("Content-Type:text/html;charset=utf-8"); 
	    if(!empty($msg))
	    {
	    	 $lngcls = new languagecls();
	 		 $msg = $lngcls->show($msg);          
			 echo '<script>alert(\''.$msg.'\');location.href=\''.$newrul.'\';</script>';
		  }else{
		     echo '<script>location.href=\''.$newrul.'\';</script>';
	  	}
      exit;
   }
   public static function success($msg,$link=''){
   	   $datatype = IFilter::act(IReq::get('datatype')); 
	 		if($datatype == 'json'){
	 			 echo json_encode(array('error'=>false,'msg'=>$msg)); 
	             exit;
	 		}else{
	 			 self::refunction($msg,$link); 
	 		}
   }
	public static function shopIsopen($is_open,$starttime,$is_orderbefore,$nowhour){ 
		   
		  $nowhour = time();
          $timekey =0;
		  $gotime ='';
		  $opentype = 0;
		  $endtime = '';
		  $checkstart = '';
		  $checkend = '';
		  if($is_open == 0){
		  	   $opentype = 4;//店铺休息
		  }else{
		 	if(empty($starttime)){
		 		  $opentype = 1;//已打烊
		 	}else{
		 		$foo = explode('|',$starttime);
		 		foreach($foo as $key=>$value){
                    $opentime=array();
		 			if(!empty($value)){
		 				$mytime = explode('-',$value);
		 				if(count($mytime) > 1){
		 					$time1 = strtotime($mytime[0]);
		 					$time2 = strtotime($mytime[1]);
							$opentime[]=$time1;
		 					if($nowhour >= $time1 && $nowhour <= $time2){ 
		 						$opentype = 2;//营业中 
		 						$gotime = empty($gotime)?$mytime[0]:$gotime;
		 						$endtime = !empty($mytime[1])?strtotime($mytime[1]):$endtime;
		 					}
		 					if($nowhour <= $time2){
		 						$hfind = 1;
		 						$gotime = empty($gotime)?$mytime[0]:$gotime; 
		 						$checkstart = empty($checkstart)?strtotime($mytime[0]):$checkstart; 
		 					    $checkend = !empty($mytime[1])?strtotime($mytime[1]):$checkend;
		 					} 
		 				}
		 			}
		 		}
		 		if($opentype == 0){
		 		   if($is_orderbefore == 1 && $hfind ==1){
		 			   $opentype = 3;//3接受预定 
		 		   }
		 		}
		 	}
		 }
		 return array('opentype'=>$opentype,'newstartime'=>$gotime,'endtime'=>$endtime,'startoktime'=>$checkstart,'startendtime'=>$checkend);
	}
	 
	public function pscost($shopinfo,$newlng=null,$newlat=null){
		$backdata = array('pscost'=>0,'pstype'=>0,'canps'=>0,'juli'=>0,'is_allow_ziti'=>0);
		if($shopinfo['sendtype'] == 1){
			$pradiusvalue =  unserialize($shopinfo['pradiusvalue']);
		}else{
			 $this->platpsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$shopinfo['admin_id']."' ");  
			$pradiusvalue = unserialize($this->platpsinfo['radiusvalue']);
		}
		
 		if(!empty($newlng) && !empty($newlat)){
			$lat = $newlat;
			$lng = $newlng;
		}else{
			$lat = ICookie::get('lat');
			$lng = ICookie::get('lng');
		}
		if(!empty($lat)){
			$lat = empty($lat)?0:$lat;
			$lng = empty($lng)?0:$lng;
			$shoplat = isset($shopinfo['lat'])?$shopinfo['lat']:0;
			$shoplng = isset($shopinfo['lng'])?$shopinfo['lng']:0;
			 
			$juli =  $this->GetDistance($lat,$lng, $shoplat,$shoplng,1);
			$juliceshi = intval($juli/1000);
			
			if(is_array($pradiusvalue)){
				foreach($pradiusvalue as $key=>$value){
				if($juliceshi == $key){
						$backdata['pscost'] = number_format($value,2);
						$backdata['canps'] = 1;
						$backdata['juli'] = number_format(($juli/1000),2);
					}
				}
			}
		}
		$backdata['juli'] = $juliceshi;
		$backdata['is_allow_ziti'] = ($shopinfo['is_ziti'] == 1 && $this->platpsinfo['is_allow_ziti'] == 1 )?1:0;
		$backdata['pstype'] = $shopinfo['sendtype'];
		$checkpstype = Mysite::$app->config['psbopen']; 
		if($shopinfo['sendtype'] == 2){
			$backdata['pstype'] =$checkpstype == 1? 2:0;
		} 
		return $backdata; 
  }
  public function pscost2($shopinfo,$newlng=null,$newlat=null){
		$backdata = array('pscost'=>0,'pstype'=>0,'canps'=>0,'juli'=>0);
	  
		if($shopinfo['sendtype'] == 1){
			$pradiusvalue =  unserialize($shopinfo['pradiusvalue']);
		}else{
			 $this->platpsinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$shopinfo['admin_id']."' ");   
			$pradiusvalue = unserialize($this->platpsinfo['radiusvalue']);
		}
		 
 		if(!empty($newlng) && !empty($newlat)){
			$lat = $newlat;
			$lng = $newlng;
		}else{
			$lat = ICookie::get('lat');
			$lng = ICookie::get('lng');
		}
		if(!empty($lat)){
			$lat = empty($lat)?0:$lat;
			$lng = empty($lng)?0:$lng;
			$shoplat = isset($shopinfo['lat'])?$shopinfo['lat']:0;
			$shoplng = isset($shopinfo['lng'])?$shopinfo['lng']:0;
			$juli =  $this->GetDistance2($lat,$lng, $shoplat,$shoplng,1);
			 
			$juliceshi =intval($juli);
			if(is_array($pradiusvalue)){
				if(isset($pradiusvalue[$juliceshi])){
					$backdata['pscost'] = number_format($pradiusvalue[$juliceshi],2);
					$backdata['canps'] = 1;
					$backdata['juli'] = number_format($juli,2);
				}
				
				 
			}
		}
		$backdata['pstype'] = $shopinfo['sendtype'];
		$checkpstype = Mysite::$app->config['psbopen']; 
		if($shopinfo['sendtype'] == 2){
			$backdata['pstype'] =$checkpstype == 1? 2:0;
		} 
		return $backdata; 
  }
  //发送通知信息
   public function checkpsinfo(){
	 	 $link = IUrl::creatUrl('member/login'); 
	 	 if($this->member['uid'] == 0) $this->message('未登录',$link); 
	 	 $link = IUrl::creatUrl('member/base');
	 	 if($this->member['group'] != 2) $this->message('不是配送员',$link); 
	}
   function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2){
    	$earth = 6378.137;
    	$pi = 3.1415926;
       $radLat1 = $lat1 * PI ()/ 180.0;   //PI()圆周率
       $radLat2 = $lat2 * PI() / 180.0;
       $a = $radLat1 - $radLat2;
       $b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
       $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
       $s = $s * EARTH_RADIUS;
       $s = round($s * 1000);
       if($len_type > 1){
           $s /= 1000;
       }
       return round($s,$decimal);
   } 
   function GetDistance2($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2){
		/* 
		bicycling 骑行
		driving	驾车
		walking	步行
		*/
		$map_webservice_key =  Mysite::$app->config['map_webservice_key'];
		$origin = $lng1.",".$lat1;//始发地
		$destination = $lng2.",".$lat2;//目的地
		$content =   file_get_contents('https://restapi.amap.com/v4/direction/bicycling?key='.$map_webservice_key.'&origin='.$origin.'&destination='.$destination.''); 
		$backinfo  = json_decode($content,true);
		if( $backinfo['errcode'] == 0 && $backinfo['errmsg'] == 'OK' && count($backinfo['data']['paths']) > 0  ){
			$s = $backinfo['data']['paths'][0]['distance'];
		}else{  
			$earth = 6378.137;
			$pi = 3.1415926;
			$radLat1 = $lat1 * PI ()/ 180.0;   //PI()圆周率
			$radLat2 = $lat2 * PI() / 180.0;
			$a = $radLat1 - $radLat2;
			$b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
			$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
			$s = $s * EARTH_RADIUS;
			$s = round($s * 1000);
		}
		if ($len_type >= 1){
			$s /= 1000;
		}
		return round($s, $decimal);
	}
   function  getOpenPosttime($is_before,$starttime,$postdate,$minit,$befortime=0){ 
	      
		$backarray = array('is_opentime'=>0,'is_posttime'=>'','is_postdate'=>'','cost'=>0);
		$maxnowdaytime = strtotime(date('Y-m-d',time()));
		$daynottime = 24*60*60 -1; 
		$findpostime = false; 
		$posttime = time();
  		$miniday = $maxnowdaytime;
  		$maxday = $miniday+$daynottime;
  	    $findps = false;
		$timelist = !empty($postdate)?unserialize($postdate):array();
		 
		$data['pstimelist'] = array();
		$checknow = time();
		 $whilestatic = $befortime;
		$nowwhiltcheck = 0; 
		while($whilestatic >= $nowwhiltcheck){
		    $nowstartcheck = $nowwhiltcheck*86400;
			 
			foreach($timelist as $key=>$value){
				$docheck = $nowstartcheck+$value['s']; 
				 
				if($docheck== $minit){
					$findps = true;
					$tempt['s'] = date('H:i',$miniday+$value['s']);
					$tempt['e'] = date('H:i',$miniday+$value['e']);
					$backarray['is_posttime'] = $nowstartcheck+$miniday+$value['s'];
					$backarray['is_postdate'] =  $tempt['s'] .'-'.$tempt['e'];
					$checkdotime = $nowstartcheck+$miniday+$value['e'];
					$backarray['cost'] = isset($value['cost'])?$value['cost']:0;
					if($checkdotime < $posttime){
						$backarray['is_opentime'] = 3; 
					}
					break;
				} 
			}
			$nowwhiltcheck = $nowwhiltcheck+1;
		}
		
		
		
		
		if($findps ==  false){
			$backarray['is_opentime'] = 2; 
		}
		 return $backarray;
	} 
	public function getweatherinfo($lat,$lng){
		$weatherkey = Mysite::$app->config['weatherkey'];
		$weatherkey = empty($weatherkey)?'42ea7a8e0f39458897a30fff32279ad5':$weatherkey;
		if(empty($lat) || empty($lat)){			 		
			$lat = ICookie::get('lat');
			$lng = ICookie::get('lng');
		}
		$data['img'] = Mysite::$app->config['siteurl'].'/images/weather/999.png';		 
		$data['tmp'] = '未知';
		if(Mysite::$app->config['is_open_weather'] == 1){			
			$link = 'https://free-api.heweather.com/s6/weather/now?location='.$lng.','.$lat.'&key='.$weatherkey;			 
			$info = json_decode($this->curl_get_content($link), TRUE);		
			if(isset($info['HeWeather6'][0]['status']) && $info['HeWeather6'][0]['status'] == 'ok' ){
				if(isset($info['HeWeather6'][0]['now']['cond_code']) ){
					$data['img'] = Mysite::$app->config['siteurl'].'/images/weather/'.$info['HeWeather6'][0]['now']['cond_code'].'.png';
				}
				if(isset($info['HeWeather6'][0]['now']['tmp'])){
					$data['tmp'] = $info['HeWeather6'][0]['now']['tmp'].'℃';
				} 
				
			} 
		}
		return $data;
	}
	
    public function curl_get_content($url){
		#	$info = file_get_contents($url,true);
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查 
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_REFERER,'');//设置Referer
		curl_setopt($curl, CURLOPT_POST, 0); //发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); //显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);//获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if(curl_errno($curl)){
		   echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; 
	}
	function strFilter($str){
			$str = str_replace('`', '', $str);
			$str = str_replace('·', '', $str);
			$str = str_replace('~', '', $str);
			$str = str_replace('!', '', $str);
			$str = str_replace('！', '', $str);
			$str = str_replace('@', '', $str);
			$str = str_replace('#', '', $str);
			$str = str_replace('$', '', $str);
			$str = str_replace('￥', '', $str);
			$str = str_replace('%', '', $str);
			$str = str_replace('^', '', $str);
			$str = str_replace('……', '', $str);
			$str = str_replace('&', '', $str);
			$str = str_replace('*', '', $str);
			$str = str_replace('(', '', $str);
			$str = str_replace(')', '', $str);
			$str = str_replace('（', '', $str);
			$str = str_replace('）', '', $str);
			$str = str_replace('-', '', $str);
			$str = str_replace('_', '', $str);
			$str = str_replace('——', '', $str);
			$str = str_replace('+', '', $str);
			$str = str_replace('=', '', $str);
			$str = str_replace('|', '', $str);
			$str = str_replace('', '', $str);
			$str = str_replace('[', '', $str);
			$str = str_replace(']', '', $str);
			$str = str_replace('【', '', $str);
			$str = str_replace('】', '', $str);
			$str = str_replace('{', '', $str);
			$str = str_replace('}', '', $str);
			$str = str_replace(';', '', $str);
			$str = str_replace('；', '', $str);
			$str = str_replace(':', '', $str);
			$str = str_replace('：', '', $str);
			$str = str_replace('\'', '', $str);
			$str = str_replace('"', '', $str);
			$str = str_replace('“', '', $str);
			$str = str_replace('”', '', $str);
			$str = str_replace(',', '', $str);
			$str = str_replace('，', '', $str);
			$str = str_replace('<', '', $str);
			$str = str_replace('>', '', $str);
			$str = str_replace('《', '', $str);
			$str = str_replace('》', '', $str);
			$str = str_replace('.', '', $str);
			$str = str_replace('。', '', $str);
			$str = str_replace('/', '', $str);
			$str = str_replace('、', '', $str);
			$str = str_replace('?', '', $str);
			$str = str_replace('？', '', $str);
			return trim($str);
	}
	function Tdata($cityid,$limitarr,$paixuarr,$lat,$lng,$source,$limitjuli=1){// 0所有  1外卖  2超时
	
		//排序方式 ：综合排序，好评优先 起送价最低  ，距离 ,
		// cityid 城市ID 
		//筛选方式 : 【店铺分类ID】，【促销活动类型】，【配送方式】【店铺类型】,【分站ID】,【首页推荐】， 
		// array(	'shopcat'=>'店铺分类ID',
		//			'cxtype'=>'促销规则类型ID',
		//			'sendtype'=>'配送类 1商家配送 2平台配送 3支持自取店铺 ',
		//			'shoptype'=>'1外卖  2超时',
		//			'index_com'=>'是否仅显示首页推荐  1时有效  其他无效',
		// 			"is_goshop"=>'1时表示 仅显示到店买单店铺 其他无效',  
		//          "is_waimai" =>'1时仅支持外送的 其他无效',
		//			"limitcost"=>  1对应下边的   limitcost《 0 
		/*
		$limitarray = array(
				0=>'',
				1=>' and b.limitcost < 10 ',
				2=>' and b.limitcost >= 10 and b.limitcost < 20 ',
				3=>' and b.limitcost >= 20  ',
			);*/
		//);
		//整个都是营业在前 非营业在后显示
		//array( 'juli'=>'desc',距离远近
		//		 'ping'=>'desc',评分排序
		//       'limitcost'=>'asc',起送价 
		//       'sell'=>desc' //销量价格
		//);
		//$sourcetype  来源类型    1.PC端 2微信端,3web端,4app(安卓,苹果
		//配置文件的 open_wxcx   表示店铺列表是否显示 促销
		//$limitjuli  是否限制距离   1不限制 
		#print_r($limitarr);
		$limitjuli = 0;
		$pxvalue = 'mijuli';
		$pxtype = SORT_ASC;
		if(isset($paixuarr['juli'])){
			$pxtype = $paixuarr['juli'] == 'asc'?SORT_ASC:SORT_DESC;
		}elseif(isset($paixuarr['ping'])){
			$pxvalue = 'point';
			$pxtype = $paixuarr['ping'] == 'asc'?SORT_ASC:SORT_DESC;
		}elseif(isset($paixuarr['limitcost'])){
			$pxvalue = 'limitcost';
			$pxtype = $paixuarr['limitcost'] == 'asc'?SORT_ASC:SORT_DESC;
		}elseif(isset($paixuarr['sell'])){
			$pxvalue = 'sellcount';
			$pxtype = $paixuarr['sell'] == 'asc'?SORT_ASC:SORT_DESC;
		}	
		$platpsinfo =  $this->mysqlcache->longTime()->select_one("select locationradius,radiusvalue,is_allow_ziti from ".Mysite::$app->config['tablepre']."platpsset  where cityid='".$cityid."' ");
		
		  
		 
		$tempwherexxx =  Mysite::$app->config['plateshopid'] > 0? ' and a.id != '.Mysite::$app->config['plateshopid'] .' ':'';
		$tempwherexxx .=  " and a.admin_id = '".$cityid."'  ";
		
		if(isset($limitarr['shopcat'])&& $limitarr['shopcat'] > 0){ 
			$tempwherexxx =   $tempwherexxx." and b.shopid in(select sh.shopid from  ".Mysite::$app->config['tablepre']."shopsearch  as sh    where sh.second_id = ".$limitarr['shopcat']."  group by shopid  ) ";
			 
			
			
		}
		//cxtype  运算中在测算 ---这个构造的 shopid太长了在list里运算
		$limitcx = 0;//是否限制店铺促销类型
		$cxshopid = array();
		$d = date("w") ==0?7:date("w");     
		if(isset($limitarr['cxtype'])&& $limitarr['cxtype'] > 0){ 
			$limitcx = 1;
			$cxshop = $this->mysqlcache->getarr("select shopid from ".Mysite::$app->config['tablepre']."rule where controltype = ".$limitarr['cxtype']." and FIND_IN_SET(".$source.",supportplatform)    and status = 1  and ( limittype = 1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".time()." and starttime < ".time().")) ");  
			if(is_array($cxshop)){
				foreach($cxshop as $k=>$v){
					$cxshopid = array_merge($cxshopid,explode(',',$v['shopid'])); 
				}
				$cxshopid = array_unique($cxshopid);
			}
				 
		}
		
		
		
		
		if(isset($limitarr['sendtype']) && $limitarr['sendtype'] > 0){
			if( $limitarr['sendtype'] == 3 ){
				$tempwherexxx = "  and a.is_ziti = 1 ";
			}else{
				$tempwherexxx = $limitarr['sendtype']==1?$tempwherexxx." and b.sendtype = 1":$tempwherexxx." and b.sendtype != 1";
			}
			
		}
		$dotype = 0;
		if(isset($limitarr['shoptype']) && $limitarr['shoptype'] > 0){
			if($limitarr['shoptype'] == 1){
				$dotype = 1;
			}elseif($limitarr['shoptype'] == 2){
				$dotype = 2;
			} 
		}
		/*if(isset($limitarr['index_com']) && $limitarr['index_com'] ==1){ 
			$tempwherexxx .=  " and a.is_recom = 1  ";
		}
		if(isset($limitarr['is_goshop']) && $limitarr['is_goshop'] ==1){ 
		    $dotype = 1;
			$tempwherexxx .=  " and b.is_goshop  = 1  ";
		}
		if(isset($limitarr['is_waimai']) && $limitarr['is_waimai'] ==1){ 
		    $dotype = 1;
			$tempwherexxx .=  " and b.is_waimai   = 1  ";
		}*/
		if(isset($limitarr['search_input']) && !empty($limitarr['search_input'])){ 
//		    $dotype = 1;
			$tempwherexxx .=  " and a.shopname like '%".$limitarr['search_input']."%'  ";
		}
		if(isset($limitarr['limitcost']) &&  $limitarr['limitcost'] > 0){ 
			$limitdo = array(
				0=>'',
				1=>' and b.limitcost < 10 ',
				2=>' and b.limitcost >= 10 and b.limitcost < 20 ',
				3=>' and b.limitcost >= 20  ',
			);
			$tempwherexxx .= $limitdo[$limitarr['limitcost']];
		
		}
		
		#print_r($tempwherexxx);
		
		$list = array();
		
		$platbangjing = isset($platpsinfo['locationradius'])?intval($platpsinfo['locationradius']):0;
		$platbangjing = $platbangjing *1000;
		
	   
		$juliwhere = $limitjuli == 1?"":" and ((b.sendtype = 1 and SQRT(  power(6370693.5*( COS(a.`lat` * 0.01745329252)  )*  (a.`lng` * 0.01745329252 - ".$lng." * 0.01745329252) ,2)+power(6370693.5*(a.`lat` * 0.01745329252 - ".$lat." * 0.01745329252),2) ) < b.pradius*1000)or(b.sendtype !=1 and SQRT(  power(6370693.5*( COS(a.`lat` * 0.01745329252)  )*  (a.`lng` * 0.01745329252 - ".$lng." * 0.01745329252) ,2)+power(6370693.5*(a.`lat` * 0.01745329252 - ".$lat." * 0.01745329252),2) ) < ".$platbangjing.")) ";
		
		 
	 	$waimailist = array();
		if($dotype == 1|| $dotype == 0){//只显示外卖
			$waimailist = $this->mysql->getarr("select a.goodlistmodule,  a.sort, a.is_ziti,a.id,a.shopname,a.sellcount,a.ordercount,a.point,a.pointcount,a.virtualsellcounts,a.is_open,a.starttime,a.pointcount as pointsellcount,a.lat,a.lng,a.shoplogo,a.shoptype,a.address,a.isforyou,a.is_recom,
			 b.shopid,b.is_orderbefore,b.limitcost,b.is_hot,b.is_com,b.is_new,b.maketime,b.pradius,b.sendtype,b.pscost,b.pradiusvalue,b.arrivetime,b.postdate,SQRT(power(6370693.5*( COS(a.`lat` * 0.01745329252)  )*  (a.`lng` * 0.01745329252 - ".$lng." * 0.01745329252) ,2)+power(6370693.5*(a.`lat` * 0.01745329252 - ".$lat." * 0.01745329252),2) ) as juli
			 from ".Mysite::$app->config['tablepre']."shopfast as b left join ".Mysite::$app->config['tablepre']."shop as a  on b.shopid  = a.id     
			 where a.is_pass = 1  and a.shoptype=0  and a.is_open =1 ".$tempwherexxx." and a.endtime > ".time()." ".$juliwhere." order by id desc  limit 0,2000 "); 
			 
		}
		$marketlist = array(); 
		if($dotype == 2|| $dotype == 0){//只显示超市
			$marketlist = $this->mysql->getarr("select a.goodlistmodule, a.sort,  a.is_ziti,a.id,a.shopname,a.sellcount,a.ordercount,a.point,a.pointcount,a.virtualsellcounts,a.is_open,a.starttime,a.pointcount as pointsellcount,a.lat,a.lng,a.shoplogo,a.shoptype,a.address,a.isforyou,a.is_recom,
			 b.shopid,b.is_orderbefore,b.limitcost,b.is_hot,b.is_com,b.is_new,b.maketime,b.pradius,b.sendtype,b.pscost,b.pradiusvalue,b.arrivetime,b.postdate,SQRT(  power(6370693.5*( COS(a.`lat` * 0.01745329252)  )*  (a.`lng` * 0.01745329252 - ".$lng." * 0.01745329252) ,2)+power(6370693.5*(a.`lat` * 0.01745329252 - ".$lat." * 0.01745329252),2) ) as juli
			 from ".Mysite::$app->config['tablepre']."shopmarket as b left join ".Mysite::$app->config['tablepre']."shop as a  on b.shopid  = a.id  
			 where a.is_pass = 1 and a.shoptype=1 and a.is_open =1 ".$tempwherexxx."  and a.endtime > ".time()."   ".$juliwhere." order by id desc limit 0,2000 "); 
		}

		 
		$tempdds = array_merge($marketlist,$waimailist);  
		$open_wxcx = Mysite::$app->config['open_wxcx'];
		 
		$datalist = array();
		$nowhour = date('H:i:s',time());
		$nowhour = strtotime($nowhour);
		
		$pxvalue2 = array();
		$pxvalue1 = array();
		$shoppsimg = Mysite::$app->config['shoppsimg'];
		$psimg = Mysite::$app->config['psimg'];
		$platvaluelist = empty($platpsinfo['radiusvalue'])? '':unserialize($platpsinfo['radiusvalue']);	
		$nowhout = strtotime(date('Y-m-d',time()));
		foreach($tempdds as $key=>$value){ 
#print_r($lat.'----'.$lng.'----'.$value['lat'].'---'.$value['lng']);exit;	
			if($limitcx == 1){
				if(!in_array($value['shopid'],$cxshopid)){
					continue;
				}
			}
			if( $value['sendtype'] == 1 ){
				 $value['psimg'] = getImgQuanDir($shoppsimg);
				 $value['valuelist'] = empty($value['pradiusvalue'])? '':unserialize($value['pradiusvalue']);
			}else{
				 $value['psimg'] = getImgQuanDir($psimg);
				 $value['valuelist'] =$platvaluelist;
			}  
			
			$value['ztimg'] = ($value['is_ziti'] == 1 && $platpsinfo['is_allow_ziti'] == 1 )?getImgQuanDir(Mysite::$app->config['ztimg']):'';
			$value['mijuli'] = $value['juli'];
			$juli = $value['juli'];   
			$value['is_show_ztimg'] = ($value['is_ziti'] == 1 && $platpsinfo['is_allow_ziti'] == 1 )?'1':'0';
			$juliceshi = intval($juli/1000);
			 
			if($juli > 1000){
				$juli = $juli*0.001;
				$juli = round($juli,2);
				$value['juli'] =  $juli.'km';//'未测距';
			}else{
				$juli = round($juli,0);
				$value['juli'] =  $juli.'m';//'未测距
			} 
			$value['pscost'] = '0';
			$value['canps'] = 0; 
			$valuelist = $value['valuelist']; 
			
			if(is_array($valuelist)){
				foreach($valuelist as $k=>$v){
					if($juliceshi == $k){
						$value['pscost'] = empty($v)?0:$v;
						$value['canps'] = 1;
					}
				}
			}
			/*
			$source =  intval(IFilter::act(IReq::get('source')));
			$ios_waiting =   Mysite::$app->config['ios_waiting'];
			if($source == 1 && $ios_waiting == true){ 
				$value['canps'] = 1;
			}  
			if($value['canps'] == 0){//不在配送范围抛掉
				continue;
			}
			*/
			
			
			$value['opentype'] = '1';//1营业  0未营业 
			$checkinfo = $this->shopIsopen($value['is_open'],$value['starttime'],$value['is_orderbefore'],$nowhour); 
			$value['newstartime']  =  $checkinfo['newstartime'];
			if($checkinfo['opentype'] != 2 && $checkinfo['opentype'] != 3){
				$value['opentype'] = '0';
			}else{
				$value['opentype'] = $checkinfo['opentype'];
			}
			$pxvalue2[$key] = $value['opentype'] == 2||$value['opentype'] == 3?1:0;
                        

			
//			print_R($value['starttime']);
			$timelist = !empty($value['postdate'])?unserialize($value['postdate']):array();
//			print_R($value['newstartime']);
			$value['pstime']=$value['newstartime']; 
			
			foreach($timelist as $k=>$v){
				if(($nowhout+$v['s'])>= strtotime($value['newstartime'])){
					$value['pstime']  = date("H:i",$nowhout+$v['s']);
					$value['starttime']=date("H:i",$nowhout+$v['s']);
					break;
				}
			}
			
			
			$cxinfo = array();
            $d = date("w") ==0?7:date("w");
            $time = time();
			if($open_wxcx == 1){
				$cxinfo = $this->mysqlcache->getarr("select id,name,imgurl,controltype,parentid from ".Mysite::$app->config['tablepre']."rule where  FIND_IN_SET(".$value['id'].",shopid) and FIND_IN_SET(".$source.",supportplatform) and status = 1 and ( limittype =1 or ( limittype = 2 and  FIND_IN_SET(".$d.",limittime) ) or ( limittype = 3 and endtime > ".$time." and starttime < ".$time.")) order by id desc ");
            }
			//筛选掉不符合配送条件的免配送费活动
			$newrule = array();
			foreach($cxinfo as $k=>$v){
				$v['imgurl'] = getImgQuanDir($v['imgurl']);
				 if($v['controltype'] == 4){
					 if($v['parentid'] == 0 && $value['sendtype'] == 1){
						 $newrule[] = $v;
					 }
					 if($v['parentid'] == 1 && $value['sendtype'] != 1){
						 $newrule[] = $v;
					 }
				 }else{
					 $newrule[] = $v;
				 }
			 }
			$value['cxlist'] =  $newrule;  
			$value['cxcount'] =  count($newrule);
                        $value['checkcx'] = 0;
			$value['cxinfo'] =  $newrule;  
			
			$value['virtualsellcounts'] = intval($value['virtualsellcounts']);   
			$value['ordercount'] = intval($value['ordercount']);
			$imgurl = empty($value['shoplogo'])?getImgQuanDir(Mysite::$app->config['shoplogo']):getImgQuanDir($value['shoplogo']);			
			$value['shopimg'] = $imgurl;
			$value['shoplogo'] = $imgurl;          
			$value['sellcount'] = $value['virtualsellcounts']+$value['ordercount'];
			$value['ordercount'] =$value['sellcount'];
			$value['arrivetime'] = $value['arrivetime'] > 0?$value['arrivetime']:'0';
			$value['pradius'] = $value['pradius'] > 0?$value['pradius']:'0';
			
			$zongpoint = $value['point'];
			$zongpointcount = $value['pointcount'];
			if($zongpointcount != 0 ){
				$shopstart = intval(round($zongpoint/$zongpointcount,1));
			}else{
				$shopstart= 0;
			}			
			$value['point'] = 	$shopstart>5?5:$shopstart; 
			
			$pxvalue1[$key] = $value[$pxvalue];
			unset( $value['valuelist'] );
			unset( $value['pradiusvalue'] );
			unset( $value['postdate'] );
			$datalist[] = $value;
		} 
		array_multisort($pxvalue2,SORT_DESC,$pxvalue1, $pxtype, $datalist);
		return $datalist;
		
	}
	function __destruct(){	
		$cacheflag = Mysite::$app->config['datacache'];
		if($cacheflag == 1){
			memData::init()->disclose();
		}
	}

function logwrite($msg,$checkflag = 1){
	/*写日志*/
	//时间   操作内容
	if($checkflag == 1){
	 
		$nowdate = date('Y-m-d',time());
		if(!file_exists(hopedir.'log/'.$nowdate.'.php'))//创建文件
	  { 
		if(!is_dir(hopedir.'log')){
			 mkdir(hopedir.'log', 0777);
		}
		$fp = @fopen(hopedir.'log/'.$nowdate.'.php', 'w');
		@fclose($fp); 
	  }
	  $file=fopen(hopedir.'log/'.$nowdate.'.php',"a+");
	  $linsk = $_SERVER['REQUEST_URI'];
	  $content = "时间:".date('Y-m-d H:i:s',time()).",".$linsk."描述:".$msg."\r\n";
	  fwrite($file, $content); 
	  fclose($file);
  }
   
   
}
	
	
}
?>