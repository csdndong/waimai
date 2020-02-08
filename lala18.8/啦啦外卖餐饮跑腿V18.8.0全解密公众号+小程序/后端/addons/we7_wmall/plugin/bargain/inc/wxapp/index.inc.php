<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
if (!check_plugin_perm("bargain")) {
    imessage(error(-1, "暂未开放此功能"), "", "ajax");
}
$config_bargain = get_plugin_config("bargain");
if ($config_bargain["status"] == 0) {
    imessage(error(-1, "暂未开放此功能"), "", "ajax");
}
$config_bargain["thumb"] = tomedia($config_bargain["thumb"]);
$_W["page"]["title"] = "天天特价";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $condition = " where a.uniacid = :uniacid and a.agentid = :agentid and a.status= 1";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $bargains = pdo_fetchall("select a.discount_price,a.goods_id,a.discount_available_total,b.title,b.thumb,b.price,b.sid,b.sailed,c.title as store_title,c.is_in_business from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id left join" . tablename("tiny_wmall_store") . " as c on b.sid = c.id" . $condition . " order by c.is_in_business desc, a.mall_displayorder desc, a.id desc limit " . ($page - 1) * $psize . " , " . $psize, $params);
    if (!empty($bargains)) {
        foreach ($bargains as &$row) {
            if ($row["discount_available_total"] == -1) {
                $row["discount_available_total"] = "无限";
            }
            $row["thumb"] = tomedia($row["thumb"]);
            $row["discount"] = round($row["discount_price"] / $row["price"] * 10, 1);
        }
    }
    $config_bargain["lazyload_goods"] = tomedia($_W["we7_wmall"]["config"]["mall"]["lazyload_goods"]);
    $respon = array("bargains" => $bargains, "config" => $config_bargain);
    $_W["_share"] = array("title" => $config_bargain["share"]["title"], "desc" => $config_bargain["share"]["desc"], "imgUrl" => tomedia($config_bargain["share"]["imgUrl"]), "link" => !empty($config_bargain["share"]["desc"]) ? $config_bargain["share"]["desc"] : ivurl("/plugin/pages/bargain/index", array(), true));
    imessage(error(0, $respon), "", "ajax");
}

?>