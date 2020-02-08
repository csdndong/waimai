<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $_W["page"]["title"] = "海报";
    $poster = store_get_data($sid, "shopPage");
}
if ($ta == "post") {
    $_W["page"]["title"] = "添加海报";
    $key = trim($_GPC["key"]);
    if (!empty($key)) {
        $posters = store_get_data($sid, "shopPage");
        $poster = $posters[$key];
        foreach ($poster["goods"] as $val) {
            $good = pdo_fetch("select id, thumb, total, price, title from" . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and id = :id", array(":uniacid" => $_W["uniacid"], ":id" => $val));
            $goods[] = $good;
        }
        $poster["goods"] = $goods;
    }
    if ($_W["ispost"]) {
        if (empty($_GPC["title"])) {
            imessage(error(-1, "请输入海报名称"), "", "ajax");
        }
        $posters = store_get_data($sid, "shopPage");
        if (!empty($_GPC["key"])) {
            unset($posters[$_GPC["key"]]);
        }
        $id = date("YmdHis", time()) . random(2, true);
        $posters[$id] = array("id" => $id, "title" => $_GPC["title"], "thumb" => $_GPC["thumb"], "wxapp_link" => $_GPC["wxapp_link"], "goods" => $_GPC["goods_id"]);
        store_set_data($sid, "shopPage", $posters);
        imessage(error(0, "设置商品海报成功"), iurl("store/decoration/shopPage/index"), "ajax");
    }
}
if ($ta == "del") {
    $posters = store_get_data($sid, "shopPage");
    if (!empty($_GPC["key"])) {
        unset($posters[$_GPC["key"]]);
        store_set_data($sid, "shopPage", $posters);
    }
    imessage(error(0, "删除海报成功"), "", "ajax");
}
include itemplate("store/decoration/index");

?>