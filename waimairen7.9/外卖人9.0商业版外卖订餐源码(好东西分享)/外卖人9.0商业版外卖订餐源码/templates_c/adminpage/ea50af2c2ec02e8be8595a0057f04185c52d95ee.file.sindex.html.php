<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:10
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\sindex.html" */ ?>
<?php /*%%SmartyHeaderCode:306515cd55f5a6c9d55-89481770%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea50af2c2ec02e8be8595a0057f04185c52d95ee' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\sindex.html',
      1 => 1537876910,
      2 => 'file',
    ),
    '3b3ff05f46a61d6006a0012129b99c877b4dc819' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin.html',
      1 => 1537876910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '306515cd55f5a6c9d55-89481770',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'is_static' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd55f5a888007_92109588',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f5a888007_92109588')) {function content_5cd55f5a888007_92109588($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
?>﻿<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<meta http-equiv="Content-Language" content="zh-CN"> 
<meta content="all" name="robots"> 
<meta name="description" content=""> 
<meta content="" name="keywords"> 
<title>后台管理中心 </title>  
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/admin1.css?v=9.0"> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/public.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>

 <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/index.css">
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/kindeditor/kindeditor.js" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js" type="text/javascript" language="javascript"></script>
 
<script>
	var menu = null;
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	 
</script> 
</head> 
<body style="position:relative;"> 
<div id="cat_zhe" class="cart_zhe" style="display:none;"></div>
 
 
<div style="clear:both;"></div>
<div class="newmain" style='height:auto!important'>
 
   <!-- 主内容区-->
   <div class="newmain_all">
   	 <!-- 主内左区-->
   	 
   	  
 
 
  
 
 
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   <div class="showtop_t" id="positionname">网站信息</div>
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	
 <style>
 .newfoot{
    bottom: 0!important;
 }
 
 
 </style>
 <div class="homeRightCon">
     <div class="homeRightTop">
         <ul>
             <li class="hoTopbg01">
                 <a   target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordertoday"),$_smarty_tpl);?>
">
                     <i class="icon_ddks"></i>
                     <span>订单快速处理系统</span>
                 </a>
             </li>
             <li class="hoTopbg02">
                 <a target="_bank"  href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/adminfastfoods"),$_smarty_tpl);?>
">
                     <i class="icon_dkxd"></i>
                     <span>客服代客下单系统</span>
                 </a>
             </li>
             <li class="hoTopbg03">
                 <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/analysis/module/js_statisyic"),$_smarty_tpl);?>
">
                     <i class="icon_sjjs"></i>
                     <span>商家结算统计</span>
                 </a>
             </li>
             <li class="hoTopbg04">
			 <?php ob_start();?><?php echo Mysite::$app->config['psbopen'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1){?>
                 <a target="_blank" href="<?php echo Mysite::$app->config['autopsblink'];?>
">
		     <?php }else{ ?>
				 <a  target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/psuser/module/psymap"),$_smarty_tpl);?>
">
		     <?php }?>
                     <i class="icon_psydd"></i>
                     <span>配送员调度管理系统</span>
                 </a>
             </li>
             <li class="hoTopbg05">
                 <a target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/shop/module/goodslibrary"),$_smarty_tpl);?>
">
                     <i class="icon_spkgl"></i>
                     <span>商品库管理系统</span>
                 </a>
             </li>
         </ul>
     </div>
     <div class="homeRightBot">
         <div class="homeRightBox">
             <div class="homeRightTit hoRiTit_bg01">
                 <!--<i class="icon_edit"></i>-->
                 <i class="icon_data" style="width:60px;height:60px;"></i>
                 <h2>系统信息</h2>
             </div>


             <div class="homeRightEdit">
                 <table>
                     <tbody style='line-height:50px;border-left: 1px solid #e6e6e6;border-right: 1px solid #e6e6e6;'>
                     <tr>
                         <th>服务器时间：</th>
                         <td><?php echo smarty_modifier_date_format(time(),"%Y-%m-%d %H:%M:%S");?>
</td>
                     </tr>
					 
                     <tr class="trbg">
                         <th>PHP版本：</th>
                         <td><?php echo phpversion();?>
</td>
                     </tr>
					 <tr>
                         <th>系统版本：</th>
                         <td>9.0</td>
                     </tr>
                     <tr>
                         <th>官方网址：</th>
                         <td><?php echo $_smarty_tpl->tpl_vars['website']->value;?>
</td> 
                     </tr>
                     <tr class="trbg">
                         <th>版权所有：</th>
                         <td><?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
</td>
                     </tr>
                     </tbody>
                 </table>
             </div>
         </div>
         <div class="homeRightBox">
             <div class="homeRightTit hoRiTit_bg02" >
                    <!--<i class="icon_data"></i>-->
                   <i class="icon_edit" style="width:60px;heith:60px;"></i>
                    <h2>数据统计</h2>
                  </div> 


             <div class="homeRightData" style='border-left: 1px solid #e6e6e6;border-right: 1px solid #e6e6e6;'>
                 <ul>
                     <li class="font_red"><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['dayallorder'];?>
</h3><span>今日总订单</span></li>
                     <li class="font_org"><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['dayworder'];?>
</h3><span>今日待审核订单</span></li>
                     <li class="font_green"><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['dayporder'];?>
</h3><span>今日已审核订单</span></li>
                     <li><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['monthallorder'];?>
</h3><span>本月完成订单</span></li>
                     <li style="border-right:none;"><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['allorder'];?>
</h3><span>完成订单总量</span></li>
                     <li><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['pmember'];?>
</h3><span>会员总数</span></li>
                     <li><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['wshop'];?>
</h3><span>待审核商家</span></li>
                     <li><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['onlineshop'];?>
</h3><span>已上线商家</span></li>
                     <li><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['market'];?>
</h3><span>商城订单</span></li>
                     <li style="border-right:none;"><h3><?php echo $_smarty_tpl->tpl_vars['tjdata']->value['marketg'];?>
</h3><span>商品数量</span></li>
                 </ul>
             </div>
         </div>
     </div>
 </div>

   	        	 	
               <div class="show_content_m_t3">
   	        	 </div>
   	        	 
   	       </div>
   	       <!-- show_content_m结束-->


   	  </div>
   	  <!-- right_content 结束-->
   	  
   </div>
   <!-- newmain_all 结束-->
</div>	
	
<!--newmain 结束-->
















<div style="clear:both;"></div>
 
<script>
$(function(){ 
 $('.show_page a').wrap('<li></li>');
 $('.show_page').find('.current').eq(0).parent().css({'background':'#f60'}); 
});
   function limitalert(){
		diaerror("您暂无权限设置,如有疑问请联系QQ：375952873  Tel：18538930577");
	}
</script>
</body>
</html>





<?php }} ?>