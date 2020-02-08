<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "应用列表〃";
    $condition = " where uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_plugincenter_plugin") . $condition, $params);
    $plugincenter = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_plugincenter_plugin") . $condition . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑应用";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $plugincenter = pdo_get("tiny_wmall_plugincenter_plugin", array("uniacid" => $_W["uniacid"], "id" => $id));
            $plugincenter["data"] = iunserializer($plugincenter["data"]);
            if (empty($plugincenter)) {
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
                $data["addtime"] = $plugincenter["addtime"];
                pdo_update("tiny_wmall_plugincenter_plugin", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
            } else {
                pdo_insert("tiny_wmall_plugincenter_plugin", $data);
            }
            imessage(error(0, "编辑应用成功"), iurl("system/plugincenter/list"), "ajax");
        }
    }
}
include itemplate("system/plugincenter");

?>