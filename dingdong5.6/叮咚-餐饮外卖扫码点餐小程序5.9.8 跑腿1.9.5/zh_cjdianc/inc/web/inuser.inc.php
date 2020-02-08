<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);

if($_GPC['keywords']){
	$op=$_GPC['keywords'];
	$where="%$op%";	
}else{
	$where='%%';
}
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
  $usersql="select distinct user_id  from " . tablename("cjdc_order") ." WHERE  store_id={$storeid}  order by  id desc";
  $inuser=pdo_fetchall($usersql);
  function array_column2($arr2, $column_key) {
		$data = [];
		foreach ($arr2 as $key => $value) {
			$data[] = $value[$column_key];
		}
		return $data;
}
  if($inuser){
  $inuser=join(",",array_column2($inuser,'user_id'));
	$sql="select *  from " . tablename("cjdc_user") ." WHERE  id in ({$inuser}) and name LIKE :name  and uniacid=:uniacid and name!=''";
	$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list = pdo_fetchall($select_sql,array(':uniacid'=>$_W['uniacid'],':name'=>$where));	   
	$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_user") ." WHERE  id in ({$inuser}) and name LIKE :name  and name!=''",array(':name'=>$where));
	$pager = pagination($total, $pageindex, $pagesize);
for($i=0;$i<count($list);$i++){
  $userorder=pdo_fetchcolumn("select count(*)  from " . tablename("cjdc_order") ." WHERE  store_id={$storeid} and user_id={$list[$i]['id']}  and (state in (4,5,10) || dn_state=2 || dm_state=2 || yy_state=3)");
  $ordertime=pdo_getall('cjdc_order',array('user_id'=>$list[$i]['id'],'store_id'=>$storeid), array(), '', 'id DESC');
  $list[$i]['ordernum']=$userorder;
  $list[$i]['ordertime']=$ordertime[0]['time'];
}
}
include $this->template('web/inuser');