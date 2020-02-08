<?php
class method   extends adminbaseclass
{
    function area(){
        $selecttype = intval(IFilter::act(IReq::get('selecttype')));
        $tempselecttype = in_array($selecttype,array(0,1,2,3))?$selecttype:0;

        $wherearray = array(
            '0'=>'',
            '1'=>'   addtime > '.strtotime('-1 month'),
            '2'=>'  addtime > '.strtotime('-7 day'),
            '3'=>'  addtime > '.strtotime(date('Y-m-d',time()))
        );




        $arealist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."area where parent_id=0");

        $total = 0;
        $nowdata = array();
        foreach($arealist as $key=>$value){
        // FIND_IN_SET('".$firstareain."',`areaids`)
            $where = empty($wherearray[$tempselecttype])? " where admin_id=".$value['adcode']." " : "  where admin_id=".$value['adcode']." and ".$wherearray[$tempselecttype]." ";

      // $where = empty($wherearray[$tempselecttype])? "  where admin_id=".$value['adcode']." and ".$wherearray[$tempselecttype]." ":  " where admin_id=".$value['adcode']." " ;

     


            $value['shuliang'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order ".$where."    ");

            $nowdata[] = $value;
            $total = $total+$value['shuliang'];

        }
        $data['total'] = $total;
        $data['allshu'] = count($arealist);
        $data['arealist'] = $nowdata;
        $data['selecttype'] = $selecttype;
        Mysite::$app->setdata($data);
    }
    function shop(){
        //店铺统计
        $selecttype = intval(IFilter::act(IReq::get('selecttype')));
        $tempselecttype = in_array($selecttype,array(0,1,2,3))?$selecttype:0;
        $wherearray = array(
            '0'=>'',
            '1'=>'  addtime > '.strtotime('-1 month'),
            '2'=>'  addtime > '.strtotime('-7 day'),
            '3'=>'  addtime > '.strtotime(date('Y-m-d',time()))
        );
        $where1 = empty($wherearray[$tempselecttype]) ? '':' where '.$wherearray[$tempselecttype];
        $where2 = empty($wherearray[$tempselecttype]) ? '':' and '.$wherearray[$tempselecttype];

        $orderlist = $this->mysql->getarr("select count(id) as shuliang ,shopid from ".Mysite::$app->config['tablepre']."order  ".$where1."   group by shopid   order by shuliang desc  limit 0,11");
        $data['list'] = array();
        $data['newdata'] = array();
        foreach($orderlist as $key=>$value){
            if($value['shopid'] > 0){

                $shopinfo = $this->mysql->select_one("select  shopname,id from ".Mysite::$app->config['tablepre']."shop  where id=".$value['shopid']." ");
                //  $value['det'] = $this->mysql->getarr("select count(id) as shuliang ,DATE_FORMAT(FROM_UNIXTIME(`addtime`),'%e') as month from ".Mysite::$app->config['tablepre']."order where addtime > ".$mintime." and shopid =".$value['shopid']." group by month    order by month desc  limit 0,10");
                $value['det'] = $this->mysql->getarr("select count(id) as shuliang ,shopid from ".Mysite::$app->config['tablepre']."order where  shopid =".$value['shopid']." ".$where2."  order by id desc  limit 0,11");
                $value['shopname'] = isset($shopinfo['shopname'])? $shopinfo['shopname']:'不存在';

                $data['list'][] = $value;

            }
        }

        $timearr= array(
            '0'=>'所有时间',
            '1'=>'最近一月',
            '2'=>'最近一周',
            '3'=>'当天'
        );

        $data['typeshow'] = $timearr[$tempselecttype];
        $data['selecttype'] = $selecttype;
        Mysite::$app->setdata($data);

    }

    function goods(){
        //店铺统计
        $selecttype = intval(IFilter::act(IReq::get('selecttype')));
        // $tempselecttype = in_array($selecttype,array(0,1,2,3))?$selecttype:0;
        $wherearray = array(
            '0'=>'',
            '1'=>'  ord.addtime > '.strtotime('-1 month'),
            '2'=>'  ord.addtime > '.strtotime('-7 day'),
            '3'=>'  ord.addtime > '.strtotime(date('Y-m-d',time()))
        );
        $where1 = empty($wherearray[$selecttype]) ? '':' where '.$wherearray[$selecttype];
        $where2 =  empty($wherearray[$selecttype]) ? '':' and '.$wherearray[$selecttype];
        $where1 .= ' and ord.status = 3  and is_reback = 0 ';
        $data['list']= $this->mysql->getarr("select count(ordet.id) as shuliang ,ordet.goodsid,ordet.goodsname as shopname from ".Mysite::$app->config['tablepre']."orderdet  as ordet left join  ".Mysite::$app->config['tablepre']."order as ord on ordet.order_id = ord.id  ".$where1." group by ordet.goodsid   order by shuliang desc  limit 0,5");

        $data['selecttype'] = $selecttype;
        Mysite::$app->setdata($data);

    }
    function user(){
        //店铺统计

        $selecttype = intval(IFilter::act(IReq::get('selecttype')));
        // $tempselecttype = in_array($selecttype,array(0,1,2,3))?$selecttype:0;
        
        $wherearray = array(
            '0'=>'',
            '1'=>' where addtime > '.strtotime('-1 month'),
            '2'=>' where addtime > '.strtotime('-7 day'),
            '3'=>' where addtime > '.strtotime(date('Y-m-d',time()))
        );
         $tempdata =   $this->mysql->getarr(" SELECT count(id) as shuliang ,DATE_FORMAT(FROM_UNIXTIME(`addtime`),'%k') as month FROM ".Mysite::$app->config['tablepre']."order  ".$wherearray[$selecttype]." GROUP BY month ");
		 #print_r($tempdata);
        $list = array();
        if(is_array($tempdata)){
            foreach($tempdata as $key=>$value){
                $list[$value['month']] = $value['shuliang'];
            }
        }
        $data['list'] = $list;
        $data['selecttype'] = $selecttype;
        Mysite::$app->setdata($data);

    }
    function ordertotal()
    {
        $data['buyerstatus'] = array(
            '0'=>'待处理订单',
            '1'=>'审核通过,待发货',
            '2'=>'订单已发货',
            '3'=>'订单完成',
            '4'=>'买家取消订单',
            '5'=>'卖家取消订单'
        );
        $querytype = IReq::get('querytype');
        $searchvalue = IReq::get('searchvalue');
        $orderstatus = intval(IReq::get('orderstatus'));
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');
        $nowday = date('Y-m-d',time());
        $starttime = empty($starttime)? $nowday:$starttime;
        $endtime = empty($endtime)? $nowday:$endtime;
        $where = '  where ord.suretime > '.strtotime($starttime.' 00:00:00').' and ord.suretime < '.strtotime($endtime.' 23:59:59');
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['querytype'] = '';
        $data['searchvalue'] = '';
        if(!empty($querytype))
        {
            if(!empty($searchvalue)){
                $data['searchvalue'] = $searchvalue;
                $where .= ' and '.$querytype.' =\''.$searchvalue.'\' ';
                $data['querytype'] = $querytype;
            }
        }

        $data['list'] = $this->mysql->getarr("select count(ord.id) as shuliang,ord.status,sum(allcost) as allcost,sum(scoredown) as scorecost from ".Mysite::$app->config['tablepre']."order as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.buyeruid   ".$where." group by ord.status order by ord.id desc limit 0, 10");
 
        Mysite::$app->setdata($data);
    }
	
	function paotuiorder()
    {
        
        $querytype = IReq::get('querytype');
        $searchvalue = IReq::get('searchvalue');
        
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');
        $nowday = date('Y-m-d',time());
        $starttime = empty($starttime)? $nowday:$starttime;
        $endtime = empty($endtime)? $nowday:$endtime;
        $where = '  where addtime > '.strtotime($starttime.' 00:00:00').' and addtime < '.strtotime($endtime.' 23:59:59').' and shoptype = 100 ';
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['querytype'] = '';
        $data['searchvalue'] = '';
        if(!empty($querytype))
        {
            if(!empty($searchvalue)){
                $data['searchvalue'] = $searchvalue;
                $where .= ' and '.$querytype.' =\''.$searchvalue.'\' ';
                $data['querytype'] = $querytype;
            }
        } 
		  
        $data['list'] = $this->mysql->getarr("select count(id) as shuliang,pttype,sum(allcost) as allcost from ".Mysite::$app->config['tablepre']."order ".$where." group by pttype  ");
        
        Mysite::$app->setdata($data);
    }
	
	 
    function orderyjin()
    {
        $searchvalue = IReq::get('searchvalue');
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));

        $quyuguanli = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."stationadmininfo  limit 0,1000");
        $data['quyuguanli'] = $quyuguanli;

        $newlink = '';
        $where= '';
        $where2 = '';
		$where3='';
        $data['searchvalue'] = '';
        if(!empty($searchvalue))
        {
            $data['searchvalue'] = $searchvalue;
            $where .= ' where shopname = \''.$searchvalue.'\' ';
            $newlink .= '/searchvalue/'.$searchvalue;
        }
        $data['starttime'] = '';
        if(!empty($starttime))
        {
            $data['starttime'] = $starttime;
            $where2 .= ' and  suretime > '.strtotime($starttime.' 00:00:01').' ';
			$where3 .= ' and  jstime > '.strtotime($starttime.' 00:00:01').' ';
            $newlink .= '/starttime/'.$starttime;
        }
        $data['endtime'] = '';
        if(!empty($endtime))
        {
            $data['endtime'] = $endtime;
            $where2 .= ' and  suretime < '.strtotime($endtime.' 23:59:59').' ';
			$where3 .= ' and  jstime < '.strtotime($endtime.' 23:59:59').' ';
            $newlink .= '/endtime/'.$endtime;
        }

        $admin_id = intval(IReq::get('admin_id'));
        if(!empty($admin_id)){
            $where .=empty($where)?' where admin_id = \''.$admin_id.'\'': ' and admin_id = \''.$admin_id.'\' ';
            $newlink .= '/admin_id/'.$admin_id;
        }

        $data['admin_id'] = $admin_id;

        $link = IUrl::creatUrl('adminpage/analysis/module/orderyjin'.$newlink);
        $data['outlink'] =IUrl::creatUrl('adminpage/analysis/module/outtjorder/outtype/query'.$newlink);
        $data['outlinkch'] =IUrl::creatUrl('adminpage/analysis/module/outtjorder'.$newlink);
        $pageinfo = new page();
        $pageinfo->setpage(IReq::get('page'));
        $shoplist = $this->mysql->getarr("select id,shopname,yjin,shoptype from ".Mysite::$app->config['tablepre']."shop ".$where."   order by id asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
        $list = array();
        if(is_array($shoplist))
        {
            foreach($shoplist as $key=>$value)
            {
                //判断店铺配送类型
                if($value['shoptype'] ==0){
                    $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopfast","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""
                }elseif($value['shoptype'] ==  1){
                    $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopmarket","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""
                }
                if($sendtype == 1){
					$value['sendtype'] = '店铺配送';
				}else{
					$value['sendtype'] = '平台配送';
				} 
                //货到支付				
                $shoptj=  $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(shopdowncost) as shopdowncost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
                //在线支付
				$line  =  $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(shopdowncost) as shopdowncost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype !=0  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

                $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
                $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $line['score']/Mysite::$app->config['scoretocost']:0;
                $value['onlinescore'] = $scordedown;
                $value['online'] = $line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
                $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $shoptj['score']/Mysite::$app->config['scoretocost']:0;
                $value['unlinescore'] = $scordedown;
                $value['unline'] = $shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown;
                $value['yhjcost'] = $line['yhcost'] +$shoptj['yhcost'];//使用优惠券
                $value['cxcost'] = $line['cxcost'] +$shoptj['cxcost'];// 总优惠
				$value['ptcxcost'] = $line['shopdowncost'] +$shoptj['shopdowncost'];//总优惠金额中  平台承担的部分
				$value['shopcxcost'] = $value['cxcost'] - $value['ptcxcost'];//总优惠金额中  商家承担的部分
                $value['score'] = $value['unlinescore'] +$value['onlinescore']; //  使用积分
                $value['bagcost'] = $line['bagcost'] +$shoptj['bagcost'];//   打包费
                $value['pscost'] = $line['pscost'] +$shoptj['pscost'];//   配送费
                $value['allcost'] = $line['shopcost'] +$shoptj['shopcost'] - $value['cxcost'];
                $value['goodscost'] = $line['shopcost'] +$shoptj['shopcost'];//商品总价	
                $jsinfo= $this->mysql->select_one("select sum(yjcost) as yjcost,sum(acountcost) as acountcost from ".Mysite::$app->config['tablepre']."shopjs  where shopid ='".$value['id']."'".$where3."  ");
				#print_r($jsinfo);
				if(!empty($jsinfo)){
					$value['yje']=$jsinfo['yjcost'];
					$value['jse']=$jsinfo['acountcost'];
				}else{
					$value['yje']='未结算';
					$value['jse']='未结算';
				}
				
				
                #$value['yje'] = $value['yb']*$value['allcost'];
                $value['outdetail'] =IUrl::creatUrl('adminpage/analysis/module/outdetail/outtype/query/shopid/'.$value['id'].$newlink);
                $list[] = $value;
            }
        }

        $data['list'] =$list;

        $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop ".$where."  ");
        $pageinfo->setnum($shuliang);
        $data['pagecontent'] = $pageinfo->getpagebar($link);
        Mysite::$app->setdata($data);
    }



    function areadtoji(){
        $searchvalue = IReq::get('searchvalue');
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));
        $admin_id = intval(IReq::get('admin_id'));
        $newlink = '';
        $where= ' where `groupid`=4';
        $where2 = '';
        $data['searchvalue'] = '';
        if(!empty($searchvalue))
        {
            $data['searchvalue'] = $searchvalue;
            $where .= ' and username = \''.$searchvalue.'\' ';
            $newlink .= '/searchvalue/'.$searchvalue;
        }
        $data['starttime'] = '';
        if(!empty($starttime))
        {
            $data['starttime'] = $starttime;
            $where2 .= ' and  suretime > '.strtotime($starttime.' 00:00:01').' ';
            $newlink .= '/starttime/'.$starttime;
        }
        $data['endtime'] = '';
        if(!empty($endtime))
        {
            $data['endtime'] = $endtime;
            $where2 .= ' and  suretime < '.strtotime($endtime.' 23:59:59').' ';
            $newlink .= '/endtime/'.$endtime;
        }

        $link = IUrl::creatUrl('adminpage/analysis/module/areadtoji'.$newlink);
        $data['outlink'] =IUrl::creatUrl('adminpage/analysis/module/outareatjorder/outtype/query'.$newlink);
        $data['outlinkch'] =IUrl::creatUrl('adminpage/analysis/module/outareatjorder'.$newlink);
        $pageinfo = new page();
        $pageinfo->setpage(IReq::get('page'));
        $memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");

        $list = array();
        if(is_array($memberlist))
        {
            foreach($memberlist as $key=>$value)
            {
                //判断店铺配送类型
				$cityids=  $this->mysql->select_one("select cityid from ".Mysite::$app->config['tablepre']."stationadmininfo where uid=".$value['uid']." ");
                // $value['sendtype'] = empty($sendtype)?'网站配送':'自送';
                $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$cityids['cityid']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
                $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$cityids['cityid']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

                $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数

                $value['online'] = $line['doallcost'];//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额

                $value['unline'] = $shoptj['doallcost'];//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown;
                $list[] = $value;
            }
        }

        $data['list'] =$list;
        //print_r($data);
        $shuliang  = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."admin ".$where."  ");
        $pageinfo->setnum($shuliang);
        $data['pagecontent'] = $pageinfo->getpagebar($link);
        Mysite::$app->setdata($data);


    }
    function outareatjorder()
    {
        $outtype = IReq::get('outtype');
        if(!in_array($outtype,array('query','ids')))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '查询条件错误';
            exit;
        }
        $where = '';
        $where2 = '';
        if($outtype == 'ids')
        {
            $id = trim(IReq::get('id'));
            if(empty($id))
            {
                header("Content-Type: text/html; charset=UTF-8");
                echo '查询条件不能为空';
                exit;
            }
            $doid = explode('-',$id);
            $id = join(',',$doid);
            $where .= ' where uid in('.$id.') ';

            $searchvalue = trim(IReq::get('searchvalue'));
            $where .= !empty($searchvalue)? ' and username = \''.$searchvalue.'\'':'';
            //   $data['searchvalue'] = $searchvalue;
            //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

            $starttime = trim(IReq::get('starttime'));
            $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';

            $endtime = trim(IReq::get('endtime'));
            $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';

        }else{
            $searchvalue = trim(IReq::get('searchvalue'));
            $where .= !empty($searchvalue)? ' where username = \''.$searchvalue.'\'':'';
            //   $data['searchvalue'] = $searchvalue;
            //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

            $starttime = trim(IReq::get('starttime'));
            $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';

            $endtime = trim(IReq::get('endtime'));
            $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
            $admin_id = intval(IReq::get('admin_id'));
            if(!empty($admin_id)){
                $where .= !empty($where)? ' and admin_id =\''.$admin_id.'\'':' where admin_id =\''.$admin_id.'\'';
            }
        }
        $where.= empty($where)?' where `groupid`=4 ':' and `groupid`=4 ';
        $memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."admin ".$where."   order by uid asc  limit 0,2000 ");
        $list = array();
        if(is_array($memberlist))
        {
            foreach($memberlist as $key=>$value)
            {

                $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =0  and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
                $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost from ".Mysite::$app->config['tablepre']."order  where admin_id = '".$value['uid']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");

                $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数

                $value['online'] = !empty($line['doallcost'])?$line['doallcost']:0;//$line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
          
                $value['unline'] = !empty($shoptj['doallcost'])?$shoptj['doallcost']:0;//$shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown;
      

                $list[] = $value;
                

            }
        }
    
                 // $list['online'])  = empty($list['online'])?$list['online']:0;
                 // $list['unline']   = empty($list['unline'])?$list['unline']:0;

        $outexcel = new phptoexcel();
        $titledata = array('区域管理员','订单总数','线上交易金额','线下交易金额');
        $titlelabel = array('username','orderNum','online','unline');
        // $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
        $outexcel->out($titledata,$titlelabel,$list,'','区域管理员结算');
    }


    function tjshophui()
    {
        $data['buyerstatus'] = array(
            '0'=>'未完成',
            '1'=>'已完成',

        );
        $querytype = IReq::get('querytype');
        $data['querytype'] = $querytype;
        $searchvalue = IReq::get('searchvalue');
        $data['searchvalue'] = $searchvalue;
        $orderstatus = intval(IReq::get('orderstatus'));
        $starttime = IReq::get('starttime');
        $endtime = IReq::get('endtime');
        $nowday = date('Y-m-d',time());
        $starttime = empty($starttime)? $nowday:$starttime;
        $endtime = empty($endtime)? $nowday:$endtime;
        $where = '  where ord.addtime > '.strtotime($starttime.' 00:00:00').' and ord.addtime < '.strtotime($endtime.' 23:59:59');
        $data['starttime'] = $starttime;
        $data['endtime'] = $endtime;
        $data['querytype'] = '';
        $data['searchvalue'] = '';
        
        if($querytype == 'ord.shopname' || $querytype == 'ord.username'){
            $data['searchvalue'] = $searchvalue;
            $where .= ' and '.$querytype.' =\''.$searchvalue.'\' ';
            $data['querytype'] = $querytype;
        }
        
        
        if($querytype == 'ord.status' && $searchvalue == 0 ){
            $tmpwhere = 'and ord.status = 0';
            $data['querytype'] = $querytype;
            $data['searchvalue'] = $searchvalue;
        }elseif($querytype == 'ord.status' && $searchvalue == 1 ){
            $tmpwhere = 'and ord.status = 1';
            $data['querytype'] = $querytype;
            $data['searchvalue'] = $searchvalue;
        }
        



        $data['list'] = $this->mysql->getarr("select count(ord.id) as shuliang,ord.status,ord.shopname,ord.username,ord.huiname,sum(xfcost) as xfcost,sum(yhcost) as yhcost,sum(sjcost) as sjcost from ".Mysite::$app->config['tablepre']."shophuiorder as ord left join  ".Mysite::$app->config['tablepre']."member as mb on mb.uid = ord.uid   ".$where." ".$tmpwhere." group by ord.status order by ord.id desc limit 0, 10");
        Mysite::$app->setdata($data);
    }

    function outtjorder()
    {
        $outtype = IReq::get('outtype');
        if(!in_array($outtype,array('query','ids')))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '查询条件错误';
            exit;
        }
        $where = '';
        $where2 = '';
		$where3='';
        if($outtype == 'ids')
        {
            $id = trim(IReq::get('id'));
            if(empty($id))
            {
                header("Content-Type: text/html; charset=UTF-8");
                echo '查询条件不能为空';
                exit;
            }
            $doid = explode('-',$id);
            $id = join(',',$doid);
            $where .= ' where id in('.$id.') ';

            $searchvalue = trim(IReq::get('searchvalue'));
            $where .= !empty($searchvalue)? ' and shopname = \''.$searchvalue.'\'':'';
            //   $data['searchvalue'] = $searchvalue;
            //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

            $starttime = trim(IReq::get('starttime'));
            $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
            $endtime = trim(IReq::get('endtime'));
            $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
        }else{
            $searchvalue = trim(IReq::get('searchvalue'));
            $where .= !empty($searchvalue)? ' where shopname = \''.$searchvalue.'\'':'';
            //   $data['searchvalue'] = $searchvalue;
            //	   $where .= ' where shopname = \''.$searchvalue.'\' ';

            $starttime = trim(IReq::get('starttime'));
            $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';
$where3 .= !empty($starttime)? ' and  jstime > '.strtotime($starttime.' 00:00:01').' ':'';
            $endtime = trim(IReq::get('endtime'));
            $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';
$where3 .= !empty($endtime)? ' and  jstime < '.strtotime($endtime.' 23:59:59').' ':'';			
        }
        $admin_id = intval(IReq::get('admin_id'));
        if(!empty($admin_id)){
            $where .= empty($where)?' where admin_id = \''.$admin_id.'\'':' and admin_id = \''.$admin_id.'\' ';

        }


        $shoplist = $this->mysql->getarr("select id,shopname,yjin from ".Mysite::$app->config['tablepre']."shop ".$where."   order by id asc  limit 0,2000");
        $list = array();
        if(is_array($shoplist))
        {
            foreach($shoplist as $key=>$value)
            {



                $sendtype = $this->mysql->value(Mysite::$app->config['tablepre']."shopfast","sendtype","shopid = '".$value['id']."'");//$table,$row,$where=""
                if($sendtype == 1){
					$value['sendtype'] = '自送';
				}else{
					$value['sendtype'] = '网站配送';
				}
                

                $shoptj=  $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost,sum(shopdowncost) as shopdowncost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =0 and shopcost > 0 and status = 3 ".$where2." order by id asc  limit 0,1000");
                $line=    $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(shopdowncost) as shopdowncost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost from ".Mysite::$app->config['tablepre']."order  where shopid = '".$value['id']."' and paytype =1  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by id asc  limit 0,1000");


                $value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数

                $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $line['score']/Mysite::$app->config['scoretocost']:0;
                $value['onlinescore'] = $scordedown;
                $value['online'] = $line['shopcost']+$line['pscost']+$line['bagcost'] -$line['cxcost'] - $line['yhcost']-$scordedown;//在线支付总金额
                $scordedown = !empty(Mysite::$app->config['scoretocost']) ? $shoptj['score']/Mysite::$app->config['scoretocost']:0;
                $value['unlinescore'] = $scordedown;
                $value['unline'] = $shoptj['shopcost']+$shoptj['pscost']+$shoptj['bagcost'] -$shoptj['cxcost'] - $shoptj['yhcost']-$scordedown;
                $value['yhjcost'] = $line['yhcost'] +$shoptj['yhcost'];//使用优惠券
                $value['cxcost'] = $line['cxcost'] +$shoptj['cxcost'];// 促销总优惠
				
				$value['ptcxcost'] = $line['shopdowncost'] +$shoptj['shopdowncost'];// 促销总优惠中  平台承担的部分
				$value['shopcxcost'] = $value['cxcost'] - $value['ptcxcost'];       // 促销总优惠中  商家承担的部分
				
				
				
                $value['score'] = $value['unlinescore'] +$value['onlinescore']; //  使用积分
                $value['bagcost'] = $line['bagcost'] +$shoptj['bagcost'];//   打包费
                $value['pscost'] = $line['pscost'] +$shoptj['pscost'];//   配送费
                $value['allcost'] = $line['shopcost'] +$shoptj['shopcost'] - $value['cxcost'];
                $value['goodscost'] = $line['shopcost'] +$shoptj['shopcost'];
                $jsinfo= $this->mysql->select_one("select sum(yjcost) as yjcost,sum(acountcost) as acountcost from ".Mysite::$app->config['tablepre']."shopjs  where shopid ='".$value['id']."'".$where3."  ");
				if(!empty($jsinfo)){
					$value['yje']=$jsinfo['yjcost'];
					$value['jse']=$jsinfo['acountcost'];
				}else{
					$value['yje']='未结算';
					$value['jse']='未结算';
				}


                $list[] = $value;
                // $list[] = $value1;
            }
        }
        $outexcel = new phptoexcel();
        $titledata = array('店铺名称','配送方式','订单数量','线上支付','线下支付','优惠券','平台促销','店铺促销','积分低扣金额','配送费','商品总价','打包费','佣金');
        $titlelabel = array('shopname','sendtype','orderNum','online','unline','yhjcost','ptcxcost','shopcxcost','score','pscost','goodscost','bagcost','yje');
        // $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 ");
        $outexcel->out($titledata,$titlelabel,$list,'','商家结算');
    }
    //导出商家结算详情
    function outdetail()
    {
        // 订单号    时间    订单内容    配送费用  总价
        $shopid =  intval(IReq::get('shopid'));

        if(empty($shopid))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '店铺获取失败';
            exit;
        }
        $shoplist = $this->mysql->select_one("select id,shopname,yjin,shoptype from ".Mysite::$app->config['tablepre']."shop  where id='".$shopid."'   order by id asc  limit 0,2000");
        if(empty($shoplist))
        {
            header("Content-Type: text/html; charset=UTF-8");
            echo '店铺获取失败';
            exit;
        }
        //dno
        $where = '';
        $where2 = '';
        $starttime = trim(IReq::get('starttime'));
        $where2 .= !empty($starttime)? ' and  suretime > '.strtotime($starttime.' 00:00:01').' ':'';

        $endtime = trim(IReq::get('endtime'));
        $where2 .= !empty($endtime)? ' and  suretime < '.strtotime($endtime.' 23:59:59').' ':'';

        $orderlist = $this->mysql->getarr("select id,dno,allcost,bagcost,shopps,shopcost,addtime,posttime,pstype ,paytype,paystatus from ".Mysite::$app->config['tablepre']."order where shopid = '".$shopid."' and  status = 3 ".$where2." order by id asc  limit 0,2000");
        $list = array();
        if(is_array($orderlist))
        {
            foreach($orderlist as $key=>$value)
            {
                $detlist = $this->mysql->getarr("select goodsname,goodscount as shuliang from ".Mysite::$app->config['tablepre']."orderdet  where order_id = '".$value['id']."' and shopid > 0  order by id asc  limit 0,5");
                $detinfo = '';
                if(is_array($detlist))
                {
                    foreach($detlist as $keys=>$val)
                    {

                        $detinfo .= $val['goodsname'].'/'.$val['shuliang'].'份,';
                    }
                }

                $value['content'] = $detinfo;
                $value['payname'] = $value['paytype'] == 0?'货到支付':'在线支付';
                $value['dotime'] = date('Y-m-d H:i:s',$value['addtime']);
                $value['posttime'] = date('Y-m-d H:i:s',$value['posttime']);
                $value['pstype'] = $value['pstype'] == 1?'自送':'平台';
                $list[] = $value;

            }
        }
        // 超市商品总价 marketps 超市配送配送  店铺商品总价 shopps 店铺配送费 pstype 配送方式 0：平台1：个人
        $outexcel = new phptoexcel();
        $titledata = array('订单编号','订单总价','配送类型','店铺商品总价','店铺配送费','打包费','订单详情','支付方式','下单时间','配送时间');
        $titlelabel = array('dno','allcost','pstype','shopcost','shopps','bagcost','content','payname','dotime','posttime');
        $outexcel->out($titledata,$titlelabel,$list,'','商家结算详情'.$shoplist['shopname']);
    }
    function shopjsover(){
        $jstime = IFilter::act(IReq::get('daytime')); //结算日
        $searchvalue = IReq::get('searchvalue');
		$yjb=Mysite::$app->config['yjin'];
		
        $nowtime = time();
        $nowmintime =  strtotime($jstime);
        $checktime = $nowtime - $nowmintime;
        if($checktime > 457141240){
            $nowmintime = strtotime(date('Y-m-d',$nowtime));
        }
        $where = " where jstime >= ".$nowmintime;

        $endtime = IFilter::act(IReq::get('endtime')); //结算日
        $checkendtime = strtotime($endtime);
        if($checkendtime   > $nowmintime){
            $where .= " and  jstime < ".$checkendtime;

        }else{
            $checkendtime = strtotime(date('Y-m-d',$nowtime));
        }
        $data['daytime'] = date('Y-m-d',$nowmintime);
        $data['endtime'] =  date('Y-m-d',$checkendtime);
        $newlink = '/daytime/'.$data['daytime'].'/endtime/'.$data['endtime'];
        $data['searchvalue'] = '';
        $where2 = '';
        if(!empty($searchvalue))
        {
            $data['searchvalue'] = $searchvalue;
            $where2 .= ' where shopname = \''.$searchvalue.'\' ';
            $newlink .= '/searchvalue/'.$searchvalue;
        }
        $pageshow = new page();
        $pageshow->setpage(IReq::get('page'),10);
        $shoplist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."shop  ".$where2." order by id desc   limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
        $shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."shop ".$where2."  order by id asc  ");
        $pageshow->setnum($shuliang);
        $datalist = array();
        if(is_array($shoplist)){
            foreach($shoplist as $key=>$value){
                 
                if($value['shoptype'] == 0){
                    $shoppsinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopfast where shopid = ".$value['id']." ");
                }else{
                    $shoppsinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopmarket where shopid = ".$value['id']." ");
                }
                $txlist =   $this->mysql->select_one("select sum(onlinecost) as onlinecost, sum(onlinecount) as onlinecount,sum(unlinecount) as unlinecount,sum(unlinecost) as unlinecost,sum(yjcost) as yjcost,pstype,sum(acountcost) as acountcost,addtime from ".Mysite::$app->config['tablepre']."shopjs  ".$where." and shopid = ".$value['id']." order by addtime desc   limit 0,1000");
				$value['yjb']=$value['yjb']>0?$value['yjb']:$yjb;
                $txlist['sendtype']=$shoppsinfo['sendtype'];
//                  print_R($txlist['sendtype']);exit;              
                $newarray =  array_merge($value,$txlist);
                $datalist[] = $newarray;

            }
        }
        $link = IUrl::creatUrl('/adminpage/analysis/module/shopjsover'.$newlink);
        $data['pagecontent'] = $pageshow->getpagebar($link);

        $data['jslist'] = $datalist;
         
       
        Mysite::$app->setdata($data);
    }

    function psyyj(){
        $searchvalue = IReq::get('searchvalue');
        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));
        $newlink = '';

        $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;

        $where= " where `group`=2   and ( admin_id = '".$cityid."' or admin_id = 0 )    ";
        $where2 = '';
        $where3 = "";
        $data['searchvalue'] = '';
        if(!empty($searchvalue))
        {
            $data['searchvalue'] = $searchvalue;
            $where .= ' and username = \''.$searchvalue.'\' ';
            $where2 .= ' and psusername = \''.$searchvalue.'\' ';
            $newlink .= '/searchvalue/'.$searchvalue;
        }
       
        $data['starttime'] = '';
        if(!empty($starttime))
        {
            $data['starttime'] = $starttime;
            $where2 .= ' and  suretime > '.strtotime($starttime.' 00:00:01').' ';
            $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';
            $newlink .= '/starttime/'.$starttime;
        }
        $data['endtime'] = '';
        if(!empty($endtime))
        {
            $data['endtime'] = $endtime;
            $where2 .= ' and  suretime < '.strtotime($endtime.' 23:59:59').' ';
            $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' ';
            $newlink .= '/endtime/'.$endtime;
        }
        $link = IUrl::creatUrl('/adminpage/analysis/module/psyyj'.$newlink);
        $pageinfo = new page();
        $pageinfo->setpage(IReq::get('page'),10);
        $memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where."   order by uid asc  limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."");
        $list = array();

        if(is_array($memberlist))
        {
            foreach($memberlist as $key=>$value)
            {
                $shoptj=  $this->mysql->select_one("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost,psuid  from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='0' and shopcost > 0 and status = 3 ".$where2." order by psuid asc  limit 0,1000");
                $line= $this->mysql->select_one("select count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost,sum(shopcost) as shopcost, sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost,psuid from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype !='0'  and paystatus =1 and shopcost > 0 and status = 3 ".$where2."   order by psuid asc  limit 0,1000");
                #print_R("select  count(id) as shuliang,sum(cxcost) as cxcost,sum(yhjcost) as yhcost, sum(shopcost) as shopcost,sum(scoredown) as score, sum(shopps)as pscost, sum(bagcost) as bagcost,sum(allcost) as doallcost,psuid  from ".Mysite::$app->config['tablepre']."order  where psuid = '".$value['uid']."' and paytype ='0' and shopcost > 0 and status = 3 ".$where2." order by psuid asc  limit 0,1000");
                #$value['orderNum'] =  $shoptj['shuliang']+$line['shuliang'];//订单总个数
              
                $value['online'] = $line['doallcost'];
                
                $value['unline'] = $shoptj['doallcost'];
                $tempc  =  $this->mysql->select_one("select sum(psycost) as tjcost ,count(id) as orderNum from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." and dotype < 3 ");
                $tempc2  =  $this->mysql->select_one("select sum(psycost) as tjcost ,count(id) as orderNum from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$value['uid']." and status =3  ".$where3." and dotype = 3 ");
                $value['tjcost'] = isset($tempc['psycost'])?0:$tempc['tjcost'];
                $value['orderNum'] = $tempc['orderNum']+$tempc2['orderNum'];//订单总个数
                $value['tjcost'] =$value['tjcost']- $tempc2['tjcost'];
                $value['outdetail'] =IUrl::creatUrl('adminpage/analysis/module/psyout/uid/'.$value['uid'].$newlink);
                $list[] = $value;
                
            }
        }
        $data['memberlist'] =$list;
        $shuliang  = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."member ".$where."  ");
        $pageinfo->setnum($shuliang);
        $data['pagecontent'] = $pageinfo->getpagebar($link);
        Mysite::$app->setdata($data);
    }
    function psyout(){

        $starttime = trim(IReq::get('starttime'));
        $endtime = trim(IReq::get('endtime'));
        $uid = intval(IReq::get('uid'));
        if(empty($uid)){
            header("Content-Type: text/html; charset=UTF-8");
            echo '用户不存在';
            exit;

        }
        $memberinfo = $this->mysql->select_one("select  * from ".Mysite::$app->config['tablepre']."member where uid = '".$uid."' ");
        if(empty($memberinfo)){
            header("Content-Type: text/html; charset=UTF-8");
            echo '用户不存在';
            exit;
        }

        $where3 = "";


        if(!empty($starttime)) $where3 .=' and  picktime > '.strtotime($starttime.' 00:00:01').' ';

        if(!empty($endtime)) $where3 .=' and  picktime < '.strtotime($endtime.' 23:59:59').' ';

        $tempc  =  $this->mysql->getarr("select  * from ".Mysite::$app->config['tablepre']."orderps  where psuid = ".$uid." and status =3  ".$where3."   limit 0,4000 ");



        $list = array();
        if(is_array($tempc))
        {
            foreach($tempc as $key=>$value)
            {
                if($value['dotype'] == 2){
                    $value['dotypename'] = '后台增加';
                }elseif($value['dotype'] == 3){
                    $value['dotypename'] = '后台减少';
                }else{
                    $value['dotypename'] = '配送订单';
                }
                $value['addtime'] = date('Y-m-d',$value['addtime']);
                $value['picktime'] = date('Y-m-d',$value['picktime']);

                $list[] = $value;
                // $list[] = $value1;
            }
        }
        $outexcel = new phptoexcel();
        $titledata = array('描述','资金类型','收入','创建时间','取货时间');
        $titlelabel = array('dno','dotypename','psycost','addtime','picktime');
        $outexcel->out($titledata,$titlelabel,$list,'',$memberinfo['username'].'配送员配送详情');
    }
}
?>