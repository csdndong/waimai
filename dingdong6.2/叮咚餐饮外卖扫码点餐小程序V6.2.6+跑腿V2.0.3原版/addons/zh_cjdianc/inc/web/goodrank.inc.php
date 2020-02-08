<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$type=$_GPC['type']?$_GPC['type']:'wm';
$start=strtotime($_GPC['time']['start'])?:strtotime(date("Y-m-d 00:00:00"));
$end=strtotime($_GPC['time']['end'])?:strtotime(date("Y-m-d 23:59:59"));;
if($type=='wm'){
	$goods=pdo_getall('cjdc_goods',array('store_id'=>$storeid,'type'=>array(1,3)),array('id'));	
	$where=' and b.state in (4,5,10) and unix_timestamp(b.time)>='.$start.' and unix_timestamp(b.time)<='.$end;
}else{
	$goods=pdo_getall('cjdc_goods',array('store_id'=>$storeid,'type'=>array(2,3)),array('id'));	
	$where=' and b.dn_state=2 and unix_timestamp(b.time)>='.$start.' and unix_timestamp(b.time)<='.$end;
}

//unix_timestamp(b.time);
$goods=array_column($goods,'id');
$data=array();
for($i=0;$i<count($goods);$i++){
$sql = "select sum(a.number) as goodnum ,a.name,a.img from " . tablename("cjdc_order_goods") . " a" . " left join " . tablename("cjdc_order") . " b on b.id=a.order_id where a.dishes_id={$goods[$i]} ".$where;
$number=pdo_fetch($sql);
$number['goodnum']=$number['goodnum']?$number['goodnum']:0;
	if($number['goodnum']>0){
		$data[]=array(
		'id'=>$goods[$i],
		'goodnum'=>$number['goodnum'],
		'name'=>$number['name'],
		'img'=>$number['img']
	);
	}
	
}

if($data){
	foreach ($data as $key => $row)
    {
        $goodnum[$key] = $row['goodnum'];

    }
	array_multisort($goodnum, SORT_DESC, $data);
}




include $this->template('web/goodrank');