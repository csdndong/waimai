<?php
defined("IN_IA") or exit("Access Denied");
mload()->model("plugin");
mload()->model("cloud");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$plugins = plugin_fetchall();
if ($op == "index") {
    $_W["page"]["title"] = "应用中心";
    $_W["plugin_types"] = plugin_types();
    $perms = get_account_perm("plugins");
    $_W["plugins"] = array();
    $config_plugincenter = get_plugin_config("plugincenter.basic");
    if ($config_plugincenter["status"] == 1 && !empty($config_plugincenter["pluginname"])) {
        if (empty($perms)) {
            $perms = $config_plugincenter["pluginname"];
            $insert = array("uniacid" => $_W["uniacid"], "plugins" => iserializer($perms));
            pdo_insert("tiny_wmall_perm_account", $insert);
        } else {
            $perms = array_merge($perms, $config_plugincenter["pluginname"]);
            $perms = array_unique($perms);
            pdo_update("tiny_wmall_perm_account", array("plugins" => iserializer($perms)), array("uniacid" => $_W["uniacid"]));
        }
    }
    $perm_logs = pdo_getall("tiny_wmall_plugincenter_grant_log", array("uniacid" => $_W["uniacid"]), array("pluginname"), "pluginname");
    if (!empty($perm_logs)) {
        $perm_logs = array_keys($perm_logs);
    }
    if (!empty($perms)) {
        $perms_log_update = $perms;
        if (!empty($perm_logs)) {
            $perms_log_update = array_diff($perms, $perm_logs);
        }
        if (!empty($perms_log_update)) {
            mload()->model("plugincenter");
            plugincenter_update_grant_log($perms_log_update, array("uniacid" => $_W["uniacid"], "month" => 0, "type" => "system"));
        }
    }
    if ($config_plugincenter["status"] == 1 && !empty($perm_logs)) {
        $perms_available = $perms;
        if (empty($perms)) {
            $perms = $perm_logs;
        } else {
            $perms = array_merge($perms, $perm_logs);
        }
    }
    foreach ($plugins as $row) {
        if (!empty($perms) && !in_array($row["name"], $perms)) {
            continue;
        }
        if (check_perm($row["name"])) {
            if (!empty($perms_available) && !in_array($row["name"], $perms_available)) {
                $row["is_overtime"] = 1;
            }
            $_W["plugins"][$row["type"]][] = $row;
            $i++;
        }
    }
    if (!$i && empty($config_plugincenter["status"])) {
    imessage("没有可用的插件,请联系平台管理员开通", "", "info");
    }
} else {
    if ($op == "perm") {
        $_W["page"]["title"] = "授权记录";
        $condition .= " where uniacid = :uniacid";
        $params = array(":uniacid" => $_W["uniacid"]);
        $type = isset($_GPC["type"]) ? trim($_GPC["type"]) : "";
        if (!empty($type)) {
            $condition .= " and type = :type";
            $params[":type"] = $type;
        }
        $pluginname = isset($_GPC["pluginname"]) ? trim($_GPC["pluginname"]) : "";
        if (!empty($pluginname)) {
            $condition .= " and pluginname = :pluginname";
            $params[":pluginname"] = $pluginname;
        }
        $page = max(1, intval($_GPC["page"]));
        $psize = 20;
        $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_plugincenter_grant_log") . $condition, $params);
        $logs = pdo_fetchall("select * from " . tablename("tiny_wmall_plugincenter_grant_log") . $condition . " order by id desc LIMIT " . ($page - 1) * $psize . "," . $psize, $params);
        $pager = pagination($total, $page, $psize);
    }
}
include itemplate("plugin/index");

?>