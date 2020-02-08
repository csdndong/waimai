<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('wpdc_seller',array('id'=>$_GPC['id']));
$store=pdo_getall('wpdc_store',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
         $data['account']=$_GPC['account'];
        $data['pwd']=md5($_GPC['pwd']);
        $data['cerated_time']=time();
        $data['store_id']=$_GPC['store_id'];
         $data['state']=$_GPC['state'];
        $data['uniacid']=$_W['uniacid'];
     if($_GPC['id']==''){  
        $res=pdo_insert('wpdc_seller',$data);
        if($res){
             message('添加成功！', $this->createWebUrl('admin'), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('wpdc_seller',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl('admin'), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}
include $this->template('web/addadmin');