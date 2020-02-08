<?php
class method   extends areaadminbaseclass
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
		$data['cityid']	= $this->admin['cityid'];
		
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

 }



?>