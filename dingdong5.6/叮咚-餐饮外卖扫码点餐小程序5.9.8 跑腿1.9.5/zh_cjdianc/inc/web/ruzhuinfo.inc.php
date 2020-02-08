<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu();
$info=pdo_get('wpdc_ruzhu',array('id'=>$_GPC['id']));
include $this->template('web/ruzhuinfo');