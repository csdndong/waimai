<?php
class method   extends adminbaseclass
{

	 function savesingle(){
	 	$id = IReq::get('uid');
		$data['addtime'] = strtotime(IReq::get('addtime').' 00:00:00');
		$data['title'] = IReq::get('title');
		$data['content'] = IReq::get('content');
		$data['code'] = IReq::get('code');
			$data['seo_key'] = IFilter::act(IReq::get('seo_key'));
	   	$data['seo_content'] = IFilter::act(IReq::get('seo_content'));
		if(empty($id))
		{
			$link = IUrl::creatUrl('adminpage/single/module/addsingle');
			if(empty($data['content'])) $this->message('single_emptycontent',$link);
			if(empty($data['title'])) $this->message('single_emptytitle',$link);
			$this->mysql->insert(Mysite::$app->config['tablepre'].'single',$data);
		}else{
			$link = IUrl::creatUrl('single/addsingle/id/'.$id);
			if(empty($data['content'])) $this->message('single_emptycontent',$link);
			if(empty($data['title'])) $this->message('single_emptytitle',$link);
			$this->mysql->update(Mysite::$app->config['tablepre'].'single',$data,"id='".$id."'");
		}
		$link = IUrl::creatUrl('adminpage/single/module/singlelist');
		$this->success('success',$link);
	 }
	 function delsingle(){
	 	 $uid = IReq::get('id');
		 $uid = is_array($uid)?$uid:array($uid);
		 $ids = join(',',$uid);
		 if(empty($ids))  $this->message('single_empty');
	   $this->mysql->delete(Mysite::$app->config['tablepre'].'single',"id in (".$ids.") ");
	   $this->success('success');
	 }
}



?>