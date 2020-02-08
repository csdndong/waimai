<?php  
/**
 * @class psbinterface
 * @brief 配送宝对接类
 */
class psbinterface
{
	protected $ordmysql; 
	private $psbvision = 'v3';
	private $psbapiid = 1;
	private $error;
	function __construct()
	{
	 	$this->ordmysql =new mysql_class();  
		$this->error = '';
	}
	//
	public function pingpsb($orderid,$sudu,$content){
		$orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		if(empty($orderinfo)){
			$this->error='订单不存在';
			return false;
		}
		if($orderinfo['shoptype'] != 100){
			$shopid = $orderinfo['shopid']; 
			$shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
			if(empty($shopinfo)){
				//店铺信息不存在
				$this->error='店铺不存在';
				logwrite($this->error);
				return false;
			}
			if(empty($shopinfo['psbaccid'])){
				//商家账号不存在
				$this->error='商家账号错误';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['is_goshop'] == 1){
				$this->error='到店订单不走第三方配送系统';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['pstype'] !=  2){
				$this->error='订单不是第三方配送';
				logwrite($this->error);
				return false; 
			}
			
			$psbaccid = trim($shopinfo['psbaccid']);
			$psbkey = trim($shopinfo['psbkey']);
			$psbcode = trim($shopinfo['psbcode']); 
			$ptpsblink = $shopinfo['psblink'];
		}else{
			//获取区域代理的  城市id   
			/**** ---------------***/
			//$orderinfo['admin_id'] == '';
			$psinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."'  ");
			if(empty($psinfo)){
				$this->error='该城市不存在';
				logwrite($this->error);
				return false; 
			}
			if($psinfo['pttopsb'] != 1){
				$this->error='该城市未启用第三方配送';
				logwrite($this->error);
				return false; 
			} 
			$ptpsblink = $psinfo['ptpsblink'];
			$psbaccid = $psinfo['ptpsbaccid'];
			$psbkey = $psinfo['ptpsbkey'];
			$psbcode = $psinfo['ptpsbcode'];
		}
		if(empty($ptpsblink)){
			$this->error='未设置对接链接';
			 // logwrite($this->error);
				return false; 
		}
		$url = $ptpsblink.":8080/orderapi/json/commentorder/".$this->psbvision."/".$this->psbapiid; 
		$data['orderid'] = $orderid;
		$data['bizid'] = $psbaccid;
		$data['key'] = $psbkey;
		$data['code'] = $psbcode; 
		$data['point_sudu'] = $sudu;
		$data['point_baozhuang'] = $sudu;
		$data['point_fuwu'] = $sudu;
		$data['content'] = $content;
		 logwrite($url);
	    $contents = $this->vpost($url,$data);
		  logwrite($contents);
	    $codeinfo = json_decode($contents,true);  
		 
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){ 
			 
			return true;
		}elseif(isset($codeinfo['success'])){   
		   $this->error = $codeinfo['msg']; 
			  logwrite($this->error);
		    return false;
		}else{ 
			$this->error = $contents; 
			  logwrite($this->error);
			return false; 
		}  
		
		
		/*
		
		$bizid = intval($_POST['bizid']);
		$orderid = intval($_POST['orderid']);
		/****检测是否可用****/ 
		//if($this->init($bizid)){ 
		/*
		    $point_sudu = intval($_POST['point_sudu']);
			$point_baozhuang = intval($_POST['point_baozhuang']);
			$point_fuwu = intval($_POST['point_fuwu']);
			$content = trim($_POST['content']); 
			
			
			*/
		//	parent::commentorder($this->apiid,$orderid,$bizid,$point_sudu,$point_baozhuang,$point_fuwu,$content);
			 
		 
	}
	//外卖人下弹成功---发送跑腿订单
	public function paotuitopsb($orderid){
		$orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		if(empty($orderinfo)){
			$this->error='订单不存在';
			return false;
		}
		if($orderinfo['shoptype'] != 100){
			$shopid = $orderinfo['shopid']; 
			$shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
			if(empty($shopinfo)){
				//店铺信息不存在
				$this->error='店铺不存在';
				logwrite($this->error);
				return false;
			}
			if(empty($shopinfo['psbaccid'])){
				//商家账号不存在
				$this->error='商家账号错误';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['is_goshop'] == 1){
				$this->error='到店订单不走第三方配送系统';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['pstype'] !=  2){
				$this->error='订单不是第三方配送';
				logwrite($this->error);
				return false; 
			}
			
			$psbaccid = trim($shopinfo['psbaccid']);
			$psbkey = trim($shopinfo['psbkey']);
			$psbcode = trim($shopinfo['psbcode']); 
			$ptpsblink = $shopinfo['psblink'];
		}else{
			//获取区域代理的  城市id   
			/**** ---------------***/
			//$orderinfo['admin_id'] == '';
			$psinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."'  ");
			if(empty($psinfo)){
				$this->error='该城市不存在';
				logwrite($this->error);
				return false; 
			}
			if($psinfo['pttopsb'] != 1){
				$this->error='该城市未启用第三方配送';
				logwrite($this->error);
				return false; 
			} 
			$ptpsblink = $psinfo['ptpsblink'];
			$psbaccid = $psinfo['ptpsbaccid'];
			$psbkey = $psinfo['ptpsbkey'];
			$psbcode = $psinfo['ptpsbcode'];
		}
		if(empty($ptpsblink)){
			$this->error='未设置对接链接';
			 logwrite($this->error);
				return false; 
		}
		$url = $ptpsblink.":8080/orderapi/json/noticeorder/".$this->psbvision."/".$this->psbapiid;
		//&bizid=".$shopinfo['psbaccid']."&key=".$shopinfo['psbkey']."&code=".$shopinfo['psbcode'].$pin_str;
		$data['orderid'] = $orderid;
		$data['bizid'] = $psbaccid;
		$data['key'] = $psbkey;
		$data['code'] = $psbcode; 
		// logwrite($url);
	    $contents = $this->vpost($url,$data);
		// logwrite($url);
	    $codeinfo = json_decode($contents,true);  
		logwrite($contents);
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){ 
			if(is_array($codeinfo['data']) && isset($codeinfo['data']['delivery_clerk_info'])){//表示存在配送员信息 表示自动派单 
				$clerkInfo = $codeinfo['data']['delivery_clerk_info']; 
				$cdata['psuid'] = $clerkInfo['clerkid'];
                $cdata['psusername'] = $clerkInfo['clerkname'];
                $cdata['psemail'] = $clerkInfo['clerkphone'];
                $cdata['psstatus'] = 1; 
                $cdata['picktime'] = time();
				if($orderinfo['psbflag'] == 2){  
					$cdata['psbflag'] = 1; 
				}
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员已接单";
                $statusdata['ststusdesc'] = '正赶往商家，配送员电话：'.$clerkInfo['clerkphone'];
                $statusdata['addtime'] = time();
                $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderstatus', $statusdata);
				
			}else{
				if($orderinfo['psbflag'] == 2){  
					$cdata['psbflag'] = 1;
					$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
				}
			}
			return true;
		}elseif(isset($codeinfo['success'])){   
		     $cdata['psbflag'] = 2;
			$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
			 $this->error = $codeinfo['errormsg'];
			$managephone = Mysite::$app->config['managephone'];
			if(IValidate::suremobi($managephone)){
				$phonecode = new phonecode($this->ordmysql,0,$managephone,1);
				$phonecode->sendother('发单提示'.$orderinfo['dno'].'发单失败,请在后台重发订单');
			}
		    return false;
		}else{ 
			$this->error = $contents;
			$cdata['psbflag'] = 2;
			$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
			 logwrite($this->error);
			$managephone = Mysite::$app->config['managephone'];
			if(IValidate::suremobi($managephone)){
				$phonecode = new phonecode($this->ordmysql,0,$managephone,1);
				$phonecode->sendother('提示'.$orderinfo['dno'].'发单失败,请在后台重发订单');
			}
			return false; 
		}  
		
		
		
		
		
	}
    //外卖人下单成功 --- 通知配送宝商家端---需要确认制作
    public function psbnoticeorder($orderid){  
		$orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		if(empty($orderinfo)){
			$this->error='订单不存在';
			return false;
		}
		if($orderinfo['shoptype'] !=100){
			$shopid = $orderinfo['shopid']; 
			$shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
			if(empty($shopinfo)){
				//店铺信息不存在
				$this->error='店铺不存在';
				logwrite($this->error);
				return false;
			}
			if(empty($shopinfo['psbaccid'])){
				//商家账号不存在
				$this->error='商家账号错误';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['is_goshop'] == 1){
				$this->error='到店订单不走第三方配送系统';
				logwrite($this->error);
				return false; 
			}
			if($orderinfo['pstype'] !=  2){
				$this->error='订单不是第三方配送';
				logwrite($this->error);
				return false; 
			}
			$psbaccid = trim($shopinfo['psbaccid']);
			$psbkey = trim($shopinfo['psbkey']);
			$psbcode = trim($shopinfo['psbcode']); 
			$ptpsblink = $shopinfo['psblink'];
		}else{
			$psinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."'  ");
			if(empty($psinfo)){
				$this->error='该城市不存在';
				logwrite($this->error);
				return false; 
			}
			if($psinfo['pttopsb'] != 1){
				$this->error='该城市未启用第三方配送';
				logwrite($this->error);
				return false; 
			} 
			$ptpsblink = $psinfo['ptpsblink'];
			$psbaccid = $psinfo['ptpsbaccid'];
			$psbkey = $psinfo['ptpsbkey'];
			$psbcode = $psinfo['ptpsbcode'];
		}
		
        $url = $ptpsblink.":8080/orderapi/json/noticeorder/".$this->psbvision."/".$this->psbapiid;
		//&bizid=".$shopinfo['psbaccid']."&key=".$shopinfo['psbkey']."&code=".$shopinfo['psbcode'].$pin_str;
		$data['orderid'] = $orderid;
		$data['bizid'] = $psbaccid;
		$data['key'] = $psbkey;
		$data['code'] = $psbcode; 
	    $contents = $this->vpost($url,$data);
	    $codeinfo = json_decode($contents,true);   
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){ 
			if(is_array($codeinfo['data']) && isset($codeinfo['data']['delivery_clerk_info'])){//表示存在配送员信息 表示自动派单 
				$clerkInfo = $codeinfo['data']['delivery_clerk_info']; 
				$cdata['psuid'] = $clerkInfo['clerkid'];
                $cdata['psusername'] = $clerkInfo['clerkname'];
                $cdata['psemail'] = $clerkInfo['clerkphone'];
                $cdata['psstatus'] = 1; 
                $cdata['picktime'] = time();
				if($orderinfo['psbflag'] == 2){  
					$cdata['psbflag'] = 1; 
				}
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员已接单";
                $statusdata['ststusdesc'] = '正赶往商家，配送员电话：'.$clerkInfo['clerkphone'];
                $statusdata['addtime'] = time();
                $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderstatus', $statusdata);
				
			}else{
				if($orderinfo['psbflag'] == 2){  
					$cdata['psbflag'] = 1;
					$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
				}
			}
			
			return true;
		}elseif(isset($codeinfo['success'])){   
		    $cdata['psbflag'] = 2;
			$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
			$this->error = $codeinfo['errormsg'];
			$managephone = Mysite::$app->config['managephone'];
			if(IValidate::suremobi($managephone)){
				$phonecode = new phonecode($this->ordmysql,0,$managephone,1);
				$phonecode->sendother('提示'.$orderinfo['dno'].'发单失败,请在后台重发订单');
			}
		    return false;
		}else{ 
			$this->error = $contents;
			$cdata['psbflag'] = 2;
			$this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$cdata,"id='".$orderid."'");
			$this->error = $codeinfo['errormsg'];
			$managephone = Mysite::$app->config['managephone'];
			if(IValidate::suremobi($managephone)){
				$phonecode = new phonecode($this->ordmysql,0,$managephone,1);
				$phonecode->sendother('提示'.$orderinfo['dno'].'发单失败,请在后台重发订单');
			}
			return false; 
		}  
    }
	public function testlink($psblink,$bizid,$key,$code){
		$url = $psblink.":8080/orderapi/json/testlink/".$this->psbvision."/".$this->psbapiid;
		$data['bizid'] =$bizid;
		$data['key'] = $key;
		$data['code'] = $code;  
	    $contents = $this->vpost($url,$data); 
	    $codeinfo = json_decode($contents,true);    
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){
			//成功 ---设置标志为
			return true;
		}elseif(isset($codeinfo['success'])){ 
			//失败--设置标志为
			//  [success] => 101
		    //	[errorid] => 80001
			// [errormsg] => 商家未开启此接口
			
			$this->error = $codeinfo['errormsg'];
		    return false;
		}else{
			$this->error = $contents;
			return false;
		}
	}
	public function err(){
		return $this->error;
	}
	//外卖人关闭订单----
	public function closeorder(){
		
	}
	
	//创建配送宝对接账号
    public function createacount($shopid,$stationid,$psgroupid,$bizdistrictid){
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink'];
		if(empty($psbkey)){
			$this->error = '平台未设置配送宝对接key'; 
            return false;
		}
		if(empty($psbhttp)){
			$this->error = '平台未设置配送宝域名';
            return false;
		}  
		$shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
		if(empty($shopinfo)){
			$this->error = '店铺不存在';
            return false;
		}
		if(empty($shopinfo['uid'])){
			$this->error = '店铺对应账号不存在';
            return false;
		}
		if($shopinfo['psbaccid'] > 0){
			$this->error = '此账号已对接不能重复对接';
            return false;
		} 
		$userinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$shopinfo['uid']."'  ");
		if(empty($userinfo)){
			$this->error = '店铺对应账号不存在';
            return false;
		} 
		$links = $psbhttp.':8080/plattoapi/json/creatacount/'.$this->psbvision.'/'.$this->psbapiid;

		$newdata['checkkey'] =  $psbkey;  
		$newdata['stationid'] = $stationid;
		$newdata['username'] = $userinfo['username'];
		$newdata['shopname'] = $shopinfo['shopname'];
		$newdata['shopphone'] = $shopinfo['phone'];
		$newdata['address'] = $shopinfo['address'];
		$newdata['managephone'] = $shopinfo['maphone'];
		$newdata['lat'] = $shopinfo['lat'];
		$newdata['lng'] = $shopinfo['lng'];
		$newdata['domain'] =Mysite::$app->config['siteurl'];
		$newdata['psgroupid'] = $psgroupid;
		$newdata['bizdistrictid'] = $bizdistrictid; 
		$contents = $this->vpost($links,$newdata);  
		$codeinfo = json_decode($contents,true);  	
		 
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){
			 //成功 ---设置标志为 
			$setinfo =$codeinfo['data']['setinfo'];  
            $data['psbaccid'] =$setinfo['bizid']; 
            $data['psbkey'] =$setinfo['key']; 
            $data['psbcode'] =$setinfo['keycode']; 
            $data['psblink'] =$psbhttp;   
			$this->ordmysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopinfo['id']."'");   
			if(empty($shopinfo['shoptype'])){ 
				$tdata['sendtype'] = 2;
				   $this->ordmysql->update(Mysite::$app->config['tablepre'].'shopfast',$tdata,"shopid='".$shopinfo['id']."'");
			 }else{
				 $tdata['sendtype'] = 2;
				  $this->ordmysql->update(Mysite::$app->config['tablepre'].'shopmarket',$tdata,"shopid='".$shopinfo['id']."'");
			 }
			return true;
		}else{ 
			//失败--设置标志为 
			if(isset($codeinfo['success']) && $codeinfo['success'] != 200){
				$this->error =  $codeinfo['errormsg']; 
			}else{
				$this->error =  $contents; 
			} 
			 
            return false;
		} 
		
	}
	function acountinfo($shopinfo){
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink']; 
		if(empty($psbkey)){
			$this->error ='平台未设置配送宝对接key';
            return false;
		}
		if(empty($psbhttp)){
			$this->error ='平台未设置配送宝域名';
            return false;
		} 
		if(empty($shopinfo['psbaccid'])){
			$this->error ='未对接';
            return false;
		}
		$links = $psbhttp.':8080/plattoapi/json/getacount/'.$this->psbvision.'/'.$this->psbapiid;  
		$newdata['checkkey'] =  $psbkey;  
		$newdata['bizid'] = $shopinfo['psbaccid'];
		$newdata['psbkey'] = $shopinfo['psbkey']; 
		$contents = $this->vpost($links,$newdata);  
		$codeinfo = json_decode($contents,true);  
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){
			 //成功 ---设置标志为 
			 $this->psbacount =$codeinfo['data']['psbinfo'];  
			 return true;
		}else{ 
			//失败--设置标志为 
			if(isset($codeinfo['success']) && $codeinfo['success'] != 200){
				$this->error = $codeinfo['errormsg']; 
			}else{
				$this->error =  $contents; 
			} 
            return false;
		}  
	}
	function getacount(){
		return $this->psbacount;
	} 
	
	//获取所有配送站
	public function stationlist(){
		
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink']; 
		if(empty($psbkey)){
			$this->error = '平台未设置配送宝对接key';
            return false;
		}
		if(empty($psbhttp)){
			$this->error ='平台未设置配送宝域名';
            return false;
		} 
		$links = $psbhttp.':8080/plattoapi/json/getstationlist/'.$this->psbvision.'/'.$this->psbapiid;  
		$newdata['checkkey'] =  $psbkey;  
		$contents = $this->vpost($links,$newdata);  
		$codeinfo = json_decode($contents,true);  
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){ 
			 $this->stationlist = $codeinfo['data']['lists']; 
			 return true;
		}else{ 
			//失败--设置标志为 
			if(isset($codeinfo['success']) && $codeinfo['success'] != 200){
				$this->error = $codeinfo['errormsg']; 
			}else{
				$this->error = $contents; 
			}
			 
            return false;
		}  
	}
	 
	function getstationlist(){
		return $this->stationlist;
	}
	public function getstationchild($stationid){
		
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink']; 
		if(empty($psbkey)){
			$this->error ='平台未设置配送宝对接key';
            return false;
		}
		if(empty($psbhttp)){
			$this->error ='平台未设置配送宝域名';
            return false;
		} 
		$links = $psbhttp.':8080/plattoapi/json/getstationchild/'.$this->psbvision.'/'.$this->psbapiid;

		$newdata['checkkey'] =  $psbkey;  
		$newdata['stationid'] = $stationid;
		$contents = $this->vpost($links,$newdata);  
		$codeinfo = json_decode($contents,true);  
		if(isset($codeinfo['success']) && $codeinfo['success'] == 200){
			 //成功 ---设置标志为 
			 $this->psgrouplist = $codeinfo['data']['psgrouplist'];
			 $this->bizdistrictlist = $codeinfo['data']['bizdistrictlist'];
			 return true;
		}else{ 
			//失败--设置标志为 
			if(isset($codeinfo['success']) && $codeinfo['success'] != 200){
				$this->error = $codeinfo['errormsg']; 
			}else{
				$this->error = $contents; 
			} 
            return false;
		}  
	}
	function getpsgroup(){
		return $this->psgrouplist;
	}
	function getbizdistrict(){
		return $this->bizdistrictlist;
	}
	
	
	/* 2017-04-20 
	 * 新增 根据 订单 配送员UID：psuid 获取配送宝配送员的位置坐标
	 * zem 
	 */ 
 	public function getpsbclerkinfo($psyuid){
	 
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink']; 
		if(empty($psbkey)){
			$this->error = '平台未设置配送宝对接key';
            logwrite($this->error);
			return array();
		}
		if(empty($psbhttp)){
			$this->error ='平台未设置配送宝域名';
			logwrite($this->error);
            return array();
		} 
		$links = $psbhttp.':8080/plattoapi/json/getpsbclerkinfo/'.$this->psbvision.'/'.$this->psbapiid; 
	  
		
 		$newdata['checkkey'] =  $psbkey;  
		$newdata['psyuid'] =  $psyuid;  
		$contents = $this->vpost($links,$newdata);  
 		  
		$info = json_decode($contents,true);  		 
		# print_r($info);exit; 
		if(isset($info['success']) && $info['success'] == 200){ 
			 $clerkinfo = $info['data']['lists']; 
			 $clerkinfo = empty($clerkinfo)?array():$clerkinfo;
			 return $clerkinfo;
		}else{ 
			//失败--设置标志为 
			  
			if(isset($info['success']) && $info['success'] != 200){
				$this->error = $info['errormsg']; 
			}else{
				$this->error = $contents; 
			}
			 
            logwrite($this->error);
            return array();
		}  
	}
	
	
	

    //直接生成配送平台的配送单
    public function psbmakeorder($orderid){
        $orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
        $shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."'  ");
        $orderdet_arr = array();
        $ordetinfo = $this->ordmysql->getarr("select * from ".Mysite::$app->config['tablepre']."orderdet where order_id='".$orderid."'");
        foreach($ordetinfo as $k=>$v){
            $orderdet_arr[] = $v['goodsname'].',,,'.$v['goodscost'].',,,'.$v['goodscount'].',,,'.$v['goodsattr'];
        }
        $order_newdet = implode("|||", $orderdet_arr);


/*方案1*/
//                          $dodata = array();
//                          $dodata['bizlat'] = $data['shoplat'];//商家lat坐标
//                          $dodata['bizlng'] = $data['shoplng'];//商家lng坐标
//                          $dodata['bizaddress'] = $data['shopaddress'];//商家地址
//                          $dodata['bizname'] = $data['shopname'];//商家名称
//                          $dodata['bizphone'] = $data['shopphone'];//商家电话
//                          $dodata['bizdaycode'] = $data['daycode'];//商家每天的订单序列号
//                          $dodata['bizdno'] = $data['dno'];//商家订单编号
//                          $dodata['goodscost'] = $data['allcost'];//订单总金额
//                          if($data['paystatus'] == 1){
//                              $paystatus = 1;
//                          }else{
//                              $paystatus = 2;
//                          }
//                          $dodata['goodspaytype'] = $paystatus;//商品支付方式(1.已付,2.未付)
//                          $dodata['receiver_name'] = $data['buyername'];//收货人名
//                          $dodata['receiver_phone'] = $data['buyerphone'];//收货人电话
//                          $dodata['receiver_address'] = $data['buyeraddress'];//收货人地址
//                          $dodata['receiver_lat'] = $data['buyerlat'];//收货人lat坐标
//                          $dodata['receiver_lng'] = $data['buyerlng'];//收货人lng坐标
//                          $dodata['time_waitpost'] = $data['posttime'];//期待送达时间(当期待送达) 时间戳
//                          $dodata['orderdet'] = $order_newdet;//订单详情：使用双|||分割子单 使用,,,分割子单数据
//                          $dodata['outorderid'] = $orderid;//外部订单id
//                          $dodata['other'] = $data['othertext'];//其他备注
//
//                          $url = $shopinfo['psblink']."/index.php?c=orderapi&act=get&uid=".$shopinfo['psbapiid']."&do=makeorder&datatype=json&key=".$shopinfo['psbkey']."&code=".$shopinfo['psbcode'];
//                          $backinfo = $this->vpost($url,$dodata);
//                          $infos = json_decode($backinfo,true);
//                          if($infos['success'] == 200){
//                              return true;
//                          }else{
//                              return false;
//                          }



        /*方案2*/
        $bizlat = '&bizlat='.$orderinfo['shoplat'];//商家lat坐标
        $bizlng = '&bizlng='.$orderinfo['shoplng'];//商家lng坐标
        $bizaddress = '&bizaddress='.$orderinfo['shopaddress'];//商家地址
        $bizname = '&bizname='.$orderinfo['shopname'];//商家名称
        $bizphone = '&bizphone='.$orderinfo['shopphone'];//商家电话
        $bizdaycode = '&bizdaycode='.$orderinfo['daycode'];//商家每天的订单序列号
        $bizdno = '&bizdno='.$orderinfo['dno'];//商家订单编号
        $goodscost = '&goodscost='.$orderinfo['allcost'];//订单总金额
        if($orderinfo['paystatus'] == 1){
            $paystatus = 1;
        }else{
            $paystatus = 1;
        }
        $goodspaytype = '&goodspaytype='.$paystatus;//商品支付方式(1.已付,2.未付)
        $receiver_name = '&receiver_name='.$orderinfo['buyername'];//收货人名
        $receiver_phone = '&receiver_phone='.$orderinfo['buyerphone'];//收货人电话
        $receiver_address = '&receiver_address='.$orderinfo['buyeraddress'];//收货人地址
        $receiver_lat = '&receiver_lat='.$orderinfo['buyerlat'];//收货人lat坐标
        $receiver_lng = '&receiver_lng='.$orderinfo['buyerlng'];//收货人lng坐标
        $postdate = explode('-',$orderinfo['postdate']);
        $time_waitpost = '&time_waitpost='.strtotime(date('Y-m-d ',$orderinfo['posttime']).$postdate[1]);//期待送达时间(当期待送达) 时间戳
        $orderdet = '&orderdet='.$order_newdet;//订单详情：使用双|||分割子单 使用,,,分割子单数据
        $outorderid = '&outorderid='.$orderid;//外部订单id
        $other = '&other='.$orderinfo['othertext'];//其他备注


        $pin_str = $bizlat.$bizlng.$bizaddress.$bizname.$bizphone.$bizdaycode.$bizdno.$goodscost.$goodspaytype.$receiver_name.$receiver_phone.$receiver_address.$receiver_lat.$receiver_lng.$time_waitpost.$orderdet.$outorderid.$other;
//        $url = $shopinfo['psblink']."/index.php?c=orderapi&act=get&uid=".$shopinfo['psbapiid']."&do=makeorder&datatype=xml&key=".$shopinfo['psbkey']."&code=".$shopinfo['psbcode'].$pin_str;
        $url = $shopinfo['psblink']."/orderapi/xml/makeorder/".$shopinfo['psbversion']."/1&bizid=".$shopinfo['psbaccid']."&key=".$shopinfo['psbkey']."&code=".$shopinfo['psbcode'].$pin_str;
        $callback_info = file_get_contents($url);
        $p = xml_parser_create();
        xml_parse_into_struct($p, $callback_info, $backinfo, $index);
        xml_parser_free($p);
        if($backinfo[1]['value'] != 200){
            return false;
        }else{
            return true;
        }
    }

 //配送宝接口关闭配送订单
    public function psbunorder($orderid){
		
		$psbkey = Mysite::$app->config['autopsbkey'];
		$psbhttp = Mysite::$app->config['autopsblink']; 
		if(empty($psbkey)){
			$this->error ='平台未设置配送宝对接key';
            return false;
		}
		if(empty($psbhttp)){
			$this->error ='平台未设置配送宝域名';
            return false;
		} 
		
        $orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
        $shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$orderinfo['shopid']."'  ");
 
$url = $psbhttp.':8080/orderapi/json/unorder/'.$this->psbvision.'/'.$this->psbapiid;  
 
 	 
 		$newdata['key'] =  $shopinfo['psbkey'];  
 		$newdata['code'] =  $shopinfo['psbcode'];  
 		$newdata['checkkey'] =  $psbkey;  
 		$newdata['outorderid'] =  $orderid;  
		$contents = $this->vpost($url,$newdata);  
 		$info = json_decode($contents,true);  
		
	 
		if(isset($info['success']) && $info['success'] == 200){ 
		 
			 logwrite("取消成功");
		}else{ 
			//失败--设置标志为 
			if(isset($info['success']) && $info['success'] != 200){
				$this->error = $info['errormsg']; 
			}else{
				$this->error = $contents; 
			}
			 
            return false;
		}  

 
    }
    //外卖人取消退款 --- 通知配送宝
    public function psbqxdraworder($orderid){
		
		 return true;
		
	}

    //外卖人同意退款 --- 通知配送宝
    public function psbdraworder($orderid){
        $orderinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
        if(empty($orderinfo)){
            $this->error='订单不存在';
            return false;
        }
        if($orderinfo['shoptype'] !=100){
            $shopid = $orderinfo['shopid'];
            $shopinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop where id='".$shopid."'  ");
            if(empty($shopinfo)){
                //店铺信息不存在
                $this->error='店铺不存在';
                logwrite($this->error);
                return false;
            }
            if(empty($shopinfo['psbaccid'])){
                //商家账号不存在
                $this->error='商家账号错误';
                logwrite($this->error);
                return false;
            }
            if($orderinfo['is_goshop'] == 1){
                $this->error='到店订单不走第三方配送系统';
                logwrite($this->error);
                return false;
            }
            if($orderinfo['pstype'] !=  2){
                $this->error='订单不是第三方配送';
                logwrite($this->error);
                return false;
            }
            $psbaccid = trim($shopinfo['psbaccid']);
            $psbkey = trim($shopinfo['psbkey']);
            $psbcode = trim($shopinfo['psbcode']);
            $ptpsblink = $shopinfo['psblink'];
        }else{
            $psinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where cityid='".$orderinfo['admin_id']."'  ");
            if(empty($psinfo)){
                $this->error='该城市不存在';
                logwrite($this->error);
                return false;
            }
            if($psinfo['pttopsb'] != 1){
                $this->error='该城市未启用第三方配送';
                logwrite($this->error);
                return false;
            }
            $ptpsblink = $psinfo['ptpsblink'];
            $psbaccid = $psinfo['ptpsbaccid'];
            $psbkey = $psinfo['ptpsbkey'];
            $psbcode = $psinfo['ptpsbcode'];
        }

        $url = $ptpsblink.":8080/orderapi/json/drawbackorder/".$this->psbvision."/".$this->psbapiid;
        $data['orderid'] = $orderid;
        $data['bizid'] = $psbaccid;
        $data['key'] = $psbkey;
        $data['code'] = $psbcode;
        logwrite($url);
        logwrite('配送宝退款：');
        logwrite(json_encode($data));
        $contents = $this->vpost($url,$data);
        $codeinfo = json_decode($contents,true);
        logwrite($contents);
        if(isset($codeinfo['success']) && $codeinfo['success'] == 200){
            return true;
        }elseif(isset($codeinfo['success'])){
            logwrite($this->error);
            return false;
        }else{
            $this->error = $contents;
            logwrite($this->error);
            return false;
        }
    }


    // 模拟提交数据函数
    function vpost($url,$data='',$cookie=''){  
			/*$options = array(  
			   'http' => array(  
				   'method' => 'POST',  
				   // 'content' => 'name=caiknife&email=caiknife@gmail.com',  
				   'content' => http_build_query($data),//$querydata,  
			   ),  
		);   
	    $result = file_get_contents($url, false, stream_context_create($options));  
	 
	    return $result;  */
	

	
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_REFERER,'');// 设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno:'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据   
    }
}
