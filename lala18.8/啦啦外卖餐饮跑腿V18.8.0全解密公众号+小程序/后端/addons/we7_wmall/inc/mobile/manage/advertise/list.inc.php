<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "店铺推广";
    $conditions = "where uniacid = :uniacid and sid = :sid and status = :status";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $params[":status"] = $status;
    $advertises = pdo_fetchall("select * from" . tablename("tiny_wmall_advertise_trade") . $conditions, $params);
    foreach ($advertises as &$advertise) {
        if ($advertise["status"] == 1) {
            $advertise["until"] = round(($advertise["endtime"] - TIMESTAMP) / 86400);
        } else {
            $advertise["until"] = -1;
        }
    }
    $type_cn = array("recommendHome" => "为您优选首页", "recommendOther" => "为您优选更多页", "stick" => "商家置顶", "slideMember" => "会员中心-幻灯片", "slideHomeTop" => "平台首页-幻灯片", "slidePaycenter" => "收银台-幻灯片", "slideOrderDetail" => "订单详情-幻灯片");
}
include itemplate("advertise/list");

?>