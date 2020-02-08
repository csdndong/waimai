<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$type=$_GPC['type']?$_GPC['type']:'wm';
if($type=='wm'){
	$goods=pdo_getall('cjdc_goods',array('store_id'=>$storeid,'type'=>array(1,3)),array('id'));	
	$where=' and b.state in (4,5,10)';
}else{
	$goods=pdo_getall('cjdc_goods',array('store_id'=>$storeid,'type'=>array(2,3)),array('id'));	
	$where=' and b.dn_state=2';
}


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

foreach ($data as $key => $row)
    {
        $goodnum[$key] = $row['goodnum'];

    }
array_multisort($goodnum, SORT_DESC, $data);



include $this->template('web/dlgoodrank');