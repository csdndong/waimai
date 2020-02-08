<?php
/**
 * Created by PhpStorm.
 * User: 坤典科技
 * Date: 2018/6/6 0006
 * Time: 13:37
 * 快餐
 */
defined("IN_IA") or exit("Access Denied");
require_once ROOT_KUNDIAN_ORDERING.'model/product.php';
require_once ROOT_KUNDIAN_ORDERING.'model/common.php';
class TakeController{
    public $uniacid='';
    public $uid='';
    static $product='';
    static $common='';

    public function __construct(){
        global $_GPC;
        $this->uniacid=$_GPC['uniacid'];
        $this->uid=$_GPC['uid'];
        self::$product=new Product_KundianOrderingModel();
        self::$common=new Common_KundianOrderingModel();
    }

    public function getGoodsData($get){
        $goodsType=self::$product->getProductType(['uniacid'=>$this->uniacid,'is_use'=>1]);
        for($i=0;$i<count($goodsType);$i++){
            $goodsData=self::$product->getProduct(['uniacid'=>$this->uniacid,'tid'=>$goodsType[$i]['id'],'is_putaway'=>1]);
            for ($j = 0; $j < count($goodsData); $j++) {
                $goodsData[$j]['price'] = number_format($goodsData[$j]['price'], 2);
                $goodsData[$j]['selectNum'] = 0;
            }
            $goodsType[$i]['items']=$goodsData;
        }
        //查询商家信息
        $aboutData=self::$common->getAboutData($this->uniacid);
        $setData=self::$common->getSetData(['shop_img'],$this->uniacid);
        $request=[
            'goodsType'=>$goodsType,
            'aboutData'=>$aboutData,
            'shopImg'=>unserialize($setData['shop_img']),
        ];
        echo json_encode($request);die;
    }

    public function getProductSpec($get){
        $skuData=pdo_getall('cqkundian_ordering_product_spec_sku',array('uniacid'=>$this->uniacid,'goods_id'=>$get['goods_id']));
        $specItem=pdo_getall('cqkundian_ordering_product_spec',array('goods_id'=>$get['goods_id'],'uniacid'=>$this->uniacid));
        for ($i=0;$i<count($specItem);$i++){
            $specVal=pdo_getall('cqkundian_ordering_product_spec_value',array('uniacid'=>$this->uniacid,'spec_id'=>$specItem[$i]['id']));
            $specItem[$i]['specVal']=$specVal;
        }
        $request=[
            'skuData'=>$skuData,
            'specItem'=>$specItem,
        ];
        echo json_encode($request);die;
    }
}
