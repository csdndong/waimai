<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$url = trim($_GPC["url"]);
if (!empty($unisetting["jsauth_acid"])) {
    $jsauth_acid = $unisetting["jsauth_acid"];
} else {
    if ($_W["account"]["level"] < 3 && !empty($unisetting["oauth"]["account"])) {
        $jsauth_acid = $unisetting["oauth"]["account"];
    } else {
        $jsauth_acid = $_W["acid"];
    }
}
if (!empty($jsauth_acid)) {
    $account_api = WeAccount::create($jsauth_acid);
    if (!empty($account_api)) {
        $_W["account"]["jssdkconfig"] = $account_api->getJssdkConfig($url);
        $_W["account"]["jsauth_acid"] = $jsauth_acid;
    }
}
$result = array("jssdkconfig" => $_W["account"]["jssdkconfig"]);
imessage(error(0, $result), "", "ajax");

?>