<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
load()->model("mc");
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "openid";
if ($ta == "openid") {
    if (empty($_W["openid"])) {
        $state = "we7sid-" . $_W["session_id"];
        if (empty($_SESSION["dest_url"])) {
            $_SESSION["dest_url"] = urlencode(ivurl("pages/home/index", array(), true));
        }
        $str = "";
        if (uni_is_multi_acid()) {
            $str = "&j=" . $_W["acid"];
        }
        $url = (!empty($unisetting["oauth"]["host"]) ? $unisetting["oauth"]["host"] . $sitepath . "/" : $_W["siteroot"] . "app/") . "index.php?i=" . $_W["uniacid"] . $str . "&c=auth&a=oauth&scope=snsapi_base";
        $callback = urlencode($url);
        $oauth_account = WeAccount::create($_W["account"]["oauth"]);
        $forward = $oauth_account->getOauthCodeUrl($callback, $state);
        header("Location: " . $forward);
        exit;
    }
    $redirct_url = base64_decode($_GPC["redirct_url"]);
    if (empty($redirct_url)) {
        $redirct_url = ivurl("pages/home/index", array(), true);
    }
    $redirct_url = irurl($redirct_url);
    header("Location: " . $redirct_url);
    exit;
}
if ($ta == "userinfo") {
    $force = intval($_GPC["force"]);
    if ($force == 1) {
        unset($_SESSION["userinfo"]);
    }
    $fansInfo = mc_oauth_userinfo();
    if (!empty($fansInfo["unionid"])) {
        pdo_update("tiny_wmall_members", array("openid" => $fansInfo["openid"]), array("unionId" => $fansInfo["unionid"]));
        pdo_update("tiny_wmall_members", array("unionId" => $fansInfo["unionid"]), array("openid" => $fansInfo["openid"]));
        member_union($fansInfo["unionid"]);
        $member = get_member($fansInfo["unionid"], "unionId");
    } else {
        $member = get_member($fansInfo["openid"]);
    }
    $avatar = rtrim(rtrim($fansInfo["headimgurl"], "0"), 132) . 132;
    if (empty($member)) {
        $mc = pdo_fetch("select a.fanid,b.credit1,b.credit2,b.uid,b.realname,b.mobile,b.gender from" . tablename("mc_mapping_fans") . " as a left join " . tablename("mc_members") . " as b on a.uid = b.uid where a.uniacid = :uniacid and a.acid = :acid and a.openid = :openid", array(":uniacid" => $_W["uniacid"], ":acid" => $_W["acid"], ":openid" => $_W["openid"]));
        if (empty($mc["uid"])) {
            $member = array("uniacid" => $_W["uniacid"], "uid" => date("His") . random(3, true), "openid" => $fansInfo["openid"], "unionId" => $fansInfo["unionid"], "nickname" => $fansInfo["nickname"], "realname" => $fansInfo["nickname"], "sex" => $fansInfo["sex"] == 1 ? "男" : "女", "avatar" => $avatar, "is_sys" => 2, "status" => 1, "token" => random(32), "addtime" => TIMESTAMP);
            pdo_insert("tiny_wmall_members", $member);
            $member["credit1"] = 0;
            $member["credit2"] = 0;
        } else {
            $member = array("uniacid" => $_W["uniacid"], "uid" => $mc["uid"], "openid" => !empty($_W["openid"]) ? $_W["openid"] : $fansInfo["openid"], "unionId" => $fansInfo["unionid"], "nickname" => $fansInfo["nickname"], "realname" => $mc["realname"], "mobile" => $mc["mobile"], "sex" => $fansInfo["sex"] == 1 ? "男" : "女", "avatar" => $avatar, "is_sys" => 1, "status" => 1, "token" => random(32), "addtime" => TIMESTAMP);
            pdo_insert("tiny_wmall_members", $member);
            $member["credit1"] = $mc["credit1"];
            $member["credit2"] = $mc["credit2"];
        }
    } else {
        if ($member["nickname"] != $fansInfo["nickname"] || $member["avatar"] != $avatar) {
            $update = array("nickname" => $fansInfo["nickname"], "avatar" => $avatar);
            pdo_update("tiny_wmall_members", $update, array("id" => $member["id"]));
        }
    }
    $redirct_url = base64_decode($_GPC["redirct_url"]);
    if ($force == 1) {
        $redirct_url = ivurl("pages/member/mine", array(), true);
    } else {
        if (empty($redirct_url)) {
            $redirct_url = ivurl("pages/home/index", array(), true);
        }
    }
    $redirct_url = irurl($redirct_url);
    header("Location: " . $redirct_url);
    exit;
}
if ($ta == "2url") {
    $redirct_url = trim($_GPC["redirct_url"]);
    if (!empty($redirct_url)) {
        $_SESSION["dest_url"] = urlencode(irurl(base64_decode($redirct_url)));
    }
    imessage(error(0, ""), "", "ajax");
}

?>