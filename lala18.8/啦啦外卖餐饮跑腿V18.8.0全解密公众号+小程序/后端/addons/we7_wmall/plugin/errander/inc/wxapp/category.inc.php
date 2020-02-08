<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if (!$_config_plugin["status"]) {
    imessage(error(-1, "平台暂未开启跑腿功能"), "", "ajax");
}
if ($op == "index") {
    $id = intval($_GPC["id"]);
    $category = errander_category_fetch($id);
    if (empty($category)) {
        imessage(error(-1000, "跑腿类型不存在"), imurl("errander/index"), "ajax");
    }
    if (empty($category["status"])) {
        imessage(error(-1001, "该跑腿类型已关闭"), imurl("errander/index"), "ajax");
    }
    $params = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
    $condition = array();
    if (!empty($params)) {
        if (!empty($params["address"])) {
            $address_buy = $params["address"]["buy"];
            if (!empty($address_buy)) {
                $status = member_errander_address_check($address_buy);
                if (is_error($status)) {
                    $address_buy = $status;
                }
            }
            $address_accept = $params["address"]["accept"];
            if (!empty($address_accept)) {
                $status = member_errander_address_check($address_accept);
                if (is_error($status)) {
                    $address_accept = array("mobile" => $address_accept["mobile"]);
                }
            }
            $condition["start_address"] = $address_buy;
            $condition["end_address"] = $address_accept;
            $address = array("buy" => $address_buy, "accept" => $address_accept);
        }
        $condition = array_merge($condition, $params);
    }
    $order = errander_order_calculate($category, $condition);
    $agreement_errander = get_config_text("agreement_errander");
    $result = array("category" => $category, "address" => $address, "order" => $order, "islegal" => 0);
    message(error(0, $result), "", "ajax");
}

?>