<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
load()->func("communication");
$_W["page"]["title"] = "登录";
$config_mall = $_W["we7_wmall"]["config"]["mall"];
if (is_weixin()) {
    header("location: " . ivurl("pages/home/index", array(), true));
    exit;
}
$sns = trim($_GPC["sns"]);
if (is_glala()) {
    $status = member_sns_check($sns);
    if (!empty($status)) {
        $openid = $_GPC["openid"];
        if ($sns == "wx") {
            $openid = "sns_wx_" . $openid;
        }
        $member = get_member($openid);
        if (!empty($member)) {
            isetcookie("itoken", $member["token"], 7 * 86400);
            $result = array("member" => $member);
            imessage(error(0, $result), "", "ajax");
            return 1;
        }
    }
} else {
    if ($_W["ispost"] && !empty($sns) && !empty($_GPC["openid"])) {
        member_sns_check($sns);
    echo "ok";
        exit;
    }
    if ($_GET["openid"]) {
        if ($sns == "qq") {
            $_GET["openid"] = "sns_qq_" . $_GET["openid"];
        } else {
            if ($sns == "wx") {
                $_GET["openid"] = "sns_wx_" . $_GET["openid"];
            }
        }
        $member = get_member($_GET["openid"]);
        if (!empty($member)) {
            isetcookie("itoken", $member["token"], 7 * 86400);
        }
        $forward = "";
        if (!empty($_GPC["forward"])) {
            $forward = urldecode($_GPC["forward"]);
            if (!empty($forward) && strexists($forward, "pages/auth/")) {
                $forward = "";
            }
        }
        $forward = empty($forward) ? ivurl("pages/home/index", array(), true) : $forward;
    header("location: " . $forward);
        exit;
    }
}
function member_sns_check($sns)
{
    global $_W;
    global $_GPC;
    if (empty($sns)) {
        $sns = $_GPC["sns"];
    }
    if (empty($sns)) {
        return false;
    }
    $snsinfo = array();
    if ($sns == "wx") {
        if (is_glala()) {
            $snsinfo["openid"] = "sns_wx_" . $_GPC["openid"];
            $snsinfo["nickname"] = $_GPC["nickname"];
            $snsinfo["sex"] = $_GPC["sex"] == 1 ? "男" : "女";
            $snsinfo["headimgurl"] = rtrim(rtrim($_GPC["headimgurl"], "0"), 132) . 132;
            $snsinfo["unionid"] = $_GPC["unionid"];
        } else {
            if (!empty($_GPC["token"])) {
                $snsinfo["openid"] = "sns_wx_" . $_GPC["openid"];
                $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $_GPC["token"] . "&openid=" . $_GPC["openid"] . "&lang=zh_CN";
                $response = ihttp_get($url);
                if (is_error($response)) {
            slog("gukeapp", "微信登陆获取粉丝信息失败", array(), "失败原因：" . $response["message"]);
        } else {
            $result = @json_decode($response["content"], true);
            if (!empty($result["errcode"])) {
                slog("gukeapp", "微信登陆获取粉丝信息失败", array(), "失败原因：" . $result["errmsg"]);
            } else {
                $snsinfo["unionid"] = $result["unionid"];
                $snsinfo["sex"] = $result["sex"] == 1 ? "男" : "女";
                $snsinfo["headimgurl"] = rtrim(rtrim($result["headimgurl"], "0"), 132) . 132;
                    }
                }
            }
        }
    } else {
        if ($sns == "qq") {
            $data = htmlspecialchars_decode($_GPC["userinfo"]);
            $snsinfo = json_decode($data, true);
            $snsinfo["openid"] = "sns_qq_" . $_GPC["openid"];
            $snsinfo["headimgurl"] = $snsinfo["figureurl_qq_2"];
        }
    }
    $data = array("uniacid" => $_W["uniacid"], "openid" => "", "unionId" => $snsinfo["unionid"], "nickname" => $snsinfo["nickname"], "avatar" => $snsinfo["headimgurl"], "sex" => $snsinfo["sex"], "register_type" => "sns_" . $sns);
    $openid = trim($_GPC["openid"]);
    if ($sns == "qq") {
        $data["openid_qq"] = trim($_GPC["openid"]);
        $openid = "sns_qq_" . trim($_GPC["openid"]);
    } else {
        if ($sns == "wx") {
            $data["openid_wx"] = trim($_GPC["openid"]);
            $openid = "sns_wx_" . trim($_GPC["openid"]);
            if (!empty($snsinfo["unionid"]) && !empty($data["openid_wx"])) {
                pdo_update("tiny_wmall_members", array("openid_wx" => $data["openid_wx"]), array("unionId" => $snsinfo["unionid"]));
                pdo_update("tiny_wmall_members", array("unionId" => $snsinfo["unionid"]), array("openid_wx" => $data["openid_wx"]));
                member_union($snsinfo["unionid"]);
                $member = get_member($snsinfo["unionid"], "unionId");
            }
        }
    }
    if (empty($member)) {
        $member = get_member($openid);
    }
    if (empty($member)) {
        $data["uid"] = date("His") . random(3, true);
        $data["is_sys"] = 2;
        $data["token"] = random(32);
        $data["addtime"] = TIMESTAMP;
        $data["salt"] = random(6, true);
        $data["password"] = md5(md5($data["salt"] . rand(100000, 999999)) . $data["salt"]);
        pdo_insert("tiny_wmall_members", $data);
        return true;
    }
    $update = array("avatar" => $snsinfo["headimgurl"]);
    if (!empty($snsinfo["unionid"])) {
        $update["unionId"] = $snsinfo["unionid"];
    }
    if ($sns == "qq") {
        $update["openid_qq"] = trim($_GPC["openid"]);
    } else {
        if ($sns == "wx") {
            $update["openid_wx"] = trim($_GPC["openid"]);
        }
    }
    pdo_update("tiny_wmall_members", $update, array("id" => $member["id"]));
    return true;
}

?>