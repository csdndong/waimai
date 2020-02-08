<?php
global $_GPC, $_W;
// $action = 'ad';
// $title = $this->actions_titles[$action];
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
 $item=pdo_get('cjdc_message',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
    $data['wechat_appId']=trim($_GPC['wechat_appId']);
    $data['wechat_appsecret']=trim($_GPC['wechat_appsecret']);
    $data['wechat_wm_tid']=trim($_GPC['wechat_wm_tid']);
    if($_GPC['id']==''){                
        $data['uniacid']=trim($_W['uniacid']);
        $res=pdo_insert('cjdc_message',$data);
        if($res){
            message('添加成功',$this->createWebUrl('wetemplate',array()),'success');
        }else{
            message('添加失败','','error');
        }
    }else{
        $res = pdo_update('cjdc_message', $data, array('id' => $_GPC['id']));
        if($res){
            message('编辑成功',$this->createWebUrl('wetemplate',array()),'success');
        }else{
            message('编辑失败','','error');
        }
    }
}
include $this->template('web/wetemplate');