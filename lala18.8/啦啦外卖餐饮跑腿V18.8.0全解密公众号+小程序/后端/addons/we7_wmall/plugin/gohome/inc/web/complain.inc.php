<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
if ($op == "list") {
    $_W["page"]["title"] = "投诉列表";
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $keyword = trim($_GPC["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.uid = :uid or b.nickname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_complain") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition, $params);
    $complain = pdo_fetchall("SELECT a.*,b.avatar,b.nickname FROM " . tablename("tiny_wmall_complain") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    $options = array("cheat" => "网页包含欺诈信息（如：假红包）", "eroticism" => "网页包含欺色情信息", "violence" => "网页包含欺暴力恐怖信息", "politics" => "网页包含欺政治敏感信息", "privacy" => "网页在手机个人隐私信息（如：钓鱼链接）", "induce" => "网页包含诱导分享/关注性质的内容", "rumor" => "网页可能包含谣言信息");
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($op == "status") {
        mload()->model("member.extra");
        $uid = intval($_GPC["uid"]);
        $status = member_to_black($uid, "gohome");
        if ($status) {
            imessage(error(0, "加入黑名单成功"), referer(), "ajax");
        } else {
            imessage(error(-1, "加入黑名单失败"), referer(), "ajax");
        }
    }
}
include itemplate("complain");

?>