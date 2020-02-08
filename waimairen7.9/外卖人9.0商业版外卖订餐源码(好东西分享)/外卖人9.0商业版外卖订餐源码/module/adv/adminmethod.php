<?php
class method   extends adminbaseclass
{
	 function deladv(){
		$id = IReq::get('id');
		if(empty($id))  $this->message('adv_empty');
		$ids = is_array($id)? join(',',$id):$id;
		$this->mysql->delete(Mysite::$app->config['tablepre'].'adv'," id in($ids) ");
		$this->success('success');
	}
	function saveadv(){
	 	$data['title'] = IReq::get('title');
		$data['advtype']= IReq::get('advtype');
		$data['img'] = IReq::get('img');
		$data['linkurl']= IReq::get('linkurl');
		$checkmodule =  trim(IReq::get('modulename'));//Mysite::$app->config['sitetemp'];saveadv 
		$data['module'] = !empty($checkmodule)?$checkmodule:Mysite::$app->config['sitetemp'];
		
		$default_cityid = Mysite::$app->config['default_cityid'];
		$data['cityid']	= empty($default_cityid)?0:$default_cityid;
		
		$uid = IReq::get('uid');
		if(empty($data['title'])) $this->message('adv_emptytitle');
		if(empty($data['img'])) $this->message('adv_emptyimg');
		if(empty($data['linkurl'])) $this->message('adv_emptylink');
		if(empty($uid)){
			$this->mysql->insert(Mysite::$app->config['tablepre'].'adv',$data);
		}else{
			$this->mysql->update(Mysite::$app->config['tablepre'].'adv',$data,"id='".$uid."'");
	 	}
    	$this->success('success');
	}

	 function saveadvtype(){
	 	 $arrtypename = IReq::get('typename');
			$arrtypeurl = IReq::get('typeurl');
			$arrtypeorder = IReq::get('typeorder');
		  if(empty($arrtypename)) $this->message('adv_emptytypename');
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
		$siteinfo['advtype'] =   serialize($newinfo);
		$config = new config('hopeconfig.php',hopedir);
	  $config->write($siteinfo);
	  $this->success('success');
   }
}



?>