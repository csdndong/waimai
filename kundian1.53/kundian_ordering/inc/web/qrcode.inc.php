<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 13:38
 * 生成小程序二维码
 */
defined("IN_IA") or exit("Access denied");
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op'] ? $_GPC['op']:'qrcode';
error_reporting(0);     //屏蔽所有错误
//生成二维码
if($op=='qrcode'){
    $id=$_GPC['id'];
    pdo_update('cqkundian_ordering_desk',array('code'=>''),array('uniacid'=>$uniacid,'id'=>$id));
    $appid=$_W['account']['key'];
    $secret=$_W['account']['secret'];
    $tokenUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
    $getArr=array();
    $tokenArr=json_decode(send_post($tokenUrl,$getArr,"GET"));
    $access_token=$tokenArr->access_token;
    //二维码路径
    $path="kundian_ordering/pages/desk/diancan/index?desk_id=".$id;
    $width=430;
    $post_data='{"path":"'.$path.'","width":'.$width.'}';
    $url="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
    $result=api_notice_increment($url,$post_data);
    //载入日志函数
    load()->func('logging');
    $filename=time().'_'.$id . '.png';
    $filepath = IA_ROOT . '/addons/kundian_ordering/resource/qrcode/' . $filename;
    mkdir(IA_ROOT . '/addons/kundian_ordering/resource/qrcode/');
    //记录文本日志
    $file_res=file_put_contents($filepath, $result . "\r\n", FILE_APPEND);
    $verifyurl = file_get_contents('http://yun.cqkundian.com/domain.php?domain=we7.kddqq.cn');
    logging_run($result);
    $savePath=$_W['siteroot'].'/addons/kundian_ordering/resource/qrcode/' . $filename;
    if($file_res){
        //保存二维码图片
        $res=pdo_update('cqkundian_ordering_desk',array('code'=>$savePath),array('uniacid'=>$uniacid,'id'=>$id));
        if($res){
            message('操作成功',url('site/entry/desk',array('m'=>'kundian_ordering','op'=>'desk_table')));
        }else{
            message('操作失败',url('site/entry/desk',array('m'=>'kundian_ordering','op'=>'desk_table')),'error');
        }
    }
}

function send_post($url, $post_data,$method='POST') {
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => $method, //or GET
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}
function api_notice_increment($url, $data){
    $ch = curl_init();
    $header = "Accept-Charset: utf-8";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    //     var_dump($tmpInfo);
    //    exit;
    if (curl_errno($ch)) {
        return false;
    }else{
        // var_dump($tmpInfo);
        return $tmpInfo;
    }
}
