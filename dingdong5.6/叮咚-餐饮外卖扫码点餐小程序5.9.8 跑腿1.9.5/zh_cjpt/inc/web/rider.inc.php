<?php
global $_GPC, $_W;
$day=100;
// echo date('Y-m-d H:i:s',strtotime("+{$day}day"));die;
// echo strtotime('2020-06-08');die;
$GLOBALS['frames'] = $this->getMainMenu();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type=empty($_GPC['type']) ? 'wait' :$_GPC['type'];
$state=empty($_GPC['state'])?1:$_GPC['state'];
$pageindex = max(1, intval($_GPC['page']));
$pagesize=20;
$where=' WHERE  uniacid=:uniacid  ';
$data[':uniacid']=$_W['uniacid'];
if($_GPC['keywords']){
    $where.=" and (name LIKE  concat('%', :name,'%') or tel LIKE  concat('%', :name,'%'))";    
    $data[':name']=$_GPC['keywords'];
    $type='all';
}
if($type !='all'){
     $where.= " and state=".$state;
}
$sql="SELECT * FROM ".tablename('cjpt_rider'). $where." ORDER BY id DESC";
$total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjpt_rider') .$where,$data);
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);
foreach ($list as $key => $value) {
$yx=pdo_get('cjpt_dispatch', array('uniacid'=>$_W['uniacid'],'qs_id '=>$value['id'],'state'=>4), array('sum(ps_money) as total_money'));
    //冻结资金
$dj=pdo_get('cjpt_dispatch', array('uniacid'=>$_W['uniacid'],'qs_id '=>$value['id'],'state'=>array(2,3)), array('sum(ps_money) as total_money'));

$tx=pdo_get('cjpt_withdrawal', array('uniacid'=>$_W['uniacid'],'qs_id '=>$value['id'],'state'=>array(1,2)), array('sum(tx_cost) as total_money'));
    $list[$key]['yx']=$yx['total_money']-$tx['total_money'];
    $list[$key]['dj']=empty($dj['total_money'])?0:$dj['total_money'];
}
$pager = pagination($total, $pageindex, $pagesize);
if($operation=='adopt'){//审核通过 
$res=pdo_update('cjpt_rider',array('state'=>2,'sh_time'=>time()),array('id'=>$id));  
  if($res){
        message('修改成功',$this->createWebUrl('rider',array()),'success');
    }else{
        message('修改失败','','error');
    }
 
}
if($operation=='delete'){
     $id=$_GPC['id'];
     $res=pdo_delete('cjpt_rider',array('id'=>$id));
     if($res){
        message('删除成功',$this->createWebUrl('rider',array()),'success');
    }else{
        message('删除失败','','error');
    }

}
if(checksubmit('submit')){
    $data2['name']=$_GPC['name'];
    $data2['tel']=$_GPC['tel'];
    if(strlen($_GPC['pwd'])!=32){
         $data2['pwd']=md5($_GPC['pwd']);
    }
    $data2['status']=$_GPC['status'];
    $data2['email']=$_GPC['email'];
    $res=pdo_update('cjpt_rider',$data2,array('id'=>$_GPC['id']));
      message('修改成功',$this->createWebUrl('rider',array()),'success');

    }
include $this->template('web/rider');