<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('cjpt_fee',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
        $data['start']=$_GPC['start'];
        $data['end']=$_GPC['end'];
        $data['money']=$_GPC['money'];
        $data['num']=$_GPC['num'];
        $data['uniacid']=$_W['uniacid'];
     if($_GPC['id']==''){  
        $res=pdo_insert('cjpt_fee',$data);
        if($res){
             message('添加成功！', $this->createWebUrl('psmoney'), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('cjpt_fee',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl('psmoney'), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}

include $this->template('web/addpsmoney');