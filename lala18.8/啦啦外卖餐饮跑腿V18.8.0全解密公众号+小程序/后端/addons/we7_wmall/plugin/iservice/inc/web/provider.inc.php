<?php

defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';

if($op == 'list') {
	$_W['page']['title'] = '服务商列表';
	$condition = " where uniacid = :uniacid";
	$params = array(
		':uniacid' => 0
	);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_iservice_provider') .  $condition, $params);
	$provider = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_iservice_provider') . $condition . ' ORDER BY id DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
	$pager = pagination($total, $pindex, $psize);
}

elseif($op == 'post') {
	$_W['page']['title'] = '编辑服务商';
	$id = intval($_GPC['id']);
	if($id > 0) {
		$provider = pdo_get('tiny_wmall_iservice_provider', array('uniacid' => 0, 'id' => $id));
	}
	if($_W['ispost']) {
		$mobile = trim($_GPC['mobile']);
		if(!is_validMobile($mobile)) {
			imessage(error(-1, '手机号格式错误'), '', 'ajax');
		}
		$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_iservice_provider') . ' where uniacid = :uniacid and mobile = :mobile and id != :id', array(':uniacid' => 0, ':mobile' => $mobile, ':id' => $id));
		if(!empty($is_exist)) {
			imessage(error(-1, '该手机号已绑定其他管理员, 请更换手机号'), '', 'ajax');
		}
		$openid = trim($_GPC['wechat']['openid']);
		if(!empty($openid)) {
			$is_exist = pdo_fetchcolumn('select id from ' . tablename('tiny_wmall_iservice_provider') . ' where uniacid = :uniacid and openid = :openid and id != :id', array(':uniacid' => 0, ':openid' => $openid, ':id' => $id));
			if(!empty($is_exist)) {
				imessage(error(-1, '该微信信息已绑定其他管理员, 请更换微信信息'), '', 'ajax');
			}
		}
		$data = array(
			'uniacid' => 0,
			'title' => trim($_GPC['title']),
			'logo' => trim($_GPC['logo']),
			'telephone' => trim($_GPC['telephone']),
			'description' => trim($_GPC['description']),
			'realname' => trim($_GPC['realname']),
			'nickname' => trim($_GPC['wechat']['nickname']),
			'avatar' => trim($_GPC['wechat']['avatar']),
			'mobile' => $mobile,
			'openid' => $openid,
		);
		if(!$id) {
			$data['password'] = trim($_GPC['password']) ? trim($_GPC['password']) : imessage(error(-1, '登录密码不能为空'), '', 'ajax');
			$length = strlen($data['password']);
			if($length < 8 || $length > 20) {
				imessage(error(-1, '请输入8-20密码'), '', 'ajax');
			}
			if(!preg_match(IREGULAR_PASSWORD, $data['password'])) {
				imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
			}
			if($data['password'] != trim($_GPC['repassword'])) {
				imessage(error(-1, '两次密码输入不一致'), '', 'ajax');
			}
			$data['salt'] = random(6);
			$data['password'] = md5(md5($data['salt'] . $data['password']) . $data['salt']);
			$data['token'] = random(32);
			$data['addtime'] = TIMESTAMP;
			pdo_insert('tiny_wmall_iservice_provider', $data);
			$id = pdo_insertid();
		} else {
			$password = trim($_GPC['password']);
			if(!empty($password)) {
				$length = strlen($password);
				if($length < 8 || $length > 20) {
					imessage(error(-1, '请输入8-20密码'), '', 'ajax');
				}
				if(!preg_match(IREGULAR_PASSWORD, $password)) {
					imessage(error(-1, '密码必须由数字和字母组合'), '', 'ajax');
				}
				if($password != trim($_GPC['repassword'])) {
					imessage(error(-1, '两次密码输入不一致'), '', 'ajax');
				}
				$data['salt'] = random(6);
				$data['password'] = md5(md5($data['salt'].$password) . $data['salt']);
			}
			pdo_update('tiny_wmall_iservice_provider', $data, array('uniacid' => 0, 'id' => $id));
		}
		imessage(error(0, '编辑服务商成功'), iurl('iservice/provider/list', array('id' => $id)), 'ajax');
	}
}

elseif($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_iservice_provider', array('uniacid' => 0, 'id' => $id));
	imessage(error(0, '删除服务商成功'), '', 'ajax');
}
include itemplate('provider');