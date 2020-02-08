<?php
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('wpdc_system',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
            $data['is_email']=$_GPC['is_email'];
            $data['type']=$_GPC['type'];
            $data['uniacid']=$_W['uniacid'];
            $data['username']=trim($_GPC['username']);
            $data['password']=trim($_GPC['password']); 
             $data['sender']=$_GPC['sender']; 
              $data['signature']=$_GPC['signature']; 
            if($_GPC['id']==''){                
                $res=pdo_insert('wpdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('email',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('wpdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('email',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/email');