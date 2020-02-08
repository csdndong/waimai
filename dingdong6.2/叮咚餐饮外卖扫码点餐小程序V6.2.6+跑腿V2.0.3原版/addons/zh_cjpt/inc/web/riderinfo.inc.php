<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$sql="SELECT * FROM ".tablename('cjpt_rider')."  where id=:id ";
$item=pdo_fetch($sql,array(':id'=>$_GPC['id']));
if(checksubmit('submit')){   
	$data['name']=$_GPC['name'];    
	$data['tel']=$_GPC['tel'];
	$data['state']=$_GPC['state'];
	$data['sh_time']=time();
	if($_GPC['state']==2){
  
		$data['status']=$_GPC['status'];
	}
	$rst=pdo_update('cjpt_rider',$data,array('id'=>$item['id']));
	if($rst){

	     message('编辑成功！', $this->createWebUrl('rider'), 'success');
	}else{
	     message('编辑失败！','','error');
	}

}

include $this->template('web/riderinfo');