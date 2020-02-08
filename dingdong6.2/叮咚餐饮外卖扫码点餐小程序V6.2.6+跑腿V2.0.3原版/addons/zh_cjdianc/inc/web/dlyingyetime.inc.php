<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$info=pdo_get('cjdc_store',array('id'=>$storeid));
if(checksubmit('submit')){
			$data['is_rest']=$_GPC['is_rest'];
			$data['time']=$_GPC['time'];
			$data['time2']=$_GPC['time2'];
			$data['time3']=$_GPC['time3'];
			$data['time4']=$_GPC['time4'];
			$data['uniacid']=$_W['uniacid'];
				$res = pdo_update('cjdc_store', $data, array('id' => $storeid));
				if($res){
					message('编辑成功',$this->createWebUrl2('dlyingyetime',array()),'success');
				}else{
					message('编辑失败','','error');
				}
		}
include $this->template('web/dlyingyetime');