<?php
class method   extends adminbaseclass
{		
 
 function appletsave(){
	   
		$info['appletAppID'] = trim(IReq::get('appletAppID'));
 		$info['appletsecret'] = trim(IReq::get('appletsecret'));
 		$info['appletmapkey'] = trim(IReq::get('appletmapkey'));
 		$info['is_pass_applet'] = intval(IReq::get('is_pass_applet'));
		if(empty($info['appletAppID'])) $this->message('小程序appid不能为空');
		if(empty($info['appletsecret'])) $this->message('小程序secret不能为空');
		if(empty($info['appletmapkey'])) $this->message('小程序高德KEY值不能为空');
		  $config = new config('hopeconfig.php',hopedir);
		  $config->write($info);
		  $single = $this->mysql->select_one("select * from ".Mysite::$app->config['tablepre']."single where code='applet' ");
		$singledata['content'] = IReq::get('content');
		if(empty($singledata['content'])) $this->message('审核首页内容不能为空');
		#print_r($singledata['content']);exit;
		if(!empty($single)){
			$this->mysql->update(Mysite::$app->config['tablepre'].'single',$singledata,"code='applet'");
		}else{
			$singledata['addtime'] = time();
			$singledata['title'] = '小程序审核首页';
			$singledata['code'] = 'applet';
			$singledata['seo_key'] = '小程序';
			$singledata['seo_content'] = '小程序首页内容';
			$link = IUrl::creatUrl('adminpage/applet/module/appletset');
			$this->mysql->insert(Mysite::$app->config['tablepre'].'single',$singledata);
		}
		  $this->success('操作成功');
   }

	
}