<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("cron");
mload()->model("clerk");
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "task";
set_time_limit(0);
if ($op == "task") {
    cron_order();
    exit("success");
}
if ($op == "order_notice") {
    clerk_info_init();
    if ($_GPC["_ac"] == "takeout" && $_GPC["_status_order_notice"]) {
        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "status" => 1, "is_pay" => 1));
        if (!empty($order)) {
            exit("success");
        }
        exit("error");
    }
    if ($_GPC["_ctrl"] == "errander" && $_GPC["_ac"] == "order" && $_GPC["_status_errander_notice"]) {
        $order = pdo_get("tiny_wmall_errander_order", array("uniacid" => $_W["uniacid"], "status" => 1, "is_pay" => 1));
        if (!empty($order)) {
            exit("success");
        }
        exit("error");
    }
    if ($_GPC["_ctrl"] == "store" && $_GPC["_ac"] == "order" && $_GPC["_status_store_order_notice"]) {
        $sid = intval($_GPC["__sid"]);
        $order = pdo_get("tiny_wmall_order", array("uniacid" => $_W["uniacid"], "sid" => $sid, "status" => 1, "is_pay" => 1));
        if (!empty($order)) {
            exit("success");
        }
        exit("error");
    }
} else {
    if ($op == "ignoreupdate") {
        if (empty($_GPC["__ieweishopversion"])) {
            load()->model("cloud");
            $manifest_cloud = cloud_m_upgradeinfo("we7_wmall");
            if (!is_error($manifest_cloud) && !empty($manifest_cloud["site_branch"]["version"])) {
                $version = $manifest_cloud["site_branch"]["version"]["version"];
                $module = pdo_get("modules", array("name" => "we7_wmall"));
                if (!empty($version) && $module["version"] != $version) {
                    pdo_run("update ims_modules set version = '" . $version . "' where name = 'we7_wmall' ");
                    load()->model("cache");
                    load()->model("setting");
                    load()->object("cloudapi");
                    cache_updatecache();
                }
            }
            isetcookie("__eweishopversion", 1, 1800);
        }
        exit("success");
    }
}

?>