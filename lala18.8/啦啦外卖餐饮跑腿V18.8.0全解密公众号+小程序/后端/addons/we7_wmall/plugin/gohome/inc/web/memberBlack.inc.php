<?php 
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "list";
mload()->model("member.extra");
if ($op == "list") {
    $_W["page"]["title"] = "黑名单列表";
    $filter = array("keyword" => $_GPC["keyword"]);
    $member_black = member_get_black($filter);
    $member_black = $member_black["member_black"];
    $limit_visit = array("gohome" => "砍价页面", "tongcheng" => "同城页面");
} else {
    if ($op == "del") {
        $uid = intval($_GPC["uid"]);
        $type = trim($_GPC["type"]);
        $status = member_del_black($uid, $type);
        if ($status) {
            imessage(error(0, "用户已经移出黑名单"), iurl("gohome/memberBlack/list"), "ajax");
        } else {
            imessage(error(-1, "移出黑名单失败"), iurl("gohome/memberBlack/list"), "ajax");
        }
    }
}
include itemplate("memberBlack");

?>