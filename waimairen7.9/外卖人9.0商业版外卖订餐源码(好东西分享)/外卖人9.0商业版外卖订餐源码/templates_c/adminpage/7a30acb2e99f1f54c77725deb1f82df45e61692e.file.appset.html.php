<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:17:26
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\app\appset.html" */ ?>
<?php /*%%SmartyHeaderCode:71685cd63ec6827f93-01569374%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7a30acb2e99f1f54c77725deb1f82df45e61692e' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\app\\appset.html',
      1 => 1538187010,
      2 => 'file',
    ),
    'ae28036ea6828d4e96748f6aeae5725e436a33a0' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\public\\admin8.html',
      1 => 1538873501,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '71685cd63ec6827f93-01569374',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'is_static' => 0,
    'tmodule' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd63ec6a62fb5_49548932',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63ec6a62fb5_49548932')) {function content_5cd63ec6a62fb5_49548932($_smarty_tpl) {?>﻿ <html xmlns="http://www.w3.org/1999/xhtml"><head> 
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
 <style>
	 #showmodule li {
		 height: 800px;
		 width: 400px;
		 margin-right: 100px;
		 float: left;
		 text-align: left;
		 margin-left: 30px;
		 margin-top: 30px;
	 }
 </style>
 
<script>
	var menu = null;
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	if(screen.width > 1359){
		
		$('.newtop').css('width',screen.width);
		$('.newmain').css('width',screen.width);
		$('.newfoot').css('width',screen.width);
	}  
	
</script> 
</head> 

<style>
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
   	 <!-- 主内左区-->
   	 
 
 
  
 
 
 <div class="right_content">
	<div class="show_content_m">
   	        	 <div class="show_content_m_ti">
   	        	 	   
					   <div class="showtop_t" id="positionname">
					       
						  <div class="navs  <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='appset'){?> navon <?php }?> ">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appset"),$_smarty_tpl);?>
">基本设置</a>
						  </div> 
						  <div class="navs <?php if ($_smarty_tpl->tpl_vars['tmodule']->value=='shoptsset'){?> navon <?php }?>">
						  <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/shoptsset"),$_smarty_tpl);?>
">商家端设置</a>
						  </div>	 
						   						  
					   </div>
					   
   	        	 </div>
   	        	 <div class="show_content_m_t2">
   	        	 	
   	        	 	 
 <style>
#close{text-align: center;  color: #f5f5f5;background-color: red;font-weight: bold; position: absolute;top: -10px;right: -10px; width: 25px;height: 25px;border-radius: 20px;line-height: 25px;}
.search_content div{display:inline-block}
#addact{background: #169bd5;padding: 5px 21px;margin-top: 8px;border-radius: 5px;color: #fff;}
</style>
	   <div style="width:auto;overflow-x:hidden;overflow-y:auto"> 
         
          <div id="tagscontent">
            <form method="post" name="form1" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/saveappset/datatype/json"),$_smarty_tpl);?>
" onsubmit="return subform('',this);">
              <div>
                <table border="0" cellspacing="2" cellpadding="4" class="list" name="table" id="table" width="100%">
                  <tbody>
					
					 
				  
				  
				  
				 
					
					
					 
					
					 
					
					
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left" valign="top">用户端推送KEY</td>
                      <td> 
                         <input type="input" name="appuser2" id="appuser2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appuser2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> 
                       </td>
                    </tr>  
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户端推送secret</td>
                      <td> <input type="input" name="appsecret2" id="appsecret2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appsecret2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					
					
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户端包名</td>
                      <td> <input type="input" name="xmbao2" id="xmbao2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['xmbao2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户通知标题</td>
                      <td> <input type="input" name="xmtitle2" id="xmtitle2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['xmtitle2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">用户miuiKey</td>
                      <td> <input type="input" name="miuiKey2" id="miuiKey2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['miuiKey2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr> 
					
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left" valign="top">配送员推送KEY</td>
                      <td> 
                         <input type="input" name="appuser3" id="appuser3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appuser3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> 
                       </td>
                    </tr>
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">配送员推送secret</td>
                      <td> <input type="input" name="appsecret3" id="appsecret3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appsecret3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr> 
					<tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">配送端包名</td>
                      <td> <input type="input" name="xmbao3" id="xmbao3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['xmbao3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">配送通知标题</td>
                      <td> <input type="input" name="xmtitle3" id="xmtitle3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['xmtitle3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					  <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">配送miuiKey</td>
                      <td> <input type="input" name="miuiKey3" id="miuiKey3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['miuiKey3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr>
					
					
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">APP用户端版本</td>
                      <td> <input type="input" name="appvison1" id="appvison1" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appvison1']->value)===null||$tmp==='' ? '1' : $tmp);?>
"  class="skey" style="width:400px;"><br/>版本号请填写时间戳，起始版是1，必须和最新apk的android:versionCode 一致 要不然每次都要下载而不是最新版本 </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">安卓APP用户端下载地址</td>
                      <td> <input type="input" name="appdownload1" id="appdownload1" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload1']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://www.xxx.com/xxx.apk </td>
                    </tr>
                    
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">苹果APP用户端下载地址</td>
                      <td> <input type="input" name="appdownload1ios" id="appdownload1" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload1ios']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://itunes.apple.com/xx/app/xx?mt=8 </td>
                    </tr>
                    
                     <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">APP商家端版本</td>
                      <td> <input type="input" name="appvison2" id="appvison2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appvison2']->value)===null||$tmp==='' ? '1' : $tmp);?>
"  class="skey" style="width:400px;"><br/>版本号请填写时间戳，起始版是1，必须和最新apk的android:versionCode 一致 要不然每次都要下载而不是最新版本 </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">安卓APP商家端下载地址</td>
                      <td> <input type="input" name="appdownload2" id="appdownload2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload2']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://www.xxx.com/xxx.apk </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">苹果APP商家端下载地址</td>
                      <td> <input type="input" name="appdownload2ios" id="appdownload2" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload2ios']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://itunes.apple.com/xx/app/xx?mt=8  </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">APP配送端版本</td>
                      <td> <input type="input" name="appvison3" id="appvison3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appvison3']->value)===null||$tmp==='' ? '1' : $tmp);?>
"  class="skey" style="width:400px;"> <br/>版本号请填写时间戳，起始版是1，必须和最新apk的android:versionCode 一致 要不然每次都要下载而不是最新版本 </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">安卓APP配送端下载地址</td>
                      <td> <input type="input" name="appdownload3" id="appdownload3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload3']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://www.xxx.com/xxx.apk </td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">苹果APP配送端下载地址</td>
                      <td> <input type="input" name="appdownload3ios" id="appdownload3" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['appdownload3ios']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">http://itunes.apple.com/xx/app/xx?mt=8  </td>
                    </tr>
					
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">分享key</td>
                      <td> <input type="input" name="ymengkey" id="ymengkey" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['ymengkey']->value)===null||$tmp==='' ? '' : $tmp);?>
"  class="skey" style="width:400px;">请联系客服给您这个值</td>
                    </tr>
                    <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">QQ分享appid</td>
                      <td> <input type="input" name="qqshareappid" id="qqshareappid" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['qqshareappid']->value)===null||$tmp==='' ? '1' : $tmp);?>
"  class="skey" style="width:400px;">自行到http://connect.qq.com/申请,资料不知道怎么填写请联系客服 </td>
                    </tr>
					 <tr onmouseover="this.bgColor='#F5F5F5';" onmouseout="this.bgColor='ffffff';" bgcolor="#ffffff">
                      <td class="left">QQ分享key</td>
                      <td> <input type="input" name="qqsharekey" id="qqsharekey" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['qqsharekey']->value)===null||$tmp==='' ? '1' : $tmp);?>
"  class="skey" style="width:400px;"> </td>
                    </tr> 
                  </tbody>
                </table>
              </div>
              <div class="blank20"></div>
              <input type="hidden" name="tijiao" id="tijiao" value="do" class="skey" style="width:250px;">
              <input type="hidden" name="saction" id="saction" value="siteset" class="skey" style="width:250px;">
               <input type="submit" value="确认提交" class="button">  
            </form>
          </div>
 
 
		  
<script>
$(function(){
	  $('.gf').show(); 
}); 

var click_button = false; 
function doubleclick(){
	click_button = false;
}
function mobilechangemodule(obj){
	var url = siteurl+'/index.php?ctrl=adminpage&action=system&module=savemobiletempset&mobilemodule='+$(obj).val()+'&datatype=json&random=@random@';
	$.ajax({
		type: 'post',
		async:false,
		url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
		dataType: 'json',success: function(content) {
			if(content.error == false){
//				window.location.reload();
			}else{
				if(content.error == true)
				{
					diaerror(content.msg);
				}else{
					diaerror(content);
				}
			}
		},
		error: function(content) {
			diaerror('数据获取失败');
		}
	});
}
function lockclick(){
	 if(click_button == false){
			click_button = true;
			setTimeout("doubleclick()", 1000); 
			return true;
	 }else{
		 return false;
	 }
}
var dialogs ;
function uploads(modulename){
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/uploadapp/obj/'+modulename+'/func/uploadsucess"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片');
}
function uploadsucess(flag,obj,linkurl){
	if(flag == true){
 		alert(linkurl);
		dialogs.close();
		uploads(obj);
	}else{
		dialogs.close(); 
		//提交数据并返回显示 
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appmdimg/datatype/json"),$_smarty_tpl);?>
', {'mudulename':obj,'imgurl':linkurl} ,function (data, textStatus){  
			if(data.error == false){
				$('#module_'+obj).attr('src',linkurl);
				$('#module_'+obj).show();
			}else{
				if(data.error == true)
				{
					diaerror(data.msg); 
				}else{
					diaerror(data); 
				}
			} 
		}, 'json');  
	}
 }
$(function(){   
   $("input[name='is_display']").bind("click", function() {   
	   if(lockclick()){
		   var doselect = 0;
				if($(this).is(':checked') == true){
				doselect =1;
				} 
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/docontroladv/datatype/json"),$_smarty_tpl);?>
', {'ctrlname':'is_show','modulename':$(this).attr('data'),'modulevalue':doselect} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true)
					{
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
			}, 'json');  
		}
	});
	
	$("input[name='is_install']").bind("click", function() {  
		if(lockclick()){	
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/docontroladv/datatype/json"),$_smarty_tpl);?>
', {'ctrlname':'is_install','modulename':$(this).attr('data'),'modulevalue':$("input[name='is_install']:checked").val()} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true)
					{
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
			}, 'json');  
		}
	});
	


});


</script>
		  
		   
<script> 
function upload_gudings(modulename){
 	  dialogs = art.dialog.open('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/other/module/uploadapp/obj/'+modulename+'/func/uploadgsucess"),$_smarty_tpl);?>
');
 	  dialogs.title('上传图片');
}
function uploadgsucess(flag,obj,linkurl){
	if(flag == true){
 		alert(linkurl);
		dialogs.close();
		upload_gudings(obj);
	}else{
		dialogs.close(); 
		//提交数据并返回显示 
			$('#'+obj).attr('src',linkurl);
			$('#'+obj).show();
		  $('#input_'+obj).val(linkurl); 
	}
 }
 function dogdsave(gdid){
	 if(lockclick()){
			var checkname =   $('#input_gname'+gdid).val();
			
			if(checkname == ''){
				alert('录入的名称不能为空');
				return false;
			}
			var checkimg = $('#input_gimg'+gdid).val();
			var orderid = $('#input_gorderid'+gdid).val();
			if(checkimg == ''){
			   alert('未选择图片');
			   return false; 
			} 	
					
			var typeid = $("select[name='input_gselect"+gdid+"']").find("option:selected").val(); //$(this).input_gselect
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/addgd/datatype/json"),$_smarty_tpl);?>
', {'imgurl':checkimg,'orderid':orderid,'typeid':typeid,'name':checkname,'appposition':2,'id':gdid} ,function (data, textStatus){  
					if(data.error == false){
						diasucces('操作成功','');
					}else{
						if(data.error == true)
						{
							diaerror(data.msg); 
						}else{
							diaerror(data); 
						}
					} 
			}, 'json'); 
	}
 }  
 function dosaveadv(gdid){
	  if(lockclick()){
		var checkname =   $('#input_aname'+gdid).val();
		if(checkname == ''){
			alert('录入的名称不能为空');
			return false;
		}
		var checkimg = $('#input_aimg'+gdid).val();
		if(checkimg == ''){
		   alert('未选择图片');
		   return false; 
		} 	
				
		 
		$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/addad/datatype/json"),$_smarty_tpl);?>
', {'imgurl':checkimg,'typeid':0,'name':checkname,'appposition':1,'id':gdid} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true)
					{
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
		}, 'json'); 
		}
 }
 function dosaveanadv(gdid){
  if(lockclick()){
	var checkname =   $('#input_anname'+gdid).val();
	if(checkname == ''){
	    alert('录入的名称不能为空');
		return false;
	}
	var checkimg = $('#input_animg'+gdid).val();
	if(checkimg == ''){
	   alert('未选择图片');
	   return false; 
	} 	
	 		
	 
    $.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/addad/datatype/json"),$_smarty_tpl);?>
', {'imgurl':checkimg,'typeid':0,'name':checkname,'appposition':3,'id':gdid} ,function (data, textStatus){  
			if(data.error == false){
				diasucces('操作成功','');
			}else{
				if(data.error == true)
				{
					diaerror(data.msg); 
				}else{
					diaerror(data); 
				}
			} 
	}, 'json');
	}
 }
 
 
//新增内容 2016/12/28
function appstartimg(gdid){
	if(lockclick()){
		var checkname =   $('#input_stname'+gdid).val();
		if(checkname == ''){
			alert('录入的名称不能为空');
			return false;
		}
		var checkimg = $('#input_stimg'+gdid).val();
		if(checkimg == ''){
			alert('未选择图片');
			return false;
		}

		$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/addad/datatype/json"),$_smarty_tpl);?>
', {'imgurl':checkimg,'typeid':0,'name':checkname,'appposition':5,'id':gdid} ,function (data, textStatus){
			if(data.error == false){
				diasucces('操作成功','');
			}else{
				if(data.error == true)
				{
					diaerror(data.msg);
				}else{
					diaerror(data);
				}
			}
		}, 'json');
	}
}

function appggsimg(gdid){
	if(lockclick()){
		var checkname =   $('#input_ggsname'+gdid).val();
		if(checkname == ''){
			alert('录入的名称不能为空');
			return false;
		}
		var checkimg = $('#input_ggsimg'+gdid).val();
		if(checkimg == ''){
			alert('未选择图片');
			return false;
		}

		$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/addad/datatype/json"),$_smarty_tpl);?>
', {'imgurl':checkimg,'typeid':0,'name':checkname,'appposition':6,'id':gdid} ,function (data, textStatus){
			if(data.error == false){
				diasucces('操作成功','');
			}else{
				if(data.error == true)
				{
					diaerror(data.msg);
				}else{
					diaerror(data);
				}
			}
		}, 'json');
	}
}
 
</script>
	  
		  
        </div> 
        
        
   
    </div> 
<script>
$(function(){  
	$("input[name='APPindex']").click(function(){
	    if(lockclick()){
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appindexshow/datatype/json"),$_smarty_tpl);?>
', {'type':'APPindex','APPindex':$("input[name='APPindex']:checked").val()} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true)
					{
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
			}, 'json');  
		}
	});
	$("input[name='apppayacount']").click(function(){
		  if(lockclick()){
				 $.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appindexshow/datatype/json"),$_smarty_tpl);?>
', {'type':'apppayacount','apppayacount':$("input[name='apppayacount']:checked").val()} ,function (data, textStatus){  
					if(data.error == false){
						diasucces('操作成功','');
					}else{
						if(data.error == true)
						{
							diaerror(data.msg); 
						}else{
							diaerror(data); 
						}
					} 
				}, 'json'); 
		}
	});
	$("input[name='apppayonline']").click(function(){
			  if(lockclick()){
		$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appindexshow/datatype/json"),$_smarty_tpl);?>
', {'type':'apppayonline','apppayonline':$("input[name='apppayonline']:checked").val()} ,function (data, textStatus){  
			if(data.error == false){
				diasucces('操作成功','');
			}else{
				if(data.error == true)
				{
					diaerror(data.msg); 
				}else{
					diaerror(data); 
				}
			} 
		}, 'json'); 
		}
	});
	$("input[name='appdataver']").change(function(e){
	 
	  if(lockclick()){
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/appindexshow/datatype/json"),$_smarty_tpl);?>
', {'type':'appdataver','appdataver':$("input[name='appdataver']").val()} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true){
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
			}, 'json');
		}
		 
	});
	document.onkeydown = function(event) {  
    var target, code, tag;  
    if (!event) {  
        event = window.event; //针对ie浏览器  
        target = event.srcElement;  
        code = event.keyCode;  
        if (code == 13) {  
            tag = target.tagName;  
            if (tag == "TEXTAREA") { return true; }  
            else { return false; }  
        }  
    }  
    else {  
        target = event.target; //针对遵循w3c标准的浏览器，如Firefox  
        code = event.keyCode;  
        if (code == 13) {  
            tag = target.tagName;  
            if (tag == "INPUT") { return false; }  
            else { return true; }   
       }  
    }  
};  
	/***  模块排序  ****/
$(".savemodulepaixu").change(function(){
		if(lockclick()){
			$.post('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/app/module/savemodulepaixu/datatype/json"),$_smarty_tpl);?>
', {'name':$(this).attr('name'),'orderid':$(this).val()} ,function (data, textStatus){  
				if(data.error == false){
					diasucces('操作成功','');
				}else{
					if(data.error == true)
					{
						diaerror(data.msg); 
					}else{
						diaerror(data); 
					}
				} 
			}, 'json');
		}
	 
})

});
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
 

</body>
</html>





<?php }} ?>