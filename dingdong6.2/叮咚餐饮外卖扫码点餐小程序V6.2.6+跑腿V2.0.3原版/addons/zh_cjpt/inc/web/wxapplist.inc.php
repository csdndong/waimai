<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
if($_GPC['keywords']){
	$op=$_GPC['keywords'];
	$where="%$op%";	
}else{
	$where='%%';
}
$modules='zh_cjdianc';
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select a.*,c.cy_uniacid  from " . tablename("account_wxapp") . " a"  . " left join " . tablename("wxapp_versions") . " b on b.uniacid=a.uniacid left join".tablename('cjpt_bind')." c on a.uniacid=c.cy_uniacid WHERE b.modules LIKE '%{$modules}%'  and a.name LIKE :name";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,array(':name'=>$where));	
// var_dump($list);die;   
$total=pdo_fetchcolumn("select count(*) from " . tablename("account_wxapp") . " a"  . " left join " . tablename("wxapp_versions") . " b on b.uniacid=a.uniacid WHERE b.modules LIKE '%{$_GPC['m']}%' and  name LIKE :name",array(':name'=>$where));
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=='bd'){
	$rst=pdo_get('cjpt_bind',array('pt_uniacid'=>$_W['uniacid']));
	if(!$rst){
	$data2['pt_uniacid']=$_W['uniacid'];
	$data2['cy_uniacid']=$_GPC['uniacid'];
	$data2['time']=time();
	$res=pdo_insert('cjpt_bind',$data2);
	if($res){
		 message('绑定成功',$this->createWebUrl('wxapplist',array()),'success');

	}else{
		 message('绑定失败',$this->createWebUrl('wxapplist',array()),'error');

	}

}else{

	 message('重复绑定,解除绑定后可绑定',$this->createWebUrl('wxapplist',array()),'error');

}


}

if($_GPC['op']=='jb'){
	$res=pdo_delete('cjpt_bind',array('cy_uniacid'=>$_GPC['uniacid'],'pt_uniacid'=>$_W['uniacid']));
	if($res){
		 message('解绑成功',$this->createWebUrl('wxapplist',array()),'success');

	}else{
		 message('解绑失败',$this->createWebUrl('wxapplist',array()),'error');

	}
}
include $this->template('web/wxapplist');