<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

global $phpjiami_decrypt;
$phpjiami_decrypt["ÁÖ®ÖÀýÃÃÃ®ˆŽ¾ˆÖ¯ÃÃ¾¯ˆÄ®¥ÃýÖ¥Ž”ýÄ"] = base64_decode("ZGVmaW5lZA==");
$phpjiami_decrypt["¯ˆÖýÃ®À¥¯ÖÁÖˆ‹¥ÖÃ®¥Ã¥ýÄÖÁÖÁÖ‹¯®Ã"] = base64_decode("dHJpbQ==");
$phpjiami_decrypt["”‹¥Ö¯ÖŽ‹ýÁ®ÖÀ®¾¾ÄÖˆ®ÄŽ¯”ý¾‹¾ÄÃýÃ"] = base64_decode("aW50dmFs");
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]) ? trim($_GPC["op"]) : "index";
if ($op == "index") {
    $_W["page"]["title"] = "åŸºç¡€è®¾ç½®";
    if ($_W["ispost"]) {
        $type = trim($_GPC["type"]);
        if ($type != "store" && $type != "plateform") {
            imessage(error(-1, "è¯·é€‰æ‹©å¯¹æŽ¥æ¨¡å¼"), "", "ajax");
        }
        $data = array("status" => intval($_GPC["status"]), "type" => $type, "city" => trim($_GPC["city"]), "mobile" => trim($_GPC["mobile"]), "md5" => trim($_GPC["md5"]), "token" => trim($_GPC["shansongtoken"]), "merchantid" => trim($_GPC["merchantid"]), "partnerNO" => trim($_GPC["partnerNO"]));
        set_plugin_config(base64_decode("c2hhbnNvbmc="), $data);
        imessage(error(0, base64_decode("6K6+572u5oiQ5Yqf")), base64_decode("cmVmcmVzaA=="), "ajax");
    }
    $notify_url = WE7_WMALL_URL . "plugin/shansong/notify.php";
    $shansong = get_plugin_config("shansong");
}
include itemplate(base64_decode("Y29uZmln"));

?>
