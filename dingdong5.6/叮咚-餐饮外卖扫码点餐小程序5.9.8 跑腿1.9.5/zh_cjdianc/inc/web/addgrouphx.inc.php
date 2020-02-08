<?php

global $_GPC, $_W;

$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$user = pdo_fetchall("SELECT * FROM " . tablename('cjdc_user') . " WHERE uniacid= :weid   and name != '' ORDER BY id DESC", array(':weid' => $_W['uniacid']), 'id');
$info=pdo_get('cjdc_grouphx',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
   $data['store_id']=$storeid;       
   $data['hx_id']=$_GPC['user_id'];         
   $data['uniacid']=$_W['uniacid'];
   if($_GPC['id']==''){  
     $data['time']=time();  
    $res=pdo_insert('cjdc_grouphx',$data);
    if($res){
       message('添加成功！', $this->createWebUrl('grouphx'), 'success');
   }else{
    message('添加失败！','','error');
}
}else{
    $res=pdo_update('cjdc_grouphx',$data,array('id'=>$_GPC['id']));
    if($res){
       message('编辑成功！', $this->createWebUrl('grouphx'), 'success');
   }else{
       message('编辑失败！','','error');
   }
}
}



include $this->template('web/addgrouphx');