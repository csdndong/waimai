<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where uniacid = :uniacid and deliveryer_id = :deliveryer_id";
    $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_deliveryer["id"]);
    $comment_type = trim($_GPC["comment_type"]) ? trim($_GPC["comment_type"]) : "all";
    if ($comment_type == "good") {
        $condition .= " and delivery_service >= 3";
    } else {
        if ($comment_type == "bad") {
            $condition .= " and delivery_service < 3";
        }
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $totalComment = floatval(pdo_fetchcolumn("select round(avg(delivery_service), 1) from " . tablename("tiny_wmall_order_comment") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $_deliveryer["id"])));
    $records = pdo_fetchall("SELECT id, delivery_service, deliveryer_tag, note, addtime FROM " . tablename("tiny_wmall_order_comment") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["delivery_service"] = intval($val["delivery_service"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["delivery_service_cn"] = 3 <= $val["delivery_service"] ? "满意" : "不满意";
            if (!empty($val["deliveryer_tag"])) {
                $val["deliveryer_tag"] = explode(",", $val["deliveryer_tag"]);
            } else {
                $val["deliveryer_tag"] = array();
            }
        }
    }
    $result = array("records" => $records, "totalComment" => $totalComment);
    imessage(error(0, $result), "", "ajax");
}

?>