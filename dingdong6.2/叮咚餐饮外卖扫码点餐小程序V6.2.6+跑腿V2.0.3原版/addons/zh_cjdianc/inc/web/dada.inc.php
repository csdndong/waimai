<?php
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getMainMenu2();
$info=pdo_get('cjdc_psset',array('store_id'=>$storeid));
    if(checksubmit('submit')){
            $data['source_id']=$_GPC['source_id'];
            $data['shop_no']=$_GPC['shop_no'];
            $data['store_id']=$storeid;
            $data['uniacid']=$_W['uniacid'];
           // $data['ps_mode']=$_GPC['ps_mode'];
            if($_GPC['id']){
                 $res = pdo_update('cjdc_psset', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('dada',array()),'success');
                }else{
                    message('编辑失败','','error');
                } 
            }else{
                $res = pdo_insert('cjdc_psset', $data);
                if($res){
                    message('编辑成功',$this->createWebUrl('dada',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }

           
        }
include $this->template('web/dada');