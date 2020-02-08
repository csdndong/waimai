<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
icheckauth();
if ($_config_plugin["basic"]["status"] != 1) {
    imessage(error(-1, "超级会员功能未开放"), "", "ajax");
}
if ($op == "index") {
    $nickname = $_W["member"]["nickname"];
    $result = array("nickname" => $nickname);
    imessage(error(0, $result), "", "ajax");
} else {
    if ($op == "exchange") {
        $code = trim($_GPC["code"]);
        $status = svip_code_exchange($code, $_W["member"]["uid"]);
        if (is_error($status)) {
            imessage($status, "", "ajax");
        }
        imessage(error(0, "兑换成功"), "", "ajax");
    }
}

?>