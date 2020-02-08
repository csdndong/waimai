<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
mload()->model("plugin");
pload()->model("seckill");
if ($ta == "list") {
    $records = seckill_allgoods();
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
        pdo_delete("tiny_wmall_seckill_goods", array("uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id));
        imessage(error(0, "删除活动成功"), "", "ajax");
    } else {
        if ($ta == "post") {
            $config = get_plugin_config("gohome.basic");
            if ($config["status"]["seckill"] != 1) {
                imessage(error(-1, "抢购功能暂时关闭，详情请联系平台管理员"), "", "ajax");
            }
            $id = intval($_GPC["id"]);
            if ($_W["ispost"]) {
                $value = $_GPC["data"];
                $data = array("uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "cid" => intval($value["cid"]), "status" => intval($value["status"]), "name" => trim($value["name"]), "price" => floatval($value["price"]), "oldprice" => floatval($value["oldprice"]), "total" => intval($value["total"]), "thumb" => trim($value["thumb"]), "click" => intval($value["click"]), "displayorder" => intval($value["displayorder"]), "content" => trim($value["content"]), "description" => htmlspecialchars_decode($value["description"]), "buy_note" => htmlspecialchars_decode($value["buy_note"]), "use_limit_day" => intval($value["use_limit_day"]), "falsejoinnum" => intval($value["falsejoinnum"]), "falselooknum" => intval($value["falselooknum"]), "falsesharenum" => intval($value["falsesharenum"]));
                $data["starttime"] = strtotime($value["starttime_cn"]);
                $data["endtime"] = strtotime($value["endtime_cn"]);
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
                    pdo_update("tiny_wmall_seckill_goods", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
                } else {
                    pdo_insert("tiny_wmall_seckill_goods", $data);
                }
                imessage(error(0, "编辑活动成功"), "", "ajax");
            }
            if ($id) {
                $records = seckill_goods($id);
                if (!empty($records)) {
                    $records["description"] = htmlspecialchars($records["description"]);
                    $records["buy_note"] = htmlspecialchars($records["buy_note"]);
                }
            }
            $category = seckill_goods_categorys();
            $records["category_title"] = $category[$records["cid"]]["title"];
            $category = array_values($category);
            $result = array("records" => $records, "category" => $category);
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>