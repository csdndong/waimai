<?php


defined("IN_IA") or exit("Access Denied");

function member_to_black($uid, $plugin, $remark = "")
{
    global $_W;
    if (empty($uid)) {
        $uid = $_W["member"]["uid"];
    }
    $is_exist = pdo_get("tiny_wmall_member_black", array("uniacid" => $_W["uniacid"], "uid" => $uid, "plugin" => $plugin));
    if (!empty($is_exist)) {
        return false;
    }
    $data = array("uniacid" => $_W["uniacid"], "uid" => $uid, "plugin" => $plugin, "remark" => $remark, "addtime" => TIMESTAMP);
    pdo_insert("tiny_wmall_member_black", $data);
    return true;
}
function member_get_black($filter = array())
{
    global $_W;
    global $_GPC;
    if (empty($filter)) {
        $filter = $_GPC;
    }
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => intval($_W["uniacid"]));
    $addtime = trim($filter["addtime"]);
    if (!empty($addtime)) {
        $condition .= " and a.addtime = :addtime";
        $params[":addtime"] = $addtime;
    }
    $keyword = trim($filter["keyword"]);
    if (!empty($keyword)) {
        $condition .= " and (a.uid = :uid or b.nickname like :keyword or b.realname like :keyword)";
        $params[":uid"] = $keyword;
        $params[":keyword"] = "%" . $keyword . "%";
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 15;
    $total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_member_black") . "as a left join " . tablename("tiny_wmall_members") . "as b on a.uid = b.uid " . $condition, $params);
    $member_black = pdo_fetchall("select a.*,b.nickname,b.realname,b.avatar from " . tablename("tiny_wmall_member_black") . " as a left join " . tablename("tiny_wmall_members") . " as b on a.uid = b.uid " . $condition . " order by a.id desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    $pager = pagination($total, $page, $psize);
    return array("member_black" => $member_black, "total" => $total, "pager" => $pager);
}
function member_del_black($uid, $type)
{
    global $_W;
    if (empty($uid) && empty($type)) {
        return false;
    }
    pdo_delete("tiny_wmall_member_black", array("uniacid" => $_W["uniacid"], "uid" => $uid, "plugin" => $type));
    return true;
}

?>