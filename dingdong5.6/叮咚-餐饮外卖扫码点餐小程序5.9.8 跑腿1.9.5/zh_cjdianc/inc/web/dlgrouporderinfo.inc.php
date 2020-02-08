<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$sql=" select a.*,b.name as nick_name from".tablename('cjdc_grouporder')." a left join ".tablename('cjdc_user')." b on a.user_id=b.id where a.store_id=:store_id and a.id=:id";
$data[':store_id']=$storeid;
$data[':id']=$_GPC['id'];
$item=pdo_fetch($sql,$data);

include $this->template('web/dlgrouporderinfo');