<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if ($_W["ispost"]) {
    $key = "we7_wmall_manager_session_" . $_W["uniacid"];
    isetcookie($key, "", -100);
    isetcookie("__mg_sid", "", -100);
    imessage(error(0, "清空缓存成功"), "", "ajax");
}

?>