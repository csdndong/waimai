<?php
global $_GPC, $_W;
$action = 'start';
$uid=$_COOKIE["uid"];
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$GLOBALS['frames'] = $this->getNaveMenu($storeid, $action,$uid);
$type=pdo_getall('cjdc_type',array('store_id'=>$storeid),array(),'','order_by asc');
$where=" WHERE a.uniacid=:uniacid and a.store_id=:store_id";
$data[':uniacid']=$_W['uniacid'];
$data[':store_id']=$storeid;

  //echo $_GPC['area'];die;
if($_GPC['keywords']){
  $where .=" and a.name LIKE :name ";
  $op=$_GPC['keywords'];
  $data[':name']="%$op%";
  
}
if($_GPC['dishes_type']){
  $where .=" and a.dishes_type=:bid";
  $data[':bid']=$_GPC['dishes_type'];
}
if($_GPC['type_id']){
  $where .=" and a.type_id=:type_id";
  $data[':type_id']=$_GPC['type_id'];
}
if($_GPC['is_show2']){
  $where .=" and a.is_show=:cid";
  $data[':cid']=$_GPC['is_show2'];
}


$pageindex = max(1, intval($_GPC['page']));
$pagesize=15;
$sql="select a.* ,b.type_name from " . tablename("cjdc_goods") . " a"  . " left join " . tablename("cjdc_type") . " b on b.id=a.type_id".$where." order by num asc";
$select_sql =$sql." LIMIT " .($pageindex - 1) * $pagesize.",".$pagesize;
$list = pdo_fetchall($select_sql,$data);     
$total=pdo_fetchcolumn("select count(*) from " . tablename("cjdc_goods") . " a"  . " left join " . tablename("cjdc_type") . " b on b.id=a.type_id".$where,$data);
$pager = pagination($total, $pageindex, $pagesize);
if($_GPC['id']){
  $data2['is_show']=$_GPC['is_show'];
  $res=pdo_update('cjdc_goods',$data2,array('id'=>$_GPC['id']));
  if($res){
    message('设置成功',$this->createWebUrl2('dldishes2',array('page'=>$_GPC['page'],'keywords'=>$_GPC['keywords'],'dishes_type'=>$_GPC['dishes_type'],'type_id'=>$_GPC['type_id'],'is_show2'=>$_GPC['is_show2'])),'success');
  }else{
    message('设置失败','','error');
  }
}
if($_GPC['op']=='delete'){
  $result = pdo_delete('cjdc_goods', array('id'=>$_GPC['delid']));
    pdo_delete('cjdc_shopcar',array('good_id'=>$_GPC['delid']));
    pdo_delete("cjdc_spec_combination",array('good_id'=>$_GPC['delid']));
    pdo_delete("cjdc_spec_val",array('good_id'=>$_GPC['delid']));
  if($result){
    message('删除成功',$this->createWebUrl2('dldishes2',array()),'success');
  }else{
    message('删除失败','','error');
  }
}

if(checksubmit('submit2')){
  $url=$_W['attachurl'];
  $filename = $_FILES['file_stu']['name'];
  $tmp_name = $_FILES['file_stu']['tmp_name'];
  $filePath = IA_ROOT . '/addons/zh_cjdianc/excel/';
  include 'phpexcelreader/reader.php';
  $data = new Spreadsheet_Excel_Reader();
  $data->setOutputEncoding('utf-8');

        //注意设置时区
        $time = date("y-m-d-H-i-s"); //去当前上传的时间
        $extend = strrchr ($filename, '.');
        //上传后的文件名
        $name = $time . $extend;
        $uploadfile = $filePath . $name; //上传后的文件名地址
    //@move_uploaded_file($tmp_name, $uploadfile);
        if (copy($tmp_name, $uploadfile)) {
          if (!file_exists($filePath)) {
            echo '文件路径不存在.';
            return;
          }
          if (!is_readable($uploadfile)) {
            echo("文件为只读,请修改文件相关权限.");
            return;
          }
          if(!in_array($extend, array('.xls')))  
    //检查文件类型
          {
            message('文件类型不符',$this->createWebUrl('dldishes2',array()),'error');
            exit;
          }
          
          $data->read($uploadfile);
          $num=count($data->sheets[0]['cells']);
          error_reporting(E_ALL ^ E_NOTICE);
          $count = 0;
            for ($i = 2; $i <=  $num; $i++) { //$=2 第二行开始
             $row = $data->sheets[0]['cells'][$i];
                //message($data->sheets[0]['cells'][$i][1]);
                //开始处理数据库
             $insert['num'] = $row[1];
             $insert['type_id'] = $row[2];
             $insert['type'] = $row[3];
             $insert['name'] = $row[4];
             if(strstr($row[5],'http')){
              $insert['logo'] =$row[5];
            }else{
              $insert['logo'] = $url.$row[5];
            }                
            $insert['inventory'] = $row[6];
            $insert['sales'] = $row[7];
            $insert['money'] = $row[8];
            $insert['money2'] = $row[9];
            $insert['dn_money'] = $row[10];             
            $insert['box_money'] = $row[11];
            $insert['restrict_num'] = $row[12];
            $insert['start_num'] = $row[13];
            $insert['details'] = $row[14];
            $insert['is_zp'] = $row[15];
            $insert['is_show'] = $row[16];
            $insert['store_id'] = $storeid;
            $insert['uniacid'] = $_W['uniacid'];
            $res= pdo_insert('cjdc_goods',$insert);  
            $count = $count + $res;
          }
        }
       //unlink($uploadfile); //删除文件
        if ($count == 0) {
          message('导入失败',$this->createWebUrl2('dldishes2',array()),'error');
          
        } else {
         message('导入成功',$this->createWebUrl2('dldishes2',array()),'success');
       }
       
     }
     include $this->template('web/dldishes2');
