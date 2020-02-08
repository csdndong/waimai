<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "订单列表";
    if ($_W["isajax"]) {
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        isetcookie("_" . $type, $status, 1000000);
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = 3 and stat_day = :stat_day";
    $stat = pdo_fetch("select count(*) as total_num, sum(final_fee) as total_price from " . tablename("tiny_wmall_errander_order") . $condition, array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":stat_day" => date("Ymd")));
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and agentid = :agentid";
    $params[":uniacid"] = $_W["uniacid"];
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    } else {
        if ($filter_type == "process") {
            $condition .= " AND status >= 1 AND status <= 2";
        }
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
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
        $condition .= " AND (accept_username LIKE '%" . $keyword . "%' OR accept_mobile LIKE '%" . $keyword . "%' OR order_sn LIKE '%" . $keyword . "%')";
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
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_errander_order") . $condition, $params);
    $condition .= " order by id desc";
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $export = intval($_GPC["export"]);
    if ($export != 1) {
        $condition .= " limit " . ($pindex - 1) * $psize . "," . $psize;
    }
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_errander_order") . $condition, $params);
    if (!empty($orders)) {
        foreach ($orders as &$da) {
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            if ($da["status"] == "4") {
                $da["cancel_reason"] = errander_order_cancel_reason($da["id"]);
            }
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $errander_types = errander_types();
    $order_status = errander_order_status();
    $order_channels = order_channel();
    $deliveryers = deliveryer_all();
    if ($export == 1) {
        $order_fields = array("id" => array("field" => "id", "title" => "订单ID", "width" => "10"), "order_sn" => array("field" => "order_sn", "title" => "订单编号", "width" => "30"), "uid" => array("field" => "uid", "title" => "下单人UID", "width" => "10"), "openid" => array("field" => "openid", "title" => "粉丝openid", "width" => "40"), "order_type" => array("field" => "order_type", "title" => "订单类型", "width" => "20"), "goods_name" => array("field" => "goods_name", "title" => "商品名称", "width" => "40"), "goods_price" => array("field" => "goods_price", "title" => "商品预期价格", "width" => "30"), "final_fee" => array("field" => "final_fee", "title" => "顾客支付", "width" => "30"), "delivery_tips" => array("field" => "delivery_tips", "title" => "小费", "width" => "30"), "goods_weight" => array("field" => "goods_weight", "title" => "商品重量", "width" => "20"), "buy_username" => array("field" => "buy_username", "title" => "发货人姓名", "width" => "30"), "buy_sex" => array("field" => "buy_sex", "title" => "发货人性别", "width" => "30"), "buy_mobile" => array("field" => "buy_mobile", "title" => "发货人手机号", "width" => "30"), "buy_address" => array("field" => "buy_address", "title" => "发货地址", "width" => "40"), "accept_username" => array("field" => "accept_username", "title" => "收货人姓名", "width" => "30"), "accept_sex" => array("field" => "accept_sex", "title" => "收货人性别", "width" => "30"), "accept_mobile" => array("field" => "accept_mobile", "title" => "收货人手机号", "width" => "30"), "accept_address" => array("field" => "accept_address", "title" => "收货地址", "width" => "40"), "pay_type" => array("field" => "pay_type", "title" => "支付方式", "width" => "15"), "addtime" => array("field" => "addtime", "title" => "下单时间", "width" => "25"), "out_trade_no" => array("field" => "out_trade_no", "title" => "本平台支付单号", "width" => "25"), "transaction_id" => array("field" => "transaction_id", "title" => "第三方支付单号", "width" => "25"), "status" => array("field" => "status", "title" => "订单状态", "width" => "25"), "status_cn" => array("field" => "status_cn", "title" => "订单最新进度", "width" => "25"), "deliveryer_id" => array("field" => "deliveryer_id", "title" => "配送员", "width" => "25"), "delivery_assign_time" => array("field" => "delivery_assign_time", "title" => "最后抢单时间", "width" => "25"), "distance" => array("field" => "distance", "title" => "配送距离", "width" => "25"));
        $ABC = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
        $all_fields = array();
        $i = 0;
        foreach ($order_fields as $key => $val) {
            $all_fields[$ABC[$i]] = $val;
            $i++;
        }
        include_once IA_ROOT . "/framework/library/phpexcel/PHPExcel.php";
        $objPHPExcel = new PHPExcel();
        foreach ($all_fields as $key => $li) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($li["width"]);
            $objPHPExcel->getActiveSheet()->getStyle($key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key . "1", $li["title"]);
        }
        if (!empty($orders)) {
            $i = 0;
            for ($length = count($orders); $i < $length; $i++) {
                $row = $orders[$i];
                $row["addtime"] = date("Y/m/d H:i", $row["addtime"]);
                $row["order_sn"] = " " . $row["order_sn"];
                if (empty($row["delivery_assign_time"])) {
                    $row["delivery_assign_time"] = "暂未接单";
                } else {
                    $row["delivery_assign_time"] = date("Y/m/d H:i", $row["delivery_assign_time"]);
                }
                if (0 < $row["distance"]) {
                    $row["distance"] = $row["distance"] . "km";
                }
                $row["out_trade_no"] = " " . $row["out_trade_no"];
                $row["transaction_id"] = " " . $row["transaction_id"];
                foreach ($all_fields as $key => $li) {
                    $field = $li["field"];
                    if (in_array($field, array_keys($order_fields))) {
                        if ($field == "order_type") {
                            $row[$field] = $errander_types[$row["order_type"]]["text"];
                        } else {
                            if ($field == "pay_type") {
                                $row[$field] = empty($row["is_pay"]) ? "未支付" : $pay_types[$row[$field]]["text"];
                            } else {
                                if ($field == "status") {
                                    $row[$field] = $order_status[$row["status"]]["text"];
                                } else {
                                    if ($field == "status_cn") {
                                        $log = pdo_fetch("select * from " . tablename("tiny_wmall_errander_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $row["id"]));
                                        $row[$field] = date("Y-m-d H:i:s", $log["addtime"]) . ": " . $log["note"];
                                    } else {
                                        if ($field == "deliveryer_id") {
                                            $row[$field] = $deliveryers[$row["deliveryer_id"]]["title"];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $objPHPExcel->getActiveSheet(0)->setCellValue($key . ($i + 2), $row[$field]);
                }
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle("跑腿订单数据");
        $objPHPExcel->setActiveSheetIndex(0);
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header('Content-Disposition: attachment;filename="跑腿订单数据.xls"');
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
        $objWriter->save("php://output");
        exit;
    } else {
        include itemplate("orderList");
    }
}
if ($op == "cancel") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        $status = errander_order_status_update($id, "cancel");
        if (is_error($status)) {
            message($status, "", "ajax");
        }
    }
    imessage(error(0, "取消订单成功"), "", "ajax");
}
if ($op == "end") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        $status = errander_order_status_update($id, "end");
        if (is_error($status)) {
            message($status, "", "ajax");
        }
    }
    imessage(error(0, "设置订单完成成功"), "", "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        $id = intval($id);
        $order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        if ($order["status"] != 4) {
            imessage(error(-1, "订单状态有误， 不能删除订单"), "", "ajax");
        }
        pdo_delete("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
        pdo_delete("tiny_wmall_errander_order_status_log", array("uniacid" => $_W["uniacid"], "oid" => $id));
        pdo_delete("tiny_wmall_order_refund_log", array("uniacid" => $_W["uniacid"], "oid" => $id, "order_type" => "errander"));
    }
    imessage(error(0, "删除订单成功"), "", "ajax");
}
if ($op == "refund_handle") {
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
}
if ($op == "refund_query") {
    $id = intval($_GPC["id"]);
    $query = errander_order_query_payrefund($id);
    if (is_error($query)) {
        imessage(error(-1, "获取退款状态失败"), "", "ajax");
    }
    imessage(error(0, "更新退款状态成功"), "", "ajax");
}
if ($op == "refund_status") {
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_errander_order", array("refund_status" => 3), array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id));
    errander_order_insert_refund_log($id, "success");
    imessage(error(0, "设置为已退款成功"), referer(), "ajax");
}
if ($op == "detail") {
    $id = intval($_GPC["id"]);
    $order = errander_order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已经删除", referer(), "error");
    }
    $pay_types = order_pay_types();
    $order_types = errander_types();
    $order_status = errander_order_status();
    $logs = errander_order_fetch_status_log($id);
    $refund_logs = errander_order_fetch_refund_status_log($id);
    include itemplate("orderDetail");
}
if ($op == "analyse") {
    $id = intval($_GPC["id"]);
    $deliveryers = errander_order_analyse($id, array("channel" => "plateform_dispatch"));
    if (is_error($deliveryers)) {
        message($deliveryers, "", "ajax");
    }
    message(error(0, $deliveryers), "", "ajax");
}
if ($op == "dispatch") {
    $order_id = intval($_GPC["order_id"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $status = errander_order_assign_deliveryer($order_id, $deliveryer_id, true);
    if (is_error($status)) {
        message($status, "", "ajax");
    }
    message(error(0, "分配订单成功"), "", "ajax");
}
if ($op == "status") {
    $order_id = intval($_GPC["id"]);
    $type = trim($_GPC["type"]);
    $status = errander_order_deliveryer_notice($order_id, $type);
    if (is_error($status)) {
        message($status, "", "ajax");
    }
    message(error(0, "通知配送员抢单成功"), "", "ajax");
}

?>