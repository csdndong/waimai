<?php
defined("IN_IA") or exit("Access Denied");
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if ($op == "list") {
    $key = trim($_GPC["key"]);
    $scene = trim($_GPC["scene"]) ? trim($_GPC["scene"]) : "notify";
    $data = pdo_fetchall("select * from " . tablename("tiny_wmall_members") . " where uniacid = :uniacid and (openid = :openid or openid_wxapp = :openid_wxapp or nickname like :key or realname like :realname or mobile like :mobile or uid = :uid) order by id desc limit 50", array(":uniacid" => $_W["uniacid"], ":key" => "%" . $key . "%", ":realname" => "%" . $key . "%", ":mobile" => "%" . $key . "%", ":openid" => $key, ":openid_wxapp" => $key, ":uid" => $key), "id");
    if (!empty($data)) {
        foreach ($data as $key => &$row) {
            if ($scene == "notify") {
                if (MODULE_FAMILY == "wxapp") {
                    if (empty($row["openid"]) && empty($row["openid_wxapp"])) {
                        unset($data[$key]);
                        continue;
                    }
                } else {
                    if (empty($row["openid"])) {
                        unset($data[$key]);
                        continue;
                    }
                }
            } else {
                if ($scene == "getcash") {
                    if ($_W["we7_wmall"]["config"]["getcash"]["channel"]["wechat"] == "wxapp") {
                        if (empty($row["openid_wxapp"])) {
                            unset($data[$key]);
                            continue;
                        }
                    } else {
                        if (empty($row["openid"])) {
                            unset($data[$key]);
                            continue;
                        }
                    }
                }
            }
            $row["avatar"] = tomedia($row["avatar"]);
        }
        $fans = array_values($data);
    }
    message(array("errno" => 0, "message" => $fans, "data" => $data), "", "ajax");
}

?>