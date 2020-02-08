<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " where a.uniacid = :uniacid";
    $params = array(":uniacid" => $_W["uniacid"]);
    $cid = intval($_GPC["cid"]);
    if (0 < $cid) {
        $condition .= " and cateid = :cateid";
        $params[":cateid"] = $cid;
    }
    $page = max(1, intval($_GPC["page"]));
    $psize = intval($_GPC["psize"]);
    $news = pdo_fetchall("select a.*, b.title as btitle from " . tablename("tiny_wmall_news") . " as a left join" . tablename("tiny_wmall_news_category") . " as b on a.cateid = b.id " . $condition . " order by id desc, displayorder desc limit " . ($page - 1) * $psize . "," . $psize, $params);
    if (!empty($news)) {
        foreach ($news as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
        }
    }
    $categorys = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_news_category") . " WHERE uniacid = :uniacid", array(":uniacid" => $_W["uniacid"]), "id");
    $result = array("records" => $news, "categorys" => $categorys);
    imessage(error(0, $result), "", "ajax");
}
if ($ta == "detail") {
    $id = intval($_GPC["id"]);
    $news = pdo_get("tiny_wmall_news", array("id" => $id, "uniacid" => $_W["uniacid"]));
    if (empty($news)) {
        imessage(error(0, "该消息不存在或已删除"), "", "ajax");
    }
    $click = ++$news["click"];
    pdo_update("tiny_wmall_news", array("click" => $click), array("uniacid" => $_W["uniacid"], "id" => $id));
    $result = array("news" => $news);
    imessage(error(0, $result), "", "ajax");
}

?>
