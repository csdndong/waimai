<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("pintuan");
if ($ta == "post") {
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $value = $_GPC["data"];
        $data = array("uniacid" => $_W["uniacid"], "sid" => $sid, "name" => trim($value["name"]), "unit" => trim($value["unit"]), "displayorder" => intval($value["displayorder"]), "logo" => trim($value["logo"]), "islimittime" => intval($value["islimittime"]), "usetype" => intval($value["usetype"]), "detail" => htmlspecialchars_decode($value["detail"]), "status" => intval($value["status"]), "price" => floatval($value["price"]), "aloneprice" => floatval($value["aloneprice"]), "oldprice" => floatval($value["oldprice"]), "peoplenum" => intval($value["peoplenum"]), "grouptime" => intval($value["grouptime"]), "cateid" => intval($value["cateid"]), "total" => intval($value["total"]), "falesailed" => intval($value["falesailed"]), "buylimit" => intval($value["buylimit"]));
        if (is_array($value["tag"])) {
            array_map("intval", $value["tag"]);
            $data["tag"] = implode(",", $value["tag"]);
        }
        $data["adv"] = array();
        if (!empty($_GPC["adv"])) {
            foreach ($_GPC["adv"] as $val) {
                if (empty($val)) {
                    continue;
                }
                $data["adv"][] = trim($val);
            }
        }
        $data["adv"] = iserializer($data["adv"]);
        if ($data["islimittime"] == 1) {
            $data["starttime"] = strtotime($value["starttime_cn"]);
            $data["endtime"] = strtotime($value["endtime_cn"]) + 86399;
        } else {
            $data["starttime"] = TIMESTAMP;
            $data["endtime"] = strtotime("7 day");
        }
        if (!empty($id)) {
            pdo_update("tiny_wmall_pintuan_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_pintuan_goods", $data);
        }
        imessage(error(0, "编辑活动成功"), "", "ajax");
    }
    if ($id) {
        $item = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_pintuan_goods") . " WHERE uniacid = :uniacid AND sid = :sid AND id = :id", array(":uniacid" => $_W["uniacid"], ":sid" => $sid, ":id" => $id));
        if (empty($item)) {
            imessage("活动不存在或已删除", iurl("store/pintuan/activity/list"), "info");
        }
        $item["tag"] = explode(",", $item["tag"]);
        $item["starttime_cn"] = date("Y-m-d H:i", $item["starttime"]);
        $item["endtime_cn"] = date("Y-m-d H:i", $item["endtime"]);
        $item["logo"] = tomedia($item["logo"]);
        $item["adv"] = iunserializer($item["adv"]);
        foreach ($item["adv"] as &$val) {
            $val = tomedia($val);
        }
    }
    $category = pdo_getall("tiny_wmall_pintuan_category", array("uniacid" => $_W["uniacid"]), array("id", "title"));
    if (!empty($category)) {
        foreach ($category as $value) {
            if (0 < $item["cateid"] && $item["cateid"] == $value["id"]) {
                $item["category_title"] = $value["title"];
            }
        }
    }
    $result = array("records" => $item, "category" => $category);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "list") {
        $records = pintuan_get_activitylist();
        if (!empty($records)) {
            foreach ($records as &$val) {
                $val["starttime_cn"] = date("Y-m-d H:i", $val["starttime"]);
                $val["endtime_cn"] = date("Y-m-d H:i", $val["endtime"]);
            }
        }
        $result = array("records" => $records);
        imessage(error(0, $result), "", "ajax");
        return 1;
    } else {
        if ($ta == "del") {
            $id = $_GPC["id"];
            if (empty($id)) {
                imessage(error(-1, "商品不存在或已被删除"), "", "ajax");
            }
            pdo_delete("tiny_wmall_pintuan_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
            imessage(error(0, "删除活动成功"), "", "ajax");
        }
    }
}

?>