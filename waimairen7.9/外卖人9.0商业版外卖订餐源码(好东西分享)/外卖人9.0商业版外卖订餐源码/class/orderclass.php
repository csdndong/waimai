<?php 

/**
 * @class Cart
 * @brief 天气预报
 */
class orderclass
{
	private $error = ''; 
	private $orderid = ''; 
	 
	 //普通用户登录
	 
	protected $ordmysql; 
	function __construct()
	{
	 	$this->ordmysql =new mysql_class();  
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
        return floatval($cost);		
	 } 
	//关闭订单通知
	function noticeclose($orderid,$reason){
		$orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		if(!empty($orderinfo['buyeruid'])){
	      	$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
	      	$wx_s = new wx_s(); 
	      	$appCls = new appbuyclass();  
	      	//app信息
	      	$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
	      	$default_tpl = new config('tplset.php',hopedir);  
	        $tpllist = $default_tpl->getInfo(); 
	        $orderinfo['reason'] = $reason;
	        if(isset($tpllist['noticemake']) && !empty($tpllist['phoneunorder'])){
	        $emailcontent = Mysite::$app->statichtml($tpllist['phoneunorder'],$orderinfo);  
	        if(!empty($appcheck)){ 
				$tempuser[] = $appcheck;
				$backinfo = $appCls->SetUserlist($tempuser)->sendNewmsg('订单被关闭',$emailcontent);
	        }
	        if(!empty($orderinfo['buyeruid']))
	        {
			   $wxbuyer = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."wxuser  where uid= '".$orderinfo['buyeruid']."'   ");
			   if(!empty($wxbuyer)){  
					 if($wx_s->sendmsg($emailcontent,$wxbuyer['openid'])){
					  }else{
						logwrite('微信客服发送错误:'.$wx_s->err());  
					 }
					
			   }
	        }
			 
	        if(Mysite::$app->config['smstype'] == 1){
				$smsParams = array(
				'dno'=>(string)$orderinfo['dno'],
				'reason'=>$reason,
				'time'=>date('Y-m-d H:i:s',time()),
				);				
				$aliCls=new alidayuClass();
				$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg3']);
				if(   ($resp->Code) == 'OK'  ){
					logwrite('大鱼关闭订单发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
				}else{
				
					logwrite("大鱼关闭订单发送成功！电话号码".$orderinfo['buyerphone']);
				}
			}
	      }
	   }
	}
	//制作订单通知 8.6更新
	function noticemake($orderid){
		$orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		//自动生成配送单------ 
		if($orderinfo['pstype'] == 0 && $orderinfo['is_goshop'] == 0){//网站配送自动生成配送单
			 
			 
			  $checkpsinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderinfo['id']."'   ");
				if(empty($checkpsinfo)){ 
					 
				
                            $psdata['orderid'] = $orderid;
                            $psdata['shopid'] = $data['shopid'];
                            $psdata['status'] =0;
                            $psdata['dno'] = $data['dno'];
                            $psdata['addtime'] = time();
                            $psdata['pstime'] = $data['posttime'];
							$admin_id = $orderinfo['admin_id'];
                                                        $psset = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
                                                        $checkpsyset = $psset['psycostset'];
                                                        $bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
                                                        $psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei; 
                           
                            $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单
							
							
							
							logwrite("写配送单的订单ID：".$orderid);
					} 
			 
			 
			 
			//$psylist =  $this->ordmysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$orderinfo['admin_id'].""); 
			//$psCls = new apppsyclass(); 
			//$psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理'); 
			
		}
		//配送单结束--------  
		
		if(!empty($orderinfo['buyeruid'])){
			$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
			$wx_s = new wx_s(); 
			$appCls = new appbuyclass();  //appBuyclass 
			//app信息
			$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
			$default_tpl = new config('tplset.php',hopedir);  
			$tpllist = $default_tpl->getInfo(); 
			if(isset($tpllist['noticemake']) && !empty($tpllist['noticemake'])){
				$emailcontent = Mysite::$app->statichtml($tpllist['noticemake'],$orderinfo);  
				if(!empty($appcheck)){
						$tempuser[] = $appcheck;
						$backinfo = $appCls->SetUserlist($tempuser)->sendNewmsg('商家确认制作该订单',$emailcontent);	
				}
				if($this->sendWxMsg($orderinfo['id'],4,1)){
					
				}
				if(Mysite::$app->config['smstype'] == 1){
					$smsParams = array(
					'dno'=>(string)$orderinfo['dno'],
					'shopname'=>$orderinfo['shopname'],				 
					);				
					$aliCls=new alidayuClass();
					$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg5']);
					if(   ($resp->Code) == 'OK'  ){
						logwrite('大鱼制作订单发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
					}else{
					
						logwrite("大鱼制作订单发送成功！电话号码".$orderinfo['buyerphone']);
					}
				}
				
				
			}
		}
	}
	//不制作订单通知
	function noticeunmake($orderid){
		  $orderinfo = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		  if(!empty($orderinfo['buyeruid'])){
				if($this->sendWxMsg($orderinfo['id'],8,1)){
						
					}
				$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
				$wx_s = new wx_s(); 
				$appCls = new appbuyclass();  
				//app信息
				$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
				$default_tpl = new config('tplset.php',hopedir);  
				$tpllist = $default_tpl->getInfo(); 
				if(isset($tpllist['noticeunmake']) && !empty($tpllist['noticeunmake'])){
					$emailcontent = Mysite::$app->statichtml($tpllist['noticeunmake'],$orderinfo);  				
                if(Mysite::$app->config['smstype'] == 1){
					$smsParams = array(
					'sitename'=>Mysite::$app->config['sitename'],
					'shopname'=>$orderinfo['shopname'],				 
					);				
					$aliCls=new alidayuClass();
					$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg4']);
					if(   ($resp->Code) == 'OK'  ){
						logwrite('大鱼不制作订单发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
					}else{
					
						logwrite("大鱼不制作订单发送成功！电话号码".$orderinfo['buyerphone']);
					}
				}
			}
		 }
	}
	//退款成功通知
	function noticeback($orderid){
		$orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		//-----------取消配送单---
		$checkpsinfo = $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."orderps where orderid='".$orderinfo['id']."'   ");
		if(!empty($checkpsinfo)){ 
			$psdata['status'] =4;
			$this->ordmysql->ordmysql(Mysite::$app->config['tablepre'].'orderps',$psdata,"id='".$checkpsinfo['id']."'");
		} 
		//-----------取消配送单---
		if(!empty($orderinfo['buyeruid'])){
			//微信推送消息
			if($this->sendWxMsg($orderinfo['id'],11,1)){
					
				}
			$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
			$wx_s = new wx_s(); 
			$appCls = new appbuyclass(); 
			$drawbacklog =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."drawbacklog  where orderid= '".$orderid."'   ");
			$orderinfo['reason'] = $drawbacklog['bkcontent'];
			
			//app信息
			$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
			$default_tpl = new config('tplset.php',hopedir);  
			$tpllist = $default_tpl->getInfo(); 
			if(isset($tpllist['noticeback']) && !empty($tpllist['noticeback'])){
				$emailcontent = Mysite::$app->statichtml($tpllist['noticeback'],$orderinfo);  
				if(!empty($appcheck)){ 
					$tempuser[] = $appcheck;
					$backinfo = $appCls->SetUserlist($tempuser)->sendNewmsg('退款成功',$emailcontent);	 
				}
				if(Mysite::$app->config['smstype'] == 1){
					$smsParams = array(
					'sitename'=>Mysite::$app->config['sitename'],
					'shopname'=>$orderinfo['shopname'],				 
					);				
					$aliCls=new alidayuClass();
					$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg2']);
					if(   ($resp->Code) == 'OK'  ){
						logwrite('大鱼退款成功发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
					}else{
					
						logwrite("大鱼退款成功发送成功！电话号码".$orderinfo['buyerphone']);
					}
				} 
			}
		} 
	}
	//发货通知
	function noticesend($orderid){
		$orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
	  if(!empty($orderinfo['buyeruid'])){
			if($orderinfo['pstype']==1){
				if($this->sendWxMsg($orderinfo['id'],5,1)){
					
				}
			}else{
				if($this->sendWxMsg($orderinfo['id'],6,1)){
					
				}
			}
	      	$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
	      	$wx_s = new wx_s(); 
	      	$appCls = new appbuyclass(); 
	        $drawbacklog =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."drawbacklog  where orderid= '".$orderid."'   ");
	        $orderinfo['reason'] = $drawbacklog['bkcontent'];
	      	//app信息
	      	$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
	      	$default_tpl = new config('tplset.php',hopedir);  
	        $tpllist = $default_tpl->getInfo(); 
	        if(isset($tpllist['noticesend']) && !empty($tpllist['noticesend'])){
	            $emailcontent = Mysite::$app->statichtml($tpllist['noticesend'],$orderinfo);  
	            if(!empty($appcheck)){ 
					  $tempuser[] = $appcheck;
					 $backinfo = $appCls->SetUserlist($tempuser)->sendNewmsg("发货提示",$emailcontent);	  
	            }
	        }
	   }
	}
	
	//退款失败通知
	function noticeunback($orderid){
	  $orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
	  if(!empty($orderinfo['buyeruid'])){
			//微信推送消息
			if($this->sendWxMsg($orderinfo['id'],12,1)){
					
				}
	      	$smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
	      	$wx_s = new wx_s(); 
	      	$appCls = new appbuyclass(); 
	        $drawbacklog =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."drawbacklog  where orderid= '".$orderid."'   ");
	        $orderinfo['reason'] = $drawbacklog['bkcontent'];
	      	//app信息
	      	$appcheck =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."appbuyerlogin where uid = '".$orderinfo['buyeruid']."'   "); 
	      	$default_tpl = new config('tplset.php',hopedir);  
	         $tpllist = $default_tpl->getInfo(); 
	        $emailcontent = Mysite::$app->statichtml($tpllist['noticeunback'],$orderinfo);  
	        if(!empty($appcheck)){ 
				$tempuser[] = $appcheck;
				$backinfo = $appCls->SetUserlist($tempuser)->sendNewmsg("退款提示",$emailcontent);
	        }	
	        if(Mysite::$app->config['smstype'] == 1){
				$smsParams = array(
				'sitename'=>Mysite::$app->config['sitename'],
				'shopname'=>$orderinfo['shopname'],				 
				);				
				$aliCls=new alidayuClass();
				$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg1']);
				if(   ($resp->Code) == 'OK'  ){
					logwrite('大鱼退款失败发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
				}else{
				
					logwrite("大鱼退款失败发送成功！电话号码".$orderinfo['buyerphone']);
				}
			}
	  }
	}
  //发送下单通知
  function  sendmess($orderid){
	  
  	   $smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false);  
  	   $wx_s = new wx_s(); 
  	   $orderinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
	     $orderdet =  $this->ordmysql->getarr("select *  from ".Mysite::$app->config['tablepre']."orderdet  where order_id= '".$orderid."'   ");  
	     $shopinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."shop  where id= '".$orderinfo['shopid']."'   "); 
		 $memberinfo =  $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."member  where uid= '".$orderinfo['buyeruid']."'   ");

	     $contents = '';
	     $checknotice =  isset($shopinfo['noticetype'])? explode(',',$shopinfo['noticetype']):array();
	     $contents = ''; 
	     
	     $orderpastatus = $orderinfo['paystatus'] == 1?'已支付':'未支付';
	     $orderpaytype = $orderinfo['paytype'] == 1?'在线支付':'货到支付';
		 $tempdata = array('orderinfo'=>$orderinfo,'orderdet'=>$orderdet,'sitename'=>Mysite::$app->config['sitename']);
		 $open_acouttempdata = array('orderinfo'=>$orderinfo,'orderdet'=>$orderdet,'sitename'=>Mysite::$app->config['sitename'],'memberinfo'=>$memberinfo);
 
		 /* 任务 自动拨打电话--未制作 开启后 N分钟自动拨打  有条件==== */
		$is_auto_callphone = intval(Mysite::$app->config['is_auto_callphone']);
		if($is_auto_callphone==1  && $orderinfo['is_make'] == 0 &&  $orderinfo['shoptype'] != 100  ){
			$TaskNotice = new TaskNotice();
			$TaskNotice->callShopMake($orderinfo['id']);
		}
		
	 
	     /*发送APP到商家**/
	      
		 if(in_array(3,$checknotice))
		   {
			   $shopordertype = $shopinfo['is_autopreceipt']==1?'自动接单提醒':'下单提醒';
			   $neirong = Mysite::$app->config['sitename'].$shopordertype;
			   foreach($orderdet  as $key=>$value){
			   $neirong .=  ','.$value['goodsname'].$value['goodscount'].'份';
			   }
			   $appCls = new appclass();
			   $shopuserlist = $this->ordmysql->getarr("select * from ".Mysite::$app->config['tablepre']."applogin where uid='".$orderinfo['shopuid']."' ");
			   
			   $appCls->SetUid($orderinfo['shopuid']) 
						->SetUserlist($shopuserlist)
			          ->SetExtra('dno|'.$orderinfo['dno'])
					  ->sendNewmsg(Mysite::$app->config['sitename'].$shopordertype,$neirong);
			    
	     } 
	    
	     /*短信通知商家*/ 
	
	    if(in_array(1,$checknotice)){
				
			 if(IValidate::suremobi($orderinfo['shopphone'])){
					
				if(Mysite::$app->config['smstype'] == 1){
					
					$smsParams = array(				 
					'shopname'=>$orderinfo['shopname'],				 
					);				
					$aliCls=new alidayuClass();
					
					$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['shopphone'], Mysite::$app->config['alimsg7']);
					
					if(   ($resp->Code) == 'OK'  ){
						
						logwrite("大鱼下单通知商家发送成功！电话号码".$orderinfo['shopphone']);
						
					}else{
						logwrite('大鱼下单通知商家发送错误：'.$orderinfo['shopphone'].":".var_export($resp,true));
						
					}
			   }else{
				    $default_tpl = new config('tplset.php',hopedir);  
					$tpllist = $default_tpl->getInfo(); 
					if(!isset($tpllist['shopphonetpl']) || empty($tpllist['shopphonetpl'])){
					 
					}else{    
						$contents = Mysite::$app->statichtml($tpllist['shopphonetpl'],$tempdata);     
						$phonecode = new phonecode($this->ordmysql,0,$orderinfo['shopphone']); 
						$phonecode->sendother($contents);     
					}  
			   }	
				     
			}else{
			   logwrite('短信发送商家'.$shopinfo['shopname'].'联系电话错误');
			}
 
	    } 

	     //微信通知商家  此功能要每天访问网站微信一次
	      
	     
	    //打印机 通知商家 
	    
	  if(!empty($shopinfo['machine_code'])&&!empty($shopinfo['mKey'])){
	     	 
	     	  $temp_content = '';
	     	  foreach($orderdet as $km=>$vc){
				$goodsattr = $vc['goodsattr'];
				if(empty($goodsattr)){
					$xx =  $this->ordmysql->select_one("select goodattr  from ".Mysite::$app->config['tablepre']."goods  where id= '".$vc['goodsid']."'   ");
					if(empty($xx['goodattr'])){
						$goodsattr = '份';
					}else{
						$goodsattr = $xx['goodattr'];
					}
				}
                $temp_content .= $vc['goodsname'].'('.$vc['goodscount'].''.$goodsattr.') \n ';
	         }
			 
			if( $orderinfo['is_goshop'] == 0 &&  $orderinfo['bagcost'] > 0  ){
 				$bagcostContent =  '打包费：'.$orderinfo['bagcost'].'元 ';  
			}else{
				$bagcostContent = '';
			}
			 
$msg = '
编号：'.$orderinfo['daycode'].'
*******************************
商家：'.$shopinfo['shopname'].'
订餐热线：'.Mysite::$app->config['litel'].' 
订单状态：'.$orderpaytype.',('.$orderpastatus.')
姓名：'.$orderinfo['buyername'].'
电话：'.$orderinfo['buyerphone'].'
地址：'.$orderinfo['buyeraddress'].'
下单时间：'.date('m-d H:i',$orderinfo['addtime']).'
配送时间：'.date('m-d H:i',$orderinfo['posttime']).' 
*******************************
'.$temp_content.' 
******************************* 

'.$bagcostContent.' 
配送费：'.$orderinfo['shopps'].'元
合计：'.$orderinfo['allcost'].'元
※※※※※※※※※※※※※※
谢谢惠顾，欢迎下次光临
订单编号'.$orderinfo['dno'].'
备注'.$orderinfo['content'].'
*******************************
编号:'.$orderinfo['daycode'].'
'; 
	    $this->dosengprint($msg,$shopinfo['machine_code'],$shopinfo['mKey']);
	  }    
	     //邮件通知卖家
	     /*
	    if(in_array(2,$checknotice)){//同时使用邮件通知 
	      
	       
	       	 if(IValidate::email($shopinfo['email'])){ 
	       	 	  
	        	  	//surelink  
	        	  	//算方计算
	        	  $tempcontent = '<table align="center" width="100%"><tbody><tr> <td colspan="2" align="center"><h1><strong>'.Mysite::$app->config['sitename'].'订单信息</strong></h1><hr></td></tr>';
	        	  $tempcontent .= '<tr><td width="100"><strong>订单编号：</strong></td><td>'.$orderinfo['dno'].'</td></tr><tr><td><strong>店铺名称：</strong></td><td>'.$orderinfo['shopname'].'</td></tr>';
	        	  $tempcontent .= '<tr><td><strong>联系姓名：</strong></td><td>'.$orderinfo['buyername'].'</td></tr><tr><td><strong>联系电话：</strong></td><td>'.$orderinfo['buyerphone'].'</td></tr>';
	        	  $tempcontent .= '<tr><td valign="top"><strong>配送地址：</strong></td><td>'.$orderinfo['buyeraddress'].'</td></tr><tr><td><strong>下单时间：</strong></td><td>'.date('Y-m-d H:i:s',$orderinfo['addtime']).'</td></tr>';
              foreach($orderdet as $key=>$value){
              	$tempre = $key == 0?'<strong> 订单详情：</strong>':'';
     	          $tempcontent .= '<tr><td>'.$tempre.'</td><td>'.$value['goodsname'].','.$value['goodscount'].'份,'.$value['goodscost'].'元/份</td></tr>';
              }
 	        	  $tempcontent .= '<tr><td valign="top"><strong>备注：</strong></td><td>'.$orderinfo['content'].'</td></tr>';
 	        	  $tempcontent .= '<tr><td valign="top"><strong>配送时间：</strong></td><td>'.date('Y-m-d H:i:s',$orderinfo['posttime']).'</td></tr>';
	        	  $tempcontent .= '<tr><td><strong>总金额：</strong></td><td><span class="price">'.$orderinfo['allcost'].'元</span>'.$orderpaytype.',('.$orderpastatus.')</td></tr>'; 
	        	  $tempcontent .= '</tbody></table>';
	        	  $title = '您有一笔'.Mysite::$app->config['sitename'].'新订单';  
	        	   logwrite('商家'.$shopinfo['shopname'].'邮箱地址'.$shopinfo['email'].'错误');
               $info = $smtp->send($shopinfo['email'], Mysite::$app->config['emailname'],$title,$tempcontent, "" , "HTML" , "" , "");  
               
           }else{
           	 logwrite('商家'.$shopinfo['shopname'].'邮箱地址'.$shopinfo['email'].'错误');
           } 	
	       
	     } 
		 */
	   //短信通知买家有效
	       $contents = '';
			if(Mysite::$app->config['allowedsendbuyer'] == 1){        				
				if($orderinfo['paytype_name'] == 'open_acout'  ){
					$orderinfo['buyerphone'] = $memberinfo['phone'];
				}
		     	 if(IValidate::suremobi($orderinfo['buyerphone'])){ 
	       	 	    if(Mysite::$app->config['smstype'] == 1){
						$smsParams = array(				 
						'shopname'=>$orderinfo['shopname'],
				        'buyername'=>$orderinfo['buyername'],	
						'dno'=>$orderinfo['dno'],		
						);				
						$aliCls=new alidayuClass();
						$resp=$aliCls->sendTextMessage(Mysite::$app->config['aliqm'], $smsParams, $orderinfo['buyerphone'], Mysite::$app->config['alimsg6']);
						if(   ($resp->Code) == 'OK'  ){
							logwrite('大鱼下单通知买家发送错误：'.$orderinfo['buyerphone'].":".var_export($resp,true));
						}else{
						
							logwrite("大鱼下单通知买家发送成功！电话号码".$orderinfo['buyerphone']);
						}
					}else{
						$default_tpl = new config('tplset.php',hopedir);  
						$tpllist = $default_tpl->getInfo(); 
						if(!isset($tpllist['userbuytpl']) || empty($tpllist['userbuytpl'])){
							logwrite('短信发送会员模版失败'); 
						}else{  
							if( $orderinfo['paytype_name'] != 'open_acout'  ){
								$contents = Mysite::$app->statichtml($tpllist['userbuytpl'],$tempdata);   
							}else{				   
								$contents = Mysite::$app->statichtml($tpllist['userbuytpl'],$open_acouttempdata);   
							} 
							$phonecode = new phonecode($this->ordmysql,0,$orderinfo['buyerphone']); 
							$phonecode->sendother($contents);  
						}
					} 
				} 
	       }  
  }
   
	 
  function request_by_other($remote_server, $post_string){
  	$context = array(   'http' => array( 
  	                              'method' => 'POST', 
                                 'header' => 'Content-type: application/x-www-form-urlencoded' .
                     
                                           '\r\n'.'User-Agent : Jimmy\'s POST Example beta' .
                     
                                           '\r\n'.'Content-length:' . strlen($post_string) + 8, 
                               'content' => '' . $post_string) 
                     );
                     
                       $stream_context = stream_context_create($context);
                      
                       $data = file_get_contents($remote_server, false, $stream_context);
                     
       return $data;
  }
  public function getorder(){
  	return $this->orderid;
  }
  public function ero(){
  	return $this->error;
  }
   public function dosengprint($msg,$machine_code,$mKey){
  	$xmlData = '<xml>
 <mKey><![CDATA['.$mKey.']]></mKey >
<machine_code><![CDATA['.$machine_code.']]></machine_code > 
<Content><![CDATA['.$msg.']]></Content >
</xml>';

//第一种发送方式，也是推荐的方式：
$url = 'http://www.waimairen.com/print/wmr.php';  //接收xml数据的文件
$header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
$ch = curl_init ($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
$response = curl_exec($ch);
if(curl_errno($ch)){
    print curl_error($ch);
}
curl_close($ch);  
  	
  }
  //订单处理 普通订单处理 
  
  function makenormal($info){
  	 //需要的公共数据 
  	 //$data['othercontent'] = $info['othercontent'];
  	// $data['cattype'] = $info['cattype'];//表示 是否是订台
         $data['areaids'] = $info['areaids'];
	     $data['admin_id'] = $info['shopinfo']['admin_id']; 
		 $data['shoptype'] = $info['shopinfo']['shoptype'];//: 0:普通订单，1订台订单 
		 //获取店铺商品总价  获取超市商品总价
		 $data['shopcost'] = $this->formatcost($info['allcost'],2);
		 $data['addpscost'] = $this->formatcost($info['addpscost'],2);
		 $data['is_ziti'] = $info['is_ziti']; //是否是自取订单		
		 $data['shopps'] = $info['is_ziti'] == 1?0:$info['shopps'];
		 $data['cx_manjian'] = $this->formatcost($info['cx_manjian'],2);
         $data['cx_zhekou'] = $this->formatcost($info['cx_zhekou'],2);
		 $data['cx_shoudan'] = $this->formatcost($info['cx_shoudan'],2);
		 $data['cx_nopsf'] = $info['is_ziti'] == 1?0:$info['cx_nopsf'];
		 $data['bagcost'] =  $this->formatcost($info['bagcost'],2);
		 $data['ordertype'] = $info['ordertype']; 
		   
		 $data['buyerlng'] = $info['buyerlng']; 
		 $data['buyerlat'] = $info['buyerlat'];  
		 $data['shoplng'] = $info['shopinfo']['lng']; 
		 $data['shoplat'] = $info['shopinfo']['lat']; 
		 
		 //支付方式检测
		 $userid = $info['userid'];
		 $data['paytype'] = $info['paytype']; 
		 $data['cxids'] = '';
		 $data['cxcost'] = 0;
		 $zpin = array(); 
		  $data['pstype'] = $info['pstype'] ; 
		 if($data['shopcost'] > 0){
		    $sellrule =new sellrule();
             if($info['platform']){
                 $platform=$info['platform'];
             }else{
                 $platform=0;
             }
			 $costx = $data['shopcost'];
             $sellrule->setdata($info['shopinfo']['id'],$costx,$info['shopinfo']['shoptype'],$info['userid'],$platform,$data['paytype'],$data['bagcost']); 
		     $ruleinfo = $sellrule->getdata();
             
             //判断是否存在打折商品
             foreach($info['goodslist'] as $k=>$v){
                 if($v['cxinfo']['is_cx']==1 &&  $v['cxinfo']['cxcost']>0){
                     //如果存在打折商品 修改促销金额为0
                     $ruleinfo['downcost'] = 0;
                     $ruleinfo['nops'] = false;
					 $ruleinfo['cxdet'] = '';
					 $ruleinfo['cxids'] = '';
					 $ruleinfo['zid'] = '';
                     break;
                 }
             }

		
		 
		 $cxdet = array();
		 if($data['is_ziti'] == 1 && !empty($ruleinfo['cxdet'])){//自提情况下，筛选掉免配送费活动
			foreach($ruleinfo['cxdet'] as $k=>$v){
				if($v['type'] != 4){
					$cxdet[] = $v;
				}	
			}
		 }else{
			 $cxdet = empty($ruleinfo['cxdet'])?array():$ruleinfo['cxdet'];
		 }
		 
		 $data['cxdet'] = serialize($cxdet);
		 $data['cxcost'] = $ruleinfo['downcost'];
         $data['shopdowncost'] = $ruleinfo['shopdowncost']>$data['cxcost']?$data['cxcost']:$ruleinfo['shopdowncost'];//促销中  平台承担的部分		 
		 if($ruleinfo['nops'] == true){
			  //存在免配送费时，把配送费算在促销金额中，平台配送情况下减免的配送费全部由平台承担，商家配送的情况下减免的配送费全部由商家承担
		      if($data['pstype'] == 1){//商家配送
				  $data['shopdowncost'] = $data['shopdowncost'];
			  }else{ //平台配送
				  $data['shopdowncost'] = $data['shopdowncost'] + $data['shopps'];  
			  }			  
			  $data['cxcost'] = $data['cxcost'] + $data['shopps'];
		  }
	      
	      $data['cxids'] = $ruleinfo['cxids'];  
	      $zpin = $ruleinfo['zid'];//赠品	      
	   }
 
	  //判断优惠劵
	  $allcost = $data['shopcost'];
	  
	  $data['yhjcost'] = 0;
		$data['yhjids'] = ''; 
		$userid = $info['userid'];
		$juanid = $info['juanid']; 
		 
	   if($juanid > 0 && $userid > 0){
	      $juaninfo = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."juan  where id= '".$juanid."' and uid='".$userid."'  and status < 2 and endtime > ".time()." ");
	   	  
		  if(!empty($juaninfo)){
	   	  	 if($allcost+$data['bagcost'] >= $juaninfo['limitcost']){ 
	   	  	 	$data['yhjcost'] =  $juaninfo['cost']; 
	   	  	 	$juandata['status'] = 2;
	   	  	 	$juandata['usetime'] =  time(); 
	   	  	 	 $this->ordmysql->update(Mysite::$app->config['tablepre'].'juan',$juandata,"id='".$juanid."'");
	   	  	 	$data['yhjids'] = $juanid;
	   	  	 } 
	   	  } 
	   }

	  //积分抵扣
	  $allcost = $allcost - $data['cxcost'] - $data['yhjcost'];
	   
	  
	  
	  $fupscost = isset($info['addpscost'])?$info['addpscost']:0;
	   
	  $data['scoredowncost'] = $dikou;
	  $data['allcost'] = $allcost+$data['shopps']+$fupscost+$data['bagcost']; //订单应收费用      
	  $data['scoredown'] = 0;
	  $dikou = $info['dikou'];
	  if(!empty($userid) && $dikou > 0 && Mysite::$app->config['scoretocost'] > 0 && $data['allcost'] > $dikou){
	    	 $checkuser= $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$userid."'  "); 
	    	 if(is_array($checkuser)){
	    	     $checkscore = $dikou*(intval(Mysite::$app->config['scoretocost']));
	    	    if($checkuser['score']  >= $checkscore){  
	    	   	  $data['scoredown'] =  $checkscore;
	    	 	    $this->ordmysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`-'.$checkscore,"uid ='".$userid."' ");
                    $data1['userid'] =  $userid;
					$data1['type'] = 1;
					$data1['addtype'] = 2;
					$data1['result'] = $checkscore;
					$data1['addtime'] = time();
					$data1['title'] = '下单抵扣积分';
					$data1['content'] = '下单抵扣积分';  
					$data1['acount'] = $checkuser['score'] - $checkscore;	;
		            $this->ordmysql->insert(Mysite::$app->config['tablepre'].'memberlog',$data1);	
	    	    } 
	    	 }
	  }
	  $dikou = $data['scoredown'] > 0?$dikou:0;
	  $data['allcost'] = $data['allcost']-$dikou;
	  $data['allcost'] = $data['allcost']>=0?$data['allcost']:0; 
	  $data['allcost'] = $this->formatcost($data['allcost'],2);
	  $data['addpscost']=$fupscost;//增加附加配送费
		//检测店铺

	  $data['shopuid'] = $info['shopinfo']['uid'];// 店铺UID	
	  $data['shopid'] =  $info['shopinfo']['id']; //店铺ID	
	  $data['shopname'] = $info['shopinfo']['shopname']; //店铺名称	
	  $data['shopphone'] = $info['shopinfo']['phone']; //店铺电话
	  $data['shopaddress'] = $info['shopinfo']['address'];// 店铺地址	
	  $data['buyeraddress'] = $info['addressdet'];
	  $data['ordertype'] = $info['ordertype'];//来源方式;
	  $data['buyeruid'] = $userid;// 购买用户ID，0未注册用户	
	  $data['buyername'] =  $info['username'];//购买热名称
	  $data['buyerphone'] = $info['mobile'];// 联系电话   
	  $panduan = Mysite::$app->config['man_ispass'];
	  $data['status'] = 0;
	  if($panduan != 1 && $data['paytype'] == 0){
 		 $data['status'] = 1;
	  } 
	  $data['paystatus'] = 0;// 支付状态1已支付	
	  $data['content'] = $info['remark'];// 订单备注	
	  
		//  daycode 当天订单序号
	  $data['ipaddress'] = $info['ipaddress'];	 
	  $data['is_ping'] = 0;// 是否评价字段 1已评完 0未评	
	  $data['addtime'] = time(); 	  
	  $data['posttime'] = $info['sendtime'];//: 配送时间  
	
		 
		//送达时间
		$sdtime = $info['shopinfo']['arrivetime']*60;  
		//制作时间
		$mztime = $info['shopinfo']['maketime']*60;  
		$ordertime1 = $data['addtime']+$sdtime+$mztime;  
		$ordertime2 = $data['posttime'];  
		//比如，如果客户点击立即下单，现在8.50  那就8.50+制作时间和配送时间。   如果客户现在下单，选择的是10.00-10.30，那就10.00是这个预计送达时间。如果客户选的是9.00-9.30送达，那就当前时间加上制作时间和配送时间。  意思就是说，
		//如果选择配送的时间段大于制作时间+配送时间的话，那就按配送时间段开启的那个时间设为预计送达时间。如果，客户选择的送达时间端，距离当前时间比较近，小于配送时间加制作时间的话，那么就从下单时间+制作时间+配送时间 得到预计送达时间 
		if( $ordertime2 < $ordertime1 ){
			$data['posttime'] = $ordertime1;
		} 
		 
	  
		
	  $data['postdate'] = $info['postdate'];//配送时间段
	  $data['posttime'] = $info['is_ziti'] == 1?strtotime($info['postdate']):$data['posttime'];
	  $data['is_hand'] = $info['is_hand'];//是否是立即送达   1是 0否
	  $data['othertext'] = $info['othercontent'];//其他说明	  
      //如果商家开启自动接单，货到付款的订单直接默认制作，在线支付的订单，支付后才默认制作
	  if($info['shopinfo']['is_autopreceipt'] ==1 && $data['paytype'] == 0 ){
          $data['is_make'] =1;
          $data['maketime'] =time();
      }
	  $data['is_goshop'] = 0;
	  //  :审核时间
	  $data['passtime'] = time();
	  if($data['status']  == 1){
	  	$data['passtime'] == 0;
	  } 
	  $data['buycode'] = substr(md5(time()),9,6);
	  $data['dno'] = time().rand(1000,9999);
	  $minitime = strtotime(date('Y-m-d',time()));
      $tj = $this->ordmysql->select_one("select daycode,id from ".Mysite::$app->config['tablepre']."order where shopid='".$info['shopid']."' and addtime > ".$minitime." order by id desc limit 0,1000");
	  $data['daycode'] = empty($tj)?1:$tj['daycode']+1; 
	  $smardb = new  newsmcart();
      $shopid = $info['shopinfo']['id'];
	  
	  $this->ordmysql->insert(Mysite::$app->config['tablepre'].'order',$data);  //写主订单 
	  $smardb->setdb($this->ordmysql)->SetShopId($shopid)->DelShop();
	  
	  $orderid = $this->ordmysql->insertid();  
	  $sendmsgtops = false; 
	  /* 写订单物流 状态 */
	  /* 第一步 提交成功 */
	  $this->writewuliustatus($orderid,1,$data['paytype']);
	  
	  $this->orderid = $orderid; 
	  foreach($info['goodslist'] as $key=>$value){ 
	    $cmd['order_id'] = $orderid; 
	    $cmd['goodsid'] = isset($value['gg'])?$value['gg']['goodsid']:$value['id'];
	    $cmd['goodsname'] = isset($value['gg'])?$value['name']."【".$value['gg']['attrname']."】":$value['name'];
	    $cmd['goodsname'] = str_replace("\/","",$cmd['goodsname']);
		$cmd['goodscost'] =  $value['is_cx'] == 1?$value['cxinfo']['cxcost']:$value['cost'];
	  	$cmd['goodscount'] = $value['count'];
		$cmd['img'] = $value['img'];
		$cmd['oldcost'] = $value['is_cx'] == 1?$value['cxinfo']['oldcost']:$cmd['goodscost'];
	  	$cmd['shopid'] = $value['shopid'];
	  	$cmd['status'] = 0; 
	  	$cmd['is_send'] = 0;
	    $attr1 = $this->ordmysql->select_one("select goodattr from ".Mysite::$app->config['tablepre']."goods where id='".$value['id']."' ");
		$attr2 = $this->ordmysql->select_one("select goodattrdefault from ".Mysite::$app->config['tablepre']."shop where id='".$value['shopid']."' ");
		$cmd['goodsattr'] = empty($attr1['goodattr'])?$attr2['goodattrdefault']:$attr1['goodattr'];
		$cmd['have_det'] = $value['have_det'];
		$cmd['product_id'] = isset($value['gg'])?$value['gg']['id']:0;
	  	$this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$cmd);
			if(isset($value['gg'])){
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'product','`stock`=`stock`-'.$cmd['goodscount'].' ',"id='".$cmd['product_id']."'"); 
				
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`sellcount`=`sellcount`+'.$cmd['goodscount'].' ',"id='".$cmd['goodsid']."'"); 
                                
                $this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`-'.$cmd['goodscount'].' ',"id='".$cmd['goodsid']."'");
                                
				 
			}else{
					$this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`-'.$cmd['goodscount'].' ,`sellcount`=`sellcount`+'.$cmd['goodscount'],"id='".$cmd['goodsid']."'"); 
		
			}
	  }
	   
	  if(is_array($zpin)&& count($zpin) > 0){
	   
	     foreach($zpin as $key=>$value){
	  	    $datadet['order_id'] = $orderid;
	  	    $datadet['goodsid'] = $key;
	  	    $datadet['goodsname'] = str_replace("\/","",$value['presenttitle']);
	  	    $datadet['goodscost'] = 0;
	  	    $datadet['goodscount'] = 1;
	  	    $datadet['shopid'] = $info['shopid'];
	  	    $datadet['status'] = 0; 
	  	    $datadet['is_send'] = 1;
			$datadet['have_det']=0;
			$datadet['product_id'] =0;
	  	    //更新促销规则中 此赠品的数量 
	  	    $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$datadet);
	  	  	$this->ordmysql->update(Mysite::$app->config['tablepre'].'rule','`controlcontent`=`controlcontent`-1',"id='".$key."'");
	    } 
	  }
	  
	  $checkbuyer = Mysite::$app->config['allowedsendbuyer']; 
	  $checksend = Mysite::$app->config['man_ispass'];
	if($checksend != 1){ 
		if($data['status'] == 1){ 
			$this->sendmess($orderid);
		}
	}
	//微信推送消息
	if($data['paytype']==0){
		if($this->sendWxMsg($orderid,3,1)){
			
		}
		if($this->sendWxMsg($orderid,1,2)){
			
		}
	}
	  if($userid > 0){ 
	     $checkinfo =   $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."'  ");   
	     if(empty($checkinfo) && $data['is_ziti'] != 1){
	        $addata['userid'] = $userid;
	        $addata['username'] = $data['buyername'];
	        $addata['address'] = $data['buyeraddress'];
	        $addata['phone'] = $data['buyerphone'];
	        $addata['contactname'] = $data['buyername'];
	        $addata['default'] = 1; 
	        $this->ordmysql->insert(Mysite::$app->config['tablepre'].'address',$addata);
	     } 
	  }
	  if($sendmsgtops == true){
		   // $psylist =  $this->ordmysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$data['admin_id'].""); 
			// $psCls = new apppsyclass(); 
			// $psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理');
	  }

	  if($data['paytype'] == 0){
		  if($panduan == 0){
			  if($data['is_make'] == 1 && $data['is_ziti'] != 1){
				  $this->writewuliustatus($orderid,4,$data['paytype']);  //订单审核后自动 商家接单
					  if($data['pstype'] == 0){//网站配送自动生成配送费
                            $psdata['orderid'] = $orderid;
                            $psdata['shopid'] = $data['shopid'];
                            $psdata['status'] =0;
                            $psdata['dno'] = $data['dno'];
                            $psdata['addtime'] = time();
                            $psdata['pstime'] = $data['posttime'];
							$admin_id = $orderinfo['admin_id'];
							$psset = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."platpsset  where cityid= '".$admin_id."'   ");
							$checkpsyset = $psset['psycostset'];
							$bilifei =$psset['psybili']*0.01*$orderinfo['allcost'];
							$psdata['psycost'] = $checkpsyset == 1?$psset['psycost']:$bilifei; 
                            $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单
							logwrite("写配送单的订单ID：".$orderid);
 						  $sendmsgtops = true;
					  }elseif($data['pstype'] == 2){
						  $sendmsgtops = false;
						  $psbinterface = new psbinterface();
						  if($psbinterface->psbnoticeorder($orderid)){

						  }
					  }
					  //自动生成配送单结束-------------
				   
			  }
		  }
	  }
	  
  
  }
  /*  
  * $orderid  订单Id
  * $step 订单物流状态 
  *
  *		function writewuliustatus($orderid,$step,$paytype){ }
  *
  *		  1 为订单提交成功 			2 为订单被管理员取消  			 3为在线支付成功    		 	4为商家确认制作  		5为商家取消订单   
  *		  6 配送发货  				7 分配给配送员（配送员已接单）   8配送元已取货   		 		9 已完成（已送达） 	10 用户已确认收货 
  *	      11用户已评价，完成订单   12用户自己取消订单（货到付款）    13用户取消订单，申请退款（在线支付）  
  *		  14 管理员同意退款给用户      15 管理员拒绝退款给用户
  *	  
  * $paytype 支付方式 1 为在线支付 0为货到付款
  *
  */
	function writewuliustatus($orderid,$step,$paytype){
        $orderinfo =   $this->ordmysql->select_one("select shoptype,pttype,shopphone from ".Mysite::$app->config['tablepre']."order where id='".$orderid."'  ");
		 
		$statusdata['orderid']     =  $orderid;
        if($orderinfo['shoptype'] ==100){
            switch ($step){
                case 1 :
                    $statusdata['statustitle'] =  "订单已提交";
                    $statusdata['ststusdesc']  =  "请在15分钟内完成支付，逾期订单将自动取消";
                    break;
                case 2 :

                    break;
                case 3 :
                    $statusdata['statustitle'] =  "在线支付成功";
                    $statusdata['ststusdesc']  =  "下单成功，请等待附近配送员抢单";
                    break;
                case 7 :
                    $statusdata['statustitle'] =  "配送员已接单";
                    $statusdata['ststusdesc']  =  "配送员正赶往商家";
                    break;
                case 8 :
                    if($orderinfo['pttype']==2){
                        $statusdata['statustitle'] =  "配送员已购买";
                        $statusdata['ststusdesc']  =  "正前往收货地，请耐心等待~";
                    }else{
                        $statusdata['statustitle'] =  "配送员已取货";
                        $statusdata['ststusdesc']  =  "正前往收货地，请耐心等待~";
                    }
                    break;            
                case 9 :
                    $statusdata['statustitle'] =  "已完成订单";
                    $statusdata['ststusdesc']  =  "请评价订单";
                    break;
                case 10 :
                    $statusdata['statustitle'] =  "已确认收货";
                    $statusdata['ststusdesc']  =  "请评价订单";
                    break;
                case 11 :
                    $statusdata['statustitle'] =  "已完成订单";
                    $statusdata['ststusdesc']  =  "已评价";
                    break;
                case 12 :
                    $statusdata['statustitle'] =  "已取消订单";
                    $statusdata['ststusdesc']  =  "已取消订单";
                    break;
                case 13 :
                    $statusdata['statustitle'] =  "已申请退款";
                    $statusdata['ststusdesc']  =  "退款申请已提交，等待平台处理";
                    break;
                case 14 :
                    $statusdata['statustitle'] =  "订单关闭";
                    $statusdata['ststusdesc']  =  "平台已退款，退款金额将于1~3个工作日内原路返回";
                    break;
                case 15 :
                    $statusdata['statustitle'] =  "拒绝退款";
                    $statusdata['ststusdesc']  =  "经审核，您的条件不符合退款标准";
                    break; 
				case 16 :
					$statusdata['statustitle'] =  "退款关闭";
					$statusdata['ststusdesc']  =  "您已取消退款申请";
					break;
                default :
                    $this->message('nodefined_func');
                    break;
            }
        }else{
			
			
			 
                switch ($step){
                    case 1 :
                        $statusdata['statustitle'] =  "订单已提交";
                        if($paytype == 1){
                            $statusdata['ststusdesc']  =  "请在15分钟内完成支付，逾期订单将自动取消";
                        }else{
                            $statusdata['ststusdesc']  =  "订单已提交，请等待商家确认";
                        }
                        break;
                    case 2 :

                        break;
                    case 3 :
                        $statusdata['statustitle'] =  "订单已支付";
                        $statusdata['ststusdesc']  =  "订单支付成功，等待商家接单";
                        break;
                    case 4 :
                        $statusdata['statustitle'] =  "商家已接单";
                        $statusdata['ststusdesc']  =  "商家电话：".$orderinfo['shopphone'];
                        break;
                    case 5 :
                        $statusdata['statustitle'] =  "商家拒接单";
                        if($paytype == 0){ 
						    $statusdata['ststusdesc']  =  "很抱歉！商家暂不接单，请到其它店铺下单";
					    }else{
						    $statusdata['ststusdesc']  =  "很抱歉！商家暂不接单，订单金额将于1~3个工作日内原路返回";
					    }
                        break;                  
                    case 6 :
                        $statusdata['statustitle'] =  "商家已发货";
                        $statusdata['ststusdesc']  =  "商家已发货，正在配送中";
                        break;
                    case 7 :
                        $statusdata['statustitle'] =  "配送员已接单";
                        $statusdata['ststusdesc']  =  "配送员正赶往商家";
                        break;
                    case 8 :
                        $statusdata['statustitle'] =  "配送员已取货";
                        $statusdata['ststusdesc']  =  "请耐心等待配送";
                        break;
					case 9 :
						$statusdata['statustitle'] =  "订单已完成";
						$statusdata['ststusdesc']  =  "商品已送达，期待再次光临";
						break;
                    case 10 :
                        $statusdata['statustitle'] =  "订单已完成";
                        $statusdata['ststusdesc']  =  "商品已送达，期待再次光临";
                        break;
                    case 11 :
                        $statusdata['statustitle'] =  "已完成订单";
                        $statusdata['ststusdesc']  =  "已评价";
                        break;
                    case 12 :
                        $statusdata['statustitle'] =  "订单已关闭";
                        $statusdata['ststusdesc']  =  "用户已取消订单";
                        break;
                    case 13 :
                        $statusdata['statustitle'] =  "已申请退款";
                        $statusdata['ststusdesc']  =  "退款申请已提交，待商家处理";
                        break;
                    case 14 :
                        $statusdata['statustitle'] =  "订单关闭";
                        $statusdata['ststusdesc']  =  "商家同意退款，退款金额将于1~3个工作日内原路返回";
                        break;
                    case 15 :
                        $statusdata['statustitle'] =  "商家拒绝退款";
                        $statusdata['ststusdesc']  =  "经审核，您的条件不符合退款标准";
                        break;
					case 16 :
						$statusdata['statustitle'] =  "已申请退款";
						$statusdata['ststusdesc']  =  "退款申请已提交，等待平台处理";
						break;
				    case 17 :
						$statusdata['statustitle'] =  "商家同意退款";
						$statusdata['ststusdesc']  =  "商家已同意退款，请等待平台退还金额";
						break;
					case 18 :
						$statusdata['statustitle'] =  "退款关闭";
						$statusdata['ststusdesc']  =  "您已取消退款申请";
						break;
					case 19 :
						$statusdata['statustitle'] =  "订单关闭";
						$statusdata['ststusdesc']  =  "平台已退款，退款金额将于1~3个工作日内原路返回";		 
						break;
					case 20 :
					$statusdata['statustitle'] =  "商家不制作订单";
					$statusdata['ststusdesc']  =  "商家不制作订单，稍后平台将进行退款处理";		 
					break;
                    default :
                        $this->message('nodefined_func');
                        break;
                }
        }
		$statusdata['addtime'] = time();
		
	    $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderstatus',$statusdata); 
		

	}
//后台 代课下单
 function houtaimakenormal($info){
  	 //需要的公共数据 
  	 //$data['othercontent'] = $info['othercontent'];
  	// $data['cattype'] = $info['cattype'];//表示 是否是订台
     $data['areaids'] = $info['areaids'];
	   $data['admin_id'] = $info['shopinfo']['admin_id']; 
		 $data['shoptype'] = $info['shopinfo']['shoptype'];//: 0:普通订单，1订台订单 
		 //获取店铺商品总价  获取超市商品总价
		 $data['shopcost'] = $info['allcost'];
		 $data['shopps'] = $info['shopps']; 
		 $data['bagcost'] =  $info['bagcost'];
		 $data['ordertype'] = $info['ordertype']; 
		 //支付方式检测
		 $userid = $info['userid'];
		 $data['paytype'] = $info['paytype']; 
		 $data['cxids'] = '';
		 $data['cxcost'] = 0;
		 $zpin = array(); 
		 if($data['shopcost'] > 0){
		    $sellrule =new sellrule(); 
		    $sellrule->setdata($info['shopinfo']['id'],$data['shopcost'],$info['shopinfo']['shoptype']);
		    $ruleinfo = $sellrule->getdata();  
	      $data['shopdowncost'] = $ruleinfo['shopdowncost'];
		  $data['cxcost'] = $ruleinfo['downcost'];
	      $data['cxids'] = $ruleinfo['cxids'];  
	      $zpin = $ruleinfo['zid'];//赠品
	      $data['shopps'] = $ruleinfo['nops'] == true?0:$data['shopps'];
	   }
	  //判断优惠劵
	  $allcost = $data['shopcost'];
	  $data['yhjcost'] = 0;
		$data['yhjids'] = ''; 
		$userid = $info['userid'];
		$juanid = $info['juanid']; 
	   if($juanid > 0 && $userid > 0){
	      $juaninfo = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."juan  where id= '".$juanid."' and uid='".$userid."'  and status = 1 and endtime > ".time()." ");
	   	  if(!empty($juaninfo)){
	   	  	 if($allcost >= $juaninfo['limitcost']){ 
	   	  	 	$data['yhjcost'] =  $juaninfo['cost']; 
	   	  	 	$juandata['status'] = 2;
	   	  	 	$juandata['usetime'] =  time(); 
	   	  	 	 $this->ordmysql->update(Mysite::$app->config['tablepre'].'juan',$juandata,"id='".$juanid."'");
	   	  	 	$data['yhjids'] = $juanid;
	   	  	 } 
	   	  } 
	   } 
	  //积分抵扣
	  $allcost = $allcost - $data['cxcost'] - $data['yhjcost'];
	  $data['scoredown'] = 0;
	  $dikou = $info['dikou'];
	  if(!empty($userid) && $dikou > 0 && Mysite::$app->config['scoretocost'] > 0 && $allcost > $dikou){
	    	 $checkuser= $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid='".$userid."'  "); 
	    	 if(is_array($checkuser)){
	    	     $checkscore = $dikou*(intval(Mysite::$app->config['scoretocost']));
	    	    if($checkuser['score']  >= $checkscore){  
	    	   	  $data['scoredown'] =  $checkscore;
	    	 	    $this->ordmysql->update(Mysite::$app->config['tablepre'].'member','`score`=`score`-'.$checkscore,"uid ='".$userid."' ");	 
	    	    } 
	    	 }
	  }
	  $dikou = $data['scoredown'] > 0?$dikou:0;
	  $allcost = $allcost-$dikou;
	  	  $fupscost = isset($info['addpscost'])?$info['addpscost']:0;
	  $data['allcost'] = $allcost+$data['shopps']+$fupscost+$data['bagcost']; //订单应收费用  
	  $data['shopps'] = $data['shopps']+$fupscost;//增加附件配送费 
		$data['pstype'] = $info['pstype'] ; 
		//检测店铺
	 
	  $data['shopuid'] = $info['shopinfo']['uid'];// 店铺UID	
	  $data['shopid'] =  $info['shopinfo']['id']; //店铺ID	
		$data['shopname'] = $info['shopinfo']['shopname']; //店铺名称	
	  $data['shopphone'] = $info['shopinfo']['phone']; //店铺电话
	  $data['shopaddress'] = $info['shopinfo']['address'];// 店铺地址	
	  $data['buyeraddress'] = $info['addressdet'];
	  $data['ordertype'] = $info['ordertype'];//来源方式;
	  $data['buyeruid'] = $userid;// 购买用户ID，0未注册用户	
		$data['buyername'] =  $info['username'];//购买热名称
		$data['buyerphone'] = $info['mobile'];// 联系电话   
		$panduan = 0;
 		 
	  $data['paystatus'] = 1;// 默认1已支付	
	  $data['paytype_name'] = 'open_acout';// 默认未余额支付
		$data['content'] = $info['remark'];// 订单备注	
	 
		//  daycode 当天订单序号
	  $data['ipaddress'] = $info['ipaddress'];	 
		$data['is_ping'] = 0;// 是否评价字段 1已评完 0未评	
		$data['addtime'] = time(); 	  
	    $data['posttime'] = $info['sendtime'];//: 配送时间  
		
	 
		//送达时间
		$sdtime = $info['shopinfo']['arrivetime']*60;   
		//制作时间
		$mztime = $info['shopinfo']['maketime']*60; 	 
		$ordertime1 = $data['addtime']+$sdtime+$mztime; 	 
		$ordertime2 = $data['posttime']; 	 
		//比如，如果客户点击立即下单，现在8.50  那就8.50+制作时间和配送时间。   如果客户现在下单，选择的是10.00-10.30，那就10.00是这个预计送达时间。如果客户选的是9.00-9.30送达，那就当前时间加上制作时间和配送时间。  意思就是说，
		//如果选择配送的时间段大于制作时间+配送时间的话，那就按配送时间段开启的那个时间设为预计送达时间。如果，客户选择的送达时间端，距离当前时间比较近，小于配送时间加制作时间的话，那么就从下单时间+制作时间+配送时间 得到预计送达时间 
		if( $ordertime2 < $ordertime1 ){
			$data['posttime'] = $ordertime1;
		} 
	 
		
	   $data['postdate'] = $info['postdate'];//配送时间段
	  $data['othertext'] = $info['othercontent'];//其他说明 	  
      if($info['shopinfo']['is_autopreceipt'] == 1){
         $data['is_make'] =1;
         $data['maketime'] =time();
     }

	  //  :审核时间
	  $data['passtime'] = time();
	  $data['status'] = 1;
	  if($data['status']  == 1){
	  	$data['passtime'] == 0;
	  } 
	  $data['buycode'] = substr(md5(time()),9,6);
	  $data['dno'] = time().rand(1000,9999);
	  $minitime = strtotime(date('Y-m-d',time()));
      $tj = $this->ordmysql->select_one("select daycode,id from ".Mysite::$app->config['tablepre']."order where shopid='".$info['shopid']."' and addtime > ".$minitime." order by id desc limit 0,1000"); 
	  $data['daycode'] = empty($tj)?1:$tj['daycode']+1; 
	  $data['status'] = 1;
	  $this->ordmysql->insert(Mysite::$app->config['tablepre'].'order',$data);  //写主订单 
	  
	  $orderid = $this->ordmysql->insertid(); 
	  
	  $sendmsgtops = false;
	  
	  
	  /* 写订单物流 状态 */
	  /* 第一步 提交成功 */
	  $this->writewuliustatus($orderid,1,$data['paytype']);
	  if($data['paytype'] == 0){
		    if($panduan == 0){
			  if($data['is_make'] == 1){
				  $this->writewuliustatus($orderid,4,$data['paytype']);  //订单审核后自动 商家接单
				  $auto_send = Mysite::$app->config['auto_send'];
				  if($auto_send == 1){
					 $this->writewuliustatus($orderid,6,$data['paytype']);//订单审核后自动 商家接单后自动发货
					  $orderdatac['sendtime'] = time(); 
					  $this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$orderdatac,"id ='".$orderid."' ");
				  }else{
					//自动生成配送单------|||||||||||||||-----------------------
					  if($data['pstype'] == 0){//网站配送自动生成配送费
					 
						  $psdata['orderid'] = $orderid;
						  $psdata['shopid'] = $data['shopid'];
						  $psdata['status'] =0;
						  $psdata['dno'] = $data['dno'];
						  $psdata['addtime'] = time();
						  $psdata['pstime'] = $data['posttime']; 
						  $checkpsyset = Mysite::$app->config['psycostset'];
						  $bilifei =Mysite::$app->config['psybili']*0.01*$data['allcost'];
						  $psdata['psycost'] = $checkpsyset == 1?Mysite::$app->config['psycost']:$bilifei; 
						  $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderps',$psdata);  //写配送订单 
					 
						 $sendmsgtops = true;
					  }elseif($orderinfo['pstype'] == 2 && $orderinfo['is_ziti'] == 0){
							$sendmsgtops = false;
							$psbinterface = new psbinterface();
							if($psbinterface->psbnoticeorder($orderid)){
								
							}
					  }
					  //自动生成配送单结束------------- 
				  }
			  }
			}
	  }
	  
	  $this->orderid = $orderid; 
	  foreach($info['goodslist'] as $key=>$value){ 
	    $cmd['order_id'] = $orderid; 
	    $cmd['goodsid'] = $value['id'];
	    $cmd['goodsname'] = $value['name'];
	    $cmd['goodscost'] = $value['cost'];
	  	$cmd['goodscount'] = $value['count'];
	  	$cmd['shopid'] = $value['shopid'];
	  	$cmd['status'] = 0; 
	  	$cmd['is_send'] = 0;
		$attr1 = $this->ordmysql->select_one("select goodattr from ".Mysite::$app->config['tablepre']."goods where id='".$value['id']."' ");
		$attr2 = $this->ordmysql->select_one("select goodattrdefault from ".Mysite::$app->config['tablepre']."shop where id='".$value['shopid']."' ");
		$cmd['goodsattr'] = empty($attr1['goodattr'])?$attr2['goodattrdefault']:$attr1['goodattr'];
	  	$this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$cmd);
	    $this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`-'.$cmd['goodscount'].' ,`sellcount`=`sellcount`+'.$cmd['goodscount'],"id='".$cmd['goodsid']."'"); 
	  }
	   
	  if(is_array($zpin)&& count($zpin) > 0){
	   
	     foreach($zpin as $key=>$value){
	  		  $datadet['order_id'] = $orderid;
	  	    $datadet['goodsid'] = $key;
	  	    $datadet['goodsname'] = $value['presenttitle'];
	  	    $datadet['goodscost'] = 0;
	  	    $datadet['goodscount'] = 1;
	  	    $datadet['shopid'] = $info['shopid'];
	  	    $datadet['status'] = 0; 
	  	    $datadet['is_send'] = 1;
	  	    //更新促销规则中 此赠品的数量 
	  	    $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$datadet);
	  	  	$this->ordmysql->update(Mysite::$app->config['tablepre'].'rule','`controlcontent`=`controlcontent`-1',"id='".$key."'");
	    } 
	  }
	  
	  $checkbuyer = Mysite::$app->config['allowedsendbuyer']; 
	  $checksend = Mysite::$app->config['man_ispass'];
	 	if($checksend != 1)
	 	{ 
	 		 if($data['status'] == 1){ 
          $this->sendmess($orderid);
       }
	  }
	  if($userid > 0){ 
	     $checkinfo =   $this->ordmysql->select_one("select * from ".Mysite::$app->config['tablepre']."address where userid='".$userid."'  ");   
	     if(empty($checkinfo)){
	        $addata['userid'] = $userid;
	        $addata['username'] = $data['buyername'];
	        $addata['address'] = $data['buyeraddress'];
	        $addata['phone'] = $data['buyerphone'];
	        $addata['contactname'] = $data['buyername'];
	        $addata['default'] = 1; 
	        $this->ordmysql->insert(Mysite::$app->config['tablepre'].'address',$addata);
	     } 
	  }
		if($sendmsgtops == true){ 
			$psylist =  $this->ordmysql->getarr("select a.*  from ".Mysite::$app->config['tablepre']."apploginps as a left join ".Mysite::$app->config['tablepre']."member as b on a.uid = b.uid where b.admin_id = ".$data['admin_id'].""); 
			$psCls = new apppsyclass(); 
			$psCls->SetUserlist($psylist)->sendNewmsg('订单提醒','有新订单可以处理'); 
		}
  
  }
	
	
  //预订订单
  function orderyuding($info){
  	 //$data['subtype'] = $info['subtype'];
  	 $data['is_goshop'] = $info['is_goshop'];
		 $data['areaids'] = $info['areaids'];
		 $data['admin_id'] = $info['shopinfo']['admin_id'];
		 $data['shopcost'] = $info['allcost'];//:店铺商品总价
		 $data['shopps'] = 0;//店铺配送费  
		 $data['shoptype'] = 0;//: 0:普通订单，1订台订单
		 $data['bagcost']=0;//:打包费 
		 //获取店铺商品总价  获取超市商品总价 
		 $data['paytype'] = $info['paytype'];
		 $data['cxids'] = '';
	   $data['cxcost'] = 0;
		 $data['yhjcost'] = 0;
	  	$data['yhjids'] = '';  
	  $data['scoredown'] = 0;
	  $data['allcost'] =$info['allcost']; //订单应收费用 
	  $data['shopuid'] =$info['shopinfo']['uid'];// 店铺UID	
		$data['shopid'] =  $info['shopinfo']['id']; //店铺ID	
		$data['shopname'] =$info['shopinfo']['shopname']; //店铺名称	
		$data['shopphone'] =  $info['shopinfo']['phone']; //店铺电话
		$data['shopaddress'] =$info['shopinfo']['address'];// 店铺地址	
		$data['pstype'] = $info['pstype'] ;
		$data['shoptype'] = 0; 
	  $data['buyeraddress'] = '';
	  $data['ordertype'] = $info['ordertype'];//来源方式;
	  $data['buyeruid'] = $info['userid'];// 购买用户ID，0未注册用户	
		$data['buyername'] =  $info['username'];//购买热名称
		$data['buyerphone'] = $info['mobile'];// 联系电话    
	  $data['paystatus'] = 0;// 支付状态1已支付	
		$data['content'] = $info['remark'];// 订单备注	 
		//  daycode 当天订单序号
	  $data['ipaddress'] = $info['ipaddress'];	 
		$data['is_ping'] = 0;// 是否评价字段 1已评完 0未评	
		$data['addtime'] = time(); 	  
	 
	    $data['posttime'] = $info['sendtime'];//: 配送时间  
		
			
		 
		//送达时间
		$sdtime = $info['shopinfo']['arrivetime']*60;   
		//制作时间
		$mztime = $info['shopinfo']['maketime']*60;  
		$ordertime1 = $data['addtime']+$sdtime+$mztime; 	 
		$ordertime2 = $data['posttime']; 	 
		//比如，如果客户点击立即下单，现在8.50  那就8.50+制作时间和配送时间。   如果客户现在下单，选择的是10.00-10.30，那就10.00是这个预计送达时间。如果客户选的是9.00-9.30送达，那就当前时间加上制作时间和配送时间。  意思就是说，
		//如果选择配送的时间段大于制作时间+配送时间的话，那就按配送时间段开启的那个时间设为预计送达时间。如果，客户选择的送达时间端，距离当前时间比较近，小于配送时间加制作时间的话，那么就从下单时间+制作时间+配送时间 得到预计送达时间 
		if( $ordertime2 < $ordertime1 ){
			$data['posttime'] = $ordertime1;
		} 
	 
		
		
		
	   $data['postdate'] = $info['postdate'];//配送时间段
	  $data['othertext'] = $info['othercontent'];//其他说明 	 
	  //  :审核时间
	  $data['passtime'] = time(); 
	  $data['buycode'] = substr(md5(time()),9,6);
	  $data['dno'] = time().rand(1000,9999);
	  $minitime = strtotime(date('Y-m-d',time()));
	   $zpin = array();
		 if($info['subtype'] == 1){
		  // $this->message('订桌位');
		   //
		  
		   
		 }elseif($info['subtype'] == 2){  
	 	     $data['shopcost'] = $data['shopcost']; 
	 	     $data['cxids'] = '';
		     $data['cxcost'] = 0; 
		     $cattype = $info['cattype'];
		     if($data['shopcost'] > 0){
		         $sellrule =new sellrule(); 
		         $sellrule->setdata($info['shopid'],$data['shopcost'],$info['shopinfo']['shoptype']);
		         $ruleinfo = $sellrule->getdata();  
				  $data['shopdowncost'] = $ruleinfo['shopdowncost'];
	           $data['cxcost'] = $ruleinfo['downcost'];
	           $data['cxids'] = $ruleinfo['cxids'];  
	           $zpin = $ruleinfo['zid'];//赠品 
	       }
	       $data['allcost'] =   $data['shopcost'] - $data['cxcost'];  
		 }
		 $panduan = Mysite::$app->config['man_ispass'];
		 $data['status'] = 0;
		 if($panduan != 1 && $data['paytype'] == 0){
			  $data['status'] = 1;
		 }

      if(Mysite::$app->config['allowed_is_make'] == 0){
          $data['is_make'] =1;
          $data['maketime'] =time();
      }

		#$data['is_make'] = Mysite::$app->config['allowed_is_make'] == 1?0:1;
	  $minitime = strtotime(date('Y-m-d',time()));
    $tj = $this->ordmysql->select_one("select count(id) as shuliang from ".Mysite::$app->config['tablepre']."order where shopid='".$info['shopid']."' and addtime > ".$minitime."  limit 0,1000");
	  $data['daycode'] = $tj['shuliang']+1; 
	  $this->ordmysql->insert(Mysite::$app->config['tablepre'].'order',$data);  //写主订单 
	  $orderid = $this->ordmysql->insertid(); 
	  
	  $this->orderid = $orderid; 
	  
	  
	    
	  /* 写订单物流 状态 */
	  /* 第一步 提交成功 */
	  $this->writewuliustatus($orderid,1,$data['paytype']);
	  if($data['paytype'] == 0){
		    if($panduan == 0){
			  if($data['is_make'] == 1){
				  $this->writewuliustatus($orderid,4,$data['paytype']);  //订单审核后自动 商家接单
				  $auto_send = Mysite::$app->config['auto_send'];
				  if($auto_send == 1){
					 $orderdatac['sendtime'] = time();
				     $this->ordmysql->update(Mysite::$app->config['tablepre'].'order',$orderdatac,"id ='".$orderid."' ");
					 $this->writewuliustatus($orderid,6,$data['paytype']);//订单审核后自动 商家接单后自动发货
					 
				  }
			  }
			}
	  }
	  
	  
	  
	  
	  
	  if($info['subtype'] == 2){
	   foreach($info['goodslist'] as $key=>$value){ 
	    $cmd['order_id'] = $orderid; 
	    $cmd['goodsid'] = isset($value['gg'])?$value['gg']['goodsid']:$value['id'];
	    $cmd['goodsname'] = isset($value['gg'])?$value['name']."【".$value['gg']['attrname']."】":$value['name'];
	    $cmd['goodscost'] = isset($value['gg'])?$value['gg']['cost']:$value['cost'];
	  	$cmd['goodscount'] = $value['count'];
	  	$cmd['shopid'] = $value['shopid'];
	  	$cmd['status'] = 0; 
	  	$cmd['is_send'] = 0;
		$cmd['have_det'] = $value['have_det'];
		$cmd['product_id'] = isset($value['gg'])?$value['gg']['id']:0;
	  	$this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$cmd);
			if(isset($value['gg'])){
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'product','`stock`=`stock`-'.$cmd['goodscount'].' ',"id='".$cmd['product_id']."'"); 
				
				$this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`sellcount`=`sellcount`+'.$cmd['goodscount'].' ',"id='".$cmd['goodsid']."'"); 
				 
			}else{
					$this->ordmysql->update(Mysite::$app->config['tablepre'].'goods','`count`=`count`-'.$cmd['goodscount'].' ,`sellcount`=`sellcount`+'.$cmd['goodscount'],"id='".$cmd['goodsid']."'"); 
		
			}
	  }
	    if(is_array($zpin)&& count($zpin) > 0){
	   
	      foreach($zpin as $key=>$value){
	  		  $datadet['order_id'] = $orderid;
	  	    $datadet['goodsid'] = $key;
	  	    $datadet['goodsname'] = $value['presenttitle'];
	  	    $datadet['goodscost'] = 0;
	  	    $datadet['goodscount'] = 1;
	  	    $datadet['shopid'] = $info['shopid'];
	  	    $datadet['status'] = 0; 
	  	    $datadet['is_send'] = 1;
			$datadet['have_det'] = 0;
			$datadet['product_id'] = 0;
	  	    //更新促销规则中 此赠品的数量 
	  	    $this->ordmysql->insert(Mysite::$app->config['tablepre'].'orderdet',$datadet);
	  	  	$this->ordmysql->update(Mysite::$app->config['tablepre'].'rule','`controlcontent`=`controlcontent`-1',"id='".$key."'");
	      } 
	    } 
	  }
	  
	  $checkbuyer = Mysite::$app->config['allowedsendbuyer']; 
	  $checksend = Mysite::$app->config['man_ispass'];
	 	if($checksend != 1)
	 	{ 
	 		 if($data['status'] == 1){ 
          $this->sendmess($orderid);
       }
	 }	
  }
	function sendWxMsg($orderid,$type,$parent_type){
		$parenttype = $this->ordmysql->select_one("select is_open from ".Mysite::$app->config['tablepre']."wxnotice  where type= '".$parent_type."' and parent_type =0 ");
		if($parenttype['is_open']==1){
			$typeinfo = $this->ordmysql->select_one("select is_open from ".Mysite::$app->config['tablepre']."wxnotice  where type= '".$type."' and parent_type ='".$parent_type."' ");
			if($typeinfo['is_open']==1){
				$order = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."' ");
				if(!empty($order['buyeruid'])){
					if($parent_type!=2){
						$wxbuyer = $this->ordmysql->select_one("select *  from ".Mysite::$app->config['tablepre']."wxuser  where uid= '".$order['buyeruid']."'   ");
					}else{
						$wxbuyer = $this->ordmysql->select_one("select openid  from ".Mysite::$app->config['tablepre']."wxuser  where uid= '".$order['shopuid']."'   ");
						if(empty($wxbuyer['openid'])){
							$shop = $this->ordmysql->select_one("select wxopenid  from ".Mysite::$app->config['tablepre']."shop  where id= '".$order['shopid']."'   ");
							$wxbuyer['openid'] = $shop['wxopenid'];
						}
					}
					//$wxbuyer['openid'] = 'oogjk1CRsbLNZQLyF0Qve3VxvthI';
					#print_r($wxbuyer['openid']);
					if(!empty($wxbuyer['openid'])){ 
						$wx_s = new wx_s();
						if($wx_s->send_tem_msg($order['id'],$wxbuyer['openid'],$type,$parent_type)){
							return true;
						}else{
							logwrite('微信推送'.$parent_type.'-'.$type.'错误:'.$wx_s->err());
							return true;							
						}
					}else{
						logwrite('微信openid为空');
						return true;	
					}
				}else{
					logwrite('订单用户信息为空');
					return true;	
				}
			}else{
				logwrite('该模板未启用');
				return true;				
			}
		}else{
			logwrite('该类型模板未启用');
			return true;
		}
		return true;
	}
}
?>