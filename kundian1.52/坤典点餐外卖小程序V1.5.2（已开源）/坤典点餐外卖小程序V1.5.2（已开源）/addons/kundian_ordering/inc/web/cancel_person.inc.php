<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30 0030
 * Time: 11:40
 */
defined("IN_IA") or exit("Access Denied");
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
global $_W,$_GPC;
//当前小程序唯一id
$uniacid=$_W['uniacid'];
$op=$_GPC['op']? $_GPC['op'] : 'list';
if($op=='list'){
    $condition=array();
    if(!empty($_GPC['phone'])){
        $phone=trim($_GPC['phone']);
        $condition['phone LIKE']= '%'.$phone.'%';
    }
    $condition['uniacid']=$uniacid;
    $condition['type']=2;
    $listCount=pdo_getall("cqkundian_ordering_cancel_person",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_cancel_person",$condition,'','','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list[$i]['create_time']=date("Y-m-d H:i:s",$list[$i]['create_time']);
    }
    include $this->template("web/cancel_person/index");
}

if($op=='edit'){
    $id=trim($_GPC['id']);
    $list=pdo_get('cqkundian_ordering_cancel_person',array('id'=>$id,'uniacid'=>$uniacid));
    include $this->template("web/cancel_person/edit");
}
if($op=='saveModel'){
    $data=array(
        'phone'=>trim($_GPC['phone']),
        'pwd'=>$_GPC['pwd'],
        'uid'=>$_GPC['uid'],
        'status'=>$_GPC['status'],
        'rank'=>$_GPC['rank'],
        'wx_openid'=>$_GPC['wx_openid'],
        'create_time'=>time(),
        'uniacid'=>$uniacid,
        'type'=>2,  //配送员
    );
    if(empty($_GPC['id'])){  //新增
        $request=pdo_insert("cqkundian_ordering_cancel_person",$data);
    }else{
        $condition=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_cancel_person",$data,$condition);
    }
    if($request){
        message("操作成功",$this->createWebUrl("cancel_person"));
    }else {
        message("操作失败", '', 'warning');
    }
}
if($op=='statusChange'){
    $id=$_GPC['id'];
    $condition=array(
        'id'=>$id,
        'uniacid'=>$uniacid,
    );
    $request=pdo_update("cqkundian_ordering_cancel_person",array('status'=>$_GPC['status']),$condition);
    if($request){
        echo json_encode(array('status'=>1,'msg'=>'操作成功'));
    }else{
        echo json_encode(array('status'=>0,'msg'=>'操作失败'));
    }
}
if($op=='delete'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_cancel_person",$condition);
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

if($op=='cancel_record'){
    $condition=array();
    if(!empty($_GPC['phone'])){
        $phone=trim($_GPC['phone']);
        $condition['phone LIKE']= '%'.$phone.'%';
    }
    $condition['uniacid']=$uniacid;
    $listCount=pdo_getall("cqkundian_ordering_cancel_record",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_cancel_record",$condition,'','',array('create_time'=>'desc'),array($pageIndex,$pageSize));
    include $this->template("web/cancel_person/cancel_record");
}
if($op=='delete_record'){
    $condition=array();
    $condition['id']=$_GPC['id'];
    $condition['uniacid']=$uniacid;
    $request=pdo_delete("cqkundian_ordering_cancel_record",$condition);
    if($request){
        echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
    }else{
        echo json_encode(array('status'=>2,'msg'=>"操作失败"));
    }
}

//服务员列表
if($op=='server_person'){
    $condition=array();
    if(!empty($_GPC['phone'])){
        $phone=trim($_GPC['phone']);
        $condition['phone LIKE']= '%'.$phone.'%';
    }
    $condition['uniacid']=$uniacid;
    $condition['type']=1;
    $listCount=pdo_getall("cqkundian_ordering_cancel_person",$condition);
    $total=count($listCount);   //数据的总条数
    $pageSize=10; //每页显示的数据条数
    $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
    $pager=pagination($total,$pageIndex,$pageSize);
    $list=pdo_getall("cqkundian_ordering_cancel_person",$condition,'','','rank asc',array($pageIndex,$pageSize));
    for ($i=0;$i<count($list);$i++){
        $list[$i]['create_time']=date("Y-m-d H:i:s",$list[$i]['create_time']);
    }
    include $this->template("web/cancel_person/server_person");
}
if($op=='server_person_edit'){
    $id=trim($_GPC['id']);
    $list=pdo_get('cqkundian_ordering_cancel_person',array('id'=>$id,'uniacid'=>$uniacid));
    include $this->template('web/cancel_person/server_person_edit');
}

if($op=='server_person_save'){
    $data=array(
        'phone'=>trim($_GPC['phone']),
        'pwd'=>$_GPC['pwd'],
        'uid'=>$_GPC['uid'],
        'status'=>$_GPC['status'],
        'rank'=>$_GPC['rank'],
        'wx_openid'=>$_GPC['wx_openid'],
        'create_time'=>time(),
        'uniacid'=>$uniacid,
        'type'=>1,  //服务员
    );
    if(empty($_GPC['id'])){  //新增
        $request=pdo_insert("cqkundian_ordering_cancel_person",$data);
    }else{
        $condition=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $request=pdo_update("cqkundian_ordering_cancel_person",$data,$condition);
    }
    if($request){
        message("操作成功",$this->createWebUrl("cancel_person"));
    }else {
        message("操作失败", '', 'warning');
    }
}

//生成授权二维码
if($op=='qcode'){
    $wxData=pdo_get('cqkundian_ordering_msg_config',array('uniacid'=>$uniacid));
    include 'phpqrcode/phpqrcode.php';
    $oldStr= url ('site/entry/cancelPerson',array('m'=>'kundian_ordering','op'=>'shouquan','version_id'=>$_GPC['version_id'],'uniacid'=>$uniacid,'id'=>$_GPC['id']));
    $oldStr=ltrim($oldStr, ".");
    $nowStr=$_W['siteroot'].'web'.$oldStr;
    $errorCorrectionLevel = 'L';    //容错级别
    $matrixPointSize = 5;           //生成图片大小
    //生成二维码图片
    $QR = QRcode::png($nowStr,false,$errorCorrectionLevel, $matrixPointSize, 2);
    var_dump($QR);die;
}

if($op=='shouquan'){
    $wxData=pdo_get('cqkundian_ordering_msg_config',array('uniacid'=>$_GPC['uniacid']));
    $appid=$wxData['wx_appid'];
    $oldStr= url ('site/entry/cancelPerson',array('m'=>'kundian_ordering','op'=>'getOpenid','version_id'=>$_GPC['version_id'],'uniacid'=>$_GPC['uniacid'],'id'=>$_GPC['id']));
    $oldStr=ltrim($oldStr, ".");
    $nowStr=$_W['siteroot'].'web'.$oldStr;
    $redirect_uri = urlencode ($nowStr );
    $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    header("Location:".$url);
}

if($op=='getOpenid'){
    $wxData=pdo_get('cqkundian_ordering_msg_config',array('uniacid'=>$_GPC['uniacid']));
    $appid =$wxData['wx_appid'];
    $secret = $wxData['wx_secret'];
    $code = $_GET["code"];
    //第一步:取得openid
    $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
    $oauth2 = getJson($oauth2Url);
    //第二步:根据全局access_token和openid查询用户信息
    $access_token = $oauth2["access_token"];
    $openid = $oauth2['openid'];
    $id=$_GPC['id'];
    if($openid) {
        $res = pdo_update('cqkundian_ordering_cancel_person', array('wx_openid' => $openid), array('id' => $id, 'uniacid' => $_GPC['uniacid']));
        if ($res) {
            echo "<script>alert('授权成功！');</script>";
        } else {
            echo "<script>alert('已授权！');</script>";
        }
    }else{
        echo "<script>alert('授权失败！');</script>";
    }
}
function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}