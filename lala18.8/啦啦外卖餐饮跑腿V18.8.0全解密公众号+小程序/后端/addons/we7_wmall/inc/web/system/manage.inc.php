<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "编辑幻灯片";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $slide = pdo_get("tiny_wmall_plugincenter_slide", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($slide)) {
            imessage("幻灯片不存在或已删除", referer(), "error");
        }
    }
    if ($_W["ispost"]) {
    }
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑应用";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $manage = pdo_get("tiny_wmall_plugincenter_slide", array("uniacid" => $_W["uniacid"], "id" => $id));
            if (empty($manage)) {
                imessage("应用不存在", referer(), "error");
            }
        }
        $plugins = pdo_fetchall("select id,title from" . tablename("tiny_wmall_plugin"));
        if ($_W["ispost"]) {
            $data = array("uniacid" => $_W["uniacid"], "displayorder" => intval($_GPC["displayorder"]), "plugintype" => intval($_GPC["plugintype"]), "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "description" => htmlspecialchars_decode($_GPC["description"]), "status" => intval($_GPC["status"]), "addtime" => TIMESTAMP);
            if (!empty($_GPC["setprice"])) {
                $setprice = array();
                foreach ($_GPC["setprice"]["date"] as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $price = $_GPC["setprice"]["price"][$key];
                    if (empty($price)) {
                        continue;
                    }
                    $setprice["setprice"][] = array("date" => $val, "price" => $price);
                }
                $data["data"] = iserializer($setprice);
            }
            if ($data["plugintype"] !== 1 && !empty($_GPC["pluginid"])) {
                $data["pluginid"] = intval($_GPC["pluginid"]);
            }
            if (0 < $id) {
                $data["addtime"] = $manage["addtime"];
                pdo_update("tiny_wmall_plugincenter_slide", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_plugincenter_slide", $data);
            }
        }
    }
}
include itemplate("system/manage");

?>