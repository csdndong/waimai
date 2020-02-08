<?php
/*微信
  转化内容
*/
class wx_b{
	private $obj;
	private $mysql; //定义数据库
	private $tablepre;
	private $result;
	function __construct($wxobj){ 	 
	  global $Mconfig; 
	  $this->tablepre =  $Mconfig['tablepre'];
		$this->mysql = new mysql_class();
		$this->obj = $wxobj; 
    //logwrite('初始化函数结束');
	}
	function text(){ 
		global $Mconfig; 
		$controller = $this->obj->Content;
		//logwrite('调用文本');
		$tempinfo = explode('##',$controller);
		if(count($tempinfo) == 2){
			//修改密码
			$wxuser = $this->mysql->select_one("select * from ".$this->tablepre."wxuser where openid='".$this->obj->FromUserName."'");
			if(empty($wxuser)) $this->Rtext('未关注我们，不可绑定帐号');
			if($wxuser['is_bang'] == 1) $this->Rtext('已绑订帐号不可重复绑定');
			if(empty($tempinfo[0])) $this->Rtext('绑定帐号失败,帐号为空');
			if(empty($tempinfo[1])) $this->Rtext('绑定帐号失败,帐号为空');  
			$info =  $this->mysql->select_one("select * from ".$this->tablepre."member where (email='".$tempinfo[0]."' or username='".$tempinfo[0]."') ");
			if(empty($info)) $this->Rtext('绑定帐号失败,帐号未查找到');
			if(!empty($info['is_bang'])) $this->Rtext('帐号已绑定其他帐号');
			if($info['password'] != md5($tempinfo[1])) $this->Rtext('帐号绑订失败,密码错误');//怎么样绑订定微信号
			$data['uid'] = $info['uid'];
			$data['is_bang'] = 1;
			$this->mysql->update($this->tablepre.'wxuser',$data,"openid='".$this->obj->FromUserName."'");  
			//删除默认绑定帐号
			$temuser  = $this->mysql->select_one("select * from ".$this->tablepre."member where uid='".$wxuser['uid']."' ");
			$all['score'] = $temuser['score']+$info['score'];
			$all['cost'] =  $temuser['cost'] +$info['cost']; 
			$all['is_bang'] = 1;
			$this->mysql->update($this->tablepre.'member',$all,"uid='".$info['uid']."'");  
			//合并积分
			$this->mysql->delete($this->tablepre.'member',"uid ='".$wxuser['uid']."'");    
			$this->Rtext('绑定帐号成功');
		}elseif($controller == 'j'){
			 $this->showjf();
		}elseif($controller == 'c'){
		   $this->showorder();
		}elseif($controller == 'test'){
			  $newlink = $this->Mlink($Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=order');
			  $string .= '<a href="'.$newlink.'">查看历史订单</a>'; 
			  $this->Rtext($string);
		}elseif($controller == 's'){
			  $newlink = $this->Mlink($Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=shophui');
			  $string .= '<a href="'.$newlink.'">查看闪慧商家列表</a>'; 
			  $this->Rtext($string);
		}else{
			//获取自动回答操作
			//xiaozu_wxback
			if(!empty($controller) && strlen($controller) <  10){
				 
			    $backinfo= $this->mysql->select_one("select * from ".$this->tablepre."wxback where code = '".$controller."' ");
			    if(!empty($backinfo)){ 
			     	//logwrite('调用自动回复');
			        $this->trmsg($backinfo['msgtype'],$backinfo['values']); 
			    }else{
				 
					$this->transmitService($this->obj->FromUserName,$this->obj->ToUserName);
 				}  
		    }
			echo '';
			exit;
		} 
	}
	/* 触发多客服会话 */
  private function transmitService($FromUserName,$ToUserName)
    {
         $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $FromUserName, $ToUserName, time());
         echo $result;
    }
	
	
	public function wmrAdminReply(){
		$showcontent = '';
		$showcontent .= '您好，欢迎测试外卖人网上订餐网站系统。';
		$showcontent .="\n";
		$showcontent .= '系统总后台';
		$showcontent .="\n";
		$showcontent .= '账号：admin  密码：ghwmr';
		$showcontent .="\n";
		$showcontent .= '商家后台';
		$showcontent .="\n";
		$showcontent .= '账号：shangjia 密码：ghwmr';
		$showcontent .="\n";
		$showcontent .= '配送端';
		$showcontent .="\n";
		$showcontent .= '账号：peisong 密码：ghwmr';
 		return $showcontent;
	}
	function showorder(){
		global $Mconfig; 
		$buyerstatus = array(
			'0'=>'待处理订单',
			'1'=>'审核通过,待发货',
			'2'=>'订单已发货',
			'3'=>'订单完成',
			'4'=>'买家取消订单',
			'5'=>'卖家取消订单'
		);
		$paytypelist = array('outpay'=>'货到支付','open_acout'=>'账号余额支付');  
		$paylist = $this->mysql->getarr("select * from ".$this->tablepre."paylist   order by id asc limit 0,50");
		if(is_array($paylist)){
			foreach($paylist as $key=>$value){
			    $paytypelist[$value['loginname']] = $value['logindesc'];
			}
		}
		$payarr = array('0'=>'未支付','1'=>'已支付');
		$userinfo = $this->userinfo();
		$nowtime = strtotime(date('Y-m-d',time()));
		$orderlist= $this->mysql->getarr("select * from ".$this->tablepre."order where   addtime > ".$nowtime." and buyeruid = '".$userinfo['uid']."'  ");
		if(empty($orderlist)){
		    $this->Rtext('您今天未下单');
		}
		$string = '';
		foreach($orderlist as $key=>$value){ 
			$string .='单号:'.$value['dno'];
			$string .="\n"; 
			$string .='店铺:'.$value['shopname'];
			$string .="\n"; 
			$string .='店铺电话:'.$value['shopphone']; 
			$string .="\n"; 
			$string .='订单状态:'.$buyerstatus[$value['status']];
			$string .="\n"; 
			$string .= $paytypelist[$value['paytype']].',('.$payarr[$value['paystatus']].')';
			$string .="\n"; 
			$string .='配送时间:'.date('Y-m-d H:i:s',$value['posttime']);
			$string .="\n"; 
			$string .=',订单总价'.$value['allcost'];
			$string .="\n"; 
			$orderdet= $this->mysql->getarr("select * from ".$this->tablepre."orderdet where order_id = '".$value['id']."' ");
			foreach($orderdet as $k=>$v){
				$string .= $v['goodsname'].'('.$v['goodscount'].'*'.$v['goodscost'].')';
				$string .="\n"; 
			}
			$string .="\n";
		} 

		$newlink = $this->Mlink($Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=order');
		$string .= '<a href="'.$newlink.'">查看历史订单</a>'; 
		$this->Rtext($string);
	}
	function  showjf(){
		$userinfo = $this->userinfo();
		$acountstr = '';
		if($userinfo['is_bang'] == 0){
		    $acountstr = '帐号积分'.$userinfo['score'];
		}else{
		    $acountstr = '已绑定'.$userinfo['username'].',帐号积分:'.$userinfo['score'];
		}
		$this->Rtext($acountstr);
	}
	
	function image(){ 
		echo  '';
		exit;
		$this->Rtext('你发的是图片');
	}
	
	function  voice(){ 
		echo  '';
		exit;
		$this->Rtext('你发的是声音');
	}
	function   video(){
		echo  '';
		exit;
		$this->Rtext('你发的是图象');
	 
	}
	function location(){
		global $Mconfig; 
		$userinfo = $this->userinfo();
        if(!empty($userinfo)){
			$shopinfo= $this->mysql->select_one("select * from ".$this->tablepre."shop where uid = '".$userinfo['uid']."' ");
			if(!empty($shopinfo)){
				$data['lat']  = $this->obj->Location_X;
				$data['lng'] = $this->obj->Location_Y;//lat 地图左坐标	lng
				$this->mysql->update($this->tablepre.'shop',$data,"id='".$shopinfo['id']."'");  
				$this->Rtext('您已经绑定店铺，发送定位是绑定商家位置');
			}else{
			//获取最近 10个店铺			
				$lat =  trim($this->obj->Location_X);
				$lng =  trim($this->obj->Location_Y);
				$shoplist =   $this->mysql->getarr("select id,shopname,lat,lng,shoplogo from ".$this->tablepre."shop  where is_open = 1 and is_pass =1 and  SQRT((`lat` -".$lat.") * (`lat` -".$lat." ) + (`lng` -".$lng." ) * (`lng` -".$lng." )) < (`pradiusa`*0.0015)      ORDER BY  (`lat` -".$lat.") * (`lat` -".$lat." ) + (`lng` -".$lng." ) * (`lng` -".$lng." ) ASC limit 0,100");
				$contents = '';
				if(is_array($shoplist)){
					$contents = '获取离您最近的10个店铺';
					$tempinfo = array();
					foreach($shoplist  as $key=>$value){				 
						$temc = array();
						$temc['biaoti'] = $value['shopname'];				 
						$temc['miaoshu'] = $value['shopname'];
						$tupian = empty($value['shoplogo'])? $Mconfig['shoplogo']:$value['shoplogo'];
						if(count(explode('://',$tupian))>1){
						    $temc['tupian'] = $tupian;
						}else{
						    $temc['tupian'] = $Mconfig['siteurl'].$tupian;
						}
						$temc['lianjie'] = $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=shopshow&id='.$value['id'];
						$tempinfo[] = $temc;
					}
					if(count($tempinfo) > 0){
					    $this->trmsg(3,serialize($tempinfo));
					} 
				}  
			}

		}
		echo  '';
		exit; 
		$this->Rtext('您在定位');
	}
	function link(){
		echo  '';
		exit;
		$this->Rtext('您在发送的是连接');
	} 
    function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2){
		define('EARTH_RADIUS', 6378.137);//地球半径，假设地球是规则的球体
		define('PI', 3.1415926);
		$earth = 6378.137;
		$pi = 3.1415926;
		$radLat1 = $lat1 * PI ()/ 180.0;   //PI()圆周率
		$radLat2 = $lat2 * PI() / 180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
		$s = $s * EARTH_RADIUS;
		$s = round($s * 1000);
		if ($len_type > 1){
		    $s /= 1000;
		}		 
		return round($s, $decimal); 
    }
	function event(){ 
		global $Mconfig; 
		$controller = $this->obj->Event;		
		switch($controller){
		    case 'subscribe': //关注		 		   				
				$shopid = 0;
				$fxuid = 0;
				$bdshopid=0;
				$checkshop = $this->obj->EventKey;
				#$this->Rtext(1111);
				if(!empty($checkshop)){
					
					$temp_sinfo = explode('_',$checkshop);
					if(count($temp_sinfo) > 2){
						if($temp_sinfo[1] == 'sp'){
							$shopid = $temp_sinfo[2]; 		
						}
						if($temp_sinfo[1] == 'fx'){
							$fxuid = $temp_sinfo[2]; 		
						}
						if($temp_sinfo[1] == 'bd'){
							$bdshopid = $temp_sinfo[2]; 		
						}				 
					}
				}
				//通过扫码分销二维码关注
				if($fxuid > 0 ){
					$fxmemberinfo = $this->mysql->select_one("select username from ".$this->tablepre."member where uid='".$fxuid."'");
					if(!empty($fxmemberinfo)){
						$fxinfo = $this->mysql->select_one("select * from ".$this->tablepre."fxpid where openid='".$this->obj->FromUserName."'");
						if(empty($fxinfo)){
							$fxdata['openid'] = $this->obj->FromUserName;
							$fxdata['fxpid'] = $fxuid;
							$fxdata['addtime'] = time();
							$this->mysql->insert($this->tablepre.'fxpid',$fxdata);
							$this->Rtext('终于等到你~ 
登录分销中心后才能成为'.$fxmemberinfo['username'].'的下线分销会员哦~
赶快去<a href="'. $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=distribution_center">分销中心</a>查看属于自己的分销二维码吧~');	
						}else{
							$cinfo =  $this->mysql->select_one("select username from ".$this->tablepre."member where uid='".$fxinfo['fxpid']."'");
							$this->Rtext('您已经是'.$cinfo['username'].'的下线分销会员啦，
不能重复扫描哦~~');
						}
					}else{
						$this->Rtext('您扫描的分销二维码的主人找不到了
换个人再试试吧~~');	
					}	
				}
				if($bdshopid>0){
					$shop = $this->mysql->select_one("select * from ".$this->tablepre."shop where id='".$bdshopid."'");
					$openid = $this->obj->FromUserName;
					$wxuser = $this->mysql->select_one("select * from ".$this->tablepre."wxuser where openid='".$openid."'");									
					if($shop['is_bdwx']==0 && empty($shop['wxopenid'])){//未绑定
						if(empty($wxuser['shopid']) && $wxuser['is_bdsp']==0){
							$wxarr['openid'] = $openid;
							$wxarr['is_bdsp'] = 1;
							$wxarr['shopid'] = $bdshopid;
							$this->mysql->insert($this->tablepre.'wxuser',$wxarr);
							$sparr['wxopenid'] = $openid;
							$sparr['is_bdwx'] = 1;
							$this->mysql->update($this->tablepre.'shop',$sparr,"id=".$bdshopid."");
							$this->Rtext('绑定成功');								
						}else{
							if($wxuser['shopid']!=$shop['id']){
								$this->Rtext('您已绑定其他店铺，请先解绑');	
							}else{
								$this->Rtext('您已绑定该店铺');	
							}
						}	
					}else{
						if($shop['wxopenid']!=$openid){
							$this->Rtext('该店铺已绑定其他微信');	
						}else{
							$this->Rtext('您已绑定该店铺');	
						}
					}											
				}
				if($shopid > 0 ){
					$shopinfo = $this->mysql->select_one("select * from ".$this->tablepre."shop where id='".$shopid."'");
				}   
				$arr['username'] = $this->obj->FromUserName;
				$arr['phone'] = '';
				$arr['address'] = '';
				$arr['password'] = md5($this->obj->FromUserName);
				$arr['email'] = '';
				$arr['creattime'] = time();
				$arr['score']  =0;
				$arr['logintime'] = time(); 
				$arr['logo'] = '';
				$arr['loginip'] ='';
				$arr['group'] = 10;
				$ehckinfo = $this->mysql->select_one("select * from ".$this->tablepre."wxuser where openid='".$this->obj->FromUserName."'");
				if(empty($ehckinfo)){
			    // 根据前台注册的手机号检测此手机号数据库中是否领取过优惠券，如果有则更新UID和username status=1   
				// 如果前台新注册的用户 存在分享者 shareuid > 0 则考虑返增推广分享者优惠券
				/*
				$checkphonejuan =  $this->mysql->getarr("select * from ".$this->tablepre."juan where bangphone='".$phone."' and uid=0 and status = 0  "); 
				if( !empty($checkphonejuan) ){
				$tdata['uid'] = $this->uid;
				$tdata['username'] = $tname;
				$tdata['status'] = 1;
				$this->mysql->update($this->tablepre.'juan',$tdata,"bangphone='".$phone."' and uid=0 and status = 0 ");	
				}
				$checksharejuan =  $this->mysql->getarr("select * from ".$this->tablepre."juan where bangphone='".$phone."' and uid='".$this->uid."' and shareuid > 0  "); 
				if( !empty($checksharejuan) ){
				foreach( $checksharejuan as $key=>$jval ){
					$sharemember =  $this->mysql->select_one("select * from ".$this->tablepre."member where uid='".$jval['shareuid']."'   "); 
					if( !empty($sharemember) ){
						$srdata['name'] = $jval['name'];
						$srdata['limitcost'] = $jval['limitcost'];
						$srdata['cost'] = $jval['cost'];
						$srdata['uid'] = $sharemember['uid'];
						$srdata['username'] = $sharemember['username'];
						$srdata['status'] = 1;
						$srdata['bangphone'] = $sharemember['phone'];
						$srdata['type'] = 6;  //返增优惠券类型
						$srdata['endtime'] = $jval['endtime'];   
						$srdata['creattime'] = $jval['endtime'];   
						$srdata['paytype'] = $jval['creattime'];   
						$this->mysql->insert($this->tablepre.'juan',$srdata);
					}
				}
				} 
				 */

				}else{
					if($shopid != '1.2345678123457E+63' ){ 
 						
						if($shopid > 0){
							#$this->Rtext('推送关注场景'.$shopid);  
							$tempinfo = array();
							$temc = array();
							$temc['biaoti'] = $shopinfo['shopname'];
							$temc['miaoshu'] = html_entity_decode($shopinfo['notice_info']);
							$tupian = empty($shopinfo['shoplogo'])? $Mconfig['shoplogo']:$shopinfo['shoplogo'];
							$temc['tupian'] = $Mconfig['siteurl'].$tupian;
							$temc['lianjie'] = $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=huisubshow&id='.$shopinfo['id'];
							$tempinfo[] = $temc;
							if(count($tempinfo) > 0){
								$this->trmsg(3,serialize($tempinfo));
							} 
							   	
						}else{
							$this->Rtext('欢迎回来');
						}			   
					}else{	
				 	
					   $this->Rtext($this->wmrAdminReply());   
					}	   
			   }
			   
			   $backinfo= $this->mysql->select_one("select * from ".$this->tablepre."wxback where code = '".$controller."' ");
				 if(!empty($backinfo)){ 
						 //logwrite('调用自动回复');
					if($shopid != '1.2345678123457E+63' ){     
						if($shopid > 0){
							#$this->Rtext('推送关注场景'.$shopid);   
							$tempinfo = array();
							$temc = array();
							$temc['biaoti'] = $shopinfo['shopname'];
							$temc['miaoshu'] = html_entity_decode($shopinfo['notice_info']);
							$tupian = empty($shopinfo['shoplogo'])? $Mconfig['shoplogo']:$shopinfo['shoplogo'];
							$temc['tupian'] = $Mconfig['siteurl'].$tupian;
							$temc['lianjie'] = $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=huisubshow&id='.$shopinfo['id'];
							$tempinfo[] = $temc;
							if(count($tempinfo) > 0){
								$this->trmsg(3,serialize($tempinfo));
							} 
						}else{
							$this->trmsg($backinfo['msgtype'],$backinfo['values']);  
						}	   
					}else{				   
						$this->Rtext($this->wmrAdminReply());			   
					}   	   				
			   }else{
					if($shopid != '1.2345678123457E+63' ){ 
						if($shopid > 0){
						#	 $this->Rtext('推送关注场景'.$shopid);
							   
							$tempinfo = array();
							$temc = array();
							$temc['biaoti'] = $shopinfo['shopname'];
							$temc['miaoshu'] = html_entity_decode($shopinfo['notice_info']);
							$tupian = empty($shopinfo['shoplogo'])? $Mconfig['shoplogo']:$shopinfo['shoplogo'];
							$temc['tupian'] = $Mconfig['siteurl'].$tupian;
							$temc['lianjie'] = $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=huisubshow&id='.$shopinfo['id'];
							$tempinfo[] = $temc;
							if(count($tempinfo) > 0){
								$this->trmsg(3,serialize($tempinfo));
							} 
						}else{
							$this->Rtext('欢迎关注微信息');  
						}	 
					}else{
						$this->Rtext($this->wmrAdminReply());
					}	 		
			   }
			   break;
		    case 'unsubscribe': //取消关注
				$tempinfo = $this->mysql->select_one("select * from ".$this->tablepre."wxuser where openid='".$this->obj->FromUserName."'");
				$data['access_token'] = '';
				$data['expires_in'] = '0';
				$data['refresh_token'] = ''; 
				$data['is_bang'] = 0;
				$this->mysql->update($this->tablepre."wxuser",$data,"openid='".$this->obj->FromUserName."'");  
				$bang['is_bang'] =  0; 
				if(!empty($tempinfo['uid'])){
				   $this->mysql->update($this->tablepre.'member',$bang,"uid='".$tempinfo['uid']."'");  
				}
				echo '';
				exit;
				break;
		    case 'CLICK':
				$code = $this->obj->EventKey;
				if(empty($code)){
					echo '';
					exit;
				}elseif($code == 'j'){
					$this->showjf();
				}elseif($code == 'c'){
					$this->showorder();
				}else{
					$backinfo= $this->mysql->select_one("select * from ".$this->tablepre."wxmenu where code = '".$code."' ");
					if(!empty($backinfo)){  
						$msgtype = $backinfo['msgtype']+1;
						$this->trmsg($msgtype,$backinfo['values']); 
					}else{
						echo '';
						exit;
					}
				}
				echo  '';
				exit;
				$this->Rtext('您点了菜单'.$caozuoma);
				break;
		    case 'VIEW':
				echo  '';
				exit;
				$this->Rtext('您点了带超连接的菜单');
				break;
		    case 'SCAN':
				$shopid = 0;
				$fxuid = 0;
				$bdshopid=0;
				$checkshop = $this->obj->EventKey;
				if(!empty($checkshop)){
					$temp_sinfo = explode('_',$checkshop);
					if(count($temp_sinfo) > 1){
						if($temp_sinfo[0] == 'fx'){
							$fxuid = $temp_sinfo[1];
						}
						if($temp_sinfo[0] == 'sp'){
							$shopid = $temp_sinfo[1]; 
						}
						if($temp_sinfo[0] == 'bd'){
							$bdshopid = $temp_sinfo[1]; 		
						}	
					}
				}
				if($fxuid > 0){		
				    //二维码主人
					$fxmemberinfo = $this->mysql->select_one("select username from ".$this->tablepre."member where uid='".$fxuid."'");
					//二维码主人wxuser
					$wxusx = $this->mysql->select_one("select openid from ".$this->tablepre."wxuser where uid='".$fxuid."'");
					 
					if($wxusx['openid'] == $this->obj->FromUserName){
						$this->Rtext('自己不可以扫描自己的分销二维码哦~');		 
					}
					$wxus = $this->mysql->select_one("select uid from ".$this->tablepre."wxuser where openid='".$this->obj->FromUserName."'");
					//扫码人member
					$mema = $this->mysql->select_one("select uid,fxpid,befxtime from ".$this->tablepre."member where uid='".$wxus['uid']."'");					
					//扫码人fxpid
					$wxu = $this->mysql->select_one("select * from ".$this->tablepre."fxpid where openid='".$this->obj->FromUserName."'");					
					if(empty($fxmemberinfo)){
						$this->Rtext('您扫描的分销二维码的主人找不到了
换个人再试试吧~~');		
					} 
					
					if(empty($wxu)){
						$fxdata['openid'] = $this->obj->FromUserName;
						$fxdata['fxpid'] = $fxuid;
						$fxdata['addtime'] = time();
						$this->mysql->insert($this->tablepre.'fxpid',$fxdata);
													
					}
					
					if(!empty($mema)){												
						if(empty($mema['fxpid'])){
							$this->mysql->update($this->tablepre.'member',array('fxpid'=>$fxuid,'befxtime'=>time()),"uid='".$mema['uid']."'");
							$this->Rtext('终于等到你~
登录分销中新后才能成为'.$fxmemberinfo['username'].'的下线分销会员哦~
赶快去<a href="'. $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=distribution_center">分销中心</a>查看属于自己的分销二维码吧~');
						}else{
							$nmem =  $this->mysql->select_one("select username from ".$this->tablepre."member where uid='".$mema['fxpid']."'");
							$befxtime = date('Y-m-d H:i:s',$mema['befxtime']);
					        $this->Rtext('你已经在'.$befxtime.'成为'.$nmem['username'].'的下线分销会员啦
不可重复扫描哦~
赶快去<a href="'. $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=distribution_center">分销中心</a>查看属于自己的分销二维码吧~');
							
						}	  
					}else{
						$this->Rtext('终于等到你~
登录分销中新后才能成为'.$fxmemberinfo['username'].'的下线分销会员哦~
赶快去<a href="'. $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=distribution_center">分销中心</a>查看属于自己的分销二维码吧~');
					}
					
				}
				if($bdshopid>0){
					$shop = $this->mysql->select_one("select * from ".$this->tablepre."shop where id='".$bdshopid."'");
					$openid = $this->obj->FromUserName;
				//$this->Rtext($openid);
					$wxuser = $this->mysql->select_one("select * from ".$this->tablepre."wxuser where openid='".$openid."'");									
					if($shop['is_bdwx']==0 && empty($shop['wxopenid'])){//未绑定
						if(empty($wxuser['shopid']) && $wxuser['is_bdsp']==0){
							$wxarr['openid'] = $openid;
							$wxarr['is_bdsp'] = 1;
							$wxarr['shopid'] = $bdshopid;
							$this->mysql->insert($this->tablepre.'wxuser',$wxarr);
							$sparr['wxopenid'] = $openid;
							$sparr['is_bdwx'] = 1;
							$this->mysql->update($this->tablepre.'shop',$sparr,"id=".$bdshopid."");
							$this->Rtext('绑定成功');							
						}else{
							if($wxuser['shopid']!=$shop['id']){
								$this->Rtext('您已绑定其他店铺，请先解绑');	
							}else{
								$this->Rtext('您已绑定该店铺');	
							}
						}	
					}else{
						if($shop['wxopenid']!=$openid){
							$this->Rtext('该店铺已绑定其他微信');	
						}else{
							$this->Rtext('您已绑定该店铺');	
						}
					}											
				}
				//$this->Rtext('推送关注场景'.$checkshop);				
				//推送关注场景1.2345678123457E+63
				if($shopid != '1.2345678123457E+63' ){		
					if($shopid > 0 ){
						$shopinfo = $this->mysql->select_one("select * from ".$this->tablepre."shop where id='".$shopid."'");			 
						#$this->Rtext('推送关注场景'.$shopid);  
						$tempinfo = array();
						$temc = array();
						$temc['biaoti'] = $shopinfo['shopname'];
						$temc['miaoshu'] = html_entity_decode($shopinfo['notice_info']);
						$tupian = empty($shopinfo['shoplogo'])? $Mconfig['shoplogo']:$shopinfo['shoplogo'];
						$temc['tupian'] = $Mconfig['siteurl'].$tupian;
						$temc['lianjie'] = $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=huisubshow&id='.$shopinfo['id'];
						$tempinfo[] = $temc;
						if(count($tempinfo) > 0){
							$this->trmsg(3,serialize($tempinfo));
						} 	 
					}	
				}else{
					 $this->Rtext($this->wmrAdminReply());	   
					}   		  
				echo  '';
				exit;
				$this->Rtext('你进行扫描进入');
				break;
		    case 'LOCATION':
				$userinfo = $this->userinfo();
				if(!empty($userinfo)){
					$memberdata['wxlat'] = $this->obj->Latitude; 
					$memberdata['wxlng'] = $this->obj->Longitude;
					$this->mysql->update($this->tablepre.'wxuser',$memberdata,"uid='".$userinfo['uid']."'");  
					$shopinfo= $this->mysql->select_one("select * from ".$this->tablepre."shop where uid = '".$userinfo['uid']."' ");
					$lat =  $this->obj->Latitude;
					$lng =  $this->obj->Longitude;	             
				}else{
			   
				}
				echo  '';
				exit; 
				break;
			default:
				$this->Rtext('未定义的事件');
				break;
			} 
	}
	function error(){
		return '';
	}
	function userinfo(){
	    $info = $this->mysql->select_one("select * from ".$this->tablepre."wxuser as a left join ".$this->tablepre."member as b on a.uid = b.uid   where a.openid='".$this->obj->FromUserName."' ");
	    return $info;
	}
	//回复信息函数
	function trmsg($msgtype,$msgcontent){//msgtype == 1 表示 连接  2 表示内容  3表示图文
		if($msgtype ==  1){ 
			if(!empty($msgcontent)){
			 	$newcontent = unserialize($msgcontent);
			    if(isset($newcontent['lj_link'] ) && isset($newcontent['lj_title'])){ 
			        $links = $this->Mlink($newcontent['lj_link']);
			        $string = '<a href="'.$links.'">'.$newcontent['lj_title'].'</a>'; 
			        $this->Rtext($string); 
			    }
			}   
		}elseif($msgtype == 2){
		    if(!empty($msgcontent)){
		        $this->Rtext($msgcontent);
		    }
		}elseif($msgtype == 3){ 
			if(!empty($msgcontent)){
			    $newcontent =  unserialize($msgcontent);//biaoti miaoshu       
			    if(is_array($newcontent)){ 
			     	$newsTplBody = "<item>
                <Title><![CDATA[%s]]></Title> 
                <Description><![CDATA[%s]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>";
					$string = '';
					foreach($newcontent  as $key=>$value){
						$links = $this->Mlink($value['lianjie']); 
						$stringtemp = sprintf($newsTplBody, $value['biaoti'], $value['miaoshu'], $value['tupian'],$links);
						$string .= $stringtemp;
					}
					if(!empty($string)){
						$this->Rnews($string,count($newcontent)); 
					}
			    }
			}  
		}
		echo '';
		exit;
	}
	function Rtext($msg){
		$msgType = 'text';
		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
		$time = time();
		$resultStr = sprintf($textTpl, $this->obj->FromUserName, $this->obj->ToUserName, $time, $msgType, $msg);
		logwrite($resultStr);
		echo $resultStr;
		exit; 	
	}
	function Rnews($msg,$shuliang){
		$msgType = 'news';
		$time = time();
		$newsTplHead = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<ArticleCount>%s</ArticleCount>
			<Articles>%s</Articles>
			<FuncFlag>0</FuncFlag>
			</xml>";
        $resultStr = sprintf($newsTplHead, $this->obj->FromUserName, $this->obj->ToUserName, $time, $msgType,$shuliang, $msg);
	    echo $resultStr;
	    exit; 	
	}
	function Mlink($link){
		global $Mconfig; 
		$time = time();
		$tempstr = md5(TOKEN.$time);
		$tempstr = substr($tempstr,3,15);
		$mynewstr = '';
		if(!empty($link)){
			$dolink = $link;
		    for($i=0;$i<strlen($dolink);$i++){
	            $mynewstr .= ord($dolink[$i]).',';
            }
        }
	 
		$linkstr =  $Mconfig['siteurl'].'/index.php?ctrl=wxsite&action=index&openid='.$this->obj->FromUserName.'&actime='.$time.'&sign='.$tempstr.'&backinfo='.$mynewstr;
		return $link;		 
	}
	function result(){
	    return $this->result;
	}
	 
	public function __call($name, $arguments) {
        logwrite("Calling object method".$name." ");
        $this->Rtext('未定义的操作');
  }
	
}


?>