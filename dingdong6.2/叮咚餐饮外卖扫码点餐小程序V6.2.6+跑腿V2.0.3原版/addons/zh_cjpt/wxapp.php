<?php
/**
 * zh_cjpt模块小程序接口定义
 *
 * @author 武汉志汇科技
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Zh_cjptModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
	//echo '123';die;
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
		
	}


	//轮播图
	public function doPageAd(){
		global $_W, $_GPC;	
		$res=pdo_getall('cjpt_ad',array('uniacid'=>$_W['uniacid'],'status'=>1),array(),'','orderby asc');
		echo  json_encode($res);
	}

	public function doPageAddOrder(){
		global $_GPC, $_W;
		include IA_ROOT.'/addons/zh_cjpt/class/peisong.php';	
		$info=pdo_get('cjpt_dispatch',array('order_id'=>$_POST['order_id'],'uniacid'=>$_W['uniacid']));
		$bind=pdo_get('cjpt_bind',array('pt_uniacid'=>$_W['uniacid']));
		if($bind['cy_uniacid']==$_POST['uniacid']){
		if(!$info){
		$data['order_id']=$_POST['order_id'];
		$data['goods_info']=$_POST['goods_info'];
		$data['goods_price']=$_POST['goods_price'];
		$data['sender_name']=$_POST['sender_name'];
		$data['sender_address']=$_POST['sender_address'];
		$data['sender_tel']=$_POST['sender_tel'];
		$data['sender_lat']=$_POST['sender_lat'];
		$data['sender_lng']=$_POST['sender_lng'];
		$data['receiver_name']=$_POST['receiver_name'];
		$data['receiver_address']=$_POST['receiver_address'];
		$data['receiver_tel']=$_POST['receiver_tel'];
		$data['receiver_lat']=$_POST['receiver_lat'];
		$data['receiver_lng']=$_POST['receiver_lng'];
		$data['store_logo']=$_POST['store_logo'];
		$data['note']=$_POST['note'];
		$data['yh_money']=$_POST['yh_money'];
		$data['pay_type']=$_POST['pay_type'];
		$data['origin_id']=$_POST['origin_id'];
		$data['delivery_time']=$_POST['delivery_time'];
		$data['oid']=$_POST['oid'];
		$ps_num='pt'.time().rand(10,19);
		$data['ps_num']=$ps_num;
		$psmoney=peisong::getMoney($_POST['sender_lat'],$_POST['sender_lng'],$_POST['receiver_lat'],$_POST['receiver_lng'],$_W['uniacid']);
		if($psmoney){
		$data['ps_money']=$psmoney;
		$data['state']=1;
		$data['time']=time();
		$data['uniacid']=$_W['uniacid'];
		$res=pdo_insert('cjpt_dispatch',$data);
		$rst=array('ps_num'=>$ps_num,'ps_money'=>$psmoney);
		echo json_encode(array('msg'=>'下单成功,','code'=>'200','rst'=>$rst),320);
		file_get_contents("".$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&a=wxapp&do=SendMail&m=zh_cjpt");exit();//发邮件
		}else{
			echo json_encode(array('msg'=>'下单失败,超出配送距离','code'=>'500'),320);exit();
		}

	}else{
		echo json_encode(array('msg'=>'下单失败,该订单已存在','code'=>'500'),320);exit();
	}

}else{

	echo json_encode(array('msg'=>'下单失败,跑腿信息不匹配','code'=>'500'),320);exit();
}

	}


	//获取openid
	public function doPageOpenid(){
		global $_W, $_GPC;
		$res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
		$code=$_GPC['code'];
		$appid=$res['appid'];
		$secret=$res['appsecret'];
		$url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";
		function httpRequest($url,$data = null){ 
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			if (!empty($data)){
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           //执行
			$output = curl_exec($curl);
			curl_close($curl);
			return $output;
		}
		$res=httpRequest($url);
		print_r($res);

	}

	//图片路径(七牛)
	public function doPageAttachurl(){
		global $_W;		
		echo $_W['attachurl'];   

	}

	//注册短信验证码
	public function doPageSms2(){
		global $_W, $_GPC;
		$res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
		if($res['item']==2){
			if($_GPC['type']==1){
			$tpl_id=$res['aliyun_id'];
		}else{
			$tpl_id=$res['aliyun_id2'];
		}		
			 var_dump($this->doPageAliyun($_GPC['tel'],$tpl_id,$_GPC['code'],$tpl_id)) ;
		}else{
		if($_GPC['type']==1){
			$tpl_id=$res['tpl_id'];
		}
		if($_GPC['type']==2){
			$tpl_id=$res['tpl_id2'];
		}				
		$tel=$_GPC['tel'];
		$code=urlencode($_GPC['code']);
		$key=$res['appkey'];
		$url = "http://v.juhe.cn/sms/send?mobile=".$tel."&tpl_id=".$tpl_id."&tpl_value=%23code%23%3D".$code."&key=".$key;	
		$data=file_get_contents($url);
		print_r($data);
	}
	}

//骑手短信
	public function doPageSmstest(){
		global $_W, $_GPC;
		$res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
		$tpl_id=$res['tpl_id4'];			
		$tel='15172415261';
		//$code=urlencode($_GPC['code']);
		$key=$res['appkey'];
		$code=urlencode("#order_num#=123456789&#ps_num#=ps15172415261&#address#=阜华大厦D座2802");
		$url = "http://v.juhe.cn/sms/send?mobile=".$tel."&tpl_id=".$tpl_id."&tpl_value=".$code."&key=".$key;	
		$data=file_get_contents($url);
		print_r($data);
	}

	//上传图片
	public function doPageUpload(){
		global $_W, $_GPC;
		$uptypes=array(  
			'image/jpg',  
			'image/jpeg',  
			'image/png',  
			'image/pjpeg',  
			'image/gif',  
			'image/bmp',  
			'image/x-png'  
			);  
    $max_file_size=2000000;     //上传文件大小限制, 单位BYTE  
    $destination_folder="../attachment/zh_cjpt/".date(Y)."/".date(m)."/".date(d)."/"; //上传文件路径  
    if (!is_uploaded_file($_FILES["upfile"]['tmp_name']))  
    //是否存在文件  
    {  
    	echo "图片不存在!";  
    	exit;  
    }    
    $file = $_FILES["upfile"];  
    if($max_file_size < $file["size"])  
    //检查文件大小  
    {  
    	echo "文件太大!";  
    	exit;  
    }    
    // if(!in_array($file["type"], $uptypes))  
    // //检查文件类型  
    // {  
    // 	echo "文件类型不符!".$file["type"];  
    // 	exit;  
    // }
    if (!file_exists($destination_folder)){
    	mkdir ($destination_folder,0777,true);
    }
    $filename=$file["tmp_name"];
    $image_size = getimagesize($filename);
    $pinfo=pathinfo($file["name"]);  
    $ftype=$pinfo['extension'];  
    $destination = $destination_folder.str_shuffle(time().rand(111111,999999)).".".$ftype;  
    if (file_exists($destination) && $overwrite != true)  
    {  
    	echo "同名文件已经存在了";  
    	exit;  
    }  
    if(!move_uploaded_file ($filename, $destination))  
    {  
   	echo "移动文件出错";  
   	exit;  
    }  
    $pinfo=pathinfo($destination);  
     $fname="zh_cjpt/".date(Y)."/".date(m)."/".date(d)."/".$pinfo['basename'];
    echo $fname;
    @require_once (IA_ROOT . '/framework/function/file.func.php');
    @$filename=$fname;
    @file_remote_upload($filename); 

}

//解密
public function doPageJiemi(){
	global $_W, $_GPC;
	$res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
	include_once  IA_ROOT . "/addons/zh_cjpt/jiemi/wxBizDataCrypt.php";
	$appid = $res['appid'];
	$sessionKey = $_GPC['sessionKey'];
	$encryptedData=$_GPC['data'];
	$iv = $_GPC['iv'];
	$pc = new WXBizDataCrypt($appid, $sessionKey);
	$errCode = $pc->decryptData($encryptedData, $iv, $data );
	if ($errCode == 0) {
       //echo json_encode($data);
		print($data . "\n");
	} else {
		print($errCode . "\n");
	}
}

//系统设置
	public function doPageGetSystem(){
		global $_W, $_GPC;
		$rst=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
		echo json_encode($rst);
	}

	//骑手入驻
	public function doPageSaveRider(){
		global $_W, $_GPC;
		$rst=pdo_get('cjpt_rider',array('tel'=>$_GPC['tel'],'uniacid'=>$_W['uniacid']));
		if(!$rst){
			$data['openid']=$_GPC['openid'];
			$data['name']=$_GPC['name'];
			$data['tel']=$_GPC['tel'];
			$data['logo']=$_GPC['logo'];
			$data['pwd']=md5($_GPC['pwd']);
			$data['zm_img']=$_GPC['zm_img'];
			$data['fm_img']=$_GPC['fm_img'];
			$data['email']=$_GPC['email'];
			$data['state']=1;
			$data['status']=1;
			$data['time']=time();
			$data['uniacid']=$_W['uniacid'];
			if($_GPC['id']){
				$res=pdo_insert('cjpt_rider',$data);
				$qs_id=pdo_insertid();
			}else{
				$res=pdo_insert('cjpt_rider',$data,array('id'=>$_GPC['id']));
			}		
			if($res){
				echo $qs_id;
			}else{
				echo '入驻失败';
			}
		}else{
			echo '该手机号已注册';
		}

	}


//订单列表
	public function doPageJdList(){
		global $_GPC, $_W;
		$pageindex = max(1, intval($_GPC['page']));
		$pagesize=10;
		$sys=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']),'distance');
		$juli=$sys['distance']*1000;
		$lat=$_GPC['lat'];
		$lng=$_GPC['lng'];
		$state=empty($_GPC['state'])?1:$_GPC['state'];
		$data[':uniacid']=$_W['uniacid'];
		$where=" where xx.uniacid=:uniacid  ";
		if($state){
			$where.=" and xx.state=:state ";  
			$data[':state']=$state;
		}
		if($_GPC['qs_id']){
			$where.=" and xx.qs_id=:qs_id ";  
			$data[':qs_id']=$_GPC['qs_id'];
		}
		if($lat){
			$sql=" select xx.* from(SELECT *, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*3.1415926/180-sender_lat*3.1415926/180)/2),2)+COS($lat*3.1415926/180)*COS(sender_lat*3.1415926/180)*POW(SIN(($lng*3.1415926/180-sender_lng*3.1415926/180)/2),2)))*1000) AS juli  
			FROM ".tablename("cjpt_dispatch").") as xx  ".$where."and  xx.juli<={$juli}  ORDER BY xx.juli ASC  ";
		}else{
			$sql="select *  from " . tablename("cjpt_dispatch") ." as xx ".$where." order by id desc ";
		}
		$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
      //echo $select_sql;die;
		$list=pdo_fetchall($select_sql,$data);
		echo json_encode($list);
	}


//订单详情
public function doPageOrderInfo(){
	global $_GPC, $_W;
	$orderInfo=pdo_get('cjpt_dispatch',array('id'=>$_GPC['order_id']));
	echo json_encode($orderInfo);
}

	//登入
	public function doPageLogin(){
		global $_GPC, $_W;
		$rst=pdo_get('cjpt_rider',array('tel'=>$_GPC['tel'],'uniacid'=>$_W['uniacid']));
		if(!$rst){
			echo '该账号不存在';die;
		}
		if($rst['state']!=2){
			echo '账号异常,请联系管理员';die;
		}
		$sql=" select * from ".tablename('cjpt_rider')."where tel=:tel and pwd=:pwd and state=2";
		$data[':tel']=$_GPC['tel'];
		$data[':pwd']=md5($_GPC['pwd']);
		$res=pdo_fetch($sql,$data);
		if($res['openid']=='undefined' && $_GPC['openid']){
			pdo_update('cjpt_rider',array('openid'=>$_GPC['openid']),array('id'=>$res['id']));
		}
		echo json_encode($res);

	}


	//忘记密码
	public function doPageUpdPwd(){
		global $_GPC, $_W;
		$rst=pdo_get('cjpt_rider',array('tel'=>$_GPC['tel'],'uniacid'=>$_W['uniacid']));
		if($rst['state']==2){
			$res=pdo_update('cjpt_rider',array('pwd'=>md5($_GPC['pwd'])),array('id'=>$rst['id']));
			if($res){
				echo '1';
			}
		}else{
			echo '账号异常,请联系管理员';die;
		}
	}

	//抢单
	public function doPageRobbing(){
		global $_GPC, $_W;
		$info=pdo_get('cjpt_dispatch',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		if($info['state']==1){
			$rst=pdo_update('cjpt_dispatch',array('state'=>2,'qs_id'=>$_GPC['qs_id'],'jd_time'=>time()),array('id'=>$_GPC['id']));
			if($rst){
				$qs=pdo_get('cjpt_rider',array('id'=>$_GPC['qs_id']));
				$res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
				if($res['item']==2){
					$tpl_id=$res['aliyun_id3'];			
				$tel=$info['receiver_tel'];	
			 $data=$this->doPageAliyun($tel,$tpl_id,'',$qs['tel'],$qs['name']);
		}else{
				$tpl_id=$res['tpl_id3'];			
				$tel=$info['receiver_tel'];
				$key=$res['appkey'];
				$code=urlencode("#name#=".$qs['name']."&#tel#=".$qs['tel']);
				$url = "http://v.juhe.cn/sms/send?mobile=".$tel."&tpl_id=".$tpl_id."&tpl_value=".$code."&key=".$key;	
				$data=file_get_contents($url);
			}
				//更新餐饮订单信息
				$data2['qs_name']=$qs['name'];
				$data2['qs_tel']=$qs['tel'];
				$str=json_encode($data2,JSON_UNESCAPED_UNICODE);
				pdo_update('cjdc_order',array('pt_info'=>$str),array('order_num'=>$info['order_id']));
				echo '1';
			}else{
				echo '抢单失败';
			}
		}else{
			echo '抢单失败';
		}
	}

//到店
	public function doPageArrival(){
		global $_GPC, $_W;
        $info=pdo_get('cjpt_dispatch',array('id'=>$_GPC['order_id'],'uniacid'=>$_W['uniacid']));
        if($info['state']==2) {
            $rst = pdo_update('cjpt_dispatch', array('dd_time' => time(), 'state' => 3), array('id' => $_GPC['order_id']));
            if ($rst) {
                echo '1';
            } else {
                echo '到店失败';
            }
        }else{
            echo '到店失败';
        }
	}
//完成
	public function doPageComplete(){
		global $_GPC, $_W;
			$rst=pdo_update('cjpt_dispatch',array('wc_time'=>time(),'state'=>4),array('id'=>$_GPC['order_id']));
		if($rst){
			//订单完成
			$orderInfo=pdo_get('cjpt_dispatch',array('id'=>$_GPC['order_id']));
			pdo_update('cjdc_order',array('state'=>4,'complete_time'=>date('Y-m-d H:i:s',time())),array('order_num'=>$orderInfo['order_id']));
			echo '1';
		}else{
			echo '完成失败';
		}
	}

//资金管理
//

//可提现金额
public function doPageKtxMoney(){
	global $_GPC, $_W;
	$yx=pdo_get('cjpt_dispatch', array('uniacid'=>$_W['uniacid'],'qs_id '=>$_GPC['qs_id'],'state'=>4), array('sum(ps_money) as total_money'));
	$sh=pdo_get('cjpt_withdrawal', array('uniacid'=>$_W['uniacid'],'qs_id '=>$_GPC['qs_id'],'state'=>array(1,2)), array('sum(tx_cost) as total_money'));
	echo json_encode($yx['total_money']-$sh['total_money']);
}

  //佣金提现
public function doPageSavetx() {
	global $_W, $_GPC;
	$data['tx_num']='tx'.time().rand(10,19);
	$data['qs_id'] = $_GPC['qs_id'];
    $data['name'] = $_GPC['name']; //姓名
    $data['username'] = $_GPC['user_name']; //账号
    $data['tx_cost'] = $_GPC['tx_cost']; //提现金额
    $data['sj_cost'] = $_GPC['sj_cost']; //实际到账金额
    $data['state'] = 1;
    $data['time'] = time();
    $data['uniacid'] = $_W['uniacid'];
    $res = pdo_insert('cjpt_withdrawal', $data);
    if ($res) {
        echo '1';
    } else {
        echo '2';
        }
    }

 //提现记录
 public function doPageTxList(){
 	global $_W, $_GPC;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
	$time=date('Y-m',time());
	$sql=" select * from".tablename('cjpt_withdrawal')." where qs_id={$_GPC['qs_id']} and uniacid={$_W['uniacid']}";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql,$data);
	echo json_encode($list);
 }

//本月流水
public function doPageGetMonth(){
	global $_GPC, $_W;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
	$time=date('Y-m',time());
	$sql=" select ps_money,ps_num,wc_time from".tablename('cjpt_dispatch')." where qs_id={$_GPC['qs_id']} and state=4 and FROM_UNIXTIME(wc_time,'%Y-%m') like '%{$time}%'";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql,$data);
	echo json_encode($list);
}

//今日统计
public function doPageToday(){
	global $_GPC, $_W;
	$time=date('Y-m-d',time());
	$sql="select count(id) as count, sum(ps_money) as money from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and state=4 and FROM_UNIXTIME(wc_time,'%Y-%m-%d') like '%{$time}%'";
	$rst=pdo_fetch($sql);
	$sql2="select count(id) as count, sum(ps_money) as money from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and (state=2 or state=3) and FROM_UNIXTIME(wc_time,'%Y-%m-%d') like '%{$time}%'";
	$rst2=pdo_fetch($sql2);
	$data['wc']=$rst;
	$data['wwc']=$rst2;
	echo json_encode($data);
}


//历史账单
public function doPageHistory(){
	global $_GPC, $_W;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
	$sql=" SELECT FROM_UNIXTIME(jd_time,'%Y-%m') days, qs_id,count(id) as count,sum(ps_money) as money FROM ".tablename('cjpt_dispatch')."  where qs_id={$_GPC['qs_id']} GROUP BY days order by days desc";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql);
	echo json_encode($list);
}

//月明细
public function doPageMonthList(){
	global $_GPC, $_W;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
	$days=$_GPC['days'];
	$where=" where xx.days like '%{$days}%' and xx.state>1ss ";
	$sql=" select xx.* from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m-%d') days,count(id) as count,sum(ps_money) as money FROM ".tablename('cjpt_dispatch')." where uniacid={$_W['uniacid']} and qs_id={$_GPC['qs_id']}  GROUP BY days ) xx".$where." order by xx.days desc";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql);
	echo json_encode($list);
}

//当日明细
public function doPageTodayList(){
	global $_GPC, $_W;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=10;
	$days=$_GPC['days'];
	$where=" where xx.uniacid={$_W['uniacid']} and xx.days like '%{$days}%' and xx.qs_id={$_GPC['qs_id']} and xx.state>1";
	$sql=" select xx.* from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m-%d') days,id, qs_id,uniacid,state,ps_money,ps_num,jd_time,origin_id FROM ".tablename('cjpt_dispatch').") xx ".$where." order by xx.id desc";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql);
	echo json_encode($list);
	
}

//搜索账单
public  function doPageSearchList(){

	global $_GPC, $_W;
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize=empty($_GPC['pagesize'])?10:$_GPC['pagesize'];
	$start=$_GPC['start_time'];
	$end=$_GPC['end_time'];
	$data[':uniacid']=$_W['uniacid'];
	$data[':qs_id']=$_GPC['qs_id'];

	$where=" where xx.uniacid=:uniacid  and xx.qs_id=:qs_id and xx.state>1";
	if($_GPC['start_time']&&$_GPC['end_time']){
		$where.=" and xx.days >='{$start}' and xx.days <='{$end}'";
	}
	if($_GPC['keywords']){
		$where.=" and (xx.ps_num LIKE  concat('%', :name,'%'))";
		$data[':name']=$_GPC['keywords'];  
	}
	$sql=" select xx.* from (SELECT FROM_UNIXTIME(jd_time,'%Y-%m') days,id, qs_id,uniacid,state,ps_money,ps_num,jd_time,origin_id FROM ".tablename('cjpt_dispatch').") xx ".$where." order by xx.id desc";
	$select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
	$list=pdo_fetchall($select_sql,$data);
	echo json_encode($list);
}

//订单统计
public function doPageOrderStatistics(){
	global $_GPC, $_W;
	$today_start=mktime(0,0,0,date('m'),date('d'),date('Y'));
	$today_end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	$thisweek_start=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
	$thisweek_end=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
	$thismonth_start=mktime(0,0,0,date('m'),1,date('Y'));
	$thismonth_end=mktime(23,59,59,date('m'),date('t'),date('Y'));
	$sql="select count(id) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and uniacid={$_W['uniacid']} and jd_time>{$today_start} and jd_time<=$today_end and state>1 ";
	$jr=pdo_fetch($sql);
	$sql2="select count(id) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and uniacid={$_W['uniacid']} and jd_time>{$thisweek_start} and jd_time<=$thisweek_end and state>1";
	$bz=pdo_fetch($sql2);
	$sql3="select count(id) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and uniacid={$_W['uniacid']} and jd_time>{$thismonth_start} and jd_time<=$thismonth_end and state>1";
	$by=pdo_fetch($sql3);
	$sql4="select count(id) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and uniacid={$_W['uniacid']} and state>1 ";
	$lj=pdo_fetch($sql4);
	$data['jr']=$jr['count'];
	$data['bz']=$bz['count'];
	$data['by']=$by['count'];
	$data['lj']=$lj['count'];
	echo json_encode($data);
}

//收入统计
public function doPageMoneyStatistics(){
	global $_GPC, $_W;
	$today_start=mktime(0,0,0,date('m'),date('d'),date('Y'));
	$today_end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	$thisweek_start=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
	$thisweek_end=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
	$thismonth_start=mktime(0,0,0,date('m'),1,date('Y'));
	$thismonth_end=mktime(23,59,59,date('m'),date('t'),date('Y'));
	$sql="select sum(ps_money) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and state=4 and uniacid={$_W['uniacid']} and jd_time>{$today_start} and jd_time<=$today_end ";
	$jr=pdo_fetch($sql);
	$sql2="select sum(ps_money) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and state=4 and uniacid={$_W['uniacid']} and jd_time>{$thisweek_start} and jd_time<=$thisweek_end ";
	$bz=pdo_fetch($sql2);
	$sql3="select sum(ps_money) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and state=4 and uniacid={$_W['uniacid']} and jd_time>{$thismonth_start} and jd_time<=$thismonth_end ";
	$by=pdo_fetch($sql3);
	$sql4="select sum(ps_money) as count from".tablename('cjpt_dispatch')."where qs_id={$_GPC['qs_id']} and state=4 and uniacid={$_W['uniacid']} ";
	$lj=pdo_fetch($sql4);
	$data['jr']=empty($jr['count'])?0:$jr['count'];
	$data['bz']=empty($bz['count'])?0:$bz['count'];
	$data['by']=empty($by['count'])?0:$by['count'];
	$data['lj']=empty($lj['count'])?0:$lj['count'];
	echo json_encode($data);
}


//工作设置
public function doPageWork(){
	global $_GPC, $_W;
	$res=pdo_update('cjpt_rider',array('status'=>$_GPC['status']),array('id'=>$_GPC['qs_id']));
	if($res){
		echo '1';
	}else{
		echo '2';
	}
}

public function doPageqxOrder(){
	global $_GPC, $_W;
	$bind=pdo_get('cjpt_bind',array('pt_uniacid'=>$_W['uniacid']));
	if($bind['cy_uniacid']==$_POST['uniacid']){
		$info=pdo_get('cjpt_dispatch',array('order_id'=>$_POST['order_id'],'uniacid'=>$_W['uniacid']));
		if($info['state']==1){
			$res=pdo_update('cjpt_dispatch',array('state'=>5,'item'=>1),array('order_id'=>$_POST['order_id']));
		}else{
			$res=pdo_update('cjpt_dispatch',array('state'=>5,'item'=>2),array('order_id'=>$_POST['order_id']));//异常订单
		}
		if($res){
			echo json_encode(array('msg'=>'取消成功,','code'=>'200','rst'=>$rst),320);exit();
		}else{
			echo json_encode(array('msg'=>'取消失败','code'=>'500'),320);exit();
		}		
	}else{
		echo json_encode(array('msg'=>'取消失败,跑腿信息不匹配','code'=>'500'),320);exit();

	}
}


//查看订单详情
public function doPageGetOrderInfo(){
	global $_W, $_GPC;
	$bind=pdo_get('cjpt_bind',array('pt_uniacid'=>$_W['uniacid']));
	if($bind['cy_uniacid']==$_POST['uniacid']){
	$sql=" select a.*,b.name,b.tel from".tablename('cjpt_dispatch')." a left join ".tablename('cjpt_rider')."b on a.qs_id=b.id where a.order_id={$_POST['order_num']} and a.uniacid={$_W['uniacid']}";
	$info=pdo_fetch($sql);
	if($info){
		echo json_encode(array('msg'=>'获取信息成功,','code'=>'200','rst'=>$info),320);exit();

	}else{
		echo json_encode(array('msg'=>'订单不存在,核实后在重试','code'=>'500'),320);exit();
	}
	
	}else{
		echo json_encode(array('msg'=>'查询失败,跑腿信息不匹配','code'=>'500'),320);exit();

	}

}


//模板消息
 public function doPageMessage(){
       global $_W, $_GPC;
       function getaccess_token($_W){
         $res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
         $appid=$res['appid'];
         $secret=$res['appsecret'];
         $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL,$url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
         $data = curl_exec($ch);
         curl_close($ch);
         $data = json_decode($data,true);
         return $data['access_token'];
       }
      //设置与发送模板信息
       function set_msg($_W){
         $access_token = getaccess_token($_W);
         $res=pdo_get('cjpt_message',array('uniacid'=>$_W['uniacid']));
         $res2=pdo_get('cjpt_dispatch',array('id'=>$_GET['order_id']));
         $res3=pdo_get('cjpt_rider',array('id'=>$res2['qs_id']));
         $time=date('Y-m-d H:i:s',$res2['jd_time']);
         $formwork ='{
           "touser": "'.$res2["openid"].'",
           "template_id": "'.$res["jd_tid"].'",
           "page": "zh_cjdianc/pages/Liar/loginindex",
           "form_id":"'.$_GET['form_id'].'",
           "data": {
             "keyword1": {
               "value": "'.$time.'",
               "color": "#173177"
             },
             "keyword2": {
               "value":"'.$res3['name'].'",
               "color": "#173177"
             },
             "keyword3": {
               "value": "'.$res3['tel'].'",
               "color": "#173177"
             },
             "keyword4": {
               "value":  "'.$res2['order_id'].'",
               "color": "#173177"
             },
             "keyword5": {
               "value": "'.$res2['goods_price'].'",
               "color": "#173177"
             },
              "keyword6": {
               "value": "'.$res2['sender_name'].'",
               "color": "#173177"
             }
           }
         }';
             // $formwork=$data;
         $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token."";
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL,$url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
         curl_setopt($ch, CURLOPT_POST,1);
         curl_setopt($ch, CURLOPT_POSTFIELDS,$formwork);
         $data = curl_exec($ch);
         curl_close($ch);
         return $data;
       }
       echo set_msg($_W);
     }


/////////////////////////
///
//发送邮件
     public function doPageSendMail(){
     	global $_W, $_GPC;
     	$config=pdo_get('cjpt_mail',array('uniacid'=>$_W['uniacid']));
     	if($config['is_email']==1){
     		$user_email=pdo_getall('cjpt_rider',array('uniacid'=>$_W['uniacid'],'state'=>2,'status'=>1),'email');
     		
     			$subject='订单提醒';
     			$body='有新的订单啦,赶快去抢单吧！<br><br><br><br>';
     			static $mailer;
     			set_time_limit(0);
     			if (empty($mailer)) {
     				if (!class_exists('PHPMailer')) {
     					load()->library('phpmailer');

     				}
     				$mailer = new PHPMailer();   
     				$config=Array ( 'username' => $config['username'], 'password' => $config['password'] ,'smtp' => Array ( 'type' => $config['type'] ,'server' =>'', 'port' =>'', 'authmode' => 0 ), 'sender' => $config['sender'], 'signature' => $config['signature'] );
     				$config['charset'] = 'utf-8';
     				if ($config['smtp']['type'] == '163') {
     					$config['smtp']['server'] = 'smtp.163.com';
     					$config['smtp']['port'] = 25;
     				} elseif ($config['smtp']['type'] == 'qq') {
     					$config['smtp']['server'] = 'ssl://smtp.qq.com';
     					$config['smtp']['port'] = 465;

     				} else {
     					if (!empty($config['smtp']['authmode'])) {
     						$config['smtp']['server'] = 'ssl://' . $config['smtp']['server'];
     					}
     				}
     				if (!empty($config['smtp']['authmode'])) {
     					if (!extension_loaded('openssl')) {
     						return error(1, '请开启 php_openssl 扩展！');
     					}
     				}
     				$mailer->signature = $config['signature'];
     				$mailer->isSMTP();    
     				$mailer->CharSet = $config['charset'];
     				$mailer->Host = $config['smtp']['server'];
     				$mailer->Port = $config['smtp']['port'];
     				$mailer->SMTPAuth = true;
     				$mailer->Username = $config['username'];
     				$mailer->Password = $config['password'];
     				!empty($config['smtp']['authmode']) && $mailer->SMTPSecure = 'ssl';
     				$mailer->From = $config['username'];
     				$mailer->FromName = $config['sender'];
     				$mailer->isHTML(true);
     			}
     			if ($body) {
     				if (is_array($body)) {
     					$body = '';
     					foreach($body as $value) {
     						if (substr($value, 0, 1) == '@') {
     							if(!is_file($file = ltrim($value, '@'))){
     								return error(1, $file . ' 附件不存在或非文件！');
     							}
     							$mailer->addAttachment($file);  
     						} else {
     							$body .= $value . '\n';
     						}
     					}
     				} else {
     					if (substr($body, 0, 1) == '@') {
     						$mailer->addAttachment(ltrim($body, '@'));  
     						$body = '';
     					}
     				}
     			}
     			if (!empty($mailer->signature)) {
     				$body .= htmlspecialchars_decode($mailer->signature);
     			}
     			$mailer->Subject = $subject;
     			$mailer->Body = $body;
     			foreach ($user_email as $key => $value) {
     			$to=$value['email'];
     			$mailer->addAddress($to);
     			}
     			if ($mailer->send()) {

     				echo'1';
     			} else {   
     				echo'2';
     			}
     		
     	}
     }



//帮助中心
public function doPageGetHelp(){
    global $_W, $_GPC;
    $res= pdo_getall('cjpt_help',array('uniacid'=>$_W['uniacid']),array() , '' , 'sort ASC');
    echo json_encode($res);
}


	//登录用户信息
public function doPageWxLogin(){
	global $_GPC, $_W;
	$openid=$_GPC['openid'];
	$user=pdo_get('cjpt_rider',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
	echo json_encode($user);

}


//提现详情
public function doPageGetTxDetails(){
	global $_GPC, $_W;
	$res=pdo_get('cjpt_withdrawal',array('id'=>$_GPC['tx_id']));
	echo json_encode($res);
}

public function doPageUrl(){
	global $_GPC, $_W;
	echo $_W['attachurl'];
}

//公告中心
public function doPageGetNotice(){
    global $_W, $_GPC;
    $res= pdo_getall('cjpt_notice',array('uniacid'=>$_W['uniacid']),array() , '' , 'sort ASC');
    echo json_encode($res);
}

//公告详情
public function doPageNoticeDetails(){
	 global $_W, $_GPC;
    $res= pdo_get('cjpt_notice',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
    echo json_encode($res);
}
//我的异常订单
public function doPageMyabnormal(){
    global $_GPC, $_W;
    $pageindex = max(1, intval($_GPC['page']));
    $pagesize=empty($_GPC['pagesize'])?10:$_GPC['pagesize'];
    $where=" where qs_id=:qs_id and state=5 and item=2 and uniacid=:uniacid";
    $data[':uniacid']=$_W['uniacid'];
    $data[':qs_id']=$_GPC['qs_id'];
    $sql="select *  from " . tablename("cjpt_dispatch") .$where." order by id desc ";
    $select_sql=$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
    $list=pdo_fetchall($select_sql,$data);
    echo json_encode($list);
}

//转单
public function doPageTransfer(){
    global $_W, $_GPC;
    $res= pdo_update('cjpt_dispatch',array('state'=>1,'qs_id'=>0,'jd_time'=>0),array('id'=>$_GPC['id']));
    echo json_encode($res);
}

    //加载函数
    public function sdkAutoLoad($className){

        $classNamePath = str_replace('\\', '/', $className);
       // echo $classNamePath . '<br>';
        //$classNamePath = strtolower($classNamePath);
        $path = __DIR__.'/'.$classNamePath.'.php';
        //echo $path;
        if(file_exists($path)){
            require $path;
            return true;
        }else{
           // echo $path;
            return false;
        }
    }

    //极光推送
    public function doPagejgts($store_id=1){
    	global $_W, $_GPC;
    	$order=pdo_get('cjdc_order',array('id'=>$_GPC['order_id']));
    	//$store_id=$order['store_id'];
    	$store_id=1;
        spl_autoload_register([$this,'sdkAutoLoad'], true, true);//自动加载类
        $client = new JPush\Client($app_key='341a857ce1878236232187c5', $master_secret='35babb2c416589b67402cc25', null, null, 'BJ');
        $platforms=array('ios','android');
        $push_payload = $client->push()
        ->setPlatform($platforms)
        ->addTag([strval($store_id)])
        //->addAllAudience()
        ->setNotificationAlert('您有新的快省到家订单请尽快处理');
        try {
        	$response = $push_payload->send();
        	print_r($response);
        } catch (\JPush\Exceptions\APIConnectionException $e) {
    // try something here
        	print $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
    // try something here
        	print $e;
        }

    }


    public function doPageAliyun($phone,$template,$code='',$qs_tel='',$qs_name='') {
        global $_W, $_GPC;
        require_once dirname(__DIR__) . "/zh_cjpt/SignatureHelper.php";
        $sms=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
      // echo $code;die;
        $params = array ();
        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = $sms['aliyun_appkey'];
        $accessKeySecret = $sms['aliyun_appsecret'];
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] =  $phone;
        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $sms['aliyun_sign'];
        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $template;
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        if($code){
        	$data['code']=$code;
        }
         if($qs_tel){
        	$data['tel']=$qs_tel;
        }
         if($qs_name){
        	$data['name']=$qs_name;
        }
        $params['TemplateParam'] = $data;
       // var_dump($params['PhoneNumbers']);die;

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );

        return $content;
}




}