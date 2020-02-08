<?php


defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth(true);
if ($op != "index") {
    $id = intval($_GPC["id"]);
    $goods = pintuan_get_activity($id);
    $_W["_share"] = array("title" => $goods["share"]["share_title"], "desc" => $goods["share"]["share_detail"], "imgUrl" => tomedia($goods["share"]["share_thumb"]), "link" => "");
}
if ($op == "index") {
    if ($_W["is_agent"] && $_W["agentid"] == -1) {
        imessage(error(41200, "您所在的区域暂未开启拼团功能,建议您手动搜索地址或切换到此前常用的地址再试试"), "", "ajax");
    }
    if ($_config_plugin["basic"]["status"]["pintuan"] != 1) {
        imessage(error(-1, "拼团功能暂时关闭，敬请关注"), "", "ajax");
    }
    pintuan_cron();
    $goods = pintuan_get_activitylist();
    $pintuan_navs = pdo_fetchall("select id,title,thumb,link from " . tablename("tiny_wmall_pintuan_category") . " where uniacid = :uniacid and agentid = :agentid and status = 1", array(":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"]));
    if (!empty($pintuan_navs)) {
        foreach ($pintuan_navs as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            if (empty($val["link"])) {
                $val["link"] = "/gohome/pages/pintuan/category?cid=" . $val["id"];
            }
        }
    }
    $navs = array_chunk($pintuan_navs, 10);
    $result = array("goods" => $goods, "navs" => $navs);
    imessage(error(0, $result), "", "ajax");
    return 1;
} else {
    if ($op == "detail") {
        if ($_config_plugin["basic"]["status"]["pintuan"] != 1) {
            imessage(error(-1, "拼团功能暂时关闭，敬请关注"), "", "ajax");
        }
        gohome_update_activity_flow("pintuan", $id, "looknum");
        $_W["_share"]["link"] = ivurl("/gohome/pages/pintuan/detail", array("id" => $goods["id"], "share_uid" => $_W["member"]["uid"]), true);
        $store = store_fetch($goods["sid"], array("id", "title", "telephone", "address", "forward_mode", "forward_url", "location_x", "location_y"));
        if (!empty($store)) {
            $store["url"] = store_forward_url($store["id"], $store["forward_mode"], $store["forward_url"]);
        }
        $goods["store"] = $store;
        $teams = pintuan_get_same_list($id, array("is_team" => 1, "status" => 2));
        $record = pintuan_get_member_takepart($id);
        $comment = gohome_get_goods_comment($id, "pintuan");
        $result = array("detail" => $goods, "more_activity" => pintuan_get_activitylist(array("psize" => 8)), "teams" => empty($teams["list"]) ? "" : $teams["list"], "record" => $record, "danmu" => gohome_get_danmu($goods["id"], "pintuan"), "comment" => $comment["comment"], "shareData" => $_W["_share"]);
        $result["shareData"]["path"] = "/gohome/pages/pintuan/detail?id=" . $goods["id"] . "&share_uid=" . $_W["member"]["uid"];
        imessage(error(0, $result), "", "ajax");
    } else {
        if ($op == "share") {
            gohome_update_activity_flow("pintuan", $id, "looknum");
            $team_id = intval($_GPC["team_id"]);
            if (0 < $team_id) {
                $teams = pintuan_get_same_list($id, array("team_id" => $team_id));
            }
            $goods["store"] = store_fetch($goods["sid"], array("id", "title"));
            $_W["_share"]["imgUrl"] = $goods["thumb"];
            $_W["_share"]["link"] = ivurl("/gohome/pages/pintuan/share", array("id" => $goods["id"], "team_id" => $team_id, "share_uid" => $_W["member"]["uid"]), true);
            $wxapp_share_path = "/gohome/pages/pintuan/detail?id=" . $goods["id"] . "&team_id=" . $team_id . "&share_uid=" . $_W["member"]["uid"];
            if (empty($team_id)) {
                $_W["_share"]["link"] = ivurl("/gohome/pages/pintuan/detail", array("id" => $goods["id"], "share_uid" => $_W["member"]["uid"]), true);
                $wxapp_share_path = "/gohome/pages/pintuan/detail?id=" . $goods["id"] . "&share_uid=" . $_W["member"]["uid"];
            }
            $record = pintuan_get_member_takepart($id);
            if ($record["status"] == 2) {
                $leave = $record["team_num"] - $record["takepart_num"];
                $share_title = $_W["_share"]["title"] == $goods["name"] ? "" : $_W["_share"]["title"];
                $_W["_share"]["title"] = "还差" . $leave . "人成团，我参加了\"" . $goods["name"] . "\"拼团，快一起来吧！" . $share_title;
            }
            $result = array("detail" => $goods, "more_activity" => pintuan_get_activitylist(array("psize" => 8)), "available" => pintuan_is_available($pintuan_goods, $team_id), "team" => $teams["list"], "member" => array("uid" => $_W["member"]["uid"], "avatar" => $_W["member"]["avatar"], "is_takepart" => empty($record) ? 0 : 1), "shareData" => $_W["_share"]);
            $result["shareData"]["path"] = $wxapp_share_path;
            imessage(error(0, $result), "", "ajax");
        }
    }
}

?>