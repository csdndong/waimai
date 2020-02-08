<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("pintuan");
if ($ta == "post") {
    $config = get_plugin_config("gohome.basic");
    if ($config["status"]["pintuan"] != 1) {
        imessage(error(-1, "拼团功能暂时关闭，详情请联系平台管理员"), "", "ajax");
    }
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $value = $_GPC["data"];
        $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "name" => trim($value["name"]), "unit" => trim($value["unit"]), "displayorder" => intval($value["displayorder"]), "thumb" => trim($value["thumb"]), "usetype" => intval($value["usetype"]), "detail" => htmlspecialchars_decode($value["detail"]), "status" => intval($value["status"]), "price" => floatval($value["price"]), "aloneprice" => floatval($value["aloneprice"]), "oldprice" => floatval($value["oldprice"]), "peoplenum" => intval($value["peoplenum"]), "grouptime" => intval($value["grouptime"]), "cateid" => intval($value["cateid"]), "total" => intval($value["total"]), "falesailed" => intval($value["falesailed"]), "falselooknum" => intval($value["falselooknum"]), "falsesharenum" => intval($value["falsesharenum"]), "buylimit" => intval($value["buylimit"]), "starttime" => strtotime($value["starttime_cn"]), "endtime" => strtotime($value["endtime_cn"]));
        if (is_array($value["tag"])) {
            array_map("intval", $value["tag"]);
            $data["tag"] = implode(",", $value["tag"]);
        }
        $data["thumbs"] = array();
        if (!empty($value["thumbs"])) {
            foreach ($value["thumbs"] as $val) {
                if (empty($val)) {
                    continue;
                }
                $data["thumbs"][] = trim($val);
            }
        }
        $data["thumbs"] = iserializer($data["thumbs"]);
        if (!empty($id)) {
            pdo_update("tiny_wmall_pintuan_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_pintuan_goods", $data);
        }
        imessage(error(0, "编辑商品成功"), "", "ajax");
    }
    if ($id) {
        $item = pintuan_get_activity($id);
        if (empty($item)) {
            imessage("商品不存在或已删除", "", "info");
        }
    }
    $category = pdo_fetchall("select * from " . tablename("tiny_wmall_pintuan_category") . " where uniacid = :uniacid and agentid = :agentid order by displayorder desc", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]), "id");
    $item["category_title"] = $category[$item["cateid"]]["title"];
    $category = array_values($category);
    $result = array("records" => $item, "category" => $category);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($ta == "list") {
        $records = pintuan_get_activitylist();
        if (!empty($records)) {
            foreach ($records as &$val) {
                $val["starttime_cn"] = date("m-d H:i", $val["starttime"]);
                $val["endtime_cn"] = date("m-d H:i", $val["endtime"]);
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