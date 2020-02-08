<?php
global $_GPC, $_W;
// $action = 'ad';
// $title = $this->actions_titles[$action];
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));

// echo date('Y-m-d H:i:s','1659633750');die;
// echo strtotime("next year");die;
    if(checksubmit('submit')){
            $data['is_mdrz']=trim($_GPC['is_mdrz']);
            $data['md_sh']=trim($_GPC['md_sh']);
            $data['uniacid']=trim($_W['uniacid']);
        
             $data['rz_title']=trim($_GPC['rz_title']);
             $data['rz_ms']=html_entity_decode($_GPC['rz_ms']);
             $data['rz_details']=html_entity_decode($_GPC['rz_details']);
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('rzset',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('rzset',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/rzset');