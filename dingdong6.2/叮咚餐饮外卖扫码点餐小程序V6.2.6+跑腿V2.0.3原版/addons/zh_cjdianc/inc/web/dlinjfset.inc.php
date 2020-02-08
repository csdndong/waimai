<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$info=pdo_get('wpdc_store',array('uniacid'=>$_W['uniacid'],'id'=>$storeid));
    if(checksubmit('submit')){
            $data['is_jf']=$_GPC['is_jf'];
            $data['is_yyjf']=$_GPC['is_yyjf'];
            $data['is_wmjf']=$_GPC['is_wmjf'];
            $data['is_dnjf']=$_GPC['is_dnjf'];
            $data['is_dmjf']=$_GPC['is_dmjf'];
            $data['is_yuejf']=$_GPC['is_yuejf'];
                $res = pdo_update('wpdc_store', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl2('dlinjfset',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
           
        }
include $this->template('web/dlinjfset');