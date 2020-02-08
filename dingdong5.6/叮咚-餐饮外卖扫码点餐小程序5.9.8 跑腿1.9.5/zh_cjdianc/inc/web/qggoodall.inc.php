<?php

global $_GPC, $_W;

$GLOBALS['frames'] = $this->getMainMenu();

$where=" WHERE a.uniacid=:uniacid";

$data[':uniacid']=$_W['uniacid'];

if($_GPC['keywords']){

    $where .=" and (a.name LIKE :name || c.name LIKE :name)";

    $op=$_GPC['keywords'];

    $data[':name']="%$op%";      

}

if($_GPC['type_id']){

  $where .=" and a.type_id=:type_id";

  $data[':type_id']=$_GPC['type_id'];

}

if($_GPC['is_shelves2']){

    $where .=" and a.state=:cid";

    $data[':cid']=$_GPC['is_shelves2'];

}

$pageindex = max(1, intval($_GPC['page']));

$pagesize=10;

$sql="select a.* ,b.name as type_name,c.name as store_name from " . tablename("cjdc_qggoods") . " a"  . " left join " . tablename("cjdc_qgtype") . " b on b.id=a.type_id left join " . tablename("cjdc_store") . " c on c.id=a.store_id ".$where."  order by num asc";

$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;

$list = pdo_fetchall($select_sql,$data);	   

$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_qggoods") . " a"  . " left join " . tablename("cjdc_qgtype") . " b on b.id=a.type_id left join " . tablename("cjdc_store") . " c on c.id=a.store_id ".$where,$data);

$pager = pagination($total, $pageindex, $pagesize);





$type=pdo_getall('cjdc_qgtype',array('uniacid'=>$_W['uniacid']));

if($_GPC['op']=='del'){

    $res=pdo_delete('cjdc_qggoods',array('id'=>$_GPC['id']));

    if($res){

        message('删除成功',$this->createWebUrl('qggoodall',array()),'success');

    }else{

        message('删除失败','','error');

    }

}

if($_GPC['state']){

    $res=pdo_update('cjdc_qggoods',array('state'=>$_GPC['state']),array('id'=>$_GPC['id']));

    if($res){

        message('编辑成功',$this->createWebUrl('qggoodall',array()),'success');

    }else{

        message('编辑失败','','error');

    }

}


if($_GPC['state2']){

    $res=pdo_update('cjdc_qggoods',array('state2'=>$_GPC['state2']),array('id'=>$_GPC['id']));

    if($res){

        message('编辑成功',$this->createWebUrl('qggoodall',array()),'success');

    }else{

        message('编辑失败','','error');

    }

}
include $this->template('web/qggoodall');