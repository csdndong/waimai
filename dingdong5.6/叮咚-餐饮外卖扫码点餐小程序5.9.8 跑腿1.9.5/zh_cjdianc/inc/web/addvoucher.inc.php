<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$list = pdo_get('wpdc_voucher',array('id'=>$_GPC['id']));
		if(checksubmit('submit')){
				$data['name']=$_GPC['name'];
				$data['preferential']=$_GPC['preferential'];
				$data['start_time']=$_GPC['time']['start'];
				$data['end_time']=$_GPC['time']['end'];
				$data['uniacid']=$_W['uniacid'];
				$data['instruction']=$_GPC['instruction'];
				$data['store_id']=$storeid;
				$data['voucher_type']=$_GPC['voucher_type'];
			if($_GPC['id']==''){
				$res=pdo_insert('wpdc_voucher',$data);
				if($res){
					message('添加成功',$this->createWebUrl('voucher',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('wpdc_voucher', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('voucher',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addvoucher');