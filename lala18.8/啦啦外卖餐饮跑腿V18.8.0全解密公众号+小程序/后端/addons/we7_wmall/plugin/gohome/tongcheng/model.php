<?php


defined("IN_IA") or exit("Access Denied");
pload()->model("gohome");
function tongcheng_get_categorys($filter = array(), $field = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($filter["agentid"]) ? intval($filter["agentid"]) : $_W["agentid"];
    if (!empty($agentid)) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $type = empty($filter["type"]) ? "parent_child" : trim($filter["type"]);
    if ($type == "parent") {
        $condition .= " and parentid = 0";
    }
    $field_str = "*";
    if (!empty($field)) {
        $field_str = implode(",", $field);
    }
    $categorys = pdo_fetchall("select " . $field_str . " from " . tablename("tiny_wmall_tongcheng_category") . $condition . " order by displayorder desc", $params, "id");
    if (!empty($categorys)) {
        foreach ($categorys as &$val) {
            if (isset($val["thumb"])) {
                $val["thumb"] = tomedia($val["thumb"]);
            }
            if (isset($val["tags"])) {
                $val["tags"] = iunserializer($val["tags"]);
            }
            if (empty($val["link"])) {
                $val["link"] = "/gohome/pages/tongcheng/category?id=" . $val["id"];
            }
            if (isset($val["config"])) {
                $val["config"] = iunserializer($val["config"]);
            }
            if ($type == "parent_child") {
                if (!empty($val["parentid"])) {
                    $categorys[$val["parentid"]]["child"][] = $val;
                    unset($categorys[$val["id"]]);
                }
            } else {
                if ($type == "parent&child") {
                    $val["name"] = $val["title"];
                    if (empty($val["parentid"])) {
                        $parent[$val["id"]] = $val;
                    } else {
                        $child[$val["parentid"]][$val["id"]] = $val;
                    }
                }
            }
        }
        if ($type == "parent&child") {
            unset($categorys);
            $categorys = array("parent" => $parent, "child" => $child);
        }
    }
    return $categorys;
}
function tongcheng_get_category($id, $field = array())
{
    global $_W;
    $condition = " where uniacid = :uniacid and id = :id";
    $params = array(":uniacid" => $_W["uniacid"], ":id" => $id);
    $field_str = "*";
    if (!empty($field)) {
        $field_str = implode(",", $field);
    }
    $category = pdo_fetch("select " . $field_str . " from " . tablename("tiny_wmall_tongcheng_category") . $condition, $params);
    if (!empty($category)) {
        $category["thumb"] = tomedia($category["thumb"]);
        $category["tags"] = iunserializer($category["tags"]);
        if (empty($category["link"])) {
            $category["link"] = "/gohome/pages/tongcheng/category?id=" . $category["id"];
        }
        if (isset($category["config"])) {
            $category["config"] = iunserializer($category["config"]);
        }
    }
    return $category;
}
function tongcheng_get_informations($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($filter["agentid"]) ? intval($filter["agentid"]) : $_W["agentid"];
    if (!empty($agentid)) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $cid = intval($filter["parentid"]);
    if (0 < $cid) {
        $condition .= " and a.parentid = :parentid";
        $params[":parentid"] = $cid;
    }
    $childid = intval($filter["childid"]);
    if (0 < $childid) {
        $condition .= " and a.childid = :childid";
        $params[":childid"] = $childid;
    }
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid";
        $params[":uid"] = $uid;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : 3;
    if (0 < $status) {
        $condition .= " and a.status = :status";
        $params[":status"] = $status;
    }
    $is_stick = isset($filter["is_stick"]) ? intval($filter["is_stick"]) : "-1";
    if (-1 < $is_stick) {
        $condition .= " and a.is_stick = :is_stick";
        $params[":is_stick"] = $is_stick;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.mobile like :keyword or a.nickname like :keyword or a.content like :keyword)";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND a.addtime > :start AND a.addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $orderby = " order by a.is_stick desc, a.id desc";
    if (0 < $cid) {
        $categorys = tongcheng_get_category($cid, array("id", "title", "thumb", "config"));
        if ($filter["orderby"] != "addtime" && in_array($categorys["config"]["orderby"], array("looknum", "likenum", "sharenum"))) {
            $orderby = " order by a.is_stick desc, a." . $categorys["config"]["orderby"] . " desc, a.id desc";
        }
    }
    $page = empty($filter["page"]) ? intval($_GPC["page"]) : intval($filter["page"]);
    $psize = empty($filter["psize"]) ? intval($_GPC["psize"]) : intval($filter["psize"]);
    $page = max(1, $page);
    $psize = $psize ? $psize : 10;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_tongcheng_information") . " as a" . $condition, $params);
    $informations = pdo_fetchall("select a.*, b.realname as ft_realname, b.mobile as ft_mobile, b.avatar as ft_avatar from " . tablename("tiny_wmall_tongcheng_information") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.uid =b.uid" . $condition . $orderby . " limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($informations)) {
        if (empty($cid)) {
            $categorys = tongcheng_get_categorys(array("type" => "all"), array("id", "title", "thumb"));
        }
        $all_status = tongcheng_information_status();
        foreach ($informations as &$val) {
            if (!empty($val["thumbs"])) {
                $val["thumbs"] = iunserializer($val["thumbs"]);
                foreach ($val["thumbs"] as &$thumb) {
                    $thumb = tomedia($thumb);
                }
            }
            $val["keyword"] = iunserializer($val["keyword"]);
            $cid = $val["childid"] ? $val["childid"] : $val["parentid"];
            $val["category"] = $categorys[$cid] ? $categorys[$cid] : $categorys;
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $val["status_all"] = $all_status[$val["status"]];
            $val["showall"] = false;
            $val["content_vue"] = nl2br($val["content"]);
            $val["content_length"] = istrlen($val["content"]);
            $br_length = substr_count($val["content_vue"], "<br />");
            if (2 < $br_length) {
                $val["content_length"] = 45;
            }
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("informations" => $informations, "total" => $total, "pager" => $pager);
}
function tongcheng_get_orders($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($filter["agentid"]) ? intval($filter["agentid"]) : $_W["agentid"];
    if (0 < $agentid) {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $type = isset($_GPC["type"]) ? intval($_GPC["type"]) : -1;
    if (0 <= $type) {
        $condition .= " and a.type = :type";
        $params[":type"] = $type;
    }
    $tid = intval($filter["tid"]);
    if (0 < $tid) {
        $condition .= " and a.tid = :tid";
        $params[":tid"] = $tid;
    }
    $uid = intval($filter["uid"]);
    if (0 < $uid) {
        $condition .= " and a.uid = :uid";
        $params[":uid"] = $uid;
    }
    $is_pay = isset($filter["is_pay"]) ? intval($filter["is_pay"]) : "-1";
    if (-1 < $is_pay) {
        $condition .= " and a.is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }
    if (!empty($filter["starttime"]) && !empty($filter["endtime"])) {
        $condition .= " AND a.addtime > :start AND a.addtime < :end";
        $params[":start"] = $filter["starttime"];
        $params[":end"] = $filter["endtime"];
    }
    $page = empty($filter["page"]) ? intval($_GPC["page"]) : intval($filter["page"]);
    $psize = empty($filter["psize"]) ? intval($_GPC["psize"]) : intval($filter["psize"]);
    $page = max(1, $page);
    $psize = $psize ? $psize : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_tongcheng_order") . " as a" . $condition, $params);
    $orders = pdo_fetchall("select a.*, b.realname as ft_realname, b.mobile as ft_mobile, b.avatar as ft_avatar from " . tablename("tiny_wmall_tongcheng_order") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.uid = b.uid" . $condition . " limit " . ($page - 1) * $psize . "," . $psize, $params);
    $pay_types = order_pay_types();
    if (!empty($orders)) {
        foreach ($orders as &$val) {
            $val["pay_type_cn"] = $pay_types[$val["pay_type"]];
        }
    }
    $pager = pagination($total, $page, $psize);
    return array("orders" => $orders, "total" => $total, "pager" => $pager);
}
function tongcheng_get_information($id, $filter = array())
{
    global $_W;
    global $_GPC;
    $condition = " where a.uniacid = :uniacid and a.id = :id";
    $params = array(":uniacid" => $_W["uniacid"], ":id" => $id);
    $information = pdo_fetch("select a.*, b.realname as ft_realname, b.mobile as ft_mobile, b.avatar as ft_avatar from " . tablename("tiny_wmall_tongcheng_information") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.uid =b.uid" . $condition, $params);
    if (!empty($information)) {
        if (!empty($information["thumbs"])) {
            $information["thumbs"] = iunserializer($information["thumbs"]);
            foreach ($information["thumbs"] as &$thumb) {
                $thumb = tomedia($thumb);
            }
        }
        if ($filter["like_member_show"] == 1) {
            $information["like_uid"] = iunserializer($information["like_uid"]);
            if (!empty($information["like_uid"])) {
                $like_uids = implode(",", $information["like_uid"]);
                $like_members = pdo_fetchall("select avatar from" . tablename("tiny_wmall_members") . " where uniacid = :uniacid and uid in (" . $like_uids . ")", array(":uniacid" => $_W["uniacid"]));
                foreach ($like_members as $avatar) {
                    $information["like_avatar"][] = tomedia($avatar["avatar"]);
                }
            }
        }
        $information["keyword"] = iunserializer($information["keyword"]);
        $cid = $information["childid"] ? $information["childid"] : $information["parentid"];
        $information["category"] = tongcheng_get_category($cid);
        $information["addtime_cn"] = date("Y-m-d H:i", $information["addtime"]);
        $information["status_all"] = tongcheng_information_status($information["status"]);
        $information["content_share"] = $information["content"];
        $information["content"] = nl2br($information["content"]);
    }
    return $information;
}
function tongcheng_get_comments($id)
{
    global $_W;
    $comments = pdo_getall("tiny_wmall_tongcheng_comment", array("uniacid" => $_W["uniacid"], "tid" => $id));
    if (!empty($comments)) {
        foreach ($comments as &$val) {
            $val["avatar"] = tomedia($val["avatar"]);
            $val["addtime_cn"] = date("Y-m-d H:i", $val["addtime"]);
            $replys = pdo_getall("tiny_wmall_tongcheng_reply", array("uniacid" => $_W["uniacid"], "cid" => $val["id"]));
            if (!empty($replys)) {
                foreach ($replys as &$v) {
                    $v["from_avatar"] = tomedia($v["from_avatar"]);
                    $v["to_avatar"] = tomedia($v["to_avatar"]);
                    $v["addtime_cn"] = date("Y-m-d H:i", $v["addtime"]);
                }
            }
            $val["reply"] = $replys;
        }
    }
    return $comments;
}
function tongcheng_information_status($type = "", $key = "all")
{
    $data = array("1" => array("text" => "待付款", "css" => "label label-warning"), "2" => array("text" => "待审核", "css" => "label label-warning"), "3" => array("text" => "显示中", "css" => "label label-success"), "4" => array("text" => "未通过", "css" => "label label-danger"));
    if (empty($type)) {
        return $data;
    }
    if ($key == "all") {
        return $data[$type];
    }
    if ($key == "text") {
        return $data[$type]["text"];
    }
    if ($key == "css") {
        return $data[$type]["css"];
    }
}
function tongcheng_information_publish_calculate($categoryOrId, $condition)
{
    global $_W;
    $category = $categoryOrId;
    if (!is_array($category)) {
        $category = pdo_get("tiny_wmall_tongcheng_category", array("uniacid" => $_W["uniacid"], "id" => $category));
    }
    if (!empty($category)) {
        $price = floatval($category["price"]);
        if (0 < $condition["information_id"]) {
            $price = 0;
        }
        $stick_is_available = 0;
        $category["config"] = iunserializer($category["config"]);
        if (!empty($category["config"]["stick_price"])) {
            $stick_num_limit = $_W["_plugin"]["config"]["tongcheng"]["stick_num"];
            if (0 < $stick_num_limit) {
                $stick_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_tongcheng_information") . " where uniacid = :uniacid and is_stick = 1", array(":uniacid" => $_W["uniacid"]));
                if ($stick_num < $stick_num_limit) {
                    $stick_is_available = 1;
                }
            } else {
                $stick_is_available = 1;
            }
        }
        $stick_fee = 0;
        if ($stick_is_available == 1) {
            $day = intval($condition["days"]);
            if (0 < $day) {
                $stick_fee = floatval($category["config"]["stick_price"][$day]["price"]);
            }
        }
        $result = array("price" => $price, "stick_price" => $stick_fee, "is_stick" => 0 < $stick_fee ? 1 : 0, "days" => $day, "stick_is_available" => $stick_is_available);
        $result["final_fee"] = $result["price"] + $result["stick_price"];
        return $result;
    }
    return false;
}
function tongcheng_information_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = pdo_get("tiny_wmall_tongcheng_order", array("uniacid" => $_W["uniacid"], "id" => $order));
    }
    if (empty($order)) {
        return error(-1, "订单不存在！");
    }
    if ($type == "pay") {
        if ($order["is_pay"] == 1) {
            return error(-1, "订单已支付，请勿重复支付");
        }
        $update = array("is_pay" => 1, "order_channel" => $extra["channel"], "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP, "transaction_id" => $extra["transaction_id"], "out_trade_no" => $extra["uniontid"]);
        if (0 < $order["stick_price"]) {
            $update["endtime"] = $update["paytime"] + $order["days"] * 86400;
        }
        pdo_update("tiny_wmall_tongcheng_order", $update, array("uniacid" => $order["uniacid"], "id" => $order["id"]));
        $information = pdo_get("tiny_wmall_tongcheng_information", array("uniacid" => $order["uniacid"], "id" => $order["tid"]), array("id", "is_stick", "status", "overtime", "edit_status"));
        if ($order["type"] == 0 || $order["type"] == 1) {
            $audit_status = get_plugin_config("gohome.tongcheng.audit");
            if ($information["edit_status"] == 0) {
                $information_status = $audit_status["new"] == 1 ? 2 : 3;
            } else {
                if ($information["edit_status"] == 1) {
                    if ($audit_status["edit"] == 1) {
                        $information_status = 3;
                    } else {
                        if ($audit_status == 2) {
                            $information_status = 2;
                        } else {
                            $information_status = $information["status"];
                        }
                    }
                }
            }
            $information_update = array("status" => $information_status);
        }
        if (0 < $order["stick_price"]) {
            $stick_starttime = max(TIMESTAMP, $information["overtime"]);
            $information_update["overtime"] = $order["days"] * 86400 + $stick_starttime;
            $information_update["is_stick"] = 1;
        }
        pdo_update("tiny_wmall_tongcheng_information", $information_update, array("uniacid" => $order["uniacid"], "id" => $order["tid"]));
        if (0 < $order["agentid"] && $order["agent_final_fee"]) {
            $remark = "同城发帖入账";
            if ($order["type"] == 2) {
                $remark = "同城帖子置顶入账";
            }
            mload()->model("agent");
            agent_update_account($order["agentid"], $order["agent_final_fee"], 10, $order["id"], $remark, "tongcheng");
        }
        return error(0, "支付成功");
    }
}
function tongcheng_information_update_status($ids, $status)
{
    global $_W;
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        pdo_update("tiny_wmall_tongcheng_information", array("status" => intval($status)), array("uniacid" => $_W["uniacid"], "id" => intval($id)));
    }
    return error(0, "设置成功");
}
function tongcheng_information_delete($ids, $type = "information")
{
    global $_W;
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $id) {
        if ($type == "information") {
            pdo_delete("tiny_wmall_tongcheng_information", array("uniacid" => $_W["uniacid"], "id" => $id));
            pdo_delete("tiny_wmall_tongcheng_comment", array("uniacid" => $_W["uniacid"], "tid" => $id));
            pdo_delete("tiny_wmall_tongcheng_reply", array("uniacid" => $_W["uniacid"], "tid" => $id));
        }
    }
    return error(0, "删除成功");
}
function tongcheng_can_publish_information()
{
    global $_W;
    $config_tongcheng = $_W["_plugin"]["config"]["tongcheng"];
    $total_limit = $config_tongcheng["limit_num"]["total_num"];
    $day_limit = $config_tongcheng["limit_num"]["day_num"];
    if (empty($total_limit) && empty($day_limit)) {
        return true;
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and uid = :uid and status = 3";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":uid" => $_W["member"]["uid"]);
    if (0 < $day_limit) {
        $condition .= " and addtime > :starttime and addtime < :endtime";
        $params[":starttime"] = strtotime(date("Y-m-d", TIMESTAMP));
        $params[":endtime"] = $params[":starttime"] + 86399;
        $day_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_tongcheng_information") . $condition, $params);
        if ($day_limit <= $day_num) {
            return error(-1, "今日发帖已超过最大限制");
        }
    }
    if (0 < $total_limit) {
        $total_num = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_tongcheng_information") . $condition, $params);
        if ($total_limit <= $total_num) {
            return error(-1, "发帖数已超过最大限制，请删除无用帖子");
        }
    }
    return true;
}
function tongcheng_information_stick_sync()
{
    global $_W;
    pdo_query("update " . tablename("tiny_wmall_tongcheng_information") . " set is_stick = 0 where uniacid = :uniacid and is_stick = 1 and overtime < :endtime", array(":uniacid" => $_W["uniacid"], ":endtime" => TIMESTAMP));
    return true;
}
function tongcheng_cron()
{
    global $_W;
    $key = "we7_wmall:" . $_W["uniacid"] . ":tongcheng:lock:120";
    if (check_cache_status($key, 120)) {
        return true;
    }
    tongcheng_information_stick_sync();
    set_cache($key, array());
    return true;
}
function tongcheng_flow_update($type = "")
{
    global $_W;
    $config = $_W["_plugin"]["config"]["tongcheng"];
    if (empty($config)) {
        if (0 < $_W["agentid"]) {
            $config = get_agent_plugin_config("gohome.tongcheng");
        } else {
            $config = get_plugin_config("gohome.tongcheng");
        }
    }
    if (in_array($type, array("falselooknum", "falsefabunum", "falselikenum"))) {
        $add_num = 1;
        if ($type == "falselooknum" && 0 < $config["minup"] && $config["minup"] <= $config["maxup"]) {
            $add_num = rand($config["minup"], $config["maxup"]);
        }
        $config[$type] = $config[$type] + $add_num;
        if (0 < $_W["agentid"]) {
            set_agent_plugin_config("gohome.tongcheng", $config);
        } else {
            set_plugin_config("gohome.tongcheng", $config);
        }
    }
    return array("falselooknum" => intval($config["falselooknum"]), "falsefabunum" => intval($config["falsefabunum"]), "falselikenum" => intval($config["falselikenum"]));
}
function tongcheng_tiezi_order_bill($order)
{
    if (empty($order)) {
        return false;
    }
    if (0 < $order["agentid"]) {
        $type = "tiezi";
        if ($order["type"] == 2) {
            $type = "tiezi_stick";
        }
        mload()->model("agent");
        $account_agent = get_agent($order["agentid"], "fee");
        $agent_fee_config = $account_agent["fee"]["fee_gohome"];
        if (empty($agent_fee_config[$type])) {
            $account_agent = get_plugin_config("agent.serve_fee");
            $agent_fee_config = $account_agent["fee_gohome"];
        }
        $agent_fee_config = $agent_fee_config[$type];
        if ($agent_fee_config["type"] == 2) {
            $agent_serve_fee = floatval($agent_fee_config["fee"]);
            $agent_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $agent_serve_fee, "note" => "固定抽成" . $agent_serve_fee . "元");
        } else {
            if ($agent_fee_config["type"] == 1) {
                $basic = 0;
                $note = array("yes" => array(), "no" => array());
                $fee_items = array("yes" => array("price" => "帖子费用", "stick_price" => "帖子置顶费用"), "no" => array());
                if (!empty($agent_fee_config["items_yes"])) {
                    foreach ($agent_fee_config["items_yes"] as $item) {
                        $basic += $order[$item];
                        $note["yes"][] = (string) $fee_items["yes"][$item] . "元" . $order[$item];
                    }
                }
                if (!empty($agent_fee_config["items_no"])) {
                    foreach ($agent_fee_config["items_no"] as $item) {
                        $basic -= $order[$item];
                        $note["no"][] = (string) $fee_items["no"][$item] . "元" . $order[$item];
                    }
                }
                if ($basic < 0) {
                    $basic = 0;
                }
                $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                $agent_serve_fee = round($basic * $agent_serve_rate / 100, 2);
                $text = "(" . implode(" + ", $note["yes"]);
                if (!empty($note["no"])) {
                    $text .= " - " . implode(" - ", $note["no"]);
                }
                $text .= ") x " . $agent_serve_rate . "%";
                if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                    $agent_serve_fee = $agent_fee_config["fee_min"];
                    $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                }
                $agent_serve = array("fee_type" => 1, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
            } else {
                if ($agent_fee_config["type"] == 3) {
                    $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                    $agent_serve_fee = round($order["final_fee"] * $agent_serve_rate / 100, 2);
                    $text = "本单代理佣金:" . $order["final_fee"] . " x " . $agent_serve_rate . "%";
                    if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                        $agent_serve_fee = $agent_fee_config["fee_min"];
                        $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                    }
                    $agent_serve = array("fee_type" => 3, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
                }
            }
        }
        $agent_final_fee = $order["final_fee"] - $agent_serve_fee;
        $agent_serve["final"] = "(代理商抽取佣金比例" . $order["final_fee"] . " - 平台服务佣金比例" . $agent_serve_fee . ")";
        $order["agent_final_fee"] = $agent_final_fee;
        $order["agent_serve"] = iserializer($agent_serve);
        $order["agent_serve_fee"] = $agent_serve_fee;
    }
    return $order;
}
function tongcheng_tiezi_notice($tieziOrId, $type, $extra = array())
{
    global $_W;
    $types = array("like", "comment", "reply");
    if (!in_array($type, $types)) {
        return error(-1, "参数错误");
    }
    $tiezi = $tieziOrId;
    if (!is_array($tiezi)) {
        $tiezi = tongcheng_get_information($tiezi);
    }
    if (empty($tiezi)) {
        return error(-1, "帖子不存在或已删");
    }
    $config_wxapp_basic = $_W["we7_wmall"]["config"]["wxapp"]["basic"];
    $channel = $tiezi["channel"];
    if ($config_wxapp_basic["wxapp_consumer_notice_channel"] == "wechat") {
        mload()->model("member");
        $openid = member_wxapp2openid($tiezi["openid"]);
        if (!empty($openid)) {
            $channel = "wap";
            $tiezi["openid"] = $openid;
        }
    }
    $acc = TyAccount::create($tiezi["uniacid"], $channel);
    if ($channel == "wap") {
        $first = array("like" => "您发表的帖子有新的点击", "comment" => "您发表的帖子有新的评价", "reply" => "您发表的帖子有新的评论回复");
        $params = array("first" => $first[$type], "keyword1" => $extra["nickname"], "keyword2" => date("Y-m-d H:i", $extra["addtime"]), "keyword3" => "审核通过");
        $extra["note"] = array("评论内容:" => $extra["content"], "帖子内容:" => $tiezi["content"]);
        if (!empty($extra["note"])) {
            $params["remark"] = implode("\n", $extra["note"]);
        }
        $url = ivurl("gohome/pages/tongcheng/detail", array("id" => $tiezi["id"]), true);
        $miniprogram = "";
        if ($config_wxapp_basic["tpl_consumer_url"] == "wxapp") {
            $miniprogram = array("appid" => $config_wxapp_basic["key"], "pagepath" => "gohome/pages/tongcheng/detail?id=" . $tiezi["id"]);
        }
        $send = sys_wechat_tpl_format($params);
        $status = $acc->sendTplNotice($tiezi["openid"], $_W["we7_wmall"]["config"]["notice"]["wechat"]["tiezi_tpl"], $send, $url, $miniprogram);
    } else {
        if ($channel == "wxapp") {
            $params = array("keyword1" => $extra["content"], "keyword2" => $extra["nickname"], "keyword3" => date("Y-m-d H:i", $extra["addtime"]), "keyword4" => $tiezi["content"]);
            $send = sys_wechat_tpl_format($params);
            $public_tpl = $_W["we7_wmall"]["config"]["wxapp"]["wxtemplate"]["tiezi_tpl"];
            $status = $acc->sendTplNotice($tiezi["openid"], $public_tpl, $send, "gohome/pages/tongcheng/detail?id=" . $tiezi["id"]);
        }
    }
    if (is_error($status)) {
        slog("wxtplNotice", "同城帖子点赞、评论、回复通知发帖，帖子id:" . $tiezi["id"], $send, $status["message"]);
    }
    return true;
}

?>

