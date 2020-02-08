<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
if (!function_exists("cloud_w_build")) {
    function cloud_w_build()
    {
        if (MODULE_FAMILY == "wxapp") {
            load()->func("file");
            $path = MODULE_ROOT . "/template/vue/";
            rmdirs($path, false);
        }
        $pars = cloud_w_build_params();
        if (empty($pars["mcrypt"])) {
            return error(-1, "服务器不支持mcrypt组建，请先安装mcrypt组建");
        }
        $pars["method"] = "upgrade";
        $dat = cloud_w_request("http://localhost/app/index.php?i=0&c=entry&do=upgrade&op=build&v=v2&m=tiny_manage", $pars);
        $file = IA_ROOT . "/data/we7_wmall.build";
        $ret = cloud_w_shipping_parse($dat, $file);
        if (!is_error($ret)) {
            if ($ret["family"] != MODULE_FAMILY) {
                if ($ret["family"] == "basic") {
                    cloud_w_upgrade_version($ret["family"], "2.0.0", "1000");
                } else {
                    if ($ret["family"] == "errander_deliveryerApp") {
                        cloud_w_upgrade_version($ret["family"], "5.0.0", "1000");
                    } else {
                        if ($ret["family"] == "wxapp") {
                            cloud_w_upgrade_version($ret["family"], "2.0.0", "1000");
                        }
                    }
                }
                return error(-2, "你购买的版本和系统当前不一致, 系统已处理这个问题, 请重新运行自动更新程序。请勿随意更改模块版本, 多次更改模块版本, 系统会自动将站点拉入黑名单");
            }
            $files = array();
            if (!empty($ret["files"])) {
                foreach ($ret["files"] as $file) {
                    $entry = MODULE_ROOT . $file["path"];
                    if (!is_file($entry) || md5_file($entry) != $file["checksum"]) {
                        $files[] = $file["path"];
                    }
                }
            }
            $ret["files"] = $files;
            $schemas = array();
            if (!empty($ret["schemas"])) {
                load()->func("db");
                foreach ($ret["schemas"] as $remote) {
                    $name = substr($remote["tablename"], 4);
                    $local = cloud_w_db_table_schema(pdo(), $name);
                    unset($remote["increment"]);
                    unset($local["increment"]);
                    if (empty($local)) {
                        $schemas[] = $remote;
                    } else {
                        $sqls = db_table_fix_sql($local, $remote);
                        if (!empty($sqls)) {
                            $schemas[] = $remote;
                        }
                    }
                }
            }
            $ret["schemas"] = $schemas;
            if (!empty($ret["schemas"])) {
                $ret["database"] = array();
                foreach ($ret["schemas"] as $remote) {
                    $row = array();
                    $row["tablename"] = $remote["tablename"];
                    $name = substr($remote["tablename"], 4);
                    $local = cloud_w_db_table_schema(pdo(), $name);
                    unset($remote["increment"]);
                    unset($local["increment"]);
                    if (empty($local)) {
                        $row["new"] = true;
                    } else {
                        $row["new"] = false;
                        $row["fields"] = array();
                        $row["indexes"] = array();
                        $diffs = db_schema_compare($local, $remote);
                        if (!empty($diffs["fields"]["less"])) {
                            $row["fields"] = array_merge($row["fields"], $diffs["fields"]["less"]);
                        }
                        if (!empty($diffs["fields"]["diff"])) {
                            $row["fields"] = array_merge($row["fields"], $diffs["fields"]["diff"]);
                        }
                        if (!empty($diffs["indexes"]["less"])) {
                            $row["indexes"] = array_merge($row["indexes"], $diffs["indexes"]["less"]);
                        }
                        if (!empty($diffs["indexes"]["diff"])) {
                            $row["indexes"] = array_merge($row["indexes"], $diffs["indexes"]["diff"]);
                        }
                        $row["fields"] = implode($row["fields"], " ");
                        $row["indexes"] = implode($row["indexes"], " ");
                    }
                    $ret["database"][] = $row;
                }
            }
            $ret["upgrade"] = false;
            if (!empty($ret["files"]) || !empty($ret["schemas"]) || !empty($ret["scripts"])) {
                $ret["upgrade"] = true;
            }
            $upgrade = array();
            $upgrade["upgrade"] = $ret["upgrade"];
            $upgrade["lastupdate"] = TIMESTAMP;
            cache_write("we7_wmall_upgrade", $upgrade);
        }
        return $ret;
    }
}
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * $sn$
 */
function cloud_w_request($url, $post = "", $extra = array(), $timeout = 60)
{
    load()->func("communication");
    $response = ihttp_request($url, $post, $extra, $timeout);
    if (is_error($response)) {
        return error(-1, "错误: " . $response["message"]);
    }
    return $response["content"];
}
function cloud_w_plugins()
{
    global $_W;
    $plugins = pdo_getall("tiny_wmall_plugin", array(), array("name"), "name");
    $plugins = array_keys($plugins);
    if (is_file(MODULE_ROOT . "/template/vue/index.html")) {
        $plugins[] = "vue";
    }
    return $plugins;
}
function cloud_w_query_auth($code, $module)
{
    global $_W;
    $plugins = cloud_w_plugins();
    $params = array("url" => rtrim($_W["siteroot"], "/"), "host" => $_SERVER["HTTP_HOST"], "code" => $code, "site_id" => $_W["setting"]["site"]["key"], "module" => $module, "method" => "query_auth", "uniacid" => $_W["uniacid"], "plugins" => $plugins);
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=auth&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"];
}
function cloud_w_plugin_auth()
{
    global $_W;
    $plugins = cloud_w_plugins();
    $params = array("url" => rtrim($_W["siteroot"], "/"), "host" => $_SERVER["HTTP_HOST"], "site_id" => $_W["setting"]["site"]["key"], "module" => "we7_wmall", "method" => "query_auth", "uniacid" => $_W["uniacid"], "plugins" => $plugins);
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=plugin&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    $result = $result["message"];
    if ($result["errno"] == -1001 && !empty($result["message"])) {
        $blacks = "'" . implode("','", $result["message"]) . "'";
        pdo_run("delete from ims_tiny_wmall_plugin where name in (" . $blacks . ")");
    }
    return true;
}
function cloud_w_build_wxapp_authorize_params($wxapp_type = "we7_wmall", $uniacid = 0)
{
    global $_W;
    global $_GPC;
    if (empty($uniacid)) {
        $uniacid = $_W["uniacid"];
    }
    $account = pdo_get("account_wechats", array("uniacid" => $uniacid), array("key", "name", "account"));
    if (empty($account)) {
        return error(-1, "公众号信息不存在");
    }
    if (empty($account["key"])) {
        return error(-1, "公众号Appid未完善");
    }
    $account["qrcode_url"] = tomedia("qrcode_" . $uniacid . ".jpg");
    $account["head_img"] = tomedia("headimg_" . $uniacid . ".jpg");
    $homepage = "pages/home/index";
    $menus = array();
    if ($wxapp_type == "we7_wmall") {
        $wxapp = get_plugin_config("wxapp");
        $wxapp["menu"] = json_decode(base64_decode($wxapp["menu"]), true);
        if (empty($wxapp["menu"])) {
            return error(-1, "未设置小程序的底部导航");
        }
        if ($wxapp["menu"]["type"] == 1) {
            $diyWxappMenuId = intval($wxapp["wxappmenu"]["takeout"]);
            $temp = pdo_get("tiny_wmall_diypage_menu", array("uniacid" => $_W["uniacid"], "id" => $diyWxappMenuId, "version" => 2));
            if (empty($temp)) {
                return error(-1, "小程序采用了自定义底部导航，但未设置底部导航菜单");
            }
            $diyWxappMenus = json_decode(base64_decode($temp["data"]), true);
            if (empty($diyWxappMenus) || empty($diyWxappMenus["data"])) {
                return error(-1, "小程序采用了自定义底部导航，但底部导航菜单数据为空");
            }
            $firstPage = reset($diyWxappMenus["data"]);
            $homepage = $firstPage["link"];
        } else {
            if (empty($wxapp["menu"]["data"])) {
                return error(-1, "小程序采用了原生底部导航, 但底部导航菜单数据为空");
            }
            $firstPage = reset($wxapp["menu"]["data"]);
            $homepage = $firstPage["pagePath"];
            $menus = array("selectedColor" => $wxapp["menu"]["css"]["selectedColor"], "backgroundColor" => $wxapp["menu"]["css"]["backgroundColor"], "color" => $wxapp["menu"]["css"]["color"], "list" => array_values($wxapp["menu"]["data"]));
        }
        $extPages = array();
        $pages = $wxapp["extPages"];
        if (!empty($pages)) {
            foreach ($pages as $key => $page) {
                $extPage = array();
                foreach ($page as $item => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    $extPage[$item] = trim($val);
                }
                if (!empty($extPage)) {
                    $extPages[$key] = $extPage;
                }
            }
        }
        $window = $wxapp["window"];
        $miniProgramAppIdList = array();
        if (!empty($wxapp["miniProgramAppIdList"])) {
            foreach ($wxapp["miniProgramAppIdList"] as $row) {
                $miniProgramAppIdList[] = $row["appid"];
            }
        }
    } else {
        if ($wxapp_type == "we7_wmall_deliveryer") {
            $wxapp = get_plugin_config("wxapp.deliveryer");
            $wxapp["menu"] = json_decode(base64_decode($wxapp["menu"]), true);
            $menus = array();
            if (!empty($wxapp["menu"]["data"])) {
                $menus = array("selectedColor" => $wxapp["menu"]["css"]["selectedColor"], "backgroundColor" => $wxapp["menu"]["css"]["backgroundColor"], "color" => $wxapp["menu"]["css"]["color"], "list" => array_values($wxapp["menu"]["data"]));
            }
            $window = array();
            $extPages = array();
        } else {
            if ($wxapp_type == "we7_wmall_manager") {
                $wxapp = get_plugin_config("wxapp.manager");
                $wxapp["menu"] = json_decode(base64_decode($wxapp["menu"]), true);
                $menus = array();
                if (!empty($wxapp["menu"]["data"])) {
                    $menus = array("selectedColor" => $wxapp["menu"]["css"]["selectedColor"], "backgroundColor" => $wxapp["menu"]["css"]["backgroundColor"], "color" => $wxapp["menu"]["css"]["color"], "list" => array_values($wxapp["menu"]["data"]));
                }
                $window = array();
                $extPages = array();
            }
        }
    }
    $params = array("url" => rtrim($_W["siteroot"], "/"), "host" => $_SERVER["HTTP_HOST"], "module" => "we7_wmall", "wxapp_type" => $wxapp_type, "plugins" => cloud_w_plugins(), "method" => "wxapp_authorize_url", "uniacid" => $uniacid, "account" => $account, "wxapp" => array("siteurl" => trim($_GPC["siteurl"]), "user_desc" => trim($_GPC["user_desc"]), "menu" => $menus, "extPages" => $extPages, "window" => $window, "navigateToMiniProgramAppIdList" => $miniProgramAppIdList, "use_diy_home" => $wxapp["diy"]["use_diy_home"], "test" => $wxapp["test"], "category" => array("first_id" => trim($_GPC["first_id"]), "second_id" => trim($_GPC["second_id"]), "third_id" => trim($_GPC["third_id"]), "first_class" => trim($_GPC["first_class"]), "second_class" => trim($_GPC["second_class"]), "third_class" => trim($_GPC["third_class"]))));
    return $params;
}
function cloud_w_get_wxapp_authorize_url($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=authorize_url&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    if (is_error($result["message"])) {
        return $result["message"];
    }
    return $result["message"];
}
function cloud_w_get_wxapp_authorize_info($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=info&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"];
}
function cloud_w_wxapp_bind_tester($wechatid, $wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $params["wechatid"] = $wechatid;
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=bind_tester&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_unbind_tester($wechatid, $wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $params["wechatid"] = $wechatid;
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=unbind_tester&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (is_error($result)) {
        return $result["message"];
    }
    return error("0", "解除绑定成功");
}
function cloud_w_wxapp_get_tester($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=get_tester&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_commit($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=commit&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_get_commit_log($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=get_commitlog&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_submit_audit($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    if ($wxapp_type == "we7_wmall_deliveryer" && (empty($params["wxapp"]["test"]["username"]) || empty($params["wxapp"]["test"]["password"]))) {
        return error(-1, "配送员小程序演示账号密码不能为空");
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=submit_audit&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_get_category($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=get_category&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "鏈煡閿欒");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_get_url($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=get_url&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_undocodeaudit($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=undocodeaudit&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_release($wxapp_type = "we7_wmall", $uniacid = 0)
{
    $params = cloud_w_build_wxapp_authorize_params($wxapp_type, $uniacid);
    if (is_error($params)) {
        return $params;
    }
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=release&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"]["message"];
}
function cloud_w_wxapp_getanywxacode($path = "", $scene = "", $wxapp_type = "we7_wmall")
{
    $params = cloud_w_build_wxapp_authorize_params();
    if (is_error($params)) {
        return $params;
    }
    $params["path"] = $path;
    $params["scene"] = $scene;
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxapp&op=getanywxacode&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if ($result["errcode"] != 0) {
        if (strexists($result["errmsg"], "invalid page")) {
            return error(-1, (string) $result["errcode"] . ": " . $result["errmsg"] . "。<br><span style='font-size: 20px; color: red'>请先确认小程序是否已通过微信官方审核并已经发布。说明：小程序二维码只有在小程序发布后才能生成！</span>");
        }
        return error(-1, (string) $result["errcode"] . ": " . $result["errmsg"]);
    }
    if ($result["message"] && is_error($result["message"])) {
        return error(-1, (string) $result["message"]["message"]);
    }
    return $content;
}
function cloud_w_get_wxapp_authorize_list($wxapp_type = "we7_wmall")
{
    global $_W;
    $params = array("url" => rtrim($_W["siteroot"], "/"), "host" => $_SERVER["HTTP_HOST"], "module" => "we7_wmall");
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxappauth&op=authorize_list&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"];
}
function cloud_w_get_wxapp_authorize_del($ids, $wxapp_type = "we7_wmall")
{
    global $_W;
    global $_GPC;
    $params = array("url" => rtrim($_W["siteroot"], "/"), "host" => $_SERVER["HTTP_HOST"], "module" => "we7_wmall", "id" => $ids);
    $content = cloud_w_request("http://localhost/app/index.php?i=1&c=entry&do=wxappauth&op=authorize_del&v=v2&m=tiny_manage", $params);
    if (is_error($content)) {
        return $content;
    }
    $result = @json_decode($content, true);
    if (empty($result["message"])) {
        return error(-1, "未知错误");
    }
    return $result["message"];
}
function cloud_w_client_define()
{
    return array("/model/cloud.mod.php");
}
function cloud_w_build_params()
{
    global $_W;
    $cache = cache_read("we7_wmall");
    if (empty($cache)) {
        $cache = get_global_config("auth");
    }
    $pars = array();
    $pars["url"] = $_W["siteroot"];
    $pars["ip"] = CLIENT_IP;
    $pars["family"] = MODULE_FAMILY;
    $pars["version"] = MODULE_VERSION;
    $pars["release"] = MODULE_RELEASE_DATE;
    $pars["code"] = $cache["code"];
    $pars["cloud_id"] = $cache["cloud_id"];
    $pars["plugins"] = cloud_w_plugins();
    $pars["ionCube"] = extension_loaded("ionCube Loader") ? 1 : 0;
    $pars["mcrypt"] = extension_loaded("mcrypt") ? 1 : 0;
    $pars["loader"] = file_exists(IA_ROOT . "/web/loader.php") ? 1 : 0;
    $pars["php_version"] = PHP_VERSION;
    $clients = cloud_w_client_define();
    $string = "";
    foreach ($clients as $cli) {
        $string .= md5_file(MODULE_ROOT . $cli);
    }
    $pars["client"] = md5($string);
    return $pars;
}
function cloud_w_shipping_parse($dat, $file)
{
    if (is_error($dat)) {
        return error(-1, "网络传输错误, 请检查您的cURL是否可用, 或者服务器网络是否正常. " . $dat["message"]);
    }
    $dat_bak = $dat;
    $dat = @json_decode($dat, true);
    if (!is_array($dat)) {
        return error(-1, $dat_bak);
    }
    $dat = $dat["message"];
    if (is_error($dat)) {
        return $dat;
    }
    if (strlen($dat["message"]) != 32) {
        return error(-1, "云服务平台向您的服务器传输数据过程中出现错误, 这个错误可能是由于您的授权码和云服务不一致, 请联系模块作者处理. 传输原始数据:" . $dat["meta"]);
    }
    $data = @file_get_contents($file);
    if (empty($data)) {
        return error(-1, "没有接收到服务器的传输的数据.");
    }
    @unlink($file);
    $ret = @iunserializer($data);
    if (empty($data) || empty($ret) || $dat["message"] != $ret["secret"]) {
        return error(-1, "云服务平台向您的服务器传输的数据校验失败, 可能是因为您的网络不稳定, 或网络不安全, 请稍后重试.");
    }
    $ret = iunserializer($ret["data"]);
    return $ret;
}
function cloud_w_build_base()
{
    $pars = cloud_w_build_params();
    $pars["method"] = "upgrade";
    $dat = cloud_w_request("http://localhost/app/index.php?i=0&c=entry&do=upgrade&op=build&v=v2&m=tiny_manage", $pars);
    $file = IA_ROOT . "/data/we7_wmall.build";
    $ret = cloud_w_shipping_parse($dat, $file);
    if (!is_error($ret)) {
        if ($ret["family"] != MODULE_FAMILY) {
            if ($ret["family"] == "basic") {
                cloud_w_upgrade_version($ret["family"], "2.0.0", "1000");
            } else {
                if ($ret["family"] == "errander_deliveryerApp") {
                    cloud_w_upgrade_version($ret["family"], "5.0.0", "1000");
                } else {
                    if ($ret["family"] == "wxapp") {
                        cloud_w_upgrade_version($ret["family"], "2.0.0", "1000");
                    }
                }
            }
            return error(-2, "你购买的版本和系统当前不一致, 系统已处理这个问题, 请重新运行自动更新程序。请勿随意更改模块版本, 多次更改模块版本, 系统会自动将站点拉入黑名单");
        }
        $files = array();
        if (!empty($ret["files"])) {
            foreach ($ret["files"] as $file) {
                $entry = MODULE_ROOT . $file["path"];
                if (!is_file($entry) || md5_file($entry) != $file["checksum"]) {
                    $files[] = $file["path"];
                }
            }
        }
        $ret["files"] = $files;
        $schemas = array();
        if (!empty($ret["schemas"])) {
            load()->func("db");
            foreach ($ret["schemas"] as $remote) {
                $name = substr($remote["tablename"], 4);
                $local = cloud_w_db_table_schema(pdo(), $name);
                unset($remote["increment"]);
                unset($local["increment"]);
                if (empty($local)) {
                    $schemas[] = $remote;
                } else {
                    $sqls = db_table_fix_sql($local, $remote);
                    if (!empty($sqls)) {
                        $schemas[] = $remote;
                    }
                }
            }
        }
        $ret["schemas"] = $schemas;
        if (!empty($ret["schemas"])) {
            $ret["database"] = array();
            foreach ($ret["schemas"] as $remote) {
                $row = array();
                $row["tablename"] = $remote["tablename"];
                $name = substr($remote["tablename"], 4);
                $local = cloud_w_db_table_schema(pdo(), $name);
                unset($remote["increment"]);
                unset($local["increment"]);
                if (empty($local)) {
                    $row["new"] = true;
                } else {
                    $row["new"] = false;
                    $row["fields"] = array();
                    $row["indexes"] = array();
                    $diffs = db_schema_compare($local, $remote);
                    if (!empty($diffs["fields"]["less"])) {
                        $row["fields"] = array_merge($row["fields"], $diffs["fields"]["less"]);
                    }
                    if (!empty($diffs["fields"]["diff"])) {
                        $row["fields"] = array_merge($row["fields"], $diffs["fields"]["diff"]);
                    }
                    if (!empty($diffs["indexes"]["less"])) {
                        $row["indexes"] = array_merge($row["indexes"], $diffs["indexes"]["less"]);
                    }
                    if (!empty($diffs["indexes"]["diff"])) {
                        $row["indexes"] = array_merge($row["indexes"], $diffs["indexes"]["diff"]);
                    }
                    $row["fields"] = implode($row["fields"], " ");
                    $row["indexes"] = implode($row["indexes"], " ");
                }
                $ret["database"][] = $row;
            }
        }
        $ret["upgrade"] = false;
        if (!empty($ret["files"]) || !empty($ret["schemas"]) || !empty($ret["scripts"])) {
            $ret["upgrade"] = true;
        }
        $upgrade = array();
        $upgrade["upgrade"] = $ret["upgrade"];
        $upgrade["lastupdate"] = TIMESTAMP;
        cache_write("we7_wmall_upgrade", $upgrade);
    }
    return $ret;
}
function cloud_w_build_script($packet)
{
    $scripts = array();
    $updatefiles = array();
    if (!empty($packet["scripts"])) {
        $updatedir = MODULE_ROOT . "/resource/update/";
        load()->func("file");
        rmdirs($updatedir, true);
        mkdirs($updatedir);
        $cfamily = MODULE_FAMILY;
        $cversion = MODULE_VERSION;
        $crelease = MODULE_RELEASE_DATE;
        $crelease_temp = intval($crelease);
        foreach ($packet["scripts"] as $script) {
            if ($script["version"] < $cversion && $script["release"] <= $crelease || 0 < $crelease_temp && $script["release"] <= $crelease) {
                continue;
            }
            $fname = "update(" . $cversion . "-" . $script["version"] . "_" . $script["release"] . ").php";
            $script["script"] = @base64_decode($script["script"]);
            if (empty($script["script"])) {
                continue;
            }
            $updatefile = $updatedir . $fname;
            file_put_contents($updatefile, $script["script"]);
            $updatefiles[] = $updatefile;
            $s = array_elements(array("message", "family", "version", "release"), $script);
            $s["fname"] = $fname;
            $scripts[] = $s;
        }
    }
    return $scripts;
}
function cloud_w_download($path)
{
    $pars = cloud_w_build_params();
    $pars["method"] = "download";
    $pars["path"] = $path;
    $pars["gz"] = function_exists("gzcompress") && function_exists("gzuncompress") ? "true" : "false";
    $headers = array("content-type" => "application/x-www-form-urlencoded");
    $dat = cloud_w_request("http://localhost/app/index.php?i=0&c=entry&do=upgrade&op=download&v=v2&m=tiny_manage", $pars, $headers, 300);
    if (is_error($dat)) {
        return error(-1, "网络存在错误， 请稍后重试。" . $dat["message"]);
    }
    $ret = @json_decode($dat, true);
    if (is_error($ret["message"])) {
        return $ret["message"];
    }
    return error(0, "success");
}
function cloud_w_lala_update_url($trade)
{
    $code = base64_encode($trade["code"]);
    $url = "http://localhost/web/index.php?c=plugin&a=index&do=detail&id=31&auth=" . $code;
    return $url;
}
function cloud_w_parse_build($post)
{
    $dat = __secure_decode($post);
    if (!empty($dat)) {
        $secret = random(32);
        $ret = array();
        $ret["data"] = $dat;
        $ret["secret"] = $secret;
        file_put_contents(IA_ROOT . "/data/we7_wmall.build", iserializer($ret));
        return error(0, $secret);
    }
    return error(-1, "文件传输失败");
}
function cloud_w_parse_schema($post)
{
    $dat = __secure_decode($post);
    if (!empty($dat)) {
        $secret = random(32);
        $ret = array();
        $ret["data"] = $dat;
        $ret["secret"] = $secret;
        file_put_contents(IA_ROOT . "/data/application.schema", iserializer($ret));
        exit($secret);
    }
}
function cloud_w_parse_download($post)
{
    $data = base64_decode($post);
    if (base64_encode($data) != $post) {
        $data = $post;
    }
    $ret = iunserializer($data);
    $gz = function_exists("gzcompress") && function_exists("gzuncompress");
    $file = base64_decode($ret["file"]);
    if ($gz) {
        $file = gzuncompress($file);
    }
    $cache = cache_read("we7_wmall");
    if (empty($cache)) {
        $cache = get_global_config("auth");
    }
    $string = md5($file) . $ret["path"] . $cache["code"];
    if (md5($string) == $ret["sign"]) {
        $path = IA_ROOT . $ret["path"];
        load()->func("file");
        @mkdirs(@dirname($path));
        file_put_contents($path, $file);
        $sign = md5(md5_file($path) . $ret["path"] . $cache["code"]);
        if ($ret["sign"] == $sign) {
            return error(0, "success");
        }
    }
    return error(-1, "文件校验失败");
}
function cloud_w_run_download()
{
    global $_GPC;
    $post = $_GPC["__input"];
    $ret = cloud_w_download($post["path"]);
    if (!is_error($ret)) {
        exit("success");
    }
    exit;
}
function cloud_w_run_script()
{
    global $_GPC;
    $post = $_GPC["__input"];
    $fname = $post["fname"];
    $entry = MODULE_ROOT . "/resource/update/" . $fname;
    if (is_file($entry) && preg_match("/^update\\(\\d{1,2}\\.\\d{1,2}\\.\\d{1,2}\\-\\d{1,2}\\.\\d{1}\\.\\d{1}\\_\\d{14}\\)\\.php\$/", $fname)) {
        $evalret = (include $entry);
        if (!empty($evalret)) {
            @unlink($entry);
            exit("success");
        }
    }
    exit("failed");
}
function cloud_w_run_schemas($packet)
{
    global $_GPC;
    $post = $_GPC["__input"];
    $tablename = $post["table"];
    foreach ($packet["schemas"] as $schema) {
        if (substr($schema["tablename"], 4) == $tablename) {
            $remote = $schema;
            break;
        }
    }
    if (!empty($remote)) {
        load()->func("db");
        $local = cloud_w_db_table_schema(pdo(), $tablename);
        $sqls = db_table_fix_sql($local, $remote);
        $error = false;
        foreach ($sqls as $sql) {
            if (pdo_query($sql) === false) {
                $error = true;
                break;
            }
        }
        if (!$error) {
            exit("success");
        }
    }
    exit;
}
function __secure_decode($post)
{
    global $_W;
    $data = base64_decode($post);
    if (base64_encode($data) != $post) {
        $data = $post;
    }
    $ret = iunserializer($data);
    $cache = cache_read("we7_wmall");
    if (empty($cache)) {
        $cache = get_global_config("auth");
    }
    if ($_W["ifrom"] == "we7") {
        return $ret["data"];
    }
    $string = $ret["data"] . $cache["code"];
    if (md5($string) == $ret["sign"]) {
        return $ret["data"];
    }
    return false;
}
function cloud_w_db_table_schema($db, $tablename = "")
{
    $result = $db->fetch("SHOW TABLE STATUS LIKE '" . trim($db->tablename($tablename), "`") . "'");
    if (empty($result)) {
        return array();
    }
    $ret["tablename"] = $result["Name"];
    $ret["charset"] = $result["Collation"];
    $ret["engine"] = $result["Engine"];
    $ret["increment"] = $result["Auto_increment"];
    $result = $db->fetchall("SHOW FULL COLUMNS FROM " . $db->tablename($tablename));
    foreach ($result as $value) {
        $temp = array();
        $type = explode(" ", $value["Type"], 2);
        $temp["name"] = $value["Field"];
        $pieces = explode("(", $type[0], 2);
        $temp["type"] = $pieces[0];
        $temp["length"] = rtrim($pieces[1], ")");
        $temp["null"] = $value["Null"] != "NO";
        if (isset($value["Default"])) {
            $temp["default"] = $value["Default"];
        }
        $temp["signed"] = empty($type[1]);
        $temp["increment"] = $value["Extra"] == "auto_increment";
        $ret["fields"][$value["Field"]] = $temp;
    }
    $result = $db->fetchall("SHOW INDEX FROM " . $db->tablename($tablename));
    foreach ($result as $value) {
        $ret["indexes"][$value["Key_name"]]["name"] = $value["Key_name"];
        $ret["indexes"][$value["Key_name"]]["type"] = $value["Key_name"] == "PRIMARY" ? "primary" : ($value["Non_unique"] == 0 ? "unique" : "index");
        $ret["indexes"][$value["Key_name"]]["fields"][] = $value["Column_name"];
    }
    return $ret;
}
function cloud_store_url()
{
    $auth = get_global_config("auth");
    $code = 0;
    if (!empty($auth["code"])) {
        $code = base64_encode($auth["code"]);
    }
    $url = "http://localhost/web/index.php?c=plugin&auth=" . $code;
    return $url;
}
function databaseEngine_transfer()
{
    global $_W;
    return true;
}
function run_install_data()
{
}
function cloud_w_account($uniacid)
{
    global $_W;
    $config_waimai = get_system_config("", $uniacid);
    $config_mall = $config_waimai["mall"];
    $config_follow = $config_waimai["follow"];
    $account = array("title" => $config_mall["title"], "logo" => tomedia($config_mall["logo"]), "qrcode" => tomedia($config_follow["qrcode"]), "mobile" => $config_mall["mobile"], "wechatid" => $_W["account"]["account"]);
    load()->model("account");
    $_W["account"] = uni_fetch($uniacid);
    if (empty($account["title"])) {
        $account["title"] = $_W["account"]["name"];
    }
    if (empty($account["logo"])) {
        $account["logo"] = tomedia($_W["account"]["logo"]);
    }
    if (empty($account["qrcode"])) {
        $account["qrcode"] = tomedia($_W["account"]["qrcode"]);
    }
    return $account;
}
function cloud_w_order_stat($date = array())
{
    global $_GPC;
    if (empty($date)) {
        $date = $_GPC;
    }
    if ($date["auth"] != md5(base64_decode("bGFsYXdhaW1haWxhbGF3YWltYWk="))) {
        return error(-1, "授权码错误");
    }
    $condition = " where status = 5";
    $params = array();
    if (!empty($date["stat_day"])) {
        $condition .= " and stat_day = :stat_day";
        $params[":stat_day"] = $date["stat_day"];
    } else {
        if (!empty($date["startday"])) {
            $condition .= " and stat_day >= :startday";
            $params[":startday"] = $date["startday"];
        }
        if (!empty($date["endday"])) {
            $condition .= " and stat_day <= :endday";
            $params[":endday"] = $date["endday"];
        }
    }
    $condition .= " group by uniacid, stat_day";
    $orders = pdo_fetchall("select count(*) as total_num, uniacid, stat_day from " . tablename("tiny_wmall_order") . $condition, $params);
    $data = array();
    if (!empty($orders)) {
        foreach ($orders as $val) {
            if (empty($data[$val["uniacid"]])) {
                $data[$val["uniacid"]] = array("uniacid" => $val["uniacid"], "total_num" => 0, "info" => cloud_w_account($val["uniacid"]));
            } else {
                $data[$val["uniacid"]]["total_num"] += $val["total_num"];
            }
            $data[$val["uniacid"]]["data"][$val["stat_day"]] = array("stat_day" => $val["stat_day"], "total_num" => $val["total_num"]);
        }
    }
    return error(0, $data);
}

?>