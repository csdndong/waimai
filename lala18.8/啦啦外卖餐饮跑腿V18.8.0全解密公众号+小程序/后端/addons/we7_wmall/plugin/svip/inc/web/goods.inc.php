<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "会员商品";
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : -1;
    $sid = intval($_GPC["sid"]);
    $data = svip_goods_getall();
    $goods = $data["goods"];
    $pager = $data["pager"];
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"));
}
include itemplate("goods");

?>