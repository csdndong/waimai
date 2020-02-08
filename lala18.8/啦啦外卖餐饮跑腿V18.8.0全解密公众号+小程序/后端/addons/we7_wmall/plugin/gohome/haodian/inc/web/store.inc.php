<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "商户列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]));
                pdo_update("tiny_wmall_store", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage("编辑商户成功", iurl("haodian/store/list"), "success");
    }
    $haodian_status_group = haodian_store_status(-1);
    $haodian_status = isset($_GPC["haodian_status"]) ? intval($_GPC["haodian_status"]) : -1;
    $filter = array("haodian_status" => $haodian_status);
    if (!empty($_GPC["cid"])) {
        $cid = $_GPC["cid"];
        if (strexists($cid, ":")) {
            $cid = explode(":", $cid);
            $filter["haodian_child_id"] = intval($cid[1]);
            $cid = $cid[0];
        }
        $filter["haodian_cid"] = intval($cid);
    }
    $filter["keyword"] = trim($_GPC["keyword"]);
    $agentid = isset($_GPC["agentid"]) ? intval($_GPC["agentid"]) : 0;
    $filter["agentid"] = $agentid;
    $store_data = haodian_store_fetchall($filter);
    $stores = $store_data["store"];
    $pager = $store_data["pager"];
    $categorys = pdo_fetchall("select id,title,parentid from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid and agentid = :agentid  order by displayorder desc, id asc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
    foreach ($categorys as &$val) {
        if (!empty($val["parentid"])) {
            $categorys[$val["parentid"]]["child"][$val["id"]] = $val;
            unset($categorys[$val["id"]]);
        }
    }
    include itemplate("store");
    return 1;
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑商户";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (!empty($store)) {
                $store["map"] = array("lat" => $store["location_x"], "lng" => $store["location_y"]);
                $store["business_hours"] = iunserializer($store["business_hours"]);
                $store["thumbs"] = iunserializer($store["thumbs"]);
                $store["sns"] = iunserializer($store["sns"]);
                $store["haodian_data"] = iunserializer($store["haodian_data"]);
            }
        }
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "haodian_cid" => intval($_GPC["category"]["parentid"]), "haodian_child_id" => intval($_GPC["category"]["childid"]), "logo" => trim($_GPC["logo"]), "content" => trim($_GPC["content"]), "telephone" => trim($_GPC["telephone"]), "description" => htmlspecialchars_decode($_GPC["description"]), "address" => trim($_GPC["address"]), "location_x" => $_GPC["map"]["lat"], "location_y" => $_GPC["map"]["lng"], "sns" => iserializer(array("qq" => trim($_GPC["sns"]["qq"]), "weixin" => trim($_GPC["sns"]["weixin"]))), "displayorder" => intval($_GPC["displayorder"]), "haodian_status" => intval($_GPC["haodian_status"]), "is_waimai" => intval($_GPC["is_waimai"]), "is_haodian" => 1, "haodian_starttime" => TIMESTAMP, "haodian_endtime" => strtotime(trim($_GPC["haodian_endtime"])));
            if (!empty($_GPC["business_start_hours"])) {
                $hour = array();
                foreach ($_GPC["business_start_hours"] as $k => $v) {
                    if (empty($v) || empty($_GPC["business_end_hours"][$k])) {
                        continue;
                    }
                    $v = date("H:i", strtotime(trim($v)));
                    $end = date("H:i", strtotime(trim($_GPC["business_end_hours"][$k])));
                    $hour[] = array("s" => $v, "e" => $end);
                }
                $data["business_hours"] = iserializer($hour);
            }
            if (!empty($_GPC["thumbs"]["image"])) {
                $thumbs = array();
                foreach ($_GPC["thumbs"]["image"] as $key => $image) {
                    if (empty($image)) {
                        continue;
                    }
                    $thumbs[] = array("image" => $image, "url" => trim($_GPC["thumbs"]["url"][$key]));
                }
                $data["thumbs"] = iserializer($thumbs);
            }
            if (!empty($_GPC["tags"])) {
                foreach ($_GPC["tags"] as $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $data["haodian_data"]["tags"][] = trim($val);
                }
                $data["haodian_data"] = iserializer($data["haodian_data"]);
            }
            if (0 < $id) {
                $data["haodian_starttime"] = $store["haodian_starttime"];
                pdo_update("tiny_wmall_store", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_store", $data);
            }
            imessage(error(0, "编辑商户成功"), iurl("haodian/store/list"), "ajax");
        }
        $categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid order by displayorder desc", array(":uniacid" => $_W["uniacid"]), "id");
        if (!empty($categorys)) {
            foreach ($categorys as &$val) {
                $val["name"] = $val["title"];
                if (empty($val["parentid"])) {
                    $parent[$val["id"]] = $val;
                } else {
                    $child[$val["parentid"]][$val["id"]] = $val;
                }
            }
            unset($categorys);
            $categorys = array("parent" => $parent, "child" => $child);
        }
        include itemplate("store");
        return 1;
    } else {
        if ($op == "status") {
            $id = intval($_GPC["id"]);
            $type = trim($_GPC["type"]);
            $value = intval($_GPC["value"]);
            pdo_update("tiny_wmall_store", array($type => $value), array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "设置状态成功"), "", "ajax");
        } else {
            if ($op == "del") {
                $id = intval($_GPC["id"]);
                pdo_delete("tiny_wmall_store", array("haodian_status" => "7"), array("uniacid" => $_W["uniacid"], "id" => $id));
                imessage(error(0, "删除商户成功"), iurl("haodian/store/list"), "ajax");
            } else {
                if ($op == "order_list") {
                    $_W["page"]["title"] = "付费入驻列表";
                    $condition = " where a.uniacid = :uniacid";
                    $params = array(":uniacid" => $_W["uniacid"]);
                    $agentid = intval($_GPC["agentid"]);
                    if (0 < $agentid) {
                        $condition = " and a.agentid = :agentid";
                        $params[":agentid"] = $agentid;
                    }
                    $is_pay = isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1;
                    if (-1 < $is_pay) {
                        $condition .= " and a.is_pay = :is_pay";
                        $params[":is_pay"] = $is_pay;
                    }
                    if (!empty($_GPC["addtime"])) {
                        $starttime = strtotime($_GPC["addtime"]["start"]);
                        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
                    } else {
                        $starttime = strtotime("-7 day");
                        $endtime = TIMESTAMP;
                    }
                    $condition .= " AND a.addtime > :start AND a.addtime < :end";
                    $params[":start"] = $starttime;
                    $params[":end"] = $endtime;
                    $keyword = trim($_GPC["keyword"]);
                    if (!empty($keyword)) {
                        $condition .= " and (a.sid = :sid or b.title like :keyword)";
                        $params[":sid"] = $keyword;
                        $params[":keyword"] = "%" . $keyword . "%";
                    }
                    $page = max(1, intval($_GPC["page"]));
                    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
                    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_haodian_order") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition, $params);
                    $records = pdo_fetchall("select a.*, b.title, b.logo, b.haodian_cid, b.haodian_child_id,b.haodian_starttime,b.haodian_endtime,b.haodian_status from" . tablename("tiny_wmall_haodian_order") . " as a left join " . tablename("tiny_wmall_store") . " as b on a.sid = b.id" . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
                    if (!empty($records)) {
                        $haodian_status = haodian_store_status(-1);
                        $pay_types = order_pay_types();
                    }
                    $pager = pagination($total, $page, $psize);
                    include itemplate("store");
                } else {
                    if ($op == "pay") {
                        $id = intval($_GPC["id"]);
                        pdo_update("tiny_wmall_haodian_order", array("is_pay" => "1"), array("uniacid" => $_W["uniacid"], "id" => $id));
                        imessage(error(0, "设为已支付成功"), referer(), "ajax");
                    } else {
                        if ($op == "storeagent") {
                            if ($_W["is_agent"]) {
                                $agents = get_agents();
                            }
                            $ids = $_GPC["id"];
                            $ids = implode(",", $ids);
                            if ($_W["ispost"] && $_GPC["set"] == 1) {
                                $storeid = explode(",", $_GPC["id"]);
                                $agentid = intval($_GPC["agentid"]);
                                if (0 < $agentid) {
                                    foreach ($storeid as $value) {
                                        pdo_update("tiny_wmall_store", array("agentid" => $agentid), array("uniacid" => $_W["uniacid"], "id" => $value));
                                    }
                                }
                                imessage(error(0, "批量操作修改成功"), iurl("haodian/store/list"), "ajax");
                            }
                            include itemplate("op");
                        }
                    }
                }
            }
        }
    }
}

?>