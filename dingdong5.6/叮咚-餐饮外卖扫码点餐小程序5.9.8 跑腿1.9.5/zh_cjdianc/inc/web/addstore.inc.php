<?php
global $_GPC, $_W;

$GLOBALS['frames'] = $this->getMainMenu();
$area=pdo_getall('cjdc_area',array('uniacid'=>$_W['uniacid']),array(),'','num asc');
$store=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid']),'user_id');
$system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));
function i_array_column($input, $columnKey, $indexKey=null){
	if(!function_exists('array_column')){ 
		$columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
		$indexKeyIsNull            = (is_null($indexKey))?true :false; 
		$indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
		$result                         = array(); 
		foreach((array)$input as $key=>$row){ 
			if($columnKeyIsNumber){ 
				$tmp= array_slice($row, $columnKey, 1); 
				$tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
			}else{ 
				$tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
			} 
			if(!$indexKeyIsNull){ 
				if($indexKeyIsNumber){ 
					$key = array_slice($row, $indexKey, 1); 
					$key = (is_array($key) && !empty($key))?current($key):null; 
					$key = is_null($key)?0:$key; 
				}else{ 
					$key = isset($row[$indexKey])?$row[$indexKey]:0; 
				} 
			} 
			$result[$key] = $tmp; 
		} 
		return $result; 
	}else{
		return array_column($input, $columnKey, $indexKey);
	}
}
$yuser=i_array_column($store, 'user_id');
$user=pdo_getall('cjdc_user',array('uniacid'=>$_W['uniacid'],'id !='=>$yuser,'name !='=>''));






$store2=pdo_getall('cjdc_store',array('uniacid'=>$_W['uniacid']),'admin_id');

function i_array_column2($input, $columnKey, $indexKey=null){
	if(!function_exists('array_column')){ 
		$columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
		$indexKeyIsNull            = (is_null($indexKey))?true :false; 
		$indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
		$result                         = array(); 
		foreach((array)$input as $key=>$row){ 
			if($columnKeyIsNumber){ 
				$tmp= array_slice($row, $columnKey, 1); 
				$tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
			}else{ 
				$tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
			} 
			if(!$indexKeyIsNull){ 
				if($indexKeyIsNumber){ 
					$key = array_slice($row, $indexKey, 1); 
					$key = (is_array($key) && !empty($key))?current($key):null; 
					$key = is_null($key)?0:$key; 
				}else{ 
					$key = isset($row[$indexKey])?$row[$indexKey]:0; 
				} 
			} 
			$result[$key] = $tmp; 
		} 
		return $result; 
	}else{
		return array_column($input, $columnKey, $indexKey);
	}
}
$yuser2=i_array_column2($store2, 'admin_id');
$user2=pdo_getall('cjdc_user',array('uniacid'=>$_W['uniacid'],'id !='=>$yuser2,'name !='=>''));

$type=pdo_getall('cjdc_storetype',array('uniacid'=>$_W['uniacid']),array(),'','num asc');
$info=pdo_get('cjdc_store',array('id'=>$_GPC['id']));
$infoset=pdo_get('cjdc_storeset',array('store_id'=>$info['id']));
$userinfo=pdo_get('cjdc_user',array('id'=>$info['user_id']));
$userinfo2=pdo_get('cjdc_user',array('id'=>$info['admin_id']));
if(checksubmit('submit')){

	$data['is_yy']=$_GPC['is_yy'];
	$data['is_wm']=$_GPC['is_wm'];
	$data['is_dn']=$_GPC['is_dn'];
	$data['is_sy']=$_GPC['is_sy'];
	$data['is_pd']=$_GPC['is_pd'];
	if($infoset['yy_img']!=$_GPC['yy_img'] and $_GPC['yy_img']){
		$data['yy_img']=$_W['attachurl'].$_GPC['yy_img'];
	}else{
		$data['yy_img']=$_GPC['yy_img'];
	}
	if($infoset['wm_img']!=$_GPC['wm_img']  and $_GPC['wm_img']){
		$data['wm_img']=$_W['attachurl'].$_GPC['wm_img'];
	}else{
		$data['wm_img']=$_GPC['wm_img'];
	}
	if($infoset['dn_img']!=$_GPC['dn_img']  and $_GPC['dn_img']){
		$data['dn_img']=$_W['attachurl'].$_GPC['dn_img'];
	}else{
		$data['dn_img']=$_GPC['dn_img']; 
	}
	if($infoset['sy_img']!=$_GPC['sy_img'] and $_GPC['sy_img']){
		$data['sy_img']=$_W['attachurl'].$_GPC['sy_img'];
	}else{
		$data['sy_img']=$_GPC['sy_img'];
	}
	if($infoset['qg_img']!=$_GPC['qg_img'] and $_GPC['qg_img']){
		$data['qg_img']=$_W['attachurl'].$_GPC['qg_img'];
	}else{
		$data['qg_img']=$_GPC['qg_img'];
	}
	if($infoset['pt_img']!=$_GPC['pt_img'] and $_GPC['pt_img']){
		$data['pt_img']=$_W['attachurl'].$_GPC['pt_img'];
	}else{
		$data['pt_img']=$_GPC['pt_img'];
	}
	if($infoset['pd_img']!=$_GPC['pd_img'] and $_GPC['pd_img']){
		$data['pd_img']=$_W['attachurl'].$_GPC['pd_img'];
	}else{
		$data['pd_img']=$_GPC['pd_img'];
	}
	if($info['logo']!=$_GPC['logo'] and $_GPC['logo']){
		$data2['logo']=$_W['attachurl'].$_GPC['logo'];
	}else{
		$data2['logo']=$_GPC['logo'];
	}

	$data['yy_name']=$_GPC['yy_name'];
	$data['wm_name']=$_GPC['wm_name'];
	$data['dn_name']=$_GPC['dn_name'];
	$data['sy_name']=$_GPC['sy_name'];
	$data['qg_name']=$_GPC['qg_name'];
	$data['pt_name']=$_GPC['pt_name'];
	$data['pd_name']=$_GPC['pd_name'];
	$data['yysm']=$_GPC['yysm'];
	$data['wmsm']=$_GPC['wmsm'];
	$data['dnsm']=$_GPC['dnsm'];
	$data['sysm']=$_GPC['sysm'];
	$data['qgsm']=$_GPC['qgsm'];
	$data['ptsm']=$_GPC['ptsm'];
	$data['pdsm']=$_GPC['pdsm'];
	$data['is_yuepay']=$_GPC['is_yuepay'];
    $data['tz_src']=$_GPC['tz_src'];
	//echo $_GPC['is_pt'];die;
	$data['is_pt']=$_GPC['is_pt'];
	$data['is_qg']=$_GPC['is_qg'];
  // $data['integral2']=$_GPC['integral2'];
  // $data['integral']=$_GPC['integral'];
	$data['box_name']=$_GPC['box_name'];  
	$data['cj_name']=$_GPC['cj_name']; 
	$data['wmps_name']=$_GPC['wmps_name']; 


	$data2['is_brand']=$_GPC['is_brand'];
	$data2['is_select']=$_GPC['is_select'];
	$data2['score']=$_GPC['score'];
	$data2['is_open']=$_GPC['is_open'];
	$data2['number']=$_GPC['number'];
	$data2['user_id']=$_GPC['user_id'];
  // print_r($_GPC['admin_id']);die;
	$data2['admin_id']=$_GPC['admin_id'];
	$data2['md_type']=$_GPC['md_type'];
	$data2['md_area']=$_GPC['md_area'];
	$data2['name']=$_GPC['name'];
	$data2['uniacid']=$_W['uniacid'];
	$data2['rzdq_time']=$_GPC['rzdq_time'];
	$data2['ps_poundage']=$_GPC['ps_poundage'];
  // echo $_GPC['md_area'];die;
//   if(!$_GPC['md_area']){
//     message('请选择门店区域!','','error'); 
//}
	if(!$_GPC['md_type']){
		message('请选择门店类型!','','error'); 
	}
	if(!$_GPC['name']){
		message('请填写门店名称!','','error'); 
	}
	if(!$_GPC['logo']){
		message('请选择门店LOGO!','','error'); 
	}
	if($_GPC['id']==''){  
		$data2['color']="#34AAFF";
		$data2['state']=2;
		$res=pdo_insert('cjdc_store',$data2);
		$storeid=pdo_insertid();
		$data['store_id']=$storeid;
		pdo_insert('cjdc_storeset',$data);
		if($res){
			message('添加成功！', $this->createWebUrl('store'), 'success');
		}else{
			message('添加失败！','','error');
		}
	}else{
		if($info['state']==4){
			$data2['state']=2;
		}

		$res=pdo_update('cjdc_store',$data2,array('id'=>$_GPC['id']));
		$res2=pdo_update('cjdc_storeset',$data,array('store_id'=>$_GPC['id']));
		if($res || $res2){
			message('编辑成功！', $this->createWebUrl('store'), 'success');
		}else{
			message('编辑失败！','','error');
		}
	}
}
include $this->template('web/addstore');