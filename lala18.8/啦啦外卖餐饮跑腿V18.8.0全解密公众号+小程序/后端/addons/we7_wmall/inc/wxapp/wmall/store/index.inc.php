<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(false);
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$id = $sid = intval($_GPC["sid"]);
$store = store_fetch($id);
if (empty($store)) {
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}
if (empty($store["notice"])) {
    $store["notice"] = "本店暂无公告";
}
$activity = store_fetch_activity($sid);
$store["qualification"]["business"]["thumb"] = tomedia($store["qualification"]["business"]["thumb"]);
$store["qualification"]["service"]["thumb"] = tomedia($store["qualification"]["service"]["thumb"]);
$store["qualification"]["more1"]["thumb"] = tomedia($store["qualification"]["more1"]["thumb"]);
$store["qualification"]["more2"]["thumb"] = tomedia($store["qualification"]["more2"]["thumb"]);
$store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
$store["activity"] = $activity;
$result = array("store" => $store, "activity" => $activity);
imessage(error(0, $result), "", "ajax");

?>