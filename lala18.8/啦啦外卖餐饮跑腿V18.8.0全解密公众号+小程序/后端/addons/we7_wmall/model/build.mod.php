<?php


defined("IN_IA") or exit("Access Denied");

function build_category($type)
{
    global $_W;
    global $_GPC;
    if (!empty($_GPC["__build"])) {
        return true;
    }
    $datas = array("TY_store_label" => array("new" => array("title" => "新店", "color" => "#ff2d4b", "alias" => "new"), "brand" => array("title" => "品牌", "color" => "#ffa60b", "alias" => "brand")));
    if (empty($datas[$type])) {
        return true;
    }
    foreach ($datas[$type] as $row) {
        $is_exist = pdo_get("tiny_wmall_category", array("uniacid" => $_W["uniacid"], "type" => $type, "alias" => $row["alias"]));
        if (empty($is_exist)) {
            $row["uniacid"] = $_W["uniacid"];
            $row["type"] = $type;
            $row["is_system"] = 1;
            pdo_insert("tiny_wmall_category", $row);
        }
    }
    isetcookie("__build", 1, 3600);
    return true;
}

?>