<?php
global $_GPC, $_W;
$action = 'start';
//$GLOBALS['frames'] = $this->getMainMenu2();
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$type=pdo_getall('cjdc_table_type',array('uniacid'=>$_W['uniacid'],'store_id'=>$storeid));
$where="WHERE a.uniacid=:uniacid and a.store_id=:store_id";
$data[':uniacid']=$_W['uniacid'];
$data[':store_id']=$storeid;
if($_GPC['op']=='del'){
	$result = pdo_delete('cjdc_table', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl2('dltable2',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
if($_GPC['status']){
	if($_GPC['status']=="4"){
		$_GPC['status']=0;
	}
	$data2['status']=$_GPC['status'];
	$res=pdo_update('cjdc_table',$data2,array('id'=>$_GPC['id']));
	if($res){
		message('编辑成功',$this->createWebUrl2('dltable2',array()),'success');
	}else{
		message('编辑失败','','error');
	}
}
if(checksubmit('submit')){
	//echo $_GPC['area'];die;
    if($_GPC['keywords']){
    	$where .=" and a.name LIKE :name ";
    	 $op=$_GPC['keywords'];
          $data[':name']="%$op%";
    	
    }
    if($_GPC['type']){
    	$where .=" and a.type_id=:type_id";
    	$data[':type_id']=$_GPC['type'];
    }
} 
if($_GPC['op']=='qt'){
	$res=pdo_update('cjdc_table',array('status'=>0));
	if($res){
		message('清台成功',$this->createWebUrl2('dltable2',array()),'success');
	}else{
		message('清台失败','','error');
	}
} 
$sql="select a.*,b.name as type_name from " . tablename("cjdc_table") . " a"  . " left join " . tablename("cjdc_table_type") . " b on b.id=a.type_id " .$where;
$list=pdo_fetchall($sql,$data);	


include $this->template('web/dltable2');