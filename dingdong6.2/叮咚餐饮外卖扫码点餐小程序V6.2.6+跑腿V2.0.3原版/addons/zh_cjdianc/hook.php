<?php
/**
 * 微商城公告模块插件定义
 *
 * @author 微擎团队
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Zh_cjdianc_plugin_jfshopModule extends WeModuleHook {
	public function hookMobileNotice() {
		global $_W;

	echo 123;die;
		$notice = pdo_getcolumn('shopping_notice', array('uniacid' => $_W['uniacid']), 'content');
		include $this->template('notice');
	}
}