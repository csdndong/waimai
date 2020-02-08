<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth(true);
$config_mall = $_W["we7_wmall"]["config"]["mall"];
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    if (0 < $_W["member"]["uid"]) {
        mload()->model("member");
        $member = member_fetch();
    }
    $filter = array("orderby" => "click", "psize" => 4);
    $result = array("hotStores" => haodian_store_fetchall($filter), "searchHistorys" => $member["search_data"]);
    imessage(error(0, $result), "", "ajax");
}
if ($op == "truncate") {
    if (0 < $_W["member"]["uid"]) {
        pdo_update("tiny_wmall_members", array("search_data" => ""), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
    }
    imessage(error(0, "清除历史记录成功"), "", "ajax");
}
if ($op == "search") {
    if (0 < $_W["member"]["uid"]) {
        mload()->model("member");
        $lat = trim($_GPC["lat"]);
        $lng = trim($_GPC["lng"]);
        $key = trim($_GPC["key"]);
        $member = member_fetch();
        if (!empty($member)) {
            $num = count($member["search_data"]);
            if (5 <= $num) {
                array_pop($member["search_data"]);
            }
            array_push($member["search_data"], $key);
            $search_data = iserializer(array_unique($member["search_data"]));
            pdo_update("tiny_wmall_members", array("search_data" => $search_data), array("uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"]));
        }
    }
    $key = trim($_GPC["key"]);
    $filter = array("keyword" => $key, "psize" => 100, "get_activity" => 1);
    $stores = haodian_store_fetchall($filter);
    $result = array("stores" => $stores);
    imessage(error(0, $result), "", "ajax");
}

?>