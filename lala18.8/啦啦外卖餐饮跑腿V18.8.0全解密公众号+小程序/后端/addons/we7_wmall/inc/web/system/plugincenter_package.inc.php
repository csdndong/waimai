<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("plugincenter");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "套餐列表";
    if (checksubmit()) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("sailed" => trim($_GPC["saileds"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
                pdo_update("tiny_wmall_plugincenter_package", $data, array("id" => intval($v)));
            }
        }
        imessage(error(0, "修改成功"), iurl("system/plugincenter_package/list"), "success");
    }
    $filter = $_GPC;
    $filter["uniacid"] = 0;
    $filter["psize"] = 15;
    $pluginpackage = get_plugincenter_package($filter);
    $packages = $pluginpackage["packages"];
    $pager = $pluginpackage["pager"];
} else {
    if ($op == "post") {
        $_W["page"]["title"] = "编辑套餐";
        $id = intval($_GPC["id"]);
        if (0 < $id) {
            $pluginpackage = pdo_get("tiny_wmall_plugincenter_package", array("id" => $id));
            if (empty($pluginpackage)) {
                imessage("应用不存在或已删除", referer(), "error");
            }
            $pluginpackage["data"] = iunserializer($pluginpackage["data"]);
            $pluginpackage["pluginid"] = explode(",", $pluginpackage["pluginid"]);
        }
        $plugins = pdo_fetchall("select id,title,name from" . tablename("tiny_wmall_plugin"), array(), "id");
        if ($_W["ispost"]) {
            $data = array("uniacid" => 0, "displayorder" => intval($_GPC["displayorder"]), "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "sailed" => intval($_GPC["sailed"]), "content" => trim($_GPC["content"]), "description" => htmlspecialchars_decode($_GPC["description"]), "status" => intval($_GPC["status"]), "addtime" => TIMESTAMP);
            if (!empty($_GPC["meal"])) {
                $meal = array();
                foreach ($_GPC["meal"]["month"] as $key => $val) {
                    $price = $_GPC["meal"]["price"][$key];
                    if (empty($price)) {
                        continue;
                    }
                    $meal["meal"][] = array("month" => $val, "price" => $price);
                }
                $data["data"] = iserializer($meal);
            }
            if (!empty($_GPC["pluginid"])) {
                $length = count($_GPC["pluginid"]);
                if (4 < $length) {
                    imessage(error(-1, "选择应用个数不能大于4个"), "", "ajax");
                }
                $data["pluginid"] = implode(",", $_GPC["pluginid"]);
                foreach ($_GPC["pluginid"] as &$val) {
                    $val = $plugins[$val]["name"];
                }
                $data["pluginname"] = implode(",", $_GPC["pluginid"]);
            }
            if (0 < $id) {
                $data["addtime"] = $pluginpackage["addtime"];
                pdo_update("tiny_wmall_plugincenter_package", $data, array("id" => $id));
            } else {
                pdo_insert("tiny_wmall_plugincenter_package", $data);
            }
            imessage(error(0, "编辑套餐成功"), iurl("system/plugincenter_package/list"), "ajax");
        }
    } else {
        if ($op == "status") {
            $id = intval($_GPC["id"]);
            $status = intval($_GPC["status"]);
            pdo_update("tiny_wmall_plugincenter_package", array("status" => $status), array("id" => $id));
            imessage(error(0, ""), "", "ajax");
        } else {
            if ($op == "del") {
                $id = intval($_GPC["id"]);
                pdo_delete("tiny_wmall_plugincenter_package", array("id" => $id));
                imessage(error(0, "删除应用套餐成功"), "", "ajax");
            }
        }
    }
}
include itemplate("system/plugincenter_package");

?>
