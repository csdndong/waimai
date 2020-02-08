<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 22:02:51
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\shopcenter\index.html" */ ?>
<?php /*%%SmartyHeaderCode:152955cd5848b65ba62-94352024%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '91c42fb0ff60648ade491c4d67951c753d2a2f54' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\shopcenter\\index.html',
      1 => 1538187010,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152955cd5848b65ba62-94352024',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sitename' => 0,
    'keywords' => 0,
    'description' => 0,
    'siteurl' => 0,
    'tempdir' => 0,
    'adminshopid' => 0,
    'logo' => 0,
    'shopinfo' => 0,
    'info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd5848b8fa940_69294097',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5848b8fa940_69294097')) {function content_5cd5848b8fa940_69294097($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>商家管理中心-<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title>
<meta name="Keywords" content="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" />  
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/css/bootstrap.css" />
<!--<link rel="stylesheet" href="css/bootstrap-responsive.css" />-->
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/css/font-awesome.min.css" />
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/css/normalize.css" />
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/css/index.css" />
 
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/js/jquery.min.js" ></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.cookie.js" ></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/js/bootstrap.js" ></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/js/index.js" ></script>
<style>
html,
body{width: 100%;height: 100%;}
.subnavaA a{color: #fe5722!important;background-color: #f5f5f5!important;}
</style>
</head>
<body>
<script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"; 
</script>
<?php echo smarty_function_load_data(array('assign'=>"shopinfo",'table'=>"shop",'where'=>"`id`=".((string)$_smarty_tpl->tpl_vars['adminshopid']->value),'type'=>"one"),$_smarty_tpl);?>
 
<!------------------------菜单导航开始------------------------>
<div class="index_content">
	<div class="index_left navbar navbar-inverse navbar-fixed-top">
		<div class="index_nav col-sm-5">
			<div class="navbar-header">
				<a class="navbar-brand" href="javascript:;">
					<img src="<?php echo $_smarty_tpl->tpl_vars['logo']->value;?>
"  style='height: 40px;border-radius: 40px;'/>
				</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav nav_top">					 
					<li class="onebtn open" data='useredit'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_home"></i>店铺设置</a>
		                <ul class="dropdown-menu">
		                    <li class='subnavaA' data='useredit'><a href="javascript:;">基本设置</a></li>
 							<li <?php if (empty($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype'])){?>data='usershopfast'<?php }else{ ?>data='usershopmarket'<?php }?>><a href="javascript:;">其它设置</a></li>
							 
		                    <li data='zitiset'><a href="javascript:;">自提设置</a></li>
		                    <li data='usershopnotice'><a href="javascript:;">店铺公告</a></li>
							<li data='usershopinstro'><a href="javascript:;">店铺介绍</a></li>							
							<li data='usershopreal'><a href="javascript:;">商家实景</a></li>
		                </ul>
					</li>
					
					
				 <li class='onebtn' <?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==0){?>data='goodslist'<?php }else{ ?>data='marketgoodslist'<?php }?> >
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_count"></i>商品管理</a>
		                <ul class="dropdown-menu"></ul>
					</li>
					
					 
					<li class='onebtn' data='shoporderlist'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_assets"></i>订单管理</a>
		                <ul class="dropdown-menu">
		                    <li  class='subnavaA' data='shoporderlist'><a href="javascript:;">订单查询</a></li>
							<li data='draworderset'><a href="javascript:;">退款订单管理</a></li>
							<li data='fastfood'><a href="javascript:;">快速下单</a></li>
							<li data='shoptotal'><a href="javascript:;">订单统计</a></li>
							<li data='txlog'><a href="javascript:;">商家金额纪录</a></li>
							<li data='shopjs'><a href="javascript:;">商家结算记录</a></li>
		                </ul>
					</li>
					
				<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==0){?>
					<?php echo smarty_function_load_data(array('assign'=>"info",'table'=>"shopfast",'type'=>"one",'where'=>"shopid = '".((string)$_smarty_tpl->tpl_vars['shopinfo']->value['id'])."'"),$_smarty_tpl);?>

  					<?php if ($_smarty_tpl->tpl_vars['info']->value['is_hui']==1){?>	 
 					<li class='onebtn' data='setshophui'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_distribution"></i>闪惠管理</a>
		                <ul class="dropdown-menu">
		                    <li  class='subnavaA' data='setshophui'><a href="javascript:;">闪惠设置</a></li>
							<li data='shophuiorder'><a href="javascript:;">闪惠订单查询</a></li>
							<li data='shophuitotal'><a href="javascript:;">闪惠订单统计</a></li>
		                </ul>
					</li>
				<?php }?>	
				<?php }?>	
				
				<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==1){?>
					<?php echo smarty_function_load_data(array('assign'=>"info",'table'=>"shopmarket",'type'=>"one",'where'=>"shopid = '".((string)$_smarty_tpl->tpl_vars['shopinfo']->value['id'])."'"),$_smarty_tpl);?>

  					<?php if ($_smarty_tpl->tpl_vars['info']->value['is_hui']==1){?>	 
 					<li class='onebtn' data='setshophui'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_distribution"></i>闪惠管理</a>
		                <ul class="dropdown-menu">
		                    <li  class='subnavaA' data='setshophui'><a href="javascript:;">闪惠设置</a></li>
							<li data='shophuiorder'><a href="javascript:;">闪惠订单查询</a></li>
							<li data='shophuitotal'><a href="javascript:;">闪惠订单统计</a></li>
		                </ul>
					</li>
				<?php }?>	
				<?php }?>	
					
					
					<li class='onebtn' data='cxrule'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_marketing"></i>营销管理</a>
						<ul class="dropdown-menu">
		                    <li  class='subnavaA' data='cxrule'><a href="javascript:;">促销列表</a></li>
							<li data='sptpclist'><a href="javascript:;">海报列表</a></li>
		                </ul>
					</li>
					<li class='onebtn' data='comment'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_setup"></i>评价管理</a>
		                <ul class="dropdown-menu"></ul>
					</li> 
					
					<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==0){?>
					<?php echo smarty_function_load_data(array('assign'=>"info",'table'=>"shopfast",'type'=>"one",'where'=>"shopid = '".((string)$_smarty_tpl->tpl_vars['shopinfo']->value['id'])."'"),$_smarty_tpl);?>

					<?php if ($_smarty_tpl->tpl_vars['info']->value['sendtype']==1){?>	 
					<li class='onebtn' data='shoppsset'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_member"></i>配送设置</a>
		                <ul class="dropdown-menu">
		                    <li  class='subnavaA' data='shoppsset'><a href="javascript:;">配送设置</a></li>
		                </ul>
					</li> 
					<?php }?>
					<?php }?> 
					<?php if ($_smarty_tpl->tpl_vars['shopinfo']->value['shoptype']==1){?>
					<?php echo smarty_function_load_data(array('assign'=>"info",'table'=>"shopmarket",'type'=>"one",'where'=>"shopid = '".((string)$_smarty_tpl->tpl_vars['shopinfo']->value['id'])."'"),$_smarty_tpl);?>

					<?php if ($_smarty_tpl->tpl_vars['info']->value['sendtype']==1){?>	 
					<li class='onebtn' data='shoppsset'>
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="icon_member"></i>配送设置</a>
		                <ul class="dropdown-menu">
		                    <li  class='subnavaA' data='shoppsset'><a href="javascript:;">配送设置</a></li>
		                </ul>
					</li> 
					<?php }?>
					<?php }?>
					
				</ul>
				<ul class="nav navbar-nav">
					
					<!--<li><a href="javascript:;"><i class="icon_setup"></i>设置</a></li>-->
					<li>
						<div class="function_content clearfix">
							<div class="function_box clearfix">
								 
								<div class="function_order pull-left hidden-xs">
									 
									<div class="function_order_list hidden">
										<small class="hidden">暂无订单消息~</small>
										<ul class="">
											<li class="clearfix"><i></i><span>有新订单待处理</span><a href="javascript:;">查看</a></li>
											<li class="clearfix"><i></i><span>有退款订单待处理</span><a href="javascript:;">查看</a></li>
										</ul>
									</div>
								</div>
								<div class="function_user pull-left">
									<img class="hidden-xs" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/newshopcss/images/userimg.png" />
									<div class="function_user_list hidden">
										<ul>
											<!--<li><a href="javascript:;" data-toggle="modal" data-target=".modifypassword"><i class="fa fa-edit"></i>修改密码</a></li>-->
											<li><a href="javascript:;" data-toggle="modal" data-target=".quitsignin"><i class="fa fa-sign-out"></i>退出登录</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<script>
		$('.hidden-xs').hover(function() {
			 $('.function_user_list').show();
		}, function() {
			 setTimeout('hidess()',3000);
			 
		});
		function hidess(){
		    $('.function_user_list').hide();
		} 
		</script>
		<div class="index_navtit col-sm-7 hidden-xs">
			<ul>			 
				<li data='useredit' class="navaA"><h3>店铺设置</h3></li>
				<li data='goodslist'><h3>商品管理</h3></li>
				<li data='marketgoodslist'><h3>商品管理</h3></li>
				<li data='shoporderlist'><h3>订单管理</h3></li>				 
				<li data='setshophui'><h3>闪惠管理</h3></li>
				<li data='cxrule'><h3>营销管理</h3></li>
				<li data='comment'><h3>评价管理</h3></li>
				<li data='shoppsset'><h3>配送设置</h3></li>
			</ul>
		</div>
	</div>
	<div class="visible-xs" style="height: 50px;"></div>
	<div class="index_right_head left">
		<div class="index_right_head_title hidden">
			<ol class="breadcrumb">
				<li class="navaA"><a href="javascript:;">Home</a></li>
				<li><a href="javascript:;">2013</a></li>
				<li><a href="javascript:;">十一月</a></li>
			</ol>
		</div>
		<div class="index_right_head_subnav">
			<ul class="list-inline text-center">
				<li class="col-sm-1 col-xs-3 navaA"><a class='yxmcl' href="javascript:;">基本设置</a></li> 
				 				
			</ul>
		</div>
	</div>
	<div class="index_right left">
		<iframe id="index_iframe" width="100%" height="100%" frameborder="no" border="0" src=""></iframe>
	</div>
</div>
<!------------------------菜单导航结束------------------------>
<div class="modal fade quitsignin" tabindex="-1" role="dialog" aria-labelledby="quitsignintit" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="quitsignintit">退出登录</h4>
			</div>
			<form class="form-horizontal" role="form">
				<div class="modal-body text-center">
					<h2>确定退出登录吗？</h2>
					<small></small>
				</div>
				<div class="modal-footer">
					<button type="button"  class="btn btn-primary">保存</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
    $('.btn-primary').click(function(){
        window.location.href=siteurl+'/index.php?ctrl=member&action=loginout&type=shop';
	})
</script>


<div class="modal fade modifypassword" tabindex="-1" role="dialog" aria-labelledby="modifypasswordtit" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modifypasswordtit">修改密码</h4>
			</div>
			<form class="form-horizontal" role="form">
				<div class="modal-body">
					<div class="form-group">
						<div class="col-sm-1 hidden-xs"></div>
						<label class="col-sm-2 col-xs-4 control-label"><i>*</i>原密码:</label>
						<div class="col-sm-7 col-xs-8">
							<input class="form-control" type="text" placeholder="请输入现在使用的密码" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-1 hidden-xs"></div>
						<label class="col-sm-2 col-xs-4 control-label"><i>*</i>设置新密码:</label>
						<div class="col-sm-7 col-xs-8">
							<input class="form-control" type="text" placeholder="6-20位数字或字母" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-1 hidden-xs"></div>
						<label class="col-sm-2 col-xs-4 control-label"><i>*</i>确认新密码:</label>
						<div class="col-sm-7 col-xs-8">
							<input class="form-control" type="text" placeholder="请输入新密码" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary">保存</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>
<?php }} ?>