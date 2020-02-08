<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "商户回收站";
    $filter = array("haodian_status" => 7);
    $store_data = haodian_store_fetchall($filter);
    $stores = $store_data["store"];
    $pager = $store_data["pager"];
    include itemplate("storage");
} else {
    if ($op == "restore") {
        $id = intval($_GPC["id"]);
        pdo_update("tiny_wmall_store", array("haodian_status" => 1), array("uniacid" => $_W["uniacid"], "id" => $id));
        imessage(error(0, "恢复门店成功"), "", "ajax");
    } else {
        if ($op == "del") {
            $id = intval($_GPC["id"]);
            $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $id));
            if ($store["is_waimai"] == 1) {
                imessage(error(-1, "该好点开启了外卖功能，无法彻底删除"), "", "ajax");
            }
            pdo_delete("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $id));
            imessage(error(0, "删除好店成功"), "", "ajax");
        }
    }
}

?>