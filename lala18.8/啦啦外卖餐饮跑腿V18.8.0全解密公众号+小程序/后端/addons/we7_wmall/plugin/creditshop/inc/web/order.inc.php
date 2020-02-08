<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "积分商城兑换记录";
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $goods_type = trim($_GPC["goods_type"]);
    if (!empty($goods_type)) {
        $condition .= " and a.goods_type = :goods_type";
        $params[":goods_type"] = $goods_type;
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $today = strtotime(date("Y-m-d"));
        $starttime = strtotime("-15 day", $today);
        $endtime = $today + 86399;
    }
    $condition .= " and a.addtime >= :starttime and a.addtime <= :endtime";
    $params[":starttime"] = $starttime;
    $params[":endtime"] = $endtime;
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.username like '%" . $keyword . "%' or a.mobile like '%" . $keyword . "%' or b.nickname like '%" . $keyword . "%')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_creditshop_order_new") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid left join " . tablename("tiny_wmall_creditshop_goods") . " as c on a.goods_id = c.id " . $condition, $params);
    $orders = pdo_fetchall("select a.*,b.avatar,b.nickname,c.title,c.thumb from " . tablename("tiny_wmall_creditshop_order_new") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid left join " . tablename("tiny_wmall_creditshop_goods") . " as c on a.goods_id = c.id " . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pay_types = order_pay_types();
    $pager = pagination($total, $pindex, $psize);
    include itemplate("order");
}
if ($op == "handle") {
    $ids = $_GPC["id"];
    if (!empty($ids)) {
        foreach ($ids as $value) {
            creditshop_order_update($value, "handle");
        }
    }
    imessage(error(0, "确认订单状态成功"), iurl("creditshop/order/list"), "ajax");
}

?>