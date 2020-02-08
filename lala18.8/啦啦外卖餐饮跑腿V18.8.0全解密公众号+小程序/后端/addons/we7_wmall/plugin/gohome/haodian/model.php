<?php
/*



 * @ 请勿传播
 */

defined("IN_IA") or exit("Access Denied");
pload()->model("gohome");
function haodian_category_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        if (!isset($filter["page"])) {
            $filter["page"] = $_GPC["page"];
        }
        if (!isset($filter["psize"])) {
            $filter["psize"] = $_GPC["psize"];
        }
    }
    $parentid = intval($filter["parentid"]);
    $condition = " where uniacid = :uniacid and parentid = :parentid ";
    $params = array(":uniacid" => $_W["uniacid"], ":parentid" => $parentid);
    $agentid = $_W["agentid"];
    if (isset($filter["agentid"])) {
        $agentid = intval($filter["agentid"]);
    }
    if (!empty($agentid)) {
        $condition .= " and agentid = :agentid ";
        $params[":agentid"] = $agentid;
    }
    $status = isset($filter["status"]) ? intval($filter["status"]) : -1;
    if (-1 < $status) {
        $condition .= " and status = :status";
        $params[":status"] = $status;
    }
    $pindex = max(1, intval($filter["page"]));
    $psize = 0 < intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    $data = array();
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_haodian_category") . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $data["pager"] = $pager;
    }
    $category = pdo_fetchall("select * from " . tablename("tiny_wmall_haodian_category") . $condition . " order by displayorder desc, id asc limit " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($category)) {
        foreach ($category as $key => &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            $val["child"] = pdo_fetchall("select * from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid and parentid = :parentid order by displayorder desc,id asc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":parentid" => $key));
        }
    }
    $data["category"] = $category;
    return $data;
}
function haodian_store_fetchall($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    } else {
        $filter = array_merge($_GPC, $filter);
    }
    $condition = " where uniacid = :uniacid and is_haodian = 1 ";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = $_W["agentid"];
    if (isset($filter["agentid"])) {
        $agentid = intval($filter["agentid"]);
    }
    if (!empty($agentid)) {
        $condition .= " and agentid = :agentid ";
        $params[":agentid"] = $agentid;
    }
    $haodian_status = isset($filter["haodian_status"]) ? intval($filter["haodian_status"]) : 1;
    if (0 < $haodian_status) {
        $condition .= " and haodian_status = :haodian_status ";
        $params[":haodian_status"] = $haodian_status;
    } else {
        $condition .= " and haodian_status < 7 ";
    }
    $haodian_cid = intval($filter["haodian_cid"]);
    if (0 < $haodian_cid) {
        $condition .= " and haodian_cid = :haodian_cid ";
        $params[":haodian_cid"] = $haodian_cid;
    }
    $haodian_child_id = intval($filter["haodian_child_id"]);
    if (0 < $haodian_cid && 0 < $haodian_child_id) {
        $condition .= " and haodian_child_id = :haodian_child_id ";
        $params[":haodian_child_id"] = $haodian_child_id;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (title like :keyword or id = '" . $keyword . "' ) ";
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $orderby = trim($filter["orderby"]);
    if (empty($orderby) && (defined("IN_WXAPP") || defined("IN_VUE"))) {
        $orderby = "distance";
    }
    if ($orderby == "distance") {
        $orderby = " order by distance asc, displayorder desc, id desc ";
    } else {
        if ($orderby == "new") {
            $orderby = " order by haodian_starttime desc, displayorder desc, id desc";
        } else {
            if ($orderby == "score") {
                $orderby = " order by haodian_score desc, displayorder desc, id desc";
            } else {
                if ($orderby == "click") {
                    $orderby = " order by click desc, displayorder desc, id desc";
                } else {
                    if (empty($orderby)) {
                        $orderby = " order by displayorder desc, id desc ";
                    }
                }
            }
        }
    }
    $lat = trim($filter["lat"]) ? trim($filter["lat"]) : "37.80081";
    $lng = trim($filter["lng"]) ? trim($filter["lng"]) : "112.57543";
    $pindex = max(1, intval($filter["page"]));
    $psize = 0 < intval($filter["psize"]) ? intval($filter["psize"]) : 15;
    if (!defined(base64_decode("SU5fV1hBUFA=")) && !defined(base64_decode("SU5fVlVF"))) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_store") . $condition, $params);
        $pager = pagination($total, $pindex, $psize);
        $data["pager"] = $pager;
    }
    $store = pdo_fetchall("select id,agentid,score,title,logo,content,sailed,score,label,delivery_type,serve_radius,not_in_serve_radius,delivery_areas,business_hours,is_in_business,is_rest,is_stick,delivery_fee_mode,delivery_price,delivery_free_price,send_price,delivery_time,delivery_mode,token_status,invoice_status,location_x,location_y,forward_mode,forward_url,displayorder,click, is_waimai, haodian_status, haodian_cid, haodian_child_id, haodian_starttime, haodian_endtime, haodian_score, haodian_data, deltime,\r\n ROUND(\r\n        6378.138 * 2 * ASIN(\r\n            SQRT(\r\n                POW(\r\n                    SIN(\r\n                        (\r\n                            " . $lat . " * 3.141593 / 180 - location_x * 3.141593 / 180\r\n                        ) / 2\r\n                    ),\r\n                    2\r\n                ) + COS(" . $lat . " * 3.141593 / 180) * COS(location_x * 3.141593 / 180) * POW(\r\n                    SIN(\r\n                        (\r\n                           " . $lng . "  * 3.141593 / 180 - location_y * 3.141593 / 180\r\n                        ) / 2\r\n                    ),\r\n                    2\r\n                )\r\n            )\r\n        ) * 1000) as distance from  " . tablename("tiny_wmall_store") . " " . $condition . " " . $orderby . " limit " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if (!empty($store)) {
        foreach ($store as $key => &$val) {
            $val["logo"] = tomedia($val["logo"]);
            $val["haodian_starttime_cn"] = date("Y-m-d H:i", $val["haodian_starttime"]);
            $val["haodian_endtime_cn"] = date("Y-m-d H:i", $val["haodian_endtime"]);
            $val["scores"] = score_format($val["score"]);
            $val["haodian_score"] = floatval($val["haodian_score"]);
            $val["business_hours"] = iunserializer($val["business_hours"]);
            $val["is_in_business_hours"] = intval($val["is_in_business"]);
            if (isset($val["business_hours"])) {
                if ($val["is_in_business"] == 1) {
                    $val["is_in_business_hours"] = $val["is_in_business_hours"] && store_is_in_business_hours($val["business_hours"]);
                }
                $hour = array();
                foreach ($val["business_hours"] as $li) {
                    if (!is_array($li)) {
                        continue;
                    }
                    $hour[] = (string) $li["s"] . "~" . $li["e"];
                }
                $val["business_hours_cn"] = implode(",", $hour);
            }
            $val["distance"] = round($val["distance"] / 1000, 1);
            $val["haodian_data"] = iunserializer($val["haodian_data"]);
            $val["haodian_tags"] = array();
            if (!empty($val["haodian_data"]["tags"])) {
                $val["haodian_tags"] = $val["haodian_data"]["tags"];
            }
            if ($filter["get_activity"] == 1) {
                $val["activity"] = array_values(haodian_get_activity($val["id"]));
            }
        }
    }
    $data["store"] = array_values($store);
    return $data;
}
function haodian_store_status($type, $key = "all")
{
    $data = array("1" => array("text" => "入驻中", "css" => "label label-success"), "2" => array("text" => "暂停中", "css" => "label label-warning"), "3" => array("text" => "已到期", "css" => "label label-warning"), "4" => array("text" => "未通过", "css" => "label label-default"), "5" => array("text" => "待审核", "css" => "label label-danger"), "6" => array("text" => "待入驻", "css" => "label label-info"));
    if ($type == -1) {
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
function haodian_new_settle_info()
{
    global $_W;
    $condition = " where uniacid = :uniacid and agentid = :agentid and haodian_status = 1 ";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]);
    $stores = pdo_fetchall("select id, title from " . tablename("tiny_wmall_store") . $condition . " order by id desc limit 10 ", $params);
    if (defined("IN_VUE")) {
        $stores = array_chunk($stores, 2);
    }
    return $stores;
}
function haodian_set_data($sid, $key, $value)
{
    global $_W;
    $data = haodian_get_data($sid);
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        $data[$keys[0]] = $value;
    } else {
        if ($counts == 2) {
            if (!is_array($data[$keys[0]])) {
                $data[$keys[0]] = array();
            }
            $data[$keys[0]][$keys[1]] = $value;
        } else {
            if ($counts == 3) {
                if (!is_array($data[$keys[0]])) {
                    $data[$keys[0]] = array();
                } else {
                    if (!is_array($data[$keys[0]][$keys[1]])) {
                        $data[$keys[0]][$keys[1]] = array();
                    }
                }
                $data[$keys[0]][$keys[1]][$keys[2]] = $value;
            }
        }
    }
    pdo_update("tiny_wmall_store", array("haodian_data" => iserializer($data)), array("uniacid" => $_W["uniacid"], "id" => $sid));
    return true;
}
function haodian_get_data($sid, $key = "")
{
    global $_W;
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid), array("haodian_data"));
    $data = iunserializer($store["haodian_data"]);
    if (!is_array($data)) {
        $data = array();
    }
    if (empty($key)) {
        return $data;
    }
    $keys = explode(".", $key);
    $counts = count($keys);
    if ($counts == 1) {
        return $data[$key];
    }
    if ($counts == 2) {
        return $data[$keys[0]][$keys[1]];
    }
    if ($counts == 3) {
        return $data[$keys[0]][$keys[1]][$keys[1]];
    }
    return true;
}
function haodian_get_activity($sid)
{
    global $_W;
    $table = array("kanjia" => array("name" => "tiny_wmall_kanjia", "field" => "name, price, oldprice "), "pintuan" => array("name" => "tiny_wmall_pintuan_goods", "field" => "name, price, oldprice, peoplenum "), "seckill" => array("name" => "tiny_wmall_seckill_goods", "field" => "name, price, oldprice "));
    $condition = " where uniacid = :uniacid and agentid = :agentid and status = :status and sid = :sid";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":status" => 1, ":sid" => $sid);
    $data = array();
    foreach ($table as $key => $value) {
        $temp = pdo_fetch("select " . $value["field"] . " from " . tablename($value["name"]) . " " . $condition . " order by id desc", $params);
        if (!empty($temp)) {
            if ($key == "kanjia") {
                $data[$key]["thumb_vue"] = "static/img/kanjia.png";
                $data[$key]["thumb_wxapp"] = "/static/img/kanjia.png";
                $data[$key]["text"] = (string) $temp["name"] . " 原价¥" . $temp["oldprice"] . " 邀请好友帮忙砍价最低¥" . $temp["price"] . "即可购买";
            } else {
                if ($key == "pintuan") {
                    $data[$key]["thumb_vue"] = "static/img/pintuan.png";
                    $data[$key]["thumb_wxapp"] = "/static/img/pintuan.png";
                    $data[$key]["text"] = "【" . $temp["peoplenum"] . "人团】" . $temp["name"] . " 原价¥" . $temp["oldprice"] . " 团购价¥" . $temp["price"];
                } else {
                    if ($key == "seckill") {
                        $data[$key]["thumb_vue"] = "static/img/seckill.png";
                        $data[$key]["thumb_wxapp"] = "/static/img/seckill.png";
                        $data[$key]["text"] = (string) $temp["name"] . " 原价¥" . $temp["oldprice"] . " 限时抢购价¥" . $temp["price"] . "即可购买";
                    }
                }
            }
        }
    }
    return $data;
}
function haodian_score_update($sid)
{
    global $_W;
    $score = round(pdo_fetchcolumn("select avg(store_service) from " . tablename("tiny_wmall_gohome_comment") . " where uniacid = :uniacid and agentid = :agentid and sid = :sid and goods_id = :goods_id and status = 0", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $sid, ":goods_id" => 0)), 1);
    pdo_update("tiny_wmall_store", array("haodian_score" => $score), array("uniacid" => $_W["uniacid"], "id" => $sid));
    return true;
}
function haodian_comment_fetchall($sid)
{
    global $_W;
    global $_GPC;
    if (empty($sid)) {
        return false;
    }
    $condition = " where uniacid = :uniacid and agentid = :agentid and sid = :sid and goods_id = 0 and status = 0";
    $params = array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $sid);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $result = array();
    if (!defined("IN_WXAPP") && !defined("IN_VUE")) {
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_gohome_comment") . $condition, $params);
        $pager = pagination($total, $page, $psize);
        $retult["total"] = $total;
        $result["pager"] = $pager;
    }
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_gohome_comment") . " " . $condition . " order by id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($data)) {
        foreach ($data as &$value) {
            $value["addtime_cn"] = date("Y-m-d H:i", $value["addtime"]);
            $value["store_service"] = intval($value["store_service"]);
            $value["thumbs"] = iunserializer($value["thumbs"]);
            if (!empty($value["thumbs"])) {
                foreach ($value["thumbs"] as &$thumb) {
                    $thumb = tomedia($thumb);
                }
            }
        }
    }
    $result["comment"] = $data;
    return $result;
}
function haodian_order_update($orderOrId, $type, $extra = array())
{
    global $_W;
    $order = $orderOrId;
    if (!is_array($order)) {
        $order = pdo_get("tiny_wmall_haodian_order", array("uniacid" => $_W["uniacid"], "id" => $order));
    }
    if (empty($order)) {
        return error(-1, "订单不存在！");
    }
    if ($type == "pay") {
        if ($order["is_pay"] == 1) {
            return error(-1, "订单已支付，请勿重复支付");
        }
        $update = array("is_pay" => 1, "pay_type" => $extra["type"], "final_fee" => $extra["card_fee"], "paytime" => TIMESTAMP, "transaction_id" => $extra["transaction_id"], "out_trade_no" => $extra["uniontid"]);
        pdo_update("tiny_wmall_haodian_order", $update, array("uniacid" => $order["uniacid"], "id" => $order["id"]));
        if (0 < $order["agentid"] && $order["agent_final_fee"]) {
            $remark = "好店入驻费入口";
            mload()->model(base64_decode("YWdlbnQ="));
            agent_update_account($order["agentid"], $order["agent_final_fee"], 9, $order["id"], $remark, "haodian");
        }
        pdo_update("tiny_wmall_store", array("haodian_status" => 5), array("uniacid" => $order["uniacid"], "id" => $order["sid"]));
        return error(0, "支付成功");
    }
}
function haodian_member_can_comment($sid)
{
    global $_W;
    $total = pdo_fetchcolumn(" select count(*) from " . tablename("tiny_wmall_gohome_comment") . " where uniacid = :uniacid and agentid = :agentid and uid = :uid and sid = :sid and goods_id = 0 and status = 0", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":uid" => $_W["member"]["uid"], ":sid" => $sid));
    if (0 < $total) {
        return false;
    }
    return true;
}
function haodian_settle_order_bill($order)
{
    if (empty($order)) {
        return false;
    }
    if (0 < $order["agentid"]) {
        $order["price"] = floatval($order["final_fee"]);
        mload()->model(base64_decode("YWdlbnQ="));
        $account_agent = get_agent($order["agentid"], "fee");
        $agent_fee_config = $account_agent["fee"]["fee_gohome"];
        if (empty($agent_fee_config["haodian"])) {
            $account_agent = get_plugin_config("agent.serve_fee");
            $agent_fee_config = $account_agent["fee_gohome"];
        }
        $agent_fee_config = $agent_fee_config["haodian"];
        if ($agent_fee_config["type"] == 2) {
            $agent_serve_fee = floatval($agent_fee_config["fee"]);
            $agent_serve = array("fee_type" => 2, "fee_rate" => 0, "fee" => $agent_serve_fee, "note" => "固定抽成" . $agent_serve_fee . "￥");
        } else {
            if ($agent_fee_config["type"] == 1) {
                $basic = 0;
                $note = array("yes" => array(), "no" => array());
                $fee_items = array("yes" => array("price" => "好店入驻费用"), "no" => array());
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
                    $text .= "佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                }
                $agent_serve = array("fee_type" => 1, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
            } else {
                if ($agent_fee_config["type"] == 3) {
                    $agent_serve_rate = floatval($agent_fee_config["fee_rate"]);
                    $agent_serve_fee = round($order["price"] * $agent_serve_rate / 100, 2);
                    $text = "本单代理佣金:" . $order["price"] . " x " . $agent_serve_rate . "%";
                    if (0 < $agent_fee_config["fee_min"] && $agent_serve_fee < $agent_fee_config["fee_min"]) {
                        $agent_serve_fee = $agent_fee_config["fee_min"];
                        $text .= " 佣金小于代理设置最少抽佣金额，以最少抽佣金额计";
                    }
                    $agent_serve = array("fee_type" => 3, "fee_rate" => $agent_serve_rate, "fee" => $agent_serve_fee, "note" => $text);
                }
            }
        }
        $agent_final_fee = $order["final_fee"] - $agent_serve_fee;
        $agent_serve["final"] = "(代理商抽取佣金比例" . $order["final_fee"] . " - 代理商抽取佣金比例" . $agent_serve_fee . ")";
        $order["agent_final_fee"] = $agent_final_fee;
        $order["agent_serve"] = iserializer($agent_serve);
        $order["agent_serve_fee"] = $agent_serve_fee;
        unset($order["price"]);
    }
    return $order;
}

?>