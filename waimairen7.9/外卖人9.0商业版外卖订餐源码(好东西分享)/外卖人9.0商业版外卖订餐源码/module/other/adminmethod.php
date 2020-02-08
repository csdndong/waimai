<?php
class method   extends adminbaseclass
{
	function savecallphoneset(){
		limitalert();
		$is_auto_callphone =  intval(IReq::get('is_auto_callphone'));
		$is_make_auto_callphone =  intval(IReq::get('is_make_auto_callphone'));
		$autoCallPhone_accessKeyId =  trim(IReq::get('autoCallPhone_accessKeyId'));
		$autoCallPhone_accessKeySecret =  trim(IReq::get('autoCallPhone_accessKeySecret'));
		$autoCallPhone_tel =  trim(IReq::get('autoCallPhone_tel'));
		$autoCallPhone_TelCode =  trim(IReq::get('autoCallPhone_TelCode'));
		$autoCallPhone_Minute =  intval(IReq::get('autoCallPhone_Minute'));
		 
		
		if( $is_auto_callphone == 1 ){
			if( empty($autoCallPhone_accessKeyId) ){
				$this->message('请填写Access Key Id');
			}
			if( empty($autoCallPhone_accessKeySecret) ){
				$this->message('请填写Access Key Secret');
			}
			if( empty($autoCallPhone_tel) ){
				$this->message('请填写被叫显号');
			}
			if( empty($autoCallPhone_TelCode) ){
				$this->message('请填写模板Code');
			}
			if( empty($autoCallPhone_Minute) ){
				$this->message('请填写间隔分钟数');
			}
		} 
		
				
		 
		$siteinfo['is_auto_callphone'] = $is_auto_callphone;
		$siteinfo['is_make_auto_callphone'] = $is_make_auto_callphone;
		$siteinfo['autoCallPhone_accessKeyId'] = $autoCallPhone_accessKeyId;
		$siteinfo['autoCallPhone_accessKeySecret'] = $autoCallPhone_accessKeySecret;
		$siteinfo['autoCallPhone_tel'] = $autoCallPhone_tel;
		$siteinfo['autoCallPhone_TelCode'] = $autoCallPhone_TelCode;
		$siteinfo['autoCallPhone_Minute'] = $autoCallPhone_Minute;
 		
		 $config = new config('hopeconfig.php',hopedir);  
		$config->write($siteinfo);
		 
		$this->success('success');
	}
    function saveimgset(){
		limitalert();
		$is_img_service =  intval(IReq::get('is_img_service'));
		$imgserver =  trim(IReq::get('imgserver'));
		 
		
		if( $is_img_service == 0 ){
			$imgserver = Mysite::$app->config['siteurl'];
		} 
		if( empty($imgserver) ){
			$this->message('请填写图片服务器域名');
		}
		 
		$siteinfo['is_img_service'] = $is_img_service;
		$siteinfo['imgserver'] = $imgserver;
		
		 $config = new config('hopeconfig.php',hopedir);  
		$config->write($siteinfo);
		 
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
        }else{
			$data['ztyinfo'] = array('name'=>'','zttype'=>'','id'=>'','listids'=>'','showtype'=>'','cx_type'=>'','zdylink'=>'');
		}
        #print_r($data['ztyinfo']);
        $data['catarr'] = array('0'=>'外卖','1'=>'超市');
        $data['catlist'] = $catlist;
        Mysite::$app->setdata($data);
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
        }else{
			$data['ztyinfo'] = array('name'=>'','ruleintro'=>'','zttype'=>'','id'=>'','listids'=>'','showtype'=>'','cx_type'=>'','zdylink'=>'');	
		}
        Mysite::$app->setdata($data);
    }


    function savezty(){  // 保存或者更新 专题页
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




	public function uploadapp(){
		$func = IFilter::act(IReq::get('func'));
		$obj = IReq::get('obj');
		$uploaddir =IFilter::act(IReq::get('uploaddir'));
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
			$uploaddir = empty($uploaddir)?'system':$uploaddir;
			$json = new Services_JSON();
			$default_cityid = Mysite::$app->config['default_cityid'];
			if( !empty($default_cityid) ){
				$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
			
 			$upload = new upload($uploadpath);//upload 自动生成压缩图片 
			$filedir = $upload->getSigImgDir(); 
			$filedir = Mysite::$app->config['imgserver'].$filedir;
 			if($upload->errno!=15&&$upload->errno!=0){
				echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
			}else{
				echo "<script>parent.".$func."(false,'".$obj."','".$filedir."');</script>";

			}
			exit;
		}
		$data['obj'] = $obj;
		$data['uploaddir'] = $uploaddir;
		$data['func'] = $func;
		Mysite::$app->setdata($data); 
	}
	
	 
	//批量上传图片
	public function piliang(){
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
		 $uploaddir = "goodspub";
		$json = new Services_JSON();
		$uploadpath = 'images/'.$uploaddir.'/';
         $upload = new upload($uploadpath);
		 $file = $upload->getfile();
			$filedir = $upload->getSigImgDir(); 
			
         if($upload->errno!=15&&$upload->errno!=0){
			 
			  $this->message($upload->errmsg());   
		   }else{
			   //写到商品库表中   
              $data['imagename']= $file[0]['saveName'];
			   $data['imageurl']= $filedir;
			   $data['addtime'] = time();
			
			   $this->mysql->insert(Mysite::$app->config['tablepre'].'imglist',$data); 
			   
			   $filedir = Mysite::$app->config['imgserver'].$filedir;
				$this->success($filedir); 
		   } 
		}else{
		 
			 $this->message('未定义的上传类型'); 
		}
	}
     function apiset(){
		 
		$cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		if( empty($cityid) ){
			$this->message("请先设置网站自营默认城市",'/index.php?ctrl=adminpage&action=system&module=siteset');
		}
		$platpssetinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		$platpssetinfo['radiusvalue'] = unserialize( $platpssetinfo['radiusvalue'] );
		$data['psinfo'] = $platpssetinfo; 
		Mysite::$app->setdata($data);
		 
	 }
	function saveapiset(){
		limitalert();
		$siteinfo['psbopen'] =  intval(IReq::get('psbopen')); 
	  	$siteinfo['autopsblink'] = trim(IReq::get('autopsblink'));  
	  	$siteinfo['autopsbkey'] =trim(IReq::get('autopsbkey'));  
	    $siteinfo['managephone'] =trim(IReq::get('managephone')); 
		$config = new config('hopeconfig.php',hopedir);  
	    $config->write($siteinfo);
	    $tests = $config->getInfo();
	
		$cityid = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		$savearray['pttopsb'] = intval(IFilter::act(IReq::get('pttopsb')));
		$savearray['ptpsblink']  = trim(IFilter::act(IReq::get('ptpsblink')));
		$savearray['ptpsbaccid']  = intval(IFilter::act(IReq::get('ptpsbaccid')));
		$savearray['ptpsbkey']  = trim(IFilter::act(IReq::get('ptpsbkey')));
		$savearray['ptpsbcode']  = trim(IFilter::act(IReq::get('ptpsbcode')));
			
		$platpssetinfo = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."platpsset  where cityid = '".$cityid."'  ");
		 
		if( !empty($platpssetinfo) ){
			 $this->mysql->update(Mysite::$app->config['tablepre'].'platpsset',$savearray,"cityid='".$cityid."'");	 
		}else{
			 $savearray['cityid'] = $cityid;
			  $this->mysql->insert(Mysite::$app->config['tablepre'].'platpsset',$savearray);  
		} 
		$this->success('success');
		
	}
	//批量上传图片
	public function glyuploadmoreimg(){
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
		 $uploaddir = "wximages";
		$json = new Services_JSON();
		$uploadpath = 'upload/'.$uploaddir.'/';
		$filepath = '/upload/'.$uploaddir.'/';
         $upload = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));//upload 自动生成压缩图片
         $file = $upload->getfile();
		 $uploadimages = array();
         if($upload->errno!=15&&$upload->errno!=0){
			 
			  $this->message($upload->errmsg());   
		   }else{
			   //写到商品库表中   
               $data['imagename']= $file[0]['saveName'];
			   $data['imageurl']= $filepath.$file[0]['saveName'];
			   $data['addtime'] = time();
			
				$uploadimages[] = $file[0]['saveName'];
			#   $this->mysql->insert(Mysite::$app->config['tablepre'].'imglist',$data); 
			#	$this->success($file[0]['saveName']); 
		   }
		}else{
		 
			 $this->message('未定义的上传类型'); 
		}
	}

	//整站店铺一键开关
	function saveopenset(){
		$start_open = intval(IReq::get('start_open'));
		$cityid = intval(IReq::get('cityid'));
		$where = '';
		if($cityid > 0){
			$where = ' and admin_id = '.$cityid.' ';
		}
		if($start_open == 1){
			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',array('is_open'=>1)," id > 0 ".$where." ");
		}elseif($start_open == 2){

			$this->mysql->update(Mysite::$app->config['tablepre'].'shop',array('is_open'=>0)," id > 0 ".$where." ");
		}

		$this->success('success');
	}

	 public function adminupload()
	 {
	 	 $func = IFilter::act(IReq::get('func'));
		 $obj = IReq::get('obj');
		 $uploaddir =IFilter::act(IReq::get('uploaddir'));
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
		  if($func == 'uploadappshare' && $_FILES['imgFile']['size'] > 102400 ){
			  echo'图片大小不能大于100kb';exit;
		  }
	   	  $uploaddir = empty($uploaddir)?'other':$uploaddir;
	  	  $json = new Services_JSON();
		  $default_cityid = Mysite::$app->config['default_cityid'];
			if( !empty($default_cityid) ){
				$uploadpath = 'images/'.$default_cityid.'/'.$uploaddir.'/'; 
			}else{
				$uploadpath = 'images/'.$uploaddir.'/'; 
			}
			
           $upload = new upload($uploadpath); 
         $filedir = $upload->getSigImgDir();
		 $filedir = Mysite::$app->config['imgserver'].$filedir;
         if($upload->errno!=15&&$upload->errno!=0){
		     echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
		   }else{
		      echo "<script>parent.".$func."(false,'".$obj."','".$filedir."');</script>";

		   }
		   exit;
	   }
	   $data['obj'] = $obj;
	   $data['uploaddir'] = $uploaddir;
	   $data['func'] = $func;
	   Mysite::$app->setdata($data);
	 }
	 	
	 public function adminsayupload()			// 一起说管理员发表主题图片
	 {
	 	 $func = IFilter::act(IReq::get('func'));
		 $obj = IReq::get('obj');
		 $uploaddir =IFilter::act(IReq::get('dir'));
		if(is_array($_FILES)&& isset($_FILES['imgFile']))
		{
	   	  $uploaddir = empty($uploaddir)?'wximages':$uploaddir;
	  	  $json = new Services_JSON();
          $uploadpath = 'upload/'.$uploaddir.'/';
		   $filepath = '/upload/'.$uploaddir.'/';
         $upload = new upload($uploadpath,array('gif','jpg','jpge','doc','png'));//upload 自动生成压缩图片
         $file = $upload->getfile();
         if($upload->errno!=15&&$upload->errno!=0){
		     echo "<script>parent.".$func."(true,'".$obj."','".json_encode($upload->errmsg())."');</script>";
		   }else{
		      echo "<script>parent.".$func."(false,'".$obj."','".$filepath.$file[0]['saveName']."');</script>";

		   }
		   exit;
	   }
	   $data['obj'] = $obj;
	   $data['uploaddir'] = $uploaddir;
	   $data['func'] = $func;
	   Mysite::$app->setdata($data);
	 }
	 public function saveupload()
	 {
	 	// $siteurl = Mysite::$app->config['siteurl'];
	 	
		  $json = new Services_JSON();
      $uploadpath = 'upload/goods/';
		  $filepath = '/upload/goods/';
		  // print_r($filepath);
      $upload = new upload($uploadpath,array('gif','jpg','jpge','png'));//upload
      $file = $upload->getfile();
     if($upload->errno!=15&&$upload->errno!=0) {
			$msg = $json->encode(array('error' => 1, 'message' => $upload->errmsg()));

		  }else{
			$msg = $json->encode(array('error' => 0, 'url' => $filepath.$file[0][saveName], 'trueurl' => $upload->returninfo['name']));
		 }
		 echo $msg;
		 exit;
	 }

	 function paylist(){
		//获取所有已安装接口
		/**获取登录接口文件夹下的所有接口说明文件**/
		$type = intval(IReq::get('type'));//$_GET['idtype'];
	     $logindir = plugdir.'/pay';
       $dirArray[]=NULL;
       if (false != ($handle = opendir ( $logindir ))) {
         $i=0;
         while ( false !== ($file = readdir ( $handle )) ) {
             //去掉"“.”、“..”以及带“.xxx”后缀的文件

             if ($file != "." && $file != ".."&&!strpos($file,".")) {

                 if(file_exists($logindir.'/'.$file.'/set.php'))
                 {

                 	  require_once($logindir.'/'.$file.'/set.php');

                 	  $dirArray[$i]['data'] = $setinfo;
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
    //paylist 支付接口表 id,name
    $exlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."paylist    order by id desc  limit 0, 50 ");
    $mlist = array();
    if(is_array($exlist))
    {
      foreach($exlist as $key=>$value)
      {
    	  if(!empty($value['logindesc']))
    	  {
    		  $mlist[] = $value['logindesc'];
    	  }
      }
    }
	$typearr =array(0=>array('alipay','alipayapp','alimobile'),1=>array('weixin','weixinapp','weixinshopapp','weixinapplet'),2=>array('open_acout'),3=>array('paypal'));
    $data['installlist'] = $mlist;
	$data['type'] = $type;
	$data['typearr'] = $typearr[$type];
    Mysite::$app->setdata($data);
	 }


	 function installpay(){
	 	  $idtype = IReq::get('idtype');//$_GET['idtype'];
		  $logindir = plugdir.'/pay';

		  if(!file_exists($logindir.'/'.$idtype.'/set.php'))
      {
      	 //不存在配置文件
      	 $data['info'] = '安装文件不存在';
      }else{
      	//不存在配置文件
      	include_once($logindir.'/'.$idtype.'/set.php');
      	 
      		$data['info'] = $plugsdata;
       
		$data['apiinfo'] = $setinfo;
        //$data['setinfo'] = plugsget($logindir,$idtype);
      }
	  $typearr =array(0=>array('alipay','alipayapp','alimobile'),1=>array('weixin','weixinapp','weixinshopapp','weixinapplet'),2=>array('open_acout'),3=>array('paypal'));
	  $checktype = 0;
	  foreach($typearr as $key=>$value){
		  if(in_array($idtype,$value)){
			  $checktype = $key;
		  }
	  }
      $getinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."paylist where loginname='".$idtype."'  ");
      $data['setinfo'] = json_decode($getinfo['temp'],true);
	  
	  $data['payinfo']=$getinfo;
      $data['idtype']=$idtype;
	  $data['checktype']=$checktype;
      Mysite::$app->setdata($data);
	}
	function savepay()
	{
        #limitalert();
		  $idtype = IReq::get('idtype');
		  $logindir = plugdir.'/pay';

		  if(!file_exists($logindir.'/'.$idtype.'/save.php'))
      {
      	 //不存在配置文件
      	 $data['info'] = '设置文件不存在';
      }else{
      	//不存在配置文件

      	 $appid = IReq::get('appid');
      // echo $appid;
      	include_once($logindir.'/'.$idtype.'/save.php');
      }
      exit;
		// include_once($logindir.'/'.$file.'/save.php');
	}
	function delpay()
	{limitalert();
		 $idtype = IReq::get('idtype');
		 if(empty($idtype))  $this->message('other_emptyapi');
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'paylist',"loginname = '$idtype'");
	   $this->success('success');
	}
	function loginlist(){
		 $logindir = plugdir.'/login';
       $dirArray[]=NULL;
       if (false != ($handle = opendir ( $logindir ))) {
         $i=0;
         while ( false !== ($file = readdir ( $handle )) ) {
             //去掉"“.”、“..”以及带“.xxx”后缀的文件
             if ($file != "." && $file != ".."&&!strpos($file,".")) {
                 if(file_exists($logindir.'/'.$file.'/set.php'))
                 {
                 	  require_once($logindir.'/'.$file.'/set.php');
                 	  $dirArray[$i]['data'] = $setinfo;
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
        #print_r($dirArray);
    $exlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."otherlogin    order by id desc  limit 0, 50 ");
    $mlist = array();
    if(is_array($exlist))
    {
      foreach($exlist as $key=>$value)
      {
    	  if(!empty($value['logindesc']))
    	  {
    		  $mlist[] = $value['logindesc'];
    	  }
      }
    }
    $data['installlist'] = $mlist;
     Mysite::$app->setdata($data);
	}
	function installlogin(){
		 $idtype = IReq::get('idtype');//$_GET['idtype'];
		  $logindir = plugdir.'/login';
		  if(!file_exists($logindir.'/'.$idtype.'/set.php'))
      {
      	 //不存在配置文件
      	 $data['info'] = '安装文件不存在';
      }else{
      	//不存在配置文件
      	include_once($logindir.'/'.$idtype.'/set.php');
      	$data['info'] = $mkdata;
        //$data['setinfo'] = plugsget($logindir,$idtype);
      }
      $getinfo =  $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."otherlogin where loginname='".$idtype."'  order by id desc ");
      $data['setinfo'] = json_decode($getinfo['temp'],true);

      $data['idtype']=$idtype;
      Mysite::$app->setdata($data);
	}
	function dellogin()
	{limitalert();
		  $idtype = IReq::get('idtype');
		  if(empty($idtype))  $this->message('other_emptyapi');
	    $this->mysql->delete(Mysite::$app->config['tablepre'].'otherlogin',"loginname = '$idtype'");
	    $this->success('success');
	 }
	function savelogin(){
		 $idtype = IReq::get('idtype');
         
		  $logindir = plugdir.'/login';
          
		  if(!file_exists($logindir.'/'.$idtype.'/save.php'))
      {
      	 //不存在配置文件
      	 $data['info'] = '设置文件不存在';
      }else{
      	//不存在配置文件
      	 $appid = IReq::get('appid');
      // echo $appid;
      	include_once($logindir.'/'.$idtype.'/save.php');
      }
      exit;
	}
   function othertpl(){
    $default_tpl = new config('tplset.php',hopedir);
	 	$data['info'] = $default_tpl->getInfo();

	 	$data['allowedsendshop'] = Mysite::$app->config['allowedsendshop'];
	 	$data['allowedsendbuyer'] = Mysite::$app->config['allowedsendbuyer'];
	 	$data['ordercheckphone'] = Mysite::$app->config['ordercheckphone'];
	 	$list = array(
	 	               'emailfindtpl'=>'找回密码邮箱模板', 
	 	                'usercodetpl'=>'用户验证码模版', 
	 	                'phoneunorder'=>'后台关闭订单短信通知模板',
	 	                'noticeunback'=>'退款失败通知',
	 	                'noticeback'=>'退款成功通知',
	 	                'noticemake'=>'商家制作订单通知',
	 	                'noticeunmake'=>'商家不制作订单通知',
	 	                'noticesend'=>'发货通知',
	 	               'shopphonetpl'=>'下单通知商家模版',
	 	                'userbuytpl'=>'下单通知用户模版',
	 	             );
	 	if($data['allowedsendshop'] != 1){
	 		unset($list['shopphonetpl']);
	 	}
	 	if($data['allowedsendbuyer'] != 1){
	 		unset($list['userbuytpl']);
	 	}
	 	if($data['ordercheckphone'] != 1){
	 		unset($list['usercodetpl']);
	 	}
	  $data['list'] = $list;
	   Mysite::$app->setdata($data);
  }
  function edittpl(){
		$tplname = IReq::get('tplname');
		$default_tpl = new config('tplset.php',hopedir);
	 	$tpllist = $default_tpl->getInfo();
	 	$list = array( 'emailfindtpl'=>'找回密码邮箱模板',
	 	                'shopphonetpl'=>'下单通知商家模版',
	 	                'userbuytpl'=>'下单通知用户模版',
	 	                'usercodetpl'=>'用户验证码模版',
	 	                'emailorder'=>'商家邮件模版',
	 	                'phoneunorder'=>'后台关闭订单短信通知模板',
	 	                'usercodetpl'=>'用户验证码模版',
	 	                 'noticeunback'=>'退款失败通知',
	 	                'noticeback'=>'退款成功通知',
	 	                'noticemake'=>'商家制作订单通知',
	 	                'noticeunmake'=>'商家不制作订单通知',
	 	                'noticesend'=>'发货通知',
						
	 	                );
	 	$info = array_keys($list);
	 	if(!in_array($tplname,$info))
	 	{
	 		 header("Content-Type:text/html;charset=utf-8");
	 		echo '编辑模板错误';
	 		exit;
	 	}
	 	$data['tplname'] = $tplname;
	 	if(isset($tpllist[$tplname])){
	 		$data['tplcontent'] = htmlspecialchars_decode($tpllist[$tplname]);
	 	}
	 	 Mysite::$app->setdata($data);
	}
	function savetpl(){
		limitalert();
		$tplname = trim(IReq::get('tplname'));
		$tplcontent = trim(IReq::get('tplcontent'));
		$list = array('emailfindtpl'=>'找回密码邮箱模板',
	 	                'shopphonetpl'=>'下单通知商家模版',
	 	                'userbuytpl'=>'下单通知用户模版',
	 	                'usercodetpl'=>'用户验证码模版',
	 	                'emailorder'=>'商家邮件模版',
	 	                'phoneunorder'=>'后台关闭订单短信通知模板',
	 	               'usercodetpl'=>'用户验证码模版',
	 	                'noticeunback'=>'退款失败通知',
	 	                'noticeback'=>'退款成功通知',
	 	                'noticemake'=>'商家制作订单通知',
	 	                'noticeunmake'=>'商家不制作订单通知',
	 	                'noticesend'=>'发货通知',
	 	                );
	 	$info = array_keys($list);
	 	if(!in_array($tplname,$info)){
	 		echo "不在操作模板内";
	 		exit;
	 	}

	 	$siteinfo[$tplname] = stripslashes($tplcontent);
		 $default_tpl = new config('tplset.php',hopedir);
	   $default_tpl->write($siteinfo);

		 echo "<meta charset='utf-8' />";
     echo "<script>parent.uploadsucess('设置成功');</script>";
     exit;
	}

	function cleartpl(){
		IFile::clearDir('templates_c');
		$link = IUrl::creatUrl('/adminpage/system/module/sindex');
		$this->refunction('清除缓存文件成功',$link);
	}
	function emailsetsave()
	{ #limitalert();
		$start_smtp = IReq::get('start_smtp');
		if($start_smtp ==1)
		{
	    $siteinfo['smpt'] = IReq::get('smpt');
	    $siteinfo['emailname'] = IReq::get('emailname');
	    $siteinfo['emailpwd'] = IReq::get('emailpwd');
	  }else{
	  	 $siteinfo['smpt'] = '';
	    $siteinfo['emailname'] = '';
	    $siteinfo['emailpwd'] = '';
	  }
	  $config = new config('hopeconfig.php',hopedir);
	  $config->write($siteinfo);
	   $this->success('success');
	}
	function smssetsave()
	{  limitalert();
	    $config = new config('hopeconfig.php',hopedir);
		$siteinfo['smstype'] = IReq::get('smstype');
		$siteinfo['msgqianming'] =  trim(IReq::get('msgqianming'));
		$siteinfo['apiuid'] =intval( IReq::get('sms86id') );
		$siteinfo['sms86ac'] =IReq::get('sms86ac');
		$siteinfo['sms86pd'] = IReq::get('sms86pd');
		$siteinfo['smstype'] = IReq::get('smstype');
		if($siteinfo['smstype'] == 2){
			if(empty($siteinfo['sms86ac'])) $this->message('acout_empty');
			if(empty($siteinfo['sms86pd'])) $this->message('emptykey');
		}
	    	/*验证码部分*/
		 $siteinfo['alicode1'] = trim(IReq::get('alicode1'));
		 $siteinfo['alicode2'] = trim(IReq::get('alicode2'));
		 $siteinfo['alicode3'] = trim(IReq::get('alicode3'));
		 $siteinfo['alicode4'] = trim(IReq::get('alicode4'));
		 $siteinfo['alicode5'] = trim(IReq::get('alicode5'));
		 $siteinfo['alicode6'] = trim(IReq::get('alicode6'));
		 $siteinfo['alicode7'] = trim(IReq::get('alicode7'));
		 $siteinfo['alicode8'] = trim(IReq::get('alicode8'));
		 $siteinfo['alicode9'] = trim(IReq::get('alicode9'));
		 $siteinfo['alicode10'] = trim(IReq::get('alicode10'));
		 /*短信通知部分*/
		 $siteinfo['alimsg1'] = trim(IReq::get('alimsg1'));
		 $siteinfo['alimsg2'] = trim(IReq::get('alimsg2'));
		 $siteinfo['alimsg3'] = trim(IReq::get('alimsg3'));
		 $siteinfo['alimsg4'] = trim(IReq::get('alimsg4'));
		 $siteinfo['alimsg5'] = trim(IReq::get('alimsg5'));
		 $siteinfo['alimsg6'] = trim(IReq::get('alimsg6'));
		 $siteinfo['alimsg7'] = trim(IReq::get('alimsg7'));
		 /*Access Key ID   Access Key Secret  及短信签名部分*/
		 $siteinfo['aliid'] = trim(IReq::get('aliid'));
		 $siteinfo['alisecret'] = trim(IReq::get('alisecret'));
		 $siteinfo['aliqm'] = trim(IReq::get('aliqm'));
        if($siteinfo['smstype'] == 1){
			if(empty($siteinfo['aliid'])) $this->message('Access Key ID值不能为空');
			if(empty($siteinfo['alisecret'])) $this->message('Access Key Secret值不能为空');
			if(empty($siteinfo['aliqm'])) $this->message('短信签名不能为空');
		}

	    $config->write($siteinfo);
	    $this->success('success');
	}
	//获取余额
	function smgetbalance(){
		echo 'xxx';
		exit;
	}
	function smtopay(){
		 
	  $this->success('success');
	}
	function basedata(){
			$data['dirname']=time();
     	$data['list'] =	$this->mysql->gettales();
   	  Mysite::$app->setdata($data);
	}
	function savesqldata(){
		//limitalert();
			$tabelname = IReq::get('tabelname');
		$dirname = IReq::get('dirname');
		//创建文件夹
		IFile::mkdir(hopedir.'/dbbak/'.$dirname);
		/***获取数据***/

			$info = $this->mysql->getarr("show create table `$tabelname`");

		$sqldata[] = $info['0']['Create Table'];


		$list = $this->mysql->getarr("select * from ".$tabelname."      limit 0, 20000 ");
		if(is_array($list)){
       foreach($list as $key=>$value){
    	 $keys = array_keys($value);
    	 $key = array_map('addslashes', $keys);
       $key = join('`,`', $key);
       $keys = "`" . $key . "`";
       $vals = array_values($value);
       $vals = array_map('addslashes', $vals);
       $vals = join("','", $vals);
       $vals = "'" . $vals . "'";
       $sqldata[]= "INSERT INTO `$tabelname`($keys) VALUES($vals)";
      }
    }
     $configData = var_export($sqldata,true);
	  $configStr = "<?php return {$configData}?>";
    $fp = fopen(hopedir.'/dbbak/'.$dirname.'/'.$tabelname.'.php', 'w');
    fputs($fp, $configStr);
    fclose($fp); //保存 建表语句
    $this->success('success');
	}
	function rebkdata(){
		$list = array();
		$handler = opendir(hopedir.'/dbbak');
	  while( ($filename = readdir($handler)) !== false )
    {
      if($filename != '.' && $filename != '..'){
         $list[]=$filename;
      }
    }
    closedir($handler);
    $data['list'] = $list;
    $data['tablist'] =	$this->mysql->gettales(); //tablist
    $detfault = array_values($data['tablist']);
    $data['deftb'] = $detfault[0];
      Mysite::$app->setdata($data);
	}
	function savebkdata(){
		limitalert();
		 $tmsg = limitalert();
		if(!empty($tmsg)) $this->message($tmsg);
		
		
		
		  $tabelname = IReq::get('tabelname');
		if(empty($tabelname)) $this->message('other_emptytablepass');
		$dirname = IReq::get('dirname');
		if(empty($dirname)) $this->message('other_emptyfilenamepass');
		if(!file_exists(hopedir.'/dbbak/'.$dirname))$this->message('fileexit');
		if(!file_exists(hopedir.'/dbbak/'.$dirname.'/'.$tabelname.'.php')) $this->message('other_emptyfilenamepass');

		 $this->mysql->query('DROP TABLE  `'.$tabelname.'`');
		 $infos = include(hopedir.'/dbbak/'.$dirname.'/'.$tabelname.'.php');
		if(is_array($infos)){
		 foreach($infos as $key=>$value){
		 	$this->mysql->query($value);
		 }
		}
 
		$this->success('success');

	}
	function debkfile(){
		limitalert();
		$this->message("无操作权限");
		 $tmsg = limitalert();
		if(!empty($tmsg)) $this->message($tmsg);
		
			$dirname = IReq::get('dirname');
		if(empty($dirname)) $this->message('empty_filename');
		IFile::clearDir(hopedir.'/dbbak/'.$dirname);
		IFile::rmdir(hopedir.'/dbbak/'.$dirname);
	  $this->success('success');
	}
	function errlog(){
   	  $list = array();
	  	$handler = opendir(hopedir.'/log');
	    while( ($filename = readdir($handler)) !== false )
      {
        if($filename != '.' && $filename != '..'){
         $list[]=$filename;
        }
      }
      closedir($handler);
      $data['list'] = $list;
   	  Mysite::$app->setdata($data);
   }
   function delerrlog(){
	   limitalert();
		  $dirname = IReq::get('dirname');
	  	if(empty($dirname)) $this->message('empty_filename');
	  	IFile::unlink(hopedir.'/log/'.$dirname);
	   $this->success('success');
   }
   function download(){
  		$dirname = IReq::get('dirname');
  		if(empty($dirname)){
  		 echo '文件不存在';
  		 exit;
  		}
  		if(!file_exists(hopedir.'log/'.$dirname))//创建文件
      {
      	 echo '文件不存在';
  		   exit;
      }
     $file = fopen(hopedir.'/log/'.$dirname,"r"); // 打开文件
     Header("Content-type: application/octet-stream");
     Header("Accept-Ranges: bytes");
     Header("Accept-Length: ".filesize(hopedir.'/log/'.$dirname));
     Header("Content-Disposition: attachment; filename=" . $dirname);
     echo fread($file,filesize(hopedir.'/log/'.$dirname));
     fclose($file);
     exit();
   }
    
   function addspecialpage11(){  //添加或者编辑 专题页
	   
		$id = intval(IReq::get('id'));
		$data['info'] = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage where id=".$id."  ");
	 

	 	Mysite::$app->setdata($data);
	}
	 function savespecialpage(){  // 保存或者更新 专题页
	 	// limitalert();
		$id = IReq::get('ztyid');
	 
 	   	$data['name'] = IReq::get('name');
		$data['indeximg'] = IReq::get('indeximg');
		$data['imgwidth'] = IReq::get('imgwidth');
		$data['imgheight'] = IReq::get('imgheight');
		$data['specialimg'] = IReq::get('specialimg');
	   	$data['color'] = IReq::get('color');
	   	$data['is_custom'] = intval(IReq::get('is_custom'));
	   	$data['showtype'] = intval(IReq::get('showtype'));
	   	$shopcx_type = intval(IReq::get('shopcx_type'));
	   	$goodscx_type = intval(IReq::get('goodscx_type'));
		$zdylink = trim(IReq::get('zdylink'));
		$data['addtime'] = time();
		if($data['showtype'] == 0 ){
			$data['cx_type'] = $shopcx_type;
		}
		if($data['showtype'] == 1 ){
			$data['cx_type'] = $goodscx_type;
		}
	 
	   	$data['is_show'] = intval(IReq::get('is_show'));
	   	$data['orderid'] = intval(IReq::get('orderid'));
	   	$data['ruleintro'] = IReq::get('ruleintro');
		 
		$data['cityid'] = isset(Mysite::$app->config['default_cityid'])?Mysite::$app->config['default_cityid']:0;
		 
		 if(empty($id)){
			 $linkurl = IUrl::creatUrl('adminpage/other/module/specialpage');
		 }else{
			 
			 $linkurl =  IUrl::creatUrl('adminpage/other/module/addspecialpage/id/'.$id); 
			 
		 }
		 
		if(empty($data['name'])) $this->message('名称不能为空',$linkurl);
		if(empty($data['indeximg'])) $this->message('首页显示图片不能为空',$linkurl);
		if(empty($data['color'])) $this->message('专题页背景色调不能为空',$linkurl);
		if( $data['is_custom'] !=0 && $data['is_custom'] !=1) $this->message('请选择活动格式',$linkurl);
		if( $data['showtype'] !=0 && $data['showtype'] !=1 && $data['showtype'] !=2  ) $this->message('请选择活动针对对象',$linkurl);
		
		
		if( $data['showtype'] == 2 ) {
			if( empty($zdylink) ) {
				$this->message('请填写跳转链接',$linkurl);
			}
		}
		
		if( $data['is_custom'] == 1 ){
			if( $data['showtype'] != 2 ) {
				if($data['cx_type'] <=0 && $data['cx_type'] >1) $this->message('请选择对象对应活动类型',$linkurl);
			}
		}
		if($data['is_custom'] == 0){
			$data['cx_type'] = 0;
		}
			$data['zdylink'] = $zdylink;
	   	if(empty($id))
	   	{
			$link = IUrl::creatUrl('adminpage/other/module/specialpage'); 
	   		$this->mysql->insert(Mysite::$app->config['tablepre'].'specialpage',$data);
	 	$this->success('success',$link);
	   	}else{
	    	$link = IUrl::creatUrl('adminpage/other/module/addspecialpage/id/'.$id); 
			$this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,"id='".$id."'");
			$this->success('success',$link);
	   	}
	//   	$link = IUrl::creatUrl('adminpage/other/module/specialpage');	 
	 //   $this->success('success',$link);
	    
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
	function setstatus(){
	    $data['shoptype'] = array('0'=>'外卖','1'=>'超市');
	   Mysite::$app->setdata($data);
	}
	function selectztyobj(){	//专题页选择对象
	    $this->setstatus();
	    $where = '';
	    $goodswhere = '';
	     
	    
	    $data['shopname'] =  trim(IReq::get('shopname'));
	    $data['name'] =  trim(IReq::get('name'));
	   $data['username'] =  trim(IReq::get('username'));
	 	 $data['phone'] = trim(IReq::get('phone'));
	 	 if(!empty($data['shopname'])){
 		    $where .= " and shopname like '%".$data['shopname']."%'";
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
		
		
		$id = IReq::get('id');
		$data['id'] = $id;
		$ztyinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage where id=".$id."  ");
		$data['ztyinfo'] = $ztyinfo;
		
		$this->pageCls->setpage(intval(IReq::get('page')),60); 
	   
	  if($ztyinfo['showtype'] ==0){ 
 			$selectlist = $this->mysql->getarr("select * from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where."  order by sort asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()."  ");
 			$shuliang  = $this->mysql->counts("select id from ".Mysite::$app->config['tablepre']."shop  where is_pass = 1 ".$where." ");
	  }     
	   
	   if($ztyinfo['showtype'] == 1){
 			$selectlist = $this->mysql->getarr("select a.*,b.shopname from ".Mysite::$app->config['tablepre']."goods as a left join  ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.id > 0 and b.id > 0 ".$goodswhere."  order by a.good_order asc  limit ".$this->pageCls->startnum().", ".$this->pageCls->getsize()." ");
			
			$shuliang  = $this->mysql->counts("select a.id from ".Mysite::$app->config['tablepre']."goods as a left join  ".Mysite::$app->config['tablepre']."shop as b  on a.shopid = b.id where a.id > 0 ".$goodswhere."  ");
		 
	   }
	  #  print_r($selectlist);
	   $this->pageCls->setnum($shuliang); 
	  $data['pagecontent'] = $this->pageCls->getpagebar();
 		$data['selectlist'] = $selectlist;
 
	    Mysite::$app->setdata($data);
	    
	}
	function saveselectztyobj(){	//选择专题页对象 集
		// limitalert();
		$id = IReq::get('id');
		$zjtype = IReq::get('zjtype');
		
		
		$selectobjids = IReq::get('selectobjids');  //160,156, 
		$temparray  = explode(',',$selectobjids);
	/* 	$seobjids = implode(',',$temparray); // 160,156 */
		
		$checkinfo = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."specialpage where id=".$id."  ");
		if(!empty($checkinfo)){
			
			
			$yuanlaiids = explode( ',',$checkinfo['listids'] );
		    $tempids = array_diff($yuanlaiids,$temparray);
		 
			if($zjtype == 1){
				foreach($temparray as $key=>$value){
						$tempids[] = $value;
				}
			
			}
			 $templistids = array();
			foreach($tempids as  $key=>$value){
				if(!empty($value)){
					$templistids[] = $value;
				}
			}
			
			$data['listids'] = count($templistids >0)? join(',',$templistids):'';
			
			$this->mysql->update(Mysite::$app->config['tablepre'].'specialpage',$data,"id='".$id."'");
			$this->success('success');
		}
		
	}
	function delpsorder(){
		//最近 10天
		$oladtime = time()-10*86400;
		$newtime = time()-86400;
		$tempwhere = ' addtime > '.$oladtime.' and addtime < '.$newtime; 
		$this->mysql->delete(Mysite::$app->config['tablepre'].'orderps'," orderid not  in(select id from ".Mysite::$app->config['tablepre']."order where ".$tempwhere."  ) and status < 3 ");
		$link = IUrl::creatUrl('/adminpage/other/module/paylist'); 
		$this->refunction('清理为空配送单成功',$link); 
	}
	
	
}