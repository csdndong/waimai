<?php
class method   extends areaadminbaseclass
{
	//交易统计
	function trade_statisyic(){
        $shopname = IReq::get('shopname');
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-14 day'));
			$endtime2 = date('Y-m-d',strtotime('-8 day'));
			$data['beforeday'] = 7;
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-60 day'));
			$endtime2 = date('Y-m-d',strtotime('-31 day'));
			$data['beforeday'] = 30;
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				#print_r(111);
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$starttime2 = date('Y-m-d',strtotime('-14 day'));
				$endtime2 = date('Y-m-d',strtotime('-8 day'));
				$data['beforeday'] = 7;
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
					
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
				$data['beforeday'] = (strtotime($endtime)-strtotime($starttime))/86400+1;
				$starttime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($starttime)));
				$endtime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($endtime)));	
			}
		}
		if($data['datetype']==2){
			$data['daytype'] = 30;
		}else{
			$data['daytype'] = 7;
		}
#print_r($data['datetype']);
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$ordertype = intval(IReq::get('ordertype'));
        $newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$where2 = '  where addtime > '.strtotime($starttime2.' 00:00:00').' and addtime < '.strtotime($endtime2.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$trendwhere = ' and admin_id ='.$this->admin['cityid'];
		$data['shopname'] = '';
		$data['ordertype'] = 0;
		if(!empty($ordertype)){
			$newlink .='/ordertype/'.$ordertype;
			$data['ordertype'] = $ordertype;
			if($ordertype==2){
				$where .= ' and shoptype = 100';
				$where2 .= ' and shoptype = 100';
				$trendwhere .= ' and shoptype = 100';
			}else{
				if(!empty($shopname)){
					$data['shopname'] = $shopname;
					$where .= ' and shopname like "%'.$shopname.'%"';
					$where2 .= ' and shopname like "%'.$shopname.'%"';
					$trendwhere .= ' and shopname like "%'.$shopname.'%"';
					$newlink .= '/shopname/'.$shopname;
				}
				if($ordertype==1){
					$where .= ' and shoptype != 100';
					$where2 .= ' and shoptype != 100';
					$trendwhere .= ' and shoptype != 100';
				}
			}
		}
		//趋势图数据
		$xarr = array();
		$datearr = array();
		$ordnumarr = array();
		$ordcostarr = array();
		for($i=1;$i<=$data['daytype'];$i++){
			$date = date('Y-m-d',strtotime('-'.$i.' day'));
			$date1 = date('m-d',strtotime('-'.$i.' day'));
			$xarr[] = "'".$date1."'";
			$datearr[] = $date;
		}
		$xarr = array_reverse($xarr);
		$datearr = array_reverse($datearr);
		foreach($datearr as $k=>$val){
			$newtrendwhere = '  where addtime > '.strtotime($val.' 00:00:00').' and addtime < '.strtotime($val.' 23:59:59').$trendwhere;
			$trenddata = $this->getorddata($newtrendwhere,$ordertype,0);
			$ordcostarr[] = $trenddata['ordcost'];
			$ordnumarr[] = $trenddata['ordernum'];
		}
		$ordcostarr = array_reverse($ordcostarr);
		$ordnumarr = array_reverse($ordnumarr);
		$data['xstr'] = implode(',',$xarr);
		$data['ordcoststr'] = implode(',',$ordcostarr);
		$data['ordnumstr'] = implode(',',$ordnumarr);
        $link = IUrl::creatUrl('areaadminpage/analysis/module/trade_statisyic'.$newlink);
        $data['outlink'] =IUrl::creatUrl('areaadminpage/analysis/module/outtrade_statisyic'.$newlink);
		$nowdata = $this->getorddata($where,$ordertype,0);
		$olddata = $this->getorddata($where2,$ordertype,0);
		$data['nowdata'] = $nowdata;
		//订单总数
		if($nowdata['ordernum']>=$olddata['ordernum']){			
			$data['ordnumbili'] = number_format((($nowdata['ordernum']-$olddata['ordernum'])/$olddata['ordernum']*100),2);
			if($olddata['ordernum']==0){
				$data['ordnumbili'] = 100;	
			}
			$data['ordnumbilitype'] = 1;
		}else{
			$data['ordnumbili'] = number_format(((1-$nowdata['ordernum']/$olddata['ordernum'])*100),2);
			$data['ordnumbilitype'] = 2;
		}
		//订单金额
		if($nowdata['ordcost']>=$olddata['ordcost']){
			
			$data['ordcostbili'] = number_format((($nowdata['ordcost']-$olddata['ordcost'])/$olddata['ordcost']*100),2);
			if($olddata['ordcost']==0){
				$data['ordcostbili'] = 100;	
			}
			$data['ordcostbilitype'] = 1;
		}else{
			$data['ordcostbili'] = number_format(((1-$nowdata['ordcost']/$olddata['ordcost'])*100),2);
			$data['ordcostbilitype'] = 2;
		}
		//订单均价
		if($nowdata['singlecost']>=$olddata['singlecost']){
			
			$data['singlebili'] = number_format((($nowdata['singlecost']-$olddata['singlecost'])/$olddata['singlecost']*100),2);
			if($olddata['singlecost']==0){
				$data['singlebili'] = 100;	
			}
			$data['singlebilitype'] = 1;
		}else{
			$data['singlebili'] = number_format(((1-$nowdata['singlecost']/$olddata['singlecost'])*100),2);
			$data['singlebilitype'] = 2;
		}
		//有效订单金额
		if($nowdata['useordcost']>=$olddata['useordcost']){
			
			$data['useordbili'] = number_format((($nowdata['useordcost']-$olddata['useordcost'])/$olddata['useordcost']*100),2);
			if($olddata['useordcost']==0){
				$data['useordbili'] = 100;	
			}
			$data['useordbilitype'] = 1;
		}else{
			$data['useordbili'] = number_format(((1-$nowdata['useordcost']/$olddata['useordcost'])*100),2);
			$data['useordbilitype'] = 2;
		}
		//有效订单数量
		if($nowdata['useordnum']>=$olddata['useordnum']){
			
			$data['usenumbili'] = number_format((($nowdata['useordnum']-$olddata['useordnum'])/$olddata['useordnum']*100),2);
			if($olddata['useordnum']==0){
				$data['usenumbili'] = 100;	
			}
			$data['usenumbilitype'] = 1;
		}else{
			$data['usenumbili'] = number_format(((1-$nowdata['useordnum']/$olddata['useordnum'])*100),2);
			$data['usenumbilitype'] = 2;
		}
		//无效订单数量
		if($nowdata['nouseordnum']>=$olddata['nouseordnum']){
			
			$data['nousebili'] = number_format((($nowdata['nouseordnum']-$olddata['nouseordnum'])/$olddata['nouseordnum']*100),2);
			if($olddata['nouseordnum']==0){
				$data['nousebili'] = 100;	
			}
			$data['nousebilitype'] = 1;
		}else{
			$data['nousebili'] = number_format(((1-$nowdata['nouseordnum']/$olddata['nouseordnum'])*100),2);
			$data['nousebilitype'] = 2;
		}
		//下单会员数
		if($nowdata['memnum']>=$olddata['memnum']){
			
			$data['membili'] = number_format((($nowdata['memnum']-$olddata['memnum'])/$olddata['memnum']*100),2);
			if($olddata['memnum']==0){
				$data['membili'] = 100;	
			}
			$data['membilitype'] = 1;
		}else{
			$data['membili'] = number_format(((1-$nowdata['memnum']/$olddata['memnum'])*100),2);
			$data['membilitype'] = 2;
		}
		//退款金额
		if($nowdata['drawcost']>=$olddata['drawcost']){
			
			$data['drawbili'] = number_format((($nowdata['drawcost']-$olddata['drawcost'])/$olddata['drawcost']*100),2);
			if($olddata['drawcost']==0){
				$data['drawbili'] = 100;	
			}
			$data['drawbilitype'] = 1;
		}else{
			$data['drawbili'] = number_format(((1-$nowdata['drawcost']/$olddata['drawcost'])*100),2);
			$data['drawbilitype'] = 2;
		}
        Mysite::$app->setdata($data);
    }
	//导出交易统计
	function outtrade_statisyic(){
        $shopname = IReq::get('shopname');
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$ordertype = intval(IReq::get('ordertype'));
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$data['shopname'] = '全部';
		$data['ordertype'] = '全部';
		$stationinfo = $this->mysql->select_one("select stationname from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid= ".$this->admin['cityid']." ");
		$data['areaname'] = $stationinfo['stationname'];
		if(!empty($ordertype)){
			if($ordertype==2){
				$where .= ' and shoptype = 100';
				$data['ordertype'] = '跑腿订单';
				$data['shopname'] = '--';
			}else{
				if(!empty($shopname)){
					$data['shopname'] = $shopname;
					$where .= ' and shopname like "%'.$shopname.'%"';
				}
				if($ordertype==1){
					$where .= ' and shoptype != 100';					
					$data['ordertype'] = '外卖订单';
				}else{
					$data['ordertype'] = '闪惠订单';
				}
			}
		}
		$nowdata = $this->getorddata($where,$ordertype,0);
		$nowdata['areaname'] = $data['areaname'];
		$nowdata['ordertype'] = $data['ordertype'];
		$nowdata['shopname'] = $data['shopname'];
		$nowdata['starttime'] = $data['starttime'];
		$nowdata['endtime'] = $data['endtime'];
		$list[0] = $nowdata;
		$outexcel = new phptoexcel();
        $titledata = array('站点','订单类型','店铺','开始时间','结束时间','订单总数','有效订单数','无效订单数','下单会员数','订单总金额','有效订单总金额','退款总金额','单均价');
        $titlelabel = array('areaname','ordertype','shopname','starttime','endtime','ordernum','useordnum','nouseordnum','memnum','ordcost','useordcost','drawcost','singlecost');
        $outexcel->out($titledata,$titlelabel,$list,'','交易统计');   
    }
	function getorddata($where,$ordertype,$uid){
		#print_r($where);print_r($ordertype);
		$where2 = $where;
		$where1 = $where;
		if($uid > 0){
			$where1 = $where.' and buyeruid = '.$uid.' ';
			$where2 = $where.' and uid = '.$uid.' ';
		}
		$order1 = $this->mysql->select_one("select count(id) as ordernum1,sum(allcost) as ordcost1 from ".Mysite::$app->config['tablepre']."order ".$where1." ");
		$ordernum1 = empty($order1['ordernum1'])?0:$order1['ordernum1'];//订单数量
		$ordcost1 = empty($order1['ordcost1'])?0:$order1['ordcost1'];//订单金额
		$useord1 = $this->mysql->select_one("select count(id) as useordnum1,sum(allcost) as useordcost1 from ".Mysite::$app->config['tablepre']."order ".$where1." and  status = 3   ");
		$useordnum1 = empty($useord1['useordnum1'])?0:$useord1['useordnum1'];//有效订单数量
		$useordcost1 = empty($useord1['useordcost1'])?0:$useord1['useordcost1'];//有效订单金额
		$nouseordnum1 = $ordernum1 - $useordnum1;//无效订单数量
		$memnum1 = 0;
		if($uid==0){
			$memnum1 = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order ".$where." and status >0 group by buyeruid ");
			$memnum1 = empty($memnum1)?0:$memnum1;//下单会员数
		}
		
		$drawcostinfo = $this->mysql->select_one("select sum(allcost) as drawcost from ".Mysite::$app->config['tablepre']."order ".$where1." and is_reback=2  ");
		$drawcost1 = empty($drawcostinfo)?0:$drawcostinfo['drawcost'];//退款金额
		//交易金额
		$trade1 = $this->mysql->select_one("select sum(allcost) as tradecost1,count(id) as tradenum1 from ".Mysite::$app->config['tablepre']."order ".$where1." and ((paytype=1 and  paystatus = 1) or (paytype=0 and is_make=1)) ");
		$tradecost1 = empty($trade1['tradecost1'])?0:$trade1['tradecost1'];
		$tradenum1 = empty($trade1['tradenum1'])?0:$trade1['tradenum1'];//交易数量
		
		if($ordertype==3 || $ordertype==0){
			$order2 = $this->mysql->select_one("select count(id) as ordernum2,sum(sjcost) as ordcost2 from ".Mysite::$app->config['tablepre']."shophuiorder ".$where2." ");
			$ordernum2 = empty($order2['ordernum2'])?0:$order2['ordernum2'];//订单数量
			$ordcost2 = empty($order2['ordcost2'])?0:$order2['ordcost2'];//订单金额
			$useord2 = $this->mysql->select_one("select count(id) as useordnum2,sum(sjcost) as useordcost2 from ".Mysite::$app->config['tablepre']."shophuiorder ".$where2." and  paystatus=1");
			$useordnum2 = empty($useord2['useordnum2'])?0:$useord2['useordnum2'];//有效订单数量
			$useordcost2 = empty($useord2['useordcost2'])?0:$useord2['useordcost2'];//有效订单金额
			$nouseordnum2 = $ordernum2-$useordnum2;//无效订单数量
			$memnum2 = 0;
			if($uid==0){
				$memnum2 = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shophuiorder ".$where." group by uid ");
				$memnum2 = empty($memnum2)?0:$memnum2;//下单会员数
			}
			$drawcost2 = 0;
			//交易金额
			$tradecost2 = $useordcost2;
			$tradenum2 = $useordnum2;
		}
		if(!empty($ordertype)){
			 if($ordertype!=3){
				$data['ordernum'] = $ordernum1;
				$data['ordcost'] = round($ordcost1,2);
				$data['singlecost'] = empty($data['ordernum'])?0:round(($data['ordcost']/$data['ordernum']),2);
				$data['useordcost'] = round($useordcost1,2);
				$data['useordnum'] = $useordnum1;
				$data['nouseordnum'] = $nouseordnum1;
				$data['memnum'] = $memnum1;
				$data['drawcost'] = round($drawcost1,2);
				$data['tradecost'] = round($tradecost1,2);
				$data['tradenum'] = $tradenum1;
			 }else{
				$data['ordernum'] = $ordernum2;
				$data['ordcost'] = round($ordcost2,2);
				$data['singlecost'] = empty($data['ordernum'])?0:round(($data['ordcost']/$data['ordernum']),2);
				$data['useordcost'] = round($useordcost2,2);
				$data['useordnum'] = $useordnum2;
				$data['nouseordnum'] = $nouseordnum2;
				$data['memnum'] = $memnum2;
				$data['drawcost'] = round($drawcost2,2);
				$data['tradecost'] = round(tradecost2,2);
				$data['tradenum'] = $tradenum2;
			 }
		}else{
			$data['ordernum'] = $ordernum1+$ordernum2;
			$data['ordcost'] = round(($ordcost1+$ordcost2),2);
			$data['singlecost'] = empty($data['ordernum'])?0:round(($data['ordcost']/$data['ordernum']),2);
			$data['useordcost'] = round(($useordcost1+$useordcost2),2);
			$data['useordnum'] = $useordnum1+$useordnum2;
			$data['nouseordnum'] = $nouseordnum1+$nouseordnum2;
			$data['memnum'] = $memnum1+$memnum2;
			$data['drawcost'] = round(($drawcost1+$drawcost2),2);
			$data['tradecost'] = round(($tradecost1+$tradecost2),2);
			$data['tradenum'] = $tradenum1+$tradenum2;
		}
		return $data;
	}
	//交易记录
	function trade_log(){
        $dno = trim(IReq::get('dno'));
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				#print_r(111);
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
			}
		}
#print_r($data['datetype']);
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$admin_id = intval(IReq::get('admin_id'));
		$tradetype = intval(IReq::get('tradetype'));
        $newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$where2 = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$data['dno'] = '';
		$data['tradetype'] = 0;
		if(!empty($dno)){
			$data['dno'] = $dno;
			$where .= " and dno = '".$dno."' ";
			$where2 .= " and dno = '".$dno."' ";
			$newlink .= '/dno/'.$dno;
		}
		if(!empty($tradetype)){
			$data['tradetype'] = $tradetype;
			if($tradetype==2){
				$where .= " and is_reback = 2 ";
			}	
		}
        $link = IUrl::creatUrl('areaadminpage/analysis/module/trade_log'.$newlink);
        $data['outlink'] =IUrl::creatUrl('areaadminpage/analysis/module/outtrade_log'.$newlink);
		$nowdata = $this->getorddata($where2,0,0);
		$data['nowdata'] = $nowdata;
		#print_r($data['nowdata']);
		$pageinfo = new page();
        $pageinfo->setpage(IReq::get('page'));
		$order1 = $this->mysql->getarr("select id,paytime,paytype,paytype_name,allcost,dno,is_reback from ".Mysite::$app->config['tablepre']."order ".$where." and paystatus=1");
		#print_r($order1);
		$neworder1 = array();
		$neworder2 = array();
		$paytypearr = array('open_acout'=>'余额支付','weixin'=>'微信支付','alipay'=>'支付宝支付','alimobile'=>'手机支付宝支付');
		if(!empty($order1)){
			foreach($order1 as $k=>$val){
				if($val['is_reback']==2){
					$drawinfo = $this->mysql->select_one("select addtime from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$val['id']."  order by  id desc ");	
					$val['paytime'] = $drawinfo['addtime'];//退款成功时间
				}
				if($val['paytype']==0){
					$val['paytypename'] = '货到付款';
				}else{
					$val['paytypename'] = $paytypearr[$val['paytype_name']];
				}
				if($val['is_reback']==2){
					$val['tradetype'] = '订单退款';
				}else{
					$val['tradetype'] = '支付订单';
				}
				$val['paytime'] = date('Y-m-d H:i:s',$val['paytime']);
				$neworder1[] = $val;
			}
		}
		$order2 = $this->mysql->getarr("select id,paytime,paytype,sjcost as allcost,dno,paytype_name from ".Mysite::$app->config['tablepre']."shophuiorder ".$where2." and paystatus=1");
		if(!empty($order2)){
			foreach($order2 as $k=>$val){
				$val['paytypename'] = $paytypearr[$val['paytype_name']];
				$val['paytime'] = date('Y-m-d H:i:s',$val['paytime']);
				$val['tradetype'] = '支付订单';
				$neworder2[] = $val;
			}
		}
		$orderlist = array_merge($neworder1,$neworder2);
		foreach($orderlist as $k=>$value){
			$paytime[$k] = strtotime($value['paytime']);
		}
		array_multisort($paytime, SORT_DESC,$orderlist); 
		$starnum = $pageinfo->startnum();
		$pagesize = $pageinfo->getsize();
		for($k = 0;$k<$pagesize;$k++){
			$checknum = $starnum+$k;
			if(isset($orderlist[$checknum])){
				$templist[] = $orderlist[$checknum];
			}else{
				break;
			}
		}   
		$data['orderlist'] = $templist;
		$shuliang1 = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order ".$where." and paystatus=1 ");
		$shuliang2 = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shophuiorder ".$where2." and paystatus=1 ");
		$pageinfo->setnum($shuliang1+$shuliang2);
        $data['pagecontent'] = $pageinfo->getpagebar($link);
        Mysite::$app->setdata($data);
    }
	//导出交易记录
	function outtrade_log(){
        $dno = trim(IReq::get('dno'));
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');		
		$admin_id = intval(IReq::get('admin_id'));
		$tradetype = intval(IReq::get('tradetype'));
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$where2 = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		if(!empty($dno)){
			$where .= " and dno = '".$dno."' ";
			$where2 .= " and dno = '".$dno."' ";
		}
		if(!empty($tradetype)){
			if($tradetype==2){
				$where .= " and is_reback = 2 ";
			}	
		}
		$order1 = $this->mysql->getarr("select id,paytime,paytype,paytype_name,allcost,dno,is_reback from ".Mysite::$app->config['tablepre']."order ".$where." and paystatus=1");
		#print_r($order1);
		$neworder1 = array();
		$neworder2 = array();
		$paytypearr = array('open_acout'=>'余额支付','weixin'=>'微信支付','alipay'=>'支付宝支付','alimobile'=>'手机支付宝支付');
		if(!empty($order1)){
			foreach($order1 as $k=>$val){
				if($val['is_reback']==2){
					$drawinfo = $this->mysql->select_one("select addtime from ".Mysite::$app->config['tablepre']."drawbacklog where orderid = ".$val['id']." and  status = 2 order by  id desc ");	
					$val['paytime'] = $drawinfo['addtime'];//退款成功时间
				}
				if($val['paytype']==0){
					$val['paytypename'] = '货到付款';
				}else{
					$val['paytypename'] = $paytypearr[$val['paytype_name']];
				}
				if($val['is_reback']==2){
					$val['tradetype'] = '订单退款';
				}else{
					$val['tradetype'] = '支付订单';
				}
				$val['paytime'] = date('Y-m-d H:i:s',$val['paytime']);
				$neworder1[] = $val;
			}
		}
		$order2 = $this->mysql->getarr("select id,paytime,paytype,sjcost as allcost,dno,paytype_name from ".Mysite::$app->config['tablepre']."shophuiorder ".$where2." and paystatus=1 ");
		if(!empty($order2)){
			foreach($order2 as $k=>$val){
				$val['paytypename'] = $paytypearr[$val['paytype_name']];
				$val['paytime'] = date('Y-m-d H:i:s',$val['paytime']);
				$val['tradetype'] = '支付订单';
				$neworder2[] = $val;
			}
		}
		$orderlist = array_merge($neworder1,$neworder2);
		foreach($orderlist as $k=>$value){
			$paytime[$k] = strtotime($value['paytime']);
		}
		array_multisort($paytime, SORT_DESC,$orderlist); 
		#print_r($orderlist);exit;
		$list = $orderlist;
        $outexcel = new phptoexcel();
        $titledata = array('交易时间','交易类型','金额','支付方式','订单号');
        $titlelabel = array('paytime','tradetype','allcost','paytypename','dno');
        $outexcel->out($titledata,$titlelabel,$list,'','交易记录');
    }
	//分站统计
	function area_statisyic(){
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				#print_r(111);
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
			}
		}
#print_r($data['datetype']);
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$ordertype = intval(IReq::get('ordertype'));
        $newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' ';
		$data['ordertype'] = 0;
		if(!empty($ordertype)){
			$newlink .='/ordertype/'.$ordertype;
			$data['ordertype'] = $ordertype;
			if($ordertype==1){
				$where .= ' and shoptype != 100';
			}else if($ordertype==2){
				$where .= ' and shoptype = 100';
			}	
		}
        $link = IUrl::creatUrl('areaadminpage/analysis/module/area_statisyic'.$newlink);
        $data['outlink'] =IUrl::creatUrl('areaadminpage/analysis/module/outarea_statisyic'.$newlink);
		$stationinfo = $this->mysql->select_one("select name,adcode  from ".Mysite::$app->config['tablepre']."area where adcode = ".$this->admin['cityid']." ");
		
		$where1 = $where.' and admin_id ='.$this->admin['cityid'];
		$new['data'] = $this->getorddata($where1,$ordertype,0);
		$areadatalist = array(
					'name'=>$stationinfo['name'],
					'ordernum'=>$new['data']['ordernum'],
					'tradecost'=>$new['data']['tradecost'],
					'useordnum'=>$new['data']['useordnum'],
					'useordcost'=>$new['data']['useordcost'],
					'memnum'=>$new['data']['memnum'],
					'singlecost'=>$new['data']['singlecost']
				);
		$data['areadatalist'] = $areadatalist;
        Mysite::$app->setdata($data);
    }
	//导出分站统计
	function outarea_statisyic(){
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');		
		$ordertype = intval(IReq::get('ordertype'));
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' ';
		if(!empty($ordertype)){
			if($ordertype==1){
				$where .= ' and shoptype != 100';
			}else if($ordertype==2){
				$where .= ' and shoptype = 100';
			}	
		}
		$stationinfo = $this->mysql->getarr("select name,adcode  from ".Mysite::$app->config['tablepre']."area where adcode = ".$this->admin['cityid']." ");
		$where1 = $where.' and admin_id ='.$this->admin['cityid'];
		$new['data'] = $this->getorddata($where1,$ordertype,0);
		$areadatalist = array(
					'name'=>$stationinfo['name'],
					'ordernum'=>$new['data']['ordernum'],
					'tradecost'=>$new['data']['tradecost'],
					'useordnum'=>$new['data']['useordnum'],
					'useordcost'=>$new['data']['useordcost'],
					'memnum'=>$new['data']['memnum'],
					'singlecost'=>$new['data']['singlecost']
				);
		$list[0] = $areadatalist;
        $outexcel = new phptoexcel();
        $titledata = array('站点','订单总数','交易总金额','有效订单数','有效订单金额','下单会员数','单均价');
        $titlelabel = array('name','ordernum','tradecost','useordnum','useordcost','memnum','singlecost');
        $outexcel->out($titledata,$titlelabel,$list,'','站点交易统计');
    }
	
	//商家统计
	function shop_statisyic(){
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
			}
		}
		if($data['datetype']==2){
			$daytype=30;
			$data['daytype'] = 30;
		}else{
			$daytype=7;
			$data['daytype'] = 7;
		}
		$newwhere = '  and addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
		$where2 = ' and admin_id ='.$this->admin['cityid'];
#print_r($data['datetype']);
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        //趋势图数据
		$trenddata = $this->getdaydata($daytype,$where2,'shop');
		#print_r($trenddata);
		$data['trenddata'] = $trenddata;
		$oldshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($starttime.' 00:00:00')." ".$where2." ");
		$newshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$newshopnum = $newshop - $oldshop;
		 
		$data['newshopnum'] = empty($newshopnum)?0:$newshopnum;
        $link = IUrl::creatUrl('areaadminpage/analysis/module/shop_statisyic'.$newlink);
        $data['outlink1'] =IUrl::creatUrl('areaadminpage/analysis/module/outshop_statisyic'.$newlink);
		
		$allshopnum = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$data['allshopnum'] = empty($allshopnum)?0:$allshopnum;
		$fastshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and shoptype = 0 and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$data['fastshopnum'] = empty($fastshop)?0:$fastshop;
		$marketshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and shoptype = 1 and addtime <".strtotime($endtime.' 23:59:59')."  ".$where2." ");
		$data['marketshopnum'] = empty($marketshop)?0:$marketshop;		
		
		$shopdatalist = $this->getrankdata($newwhere,'shop');
		$newshopshuliang = count($shopdatalist);
		$shopnumtype = intval(IReq::get('shopnumtype'));
		$data['shopnumtype'] = 10;
		$slicenum = 10;
		if($newshopshuliang<10){
			$slicenum = $newshopshuliang;
		}
		if($shopnumtype==30){
			$slicenum = 30;
			if($newshopshuliang<30){
				$slicenum = $newshopshuliang;
			}
			$data['shopnumtype'] = 30;
		}else if($shopnumtype==50){
			$slicenum = 50;
			if($newshopshuliang<50){
				$slicenum = $newshopshuliang;
			}
			$data['shopnumtype'] = 50;
		}
		$newlink .= '/shopnumtype/'.$data['shopnumtype'];
		$data['outlink2'] =IUrl::creatUrl('areaadminpage/analysis/module/outshop_log'.$newlink);
		$data['shopdatalist'] = array_slice($shopdatalist,0,$slicenum);
        Mysite::$app->setdata($data);
    }
	//导出商家数量统计
	function outshop_statisyic(){
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$where2 = ' and admin_id ='.$this->admin['cityid'];
		$stationinfo = $this->mysql->select_one("select stationname from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid= ".$this->admin['cityid']." ");
		$data['areaname'] = $stationinfo['stationname'];	
		$oldshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($starttime.' 00:00:00')." ".$where2." ");
		$newshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$newshopnum = $newshop - $oldshop;
		 
		$data['newshopnum'] = empty($newshopnum)?0:$newshopnum;
        
		$allshopnum = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$data['allshopnum'] = empty($allshopnum)?0:$allshopnum;
		$fastshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and shoptype = 0 and addtime <".strtotime($endtime.' 23:59:59')." ".$where2." ");
		$data['fastshopnum'] = empty($fastshop)?0:$fastshop;
		$marketshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and shoptype = 1 and addtime <".strtotime($endtime.' 23:59:59')."  ".$where2." ");
		$data['marketshopnum'] = empty($marketshop)?0:$marketshop;	
		$list[0] = $data;
        $outexcel = new phptoexcel();
        $titledata = array('站点','开始时间','结束时间','商家总数量','外卖商家数量','超市商家数量','新增商家数量');
        $titlelabel = array('areaname','starttime','endtime','allshopnum','fastshopnum','marketshopnum','newshopnum');
        $outexcel->out($titledata,$titlelabel,$list,'','商家数量统计');
    }
	//导出商家交易额排行
	function outshop_log(){
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));	
		$newwhere = '  and addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$shopdatalist = $this->getrankdata($newwhere,'shop');
		$newshopshuliang = count($shopdatalist);
		$shopnumtype = intval(IReq::get('shopnumtype'));
		$slicenum = 10;
		if($newshopshuliang<10){
			$slicenum = $newshopshuliang;
		}
		if($shopnumtype==30){
			$slicenum = 30;
			if($newshopshuliang<30){
				$slicenum = $newshopshuliang;
			}
		}else if($shopnumtype==50){
			$slicenum = 50;
			if($newshopshuliang<50){
				$slicenum = $newshopshuliang;
			}
		}
		$shoplist = array_slice($shopdatalist,0,$slicenum);
		$list = array();
		if(!empty($shoplist)){
			foreach($shoplist as $k=>$val){
				$val['sort'] = $k+1;
				$list[] = $val;
			}
		}
		#print_R($list);exit;
        $outexcel = new phptoexcel();
        $titledata = array('排序','商家名称','订单总数','交易总金额','有效订单数','有效订单金额','下单会员数','单均价');
        $titlelabel = array('sort','shopname','ordernum','tradecost','useordnum','useordcost','memnum','singlecost');
        $outexcel->out($titledata,$titlelabel,$list,'','商家交易额排行');
    }
	//会员统计
	function mem_statisyic(){
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-14 day'));
			$endtime2 = date('Y-m-d',strtotime('-8 day'));
			$data['beforeday'] = 7;
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-60 day'));
			$endtime2 = date('Y-m-d',strtotime('-31 day'));
			$data['beforeday'] = 30;
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				#print_r(111);
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$starttime2 = date('Y-m-d',strtotime('-14 day'));
				$endtime2 = date('Y-m-d',strtotime('-8 day'));
				$data['beforeday'] = 7;
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
					
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
				$data['beforeday'] = (strtotime($endtime)-strtotime($starttime))/86400+1;
				$starttime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($starttime)));
				$endtime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($endtime)));	
			}
		}
		if($data['datetype']==2){
			$daytype=30;
			$data['daytype'] = 30;
		}else{
			$daytype=7;
			$data['daytype'] = 7;
		}
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
		$where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$where2 = '  where addtime > '.strtotime($starttime2.' 00:00:00').' and addtime < '.strtotime($endtime2.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$memwhere = ' and admin_id ='.$this->admin['cityid'];
		$newwhere= '  and addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		//趋势图数据
		$trenddata = $this->getdaydata($daytype,$memwhere,'member');
		#print_r($trenddata);
		$data['trenddata'] = $trenddata;
		//统计数据
		$oldmem1 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($starttime.' 23:59:59')." ".$memwhere." ");
		$newmem1 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($endtime.' 23:59:59')." ".$memwhere." ");
		$addmem1 = $newmem1 - $oldmem1;
		$oldmem2 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($starttime2.' 23:59:59')." ".$memwhere." ");
		$newmem2 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($endtime2.' 23:59:59')." ".$memwhere." ");
		$addmem2 = $newmem2 - $oldmem2;
		$data['allmem'] = $newmem1;//会员总数
		$data['addmem'] = $addmem1;//新增会员数
		//会员总数
		if($newmem1>=$newmem2){
			$data['membili'] = number_format((($newmem1-$newmem2)/$newmem2*100),2);
			if($newmem2==0){
				$data['membili'] = 100;	
			}
			$data['membilitype'] = 1;
		}else{
			$data['membili'] = number_format(((1-$newmem1/$newmem2)*100),2);
			$data['membilitype'] = 2;
		}
		//新增会员数
		if($addmem1>=$addmem2){
			
			$data['addmembili'] = number_format((($addmem1-$addmem2)/$addmem2*100),2);
			if($addmem2==0){
				$data['addmembili'] = 100;	
			}
			$data['addmembilitype'] = 1;
		}else{
			$data['addmembili'] = number_format(((1-$addmem1/$addmem2)*100),2);
			$data['addmembilitype'] = 2;
		}
		$nowdata = $this->getorddata($where,0,0);
		$olddata = $this->getorddata($where2,0,0);
		$data['makemem'] = $nowdata['memnum'];//下单会员数
		$ordmembili1 = empty($newmem1)?0:number_format(($nowdata['memnum']/$newmem1),2);//会员下单率
		$data['ordmem'] = $ordmembili1;
		$ordmembili2 = empty($newmem2)?0:number_format(($olddata['memnum']/$newmem2),2);
		//下单会员数
		if($nowdata['memnum']>=$olddata['memnum']){
			
			$data['makemembili'] = number_format((($nowdata['memnum']-$olddata['memnum'])/$olddata['memnum']*100),2);
			if($olddata['memnum']==0){
				$data['makemembili'] = 100;	
			}
			$data['makemembilitype'] = 1;
		}else{
			$data['makemembili'] = number_format(((1-$nowdata['memnum']/$olddata['memnum'])*100),2);
			$data['makemembilitype'] = 2;
		}
		//会员下单率
		if($ordmembili1>=$ordmembili2){
			$data['ordmembili'] = number_format((($ordmembili1-$ordmembili2)/$ordmembili2*100),2);
			if($ordmembili2==0){
				$data['ordmembili'] = 100;	
			}
			$data['ordmembilitype'] = 1;
		}else{
			$data['ordmembili'] = number_format(((1-$ordmembili1/$ordmembili2)*100),2);
			$data['ordmembilitype'] = 2;
		}
        $link = IUrl::creatUrl('areaadminpage/analysis/module/trade_statisyic'.$newlink);
        $data['outlink1'] =IUrl::creatUrl('areaadminpage/analysis/module/outmem_statisyic'.$newlink);
		
		$newmemlist = $this->getrankdata($newwhere,'member'); 
		#print_r($newmemlist);
		$newmemshuliang = count($newmemlist);
		$memnumtype = intval(IReq::get('memnumtype'));
		$data['memnumtype'] = 10;
		$slicenum = 10;
		if($newmemshuliang<10){
			$slicenum = $newmemshuliang;
		}
		if($memnumtype==30){
			$slicenum = 30;
			if($newmemshuliang<30){
				$slicenum = $newmemshuliang;
			}
			$data['memnumtype'] = 30;
		}else if($memnumtype==50){
			$slicenum = 50;
			if($newmemshuliang<50){
				$slicenum = $newmemshuliang;
			}
			$data['memnumtype'] = 50;
		}
		$newlink .= '/memnumtype/'.$data['memnumtype'];
		$data['outlink2'] =IUrl::creatUrl('areaadminpage/analysis/module/outmem_log'.$newlink);
		$data['memlist'] = array_slice($newmemlist,0,$slicenum);
        Mysite::$app->setdata($data);
    }
	//导出会员数量统计
	function outmem_statisyic(){
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$memwhere = ' and admin_id ='.$this->admin['cityid'];
		
		$stationinfo = $this->mysql->select_one("select stationname from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid= ".$this->admin['cityid']." ");
		$data['areaname'] = $stationinfo['stationname'];
			
		$oldmem1 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($starttime.' 23:59:59')." ".$memwhere." ");
		$newmem1 = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($endtime.' 23:59:59')." ".$memwhere." ");
		$addmem1 = $newmem1 - $oldmem1;
		$data['allmem'] = $newmem1;//会员总数
		$data['addmem'] = $addmem1;//新增会员数
		$nowdata = $this->getorddata($where,0,0);
		$data['makemem'] = $nowdata['memnum'];//下单会员数
		$ordmembili1 = empty($newmem1)?0:number_format(($nowdata['memnum']/$newmem1),2);//会员下单率
		$data['ordmem'] = $ordmembili1;
		$list[0] = $data;
        $outexcel = new phptoexcel();
        $titledata = array('站点','开始时间','结束时间','会员总数量','新增会员数','下单会员数','会员下单率');
        $titlelabel = array('areaname','starttime','endtime','allmem','addmem','makemem','ordmem');
        $outexcel->out($titledata,$titlelabel,$list,'','会员数量统计');
    }
	//导出会员消费额排行
	function outmem_log(){
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));	
		$where = '  and addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and admin_id ='.$this->admin['cityid'];
		$newwhere= '  where ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59').' and ord.admin_id ='.$this->admin['cityid'];
		$newwhere .= " and mem.creattime <".strtotime($endtime.' 23:59:59')." ";
		
		$newmemlist = $this->getrankdata($where,'member'); 
		#print_r($newmemlist);
		$newmemshuliang = count($newmemlist);
		$memnumtype = intval(IReq::get('memnumtype'));
		$slicenum = 10;
		if($newmemshuliang<10){
			$slicenum = $newmemshuliang;
		}
		if($memnumtype==30){
			$slicenum = 30;
			if($newmemshuliang<30){
				$slicenum = $newmemshuliang;
			}
		}else if($memnumtype==50){
			$slicenum = 50;
			if($newmemshuliang<50){
				$slicenum = $newmemshuliang;
			}
		}
		$memlist = array_slice($newmemlist,0,$slicenum);
		$list = array();
		if(!empty($memlist)){
			foreach($memlist as $k=>$val){
				$val['sort'] = $k+1;
				$list[] = $val;
			}
		}
        $outexcel = new phptoexcel();
        $titledata = array('排序','会员名称','订单总数','消费总金额','有效订单数','有效订单金额','退款金额','单均价');
        $titlelabel = array('sort','username','ordernum','tradecost','useordnum','useordcost','drawcost','singlecost');
        $outexcel->out($titledata,$titlelabel,$list,'','会员消费额排行');
    }
	//结算统计
	function js_statisyic(){
        $shopname = trim(IReq::get('shopname'));
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
		$datetype = intval(IReq::get('datetype'));
		if($datetype==1){
			$starttime =  date('Y-m-d',strtotime('-7 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-14 day'));
			$endtime2 = date('Y-m-d',strtotime('-8 day'));
			$data['beforeday'] = 7;
			$data['datetype'] = 1;
		}else if($datetype==2){
			$starttime = date('Y-m-d',strtotime('-30 day'));
			$endtime = date('Y-m-d',strtotime('-1 day'));
			$starttime2 = date('Y-m-d',strtotime('-60 day'));
			$endtime2 = date('Y-m-d',strtotime('-31 day'));
			$data['beforeday'] = 30;
			$data['datetype'] = 2;
		}else{
			$data['datetype'] = 3;
			if(empty($starttime) && empty($endtime)){
				#print_r(111);
				$starttime =  date('Y-m-d',strtotime('-7 day'));
				$endtime = date('Y-m-d',strtotime('-1 day'));
				$starttime2 = date('Y-m-d',strtotime('-14 day'));
				$endtime2 = date('Y-m-d',strtotime('-8 day'));
				$data['beforeday'] = 7;
				$data['datetype'] = 1;
			}else{
				if(((strtotime($endtime)-strtotime($starttime))/86400+1)==7){
					$data['datetype'] = 1;
					
				}else if(((strtotime($endtime)-strtotime($starttime))/86400+1)==30){
					$data['datetype'] = 2;
				}
				$data['beforeday'] = (strtotime($endtime)-strtotime($starttime))/86400+1;
				$starttime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($starttime)));
				$endtime2 = date('Y-m-d',strtotime('-'.$data['beforeday'].' day',strtotime($endtime)));	
			}
		}
#print_r($data['datetype']);
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $newlink = '/starttime/'.$starttime.'/endtime/'.$endtime;
		$jswhere = '  where sj.jstime > '.strtotime($starttime.' 00:00:00').' and sj.jstime < '.strtotime($endtime.' 23:59:59').' and ord.admin_id ='.$this->admin['cityid'];
		$jswhere2 = '  where sj.jstime > '.strtotime($starttime2.' 00:00:00').' and sj.jstime < '.strtotime($endtime2.' 23:59:59').' and ord.admin_id ='.$this->admin['cityid'];
		$data['shopname'] = '';
		if(!empty($shopname)){			
			$jswhere .= ' and ord.shopname like "%'.$shopname.'%" ';
			$jswhere2 .= ' and ord.shopname like "%'.$shopname.'%" ';
			$newlink .= '/shopname/'.$shopname;
			$data['shopname'] = $shopname;
		}
		#print_r($where);
        $link = IUrl::creatUrl('areaadminpage/analysis/module/js_statisyic'.$newlink);
        $data['outlink1'] =IUrl::creatUrl('areaadminpage/analysis/module/outjs_statisyic'.$newlink);
		$data['outlink2'] =IUrl::creatUrl('areaadminpage/analysis/module/outjs_log'.$newlink);
		//订单总金额
		$orderinfo1 = $this->mysql->select_one("select sum(allcost) as totalcost,sum(cxcost) as allcxcost,sum(ord.shopdowncost) as ptcxcost from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." ");
		$orderinfo2 = $this->mysql->select_one("select sum(allcost) as totalcost,sum(cxcost) as allcxcost from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere2." ");
		$totalcost1 = empty($orderinfo1['totalcost'])?0:$orderinfo1['totalcost'];
		$totalcost2 = empty($orderinfo2['totalcost'])?0:$orderinfo2['totalcost'];
		$data['totalcost'] = $totalcost1;
		$cxcost1 = empty($orderinfo1['allcxcost'])?0:$orderinfo1['allcxcost'];
		$cxcost2 = empty($orderinfo2['allcxcost'])?0:$orderinfo2['allcxcost'];
		$data['allcxcost'] = $cxcost1;
		$data['ptcxcost'] = empty($orderinfo1['ptcxcost'])?0:$orderinfo1['ptcxcost'];
		$data['shopcxcost'] = number_format(($data['allcxcost'] - $data['ptcxcost']),2);
		if($totalcost1>=$totalcost2){			
			$data['totalbili'] = number_format((($totalcost1-$totalcost2)/$totalcost2*100),2);
			if($totalcost2==0){
				$data['totalbili'] = 100;	
			}
			$data['totalbilitype'] = 1;
		}else{
			$data['totalbili'] = number_format(((1-$totalcost1/$totalcost2)*100),2);
			$data['totalbilitype'] = 2;
		}
		//活动补贴金额
		if($cxcost1>=$cxcost2){
			
			$data['cxbili'] = number_format((($cxcost1-$cxcost2)/$cxcost2*100),2);
			if($cxcost2==0){
				$data['cxbili'] = 100;	
			}
			$data['cxbilitype'] = 1;
		}else{
			$data['cxbili'] = number_format(((1-$cxcost1/$cxcost2)*100),2);
			$data['cxbilitype'] = 2;
		}
		#print_r($jswhere.'<br>');print_r($jswhere2.'<br>');
		$jsinfo1 = $this->mysql->select_one("select sum(acountcost) as jscost,sum(yjcost) as ordyj  from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." ");
		$jsinfo2 = $this->mysql->select_one("select sum(acountcost) as jscost,sum(yjcost) as ordyj  from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere2." ");
		$jscost1 = empty($jsinfo1['jscost'])?0:round($jsinfo1['jscost'],2);
		$jscost2 = empty($jsinfo2['jscost'])?0:round($jsinfo2['jscost'],2);
		#print_r($jscost1.'<br>');print_r($jscost2.'<br>');
		$data['jscost'] = $jscost1;
		$ordyj1 = empty($jsinfo1['ordyj'])?0:$jsinfo1['ordyj'];
		$ordyj2 = empty($jsinfo2['ordyj'])?0:$jsinfo2['ordyj'];
		$data['ordyj'] = $ordyj1;
		//结算金额
		if($jscost1 >= $jscost2){			
			$data['jsbili'] = number_format((($jscost1-$jscost2)/$jscost2*100),2);
			if($jscost2==0){
				$data['jsbili'] = 100;	
			}
			$data['jsbilitype'] = 1;
		}else{
			$data['jsbili'] = number_format(((1-$jscost1/$jscost2)*100),2);
			$data['jsbilitype'] = 2;
		}
		//佣金
		if($ordyj1>=$ordyj2){
			
			$data['ordyjbili'] = number_format((($ordyj1-$ordyj2)/$ordyj2*100),2);
			if($ordyj2==0){
				$data['ordyjbili'] = 100;	
			}
			$data['ordyjbilitype'] = 1;
		}else{
			$data['ordyjbili'] = number_format(((1-$ordyj1/$ordyj2)*100),2);
			$data['ordyjbilitype'] = 2;
		}
		$pageinfo = new page();
        $pageinfo->setpage(IReq::get('page'));
		#print_r($jswhere);
		$orderlist = $this->mysql->getarr("select ord.id,ord.shopname,ord.dno,ord.allcost,ord.cxcost,sj.acountcost,sj.yjcost,sj.jstime from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." order by jstime desc limit ".$pageinfo->startnum().", ".$pageinfo->getsize()." ");
		$newordlist = array();
		if(!empty($orderlist)){
			foreach($orderlist as $k=>$val){
				$val['jstime'] = date('Y-m-d H:i:s',$val['jstime']);
				$val['acountcost'] = number_format($val['acountcost'],2);
				$newordlist[] = $val;
			}
		}
		$data['jsordlist']= $newordlist;
		$shuliang  = $this->mysql->counts("select ord.id from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere."  ");
        $pageinfo->setnum($shuliang);
        $data['pagecontent'] = $pageinfo->getpagebar($link);
		#print_r($data);
        Mysite::$app->setdata($data);
    }
	// 点击明细展示订单详情
	function showdetail(){
		$id = intval(IReq::get('orderid'));
		 $orderinfo = $this->mysql->select_one("select ord.allcost,ord.cxcost,ord.shopcost,ord.bagcost,ord.shopps,ord.addpscost,ord.cxdet,ord.yhjcost,ord.scoredowncost,ord.shopdowncost,sj.yjcost,sj.yjb from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."shopjs as sj on sj.orderid = ord.id where ord.id = ".$id." ");
		 if(!empty($orderinfo['cxdet'])){
			$orderinfo['cxdet'] = unserialize($orderinfo['cxdet']); 
		 }
		 #print_r($orderinfo['cxdet']);
		 
		$data['orderinfo']  = $orderinfo;
		 Mysite::$app->setdata($data);
	}
	//导出结算统计
	function outjs_statisyic(){  
		$starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
        $admin_id = intval(IReq::get('admin_id'));
		$shopname = trim(IReq::get('shopname'));
		$data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
		$data['areaname'] = '全部';
		$data['shopname'] = '全部';
		$jswhere = '  where sj.jstime > '.strtotime($starttime.' 00:00:00').' and sj.jstime < '.strtotime($endtime.' 23:59:59').' and ord.admin_id ='.$this->admin['cityid'];

		$stationinfo = $this->mysql->select_one("select stationname from ".Mysite::$app->config['tablepre']."stationadmininfo where cityid= ".$this->admin['cityid']." ");
		$data['areaname'] = $stationinfo['stationname'];
		if(!empty($shopname)){
			$shopinfo = $this->mysql->select_one("select shopname from ".Mysite::$app->config['tablepre']."shop where shopname like '%".$shopname."%' ");
			$data['shopname'] = $shopinfo['shopname'];
			$jswhere .= ' and ord.shopname like "%'.$shopname.'%" ';
		}
		//订单总金额
		$orderinfo1 = $this->mysql->select_one("select sum(allcost) as totalcost,sum(cxcost) as allcxcost from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." ");
		$totalcost1 = empty($orderinfo1['totalcost'])?0:$orderinfo1['totalcost'];
		$data['totalcost'] = $totalcost1;
		$cxcost1 = empty($orderinfo1['allcxcost'])?0:$orderinfo1['allcxcost'];
		$data['allcxcost'] = $cxcost1;
		$jsinfo1 = $this->mysql->select_one("select sum(acountcost) as jscost,sum(yjcost) as ordyj  from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." ");
		$jscost1 = empty($jsinfo1['jscost'])?0:round($jsinfo1['jscost'],2);
		#print_r($jscost1.'<br>');print_r($jscost2.'<br>');
		$data['jscost'] = $jscost1;
		$ordyj1 = empty($jsinfo1['ordyj'])?0:$jsinfo1['ordyj'];
		$data['ordyj'] = $ordyj1;
		$list[0] = $data;
		#print_r($list);exit;
        $outexcel = new phptoexcel();
        $titledata = array('站点','店铺','开始时间','结束时间','订单总金额','商家结算金额','平台佣金收入','活动补贴金额');
        $titlelabel = array('areaname','shopname','starttime','endtime','totalcost','jscost','ordyj','allcxcost');
        $outexcel->out($titledata,$titlelabel,$list,'','结算统计');
    }
	//导出结算记录
	function outjs_log(){  
		$starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));		
        $admin_id = intval(IReq::get('admin_id'));
		$shopname = trim(IReq::get('shopname'));
		$jswhere = '  where sj.jstime > '.strtotime($starttime.' 00:00:00').' and sj.jstime < '.strtotime($endtime.' 23:59:59').' and ord.admin_id ='.$this->admin['cityid'];
		if(!empty($shopname)){
			$jswhere .= ' and ord.shopname like "%'.$shopname.'%" ';
		}
		$orderlist = $this->mysql->getarr("select ord.id,ord.shopname,ord.dno,ord.allcost,ord.cxcost,sj.acountcost,sj.yjcost,sj.jstime from ".Mysite::$app->config['tablepre']."shopjs as sj left join  ".Mysite::$app->config['tablepre']."order as ord on sj.orderid = ord.id  ".$jswhere." order by jstime desc");
		$newordlist = array();
		if(!empty($orderlist)){
			foreach($orderlist as $k=>$val){
				$val['jstime'] = date('Y-m-d H:i:s',$val['jstime']);
				$val['dno'] = '【'.$val['dno'].'】';
				$val['acountcost'] = number_format($val['acountcost'],2);
				$newordlist[] = $val;
			}
		}		 
		$list = $newordlist;
        $outexcel = new phptoexcel();
        $titledata = array('结算时间','订单号','商家名称','订单金额','优惠金额','订单佣金','结算金额');
        $titlelabel = array('jstime','dno','shopname','allcost','cxcost','yjcost','acountcost');
        $outexcel->out($titledata,$titlelabel,$list,'','结算记录');
    }
	function getdaydata($daytype,$where,$type){
		$xarr = array();
		$datearr = array();
		for($i=1;$i<=$daytype;$i++){
			$date = date('Y-m-d',strtotime('-'.$i.' day'));
			$date1 = date('m-d',strtotime('-'.$i.' day'));
			$xarr[] = "'".$date1."'";
			$datearr[] = $date;
		}
		$xarr = array_reverse($xarr);
		$datearr = array_reverse($datearr);
		
		$yarr = array();
		foreach($datearr as $k=>$val){
			if($type=='member'){
				$oldmem = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($val.' 00:00:01')." ".$where." ");
				$newmem = $this->mysql->counts("select uid  from ".Mysite::$app->config['tablepre']."member where `group` > 3  and creattime <".strtotime($val.' 23:59:59')." ".$where." ");
				$yarr[] = $newmem - $oldmem;
			}else if($type=='shop'){
				$oldshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($val.' 00:00:01')." ".$where." ");
				$newshop = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop where is_pass = 1  and addtime <".strtotime($val.' 23:59:59')." ".$where." ");
				$addshop = $newshop - $oldshop;
				$yarr[] = empty($addshop)?0:$addshop;
			}
		}
		$yarr = array_reverse($yarr);
		$xstr = implode(',',$xarr);
		$ystr = implode(',',$yarr);
		return array($xstr,$ystr);
	}
	function getrankdata($where,$type){
		if($type=='shop'){
			$id = 'shopid';
			$orderdet =  $this->mysql->getarr(" select shopid  from (select  shopid from ".Mysite::$app->config['tablepre']."order as ord where  ord.shopid > 0 ".$newwhere."  group by shopid  UNION   select shopid from ".Mysite::$app->config['tablepre']."shophuiorder as ord where  ord.shopid > 0 ".$where." group by shopid) as a   group by shopid ");
			if(!empty($orderdet) && count($orderdet) > 0){
				$idsstr = array();
				foreach($orderdet as $key=>$value){
					$idsstr[] = $value['shopid'];
				}
				$idsstr = join(',',$idsstr); //店铺id集
				$namearr = $this->mysql->getarr("select shopname as name,id as shopid from ".Mysite::$app->config['tablepre']."shop where find_in_set(id,'".$idsstr."')  ");
				#print_r($namearr);
				//外卖订单
				$order1arr = $this->mysql->getarr("select count(id) as ordernum1,sum(allcost) as ordcost1,shopid from ".Mysite::$app->config['tablepre']."order where find_in_set(shopid,'".$idsstr."') ".$where." group by shopid ");//订单总数
				$useord1arr = $this->mysql->getarr("select count(id) as useordnum1,sum(allcost) as useordcost1,shopid from ".Mysite::$app->config['tablepre']."order where status = 3 and find_in_set(shopid,'".$idsstr."') ".$where." group by shopid ");//有效订单
				$memnum1arr = $this->mysql->getarr("select count(distinct  buyeruid ) as shuliang ,shopid from ".Mysite::$app->config['tablepre']."order where find_in_set(shopid,'".$idsstr."') and status >0 ".$where." group by shopid  ");//下单会员数
				$drawcost1arr = $this->mysql->getarr("select sum(allcost) as drawcost,shopid from ".Mysite::$app->config['tablepre']."order where find_in_set(shopid,'".$idsstr."') and is_reback=2 ".$where." group by shopid  ");//退款订单
				$trade1arr = $this->mysql->getarr("select sum(allcost) as tradecost1,count(id) as tradenum1,shopid from ".Mysite::$app->config['tablepre']."order where find_in_set(shopid,'".$idsstr."') and ((paytype=0 and is_make=1) or (paytype=1 and paystatus = 1)) ".$where." group by shopid ");//交易订单
				//闪惠订单
				$order2arr = $this->mysql->getarr("select count(id) as ordernum2,sum(sjcost) as ordcost2,shopid from ".Mysite::$app->config['tablepre']."shophuiorder where find_in_set(shopid,'".$idsstr."') ".$where."  group by shopid  ");
				$useord2arr = $this->mysql->getarr("select count(id) as useordnum2,sum(sjcost) as useordcost2,shopid from ".Mysite::$app->config['tablepre']."shophuiorder where find_in_set(shopid,'".$idsstr."') and paystatus = 1 ".$where." group by shopid ");
				$memnum2arr = $this->mysql->getarr("select count(distinct  uid ) as shuliang ,shopid from ".Mysite::$app->config['tablepre']."shophuiorder where find_in_set(shopid,'".$idsstr."') ".$where."  group by shopid  ");
				$trade2arr = $useord2arr;
			}
		
		}else if($type=='member'){
			$id='buyeruid';
			$orderdet =  $this->mysql->getarr(" select buyeruid  from (select  buyeruid from ".Mysite::$app->config['tablepre']."order as ord where  ord.buyeruid > 0 ".$newwhere."  group by buyeruid  UNION   select uid as buyeruid from ".Mysite::$app->config['tablepre']."shophuiorder as ord where  ord.uid > 0 ".$where." group by uid) as a   group by buyeruid ");
			if(!empty($orderdet) && count($orderdet) > 0){
				$idsstr = array();
				foreach($orderdet as $key=>$value){
					$idsstr[] = $value['buyeruid'];
				}
				$idsstr = join(',',$idsstr); //会员id集
				$namearr = $this->mysql->getarr("select username as name,uid as buyeruid from ".Mysite::$app->config['tablepre']."member where find_in_set(uid,'".$idsstr."')  ");
				//外卖订单
				$order1arr = $this->mysql->getarr("select count(id) as ordernum1,sum(allcost) as ordcost1,buyeruid from ".Mysite::$app->config['tablepre']."order where find_in_set(buyeruid,'".$idsstr."') ".$where." group by buyeruid ");//订单总数
				$useord1arr = $this->mysql->getarr("select count(id) as useordnum1,sum(allcost) as useordcost1,buyeruid from ".Mysite::$app->config['tablepre']."order where status = 3 and find_in_set(buyeruid,'".$idsstr."') ".$where." group by buyeruid ");//有效订单
				$drawcost1arr = $this->mysql->getarr("select sum(allcost) as drawcost,buyeruid from ".Mysite::$app->config['tablepre']."order  where find_in_set(buyeruid,'".$idsstr."') and is_reback=2 ".$where." group by buyeruid  ");//退款
				$trade1arr = $this->mysql->getarr("select sum(allcost) as tradecost1,count(id) as tradenum1,buyeruid from ".Mysite::$app->config['tablepre']."order where find_in_set(buyeruid,'".$idsstr."') and ((paytype=0 and is_make=1) or (paytype=1 and paystatus = 1)) ".$where." group by buyeruid ");//交易
				//闪惠订单
				$order2arr = $this->mysql->getarr("select count(id) as ordernum2,sum(sjcost) as ordcost2,uid from ".Mysite::$app->config['tablepre']."shophuiorder where find_in_set(uid,'".$idsstr."') ".$where."  group by uid  ");
				$useord2arr = $this->mysql->getarr("select count(id) as useordnum2,sum(sjcost) as useordcost2,uid from ".Mysite::$app->config['tablepre']."shophuiorder where find_in_set(uid,'".$idsstr."') and paystatus = 1 ".$where." group by uid ");
				$trade2arr = $useord2arr;
			}
		} 	
		if(!empty($orderdet) && count($orderdet) > 0){
			$newnamearr = array();
			foreach($namearr as $key=>$value){
				$newnamearr[$value[$id]] = $value;//名称数组
				#print_r($newnamearr);
			}
			$orderarr1 = array();
			foreach($order1arr as $key=>$value){
				$orderarr1[$value[$id]] = $value;//外卖总订单数组
			} 
			$useorderarr1 = array();
			foreach($useord1arr as $key=>$value){
				$useorderarr1[$value[$id]] = $value;//外卖有效订单数组
			} 
			if($type!='member'){
				$memnumarr1 = array();
				foreach($memnum1arr as $key=>$value){
					$memnumarr1[$value[$id]] = $value;//外卖用户购买次数数组
				} 
				$memnumarr2 = array();
				foreach($memnum2arr as $key=>$value){
					$memnumarr2[$value[$id]] = $value;//闪惠买单用户数量统计
				}
			}else{
				$drawcostarr1 = array();
				foreach($drawcost1arr as $key=>$value){
					$drawcostarr1[$value[$id]] = $value; //外卖退款金额数组 
				} 	
			}
			$tradearr1 = array();
			foreach($trade1arr as $key=>$value){
				$tradearr1[$value[$id]] = $value;//外卖交易金额
			}
					 
			$orderarr2 = array();
			foreach($order2arr as $key=>$value){
				$orderarr2[$value[$id]] = $value;//闪惠买单总订单数组
			}
			$useorderarr2 = array();
			foreach($useord2arr as $key=>$value){
				$useorderarr2[$value[$id]] = $value;//闪惠买单有效统计数组(也是闪惠交易)
			}		
			$tradearr2 = $useorderarr2;
			
			//订单总数[所有交易包含关闭的]	交易总金额【在线支付，已经支付，货到支付，商家接单】	有效订单数【订单完成】	有效订单金额【订单完成】	下单会员数	单均价
			$datalist = array();
			foreach($orderdet as $key=>$value){
				$newdata = array();
				$newdata['name'] = isset($newnamearr[$value[$id]]['name'])?$newnamearr[$value[$id]]['name']:'';
				//外卖订单数+优惠买单总订单数 
				$waimaizongshu = isset($orderarr1[$value[$id]])?$orderarr1[$value[$id]]['ordernum1']:0;
				$youhuizhongshu = isset($orderarr2[$value[$id]])?$orderarr2[$value[$id]]['ordernum2']:0;  
				$newdata['ordernum'] =$waimaizongshu +$youhuizhongshu;
				//外卖订单+优惠买单总金额 
				$waimaizongjine = isset($orderarr1[$value[$id]])?$orderarr1[$value[$id]]['ordcost1']:0;
				$youhuizongjine =  isset($orderarr2[$value[$id]])?$orderarr2[$value[$id]]['ordcost2']:0; 
				$newdata['ordcost'] = round(($waimaizongjine+$youhuizongjine),2);
				//有效订单金额
				$waimaiyouxiao = isset($useorderarr1[$value[$id]])?$useorderarr1[$value[$id]]['useordcost1']:0;
				$youhuiyouxiao = isset($useorderarr2[$value[$id]])?$useorderarr2[$value[$id]]['useordcost2']:0;
				$newdata['useordcost'] =  round(($waimaiyouxiao+$youhuiyouxiao),2);
				//有效订单数
				$waimaiyouxiaoshu = isset($useorderarr1[$value[$id]])?$useorderarr1[$value[$id]]['useordnum1']:0;
				$youhuiyouxiaoshu = isset($useorderarr2[$value[$id]])?$useorderarr2[$value[$id]]['useordnum2']:0;
				$newdata['useordnum'] = round(($waimaiyouxiaoshu+$youhuiyouxiaoshu),2);
				//无效订单数，无效订单金额
				$newdata['nouseordnum'] = $newdata['ordernum'] - $newdata['useordnum'];
				$newdata['nouseordcost'] = round(($newdata['ordernum'] - $newdata['useordcost']),2);
				//交易总金额
				$waimaijiaoyi = isset($tradearr1[$value[$id]])?$tradearr1[$value[$id]]['tradecost1']:0;
				$youhuijiaoyi =  isset($tradearr2[$value[$id]])?$tradearr2[$value[$id]]['useordcost2']:0;
				$newdata['tradecost'] = round(($waimaijiaoyi+$youhuijiaoyi),2);
				if($type=='member'){
					$newdata['drawcost'] = isset($drawcostarr1[$value[$id]])?round($drawcostarr1[$value[$id]]['drawcost'],2):0;
				}else{
					//下单会员数
					$waimaiyonhuidshu =isset($memnumarr1[$value[$id]])?$memnumarr1[$value[$id]]['shuliang']:0; 
					$youhuiyonghuidshu = isset($memnumarr2[$value[$id]])?$memnumarr2[$value[$id]]['shuliang']:0; 
					$newdata['memnum'] = $waimaiyonhuidshu+$youhuiyonghuidshu;
				}
				//单均价
				$newdata['singlecost'] = empty($newdata['ordernum'])?0:round($newdata['ordcost']/$newdata['ordernum'],2);
				$datalist[] = $newdata;
			} 
		} 
		foreach($datalist as $k=>$value){
			$tradecost[$k] = $value['tradecost'];
			$ordernum[$k] = $value['ordernum'];
			$useordcost[$k] = $value['useordcost'];
			$useordnum[$k] = $value['useordnum'];
		}
		array_multisort($tradecost, SORT_DESC,$ordernum, SORT_DESC,$useordcost, SORT_DESC,$useordnum, SORT_DESC, $datalist);
		return $datalist;
	}
}
?>