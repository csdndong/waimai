<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $_W["page"]["title"] = "编辑门店底部菜单";
    $store = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_store") . " WHERE uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $sid));
    if (empty($store["menu"])) {
        $store["menu"] = array("data" => array(), "path" => array("goods" => 0, "home" => 0));
    } else {
        $store["menu"] = json_decode(base64_decode($store["menu"]), true);
    }
    if ($_W["ispost"]) {
        $data = $_GPC["menu"];
        $store["menu"]["data"] = $data;
        $menudata = array("menu" => base64_encode(json_encode($store["menu"])));
        pdo_update("tiny_wmall_store", $menudata, array("uniacid" => $_W["uniacid"], "id" => $sid));
        imessage(error(0, "添加成功"), iurl("store/decoration/menu/list"), "ajax");
    }
} else {
    if ($ta == "setting") {
        $_W["page"]["title"] = "底部菜单设置";
        $store = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_store") . " WHERE uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $sid));
        if (empty($store["menu"])) {
            imessage(error(-1, "请先设置底部菜单"), iurl("store/decoration/menu/list"), "error");
        } else {
            $store["menu"] = json_decode(base64_decode($store["menu"]), true);
        }
        if ($_W["ispost"]) {
            $path = $_GPC["path"];
            $store["menu"]["path"] = $path;
            $menudata = array("menu" => base64_encode(json_encode($store["menu"])));
            pdo_update("tiny_wmall_store", $menudata, array("id" => $sid, "uniacid" => $_W["uniacid"]));
            imessage(error(0, "添加成功"), iurl("store/decoration/menu/setting"), "ajax");
        }
    }
}
include itemplate("store/decoration/menu");

?>
