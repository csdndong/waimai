<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
$ta = trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index";
if ($ta == "index") {
    $where = " where a.uniacid = :uniacid AND a.uid = :uid order by b.is_rest asc, a.id desc limit 50";
    $params = array(":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"]);
    $id = intval($_GPC["min"]);
    if (0 < $id) {
        $condition .= " and a.id < :id";
        $params[":id"] = $id;
    }
    $cartsInfo = pdo_fetchall("SELECT a.id, a.sid, b.title as storeName,b.logo,b.is_rest FROM" . tablename("tiny_wmall_order_cart") . "as a left join" . tablename("tiny_wmall_store") . "as b on a.sid = b.id" . $where, $params, "id");
    $min = 0;
    if (!empty($cartsInfo)) {
        foreach ($cartsInfo as $key => &$val) {
            $carts = cart_data_init($val["sid"], 0, $option_id, "");
            if (is_error($carts) || !is_array($carts["message"]) || empty($carts["message"]["cart"]["data"])) {
                unset($cartsInfo[$key]);
                continue;
            }
            $val["logo"] = tomedia($val["logo"]);
            $val["activity"] = pdo_get("tiny_wmall_store_activity", array(":uniacid" => $_W["uniacid"], ":sid" => $val["sid"]), array("title"));
            $val["cart"] = $carts["message"]["cart"];
            $_W["member"]["is_store_newmember"] = is_store_newmember($val["sid"]);
            $val["discounts"] = order_count_activity($val["sid"], $val["cart"], 0, 0, 0, 0, 1);
            $val["final_fee"] = $val["cart"]["price"];
            if (0 < $val["discounts"]["total"]) {
                $val["final_fee"] = round($val["cart"]["price"] - $val["discounts"]["total"], 2);
                if ($val["final_fee"] < 0) {
                    $val["final_fee"] = 0;
                }
            }
            $send_condition = store_order_condition($val["sid"]);
            $val["send_limit"] = round($send_condition["send_price"] - $val["cart"]["price"], 2);
        }
        $min = min(array_keys($cartsInfo));
    }
    $cartsInfo = array_values($cartsInfo);
    $result = array("cartsInfo" => $cartsInfo, "errno" => 0, "min" => $min);
    imessage($result, "", "ajax");
}
if ($ta == "truncate") {
    $sid = intval($_GPC["sid"]);
    $data = pdo_delete("tiny_wmall_order_cart", array("uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"]));
    imessage(error(0, "删除成功"), "", "ajax");
}

?>