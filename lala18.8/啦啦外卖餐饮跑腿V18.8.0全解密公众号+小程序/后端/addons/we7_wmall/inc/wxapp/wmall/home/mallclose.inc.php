<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
icheckauth();
imessage(error(0, "平台已开启"), "", "ajax");

?>