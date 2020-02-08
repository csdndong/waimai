<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $conditions = "where uniacid = :uniacid and sid = :sid and status = :status";
    $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
    $params[":status"] = $status;
    $advertises = pdo_fetchall("select * from" . tablename("tiny_wmall_advertise_trade") . $conditions, $params);
    $type_cn = array("recommendHome" => "为您优选首页", "recommendOther" => "为您优选更多页", "stick" => "商家置顶", "slideMember" => "会员中心-幻灯片", "slideHomeTop" => "平台首页-幻灯片", "slidePaycenter" => "收银台-幻灯片", "slideOrderDetail" => "订单详情-幻灯片");
    foreach ($advertises as &$advertise) {
        $advertise["type_cn"] = $type_cn[$advertise["type"]];
        if ($advertise["status"] == 1) {
            $advertise["until"] = round(($advertise["endtime"] - TIMESTAMP) / 86400);
        } else {
            $advertise["until"] = -1;
        }
        $advertise["starttime_cn"] = date("Y-m-d H:i", $advertise["starttime"]);
        $advertise["endtime_cn"] = date("Y-m-d H:i", $advertise["endtime"]);
        $advertise["addtime_cn"] = date("Y-m-d H:i", $advertise["addtime"]);
    }
    $result = array("advertise" => $advertises);
    imessage(error(0, $result), "", "ajax");
}

?>