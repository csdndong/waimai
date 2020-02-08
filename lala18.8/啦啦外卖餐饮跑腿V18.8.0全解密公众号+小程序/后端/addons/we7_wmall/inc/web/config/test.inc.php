<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("sms");
p("号码加密接口测试");
$params = array("Expiration" => "2019-04-26 12:00:00", "PhoneNoA" => "18234096432", "PoolKey" => "FC100000064970023");
$result = sms_bindAxnExtension($params);
p($result);

?>

