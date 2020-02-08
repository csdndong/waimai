<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
global $_W;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "invite";
$_W["page"]["title"] = $redPacket["title"];
icheckauth();
$_W["_share"] = array("title" => $redPacket["share"]["title"], "desc" => $redPacket["share"]["desc"], "link" => ivurl("/package/pages/shareRedpacket/invite", array("u" => $_W["member"]["uid"]), true), "imgUrl" => tomedia($redPacket["share"]["imgUrl"]));
if ($op == "invite") {
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_shareredpacket_invite_record") . " where uniacid = :uniacid and share_uid = :share_uid", array(":uniacid" => $_W["uniacid"], ":share_uid" => $_W["member"]["uid"]));
    $redPacket_num = pdo_fetchcolumn("select sum(share_redPacket_discount) from " . tablename("tiny_wmall_shareredpacket_invite_record") . " where uniacid = :uniacid and share_uid = :share_uid and status = 1", array(":uniacid" => $_W["uniacid"], ":share_uid" => $_W["member"]["uid"]));
    $condition = " where a.uniacid = :uniacid and a.share_uid = :share_uid";
    $params = array(":uniacid" => $_W["uniacid"], ":share_uid" => $_W["member"]["uid"]);
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $invited_info = pdo_fetchall("select a.*, b.nickname,b.avatar from " . tablename("tiny_wmall_shareredpacket_invite_record") . " as a left join" . tablename("tiny_wmall_members") . " as b on a.follow_uid = b.uid " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . ", " . $psize, $params);
    if (!empty($invited_info)) {
        foreach ($invited_info as &$row) {
            $row["avatar"] = tomedia($row["avatar"]);
        }
    }
    $wxapp_shareRedpacket_path = "/package/pages/shareRedpacket/invite?u=" . $_W["member"]["uid"];
    $redPacket["share"]["path"] = $wxapp_shareRedpacket_path;
    $redPacket["share"]["imageUrl"] = tomedia($redPacket["share"]["imgUrl"]);
    $result = array("redPacket" => $redPacket, "total" => $total, "redPacket_num" => $redPacket_num, "invited_info" => $invited_info);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "ranking") {
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $rankings = pdo_fetchall("select count(*) as total, a.*,b.nickname,b.avatar from " . tablename("tiny_wmall_shareredpacket_invite_record") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.share_uid = b.uid  where a.uniacid = :uniacid group by a.share_uid order by total desc limit " . ($page - 1) * $psize . ", " . $psize, array(":uniacid" => $_W["uniacid"]));
    if (!empty($rankings)) {
        foreach ($rankings as &$val) {
            $val["avatar"] = tomedia($val["avatar"]);
        }
    }
    $result = array("rankings" => $rankings);
    imessage(error(0, $result), "", "ajax");
}

?>