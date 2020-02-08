<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    mload()->model("member");
    $_W["page"]["title"] = "外卖订单";
    if ($_W["isajax"]) {
        $type = trim($_GPC["type"]);
        $status = intval($_GPC["value"]);
        isetcookie("_" . $type, $status, 1000000);
    }
	//订单统计
    $condition = " where uniacid = :uniacid and status = 5 and order_type < 3 and stat_day = :stat_day";
    $stat = pdo_fetch("select count(*) as total_num, sum(final_fee) as total_price from " . tablename("tiny_wmall_order") . $condition, array(":uniacid" => $_W["uniacid"], ":stat_day" => date("Ymd")));
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and order_type < 3";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
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
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
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
    $is_reserve = isset($_GPC["is_reserve"]) ? intval($_GPC["is_reserve"]) : -1;
    if (-1 < $is_reserve) {
        $condition .= " AND is_reserve = :is_reserve";
        $params[":is_reserve"] = $is_reserve;
    }
    $order_plateform = trim($_GPC["order_plateform"]);
    if (!empty($order_plateform)) {
        $condition .= " AND order_plateform = :order_plateform";
        $params[":order_plateform"] = $order_plateform;
    }
    $order_channel = trim($_GPC["order_channel"]);
    if (!empty($order_channel)) {
        $condition .= " AND order_channel = :order_channel";
        $params[":order_channel"] = $order_channel;
    }
    $zhunshibao_status = intval($_GPC["zhunshibao_status"]);
    if (!empty($zhunshibao_status)) {
        $condition .= " AND zhunshibao_status = :zhunshibao_status";
        $params[":zhunshibao_status"] = $zhunshibao_status;
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
        $endtime = TIMESTAMP;
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
            if ($da["status"] == "6") {
                $da["cancel_reason"] = order_cancel_reason($da["id"]);
            }
            $da["remind"] = order_timeout_remind($da);
            $da["data"] = iunserializer($da["data"]);
            if (0 < $da["refund_status"]) {
                $da["refund_data"] = order_fetchall_refund($da["id"], array("refund_logs" => 1));
            }
            $da["order_num"] = member_plateform_order_num("waimai", $da["uid"]);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $refund_status = order_refund_status();
    if ($_GPC["_show_search_deliveryer"] == 1) {
        $inwork_deliveryers = deliveryer_fetchall(0, array("agentid" => -1));
    }
    $deliveryers = deliveryer_all();
    $order_channels = order_channel();
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"]), array("id", "title", "telephone"), "id");
    load()->model("mc");
    $fields = mc_acccount_fields();
    include itemplate("order/takeoutList");
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
    $order_channel = order_channel($order["order_channel"], true);
    $logs = order_fetch_status_log($id);
    include itemplate("order/takeoutDetail");
}
if ($op == "remind") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $reply = trim($_GPC["reply"]);
        $result = order_status_update($id, "reply", array("reply" => $reply));
        if (is_error($result)) {
            imessage(error(-1, "回复催单失败:" . $result["message"]), referer(), "ajax");
        }
        imessage(error(0, "回复催单成功"), referer(), "ajax");
    }
    include itemplate("order/takeoutOp");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $type = trim($_GPC["type"]);
    if (empty($type)) {
        imessage(error(-1, "订单状态错误"), "", "ajax");
    }
    $extra = array();
    if ($type == "notify_deliveryer_collect") {
        $extra["force"] = 1;
    } else {
        if ($type == "re_notify_deliveryer_collect") {
            $extra["force"] = 1;
            $extra["channel"] = "re_notify_deliveryer_collect";
            $type = "notify_deliveryer_collect";
        }
    }
    $result = order_status_update($id, $type, $extra);
    if (is_error($result)) {
        imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因:" . $result["message"]), "", "ajax");
    }
    imessage(error(0, $result["message"]), "", "ajax");
}
if ($op == "cancel") {
    $id = intval($_GPC["id"]);
    $reasons = order_cancel_types("manager");
    if ($_W["ispost"]) {
        $reason = $_GPC["reason"];
        if (empty($reason)) {
            imessage(error(-1, "请选择退款理由"), "", "ajax");
        }
        $remark = trim($_GPC["remark"]);
        $result = order_status_update($id, "cancel", array("force_refund" => 1, "force_cancel" => 1, "reason" => $reason, "remark" => $remark, "note" => (string) $reasons[$reason] . " " . $remark));
        if (is_error($result)) {
            imessage(error(-1, "处理编号为：" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }
        if ($result["message"]["is_refund"]) {
            imessage(error(0, "取消订单成功," . $result["message"]["refund_message"]), iurl("order/takeout/list"), "ajax");
        } else {
            imessage(error(0, "取消订单成功"), iurl("order/takeout/list"), "ajax");
        }
    }
    include itemplate("order/takeoutOp");
}
if ($op == "refund_update") {
    $order_id = intval($_GPC["id"]);
    $refund_id = intval($_GPC["refund_id"]);
    $type = trim($_GPC["type"]);
    $result = order_refund_status_update($order_id, $refund_id, $type);
    imessage($result, referer(), "ajax");
}
if ($op == "handleGoodsRefund") {
    $refund_id = intval($_GPC["refund_id"]);
    $refund = pdo_get("tiny_wmall_order_refund", array("uniacid" => $_W["uniacid"], "id" => $refund_id), array("id", "data"));
    if (empty($refund)) {
        imessage(error(-1, "部分商品退款不存在"), "", "ajax");
    }
    if ($refund["status"] != 1) {
        imessage(error(-1, "退款已处理，不能再次处理"), "", "ajax");
    }
    $order_id = intval($_GPC["order_id"]);
    $order = order_fetch($order_id);
    if (empty($order)) {
        imessage(error(-1, "订单不存在或已删除"), "", "ajax");
    }
    if (2 < $order["order_type"]) {
        imessage(error(-1, "外卖订单才可以部分商品退款"), "", "ajax");
    }
    if ($order["status"] == 5) {
        imessage(error(-1, "订单已完成，不能部分退款"), "", "ajax");
    }
    if ($order["status"] == 6) {
        imessage(error(-1, "订单已取消，不能部分退款"), "", "ajax");
    }
    $type = trim($_GPC["type"]);
    if ($type == "agree") {
        $refund["data"] = iunserializer($refund["data"]);
        $calculate = order_goods_cancel_calculate($order_id, 0, "", array("refund_part" => $refund["data"]["refund_goods"]), array("is_submit" => 1));
        $result = order_refund_status_update($order_id, $refund_id, "handle");
        if (is_error($result)) {
            imessage($result, "", "ajax");
        }
        $data = $order["data"];
        if (empty($data["part_refund"])) {
            $data["part_refund"] = array("refund_total_fee" => 0);
        }
        $data["part_refund"]["refund_total_fee"] += $calculate["refund_total_fee"];
        $update_order = array("store_final_fee" => $order["store_final_fee"] - $calculate["refund_total_fee"], "data" => iserializer($data), "refund_fee" => $order["refund_fee"] + $calculate["refund_total_fee"]);
        pdo_update("tiny_wmall_order", $update_order, array("uniacid" => $_W["uniacid"], "id" => $order["id"]));
        imessage($result, "", "ajax");
    } else {
        pdo_update("tiny_wmall_order_refund", array("status" => 6), array("uniacid" => $_W["uniacid"], "id" => $refund_id));
        order_insert_refund_log($order_id, $refund_id, "rejected", "商户拒绝部分商品退款");
        imessage(error(0, "拒绝退订部分商品成功"), "", "ajax");
    }
}
if ($op == "analyse") {
    $id = intval($_GPC["id"]);
    $deliveryers = order_dispatch_analyse($id, array("channel" => "plateform_dispatch", "sort" => $_W["we7_wmall"]["config"]["takeout"]["order"]["dispatch_sort"]));
    if (is_error($deliveryers)) {
        imessage($deliveryers, "", "ajax");
    }
    imessage(error(0, $deliveryers), "", "ajax");
}
if ($op == "dispatch") {
    $order_id = intval($_GPC["order_id"]);
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    $status = order_assign_deliveryer($order_id, $deliveryer_id, true, "本订单由平台管理员调度分配，请尽快处理");
    if (is_error($status)) {
        imessage($status, "", "ajax");
    }
    imessage(error(0, "分配订单成功"), "", "ajax");
}
if ($op == "print") {
    $order_id = intval($_GPC["id"]);
    $result = order_print($order_id);
    if (is_error($result)) {
        imessage(error(-1, $result["message"]), "", "ajax");
    }
    imessage(error(0, "订单打印成功"), "", "ajax");
}
if ($op == "export") {
    load()->model("mc");
    mload()->model("deliveryer");
    $stores = store_fetchall(array("id", "title"));
    $pay_types = order_pay_types();
    $order_status = order_status();
    $deliveryers = deliveryer_all(true);
    $filter_type = trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process";
    $condition = " WHERE uniacid = :uniacid and order_type < 3";
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
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if (0 < $deliveryer_id) {
        $condition .= " AND deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
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
    $re_status = intval($_GPC["refund_status"]);
    if (0 < $re_status) {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
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
    $status = intval($_GPC["status"]);
    if (0 < $status) {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }
    $order_plateform = trim($_GPC["order_plateform"]);
    if (!empty($order_plateform)) {
        $condition .= " AND order_plateform = :order_plateform";
        $params[":order_plateform"] = $order_plateform;
    }
    $order_channel = trim($_GPC["order_channel"]);
    if (!empty($order_channel)) {
        $condition .= " AND order_channel = :order_channel";
        $params[":order_channel"] = $order_channel;
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
    $order_fields = array("id" => array("field" => "id", "title" => "订单ID", "width" => "10"), "ordersn" => array("field" => "ordersn", "title" => "订单编号", "width" => "30"), "uid" => array("field" => "uid", "title" => "下单人UID", "width" => "10"), "openid" => array("field" => "openid", "title" => "粉丝openid", "width" => "40"), "sid" => array("field" => "sid", "title" => "下单门店", "width" => "15"), "username" => array("field" => "username", "title" => "收货人", "width" => "15"), "mobile" => array("field" => "mobile", "title" => "手机号" , "width" => "20"), "address" => array("field" => "address", "title" => "收货地址", "width" => "40"), "pay_type" => array("field" => "pay_type", "title" => "支付方式", "width" => "15"), "num" => array("field" => "num", "title" => "份数", "width" => "10"), "price" => array("field" => "price", "title" => "商品费用", "width" => "10"), "box_price" => array("field" => "box_price", "title" => "餐盒费", "width" => "10"), "pack_fee" => array("field" => "pack_fee", "title" => "包装费", "width" => "10"), "delivery_fee" => array("field" => "delivery_fee", "title" => "配送费", "width" => "10"), "extra_fee" => array("field" => "extra_fee", "title" => "附加费", "width" => "15"), "total_fee" => array("field" => "total_fee", "title" => "总价", "width" => "15"), "discount_fee" => array("field" => "discount_fee", "title" => "优惠金额", "width" => "15"), "store_discount_fee" => array("field" => "store_discount_fee", "title" => "商户承担金额", "width" => "15"), "agent_discount_fee" => array("field" => "agent_discount_fee", "title" => "代理商承担金额", "width" => "15"), "plateform_discount_fee" => array("field" => "plateform_discount_fee", "title" => "平台承担金额", "width" => "15"), "final_fee" => array("field" => "final_fee", "title" => "优惠后价格", "width" => "15"), "addtime" => array("field" => "addtime", "title" => "下单时间", "width" => "25"), "out_trade_no" => array("field" => "out_trade_no", "title" => "本平台支付单价", "width" => "25"), "transaction_id" => array("field" => "transaction_id", "title" => "第三方支付单价", "width" => "25"), "status" => array("field" => "status", "title" => "订单状态", "width" => "25"), "status_cn" => array("field" => "status_cn", "title" => "订单最新进度", "width" => "25"), "deliveryer_id" => array("field" => "deliveryer_id", "title" => "配送员", "width" => "25"), "goods" => array("field" => "goods", "title" => "商品信息", "width" => "100"), "delivery_assign_time" => array("field" => "delivery_assign_time", "title" => "最后抢单时间", "width" => "25"), "endtime" => array("field" => "endtime", "title" => "订单完成时间", "width" => "25"), "distance" => array("field" => "distance", "title" => "配送距离", "width" => "25"));
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
            if (0 < $row["distance"]) {
                $row["distance"] = $row["distance"] . "km";
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
} else {
    if ($op == "changeOrder") {
        $id = intval($_GPC["id"]);
        $order = pdo_get("tiny_wmall_order", array("id" => $id), array("username", "mobile", "address", "serial_sn", "note"));
        if ($_W["ispost"] && intval($_GPC["set"]) == 1) {
            $id = intval($_GPC["id"]);
            pdo_update("tiny_wmall_order", array("username" => trim($_GPC["username"]), "mobile" => trim($_GPC["mobile"]), "address" => trim($_GPC["address"]), "note" => trim($_GPC["note"])), array("id" => $id));
            imessage(error(0, "修改成功"), referer(), "ajax");
        }
        include itemplate("order/takeoutOp");
    }
    if ($op == "customer") {
        $id = intval($_GPC["id"]);
        if ($_W["ispost"]) {
            $communicate = trim($_GPC["communicate"]);
            if (empty($communicate)) {
                imessage(error(-1, "请填写发送的消息"), "", "ajax");
            }
            $status = order_status_notice($id, "communicate", $communicate);
            if (is_error($status)) {
                imessage(error(-1, $status["message"]), iurl("order/takeout/list"), "ajax");
            }
            imessage(error(0, "发送消息成功"), iurl("order/takeout/list"), "ajax");
        }
        include itemplate("order/takeoutOp");
    }
    if ($op == "push_uupaotui") {
        $id = intval($_GPC["id"]);
        $push = intval($_GPC["push"]) ? true : false;
        if ($_W["ispost"]) {
            $status = order_push_uupaotui($id, $push);
            if (is_error($status)) {
                imessage($status, referer(), "ajax");
            }
            if (!$push) {
                $result = array("tips" => "UU跑腿需要配送费" . $status["need_paymoney"] . "元，确定推送到UU跑腿吗？", "id" => $id);
            } else {
                $result = "订单推送到UU跑腿成功";
            }
            imessage(error(0, $result), referer(), "ajax");
        }
    }
    if ($op == "zhunshibao_status") {
        $id = intval($_GPC["id"]);
        $status = intval($_GPC["status"]);
        if ($_W["ispost"]) {
            mload()->model("plugin");
            pload()->model("zhunshibao");
            $result = zhunshibao_update_status($id, $status);
            imessage($result, referer(), "ajax");
        }
    }
    if ($op == "refund") {
        $id = intval($_GPC["id"]);
        $order = order_fetch($id);
        if (empty($order)) {
            imessage(error(-1, "订单不存在或已删除"), "", "ajax");
        }
        if (2 < $order["order_type"]) {
            imessage(error(-1, "外卖订单才可以部分商品退款"), "", "ajax");
        }
        if ($order["status"] == 1) {
            imessage(error(-1, "未接单，不能部分退款"), "", "ajax");
        }
        if ($order["status"] == 6) {
            imessage(error(-1, "订单已取消，不能部分退款"), "", "ajax");
        }
        $reasons = order_cancel_types("manager");
        if ($_W["ispost"]) {
            $refund_data = $_GPC["refund_data"];
            if (empty($refund_data["refund_part"])) {
                imessage(error(-1, "请选择退货商品"), "", "ajax");
            }
            if (empty($refund_data["refund_total_fee"])) {
                imessage(error(-1, "请选择退货商品"), "", "ajax");
            }
            $reason_key = trim($_GPC["reason"]);
            $reason = $reasons[$reason_key];
            if (empty($reason)) {
                imessage(error(-1, "原因不能为空"), "", "ajax");
            }
            $calculate = order_goods_cancel_calculate($order, 0, "", $refund_data, array("is_submit" => 1));
            if (is_error($calculate)) {
                imessage($calculate, "", "ajax");
            }
            $insert = array("uniacid" => $order["uniacid"], "acid" => $order["acid"], "sid" => $order["sid"], "uid" => $order["uid"], "order_id" => $order["id"], "order_sn" => $order["ordersn"], "order_channel" => $order["order_channel"], "pay_type" => $order["pay_type"], "fee" => $calculate["refund_total_fee"], "total_fee" => $order["data"]["final_fee_pay"], "status" => 1, "out_trade_no" => $order["out_trade_no"], "out_refund_no" => date("YmdHis") . random(10, true), "apply_time" => TIMESTAMP, "reason" => "部分商品退款" . $reason, "data" => iserializer(array("refund_info" => array("title" => "商户发起部分退款", "reason" => $reason), "refund_goods" => $calculate["refund_part"])), "type" => "goods");
            pdo_insert("tiny_wmall_order_refund", $insert);
            $refund_id = pdo_insertid();
            order_part_refund_handle($order, $refund_id, array("refund_fee" => $calculate["refund_total_fee"]));
            mlog(1001, $refund_id);
            imessage(error(0, "部分商品退订成功"), "", "ajax");
        }
        $order["goods"] = order_fetch_goods($id);
        include itemplate("order/takeoutOp");
    }
    if ($op == "refund_calculate") {
        $id = intval($_GPC["id"]);
        $stat_id = intval($_GPC["stat_id"]);
        $sign = trim($_GPC["sign"]);
        $refund_data = $_GPC["refund_data"];
        $refund_data = order_goods_cancel_calculate($id, $stat_id, $sign, $refund_data);
        if (is_error($refund_data)) {
            imessage($refund_data, "", "ajax");
        }
        $result = array("refund_data" => $refund_data);
        imessage(error(0, $result), "", "ajax");
    }
    if ($op == "push_shansong") {
        $id = intval($_GPC["id"]);
        $push = intval($_GPC["push"]) ? true : false;
        if ($_W["ispost"]) {
            $status = order_push_shansong($id, $push);
            if (is_error($status)) {
                imessage($status, referer(), "ajax");
            }
            if (!$push) {
                $result = array("tips" => "闪送配送需要配送费" . $status["amount"] . "元，确定推送闪送吗?", "id" => $id);
            } else {
                $result = "订单推送到闪送成功";
            }
            imessage(error(0, $result), referer(), "ajax");
        }
    }
    if ($op == "push_dianwoda") {
        $id = intval($_GPC["id"]);
        $push = intval($_GPC["push"]) ? true : false;
        if ($_W["ispost"]) {
            $status = order_push_dianwoda($id, $push);
            if (is_error($status)) {
                imessage($status, referer(), "ajax");
            }
            $fee = floatval($status["total_price"] / 100);
            if (!$push) {
                $result = array("tips" => "点我达配送需要配送费" . $fee . "元，确定推送点我达吗？", "id" => $id);
            } else {
                $result = "订单成功推送到点我达";
            }
            imessage(error(0, $result), referer(), "ajax");
        }
    }
}

?>