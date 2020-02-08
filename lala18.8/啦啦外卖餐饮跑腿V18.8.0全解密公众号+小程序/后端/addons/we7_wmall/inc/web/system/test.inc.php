<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
set_time_limit(0);
$uniacid_old = 1;
$agentid = 0;
$uniacid_new = 165;
if ($op == "delcopy") {
    $agents = array(369, 368, 367, 366);
    foreach ($agents as $agentid) {
        $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $uniacid_new, "is_copy" => 1, "agentid" => $agentid), array("id"));
        foreach ($stores as $store) {
            pdo_delete("tiny_wmall_store_account", array("uniacid" => $uniacid_new, "agentid" => 0, "sid" => $store["id"]));
        }
    }
}
if ($op == "delaccount") {
    pdo_delete("tiny_wmall_store_account", array("uniacid" => $uniacid_new, "is_copy" => 1, "agentid" => $agentid));
}
if ($op == "copyaccount") {
    $stores = pdo_getall("tiny_wmall_store", array("uniacid" => $uniacid_new, "is_copy" => 1, "agentid" => $agentid), array("id"));
    foreach ($stores as $store) {
        pdo_delete("tiny_wmall_store_account", array("uniacid" => $uniacid_new, "agentid" => 0, "sid" => $store["id"]));
        $insert = array("uniacid" => $uniacid_new, "agentid" => $agentid, "sid" => $store["id"]);
        pdo_insert("tiny_wmall_store_account", $insert);
    }
}
if ($op == "copycategory") {
    $categorys = pdo_getall("tiny_wmall_store_category", array("uniacid" => $uniacid_old));
    $category_old2new = array();
    foreach ($categorys as $value) {
        $category_old_id = $value["id"];
        unset($value["id"]);
        $value["uniacid"] = $uniacid_new;
        $value["agentid"] = $agentid;
        pdo_insert("tiny_wmall_store_category", $value);
        $category_new_id = pdo_insertid();
        $category_old2new[$category_old_id] = $category_new_id;
    }
    cache_write("heidou:storeCategory:" . $uniacid_old, $category_old2new);
    imessage("商户分类复制成功，即将开始转移商户数据", iurl("system/test/copyall"), "success");
}
if ($op == "copyall") {
    $psize = 10;
    $pindex = max(1, intval($_GPC["page"]));
    $stores = pdo_fetchall("select * from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, array("uniacid" => $uniacid_old));
    $stores_length = count($stores);
    $cache_storeCategory = cache_read("heidou:storeCategory:" . $uniacid_old);
    $store_old2new = cache_read("heidou:store:" . $uniacid_old);
    if (empty($store_old2new)) {
        $store_old2new = array();
    }
    foreach ($stores as $store) {
        $sid = $store["id"];
        $store["uniacid"] = $uniacid_new;
        unset($store["id"]);
        unset($store["push_token"]);
        unset($store["wechat_qrcode"]);
        unset($store["assign_qrcode"]);
        $store["agentid"] = $agentid;
        if (!empty($store["cid"])) {
            $store["cid"] = trim($store["cid"], "|");
            $cids = explode("|", $store["cid"]);
            $cids_new = array();
            if (!empty($cids)) {
                foreach ($cids as $cid) {
                    $cids_new[] = $cache_storeCategory[$cid];
                }
                unset($store["cid"]);
                $store["cid"] = implode("|", $cids_new);
                $store["cid"] = "|" . $store["cid"] . "|";
            }
        }
        pdo_insert("tiny_wmall_store", $store);
        $store_id = pdo_insertid();
        $store_account = pdo_get("tiny_wmall_store_account", array("uniacid" => $uniacid_old, "sid" => $sid));
        unset($store_account["id"]);
        unset($store_account["amount"]);
        unset($store_account["deposit"]);
        unset($store_account["wechat"]);
        $store_account["uniacid"] = $uniacid_new;
        $store_account["sid"] = $store_id;
        $store_account["agentid"] = $agentid;
        pdo_insert("tiny_wmall_store_account", $store_account);
        unset($store);
        unset($store_account);
        $goods_categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_goods_category") . " where uniacid = :uniacid and sid = :sid order by parentid asc", array(":uniacid" => $uniacid_old, ":sid" => $sid));
        $old_new_cid = array();
        if (!empty($goods_categorys)) {
            foreach ($goods_categorys as $category) {
                $cid = $category["id"];
                unset($category["id"]);
                $category["uniacid"] = $uniacid_new;
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
                $goods = pdo_getall("tiny_wmall_goods", array("uniacid" => $uniacid_old, "sid" => $sid, "cid" => $cid, "child_id" => $child_id));
                if (!empty($goods)) {
                    foreach ($goods as $good) {
                        $good["uniacid"] = $uniacid_new;
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
                            $options = pdo_getall("tiny_wmall_goods_options", array("uniacid" => $uniacid_old, "sid" => $sid, "goods_id" => $goods_id));
                            if (!empty($options)) {
                                foreach ($options as $option) {
                                    $option["uniacid"] = $uniacid_new;
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
        unset($goods_categorys);
        unset($goods);
        $table_categorys = pdo_getall("tiny_wmall_tables_category", array("uniacid" => $uniacid_old, "sid" => $sid));
        if (!empty($table_categorys)) {
            foreach ($table_categorys as $category) {
                $cid = $category["id"];
                unset($category["id"]);
                $category["uniacid"] = $uniacid_new;
                $category["sid"] = $store_id;
                pdo_insert("tiny_wmall_tables_category", $category);
                $category_id = pdo_insertid();
                $tables = pdo_getall("tiny_wmall_tables", array("uniacid" => $uniacid_old, "sid" => $sid, "cid" => $cid));
                if (!empty($tables)) {
                    foreach ($tables as $table) {
                        unset($table["id"]);
                        unset($table["qrcode"]);
                        $table["uniacid"] = $uniacid_new;
                        $table["sid"] = $store_id;
                        $table["cid"] = $category_id;
                        pdo_insert("tiny_wmall_tables", $table);
                    }
                }
                $reserves = pdo_getall("tiny_wmall_reserve", array("uniacid" => $uniacid_old, "sid" => $sid, "table_cid" => $cid));
                if (!empty($reserves)) {
                    foreach ($reserves as $reserve) {
                        unset($reserve["id"]);
                        $reserve["uniacid"] = $uniacid_new;
                        $reserve["sid"] = $store_id;
                        $reserve["table_cid"] = $category_id;
                        pdo_insert("tiny_wmall_reserve", $reserve);
                    }
                }
            }
        }
        unset($table_categorys);
        unset($reserves);
        $assigns = pdo_getall("tiny_wmall_assign_queue", array("uniacid" => $uniacid_old, "sid" => $sid));
        if (!empty($assigns)) {
            foreach ($assigns as $assign) {
                unset($assign["id"]);
                $assign["sid"] = $store_id;
                $assign["uniacid"] = $uniacid_new;
                pdo_insert("tiny_wmall_assign_queue", $assign);
            }
        }
        unset($assigns);
        $store_old2new[$sid] = $store_id;
    }
    cache_write("heidou:store:" . $uniacid_old, $store_old2new);
    if ($stores_length < 10) {
        imessage("即将复制生活圈数", iurl("system/test/gohome_copy"), "success");
    } else {
        $pindex++;
        imessage("即将复制" . $pindex . "页数", iurl("system/test/copyall", array("page" => $pindex)), "success");
    }
}
if ($op == "gohome_copy") {
    $slides = pdo_getall("tiny_wmall_gohome_slide", array("uniacid" => $uniacid_old));
    foreach ($slides as $val) {
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_gohome_slide", $val);
    }
    unset($slides);
    echo "生活圈幻灯片ok";
    $notices = pdo_getall("tiny_wmall_gohome_notice", array("uniacid" => $uniacid_old));
    foreach ($notices as $val) {
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_gohome_notice", $val);
    }
    unset($notices);
    echo "生活圈公告ok";
    $categorys = pdo_getall("tiny_wmall_gohome_category", array("uniacid" => $uniacid_old));
    foreach ($categorys as $val) {
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_gohome_category", $val);
    }
    unset($categorys);
    echo "生活圈分类ok";
    $store_old2new = cache_read("heidou:store:" . $uniacid_old);
    $kanjia_categorys = pdo_getall("tiny_wmall_kanjia_category", array("uniacid" => $uniacid_old));
    $kanjia_category_old2new = array();
    foreach ($kanjia_categorys as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_kanjia_category", $val);
        $newid = pdo_insertid();
        $kanjia_category_old2new[$oldid] = $newid;
    }
    unset($kanjia_categorys);
    echo "砍价分类ok";
    $kanjia_goods = pdo_getall("tiny_wmall_kanjia", array("uniacid" => $uniacid_old));
    foreach ($kanjia_goods as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        $val["sid"] = $store_old2new[$val["sid"]];
        $val["cateid"] = $kanjia_category_old2new[$val["cateid"]];
        pdo_insert("tiny_wmall_kanjia", $val);
    }
    unset($kanjia_goods);
    unset($kanjia_category_old2new);
    echo "砍价商品ok";
    $pintuan_categorys = pdo_getall("tiny_wmall_pintuan_category", array("uniacid" => $uniacid_old));
    $pintuan_category_old2new = array();
    foreach ($pintuan_categorys as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_pintuan_category", $val);
        $newid = pdo_insertid();
        $pintuan_category_old2new[$oldid] = $newid;
    }
    unset($pintuan_categorys);
    echo "拼团分类ok";
    $goods = pdo_getall("tiny_wmall_pintuan_goods", array("uniacid" => $uniacid_old));
    foreach ($goods as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        $val["sid"] = $store_old2new[$val["sid"]];
        $val["cateid"] = $pintuan_category_old2new[$val["cateid"]];
        pdo_insert("tiny_wmall_pintuan_goods", $val);
    }
    unset($goods);
    unset($pintuan_category_old2new);
    echo "拼团商品ok";
    $seckill_categorys = pdo_getall("tiny_wmall_seckill_goods_category", array("uniacid" => $uniacid_old));
    $seckill_category_old2new = array();
    foreach ($seckill_categorys as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_seckill_goods_category", $val);
        $newid = pdo_insertid();
        $seckill_category_old2new[$oldid] = $newid;
    }
    unset($seckill_categorys);
    echo "抢购分类ok";
    $goods = pdo_getall("tiny_wmall_seckill_goods", array("uniacid" => $uniacid_old));
    foreach ($goods as $val) {
        $oldid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        $val["sid"] = $store_old2new[$val["sid"]];
        $val["cid"] = $seckill_category_old2new[$val["cid"]];
        pdo_insert("tiny_wmall_seckill_goods", $val);
    }
    unset($goods);
    unset($seckill_category_old2new);
    unset($store_old2new);
    echo "抢购商品ok";
    $tongcheng_categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_tongcheng_category") . " where uniacid = :uniacid order by parentid asc", array(":uniacid" => $uniacid_old));
    $old_new_cid = array();
    foreach ($tongcheng_categorys as $val) {
        $cid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_tongcheng_category", $val);
        $category_id = pdo_insertid();
        $old_new_cid[$cid] = $category_id;
        if (!empty($val["parentid"])) {
            pdo_update("tiny_wmall_tongcheng_category", array("parentid" => $old_new_cid[$val["parentid"]]), array("id" => $category_id));
        }
    }
    unset($tongcheng_categorys);
    unset($old_new_cid);
    echo "同城分类ok";
    $haodian_categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_haodian_category") . " where uniacid = :uniacid order by parentid asc", array(":uniacid" => $uniacid_old));
    $old_new_cid = array();
    foreach ($haodian_categorys as $val) {
        $cid = $val["id"];
        unset($val["id"]);
        $val["uniacid"] = $uniacid_new;
        $val["agentid"] = 0;
        pdo_insert("tiny_wmall_haodian_category", $val);
        $category_id = pdo_insertid();
        $old_new_cid[$cid] = $category_id;
        $child_id = 0;
        if (!empty($val["parentid"])) {
            $child_id = $cid;
            $cid = $val["parentid"];
            pdo_update("tiny_wmall_haodian_category", array("parentid" => $old_new_cid[$val["parentid"]]), array("id" => $category_id));
        }
        $haodian_stores = pdo_getall("tiny_wmall_store", array("uniacid" => $uniacid_new, "haodian_cid" => $cid, "haodian_child_id" => $child_id), array("id", "haodian_cid", "haodian_child_id"));
        if (!empty($haodian_stores)) {
            foreach ($haodian_stores as $haodian_store) {
                if (!empty($haodian_store["haodian_child_id"])) {
                    $haodian_update["haodian_child_id"] = $category_id;
                    $haodian_update["haodian_cid"] = $old_new_cid[$val["parentid"]];
                } else {
                    $haodian_update["haodian_cid"] = $category_id;
                    $haodian_update["haodian_child_id"] = 0;
                }
                pdo_update("tiny_wmall_store", $haodian_update, array("uniacid" => $uniacid_new, "id" => $haodian_store["id"]));
            }
        }
    }
    unset($haodian_categorys);
    unset($old_new_cid);
    echo "好店ok";
}

?>