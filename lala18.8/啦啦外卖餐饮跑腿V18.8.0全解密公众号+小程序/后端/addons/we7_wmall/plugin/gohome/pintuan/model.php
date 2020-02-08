<?php
defined("IN_IA") or exit("Access Denied");
pload()->model("gohome");
function pintuan_get_activitylist($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    if (empty($filter["page"])) {
        $filter["page"] = $_GPC["page"];
    }
    if (empty($filter["psize"])) {
        $filter["psize"] = $_GPC["psize"];
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and name like :keyword";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $sid = intval($filter["sid"]);
    if (0 < $sid) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }
    $cateid = intval($filter["cateid"]);
    if (0 < $cateid) {
        $condition .= " and cateid = :cateid";
        $params[":cateid"] = $cateid;
    }
    if (!empty($filter["ids"]) && is_array($filter["ids"])) {
        $ids_str = implode(",", $filter["ids"]);
        $condition .= " and id in (" . $ids_str . ")";
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    $orderby = " order by displayorder desc, id desc";
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    } else {
        $condition .= " and status > 0";
        $orderby = " order by status asc, displayorder desc, id desc";
    }
    $page = max(1, intval($filter["page"]));
    $psize = intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $goods = pdo_fetchall("select * from " . tablename("tiny_wmall_pintuan_goods") . $condition . $orderby . " LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($goods)) {
        $store = store_fetchall(array("id", "title"));
        foreach ($goods as &$da) {
            $da["thumb"] = tomedia($da["thumb"]);
            $da["store"] = $store[$da["sid"]];
            if (0 < $da["oldprice"]) {
                $da["discount"] = number_format($da["price"] / $da["oldprice"] * 10, 1);
            } else {
                $da["discount"] = 10;
            }
            $da["falesailed_total"] = $da["sailed"] + $da["falesailed"];
            if ($da["total"] != -1) {
                $orgin_total = $da["sailed"] + $da["total"];
                $da["sailed_percent"] = $orgin_total < $da["falesailed_total"] ? round($da["sailed"] / $orgin_total * 100, 1) : round($da["falesailed_total"] / $orgin_total * 100, 1);
            }
        }
    }
    return $goods;
}
function pintuan_get_activity($id)
{
    global $_W;
    $activity = pdo_get("tiny_wmall_pintuan_goods", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (!empty($activity)) {
        $update = array();
        if (TIMESTAMP < $activity["starttime"] && $activity["status"] != 2) {
            $update = array("status" => 2);
        } else {
            if ($activity["endtime"] <= TIMESTAMP && $activity["status"] != 3) {
                $update = array("status" => 3);
            }
        }
        if (!empty($update)) {
            pdo_update("tiny_wmall_pintuan_goods", $update, array("uniacid" => $_W["uniacid"], "id" => $id));
            $activity["status"] = $update["status"];
        }
        $activity["thumb"] = tomedia($activity["thumb"]);
        $activity["thumbs"] = iunserializer($activity["thumbs"]);
        if (!empty($activity["thumbs"])) {
            foreach ($activity["thumbs"] as &$thumb) {
                $thumb = tomedia($thumb);
            }
        }
        $activity["falesailed_total"] = $activity["sailed"] + $activity["falesailed"];
        if ($activity["total"] != -1) {
            $orgin_total = $activity["sailed"] + $activity["total"];
            $activity["sailed_percent"] = $orgin_total < $activity["sailed"] + $activity["falesailed"] ? round($activity["sailed"] / $orgin_total * 100, 1) : round(($activity["sailed"] + $activity["falesailed"]) / $orgin_total * 100, 1);
        }
        $activity["discount"] = number_format($activity["price"] / $activity["oldprice"] * 10, 1);
        $activity["total_looknum"] = $activity["falselooknum"] + $activity["looknum"];
        $activity["total_sharenum"] = $activity["falsesharenum"] + $activity["sharenum"];
        $activity["share"] = iunserializer($activity["share"]);
        $activity["is_favor"] = gohome_goods_favorite_check($activity["id"], "pintuan");
        $activity["starttime_cn"] = date("Y-m-d H:i:s", $activity["starttime"]);
        $activity["endtime_cn"] = date("Y-m-d H:i:s", $activity["endtime"]);
    }
    return $activity;
}
function pintuan_get_same_list($goods_id = 0, $extra = array())
{
    global $_W;
    $condition = " where a.uniacid = :uniacid and a.order_type = :order_type";
    $params = array(":uniacid" => $_W["uniacid"], ":order_type" => "pintuan");
    $goods_id = intval($goods_id);
    if (0 < $goods_id) {
        $condition .= " and a.goods_id = :goods_id";
        $params[":goods_id"] = $goods_id;
    }
    if (!empty($extra["uid"])) {
        $condition .= " and a.uid = :uid";
        $params[":uid"] = intval($extra["uid"]);
    }
    if (!empty($extra["team_id"])) {
        $condition .= " and a.team_id = :team_id";
        $params[":team_id"] = intval($extra["team_id"]);
    }
    if (!empty($extra["is_team"])) {
        $condition .= " and a.is_team = :is_team and a.team_id = a.id";
        $params[":is_team"] = 1;
    }
    if (0 < $extra["status"]) {
        $condition .= " and a.status = :status";
        $params[":status"] = intval($extra["status"]);
    }
    $list = pdo_fetchall("select a.*, b.nickname, b.avatar from " . tablename("tiny_wmall_gohome_order") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition, $params);
    if (!empty($list)) {
        foreach ($list as &$val) {
            $val["overtime_cn"] = date("Y-m-d H:i:s", $val["overtime"]);
        }
    }
    $result = array("list" => $list);
    return $result;
}
function pintuan_order_calculate($goodsOrId, $condition, $extra = array())
{
    global $_W;
    $pintuan_goods = $goodsOrId;
    if (!is_array($pintuan_goods)) {
        $pintuan_goods = pintuan_get_activity($pintuan_goods);
    }
    $store = store_fetch($pintuan_goods["sid"]);
    if (empty($condition["is_team"])) {
        $pintuan_goods["price"] = $pintuan_goods["aloneprice"];
    }
    if ($pintuan_goods["usetype"] == 1) {
        $address = array("address" => $store["address"]);
    } else {
        if ($pintuan_goods["usetype"] == 2 && !empty($condition)) {
            $address_id = intval($condition["address_id"]);
            if (0 < $address_id) {
                $address = member_takeout_address_check($store, $address_id);
            }
        }
    }
    $goods_num = 1;
    if (1 < $pintuan_goods["buylimit"] || !$pintuan_goods["buylimit"]) {
        $goods_num = intval($condition["goods_num"]) ? intval($condition["goods_num"]) : 1;
        if (1 < $pintuan_goods["buylimit"] && $pintuan_goods["buylimit"] < $goods_num) {
            $goods_num = $pintuan_goods["buylimit"];
        }
    }
    $order = array("goods_num" => $goods_num, "address" => $address, "goods_price" => round($goods_num * $pintuan_goods["price"], 2), "discount" => 0 < $pintuan_goods["oldprice"] - $pintuan_goods["price"] ? $goods_num * ($pintuan_goods["oldprice"] - $pintuan_goods["price"]) : 0, "discount_real" => 0, "remark" => trim($condition["remark"]), "store" => $store, "username" => empty($condition["username"]) ? $_W["member"]["realname"] : trim($condition["username"]), "mobile" => empty($condition["mobile"]) ? $_W["member"]["mobile"] : trim($condition["mobile"]));
    $order["total_fee"] = round($goods_num * $pintuan_goods["oldprice"], 2);
    $order["final_fee"] = round($order["total_fee"] - $order["discount"], 2);
    return $order;
}
function pintuan_is_available($goodsOrId, $team_id = 0)
{
    global $_W;
    $goods = $goodsOrId;
    if (!is_array($goods)) {
        $goods = pdo_get("tiny_wmall_pintuan_goods", array("uniacid" => $_W["uniacid"], "id" => $goodsOrId));
    }
    if (TIMESTAMP <= $goods["starttime"] || $goods["endtime"] <= TIMESTAMP) {
        return error(-1, "商品不在可售时间内");
    }
    if ($goods["total"] == 0) {
        return error(-1, "商品已被抢完了");
    }
    if (0 < $team_id) {
        $condition = " where uniacid = :uniacid and order_type = :order_type and goods_id = :goods_id and team_id = :team_id and status = 2";
        $params = array(":uniacid" => $_W["uniacid"], ":order_type" => "pintuan", ":goods_id" => $goods["id"], ":team_id" => $team_id);
        $teams = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_order") . $condition, $params);
        if (empty($teams)) {
            return error(-1000, "请先发起团购");
        }
        $teams_length = count($teams);
        if ($goods["peoplenum"] <= $teams_length) {
            return error(-1000, "该团购人数已达上限，请重新下单");
        }
        if (0 < $goods["grouptime"] && $goods["grouptime"] * 3600 < TIMESTAMP - $teams[0]["paytime"]) {
            return error(-1000, "该团购组团已超时");
        }
        return $teams[0];
    }
    return true;
}
function pintuan_get_member_takepart($goodsid, $uid = 0)
{
    global $_W;
    if (empty($uid)) {
        $uid = $_W["member"]["uid"];
    }
    $is_exist = pdo_fetch("select id, uid, goods_id, team_id, status, team_num, takepart_num from " . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and order_type = :order_type and goods_id = :goods_id and uid = :uid and is_team = 1 and status in (1,2,3,4)", array(":uniacid" => $_W["uniacid"], ":order_type" => "pintuan", ":goods_id" => $goodsid, ":uid" => $uid));
    return $is_exist;
}
function pintuan_order_update($orderOrId, $type, $extra = array())
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
        $update = array("is_pay" => 1, "status" => 2, "order_channel" => $extra["channel"], "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP, "transaction_id" => $extra["transaction_id"], "out_trade_no" => $extra["uniontid"]);
        if (empty($order["is_team"])) {
            $update["status"] = 3;
        } else {
            if ($order["is_team"] == 1 && $order["id"] == $order["team_id"]) {
                $update["overtime"] = $order["overtime"] - $order["addtime"] + TIMESTAMP;
                $update["takepart_num"] = 1;
            } else {
                pdo_query("UPDATE " . tablename("tiny_wmall_gohome_order") . " set takepart_num = takepart_num + 1 WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $order["uniacid"], ":id" => $order["team_id"]));
            }
        }
        pdo_update("tiny_wmall_gohome_order", $update, array("id" => $order["id"]));
        pdo_query("update " . tablename("tiny_wmall_pintuan_goods") . " set sailed = sailed + " . $order["num"] . " where uniacid = :uniacid and id = :id", array(":uniacid" => $order["uniacid"], ":id" => $order["goods_id"]));
        gohome_goods_total_update($order, 1);
        if ($order["is_team"] == 1) {
            if ($order["id"] == $order["team_id"]) {
                $team_head["takepart_num"] = 1;
            } else {
                $team_head = pdo_get("tiny_wmall_gohome_order", array("uniacid" => $order["uniacid"], "id" => $order["team_id"]), array("id", "takepart_num"));
            }
            if ($team_head["takepart_num"] == $order["team_num"]) {
                pintuan_order_update($order, "team_success");
                return NULL;
            }
        }
        gohome_order_clerk_notice($order["id"], "pay");
        gohome_order_print($order["id"]);
        return error(0, "支付成功");
    }
    if ($type == "team_success") {
        if (empty($order["team_id"])) {
            return error(-1, "订单不是团购订单");
        }
        pdo_query("update " . tablename("tiny_wmall_gohome_order") . " set status = 3 where uniacid = :uniacid and team_id = :team_id and status = 2", array(":uniacid" => $_W["uniacid"], ":team_id" => $order["team_id"]));
        $teams = pdo_fetchall("select id from " . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and team_id = :team_id and status = 3", array(":uniacid" => $_W["uniacid"], ":team_id" => $order["team_id"]));
        foreach ($teams as $val) {
            gohome_order_status_notice($val["id"], "team_success");
        }
        gohome_order_clerk_notice($order["id"], "team_success");
        return error(0, "恭喜组团成功,请等待开团");
    } else {
        if ($type == "delivery") {
            if ($order["status"] == 1 || $order["status"] == 2) {
                return error(-1, "请等待开团");
            }
            if ($order["status"] == 4) {
                return error(-1, "订单已确认");
            }
            if (in_array($order["status"], array(5, 6))) {
                return error(-1, "订单已完成");
            }
            if ($order["status"] == 7) {
                return error(-1, "订单已取消");
            }
            $update = array("status" => 4);
            pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        } else {
            if ($type == "confirm") {
                if ($order["status"] == 1 || $order["status"] == 2) {
                    return error(-1, "请等待开团");
                }
                if (in_array($order["status"], array(5, 6))) {
                    return error(-1, "订单已完成");
                }
                if ($order["status"] == 7) {
                    return error(-1, "订单已取消");
                }
                $update = array("status" => 5, "applytime" => TIMESTAMP);
                pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
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
                        $result = member_credit_update($order["uid"], "credit1", $credit1, array(0, "生活圈拼团订单完成，赠送" . $credit1 . "积分"));
                        if (is_error($result)) {
                            slog("credit1Update", "生活圈拼团下单送积分-order_id:" . $order["id"], array("order_id" => $order["id"], "uid" => $order["uid"], "credit_type" => "credit1"), $result["message"]);
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
                $teams[] = $order;
                if ($extra["team_cancel"] == 1 && 0 < $order["team_id"]) {
                    $teams = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_order") . " where uniacid = :uniacid and team_id = :team_id", array(":uniacid" => $_W["uniacid"], ":team_id" => $order["team_id"]));
                }
                foreach ($teams as $val) {
                    if ($val["is_pay"] == 0) {
                        pdo_update("tiny_wmall_gohome_order", array("status" => 7), array("uniacid" => $_W["uniacid"], "id" => $val["id"]));
                    } else {
                        $update = array("status" => 7, "refund_status" => 1, "refund_out_no" => date("YmdHis") . random(10, true), "refund_apply_time" => TIMESTAMP);
                        pdo_update("tiny_wmall_gohome_order", $update, array("uniacid" => $_W["uniacid"], "id" => $val["id"]));
                        if ($order["is_team"] == 1 && $extra["team_cancel"] != 1) {
                            pdo_query("UPDATE " . tablename("tiny_wmall_gohome_order") . " set takepart_num = takepart_num - 1 WHERE uniacid = :uniacid AND id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $order["team_id"]));
                        }
                        gohome_order_status_notice($val["id"], "cancel");
                        $refund_result = gohome_order_begin_refund($val["id"]);
                        if (is_error($refund_result)) {
                            gohome_order_refund_notice($val["id"], "fail", "失败原因: " . $refund_result["message"]);
                        } else {
                            gohome_order_refund_notice($val["id"], "success");
                        }
                        if ($extra["team_cancel"] != 1) {
                            return error(0, array("is_refund" => 1, "refund_message" => $refund_result["message"], "refund_code" => $refund_result["errno"]));
                        }
                    }
                }
                return error(0, "取消拼团成功");
            } else {
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
                            $result = member_credit_update($order["uid"], "credit1", $credit1, array(0, "生活圈拼团订单完成，赠送" . $credit1 . "积分"));
                            if (is_error($result)) {
                                slog("credit1Update", "生活圈拼团下单送积分-order_id:" . $order["id"], array("order_id" => $order["id"], "uid" => $order["uid"], "credit_type" => "credit1"), $result["message"]);
                            }
                        }
                    }
                    return error(0, "订单成功设为已处理");
                }
            }
        }
    }
}
function pintuan_order_sync()
{
    global $_W;
    $condition = " where uniacid = :uniacid and agentid = :agentid and is_team = 1 and status = 2 and id = team_id and overtime < :overtime";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":overtime" => TIMESTAMP);
    $teams = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_order") . $condition, $params);
    if (!empty($teams)) {
        foreach ($teams as $val) {
            pintuan_order_update($val, "cancel", array("team_cancel" => 1, "team_id" => $val["team_id"]));
        }
    }
    return true;
}
function pintuan_cron()
{
    global $_W;
    $key = "we7_wmall:" . $_W["uniacid"] . ":pintuan:lock:120";
    if (check_cache_status($key, 120)) {
        return true;
    }
    gohome_goods_sync("pintuan");
    pintuan_order_sync();
    set_cache($key, array());
    return true;
}

?>