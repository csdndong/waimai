<?php
defined("IN_IA") or exit("Access Denied");
global $_GPC;
global $_W;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "invite";
$_W["page"]["title"] = $redPacket["title"];
if (!is_weixin()) {
    imessage("请在微信中访问该链接", "", "info");
}
icheckauth();
if ($op == "invite") {
    if ($_W["ispost"]) {
        if (empty($_W["member"]["is_mall_newmember"])) {
            imessage(error(-1, "您已是老用户"), imurl("shareRedpacket/share/repeat"), "ajax");
        }
        $uid = intval($_GPC["uid"]);
        $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
        $code = trim($_GPC["code"]);
        $status = icheck_verifycode($mobile, $code);
        if (!$status) {
            imessage(error(-1, "验证码错误"), "", "ajax");
        }
        if ($mobile != $_W["member"]["mobile"]) {
            pdo_update("tiny_wmall_members", array("mobile" => $mobile), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
        }
        $is_get = pdo_get("tiny_wmall_shareredpacket_invite_record", array("uniacid" => $_W["uniacid"], "activity_id" => $redPacket["id"], "follow_uid" => $_W["member"]["uid"]));
        if (!empty($is_get)) {
            imessage(error(-1, "您已领取过这个红包"), "", "ajax");
        }
        $share_redpacket = rand($redPacket["share_redpacket_min"], $redPacket["share_redpacket_max"]);
        $follow_redpacket = rand($redPacket["follow_redpacket_min"], $redPacket["follow_redpacket_max"]);
        $insert = array("uniacid" => $_W["uniacid"], "activity_id" => $redPacket["id"], "share_uid" => $uid, "follow_uid" => $_W["member"]["uid"], "share_redpacket_condition" => $redPacket["share_redpacket_condition"], "share_redpacket_discount" => $share_redpacket, "share_redpacket_days_limit" => $redPacket["share_redpacket_days_limit"], "follow_redpacket_condition" => 0, "follow_redpacket_discount" => $follow_redpacket, "follow_redpacket_days_limit" => $redPacket["follow_redpacket_days_limit"], "addtime" => TIMESTAMP);
        pdo_insert("tiny_wmall_shareredpacket_invite_record", $insert);
        mload()->model("redPacket");
        $params = array("activity_id" => $redPacket["id"], "title" => "新用户专享红包", "channel" => "shareRedpacket", "type" => "mallMewMember", "uid" => $_W["member"]["uid"], "discount" => $follow_redpacket, "condition" => 0, "days_limit" => $redPacket["follow_redpacket_days_limit"]);
        $status = redPacket_grant($params);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        imessage(error(0, $_W["member"]["uid"]), "", "ajax");
    }
    if (empty($_W["member"]["is_mall_newmember"])) {
        header("location:" . imurl("shareRedpacket/share/repeat"));
        exit;
    }
    $uid = intval($_GPC["u"]);
    $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $uid), array("nickname", "avatar", "addtime"));
    if (empty($member)) {
        imessage("分享人不存在", "", "info");
    }
    $days_format = ceil(($member["addtime"] - time()) / 86400) . "天";
}
if ($op == "success") {
    $uid = intval($_GPC["uid"]);
    $data = pdo_get("tiny_wmall_shareredpacket_invite_record", array("uniacid" => $_W["uniacid"], "follow_uid" => $uid));
}
include itemplate("share");

?>