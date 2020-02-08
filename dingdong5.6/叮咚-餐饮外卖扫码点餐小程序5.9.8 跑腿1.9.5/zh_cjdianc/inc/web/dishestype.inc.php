<?php
global $_GPC, $_W;
$GLOBALS['frames'] = $this->getMainMenu2();
$storeid=$_COOKIE["storeid"];
$cur_store = $this->getStoreById($storeid);
$list = pdo_getall('cjdc_type',array('uniacid' => $_W['uniacid'],'store_id'=>$storeid), array() , '' , 'order_by ASC');
if($_GPC['op']=='del'){
	$rst=pdo_getall('cjdc_goods',array('type_id'=>$_GPC['id']));
		if(!$rst){
		$result = pdo_delete('cjdc_type', array('id'=>$_GPC['id']));
		if($result){
			message('删除成功',$this->createWebUrl('dishestype',array()),'success');
		}else{
		message('删除失败','','error');
		}
	}else{
		message('该分类下有菜品无法删除','','error');
	}
}
if($_GPC['op']=='upd'){
	$res=pdo_update('cjdc_type',array('is_open'=>$_GPC['state']),array('id'=>$_GPC['id']));
	if($res){
			message('修改成功',$this->createWebUrl('dishestype',array()),'success');
		}else{
		message('修改失败','','error');
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
            message('文件类型不符',$this->createWebUrl('dishestype',array()),'error');
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
             $insert['order_by'] = $row[1];
             $insert['type_name'] = $row[2];
             $insert['is_open'] = $row[3];

            $insert['store_id'] = $storeid;
            $insert['uniacid'] = $_W['uniacid'];
            $res= pdo_insert('cjdc_type',$insert);  
            $count = $count + $res;
          }
        }
       //unlink($uploadfile); //删除文件
        if ($count == 0) {
          message('导入失败',$this->createWebUrl('dishestype',array()),'error');

        } else {
          message('导入成功',$this->createWebUrl('dishestype',array()),'success');
        }

      }

include $this->template('web/dishestype');