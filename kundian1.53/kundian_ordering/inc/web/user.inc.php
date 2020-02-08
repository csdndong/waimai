<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17 0017
 * Time: 10:44
 */
defined("IN_IA") or exit("Access denied");
checklogin();
$ops=array('list','edit','saveModel','delete','qcode','shouquan','getOpenid');
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :'list';
switch ($op){
    case 'list':

        $condition=array();
        if(!empty($_GPC['nickname'])){
            $nickname=trim($_GPC['nickname']);
            $condition['nickname LIKE']= '%'.$nickname.'%';
        }
        $condition['uniacid']=$uniacid;
        $listCount=pdo_getall("cqkundian_ordering_user",$condition);
        $total=count($listCount);   //数据的总条数
        $pageSize=10; //每页显示的数据条数
        $pageIndex=intval($_GPC['page']) ? intval($_GPC['page']) :1;  //当前页
        $pager=pagination($total,$pageIndex,$pageSize);
        $list=pdo_getall("cqkundian_ordering_user",$condition,'','','create_time desc',array($pageIndex,$pageSize));

        include $this->template("web/user/index");
        break;

    case 'edit':
        $edit_where=array(
            'id'=>$_GPC['id'],
            'uniacid'=>$uniacid,
        );
        $list=pdo_get("cqkundian_ordering_user",$edit_where);
        include $this->template("web/user/edit");
        break;

    case 'saveModel':
        $updateData=array(
            'nickname'=>$_GPC['nickname'],
            'avatarurl'=>tomedia($_GPC['head_img']),
            'phone'=>$_GPC['phone'],
            'sex'=>$_GPC['sex'],
            'rank'=>$_GPC['rank'],
            'address'=>$_GPC['address'],
            'create_time'=>time(),
            'uniacid'=>$uniacid,
            'uid'=>$_W['uid'],
        );

        if(empty($_GPC['id'])){
            $updateData['order_count']=0;
            $request=pdo_insert("cqkundian_ordering_user",$updateData);
        }else{
            $model_where=array(
                'id'=>$_GPC['id'],
                'uniacid'=>$uniacid,
            );
            $request=pdo_update("cqkundian_ordering_user",$updateData,$model_where);
        }
        if($request){
            message('操作成功',$this->createWebUrl('user'));
        }else{
            message("操作失败");
        }
        break;

    case 'delete':
        $condition=array();
        $condition['id']=$_GPC['id'];
        $condition['uniacid']=$uniacid;
        $request=pdo_delete("cqkundian_ordering_user",$condition);
        if($request){
            echo  json_encode(array('status'=>1,'msg'=>"操作成功"));
        }else{
            echo json_encode(array('status'=>2,'msg'=>"操作失败"));
        }
        break;
    case 'qcode':           //生成授权二维码

        $wxData=pdo_get('cqkundian_ordering_msg_config',array('uniacid'=>$uniacid));
        include 'phpqrcode/phpqrcode.php';
//        $value = "https://we7.kddqq.cn/addons/kundian_magasins/inc/web/wx.php";                  //二维码内容
        $oldStr= url ('site/entry/user',array('m'=>'kundian_ordering','op'=>'shouquan','version_id'=>$_GPC['version_id'],'uniacid'=>$uniacid,'id'=>$_GPC['id']));
        $oldStr=ltrim($oldStr, ".");
        $nowStr=$_W['siteroot'].'web'.$oldStr;
        $errorCorrectionLevel = 'L';    //容错级别
        $matrixPointSize = 5;           //生成图片大小
        //生成二维码图片
        $QR = QRcode::png($nowStr,false,$errorCorrectionLevel, $matrixPointSize, 2);
        var_dump($QR);die;
        break;

    case 'shouquan':    //收取获取code
        $wxData=pdo_get('cqkundian_ordering_msg_config',array('uniacid'=>$_GPC['uniacid']));
        $appid=$wxData['wx_appid'];
        $oldStr= url ('site/entry/user',array('m'=>'kundian_ordering','op'=>'getOpenid','version_id'=>$_GPC['version_id'],'uniacid'=>$_GPC['uniacid'],'id'=>$_GPC['id']));
        $oldStr=ltrim($oldStr, ".");
        $nowStr=$_W['siteroot'].'web'.$oldStr;
        $redirect_uri = urlencode ($nowStr );
        $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header("Location:".$url);
        break;

    case 'getOpenid':       //获取用户的openid
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
            $res = pdo_update('cqkundian_ordering_user', array('wx_openid' => $openid), array('id' => $id, 'uniacid' => $_GPC['uniacid']));
            if ($res) {
                echo "<script>alert('授权成功！');</script>";
            } else {
                echo "<script>alert('已授权！');</script>";
            }
        }else{
            echo "<script>alert('授权失败！');</script>";
        }
        break;
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