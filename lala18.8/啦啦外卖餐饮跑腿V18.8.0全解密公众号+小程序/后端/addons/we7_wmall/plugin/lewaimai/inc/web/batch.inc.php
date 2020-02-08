<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$_W["page"]["title"] = "基础设置";
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "batch";
set_time_limit(0);
if ($op == "batch") {
    load()->func("communication");
    $config = get_plugin_config("lewaimai.config");
    $data = $config;
    if (empty($data["admin_id"]) || empty($data["lwm_appid"])) {
        imessage("参数不完整,请先填写", iurl("lewaimai/config/index"), "error");
    }
    $page = intval($_GPC["page"]) ? intval($_GPC["page"]) : 1;
    $data["page"] = $page;
    $url = "https://api.lewaimai.com/customer/common/shop/shop/list?ver=v2";
    $respon = ihttp_post($url, $data);
    if (is_error($respon)) {
        imessage("拉取失败", iurl("lewaimai/config/index"), "error");
    }
    $result = @json_decode($respon["content"], true);
    if ($result["error_code"] != 0) {
        imessage($result["error_msg"], iurl("lewaimai/config/index"), "error");
    }
    $result = $result["data"];
    $cache_category = cache_read("lewaimai:category:" . $_W["uniacid"]);
    $category = $result["shoptype"];
    function lewaimai_img($id, $img, $type)
    {
        global $_W;
        if (empty($type)) {
            $type = "goods";
        }
        pdo_insert("tiny_wmall_lewaimai_log", array("uniacid" => $_W["uniacid"], "storeidOrgoodsid" => $id, "img" => $img, "type" => $type));
        return true;
    }
    foreach ($category as &$v) {
        if (!empty($cache_category) && in_array($v["id"], array_keys($cache_category))) {
            continue;
        }
        $insert = array("uniacid" => $_W["uniacid"], "title" => $v["name"], "status" => $v["is_show"]);
        pdo_insert("tiny_wmall_store_category", $insert);
        $cid = pdo_insertid();
        $v[$v["id"]] = $cid;
        $cache_category[$v["id"]] = $cid;
    }
    cache_write("lewaimai:category:" . $_W["uniacid"], $cache_category);
    $stores = $result["shoplist"];
    $cache_store = cache_read("lewaimai:store:" . $_W["uniacid"]);
    $cache_goods_list = cache_read("lewaimai:goods_list:" . $_W["uniacid"]);
    $goodsUrl = "https://api.lewaimai.com/customer/common/page/food/choose?ver=v2";
    $goodsArr = array("lwm_sess_token" => $data["lwm_sess_token"], "lwm_appid" => $data["lwm_appid"], "admin_id" => $data["admin_id"], "sign" => $data["sign"], "from_type" => 1);
    foreach ($stores as &$value) {
        if (!empty($cache_store) && in_array($value["id"], array_keys($cache_store))) {
            continue;
        }
        $insert = array("uniacid" => $_W["uniacid"], "title" => $value["shopname"], "sailed" => $value["xiaoliang"], "score" => $value["commentgrade"], "cid" => $cache_category[$value["type_id"]], "is_in_business" => 1, "delivery_mode" => 2);
        pdo_insert("tiny_wmall_store", $insert);
        $sid = pdo_insertid();
        $cache_store[$value["id"]] = $sid;
        if (!empty($value["shopimage"])) {
            lewaimai_img($sid, $value["shopimage"], "store");
        }
        $config_settle = $_W["we7_wmall"]["config"]["store"]["settle"];
        $store_account = array("uniacid" => $_W["uniacid"], "sid" => $sid, "fee_takeout" => iserializer($config_settle["fee_takeout"]), "fee_selfDelivery" => iserializer($config_settle["fee_selfDelivery"]), "fee_instore" => iserializer($config_settle["fee_instore"]), "fee_paybill" => iserializer($config_settle["fee_paybill"]), "fee_limit" => $config_settle["get_cash_fee_limit"], "fee_rate" => $config_settle["get_cash_fee_rate"], "fee_min" => $config_settle["get_cash_fee_min"], "fee_max" => $config_settle["get_cash_fee_max"]);
        pdo_insert("tiny_wmall_store_account", $store_account);
        $goodsArr["shop_id"] = $value["id"];
        $results = ihttp_post($goodsUrl, $goodsArr);
        if (is_error($results)) {
            continue;
        }
        $results = @json_decode($results["content"], true);
        if ($results["error_code"] != 0) {
            continue;
        }
        $results = $results["data"];
        $hour = array();
        if (!empty($results["business_hours"])) {
            $hour = array();
            foreach ($results["business_hours"] as $h) {
                $s = substr($h["start"], 0, 5);
                $e = substr($h["stop"], 0, 5);
                $hour[] = array("s" => $s, "e" => $e);
            }
        }
        $store_update = array("business_hours" => iserializer($hour), "notice" => $results["shop_notice"]);
        pdo_update("tiny_wmall_store", $store_update, array("uniacid" => $_W["uniacid"], "id" => $sid));
        $goods_category = $results["foodtype"];
        $cache_goods_category = cache_read("lewaimai:goods_category:" . $_W["uniacid"]);
        foreach ($goods_category as $c) {
            if (!empty($cache_goods_category) && in_array($c["id"], array_keys($cache_goods_category))) {
                continue;
            }
            $goods_category_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "title" => $c["name"], "status" => $c["is_show"]);
            pdo_insert("tiny_wmall_goods_category", $goods_category_insert);
            $goods_category_id = pdo_insertid();
            $cache_goods_category[$c["id"]] = $goods_category_id;
        }
        cache_write("lewaimai:goods_category:" . $_W["uniacid"], $cache_goods_category);
        $goodsList = $results["foodlist"];
        foreach ($goodsList as $good) {
            if (!empty($cache_goods_list) && in_array($good["id"], array_keys($cache_goods_list))) {
                continue;
            }
            $goods_insert = array("uniacid" => $_W["uniacid"], "sid" => $sid, "cid" => $cache_goods_category[$good["type_id"]], "title" => $good["name"], "price" => $good["price"], "unitname" => $good["unit"], "status" => 1, "total" => -1, "box_price" => $good["dabao_money"], "content" => $good["descript"], "old_price" => $good["formerprice"]);
            if ($good["is_nature"] == 1) {
                foreach ($good["nature"] as $v) {
                    $labels = array();
                    foreach ($v["data"] as $attr) {
                        $labels[] = $attr["naturevalue"];
                    }
                    $goods_insert["attrs"][] = array("name" => $v["naturename"], "label" => $labels);
                }
                $goods_insert["attrs"] = iserializer($goods_insert["attrs"]);
            }
            pdo_insert("tiny_wmall_goods", $goods_insert);
            $goodsList_id = pdo_insertid();
            $cache_goods_list[$good["id"]] = $goodsList_id;
            if (!empty($good["img"])) {
                lewaimai_img($goodsList_id, $good["img"], "goods");
            }
        }
    }
    cache_write("lewaimai:goods_list:" . $_W["uniacid"], $cache_goods_list);
    cache_write("lewaimai:store:" . $_W["uniacid"], $cache_store);
    if (count($stores) == 20) {
        $page++;
        imessage("即将拉取第" . $page . "页数据,请勿关闭浏览器", iurl("lewaimai/batch/batch", array("page" => $page)), "success");
    } else {
        imessage("拉取成功,即将拉取商户logo,请勿关闭浏览器", iurl("lewaimai/batch/storeImg"), "success");
    }
}
if ($op == "storeImg") {
    $store_img = pdo_fetchall("select * from " . tablename("tiny_wmall_lewaimai_log") . "where uniacid = :uniacid and type = :type order by id desc limit 20", array(":uniacid" => $_W["uniacid"], ":type" => "store"));
    if (!empty($store_img)) {
        foreach ($store_img as $value) {
            $img = ihttp_get($value["img"]);
            if (is_error($img)) {
                continue;
            }
            $content = $img["content"];
            $name = ifile_write($content, "", true);
            if (is_error($name)) {
                continue;
            }
            pdo_update("tiny_wmall_store", array("logo" => $name), array("uniacid" => $_W["uniacid"], "id" => $value["storeidOrgoodsid"]));
            pdo_delete("tiny_wmall_lewaimai_log", array("uniacid" => $_W["uniacid"], "id" => $value["id"]));
        }
    }
    if (!empty($store_img)) {
        imessage("正在拉取商户图片,请勿关闭浏览器", iurl("lewaimai/batch/storeImg"), "success");
    } else {
        imessage("即将拉取商品图片,请勿关闭浏览器", iurl("lewaimai/batch/goodsImg"), "success");
    }
}
if ($op == "goodsImg") {
    $goods_img = pdo_fetchall("select * from" . tablename("tiny_wmall_lewaimai_log") . "where uniacid = :uniacid and type = :type order by id desc limit 20", array(":uniacid" => $_W["uniacid"], ":type" => "goods"));
    if (!empty($goods_img)) {
        foreach ($goods_img as $value) {
            $img = ihttp_get($value["img"]);
            if (is_error($img)) {
                continue;
            }
            $content = $img["content"];
            $name = ifile_write($content, "", true);
            if (is_error($name)) {
                continue;
            }
            pdo_update("tiny_wmall_goods", array("thumb" => $name), array("uniacid" => $_W["uniacid"], "id" => $value["storeidOrgoodsid"]));
            pdo_delete("tiny_wmall_lewaimai_log", array("uniacid" => $_W["uniacid"], "id" => $value["id"]));
        }
    }
    if (!empty($goods_img)) {
        imessage("正在拉取商品图片,请勿关闭浏览器", iurl("lewaimai/batch/goodsImg"), "success");
    } else {
        imessage("拉取成功", iurl("lewaimai/config/index"), "success");
    }
}
include itemplate("config");

?>