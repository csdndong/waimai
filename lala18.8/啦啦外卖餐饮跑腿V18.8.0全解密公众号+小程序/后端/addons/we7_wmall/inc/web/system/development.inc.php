<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
$configfile = IA_ROOT . "/data/config.php";
require $configfile;
$_W["config"] = $config;
if ($op == "index") {
    $_W["page"]["title"] = "调试模式";
    $module = pdo_get("modules", array("name" => "we7_wmall"));
    if ($_W["ispost"]) {
        set_global_config("development", intval($_GPC["development"]));
        set_global_config("development_delivery_location", intval($_GPC["development_delivery_location"]));
        set_global_config("slog_status", intval($_GPC["slog_status"]));
        $version = trim($_GPC["version"]);
        if (!empty($version) && $module["version"] != $version) {
            pdo_run("update ims_modules set version = '" . $version . "' where name = 'we7_wmall' ");
            load()->model("cache");
            load()->model("setting");
            load()->object("cloudapi");
            cache_updatecache();
        }
        imessage(error(0, "调试模式设置成功"), referer(), "ajax");
    }
    $config_global = $_W["we7_wmall"]["global"];
} else {
    if ($op == "agent_config") {
        mload()->model("agent");
        $agents = get_agents();
        foreach ($agents as $val) {
            $sysset = get_agent_system_config("", $val["id"]);
            if (!empty($sysset["takeout"]["order"]["notify_rule_clerk"])) {
                unset($sysset["takeout"]["order"]["notify_rule_clerk"]);
                unset($sysset["takeout"]["order"]["notify_rule_deliveryer"]);
                unset($sysset["takeout"]["order"]["pay_time_limit"]);
                unset($sysset["takeout"]["order"]["handle_time_limit"]);
                unset($sysset["takeout"]["order"]["auto_success_hours"]);
                unset($sysset["takeout"]["order"]["deliveryer_collect_time_limit"]);
                pdo_update("tiny_wmall_agent", array("sysset" => iserializer($sysset)), array("uniacid" => $_W["uniacid"], "id" => $val["id"]));
            }
        }
        imessage(error(0, "处理成功"), referer(), "ajax");
    } else {
        if ($op == "creditshop") {
            if ($_W["ispost"]) {
                $sql = "drop table if exists ims_tiny_wmall_creditshop_goods;\r\n\t\t\tCREATE TABLE IF NOT EXISTS `ims_tiny_wmall_creditshop_goods` (\r\n\t\t\t  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,\r\n\t\t\t  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',\r\n\t\t\t  `title` varchar(50) CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  `category_id` int(10) NOT NULL,\r\n\t\t\t  `type` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',\r\n\t\t\t  `thumb` varchar(255) CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  `old_price` varchar(10) CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  `chance` tinyint(3) unsigned NOT NULL,\r\n\t\t\t  `totalday` tinyint(3) unsigned NOT NULL,\r\n\t\t\t  `use_credit1` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '0',\r\n\t\t\t  `use_credit2` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '0',\r\n\t\t\t  `description` text CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',\r\n\t\t\t  `credit2` varchar(10) CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0',\r\n\t\t\t  `redpacket` text CHARACTER SET utf8 NOT NULL,\r\n\t\t\t  PRIMARY KEY (`id`),\r\n\t\t\t  KEY `uniacid` (`uniacid`),\r\n\t\t\t  KEY `type` (`type`)\r\n\t\t\t) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;\r\n\t\t\t";
                pdo_run($sql);
                imessage(error(0, "处理成功"), referer(), "ajax");
            }
        } else {
            if ($op == "session") {
                if ($_W["ispost"]) {
                    $sql = "TRUNCATE ims_core_sessions";
                    pdo_run($sql);
                    imessage(error(0, "处理成功"), referer(), "ajax");
                }
            } else {
                if ($op == "goodsprice") {
                    if ($_W["ispost"]) {
                        $sql = "update ims_tiny_wmall_goods set ts_price = price";
                        pdo_run($sql);
                        imessage(error(0, "处理成功"), referer(), "ajax");
                    }
                } else {
                    if ($op == "goodstype") {
                        if ($_W["ispost"]) {
                            $sql = "update ims_tiny_wmall_goods set type = 3";
                            pdo_run($sql);
                            imessage(error(0, "处理成功"), referer(), "ajax");
                        }
                    } else {
                        if ($op == "plugin") {
                            if ($_W["ispost"]) {
                                $sql = "TRUNCATE ims_tiny_wmall_plugin";
                                pdo_run($sql);
                                imessage(error(0, "处理成功"), referer(), "ajax");
                            }
                        } else {
                            if ($op == "discountprice") {
                                if ($_W["ispost"]) {
                                    set_system_config("itime", 0);
                                    $sql = "alter table `ims_tiny_wmall_goods` drop discount_price";
                                    pdo_run($sql);
                                    imessage(error(0, "处理成功"), referer(), "ajax");
                                }
                            } else {
                                if ($op == "develop_status") {
                                    if ($_W["ispost"]) {
                                        $_W["setting"]["copyright"]["develop_status"] = 0;
                                        $test = setting_save($_W["setting"]["copyright"], "copyright");
                                        imessage(error(0, "处理成功"), referer(), "ajax");
                                    }
                                } else {
                                    if ($op == "bargain") {
                                        if ($_W["ispost"]) {
                                            $goods = pdo_fetchall("select a.id,a.discount_price,a.goods_id,b.sid,b.title,c.uniacid from " . tablename("tiny_wmall_activity_bargain_goods") . "as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id left join " . tablename("tiny_wmall_store") . " as c on b.sid = c.id where 1 order by c.is_rest asc, a.mall_displayorder desc limit 10000");
                                            if (!empty($goods)) {
                                                foreach ($goods as $item) {
                                                    $item["uniacid"] = intval($item["uniacid"]);
                                                    if (empty($item["title"]) || empty($item["uniacid"])) {
                                                        pdo_delete("tiny_wmall_activity_bargain_goods", array("id" => $item["id"]));
                                                    }
                                                }
                                            }
                                            imessage(error(0, "处理成功"), referer(), "ajax");
                                        }
                                    } else {
                                        if ($op == "file") {
                                            if ($_W["ispost"]) {
                                                $files = array(array("from" => "api.php", "to" => "app/api.php"), array("from" => "wxapp.php", "to" => "app/wxapp.php"), array("from" => "wmerchant.php", "to" => "web/wmerchant.php"), array("from" => "wagent.php", "to" => "web/wagent.php"));
                                                load()->func("file");
                                                foreach ($files as $file) {
                                                    $src = MODULE_ROOT . "/" . $file["from"];
                                                    $filename = IA_ROOT . "/" . $file["to"];
                                                    mkdirs(dirname($filename));
                                                    copy($src, $filename);
                                                }
                                                imessage(error(0, "处理成功"), referer(), "ajax");
                                            }
                                        } else {
                                            if ($op == "py") {
                                                if ($_W["ispost"]) {
                                                load()->func("file");
                                                $src = MODULE_ROOT . "/py.php";
                                                $filename = IA_ROOT . "/py.php";
                                                mkdirs(dirname($filename));
                                                copy($src, $filename);
                                                imessage(error(0, "处理成功"), referer(), "ajax");
                                            }
                                            } else {
                                                if ($op == "py" && $_W["ispost"]) {
                                                    cache_build_template();
                                                    cache_build_users_struct();
                                                    cache_build_setting();
                                                    cache_build_account_modules();
                                                    cache_build_account();
                                                    cache_build_accesstoken();
                                                    cache_build_frame_menu();
                                                    cache_build_module_subscribe_type();
                                                    cache_build_platform();
                                                    imessage(error(0, "缓存更新成功"), referer(), "ajax");
                                            }
                                        }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
include itemplate("system/development");

?>