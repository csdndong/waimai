<?php

define("IN_SYS", true);
require "../framework/bootstrap.inc.php";
require IA_ROOT . "/web/common/bootstrap.sys.inc.php";
load()->web("common");
load()->web("template");
load()->func("communication");
load()->func("file");
load()->model("user");
load()->func("communication");
set_time_limit(0);
$uniacid = 10;
$takeaway = pdo_fetchall("select * from " . tablename("cjdc_storetype"));
$old2new = array();
foreach ($takeaway as $value) {
    $data = array("uniacid" => $uniacid, "title" => trim($value["type_name"]), "thumb" => trim($value["img"]), "displayorder" => intval($value["num"]));
    $store = pdo_insert("tiny_wmall_store_category", $data);
    $new_store_category_id = pdo_insertid();
    $old2new["store_category"][$value["id"]] = $new_store_category_id;
}
echo "商户类型OK";
$wxcsShop = pdo_fetchall("select * from " . tablename("cjdc_store"));
$dataShop = array();
$thumbs = array();
foreach ($wxcsShop as $value) {
    $coordinates = explode(",", $value["coordinates"]);
    $yyzz = explode(",", $value["yyzz"]);
    $environment = explode(",", $value["environment"]);
    foreach ($environment as $env) {
        $thumbs["image"][] = $env;
    }
    $business_hours = array(array("business_start_hours" => $value["time"], "business_end_hours" => $value["time4"]));
    $dataShop = array("uniacid" => $uniacid, "cid" => "|" . $old2new["store_category"][$value["md_type"]] . "|", "logo" => trim($value["logo"]), "title" => trim($value["name"]), "telephone" => $value["tel"], "address" => trim($value["address"]), "location_x" => $coordinates["0"], "location_y" => $coordinates["1"], "consume_per_person" => floatval($value["capita"]), "description" => trim($value["details"]), "thumbs" => iserializer($thumbs), "qualification" => iserializer(array("business" => array("thumb" => trim($yyzz["0"])), "more1" => array("thumb" => trim($yyzz["1"])), "more2" => array("thumb" => trim($yyzz["2"])))), "sailed" => intval($value["score"]), "business_hours" => iserializer($business_hours));
    $stores = pdo_insert("tiny_wmall_store", $dataShop);
    $new_store_id = pdo_insertid();
    $old2new["store"][$value["id"]] = $new_store_id;
}
echo "商户OK";
$wxcsGoodsCategory = pdo_fetchall("select * from " . tablename("cjdc_type"));
foreach ($wxcsGoodsCategory as $value) {
    $dataGoodsCategory = array("uniacid" => $uniacid, "title" => trim($value["type_name"]), "sid" => $old2new["store"][$value["store_id"]], "displayorder" => $value["order_by"]);
    pdo_insert("tiny_wmall_goods_category", $dataGoodsCategory);
    $new_goods_category_id = pdo_insertid();
    $old2new["goods_category"][$value["id"]] = $new_goods_category_id;
}
echo "商品类型OK";
$wxcsGoods = pdo_fetchall("select * from " . tablename("cjdc_goods"));
$dataGoods = array();
foreach ($wxcsGoods as $value) {
    if ($value["is_hot"] == 1) {
        $hot = 1;
    } else {
        $hot = 0;
    }
    if ($value["status"] == 1) {
        $status = 1;
    } else {
        $status = 0;
    }
    $dataGoods = array("uniacid" => $uniacid, "sid" => $old2new["store"][$value["store_id"]], "cid" => $old2new["goods_category"][$value["type_id"]], "title" => trim($value["name"]), "price" => floatval($value["money"]), "old_price" => floatval($value["money2"]), "svip_price" => floatval($value["vip_money"]), "ts_price" => floatval($value["dn_money"]), "thumb" => trim($value["logo"]), "status" => intval($status), "box_price" => floatval($value["box_money"]), "total" => intval($value["inventory"]), "is_hot" => intval($hot), "displayorder" => intval($value["num"]), "content" => trim($value["content"]), "num" => intval($value["displayorder"]), "type" => intval($value["type"]));
    $goods = pdo_insert("tiny_wmall_goods", $dataGoods);
}
echo "OK12345";
function _calc_current_frames()
{
}

?>