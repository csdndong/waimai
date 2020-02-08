<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$yj=pdo_getall('cjdc_address',array('uniacid'=>$_W['uniacid'],'level'=>1), array(), '', 'num asc');
$ej=pdo_getall('cjdc_address',array('uniacid'=>$_W['uniacid'],'level'=>2), array(), '', 'num asc');
$sj=pdo_getall('cjdc_address',array('uniacid'=>$_W['uniacid'],'level'=>3), array(), '', 'num asc');

for($i=0;$i<count($yj);$i++){
	$data2=array();
	for($j=0;$j<count($ej);$j++){
		$data3=array();
		for($k=0;$k<count($sj);$k++){
			if($sj[$k]['fid']==$ej[$j]['id']){
				$data3[]=$sj[$k];
			}
			$ej[$j]['sj']=$data3;
		}

		if($ej[$j]['fid']==$yj[$i]['id']){
			$data2[]=$ej[$j];
		}
		$yj[$i]['ej']=$data2;
		
	}

}
$list=$yj;
if($_GPC['op']=='change'){
	 $res=pdo_update('cjdc_address',array('state'=>$_GPC['state']),array('id'=>$_GPC['id']));
    if($res){
        message('操作成功',$this->createWebUrl('address',array()),'success');
    }else{
        message('操作失败','','error');
    }
}
if($_GPC['op']=='delete'){
	$ej=pdo_get('cjdc_address',array('fid'=>$_GPC['id']));
	if($ej){
		message('该地区下有二级地区无法删除','','error');
	}
    $res=pdo_delete('cjdc_address',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功',$this->createWebUrl('address',array()),'success');
    }else{
        message('删除失败','','error');
    }
}
//print_R($yj);die;

include $this->template('web/address');