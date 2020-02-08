<?php
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getMainMenu2();
$info=pdo_get('cjdc_storeset',array('store_id'=>$storeid));
    if(checksubmit('submit')){
            $data['is_jd']=$_GPC['is_jd'];

                $res = pdo_update('cjdc_storeset', $data, array('store_id' => $storeid));
                if($res){
                    message('编辑成功',$this->createWebUrl('laoz',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
           
        }
include $this->template('web/laoz');