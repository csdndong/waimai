<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/18 0018
 * Time: 10:56
 */
defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$uniacid=$_W['uniacid'];
$op=$_GPC['op'] ? $_GPC['op'] :"msg_set";
//短信配置
if($op=='msg_set'){
    $list=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    include $this->template("web/inform/msg_set");
}
if($op=='msg_set_save'){
    $updateData=array(
        'appkey'=>trim($_GPC['appkey']),
        'secret'=>trim($_GPC['secret']),
        'sign_name'=>trim($_GPC['sign_name']),
        'template_code'=>trim($_GPC['template_code']),
        'phone'=>$_GPC['phone'],
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){
        $res=pdo_insert("cqkundian_ordering_msg_config",$updateData);
    }else{
        $res=pdo_update("cqkundian_ordering_msg_config",$updateData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    if($res){
        message("操作成功",$this->createWebUrl('inform'));
    }else{
        message('操作失败');
    }
}

//打印机配置
if($op=='print_set'){
    $list=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    $printData=pdo_getall('cqkundian_ordering_print',array('uniacid'=>$uniacid));
    include $this->template('web/inform/print_set');
}
if($op=='print_set_save'){
    $updateData=array(
        'user'=>$_GPC['user'],
        'ukey'=>$_GPC['ukey'],
        'sn'=>$_GPC['sn'],
        'uniacid'=>$uniacid,
    );
    if(empty($_GPC['id'])){
        $res=pdo_insert("cqkundian_ordering_msg_config",$updateData);
    }else{
        $res=pdo_update("cqkundian_ordering_msg_config",$updateData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    if($res){
        message("操作成功",$this->createWebUrl('inform'));
    }else{
        message('操作失败');
    }
}

//微信公众号配置
if($op=='wx_set'){
    $list=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    include $this->template("web/inform/wx_set");
}

if($op=='wx_set_save'){
    $updateData=array(
        'wx_appid'=>$_GPC['wx_appid'],
        'wx_secret'=>$_GPC['wx_secret'],
        'wx_template_id'=>$_GPC['wx_template_id'],
        'wx_order_template_id'=>$_GPC['wx_order_template_id'],
        'wx_small_template_id'=>$_GPC['wx_small_template_id'],
        'wx_cancel_order_template'=>$_GPC['wx_cancel_order_template'],
        'msg_type'=>$_GPC['msg_type'],
        'get_openid'=>$_GPC['get_openid'],
        'uniacid'=>$uniacid,
    );

    if($_FILES['wx_key']['name']!=''){
        $updateData['wx_key']=uploadPem($_FILES['wx_key']);
    }
    if($_FILES['wx_cert']['name']!=''){
        $updateData['wx_cert']=uploadPem($_FILES['wx_cert']);
    }
    if(empty($_GPC['id'])){
        $res=pdo_insert("cqkundian_ordering_msg_config",$updateData);
    }else{
        $res=pdo_update("cqkundian_ordering_msg_config",$updateData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
    }
    if($res){
        message("操作成功",$this->createWebUrl('inform'));
    }else{
        message('操作失败');
    }
}

if($op=='print_test'){
    $printData=pdo_getall('cqkundian_ordering_print',array('uniacid'=>$uniacid));
    include $this->template('web/inform/print_test');
}

if($op=='print_test_save'){
    $res=printOrder($_GPC['title'],$_GPC['content'],$_W['uniacid'],$_GPC['sn']);
    var_dump($res);
}

function printOrder($title,$content,$uniacid,$sn){
    $msgConfig=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
    include 'print/HttpClient.class.php';
    define('USER', $msgConfig['user']);    //*必填*：飞鹅云后台注册账号
    define('UKEY', $msgConfig['ukey']);    //*必填*: 飞鹅云注册账号后生成的UKEY
    define('SN', $sn);        //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
    //以下参数不需要修改
    define('IP', 'api.feieyun.cn');            //接口IP或域名
    define('PORT', 80);                        //接口IP端口
    define('PATH', '/Api/Open/');        //接口路径
    define('STIME', time());                //公共参数，请求时间
    define('SIG', sha1(USER . UKEY . STIME));   //公共参数，请求公钥

    $orderInfo = '<CB>'.$title.'</CB><BR>';
    $orderInfo .= $content.'<BR>';
    //echo $orderInfo;
    //打开注释可测试
    return wp_print(SN, $orderInfo, 1);
}

/*
 *  方法1
	拼凑订单内容时可参考如下格式
	根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
*/
function wp_print($printer_sn,$orderInfo,$times){

    $content = array(
        'user'=>USER,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_printMsg',

        'sn'=>$printer_sn,
        'content'=>$orderInfo,
        'times'=>$times//打印次数
    );

    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        echo 'error';
    }
    else{
        //服务器返回的JSON字符串，建议要当做日志记录起来
        echo $client->getContent();
    }

}


/**
 * 微擎原始文件上传文件（pem文件）
 * @param $file
 * @return bool
 */
function uploadPem($file){
    global $_W;
    $dir_url=ATTACHMENT_ROOT.'/kundian_ordering/'.$_W['uniacid']."/"; //上传路径
    mkdirs($dir_url); //创建目录
    if (($file["type"] == "application/octet-stream") && ($file["size"] < 20000)) {
        if ($file["error"] > 0){
            echo false;
        }
        else{
            if (file_exists("upload/" . $file["name"])){
                return false;
            }
            else{
                $res=move_uploaded_file($file["tmp_name"],$dir_url . $file["name"]);
                if($res){
                    return $dir_url.$file['name'];
                }else{
                    return false;
                }
            }
        }
    }
    else{
        return false;
    }
}