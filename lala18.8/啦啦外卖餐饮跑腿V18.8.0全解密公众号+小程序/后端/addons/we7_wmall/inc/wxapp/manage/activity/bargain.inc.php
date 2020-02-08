<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("activity");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "post") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $bargain = $_GPC["bargain"];
        $title = !empty($bargain["title"]) ? trim($bargain["title"]) : imessage(error(-1, "活动主题不能为空"), "", "ajax");
        $bargain_goods = $_GPC["bargainGoods"];
        $goods = array();
        if (!empty($bargain_goods)) {
            foreach ($bargain_goods as $value) {
                $temp = pdo_fetch("select a.id, a.price, b.bargain_id from " . tablename("tiny_wmall_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain_goods") . " as b on a.id = b.goods_id where a.uniacid = :uniacid and a.id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $value["id"]));
                if (empty($temp) || 0 < $temp["bargain_id"] && $temp["bargain_id"] != $id) {
                    continue;
                }
                $row = array("goods_id" => $value["id"], "discount_price" => floatval($value["discount_price"]), "max_buy_limit" => intval($value["max_buy_limit"]), "poi_user_type" => trim($value["poi_user_type"]) == "all" ? "all" : "new", "discount_total" => intval($value["discount_total"]), "discount_available_total" => intval($value["discount_available_total"]));
                $goods[$value["id"]] = $row;
            }
        }
        if (empty($goods)) {
            imessage(error(-1, "请选择参与活动的商品"), "", "ajax");
        }
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "title" => $title, "content" => trim($bargain["content"]), "order_limit" => intval($bargain["order_limit"]), "goods_limit" => intval($bargain["goods_limit"]), "starttime" => intval($bargain["starttime"]), "endtime" => intval($bargain["endtime"]), "starthour" => str_replace(":", "", trim($bargain["starthour"])), "endhour" => str_replace(":", "", trim($bargain["endhour"])), "use_limit" => intval($bargain["use_limit"]), "addtime" => TIMESTAMP, "total_updatetime" => strtotime(date("Y-m-d")) + 86400, "thumb" => trim($bargain["thumb"]));
        $activity = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $title, "starttime" => intval($bargain["starttime"]), "endtime" => intval($bargain["endtime"]), "type" => "bargain", "status" => 1);
        $status = activity_set($sid, $activity);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        if (0 < $id) {
            pdo_update("tiny_wmall_activity_bargain", $data, array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        } else {
            pdo_insert("tiny_wmall_activity_bargain", $data);
            $id = pdo_insertid();
        }
        foreach ($goods as $row) {
            $row["uniacid"] = $_W["uniacid"];
            $row["agentid"] = $_W["agentid"];
            $row["bargain_id"] = $id;
            $row["sid"] = $sid;
            $is_exist = pdo_get("tiny_wmall_activity_bargain_goods", array("bargain_id" => $id, "goods_id" => $row["goods_id"]));
            if (empty($is_exist)) {
                pdo_insert("tiny_wmall_activity_bargain_goods", $row);
            } else {
                pdo_update("tiny_wmall_activity_bargain_goods", $row, array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "bargain_id" => $id, "goods_id" => $row["goods_id"]));
            }
        }
        $goods_ids = implode(",", array_keys($goods));
        pdo_query("delete from " . tablename("tiny_wmall_activity_bargain_goods") . " where uniacid = :uniacid and sid = :sid and bargain_id = :bargain_id and goods_id not in (" . $goods_ids . ")", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":bargain_id" => $id));
        activity_cron();
        imessage(error(0, "编辑特价活动成功"), "", "ajax");
    }
    if (0 < $id) {
        $bargain = pdo_get("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "id" => $id));
        if (empty($bargain)) {
            imessage("特价活动不存在或已删除", referer(), "error");
        }
        if (strlen($bargain["starthour"]) < 4) {
            $bargain["starthour"] = "0" . $bargain["starthour"];
        }
        if (strlen($bargain["endhour"]) < 4) {
            $bargain["endhour"] = "0" . $bargain["endhour"];
        }
        $bargain["starthour"] = date("H:i", strtotime($bargain["starthour"]));
        $bargain["endhour"] = date("H:i", strtotime($bargain["endhour"]));
        $row = pdo_fetchall("select a.*,b.id, b.title,b.price,b.thumb from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id where a.bargain_id = :bargain_id order by a.displayorder desc", array(":bargain_id" => $bargain["id"]));
        $bargain["goods"] = $row;
    }
    if (empty($bargain)) {
        $bargain = array("starttime" => TIMESTAMP, "endtime" => TIMESTAMP + 86400 * 15, "starthour" => "00:00", "endhour" => "23:59", "goods_limit" => 1, "order_limit" => 1, "goods" => array());
    }
    $result = array("bargain" => $bargain);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "goods_list") {
        $categorys = store_fetchall_goods_category($sid, 1, true, "all", "available");
        $cids = array_keys($categorys);
        $condition = " where uniacid = :uniacid and sid = :sid and (type = 1 or type = 3)";
        $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
        $category_first = reset($categorys);
        $cid = intval($_GPC["cid"]) ? intval($_GPC["cid"]) : $category_first["id"];
        $condition .= " and cid = :cid";
        $params[":cid"] = $cid;
        $condition .= " and is_options = 0 and svip_status = 0";
        $orderby = trim($_GPC["orderby"]) ? trim($_GPC["orderby"]) : "sailed desc";
        $condition .= " order by " . $orderby . ", displayorder desc, id asc";
        $goods = pdo_fetchall("select id, title, thumb, cid, price, sailed, comment_total, comment_good, total, displayorder from " . tablename("tiny_wmall_goods") . $condition, $params);
        $bargain_goods = array();
        $bargain_id = intval($_GPC["bargain_id"]);
        if (0 < $bargain_id) {
            $bargain_goods = pdo_fetchall("select a.discount_price, a.max_buy_limit, a.discount_total, a.discount_available_total, a.poi_user_type, b.id, b.title, b.thumb, b.cid, b.price, b.sailed, b.comment_total, b.comment_good, b.total, b.displayorder from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id where a.uniacid = :uniacid and a.agentid = :agentid and a.sid = :sid and a.bargain_id = :bargain_id ", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":sid" => $sid, ":bargain_id" => $bargain_id));
        }
        if (!empty($bargain_goods)) {
            foreach ($bargain_goods as &$bgoods) {
                $bgoods["is_bargain"] = true;
            }
        }
        if (!empty($goods)) {
            foreach ($goods as &$val) {
                $val["thumb"] = tomedia($val["thumb"]);
                if (0 < $val["comment_total"]) {
                    $val["per_comment_good"] = $val["comment_good"] / $val["comment_total"] * 100;
                } else {
                    $val["per_comment_good"] = 0;
                }
                $val["is_bargain"] = false;
                $val["discount_price"] = $val["price"];
                $val["max_buy_limit"] = 1;
                $val["discount_total"] = -1;
                $val["discount_available_total"] = -1;
                $val["poi_user_type"] = "all";
            }
        }
        $result = array("categorys" => array_values($categorys), "goods" => $goods, "cid" => $cid, "cids" => $cids, "bargain_goods" => $bargain_goods);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "search") {
            $condition = " where uniacid = :uniacid and sid = :sid and (type = 1 or type = 3)";
            $params = array(":uniacid" => $_W["uniacid"], ":sid" => $sid);
            $keyword = trim($_GPC["keyword"]);
            $condition .= " and title like '%" . $keyword . "%'";
            $goods = pdo_fetchall("select id, title, thumb, cid, price, sailed, comment_total, comment_good, total, displayorder from " . tablename("tiny_wmall_goods") . $condition, $params);
            if (!empty($goods)) {
                foreach ($goods as &$val) {
                    $val["thumb"] = tomedia($val["thumb"]);
                    if (0 < $val["comment_total"]) {
                        $val["per_comment_good"] = $val["comment_good"] / $val["comment_total"] * 100;
                    } else {
                        $val["per_comment_good"] = 0;
                    }
                    $val["is_bargain"] = false;
                    $val["discount_price"] = $val["price"];
                    $val["max_buy_limit"] = 1;
                    $val["discount_total"] = -1;
                    $val["discount_available_total"] = -1;
                    $val["poi_user_type"] = "all";
                }
            }
            $result = array("goods" => $goods);
            imessage(error(0, $result), "", "ajax");
            return 1;
        } else {
            if ($ta == "bargainList") {
                $status = isset($_GPC["status"]) ? intval($_GPC["status"]) : 1;
                $records = pdo_getall("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => $status, "type" => "bargain"));
                if (!empty($records)) {
                    foreach ($records as $key => &$val) {
                        $val["until"] = round(($val["endtime"] - time()) / 86400);
                        $val["type_cn"] = "特价优惠";
                        $val["starttime_cn"] = date("Y-m-d", $val["starttime"]);
                        $val["endtime_cn"] = date("Y-m-d", $val["endtime"]);
                        $val["addtime_cn"] = date("Y-m-d", $val["addtime"]);
                    }
                }
                $result = array("activity" => $records);
                imessage(error(0, $result), "", "ajax");
                return 1;
            } else {
                if ($ta == "del") {
                    $id = $_GPC["id"];
                    $bargain = pdo_get("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "status" => 1, "sid" => $sid, "type" => "bargain"));
                    if (empty($bargain)) {
                        $status = activity_del($sid, "bargain");
                        if (is_error($status)) {
                            imessage($status, referer(), "ajax");
                        }
                    }
                    if (!empty($id)) {
                        pdo_delete("tiny_wmall_activity_bargain", array("uniacid" => $_W["uniacid"], "id" => $id, "sid" => $sid));
                        pdo_delete("tiny_wmall_activity_bargain_goods", array("uniacid" => $_W["uniacid"], "bargain_id" => $id, "sid" => $sid));
                    }
                    imessage(error(0, "撤销活动成功"), "", "ajax");
                }
            }
        }
    }
}

?>