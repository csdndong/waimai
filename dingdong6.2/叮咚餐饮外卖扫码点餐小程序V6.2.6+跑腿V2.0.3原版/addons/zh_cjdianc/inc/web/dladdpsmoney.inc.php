<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$info=pdo_get('cjdc_distribution',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
         $data['start']=$_GPC['start'];
        $data['end']=$_GPC['end'];
        $data['money']=$_GPC['money'];
        $data['num']=$_GPC['num'];
      	$data['store_id']=$storeid;
     if($_GPC['id']==''){  
        $res=pdo_insert('cjdc_distribution',$data);
        if($res){
             message('添加成功！', $this->createWebUrl2('dlpsmoney'), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('cjdc_distribution',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl2('dlpsmoney'), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}

include $this->template('web/dladdpsmoney');