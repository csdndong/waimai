<?php
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$certfile = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_cert_' . $_W['uniacid'] . '.pem';
$keyfile = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_key_' . $_W['uniacid'] . '.pem';
$item=pdo_get('cjdc_pay',array('uniacid'=>$_W['uniacid']));
$item2=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
if(checksubmit('submit')){
    if($_GPC['apiclient_cert']){
        file_put_contents($certfile, trim($_GPC['apiclient_cert']));
        $data['apiclient_cert']=$_GPC['apiclient_cert'];
    }
    if($_GPC['apiclient_key']){
       file_put_contents($keyfile, trim($_GPC['apiclient_key']));
       $data['apiclient_key']=$_GPC['apiclient_key']; 
   }
   $data['mchid']=trim($_GPC['mchid']);
   $data['wxkey']=trim($_GPC['wxkey']);
   $data['uniacid']=trim($_W['uniacid']);
   $data['ip']=$_GPC['ip'];
   $data2['is_yuepay']=$_GPC['is_yuepay'];
   $res2=pdo_update('cjdc_system', $data2, array('uniacid' => $_W['uniacid']));
   if($_GPC['id']==''){                
    $res=pdo_insert('cjdc_pay',$data);
    if($res || $res2){
        message('添加成功',$this->createWebUrl('pay',array()),'success');
    }else{
        message('添加失败','','error');
    }
}else{
    $res = pdo_update('cjdc_pay', $data, array('id' => $_GPC['id']));
    if($res || $res2){
        message('编辑成功',$this->createWebUrl('pay',array()),'success');
    }else{
        message('编辑失败','','error');
    }
}
}
include $this->template('web/pay');