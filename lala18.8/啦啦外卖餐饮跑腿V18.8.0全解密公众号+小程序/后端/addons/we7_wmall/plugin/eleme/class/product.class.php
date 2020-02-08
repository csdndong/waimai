<?php
defined("IN_IA") or exit("Access Denied");
pload()->classs("eleme");
class product extends Eleme
{
    public function getShopCategories($shopid = 0)
    {
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $params = array("shopId" => $shopid);
        $data = $this->httpPost("eleme.product.category.getShopCategories", $params);
        return $data;
    }
    public function getShopCategoriesWithChildren($shopid = 0)
    {
        if (empty($shopid)) {
            $shopid = $this->shopid;
        }
        $params = array("shopId" => $shopid);
        $data = $this->httpPost("eleme.product.category.getShopCategoriesWithChildren", $params);
        return $data;
    }
    public function getItemsByCategoryId($categoryId)
    {
        $params = array("categoryId" => $categoryId);
        $data = $this->httpPost("eleme.product.item.getItemsByCategoryId", $params);
        return $data;
    }
    public function getItem($itemId)
    {
        $params = array("itemId" => $itemId);
        $data = $this->httpPost("eleme.product.item.getItem", $params);
        return $data;
    }
}

?>