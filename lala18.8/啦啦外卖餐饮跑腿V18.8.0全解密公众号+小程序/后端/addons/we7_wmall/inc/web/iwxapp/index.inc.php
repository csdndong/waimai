<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if (!check_plugin_exist("wxapp")) {
    imessage("注意：小程序目前仅对购买过\"小程序\"插件的客户开放。", referer(), "info");
}
header("location:" . iurl("wxapp/config"));
exit;

?>