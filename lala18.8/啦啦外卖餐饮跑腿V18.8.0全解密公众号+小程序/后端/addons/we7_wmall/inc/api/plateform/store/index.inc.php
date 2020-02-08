<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "list";
if ($ta == "list") {
    $condition = " uniacid = :uniacid and status != 4";
    $params = array(":uniacid" => $_W["uniacid"]);
    $agentid = intval($_GPC["agentid"]);
    if (0 < $agentid) {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }
    if (!empty($_GPC["keyword"])) {
        $condition .= " AND title LIKE '%" . $_GPC["keyword"] . "%'";
    }
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 3;
    $stores = pdo_fetchall("SELECT id,title,logo,address,telephone,business_hours,is_rest,status FROM " . tablename("tiny_wmall_store") . " WHERE " . $condition . " ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if (!empty($stores)) {
        foreach ($stores as &$li) {
            $li["logo"] = tomedia($li["logo"]);
            $li["is_rest"] = 1;
            if ($li["is_in_business"] && store_is_in_business_hours($li["business_hours"])) {
                $li["is_rest"] = 0;
            }
            $li["status_cn"] = "营业中";
            unset($li["business_hours"]);
        }
    }
    $result = array("stores" => $stores);
    message(ierror(0, "", $result), "", "ajax");
}

?>