<?php
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
            $data['kfw_appid']=$_GPC['kfw_appid'];
            $data['kfw_appsecret']=$_GPC['kfw_appsecret'];
            $data['uniacid']=$_W['uniacid'];
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('kfw',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('kfw',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/kfw');