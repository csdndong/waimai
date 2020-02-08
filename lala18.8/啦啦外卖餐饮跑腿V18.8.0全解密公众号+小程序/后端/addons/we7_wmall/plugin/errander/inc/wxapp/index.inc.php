<?php

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(false);
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$_W["page"]["title"] = "随意购";
if (!$_config_plugin["status"]) {
    imessage(error(-1, "平台暂未开启跑腿功能"), "", "ajax");
}
$_W["_share"] = get_errander_share();
if ($op == "index") {
    $categorys = pdo_fetchall("select id, name, thumb from " . tablename("tiny_wmall_errander_page") . " where uniacid = :uniacid and agentid = :agentid and type = :type", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":type" => "scene"));
    $orders = pdo_fetchall("select a.anonymous_username,a.goods_name,a.order_cid, b.name, b.thumb from " . tablename("tiny_wmall_errander_order") . " as a left join " . tablename("tiny_wmall_errander_page") . " as b on a.order_cid = b.id where a.uniacid = :uniacid and a.agentid = :agentid order by a.id desc limit 5", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    $delivery_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_deliveryer") . " where uniacid = :uniacid and agentid = :agentid and is_errander = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    if (!empty($categorys)) {
        foreach ($categorys as &$val) {
            $val["thumb"] = empty($val["thumb"]) ? "" : tomedia($val["thumb"]);
        }
    }
    if (!empty($orders)) {
        foreach ($orders as &$val) {
            $val["thumb"] = empty($val["thumb"]) ? "" : tomedia($val["thumb"]);
        }
    }
    $deliveryers = deliveryer_fetchall(0, array("work_status" => 1, "order_type" => "is_errander"));
    $result = array("config" => $_config_plugin, "categorys" => $categorys, "orders" => $orders, "delivery_num" => $delivery_num, "deliveryers" => array_values($deliveryers));
    imessage(error(0, $result), "", "ajax");
}

?>