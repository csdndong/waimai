<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$item=pdo_get('cjdc_order',array('id'=>$_GPC['id']));
include $this->template('web/inydorderinfo');