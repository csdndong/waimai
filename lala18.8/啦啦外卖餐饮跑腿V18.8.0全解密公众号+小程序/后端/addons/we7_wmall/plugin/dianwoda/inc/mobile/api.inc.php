<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;

pload()->classs("dianwoda");
$dianwoda = new dianwoda(893, 3);
$result = $dianwoda->orderQuery();
p($dianwoda);

?>
