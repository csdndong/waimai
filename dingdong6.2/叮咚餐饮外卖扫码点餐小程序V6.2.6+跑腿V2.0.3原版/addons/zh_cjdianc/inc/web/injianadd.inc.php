<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('cjdc_reduction',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
         $data['name']=$_GPC['name'];
        $data['full']=$_GPC['full'];
        $data['reduction']=$_GPC['reduction'];
        $data['type']=$_GPC['type'];
      	$data['store_id']=$storeid;
     if($_GPC['id']==''){  
        $res=pdo_insert('cjdc_reduction',$data);
        if($res){
             message('添加成功！', $this->createWebUrl('injian'), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('cjdc_reduction',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl('injian'), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}
include $this->template('web/injianadd');