<?php
class method   extends adminbaseclass
{   
    function savedistributionset(){		
		$is_open_distribution = IFilter::act(IReq::get('is_open_distribution'));
        $fxfeelv = IFilter::act(IReq::get('fxfeelv'));			
	    $distribution_grade = IFilter::act(IReq::get('distribution_grade'));  
        $distribution_yj1 = IFilter::act(IReq::get('distribution_yj1'));
		$distribution_yj2 = IFilter::act(IReq::get('distribution_yj2'));      
		$distribution_yj3 = IFilter::act(IReq::get('distribution_yj3'));
        $minfxtxcost = IFilter::act(IReq::get('minfxtxcost'));		
		if(!is_numeric($fxfeelv) || !is_numeric($distribution_yj1) || !is_numeric($distribution_yj2) || !is_numeric($distribution_yj3) )$this->message('请输入数字');
		if($is_open_distribution == 1 && $distribution_grade < 1 ) $this->message('请设置分销级别');
		if($distribution_grade == 1 && $distribution_yj1 <= 0){
			$this->message('佣金比例请输入大于0的数字');
		}
		if($distribution_grade == 2 && ($distribution_yj1 <= 0 || $distribution_yj2 <= 0)){
			$this->message('佣金比例请输入大于0的数字');
		}
		if($distribution_grade == 3 && ($distribution_yj1 <= 0 || $distribution_yj2 <= 0 || $distribution_yj3 <= 0)){
			$this->message('佣金比例请输入大于0的数字');
		}
		$config = new config('hopeconfig.php',hopedir);
	    $siteinfo['is_open_distribution'] = $is_open_distribution;
		$siteinfo['fxfeelv'] = $fxfeelv;
		$siteinfo['distribution_grade'] = $distribution_grade;	
		$siteinfo['distribution_yj1'] = $distribution_yj1;	
		$siteinfo['distribution_yj2'] = $distribution_yj2;
 		$siteinfo['distribution_yj3'] = $distribution_yj3;	
		$siteinfo['minfxtxcost'] = $minfxtxcost;	
        	 
		$config->write($siteinfo);	     
	    $this->success('设置成功');	
	}
	 
	function savedistributioncontent(){	
        $fxcontent = IReq::get('content');
		$link = IUrl::creatUrl('adminpage/card/module/distributioncontent');
		if(empty($fxcontent)) $this->message('内容不能为空',$link);		
		$data['content'] = $fxcontent;
		$data['title'] = '分销说明';
		$data['code'] = 'fxsm';
		$data['addtime'] = time();
		$info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."single where code='fxsm' and title = '分销说明'  ");
		if(empty($info)){
			$this->mysql->insert(Mysite::$app->config['tablepre'].'single',$data);
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'single',$data," code='fxsm' and title = '分销说明' ");			 
		}
		$fxcodetit = IReq::get('fxcodetit');
		$fxcodedes = IReq::get('fxcodedes');
		if(empty($fxcodetit) || empty($fxcodedes))$this->message('请完善分销二维码分享标题/描述',$link);		
		$config = new config('hopeconfig.php',hopedir);
	    $siteinfo['fxcodetit'] = $fxcodetit;
		$siteinfo['fxcodedes'] = $fxcodedes;		 
		$config->write($siteinfo);	     
		
        $this->success('保存成功',$link);			
	}
	function fxtxlog(){
		$pageshow = new page();
		$pageshow->setpage(IReq::get('page'),10);
		$username = trim(IFilter::act(IReq::get('username'))); //店铺名称
		$status = IReq::get('status'); //状态
		$starttime = IFilter::act(IReq::get('starttime')); //开始时间 
		$endtime =  IFilter::act(IReq::get('endtime')); //结束时间
		$newlink = '';
		$where = " where cost > 0 " ;		 
		if(!empty($username)){
		    $info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."member where username='".$username."'  ");
			if(!empty($info)) {
				$where.=" and uid = ".$info['uid']." ";
				$newlink .= '/username/'.$username;
			}
		}
		if(!empty($status)){
		    $where.=" and status = ".$status." ";
		    $newlink .= '/status/'.$status;
		    $data['status'] = $status;
		}else{
			$where.=" and status = 0 ";
		}
		if(!empty($starttime)){
			$where.=" and addtime > ".strtotime($starttime)." ";
			$newlink .= '/starttime/'.$starttime.'/endtime/'.$endtime;
		}
		if(!empty($endtime)){
			$where.=" and addtime < ".strtotime($endtime)." ";
			$newlink .= '/endtime/'.$endtime;
		}
		$link = IUrl::creatUrl('/adminpage/card/module/fxtxlog'.$newlink);
		$txlist =   $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."distributiontxlog  ".$where."  order by addtime desc   limit ".$pageshow->startnum().", ".$pageshow->getsize().""); 
		$shuliang  = $this->mysql->counts("select id  from ".Mysite::$app->config['tablepre']."distributiontxlog  ".$where."  order by id asc  ");
		$pageshow->setnum($shuliang);
		$data['pagecontent'] = $pageshow->getpagebar($link);
		$tempdata = array();
		$statusarray = array(0=>'申请',1=>'处理成功',2=>'已取消');
		$typearr = array(1=>'账户余额',2=>'支付宝',3=>'银行卡');
		if(is_array($txlist)){
			foreach($txlist as $key=>$value){
				$info = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."member where uid=".$value['uid']." ");
				$value['username'] = isset($info['username'])?$info['username']:'未定义';			 
				$value['statusname'] = isset($statusarray[$value['status']])?$statusarray[$value['status']]:'未定义';
				$value['typename'] = isset($typearr[$value['type']])?$typearr[$value['type']]:'未定义';
				$value['adddate'] = date('Y-m-d H:i:s',$value['addtime']);
				$tempdata[] = $value;
			}
		}
		$data['txlist'] = $tempdata;
		$data['username'] = $username;
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;		 
		Mysite::$app->setdata($data);
	}
	//后台处理分销佣金提现申请
	function admindotx(){
		$txid =  trim(IReq::get('txid'));
		$type =  trim(IReq::get('type'));  //1同意   2取消
		if(!in_array($type, array(1, 2))) $this->message('操作类型获取失败');
		$txinfo = $this->mysql->select_one(" select *  from ".Mysite::$app->config['tablepre']."distributiontxlog where id=".$txid." ");		
		if(empty($txinfo)) $this->message('提现申请不存在');
		$memberinfo = $this->mysql->select_one(" select uid,cost,fxcost,username  from ".Mysite::$app->config['tablepre']."member where uid=".$txinfo['uid']." ");
		if(empty($memberinfo)) $this->message('申请会员不存在');
		if($txinfo['status'] > 0) $this->message('提现申请不在待处理状态');
		if($type == 1){
			if($txinfo['type'] == 1){
				$cost = $memberinfo['cost'] + $txinfo['reallycost'];
				$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('cost'=>$cost),"uid='".$txinfo['uid']."'");			
				$this->memberCls->addlog($txinfo['uid'],2,1,$txinfo['reallycost'],'分销佣金提现收入','分销佣金提现收入',$cost);
				$this->memberCls->addmemcostlog( $txinfo['uid'],$memberinfo['username'],$memberinfo['cost'],1,$txinfo['reallycost'],$cost,'分销佣金提现收入',ICookie::get('adminuid'),ICookie::get('adminname') );
			}
			$this->mysql->update(Mysite::$app->config['tablepre'].'distributiontxlog',array('status'=>1),"id='".$txid."'");	
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'distributiontxlog',array('status'=>2),"id='".$txid."'");	
            $fxcost = $memberinfo['fxcost'] + $txinfo['cost'];
			$this->mysql->update(Mysite::$app->config['tablepre'].'member',array('fxcost'=>$fxcost),"uid='".$txinfo['uid']."'");				
		}
		$this->success('success'); 
		
	}
    function specialpage(){
        $data['name'] =  trim(IReq::get('name'));
        $where = '';
        if(!empty($data['name'])){
            $where .= " and name like '%".$data['name']."%'";
        }
        //构造查询条件
        $data['where'] = $where;
        Mysite::$app->setdata($data);
    }
    function addspecialpage(){
        $id =  intval(IReq::get('id'));
        $catparent = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where  type='checkbox' order by cattype asc limit 0,100");
        $catlist = array();
        foreach($catparent as $key=>$value){
            $tempcat   = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shoptype  where parent_id = '".$value['id']."'  limit 0,100");

            foreach($tempcat as $k=>$v){
                $catlist[] = $v;
            }
        }
        if($id>0){
            $tempcat   = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage  where id = '".$id."'  ");
            $data['ztyinfo'] = $tempcat;
        }
        #print_r($data['ztyinfo']);
        $data['catarr'] = array('0'=>'外卖','1'=>'超市');
        $data['catlist'] = $catlist;
        Mysite::$app->setdata($data);
    }
    function delspecialpage(){  //删除专题页
//		$this->message("您暂无权限删除，请联系管理员");
	limitalert();
		$id = IReq::get('id');
		if(empty($id))  $this->message('未选中');
		$ids = is_array($id)? join(',',$id):$id;
		$this->mysql->delete(Mysite::$app->config['tablepre'].'specialpage'," id in($ids) ");
		$this->success('success');
	}
    function getshoplist(){
        $id =  intval(IReq::get('shopclassid'));
        $search_shop =  IReq::get('search_shop');
        $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
        $where = 'where admin_id = '.$cityid;
        if(!empty($search_shop)){
            $where.=' and shopname like "%'.$search_shop.'%"';
        }
        if($id == 'marketshop'){
			$where .=' and shoptype  = 1 ';
		}
		if($id>1){
            $where .= "  and id in((select shopid from ".Mysite::$app->config['tablepre']."shopattr where attrid = ".$id." ))";
        }
		
        $shoplist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop  {$where}  and is_pass=1 order by sort asc");
        if(!empty($shoplist)){
            $this->success($shoplist);
        }else{
            $this->message("该分类下暂无店铺");
        }
    }
    function getgoods(){
        $id =  intval(IReq::get('shopclassid'));
        $search_shop =  IReq::get('search_shop');
        $cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
        $where = 'where admin_id = '.$cityid;
        if(!empty($search_shop)){
            $where.=' and shopname like "%'.$search_shop.'%"';
        }
        if($id>1){
            $where .= "  and id in((select shopid from ".Mysite::$app->config['tablepre']."shopattr where attrid = ".$id." ))";
        }
        $shoplist = $this->mysql->getarr("select id,shopname from ".Mysite::$app->config['tablepre']."shop  {$where} and is_pass=1 order by sort asc");
        if(!empty($shoplist)){
            foreach($shoplist as $v){
                $v['goods'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goods  where shopid = {$v['id']}");
                $t[] = $v;
            }
        }
        $data['list'] = $t;
        #print_r($data);exit;
        $this->success($data);

    }
    function saveimgandcolor(){
		limitalert();
		$jntimg = IFilter::act(IReq::get('jntimg'));		 
	    $jntcolor = IFilter::act(IReq::get('jntcolor'));        
		$config = new config('hopeconfig.php',hopedir);
	    $siteinfo['jntimg'] = $jntimg;
		$siteinfo['jntcolor'] = $jntcolor;		 
		$config->write($siteinfo);	     
	    $this->success('设置成功');	
	}
    function addspecialpagenext(){
        $data['name'] =  IReq::get('name');
        $data['opttype'] =  IReq::get('opttype');
        $data['params'] =  IReq::get('params');
        $data['shoptype'] =  IReq::get('shoptype');
        $data['cxtype'] =  IReq::get('cxtype');
        $data['oriid'] =  intval(IReq::get('oriid'));
        if($data['oriid']>0){
            $tempcat   = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage  where id = '".$data['oriid']."'  ");
            $data['ztyinfo'] = $tempcat;
        }
        Mysite::$app->setdata($data);
    }
    function savezty(){  // 保存或者更新 专题页
		#limitalert();
        $id = intval(IReq::get('ztyid'));
        $data['name'] = IReq::get('name');
        $data['specialimg'] = IReq::get('img');
        $data['color'] = IReq::get('color');

        $params = IReq::get('params');
        $shoptype = IReq::get('shoptype');
        $cxtype = intval(IReq::get('cxtype'));
        $type =  intval(IReq::get('opttype'));
        $data['zttype'] = $type;
        switch($type){
            case 1:
                $data['listids'] = $params;
                $data['is_custom'] = 0;
                $data['showtype'] =0;
                break;
            case 2:
                $data['listids'] = $params;
                $data['is_custom'] = 0;
                $data['showtype'] =1;
                break;
            case 3:

                if($params == 'marketshop'){
                    $data['cx_type'] =14;
                }else{
                    if($shoptype==0){
                        $data['cx_type'] =6;
                    }else{
                        $data['cx_type'] =7;
                    }
                }
                $data['listids'] =$params;
                $data['showtype'] =0;
                $data['is_custom'] = 1;
                break;
            case 4:
                $data['showtype'] =$params;
                $data['is_custom'] = 1;
                if($params==1){
                    $data['cx_type'] = 1;
                }else{
                    $data['cx_type'] = $cxtype;
                }
               # print_r($data);exit;
                break;
            case 5:
                $data['is_custom'] = 1;
                $data['showtype'] =0;
                $data['cx_type'] = $params;
                break;
            case 6:
                $data['showtype'] = 2;
                $data['is_custom'] = 0;
                $data['cx_type'] =13;
                $data['zdylink'] = $params;
                break;
        }

        $data['ruleintro'] = IReq::get('content');
        $data['cityid'] = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
        if(empty($id)){
            $linkurl = IUrl::creatUrl('adminpage/other/module/specialpage');
        }else{

            $linkurl =  IUrl::creatUrl('adminpage/other/module/addspecialpage/id/'.$id);

        }

        if(empty($data['name'])) $this->message('名称不能为空',$linkurl);
        if(empty($data['color'])) $this->message('专题页背景色调不能为空',$linkurl);
        if( $data['is_custom'] !=0 && $data['is_custom'] !=1) $this->message('请选择活动格式',$linkurl);
        if( $data['showtype'] !=0 && $data['showtype'] !=1 && $data['showtype'] !=2  ) $this->message('请选择活动针对对象',$linkurl);


        if( $data['showtype'] == 2 ) {
            if( empty($data['zdylink']) ) {
                $this->message('请填写跳转链接',$linkurl);
            }
        }

        if( $data['is_custom'] == 1 ){
            if( $data['showtype'] != 2 ) {
                if($data['cx_type'] <=0 && $data['cx_type'] >1) $this->message('请选择对象对应活动类型',$linkurl);
            }
        }
        if($data['is_custom'] == 0 && $type!=6){
            $data['cx_type'] = 0;
        }

        if(empty($id))
        {
            $data['is_show'] = 0;
            $data['is_bd'] = 1;
            $link = IUrl::creatUrl('adminpage/other/module/specialpage');
            $this->mysql->insert(Mysite::$app->config['tablepre'].'specialpage',$data);
            $this->success('success',$link);
        }else{
          #  print_r($data);exit;
            $link = IUrl::creatUrl('adminpage/other/module/addspecialpage/id/'.$id);
            $this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,"id='".$id."'");
            $this->success('success',$link);
        }
        //   	$link = IUrl::creatUrl('adminpage/other/module/specialpage');
        //   $this->success('success',$link);

    }
	/*手动发优惠券*/
   function savehandjuanset(){	   
		#limitalert();
	   $sendusertype = IReq::get('sendusertype');	   	    
	   if($sendusertype != 3 ) $this->message('为了有效避免垃圾数据的产生，网站暂时限制只支持发放指定会员');
	   if(empty($sendusertype)) $this->message('请指定发放范围');
	   if($sendusertype == 1){ //全部会员
		   	$users = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where uid > 0 " ); 
 			$alluser = array();
			foreach($users as $k=>$v){
				$alluser[] = $v['uid'];
			}
			$senduser = $alluser;
	   }
	   if($sendusertype == 2){ //指定条件 1新会员  2 老会员  12全部	 新老会员区分为是否下过单  
		   $usertype = IReq::get('usertype');
		   $usertypestr = implode(',',$usertype);
		   if(empty($usertypestr))$this->message('请选择指定条件会员分组');
		   $users = $this->mysql->getarr("select uid from ".Mysite::$app->config['tablepre']."member where uid > 0 " );           
		   $newuser = array();
		   $olduser = array();
		   foreach($users as $k=>$v){
			   $checkorder = $this->mysql->select_one("select id from ".Mysite::$app->config['tablepre']."order where buyeruid = ".$v['uid']." and status = 3 " );  
			   if(empty($checkorder)){
				   $newuser[] = $v['uid'];
			   }else{
				   $olduser[] = $v['uid'];
			   }
		   }
		   if($usertypestr == '1'){//新会员
			   $senduser = $newuser;			   
		   }
		   if($usertypestr == '2'){//老会员
			   $senduser = $olduser;			   
		   }
		   if($usertypestr == '1,2'){//全部
			   $senduser = array_merge($newuser,$olduser);			   
		   }
	   }
	   if($sendusertype == 3){ //指定会员
		   $userstr = IReq::get('userstr');
		   if(empty($userstr)) $this->message('请填写指定会员用户名');
		   $userarr = explode('#',$userstr);
		   $senduser = array();		    
		   foreach($userarr as $k=>$v){
			   $checkmem = $this->mysql->select_one("select uid from ".Mysite::$app->config['tablepre']."member where username = '".$v."' " );  
			   if(!empty($checkmem)){
				   $senduser[] = $checkmem['uid'];
			   }			   
		   }
	   }     
	   if(empty($senduser)) $this->message('未查找到符合条件的用户');	
	   $acttitle = IReq::get('title');
	   if(empty($acttitle))$this->message('请填写活动名称');	 
	   $costtype = IReq::get('costtype');
	   
	   $ajuanname = IReq::get('ajuanname');
	   $bjuanname = IReq::get('bjuanname');

	   $ajuancost = IReq::get('ajuancost');

	   $bjuancostmin = IReq::get('bjuancostmin');
	   $bjuancostmax = IReq::get('bjuancostmax');
	   $ajuanlimitcost = IReq::get('ajuanlimitcost'); 
	   $bjuanlimitcost = IReq::get('bjuanlimitcost'); 
	   
	   $spotordtype = IReq::get('spotordtype');
	   $spotordtypestr = implode(',',$spotordtype);
	   if(empty($spotordtypestr)) $this->message('请至少选择一种优惠券支持频道');
	   
	   $paytype = IReq::get('paytype');
	   if(empty($paytype)){//优惠券中支持的支付方式   1在线支付  2货到付款  1,2都支持
		   $paytype = '1,2';
	   }else{
		   $paytype = '1';		   
	   }
	   
	   $timetype = IReq::get('timetype');
       if($timetype == 1){ //固定天数
		   $juanday = IReq::get('juanday');//有效天数
		   if(empty($juanday))$this->message('请填写有效天数');
		   $starttime = time();
		   $date = date('Y-m-d',$starttime);
		   $endtime = strtotime($date) + ($juanday-1)*24*60*60 + 86399;
	   }else{ //指定时间段
		    $starttime = IReq::get('starttime');
			if(empty($starttime))$this->message('请选择生效时间');
		    $endtime = IReq::get('endtime');
			if(empty($endtime))$this->message('请选择失效时间');
			$starttime = strtotime($starttime.' 00:00:00');
			$endtime = strtotime($endtime.' 23:59:59');	
            if($starttime > $endtime) $this->message('失效时间不能早于生效时间');
	   }
	   //先生成一条发放记录   获取记录id   发放完成后  再更新该记录详细数据
	   $logdata['actname'] = $acttitle;
	   $logdata['sendrange'] = $sendusertype;
	   $this->mysql->insert(Mysite::$app->config['tablepre'].'handsendjuanlog',$logdata);
	   $actid = $this->mysql->insertid(); 
	   /*优惠券信息设置*/
	   $juandata['status'] = 0;
	   $juandata['creattime'] = $starttime;
	   $juandata['endtime'] = $endtime;
	   $juandata['actid'] = $actid;
	   $juandata['spotordtype'] = $spotordtypestr;
	   $juandata['type'] = 6;
	   $juandata['paytype'] = $paytype;	  

       $noticedata['actname'] = $acttitle;	    
	   $noticedata['actid'] = $actid;
	   $noticedata['is_read'] = 0;		   
	   if($costtype == 1){ //固定面值
		   $oneusercount = count($ajuanname);
		   foreach($ajuanname as $k3=>$v3){
			   if(empty($v3))$this->message('优惠券名称不能为空');	 
		   }      
		   foreach($ajuanlimitcost as $k5=>$v5){
			   if(empty($v5))$this->message('请输入大于0的优惠券限制门槛金额');	 
		   }
		   foreach($ajuancost as $k4=>$v4){
			   if(empty($v4))$this->message('请输入大于0的优惠券面值');
               if($v4 > $ajuanlimitcost[$k4])$this->message('优惠券面值不能大于满足使用条件的门槛金额');	   
		   }
		   foreach($senduser as $ka=>$va){			    
			   $juandata['uid'] = $va;
			   foreach($ajuanname as $kb=>$vb){
				   $juandata['name'] = $vb;
				   $juandata['cost'] = $ajuancost[$kb];
				   $juandata['limitcost'] = $ajuanlimitcost[$kb];
				   $juandata['name'] = $ajuanname[$kb];
				   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);  
			   }				   	 
			   $noticedata['uid'] = $va;			    	   
               $this->mysql->insert(Mysite::$app->config['tablepre'].'userjuannotice',$noticedata);  			   
		   }   		   
	   }else{ //随机面值
	       $oneusercount = count($bjuanname);
	       foreach($bjuanname as $k6=>$v6){
			   if(empty($v6))$this->message('优惠券名称不能为空');	 
		   }
		   foreach($bjuanlimitcost as $k9=>$v9){
			   if(empty($v9))$this->message('请输入大于0的优惠券限制门槛金额');	 
		   }
		   foreach($bjuancostmin as $k7=>$v7){
			   if(empty($v7))$this->message('请输入大于0的优惠券随机面值最小值');
               if($v7 > $bjuanlimitcost[$k7])$this->message('优惠券随机面值最小值不能大于满足使用条件的门槛金额');	
               if($v7 > $bjuancostmax[$k7])$this->message('优惠券随机面值最小值不能大于其对应的随机最大值');	   			   
		   } 
		   foreach($bjuancostmax as $k8=>$v8){
			   if(empty($v8))$this->message('请输入大于0的优惠券随机面值最大值');
               if($v8 > $bjuanlimitcost[$k8])$this->message('优惠券随机面值最大值不能大于满足使用条件的门槛金额');	   
		   } 
		   foreach($senduser as $ka=>$va){			   			    
			   $juandata['uid'] = $va;			    
			   foreach($bjuanname as $kb=>$vb){
				   $juandata['name'] = $vb;
				   $juandata['cost'] = rand($bjuancostmin[$kb],$bjuancostmax[$kb]);
				   $juandata['limitcost'] = $bjuanlimitcost[$kb];
				   $juandata['name'] = $bjuanname[$kb];
				   $this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$juandata);  
			   }
               $noticedata['uid'] = $va;			    	   
               $this->mysql->insert(Mysite::$app->config['tablepre'].'userjuannotice',$noticedata);  				   
		   }	   
	   }  
	   //更新发放记录数据
	   $udata['sendtime'] = time();
	   $udata['oneusercount'] = $oneusercount;
	   $this->mysql->update(Mysite::$app->config['tablepre'].'handsendjuanlog',$udata,"id='".$actid."'");
       $this->mysql->delete(Mysite::$app->config['tablepre']."handsendjuanlog"," sendtime < 1 or oneusercount < 1 ");  	   
	   $this->success('发放成功'); 	   
   }
   function handsendjuanlog(){
	   $this->pageCls->setpage(intval(IReq::get('page')),10); 
	   $loglist = array();
	   $data['actname'] = IReq::get('title');
	   if(!empty($data['actname'])){
		   $where = " and actname = '".$data['actname']."' ";
	   }
	   $this->pageCls->setpage(intval(IReq::get('page')),10); 
	   $loglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."handsendjuanlog where id > 0 ".$where." order by id desc  limit  ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
	   $usertype = array('1'=>'全部会员','2'=>'指定条件','3'=>'指定会员');
	   $data['loglist'] = array();
	   foreach($loglist as $k=>$v){
		   $v['sendtime'] = date('Y-m-d H:i:s',$v['sendtime']);
		   $v['sendrange'] = $usertype[$v['sendrange']];
		   $v['allcount'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juan where actid = ".$v['id']." " ); 
		   $v['usecount'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juan where actid = ".$v['id']." and status = 2" ); 
		   $data['loglist'][] = $v;
	   }
	   $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."handsendjuanlog where id > 0 ".$where."   ");
	   $this->pageCls->setnum($shuliang); 
	   $data['pagecontent'] = $this->pageCls->getpagebar();
	   Mysite::$app->setdata($data);
   }
   function delsendjuanlog(){
	   $id = IReq::get('id');
	   $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."handsendjuanlog where id = ".$id." " ); 	   
	   if(empty($checkinfo)) $this->message('记录不存在');
	   $this->mysql->delete(Mysite::$app->config['tablepre']."handsendjuanlog"," id='".$id."'");
	   $this->success('success');   
   }
	
	
   /**编辑促销活动设置**/
   function editrule(){
	   $id = IReq::get('id');
	   $data['ruleinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."cxruleset where id = ".$id." " ); 	   
	   if(empty($data['ruleinfo'])) $this->message('信息获取失败');
	   Mysite::$app->setdata($data);    
   }
   /**保存促销活动设置**/
   function saveruleset(){
	   limitalert();
	   $id = IReq::get('id');	    
	   $data['imgurl'] = IReq::get('imgurl');
	   $data['supportorder'] = IReq::get('supportorder');
	   $supportplat = IReq::get('supportplat');
	   $data['supportplat'] = implode(',',$supportplat);
	   if(empty($id)) $this->message('优惠活动id获取失败');
	   if(empty($data['imgurl'])) $this->message('优惠活动标签获取失败');	   
	   if(empty($data['supportorder'])) $this->message('优惠活动支持订单获取失败');
	   if(empty($data['supportplat'])) $this->message('优惠活动支持平台获取失败');
	   $this->mysql->update(Mysite::$app->config['tablepre'].'cxruleset',$data,"id=".$id."");
       $this->success('success'); 
   }

   /**关注微信领取优惠券相关信息**/
   function followsjset(){
	   $data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 1 or name = '关注送优惠券' " );  	   
	   $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 1 or name= '关注送优惠券' order by id asc " );	   
	   Mysite::$app->setdata($data);          
   }
    /**保存关注微信领取优惠券相关信息**/
   function savefollowjuanset(){
	   limitalert();
       $followjuan = IReq::get('followjuan');//是否开启  0关闭 1开启
	   $costtype = IReq::get('costtype');//面值类型  1固定面值  2随机面值	   
       $cost = IReq::get('fjuancost');//优惠券固定面值数组
	   $flimitcost = IReq::get('fjuanlimitcost');//优惠券固定面值限制金额数组
	   $rlimitcost = IReq::get('rjuanlimitcost');//优惠券随机面值限制金额数组
	   $costmin = IReq::get('rjuancostmin');//优惠券随机面值金额下限数组
	   $costmax = IReq::get('rjuancostmax');//优惠券随机面值金额上限数组
	   $paytype = IReq::get('paytype'); //支持类型 1在线支付 空的时候都支持赋值1,2
	   
	   $timetype = IReq::get('timetype');// 失效时间类型 1固定天数 2固定时间段
	   $days = IReq::get('juanday');  //失效天数
	   if($timetype == 1 && $days <=0 ) $this->message('请输入正确的失效天数');
	   $starttime = IReq::get('starttime');//有效时间开始值	  
	   $endtime = IReq::get('endtime');//有效时间结束值
	   if(strtotime($endtime.' 23:59:59') < strtotime($starttime.' 00:00:00'))$this->message('过期时间不能早于生效时间'); 
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   if($data1['cost']<= 0 || $data1['limitcost']<=0)$this->message('请输入大于0的金额数值');
			   if($data1['cost'] > $data1['limitcost'] )$this->message('优惠券面值不可大于限制门槛金额'); 
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;			   
               $data1['costmax'] = $costmax[$k2];
			   if($data1['costmax'] > $data1['limitcost'] )$this->message('优惠券面值随机数最大值不可大于限制门槛金额');  
		   }   
	   }
	   //更新关注微信领取优惠券设置
	   $data['status'] = $followjuan;
	   $data['costtype'] = $costtype;
	   $data['paytype'] = $paytype == 1?'1':'1,2';
	    
	   $data['timetype'] = $timetype;
	   $data['days'] = $days;
	   $data['starttime'] = strtotime($starttime.' 00:00:00');
	   $data['endtime'] = strtotime($endtime.' 23:59:59');	   
	   $this->mysql->update(Mysite::$app->config['tablepre'].'alljuanset',$data,"type = 1 or name = '关注送优惠券'");	   
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'alljuan', " type = 1" );//更新时  先删除以前的后插入新的	    
	   $data1['paytype'] = $paytype == 1?'1':'1,2';
       $data1['type'] = 1;
       $data1['name'] = '关注送优惠券';   
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];			    
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;
			   $data1['costmin'] = $costmin[$k2];
               $data1['costmax'] = $costmax[$k2];			    
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }
	   $this->success('success'); 
   }
   
   /**注册领取优惠券相关信息**/
   function registersjset(){
	   $data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 2 or name = '注册送优惠券' " );  	   
	   $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 2 or name= '注册送优惠券' order by id asc " );	   
	   Mysite::$app->setdata($data);          
   }
    /**保存注册领取优惠券相关信息**/
   function saveregistersjset(){
	   //limitalert();
       $followjuan = IReq::get('followjuan');//是否开启  0关闭 1开启
	   $costtype = IReq::get('costtype');//面值类型  1固定面值  2随机面值	   
       $cost = IReq::get('fjuancost');//优惠券固定面值数组
	   $flimitcost = IReq::get('fjuanlimitcost');//优惠券固定面值限制金额数组
	   $rlimitcost = IReq::get('rjuanlimitcost');//优惠券随机面值限制金额数组
	   $costmin = IReq::get('rjuancostmin');//优惠券随机面值金额下限数组
	   $costmax = IReq::get('rjuancostmax');//优惠券随机面值金额上限数组
	   $paytype = IReq::get('paytype'); //支持类型数组 1在线支付 2货到付款 1,2都支持
	   $timetype = IReq::get('timetype');// 失效时间类型 1固定天数 2固定时间段
	   $days = IReq::get('juanday');  //失效天数
	   if($timetype == 1 && $days <=0 ) $this->message('请输入正确的失效天数');
	   $starttime = IReq::get('starttime');//有效时间开始值	  
	   $endtime = IReq::get('endtime');//有效时间结束值
	   if(strtotime($endtime.' 23:59:59') < strtotime($starttime.' 00:00:00'))$this->message('过期时间不能早于生效时间'); 
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   if($data1['cost']<= 0 || $data1['limitcost']<=0)$this->message('请输入大于0的金额数值');
			   if($data1['cost'] > $data1['limitcost'] )$this->message('优惠券面值不可大于限制门槛金额'); 
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;			   
               $data1['costmax'] = $costmax[$k2];
			   if($data1['costmax'] > $data1['limitcost'] )$this->message('优惠券面值随机数最大值不可大于限制门槛金额');  
		   }   
	   }
	   //更新关注微信领取优惠券设置
	   $data['status'] = $followjuan;
	   $data['costtype'] = $costtype;
	   $data['paytype'] = $paytype == 1?'1':'1,2';
	   $data['timetype'] = $timetype;
	   $data['days'] = $days;
	   $data['starttime'] = strtotime($starttime.' 00:00:00');
	   $data['endtime'] = strtotime($endtime.' 23:59:59');	   
	   $this->mysql->update(Mysite::$app->config['tablepre'].'alljuanset',$data,"type = 2 or name = '注册送优惠券'");
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'alljuan', " type = 2" );//更新时  先删除以前的后插入新的	    
	   $data1['paytype'] = $paytype == 1?'1':'1,2';
       $data1['type'] = 2;
       $data1['name'] = '注册送优惠券';   
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];			    
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;
			   $data1['costmin'] = $costmin[$k2];
               $data1['costmax'] = $costmax[$k2];			   
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }
	   $this->success('success'); 
   }
   /**充值送优惠券相关信息**/
   function rechargesjset(){
	   $data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 3 or name = '充值送优惠券' " );  	   
	   $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 3 or name= '充值送优惠券' order by id asc " );	   
	   Mysite::$app->setdata($data);          
   }
    /**保存充值送优惠券相关信息**/
   function saverechargesjset(){   
		limitalert();   
	   $costtype = IReq::get('costtype');//面值类型  1固定面值  2随机面值	   
       $cost = IReq::get('fjuancost');//优惠券固定面值数组
	   $flimitcost = IReq::get('fjuanlimitcost');//优惠券固定面值限制金额数组
	   $rlimitcost = IReq::get('rjuanlimitcost');//优惠券随机面值限制金额数组
	   $costmin = IReq::get('rjuancostmin');//优惠券随机面值金额下限数组
	   $costmax = IReq::get('rjuancostmax');//优惠券随机面值金额上限数组
	   $paytype = IReq::get('paytype'); //支持类型数组 1在线支付 2货到付款 1,2都支持
	   $timetype = IReq::get('timetype');// 失效时间类型 1固定天数 2固定时间段
	   $days = IReq::get('juanday');  //失效天数
	   if($timetype == 1 && $days <=0 ) $this->message('请输入正确的失效天数');
	   $starttime = IReq::get('starttime');//有效时间开始值	  
	   $endtime = IReq::get('endtime');//有效时间结束值
	   if(strtotime($endtime.' 23:59:59') < strtotime($starttime.' 00:00:00'))$this->message('过期时间不能早于生效时间'); 
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   if($data1['cost']<= 0 || $data1['limitcost']<=0)$this->message('请输入大于0的金额数值');
			   if($data1['cost'] > $data1['limitcost'] )$this->message('优惠券面值不可大于限制门槛金额'); 
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;			   
               $data1['costmax'] = $costmax[$k2];
			   if($data1['costmax'] > $data1['limitcost'] )$this->message('优惠券面值随机数最大值不可大于限制门槛金额');  
		   }   
	   }
	   //更新关注微信领取优惠券设置
	   $data['status'] = $followjuan;
	   $data['costtype'] = $costtype;
	   $data['paytype'] = $paytype == 1?'1':'1,2';
	   $data['timetype'] = $timetype;
	   $data['days'] = $days;
	   $data['starttime'] = strtotime($starttime.' 00:00:00');
	   $data['endtime'] = strtotime($endtime.' 23:59:59');	   
	   $this->mysql->update(Mysite::$app->config['tablepre'].'alljuanset',$data,"type = 3 or name = '充值送优惠券'");
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'alljuan', " type = 3" );//更新时  先删除以前的后插入新的	    
	   $data1['paytype'] = $paytype == 1?'1':'1,2';
       $data1['type'] = 3;
       $data1['name'] = '充值送优惠券';   
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];			  
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;
			   $data1['costmin'] = $costmin[$k2];
               $data1['costmax'] = $costmax[$k2];			  
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }
	   $this->success('success'); 
   }
      /**下单领取优惠券相关信息**/
   function makeordersjset(){
	   $data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 4 or name = '下单送优惠券' " );  	   
	   $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 4 or name= '下单送优惠券' order by id asc " );	   
	   Mysite::$app->setdata($data);          
   }
    /**保存下单领取优惠券相关信息**/
   function savemakeordersjset(){
	   #limitalert();
       $followjuan = IReq::get('followjuan');//是否开启  0关闭 1开启
	   $costtype = IReq::get('costtype');//面值类型  1固定面值  2随机面值	   
       $cost = IReq::get('fjuancost');//优惠券固定面值数组
	   $flimitcost = IReq::get('fjuanlimitcost');//优惠券固定面值限制金额数组
	   $rlimitcost = IReq::get('rjuanlimitcost');//优惠券随机面值限制金额数组
	   $costmin = IReq::get('rjuancostmin');//优惠券随机面值金额下限数组
	   $costmax = IReq::get('rjuancostmax');//优惠券随机面值金额上限数组
	   $paytype = IReq::get('paytype'); //支持类型数组 1在线支付 2货到付款 1,2都支持
	   $timetype = IReq::get('timetype');// 失效时间类型 1固定天数 2固定时间段
	   $days = IReq::get('juanday');  //失效天数
	   if($timetype == 1 && $days <=0 ) $this->message('请输入正确的失效天数');
	   $starttime = IReq::get('starttime');//有效时间开始值	  
	   $endtime = IReq::get('endtime');//有效时间结束值
	   if(strtotime($endtime.' 23:59:59') < strtotime($starttime.' 00:00:00'))$this->message('过期时间不能早于生效时间'); 
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   if($data1['cost']<= 0 || $data1['limitcost']<=0)$this->message('请输入大于0的金额数值');
			   if($data1['cost'] > $data1['limitcost'] )$this->message('优惠券面值不可大于限制门槛金额'); 
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;			   
               $data1['costmax'] = $costmax[$k2];
			   if($data1['costmax'] > $data1['limitcost'] )$this->message('优惠券面值随机数最大值不可大于限制门槛金额');  
		   }   
	   }
	   //更新关注微信领取优惠券设置
	   $data['status'] = $followjuan;
	   $data['costtype'] = $costtype;
	   $data['paytype'] = $paytype == 1?'1':'1,2';
	   $data['timetype'] = $timetype;
	   $data['days'] = $days;
	   $data['starttime'] = strtotime($starttime.' 00:00:00');
	   $data['endtime'] = strtotime($endtime.' 23:59:59');	   
	   $this->mysql->update(Mysite::$app->config['tablepre'].'alljuanset',$data,"type = 4 or name = '下单送优惠券'");
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'alljuan', " type = 4" );//更新时  先删除以前的后插入新的	    
	   $data1['paytype'] = $paytype == 1?'1':'1,2';
       $data1['type'] = 4;
       $data1['name'] = '下单送优惠券';   
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;
			   $data1['costmin'] = $costmin[$k2];
               $data1['costmax'] = $costmax[$k2];
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }
	   $this->success('success'); 
   }
      /**邀请好友送红包相关信息**/
   function invitesjset(){
	   $data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 5 or name = '邀请好友送红包' " );  	   
	   $data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 5 or name= '邀请好友送红包' order by id asc " );	   
	   Mysite::$app->setdata($data);          
   }
    /**保存邀请好友送红包相关信息**/
   function saveinvitesjset(){
	   #limitalert();
       $followjuan = IReq::get('followjuan');//是否开启  0关闭 1开启
	   $costtype = IReq::get('costtype');//面值类型  1固定面值  2随机面值	   
       $cost = IReq::get('fjuancost');//优惠券固定面值数组
	   $flimitcost = IReq::get('fjuanlimitcost');//优惠券固定面值限制金额数组
	   $rlimitcost = IReq::get('rjuanlimitcost');//优惠券随机面值限制金额数组
	   $costmin = IReq::get('rjuancostmin');//优惠券随机面值金额下限数组
	   $costmax = IReq::get('rjuancostmax');//优惠券随机面值金额上限数组
	   $paytype = IReq::get('paytype'); //支持类型数组 1在线支付 2货到付款 1,2都支持
	   $timetype = IReq::get('timetype');// 失效时间类型 1固定天数 2固定时间段
	   $days = IReq::get('juanday');  //失效天数
	   if($timetype == 1 && $days <=0 ) $this->message('请输入正确的失效天数');
	   $starttime = IReq::get('starttime');//有效时间开始值	  
	   $endtime = IReq::get('endtime');//有效时间结束值
	  
	   if(strtotime($endtime.' 23:59:59') < strtotime($starttime.' 00:00:00'))$this->message('过期时间不能早于生效时间'); 
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];
			   if($data1['cost']<= 0 || $data1['limitcost']<=0)$this->message('请输入大于0的金额数值');
			   if($data1['cost'] > $data1['limitcost'] )$this->message('优惠券面值不可大于限制门槛金额'); 
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;			   
               $data1['costmax'] = $costmax[$k2];
			   if($data1['costmax'] > $data1['limitcost'] )$this->message('优惠券面值随机数最大值不可大于限制门槛金额');  
		   }   
	   }
	   //更新关注微信领取优惠券设置
	   $data['status'] = $followjuan;
	   $data['costtype'] = $costtype;
	   $data['paytype'] = $paytype == 1?'1':'1,2';
	   $data['timetype'] = $timetype;
	   $data['days'] = $days;
	   $data['starttime'] = strtotime($starttime.' 00:00:00');
	   $data['endtime'] = strtotime($endtime.' 23:59:59');	   
	   $this->mysql->update(Mysite::$app->config['tablepre'].'alljuanset',$data,"type = 5 or name = '邀请好友送红包'");
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'alljuan', " type = 5" );//更新时  先删除以前的后插入新的	    
	   $data1['paytype'] = $paytype == 1?'1':'1,2';
       $data1['type'] = 5;
       $data1['name'] = '邀请好友送红包';   
	   if($costtype == 1){ //固定面值		   	   
		   foreach($cost as $k1=>$v1){
			   $data1['cost'] = $v1; 			   
			   $data1['limitcost'] = $flimitcost[$k1];			   
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }else{
		   foreach($rlimitcost as $k2=>$v2){
			   $data1['limitcost'] = $v2;
			   $data1['costmin'] = $costmin[$k2];
               $data1['costmax'] = $costmax[$k2];			   
			   $data1['starttime'] = strtotime($starttime.' 00:00:00');
			   $data1['endtime'] = strtotime($endtime.' 23:59:59');	   
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'alljuan',$data1);
		   }   
	   }
	   $this->success('success'); 
   }
   /**添加充值送余额时   选择赠送优惠券数据**/
   function addrechargecost(){   
	$data['juansetinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 3 or name = '充值送优惠券' " );  	   
	$data['juaninfo'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."alljuan where type = 3 or name= '充值送优惠券' order by id asc " );	  
	Mysite::$app->setdata($data);       
   }
   /**促销活动列表**/
   function cxrulelist(){
       $cityid = Mysite::$app->config['default_cityid'];
	   $type = intval(IReq::get('type'));//0全部 1待生效 2进行中 3已结束 4未启用
       $type = in_array($type, array(0, 1, 2, 3, 4)) ? $type : 0;
	   $wherearr = array(
	   '0'=>' ',
	   '1'=>' and limittype = 3  and starttime > '.time().' and status = 1 ',
	   '2'=>' and status = 1 and ( limittype < 3  or ( limittype = 3 and endtime > '.time().' and starttime < '.time().')) ',
	   '3'=>' and limittype = 3 and endtime < '.time().' and status = 1 ',
	   '4'=>' and status =0 '   
	   );	   
       $cxrulelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rule where  parentid = 1 ".$wherearr[$type]."  and  cityid = ".$cityid." order by id desc " );
       $data['cxrulelist'] = $cxrulelist;	  
       $data['type'] = $type;
	   $data['nowtime'] = time();
	   Mysite::$app->setdata($data);                
   }
   function addcxrule(){
       $id = intval(IReq::get('id'));      
       $cxinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."rule where  id = ".$id."   " );
       $cityid = Mysite::$app->config['default_cityid'];       
	   $shoplist = array();
	   $shoplist = $this->mysql->getarr("select id,shopname,shoptype from ".Mysite::$app->config['tablepre']."shop where is_pass = 1 and admin_id = ".$cityid."   " );
	   foreach($shoplist as $k=>$v){
		   $v['cxclass'] = '';
		   //1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减
		   $checkcx1 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 1 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx2 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 2 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx3 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 3 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx4 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 4 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   $checkcx5 = $this->mysql->getarr("select id from ".Mysite::$app->config['tablepre']."rule where controltype = 5 and  FIND_IN_SET(" . $v['id'] . ",shopid)  and status = 1 and parentid = 1 and cityid = ".$cityid." and (  limittype < 3  or ( limittype = 3 and endtime > ".time().")) " );
		   if(!empty($checkcx1)){
			   $v['cxclass'] = $v['cxclass'].'act1  ';
		   }
		   if(!empty($checkcx2)){
			   $v['cxclass'] = $v['cxclass'].'act2  ';
		   }
		   if(!empty($checkcx3)){
			   $v['cxclass'] = $v['cxclass'].'act3  ';
		   }
		   if(!empty($checkcx4)){
			   $v['cxclass'] = $v['cxclass'].'act4  ';
		   }
		   if(!empty($checkcx5)){
			   $v['cxclass'] = $v['cxclass'].'act5  ';
		   }
		   if(in_array($v['id'],explode(',',$cxinfo['shopid']))){
			   $v['cxclass'] = $v['cxclass'].'oldshop  ';
		   }
		   if($v['shoptype']==1){
			   $psinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopmarket where  shopid = ".$v['id']."   " );
			   if($psinfo['sendtype'] == 1){
				   $shopps[] = $v;   
			   }else{
				   $platps[] = $v;   
			   }			   
		   }else{
			   $psinfo = $this->mysql->select_one("select sendtype from ".Mysite::$app->config['tablepre']."shopfast where  shopid = ".$v['id']."   " );
			   if($psinfo['sendtype'] == 1){
				   $shopps[] = $v;   
			   }else{
				   $platps[] = $v;   
			   }
			   
			   
		   } 
	   }
       $data['cxsignlist'] = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."goodssign where type='cx' order by id desc limit 0, 100");
       $data['cxinfo'] = $cxinfo;
       $data['shopps'] = $shopps;	   
	   $data['platps'] = $platps;
	   $data['nowtime'] = time();
	   #print_r($data);
       Mysite::$app->setdata($data);                
   }
    function delcxrule(){
		limitalert();
       $id = IReq::get('id'); 
       if(empty($id))  $this->message('id为空');
       $ids = is_array($id)? join(',',$id):$id;    
       $this->mysql->delete(Mysite::$app->config['tablepre'].'rule',"id in($ids)");  
       $this->success('success'); 
       
    }
    
     function savecxrule(){
		 
        $shopidarr = IReq::get('shopid');
		if(empty($shopidarr))$this->message('请选择参与活动商家');
        $data['shopid'] = implode(',',$shopidarr);	
        $data['parentid'] = intval(IReq::get('parentid'));
        $data['shopbili'] = intval(IReq::get('shopbili'));
        if($data['shopbili']>100)$this->message('网站承担比例数值不能大于100');
		$data['cityid'] = Mysite::$app->config['default_cityid'];
        $data['type'] = 1;//默认购物车限制
		$cxid = intval(IReq::get('cxid'));
        $controltype = intval(IReq::get('controltype'));//1满赠活动 2满减活动 3折扣活动 4免配送费 5首单立减
		$data['controltype'] = $controltype;
        $setinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."cxruleset where  id = ".$controltype."   " );
		$data['imgurl'] = $setinfo['imgurl'];//活动图标
		$data['supporttype'] = $setinfo['supportorder'];//支持订单类型 1支持全部订单 2只支持在线支付订单
		$data['supportplatform'] = $setinfo['supportplat'];//支持平台类型 1pc 2微信 3触屏 4app
		$data['status'] =  intval(IReq::get('status'));
		$ordertype = $data['supporttype']==2?'在线支付满':'满';
		if($controltype == 1){//1满赠活动
			$data['limitcontent'] = intval(IReq::get('limitcontent_1'));
			$data['presenttitle'] = trim(IFilter::act(IReq::get('presenttitle')));
			if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');
			if(empty($data['presenttitle'])) $this->message('请输入赠品名称及数量'); 
			if(!(IValidate::len($data['presenttitle'],2,10))) $this->message('赠品名称及数量2~10个字符之间'); 
			$data['name']= $ordertype.''.$data['limitcontent'].'赠送'.$data['presenttitle'];	 
		}
		if($controltype == 2){//2满减活动
			$limitcontent = IReq::get('limitcontent_2');
			$controlcontent = IReq::get('controlcontent_2');
            $data['limitcontent'] = implode(',',$limitcontent);
			$data['controlcontent'] = implode(',',$controlcontent);			
			$name = $data['supporttype']==2?'在线支付':'';
			foreach($limitcontent as $k=>$v){
				if($controlcontent[$k] > $v)$this->message('减免金额不能大于限制金额');
				$name .= '满'.$v.'减'.$controlcontent[$k].';';
			}
			$data['name'] = rtrim($name, ";");
		}
		if($controltype == 3){//3折扣活动
			$data['limitcontent'] = intval(IReq::get('limitcontent_3'));
			$data['controlcontent'] = IReq::get('controlcontent_3');
			$zhe = $data['controlcontent'];
			if( $zhe <= 0 || $zhe >= 10 )$this->message('折扣值请录入大于0小于10的数值');
			$data['name']= $ordertype.''.$data['limitcontent'].'享'.$zhe.'折优惠';
			if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');
			if(empty($data['controlcontent'])) $this->message('请输入折扣值'); 
		}
		if($controltype == 4){//4免配送费
			$data['limitcontent'] = intval(IReq::get('limitcontent_4'));			 
			$data['name']= $ordertype.''.$data['limitcontent'].'免配送费';
			if(empty($data['limitcontent'])) $this->message('请输入订单限制金额');			 
		}
		if($controltype == 5){//5首单立减
			$data['limitcontent'] = intval(IReq::get('limitcontent_5'));	
            $data['controlcontent'] = intval(IReq::get('controlcontent_5'));
            if($data['controlcontent'] > $data['limitcontent']) $this->message('减免金额不能大于限制金额');						
			$data['name']= '新用户下单满'.$data['limitcontent'].'立减'.$data['controlcontent'].'元';	 	
		}
        if(empty($data['name'])) $this->message('促销标题不能为空');
        $limittype = intval(IReq::get('limittype'));//1不限制 2表示指定星期 3自定义日期
        $data['limittype'] = in_array($limittype,array('1,','2','3')) ? $limittype:1;
        if($data['limittype'] ==  1){
            $data['limittime'] = '';
        }elseif($data['limittype'] == 2){
            $limittime = IFilter::act(IReq::get('limittime1'));
            if(!is_array($limittime)) $this->message('errweek');
            $data['limittime'] = join(',',$limittime);
        }else{
			$starttime = IFilter::act(IReq::get('starttime'));
            $endtime = IFilter::act(IReq::get('endtime'));			
            if(empty($starttime)) $this->message('cx_starttime');
			if(empty($endtime)) $this->message('cx_endtime');        
			$data['starttime'] = strtotime($starttime.' 00:00:00');
			$data['endtime'] = strtotime($endtime.' 23:59:59');
			if($data['endtime'] <= $data['starttime']) $this->message('结束时间不能早于开始时间');     
        }  		
        if(empty($cxid)){
			$data['creattime'] = time();
            $this->mysql->insert(Mysite::$app->config['tablepre'].'rule',$data);
        }else{        
            $this->mysql->update(Mysite::$app->config['tablepre'].'rule',$data,"id='".$cxid."'");
        }
		
        $this->success('success');
    }
   
 
   function delcard(){ 
   limitalert();
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('card_empty'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'card',"id in($ids)");  
	   $this->success('success'); 
   } 
   function saveprensentjuan(){
	   limitalert();
		$siteinfo['regester_juan'] = intval(IReq::get('regester_juan'));
		$siteinfo['regester_juanlimit'] = intval(IReq::get('regester_juanlimit'));
		$siteinfo['regester_juancost'] = intval(IReq::get('regester_juancost'));
		$siteinfo['regester_juanday'] = intval(IReq::get('regester_juanday')); 
		$siteinfo['wx_juan'] = intval(IReq::get('wx_juan'));
		$siteinfo['wx_juancost'] = intval(IReq::get('wx_juancost'));
		$siteinfo['wx_juanlimit'] = intval(IReq::get('wx_juanlimit'));
		$siteinfo['wx_juanday'] = intval(IReq::get('wx_juanday'));
		$siteinfo['login_juan'] = intval(IReq::get('login_juan'));
		$siteinfo['login_data'] = strtotime(IReq::get('login_data'));
		$siteinfo['login_juanlimit'] = intval(IReq::get('login_juanlimit'));
		$siteinfo['login_juancost'] = intval(IReq::get('login_juancost'));
		$siteinfo['login_juanday'] = intval(IReq::get('login_juanday'));
		$siteinfo['tui_juan'] = intval(IReq::get('tui_juan'));
		$siteinfo['tui_juanlimit'] = intval(IReq::get('tui_juanlimit'));
		$siteinfo['tui_juancost'] = intval(IReq::get('tui_juancost'));
		$siteinfo['tui_juanday'] = intval(IReq::get('tui_juanday'));
		$config = new config('hopeconfig.php',hopedir);  
		$config->write($siteinfo); 
		$this->success('success');	
   }
   function cardlist(){ 
       	$searchvalue = intval(IReq::get('searchvalue'));
      	$orderstatus = intval(IReq::get('orderstatus'));
      	$starttime = trim(IReq::get('starttime'));
      	$endtime = trim(IReq::get('endtime'));
      	$newlink = '';
      	$where= '';
      	$data['searchvalue'] = '';
      	if($searchvalue > 0)//限制值
      	{ 
      			 $data['searchvalue'] = $searchvalue;
         	   $where .= ' and  cost = \''.$searchvalue.'\' ';
         	   $newlink .= '/searchvalue/'.$searchvalue; 
      	}
      	 
      	$data['starttime'] = '';
      	if(!empty($starttime))
      	{
      		 $data['starttime'] = $starttime;
      		 $where .= ' and  creattime > '.strtotime($starttime.' 00:00:01').' ';
         	 $newlink .= '/starttime/'.$starttime; 
      	}
      	$data['endtime'] = '';
      	if(!empty($endtime))
      	{
      		 $data['endtime'] = $endtime;
      		 $where .= ' and  creattime < '.strtotime($endtime.' 23:59:59').' ';
         	 $newlink .= '/endtime/'.$endtime; 
      	} 
      	$data['where'] = " id > 0 ".$where;
 	#	print_r($data['where']);
		
		$pageinfo = new page();
 	   $pageinfo->setpage(intval(IReq::get('page')),10); 
		
		$cardlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."card where   ".$data['where']."   order by id desc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."card where   ".$data['where']."   order by id desc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/cardlist'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 		
		
		$data['cardlist'] = $cardlist;
		
      	$link = IUrl::creatUrl('adminpage/card/module/cardlist'.$newlink);
      	$data['outlink'] =IUrl::creatUrl('adminpage/card/module/outcard/outtype/query'.$newlink);           
        Mysite::$app->setdata($data);    
   }
   function carduserecord(){ 
       	$username = IReq::get('username');     	 
      	$starttime = trim(IReq::get('starttime'));
      	$endtime = trim(IReq::get('endtime'));
      	$newlink = '';
      	$where= '';
      	$data['username'] = '';
      	if(!empty($username)) 
      	{ 
      		   $data['username'] = $username;
         	   $where .= ' and  username = \''.$username.'\' ';
         	   $newlink .= '/username/'.$username; 
      	}     	
      	$data['starttime'] = '';
      	if(!empty($starttime))
      	{
      		 $data['starttime'] = $starttime;
      		 $where .= ' and  usetime > '.strtotime($starttime.' 00:00:01').' ';
         	 $newlink .= '/starttime/'.$starttime; 
      	}
      	$data['endtime'] = '';
      	if(!empty($endtime))
      	{
      		 $data['endtime'] = $endtime;
      		 $where .= ' and  usetime < '.strtotime($endtime.' 23:59:59').' ';
         	 $newlink .= '/endtime/'.$endtime; 
      	} 
      	$data['where'] = " id > 0 ".$where;		
		$pageinfo = new page();
 	    $pageinfo->setpage(intval(IReq::get('page')),10); 		
		$cardlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."card where   ".$data['where']." and status = 1   order by id desc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."card where   ".$data['where']." and status = 1  order by id desc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/carduserecord'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 				
		$data['cardlist'] = $cardlist;  	
        Mysite::$app->setdata($data);    
   }
   function savecard()
	{ limitalert();
		$card_temp = trim(IReq::get('card_temp')); 
		$card_acount = intval(IReq::get('card_acount')); 
		$card_cost = intval(IReq::get('card_cost')); 
		if(empty($card_temp))$this->message('card_emptypre');
		if($card_acount < 1)$this->message('card_emptycout');
		if(!in_array($card_cost,array(10,20,50,100,200,500,1000)))$this->message('card_costerr');
		$timenow = time();
		for($i=0;$i< $card_acount;$i++)
		{
			$data['card'] = $card_temp.$timenow.$i.rand(1000,9999);
			$data['card_password'] = substr(md5($data['card']),0,11);
			$data['status'] = 0;
			$data['cost'] = $card_cost;
			$data['creattime'] = $timenow;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'card',$data); 
		}
		$this->success('success');
	}
	function outcard(){ 
		$outtype = IReq::get('outtype'); 
		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}	
		$where = '';
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
			  $where .= ' and id in('.$id.') ';
		}else{
		   $searchvalue = intval(IReq::get('searchvalue'));
		   $where .= $searchvalue > 0? ' and  cost = \''.$searchvalue.'\' ':'';
		   
		   $orderstatus = intval(IReq::get('orderstatus')); 
		   $where .= $orderstatus > 0?' and  status = \''.($orderstatus-1).'\' ':'';
		   
		   $starttime = trim(IReq::get('starttime')); 
		   $where .= !empty($starttime)? ' and  creattime > '.strtotime($starttime.' 00:00:01').' ':'';
		   
		   $endtime = trim(IReq::get('endtime')); 
		   $where .= !empty($endtime)? ' and  creattime < '.strtotime($endtime.' 23:59:59').' ':'';
		}		 
		
		 $outexcel = new phptoexcel();
		 $titledata = array('卡号','密码','充值金额');
		 $titlelabel = array('card','card_password','cost');  
		 $datalist = $this->mysql->getarr("select card,card_password,cost from ".Mysite::$app->config['tablepre']."card where id > 0 ".$where."   order by id desc  limit 0,2000 "); 
		 $outexcel->out($titledata,$titlelabel,$datalist,'','充指卡导出结果'); 
	}
	function juanlist(){		 
	    $searchvalue = intval(IReq::get('searchvalue'));
		$orderstatus = intval(IReq::get('orderstatus'));
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));		
		$newlink = '';
		$where= '';
		$data['searchvalue'] = '';
		if($searchvalue > 0)//限制值
		{ 
			$data['searchvalue'] = $searchvalue;
	   	    $where .= ' and  limitcost = \''.$searchvalue.'\' ';
	   	    $newlink .= '/searchvalue/'.$searchvalue; 
		}       
		$data['orderstatus'] = '';
		if($orderstatus > 0)
		{
			$chastatus = $orderstatus-1 ;
			$data['orderstatus'] = $orderstatus;
	   	    $where .= ' and  status = \''.$chastatus.'\' ';
	   	    $newlink .= '/orderstatus/'.$orderstatus; 
		}

		$data['starttime'] = '';
		if(!empty($starttime))
		{
			$data['starttime'] = $starttime;
			$where .= ' and  creattime > '.strtotime($starttime.' 00:00:01').' ';
	   	    $newlink .= '/starttime/'.$starttime; 
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			$data['endtime'] = $endtime;
			$where .= ' and  creattime < '.strtotime($endtime.' 23:59:59').' ';
	   	    $newlink .= '/endtime/'.$endtime; 
		}  
		$where = ' where id > 0  '.$where;		  
		$link = IUrl::creatUrl('adminpage/card/module/juanlist'.$newlink);
		$pageshow = new page();
		$pageshow->setpage(IReq::get('page'));
		$juanlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan ".$where." order by id desc limit ".$pageshow->startnum().", ".$pageshow->getsize()."");
	    $juanshuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juan ".$where." ");
		$pageshow->setnum($juanshuliang);
		$data['pagecontent'] = $pageshow->getpagebar($link);
		$data['juanlist']=$juanlist;
		$data['outlink'] =IUrl::creatUrl('adminpage/card/module/outjuan/outtype/query'.$newlink);
		$data['nowtime'] = time();
		$data['statustype'] = array(
		    '1'=>'已绑定',
		    '2'=>'已使用',
		    '3'=>'无效'
		);                
		Mysite::$app->setdata($data); 
	}
	function addregjuan(){
		
		$id = intval(IReq::get('id'));
		$regjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."regsendjuan where id  =  ".$id."    "); 
		$data['regjuan'] = $regjuan;
		 Mysite::$app->setdata($data); 
		
	}
	function delregsendcard(){ 
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('card_emptyjuan'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'regsendjuan',"id in($ids)");  
	   $this->success('success'); 
  }
	function saveregsendjuan(){ 
		limitalert();
		$id = intval(IReq::get('id'));
		if(!empty($id)){
			$checkregjuan = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."regsendjuan where id  =  ".$id."    "); 
			if(empty($checkregjuan)){
				$this->message('获取注册优惠券失败');
			}
		}
		$name = trim(IReq::get('name')); 
		$limitcost = trim(IReq::get('limitcost')); 
		$jiancost = trim(IReq::get('jiancost')); 
		$starttime = strtotime(IReq::get('starttime')); 
		$endtime =   strtotime(IReq::get('endtime')); 
		$is_open = intval(IReq::get('is_open')); 
		
		if(empty($name)) $this->message('请填写优惠券名称');
		if(empty($limitcost)) $this->message('请填写最低消费使用金额');
		if(empty($jiancost)) $this->message('请填写优惠金额');
		if(empty($starttime)) $this->message('请填写开始时间');
		if(empty($endtime)) $this->message('请填写结束时间');
		if( $starttime > $endtime ) $this->message('时间填写不规范，请重新选择');
		
		$data['name'] = $name;
		$data['limitcost'] = $limitcost;
		$data['jiancost'] = $jiancost;
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;
		$data['is_open'] = $is_open;
		
		if(empty($id)){
			$this->mysql->insert(Mysite::$app->config['tablepre'].'regsendjuan',$data); 
		}else{
			 
			$this->mysql->update(Mysite::$app->config['tablepre'].'regsendjuan',$data,"id='".$id."'");  
		}
		$this->success('success');
		
	}
	
  function savejuan()
	{ 
		#limitalert();
		$paytype = IReq::get('paytype');
		$paytype = implode(',',$paytype);
		 
		if(empty($paytype))$this->message('请选择优惠券支持的支付方式');
		$card_temp = trim(IReq::get('card_temp')); //卡前缀
		$card_acount = intval(IReq::get('card_acount')); //卡数量
		$card_cost = intval(IReq::get('card_cost')); //优惠金额
		$limit_cost = intval(IReq::get('limit_cost')); //限制金额
		$timetype = intval(IReq::get('timetype'));//1固定天数 2固定时间
		$days = intval(IReq::get('juanday'));//有效天数
		$stime = trim(IReq::get('starttime'));
		$etime = trim(IReq::get('endtime'));
		$starttime = strtotime($stime.' 00:00:01');
		$endtime = strtotime($etime.' 23:59:59');
		$name = trim(IReq::get('name'));
		if($timetype == 1){
			if($days <= 0)$this->message('请填写有效天数');
		}elseif($timetype == 2){
			if(empty($stime) || empty($etime))$this->message('生效时间/过期时间不能为空');
			if($starttime > $endtime)$this->message('生效时间不能晚于过期时间');
		}else{
			$this->message('有效时间类型获取失败');
		}
		if(empty($name)) $this->message('card_emptyjuanname');
		if(empty($card_temp))$this->message('card_emptyjuanpre');
		if($card_acount < 1)$this->message('card_emptyjuancount'); 
		if($card_cost < 1) $this->message('card_emptyjuancost');
		$limit_cost = $limit_cost>0?$limit_cost:0;
		  
		if($card_acount > 100) $this->message('card_emptyjuanlimitcount'); 
		$timenow = time();
		$datetime = strtotime(date('Y-m-d'));
		for($i=0;$i< $card_acount;$i++)
		{
			$data['card'] = $card_temp.$timenow.$i.rand(10,99);
			$data['card_password'] = substr(md5($data['card']),0,5);
			$data['status'] = 0;
			$data['timetype'] = $timetype;
			if($timetype == 2){
				$data['creattime'] = $starttime;
				$data['endtime'] = $endtime;
			}
			$data['days'] = $days;
			$data['cost'] = $card_cost;
			$data['paytype'] = $paytype;
			$data['limitcost'] = $limit_cost;
			$data['name'] = $name;
			$data['type'] = 9;
			//uid ,  usetime username 
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juan',$data); 
		}
		$this->success('success');
	}
	function deljuan(){ 
	// limitalert();
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('card_emptyjuan'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'juan',"id in($ids)");  
	   $this->success('success'); 
  }
  function outjuan()
	{ 
		 $this->checkadminlogin();
		$outtype = IReq::get('outtype'); 
	 
		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}	
		$where = '';
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
			  $where .= ' and id in('.$id.') ';
		}else{
		   $searchvalue = intval(IReq::get('searchvalue'));
		   $where .= $searchvalue > 0? ' and  limitcost = \''.$searchvalue.'\' ':'';
		   
		   $orderstatus = intval(IReq::get('orderstatus')); 
		   $where .= $orderstatus > 0?' and  status = \''.($orderstatus-1).'\' ':'';
		   
		   $starttime = trim(IReq::get('starttime')); 
		   $where .= !empty($starttime)? ' and  creattime > '.strtotime($starttime.' 00:00:01').' ':'';
		   
		   $endtime = trim(IReq::get('endtime')); 
		   $where .= !empty($endtime)? ' and  creattime > '.strtotime($endtime.' 23:59:59').' ':'';
		}		 
		
		 $outexcel = new phptoexcel();
		 $titledata = array('卡号','密码','购物车限制金额','优惠金');
		 $titlelabel = array('card','card_password','limitcost','cost');  
		 $datalist = $this->mysql->getarr("select card,card_password,limitcost,cost from ".Mysite::$app->config['tablepre']."juan where id > 0 ".$where."   order by id desc  limit 0,2000 "); 
		 $outexcel->out($titledata,$titlelabel,$datalist,'','消费卷导出结果'); 
		            	  
	}	  
	function savescore(){ 	 
   	    limitalert();
		$siteinfo['commentscore'] = intval(IReq::get('commentscore'));
		$siteinfo['loginscore'] = intval(IReq::get('loginscore'));
		$siteinfo['regesterscore'] = intval(IReq::get('regesterscore')); 
		$siteinfo['commenttype'] =intval(IReq::get('commenttype'));		 
		$siteinfo['maxdayscore'] =intval(IReq::get('maxdayscore')); 
		$siteinfo['commentday'] = intval(IReq::get('commentday'));
        $siteinfo['consumption']=intval(IReq::get('consumption'));
        $siteinfo['con_extend']=intval(IReq::get('con_extend'));
		$config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
		$this->success('success'); 
  }
  function savescoredx(){ 	 
   	    $siteinfo['scoretocost'] = intval(IReq::get('scoretocost'));
		if($siteinfo['scoretocost'] < 1) $this->message('抵现比例请输入大于0的整数');
		$siteinfo['isopenscoretocost'] = intval(IReq::get('isopenscoretocost'));
        $siteinfo['scoretocostmax'] = intval(IReq::get('scoretocostmax'));			
		$config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
		$this->success('success'); 
  }
   /*礼品兑换模块*/
   function savegifttype(){
	 	   $id = intval(IReq::get('uid'));
		   $data['name'] = IReq::get('name');
		   $data['orderid']  = intval(IReq::get('orderid'));
		   if(empty($data['name'])) $this->message('gift_emptytypename');
		   if(empty($id))
		   {
		   	$this->mysql->insert(Mysite::$app->config['tablepre'].'gifttype',$data);
		   }else{
		   	$this->mysql->update(Mysite::$app->config['tablepre'].'gifttype',$data,"id='".$id."'");
		   }
		   $this->success('success');
	 }
	 function delgifttype(){
	 	 $id = IReq::get('id');
		 if(empty($id))  $this->message('gift_emptytype');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'gifttype',"id in($ids)");
	   $this->success('success');
	 }
   function savegift(){
      $id = IReq::get('uid');
		  $data['title'] = IReq::get('title');
		  $data['content'] = IReq::get('content');
		  $data['market_cost'] = intval(IReq::get('market_cost'));		   
		  $data['score'] = intval(IReq::get('score'));
		  $data['stock'] = intval(IReq::get('stock'));
		  $data['img'] = IReq::get('img');
		  $data['sell_count'] = intval(IReq::get('sell_count'));
		  if(empty($id))
		  {
		  	$link = IUrl::creatUrl('adminpage/card/module/addgift');
		  	if(empty($data['content'])) $this->message('gift_emptycontent',$link);
		  	if(empty($data['title'])) $this->message('gift_emptytitle',$link);
		  	if(empty($data['score'])) $this->message('gift_emptyscore',$link);
		  	$this->mysql->insert(Mysite::$app->config['tablepre'].'gift',$data);
		  }else{
		  	$link = IUrl::creatUrl('adminpage/card/module/addgift/id/'.$id);
		  	if(empty($data['content'])) $this->message('gift_emptycontent',$link);
		  	if(empty($data['title'])) $this->message('gift_emptytitle',$link);
		  	if(empty($data['score'])) $this->message('gift_emptyscore',$link);
		  	$this->mysql->update(Mysite::$app->config['tablepre'].'gift',$data,"id='".$id."'");
		  }
	   	$link = IUrl::creatUrl('adminpage/card/module/giftlist');
		  $this->message('success',$link);
   }
   function delgift(){
   	 $id = IReq::get('id');
		 if(empty($id))  $this->message('gift_empty');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'gift',"id in($ids)");
	   $this->success('success');
   }
  function logstat(){
  	$data['logstat'] = array('0'=>'待处理','1'=>'已处理，配送中','2'=>'兑换完成','3'=>'兑换成功','4'=>'已取消兑换');
  	 Mysite::$app->setdata($data);
  }
  function giftlog(){
    $orderstatus = intval(IReq::get('orderstatus'));
		$starttime = trim(IReq::get('starttime'));
		$endtime = trim(IReq::get('endtime'));
		$newlink = '';
		$where= '';
		$data['orderstatus'] = '';
		if($orderstatus > 0)
		{
			   $chastatus = $orderstatus -1;
			   $data['orderstatus'] = $orderstatus;
	   	   $where .= ' and  gg.status = \''.$chastatus.'\' ';
	   	   $newlink .= '/orderstatus/'.$orderstatus;
		}
		$data['starttime'] ='';
		if(!empty($starttime))
		{
			 $data['starttime'] = $starttime;
			 $where .= ' and  gg.addtime > '.strtotime($starttime.' 00:00:01').' ';
	   	 $newlink .= '/starttime/'.$starttime;
		}
		$data['endtime'] = '';
		if(!empty($endtime))
		{
			 $data['endtime'] = $endtime;
			 $where .= ' and  gg.addtime < '.strtotime($endtime.' 23:59:59').' ';
	   	 $newlink .= '/endtime/'.$endtime;
		}

		$link = IUrl::creatUrl('adminpage/card/module/giftlog'.$newlink);
		$data['outlink'] =IUrl::creatUrl('adminpage/card/module/outgiftlog/outtype/query'.$newlink);

	    $this->pageCls->setpage(IReq::get('page'));
		$data['list'] = $this->mysql->getarr("select gg.*,gf.title,mb.username from ".Mysite::$app->config['tablepre']."giftlog as gg left join ".Mysite::$app->config['tablepre']."gift as gf on gf.id = gg.giftid  left join ".Mysite::$app->config['tablepre']."member as mb on mb.uid=gg.uid where  gg.id > 0  ".$where." order by gg.id desc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."");
		$shuliang  = $this->mysql->counts("select gg.id from ".Mysite::$app->config['tablepre']."giftlog as gg where  gg.id > 0 ".$where." ");
		$this->pageCls->setnum($shuliang);
		$data['pagecontent'] = $this->pageCls->getpagebar($link);

		 Mysite::$app->setdata($data);
  }
  function delgiftlog(){
  	  $id = IReq::get('id');
		 if(empty($id))  $this->message('gift_emptygiftlog');
		 $ids = is_array($id)? join(',',$id):$id;
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'giftlog'," id in($ids) ");
	   $this->success('success');
  }
  function outgiftlog()
	{
		$outtype = IReq::get('outtype');

		if(!in_array($outtype,array('query','ids')))
		{
		  	header("Content-Type: text/html; charset=UTF-8");
			 echo '查询条件错误';
			 exit;
		}
		$where = '';
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
			  $where .= ' and gg.id in('.$id.') ';
		}else{

		   $orderstatus = intval(IReq::get('orderstatus'));
		   $where .= $orderstatus > 0?' and   gg.status = \''.($orderstatus-1).'\' ':'';

		   $starttime = trim(IReq::get('starttime'));
		   $where .= !empty($starttime)? ' and   gg.addtime > '.strtotime($starttime.' 00:00:01').' ':'';

		   $endtime = trim(IReq::get('endtime'));
		   $where .= !empty($endtime)? ' and   gg.addtime > '.strtotime($endtime.' 23:59:59').' ':'';
		}

		 $outexcel = new phptoexcel();
		 $titledata = array('礼品名称','用户名','用户地址','联系电话','联系人');
		 $titlelabel = array('title','username','address','telphone','contactman');

		 $datalist = $this->mysql->getarr("select gf.title,mb.username,gg.address,gg.telphone,gg.contactman from ".Mysite::$app->config['tablepre']."giftlog as gg left join ".Mysite::$app->config['tablepre']."gift as gf on gf.id = gg.giftid  left join ".Mysite::$app->config['tablepre']."member as mb on mb.uid=gg.uid where  gg.id > 0  ".$where." order by gg.id desc  limit 0,2000");

		 $outexcel->out($titledata,$titlelabel,$datalist,'','积分兑换导出结果');
	}
  function exgift()
	{
	   $id = intval(IReq::get('id'));
	   $type = IReq::get('type');//un取消  pass审核  unpass 取消审核  send发货 over完成
	   if(empty($id))  $this->message('gift_empty');
	   $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."giftlog where id=".$id."  ");
	   if(empty($checkinfo))$this->message('gift_emptygiftlog');
	   $giftinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."gift where id=".$checkinfo['giftid']."  ");
	   if(empty($giftinfo)) $this->message('gift_empty');
	    $memberinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."member where uid=".$checkinfo['uid']."  ");
	   switch($type)
	   {
	   	  case 'un':
	   	        //取消 积分兑换
	   	        if($checkinfo['status'] != 0)$this->message('gift_cantlogun');
	   	        //更新兑换记录
	   	        $data['status'] =4;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'giftlog',$data,"id='".$id."'");
	   	        //更新用户积分 并写消息
	   	        if(!empty($memberinfo))
	   	        {
	   	        	   $ndata['score'] = $memberinfo['score'] + $checkinfo['score'];
	                 $this->mysql->update(Mysite::$app->config['tablepre'].'member','`score` = `score`+'.$checkinfo['score'],"uid='".$memberinfo['uid']."'");
	                 $this->memberCls->addlog($memberinfo['uid'],1,1,$checkinfo['score'],'取消兑换礼品','管理员取消兑换ID为:'.$giftinfo['id'].'的礼品['.$giftinfo['title'].'],帐号积分'.$ndata['score'] ,$ndata['score'] );
	   	        }
	   	        //还库存
	   	        $gdata['sell_count'] = $giftinfo['sell_count'] -$checkinfo['count'];
	   	        $gdata['stock'] = $giftinfo['stock'] +$checkinfo['count'];
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'gift',$gdata,"id='".$giftinfo['id']."'");
	   	  break;
	   	  case 'pass':
	   	         if($checkinfo['status'] != 0)$this->message('gift_cantlogpass');
	   	        $data['status'] =1;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'giftlog',$data,"id='".$id."'");
	   	  break;
	   	  case 'unpass':
	   	         if($checkinfo['status'] != 1)$this->message('gift_cantlogunpass');
	   	        $data['status'] =0;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'giftlog',$data,"id='".$id."'");
	   	  break;
	   	  case 'send':
	   	       if($checkinfo['status'] != 1)$this->message('gift_cantlogsend');
	   	        $data['status'] =2;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'giftlog',$data,"id='".$id."'");
	   	  break;
	   	  case 'over':
	   	       if($checkinfo['status'] != 2)$this->message('gift_cantlogover');
	   	        $data['status'] =3;
	   	        $this->mysql->update(Mysite::$app->config['tablepre'].'giftlog',$data,"id='".$id."'");
	   	  break;
	   	  default:
	   	   $this->message('nodefined_func');
	   	  break;
	    }

	  $this->success('success');
  }
  
  function savesendtask(){ 
  
  	$data['taskname'] = IReq::get('taskname'); 
		$data['tasktype'] = IReq::get('tasktype');
		$data['tasktype'] = empty($data['tasktype'])?1:$data['tasktype'];
		$data['taskusertype'] = IReq::get('taskusertype');
		$data['taskusertype'] = empty($data['taskusertype'])?1:$data['taskusertype'];
		$data['usertype'] = IReq::get('usertype');
		$data['userscore'] = IReq::get('userscore');
		$data['creattime_starttime'] = IReq::get('creattime_starttime');
		$data['creattime_endtime'] = IReq::get('creattime_endtime');
		$data['logintime_starttime'] = IReq::get('logintime_starttime');
		$data['logintime_endtime'] = IReq::get('logintime_endtime');
		$data['objcontent'] = IReq::get('objcontent');
		$data['content']  = IReq::get('content');
		  $link = IUrl::creatUrl('adminpage/card/module/sendtask'); 
           
	  if(empty($data['taskname']))  $this->message('task_emptytitle',$link);
	  if(empty($data['content'])) $this->message('task_emptycontent',$link);  
	  $miaoshu = $data['tasktype']==1?'群发邮件':'群发短信'; 
	  if($data['taskusertype'] ==1 )
	  { 
	  	$where = '';
	  	$miaoshu .= '根据条件：';
	  	if($data['usertype'] > 0) 
	  	{
	  		if($data['usertype'] == 1)
	  		{
	  			 $where .= " and usertype  = \'0\' ";
	  		}else{
	  			$where .= " and usertype  = \'1\' ";
	  		} 
	  		$miaoshu .= $data['usertype'] == 1?'普通会员':'商家会员';
	  	}
	  	if($data['userscore'] > 0)
	  	{
	  		$where .= " and score   > ".$data['userscore']." ";
	  		$miaoshu .=  '积分大于'.$data['userscore'];
	  	}
	  	if(!empty($data['creattime_starttime']))
	  	{
	  		 $limittime = strtotime($data['creattime_starttime'].' 00:00:00');
	  		 $where .= " and creattime   > ".$limittime." ";
	  		 $miaoshu .=  '注册时间大于'.$data['creattime_starttime'];
	  	}
	  	if(!empty($data['logintime_starttime']))
	  	{
	  		 $limittime = strtotime($data['creattime_endtime'].' 00:00:00');
	  		 $where .= " and creattime   < ".$limittime." ";
	  		 $miaoshu .=  '注册时间小于'.$data['creattime_endtime'];
	  	}
	  	if(!empty($data['logintime_starttime']))
	  	{
	  		 $limittime = strtotime($data['logintime_starttime'].' 00:00:00');
	  		 $where .= " and logintime   > ".$limittime." ";
	  		 $miaoshu .=  '最近登录时间大于'.$data['logintime_starttime'];
	  	}
	  	if(!empty($data['logintime_endtime']))
	  	{
	  		 $limittime = strtotime($data['logintime_endtime'].' 00:00:00');
	  		 $where .= " and logintime   < ".$limittime." ";
	  		 $miaoshu .=  '最近登录时间小于'.$data['logintime_endtime'];
	  	}
	  	$data['tasklimit'] = $where;
	  	$data['othercontent'] = $miaoshu; 
	  }else{ 
	  	if(empty($data['objcontent'])) $this->message('task_emptyobj',$link);  
	  	$data['tasklimit'] = $data['objcontent'];
	  	$data['othercontent'] = $miaoshu.'指定对象:'.$data['objcontent'];
	  } 
	  unset($data['usertype']);
		unset($data['userscore']);
		unset($data['creattime_starttime']);
		unset($data['creattime_endtime']);
		unset($data['logintime_starttime']);
		unset($data['logintime_endtime']);
		unset($data['objcontent']);
	  $this->mysql->insert(Mysite::$app->config['tablepre'].'task',$data);
	  $link = IUrl::creatUrl('adminpage/card/module/sendtasklist'); 
		 $this->message('',$link);  
  }
  function starttask(){ 
		$taskid = IReq::get('taskid'); 
		$taskinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."task where id='".$taskid."'  "); 
		if(empty($taskinfo))
		{
			echo '任务不存在';
			exit;
		} 
		if($taskinfo['status'] > 1)
		{
			  echo '任务执行完毕,请关闭窗口';
			  exit;
		}
		$data = array('taskmiaoshu'=>'');
		//执行任务
	  if($taskinfo['tasktype'] == 1)
	  {
	  	$emailids = '';//邮箱ID集
	  	$newdata = array();//任务处理数据
	    $data['taskmiaoshu'] .= '邮件群发任务';
	  	if($taskinfo['taskusertype'] == 1)
	  	{
	  	//	echo '根据用户表筛选查询'.$taskinfo['tasklimit'];//tasklimit 
	  		//构造默认查询
	  		$where = ' where uid > '.$taskinfo['start_id'].'  '.$taskinfo['tasklimit']; //start_id;//起点UID
	  		
	  		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where." order by uid asc  limit 0, 10");  
	  		$startid =  $taskinfo['start_id'];
	  		if(count($memberlist) > 9)
	  		{
          	foreach($memberlist as $key=>$value)//循环取出邮件集 
          	{
          	  if(IValidate::email($value['email'])) {
          		 $emailids .= empty($emailids)?	$value['email']:','.$value['email'];
              }  
             $startid = $value['uid'];
             
          	}
	  		}	 
	  		if(count($memberlist) < 10)
	  		{
	  			//更新任务执行完毕
	  			$newdata['status'] = 2;
	  			 $data['taskmiaoshu'] .= ',执行完毕';
	  		}else{
	  			//
	  			$newdata['status'] = 1;
	  			$newdata['start_id'] = $startid;//更新下一页
	  		  $data['taskmiaoshu'] .= ',从用户表uid为'.$taskinfo['start_id'].'执行到uid为'.$startid;
	  		}		 
	  	  //更新任务
	  	}else{
	  		$tasklimit = $taskinfo['tasklimit'];
	  		$checklist = explode(',',$tasklimit);
	  		foreach($checklist as $key=>$value)
	  		{
	  			 if(IValidate::email($value))
	  			  {
	  			  	 $emailids .= empty($emailids)? $value:','.$value;
	  			  }  
	  		} 
	  		$newdata['status'] = 2;
	  		//更新任务
	  		$data['taskmiaoshu'] .= ',根据指定邮箱地址发送邮件完成';
	  	}
	  	//更新任务
	  	 $this->mysql->update(Mysite::$app->config['tablepre'].'task',$newdata,"id='".$taskid."'");  
	  	if(!empty($emailids))
	  	{
	       $smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],false); 
       //$content = iconv('utf-8','gb2312',$content);
        $info = $smtp->send($emailids, Mysite::$app->config['emailname'],$taskinfo['taskname'],$taskinfo['content'] , "" , "HTML" , "" , "");
      } 
      $data['taskdata'] = $newdata;
      $data['showcontent'] = $emailids; 
     
	  }else{ 
	  	
	  	$emailids = '';//邮箱ID集
	  	$newdata = array();//任务处理数据
	    $data['taskmiaoshu'] .= '短信群发任务';
	  	if($taskinfo['taskusertype'] == 1)
	  	{
	  	//	echo '根据用户表筛选查询'.$taskinfo['tasklimit'];//tasklimit 
	  		//构造默认查询
	  		$where = ' where uid > '.$taskinfo['start_id'].'  '.$taskinfo['tasklimit']; //start_id;//起点UID
	  		
	  		$memberlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."member ".$where." order by uid asc  limit 0, 10");  
	  		$startid =  $taskinfo['start_id'];
	  		if(count($memberlist) > 9)
	  		{
          	foreach($memberlist as $key=>$value)//循环取出邮件集 
          	{
          	  if(IValidate::suremobi($value['phone'])) {
          		 $emailids .= empty($emailids)?	$value['phone']:','.$value['phone'];
              }  
             $startid = $value['uid'];
             
          	}
	  		}	 
	  		if(count($memberlist) < 10)
	  		{
	  			//更新任务执行完毕
	  			$newdata['status'] = 2;
	  			 $data['taskmiaoshu'] .= ',执行完毕';
	  		}else{
	  			//
	  			$newdata['status'] = 1;
	  			$newdata['start_id'] = $startid;//更新下一页
	  		  $data['taskmiaoshu'] .= ',从用户表uid为'.$taskinfo['start_id'].'执行到uid为'.$startid;
	  		}		 
	  	  //更新任务
	  	}else{
	  		$tasklimit = $taskinfo['tasklimit'];
	  		$checklist = explode(',',$tasklimit);
	  		foreach($checklist as $key=>$value)
	  		{
	  			 if(IValidate::suremobi($value))
	  			  {
	  			  	 $emailids .= empty($emailids)? $value:','.$value;  
	  			  }  
	  		} 
	  		$newdata['status'] = 2;
	  		//更新任务
	  		$data['taskmiaoshu'] .= ',根据指定手机号发送短信完成';
	  	}
	  	//更新任务
	  	$data['showcontent'] = $emailids; 
	   if(!empty($emailids))
	   {
		   
	      	  	
	      	  	$data['taskmiaoshu'] .= ',不支持群发,错误代码:'.$chekcinfo;
	      	  
	    } 
		  
      $data['taskdata'] = $newdata;
      
	  } 
	 Mysite::$app->setdata($data);
	}
	function deltask()
	{ limitalert();
		 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('task_empty'); 
		 $ids = is_array($id)? join(',',$id):$id; 
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'task'," id in($ids) ");   
	   $this->success('success');//(array('error'=>false)); 
	 }
	 
	 
 /* 8.3新增  2016-05-29  zem	 */
	 
	 function juanmarketing(){
		 
		 	$name = IReq::get('name');
       	$type = intval(IReq::get('type'));
      	$starttime = trim(IReq::get('starttime'));
      	$endtime = trim(IReq::get('endtime'));
      	$newlink = '';
      	$where= '';
       	$data['name'] = '';
      	if(!empty($name))//限制值
      	{ 
      			 $data['name'] = $name;
				 $where .= " and name like '%".$name."%'";
          	    $newlink .= '/name/'.$name; 
      	}
      	
		$data['type'] = '';
      	if(!empty($type))//限制值
      	{ 
      			 $data['type'] = $type;
				 $where .= " and type =  ".$type." ";
          	    $newlink .= '/type/'.$type; 
      	}
      	 
		 
		 
      	$data['starttime'] = '';
      	if(!empty($starttime))
      	{
      		 $data['starttime'] = $starttime;
      		 $where .= ' and  addtime > '.strtotime($starttime.' 00:00:01').' ';
         	 $newlink .= '/addtime/'.$starttime; 
      	}
      	$data['endtime'] = '';
      	if(!empty($endtime))
      	{
      		 $data['endtime'] = $endtime;
      		 $where .= ' and  endtime < '.strtotime($endtime.' 23:59:59').' ';
         	 $newlink .= '/endtime/'.$endtime; 
      	} 
      	$data['where'] = " id > 0 ".$where;
 	  
		 
		
		$pageinfo = new page();
 	   $pageinfo->setpage(intval(IReq::get('page')),10); 
		
		$marketinglist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juanrule where   ".$data['where']."   order by orderid asc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 
		#print_r($marketinglist);
		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juanrule where   ".$data['where']."   order by orderid asc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/juanmarketing'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 		
		
		$data['marketinglist'] = $marketinglist;
		
		
		
		$data['juantypename'] = array(
			'1'=>'充值',
			'2'=>'下单成功分享',
			'3'=>'推广',
			'4'=>'首次关注微信领取',
		);
		
		
           Mysite::$app->setdata($data);
		
		
	 }
	 
	 
	 function savemarketing()
	{ 
		limitalert();
		$id = intval(IReq::get('id'));  
		$data['id'] = $id;
		$name = trim(IReq::get('name')); 
		$type = intval(IReq::get('type')); 
		$juantotalcost = intval(IReq::get('juantotalcost')); 
		$juannum = intval(IReq::get('juannum')); 
		$jiancostmin = intval(IReq::get('jiancostmin')); 
		$jiancostmax = intval(IReq::get('jiancostmax')); 
		$jiacostmin = intval(IReq::get('jiacostmin')); 
		$jiacostmax = intval(IReq::get('jiacostmax')); 
		$paytype = IReq::get('paytype'); 
		$daynum = intval(IReq::get('daynum')); 
		$is_open = intval(IReq::get('is_open')); 
 		$orderid = intval(IReq::get('orderid')); 
		  
		 
		if(empty($name))$this->message('请填写名称！');
		if($type <= 0)$this->message('请选择优惠券类型');
		if(!in_array($type,array(1,2,3,4)))$this->message('获取优惠券类型失败');
		if($type != 1){
			if(empty($juannum))$this->message('优惠券数量为空');
		}
		if( $jiancostmax > 0 ){
			if( $jiancostmin > $jiancostmax ) {   
				$this->message('请正确填写优惠券限制金额范围');  
			}
		} 
		if( $jiacostmin > $jiacostmax )$this->message('请正确填写优惠券优惠金额范围');
		
		 $tempvalue = '';
		 if(is_array($paytype)){
		 	$tempvalue = join(',',$paytype);
		 }

		if(empty($tempvalue))$this->message('请选择优惠券支持的支付方式');
	
		// if(substr($daynum,1,1) == '.')$this->message('请填写天数');exit;
		if(empty($daynum))$this->message('请填写优惠券有效时间');

		
		
		
		
		$data['name'] = $name;
		$data['type'] = $type;
		$data['juantotalcost'] = $juantotalcost;
		$data['juannum'] = $juannum;
		$data['jiancostmin'] = $jiancostmin;
		$data['jiancostmax'] = $jiancostmax;
		$data['jiacostmin'] = $jiacostmin;
		$data['jiacostmax'] = $jiacostmax;
		$data['paytype'] = $tempvalue;

		$data['is_open'] = $is_open;
		$data['orderid'] = $orderid;
		
		
 		if(empty($id)){
			 $data['addtime'] = time();
			$data['endtime'] = $data['addtime']+$daynum*24*60*60;
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juanrule',$data); 
		}else{
			$juans = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanrule where  id={$id}  order by orderid asc");
			$data['endtime'] =  $juans['addtime']+$daynum*24*60*60;
			$this->mysql->update(Mysite::$app->config['tablepre'].'juanrule',$data,"id='".$id."'");  
		}
		$this->success('success');
	} 
	 
	 function delmarketing(){ 
	 limitalert();
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('获取失败'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'juanrule',"id in($ids)");  
	   $this->success('success'); 
   } 
   
   
    
	 function saverechargecost(){
		$id = intval(IReq::get('id'));  
		$data['id'] = $id;
		$cost = intval(IReq::get('cost')); 
		$is_sendcost = intval(IReq::get('is_sendcost')); 
		$sendcost = trim(IReq::get('sendcost')); 
		$is_sendjuan = intval(IReq::get('is_sendjuan'));  		 
		$orderid = intval(IReq::get('orderid')); 
        $juanid = intval(IReq::get('juanid'));
	     
		if(empty($cost))$this->message('请填写充值金额！');
 		if( $is_sendcost > 0 ){
			if(empty($sendcost))$this->message('请填写赠送金额！');
		}
		$juaninfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuan where type  = 3 and id = ".$juanid."   "); 		
		if( $is_sendjuan == 1 ){				
			if(empty($juaninfo)) $this->message('优惠券信息获取失败！');
		} 
	    $juansetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."alljuanset where type = 3 or name = '充值送优惠券' " );  	   	    
 		 
		$data['juanid'] = $juanid;
		$data['cost'] = $cost;
		$data['is_sendcost'] = $is_sendcost;
		$data['sendcost'] = $sendcost;
		$data['is_sendjuan'] = $is_sendjuan; 
		if($juansetinfo['costtype'] == 1){
			$sendjuancost = $juaninfo['cost'];
		}else{			 
			$sendjuancost = rand($juaninfo['costmin'],$juaninfo['costmax']);			 
		}
		$data['sendjuancost'] = $sendjuancost; 
		$data['orderid'] = $orderid;
 		if(empty($id)){
			$this->mysql->insert(Mysite::$app->config['tablepre'].'rechargecost',$data); 
		}else{
			 
			$this->mysql->update(Mysite::$app->config['tablepre'].'rechargecost',$data,"id='".$id."'");  
		}
		$this->success('success');
	} 
	 
	 
	 function rechargezend(){
		 
		 	$cost = IReq::get('cost');
       	$newlink = '';
      	$where= '';
       	 
      	
		$data['cost'] = '';
      	if(!empty($cost))//限制值
      	{ 
      			 $data['cost'] = $cost;
				 $where .= " and cost =  ".$cost." ";
          	    $newlink .= '/cost/'.$cost; 
      	}
      	 
		 
		 
      	$data['where'] = " id > 0 ".$where;
 	  
		 
		
		$pageinfo = new page();
 	   $pageinfo->setpage(intval(IReq::get('page')),10); 
		
		$rechargelist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."rechargecost where   ".$data['where']."   order by orderid asc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."rechargecost where   ".$data['where']."   order by orderid asc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/rechargezend'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 		
		
                $data['shuliang'] =$shuliang;
                
		$data['rechargelist'] = $rechargelist;
		 
		$data['juantypename'] = array(
			'1'=>'充值',
			'2'=>'下单成功分享',
			'3'=>'推广',
		);
		
		
           Mysite::$app->setdata($data);
		
		
	 }
	  
	 function delrechargecost(){ 
	 limitalert();
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('获取失败'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'rechargecost',"id in($ids)");  
	   $this->success('success'); 
   } 
   
     public function juanupload()
	 {
		$uploaddir =IFilter::act(IReq::get('uploaddir'));
		$_FILES['imgFile'] = $_FILES['head'];
	 
		$uploaddir = empty($uploaddir)?'other':$uploaddir;
				$default_cityid = Mysite::$app->config['default_cityid'];
			if( !empty($default_cityid) ){
				$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
			
 			$upload = new upload($uploadpath);//upload 自动生成压缩图片 
			$filedir = $upload->getSigImgDir(); 
			$filedir = Mysite::$app->config['imgserver'].$filedir;
		 
	   
		  if($upload->errno!=15&&$upload->errno!=0) {
				  $this->message($upload->errmsg());
			  }else{ 
				  $this->success($filedir);
			  }
	 }
	 
	  
	  function savejuanshowinfo(){ 
		$id = intval(IReq::get('id'));  
                if($id == 1){
                    $data['type'] = 2;
                }elseif($id == 2){
                    $data['type'] = 3;
                }else{
					$data['type'] = 1;
				}
		$data['id'] = $id;	 
		$bigimg = trim(IReq::get('bigimg')); 
		$color = trim(IReq::get('color')); 
		$actcolor = trim(IReq::get('actcolor')); 
		$avtrule = trim(IReq::get('content')); 		 
		if(empty($bigimg)) $this->message('请上传领取优惠券页面头部展示大图！');
		if(empty($color)) $this->message('请填写领取优惠券页面背景色！');
		if(empty($actcolor)) $this->message('请填写领取优惠券页面活动规则背景色！');
		if(empty($avtrule)) $this->message('请填写活动规则！');
 
 		$data['bigimg'] = $bigimg;
 		$data['color'] = $color;
 		$data['actcolor'] = $actcolor;
 		$data['avtrule'] = $avtrule;

 		 $checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where   id = ".$id." "); 
 		if(empty( $checkinfo)){
			 $data['addtime'] = time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juanshowinfo',$data); 
		}else{
			
			$this->mysql->update(Mysite::$app->config['tablepre'].'juanshowinfo',$data,"id='".$id."'");  
		}
		$this->success('success');
	} 
	function savejuanshareinfo(){ 
		$id = intval(IReq::get('id'));  
		$data['id'] = $id; 
		$title = trim(IReq::get('title')); 
		$img = trim(IReq::get('img')); 
		$describe = trim(IReq::get('describe')); 
		$url = '/index.php?ctrl=adminpage&action=card&module=addshareinfo&id='.$id;
		if(empty($title)) $this->message('请填写标题！',$url);
		if(empty($img)) $this->message('请上传分享展示图标！',$url);
		if(empty($describe)) $this->message('请填写描述！',$url);
 		$data['type'] = $type;
 		$data['title'] = $title;
 		$data['img'] = $img;
 		$data['describe'] = $describe;
 		if(empty($id)){
			 $data['addtime'] = time();
			$this->mysql->insert(Mysite::$app->config['tablepre'].'juanshowinfo',$data); 
		}else{
			
			$this->mysql->update(Mysite::$app->config['tablepre'].'juanshowinfo',$data,"id='".$id."'");  
		}
		$this->success('success',$url);
	}  
	 
    function sharejsinfo(){
		 
		$title = IReq::get('title');
       	$type = intval(IReq::get('type'));
       	$newlink = '';
      	$where= '';
       	$data['title'] = '';
      	if(!empty($title))//限制值
      	{ 
      			 $data['title'] = $title;
				 $where .= " and title like '%".$title."%'";
          	    $newlink .= '/title/'.$title; 
      	}
      	
		$data['type'] = '';
      	if(!empty($type))//限制值
      	{ 
      			 $data['type'] = $type;
				 $where .= " and type =  ".$type." ";
          	    $newlink .= '/type/'.$type; 
      	} 
		 
		 
      	$data['where'] = " id > 0 ".$where;
 	  
		 
		
		$pageinfo = new page();
 	   $pageinfo->setpage(intval(IReq::get('page')),10); 
		
		$shareshowinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juanshowinfo where   ".$data['where']."   order by orderid asc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juanshowinfo where   ".$data['where']."   order by orderid asc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/sharejsinfo'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 		
		
		$data['shareshowinfo'] = $shareshowinfo;
		
		
		
		$data['juantypename'] = array(
 			'2'=>'下单分享页面',
			'3'=>'推广页面',
			'4'=>'关注微信领取优惠券',
		);
		
		
           Mysite::$app->setdata($data);
		
		
	 }
	 
	  function delsjsinfo(){ 
	  limitalert();
   	 $id = IReq::get('id'); 
		 if(empty($id))  $this->message('获取失败'); 
		 $ids = is_array($id)? join(',',$id):$id;    
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'juanshowinfo',"id in($ids)");  
	   $this->success('success'); 
   } 
   
  function receivejuanlog(){  // 优惠券领取记录列表
		 
		$name = IReq::get('name');
		$username = IReq::get('username');
		$bangphone = IReq::get('bangphone');
       	$type = intval(IReq::get('type'));
       	$status = intval(IReq::get('status'));
      	$starttime = trim(IReq::get('starttime'));
      	$endtime = trim(IReq::get('endtime'));
      	$newlink = '';
      	$where= '';
       	$data['name'] = '';
      	if(!empty($name))//限制值
      	{ 
      			 $data['name'] = $name;
				 $where .= " and name like '%".$name."%'";
          	    $newlink .= '/name/'.$name; 
      	}
      	 	$data['username'] = '';
      	if(!empty($username))//限制值
      	{ 
      			 $data['username'] = $username;
				 $where .= " and username like '%".$username."%'";
          	    $newlink .= '/username/'.$username; 
      	}
      		 	$data['bangphone'] = '';
      	if(!empty($bangphone))//限制值
      	{ 
      			 $data['bangphone'] = $bangphone;
				 $where .= " and bangphone like '%".$bangphone."%'";
          	    $newlink .= '/bangphone/'.$bangphone; 
      	}
      	
		$data['type'] = '';
      	if(!empty($type))//限制值
      	{ 
      			 $data['type'] = $type;
				 $where .= " and type =  ".$type." ";
          	    $newlink .= '/type/'.$type; 
      	}
      	 $data['status'] = '';
      	if(!empty($status))//限制值
      	{ 			
				$newstatus = $status-1;
      			 $data['status'] = $status;
				 $where .= " and status =  ".$newstatus." ";
          	    $newlink .= '/status/'.$newstatus; 
      	}
      	  
      	$data['starttime'] = '';
      	if(!empty($starttime))
      	{
      		 $data['starttime'] = $starttime;
      		 $where .= ' and  creattime > '.strtotime($starttime.' 00:00:01').' ';
         	 $newlink .= '/addtime/'.$starttime; 
      	}
      	$data['endtime'] = '';
      	if(!empty($endtime))
      	{
      		 $data['endtime'] = $endtime;
      		 $where .= ' and  creattime < '.strtotime($endtime.' 23:59:59').' ';
         	 $newlink .= '/endtime/'.$endtime; 
      	} 
      	$data['where'] = " id > 0 ".$where; 
		$pageinfo = new page();
 	   $pageinfo->setpage(intval(IReq::get('page')),10); 
		
		$receivejuanlog = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."juan where  id > 0 and  ".$data['where']."   order by creattime desc    limit ".$pageinfo->startnum().", ".$pageinfo->getsize()."  "); 

		$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."juan where   id > 0  and   ".$data['where']."   order by creattime desc");
		$pageinfo->setnum($shuliang);
		$pagelink = IUrl::creatUrl('adminpage/card/module/receivejuanlog'.$newlink);
		$data['pagecontent'] = $pageinfo->getpagebar($pagelink); 	 
		$data['receivejuanlog'] = $receivejuanlog; 
		$data['juantypename'] = array(
			'1'=>'关注送优惠券',
			'2'=>'注册送优惠券',
			'3'=>'充值送优惠券',
			'4'=>'下单发红包',
			'5'=>'邀请好友送红包',
			'6'=>'后台群发优惠券',
			'9'=>'后台添加优惠券',
		); 
		$data['juanstatus'] = array(
			'0'=>'未使用',
			'1'=>'已绑定',
			'2'=>'已使用',
			'3'=>'无效',
		); 
           Mysite::$app->setdata($data);
		
		
	 }
 function savesharejuanset(){   //保存分享优惠券设置
 limitalert();
	    $siteinfo['userordersharejuan'] =  intval(IReq::get('userordersharejuan'));
	    $siteinfo['userextensionsharejuan'] =  intval(IReq::get('userextensionsharejuan'));
	    $config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
	    $configs = new config('hopeconfig.php',hopedir);   
	    $tests = $config->getInfo();
		$this->success('success');
 }  
   
   	 
	
/*
*	8.3新增功能 
*	2016-06-04------ 
*	zem   
*/
	 function setstatus(){
	    $data['shoptype'] = array('0'=>'外卖','1'=>'超市');
	   Mysite::$app->setdata($data);
	}
	function virtualinfo(){	//增加店铺虚拟信息
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	     
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
	    $data['name'] =  trim(IReq::get('name'));
	   $data['username'] =  trim(IReq::get('username'));
	   $data['shop_type'] =  intval(IReq::get('shop_type'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 if(!empty($data['shopname'])){
 		    $where .= " and shopname like '%".$data['shopname']."%'";
	 	 } 
 		 if(!empty($data['shop_type'])){
			 $newshoptype = $data['shop_type']-1;
 		    $where .= " and shoptype = '".$newshoptype."'  ";
	 	 }
	 	 if(!empty($data['username'])){
	 	   $where .= " and uid in(select uid from ".Mysite::$app->config['tablepre']."member where username='".$data['username']."')";
	 	 }
	 	 if(!empty($data['phone'])){
	 	    $where .=" and phone='".$data['phone']."'";
	 	 }
 	 	 //构造查询条件
	 	 $data['where'] = $where; 
	    
		
		 if(!empty($data['shopname'])){
 		    $goodswhere .= " and shopname like '%".$data['shopname']."%'";
	 	 }
		 if(!empty($data['name'])){
	 	    $goodswhere .= " and name like '%".$data['name']."%'";
	 	 }
	 
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	 
 			$selectlist = $this->mysql->getarr("select id,shopname,phone,shoptype,uid,virtualsellcounts from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where."  order by sort asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
 			$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where." ");
 	   
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
 	function saveshopsellcount(){   //保存店铺虚拟总销量
	// limitalert();
		$shopid = intval(IReq::get('shopid'));
		$virtualsellcounts= intval(IReq::get('savesellcounts'));
		$data['virtualsellcounts'] = $virtualsellcounts;
 		$this->mysql->update(Mysite::$app->config['tablepre'].'shop',$data,"id='".$shopid."'");
		$this->success('success');
	}
	
	
	function virtualgoods(){	//增加商品虚拟信息
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	    $goodswhere2 = '';
	     $shopid =  intval(IReq::get('id'));
 		 $shopinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."shop   where id = '".$shopid."'  ");
 		if(empty($shopinfo)){
			echo "获取店铺失败";
			exit;
		}
		$data['shopinfo'] = $shopinfo;
 	    $data['name'] =  trim(IReq::get('name'));
 	 	 
 	 	 //构造查询条件
	 	 $data['where'] = $where; 
	     
		 if(!empty($data['name'])){
	 	    $goodswhere .= " and name like '%".$data['name']."%'";
	 	    $goodswhere2 .= " and goodsname like '%".$data['name']."%'";
	 	 }
	 
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	 
  $selectlist1 = $this->mysql->getarr("select id,name,sellcount,virtualsellcount from ".Mysite::$app->config['tablepre']."goods  where shopid = '".$shopinfo['id']."'  ".$goodswhere."  order by good_order asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
	$selectlist3 =array();
  $selectlist2 = $this->mysql->getarr("select id,goodsid,goodsname,attrname from ".Mysite::$app->config['tablepre']."product  where shopid = '".$shopinfo['id']."'  ".$goodswhere2."  order by id asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
 
	foreach($selectlist2 as $key=>$val){ 
		$val['name'] = $val['goodsname'].'【'.$val['attrname'].'】';
		$val['id'] = $val['goodsid'];
		$selectlist3[] = $val; 
	}
 	$selectlist = array_merge($selectlist1,$selectlist3);

 $shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goods  where shopid = '".$shopinfo['id']."'   ".$goodswhere." ");
 	   
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
	 
	function savevirtualgoodcom(){  //后台保存添加商品虚拟评价
		limitalert();
		$goodid = intval(IReq::get('goodid'));
		$point = intval(IReq::get('point'));
		$content = trim(IReq::get('content'));
		$addtime = trim(IReq::get('addtime'));
		$virtualname = trim(IReq::get('virtualname'));   // 新增   虚拟人名称
		
		 $goodsinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."goods   where id = '".$goodid."'  ");
		 if(empty($goodsinfo)) $this->message('获取商品信息失败');
		 if(empty($point)) $this->message('请对商品进行评分');
		 if(empty($virtualname)) $this->message('请填写评论人');
		
		
		$data['goodsid'] = $goodid;
		$data['shopid'] = $goodsinfo['shopid'];
		$data['content'] = $content;
		$data['addtime'] = strtotime($addtime);
		$data['point'] = $point;
		$data['is_show'] = 0;
		$data['virtualname'] = $virtualname; 
		$this->mysql->insert(Mysite::$app->config['tablepre'].'comment',$data);
		$this->success('success');
	}
	function wxnotice(){
		$info = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."wxnotice   where id >0 and id !=10 order by id");
		$data['list'] = array();
		$temparr = array();
		$det1 = array();
		$det2 = array();
		$det3 = array();
		if(!empty($info)){
			foreach($info as $k=>$val){
				if($val['parent_type']==0){
					$temparr[] = $val;
				}else{
					if($val['parent_type']==1){
						$det1[] = $val;
					}else if($val['parent_type']==2){
						$det2[] = $val;
					}else if($val['parent_type']==3){
						$det3[] = $val;
					}
				}				
			}
		}
		foreach($temparr as $k=>$val){
			if($val['type']==1){
				$val['det'] = $det1;
			}else if($val['type']==2){
				$val['det'] = $det2;
			}else if($val['type']==3){
				$val['det'] = $det3;
			}
			$data['list'][] = $val;
		}
		#print_r($data);exit;
		Mysite::$app->setdata($data);
	}
	function updatewxnotice(){
		$type = intval(IReq::get('changetype'));
		$parent_type = intval(IReq::get('parent_type'));
		$notice = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxnotice   where type=".$type." and parent_type=".$parent_type." ");
		if(empty($notice)) $this->message('不存在该通知类型');
		if($notice['is_open']==1){
			$data['is_open'] =0;
		}else{
			$data['is_open'] =1;
		}
		$this->mysql->update(Mysite::$app->config['tablepre'].'wxnotice',$data,"id=".$notice['id']."");
		$this->success('success');
	}	 
	function savewxnotice(){
		$type = intval(IReq::get('type'));
		$is_open = intval(IReq::get('openwxnotice_'.$type));
		$template_id = IReq::get('template_id_'.$type);
		$link = Mysite::$app->config['siteurl'].'/index.php?ctrl=adminpage&action=card&module=wxnotice';
		$notice = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."wxnotice   where type=".$type." and parent_type=0 ");
		if(empty($notice)) $this->message('不存在该通知类型');
		if(empty($template_id) && $is_open==1) $this->message('模板id不能为空');
		$data['is_open'] =$is_open;
		$data['template_id'] =$template_id;
		#print_r($data);exit;
		$this->mysql->update(Mysite::$app->config['tablepre'].'wxnotice',$data,"id=".$notice['id']."");
		$this->success('success',$link);
	}	 

	 
	 
	 
	 
}