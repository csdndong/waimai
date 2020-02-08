<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/22 0022
 * Time: 17:37
 */

defined("IN_IA") or exit("Access denied");
checklogin();
global $_W,$_GPC;
$ops=array('list','edit','saveModel','delete');
$uniacid=$_W['uniacid'];
$op=in_array($_GPC['op'],$ops) ? $_GPC['op'] :"list";
switch ($op){
    case 'list':
        $list=pdo_getall("cqkundian_ordering_print",$uniacid);
        include $this->template("web/print/index");
        break;

    case 'edit':
        $id=$_GPC['id'];
        $list=pdo_get("cqkundian_ordering_print",array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
        include $this->template("web/print/edit");
        break;

    case 'saveModel':
        $insertData=array(
            'sn'=>$_GPC['sn'],
            'key'=>$_GPC['key'],
            'name'=>$_GPC['name'],
            'carnum'=>$_GPC['carnum'],
            'create_time'=>time(),
            'uniacid'=>$uniacid,
        );
        include 'print/HttpClient.class.php';
        $snlist = $insertData['sn']."#".$insertData['key']."#".$insertData['name']."#".$insertData['carnum'];
        $msgData=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
        define('IP','api.feieyun.cn');			//接口IP或域名
        define('PORT',80);						//接口IP端口
        define('PATH','/Api/Open/');		//接口路径
        define('STIME', time());			    //公共参数，请求时间
        define('SIG', sha1($msgData['user'].$msgData['ukey'].STIME));   //公共参数，请求公钥
        if(empty($_GPC['id'])){
            $data=pdo_insert("cqkundian_ordering_print",$insertData);
            if($data){
                $res=addprinter($snlist,$msgData['user'],'Open_printerAddlist');
//                var_dump(json_decode($res));die;
                $res1=json_decode($res);
                if($res1->msg=='ok'){
                    message("操作成功",$this->createWebUrl('print'));
                }else{
                    message("操作失败");
                }
            }else{
                message("操作失败");
            }
        }else{
            $data=pdo_update("cqkundian_ordering_print",$insertData,array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
            if($data){
                $printData=pdo_get("cqkundian_ordering_print",array('id'=>$_GPC['id'],'uniacid'=>$uniacid));
                $res=editPrinter($msgData['user'],$printData['sn'],$snlist);
                $res1=json_decode($res);
                if($res1->msg=='ok'){
                    message("操作成功",$this->createWebUrl('print'));
                }else{
                    message("操作失败");
                }
            }else{
                message("操作失败");
            }
        }
        break;

    case 'delete':
        $id=$_GPC['id'];
        include 'print/HttpClient.class.php';
        $msgData=pdo_get("cqkundian_ordering_msg_config",array('uniacid'=>$uniacid));
        define('IP','api.feieyun.cn');			//接口IP或域名
        define('PORT',80);						//接口IP端口
        define('PATH','/Api/Open/');		//接口路径
        define('STIME', time());			    //公共参数，请求时间
        define('SIG', sha1($msgData['user'].$msgData['ukey'].STIME));   //公共参数，请求公钥
        $data=pdo_get("cqkundian_ordering_print",array('id'=>$id,'uniacid'=>$uniacid));
        $res=pdo_delete("cqkundian_ordering_print",array('id'=>$id,'uniacid'=>$uniacid));

        if($res){
            $res=deletePrinter($msgData['user'],$data['sn']);
            $res1=json_decode($res);
            if($res1->msg=='ok'){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
        }else{
            echo json_encode(array('status'=>1));
        }
        break;
}


/**
 * 添加打印机
 * @param $snlist  打印机添加信息
 * @param $user    打印机用户
 * @param $apiname 接口名称
 * @return string  返回类型
 */
function addprinter($snlist,$user,$apiname){

    $content = array(
        'user'=>$user,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>$apiname,

        'printerContent'=>$snlist
    );

    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        echo 'error';
    }
    else{
        return $client->getContent();
    }

}

/**
 * 编辑打印机信息
 * @param $user  打印机用户
 * @param $sn    打印机编号
 * @param $snlist 需要修改的值
 * @return string  返回值
 */
function editPrinter($user,$sn,$snlist){
    $content = array(
        'user'=>$user,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_printerEdit',
        'sn'=>$sn,
        'printerContent'=>$snlist
    );
    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        echo 'error';
    }else{
        return $client->getContent();
    }
}

function deletePrinter($user,$snlist){
    $content = array(
        'user'=>$user,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_printerDelList',
        'snlist'=>$snlist
    );
    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$content)){
        echo 'error';
    }else{
        return $client->getContent();
    }
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
/*
 *  方法2
	根据订单索引,去查询订单是否打印成功,订单索引由方法1返回
*/
function queryOrderState($index){
    $msgInfo = array(
        'user'=>USER,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_queryOrderState',

        'orderid'=>$index
    );

    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }

}




/*
 *  方法3
	查询指定打印机某天的订单详情
*/
function queryOrderInfoByDate($printer_sn,$date){
    $msgInfo = array(
        'user'=>USER,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_queryOrderInfoByDate',

        'sn'=>$printer_sn,
        'date'=>$date
    );

    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }

}



/*
 *  方法4
	查询打印机的状态
*/
function queryPrinterStatus($printer_sn){

    $msgInfo = array(
        'user'=>USER,
        'stime'=>STIME,
        'sig'=>SIG,
        'apiname'=>'Open_queryPrinterStatus',

        'sn'=>$printer_sn
    );

    $client = new HttpClient(IP,PORT);
    if(!$client->post(PATH,$msgInfo)){
        echo 'error';
    }
    else{
        $result = $client->getContent();
        echo $result;
    }
}