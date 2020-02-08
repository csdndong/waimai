<?php
class method   extends adminbaseclass
{ 
    function saveweatherset(){
		$siteinfo['is_open_weather'] = trim(IReq::get('is_open'));
		$siteinfo['weatherkey'] = trim(IReq::get('weatherkey'));
	    $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);	   
	    $tests = $config->getInfo();
	    $this->success('success');
	}
	function setCityDatas(){
		 
		 
		 
		 $default_cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		
		 if( empty($default_cityid) ){
			 $this->message("请先选择设置默认城市");
		 }
		 $checkshopinfo = $this->mysql->select_one("select   * from ".Mysite::$app->config['tablepre']."area where adcode='".$default_cityid."' ");
 		if(empty($checkshopinfo)){
			$this->message('所选城市不存在');
		}
		 $data['cityid'] = $default_cityid;
		 $addata['admin_id'] = $default_cityid;
		 
		 $where = ' id > 0 ';
		 $uwhere = ' uid > 0 ';
		 
	 	 $this->mysql->update(Mysite::$app->config['tablepre'].'area',$addata,$where); //更新地址
		 $this->mysql->update(Mysite::$app->config['tablepre'].'adv',$data,$where); //更新广告图
	  	 $this->mysql->update(Mysite::$app->config['tablepre'].'appadv',$data,"type=2"); //更新分类
		 $this->mysql->update(Mysite::$app->config['tablepre'].'shop',$addata,$where); //更新店铺绑定城市
		 $this->mysql->update(Mysite::$app->config['tablepre'].'member',$addata,$uwhere); //更新配送员绑定城市
		 $this->mysql->update(Mysite::$app->config['tablepre'].'information',$data,$where); //更新网站通知和生活服务绑定城市
		 $this->mysql->update(Mysite::$app->config['tablepre'].'paotuiset',$data,$where); //更新跑腿信息设置绑定城市
		 $this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,$where); //专题页
		  
		 
		 $this->success('success');
	 }
	 function logshow(){
		 
	 
$checkstr = date('Y-m-d',time());
$data['showlog'] = file_get_contents(hopedir.'log/'.$checkstr.'.php');
// print_r(hopedir.'log/'.$checkstr.'.php');
 print_r($data['showlog']);
 exit;
  Mysite::$app->setdata($data); 
	 }
	 
	 function sindex(){
	  
		 
	   $mftime = strtotime(date('Y-m',time()));
		 $metime = time();//strtotime(date('Y-m',time()).'-'.date('t',time()).' 23:59:59 ');//,"lasttime"=>mktime(23,59,59,$m,$d,$y)); 
		 $dftime = strtotime(date('Y-m-d',time())); 
		 // print_r($dftime);
		 $detime = time();//今天订单将配送时间做为当前时间
		 // print_r($detime);
               $nowday = date('Y-m-d',time());
	      $where = '  where addtime > '.strtotime($nowday.' 00:00:00').' and addtime < '.strtotime($nowday.' 23:59:59');
     // 今日总订单	
     $tjdata['dayallorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  ".$where."  ");
     // print_r($tjdata['dayallorder']);
     //今日待审核订单	  
	   $tjdata['dayworder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  ".$where."  and status = 0");
     /// 今日已审核订单
     $tjdata['dayporder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  ".$where."  and status > 0 and status < 4");
     // 本月已完成订单	 
     $tjdata['monthallorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where addtime > $mftime and addtime < $metime  and status = 3");//
     /// 已完成订单总量 
	   $tjdata['allorder'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order  where  status = 3"); 
    //已上线商家
    $tjdata['onlineshop'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 1");
    //待审核商家
        $tjdata['wshop'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 0  ");
//    $tjdata['wshop'] = $this->mysql->counts("select * from ".Mysite::$app->config['tablepre']."shop  where  is_pass = 0 and admin_id = ".Mysite::$app->config['default_cityid']." ");
    //普通会员
    $tjdata['pmember'] = $this->mysql->counts("select uid from ".Mysite::$app->config['tablepre']."member ");
    //商城订单
    $tjdata['market'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."order where shoptype=1 ");
    //商品数量
    $tjdata['marketg'] = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."goods");
    $data['tjdata'] = $tjdata; 
	  $data['serverurl'] = Mysite::$app->config['serverurl']; 
	  Mysite::$app->setdata($data); 
	 }
	  
	 function saveotherset(){ 
		
 		
	 	$siteinfo['addresslimit'] = 10000;
	    $siteinfo['shoptypelimit'] = 10000;
	    $siteinfo['shopgoodslimit'] = 10000; 
		$siteinfo['allowedcode'] = intval(IReq::get('allowedcode')); 
		$siteinfo['allowedsendshop'] = intval(IReq::get('allowedsendshop'));
		$siteinfo['allowedsendbuyer'] = intval(IReq::get('allowedsendbuyer'));
		$siteinfo['pay_wechat'] = intval(IReq::get('pay_wechat'));
		$siteinfo['man_ispass'] = intval(IReq::get('man_ispass'));//为1管理员后台审核
	     
		
		$siteinfo['addAreaType'] = 0;//添加地址选择方式   默认0地图选择  1手动输入
		$siteinfo['wxLoginType'] = intval(IReq::get('wxLoginType'));//微信端登录方式  默认0自动登录 1账号登录
       // $siteinfo['open_wxcx'] = intval(IReq::get('open_wxcx'));//微信端首页商家列表显示促销信息
		#print_r($siteinfo['open_wxcx']);exit; 
		$siteinfo['auto_send'] = 0;
		$siteinfo['regestercode'] = intval(IReq::get('regestercode'));
		$siteinfo['allowreback'] = intval(IReq::get('allowreback'));
		$siteinfo['allowedallowed_is_makeguestbuy'] = 0;
		$siteinfo['shenhedrawback'] = intval(IReq::get('shenhedrawback'));
		#print_r($siteinfo);exit;
		 $siteinfo['plateshopid'] = 0;
		$siteinfo['datacache'] =  intval(IReq::get('datacache'));
		 
		if($siteinfo['datacache']  == 1){
			if(!class_exists('Memcached')){
				$this->message('不支持Memcached');
			}
			$siteinfo['datacachetime'] =  intval(IReq::get('datacachetime'));
			$siteinfo['datacachelongtime'] =  intval(IReq::get('datacachelongtime'));
			if($siteinfo['datacachetime'] < 300){
				$this->message('开启缓存后，最小缓存时间不能少于300秒');
			}
			if($siteinfo['datacachelongtime'] < 1800){
				$this->message('开启缓存后，不常用缓存时间不能少于1800秒'); 
			}
		}
		/* $checkshopinfo = $this->mysql->select_one("select   * from ".Mysite::$app->config['tablepre']."shop where id='".$siteinfo['plateshopid']."' ");
		if(empty($checkshopinfo)){
			$this->message('采购店铺不存在');
		}elseif($checkshopinfo['shoptype'] == 0){
			$this->message('采购店铺必须为超市');
		}   */ 
		
		
	  $siteinfo['weixinpay'] = intval(IReq::get('weixinpay'));
	  $checkios = intval(IReq::get('ios_waiting'));
	  if($checkios == 1){
		  $siteinfo['ios_waiting'] = true;
	  }else{
		   $siteinfo['ios_waiting'] = false;
	  }
	  
	  
    //自动完成时间auto_overtime
	  $config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo);
		 
		$this->success('success');
	 }
	  function  savecolorset(){
		 $color = IReq::get('color');
	
		 if($color == 1){
			  $siteinfo['color'] = "red";
		 }
		 if($color == 2){
			  $siteinfo['color'] = "yellow";
		 }
		 if($color == 3){
			  $siteinfo['color'] = "green";
		 }
		
		$config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
		$this->success('success');
	 
	 }	 
	  function saveredicect(){
		$redicectlimit = IReq::get('typename');
	    $siteinfo['redicectlimit'] = serialize($redicectlimit);
	    $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);   
	    $tests = $config->getInfo();
	    $this->success('success');
	 }
	 function redicectset(){
		 $data['redicectlimit'] = unserialize(Mysite::$app->config['redicectlimit']); 
		 Mysite::$app->setdata($data); 
	 }
	 
	  /*8.6  将logo设置摘出来  组成新的模块*/
	 function savelogoset(){
        
		$shoplogo = trim(IReq::get('shoplogo'));     //logo设置
	    $userlogo = trim(IReq::get('userlogo'));     //logo设置
	    $sitelogo = trim(IReq::get('sitelogo'));     //logo设置
		$webcaption = trim(IReq::get('webcaption')); //logo设置
		$wxewm = trim(IReq::get('wxewm'));           //logo设置
		$appewm = trim(IReq::get('appewm'));         //logo设置		
        $adminlogo = trim(IReq::get('adminlogo'));   //logo设置
        $loginlogo = trim(IReq::get('loginlogo'));   //logo设置
        $psimg = trim(IReq::get('psimg'));
		$shoppsimg = trim(IReq::get('shoppsimg')); 
		 
		$wxbglogo = trim(IReq::get('wxbglogo'));
	  	$siteinfo['shoplogo'] = $shoplogo;
		$siteinfo['userlogo'] = $userlogo;	
	  	$siteinfo['sitelogo'] = $sitelogo;
        $siteinfo['adminlogo'] = $adminlogo;  	
	  	$siteinfo['webcaption'] = $webcaption;
	  	$siteinfo['appewm'] = $appewm;
	  	$siteinfo['wxewm'] = $wxewm;
	    $siteinfo['psimg'] = $psimg; 
	  	$siteinfo['shoppsimg'] = $shoppsimg;
		$siteinfo['share_img'] = trim(IReq::get('wxshare'));//微信分享消息图标
		$siteinfo['regimg'] = trim(IReq::get('regimg'));//未登录状态下弹出注册图标领取优惠劵
	  	 
	  	$siteinfo['loginlogo'] = $loginlogo;
	  	$siteinfo['wxbglogo'] = $wxbglogo;
		$siteinfo['zcimg'] = trim(IReq::get('zcimg'));//专场
		$siteinfo['zkimg'] = trim(IReq::get('zkimg'));//折扣
		$siteinfo['goodlogo'] = trim(IReq::get('goodlogo'));
		$siteinfo['ztimg'] = trim(IReq::get('ztimg'));
		$siteinfo['appshare_img'] = trim(IReq::get('appshare'));
	    $config = new config('hopeconfig.php',hopedir);
	    $config->write($siteinfo);
	    $configs = new config('hopeconfig.php',hopedir);   
	    $tests = $config->getInfo();
	    $this->success('success');
	 } 
	 
	 /*8.6  将地图设置摘出来  组成新的模块*/
	 	function savemapset(){
limitalert();
	    $siteinfo['map_javascript_key'] =  trim(IReq::get('map_javascript_key'));//地图设置
	    $siteinfo['map_webservice_key'] =  trim(IReq::get('map_webservice_key'));//地图设置
	    $siteinfo['maplng'] =  trim(IReq::get('maplng'));                        //地图设置
		if(strpos($siteinfo['maplng'], ',')==true){
			$this->message('lng坐标错误');
		}
	    $siteinfo['maplat'] =  trim(IReq::get('maplat'));                        //地图设置
		if(strpos($siteinfo['maplat'], ',')==true){
			$this->message('lat坐标错误');
		}
	    $siteinfo['map_comment_link'] =  trim(IReq::get('map_comment_link'));    //地图设置
		$siteinfo['https'] =  trim(IReq::get('https'));                          //地图设置

	    $config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
	    $configs = new config('hopeconfig.php',hopedir);   
	    $tests = $config->getInfo();	 
		$this->success('success');
	 } 
	 
	 function saveset(){
		//limitalert();
 	 	$sitename = IReq::get('sitename');
	    $description = IReq::get('description');
	    $keywords = IReq::get('keywords');
	    $beian = IReq::get('beian');
	    $yjin = IReq::get('yjin');
	    $yjin = round($yjin, 2);
	    $leasttx = IReq::get('leasttx');
	    $notice = trim(IReq::get('notice')); 
		$shopnotice = trim(IReq::get('shopnotice')); 
	    $area_grade = intval(IReq::get('area_grade'));
	     
	    $footerdata = trim(IReq::get('footerdata'));
		$companyname = IReq::get('companyname');
        $website = IReq::get('website');
	            
	    $siteinfo['default_cityid'] =  intval(IReq::get('default_cityid'));
	    
		
	    //提成设置
	    if(empty($sitename)) $this->message('system_emptysitename'); 
	    if(empty($description)) $this->message('system_emptydes'); 
	    if(empty($keywords)) $this->message('system_emptyseo'); 
	     
	    $siteinfo['litel'] = IReq::get('litel'); 
	    $siteinfo['sitename'] = $sitename;       
 	  	$siteinfo['description'] = $description; 
	  	$siteinfo['keywords'] = $keywords;     
		$siteinfo['beian'] = $beian;     
		$siteinfo['yjin'] = $yjin;   
		 $siteinfo['leasttx'] = empty($leasttx)?100:$leasttx;   
	   	$siteinfo['notice'] = $notice;	  
        $siteinfo['shopnotice'] = $shopnotice;	  	
	  	 
	  	$siteinfo['footerdata'] = $footerdata;   
	  	
        $siteinfo['companyname'] = $companyname; 
		$siteinfo['website'] = $website;	 
		
	
	    $config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
	    $configs = new config('hopeconfig.php',hopedir);   
	    $tests = $config->getInfo();
	
		$this->success('success');
	 } 
	 function savesitebk(){
	 	 
	 	 $siteinfo['sitebk'] = IReq::get('userlogo'); 
		 $siteinfo['is_water'] = IReq::get('is_water'); 
		 $siteinfo['water_pos'] = IReq::get('water_pos');
	  	$siteinfo['logo_water'] = IReq::get('logo_water');
	  	$siteinfo['text_water'] = IReq::get('text_water');
	  	$siteinfo['size_water'] = IReq::get('size_water');
	  	$siteinfo['color_water'] = IReq::get('color_water');   
	  $config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo); 
	 	 $this->success('success'); 
   }
   function savetoplink(){
   #	$this->message("无操作权限");
   limitalert();
   	$arrtypename = IReq::get('typename');
			$arrtypeurl = IReq::get('typeurl'); 
			$arrtypeorder = IReq::get('typeorder'); 
		  if(empty($arrtypename)) $this->message('empty_link'); 
		  if(is_array($arrtypename))
		  {
		  	$orderinfo = array(); 
		  	foreach($arrtypename as $key=>$value)
		  	{
		  		if(isset($arrtypeorder[$key]))
		  		{
		  		  $dokey = !empty($arrtypeorder[$key])? $arrtypeorder[$key]:0; 
		  		  array_push($orderinfo,$dokey);
		  	  }else{
		  	  	 array_push($orderinfo,0);
		  	  }
		  	} 
		  	$orderinfo = array_unique($orderinfo); 
		  	sort($orderinfo); 
		  	$newinfo =  array();
		  	foreach($orderinfo as $key=>$value)
		  	{
		  		foreach($arrtypename as $k=>$v)
		  		{
		  	    if(isset($arrtypeorder[$k]))
		  	   	{
		  	   	  $checkcode = !empty($arrtypeorder[$k])? $arrtypeorder[$k]:0; 
		  	   	 
		  	     }else{
		  	     	$checkcode = 0;
		  	     }
		  		 
		  			if($checkcode == $value)
		  			{
		  				$data['typename'] = $v;
		  				$data['typeurl'] = isset($arrtypeurl[$k])? $arrtypeurl[$k]:'';
		  				$data['typeorder'] = $checkcode;
		  				$newinfo[] = $data;
		  			}
		  		}
		  	}
		  	 
		  }else{
		  	$newinfo['typename'] = $arrtypename;
		  	$newinfo['typeurl'] = $arrtypeurl;
		  	$newinfo['typeorder'] = $arrtypeorder;
		  }
		$siteinfo['footlink'] =   serialize($newinfo);
		$config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo);
	   $this->success('success'); 
   }
   function savefootinfo(){
   	// limitalert();
    $arrtypename = IReq::get('typename');
			$arrtypeurl = IReq::get('typeurl'); 
			$arrtypeorder = IReq::get('typeorder'); 
		  if(empty($arrtypename)) $this->message('empty_link'); 
		  if(is_array($arrtypename))
		  {
		  	$orderinfo = array(); 
		  	foreach($arrtypename as $key=>$value)
		  	{
		  		if(isset($arrtypeorder[$key]))
		  		{
		  		  $dokey = !empty($arrtypeorder[$key])? $arrtypeorder[$key]:0; 
		  		  array_push($orderinfo,$dokey);
		  	  }else{
		  	  	 array_push($orderinfo,0);
		  	  }
		  	} 
		  	$orderinfo = array_unique($orderinfo); 
		  	sort($orderinfo); 
		  	$newinfo =  array();
		  	foreach($orderinfo as $key=>$value)
		  	{
		  		foreach($arrtypename as $k=>$v)
		  		{
		  	    if(isset($arrtypeorder[$k]))
		  	   	{
		  	   	  $checkcode = !empty($arrtypeorder[$k])? $arrtypeorder[$k]:0; 
		  	   	 
		  	     }else{
		  	     	$checkcode = 0;
		  	     }
		  		 
		  			if($checkcode == $value)
		  			{
		  				$data['typename'] = $v;
		  				$data['typeurl'] = isset($arrtypeurl[$k])? $arrtypeurl[$k]:'';
		  				$data['typeorder'] = $checkcode;
		  				$newinfo[] = $data;
		  			}
		  		}
		  	}
		  	 
		  }else{
		  	$newinfo['typename'] = $arrtypename;
		  	$newinfo['typeurl'] = $arrtypeurl;
		  	$newinfo['typeorder'] = $arrtypeorder;
		  }
		$siteinfo['toplink'] =   serialize($newinfo);
		$config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo);
	    $this->success('success'); 
   }
   
	 function savemodule(){
	 	limitalert(); 
	 	   //module   id	name	cnname	install
	 	   $arr['name'] = IFilter::act(IReq::get('name')); 
	 	   $arr['cnname'] = IFilter::act(IReq::get('cnname')); 
	 	   $arr['install'] = 1;
	 	   $is_main = intval(IFilter::act(IReq::get('is_main'))); 
	 	  if(empty($arr['name'])) $this->message('empty_filename');
	 	  if(empty($arr['cnname'])) $this->message('empty_CNname');
	 	  if(empty($is_main)) $this->message('module_nochoice');
	 	  $parent_id = intval(IFilter::act(IReq::get('parent_id'))); 
	 	  if($is_main == 1){
	 	    $arr['parent_id'] = 0;
	 	  }else{
	 	    $arr['parent_id'] = $parent_id;
	 	    if(empty($parent_id)) $this->message('module_noparent');
	 	  }
	 	  
	 	  $this->mysql->insert(Mysite::$app->config['tablepre'].'module',$arr);  
	 	  $moduleid = $this->mysql->insertid();
	 	  //写入菜单 
	 	  $menudata['name'] = 'limitset';
	 	  $menudata['cnname'] = '权限设置';
	 	  $menudata['moduleid'] = $moduleid;
	 	  $menudata['group'] = 1;
	 	  $menudata['id'] = 0;
	 	  $this->mysql->insert(Mysite::$app->config['tablepre'].'menu',$menudata);  
	 	  //写入权限
	 	  $limitdata['name'] =  'limitset';
	 	  $limitdata['cnname'] = '权限列表';
	 	  $limitdata['moduleid'] = $moduleid;
	 	  $limitdata['group'] = 1;
	 	  $this->mysql->insert(Mysite::$app->config['tablepre'].'usrlimit',$limitdata);  
	 	  $limitdata['name'] = 'savelimit';
	 	  $limitdata['cnname'] = '保存权限设置'; 
	 	  $this->mysql->insert(Mysite::$app->config['tablepre'].'usrlimit',$limitdata);  
	 	  $this->success('success');
	 }
	 function tempset(){
	 	limitalert();
	 	   $logindir = hopedir.'/templates'; 
       $dirArray[]=NULL;   
       if (false != ($handle = opendir ( $logindir ))) {   
         $i=0;   
         while ( false !== ($file = readdir ( $handle )) ) {   
             //去掉"“.”、“..”以及带“.xxx”后缀的文件   
             if ($file != "." && $file != ".."&&!strpos($file,".")) { 
             	  if(file_exists($logindir.'/'.$file.'/stro.txt'))
                 { 
                 		$license = file_get_contents($logindir.'/'.$file.'/stro.txt');   
	                  $dirArray[$i]['instro'] =  nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($license,ENT_COMPAT,'UTF-8')));  
                 	  $dirArray[$i]['img'] = file_exists($logindir.'/'.$file.'/instro.jpg')? Mysite::$app->config['siteurl'].'/templates/'.$file.'/instro.jpg':'';
                 	  $dirArray[$i]['filename'] =$file;  
                 	  
                    $i++;   
                 }
             }  

         }   
         //关闭句柄  
         //if(!file_exists(hopedir.'/templates/'.$templtepach))//判断文件是否存在  判断配置文件是否存在
         closedir ( $handle );  

       } 
       $data['apilist'] = $dirArray; 
        Mysite::$app->setdata($data);
	 	
   }
   function savetempset(){
   	limitalert();
   	$tempname = IFilter::act(IReq::get('modulename')); 
   	if(empty($tempname)){
   		 $this->message('module_emptyname');
   	}
    $logindir = hopedir.'/templates'; 
    if(!file_exists($logindir.'/'.$tempname.'/stro.txt')) $this->message('template_noexit');
    $siteinfo['sitetemp'] =  $tempname;
		$config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo);
    IFile::clearDir('templates_c'); 
	  $this->success('success'); 
   }
function savemobiletempset(){
   	limitalert();
   	$tempname = IFilter::act(IReq::get('mobilemodule')); 
   	if(empty($tempname)){
   		 $this->message('module_emptyname');
   	}
      $siteinfo['mobilemodule'] =  $tempname;
		$config = new config('hopeconfig.php',hopedir);  
	  $config->write($siteinfo);
    IFile::clearDir('templates_c'); 
	  $this->success('success'); 
   }

   function delmodule(){
	   limitalert();
   	 $tmsg = limitalert();
		if(!empty($tmsg)) $this->message($tmsg);
      	$id = intval(IFilter::act(IReq::get('id'))); 
   	     $checinfo = $this->mysql->select_one("select *  from ".Mysite::$app->config['tablepre']."module  where id= '".$id."'  ");
   	     if(empty($checinfo)) $this->message('module_noexit');
   	     if($checinfo['parent_id'] == 0){
   	       	 $sublist = $this->mysql->getarr("select *  from ".Mysite::$app->config['tablepre']."module  where parent_id= '".$id."'  ");
   	       	 foreach($sublist as $key=>$value){
   	       	 	  $this->mysql->delete(Mysite::$app->config['tablepre']."module"," id='".$value['id']."'  "); 
   	       	 	   $this->mysql->delete(Mysite::$app->config['tablepre']."menu"," moduleid='".$value['id']."'  "); 
   	       	 	   $this->mysql->delete(Mysite::$app->config['tablepre']."usrlimit"," moduleid='".$value['id']."'  "); 
   	       	 }
   	       	 
   	     }
   	      $this->mysql->delete(Mysite::$app->config['tablepre']."module"," id='".$id."'  "); 
   	      $this->mysql->delete(Mysite::$app->config['tablepre']."menu"," moduleid='".$id."'  "); 
   	      $this->mysql->delete(Mysite::$app->config['tablepre']."usrlimit"," moduleid='".$id."'  "); 
   	     $this->success('success');
    
   }
   function limitset(){
   	
   	 $id = intval(IReq::get('id'));	  
	 	   $data['groupid'] = intval(IReq::get('usergrade'));

	 	     $data['groupinfo'] = array();
	 	     if($data['groupid'] > 0){
	 	     	
	 	     	 $data['groupinfo'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."group where id = ".$data['groupid']." "); 
	 	    }
   	 
	 	   $modelist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."module  where install='1' limit 0,30"); 
	 	   //name 操作名称	cnname	moduleid	group	id
	 	   $templist = array();
	 	   foreach($modelist as $key=>$value){
	 	        $menufile = hopedir."/module/".$value['name']."/menudata.php";  
	 	        if(file_exists($menufile))//判断文件是否存在
	 	        {
	 	        	    $value['det'] = include($menufile);    
	 	        	    $temp_c = $this->mysql->getarr("select name from ".Mysite::$app->config['tablepre']."menu where moduleid='".$value['id']."'  and `group`=".$data['groupid']." ");

	 	        	       $value['menuarray'] = array();
	 	        	    if(is_array($temp_c)){
	 	        	      foreach($temp_c as $k=>$val){
	 	        	      	$value['menuarray'][] = $val['name'];
	 	        	      }
	 	        	    }
	 	        }else{
	 	            $value['det'] = array();
	 	        }
	 	        $templist[] = $value;
	 	   }  
	 	  # print_r($templist);
	 	   $data['modelist'] = $templist;
	 	  
	 	   Mysite::$app->setdata($data); 
	 }
	 //保存权限
	 function savelimit(){
	 	 // limitalert();
	 	  $groupid = intval(IReq::get('groupid'));	
	 	  if(empty($groupid)) $this->message('member_group_noexit'); 
	 	  $groupinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."group where id = ".$groupid." "); 
	 	  if(empty($groupinfo)) $this->message('member_group_noexit');  
	 	  if($groupinfo['type'] != 'admin') $this->message('不是管理员不需要设置导航条');
	 	  $this->mysql->delete(Mysite::$app->config['tablepre'].'menu',"`group`=".$groupid."");   
	 	  $modelist =  $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."module  where install='1' limit 0,30");
	 	  //获取所有的模块
	 	  foreach($modelist as $key=>$value){
	 	  	    $menufile = hopedir."/module/".$value['name']."/menudata.php";  
	 	        if(file_exists($menufile))//判断文件是否存在
	 	        {
	 	        	  $getinfo = IFilter::act(IReq::get($value['name']));
	 	        	  if(is_array($getinfo)){
	 	        	     $munulist =  include($menufile); 
	 	        	     foreach($getinfo as $k=>$val){
	 	        	     	   if(isset($munulist[$val])){
	 	        	     	   	$xieru['name'] = $val;
	 	        	     	   	$xieru['cnname'] = $munulist[$val];
	 	        	          $xieru['moduleid'] = $value['id'];
	 	        	           $xieru['group'] = $groupid;
	 	        	           $xieru['id'] = $k;
	 	        	          $this->mysql->insert(Mysite::$app->config['tablepre'].'menu',$xieru);  
	 	        	         }
	 	        	         
	 	        	     }
	 	        	 }
	 	        	
	 	        }  
	 	  } 
	 	  $this->success('success');  
	 	
	 }
   
	 
}



?>