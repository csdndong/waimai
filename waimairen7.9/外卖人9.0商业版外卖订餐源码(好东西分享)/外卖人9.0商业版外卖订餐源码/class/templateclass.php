<?php 

/**
 * @class templateclass 
 * @描述   模板消息
 1、
有未支付订单
您在<商家名称>下的外卖订单还未付款，15分钟未付款订单将自动取消，请及时付款哦~
订单编号:1104512547214560
订单状态：待付款
订单金额：¥20.5
支付状态：未支付

2、
订单支付成功
您在<商家>下的外卖订单已支付成功，商家接单后会尽快发货
订单编号:1104512547214560
订单状态:待接单
订单金额:¥20.5 
支付状态:已支付
 
3、
货到付款下单成功
您在<商家名称>下的外卖订单已下单成功，您选择的是货到付款，请自备零钱哦~ 
订单编号：1104512547214560 
订单状态：待接单
订单金额：¥20.5 
支付状态：已支付

4、
商家已接单
您在<商家名称>下的外卖订单，商已接单正在为您备货
订单编号：1104512547214560
订单状态：待发货
订单金额：¥20.5 
支付状态：已支付

5、
商品正在配送中
您在<商家名称>下的外卖订单，商家已发货，联系电话<商家电话> 
订单编号：1104512547214560
订单状态：配送中
订单金额：¥20.5 
支付状态：已支付
 
6、
商品正在配送中
您在<商家名称>下的外卖订单，商家已发货，配送员电话<配送员电话> 
订单编号：1104512547214560
订单状态：配送中
订单金额：¥20.5 
支付状态：已支付
 
7、
商品已成功送达
您在<商家名称>下的外卖订单，在<送达时间>已成功送达，欢迎再次下单~ 
订单编号：1104512547214560
订单状态：已送达
订单金额：¥20.5 
支付状态：已支付
 
8、
订单已被取消
您在<商家名称>下的外卖订单，已经被商家取消，如有疑问，请联系商家<商家电话> 
订单编号：1104512547214560
订单状态：已取消
订单金额：¥20.5 
支付状态：已支付
 
9、
订单已取消
您在<商家>下的外卖订单，没有及时付款，系统已自动取消，欢迎再次下单~ 
订单编号：1104512547214560
订单状态：已取消 
订单金额：¥20.5 
支付状态：已支付

10、
有未确认收货订单
您在<商家名称>下的外卖订单，还未确认收货，请到订单详情页面<确认收货> 
订单编号：1104512547214560
订单状态：待确认收货
订单金额：¥20.5 
支付状态：已支付

11、
退款审核通过
您在<商家名称>下的外卖订单，退款申请已经通过审核，我们会尽快为您退款
退款金额：¥20.5 
退款编号：1104512547214560 
申请时间：2017-06-06 12:00
12、
退款申请失败
很抱歉！您在<商家名称>下的外卖订单，退款申请已被拒绝，拒绝理由：<拒绝理由> 
退款金额：¥20.5 
退款编号：1104512547214560 
申请时间：2017-06-06 12:00


13、
有新的外卖订单
您有新的外卖订单，请及时处理！
商家：外卖人
收货人：李小雨
地址：河南电子产业园6号楼 
电话：18712355623
总价：¥20.5 
预计收入：¥18.5 
支付方式：在线支付
期望时间：立即送达
14、
商品已成功送达
您的<订单序号>外卖订单，已经成功送达。配送员：<配送员><联系电话> 
商家：外卖人
收货人：李小雨
地址：河南电子产业园6号楼 
电话：18712355623
总价：¥20.5
本单收入：¥18.5 
支付方式：在线支付
期望时间：立即送达
15、
订单退款申请
您的顾客发起了退款，请及时登录系统后台处理！
退款金额：¥20.5 
退款编号：1104512547214560 
申请时间：2017-06-06 12:00



16、
有未付款订单
您好，您下的<跑腿类型>订单还未付款哦，请尽快付款~ 
订单编号：1104512547214560
订单状态：待支付
订单金额：¥20.5 

17、
订单支付成功
您下的<跑腿类型>订单已经支付成功，等待配送员接单~ 
订单编号：1104512547214560
订单状态：待接单
订单金额：¥20.5
  
18、
配送员已接单
您下的<跑腿类型>订单，配送员已接单，联系电话<配送员电话> 
订单编号：1104512547214560
订单状态：已接单
订单金额：¥20.5 

19、
配送员已取货
您下的<跑腿类型>订单，配送员已取货，正在配送中，联系电话<配送员电话> 
订单编号：1104512547214560
订单状态：配送中
订单金额：¥20.5 

20、
配送员已购买
您下的<跑腿类型>订单，配送员已购买，正在配送中，联系电话<配送员电话> 
订单编号：1104512547214560
订单状态：配送中
订单金额：¥20.5 

21、
商品已送达
您好，您下的<跑腿类型>订单，已成功送达，订单完成，欢迎再次下单~ 
订单编号：1104512547214560
订单状态：已完成
订单金额：¥20.5 
 

 
 */
class templateclass{ 
	//模板消息
	//初始化函数
    function __construct()
    {
        $this->siteurl = Mysite::$app->config['siteurl'];
		$this->mysql =new mysql_class(); 
    }
	function get_template($orderid,$useropenid,$type,$parent_type){
		$order = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."order  where id= '".$orderid."'   ");
		//支付方式
		if($order['paytype']==1){
			$paytype = '在线支付';
		}else{
			$paytype = '货到支付';
		}
		//退款内容
		
		$draw = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."drawbacklog  where orderid= '".$orderid."' order by addtime desc");
		if(!empty($draw)){
			if($draw['status']==0){
				$b = '退款中';
			}else if($draw['status']==3){
				$b = '退款失败';
			}else if($draw['status']==2){
				$b = '商家同意退款';
			}else if($draw['status']==1){
				$b = '退款结束';
			}else if($draw['status']==4){
				$b = '平台退款';
			}	
			$drawtime = date('Y-m-d H:i',$draw['addtime']);
		}
		$template_type = $parent_type;
		if($parent_type==3){
			$template_type = 1;
		}
		$template = $this->mysql->select_one("select template_id  from ".Mysite::$app->config['tablepre']."wxnotice  where type= '".$template_type."' and parent_type=0  ");
		$firstcolor = '#FF0000';
		if($parent_type==1){
			if($order['status'] == 0) $ordstatus = '待处理';
			if($order['status'] == 1) $ordstatus = '待发货';
			if($order['status'] == 2) $ordstatus = '已发货';
			if($order['status'] > 3) $ordstatus = '已取消';	
			if($order['is_make'] == 0) $ordstatus = '待商家制作';		
			if($order['is_ziti'] == 1){
				$shop = $this->mysql->select_one("select ziti_time  from ".Mysite::$app->config['tablepre']."shop  where id= '".$order['shopid']."'  ");
				if($order['is_make'] == 1) $ordstatus = '商家已接单';		
				if($order['posttime'] - time() <= $shop['ziti_time']*60 )$ordstatus = '待用户自取';		
			}
			if($order['is_reback'] == 1) $ordstatus = '退款中 待平台处理';
			if($order['is_reback'] == 2) $ordstatus = '退款成功';
			if($order['is_reback'] == 4) $ordstatus = '退款中 待商家处理';
			if($order['status'] == 3) $ordstatus = '已完成';
			switch($type){
				/* case 1:
					$first = array(
						'value'=>urlencode('有未支付订单\n您在<'.$order['shopname'].'>下的外卖订单还未付款，15分钟未付款订单将自动取消，请及时付款哦~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break; */
				case 2:
					$first = array(
						'value'=>urlencode('订单支付成功\n您在<'.$order['shopname'].'>下的外卖订单已支付成功，商家接单后会尽快发货'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 3:
					$first = array(
						'value'=>urlencode('货到付款下单成功\n您在<'.$order['shopname'].'>下的外卖订单已下单成功，您选择的是货到付款，请自备零钱哦~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 4:
					$first = array(
						'value'=>urlencode('商家已接单\n您在<'.$order['shopname'].'>下的外卖订单，商家已接单正在为您备货'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 5:
					$first = array(
						'value'=>urlencode('商品正在配送中\n您在<'.$order['shopname'].'>下的外卖订单，商家已发货，联系电话'.$order['shopphone']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 6:
					$first = array(
						'value'=>urlencode('商品正在配送中\n您在<'.$order['shopname'].'>下的外卖订单，商家已发货，配送员电话'.$order['psemail']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;	
				case 7:
					$first = array(
						'value'=>urlencode('商品已成功送达\n您在<'.$order['shopname'].'>下的外卖订单，在'.date('Y-m-d H:i',$order['suretime']).'已成功送达，欢迎再次下单~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;	
				case 8:
					$first = array(
						'value'=>urlencode('订单已被取消\n您在<'.$order['shopname'].'>下的外卖订单，已经被商家取消，如有疑问，请联系商家'.$order['shopphone']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;	
				/* case 9:
					$first = array(
						'value'=>urlencode('订单已取消\n您在<'.$order['shopname'].'>下的外卖订单，没有及时付款，系统已自动取消，欢迎再次下单~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode('已取消'),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break; */	
				case 10:
					$first = array(
						'value'=>urlencode('有未确认收货订单\n您在<'.$order['shopname'].'>下的外卖订单，还未确认收货，请到订单详情页面确认收货'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;	
				case 11:
					$first = array(
						'value'=>urlencode('退款审核通过\n您在<'.$order['shopname'].'>下的外卖订单，退款申请已经通过审核，我们会尽快为您退款'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost'].'\n退款金额：¥'.$draw['cost'].'\n退款状态：'.$b.'\n申请时间：'.$drawtime),'color' =>'#333333');
					break;	
				case 12:
					$first = array(
						'value'=>urlencode('退款申请失败\n您在<'.$order['shopname'].'>下的外卖订单，退款申请已被拒绝，拒绝理由:'.$draw['reason']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost'].'\n退款金额：¥'.$draw['cost'].'\n退款状态：'.$b.'\n申请时间：'.$drawtime),'color' =>'#333333');
					break;			
				default:
					return 'undefined_type';
                        break;
			};
			$OrderSn = array('value' =>urlencode($order['dno']),'color' =>'#333333');
			$template = array(
				"touser" => $useropenid,
				"template_id" => $template['template_id'],
				'url' => $this->siteurl.'/index.php?ctrl=wxsite&action=ordershow&orderid='.$orderid,
				'topcolor' => '#000',
				'data' => array('first'=>$first,
								'OrderSn' =>$OrderSn,						
								'OrderStatus' =>$OrderStatus,							
								'remark' => $remark,
								)
			);
		}else if($parent_type==2){
			//订单预计收入
			$jsinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."jscompute where id > 0  ");
			$ptyj = $jsinfo[0];   //平台配送情况下的佣金设置
			$sjyj = $jsinfo[1];   //商家配送情况下的佣金设置
			$ptjs = $jsinfo[2];   //平台配送情况下的结算设置
			$sjjs = $jsinfo[3];   //商家配送情况下的结算设置
			$shop =   $this->mysql->select_one("select id,yjin,shoptype,zitiyjb,zitilimityj,zitianyj from ".Mysite::$app->config['tablepre']."shop where id='".$order['shopid']."' ");
			if($shop['shoptype'] == 0){
				$sendinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopfast where shopid='".$order['shopid']."' ");	
			}else{
				$sendinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopmarket where shopid='".$order['shopid']."' ");				
			}
			if($sendinfo['sendtype'] == 1){				
				//计算商家配送情况下佣金
				$sjyjnum = $order['shopcost'];
				if($sjyj['pscost'] == 1){
					$sjyjnum = 	$sjyjnum + $order['shopps'];
				}
				if($sjyj['bagcost'] == 1){
					$sjyjnum = 	$sjyjnum + $order['bagcost'];
				}
				if($sjyj['shopdowncost'] == 1){
					$sjyjnum = 	$sjyjnum - ($order['cxcost'] - $order['shopdowncost']);
				}
				$order['servicecost'] = $sjyjnum * $shop['yjin'] * 0.01;//平台服务费（佣金）
				if($order['is_ziti'] == 1){
					$ztyj = $sjyjnum * $shop['zitiyjb'] * 0.01;
					$ztyj = $ztyj < $shop['zitilimityj']?$shop['zitianyj']:$ztyj;
					$order['servicecost'] = $ztyj;
				}
				$order['servicecost'] = round($order['servicecost'] ,2);
				//计算商家配送情况下本单预计收入（结算金额）
				$sjjsnum = $order['shopcost'];
				if($sjjs['pscost'] == 1){
					$sjjsnum = 	$sjjsnum + $order['shopps'];
				}
				if($sjjs['bagcost'] == 1){
					$sjjsnum = 	$sjjsnum + $order['bagcost'];
				}
				if($sjjs['shopdowncost'] == 1){
					$sjjsnum = 	$sjjsnum - ($order['cxcost'] - $order['shopdowncost']);
				}			
				$order['expectincome'] = $sjjsnum - $order['servicecost'];//本单预计收入（结算金额）		
				$order['expectincome'] = number_format($order['expectincome'] ,2);
			}else{
				 
				//计算平台配送情况下佣金
				$ptyjnum = $order['shopcost'];          			
				if($ptyj['pscost'] == 1){
					$ptyjnum = 	$ptyjnum + $order['shopps'];
				}
				if($ptyj['bagcost'] == 1){
					$ptyjnum = 	$ptyjnum + $order['bagcost'];
				}
				if($ptyj['shopdowncost'] == 1){
					$ptyjnum = 	$ptyjnum - ($order['cxcost'] - $order['shopdowncost']);
				}
				$order['servicecost'] = $ptyjnum * $shop['yjin'] * 0.01;//平台服务费（佣金）
				if($order['is_ziti'] == 1){
					$ztyj = $sjyjnum * $shop['zitiyjb'] * 0.01;
					$ztyj = $ztyj < $shop['zitilimityj']?$shop['zitianyj']:$ztyj;
					$order['servicecost'] = $ztyj;
				}
				$order['servicecost'] = round($order['servicecost'] ,2);
				//计算平台配送情况下本单预计收入（结算金额）
				$ptjsnum = $order['shopcost'];
				if($ptjs['pscost'] == 1){
					$ptjsnum = 	$ptjsnum + $order['shopps'];
				}
				if($ptjs['bagcost'] == 1){
					$ptjsnum = 	$ptjsnum + $order['bagcost'];
				}
				if($ptjs['shopdowncost'] == 1){
					$ptjsnum = 	$ptjsnum - ($order['cxcost'] - $order['shopdowncost']);
				}			
				$order['expectincome'] = $ptjsnum - $order['servicecost'];//本单预计收入（结算金额）
				$order['expectincome'] = number_format($order['expectincome'] ,2);			
			}
			#print_r($order);exit;
			if(!empty($order['psusername']) &&(!empty($order['psemail']))){
				$psy = '配送员：'.$order['psusername'].' '.$order['psemail'];
			}else{
				$psy = '';
			}
			$keyword1 = array('value' =>urlencode($order['shopname']),'color' =>'#333333');
			$keyword2 = array('value' =>urlencode($order['buyername']),'color' =>'#333333');
			$keyword3 = array('value' =>urlencode($order['buyeraddress']),'color' =>'#333333');
			$keyword4 = array('value' =>urlencode($order['buyerphone']),'color' =>'#333333');
			$keyword5 = array('value' =>urlencode('¥'.$order['allcost']),'color' =>'#333333');
			switch($type){
				case 1:
					$first = array(
						'value'=>urlencode('有新的外卖订单\n您有新的外卖订单，请及时处理！\n'),
						'color'=>$firstcolor,
					);
					$remark = array('value' =>urlencode('预计收入：¥'.$order['expectincome'].'\n支付方式：'.$paytype.'\n期望时间：'.$order['postdate']),'color' =>'#333333');
					break;
				case 2:
					$first = array(
						'value'=>urlencode('商品已成功送达\n您的#'.$order['daycode'].'外卖订单，已经成功送达。'.$psy.'\n'),
						'color'=>$firstcolor,
					);
					$remark = array('value' =>urlencode('本单收入：¥'.$order['expectincome'].'\n支付方式：'.$paytype.'\n期望时间：'.$order['postdate']),'color' =>'#333333');
					break;
				case 3:
					$first = array(
						'value'=>urlencode('订单退款申请\n您的顾客发起了退款，请及时登录系统处理！\n'),
						'color'=>$firstcolor,
					);
					$remark = array('value' =>urlencode('本单收入：¥'.$order['expectincome'].'\n支付方式：'.$paytype.'\n期望时间：'.$order['postdate'].'\n退款金额：¥'.$draw['cost'].'\n退款状态：待商家处理\n申请时间：'.$drawtime),'color' =>'#333333');
					break;		
				default:
					return 'undefined_type';
                        break;
			};
			$template = array(
				"touser" => $useropenid,
				"template_id" => $template['template_id'],
				'url' => '',
				'topcolor' => '#000',
				'data' => array('first'=>$first,
								'keyword1' =>$keyword1,						
								'keyword2' =>$keyword2,
								'keyword3' =>$keyword3,						
								'keyword4' =>$keyword4,
								'keyword5' =>$keyword5,						
								'remark' => $remark,
								)
			);
		}else if($parent_type==3){
			if($order['status'] == 0) $ordstatus = '待处理订单';
			if($order['status'] == 1 && $order['is_reback'] ==0) $ordstatus = '待发货';
			if($order['status'] == 1 && $order['is_reback'] ==1) $ordstatus = '退款中';
			if($order['status'] == 1 && $order['is_reback'] ==2) $ordstatus = '已完成用户退款处理';
			if($order['status'] == 1 && $order['is_reback'] ==3) $ordstatus = '拒绝退款';
			if($order['status'] == 1 && $order['is_reback'] ==5) $ordstatus = '退款中待商家处理';
			if($order['status'] == 2) $ordstatus = '已发货';
			if($order['status'] == 3) $ordstatus = '订单完成';
			if($order['status'] == 4 && $order['is_reback'] !=2) $ordstatus = '买家取消订单';
			if($order['status'] == 4 && $order['is_reback'] ==2) $ordstatus = '已完成用户退款处理';
			if($order['status'] == 5){
				if($order['paystatus'] == 1){
					$ordstatus = '管理员已退款';
				}else{
					$ordstatus = '卖家取消订单';
				} 
			}
			$OrderSn = array('value' =>urlencode($order['dno']),'color' =>'#333333');
			switch($type){
				/* case 1:
					$first = array(
						'value'=>urlencode('有未付款订单\n您好，您下的<跑腿>订单还未付款哦，请尽快付款~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode('待支付'),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break; */
				case 2:
					$first = array(
						'value'=>urlencode('订单支付成功\n您下的<跑腿>订单已经支付成功，等待配送员接单~ '),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 3:
					$first = array(
						'value'=>urlencode('配送员已接单\n您下的<跑腿>订单，配送员已接单，联系电话'.$order['psemail']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 4:
					$first = array(
						'value'=>urlencode('配送员已取货\n您下的<跑腿>订单，配送员已取货，正在配送中，联系电话'.$order['psemail']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 5:
					$first = array(
						'value'=>urlencode('配送员已购买\n您下的<跑腿>订单，配送员已购买，正在配送中，联系电话'.$order['psemail']),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;
				case 6:
					$first = array(
						'value'=>urlencode('商品已送达\n您好，您下的<跑腿>订单，已成功送达，订单完成，欢迎再次下单~'),
						'color'=>$firstcolor,
					);
					$OrderStatus = array('value' =>urlencode($ordstatus),'color' =>'#333333');
					$remark = array('value' =>urlencode('订单金额：¥'.$order['allcost']),'color' =>'#333333');
					break;			
				default:
					return 'undefined_type';
                        break;
			};
			$template = array(
				"touser" => $useropenid,
				"template_id" => $template['template_id'],
				'url' => $this->siteurl.'/index.php?ctrl=wxsite&action=paotuidetail&orderid='.$orderid,
				'topcolor' => '#000',
				'data' => array('first'=>$first,
								'OrderSn' =>$OrderSn,						
								'OrderStatus' =>$OrderStatus,							
								'remark' => $remark,
								)
			);
		}
		$data = urldecode(json_encode($template));
		return $data;
	}
}
?>