<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->func("common");
mload()->classs("TyAccount");
$routers = str_replace("//", "/", trim($_GPC["r"], "/"));
$routers = explode(".", $routers);
$_W["_do"] = !empty($_W["_do"]) ? $_W["_do"] : trim($_GPC["do"]);
$_W["_controller"] = !empty($_W["_controller"]) ? $_W["_controller"] : trim($_GPC["ctrl"]);
$_W["_action"] = trim($_GPC["ac"]);
$_W["_op"] = trim($_GPC["op"]);
$_W["_ta"] = trim($_GPC["ta"]);
$_W["_router"] = implode("/", array($_W["_controller"], $_W["_action"], $_W["_op"]));
$_plugins = pdo_getall("tiny_wmall_plugin", array(), array("name", "title"), "name");
$_W["_plugins"] = $_plugins;
in_array($_W["_controller"], array_keys($_plugins)) and define("IN_PLUGIN", 1);
if (in_array($_W["_controller"], array("seckill", "kanjia", "pintuan", "tongcheng", "haodian"))) {
    define("IN_GOHOME_WPLUGIN", 1);
    if (!defined("IN_PLUGIN")) {
        define("IN_PLUGIN", 1);
    }
}
if (in_array($_W["_action"], array("seckill", "kanjia", "pintuan", "tongcheng", "haodian"))) {
    define("IN_GOHOME_APLUGIN", 1);
}
if ($_W["_controller"] == "gohome" || defined("IN_GOHOME_WPLUGIN")) {
    define("IN_GOHOME", 1);
}
if (strexists($_W["siteurl"], "web/index.php")) {
    define("IN_MANAGE", 1);
} else {
    if (strexists($_W["siteurl"], "web/wagent.php")) {
        define("IN_PLUGIN", 1);
        define("IN_AGENT", 1);
    }
}
if (defined("IN_SYS")) {
    if (empty($_W["uniacid"])) {
        message("公众号信息错误,请重新管理公众号", url("account/display"), "info");
    }
    if ($_W["_controller"] == "store") {
        define("IN_MERCHANT", 1);
    }
    if (empty($_W["_controller"])) {
        $_W["_controller"] = "dashboard";
        $_W["_action"] = "index";
    }
    require WE7_WMALL_PATH . "inc/web/__init.php";
    $file_init = WE7_WMALL_PATH . "inc/web/" . $_W["_controller"] . "/__init.php";
    $file_path = WE7_WMALL_PATH . "inc/web/" . $_W["_controller"] . "/" . $_W["_action"] . ".inc.php";
    if (defined("IN_MERCHANT")) {
        $file_path = WE7_WMALL_PATH . "inc/web/" . $_W["_controller"] . "/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
        if (defined("IN_GOHOME_APLUGIN")) {
            $file_path = WE7_WMALL_PATH . "inc/web/" . $_W["_controller"] . "/gohome/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
        }
        if (!is_file($file_path)) {
            imessage("控制器 " . $_W["_controller"] . " 方法 " . $_W["_action"] . "/" . $_W["_op"] . " 未找到!", "", "info");
        }
    } else {
        if (defined("IN_PLUGIN")) {
            $plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
            require $plugin_init;
            $file_init = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/web/__init.php";
            $file_path = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/web/" . $_W["_action"] . ".inc.php";
            if (defined("IN_AGENT")) {
                $file_init = WE7_WMALL_PLUGIN_PATH . "agent/inc/web/__init.php";
                $file_init = WE7_WMALL_PATH . "inc/web/" . $_W["_controller"] . "/__init.php";
                $file_ctrl_init = WE7_WMALL_PLUGIN_PATH . "agent/inc/web/manage/" . $_W["_controller"] . "/__init.php";
                $file_path = WE7_WMALL_PLUGIN_PATH . "agent/inc/web/manage/" . $_W["_controller"] . "/" . $_W["_action"] . ".inc.php";
                if (in_array($_W["_controller"], array("errander", "bargain", "diypage", "zhunshibao")) || defined("IN_GOHOME")) {
                    define("IN_AGENT_PLUGIN", 1);
                    $plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
                    require $plugin_init;
                    $file_path = WE7_WMALL_PLUGIN_PATH . "agent/plugin/" . $_W["_controller"] . "/inc/web/" . $_W["_action"] . ".inc.php";
                    if (defined("IN_GOHOME_WPLUGIN")) {
                        $file_path = WE7_WMALL_PLUGIN_PATH . "agent/plugin/gohome/" . $_W["_controller"] . "/inc/web/" . $_W["_action"] . ".inc.php";
                    }
                }
            } else {
                if (defined("IN_GOHOME_WPLUGIN")) {
                    $file_path = WE7_WMALL_PLUGIN_PATH . "gohome/" . $_W["_controller"] . "/inc/web/" . $_W["_action"] . ".inc.php";
                }
            }
        }
    }
    if (is_file($file_init)) {
        require $file_init;
    }
    if (defined("IN_AGENT") && is_file($file_ctrl_init)) {
        require $file_ctrl_init;
    }
    if (!is_file($file_path)) {
        imessage("控制器 " . $_W["_controller"] . " 方法 " . $_W["_action"] . " 未找到!", "", "info");
    }
} else {
    if (!in_array($_GPC["from"], array("wxapp", "vue"))) {
        $_W["ochannel"] = "wechat";
        $_W["channel"] = $_W["ochannel"];
        require WE7_WMALL_PATH . "inc/mobile/__init.php";
        $file_init = WE7_WMALL_PATH . "inc/mobile/" . $_W["_controller"] . "/__init.php";
        $file_path = WE7_WMALL_PATH . "inc/mobile/" . $_W["_controller"] . "/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
        if (defined("IN_PLUGIN")) {
            $plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
            require $plugin_init;
            $file_init = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/mobile/__init.php";
            $file_path = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/mobile/" . $_W["_action"] . ".inc.php";
        }
        if (is_file($file_init)) {
            require $file_init;
        }
        if (!is_file($file_path)) {
            imessage("控制器 " . $_W["_controller"] . " 方法 " . $_W["_action"] . "/" . $_W["_op"] . " 未找到!", "close", "error");
        }
    } else {
        $_W["ochannel"] = "wxapp";
        $_W["channel"] = $_W["ochannel"];
        if ($_GPC["from"] == "wxapp") {
            define("IN_WXAPP", 1);
        } else {
            if ($_GPC["from"] == "vue") {
                $_W["ochannel"] = "wap";
                define("IN_VUE", 1);
            }
        }
        require WE7_WMALL_PATH . "inc/wxapp/__init.php";
        $file_init = WE7_WMALL_PATH . "inc/wxapp/" . $_W["_controller"] . "/__init.php";
        $file_path = WE7_WMALL_PATH . "inc/wxapp/" . $_W["_controller"] . "/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
        if ($_W["_controller"] == "plateform") {
            define("IN_PLATEFORM", 1);
            $file_init = "";
            require WE7_WMALL_PATH . "inc/wxapp/plateform/__init.php";
            if (in_array($_W["_action"], array_keys($_plugins))) {
                $file_init = WE7_WMALL_PATH . "inc/wxapp/" . $_W["_controller"] . "/plugin/" . $_W["_action"] . "/__init.php";
                $file_path = WE7_WMALL_PATH . "inc/wxapp/" . $_W["_controller"] . "/plugin/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
            }
        } else {
            if ($_W["_controller"] == "manage") {
                if (defined("IN_GOHOME_APLUGIN")) {
                    $file_path = WE7_WMALL_PATH . "inc/wxapp/" . $_W["_controller"] . "/gohome/" . $_W["_action"] . "/" . $_W["_op"] . ".inc.php";
                }
            } else {
                if (defined("IN_PLUGIN")) {
                    $plugin_init = WE7_WMALL_PLUGIN_PATH . "__init.php";
                    require $plugin_init;
                    $file_init = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/wxapp/__init.php";
                    $file_path = WE7_WMALL_PLUGIN_PATH . (string) $_W["_controller"] . "/inc/wxapp/" . $_W["_action"] . ".inc.php";
                    if (defined("IN_GOHOME_WPLUGIN")) {
                        $file_init = WE7_WMALL_PLUGIN_PATH . "gohome/" . $_W["_controller"] . "/inc/wxapp/__init.php";
                        $file_path = WE7_WMALL_PLUGIN_PATH . "gohome/" . $_W["_controller"] . "/inc/wxapp/" . $_W["_action"] . ".inc.php";
                    }
                }
            }
        }
        if (is_file($file_init)) {
            require $file_init;
        }
        if (!is_file($file_path)) {
            imessage(error(-1, "控制器wxapp " . $_W["_controller"] . " 方法 " . $_W["_action"] . "/" . $_W["_op"] . " 未找到!"), "", "ajax");
        }
    }
}
require $file_path;

?>