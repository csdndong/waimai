<?php
global $_GPC, $_W;
//founder 创始人
$GLOBALS['frames'] = $this->getMainMenu();
 $item=pdo_get('cjdc_system',array('uniacid'=>$_GPC['uniacid']));
    if(checksubmit('submit')){
    		$data['link_name']=$_GPC['link_name'];
             if($item['link_logo']!=$_GPC['link_logo']){
            $data['link_logo']=$_W['attachurl'].$_GPC['link_logo'];
        }
    		
             if($item['bq_logo']!=$_GPC['bq_logo']){
            $data['bq_logo']=$_W['attachurl'].$_GPC['bq_logo'];
        }
    		$data['bq_name']=$_GPC['bq_name'];
    		$data['tz_name']=$_GPC['tz_name'];
    		$data['tz_appid']=trim($_GPC['tz_appid']);
    		$data['support']=$_GPC['support'];
            $data['uniacid']=$_GPC['uniacid'];
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_system',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('banquanset',array('uniacid'=>$_GPC['uniacid'])),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('cjdc_system', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('banquanset',array('uniacid'=>$_GPC['uniacid'])),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }

include $this->template('web/banquanset');