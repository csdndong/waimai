<?php


defined("IN_IA") or exit("Access Denied");
include "version.php";
include "defines.php";
include "model.php";
class We7_wmallModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $config = pdo_get("tiny_wmall_config", array("uniacid" => $_W["uniacid"]), array("sysset", "id"));
        $_W["we7_wmall"]["config"] = iunserializer($config["sysset"]);
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename("tiny_wmall_reply") . " WHERE uniacid = :uniacid and `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(":uniacid" => $_W["uniacid"], ":rid" => $rid));
        if (empty($row)) {
            return "";
        }
        $row["extra"] = iunserializer($row["extra"]);
        if (in_array($row["type"], array("store", "assign", "table"))) {
            $store = pdo_get("tiny_wmall_store", array("uniacid" => $_W["uniacid"], "id" => $row["sid"]));
            if (empty($store)) {
                return "";
            }
            $sid = $store["id"];
            if ($row["type"] == "store") {
				//商家二维码
                $url = ivurl("pages/store/goods", array("sid" => $sid), true);
                $news = array();
                $news[] = array("title" => $store["title"], "description" => $store["content"], "picurl" => tomedia($store["logo"]), "url" => $url);
                return $this->respNews($news);
            }
            if ($row["type"] == "assign") {
                if (!$store["is_assign"]) {
                    return $this->respText((string) $store["title"] . "已关闭排号功能，请联系商家");
                }
                $url = ivurl("tangshi/pages/assign/assign", array("sid" => $sid), true);
                $news = array();
                $news[] = array("title" => $store["title"] . "-点击进入排号", "description" => $store["content"], "picurl" => tomedia($store["logo"]), "url" => $url);
                return $this->respNews($news);
            }
            if ($row["type"] == "table") {
				//扫桌号
                $table = pdo_get("tiny_wmall_tables", array("uniacid" => $_W["uniacid"], "id" => $row["table_id"]));
                if (empty($table)) {
                    return "";
                }
                $fans = mc_fansinfo($_W["openid"]);
                $data = array("uniacid" => $_W["uniacid"], "sid" => $row["sid"], "table_id" => $row["table_id"], "openid" => $_W["openid"], "nickname" => $fans["nickname"], "avatar" => $fans["tag"]["avatar"], "createtime" => TIMESTAMP);
                pdo_insert("tiny_wmall_tables_scan", $data);
                pdo_update("tiny_wmall_tables", array("scan_num" => $table["scan_num"] + 1), array("uniacid" => $_W["uniacid"], "id" => $row["table_id"]));
                $url = ivurl("tangshi/pages/table/goods", array("sid" => $sid, "table_id" => $row["table_id"]), true);
                $news = array();
                $news[] = array("title" => $store["title"] . "-" . $table["title"] . "号桌", "description" => "欢迎光临" . $store["title"] . ", 您当前在" . $table["title"] . "号桌点餐", "picurl" => tomedia($store["logo"]), "url" => $url);
                return $this->respNews($news);
            }
        } else {
            if ($row["type"] == "spread") {
                $invite_uid = intval($row["extra"]["uid"]);
                $member = pdo_get("tiny_wmall_members", array("uniacid" => $_W["uniacid"], "uid" => $invite_uid), array("uid", "nickname"));
                if (empty($member)) {
                    return "";
                }
                $config_mall = $_W["we7_wmall"]["config"]["mall"];
                $config_share = $_W["we7_wmall"]["config"]["share"];
                $news = array(array("title" => "您的好友(" . $member["nickname"] . ")向您推荐" . $config_mall["title"], "description" => (string) $config_share["desc"], "picurl" => tomedia($config_mall["logo"]), "url" => ivurl("pages/home/index", array("code" => $row["extra"]["uid"]), true)));
                return $this->respNews($news);
            }
        }
    }
}

?>
