<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
    $data['url_name']=$_GPC['url_name'];
    $data['tel']=$_GPC['tel'];
    $data['logo']=$_GPC['logo'];
    $data['distance']=$_GPC['distance'];
    $data['bj_logo']=$_GPC['bj_logo'];
    $data['db_logo']=$_GPC['db_logo'];
    $data['yc_money']=$_GPC['yc_money'];
    $data['db_content']=html_entity_decode($_GPC['db_content']);
    if($_GPC['color']){
        $data['color']=$_GPC['color'];
    }else{
        $data['color']="#34AAFF";
    }
    $data['details']=html_entity_decode($_GPC['details']);
    $data['rz_details']=html_entity_decode($_GPC['rz_details']);
    $data['uniacid']=$_W['uniacid'];
    if($_GPC['id']==''){                
        $res=pdo_insert('cjpt_system',$data);
        if($res){
            message('添加成功',$this->createWebUrl('settings',array()),'success');
        }else{
            message('添加失败','','error');
        }
    }else{
        $res = pdo_update('cjpt_system', $data, array('id' => $_GPC['id']));
        if($res){
            message('编辑成功',$this->createWebUrl('settings',array()),'success');
        }else{
            message('编辑失败','','error');
        }
    }
}
include $this->template('web/settings');