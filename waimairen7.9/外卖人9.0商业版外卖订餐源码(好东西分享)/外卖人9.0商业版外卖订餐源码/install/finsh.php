<?php
define('rootdir', './../');  
header("Content-Type:text/html;charset=utf-8"); 
date_default_timezone_set("Asia/Hong_Kong"); 
include(rootdir."/lib/Smarty/libs/Smarty.class.php");  
include('function.php');
$smarty = new Smarty();  //建立smarty实例对象$smarty       
$smarty->cache_lifetime = 0;  //设置缓存时间
$smarty->caching = false; 
$smarty->template_dir = "./templates"; //设置模板目录  
$smarty->compile_dir = "./templates_c"; //设置编译目录 
$smarty->cache_dir = "./smarty_cache"; //缓存文件夹  
$smarty->left_delimiter = "<{"; 
$smarty->right_delimiter = "}>";
if(file_exists(rootdir.'install/lock.php'))
{
	echo '已经安装过此程序...';
	exit;
}
if($fp = @fopen(rootdir."install/lock.php", 'w')) {
       @fclose($fp);    
       }else{
       	echo '创建锁定文件失败，请手动删除install文件夹内容';
       	exit;
      }  
 
 
$step = trim($_REQUEST['step']) ? trim($_REQUEST['step']) : 1;
$mode = 0777; 
$smarty->display('step5.tpl.php'); 

 


 
?>