<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$action = 'start';
$storeid=$_COOKIE["storeid"];
// if($_GPC['id']){
// setcookie("storeid",$_GPC['id']);
// $cur_store = $this->getStoreById($_GPC['id']);	
// $storeid=$_GPC['id'];

// }else{
// $storeid=$_COOKIE["storeid"];
// $cur_store = $this->getStoreById($storeid);	
// }
$cur_store = $this->getStoreById($storeid);
$info=pdo_get('cjdc_store',array('id'=>$storeid));
$info2=pdo_get('cjdc_storeset',array('store_id'=>$storeid));

		//print_r($img);die;
		if($info['environment']){
			if(strlen($info['environment'])>51){
			$environment= explode(',',$info['environment']);
		}else{
			$environment=array(
				0=>$info['environment']
				);
		}
		}
		if($info['yyzz']){
		if(strlen($info['yyzz'])>51){
			$yyzz= explode(',',$info['yyzz']);
		}else{
			$yyzz=array(
				0=>$info['yyzz']
				);
		}	
		}
if(checksubmit('submit')){
	if(!$_GPC['address']){
		message('商家地址不能为空','','error');
	}
	if(!$_GPC['coordinates']){
		message('商家坐标不能为空','','error');
	}
			
		if($_GPC['yyzz']){
			$data['yyzz']=implode(",",$_GPC['yyzz']);
		}else{
			$data['yyzz']='';
		}
		if($_GPC['environment']){
			$data['environment']=implode(",",$_GPC['environment']);
		}else{
			$data['environment']='';
		}
			$data['name']=$_GPC['name'];
			$data['address']=$_GPC['address'];
			$data['tel']=$_GPC['tel'];
			$data['announcement']=$_GPC['announcement'];
			$data['start_at']=$_GPC['start_at'];
			$data['capita']=$_GPC['capita'];
			 if($info['logo']!=$_GPC['logo']){
            $data['logo']=$_W['attachurl'].$_GPC['logo'];
        	}
        	 if($info['qrcode']!=$_GPC['qrcode']){
            $data['qrcode']=$_W['attachurl'].$_GPC['qrcode'];
        	}
			//$data['logo']=$_GPC['logo'];
			// if($_GPC['color']){
			// 	$data['color']=$_GPC['color'];
			// }else{
			// 	$data['color']="#34AAFF";
			// }
			$data['uniacid']=$_W['uniacid'];
			
			$data['coordinates']=$_GPC['coordinates'];

			$data['details']=html_entity_decode($_GPC['details']);


			$data2['xyh_money']=$_GPC['xyh_money'];
			$data2['xyh_open']=$_GPC['xyh_open'];
			$data2['top_style']=$_GPC['top_style'];
			$data2['info_style']=$_GPC['info_style'];
			$data2['is_dcyhq']=$_GPC['is_dcyhq'];
			$res = pdo_update('cjdc_store', $data, array('id' => $storeid));
			 $res2 =pdo_update('cjdc_storeset', $data2, array('store_id' => $storeid));
			if($res || $res2){
				message('编辑成功',$this->createWebUrl('storeinfo',array()),'success');
			}else{
				message('编辑失败','','error');
			}
		}


function  getCoade($storeid){
		function getaccess_token(){
			global $_W, $_GPC;
         $res=pdo_get('cjdc_system',array('uniacid' => $_W['uniacid']));
         $appid=$res['appid'];
         $secret=$res['appsecret'];
         
       $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
       $data = curl_exec($ch);
       curl_close($ch);
       $data = json_decode($data,true);
       return $data['access_token'];
}
     function set_msg($storeid){
       $access_token = getaccess_token();
        $data2=array(
				"scene"=>$storeid,
				"page"=>"zh_dianc/pages/info/info",
				"width"=>400
               );
 		$data2 = json_encode($data2);
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data2);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
         }
        $img=set_msg($storeid);
        $img=base64_encode($img);
  return $img;
	}

	$img=getCoade($storeid);

		function  getCoade2($storeid){
			function getaccess_token2(){
				global $_W, $_GPC;
				$res=pdo_get('cjdc_system',array('uniacid' => $_W['uniacid']));
				$appid=$res['appid'];
				$secret=$res['appsecret'];

				$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
				$data = curl_exec($ch);
				curl_close($ch);
				$data = json_decode($data,true);
				return $data['access_token'];
			}
			function set_msg2($storeid){
				$access_token = getaccess_token2();
				$data2=array(
					"scene"=>$storeid,
				"page"=>"zh_cjdianc/pages/seller/fukuan",
					"width"=>100
					);
				$data2 = json_encode($data2);
				$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token."";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$data2);
				$data = curl_exec($ch);
				curl_close($ch);
				return $data;
			}
			$img=set_msg2($storeid);
			$img=base64_encode($img);
			return $img;
		}

		$img2=getCoade2($storeid);
		//print_r($img);die;
include $this->template('web/storeinfo');