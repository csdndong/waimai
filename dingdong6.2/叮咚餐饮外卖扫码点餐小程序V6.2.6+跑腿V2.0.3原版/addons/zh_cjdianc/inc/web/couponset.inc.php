<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();

$item=pdo_get('cjdc_couponset',array('uniacid'=>$_W['uniacid']));


    if(checksubmit('submit')){
    	if($_GPC['time2']==$_GPC['time']){
    		message('开始时间和结束时间不能一样','','error');
    	}
    	if($_GPC['time2']<$_GPC['time']){
    		message('结束时间不能小于开始时间','','error');
    	}
            $data['yhq_set']=$_GPC['yhq_set'];
            $data['is_tjhb']=$_GPC['is_tjhb'];
            $data['time']=$_GPC['time'];
            $data['time2']=$_GPC['time2'];
            $data['number']=$_GPC['number'];
            $data['uniacid']=$_W['uniacid'];
            if($_GPC['id']==''){                
                $res=pdo_insert('cjdc_couponset',$data);
                if($res){
                    message('添加成功',$this->createWebUrl('couponset',array()),'success');
                }else{
                    message('添加失败','','error');
                }
            }else{	
	            $res = pdo_update('cjdc_couponset', $data, array('id' => $_GPC['id']));
	            if($res){
	                message('编辑成功',$this->createWebUrl('couponset',array()),'success');
	            }else{
	                message('编辑失败','','error');
	            }
        }
        }
include $this->template('web/couponset');