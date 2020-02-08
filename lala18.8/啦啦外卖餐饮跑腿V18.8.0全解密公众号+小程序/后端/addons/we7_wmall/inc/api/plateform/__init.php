<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("plateform");
global $_W;
global $_GPC;
if (!in_array($_W["_op"], array("login"))) {
    $token = trim($_GPC["token"]);
    if (empty($token)) {
        message(ierror(-1, "身份验证失败, 请重新登录"), "", "ajax");
    }
}

?>