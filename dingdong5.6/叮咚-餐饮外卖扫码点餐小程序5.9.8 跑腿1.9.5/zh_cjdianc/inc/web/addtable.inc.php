<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
function  getCoade($table_id,$storeid){
		function getaccess_token(){
			global $_W, $_GPC;
         $res=pdo_get('cjdc_system',array('uniacid' => $_W['uniacid']));
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
     function set_msg($table_id,$storeid){
       $access_token = getaccess_token();
        $data2=array(
				"scene"=>$table_id.",".$storeid,
				"page"=>"zh_cjdianc/pages/smdc/smdcindex",
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
        $img=set_msg($table_id,$storeid);
        $img=base64_encode($img);
  return $img;
	}

	if(!$_GPC['id']==''){
		$img=getCoade($_GPC['id'],$storeid);
	}
		$list = pdo_get('cjdc_table',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['id']));
		$type = pdo_getall('cjdc_table_type',array('uniacid' => $_W['uniacid'],'store_id'=>$storeid));
		if(checksubmit('submit')){
			$data['name']=$_GPC['name'];
			$data['num']=$_GPC['num'];
			$data['type_id']=$_GPC['type_id'];
			$data['tag']=$_GPC['tag'];
			
			$data['uniacid']=$_W['uniacid'];
			$data['store_id']=$storeid;
			$data['orderby']=$_GPC['orderby'];		
			if($_GPC['id']==''){
			$data['status']=0;		
				$res=pdo_insert('cjdc_table',$data);
				if($res){
					message('添加成功',$this->createWebUrl('table',array()),'success');
				}else{
					message('添加失败','','error');
				}
			}else{		
		$res = pdo_update('cjdc_table', $data, array('id' => $_GPC['id']));
				if($res){
					message('编辑成功',$this->createWebUrl('table',array()),'success');
				}else{
					message('编辑失败','','error');
				}
			}
		}
include $this->template('web/addtable');