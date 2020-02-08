<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
        $data['is_hy']=$_GPC['is_hy'];
        $data['hy_discount']=$_GPC['hy_discount'];
        $data['hy_note']=$_GPC['hy_note'];
        if($_GPC['zb_img']!=$item['zb_img']){
            $data['zb_img']=$_W['attachurl'].$_GPC['zb_img'];
        } 
        $data['uniacid']=$_W['uniacid'];
        $data['hy_details']=html_entity_decode($_GPC['hy_details']);
        $data['kt_details']=html_entity_decode($_GPC['kt_details']);
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('hyset',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('hyset',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/hyset');