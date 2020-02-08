<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
//$list = pdo_getall('cjdc_jfgoods',array('uniacid' => $_W['uniacid']),array(),'','num ASC');

$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$sql="select a.* ,b.name as type_name from " . tablename("cjdc_jfgoods") . " a"  . " left join " . tablename("cjdc_jftype") . " b on b.id=a.type_id where a.uniacid=".$_W['uniacid']."  order by num asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);	   
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_jfgoods") . " a"  . " left join " . tablename("cjdc_jftype") . " b on b.id=a.type_id where a.uniacid=".$_W['uniacid']."");
$pager = pagination($total, $pageindex, $pagesize);



if($_GPC['id']){
    $res=pdo_delete('cjdc_jfgoods',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功',$this->createWebUrl('jfgoods',array()),'success');
    }else{
        message('删除失败','','error');
    }
}
include $this->template('web/jfgoods');