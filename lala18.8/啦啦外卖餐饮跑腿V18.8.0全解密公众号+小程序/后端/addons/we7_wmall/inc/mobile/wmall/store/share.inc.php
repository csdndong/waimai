<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
$_W["page"]["title"] = $store["title"];
if (empty($store)) {
    imessage("门店不存在或已删除", "close", "info");
}
$activity = store_fetch_activity($sid);
$hot_goods = pdo_fetchall("select id,title,price,sailed,thumb from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid order by is_hot desc, id desc limit 6", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
include itemplate("store/share");

?>