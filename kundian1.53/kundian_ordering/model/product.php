<?php
/**
 * Created by PhpStorm.
 * User: zyl
 * Date: 2018/10/21
 * Time: 12:43
 */
defined("IN_IA") or exit("Access Denied");
class Product_KundianOrderingModel{
    protected $tableName='cqkundian_ordering_product';
    public function __construct($tableName=''){
        if($tableName){
            $this->tableName=$tableName;
        }
    }

    /**
     * 查询商品分类信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param string $order_by
     * @return array
     */
    public function getProductType($cond,$pageIndex='',$pageSize='',$order_by='rank asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall('cqkundian_ordering_product_type',$cond,'','',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall('cqkundian_ordering_product_type',$cond,'','',$order_by);
        }
        return $list;
    }

    /**
     * 根据ID获取分类信息
     * @param $id
     * @param $uniacid
     * @return bool
     */
    public function getProductTypeById($id,$uniacid){
        $list=pdo_get('cqkundian_ordering_product_type',['id'=>$id,'uniacid'=>$uniacid]);
        return $list;
    }

    /**
     * 更新商品分类信息
     * @param $updateData
     * @param array $cond
     * @return bool
     */
    public function updateProductType($updateData,$cond=[]){
        if(!empty($cond)){
            $res=pdo_update('cqkundian_ordering_product_type',$updateData,$cond);
        }else{
            $res=pdo_insert('cqkundian_ordering_product_type',$updateData);
        }
        return $res;
    }

    /**
     * 查询商品信息
     * @param $cond
     * @param string $pageIndex
     * @param string $pageSize
     * @param string $order_by
     * @return array
     */
    public function getProduct($cond,$pageIndex='',$pageSize='',$order_by='rank asc'){
        if(!empty($pageIndex) && !empty($pageSize)){
            $list=pdo_getall($this->tableName,$cond,'','',$order_by,[$pageIndex,$pageSize]);
        }else{
            $list=pdo_getall($this->tableName,$cond,'','',$order_by);
        }
        return $list;
    }

    /**
     * 根据ID获取信息
     * @param $id
     * @param $uniacid
     * @return bool
     */
    public function getProductById($id,$uniacid){
        $list=pdo_get($this->tableName,['id'=>$id,'uniacid'=>$uniacid]);
        return $list;
    }

    /**
     * 更新商品信息
     * @param $updateData
     * @param array $cond
     * @return bool
     */
    public function updateProduct($updateData,$cond=[]){
        if(!empty($cond)){
            $res=pdo_update($this->tableName,$updateData,$cond);
        }else{
            $res=pdo_insert($this->tableName,$updateData);
        }
        return $res;
    }

    /**
     * 更新商品库存和销量
     * @param $orderid
     * @param $uniacid
     */
    public function updateProductCount($orderid,$uniacid){
        $orderDetail=pdo_getall('cqkundian_ordering_order_detail',array('uniacid'=>$uniacid,'order_id'=>$orderid));
        for ($i=0;$i<count($orderDetail);$i++){
            $goodsData=pdo_get('cqkundian_ordering_product',array('uniacid'=>$uniacid,'id'=>$orderDetail[$i]['pid']));
            if($goodsData['count']>=$orderDetail[$i]['num'] && $goodsData['count']>0){
                pdo_update('cqkundian_ordering_product',array('count -='=>$orderDetail[$i]['num'] ,'sale_count +='=>$orderDetail[$i]['num'] ),array('uniacid'=>$uniacid,'id'=>$orderDetail[$i]['pid']));
            }else{
                pdo_update('cqkundian_ordering_product',array('count='=>0,'sale_count +='=>$orderDetail[$i]['num'] ),array('uniacid'=>$uniacid,'id'=>$orderDetail[$i]['pid']));
            }
        }
    }
}