<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23
 * Time: 10:06
 */
defined("IN_IA") or exit("Access Denied");
class Goods_KundianOrderingModel{

    /**
     * 获取商品分类列表
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param string $order_by
     * @return array
     */
    public function getGoodsType($cond,$pageIndex='',$pageSize='',$order_by='rank asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall('cqkundian_ordering_goods_type',$cond,'','',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall('cqkundian_ordering_goods_type',$cond,'','',$order_by);
        }
        return $list;
    }

    /**
     * 获取商品列表
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param string $order_by
     * @return array
     */
    public function getGoodsList($cond,$pageIndex='',$pageSize='',$order_by='rank asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall('cqkundian_ordering_goods',$cond,'','',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall('cqkundian_ordering_goods',$cond,'','',$order_by);
        }
        return $list;
    }


}