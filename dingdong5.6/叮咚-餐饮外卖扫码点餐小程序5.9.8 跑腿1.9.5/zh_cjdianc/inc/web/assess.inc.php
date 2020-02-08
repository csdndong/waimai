<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$pageindex = max(1, intval($_GPC['page']));
$pagesize=10;
$where="where a.uniacid=".$_W['uniacid'];
if($_GPC['type']==2){
  $where=" and a.state=1";
}
$sql="select  a.*,b.name ,b.img as user_img,c.name as  store_name  from " . tablename("cjdc_assess")  . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id  " . " left join " . tablename("cjdc_store") . " c on c.id=a.store_id ".$where." order by a.id DESC";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql);     
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_assess") . " a"  . " left join " . tablename("cjdc_user") . " b on b.id=a.user_id " . " left join " . tablename("cjdc_store") . " c on c.id=a.store_id  ".$where);
$pager = pagination($total, $pageindex, $pagesize);
for($i=0;$i<count($list);$i++){
    if($list[$i]['img']){
    if(strpos($list[$i]['img'],',')){
        $list[$i]['img']= explode(',',$list[$i]['img']);
    }else{
        $list[$i]['img']=array(
          0=>$list[$i]['img']
        );
    }
  }
  }
  if(checksubmit('submit')){
      $data['state']=2;
      $data['hf']=$_GPC['con'];
      $data['hf_time']=date("Y-m-d H:i:s");
      $res=pdo_update('cjdc_assess',$data,array('id'=>$_GPC['id']));
      if($res){
        message('回复成功！', $this->createWebUrl('assess',array('type'=>$_GPC['type'],'page'=>$_GPC['page'])), 'success');
      }else{
        message('回复失败！','','error');
      }
  }
  if($_GPC['op']=='delete'){
    $res=pdo_delete('cjdc_assess',array('id'=>$_GPC['id']));
    if($res){
        message('删除成功！', $this->createWebUrl('assess',array('type'=>$_GPC['type'],'page'=>$_GPC['page'])), 'success');
      }else{
        message('删除失败！','','error');
      }
  }
$qb=pdo_getall('cjdc_assess',array('uniacid'=>$_W['uniacid']));
$whf=pdo_getall('cjdc_assess',array('uniacid'=>$_W['uniacid'],'state'=>1));
include $this->template('web/assess');
