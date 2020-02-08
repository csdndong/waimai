<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item = pdo_fetch("SELECT a.*,b.name as table_name,c.name as type_name FROM ".tablename('cjdc_order'). " a"  . " left join " . tablename("cjdc_table") . " b on a.table_id=b.id  left join " . tablename("cjdc_table_type") ." c on b.type_id=c.id WHERE  a.id=:id", array(':id'=>$_GPC['id']));
$goods=pdo_getall('cjdc_order_goods',array('order_id'=>$_GPC['id']));
if(checksubmit('submit')){
      $res=pdo_update('cjdc_order',array('old_money'=>$item['money'],'money'=>$_GPC['reply']),array('id'=>$_GPC['id2']));
    if($res){
       message('修改成功！', $this->createWebUrl('dnorderinfo',array('id'=>$_GPC['id2'])), 'success');
    }else{
       message('修改失败！','','error');
    }
}
include $this->template('web/dnorderinfo');