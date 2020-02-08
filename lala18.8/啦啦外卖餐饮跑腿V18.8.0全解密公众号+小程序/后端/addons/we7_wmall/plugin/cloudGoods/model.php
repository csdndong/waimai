<?php
defined("IN_IA") or exit("Access Denied");
function cloudgoods_getall_menus($filter = array())
{
    global $_W;
    global $_GPC;
    $params = array();
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        if (isset($condition)) {
            $condition .= " and agentid = :agentid";
        } else {
            $condition = " where agentid = :agentid";
        }
        $params[":agentid"] = $agentid;
    }
    if (!empty($filter) && !empty($filter["keywords"])) {
        $keywords = trim($filter["keywords"]);
        if (!empty($keywords)) {
            if (isset($condition)) {
                $condition .= " and title like '%" . $keywords . "%'";
            } else {
                $condition = " where title like '%" . $keywords . "%'";
            }
        }
    }
    $condition .= " order by displayorder desc";
    $menus = pdo_fetchall("select * from " . tablename("tiny_wmall_cloudgoods_menu_category") . $condition, $params, "id");
    if (!empty($menus)) {
        foreach ($menus as &$menu) {
            $menu["total"] = count(pdo_getall("tiny_wmall_cloudgoods_goods", array("menu_id" => $menu["id"])));
        }
    }
    return $menus;
}
function cloudgoods_option_fetch($id)
{
    global $_W;
    return pdo_fetchall("select * from " . tablename("tiny_wmall_cloudgoods_goods_options") . " where goods_id = :goods_id order by displayorder desc, id asc", array(":goods_id" => $id));
}
function cloudgoods_getall_goods($filter = array())
{
    global $_GPC;
    $params = array();
    if (!empty($filter)) {
        if (0 < $filter["goods_categoryid"]) {
            if (isset($condition)) {
                $condition .= " and category_id = :category_id";
            } else {
                $condition = "where category_id = :category_id";
            }
            $params = array(":category_id" => $filter["goods_categoryid"]);
        }
        if (!empty($filter["keywords"])) {
            $keywords = trim($filter["keywords"]);
            if (isset($condition)) {
                $condition .= " and title like '%" . $keywords . "%'";
            } else {
                $condition = " where title like '%" . $keywords . "%'";
            }
        }
    }
    $page = max(intval($_GPC["page"]), 1);
    $psize = intval($_GPC["psize"]) ? intval($_GPC["psize"]) : 50;
    $condition .= " order by displayorder desc, id desc limit " . ($page - 1) * $psize . ", " . $psize;
    $goods = pdo_fetchall("select * from " . tablename("tiny_wmall_cloudgoods_goods") . $condition, $params, "id");
    if (!empty($goods)) {
        foreach ($goods as &$val) {
            $val["thumb"] = tomedia($val["thumb"]);
            if ($val["is_options"] == 1) {
                $val["options"] = cloudgoods_option_fetch($val["id"]);
            }
            $val["checked"] = 0;
            $val["price"] = floatval($val["price"]);
        }
    }
    return $goods;
}
function cloudgoods_menu_fetch($id)
{
    $menu = pdo_get("tiny_wmall_cloudgoods_menu_category", array("id" => $id));
    $goods_categorys = pdo_fetchall("select * from " . tablename("tiny_wmall_cloudgoods_goods_category") . " where menu_id = :menu_id order by displayorder desc", array(":menu_id" => $id));
    $result = array("menu" => $menu, "goods_categorys" => $goods_categorys);
    return $result;
}

?>