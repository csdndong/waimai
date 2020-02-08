<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "套餐列表";
    if ($_W["ispost"] && !empty($_GPC["ids"])) {
        foreach ($_GPC["ids"] as $k => $v) {
            $title = trim($_GPC["title"][$k]);
            if (empty($title)) {
                continue;
            }
            $data = array("title" => $title, "displayorder" => intval($_GPC["displayorder"][$k]));
            pdo_update("tiny_wmall_svip_meal", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
        }
        imessage(error(0, "修改成功"), iurl("svip/setmeal/list"), "ajax");
    }
    $meals = svip_meal_getall();
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑超级会员套餐";
    $id = intval($_GPC["id"]);
    if ($_W["ispost"]) {
        $data = array("uniacid" => $_W["uniacid"], "title" => trim($_GPC["title"]), "days" => intval($_GPC["days"]), "price" => floatval($_GPC["price"]), "oldprice" => floatval($_GPC["oldprice"]), "description" => trim($_GPC["description"]), "displayorder" => intval($_GPC["displayorder"]));
        if (empty($data["title"])) {
            imessage(error(-1, "套餐名称不能为空"), "", "ajax");
        }
        if (empty($data["days"])) {
            imessage(error(-1, "套餐时长不能为空"), "", "ajax");
        }
        if (0 < $id) {
            pdo_update("tiny_wmall_svip_meal", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_svip_meal", $data);
        }
        imessage(error(0, "编辑套餐成功"), iurl("svip/setmeal/list"), "ajax");
    }
    if (0 < $id) {
        $meal = svip_meal_get($id);
        if (empty($meal)) {
            imessage("套餐不存在或已删除", referer(), "error");
        }
    }
}
if ($op == "del") {
    $id = intval($_GPC["id"]);
    pdo_delete("tiny_wmall_svip_meal", array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "删除套餐成功"), "", "ajax");
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_svip_meal", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, "设置套餐状态成功"), "", "ajax");
}
include itemplate("setmeal");

?>