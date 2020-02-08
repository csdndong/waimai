<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$sys=pdo_get('cjdc_fxset',array('uniacid'=>$_W['uniacid']),'is_ej');
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$state=$_GPC['state'];
$where=' WHERE  a.uniacid=:uniacid ';
$data[':uniacid']=$_W['uniacid'];
if($_GPC['keywords']){
    $where.=" and (b.name LIKE  concat('%', :name,'%') || a.user_name LIKE  concat('%', :name,'%'))";   
    $data[':name']=$_GPC['keywords'];
} 
if($state&&$state!="all"){
    $where.=" and  a.state=:state";   
    $data[':state']=$state;
} 


$sql="select a.* ,b.img,b.name from " . tablename("cjdc_retail") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id  ". $where." ORDER BY id DESC";
  $total=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('cjdc_retail') . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id ".  "".$where,$data);
$list=pdo_fetchall( $sql,$data);

$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list=pdo_fetchall($select_sql,$data);

foreach ($list as $key => $value) {
  //上级分销商
  $sql3=" select b.name from".tablename('cjdc_fxuser')." a left join".tablename('cjdc_user')." b on a.user_id=b.id where a.fx_user={$value['user_id']}";
    $sj=pdo_fetch($sql3);
  //一级分销商人数
  $xjrs=pdo_get('cjdc_fxuser', array('user_id'=>$value['user_id']), array('count(id) as count','fx_user'));
 if($sys['is_ej']==2){
 	$ejrs=pdo_get('cjdc_fxuser', array('user_id'=>$xjrs['fx_user']), array('count(id) as count'));

 }
  //有效佣金
  $sql2="select sum( case when state=1 then money else 0 end) as djyj, sum( case when state=2 then money else 0 end) as yxjy from  ".tablename('cjdc_earnings')." where user_id={$value['user_id']}";
$yj=pdo_fetch($sql2);
$sql3="select sum( tx_cost) as money from  ".tablename('cjdc_commission_withdrawal')." where user_id={$value['user_id']} and state in (1,2)";
$tx=pdo_fetch($sql3);
$list[$key]['sj']='总店';
$list[$key]['xjrs']='0';
$list[$key]['djyj']='0.00';
$list[$key]['yxjy']='0.00';
$list[$key]['ejrs']='0';
$list[$key]['tx']='0.00';
if($sj['name']){
  $list[$key]['sj']=$sj['name'];
}
if($xjrs['count']){
  $list[$key]['xjrs']=$xjrs['count'];
}
if($yj['djyj']){
  $list[$key]['djyj']=$yj['djyj'];
}
if($yj['yxjy']){
  $list[$key]['yxjy']=$yj['yxjy'];
}
if($ejrs['count']){
  $list[$key]['ejrs']=$ejrs['count'];
}
if($xjrs['fx_user']){
  $list[$key]['fx_userid']=$xjrs['fx_user'];
}
if($tx['money']){
  $list[$key]['tx']=$tx['money'];
}
}
//var_dump($list);die;
$pager = pagination($total, $pageindex, $pagesize);
$operation=$_GPC['op'];
if($operation=='adopt'){//审核通过
    $id=$_GPC['id'];
    $res=pdo_update('cjdc_retail',array('state'=>2,'sh_time'=>time()),array('id'=>$id));  
    if($res){
        message('审核成功',$this->createWebUrl('fxlist',array()),'success');
    }else{
        message('审核失败','','error');
    }
}
if($operation=='reject'){
     $id=$_GPC['id'];
    $res=pdo_update('cjdc_retail',array('state'=>3,'sh_time'=>time()),array('id'=>$id));
     if($res){
        message('拒绝成功',$this->createWebUrl('fxlist',array()),'success');
    }else{
        message('拒绝失败','','error');
    }
}
if($operation=='delete'){
     $id=$_GPC['id'];
     $user_id=pdo_get('cjdc_retail',array('id'=>$id),'user_id');
     $user_id=$user_id['user_id'];
     $res=pdo_delete('cjdc_retail',array('id'=>$id));
     if($res){
     	 //删除上下级关系
     $sql="delete from ".tablename('cjdc_fxuser')." where fx_user={$user_id} or user_id={$user_id} ";
     pdo_query($sql);
        message('删除成功',$this->createWebUrl('fxlist',array()),'success');
    }else{
        message('删除失败','','error');
    }

}
if(checksubmit('submit')){
  $data2['user_id']=$_GPC['user_id'];
  $data2['money']=$_GPC['money'];
  $data2['time']=time();
  $data2['note']='后台充值';
  $data2['state']=2;
  $data2['uniacid']=$_W['uniacid'];
  $res=pdo_insert('cjdc_earnings',$data2);
  if($res){
      message('充值成功',$this->createWebUrl('fxlist',array()),'success');
  }else{
       message('充值失败','','error');
  }

  



  }

include $this->template('web/fxlist');