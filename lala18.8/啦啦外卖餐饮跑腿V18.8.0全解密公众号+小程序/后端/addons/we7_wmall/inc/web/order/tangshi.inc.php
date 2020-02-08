<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("table");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "店内订单";
    $condition = " where uniacid = :uniacid and status = 5 and order_type > 2 and stat_day = :stat_day";
    $stat = pdo_fetch("select count(*) as total_num, sum(final_fee) as total_price from " . tablename("tiny_wmall_order") . $condition, array(":uniacid" => $_W["uniacid"], ":stat_day" => date("Ymd")));
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and order_type > 2";
    $params = array(":uniacid" => $_W["uniacid"]);
    if ($filter_type == "process") {
        $condition .= " AND (status != 5 and status != 6)";
    }
    $uid = intval($_GPC["uid"]);
    if (0 < $uid) {
        $condition .= " AND uid = :uid";
        $params[":uid"] = $uid;
    }
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }
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
    $is_remind = intval($_GPC["is_remind"]);
    if (0 < $is_remind) {
        $condition .= " AND is_remind = :is_remind";
        $params[":is_remind"] = $is_remind;
    }
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }
    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1;
    if (-1 < $is_pay) {
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
        $condition .= " AND (username LIKE '%" . $keyword . "%' OR mobile LIKE '%" . $keyword . "%' OR ordersn LIKE '%" . $keyword . "%')";
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    } else {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP + 86400;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_order") . $condition, $params);
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($orders)) {
        $order_ids = implode(",", array_keys($orders));
        $goods_temp = pdo_fetchall("select * from " . tablename("tiny_wmall_order_stat") . " where uniacid = :uniacid and oid in (" . $order_ids . ")", array(":uniacid" => $_W["uniacid"]));
        $goods_all = array();
        foreach ($goods_temp as $row) {
            $goods_all[$row["oid"]][] = $row;
        }
        foreach ($orders as &$da) {
            $da["pay_type_class"] = "";
            if ($da["is_pay"] == 1) {
                $da["pay_type_class"] = "have-pay";
                if ($da["pay_type"] == "delivery") {
                    $da["pay_type_class"] = "delivery-pay";
                }
            }
            if ($da["order_type"] == 3) {
                $tables[] = $da["table_id"];
            }
        }
        if (!empty($tables)) {
            $tables_str = implode(",", $tables);
            $tables = pdo_fetchall("select * from " . tablename("tiny_wmall_tables") . " where uniacid = :uniacid and id in (" . $tables_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $refund_status = order_refund_status();
    $order_reserve_types = order_reserve_type();
    $table_categorys = table_category_fetchall($sid);
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title"), "id");
    load()->model("mc");
    $fields = mc_acccount_fields();
    include itemplate("order/tangshiList");
}
if ($op == "detail") {
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if (empty($order)) {
        imessage("订单不存在或已经删除", iurl("order/takeout/list"), "error");
    }
    $order["goods"] = order_fetch_goods($order["id"]);
    if ($order["is_comment"] == 1) {
        $comment = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_order_comment") . " WHERE uniacid = :aid AND oid = :oid", array(":aid" => $_W["uniacid"], ":oid" => $id));
        if (!empty($comment)) {
            $comment["data"] = iunserializer($comment["data"]);
            $comment["thumbs"] = iunserializer($comment["thumbs"]);
        }
    }
    if (0 < $order["discount_fee"]) {
        $discount = order_fetch_discount($id);
    }
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $logs = order_fetch_status_log($id);
    include itemplate("order/tangshiDetail");
}
if ($op == "status") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $type = trim($_GPC["type"]);
    if (empty($type)) {
        imessage(error(-1, "订单状态错误"), "", "ajax");
    }
    foreach ($ids as $id) {
        $id = intval($id);
        if ($id <= 0) {
            continue;
        }
        $result = order_status_update($id, $type);
        if (is_error($result)) {
            imessage(error(-1, "处理编号为:" . $id . "的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }
    }
    imessage(error(0, "更新订状态成功"), "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $result = order_status_update($id, "cancel", array("force_cancel" => 1));
    if (is_error($result)) {
        imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
    }
    if ($result["message"]["is_refund"]) {
        $refund = order_refund_status_update($id, 0, "handle");
        if (is_error($refund)) {
            imessage(error(-1, $refund["message"]), "", "ajax");
        }
        imessage(error(0, "取消订单成功," . $refund["message"]), "", "ajax");
    } else {
        imessage(error(0, "取消订单成功"), "", "ajax");
    }
}
if ($op == "refund_update") {
    $order_id = intval($_GPC["id"]);
    $refund_id = intval($_GPC["refund_id"]);
    $type = trim($_GPC["type"]);
    $result = order_refund_status_update($order_id, $refund_id, $type);
    imessage($result, referer(), "ajax");
}
if ($op == "remind") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $reply = trim($_GPC["reply"]);
        $result = order_status_update($id, "reply", array("reply" => $reply));
        imessage(error(0, "回复催单成功"), referer(), "ajax");
    }
    include itemplate("store/order/tangshiOp");
}
if ($op == "print") {
    $id = intval($_GPC["id"]);
    $status = order_print($id);
    if (is_error($status)) {
        imessage(error(-1, $status["message"]), "", "ajax");
    }
    imessage(error(0, "发送打印指定成功"), "", "ajax");
}
if ($op == "pay_status") {
    $id = intval($_GPC["id"]);
    $result = order_status_update($id, "pay");
    if (is_error($result)) {
        message($result["message"], referer(), "error");
    }
    message("设置订单支付成功", referer(), "success");
}
if ($op == "export") {
    load()->model("mc");
    mload()->model("deliveryer");
    $stores = store_fetchall(array("id", "title"));
    $pay_types = order_pay_types();
    $order_status = order_status();
    $deliveryers = deliveryer_all(true);
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and order_type = 3";
    $params[":uniacid"] = $_W["uniacid"];
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $sid = intval($_GPC["sid"]);
    if (0 < $sid) {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    } else {
        if ($filter_type == "process") {
            $condition .= " AND status >= 1 AND status <= 4";
        }
    }
    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : 1;
    if (-1 < $is_pay) {
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
        $condition .= " AND (ordersn LIKE '%" . $keyword . "%' or mobile LIKE '%" . $keyword . "%' or username LIKE '%" . $keyword . "%')";
    }
    if (!empty($_GPC["addtime"])) {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    } else {
        $starttime = strtotime("-15 day");
        $endtime = TIMESTAMP;
    }
    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $list = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY id DESC", $params);
    foreach ($list as &$item) {
        $tables[] = $item["table_id"];
    }
    if (!empty($tables)) {
        $tables_str = implode(",", $tables);
        $tables = pdo_fetchall("select * from " . tablename("tiny_wmall_tables") . " where uniacid = :uniacid and id in (" . $tables_str . ")", array(":uniacid" => $_W["uniacid"]), "id");
    }
    $order_fields = array("id" => array("field" => "id", "title" => "订单ID", "width" => "10"), "ordersn" => array("field" => "ordersn", "title" => "订单编号", "width" => "30"), "uid" => array("field" => "uid", "title" => "下单人UID", "width" => "10"), "openid" => array("field" => "openid", "title" => "粉丝openid", "width" => "40"), "sid" => array("field" => "sid", "title" => "下单门店", "width" => "15"), "pay_type" => array("field" => "pay_type", "title" => "支付方式", "width" => "15"), "num" => array("field" => "num", "title" => "份数", "width" => "10"), "price" => array("field" => "price", "title" => "商品费用", "width" => "10"), "box_price" => array("field" => "box_price", "title" => "餐盒费", "width" => "10"), "pack_fee" => array("field" => "pack_fee", "title" => "包装费", "width" => "10"), "extra_fee" => array("field" => "extra_fee", "title" => "附加费", "width" => "15"), "total_fee" => array("field" => "total_fee", "title" => "总价", "width" => "15"), "discount_fee" => array("field" => "discount_fee", "title" => "优惠金额", "width" => "15"), "store_discount_fee" => array("field" => "store_discount_fee", "title" => "商户承担金额", "width" => "15"), "agent_discount_fee" => array("field" => "agent_discount_fee", "title" => "代理商承担金额", "width" => "15"), "plateform_discount_fee" => array("field" => "plateform_discount_fee", "title" => "平台承担金额", "width" => "15"), "final_fee" => array("field" => "final_fee", "title" => "优惠后价格", "width" => "15"), "addtime" => array("field" => "addtime", "title" => "下单时间", "width" => "25"), "person_num" => array("field" => "person_num", "title" => "就餐人数", "width" => "15"), "table_title" => array("field" => "table_title", "title" => "就餐桌号", "width" => "25"), "out_trade_no" => array("field" => "out_trade_no", "title" => "本平台支付单价", "width" => "25"), "transaction_id" => array("field" => "transaction_id", "title" => "第三方支付单价", "width" => "25"), "status" => array("field" => "status", "title" => "订单状态", "width" => "25"), "status_cn" => array("field" => "status_cn", "title" => "订单最新进度", "width" => "25"), "goods" => array("field" => "goods", "title" => "商品信息", "width" => "100"), "endtime" => array("field" => "endtime", "title" => "订单完成时间", "width" => "25"));
    $_GPC["fields"] = explode("|", $_GPC["fields"]);
    if (!empty($_GPC["fields"])) {
        $groups = mc_groups();
        $fields = mc_acccount_fields();
        $user_fields = array();
        foreach ($_GPC["fields"] as $field) {
            if (in_array($field, array_keys($fields))) {
                $user_fields[$field] = array("field" => $field, "title" => $fields[$field], "width" => "25");
            }
        }
        if (!empty($user_fields)) {
            $uids = array();
            foreach ($list as $li) {
                if (!in_array($li["uid"], $uids)) {
                    $uids[] = $li["uid"];
                }
            }
            $uids = array_unique($uids);
            $uids_str = implode(",", $uids);
            $users = pdo_fetchall("select * from " . tablename("mc_members") . " where uniacid = :uniacid and uid in (" . $uids_str . ")", array(":uniacid" => $_W["uniacid"]), "uid");
        }
        $header = array_merge($order_fields, $user_fields);
    }
    $ABC = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
    $i = 0;
    foreach ($header as $key => $val) {
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
    if (!empty($list)) {
        $oids = array();
        foreach ($list as $li) {
            $oids[] = $li["id"];
        }
        $oid_str = implode(",", $oids);
        $goods_temp = pdo_fetchall("select * from " . tablename("tiny_wmall_order_stat") . " where uniacid = :uniacid and oid in (" . $oid_str . ")", array(":uniacid" => $_W["uniacid"]));
        foreach ($goods_temp as $row) {
            if (!empty($row["goods_number"])) {
                $row["goods_title"] = (string) $row["goods_title"] . "-" . $row["goods_number"];
            }
            $goods[$row["oid"]][] = (string) $row["goods_title"] . " X " . $row["goods_num"];
        }
        $i = 0;
        for ($length = count($list); $i < $length; $i++) {
            $row = $list[$i];
            $row["addtime"] = date("Y/m/d H:i", $row["addtime"]);
            $row["ordersn"] = " " . $row["ordersn"];
            if (empty($row["delivery_assign_time"])) {
                $row["delivery_assign_time"] = "暂未接单";
            } else {
                $row["delivery_assign_time"] = date("Y/m/d H:i", $row["delivery_assign_time"]);
            }
            if (empty($row["endtime"])) {
                $row["endtime"] = "订单未完成";
            } else {
                $row["endtime"] = date("Y/m/d H:i", $row["endtime"]);
            }
            $row["out_trade_no"] = " " . $row["out_trade_no"];
            $row["transaction_id"] = " " . $row["transaction_id"];
            foreach ($all_fields as $key => $li) {
                $field = $li["field"];
                if (in_array($field, array_keys($order_fields))) {
                    if ($field == "sid") {
                        $row[$field] = $stores[$row[$field]]["title"];
                    } else {
                        if ($field == "pay_type") {
                            $row[$field] = empty($row["is_pay"]) ? "未支付" : $pay_types[$row[$field]]["text"];
                        } else {
                            if ($field == "goods") {
                                $row[$field] = implode(", ", $goods[$row["id"]]);
                            } else {
                                if ($field == "status") {
                                    $row[$field] = $order_status[$row["status"]]["text"];
                                } else {
                                    if ($field == "status_cn") {
                                        $log = pdo_fetch("select * from " . tablename("tiny_wmall_order_status_log") . " where uniacid = :uniacid and oid = :oid order by id desc", array(":uniacid" => $_W["uniacid"], ":oid" => $row["id"]));
                                        $row[$field] = date("Y-m-d H:i:s", $log["addtime"]) . ": " . $log["note"];
                                    } else {
                                        if ($field == "deliveryer_id") {
                                            $row[$field] = $deliveryers[$row["deliveryer_id"]]["title"];
                                        } else {
                                            if ($field == "table_title") {
                                                $row[$field] = $tables[$row["table_id"]]["title"];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $row[$field] = $users[$row["uid"]][$field];
                    if ($field == "groupid") {
                        $row[$field] = $groups[$row["groupid"]]["title"];
                    }
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue($key . ($i + 2), $row[$field]);
            }
        }
    }
    $objPHPExcel->getActiveSheet()->setTitle("订单数据");
    $objPHPExcel->setActiveSheetIndex(0);
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header('Content-Disposition: attachment;filename="订单数据.xls"');
    header("Cache-Control: max-age=0");
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
    $objWriter->save("php://output");
    exit;
}

?>