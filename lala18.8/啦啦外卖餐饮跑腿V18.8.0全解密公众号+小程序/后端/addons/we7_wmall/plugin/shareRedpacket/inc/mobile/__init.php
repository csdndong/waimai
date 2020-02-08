<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$redPacket = shareRedpacket_get();
if (is_error($redPacket)) {
    imessage($redPacket["message"], "close", "info");
}

?>