<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:28:13
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\member\shoplogin.html" */ ?>
<?php /*%%SmartyHeaderCode:91215cd5604d362a85-13243177%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c94e075bcfd4002455b3994683f0299b85b23ef9' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\member\\shoplogin.html',
      1 => 1538187010,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '91215cd5604d362a85-13243177',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sitename' => 0,
    'keywords' => 0,
    'description' => 0,
    'metadata' => 0,
    'siteurl' => 0,
    'tempdir' => 0,
    'is_static' => 0,
    'controlname' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd5604d471a59_02512099',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5604d471a59_02512099')) {function content_5cd5604d471a59_02512099($_smarty_tpl) {?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>商家登录-<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title>
<meta name="Keywords" content="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" />
<?php echo stripslashes($_smarty_tpl->tpl_vars['metadata']->value);?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/shangjialogin.css"> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquerynew.js" type="text/javascript" language="javascript"></script>
  <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.lazyload.min.js" type="text/javascript" language="javascript"></script>
  <script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	var controllername= '<?php echo $_smarty_tpl->tpl_vars['controlname']->value;?>
';
</script>
<script>
	$(function(){
		var cilentHeight = document.documentElement.clientHeight;
		var topheight = cilentHeight-50;
		$("#sjfooter").css("top",topheight+"px");

	});
</script>

</head>
<body style='background:none'>
<div style='height:120px;background-color:#2a304a;text-align:center'>
    <img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/sjzizhuloginBg1.png" style='margin-top:36px;'/>
</div> 

 
<div id="sjlogin" style='border: 1px solid #f0f0f0;margin:80px auto 80px;'>

<div id="shoploginBg"><img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/images/shopLogin_Bg.png" /></div>


	<div class="sjlogin_title">
		<span>商家登录</span>
	</div>
	<div class="sj_cont">
		 
		
		<div class="sj_cont_right" style="margin-top:10px;">
		<form id="loginForm" method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/shoploginin/datatype/json"),$_smarty_tpl);?>
">
			<div class="js_username">
			    <div style='position: relative; margin: 0 auto;'>
				<div style='height: 38px;width: 38px;border: 1px solid #bcbcbc;position: absolute;left: 11px;background-color: #dddddd;'>
				     <img src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/shopuser.png' style='margin-top:6px;height:25px'>
				</div>
			    <input class="unameinput" placeholder="用户名" type="text" name="uname" id="uname" value="" style='position:absolute;left:50px;'/>
				</div>
			</div>
			

			<div class="js_username">
				<div style='position: relative; margin: 0 auto;'>
					<div style='height: 38px;width: 38px;border: 1px solid #bcbcbc;position: absolute;left: 11px;background-color: #dddddd;'>
						 <img src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/shoppwd.png' style='margin-top:6px;height:25px'>
					</div>
					<input class="upasinput" placeholder="密码"  type="password" name="pwd" id="pwd" value="" style='position:absolute;left:50px;'/>
				</div>			    
			</div>
			
			
			
			<div class="js_username">
			    <div style='position: relative; margin: 0 auto;'>
					<div style='height: 38px;width: 38px;border: 1px solid #bcbcbc;position: absolute;left: 11px;background-color: #dddddd;'>
						 <img src='<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/images/shopcode.png' style='margin-top:6px;height:25px'>
					</div>
					<input name="Captcha" placeholder="验证码" type="text" name="" value="" style='position:absolute;left:50px;width:110px'/>
					<div style="width: 111px;position: absolute;left: 179px;border: 1px solid #bcbcbc;text-align: center;"> 
					    <img title="点击可更换验证码" onclick="javascript:freshcode();" src="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/getCaptcha"),$_smarty_tpl);?>
" id="captchaimg" style="cursor:pointer; width:100px;height:38px;line-height:38px;">					 
				    </div>
				</div>		
			</div>
			 
			<div class='loginbtn'>
				 登 录
			</div>
		</form>
	</div>
		 <script type="text/javascript">
	$('.loginbtn').click(function(){
		
	    subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/shopcenter/index"),$_smarty_tpl);?>
',$('#loginForm'));
	})
	
	$("input").keyup(function(){
        if(event.keyCode == 13){
          subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/shopcenter/index"),$_smarty_tpl);?>
',$('#loginForm'));
        }
    });
	
</script>
	</div>
	
</div>

</body>
</html>
<?php }} ?>