<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
pload()->classs("product");
$product = new product(1);
$result = $product->queryListByEdishCodes(625, 59);
p($result);
exit;

?>