<?php
defined("IN_IA") or exit("Access Denied");
function table_category_fetchall($sid)
{
    global $_W;
    $data = pdo_getall("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "sid" => $sid), array(), "id");
    return $data;
}
function table_fetchall($filter = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    if (!empty($filter["sid"])) {
        $condition .= " and sid = :sid";
        $params[":sid"] = $filter["sid"];
    }
    if (!empty($filter["cid"])) {
        $condition .= " and cid = :cid";
        $params[":cid"] = $filter["cid"];
    }
    if (!empty($filter["status"])) {
        $condition .= " and status = :status";
        $params[":status"] = $filter["status"];
    }
    $data = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_tables") . $condition, $params);
    return $data;
}
function table_status()
{
    $data = array(array(), array("css" => "label label-default", "css_block" => "block-gray", "text" => "空闲中"), array("css" => "label label-danger", "css_block" => "block-red", "text" => "已开台"), array("css" => "label label-primary", "css_block" => "block-primary", "text" => "已下单"), array("css" => "label label-success", "css_block" => "block-success", "text" => "已支付"));
    return $data;
}
function table_fetch($table_id)
{
    global $_W;
    $table = pdo_get("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "id" => $table_id));
    if (!empty($table)) {
        $store_table_config = store_get_data($table["sid"], "tangshi.table");
        if ($store_table_config["status_not_update"] == 1) {
            $table["status"] = 1;
        }
        $table["category"] = pdo_get("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "id" => $table["cid"]));
    }
    return $table;
}
function table_order_update($table_id, $order_id, $status)
{
    global $_W;
    $update = array("order_id" => $order_id, "status" => $status);
    if ($status == 3 || $status == 4) {
        $update["cart_id"] = 0;
    }
    $status = pdo_update("tiny_wmall_tables", $update, array("uniacid" => $_W["uniacid"], "id" => $table_id));
    return $status;
}
function assign_board_status()
{
    $data = array(array(), array("css" => "label label-primary", "text" => "排队中"), array("css" => "label label-success", "text" => "已入号"), array("css" => "label label-warning", "text" => "已过号"), array("css" => "label label-danger", "text" => "已取消"));
    return $data;
}
function assign_board_fetch($id)
{
    global $_W;
    $board = pdo_get("tiny_wmall_assign_board", array("uniacid" => $_W["uniacid"], "id" => $id));
    return $board;
}
function assign_queue_fetch($id)
{
    global $_W;
    $queue = pdo_get("tiny_wmall_assign_queue", array("uniacid" => $_W["uniacid"], "id" => $id));
    return $queue;
}
function assign_notice($sid, $id, $status)
{
    global $_W;
    $config = $_W["we7_wmall"]["config"];
    $result = error(-1, "通知参数错误");
    if (!empty($config["notice"]["wechat"]["assign_tpl"])) {
        $store = store_fetch($sid, array("id", "title"));
        $board = assign_board_fetch($id);
        if (empty($board)) {
            return false;
        }
        $queue = assign_queue_fetch($board["queue_id"]);
        if (empty($queue)) {
            return false;
        }
        $board_status = assign_board_status();
        $url = murl("entry", array("m" => "we7_wmall", "do" => "assign", "sid" => $sid, "op" => "goods"), true, true);
        $wait_count = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $board["queue_id"], ":id" => $board["id"]));
        $createtime = date("Y-m-d H:i", $board["createtime"]);
        if ($status == 1) {
            $first = "排号提醒：编号" . $board["number"] . "已成功领号，您可以点击本消息提前点菜，节约等待时间哦";
            $remark = array("门店名称：" . $store["title"], "排队号码：" . $queue["title"] . " " . $board["number"], "前面等待：" . $wait_count . "桌", "排队状态：排队中");
        } else {
            if ($status == 2) {
                $first = "排号入号提醒：编号" . $board["number"] . "已入号,请您立即前往迎宾台";
                $remark = array("门店名称：" . $store["title"], "排队号码：" . $queue["title"] . " " . $board["number"], "排队状态：已入号", "您在" . $store["title"] . "的的排队状态更新为已入号，请您立即前往迎宾台，如果疑问，请联系我们工作人员");
            } else {
                if ($status == 3) {
                    $first = "排号过号提醒：编号" . $board["number"] . "已过号";
                    $remark = array("门店名称：" . $store["title"], "排队号码：" . $queue["title"] . " " . $board["number"], "排队状态：已过号", "您在" . $store["title"] . "的的排队状态更新为已过号，如果疑问，请联系我们工作人员");
                } else {
                    if ($status == 4) {
                        $first = "排号取消提醒：编号" . $board["number"] . "已取消";
                        $remark = array("门店名称：" . $store["title"], "排队号码：" . $queue["title"] . " " . $board["number"], "排队状态：已取消", "您在" . $store["title"] . "的的排队状态更新为已取消，如果疑问，请联系我们工作人员");
                    } else {
                        if ($status == 5) {
                            $first = "排号提醒：还需等待" . $wait_count . "桌";
                            $remark = array("门店名称：" . $store["title"], "还需等待：" . $wait_count . "桌", "排队号码：" . $queue["title"] . " " . $board["number"], "排队状态：" . $board_status[$board["status"]]["text"]);
                        }
                    }
                }
            }
        }
        $remark = implode("\n", $remark);
        $send = array("first" => array("value" => $first, "color" => "#ff510"), "keyword1" => array("value" => $board["number"], "color" => "#ff510"), "keyword2" => array("value" => $createtime, "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
        $acc = WeAccount::create();
        $result = $acc->sendTplNotice($board["openid"], $config["notice"]["wechat"]["assign_tpl"], $send, $url);
        if (is_error($status)) {
            slog("wxtplNotice", "排号状态变动微信通知顾客", $send, $status["message"]);
        }
    }
    return $result;
}
function assign_notice_clerk($sid, $id)
{
    global $_W;
    $config = $_W["we7_wmall"]["config"];
    if (!empty($config["notice"]["wechat"]["assign_tpl"])) {
        $store = store_fetch($sid, array("id", "title"));
        $board = assign_board_fetch($id);
        if (empty($board)) {
            return false;
        }
        $queue = assign_queue_fetch($board["queue_id"]);
        if (empty($queue)) {
            return false;
        }
        mload()->model("clerk");
        $clerks = clerk_fetchall($sid);
        if (empty($clerks)) {
            return false;
        }
        $wait_count = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and status = 1 and id < :id and queue_id = :queue_id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":queue_id" => $board["queue_id"], ":id" => $board["id"]));
        $createtime = date("Y-m-d H:i", $board["createtime"]);
        if (empty($config["notice"]["wechat"]["assign_tpl"])) {
            return false;
        }
        $first = "排号提醒：有新的排号，编号" . $board["number"] . ".请登录后台进行处理";
        $remark = array("门店名称：" . $store["title"], "排队号码：" . $queue["title"] . " " . $board["number"], "还需等待：" . $wait_count . "桌");
        $remark = implode("\n", $remark);
        $send = array("first" => array("value" => $first, "color" => "#ff510"), "keyword1" => array("value" => $board["number"], "color" => "#ff510"), "keyword2" => array("value" => $createtime, "color" => "#ff510"), "remark" => array("value" => $remark, "color" => "#ff510"));
        $acc = WeAccount::create();
        foreach ($clerks as $clerk) {
            if (empty($clerk["openid"])) {
                continue;
            }
            $status = $acc->sendTplNotice($clerk["openid"], $config["notice"]["wechat"]["assign_tpl"], $send);
            if (is_error($status)) {
                slog("wxtplNotice", "新排号微信通知平台管理员", $send, $status["message"]);
            }
        }
    }
    return $status;
}
function assign_notice_queue($board_id, $queue_id)
{
    global $_W;
    $queue = assign_queue_fetch($queue_id);
    if (!empty($queue) && 0 < $queue["notify_num"]) {
        $boards = pdo_fetchall("select * from " . tablename("tiny_wmall_assign_board") . " where uniacid = :uniacid and sid = :sid and queue_id = :queue_id and status = 1 and id > :id limit " . $queue["notify_num"], array(":uniacid" => $_W["uniacid"], ":sid" => $queue["sid"], ":queue_id" => $queue_id, ":id" => $board_id));
        if (!empty($boards)) {
            foreach ($boards as $board) {
                if (!empty($board["openid"])) {
                    assign_notice($queue["sid"], $board["id"], 5);
                }
            }
        }
    }
}
function call_notice_clerk($id, $type = "tablecall", $extra = array())
{
    global $_W;
    $call = pdo_get("tiny_wmall_table_call_record", array("uniacid" => $_W["uniacid"], "id" => $id));
    if (empty($call)) {
        return error(-1, "呼叫记录不存在");
    }
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $call["sid"]), array("id", "title", "push_token"));
    if (empty($store)) {
        return error(-1, "店铺不存在或已删除");
    }
    $table = table_fetch($call["table_id"]);
    if (empty($table)) {
        return error(-1, "桌台不存在");
    }
    mload()->model("clerk");
    $clerks = clerk_fetchall($call["sid"]);
    if (empty($clerks)) {
        return error(-1, "商户未绑定店铺");
    }
    $title = "呼叫服务：桌号" . $table["title"] . "呼叫服务员.请尽快前去处理";
    $remark = array("桌　　号：" . $table["title"], "桌台类型：" . $table["category"]["title"], "呼叫时间：" . date("Y-m-d H:i", $call["addtime"]));
    $remark = implode("\n", $remark);
    $send = tpl_format($title, "", "", $remark);
    $acc = TyAccount::create();
    foreach ($clerks as $clerk) {
        if (empty($clerk["openid"])) {
            continue;
        }
        if ($clerk["extra"]["accept_wechat_notice"] == 1) {
            $status = $acc->sendTplNotice($clerk["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["public_tpl"], $send, "");
            if (is_error($status)) {
                slog("wxtplNotice", "顾客呼叫服务通知店员-" . $clerk["title"], $send, $status["message"]);
            }
        }
    }
    $audience = array("tag" => array($store["push_token"]));
    $data = Jpush_clerk_send("有顾客呼叫服务", $title, array("voice_text" => $title, "url" => "", "notify_type" => $type, "order_from" => "tangshi"), $audience);
    return true;
}
function table_order_jiacai($sid, $order_id)
{
    global $_W;
    mload()->model("order");
    $cart = order_check_member_cart($sid);
    if (is_error($cart)) {
        return $cart;
    }
    $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "id" => $order_id), array("id", "num", "order_type", "is_pay", "pay_type", "status", "total_fee", "final_fee", "store_final_fee", "data"));
    if (empty($order)) {
        return error(-1, "订单不存在");
    }
    if ($order["order_type"] != 3) {
        return error(-1, "不是店内单");
    }
    if (5 < $order["status"]) {
        return error(-1, "订单已完成或已取消");
    }
    if ($order["is_pay"] == 1 && $order["pay_type"] != "finishMeal") {
        return error(-1, "订单已支付，不能加菜");
    }
    $cart["goods_type"] = "jiacai";
    $jiacai_ids = order_update_goods_info($order_id, $sid, $cart);
    $jiacai_ids = implode(",", $jiacai_ids);
    $update = array("num" => $order["num"] + $cart["num"], "total_fee" => $order["total_fee"] + $cart["price"], "final_fee" => $order["final_fee"] + $cart["price"], "store_final_fee" => $order["store_final_fee"] + $cart["price"]);
    pdo_update("tiny_wmall_order", $update, array("uniacid" => $_W["uniacid"], "id" => $order_id));
    order_print($order_id, "jiacai", array("jiacai_ids" => $jiacai_ids));
    order_clerk_notice($order_id, "store_order_jiacai", "", array("jiacai_ids" => $jiacai_ids));
    return error(0, "加菜成功");
}

?>