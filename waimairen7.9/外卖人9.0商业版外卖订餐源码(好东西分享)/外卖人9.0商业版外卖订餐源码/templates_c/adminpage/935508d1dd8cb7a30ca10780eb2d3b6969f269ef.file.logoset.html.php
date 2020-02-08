<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:14:19
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\system\logoset.html" */ ?>
<?php /*%%SmartyHeaderCode:272275cd63e0b5b7b62-39230759%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '935508d1dd8cb7a30ca10780eb2d3b6969f269ef' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\system\\logoset.html',
      1 => 1537876910,
      2 => 'file',
    ),
    'ce688c62a0ef9c1ec6275cc2b7cf0c7496dcb1f7' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\logoadmin.html',
      1 => 1538873527,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '272275cd63e0b5b7b62-39230759',
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
  'unifunc' => 'content_5cd63e0b892c10_16158333',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63e0b892c10_16158333')) {function content_5cd63e0b892c10_16158333($_smarty_tpl) {?>﻿ <html xmlns="http://www.w3.org/1999/xhtml"><head> 
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
/public/js/public1.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>

<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js"></script>
 
<script>
	var menu = null;
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	 
</script> 
</head> 

<style>
.tb{
    display:none;
}
.showtop_t{
   padding-top: 6px!important;
   border: none;
    padding: 0;
}
 .navs{
	display: inline-block;
	height: 30px;
	width: 90px;
	text-align: center; 
	border-top-left-radius: 7px;   
    border-top-right-radius: 7px;   	
 }
 .navon{
	 background-color: #fff!important;
 }
</style>
<body> 
<div id="cat_zhe" class="cart_zhe" style="display:none;"></div>
 
<div style="clear:both;"></div>
<div class="newmain">
 
   <!-- 主内容区-->
   <div class="newmain_all">
   	  
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   
					   <div class="showtop_t"  id="positionname">
						  <div class="navs navon" data='sjlogo'>
						  <a href="#">手机端图标</a>
						  </div>						  
						  <div class="navs" data='pclogo'>
						  <a href="#">pc端图标</a>
						  </div>	
                          <div class="navs" data='htlogo'>
						  <a href="#">后台图标</a>
						  </div>						    						  
					   </div>
					   
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
 	
	  <div style="width:auto;overflow-x:hidden;overflow-y:auto;">   
          <div id="tagscontent" style="margin-top:10px;">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/savelogoset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
                    <tr onmouseover="this.bgColor='#ebe7dc';" onmouseout="this.bgColor='#ebe7dc';" bgcolor="#ebe7dc">
                       <th style='font-size: 14px;font-weight: bold;' class="left">图标类型</th>
                       <th style='text-align:center;font-size: 14px;font-weight: bold;'>图标内容</th>					  
					   <th style='font-size: 14px;font-weight: bold;text-align:center;' >建议尺寸(px)</th>
					   <th style='font-size: 14px;font-weight: bold;text-align:center;' >图标说明</th>
					   <th style='font-size: 14px;font-weight: bold;text-align:center;' >操作</th>
                    </tr> 
                     
					<tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">网站logo</td>
                      <td style='text-align:center;width:30%'>
                      	<input type="hidden" name="sitelogo" id="sitelogo" value="<?php echo $_smarty_tpl->tpl_vars['sitelogo']->value;?>
" class="skey" style="width:100px;"> 
                      	<img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['sitelogo']->value)),$_smarty_tpl);?>
    width=150px height=50px id="imgshowc" <?php if (empty($_smarty_tpl->tpl_vars['sitelogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;"> 
                      </td>
					  <td style='text-align:center;'>166*46</td>
					  <td style='text-align:center;width:30%'>PC端首页左上角网站图标</td>
					  <td style='text-align:center;'>
					      <img style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg" onclick="uploadc();">
					  </td>
                    </tr>
					  
					<tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">网站首页logo</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="webcaption" id="webcaption" value="<?php echo $_smarty_tpl->tpl_vars['webcaption']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['webcaption']->value)),$_smarty_tpl);?>
    width=300px height=50px id="imgwebcaption" <?php if (empty($_smarty_tpl->tpl_vars['webcaption']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">   
                        </td>
						<td style='text-align:center;'>550*54</td>
						<td style='text-align:center;'>PC端首页搜索输入框上方图标</td>
						<td style='text-align:center;'><img onclick="uploadw();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
						 
                    </tr>
                    <tr class='htlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">后台logo</td>
                      <td style='text-align:center; '>
                      	<input type="hidden" name="adminlogo" id="adminlogo" value="<?php echo $_smarty_tpl->tpl_vars['adminlogo']->value;?>
" class="skey" style="width:100px;"> 
                        <?php if (empty($_smarty_tpl->tpl_vars['adminlogo']->value)){?>
                      	<img src="/templates/adminpage/public/images/admin/logo.png" width=200px height=50px id="imgshowadmin" <?php if (empty($_smarty_tpl->tpl_vars['sitelogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;"> 
                      	<?php }else{ ?>
                        <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['adminlogo']->value)),$_smarty_tpl);?>
    width=200px height=50px id="imgshowadmin" <?php if (empty($_smarty_tpl->tpl_vars['sitelogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">      
                        <?php }?>     
                              
                      </td>
					  <td style='text-align:center;'>267*51</td>
					  <td style='text-align:center;'>网站后台左上角图标</td>
					  <td style='text-align:center;'><img onclick="uploadadmin();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
					   
                   </tr>

                    <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信端背景logo</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="wxbglogo" id="wxbglogo" value="<?php echo $_smarty_tpl->tpl_vars['wxbglogo']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxbglogo']->value)),$_smarty_tpl);?>
    width=130px height=50px id="wxbglogoshow" <?php if (empty($_smarty_tpl->tpl_vars['wxbglogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">                          
                        </td>
						<td style='text-align:center;'>220*68</td>
						<td style='text-align:center;'>微信端首页下拉显示图标</td>
						<td style='text-align:center;'><img onclick="uploadwxbglogo();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
						  
                    </tr>

				    <tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户默认头像</td>
                      <td style='text-align:center; '>
                      	<input type="hidden" name="userlogo" id="userlogo" value="<?php echo $_smarty_tpl->tpl_vars['userlogo']->value;?>
" class="skey" style="width:100px;">
                      	<img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['userlogo']->value)),$_smarty_tpl);?>
  width=50px height=50px id="imgshow" <?php if (empty($_smarty_tpl->tpl_vars['userlogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;"> 
                      </td>
					  <td style='text-align:center;'>120*120</td>
					  <td style='text-align:center;'>用户未设置头像默认显示该头像</td>
					  <td style='text-align:center;'><img onclick="uploads();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
   
                    </tr>
					<tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
					    <td class="left">店铺默认头像</td>
						<td style='text-align:center; '>
							<input type="hidden" name="shoplogo" id="shoplogo" value="<?php echo $_smarty_tpl->tpl_vars['shoplogo']->value;?>
" class="skey" style="width:100px;"> 
							<img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['shoplogo']->value)),$_smarty_tpl);?>
  width=50px height=50px id="imgshows" <?php if (empty($_smarty_tpl->tpl_vars['shoplogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;"> 
						</td>
						<td style='text-align:center;'>120*120</td>
						<td style='text-align:center;'>店铺未设置头像默认显示该头像</td>
						<td style='text-align:center;'><img onclick="uploadm();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td> 					
					</tr>
					<tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
					    <td class="left">商品默认图片</td>
						<td style='text-align:center; '>
							<input type="hidden" name="goodlogo" id="goodlogo" value="<?php echo $_smarty_tpl->tpl_vars['goodlogo']->value;?>
" class="skey" style="width:100px;"> 
							<img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['goodlogo']->value)),$_smarty_tpl);?>
  width=50px height=50px id="goodimgshow" <?php if (empty($_smarty_tpl->tpl_vars['goodlogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;"> 
						</td>
						<td style='text-align:center;'>120*120</td>
						<td style='text-align:center;'>商品未设置图片默认显示该图片</td>
						<td style='text-align:center;'><img onclick="uploadgood();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td> 					
					</tr>
					
                    <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">登录页面logo</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="loginlogo" id="loginlogo" value="<?php echo $_smarty_tpl->tpl_vars['loginlogo']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['loginlogo']->value)),$_smarty_tpl);?>
   width=50px height=50px id="loginlogoshow" <?php if (empty($_smarty_tpl->tpl_vars['loginlogo']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">   
                        </td>
						<td style='text-align:center;'>65*65</td>
						<td style='text-align:center;'>微信端登录页面显示图标</td>
						<td style='text-align:center;'><img onclick="uploadl();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
   
						 
                    </tr>
                    <tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信关注二维码</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="wxewm" id="wxewm" value="<?php echo $_smarty_tpl->tpl_vars['wxewm']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxewm']->value)),$_smarty_tpl);?>
   width=50px height=50px id="imgwxewm" <?php if (empty($_smarty_tpl->tpl_vars['wxewm']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                             
                        </td>
						<td style='text-align:center;'>142*142</td>
						<td style='text-align:center;'>pc端首页关注微信扫描二维码</td>
						<td style='text-align:center;'><img onclick="uploadwx();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
   
						 
                    </tr>

                    <tr class='pclogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">APP下载二维码</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="appewm" id="appewm" value="<?php echo $_smarty_tpl->tpl_vars['appewm']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['appewm']->value)),$_smarty_tpl);?>
   width=50px height=50px id="imgappewm" <?php if (empty($_smarty_tpl->tpl_vars['appewm']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">  
                        </td>
						<td style='text-align:center;'>142*142</td>
						<td style='text-align:center;'>pc端首页下载APP扫描二维码</td>
						<td style='text-align:center;'><img onclick="uploadappewm();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
   
						 
                    </tr>
                    <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">注册送优惠劵图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="regimg" id="regimg" value="<?php echo $_smarty_tpl->tpl_vars['regimg']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['regimg']->value)),$_smarty_tpl);?>
    width=170px height=40px id="imgregimg" <?php if (empty($_smarty_tpl->tpl_vars['regimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>375*70</td>
						<td style='text-align:center;'>微信端未登录状态首页引导注册图标</td>
						<td style='text-align:center;'><img onclick="uploadregimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
    
                    </tr>
				  
				  <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">微信分享默认logo</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="wxshare" id="wxshare" value="<?php echo $_smarty_tpl->tpl_vars['share_img']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['share_img']->value)),$_smarty_tpl);?>
  width=50px height=50px id="imgwxshare" <?php if (empty($_smarty_tpl->tpl_vars['share_img']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>50*50</td>
						<td style='text-align:center;'>微信端分享链接默认图标</td>
						<td style='text-align:center;'><img onclick="uploadwxshare();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
    
                    </tr>
					<tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">app分享默认logo</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="appshare" id="appshare" value="<?php echo $_smarty_tpl->tpl_vars['appshare_img']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['appshare_img']->value)),$_smarty_tpl);?>
  width=50px height=50px id="imgappshare" <?php if (empty($_smarty_tpl->tpl_vars['appshare_img']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>50*50</td>
						<td style='text-align:center;'>app端分享链接默认图标(必须为正方形且文件不大于100kb)</td>
						<td style='text-align:center;'><img onclick="uploadappshare();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
    
                    </tr>
                    <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">商家配送图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="shoppsimg" id="shoppsimg" value="<?php echo $_smarty_tpl->tpl_vars['shoppsimg']->value;?>
" class="skey" style="width:100px;">
                            <img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['shoppsimg']->value)),$_smarty_tpl);?>
   width=80px height=25px id="shoppsimgshow" <?php if (empty($_smarty_tpl->tpl_vars['shoppsimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>57*15</td>
						<td style='text-align:center;'>商家配送类型店铺配送图标</td>
						<td style='text-align:center;'><img onclick="uploadshoppsimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
     
                    </tr>

                    
                    <tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">网站配送图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="psimg" id="psimg" value="<?php echo $_smarty_tpl->tpl_vars['psimg']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['psimg']->value)),$_smarty_tpl);?>
   width=80px height=25px id="psimgshow" <?php if (empty($_smarty_tpl->tpl_vars['psimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>57*15</td>
						<td style='text-align:center;'>平台配送类型店铺配送图标</td>
						<td style='text-align:center;'><img onclick="uploadpsimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
     
						 
                    </tr>
					
					<tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">到店自取图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="ztimg" id="ztimg" value="<?php echo $_smarty_tpl->tpl_vars['ztimg']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['ztimg']->value)),$_smarty_tpl);?>
    width=80px height=25px id="ztimgshow" <?php if (empty($_smarty_tpl->tpl_vars['ztimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>57*15</td>
						<td style='text-align:center;'>支持到店自取店铺的自取图标</td>
						<td style='text-align:center;'><img onclick="uploadztimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
     
						 
                    </tr>
					
					<tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">店铺专场分类图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="zcimg" id="zcimg" value="<?php echo $_smarty_tpl->tpl_vars['zcimg']->value;?>
" class="skey" style="width:100px;">
                            <img  <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['zcimg']->value)),$_smarty_tpl);?>
  width=15px height=15px id="zcimgshow" <?php if (empty($_smarty_tpl->tpl_vars['zcimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>15*15</td>
						<td style='text-align:center;'>店铺首页专场分类左侧小图标</td>
						<td style='text-align:center;'><img onclick="uploadzcimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
     
						 
                    </tr>
					<tr class='sjlogo tb' onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                        <td class="left">店铺折扣分类图标</td>
                        <td style='text-align:center; '>
                            <input type="hidden" name="zkimg" id="zkimg" value="<?php echo $_smarty_tpl->tpl_vars['zkimg']->value;?>
" class="skey" style="width:100px;">
                            <img   <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['zkimg']->value)),$_smarty_tpl);?>
  width=15px height=15px id="zkimgshow" <?php if (empty($_smarty_tpl->tpl_vars['zkimg']->value)){?> style="display:none;"<?php }?> style="margin-top: 15px;">
                        </td>
						<td style='text-align:center;'>15*15</td>
						<td style='text-align:center;'>店铺首页折扣分类左侧小图标</td>
						<td style='text-align:center;'><img onclick="uploadzkimg();" style='margin-bottom:-8px' src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/admin/edit.jpg"></td>
     
						 
                    </tr>

                     
			 
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="logoset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
         
           
         </div>
      
    

          </div> 
<script>
	var dialogs ;
 function uploads(){  
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucess"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片'); 
 }
 function uploadsucess(flag,obj,linkurl){
 	 if(flag == true){
 		
		dialogs.close();
		uploads();
 	 }else{ 	 
 		dialogs.close();
 	    $('#userlogo').val(linkurl);
   	    $('#imgshow').attr('src',linkurl);
 	    $('#imgshow').show();
   }
 } 
  function uploadwxshare(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsuceswxshare"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsuceswxshare(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#wxshare').val(linkurl);
            $('#imgwxshare').attr('src',linkurl);
            $('#imgwxshare').show();
        }
    }
	function uploadappshare(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucesappshare"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsucesappshare(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#appshare').val(linkurl);
            $('#imgappshare').attr('src',linkurl);
            $('#imgappshare').show();
        }
    }
function uploadregimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucesregimg"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsucesregimg(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#regimg').val(linkurl);
            $('#imgregimg').attr('src',linkurl);
            $('#imgregimg').show();
        }
    } 
 function uploadm(){  
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucessm"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片'); 
 }
 function uploadsucessm(flag,obj,linkurl){
 	 if(flag == true){
 		
		dialogs.close();
		uploadm();
 	 }else{ 
 		dialogs.close();
 	 $('#shoplogo').val(linkurl);
 	$('#imgshows').attr('src',linkurl);
 	$('#imgshows').show(); 
   }
 } 
 function uploadadmin(){  
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucessadmin"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片'); 
 }
 function uploadsucessadmin(flag,obj,linkurl){
 	 if(flag == true){
 		
		dialogs.close();
		uploadadmin();
 	 }else{ 
 		dialogs.close();
 	 $('#adminlogo').val(linkurl);
 	$('#imgshowadmin').attr('src',linkurl);
 	$('#imgshowadmin').show(); 
   }
 }   
function uploadc(){  
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucessc"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片'); 
 }
 function uploadsucessc(flag,obj,linkurl){
 	 if(flag == true){
 		
		dialogs.close();
		uploadc();
 	 }else{ 
 		dialogs.close();
 	 	$('#sitelogo').val(linkurl);
 	  $('#imgshowc').attr('src',linkurl);
 	  $('#imgshowc').show(); 
   }
 } 
 function uploadk(){
 	 dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucessk"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片'); 
}
function uploadsucessk(flag,obj,linkurl){
 	 if(flag == true){
 		
		dialogs.close();
		uploadk();
 	 }else{ 
 		dialogs.close();
 	 	$('#html5logo').val(linkurl);
 	  $('#html5logoshow').attr('src',linkurl);
 	  $('#html5logoshow').show(); 
   }
 }

    function uploadw(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsuceswebc"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsuceswebc(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#webcaption').val(linkurl);
            $('#imgwebcaption').attr('src',linkurl);
            $('#imgwebcaption').show();
        }
    }
    function uploadwx(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsuceswxewm"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsuceswxewm(flag,obj,linkurl){
        if(flag == true){
            
            dialogs.close();
            uploadw();
        }else{
            
            dialogs.close();
            $('#wxewm').val(linkurl);
            $('#imgwxewm').attr('src',linkurl);
            $('#imgwxewm').show();
        }
    }

    function uploadappewm(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucesappewm"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsucesappewm(flag,obj,linkurl){
        if(flag == true){

            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#appewm').val(linkurl);
            $('#imgappewm').attr('src',linkurl);
            $('#imgappewm').show();
        }
    }
    function uploadptewm(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadsucesptewm"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadsucesptewm(flag,obj,linkurl){
        if(flag == true){

            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#ptewm').val(linkurl);
            $('#imgptewm').attr('src',linkurl);
            $('#imgptewm').show();
        }
    }

    function uploadshoppsimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadshoppsimga"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadshoppsimga(flag,obj,linkurl){
        if(flag == true){

            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#shoppsimg').val(linkurl);
            $('#shoppsimgshow').attr('src',linkurl);
            $('#shoppsimgshow').show();
        }
    }
    function uploadpsimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadpsimga"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadpsimga(flag,obj,linkurl){
        if(flag == true){

            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#psimg').val(linkurl);
            $('#psimgshow').attr('src',linkurl);
            $('#psimgshow').show();
        }
    }
	function uploadztimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadztimga"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadztimga(flag,obj,linkurl){
        if(flag == true){

            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#ztimg').val(linkurl);
            $('#ztimgshow').attr('src',linkurl);
            $('#ztimgshow').show();
        }
    }
    function uploadzcimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadzcimga"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadzcimga(flag,obj,linkurl){
        if(flag == true){
            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#zcimg').val(linkurl);
            $('#zcimgshow').attr('src',linkurl);
            $('#zcimgshow').show();
        }
    }
    function uploadzkimg(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadzkimga"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadzkimga(flag,obj,linkurl){
        if(flag == true){
            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#zkimg').val(linkurl);
            $('#zkimgshow').attr('src',linkurl);
            $('#zkimgshow').show();
        }
    }
    function uploadl(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadloginlogo"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadloginlogo(flag,obj,linkurl){
        if(flag == true){
            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#loginlogo').val(linkurl);
            $('#loginlogoshow').attr('src',linkurl);
            $('#loginlogoshow').show();
        }
    }

    function uploadwxbglogo(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadwxbglogos"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadwxbglogos(flag,obj,linkurl){
        if(flag == true){
            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#wxbglogo').val(linkurl);
            $('#wxbglogoshow').attr('src',linkurl);
            $('#wxbglogoshow').show();
        }
    }
    function uploadgood(){
        dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/adminupload/func/uploadgoods"),$_smarty_tpl);?>
');
        dialogs.title('上传图片');
    }
    function uploadgoods(flag,obj,linkurl){
        if(flag == true){
            dialogs.close();
            uploadw();
        }else{
            dialogs.close();
            $('#goodlogo').val(linkurl);
            $('#goodimgshow').attr('src',linkurl);
            $('#goodimgshow').show();
        }
    }


</script>    
<!--newmain 结束-->
 
   	        	 	
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
<script>    
    $('.sjlogo').show();
    $(".left_nav ul li a:contains('logo设置')").addClass("on");   
    var nowname =  $('.navon').text();	 
    $('#nowactioninfo').text(nowname);
    $('#positionname2').text(nowname);
    $('.navs').live('click',function(){ 
        $('.navs').removeClass("navon");
	    $(this).addClass("navon");  
        var type =  $(this).attr("data"); 
		$('.tb').hide();
		$('.'+type).show();
    });
 </script>

</body>
</html>





<?php }} ?>