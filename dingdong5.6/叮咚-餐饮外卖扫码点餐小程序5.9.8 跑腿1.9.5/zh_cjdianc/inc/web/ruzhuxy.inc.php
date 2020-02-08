<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('wpdc_system',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
            $data['uniacid']=$_W['uniacid'];
            $data['cjwt']=html_entity_decode($_GPC['cjwt']);
            $data['rzxy']=html_entity_decode($_GPC['rzxy']);
            if($_GPC['id']==''){                
                $res=pdo_insert('wpdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('ruzhuxy',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('wpdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('ruzhuxy',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
include $this->template('web/ruzhuxy');