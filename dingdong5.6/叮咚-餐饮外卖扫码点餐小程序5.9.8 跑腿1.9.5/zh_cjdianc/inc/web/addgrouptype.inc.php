<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
	$info = pdo_get('cjdc_grouptype',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
		if(checksubmit('submit')){
			if($info['img']!=$_GPC['img']){
				$data['img']=$_W['attachurl'].$_GPC['img'];
			}else{
				$data['img']=$_GPC['img'];
			}
			
			$data['num']=$_GPC['num'];
			$data['name']=$_GPC['name'];
			$data['uniacid']=$_W['uniacid'];
			if($_GPC['id']==''){				
				$res=pdo_insert('cjdc_grouptype',$data);
				if($res){
					message('添加成功',$this->createWebUrl('grouptype',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('cjdc_grouptype', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('grouptype',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addgrouptype');