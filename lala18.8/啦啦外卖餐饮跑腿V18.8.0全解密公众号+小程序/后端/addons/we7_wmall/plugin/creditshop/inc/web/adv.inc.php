<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "幻灯片列表";
    if ($_W["ispost"]) {
        if (!empty($_GPC["ids"])) {
            foreach ($_GPC["ids"] as $k => $v) {
                $data = array("advname" => trim($_GPC["advnames"][$k]), "displayorder" => intval($_GPC["displayorders"][$k]));
                pdo_update("tiny_wmall_creditshop_adv", $data, array("uniacid" => $_W["uniacid"], "id" => intval($v)));
            }
        }
        imessage(error(0, "编辑幻灯片成功"), iurl("creditshop/adv/list"), "ajax");
    }
    $slides = pdo_fetchall("select * from " . tablename("tiny_wmall_creditshop_adv") . " where uniacid = :uniacid order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
}
if ($op == "post") {
    $_W["page"]["title"] = "编辑幻灯片";
    $id = intval($_GPC["id"]);
    if (0 < $id) {
        $slide = pdo_get("tiny_wmall_creditshop_adv", array("uniacid" => $_W["uniacid"], "id" => $id));
        if (empty($slide)) {
            imessage("幻灯片不存在或已删除", referer(), "error");
        }
    }
    if ($_W["ispost"]) {
        $advname = trim($_GPC["advname"]) ? trim($_GPC["advname"]) : imessage(error(-1, "标题不能为空"), "", "ajax");
        $data = array("uniacid" => $_W["uniacid"], "advname" => $advname, "thumb" => trim($_GPC["thumb"]), "link" => trim($_GPC["link"]), "displayorder" => intval($_GPC["displayorder"]), "status" => intval($_GPC["status"]), "wxapp_link" => trim($_GPC["wxapp_link"]));
        if (!empty($slide)) {
            pdo_update("tiny_wmall_creditshop_adv", $data, array("uniacid" => $_W["uniacid"], "id" => $id));
        } else {
            pdo_insert("tiny_wmall_creditshop_adv", $data);
        }
        imessage(error(0, "编辑幻灯片成功"), iurl("creditshop/adv/list"), "ajax");
    }
}
if ($op == "status") {
    $id = intval($_GPC["id"]);
    $status = intval($_GPC["status"]);
    pdo_update("tiny_wmall_creditshop_adv", array("status" => $status), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "displayorder") {
    $id = intval($_GPC["id"]);
    $displayorder = intval($_GPC["displayorder"]);
    pdo_update("tiny_wmall_creditshop_adv", array("displayorder" => $displayorder), array("uniacid" => $_W["uniacid"], "id" => $id));
    imessage(error(0, ""), "", "ajax");
}
if ($op == "del") {
    $ids = $_GPC["id"];
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    foreach ($ids as $value) {
        pdo_delete("tiny_wmall_creditshop_adv", array("uniacid" => $_W["uniacid"], "id" => $value));
    }
    imessage(error(0, "删除成功"), "", "ajax");
}
include itemplate("adv");

?>