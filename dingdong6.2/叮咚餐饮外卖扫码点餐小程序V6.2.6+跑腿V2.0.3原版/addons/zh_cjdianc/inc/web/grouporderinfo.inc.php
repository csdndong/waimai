<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$sql=" select a.*,b.name as nick_name from".tablename('cjdc_grouporder')." a left join ".tablename('cjdc_user')." b on a.user_id=b.id where a.store_id=:store_id and a.id=:id";
$data[':store_id']=$storeid;
$data[':id']=$_GPC['id'];
$item=pdo_fetch($sql,$data);
//$item=pdo_get('cjdc_grouporder',array('id'=>$_GPC['id']));
//var_dump($item);die;
$goods=pdo_getall('cjdc_order_goods',array('order_id'=>$_GPC['id']));
if(checksubmit('submit')){
  // $data['state']=$_GPC['state'];
  $data['money']=$_GPC['money'];
  $data['preferential']=$_GPC['preferential'];
  // if($_GPC['dn_state']=="2"){
  //  $data['pay_time']=time();
  // }
  $res=pdo_update('wpdc_order',$data,array('id'=>$_GPC['id']));
  if($res){
             message('编辑成功！', $this->createWebUrl('inorder'), 'success');
        }else{
             message('编辑失败！','','error');
        }
}
include $this->template('web/grouporderinfo');