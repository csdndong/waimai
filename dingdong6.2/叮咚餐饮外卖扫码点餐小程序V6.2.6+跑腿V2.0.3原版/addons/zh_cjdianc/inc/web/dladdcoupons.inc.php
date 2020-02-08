<?php
global $_GPC, $_W;
$action = 'start';
//$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$uid=$_COOKIE["uid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$list = pdo_get('wpdc_coupons',array('id'=>$_GPC['id']));
		if(checksubmit('submit')){
				$data['name']=$_GPC['name'];
				$data['conditions']=$_GPC['conditions'];
				$data['preferential']=$_GPC['preferential'];
				$data['start_time']=$_GPC['time']['start'];
				$data['end_time']=$_GPC['time']['end'];
				$data['uniacid']=$_W['uniacid'];
				$data['store_id']=$storeid;
				$data['instruction']=$_GPC['instruction'];
				$data['coupons_type']=$_GPC['coupons_type'];
			if($_GPC['id']==''){
				$res=pdo_insert('wpdc_coupons',$data);
				if($res){
					message('添加成功',$this->createWebUrl2('dlcoupons',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('wpdc_coupons', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl2('dlcoupons',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/dladdcoupons');