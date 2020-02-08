<?php
define("IN_MOBILE", true);
require "../framework/bootstrap.inc.php";
load()->app("common");
load()->app("template");
load()->model("mc");
load()->model("app");
$_W["uniacid"] = intval($_GPC["i"]);
if (empty($_W["uniacid"])) {
    $_W["uniacid"] = intval($_GPC["weid"]);
}
if (empty($_W["uniacid"])) {
    message(error(-1, "公众号uniacid为空 "), "", "ajax");
}
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["uniaccount"] = $_W["account"];
if (empty($_W["uniaccount"])) {
    message(error(-1, "公众号不存在 "), "", "ajax");
}
$_W["acid"] = $_W["uniaccount"]["acid"];
$isdel_account = pdo_get("account", array("isdeleted" => 1, "acid" => $_W["acid"]));
if (!empty($isdel_account)) {
    message(error(-1, "指定公众号已被删除 "), "", "ajax");
}
$w = trim($_GPC["w"]) ? trim($_GPC["w"]) : "member";
if ($w == "member") {
    $_W["session_id"] = "";
    if (isset($_GPC["istate"]) && !empty($_GPC["istate"])) {
        $_W["session_id"] = trim($_GPC["istate"]);
        $_W["itoken"] = trim($_GPC["istate"]);
    } else {
        if (isset($_GPC["state"]) && !empty($_GPC["state"]) && strexists($_GPC["state"], "we7sid-")) {
            $pieces = explode("-", $_GPC["state"]);
            $_W["session_id"] = $pieces[1];
            unset($pieces);
        }
    }
    if (empty($_W["session_id"])) {
        $_W["session_id"] = $_COOKIE[session_name()];
    }
    if (empty($_W["session_id"])) {
        $_W["session_id"] = (string) $_W["uniacid"] . "-" . random(20);
        $_W["session_id"] = md5($_W["session_id"]);
        setcookie(session_name(), $_W["session_id"], 0, "/");
    }
    session_id($_W["session_id"]);
    load()->classs("wesession");
    WeSession::start($_W["uniacid"], CLIENT_IP, 864000);
    if (!empty($_W["openid"])) {
        $_SESSION["openid"] = $_W["openid"];
    }
    if (!empty($_GPC["j"])) {
        $acid = intval($_GPC["j"]);
        $_W["account"] = account_fetch($acid);
        if (is_error($_W["account"])) {
            $_W["account"] = account_fetch($_W["acid"]);
        } else {
            $_W["acid"] = $acid;
        }
        $_SESSION["__acid"] = $_W["acid"];
        $_SESSION["__uniacid"] = $_W["uniacid"];
    }
    if (!empty($_SESSION["__acid"]) && $_SESSION["__uniacid"] == $_W["uniacid"]) {
        $_W["acid"] = intval($_SESSION["__acid"]);
        $_W["account"] = account_fetch($_W["acid"]);
    }
    if (!empty($_SESSION["acid"]) && $_W["acid"] != $_SESSION["acid"] || !empty($_SESSION["uniacid"]) && $_W["uniacid"] != $_SESSION["uniacid"]) {
        $keys = array_keys($_SESSION);
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
        unset($keys);
        unset($key);
    }
    $_SESSION["acid"] = $_W["acid"];
    $_SESSION["uniacid"] = $_W["uniacid"];
    $dest_url = rtrim($_W["siteroot"], "/");
    $dest_url = (string) $dest_url . "/addons/we7_wmall/template/vue/index.html?menu=aaa#/pages/home/index?i=" . $_W["uniacid"];
    $_SESSION["dest_url"] = urlencode($dest_url);
    if (!empty($_SESSION["openid"])) {
        $_W["openid"] = $_SESSION["openid"];
        $_W["unionid"] = $_SESSION["unionid"];
        $_W["fans"] = mc_fansinfo($_W["openid"]);
        $_W["fans"]["openid"] = $_W["openid"];
        $_W["fans"]["from_user"] = $_W["fans"]["openid"];
        if (empty($_W["unionid"])) {
            $_W["unionid"] = $_W["fans"]["unionid"];
        }
    }
    if (empty($_W["openid"]) && !empty($_SESSION["oauth_openid"])) {
        $_W["openid"] = $_SESSION["oauth_openid"];
        $_W["fans"] = array("openid" => $_SESSION["oauth_openid"], "from_user" => $_SESSION["oauth_openid"], "follow" => 0);
    }
    if (!empty($_W["openid"]) && $_GPC["from"] == "wxapp") {
        $_W["openid_wxapp"] = $_W["openid"];
    }
} else {
    $_W["session_id"] = "";
    if (isset($_GPC["istate"]) && !empty($_GPC["istate"])) {
        $_W["session_id"] = trim($_GPC["istate"]);
        $_W["itoken"] = trim($_GPC["istate"]);
    } else {
        if (isset($_GPC["state"]) && !empty($_GPC["state"]) && strexists($_GPC["state"], "we7sid-")) {
            $pieces = explode("-", $_GPC["state"]);
            $_W["session_id"] = $pieces[1];
            unset($pieces);
        }
    }
    if (empty($_W["session_id"])) {
        $_W["session_id"] = $_COOKIE[session_name()];
    }
    if (empty($_W["session_id"])) {
        $_W["session_id"] = (string) $_W["uniacid"] . "-" . random(20);
        $_W["session_id"] = md5($_W["session_id"]);
        setcookie(session_name(), $_W["session_id"], 0, "/");
    }
    session_id($_W["session_id"]);
    load()->classs("wesession");
    WeSession::start($_W["uniacid"], CLIENT_IP, 864000);
}
if (!function_exists("uni_setting_load")) {
    function uni_setting_load($name = "", $uniacid = 0)
    {
        global $_W;
        $uniacid = empty($uniacid) ? $_W["uniacid"] : $uniacid;
        $cachekey = "unisetting:" . $uniacid;
        $unisetting = cache_load($cachekey);
        if (empty($unisetting)) {
            $unisetting = pdo_get("uni_settings", array("uniacid" => $uniacid));
            if (!empty($unisetting)) {
                $serialize = array("site_info", "stat", "oauth", "passport", "uc", "notify", "creditnames", "default_message", "creditbehaviors", "payment", "recharge", "tplnotice", "mcplugin", "statistics", "bind_domain");
                foreach ($unisetting as $key => &$row) {
                    if (in_array($key, $serialize) && !empty($row)) {
                        $row = (array) iunserializer($row);
                    }
                }
            } else {
                $unisetting = array();
            }
            cache_write($cachekey, $unisetting);
        }
        if (empty($unisetting)) {
            return array();
        }
        if (empty($name)) {
            return $unisetting;
        }
        if (!is_array($name)) {
            $name = array($name);
        }
        return array_elements($name, $unisetting);
    }
}
if (!function_exists("uni_permission")) {
    function uni_permission($uid = 0, $uniacid = 0)
    {
        global $_W;
        $uid = empty($uid) ? $_W["uid"] : intval($uid);
        $uniacid = empty($uniacid) ? $_W["uniacid"] : intval($uniacid);
        $founders = explode(",", $_W["config"]["setting"]["founder"]);
        if (in_array($uid, $founders)) {
            return "founder";
        }
        $sql = "SELECT `role` FROM " . tablename("uni_account_users") . " WHERE `uid`=:uid AND `uniacid`=:uniacid";
        $pars = array();
        $pars[":uid"] = $uid;
        $pars[":uniacid"] = $uniacid;
        $role = pdo_fetchcolumn($sql, $pars);
        if (empty($role)) {
            return false;
        }
        return in_array($role, array("manager", "owner")) ? "manager" : "operator";
    }
}
$_W["account"]["groupid"] = $_W["uniaccount"]["groupid"];
$_W["account"]["qrcode"] = tomedia("qrcode_" . $_W["acid"] . ".jpg") . "?time=" . $_W["timestamp"];
$_W["account"]["avatar"] = tomedia("headimg_" . $_W["acid"] . ".jpg") . "?time=" . $_W["timestamp"];
$unisetting = uni_setting_load();
if (empty($_W["setting"]["upload"])) {
    $_W["setting"]["upload"] = array_merge($_W["config"]["upload"]);
}
if (!empty($unisetting["remote"]["type"])) {
    $_W["setting"]["remote"] = $unisetting["remote"];
}
if (!empty($_W["setting"]["remote"][$_W["uniacid"]]["type"])) {
    $_W["setting"]["remote"] = $_W["setting"]["remote"][$_W["uniacid"]];
}
$_W["attachurl_local"] = $_W["siteroot"] . $_W["config"]["upload"]["attachdir"] . "/";
$_W["attachurl"] = $_W["attachurl_local"];
if (!empty($_W["setting"]["remote"]["type"])) {
    if ($_W["setting"]["remote"]["type"] == 1) {
        $_W["attachurl_remote"] = $_W["setting"]["remote"]["ftp"]["url"] . "/";
        $_W["attachurl"] = $_W["attachurl_remote"];
    } else {
        if ($_W["setting"]["remote"]["type"] == 2) {
            $_W["attachurl_remote"] = $_W["setting"]["remote"]["alioss"]["url"] . "/";
            $_W["attachurl"] = $_W["attachurl_remote"];
        } else {
            if ($_W["setting"]["remote"]["type"] == 3) {
                $_W["attachurl_remote"] = $_W["setting"]["remote"]["qiniu"]["url"] . "/";
                $_W["attachurl"] = $_W["attachurl_remote"];
            } else {
                if ($_W["setting"]["remote"]["type"] == 4) {
                    $_W["attachurl_remote"] = $_W["setting"]["remote"]["cos"]["url"] . "/";
                    $_W["attachurl"] = $_W["attachurl_remote"];
                }
            }
        }
    }
}
$acl = array("home" => array("default" => "home"), "mc" => array("default" => "home"));
$controllers = array();
$handle = opendir(IA_ROOT . "/app/source/");
if (!empty($handle)) {
    while ($dir = readdir($handle)) {
        if ($dir != "." && $dir != "..") {
            $controllers[] = $dir;
        }
    }
}
if (!in_array($controller, $controllers)) {
    $controller = "home";
}
$init = IA_ROOT . "/app/source/" . $controller . "/__init.php";
if (is_file($init)) {
    require $init;
}
$actions = array();
$handle = opendir(IA_ROOT . "/app/source/" . $controller);
if (!empty($handle)) {
    while ($dir = readdir($handle)) {
        if ($dir != "." && $dir != ".." && strexists($dir, ".ctrl.php")) {
            $dir = str_replace(".ctrl.php", "", $dir);
            $actions[] = $dir;
        }
    }
}
if (!in_array($action, $actions)) {
    $action = $acl[$controller]["default"];
}
if (!in_array($action, $actions)) {
    $action = $actions[0];
}
require _forward($controller, $action);
function _forward($c, $a)
{
    $file = IA_ROOT . "/app/source/" . $c . "/" . $a . ".ctrl.php";
    return $file;
}

?>