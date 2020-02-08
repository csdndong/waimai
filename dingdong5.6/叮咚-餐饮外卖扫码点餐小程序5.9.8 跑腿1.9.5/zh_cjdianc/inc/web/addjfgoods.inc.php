<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
	$info = pdo_get('cjdc_jfgoods',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
	$type = pdo_getall('cjdc_jftype',array('uniacid' => $_W['uniacid']));
		if(checksubmit('submit')){
			$data['name']=$_GPC['name'];
			if($info['img']!=$_GPC['img']){
				$data['img']=$_W['attachurl'].$_GPC['img'];
			}else{
				$data['img']=$_GPC['img'];
			}
			$data['money']=$_GPC['money'];
			$data['type_id']=$_GPC['type_id'];
			$data['goods_details']=html_entity_decode($_GPC['goods_details']);
			$data['process_details']=html_entity_decode($_GPC['process_details']);
			$data['attention_details']=html_entity_decode($_GPC['attention_details']);
			$data['number']=$_GPC['number'];
			$data['is_open']=$_GPC['is_open'];
			$data['type']=$_GPC['type'];
			$data['num']=$_GPC['num'];
			$data['hb_moeny']=$_GPC['hb_moeny'];
			$data['uniacid']=$_W['uniacid'];
			if($_GPC['id']==''){				
				$res=pdo_insert('cjdc_jfgoods',$data);
				if($res){
					message('添加成功',$this->createWebUrl('jfgoods',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{
				$res = pdo_update('cjdc_jfgoods', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('jfgoods',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addjfgoods');