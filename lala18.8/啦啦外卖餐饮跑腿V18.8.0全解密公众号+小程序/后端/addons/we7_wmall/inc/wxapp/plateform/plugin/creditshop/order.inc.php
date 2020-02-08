<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "order";
if ($ta == "order") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.uid = :uid or b.nickname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $records = pdo_fetchall("select a.*,b.avatar,b.nickname,c.title,c.thumb from " . tablename("tiny_wmall_creditshop_order_new") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid left join " . tablename("tiny_wmall_creditshop_goods") . " as c on a.goods_id = c.id " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($records)) {
        foreach ($records as &$val) {
            $val["avatar"] = tomedia($val["avatar"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
        }
    }
    $result = array("records" => $records);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        mload()->model("plugin");
        pload()->model("creditshop");
        $order = creditshop_order_get($id);
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已删除"), "", "ajax");
        }
        $result = array("order" => $order);
        imessage(error(0, $result), "", "ajax");
    } else {
    if ($ta == "handle") {
        mload()->model("plugin");
        pload()->model("creditshop");
        $id = intval($_GPC["id"]);
        if (!empty($id)) {
            creditshop_order_update($id, "handle");
        }
        imessage(error(0, ""), "", "ajax");
        } else {
            if ($ta == "confirm") {
                $code = trim($_GPC["code"]);
                $order = pdo_get("tiny_wmall_creditshop_order_new", array("uniacid" => $_W["uniacid"], "code" => $code));
                mload()->model("plugin");
                pload()->model("creditshop");
                $status = creditshop_order_update($order, "handle");
                if (is_error($status)) {
                    imessage($status, "", "ajax");
                }
                imessage(error(0, "核销成功"), "", "ajax");
            }
        }
    }
}

?>