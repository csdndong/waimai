<?php
/*
 * @ ÂòÂôÅÜÍÈÏµÍ³
 * @ APP¹«ÖÚºÅÐ¡³ÌÐò°æ
 * @ PHP¿ªÔ´Õ¾£¬×ñ´ÓPHP¿ªÔ´¾«Éñ
 * @ Ô´Âë½ö¹©Ñ§Ï°ÑÐ¾¿£¬½ûÖ¹ÉÌÒµÓÃÍ¾
 */

defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$basic = $_config_plugin["basic"];
$routers = array("takeout" => array("table" => "tiny_wmall_order", "status_end" => 5, "status_cancel" => 6, "ordersn" => "ordersn"));
$spread_types = array(array("type" => "takeout", "title" => "外卖订单"));
if ($basic["paotui_status"] == 1 && check_plugin_perm("errander")) {
    $routers["paotui"] = array("table" => "tiny_wmall_errander_order", "status_end" => 3, "status_cancel" => 4, "ordersn" => "order_sn");
    $spread_types[] = array("type" => "paotui", "title" => "跑腿订单");
}
if ($basic["gohome_status"] == 1 && check_plugin_perm("gohome")) {
    $routers["gohome"] = array("table" => "tiny_wmall_gohome_order", "status_end" => 5, "status_end1" => 6, "status_cancel" => 7, "ordersn" => "ordersn");
    $spread_types[] = array("type" => "gohome", "title" => "生活圈订单");
}
$order_type = empty($_GPC["order_type"]) ? "takeout" : trim($_GPC["order_type"]);
$router = $routers[$order_type];
if ($op == "index") {
    $condition = " where uniacid = :uniacid and is_pay = 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($basic["level"] == 2) {
        $condition .= " and (spread1 = :spread or spread2 = :spread)";
    } else {
        if ($basic["level"] == 1) {
            $condition .= " and spread1 = :spread";
        }
    }
    $params[":spread"] = $_W["member"]["uid"];
    $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 0;
    if ($status == 1) {
        $condition .= " and status < :status";
        $params[":status"] = $router["status_end"];
    } else {
        if ($status == 2) {
            if (!empty($router["status_end1"])) {
                $condition .= " and (status = :status or status = :status1)";
                $params[":status"] = $router["status_end"];
                $params[":status1"] = $router["status_end1"];
            } else {
                $condition .= " and status = :status";
                $params[":status"] = $router["status_end"];
            }
        } else {
            if ($status == 3) {
                $condition .= " and status = :status";
                $params[":status"] = $router["status_cancel"];
            }
        }
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $orders = pdo_fetchall("select id,spread1,spread2," . $router["ordersn"] . " as ordersn,paytime,spreadbalance,data,status,plateform_serve_fee from " . tablename($router["table"]) . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($orders)) {
        foreach ($orders as &$value) {
            $value["data"] = iunserializer($value["data"]);
            $value["paytime_cn"] = date("Y-m-d H:i:s", $value["paytime"]);
            $value["spreadid"] = $_W["member"]["uid"];
            if ($value["spread1"] == $value["spreadid"]) {
                $value["commission"] = $value["data"]["spread"]["commission"]["spread1"];
                if ($basic["commission_from"] == 1 && $value["data"]["spread"]["commission"]["commission1_type"] == "ratio") {
                    if (0 <= $value["plateform_serve_fee"]) {
                        $value["commission"] = round(floatval($value["data"]["spread"]["commission"]["spread1_rate"]) * $value["plateform_serve_fee"] / 100, 2);
                    } else {
                        $value["commission"] = 0;
                    }
                }
            }
            if ($value["spread2"] == $value["spreadid"]) {
                $value["commission"] = $value["data"]["spread"]["commission"]["spread2"];
                if ($basic["commission_from"] == 1 && $value["data"]["spread"]["commission"]["commission2_type"] == "ratio") {
                    if (0 <= $value["plateform_serve_fee"]) {
                        $value["commission"] = round(floatval($value["data"]["spread"]["commission"]["spread2_rate"]) * $value["plateform_serve_fee"] / 100, 2);
                    } else {
                        $value["commission"] = 0;
                    }
                }
            }
            if ($value["status"] == $router["status_cancel"]) {
                $value["commission"] = 0;
                $value["status_cn"] = "已取餐";
            } else {
                if ($value["status"] == $router["status_end"] || $value["status"] == $router["status_end1"]) {
                    $value["status_cn"] = "已完成";
                } else {
                    $value["status_cn"] = "未完成";
                }
            }
        }
    }
    $result = array("records" => $orders, "config" => array("spread_types" => $spread_types));
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($op == "detail") {
        $id = intval($_GPC["id"]);
        $fields = array("id", "spread1", "spread2", "uid", $router["ordersn"], "spreadbalance", "status", "data");
        if ($order_type != "paotui") {
            $fields[] = "username";
        }
        $order = pdo_get($router["table"], array("uniacid" => $_W["uniacid"], "id" => $id), $fields);
        if ($order_type == "paotui") {
            $order_member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $order["uid"]), array("realname", "nickname"));
            $order["username"] = empty($order_member["realname"]) ? $order_member["nickname"] : $order_member["realname"];
            $order["ordersn"] = $order["order_sn"];
        }
        $order["data"] = iunserializer($order["data"]);
        if ($order["spread1"] == $_W["member"]["uid"]) {
            $order["commission"] = $order["data"]["spread"]["commission"]["spread1"];
        } else {
            if ($order["spread2"] == $_W["member"]["uid"]) {
                $order["commission"] = $order["data"]["spread"]["commission"]["spread2"];
            }
        }
        $order["real_commission"] = $order["commission"];
        if ($order["status"] == $router["status_cancel"]) {
            $order["real_commission"] = 0;
            $order["status_cn"] = "已取餐";
        } else {
            if ($order["status"] == $router["status_end"] || $order["status"] == $router["status_end1"]) {
                $order["status_cn"] = "已完成";
            } else {
                $order["status_cn"] = "未完成";
            }
        }
        imessage(error(0, $order), "", "ajax");
    }
}

?>