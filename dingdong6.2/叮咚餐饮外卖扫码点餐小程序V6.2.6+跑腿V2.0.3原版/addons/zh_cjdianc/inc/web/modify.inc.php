<?php
global $_GPC, $_W;
$storeid=$_COOKIE["storeid"];
$uid=$_COOKIE["uid"];
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action='start',$uid);
$cur_store = $this->getStoreById($storeid);
$users=pdo_get('users',array('uid'=>$uid));
 if (checksubmit('submit')) {

        load()->model('user');
        $user = array();
        $user['username'] = trim($_GPC['username']);
        if (!preg_match(REGULAR_USERNAME, $user['username'])) {
            message('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。');
        }
        if (empty($users)) {
            $user['password'] = $_GPC['password'];
            if (istrlen($user['password']) < 8) {
                message('必须输入密码，且密码长度不得低于8位。');
            }
        }
        if (!empty($_GPC['password'])) {
            $user['password'] = $_GPC['password'];
            if (istrlen($user['password']) < 8) {
                message('必须输入密码，且密码长度不得低于8位。');
            }
        }
        if (!empty($users)) {
            $user['salt'] = $users['salt'];
            $user['uid'] = $uid;
        }
        $user['remark'] = $_GPC['remark'];
        $user['status'] = $_GPC['status'];
        $user['groupid'] = -1;
       // var_dump($users);die;
           $rst= user_update($user);
            pdo_update('cjdc_account', array(
                'weid' => $_W['uniacid'],
                'storeid' => intval($_GPC['storeid']),
                'from_user' => trim($_GPC['from_user']),
                'email' => trim($_GPC['email']),
                'mobile' => trim($_GPC['mobile']),
                'pay_account' => trim($_GPC['pay_account']),
                'status' => intval($_GPC['status']),
                'remark' => trim($_GPC['remark']),
                'dateline' => TIMESTAMP,
                'role' => 1,
                'username' => trim($_GPC['truename']),
                'is_admin_order' => intval($_GPC['is_admin_order']),
                'is_notice_order' => intval($_GPC['is_notice_order']),
                'is_notice_service' => intval($_GPC['is_notice_service']),
                'is_notice_boss' => intval($_GPC['is_notice_boss']),
                'is_notice_queue' => intval($_GPC['is_notice_queue']),
            ), array('id' => $id));
            message('更新成功！', $this->createWebUrl2('modify', array(), true));

        message('操作用户失败，请稍候重试或联系网站管理员解决！');
   }

 

include $this->template('web/modify');