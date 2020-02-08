<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
            $data['dada_key']=trim($_GPC['dada_key']);
            $data['dada_secret']=trim($_GPC['dada_secret']);
            $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
            if($res){
                message('编辑成功',$this->createWebUrl('dasettings',array()),'success');
            }else{
                message('编辑失败','','error');
            }
           
    }
include $this->template('web/dasettings');