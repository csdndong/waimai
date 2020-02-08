<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 22:02:42
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\shop\showshopdetail.html" */ ?>
<?php /*%%SmartyHeaderCode:243425cd584827b8182-35958264%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71f7e882dc7b342ae0632777ce34769f40a93ae6' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\shop\\showshopdetail.html',
      1 => 1536024620,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '243425cd584827b8182-35958264',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'tempdir' => 0,
    'shopinfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd584829b7049_36847085',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd584829b7049_36847085')) {function content_5cd584829b7049_36847085($_smarty_tpl) {?><html xmlns="http://www.w3.org/1999/xhtml"><head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<meta http-equiv="Content-Language" content="zh-CN"> 
<meta content="all" name="robots"> 
<meta name="description" content=""> 
<meta content="" name="keywords"> 
<title>店铺标签选择</title> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/admin.css">
 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js" type="text/javascript" language="javascript"></script>  
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue"></script>
</head>
<body style="background:none;height:650px;overflow:scroll;width:800px;">
	 <style>
	 .hc_login_div{ padding:5px 0px;}
	 .hc_login_div img{width:80px; height:50px;}
	 </style>
	  <div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>申请店铺名称：</span>
						<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['shopname'];?>

						<p class="tip2"></p>
					</div>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>申请联系电话：</span>
						<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['maphone'];?>

					</div>		
					
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>申请联系地址：</span> 
							<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['address'];?>

					</div>
						
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>申请类型：</span> 
							<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==0){?>外卖<?php }else{ ?>超市<?php }?>
					</div>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>入驻单位：</span> 
							<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['ruzhutype']==1){?>企业入驻<?php }else{ ?>个人入驻<?php }?>
					</div>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>申请处理状态：</span> 
							<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['is_pass']==0){?>处理中<?php }else{ ?>通过审核<?php }?>
					</div>
					
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>营业执照：</span> 
						<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['shoplicense'])){?>		<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['shoplicense'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					
					<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['ruzhutype']==1){?>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>企业执照：</span> 
						<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['qiyeimg'])){?>		<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['qiyeimg'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>
					 
					<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['zmimg'])){?>	
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>身份证正面：</span> 
						<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['zmimg'])){?>		<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['zmimg'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>
					<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['fmimg'])){?>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>身份证反面：</span> 
							<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['fmimg'])){?>	<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['fmimg'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>
					
					<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['foodtongimg'])){?>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>食品流通证：</span> 
							<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['foodtongimg'])){?>	<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['foodtongimg'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>
					<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['jkzimg'])){?>		
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>健康证：</span> 
						<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['jkzimg'])){?>		<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['jkzimg'];?>
" /><?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['ruzhutype']==1){?>
					<div class="hc_login_div">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>商家授权书：</span> 
						<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['sqimg'])){?>	<img src="<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['sqimg'];?>
" />  <?php }else{ ?> 无	<?php }?>
					</div>
					<?php }?>	

					<?php if (!empty($_smarty_tpl->tpl_vars['shopinfo']->value['qtshuoming'])){?>
					<div class="hc_login_div" style="margin-bottom:30px;">
						<span class="hc_login_div_span hc_login_zhuce_margin"><span class="hc_login_div_span_span">*</span>其它说明：</span> 
							<?php echo $_smarty_tpl->tpl_vars['shopinfo']->value['qtshuoming'];?>

					</div>
                    <?php }?>	
					
						<script>
						$(function(){
							$(".hc_login_div img").click(function(){
								//alert( $(this).attr('src') );
								var srcimg =  $(this).attr('src');
								$("#mask").show();		
								$("#showbigimg").show();	
								$("#showbigimg img").attr('src',srcimg);
						
								var imgwidth  =	$("#showbigimg img").width()	;
								var imgheight  =$("#showbigimg img").height()	;
								var marginleft = Number(imgwidth)/2;
								var margintop = Number(imgheight)/2;
								$("#showbigimg").css('margin-top',-margintop+'px');
								$("#showbigimg").css('margin-left',-marginleft+'px');
								
							});
						
																
							$("#mask").click(function(){
								$("#mask").hide();
								$("#showbigimg").hide();
							});
							
						});
					</script>
<style>
#showbigimg{position:fixed; z-index:9999; top:50%; left:50%;}
#shenhaitd img{ cursor:pointer; width:50px; height:50px;}
#mask {
  background: none repeat scroll 0 0 #000000;
  height: 100%;
  opacity: 0.2;
  filter: alpha(opacity=20);
  position: fixed;
  width: 100%;
  z-index: 666;
  left: 0px;
  top: 0px;
  display: none;
}
</style>
					
	<div  id="showbigimg">
<img src="" />
</div>
<div id="mask" style=""></div>				
</body>
</html><?php }} ?>