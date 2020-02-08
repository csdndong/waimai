<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 15:28
 */

defined("IN_IA") or exit("Access denied");
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
require_once ROOT_KUNDIAN_ORDERING.'model/common.php';
$commonModel=new Common_KundianOrderingModel();
global $_W,$_GPC;
$ops=array("edit",'saveModel');
$uniacid=$_W['uniacid'];
$op=$_GPC['op']? $_GPC['op'] :"edit";

if($op=='edit'){
    $list=pdo_get("cqkundian_ordering_about",array('uniacid'=>$uniacid));
    include $this->template("web/about/edit");
}

if($op=='saveModel'){
    $updateData=array(
        'merchant_name'=>trim($_GPC['merchant_name']),
        'logo_img'=>tomedia(trim($_GPC['logo_img'])),
        'merchant_desc'=>trim($_GPC['merchant_desc']),
        'wxchat'=>trim($_GPC['wxchat']),
        'phone'=>$_GPC['phone'],
        'address'=>$_GPC['address'],
        'in_time'=>$_GPC['in_time'],
        'begin_price'=>$_GPC['begin_price'],
        'send_price'=>$_GPC['send_price'],
        'package_price'=>$_GPC['package_price'],
        'send_time'=>$_GPC['send_time'],
        'is_jian_send_price'=>$_GPC['is_jian_send_price'],
        'man_price'=>$_GPC['man_price'],
        'tags'=>$_GPC['tags'],
        'longitude'=>$_GPC['longitude'],
        'latitude'=>$_GPC['latitude'],
        'uniacid'=>$uniacid,
        'center_banner'=>tomedia($_GPC['center_banner']),
        'ordering_title'=>$_GPC['ordering_title'],
    );
    if(empty($_GPC['id'])){
        $res=pdo_insert("cqkundian_ordering_about",$updateData);
    }else{
        $res=pdo_update("cqkundian_ordering_about",$updateData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    if($res){
        message("操作成功",$this->createWebUrl('about'));die;
    }else{
        message('操作失败');die;
    }
}


//保存
if($op=='shopImg'){
    $condition=array(
        'ikey'=>array('shop_img'),
        'uniacid'=>$uniacid,
    );
    $list=pdo_get('cqkundian_ordering_set',$condition);
    $list['value']=unserialize($list['value']);
    include $this->template("web/about/shopImg");
}
if($op=='shopImgSave'){
    $shop_img=$_GPC['shop_img'];
    for ($i=0;$i<count($shop_img);$i++){
        $shop_img[$i]=toimage($shop_img[$i]);
    }
    $data=array(
        'ikey'=>'shop_img',
        'value'=>serialize($shop_img),
        'uniacid'=>$uniacid,
    );
    $shopImgData=pdo_get('cqkundian_ordering_set',array('uniacid'=>$uniacid,'ikey'=>'shop_img'));
    if(empty($shopImgData)){
        $res=pdo_insert('cqkundian_ordering_set',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_set',$data,array('uniacid'=>$uniacid,'ikey'=>'shop_img'));
    }
    if($res){
        message('操作成功',url('site/entry/about',array('m'=>'kundian_ordering','op'=>'shopImg')));die;
    }else{
        message('操作失败或没有修改任何信息');die;
    }
}


//货到付款设置
if($op=='pay_on_delivery'){
    $condition=array(
        'ikey'=>array('pay_on_delivery'),
        'uniacid'=>$uniacid,
    );
    $list=pdo_get('cqkundian_ordering_set',$condition);
    include $this->template('web/about/pay_on_delivery');
}

//餐台营业模式设置（1普通模式和2快餐模式）
if($op=='business_mode'){
    $where = [
        //'ikey'=>['business_mode'],
        'uniacid'=>$uniacid,
    ];
    $temp = pdo_get('cqkundian_ordering_about',$where);
    $list = $temp['business_mode'];
    if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST'){
        $mode = isset($_POST['business_mode'])? $_POST['business_mode'] : 0;
        $res = pdo_update('cqkundian_ordering_about',array('business_mode'=>(int)$mode),$where);
        //echo (int)$_POST['business_mode'];die;
        if($res){
            message('操作成功',url('site/entry/about',array('m'=>'kundian_ordering','op'=>'business_mode')));die;
        }
            message('操作失败或没有修改任何信息');
        die;
    }

    //
    include $this->template('web/config/business_mode');
}

if($op=='pay_on_delivery_save'){
    $data=array(
        'ikey'=>'pay_on_delivery',
        'value'=>$_GPC['pay_on_delivery'],
        'uniacid'=>$uniacid,
    );
    $deliveryData=pdo_get('cqkundian_ordering_set',array('uniacid'=>$uniacid,'ikey'=>'pay_on_delivery'));
    if(empty($deliveryData)){
        $res=pdo_insert('cqkundian_ordering_set',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_set',$data,array('uniacid'=>$uniacid,'ikey'=>'pay_on_delivery'));
    }
    if($res){
        message('操作成功',url('site/entry/about',array('m'=>'kundian_ordering','op'=>'pay_on_delivery')));die;
    }else{
        message('操作失败或没有修改任何信息');die;
    }
}

/*新版本首页导航栏设置*/
if($op=='navSet'){
    $list=$commonModel->getNavList(array('uniacid'=>$uniacid));
    $count=count($list);
    include $this->template('web/about/navSet');
}

if($op=='addNav'){
    if(!empty($_GPC['id'])){
        $list=$commonModel->getNavList(['id'=>$_GPC['id'],'uniacid'=>$uniacid],false);
    }
    include $this->template('web/about/addNav');
}

if($op=='saveNav'){
    $data=array(
        'title'=>$_GPC['title'],
        'eng_title'=>$_GPC['eng_title'],
        'icon'=>tomedia($_GPC['icon']),
        'color'=>$_GPC['color'],
        'rank'=>$_GPC['rank'],
        'path'=>$_GPC['path'],
        'uniacid'=>$uniacid
    );
    if(!empty($_GPC['id'])){
        $res=$commonModel->updateNavData($data,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }else{
        $res=$commonModel->updateNavData($data);
    }
    if($res){
        message('保存成功',url('site/entry/about',['m'=>$_GPC['m'],'op'=>'navSet']));
    }else{
        message('保存失败或没有修改任何信息');die;
    }
}

if($op=='deleteNav'){
    $id=$_GPC['id'];
    $res=$commonModel->deleteNav($id,$uniacid);
    echo $res ? json_encode(array('status'=>1,'msg'=>'删除成功')) : json_encode(array('status'=>2,'msg'=>'删除失败'));
    die;
}

if($op=='limit'){
    $limit=getDistance('29.600320','106.496310','29.629980','106.485420');
    $limit1=distance('29.600320','106.496310','29.629980','106.485420');
    var_dump($limit);
    var_dump($limit1);
}

if($op=='addressSet'){
    $condition=array(
        'ikey'=>array('address_switch'),
        'uniacid'=>$uniacid,
    );
    $list=pdo_get('cqkundian_ordering_set',$condition);
    include $this->template('web/about/addressSet');
}

if($op=='addressSetSave'){
    $data=array(
        'ikey'=>'address_switch',
        'value'=>$_GPC['address_switch'],
        'uniacid'=>$uniacid,
    );
    $deliveryData=pdo_get('cqkundian_ordering_set',array('uniacid'=>$uniacid,'ikey'=>'address_switch'));
    if(empty($deliveryData)){
        $res=pdo_insert('cqkundian_ordering_set',$data);
    }else{
        $res=pdo_update('cqkundian_ordering_set',$data,array('uniacid'=>$uniacid,'ikey'=>'address_switch'));
    }
    if($res){
        message('操作成功',url('site/entry/about',array('m'=>'kundian_ordering','op'=>'addressSet')));die;
    }else{
        message('操作失败或没有修改任何信息');die;
    }
}


/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2){
    $earthRadius = 6367000; //approximate radius of earth in meters
    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;
    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    return round($calculatedDistance);
}

/**
 * @param $lat1
 * @param $lon1
 * @param $lat2
 * @param $lon2
 * @param float $radius  星球半径
 * @return float
 */
function distance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137)
{
    $rad = floatval(M_PI / 180.0);

    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;

    $theta = $lon2 - $lon1;

    $dist = acos(sin($lat1) * sin($lat2) +
        cos($lat1) * cos($lat2) * cos($theta)
    );

    if ($dist < 0 ) {
        $dist += M_PI;
    }

    return $dist = $dist * $radius;
}





