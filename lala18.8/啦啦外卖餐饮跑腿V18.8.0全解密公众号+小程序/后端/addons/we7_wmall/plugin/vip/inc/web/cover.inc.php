<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('cover');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';

$routers = array(
	'index' => array(
		'title' => '平台入口',
		'url' => ivurl('pages/vip/index', array(), true),
		'do' => 'index',
	),
);
$_W['page']['title'] = $routers['index']['title'];
$cover = $routers['index'];
include itemplate('cover');
?>