<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();

$type=pdo_getall('cjdc_grouptype',array('uniacid'=>$_W['uniacid']),array('name','id'),'','num asc');

$where=" WHERE a.uniacid=:uniacid ";
$data[':uniacid']=$_W['uniacid'];


	//echo $_GPC['area'];die;
    if($_GPC['keywords']){
    	 $where.=" and (a.name LIKE  concat('%', :name,'%') || c.name LIKE  concat('%', :name,'%'))";
    	 $op=$_GPC['keywords'];
          $data[':name']="%$op%";
    	
    }

    if($_GPC['type_id']){
      $where .=" and a.type_id=:type_id";
      $data[':type_id']=$_GPC['type_id'];
    }
    if($_GPC['is_shelves2']){
    	$where .=" and a.is_shelves=:cid";
    	$data[':cid']=$_GPC['is_shelves2'];
    }


$pageindex = max(1, intval($_GPC['page']));
$pagesize=15;
$sql="select a.* ,b.name as type_name,c.name as store_name from " . tablename("cjdc_groupgoods") . " a"  . " left join " . tablename("cjdc_grouptype") . " b on b.id=a.type_id  left join " . tablename("cjdc_store") . " c on c.id=a.store_id".$where." order by num asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,$data);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_groupgoods") . " a"  . " left join " . tablename("cjdc_grouptype") . " b on b.id=a.type_id left join " . tablename("cjdc_store") . " c on c.id=a.store_id".$where,$data);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['id']){
	$data2['is_shelves']=$_GPC['is_shelves'];
	$res=pdo_update('cjdc_groupgoods',$data2,array('id'=>$_GPC['id']));
	if($res){
		message('设置成功',$this->createWebUrl('allgroupgoods',array('page'=>$_GPC['page'],'keywords'=>$_GPC['keywords'],'dishes_type'=>$_GPC['dishes_type'],'type_id'=>$_GPC['type_id'],'is_show2'=>$_GPC['is_show2'])),'success');
	}else{
		message('设置失败','','error');
	}
}
if($_GPC['op']=='delete'){
	$result = pdo_delete('cjdc_groupgoods', array('id'=>$_GPC['delid']));
		if($result){
			message('删除成功',$this->createWebUrl('allgroupgoods',array()),'success');
		}else{
			message('删除失败','','error');
		}
}
if($_GPC['op']=='play'){
	$data2['display']=$_GPC['display'];
	$res=pdo_update('cjdc_groupgoods',$data2,array('id'=>$_GPC['did']));
	if($res){
		message('设置成功',$this->createWebUrl('allgroupgoods',array('page'=>$_GPC['page'],'keywords'=>$_GPC['keywords'],'dishes_type'=>$_GPC['dishes_type'],'type_id'=>$_GPC['type_id'])),'success');
	}else{
		message('设置失败','','error');
	}
}

include $this->template('web/allgroupgoods');
