<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "门店列表";
    if (checksubmit("submit")) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("displayorder" => intval($_GPC["displayorder"][$k]), "click" => intval($_GPC["click"][$k]), "sailed" => intval($_GPC["sailed"][$k]));
                pdo_update("tiny_wmall_store", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage("编辑成功", iurl("merchant/store/list"), "success");
    }
    $store_label = category_store_label();
    $condition = " uniacid = :uniacid and (status = 1 or status = 0) and is_waimai = 1";
    $params[":uniacid"] = $_W["uniacid"];
    $cid = intval($_GPC["cid"]);
    if (0 < $cid) {
        $condition .= " AND cid LIKE :cid";
        $params[":cid"] = "%|" . $cid . "|%";
    }
    $label = intval($_GPC["label"]);
    if (0 < $label) {
        $condition .= " AND label = :label";
        $params[":label"] = $label;
    }
    $is_rest = isset($_GPC["is_rest"]) ? intval($_GPC["is_rest"]) : -1;
    if (-1 < $is_rest) {
        $condition .= " AND is_rest = :is_rest";
        $params[":is_rest"] = $is_rest;
    }
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    $keyword = trim($_GPC["keyword"]);
    if (!empty($_GPC["keyword"])) {
        $condition .= " and (title like '%" . $keyword . "%' or id = '" . $keyword . "')";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_store") . " WHERE " . $condition, $params);
    $lists = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_store") . " WHERE " . $condition . " ORDER BY is_stick desc, displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
    if (!empty($lists)) {
        foreach ($lists as &$li) {
            $li["cid"] = explode("|", $li["cid"]);
            $li["sys_url"] = imurl("wmall/store/goods", array("sid" => $li["id"]), true);
            $li["vue_url"] = ivurl("pages/store/goods", array("sid" => $li["id"]), true);
            $li["wechat_qrcode"] = (array) iunserializer($li["wechat_qrcode"]);
            $li["wechat_url"] = $li["wechat_qrcode"]["url"];
        }
    }
    $categorys = store_fetchall_category();
    $store_status = store_status();
    include itemplate("merchant/list");
}
if ($op == "post") {
    $_W["page"]["title"] = "添加门店";
    $perm = check_max_store_perm();
    if (empty($perm)) {
        imessage("门店入驻量已超过上限,请联系公众号管理员", "", "info");
    }
    $config_store = $_W["we7_wmall"]["config"]["store"];
    $config_mall = $_W["we7_wmall"]["config"]["mall"];
    if ($_W["ispost"]) {
        $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "logo" => trim($_GPC["logo"]), "telephone" => trim($_GPC["telephone"]), "content" => trim($_GPC["content"]), "address" => trim($_GPC["address"]), "displayorder" => intval($_GPC["displayorder"]), "delivery_mode" => $config_store["delivery"]["delivery_mode"] ? $config_store["delivery"]["delivery_mode"] : 1, "delivery_fee_mode" => 1, "delivery_price" => $config_store["delivery"]["delivery_fee"], "business_hours" => iserializer(array()), "addtime" => TIMESTAMP, "push_token" => random(32), "self_audit_comment" => intval($config_store["extra"]["self_audit_comment"]));
        if (empty($config_store["delivery"])) {
            $config_store["delivery"] = array("delivery_fee_mode" => 1, "delivery_price" => 0);
        }
        if ($config_store["delivery"]["delivery_fee_mode"] == 2) {
            $data["delivery_fee_mode"] = 2;
            $data["delivery_price"] = iserializer($data["delivery_price"]);
        } else {
            $data["delivery_fee_mode"] = 1;
            $data["delivery_price"] = floatval($data["delivery_price"]);
        }
        $delivery_times = get_config_text("takeout_delivery_time");
        $data["delivery_times"] = iserializer($delivery_times);
        $cids = array();
        if (!empty($_GPC["category1"])) {
            $data["cate_parentid1"] = intval($_GPC["category1"]["parentid"]);
            $cids[] = $data["cate_parentid1"];
            if ($config_mall["store_use_child_category"] == 1) {
                $data["cate_childid1"] = intval($_GPC["category1"]["childid"]);
                $cids[] = $data["cate_childid1"];
            }
        }
        if (!empty($_GPC["category1"])) {
            $data["cate_parentid2"] = intval($_GPC["category2"]["parentid"]);
            $cids[] = $data["cate_parentid2"];
            if ($config_mall["store_use_child_category"] == 1) {
                $data["cate_childid2"] = intval($_GPC["category2"]["childid"]);
                $cids[] = $data["cate_childid2"];
            }
        }
        $cids = implode("|", $cids);
        $data["cid"] = "|" . $cids . "|";
        pdo_insert("tiny_wmall_store", $data);
        $sid = pdo_insertid();
		//添加门店账户数据
        $config_serve_fee = $config_store["serve_fee"];
        $store_account = array("uniacid" => $_W["uniacid"], "sid" => $sid, "fee_takeout" => iserializer($config_serve_fee["fee_takeout"]), "fee_selfDelivery" => iserializer($config_serve_fee["fee_selfDelivery"]), "fee_instore" => iserializer($config_serve_fee["fee_instore"]), "fee_paybill" => iserializer($config_serve_fee["fee_paybill"]), "fee_limit" => $config_serve_fee["get_cash_fee_limit"], "fee_rate" => $config_serve_fee["get_cash_fee_rate"], "fee_min" => $config_serve_fee["get_cash_fee_min"], "fee_max" => $config_serve_fee["get_cash_fee_max"]);
        pdo_insert("tiny_wmall_store_account", $store_account);
        mlog(2000, $sid);
        imessage(error(0, "添加门店成功"), iurl("store/shop/setting", array("_sid" => $sid)), "ajax");
    }
    if ($config_mall["store_use_child_category"] == 1) {
        $categorys = store_fetchall_category("parent&child", array("is_sys" => 1));
    } else {
        $categorys = store_fetchall_category("parent_child", array("is_sys" => 1));
    }
    include itemplate("merchant/post");
}
if ($op == "template") {
    $sid = intval($_GPC["id"]);
    $template = trim($_GPC["t"]) ? trim($_GPC["t"]) : "index";
    pdo_update("tiny_wmall_store", array("template" => $template), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, "设置页面风格成功"), referer(), "ajax");
}
if ($op == "label") {
    $sid = intval($_GPC["sid"]);
    $label = intval($_GPC["label"]);
    pdo_update("tiny_wmall_store", array("label" => $label), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, "设置商户标签成功"), "", "ajax");
}
if ($op == "label_del") {
    $sid = intval($_GPC["id"]);
    pdo_update("tiny_wmall_store", array("label" => -1), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, "删除商户标签成功"), "", "ajax");
}
if ($op == "is_in_business") {
    $sid = intval($_GPC["id"]);
    $is_in_business = intval($_GPC["is_in_business"]);
    pdo_update("tiny_wmall_store", array("is_in_business" => $is_in_business), array("uniacid" => $_W["uniacid"], "id" => $sid));
    store_business_hours_init($sid);
    mlog(2012, $sid);
    imessage(error(0, ""), "", "ajax");
}
if ($op == "status") {
    $sid = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_store", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "is_recommend") {
    $sid = intval($_GPC["id"]);
    $recommend = intval($_GPC["is_recommend"]);
    pdo_update("tiny_wmall_store", array("is_recommend" => $recommend), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "is_stick") {
    $sid = intval($_GPC["id"]);
    $is_stick = intval($_GPC["is_stick"]);
    pdo_update("tiny_wmall_store", array("is_stick" => $is_stick), array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "is_haodian") {
    $sid = intval($_GPC["id"]);
    $is_haodian = intval($_GPC["is_haodian"]);
    $update = array("is_haodian" => $is_haodian);
    if ($is_haodian == 1) {
        $update["haodian_status"] = 1;
        $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid), array("haodian_starttime"));
        if (empty($store["haodian_starttime"])) {
            $update["haodian_starttime"] = TIMESTAMP;
        }
    }
    pdo_update("tiny_wmall_store", $update, array("uniacid" => $_W["uniacid"], "id" => $sid));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "batch") {
    $type = trim($_GPC["type"]);
    $value = intval($_GPC["value"]);
    if ($type == "is_in_business") {
        if ($value == 1) {
            $stores_is_business = icache_read("stores_is_business");
            if (!empty($stores_is_business)) {
                foreach ($stores_is_business as $val) {
                    if ($val["is_in_business"] == 0) {
                        continue;
                    }
                    pdo_update("tiny_wmall_store", array($type => 1), array("id" => $val["id"], "uniacid" => $_W["uniacid"], "status" => 1));
                }
            }
        } else {
            $stores_is_business = pdo_getall("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "status" => 1), array("id", "is_in_business"));
            icache_write("stores_is_business", $stores_is_business, 3600);
            pdo_update("tiny_wmall_store", array($type => 0), array("uniacid" => $_W["uniacid"], "status" => 1));
        }
        store_business_hours_init();
    } else {
        pdo_query("UPDATE " . tablename("tiny_wmall_store") . " SET " . $type . " = " . $value . " WHERE uniacid = " . $_W["uniacid"] . " AND status != 4");
    }
    imessage(error(0, ""), "", "ajax");
}
if ($op == "copy") {
    set_time_limit(0);
    $sid = intval($_GPC["sid"]);
    $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $sid));
    if (empty($store)) {
        imessage(error(-1, "门店不存在或已删除"), "", "ajax");
    }
    $store["title"] = $store["title"] . "-复制";
    unset($store["id"]);
    unset($store["push_token"]);
    unset($store["wechat_qrcode"]);
    unset($store["assign_qrcode"]);
    pdo_insert("tiny_wmall_store", $store);
    $store_id = pdo_insertid();
	//门店账户
    $config_serve_fee = $_W["we7_wmall"]["config"]["store"]["serve_fee"];
    $store_account = array("uniacid" => $_W["uniacid"], "sid" => $store_id, "fee_limit" => $config_serve_fee["get_cash_fee_limit"], "fee_rate" => $config_serve_fee["get_cash_fee_rate"], "fee_min" => $config_serve_fee["get_cash_fee_min"], "fee_max" => $config_serve_fee["get_cash_fee_max"]);
    pdo_insert("tiny_wmall_store_account", $store_account);
	//复制菜品分类
    $goods_categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_category") . " where uniacid = :uniacid and sid = :sid order by parentid asc", array(":uniacid" => $_W["uniacid"], ":sid" => $sid));
    $old_new_cid = array();
    if (!empty($goods_categorys)) {
        foreach ($goods_categorys as $category) {
            $cid = $category["id"];
            unset($category["id"]);
            $category["sid"] = $store_id;
            pdo_insert("tiny_wmall_goods_category", $category);
            $category_id = pdo_insertid();
            $old_new_cid[$cid] = $category_id;
            $child_id = 0;
            if (!empty($category["parentid"])) {
                $child_id = $cid;
                $cid = $category["parentid"];
                pdo_update("tiny_wmall_goods_category", array("parentid" => $old_new_cid[$category["parentid"]]), array("id" => $category_id));
            }
            $goods = pdo_getall("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $cid, "child_id" => $child_id));
            if (!empty($goods)) {
                foreach ($goods as $good) {
                    $goods_id = $good["id"];
                    unset($good["id"]);
                    $good["sid"] = $store_id;
                    if (!empty($good["child_id"])) {
                        $good["child_id"] = $category_id;
                        $good["cid"] = $old_new_cid[$category["parentid"]];
                    } else {
                        $good["cid"] = $category_id;
                        $good["child_id"] = 0;
                    }
                    pdo_insert("tiny_wmall_goods", $good);
                    $new_goods_id = pdo_insertid();
                    if ($good["is_options"] == 1) {
                        $options = pdo_getall("tiny_wmall_goods_options", array("uniacid" => $_W["uniacid"], "sid" => $sid, "goods_id" => $goods_id));
                        if (!empty($options)) {
                            foreach ($options as $option) {
                                unset($option["id"]);
                                $option["sid"] = $store_id;
                                $option["goods_id"] = $new_goods_id;
                                pdo_insert("tiny_wmall_goods_options", $option);
                            }
                        }
                    }
                }
            }
        }
    }
	//复制桌台类型
    $table_categorys = pdo_getall("tiny_wmall_tables_category", array("uniacid" => $_W["uniacid"], "sid" => $sid));
    if (!empty($table_categorys)) {
        foreach ($table_categorys as $category) {
            $cid = $category["id"];
            unset($category["id"]);
            $category["sid"] = $store_id;
            pdo_insert("tiny_wmall_tables_category", $category);
            $category_id = pdo_insertid();
            $tables = pdo_getall("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $cid));
            if (!empty($tables)) {
                foreach ($tables as $table) {
                    unset($table["id"]);
                    unset($table["qrcode"]);
                    $table["sid"] = $store_id;
                    $table["cid"] = $category_id;
                    pdo_insert("tiny_wmall_tables", $table);
                }
            }
			//复制预定
            $reserves = pdo_getall("tiny_wmall_reserve", array("uniacid" => $_W["uniacid"], "sid" => $sid, "table_cid" => $cid));
            if (!empty($reserves)) {
                foreach ($reserves as $reserve) {
                    unset($reserve["id"]);
                    $reserve["sid"] = $store_id;
                    $reserve["table_cid"] = $category_id;
                    pdo_insert("tiny_wmall_reserve", $reserve);
                }
            }
        }
    }
	//复制排号
    $assigns = pdo_getall("tiny_wmall_assign_queue", array("uniacid" => $_W["uniacid"], "sid" => $sid));
    if (!empty($assigns)) {
        foreach ($assigns as $assign) {
            unset($assign["id"]);
            $assign["sid"] = $store_id;
            pdo_insert("tiny_wmall_assign_queue", $assign);
        }
    }
    imessage(error(0, "复制门店成功"), "", "ajax");
}
if ($op == "storage") {
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_store", array("status" => 4, "deltime" => TIMESTAMP), array("uniacid" => $_W["uniacid"], "id" => $id));
    pdo_update("tiny_wmall_activity_bargain_goods", array("status" => 0), array("uniacid" => $_W["uniacid"], "sid" => $id));
    pdo_update("tiny_wmall_activity_bargain", array("status" => 0), array("uniacid" => $_W["uniacid"], "sid" => $id));
    mlog(2002, $id);
    imessage(error(0, "门店删除成功"), "", "ajax");
}
if ($op == "lots") {
    if ($_W["is_agent"]) {
        $agents = get_agents();
    }
    if ($_W["ispost"] && $_GPC["set"] == 1) {
        $sid = explode(",", $_GPC["sid"]);
        if (empty($sid)) {
            imessage(error(-1, "请选择需要修改的门店"), "", "ajax");
        }
        $data = array("self_audit_comment" => intval($_GPC["self_audit_comment"]), "auto_handel_order" => intval($_GPC["auto_handel_order"]), "auto_notice_deliveryer" => intval($_GPC["auto_notice_deliveryer"]));
        if (!$data["self_audit_comment"]) {
            $data["comment_status"] = 1;
        } else {
            $data["comment_status"] = intval($_GPC["comment_status"]);
        }
        if ($_W["is_agent"]) {
            $agentid = intval($_GPC["agentid"]);
            if (0 < $agentid) {
                foreach ($sid as $val) {
                    update_store_agent($val, $agentid);
                }
            }
        }
        foreach ($sid as &$row) {
            pdo_update("tiny_wmall_store", $data, array("id" => $row, "uniacid" => $_W["uniacid"]));
        }
        imessage(error(0, "批量操作修改成功"), iurl("merchant/store/list"), "ajax");
    }
    $ids = $_GPC["id"];
    if (empty($ids)) {
        imessage(error(-1, "请选择需要操作的门店"), "", "ajax");
    }
    $ids = implode(",", $ids);
    include itemplate("merchant/listOp");
}
if ($op == "sailed") {
    if ($_W["we7_wmall"]["config"]["takeout"]["order"]["store_sailed_type"] == "goods") {
        imessage(error(-1, "当前门店销量设置为按订单商品数进行统计，不能同步销量"), "", "ajax");
    }
    store_stat_init("sailed", 0);
    imessage(error(0, "同步门店销量成功"), "", "ajax");
}
if ($op == "business") {
    store_business_hours_init();
    imessage(error(0, "门店营业时间修复成功!"), "", "ajax");
}
if ($op == "delivery_time") {
    store_stat_init("delivery_time", 0);
    imessage(error(0, "同步门店预计送达时间成功"), "", "ajax");
}

?>
