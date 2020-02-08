<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('cjdc_cptj',array('id'=>$_GPC['id']));
if(checksubmit('submit')){
    if($info['img']!=$_GPC['img']){
    $data['img']=$_W['attachurl'].$_GPC['img'];
  }
  $data['name']=$_GPC['name'];
  $data['content']=$_GPC['content'];
  $data['num']=$_GPC['num'];
  $data['src']=$_GPC['src'];
  $data['details']=html_entity_decode($_GPC['details']);
  $data['uniacid']=$_W['uniacid'];
     if($_GPC['id']==''){  
        $res=pdo_insert('cjdc_cptj',$data);
        if($res){
             message('添加成功！', $this->createWebUrl('cptj'), 'success');
        }else{
             message('添加失败！','','error');
        }
    }else{
        $res=pdo_update('cjdc_cptj',$data,array('id'=>$_GPC['id']));
        if($res){
             message('编辑成功！', $this->createWebUrl('cptj'), 'success');
        }else{
             message('编辑失败！','','error');
        }
    }
}
include $this->template('web/addcptj');