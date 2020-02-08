<?php
defined('IN_IA') or exit('Access Denied');
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getMainMenu2();
    $item=pdo_get('wpdc_store',array('id'=>$storeid));
    if(checksubmit('submit')){
            $data['is_yue']=$_GPC['is_yue']; 
            $data['is_jfpay']=$_GPC['is_jfpay'];
            if($_GPC['id']==''){                
                $res=pdo_insert('wpdc_store',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('inpay',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{
                $res = pdo_update('wpdc_store', $data, array('id' => $_GPC['id']));
                if($res){
                    message('编辑成功',$this->createWebUrl('inpay',array()),'success');
                }else{
                    message('编辑失败','','error');
                }
            }
        }
    include $this->template('web/inpay');