<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$pageindex = max(1, intval($_GPC['page']));
$pagesize=20;
$time=strtotime(date("Y-m-d"));
$del="delete from ".tablename('cjdc_number')." where uniacid={$_W['uniacid']} and unix_timestamp(time)< {$time}";
pdo_query($del);
$where=" WHERE uniacid={$_W['uniacid']} and store_id={$storeid} and state!=4";
$sql=" select id,num,state,count(id) as count from" . tablename("cjdc_number") .$where." group by num ";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_number").$where." group by num ");
foreach($list as $key => $value){
	$num=$value['num'];
	$newsql=" select id,num,code from ".tablename('cjdc_number')." where uniacid={$_W['uniacid']} and store_id={$storeid}  and num='{$num}' and state=1  order by id asc";
	$res=pdo_fetch($newsql);
	if($res){
		$list[$key]['dq']=$res['code'];
		$list[$key]['pid']=$res['id'];
	}else{
		$list[$key]['dq']='暂无排队信息';
		$list[$key]['pid']='null';
	}
}
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['op']=="delete"){
	$result = pdo_delete('cjdc_number', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl2('dlnumber',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
include $this->template('web/dlnumber');