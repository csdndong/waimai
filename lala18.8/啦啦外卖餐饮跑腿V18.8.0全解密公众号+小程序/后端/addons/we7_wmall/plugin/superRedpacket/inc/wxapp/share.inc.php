<?php
defined("IN_IA") or exit("Access Denied");
global $_GPC;
global $_W;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if (!is_weixin()) {
    imessage(error(-1, "璇峰湪寰俊涓闂閾炬帴"), "", "ajax");
}
icheckauth();
if (!$_W["ispost"]) {
    $order_id = intval($_GPC["order_id"]);
    $grant = pdo_get("tiny_wmall_superredpacket_grant", array("uniacid" => $_W["uniacid"], "order_id" => $order_id));
    if (empty($grant)) {
        imessage(error(-1, "分享记录不存在"), "", "ajax");
    }
    $activity = pdo_get("tiny_wmall_superredpacket", array("uniacid" => $_W["uniacid"], "id" => $grant["activity_id"], "type" => "share"));
    if (empty($activity)) {
        imessage(error(-1, "分享红包活动不存在"), "", "ajax");
    }
    $_W["page"]["title"] = $activity["name"];
    $activity["data"] = json_decode(base64_decode($activity["data"]), true);
    $activity["data"]["activity"]["image"] = tomedia($activity["data"]["activity"]["image"]);
    $activity["data"]["activity"]["agreement"] = nl2br($activity["data"]["activity"]["agreement"]);
    $_W["_share"] = array("title" => $activity["data"]["share"]["title"], "desc" => $activity["data"]["share"]["desc"], "imgUrl" => tomedia($activity["data"]["share"]["imgUrl"]), "link" => ivurl("/pages/superRedpacket/index", array("order_id" => $order_id), true));
}
if ($op == "index") {
    if ($_W["ispost"]) {
        $mobile = trim($_GPC["mobile"]) ? trim($_GPC["mobile"]) : imessage(error(-1, "请输入手机号"), "", "ajax");
        pdo_update("tiny_wmall_members", array("mobile" => $mobile), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
        imessage(error(0, ""), "", "ajax");
    }
    if (!empty($_W["member"]["mobile"])) {
        imessage(error(-1000, ""), "", "ajax");
    }
    $result = array("activity_img" => $activity["data"]["activity"]["image"], "activity_title" => $activity["name"], "agreement" => activity_title($activity["data"]["activity"]["agreement"]));
    imessage(error(0, $result), "", "ajax");
}
if ($op == "grant") {
    $result = array("activity" => $activity);
    if ($activity["status"] != 1) {
        imessage(error(-1, $result), "", "ajax");
    }
    $is_get = 0;
    $get_status = 0;
    $is_exist = pdo_get("tiny_wmall_activity_redpacket_record", array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "super_share_id" => $grant["id"], "channel" => "superRedpacket", "type" => "share"), array("id"));
    if (empty($is_exist) && 0 < $grant["packet_dosage"]) {
        $grant_status = superRedpacket_share_grant($order_id);
        if (is_error($grant_status)) {
            imessage(error(-1, $grant_status["message"]), "", "ajax");
        }
        $get_status = 1;
    }
    $redpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and super_share_id = :super_share_id and uid = :uid and type = :type and channel = :channel", array(":uniacid" => $_W["uniacid"], ":super_share_id" => $grant["id"], ":uid" => $_W["member"]["uid"], ":type" => "share", ":channel" => "superRedpacket"));
    if (!empty($redpackets)) {
        $is_get = 1;
        foreach ($redpackets as &$val) {
            $val["condition_cn"] = date("Y-m-d", $val["starttime"]) . "~" . date("Y-m-d", $val["endtime"]) . "有效";
            $val["category_cn"] = tocategory($val["category_limit"]);
            if (!empty($val["category_cn"])) {
                $val["category_cn"] = "仅限" . tocategory($val["category_limit"]) . "分类使用";
            }
            $val["times_cn"] = totime($val["times_limit"]);
            if (!empty($val["times_cn"])) {
                $val["times_cn"] = "仅限" . $val["times_cn"] . "时段使用";
            }
        }
    }
    $rankings = pdo_fetchall("select uid,granttime,sum(discount) as total_discount from " . tablename("tiny_wmall_activity_redpacket_record") . " where uniacid = :uniacid and super_share_id = :super_share_id and channel = :channel and type = :type group by uid order by total_discount desc", array(":uniacid" => $_W["uniacid"], ":super_share_id" => $grant["id"], ":channel" => "superRedpacket", ":type" => "share"), "uid");
    $result["member"] = $_W["member"];
    if (!empty($rankings)) {
        $uids = array_keys($rankings);
        $uids = implode(",", $uids);
        $members = pdo_fetchall("select uid,avatar,nickname from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and uid in(" . $uids . ")", array(":uniacid" => $_W["uniacid"]), "uid");
        $rank_num = 0;
        foreach ($rankings as &$val) {
            $rank_num++;
            if ($val["uid"] == $_W["member"]["uid"]) {
                $result["member"]["ranking"] = $rank_num;
            }
            $val["granttime_cn"] = date("Y-m-d H:i:s", $val["granttime"]);
            $val["avatar"] = tomedia($members[$val["uid"]]["avatar"]);
            $val["nickname"] = $members[$val["uid"]]["nickname"];
        }
    }
    $result["get_status"] = $get_status;
    $result["redpackets"] = $redpackets;
    $result["is_get"] = $is_get;
    $result["rankings"] = $rankings;
    imessage(error(0, $result), "", "ajax");
}

?>
