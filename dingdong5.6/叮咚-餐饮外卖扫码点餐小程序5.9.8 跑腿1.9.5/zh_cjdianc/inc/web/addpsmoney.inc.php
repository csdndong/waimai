<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('cjdc_distribution',array('id'=>$_GPC['id']));
$store_id=$_GPC['store_id'];
if($info){
    $store_id=$info['store_id'];
}
if(checksubmit('submit')){
         $data['start']=$_GPC['start'];
        $data['end']=$_GPC['end'];
        $data['money']=$_GPC['money'];
        $data['num']=$_GPC['num'];
      	
     if($_GPC['id']==''){  
     	$store=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid'],'state'=>2),'id');
     	foreach ($store as $key => $value) {
     		if($value['id']!=$store_id){
     		$psmoney=pdo_get('cjdc_distribution',array('store_id'=> $value['id']));
     		if(empty($psmoney)){
     			$data['store_id']= $value['id'];
     			  $res=pdo_insert('cjdc_distribution',$data);
     		}
     	}else{
     		$data['store_id']= $store_id;
     		 $res=pdo_insert('cjdc_distribution',$data);
     	}
     	}     
        if($res){
             message('添加成功！', $this->createWebUrl('psmoney',array('id'=>$store_id)), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('cjdc_distribution',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl('psmoney',array('id'=>$store_id)), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}

include $this->template('web/addpsmoney');