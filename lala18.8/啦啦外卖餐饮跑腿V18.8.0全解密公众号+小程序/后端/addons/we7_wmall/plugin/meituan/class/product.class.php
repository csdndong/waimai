<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("meituan");
class product extends meituan
{
    public function queryCatList()
    {
        $data = $this->httpGet("dish/queryCatList");
        return $data;
    }
    public function queryListByEPoiId($offset = 0, $limit = 150, $shopid = 0)
    {
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $params = array("ePoiId" => $shopid, "offset" => 200 < $offset ? 200 : $offset, "limit" => $limit);
        $data = $this->httpGet("dish/queryListByEPoiId", $params);
        return $data;
    }
    public function queryBaseListByEPoiId($shopid = 0)
    {
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $params = array("ePoiId" => $shopid);
        $data = $this->httpGet("dish/queryBaseListByEPoiId", $params);
        return $data;
    }
    public function queryListByEdishCodes($goodsid, $shopid = 0)
    {
        global $_W;
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $goods = pdo_get("tiny_wmall_goods", array("uniacid" => $_W["uniacid"], "id" => $goodsid));
        if (empty($goods)) {
            return error(-1, "商品不存在");
        }
        if (empty($goods["openplateformCode"])) {
            $goods["openplateformCode"] = random(20, true);
            pdo_update("tiny_wmall_goods", array("openplateformCode" => $goods["openplateformCode"]), array("uniacid" => $_W["uniacid"], "id" => $goodsid));
        }
        $mapping = array(array("dishId" => $goods["meituanId"], "eDishCode" => $goods["openplateformCode"]));
        $result = $this->mapping($mapping, $shopid);
        if (is_error($result)) {
            return error(-1, "进行美团商品映射失败:" . $result["message"]);
        }
        $params = array("ePoiId" => $shopid, "eDishCodes" => $goods["openplateformCode"]);
        $data = $this->httpGet("dish/queryListByEdishCodes", $params);
        if (is_error($data)) {
            return error(-1, "获取商品基本信息失败:" . $data["message"]);
        }
        $openplateformCode = $goods["openplateformCode"];
        $goods = $data["list"][0];
        $attrs = $this->queryPropertyList($openplateformCode);
        if (is_error($attrs)) {
            return error(-1, "获取商品属性信息失败:" . $data["message"]);
        }
        $goods["attrs"] = $attrs;
        return $goods;
    }
    public function mapping($mappings, $shopid = 0)
    {
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $params = array("ePoiId" => $shopid, "dishMappings" => json_encode($mappings));
        $data = $this->httpPost("dish/mapping", $params);
        return $data;
    }
    public function queryPropertyList($eDishCode)
    {
        $params = array("eDishCode" => $eDishCode);
        $data = $this->httpGet("dish/queryPropertyList", $params);
        return $data;
    }
}

?>