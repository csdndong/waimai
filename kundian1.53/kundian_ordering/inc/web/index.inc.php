<?php 
defined("IN_IA") or exit("Access Denied");
checklogin();  //验证是否登录
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op'] ? $_GPC['op'] : "index";
if($op=='index'){


    $con=array(
        'uniacid'=>$uniacid,
        'ref_date'=>date("Ymd",strtotime('-1 days')),
    );
    $global=pdo_get('wxapp_general_analysis',$con);
    //用户总数
    $user_total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('cqkundian_ordering_user')." WHERE uniacid=".$uniacid);

    //商品总数
    $shop_goods_count=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('cqkundian_ordering_goods')." WHERE uniacid=".$uniacid);

    $waimai_goods_count=pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('cqkundian_ordering_product')." WHERE uniacid=".$uniacid);
    $goods_count=$shop_goods_count+$waimai_goods_count;
    //商城订单总数
    $shop_order_count=pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('cqkundian_ordering_order')." WHERE uniacid=".$uniacid);
    include $this->template('web/common/home');
}