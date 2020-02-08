<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "map";
$_W["page"]["title"] = "调度中心";
$stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title", "address", "location_x", "location_y"), "id");
if ($op == "map") {
    if ($_W["isajax"]) {
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        isetcookie("_" . $type, $status, 1000000);
    }
    $deliveryer_alls = deliveryer_fetchall(0, array("agentid" => -1));
    $sid = intval($_GPC["sid"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if ($_W["ispost"]) {
        $condition = " where uniacid = :uniacid and (status = 2 or status = 3) and order_type = 1 and delivery_type = 2";
        $params = array(":uniacid" => $_W["uniacid"]);
        if (0 < $sid) {
            $condition .= " and sid = :sid";
            $params[":sid"] = $sid;
        }
        $orders = pdo_fetchall("select id,sid,serial_sn,address,number,mobile,username,sex,location_x,location_y,paytime,status,dispatch_status,is_reserve from " . tablename("tiny_wmall_order") . $condition, $params, "id");
        if (!empty($orders)) {
            foreach ($orders as &$val) {
                $val["store"] = $stores[$val["sid"]];
                $val["paytime_cn"] = date("m-d H:i", $val["paytime"]);
            }
        }
        $condition = " where uniacid = :uniacid and work_status = 1 and is_takeout = 1 and status = 1 ";
        $params = array(":uniacid" => $_W["uniacid"]);
        if (0 < $deliveryer_id) {
            $condition .= " and id = :id";
            $params[":id"] = $deliveryer_id;
        }
        $deliveryers = pdo_fetchall("select id,mobile,title,location_x,location_y from " . tablename("tiny_wmall_deliveryer") . $condition, $params);
        $stat_day = date("Ymd");
        $def_starttime = strtotime("00:00");
        $def_endtime = strtotime("05:00");
        if ($def_starttime <= TIMESTAMP && TIMESTAMP <= $def_endtime) {
            $stat_day = date("Ymd", strtotime("-1 day"));
        }
        if (!empty($deliveryers)) {
            foreach ($deliveryers as &$row) {
                $row["finish"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and delivery_status = 5", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"], ":stat_day" => $stat_day));
                $row["wait_pickup"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and (delivery_status = 7 or delivery_status = 8)", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"], ":stat_day" => $stat_day));
                $row["wait_delivery"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and delivery_status = 4", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"], ":stat_day" => $stat_day));
                $row["work_status"] = 1;
                $addtime = pdo_fetchcolumn("select addtime from " . tablename("tiny_wmall_deliveryer_location_log") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id order by id desc limit 1", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $row["id"]));
                if (300 < TIMESTAMP - $addtime) {
                    $row["work_status"] = 2;
                }
                $row["css"] = "";
                if ($row["work_status"] == 2) {
                    $row["css"] = "off-line";
                } else {
                    if ($row["work_status"] == 1 && empty($row["wait_pickup"]) && empty($row["wait_delivery"])) {
                        $row["css"] = "active";
                    }
                }
            }
        }
        $dispatch = array("orders" => $orders, "deliveryers" => $deliveryers);
        imessage(error(0, $dispatch), "", "ajax");
    }
} else {
    if ($op == "deliveryer") {
        if ($_W["ispost"]) {
            $id = intval($_GPC["id"]);
            $deliveryer = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (empty($deliveryer)) {
                imessage(error(-1, "配送员不存在或已删除"), referer(), "ajax");
            }
            $stat_day = date("Ymd");
            $def_starttime = strtotime("00:00");
            $def_endtime = strtotime("05:00");
            if ($def_starttime <= TIMESTAMP && TIMESTAMP <= $def_endtime) {
                $stat_day = date("Ymd", strtotime("-1 day"));
            }
            $params = array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $id, ":stat_day" => $stat_day);
            $deliveryer["finish"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and delivery_status = 5", $params);
            $deliveryer["wait_pickup"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and (delivery_status = 8 or delivery_status = 7)", $params);
            $deliveryer["wait_delivery"] = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . " where uniacid = :uniacid and deliveryer_id = :deliveryer_id and stat_day >= :stat_day and delivery_status = 4", $params);
            $deliveryer["orders"] = pdo_fetchall("select a.*, b.title, b.location_x as store_location_x,b.location_y as store_location_y from " . tablename("tiny_wmall_order") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id where a.uniacid = :uniacid and a.deliveryer_id = :deliveryer_id and a.delivery_status in (4, 7, 8) order by a.delivery_assign_time asc", array(":uniacid" => $_W["uniacid"], ":deliveryer_id" => $id));
            if (!empty($deliveryer["orders"])) {
                $delivery_status = order_delivery_status();
                foreach ($deliveryer["orders"] as &$row) {
                    $row["store_title"] = $row["title"];
                    $row["delivery_status_cn"] = $delivery_status[$row["delivery_status"]]["text"];
                    $row["store"] = array("location_x" => $row["store_location_x"], "location_y" => $row["store_location_y"]);
                    $row["time_interval"] = order_time_analyse($row["id"]);
                }
            }
            imessage(error(0, $deliveryer), "", "ajax");
        }
    } else {
        if ($op == "menu") {
            $condition = " where uniacid  = :uniacid and delivery_status > 0 and delivery_type = 2";
            $params = array(":uniacid" => $_W["uniacid"]);
            $delivery_status = isset($_GPC["delivery_status"]) ? intval($_GPC["delivery_status"]) : 0;
            if (0 < $delivery_status) {
                $condition .= " and delivery_status = :delivery_status";
                $params[":delivery_status"] = $delivery_status;
            }
            $pindex = max(1, intval($_GPC["page"]));
            $psize = 15;
            $total = pdo_fetchcolumn("SELECT count(*) from " . tablename("tiny_wmall_order") . $condition, $params);
            $orders = pdo_fetchall("select * from " . tablename("tiny_wmall_order") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
            $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
            $deliveryer_alls = deliveryer_all();
            if (!empty($orders)) {
                foreach ($orders as &$value) {
                    $value["store"] = $stores[$value["sid"]];
                    $value["deliveryer"] = $deliveryer_alls[$value["deliveryer_id"]];
                }
            }
            $order_delivery_status = order_delivery_status();
            $pager = pagination($total, $pindex, $psize);
        } else {
            if ($op == "dispatch") {
                $ids = $_GPC["ids"];
                if (!is_array($ids)) {
                    $ids = array($ids);
                }
                $force = $_GPC["force"] ? true : false;
                $deliveryer_id = intval($_GPC["deliveryer_id"]);
                if (!empty($ids)) {
                    foreach ($ids as $key => $id) {
                        $status = order_assign_deliveryer($id, $deliveryer_id, $force, "本订单由平台管理员调度分配,请尽快处理");
                        if (is_error($status)) {
                            if ($status["errno"] == -1000) {
                                $status["order_id"] = $id;
                                $status["deliveryer_id"] = $deliveryer_id;
                            }
                            imessage($status, "", "ajax");
                        }
                    }
                }
                imessage(error(0, "分配订单成功"), "", "ajax");
            } else {
                if ($op == "notify") {
                    $id = intval($_GPC["id"]);
                    $extra["force"] = 1;
                    $result = order_status_update($id, "notify_deliveryer_collect", $extra);
                    if (is_error($result)) {
                        imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
                    }
                    imessage(error(0, $result["message"]), "", "ajax");
                }
            }
        }
    }
}
include itemplate("order/dispatch");

?>