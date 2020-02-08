<?php
/*



 * @ 请勿传播
 */

defined("IN_IA") or exit("Access Denied");
pload()->model("gohome");
function seckill_cron()
{
    global $_W;
    $key = "we7_wmall:" . $_W["uniacid"] . ":seckill:lock:120";
    if (check_cache_status($key, 120)) {
        return true;
    }
    gohome_goods_sync("seckill");
    set_cache($key, array());
    return true;
}
function seckill_slides()
{
    global $_W;
    $slides = pdo_fetchall("select * from " . tablename("tiny_wmall_seckill_slide") . " where uniacid = :uniacid and status = :status order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":status" => 1));
    if (!empty($slides)) {
        foreach ($slides as &$slide) {
            $slide["thumb"] = tomedia($slide["thumb"]);
        }
    }
    return $slides;
}
function seckill_goods_cate($id)
{
    global $_W;
    $category = pdo_get("tiny_wmall_seckill_goods_category", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($category)) {
        $category["thumb"] = tomedia($category["thumb"]);
    }
    return $category;
}
function seckill_goods_categorys()
{
    global $_W;
    $categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_seckill_goods_category") . " where uniacid = :uniacid and agentid = :agentid order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
    if (!empty($categorys)) {
        foreach ($categorys as &$cate) {
            $cate["thumb"] = tomedia($cate["thumb"]);
            if (empty($cate["link"])) {
                $cate["link"] = "/gohome/pages/seckill/category?cid=" . $cate["id"];
            }
        }
    }
    return $categorys;
}
function seckill_allgoods($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid and a.agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    if (!empty($filter["cid"])) {
        $condition .= " and a.cid = :cid";
        $params[":cid"] = intval($filter["cid"]);
    }
    if (0 < $filter["sid"]) {
        $condition .= " and a.sid = :sid";
        $params[":sid"] = intval($filter["sid"]);
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : "-1";
    if (-1 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    } else {
        $condition .= " and a.status > 0";
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and a.name like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = intval($_GPC["page"]);
    if (!empty($filter["page"])) {
        $page = intval($filter["page"]);
    }
    if (!empty($filter["ids"]) && is_array($filter["ids"])) {
        $ids_str = implode(",", $filter["ids"]);
        $condition .= " and a.id in (" . $ids_str . ")";
    }
    $psize = intval($_GPC["psize"]);
    if (!empty($filter["psize"])) {
        $psize = intval($filter["psize"]);
    }
    $pindex = max(1, $page);
    $psize = $psize ? $psize : 15;
    $condition .= " order by status asc, displayorder desc limit " . ($pindex - 1) * $psize . " , " . $psize;
    $goods = pdo_fetchall("select a.*,b.title as store_title from " . tablename("tiny_wmall_seckill_goods") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id " . $condition, $params);
    if (!empty($goods)) {
        foreach ($goods as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            if (0 < $val["oldprice"]) {
                $val["discount"] = round($val["price"] / $val["oldprice"] * 10, 1);
            } else {
                $val["discount"] = 10;
            }
            if ($val["status"] == 1 && $val["endtime"] < TIMESTAMP) {
                $val["status"] = 3;
            }
            $val["falesailed_total"] = $val["sailed"] + $val["falsejoinnum"];
            if ($val["total"] != -1) {
                $orgin_total = $val["sailed"] + $val["total"];
                $val["sailed_percent"] = $orgin_total < $val["falesailed_total"] ? round($val["sailed"] / $orgin_total * 100, 1) : round($val["falesailed_total"] / $orgin_total * 100, 1);
            }
        }
    }
    return $goods;
}
function seckill_goods($id, $type = "")
{
    global $_W;
    $goods = pdo_get("tiny_wmall_seckill_goods", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($goods)) {
        $update = array();
        if (TIMESTAMP < $goods["starttime"] && $goods["status"] != 2) {
            $update = array("status" => 2);
        } else {
            if ($goods["endtime"] <= TIMESTAMP && $goods["status"] != 3) {
                $update = array("status" => 3);
            }
        }
        if (!empty($update)) {
            pdo_update("tiny_wmall_seckill_goods", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
            $goods["status"] = $update["status"];
        }
        $goods["thumb"] = tomedia($goods["thumb"]);
        $goods["sailed_percent"] = $goods["sailed"] / ($goods["total"] + $goods["sailed"]) * 100;
        $goods["starttime_cn"] = date("Y-m-d H:i:s", $goods["starttime"]);
        $goods["endtime_cn"] = date("Y-m-d H:i:s", $goods["endtime"]);
        $goods["endtime"] = $goods["endtime"] + 0;
        $goods["total_looknum"] = $goods["falselooknum"] + $goods["looknum"];
        $goods["total_sharenum"] = $goods["falsesharenum"] + $goods["sharenum"];
        $goods["sailed"] = $goods["falsejoinnum"] + $goods["sailed"];
        if (!empty($goods["thumbs"])) {
            $goods["thumbs"] = iunserializer($goods["thumbs"]);
            foreach ($goods["thumbs"] as &$thumb) {
                $thumb = tomedia($thumb);
            }
        } else {
            $goods["thumbs"] = array();
        }
        $goods["share"] = iunserializer($goods["share"]);
        if ($type == "all") {
            $goods["is_favor"] = gohome_goods_favorite_check($goods["id"], "seckill");
            $store = store_fetch($goods["sid"], array("id", "agentid", "title", "telephone", "address", "location_x", "location_y", "forward_mode", "forward_url"));
            if (!empty($store)) {
                $store["url"] = store_forward_url($store["id"], $store["forward_mode"], $store["forward_url"]);
            }
            $goods["store"] = $store;
        }
    }
    return $goods;
}
function seckill_order_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = gohome_order_fetch($order);
    }
    if (empty($order)) {
        return error(-1, "订单不存在！");
    }
    if ($type == "pay") {
        if ($order["is_pay"] == 1) {
            return error(-1, "订单已支付，请勿重复支付");
        }
        $update = array("is_pay" => 1, "status" => 3, "order_channel" => $extra["channel"], "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP, "transaction_id" => $extra["transaction_id"], "out_trade_no" => $extra["uniontid"]);
        pdo_update("tiny_wmall_gohome_order", $update, array("id" => $order["id"]));
        pdo_query("update " . tablename("tiny_wmall_seckill_goods") . " set sailed = sailed + " . $order["num"] . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $order["goods_id"]));
        gohome_goods_total_update($order, 1);
        gohome_order_clerk_notice($order["id"], "pay");
        gohome_order_print($order["id"]);
        return error(0, "支付成功");
    }
    if ($type == "status") {
        if ($order["is_pay"] == 1 && 2 < $extra["status"] && $extra["status"] != 5) {
            pdo_update("tiny_wmall_gohome_order", array("status" => $extra["status"]), array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            return error(0, "状态更新成功");
        }
    } else {
        if ($type == "confirm") {
            if ($order["is_pay"] != 1 || $order["status"] == 1) {
                return error(-1, "该订单未支付");
            }
            if (4 < $order["status"]) {
                return error(-1, "该订单已核销或已取消");
            }
            pdo_update("tiny_wmall_gohome_order", array("status" => 5, "applytime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            if ($order["is_pay"] == 1) {
                if (in_array($order["pay_type"], array("wechat", "alipay", "credit", "peerpay", "qianfan", "majia", "eleme", "meituan"))) {
                    mload()->model("store");
                    store_update_account($order["sid"], $order["store_final_fee"], 8, $order["id"]);
                }
                if (0 < $order["agentid"]) {
                    $remark = "";
                    mload()->model("agent");
                    agent_update_account($order["agentid"], $order["agent_final_fee"], 8, $order["id"], $remark, "gohome");
                }
            }
            if (check_plugin_perm("spread")) {
                pload()->model("spread");
                spread_order_balance($order["id"], "gohome");
            }
            $credit1_config = get_plugin_config("gohome.credit.credit1");
            if (!empty($credit1_config) && $credit1_config["status"] == 1 && 0 < $credit1_config["grant_num"] && 0 < $order["uid"]) {
                $credit1 = $credit1_config["grant_num"];
                if ($credit1_config["grant_type"] == 2) {
                    $credit1 = round($order["final_fee"] * $credit1_config["grant_num"], 2);
                }
                if (0 < $credit1) {
                    mload()->model("member");
                    $result = member_credit_update($order["uid"], "credit1", $credit1, array(0, "生活圈抢购订单完成，赠：" . $credit1 . "积分"));
                    if (is_error($result)) {
                        slog("credit1Update", "生活圈抢购下单送积分-order_id:" . $order["id"], array("order_id" => $order["id"], "uid" => $order["uid"], "credit_type" => "credit1"), $result["message"]);
                    }
                }
            }
            gohome_order_clerk_notice($order["id"], "confirm");
            return error(0, "核销成功");
        }
        if ($type == "cancel") {
            if ($order["status"] == 5) {
                return error(-1, "订单处于待评价状态, 无法取消订单");
            }
            if ($order["status"] == 6) {
                return error(-1, "订单已完成, 无法取消订单");
            }
            if ($order["status"] == 7) {
                return error(-1, "订单已取消, 无法取消订单");
            }
            if ($order["is_pay"] == 0) {
                pdo_update("tiny_wmall_gohome_order", array("status" => 7), array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
                return error(0, "取消成功");
            }
            $update = array("status" => 7, "refund_status" => 1, "refund_out_no" => date("YmdHis") . random(10, true), "refund_apply_time" => TIMESTAMP);
            pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            $refund_result = gohome_order_begin_refund($order["id"]);
            if (is_error($refund_result)) {
                gohome_order_refund_notice($order["id"], "fail", "失败原因: " . $refund_result["message"]);
            } else {
                gohome_order_refund_notice($order["id"], "success");
                gohome_order_status_notice($order, "cancel");
            }
            return error(0, array("is_refund" => 1, "refund_message" => $refund_result["message"], "refund_code" => $refund_result["errno"]));
        }
        if ($type == "end") {
            if ($order["status"] == 1) {
                return error(-1, "该订单未支付");
            }
            if ($order["status"] == 2) {
                return error(-1, "该订单待生效");
            }
            if ($order["status"] == 5) {
                return error(-1, "该订单已核销，待评价");
            }
            if ($order["status"] == 6) {
                return error(-1, "该订单已完成");
            }
            if ($order["status"] == 7) {
                return error(-1, "该订单已取消");
            }
            pdo_update("tiny_wmall_gohome_order", array("status" => 5), array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
            if ($order["is_pay"] == 1) {
                if (in_array($order["pay_type"], array("wechat", "alipay", "credit", "peerpay", "qianfan", "majia", "eleme", "meituan"))) {
                    mload()->model("store");
                    store_update_account($order["sid"], $order["store_final_fee"], 8, $order["id"]);
                }
                if (0 < $order["agentid"]) {
                    $remark = "";
                    mload()->model("agent");
                    agent_update_account($order["agentid"], $order["agent_final_fee"], 8, $order["id"], $remark, "gohome");
                }
            }
            if (check_plugin_perm("spread")) {
                pload()->model("spread");
                spread_order_balance($order["id"], "gohome");
            }
            $credit1_config = get_plugin_config("gohome.credit.credit1");
            if (!empty($credit1_config) && $credit1_config["status"] == 1 && 0 < $credit1_config["grant_num"] && 0 < $order["uid"]) {
                $credit1 = $credit1_config["grant_num"];
                if ($credit1_config["grant_type"] == 2) {
                    $credit1 = round($order["final_fee"] * $credit1_config["grant_num"], 2);
                }
                if (0 < $credit1) {
                    mload()->model("member");
                    $result = member_credit_update($order["uid"], "credit1", $credit1, array(0, "生活圈抢购订单完成，赠：" . $credit1 . "积分"));
                    if (is_error($result)) {
                        slog("credit1Update", "生活圈抢购下单送积分"order_id:" . $order["id"], array("order_id" => $order["id"], "uid" => $order["uid"], "credit_type" => "credit1"), $result["message"]);
                    }
                }
            }
            return error(0, "订单成功设为已处理");
        }
    }
}

?>