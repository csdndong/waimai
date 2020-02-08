<?php
defined("IN_IA") or exit("Access Denied");
icheckauth();
if (!empty($_W["member"])) {
    if (MODULE_FAMILY == "wxapp") {
        echo "<div style=\"width: 100%; margin: 30% auto; text-align: center; font-size: 50px\">绑定粉丝成功</div>";
        exit;
    }
    imessage("绑定粉丝成功", "close", "success");
}
if (MODULE_FAMILY == "wxapp") {
    echo "绑定粉丝失败,请重新绑定";
    exit;
}
imessage("绑定粉丝失败,请重新绑定", "close", "info");

?>