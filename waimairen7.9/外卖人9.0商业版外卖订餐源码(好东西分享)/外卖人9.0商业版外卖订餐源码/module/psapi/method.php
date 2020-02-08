<?php
/**
 *配送宝  获取信息、通知信息   操作类
 *@package psapi
 *@author-text  lzh
 **/
class method   extends baseclass
{
    private $shopinfo;//店铺信息
    private $orderinfo;//订单信息
    //检测配送宝
    function checkpsb(){
        $psbaccid = intval(IFilter::act(IReq::get('psbaccid')));
        $psb_key = trim(IFilter::act(IReq::get('psbkey')));
        $psb_code = trim(IFilter::act(IReq::get('psbcode')));

        if($psbaccid < 1){
            $this->message('配送宝账号不存在');
            return false;
        }
		//3.3修正代码---- 配送宝对接代码修改
		
		
        $shopinfo = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "shop where psbaccid = '" . $psbaccid . "' and psbkey = '".$psb_key."' and psbcode = '".$psb_code."' ");
        if(empty($shopinfo)){
			//开始检测是不是平台配置的 跑腿对接商家  ptpsbaccid
			 
			$psinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset where ptpsbaccid='".$psbaccid."' and ptpsbkey = '".$psb_key."' and ptpsbcode = '".$psb_code."' ");
			
			if(empty($psinfo)){
			    $this->message('店铺不存在');
				return false;
			} 
			$newshopinfo = array('id'=>0,'shopname'=>'跑腿店铺');
			$this->shopinfo = $newshopinfo;
			return true; 
        }
        if($shopinfo['psbkey'] != $psb_key){
            $this->message('配送宝验证key错误');
            return false;
        }
        if($shopinfo['psbcode'] != $psb_code){
            $this->message('配送宝验证code错误');
            return false;
        }
        $this->shopinfo = $shopinfo;
        return true;
    }
    //验证订单
    function checkorder(){
        $orderid = intval(IFilter::act(IReq::get('orderid')));
        $ordercheck = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "order where id='" . $orderid . "' ");
        if(empty($ordercheck)){
            $this->message('订单不存在');
            return false;
        }else{
			if($ordercheck['shoptype'] == 100){
			
			}else{
				if($ordercheck['shopid'] != $this->shopinfo['id']){
					$this->message('订单不属于该店铺管理');
					return false;
				}
				if($ordercheck['pstype'] != 2){
					$this->message('订单类型不能通过配送宝管理');
					return false;
				}
			}
        }
        $this->orderinfo = $ordercheck;
        return true;
    }
    //配送平台返回配送操作结果
    //http://psb.waimairen.com/index.php?ctrl=psapi&action=ordercontrol&datatype=json&type=&orderid=&clerk_id=&clerk_name=&clerk_phone=
    function ordercontrol()
    {
        $dotype = trim(IFilter::act(IReq::get('type')));
        $orderid = intval(IFilter::act(IReq::get('orderid')));//订单id
        
        $clerk_id = intval(IFilter::act(IReq::get('clerk_id')));//配送员id
        $clerk_name = trim(IFilter::act(IReq::get('clerk_name')));//配送员姓名
        $clerk_phone = IFilter::act(IReq::get('clerk_phone'));//配送员手机
       $info = var_export($_POST,true);
        if($this->checkpsb() && $this->checkorder()) { 
			$ordercheck = $this->orderinfo;
			$ordCls = new orderclass();			
            if ($dotype == 'qiangorder') {//配送员抢单
                if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作改订单不能取单');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }                
				if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2  ) {
                    $this->message('订单退款中');
                }
		        $data['psuid'] = $clerk_id;
                $data['psusername'] = $clerk_name;
                $data['psemail'] = $clerk_phone;
                $data['psstatus'] = 1; 
                $data['picktime'] = time();
                #若是被该派单子，改派配送员抢单后，需要将之前配送员的操作记录删除掉	
				if($ordercheck['psuid'] > 0 && $ordercheck['psuid'] != $clerk_id){
					$this->mysql->delete(Mysite::$app->config['tablepre']."orderstatus"," statustitle = '配送员已接单' or statustitle = '配送员已到店' or statustitle = '配送员已取货' or statustitle = '配送员已购买' and orderid = ".$orderid."  "); 
				} 	 	
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "' and (psuid = 0 or psuid is null)");
                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员已接单";
                $statusdata['ststusdesc'] = '正赶往商家，配送员电话：'.$clerk_phone;
                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);

                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'   ");
                    $tempuser[] = $appcheck;
					$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已抢单',$clerk_name . '抢单成功,联系电话' . $clerk_phone);

                }
				if($ordercheck['shoptype']==100){
					if($ordCls->sendWxMsg($ordercheck['id'],3,3)){
						
					}
				}          
                $this->success('配送员抢单成功');

            } elseif ($dotype == 'changepsy') {//改派配送员
                logwrite('*****改派配送员******');
				if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作该订单');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }                
				if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2  ) {
                    $this->message('订单退款中');
                }			
		        $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "改派配送员";
                $statusdata['ststusdesc'] = '订单改派给' . $clerk_name . '配送员';
                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);
                $orderdata['psusername'] = $clerk_name;
                $orderdata['psemail'] = $clerk_phone;
                $orderdata['psuid'] = $clerk_id;
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $orderdata,"id='" . $orderid . "' ");

                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'   ");
					$tempuser[] = $appcheck;
					$appCls->SetUserlist($tempuser)->sendNewmsg('改派配送员','订单改派给' . $clerk_name . '配送员');
                }
                $this->success('改派订单成功');

            } elseif ($dotype == 'togetpai') {//当派单后配送员确认接单
                logwrite('*****改派配送员接单******');
				if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作该订单');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }                
				if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2 || $ordercheck['is_reback'] == 4 ) {
                    $this->message('订单退款中');
                }	
				
				$data['psuid'] = $clerk_id;
                $data['psusername'] = $clerk_name;
                $data['psemail'] = $clerk_phone;
				$data['psstatus'] = 1; 
                $data['picktime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "'");
                logwrite('改派配送员名称~配送员已接指派订单'.$data['psusername']);
                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员已接单";
                $statusdata['ststusdesc'] = $clerk_name . '接单成功,联系电话' . $clerk_phone;
                $statusdata['addtime'] = time();
               /*  $this->mysql->delete(Mysite::$app->config['tablepre'] . 'orderstatus', 'orderid = '.$orderid.' and statustitle="配送员已接单"'); */
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);
                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'   ");
                    $tempuser[] = $appcheck;
					$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已接指派订单',$clerk_name . '接单成功,联系电话' . $clerk_phone);

                }
                $this->success('配送员接单成功');

            } elseif ($dotype == 'goshop') {//配送员到店成功
                if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作改订单不能取单');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }
                if ($ordercheck['status'] != 1) {
                    $this->message('配送单不在待取状态');
                }
				if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2   ) {
                    $this->message('订单退款中');
                }
				$data['psstatus'] = 2; 
				$data['picktime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "' ");
 
                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员已到店";
                $statusdata['ststusdesc'] = "配送员已经到达商家，正在取货中";
                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);
                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'");
                    $tempuser[] = $appcheck;
					$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已到店','配送员开始取货'); 
                }
                $this->success('配送员到店成功');

            } elseif ($dotype == 'pickorder') {//配送员取单
                if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2 ) {
                    $this->message('订单退款中');
                }
                if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作改订单不能取单');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }
				// print_r($ordercheck);
				if ($ordercheck['status'] != 1) {
					// if($ordercheck['status'] < 1){
					// }
                    $this->message('配送单不在待取状态');
                }  
                $data['psstatus'] = 3; 
				$data['picktime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "' ");

                $statusdata['orderid'] = $orderid;
                if($ordercheck['shoptype']==100){
                    if($ordercheck['pttype']==2){
                        $statusdata['statustitle'] =  "配送员已购买";
                        $statusdata['ststusdesc']  =  "正前往收货地，请耐心等待~";
                    }else{
                        $statusdata['statustitle'] =  "配送员已取货";
                        $statusdata['ststusdesc']  =  "正前往收货地，请耐心等待~";
                    }
                }else{
                    $statusdata['statustitle'] = "配送员已取货";
                    $statusdata['ststusdesc'] = "配送员已取货，正在配送中";
                }

                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);
                if ($ordercheck['status'] != 2) {
                    $orderdata['status'] = 2;
                    $orderdata['sendtime'] = time();
                    $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $orderdata, "id='" . $orderid . "'");
                }
                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'   ");
                    $tempuser[] = $appcheck;
					$appCls->SetUserlist($tempuser)->sendNewmsg('配送员已取货','请等待配送');
					if($ordercheck['shoptype']==100){
						if($ordercheck['pttype']==2){
							if($ordCls->sendWxMsg($ordercheck['id'],5,3)){
								
							}
						}else{
							if($ordCls->sendWxMsg($ordercheck['id'],4,3)){
								
							}
						}
					}else{
						if($ordercheck['pstype']!=1){
							if($ordCls->sendWxMsg($ordercheck['id'],6,1)){
							
							}
						}
					}
                }		
				 
                $this->success('配送员取单成功');

            } elseif ($dotype == 'unpickorder') {//配送平台取消配送
                $data['psuid'] = 0;
                $data['psusername'] = '';
                $data['psemail'] = '';
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "orderid='" . $orderid . "'");


                $statusdata['orderid'] = $orderid;
                $statusdata['statustitle'] = "配送员取消配送";
                $statusdata['ststusdesc'] = "等待配送员抢单配送";
                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);

                if ($ordercheck['buyeruid'] > 0) {
                    $appCls = new appbuyclass();
                    $appcheck = $this->mysql->select_one("select *  from " . Mysite::$app->config['tablepre'] . "appbuyerlogin where uid = '" . $ordercheck['buyeruid'] . "'   ");
                    if (!empty($appcheck)) {
                        $appCls->sendmsg($appcheck['userid'], $appcheck['channelid'], "配送取消配送", '等待配送员抢单配送', 1);
                    }
                }
                $this->success('取消配送成功');

            } elseif ($dotype == 'sendorder'){//配送员已送达
                if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已关闭');
                }
				if ($ordercheck['status'] != 2&&$ordercheck['status'] != 1) {
                    $this->message('配送单不在待取状态');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作该订单');
                }
                if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2  ) {
                    $this->message('订单退款中');
                }
				$data['psstatus'] = 4; 
				$data['picktime'] = time();
						
                //更新订单状态
                $data['is_acceptorder'] = 1; 

                if ($ordercheck['status'] !=3) {
                    $data['status'] = 3;
                }

                $data['suretime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "' ");

				//分销返佣
				$is_open_distribution = Mysite::$app->config['is_open_distribution'];
				logwrite('商家完成订单，后台分销状态'.$is_open_distribution);
				if($is_open_distribution == 1){
					logwrite('配送员完成配送，后台分销状态开启');
					$distribution = new distribution();
					if($distribution->operateorder($orderid)){
						 logwrite('返佣成功');
					}else{
						$err = $distribution->Error();
						logwrite('返佣失败，失败原因：'.$err);
					}
				}
				
                $statusdata['orderid'] = $orderid;
                if($ordercheck['shoptype']==100){
                    $statusdata['statustitle'] = "订单已完成";
                    $statusdata['ststusdesc'] = "配送员已成功送达";
                }else{
                    $statusdata['statustitle'] = "订单已完成";
                    $statusdata['ststusdesc'] = "配送员已送达，期待再次光临";
                }

                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);

 				/* 记录配送员送达时候坐标 */

              //************送积分*******************
                 if($ordercheck['buyeruid'] > 0){
                     $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$ordercheck['buyeruid']."'");
                     $arr['score'] = $memberinfo['score']+Mysite::$app->config['consumption'];
                     if(Mysite::$app->config['con_extend'] > 0){
                         $allscore= $ordercheck['allcost']*Mysite::$app->config['con_extend'];
                         $arr['score']+=$allscore;
                         $consumption=$allscore;
                     }
                     if(!empty($consumption)){
                         $consumption+=Mysite::$app->config['consumption'];
                     }else{
                         $consumption=Mysite::$app->config['consumption'];
                     }
                     $this->mysql->update(Mysite::$app->config['tablepre'].'member',$arr,"uid ='".$ordercheck['buyeruid']."' ");
                     if($consumption > 0){
                         $memberCls=new memberclass($this->mysql);
                         $memberCls->addlog($ordercheck['buyeruid'],1,1,$consumption,'消费送积分','消费送积分'.$consumption,$arr['score']);
                     }
                 }
                 //************送积分结束*******************	 

                //更新销量
                $shuliang = $this->mysql->select_one("select sum(goodscount) as sellcount from " . Mysite::$app->config['tablepre'] . "orderdet where order_id='" . $orderid . "'  ");
                if (!empty($shuliang) && $shuliang['sellcount'] > 0) {
                    $this->mysql->update(Mysite::$app->config['tablepre'] . 'shop', '`sellcount`=`sellcount`+' . $shuliang['sellcount'] . '', "id ='" . $ordercheck['shopid'] . "' ");
                }

                //更新用户成长值
                if (!empty($ordercheck['buyeruid'])) {
                    $memberinfo = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "member where uid='" . $ordercheck['buyeruid'] . "'   ");
                    if (!empty($memberinfo)) {
                        $this->mysql->update(Mysite::$app->config['tablepre'] . 'member', '`total`=`total`+' . $ordercheck['allcost'], "uid ='" . $ordercheck['buyeruid'] . "' ");
                    }
                    /*写优惠券*/
                    if ($memberinfo['parent_id'] > 0) {
                        $upuser = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "member where uid='" . $memberinfo['parent_id'] . "'   ");
                        if (!empty($upuser)) {
                            if (Mysite::$app->config['tui_juan'] == 1) {
                                $nowtime = time();
                                $endtime = $nowtime + Mysite::$app->config['tui_juanday'] * 24 * 60 * 60;
                                $juandata['card'] = $nowtime . rand(100, 999);
                                $juandata['card_password'] = substr(md5($juandata['card']), 0, 5);
                                $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效
                                $juandata['creattime'] = $nowtime;// 制造时间
                                $juandata['cost'] = Mysite::$app->config['tui_juancost'];// 优惠金额
                                $juandata['limitcost'] = Mysite::$app->config['tui_juanlimit'];// 购物车限制金额下限
                                $juandata['endtime'] = $endtime;// 失效时间
                                $juandata['uid'] = $upuser['uid'];// 用户ID
                                $juandata['username'] = $upuser['username'];// 用户名
                                $juandata['name'] = '推荐送优惠券';//  优惠券名称
                                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'juan', $juandata);
                                $this->mysql->update(Mysite::$app->config['tablepre'] . 'member', '`parent_id`=0', "uid ='" . $ordercheck['buyeruid'] . "' ");
                                $logdata['uid'] = $upuser['uid'];
                                $logdata['childusername'] = $memberinfo['username'];
                                $logdata['titile'] = '推荐送优惠券';
                                $logdata['addtime'] = time();
                                $logdata['content'] = '被推荐下单完成';
                                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'sharealog', $logdata);
                            }
                        }
                    }
                } 
				if($ordercheck['shoptype'] != 100){
					if($ordCls->sendWxMsg($ordercheck['id'],7,1)){
						
					}
					if($ordCls->sendWxMsg($ordercheck['id'],2,2)){
					
					}
				}else{
					if($ordCls->sendWxMsg($ordercheck['id'],6,3)){
						
					}
				}
				
				//更新坐标 放到最下边，因为有可能去取位置失败
				if(  $ordercheck['psuid'] > 0 ){
					$orderdata = array();
					if(  $ordercheck['pstype'] == 0 ){
						$psylocationonfo = $this->mysql->select_one("select uid,lng,lat from ".Mysite::$app->config['tablepre']."locationpsy where uid='".$ordercheck['psuid']."' ");
						if(!empty($psylocationonfo)){
							 $orderdata['psyoverlng'] = $psylocationonfo['lng'];
							 $orderdata['psyoverlat'] = $psylocationonfo['lat'];
						}
					}
					if(  $ordercheck['pstype'] == 2 ){
						$psbinterface = new psbinterface(); 
						$psylocationonfo = $psbinterface->getpsbclerkinfo($ordercheck['psuid']);
						if( !empty($psylocationonfo) && !empty($psylocationonfo['posilnglat']) ){
							$posilnglatarr = explode(',',$psylocationonfo['posilnglat']);
							$posilng = $posilnglatarr[0];
							$posilat = $posilnglatarr[1];
							if( !empty($posilng) && !empty($posilat)  ){
								 $orderdata['psyoverlng'] = $posilng;
								 $orderdata['psyoverlat'] = $posilat;
							} 
						}
					}
					
					if(isset( $orderdata['psyoverlat'])){
						$this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $orderdata, "id='" . $orderid . "'");
					}
					
				}
				
				
                $this->success('配送员送达成功');

            }elseif($dotype=='resendorder'){
				 if ($ordercheck['status'] != 2&&$ordercheck['status'] != 1) {
                    $this->message('订单已完成或者已关闭');
                }
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作该订单');
                }
                if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2 ) {
                    $this->message('订单退款中');
                }
                if ($ordercheck['status'] == 3) {
                    $this->message('订单已完成');
                }
                if ($ordercheck['status'] > 3) {
                    $this->message('订单已不关闭');
                }
				$data['psstatus'] = 4; 
				$data['picktime'] = time();
				$data['is_acceptorder'] = 1; 
                if ($ordercheck['status'] !=3) {
                    $data['status'] = 3;
                } 
                $data['suretime'] = time();
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "' ");

				//分销返佣
				$is_open_distribution = Mysite::$app->config['is_open_distribution'];
				logwrite('商家完成订单，后台分销状态'.$is_open_distribution);
				if($is_open_distribution == 1){
					logwrite('配送员完成配送，后台分销状态开启');
					$distribution = new distribution();
					if($distribution->operateorder($orderid)){
						 logwrite('返佣成功');
					}else{
						$err = $distribution->Error();
						logwrite('返佣失败，失败原因：'.$err);
					}
				}
				
				
                $statusdata['orderid'] = $orderid;
                if($ordercheck['shoptype']==100){
                    $statusdata['statustitle'] = "商品已送达";
                    $statusdata['ststusdesc'] = "配送员已成功送达";
                }else{
                    $statusdata['statustitle'] = "配送员已送达";
                    $statusdata['ststusdesc'] = "配送完成";
                }

                $statusdata['addtime'] = time();
                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'orderstatus', $statusdata);
				
				
				 
				
              //************送积分*******************
                 if($ordercheck['buyeruid'] > 0){
                     $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid ='".$ordercheck['buyeruid']."'");
                     $arr['score'] = $memberinfo['score']+Mysite::$app->config['consumption'];
                     if(Mysite::$app->config['con_extend'] > 0){
                         $allscore= $ordercheck['allcost']*Mysite::$app->config['con_extend'];
                         $arr['score']+=$allscore;
                         $consumption=$allscore;
                     }
                     if(!empty($consumption)){
                         $consumption+=Mysite::$app->config['consumption'];
                     }else{
                         $consumption=Mysite::$app->config['consumption'];
                     }
                     $this->mysql->update(Mysite::$app->config['tablepre'].'member',$arr,"uid ='".$ordercheck['buyeruid']."' ");
                     if($consumption > 0){
                         $memberCls=new memberclass($this->mysql);
                         $memberCls->addlog($ordercheck['buyeruid'],1,1,$consumption,'消费送积分','消费送积分'.$consumption,$arr['score']);
                     }
                 }
                 //************送积分结束*******************	 
				
				
                //更新订单状态
             
               

                //更新销量
                $shuliang = $this->mysql->select_one("select sum(goodscount) as sellcount from " . Mysite::$app->config['tablepre'] . "orderdet where order_id='" . $orderid . "'  ");
                if (!empty($shuliang) && $shuliang['sellcount'] > 0) {
                    $this->mysql->update(Mysite::$app->config['tablepre'] . 'shop', '`sellcount`=`sellcount`+' . $shuliang['sellcount'] . '', "id ='" . $ordercheck['shopid'] . "' ");
                }

                //更新用户成长值
                if (!empty($ordercheck['buyeruid'])) {
                    $memberinfo = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "member where uid='" . $ordercheck['buyeruid'] . "'   ");
                    if (!empty($memberinfo)) {
                        $this->mysql->update(Mysite::$app->config['tablepre'] . 'member', '`total`=`total`+' . $ordercheck['allcost'], "uid ='" . $ordercheck['buyeruid'] . "' ");
                    }
                    /*写优惠券*/
                    if ($memberinfo['parent_id'] > 0) {
                        $upuser = $this->mysql->select_one("select * from " . Mysite::$app->config['tablepre'] . "member where uid='" . $memberinfo['parent_id'] . "'   ");
                        if (!empty($upuser)) {
                            if (Mysite::$app->config['tui_juan'] == 1) {
                                $nowtime = time();
                                $endtime = $nowtime + Mysite::$app->config['tui_juanday'] * 24 * 60 * 60;
                                $juandata['card'] = $nowtime . rand(100, 999);
                                $juandata['card_password'] = substr(md5($juandata['card']), 0, 5);
                                $juandata['status'] = 1;// 状态，0未使用，1已绑定，2已使用，3无效
                                $juandata['creattime'] = $nowtime;// 制造时间
                                $juandata['cost'] = Mysite::$app->config['tui_juancost'];// 优惠金额
                                $juandata['limitcost'] = Mysite::$app->config['tui_juanlimit'];// 购物车限制金额下限
                                $juandata['endtime'] = $endtime;// 失效时间
                                $juandata['uid'] = $upuser['uid'];// 用户ID
                                $juandata['username'] = $upuser['username'];// 用户名
                                $juandata['name'] = '推荐送优惠券';//  优惠券名称
                                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'juan', $juandata);
                                $this->mysql->update(Mysite::$app->config['tablepre'] . 'member', '`parent_id`=0', "uid ='" . $ordercheck['buyeruid'] . "' ");
                                $logdata['uid'] = $upuser['uid'];
                                $logdata['childusername'] = $memberinfo['username'];
                                $logdata['titile'] = '推荐送优惠券';
                                $logdata['addtime'] = time();
                                $logdata['content'] = '被推荐下单完成';
                                $this->mysql->insert(Mysite::$app->config['tablepre'] . 'sharealog', $logdata);
                            }
                        }
                    }
					if($ordercheck['shoptype'] != 100){
						if($ordCls->sendWxMsg($ordercheck['id'],7,1)){
							
						}
					}else{
						if($ordCls->sendWxMsg($ordercheck['id'],6,3)){
							
						}
					}
					if($ordCls->sendWxMsg($ordercheck['id'],2,2)){
						
					}
                }          
                $this->success('配送员送达成功');
			}elseif ($dotype == 'overorder') {//配送完成
                if ($ordercheck['is_make'] == 2) {
                    $this->message('商家不制作该订单');
                }
                if ($ordercheck['status'] != 2) {
                    $this->message('该订单不在配送状态');
                }
				if ($ordercheck['is_reback'] == 1 || $ordercheck['is_reback'] == 2  ) {
                    $this->message('订单退款中');
                }
				
                $data['status'] = 3;
				$data['psstatus'] = 4;
                $this->mysql->update(Mysite::$app->config['tablepre'] . 'order', $data, "id='" . $orderid . "'");
				
				//分销返佣
				$is_open_distribution = Mysite::$app->config['is_open_distribution'];
				logwrite('商家完成订单，后台分销状态'.$is_open_distribution);
				if($is_open_distribution == 1){
					logwrite('配送员完成配送，后台分销状态开启');
					$distribution = new distribution();
					if($distribution->operateorder($orderid)){
						 logwrite('返佣成功');
					}else{
						$err = $distribution->Error();
						logwrite('返佣失败，失败原因：'.$err);
					}
				}
				if($ordercheck['shoptype'] != 100){
					if($ordCls->sendWxMsg($ordercheck['id'],7,1)){
						
					}
				}else{
					if($ordCls->sendWxMsg($ordercheck['id'],6,3)){
						
					}
				}
				if($ordCls->sendWxMsg($ordercheck['id'],2,2)){
					
				}
                $this->success('操作成功');

            } else {
                $this->message('未定义的操作');
            }
        }
    }


    //配送宝获取外卖平台订单列表
    //http://psb.waimairen.com/index.php?ctrl=psapi&action=shoporderlist&datatype=json&apiid=&key=&code=&querytype=&searchvalue=&orderstatus=&starttime=&endtime=&page=&pagesize=
    function shoporderlist(){
        if($this->checkpsb()) {
            $shopinfo = $this->shopinfo;
            if(empty($shopinfo)){
                $this->message('店铺不存在');
            }

            /* 店铺销量计算 */
            $shopcounts = $this->mysql->select_one( "select count(id) as shuliang  from ".Mysite::$app->config['tablepre']."order	 where  status = 3 and  shopid = ".$shopinfo['id']."" );

            if(empty( $shopcounts['shuliang']  )){
                $shopinfo['ordercount'] = 0;
            }else{
                $shopinfo['ordercount']  = $shopcounts['shuliang'];
            }
            $shopinfo['ordercount'] = $shopinfo['ordercount']+$shopinfo['virtualsellcounts'];
            /* 店铺星级计算 */
            $zongpoint = $shopinfo['point'];
            $zongpointcount = $shopinfo['pointcount'];
            if($zongpointcount != 0 ){
                $shopstart = intval( round($zongpoint/$zongpointcount) );
            }else{
                $shopstart= 0;
            }
            $shopinfo['point'] = 	$shopstart;

            $querytype = IReq::get('querytype');
            $searchvalue = IReq::get('searchvalue');
            $orderstatus = intval(IReq::get('orderstatus'));
            $starttime = IReq::get('starttime');
            $endtime = IReq::get('endtime');
            $nowday = date('Y-m-d', time());
            $starttime = empty($starttime) ? $nowday : $starttime;
            $endtime = empty($endtime) ? $nowday : $endtime;
            $where = '  where ord.addtime > ' . strtotime($starttime . ' 00:00:00') . ' and ord.addtime < ' . strtotime($endtime . ' 23:59:59');

            if (!empty($shopinfo)) {
                $where .= ' and ord.shopid = ' . $shopinfo['id'];
            }

            if (!empty($querytype)) {
                if (!empty($searchvalue)) {
                    $where .= ' and ' . $querytype . ' LIKE \'%' . $searchvalue . '%\' ';
                }
            }
            if ($orderstatus > 0) {
                if ($orderstatus > 4) {
                    $where .= empty($where) ? ' where ord.status > 3 ' : ' and ord.status > 3 ';
                } else {
                    $newstatus = $orderstatus - 1;
                    $where .= empty($where) ? ' where ord.status =' . $newstatus : ' and ord.status = ' . $newstatus;
                }

            }
            $pageshow = new page();
            $pageshow->setpage(IReq::get('page'), IReq::get('pagesize'));

            $orderlist = $this->mysql->getarr("select ord.*,mb.username as acountname from " . Mysite::$app->config['tablepre'] . "order as ord left join  " . Mysite::$app->config['tablepre'] . "member as mb on mb.uid = ord.buyeruid   " . $where . " order by ord.id desc limit " . $pageshow->startnum() . ", " . $pageshow->getsize() . "");
            $data['orderlist'] = array();
            if ($orderlist) {
                foreach ($orderlist as $key => $value) {
                    $value['detlist'] = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "orderdet where   order_id = " . $value['id'] . " order by id desc ");
                    $value['buyeraddress'] = urldecode($value['buyeraddress']);
                    $data['orderlist'][] = $value;
                }
            }
            $data['shopinfo'] = $shopinfo;
            $this->success($data);
        }
    }


    //配送宝获取外卖平台单个订单
    //http://psb.waimairen.com/index.php?ctrl=psapi&action=shoponeorder&datatype=json&apiid=&key=&code=&querytype=&searchvalue=&orderstatus=&starttime=&endtime=&page=&pagesize=
    function shoponeorder()
    {
        if($this->checkpsb() && $this->checkorder()) {
            $orderinfo = $this->orderinfo;
            $data['orderinfo'] = array();
            if ($orderinfo) {
                $orderinfo['detlist'] = $this->mysql->getarr("select * from " . Mysite::$app->config['tablepre'] . "orderdet where order_id = " . $orderinfo['id'] . " order by id desc ");
                $orderinfo['buyeraddress'] = urldecode($orderinfo['buyeraddress']);
                $scoretocost = Mysite::$app->config['scoretocost'];
                $orderinfo['scoredown'] =  $orderinfo['scoredown']/$scoretocost;//抵扣积分
                $orderinfo['scoretype'] = 1; 
				$data['orderinfo'] = $orderinfo;
            }
            $this->success($data);
        }
    }



    //配送平台商家对订单的操作
    //http://psb.waimairen.com/index.php?ctrl=psapi&action=shopcontrol&datatype=json&type=&apiid=&key=&code=&orderid=
    function shopcontrol(){
        $type =trim(IFilter::act(IReq::get('type')));
        $orderid = intval(IReq::get('orderid'));
        if($this->checkpsb() && $this->checkorder()) {
            $shopinfo = $this->shopinfo;
            if(empty($shopinfo)){
                $this->message('店铺不存在');
            }
            $shopctlord = new shopctlord($orderid,$shopinfo['id'],$this->mysql);
            switch($type){
                case 'unorder':
                    if($shopctlord->unorder()){
                        $this->success('取消成功');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'makeorder':
                    //制作该订单
                    if($shopctlord->makeorder()){
                        $this->success('制作成功');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'unmakeorder':
                    if($shopctlord->SetMemberls($this->memberCls)->unmakeorder()){
                        $this->success('已取消制作');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'sendorder':
                    if($shopctlord->sendorder()){
                        $this->success('发货成功');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'delorder':
                    if($shopctlord->delorder()){
                        $this->success('删除成功');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'wancheng':
                    if($shopctlord->SetMemberls($this->memberCls)->wancheng()){
                        $this->success('success');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'reback'://退款
                    if($shopctlord->reback()){
                        $this->success('success');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                case 'unreback'://取消退款
                    if($shopctlord->unreback()){
                        $this->success('success');
                    }else{
                        $this->message($shopctlord->Error());
                    }
                    break;
                default:
                    $this->message('未定义的操作');
                    break;
            }
        }

    }

	
	  /* 配送宝上传图片接口 */
	 function psbimgUpload(){ 
 	 
		$uploadname ='imgFile';//传入参数  用户名 
		$json = new Services_JSON();
		$uploadpath = 'upload/psbimg/';//size 获取文件大小 
		if(isset($_FILES[$uploadname])){  
		    if($_FILES[$uploadname]['error'] == 0 ){//可以上传
				$upload1 = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));
				$file1 = $upload1->getfile(); 
				 
				if($upload1->errno !=15 && $upload1->errno !=0){   
					$this->message($upload1->errmsg());
				}else{ 
					$data['psbimgUploadUrl'] = Mysite::$app->config['siteurl'].'/'.$uploadpath.$file1[0]['saveName'];
					$this->success($data);
			    }
		    }else{
				$this->message('上传失败');
			}
		}else{
			$this->message('上传失败');
		}
  	}  
	  
	


}