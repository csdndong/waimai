<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$item=pdo_get('wpdc_ydorder',array('id'=>$_GPC['id']));
include $this->template('web/1ydorderinfo');