<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 15:55
 */
defined("IN_IA") or exit("Access Denied");
global $_W,$_GPC;
$op=$_GPC['op']? $_GPC['op'] :'index';
if($op=='index'){
    echo json_encode(array('code'=>1));
}
if($op=='getAbout'){
    $request=array();
    $uniacid=$_GPC['uniacid'];
    $aboutData=pdo_get("cqkundian_ordering_about",array('uniacid'=>$uniacid));
    $request['aboutData']=$aboutData;
    echo json_encode($request);
}