<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
mload()->model("plugin");
pload()->model("errander");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " WHERE uniacid = :uniacid";
    $params[":uniacid"] = $_W["uniacid"];
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status >= :refund_status";
        $params[":refund_status"] = $re_status;
    }
    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1;
    if (0 <= $is_pay) {
        $condition .= " AND is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    $pay_type = trim($_GPC["pay_type"]);
    if (!empty($pay_type)) {
        $condition .= " AND is_pay = 1 AND pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " AND (buy_username LIKE '%" . $keyword . "%' OR buy_mobile LIKE '%" . $keyword . "%' OR accept_username LIKE '%" . $keyword . "%' OR accept_mobile LIKE '%" . $keyword . "%' OR order_sn LIKE '%" . $keyword . "%')";
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    } else {
        $starttime = strtotime("-15 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $condition .= " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize;
    $orders = pdo_fetchall("SELECT id,order_type,order_cid,goods_name,goods_price,goods_weight,buy_username,buy_address,buy_mobile,accept_username,accept_mobile,accept_address,note,is_pay,pay_type,final_fee,deliveryer_id,delivery_status,delivery_time,addtime,status,refund_status,delivery_assign_time,delivery_instore_time,delivery_success_time,delivery_collect_type FROM " . tablename("tiny_wmall_errander_order") . $condition, $params);
    $deliveryers = deliveryer_all();
    if (!empty($orders)) {
        foreach ($orders as &$da) {
            $da["addtime_cn"] = date("Y-m-d H:i", $da["addtime"]);
            if (0 < $da["deliveryer_id"]) {
                $da["deliveryer"] = $deliveryers[$da["deliveryer_id"]];
                if (!empty($da["delivery_assign_time"])) {
                    $da["delivery_assign_time_cn"] = date("Y-m-d H:i", $da["delivery_assign_time"]);
                }
                if (!empty($da["delivery_instore_time"])) {
                    $da["delivery_instore_time_cn"] = date("Y-m-d H:i", $da["delivery_instore_time"]);
                }
                if (!empty($da["delivery_success_time"])) {
                    $da["delivery_success_time_cn"] = date("Y-m-d H:i", $da["delivery_success_time"]);
                }
            }
            if ($da["status"] == "4") {
                $da["cancel_reason"] = errander_order_cancel_reason($da["id"]);
            }
        }
    }
    $result = array("order" => $orders);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "detail") {
        $id = intval($_GPC["id"]);
        $order = errander_order_fetch($id);
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
        }
        $order["log_current"] = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_errander_order_status_log") . " WHERE uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $id));
        $order["log_current"]["addtime_cn"] = date("Y-m-d H:i", $order["log_current"]["addtime"]);
        if (0 < $order["deliveryer_id"]) {
            $order["deliveryer"] = pdo_get("tiny_wmall_deliveryer", array("uniacid" => $_W["uniacid"], "id" => $order["deliveryer_id"]), array("id", "title", "avatar", "mobile", "location_x", "location_y"));
            $order["delivery_collect_type_cn"] = order_collect_type($order);
        }
        $order["extra_fee"] = array();
        if (!empty($order["data"]["order"]["extra_fee"])) {
            foreach ($order["data"]["order"]["extra_fee"] as $val) {
                $item_str = "";
                foreach ($val["value"] as $child) {
                    $item_str .= " " . $child["name"] . "-￥" . $child["fee"];
                }
                $order["extra_fee"][] = array("title" => $val["title"], "text" => $item_str);
            }
        }
        $result = array("order" => $order);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "logs") {
            $id = intval($_GPC["id"]);
            $logs = errander_order_fetch_status_log($id);
            $result = array("logs" => array_values($logs));
            imessage(error(0, $result), "", "ajax");
        } else {
            if ($ta == "cancel") {
                $id = intval($_GPC["id"]);
                $status = errander_order_status_update($id, "cancel");
                if (is_error($status)) {
                    message($status, "", "ajax");
                }
                if ($status["message"]["is_refund"]) {
                    $refund = errander_order_begin_payrefund($id);
                    if (is_error($refund)) {
                        imessage(error(-1, $refund["message"]), "", "ajax");
                    }
                    imessage(error(0, "取消订单成功," . $refund["message"]), "", "ajax");
                } else {
                    imessage(error(0, "取消订单成功"), "", "ajax");
                }
            } else {
                if ($ta == "status") {
                    $id = intval($_GPC["id"]);
                    $type = trim($_GPC["type"]);
                    if ($type == "delivery_wait") {
                        $status = errander_order_deliveryer_notice($id, $type);
                        if (is_error($status)) {
                            message($status, "", "ajax");
                        }
                        message(error(0, ("通知配送员抢单成功"), "", "ajax");
                    } else {
                        $status = errander_order_status_update($id, $type);
                        imessage($status, "", "ajax");
                    }
                } else {
                    if ($ta == "del") {
                        $ids = $_GPC["id"];
                        if (!is_array($ids)) {
                            $ids = array($ids);
                        }
                        foreach ($ids as $id) {
                            $id = intval($id);
                            $order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "id" => $id));
                            if ($order["status"] != 4) {
                                imessage(error(-1, "订单状态有误， 不能删除订单"), "", "ajax");
                            }
                            pdo_delete("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "id" => $id));
                            pdo_delete("tiny_wmall_errander_order_status_log", array("uniacid" => $_W["uniacid"], "oid" => $id));
                            pdo_delete("tiny_wmall_order_refund_log", array("uniacid" => $_W["uniacid"], "oid" => $id, "order_type" => "errander"));
                        }
                        imessage(error(0, "删除订单成功"), "", "ajax");
                        return 1;
                    } else {
                        if ($ta == "refund_handle") {
                            $id = intval($_GPC["id"]);
                            $result = errander_order_begin_payrefund($id);
                            if (!is_error($result)) {
                                $query = errander_order_query_payrefund($id);
                                if (is_error($query)) {
                                    imessage(error(-1, "发起退款成功, 获取退款状态失败"), "", "ajax");
                                } else {
                                    imessage(error(0, "发起退款成功, 退款状态已更新"), "", "ajax");
                                }
                            } else {
                                message($result, "", "ajax");
                            }
                        } else {
                            if ($ta == "refund_query") {
                                $id = intval($_GPC["id"]);
                                $query = errander_order_query_payrefund($id);
                                if (is_error($query)) {
                                    imessage(error(-1, "获取退款状态失败"), "", "ajax");
                                }
                                imessage(error(0, "更新退款状态成功"), "", "ajax");
                            } else {
                                if ($ta == "refund_status") {
                                    $id = intval($_GPC["id"]);
                                    pdo_update("tiny_wmall_errander_order", array("refund_status" => 3), array("uniacid" => $_W["uniacid"], "id" => $id));
                                    errander_order_insert_refund_log($id, "success");
                                    imessage(error(0, "设置为已退款成功"), referer(), "ajax");
                                } else {
                                    if ($ta == "analyse") {
                                        $id = intval($_GPC["id"]);
                                        $deliveryers = errander_order_analyse($id, array("channel" => "plateform_dispatch"));
                                        if (is_error($deliveryers)) {
                                            message($deliveryers, "", "ajax");
                                        }
                                        message(error(0, $deliveryers), "", "ajax");
                                    } else {
                                        if ($ta == "dispatch") {
                                            $order_id = intval($_GPC["id"]);
                                            $dispatch = intval($_GPC["dispatch"]);
                                            if ($dispatch) {
                                                $deliveryer_id = intval($_GPC["deliveryer_id"]);
                                                $status = errander_order_assign_deliveryer($order_id, $deliveryer_id, true);
                                                message($status, "", "ajax");
                                            }
                                            $order = errander_order_fetch($order_id);
                                            if (empty($order)) {
                                                message(error(-1, "订单不存在或已经删除"), "", "ajax");
                                            }
                                            mload()->model("deliveryer.extra");
                                            $deliveryer = deliveryer_get_location(array("order_type" => "is_errander", "agentid" => $order["agentid"]));
                                            $result = array("order" => $order, "deliveryer" => $deliveryer);
                                            imessage(error(0, $result), "", "ajax");
                                        } else {
                                            if ($ta == "search") {
                                                $params = array("input" => array("keyword" => "姓名/手机号/订单编号"), "time" => array("name" => "addtime", "start" => "下单开始时间", "end" => "下单截止时间"));
                                                if (0 < $_W["agentid"]) {
                                                    $params["extra"]["agent"] = "1";
                                                }
                                                $filter = get_filter_params($params);
                                                $result = array("filter" => $filter);
                                                imessage(error(0, $result), "", "ajax");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>