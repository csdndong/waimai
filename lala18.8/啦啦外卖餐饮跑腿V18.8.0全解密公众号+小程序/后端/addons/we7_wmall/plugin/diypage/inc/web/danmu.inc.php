<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "订单弹幕设置";
if ($_W["ispost"]) {
    $danmu = $_GPC["danmu"];
    set_plugin_config("diypage.danmu", $danmu);
    imessage(error(0, "下单弹幕设置成功"), "refresh", "ajax");
}
$config_danmu = get_plugin_config("diypage.danmu");
include itemplate("danmu");

?>