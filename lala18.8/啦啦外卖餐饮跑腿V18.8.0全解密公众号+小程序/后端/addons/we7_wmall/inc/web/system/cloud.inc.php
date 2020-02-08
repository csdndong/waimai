<?php
defined('IN_IA') || exit('Access Denied');
mload()->model('cloud');
global $_W;
global $_GPC;
$op = (trim($_GPC['op']) ? trim($_GPC['op']) : 'auth');
if ('auth' === $op) {
    $_W['page']['title'] = '授权管理';
    define('HTTP_X_FOR', (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://');	
		$auth = array();
		$auth['password'] = '';
		$auth['modname'] = 'we7_wmall';
		$auth['modnamemm'] = '啦啦外卖';
		$auth['url'] = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
		$auth['forward'] = 'profile';
		$query = base64_encode(json_encode($auth));
		$auth_url = HTTP_X_FOR .base64_decode('dXB1cC45OTY5Ni50b3AvYXBpL2F1dGhfbW9kLnBocD9fX2F1dGg9') . $query;
}

if ('upgrade' === $op) {
    $_W['page']['title'] = '系统更新';
    $auth_url = '/web/index.php?c=mod&a=mod_wmall&';
}

include itemplate('system/cloud');

?>
