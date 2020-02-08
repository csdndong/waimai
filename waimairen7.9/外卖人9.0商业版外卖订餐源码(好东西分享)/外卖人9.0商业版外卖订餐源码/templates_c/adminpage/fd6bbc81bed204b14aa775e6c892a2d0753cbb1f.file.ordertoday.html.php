<?php /* Smarty version Smarty-3.1.10, created on 2019-05-11 11:14:44
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\order\ordertoday.html" */ ?>
<?php /*%%SmartyHeaderCode:76665cd63e24853e16-15184259%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd6bbc81bed204b14aa775e6c892a2d0753cbb1f' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\order\\ordertoday.html',
      1 => 1539078777,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '76665cd63e24853e16-15184259',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'tempdir' => 0,
    'arealist' => 0,
    'items' => 0,
    'frinput' => 0,
    'playwave' => 0,
    'man_ispass' => 0,
    'statustype' => 0,
    'dno' => 0,
    'showdet' => 0,
    'list' => 0,
    'oderinfo' => 0,
    'backarray' => 0,
    'paytypearr' => 0,
    'payway' => 0,
    'buyerstatus' => 0,
    'value' => 0,
    'scoretocost' => 0,
    'itemb' => 0,
    'ordertypearr' => 0,
    'pagecontent' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd63e2521c3d6_07347198',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd63e2521c3d6_07347198')) {function content_5cd63e2521c3d6_07347198($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>订单快速处理系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/commom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/shangjiaAdmin.css" />
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=wmrPopup"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js" type="text/javascript" language="javascript"></script>
<script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"; 
</script>
</head>
<body>
	<div style="position: fixed;top: 0;left: 0;right: 0;bottom: 0;z-index: -1;background:url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/background.png);"></div>
<!---header start--->
<div class="header" style=" height:50px;">
  <div class="top" style=" height:50px;">
   
   
    <div class="topRight fr">  <span style=" height:50px; line-height:50px;cursor: pointer;" class="username" onclick="openlink('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/system/module/index"),$_smarty_tpl);?>
');">返回后台管理<img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/usernameBg.png" /></span> </div>
    <div class="cl"></div>
    <div class="shangjiaTop" style=" top:-22px; background:none;margin-left:-150px;">
      <div class="sjglaa"> </div>
    </div>
  </div>
</div>

<!---header end---> 
	
	
<div class="main">
	
	<div class="main_titile">
	<div class="main_tl">
		<div class="qhaddress fl">
			<select name="firstarea" onchange="dofirch(this);">
      		 		<option value="">选择区域</option> 
      		        <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['arealist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>  
                               <option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['items']->value['id']==$_smarty_tpl->tpl_vars['frinput']->value){?>selected<?php }?>>
                               	   <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['one'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['one']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['name'] = 'one';
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['items']->value['space']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'] = (int)0;
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['one']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['one']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['one']['total']);
?> 
                               	   &nbsp;&nbsp;
                                  <?php endfor; endif; ?>  <?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
 
                               </option>
                                  
                         <?php } ?> 
      		  
      		  </select>
		</div>
		<div  class="auto_time fl">
			<span id="showztai" style="color:#666;"  data="20"> 0 </span>秒
		</div>
		<div class=" shoushua fl" onclick="gorefresh();" style="cursor: pointer;">
			手动刷新
		</div>
		  <div class="closeVoi fl">
        <input type="checkbox" name="playwave" id="playwave" value="1" <?php if ($_smarty_tpl->tpl_vars['playwave']->value!=2){?>checked<?php }?> style="cursor: pointer;">
          播放铃声</div>
	</div>	 
		 <div class="dingdanGl fl">
		 
			<ul>
			<?php if ($_smarty_tpl->tpl_vars['man_ispass']->value==1){?>
				<li <?php if (empty($_smarty_tpl->tpl_vars['statustype']->value)){?>class="on"<?php }?> style="cursor: pointer;"  data="0"><span>待审核</span> </li>
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==1){?>class="on"<?php }?> style="cursor: pointer;" data="1"><span>未确认制作</span> </li>
			<?php }else{ ?>
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==1){?>class="on"<?php }?> style="cursor: pointer;" data="1"><span>未确认制作</span> </li>
			<?php }?>
				
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==2){?>class="on"<?php }?> style="cursor: pointer;" data="2"><span>待发货</span> </li>
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==3){?>class="on"<?php }?> style="cursor: pointer;" data="3"><span>已发货</span> </li>
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==4){?>class="on"<?php }?> style="cursor: pointer;" data="4"><span>已完成</span> </li>
				<li <?php if ($_smarty_tpl->tpl_vars['statustype']->value==5){?>class="on"<?php }?> style="cursor: pointer;" data="5"><span>退款处理</span> </li>
			</ul>
			<div class="cl"></div>
		 </div>
		
		
	</div>
	<div class="cl"></div>
	
	
	<div class="main_ord_list">
		<div style="margin-bottom:15px;">
		<div class="chaxun fl">
		
			<input class="chainp" placeholder="输入订单号" type="text" name="dno" id="dno" value="<?php echo $_smarty_tpl->tpl_vars['dno']->value;?>
" />
			<input class="chaxunhBg" type="button" name="" value="查 询" onclick="gorefresh();" style="cursor: pointer;">
			<div class="cl"></div>
		</div>
		
		<div class="ycOrd fr">
			 
		  
		  <label>
					<input type="checkbox" name="showdet" id="showdet" value="1" <?php if ($_smarty_tpl->tpl_vars['showdet']->value!=1){?>checked<?php }?>>
					  隐藏订单详情
			</label>
			
		</div>
			<div class="cl"></div>
		
		</div>
		
		
		
		
		<div class="order_list_show">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#544e48">
	<th>订单号</th>
	<th>联系人</th>
	<th>联系电话</th>
	  <th>地址</th>
    <th>IP地址</th>
    <th>下单次数</th>&nbsp;&nbsp;
    <th>订单价格</th>
    <th>操作</th>
  </tr>
 <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
 
 <?php if (!empty($_smarty_tpl->tpl_vars['items']->value['shopid'])){?>
 
	  <tr class="orLiheight" align="center" bgcolor="#f0f0f0">
		<td style='min-width:200px;'><?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
<?php if ($_smarty_tpl->tpl_vars['items']->value['is_ziti']==1){?><span style='background: #28b694;color: #fff;padding: 2px 4px;border-radius: 3px;margin-left: 5px;'>自取单</span><?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['is_hand']==0&&$_smarty_tpl->tpl_vars['items']->value['is_ziti']==0){?><span style='background: #33bbee;color: #fff;padding: 2px 4px;border-radius: 3px;margin-left: 5px;'>预订单</span><?php }?></td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['buyername'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['buyerphone'];?>
</td>
		<td style='max-width:450px'> <?php echo $_smarty_tpl->tpl_vars['items']->value['buyeraddress'];?>
</td>
		<td><?php echo preg_replace('/[^\.0123456789]/s','',$_smarty_tpl->tpl_vars['items']->value['ipaddress']);?>
</td>
		  <!--<?php echo preg_replace('/[^\.0123456789]/s','',$_smarty_tpl->tpl_vars['oderinfo']->value['ipaddress']);?>
-->
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['maijiagoumaishu'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['allcost'];?>
</td>
		
		<td style=" color:#076f9c;  font-weight:bold;min-width: 300px;" class="buttd"> 
		  	<?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==0||$_smarty_tpl->tpl_vars['items']->value['is_reback']==3||$_smarty_tpl->tpl_vars['items']->value['is_reback']==5){?> 
				<?php if ($_smarty_tpl->tpl_vars['items']->value['status']==0){?> 
					<?php if ($_smarty_tpl->tpl_vars['items']->value['paytype']==0){?>
					<a class="passorder curbtn" onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/pass/datatype/json"),$_smarty_tpl);?>
"> 审核</a>
					<?php }?>
					<a class=" curbtn"  onclick="unorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paystatus'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paytype'];?>
);" href="#"> 取消</a>
				<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==1){?>
					<?php if ($_smarty_tpl->tpl_vars['items']->value['is_make']==0){?>
						<span>待商家确认</span>
					<?php }elseif($_smarty_tpl->tpl_vars['items']->value['is_make']==1){?> 
						 <a class="sendorder curbtn"  onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/send/datatype/json"),$_smarty_tpl);?>
">发货</a> 
					<?php }elseif($_smarty_tpl->tpl_vars['items']->value['is_make']==2){?>
					  商家不制作该订单
					<?php }?> 
					<?php if ($_smarty_tpl->tpl_vars['items']->value['is_make']==0&&$_smarty_tpl->tpl_vars['items']->value['paytype']==0){?>
				        <a class="sendorder curbtn"  onclick="unorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paystatus'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paytype'];?>
);" href="#">
					    取消</a>
				    <?php }?>
					 
				<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==2){?>
					 <a class="sendorder curbtn" onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/over/datatype/json"),$_smarty_tpl);?>
">
					 完成
					 </a>
					  <?php if ($_smarty_tpl->tpl_vars['items']->value['is_make']==0&&$_smarty_tpl->tpl_vars['items']->value['paytype']==0){?>
					 <a class="sendorder curbtn"  onclick="unorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paystatus'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paytype'];?>
);" href="#">
						 取消
					 </a>
					 <?php }?>
				<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==3){?>
				  <font color=#FBA101><?php if ($_smarty_tpl->tpl_vars['items']->value['is_acceptorder']==1){?>用户已确认收货<?php }else{ ?>用户未确认收货<?php }?></font>
				<?php }else{ ?>
					<a class="sendorder curbtn" onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/del/datatype/json"),$_smarty_tpl);?>
">删除</a>
				<?php }?>
				
				<?php if ($_smarty_tpl->tpl_vars['items']->value['shoptype']==100){?>
					<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['psusername'])){?><?php echo $_smarty_tpl->tpl_vars['items']->value['psusername'];?>
<?php }else{ ?><a onclick="psorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
)" href="javascript:void(0);">选择配送员</a>   <?php }?>
				<?php }?>
			<?php }else{ ?>
			    <?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==2){?>
				    已退款
				<?php }else{ ?>
				    <?php if ($_smarty_tpl->tpl_vars['items']->value['is_make']==2){?>
					商家不制作
					<?php }else{ ?>
						<?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==1){?>
						已申请退款等待平台处理
						<?php }elseif($_smarty_tpl->tpl_vars['items']->value['is_reback']==4){?>已申请退款等待商家处理
						<?php }else{ ?>待处理											
						<?php }?>
					<?php }?>
				<?php }?>
			<?php }?>
	   	  <span class="chakan curbtn" data="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
">查看</span>	</td>
	  </tr>
	  <?php }else{ ?>
	  <tr class="orLiheight" align="center" bgcolor="#f0f0f0">
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
<span style='background: #f60;color: #fff;padding: 2px 4px;border-radius: 3px;margin-left: 5px;'><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?>帮我送<?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==2){?>帮我买<?php }?></span>
		</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['buyername'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['buyerphone'];?>
</font></td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['buyeraddress'];?>
<font color="#f60"><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?><?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==2){?><?php }?></font></td>
		<td><?php echo preg_replace('/[^\.0123456789]/s','',$_smarty_tpl->tpl_vars['items']->value['ipaddress']);?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['maijiagoumaishu'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['items']->value['allcost'];?>
</td>
		 
		  
		  <td style=" color:#076f9c;  font-weight:bold;" class="buttd"><font color=#f60><?php echo $_smarty_tpl->tpl_vars['backarray']->value[$_smarty_tpl->tpl_vars['items']->value['is_reback']];?>
</font>
		  	<?php if ($_smarty_tpl->tpl_vars['items']->value['shoptype']==100){?>
				<?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']>0&&$_smarty_tpl->tpl_vars['items']->value['is_reback']<3){?>
				<?php }else{ ?> 
					<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['psusername'])){?>配送员:<?php echo $_smarty_tpl->tpl_vars['items']->value['psusername'];?>

					<?php }else{ ?><a onclick="psorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
)" href="javascript:void(0);">选择配送员</a>   
					<?php }?>
				<?php }?>
	     	 <?php }?>
			<?php if ($_smarty_tpl->tpl_vars['items']->value['status']==0){?> 
				<?php if ($_smarty_tpl->tpl_vars['items']->value['paytype']==0){?>
				<a class="passorder curbtn" onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/pass/datatype/json"),$_smarty_tpl);?>
">
					   审核
					</a>
				<?php }?>
				
	     <a class=" curbtn"  onclick="unorder(<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['dno'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paystatus'];?>
,<?php echo $_smarty_tpl->tpl_vars['items']->value['paytype'];?>
);" href="#">
	   	     取消
	     	</a>
	     	 
  	      <?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==3){?>
  	          <font color=#FBA101><?php if ($_smarty_tpl->tpl_vars['items']->value['is_acceptorder']==1){?>用户已确认收货<?php }else{ ?>用户未确认收货<?php }?></font>
  	     <?php }else{ ?>
  	        <a class="sendorder curbtn" onclick="return remind(this);" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ordercontrol/id/".((string)$_smarty_tpl->tpl_vars['items']->value['id'])."/type/del/datatype/json"),$_smarty_tpl);?>
">
                 删除
  	         </a>
	    <?php }?>
	   	  <span class="chakan curbtn" data="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
">查看</span>	</td>
		  
		  
		  
	  </tr>
	 

	  
	  
	  <?php }?>
	  
	  
	  <tr> 
	  <td colspan="8">
		<table class="xqOrderlist showdet_<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
"  width="100%" align="center" border="" cellpadding="0" cellspacing="0" bordercolor="#ffffff" >
	 <?php if (!empty($_smarty_tpl->tpl_vars['items']->value['shopid'])&&$_smarty_tpl->tpl_vars['items']->value['shoptype']!=100){?>		
			  <?php if ($_smarty_tpl->tpl_vars['items']->value['is_goshop']==1){?>
			   <tr align="center">
              <td><div><span  style=" color:#076f9c; font-size:14px; ">支付方式: </span><span><?php echo (($tmp = @$_smarty_tpl->tpl_vars['paytypearr']->value[$_smarty_tpl->tpl_vars['items']->value['paytype']])===null||$tmp==='' ? '未定义' : $tmp);?>
(<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['paytype_name'])){?><?php echo $_smarty_tpl->tpl_vars['payway']->value[$_smarty_tpl->tpl_vars['items']->value['paytype_name']];?>
，<?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['paystatus']==1){?>已付<?php }else{ ?>未付<?php }?>)<font color=red><?php echo $_smarty_tpl->tpl_vars['backarray']->value[$_smarty_tpl->tpl_vars['items']->value['is_reback']];?>
</font></span></div></td>
              <td><div><span  style=" color:#076f9c; font-size:14px; ">订单状态: </span><span>
			  <?php if ($_smarty_tpl->tpl_vars['items']->value['status']==1){?>
					等待用户到店消费
				<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==0){?>
					等待处理	
				<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==2||$_smarty_tpl->tpl_vars['items']->value['status']==3){?>
					已完成，用户已消费
				<?php }?>
			  </span></div></td>
              <td colspan="2"><div><span  style=" color:#076f9c; font-size:14px; ">消费时间:</span> <span><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['posttime'],"%Y-%m-%d");?>
 <?php echo $_smarty_tpl->tpl_vars['items']->value['postdate'];?>
</span></div></td>
              
            </tr>
            <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">店铺: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
</span></div></td>
			         <td colspan="3"><div><span  style=" color:#076f9c; font-size:14px; ">店铺联系电话: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopphone'];?>
</span></div></td>
            </tr>
            <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">订单备注: </span></div></td>
			         <td colspan="3"><div><?php echo $_smarty_tpl->tpl_vars['items']->value['content'];?>
</div></td>
            </tr>
			  <?php }else{ ?>
            <tr align="center">
              <td><div><span  style=" color:#076f9c; font-size:14px; ">支付方式: </span><span><?php echo (($tmp = @$_smarty_tpl->tpl_vars['paytypearr']->value[$_smarty_tpl->tpl_vars['items']->value['paytype']])===null||$tmp==='' ? '未定义' : $tmp);?>
(<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['paytype_name'])){?><?php echo $_smarty_tpl->tpl_vars['payway']->value[$_smarty_tpl->tpl_vars['items']->value['paytype_name']];?>
，<?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['paystatus']==1){?>已付<?php }else{ ?>未付<?php }?>)<font color=red><?php echo $_smarty_tpl->tpl_vars['backarray']->value[$_smarty_tpl->tpl_vars['items']->value['is_reback']];?>
</font></span></div></td>
              <td><div><span  style=" color:#076f9c; font-size:14px; ">订单状态: </span><span <?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==1||$_smarty_tpl->tpl_vars['items']->value['is_reback']==4){?> style='color:red'<?php }?>><?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==1||$_smarty_tpl->tpl_vars['items']->value['is_reback']==4){?>退款中待<?php if ($_smarty_tpl->tpl_vars['items']->value['is_reback']==1){?>平台<?php }else{ ?>商家<?php }?>处理<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['buyerstatus']->value[$_smarty_tpl->tpl_vars['items']->value['status']];?>
<?php }?></span></div></td>
              <td colspan="2"><div style="text-align:left"><span  style=" color:#076f9c; font-size:14px;">配送时间:</span> <span><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['posttime'],"%Y-%m-%d");?>
 <?php echo $_smarty_tpl->tpl_vars['items']->value['postdate'];?>
</span></div></td>
              
            </tr>
            <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">店铺: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopname'];?>
</span></div></td>
                <td ><div></div></td>
			         <td colspan="2"><div  style="text-align:left"><span  style=" color:#076f9c; font-size:14px; ">店铺联系电话: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopphone'];?>
</span></div></td>
            </tr>
            <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">订单备注: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['content'];?>
</span></div></td>
                <td ><div></div></td>
			         <td colspan="2"><div  style="text-align:left"><span  style=" color:#076f9c; font-size:14px;">配送方式: </span><span><?php if ($_smarty_tpl->tpl_vars['items']->value['pstype']==2){?>配送宝<?php }else{ ?>店铺<?php }?></span></div></td>
            </tr>
       <?php }?>
        <?php if (!empty($_smarty_tpl->tpl_vars['items']->value['detlist'])){?>
		         	<tr><td colspan="4" style="height:20px;"></td></tr>
		         	<tr>
                <td style=" color:#333; font-size:14px; font-weight:bold;">美食篮子</td>
                <td style=" font-size:14px; color:#333;font-weight:bold;font-family:'微软雅黑';"  align="center">单价</td>
                  <td style=" font-size:14px; color:#333;font-weight:bold; font-family:'微软雅黑';"  align="center" align="center">
                数量
                  </td>
                  <td style=" font-size:14px; color:#333;font-weight:bold; font-family:'微软雅黑';"  align="center" align="center">
                总价
                  </td>
              </tr>
              
              <?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value['detlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>   
              <tr>
                  <td  style=" color:#333; font-size:14px; font-weight:bold;" ><?php echo $_smarty_tpl->tpl_vars['value']->value['goodsname'];?>
<?php if ($_smarty_tpl->tpl_vars['value']->value['is_send']==1){?><font color=red>[赠品]</font><?php }?></td>
                  <td style=" font-size:14px; color:#333; font-weight:bold; "  align="center"><?php echo $_smarty_tpl->tpl_vars['value']->value['goodscost'];?>
</td>
                  <td style=" font-size:14px; color:#333; font-weight:bold; "  align="center" align="center"> <?php echo $_smarty_tpl->tpl_vars['value']->value['goodscount'];?>
 </td>
                  <td style=" font-size:14px; color:#333; font-weight:bold;"  align="center" align="center">  <?php echo $_smarty_tpl->tpl_vars['value']->value['goodscost']*$_smarty_tpl->tpl_vars['value']->value['goodscount'];?>
  </td>
              </tr> 
             <?php } ?>    
             <?php }?>
              <tr>
                <td  colspan=" 4" >
                	
                	 <?php if ($_smarty_tpl->tpl_vars['items']->value['shopcost']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" >商品总价：</span> <?php echo $_smarty_tpl->tpl_vars['items']->value['shopcost'];?>
   &nbsp;&nbsp;&nbsp;   
		            		 		<?php }?>
		            		 		 <?php if ($_smarty_tpl->tpl_vars['items']->value['shopps']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" > 配送费：</span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopps'];?>
  &nbsp;&nbsp;&nbsp;   
		            		 		<?php }?> 
		            		 		<?php if ($_smarty_tpl->tpl_vars['items']->value['scoredown']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" >积分低扣：</span>-<?php echo $_smarty_tpl->tpl_vars['items']->value['scoredown']/$_smarty_tpl->tpl_vars['scoretocost']->value;?>
&nbsp;&nbsp;&nbsp; 
		            		 		<?php }?>
		            		 		<?php if ($_smarty_tpl->tpl_vars['items']->value['yhjcost']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" >优惠券低扣：</span>-<?php echo $_smarty_tpl->tpl_vars['items']->value['yhjcost'];?>
&nbsp;&nbsp;&nbsp; 
		            		 		<?php }?>
									<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['cxdet'])){?>
									<?php  $_smarty_tpl->tpl_vars['itemb'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itemb']->_loop = false;
 $_smarty_tpl->tpl_vars['keyb'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['items']->value['cxdet']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itemb']->key => $_smarty_tpl->tpl_vars['itemb']->value){
$_smarty_tpl->tpl_vars['itemb']->_loop = true;
 $_smarty_tpl->tpl_vars['keyb']->value = $_smarty_tpl->tpl_vars['itemb']->key;
?>
									<span style=" color:#076f9c; font-size:14px; font-weight:bold;" ><?php echo $_smarty_tpl->tpl_vars['itemb']->value['name'];?>
：</span>-<?php echo $_smarty_tpl->tpl_vars['itemb']->value['downcost'];?>

									<?php } ?>
									<?php }?>
		            		 		<!--<?php if ($_smarty_tpl->tpl_vars['items']->value['cxcost']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" >店铺促销：</span>-<?php echo $_smarty_tpl->tpl_vars['items']->value['cxcost'];?>
&nbsp;&nbsp;&nbsp;
		            		 		<?php }?>-->
		            		 		<?php if ($_smarty_tpl->tpl_vars['items']->value['bagcost']>0){?>
		            		   	<span style=" color:#076f9c; font-size:14px; font-weight:bold;" >打包费：</span><?php echo $_smarty_tpl->tpl_vars['items']->value['bagcost'];?>
&nbsp;&nbsp;&nbsp;
		            		 		<?php }?>
                	      <span style=" color:#076f9c; font-size:14px; font-weight:bold;" >应收金额：</span><?php echo $_smarty_tpl->tpl_vars['items']->value['allcost'];?>
</td> 
              </tr> 
<?php }else{ ?>

			<tr align="center">
              <td><div><span  style=" color:#076f9c; font-size:14px; ">下单时间: </span><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['addtime'],"%Y-%m-%d %H:%M:%S");?>
</div></td>
              <td><div><span  style=" color:#076f9c; font-size:14px; ">订单状态: </span><span><?php if ($_smarty_tpl->tpl_vars['items']->value['status']==1){?>已审核<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==2){?>已发货<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==3){?>已完成<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==4){?>买家已取消订单<?php }elseif($_smarty_tpl->tpl_vars['items']->value['status']==5){?>卖家已取消订单<?php }else{ ?>待处理<?php }?></span></div></td>
              <td><div><span  style=" color:#076f9c; font-size:14px; "><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?>取货时间<?php }?><?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==2){?>收货时间<?php }?>: </span><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['items']->value['posttime'],"%Y-%m-%d");?>
 <?php echo $_smarty_tpl->tpl_vars['items']->value['postdate'];?>
</div></td>
            
            </tr>
			  <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">支付方式: </span><span><font color=red><?php echo (($tmp = @$_smarty_tpl->tpl_vars['paytypearr']->value[$_smarty_tpl->tpl_vars['items']->value['paytype']])===null||$tmp==='' ? '未定义' : $tmp);?>
<?php if (!empty($_smarty_tpl->tpl_vars['items']->value['paytype_name'])){?>(<?php echo $_smarty_tpl->tpl_vars['payway']->value[$_smarty_tpl->tpl_vars['items']->value['paytype_name']];?>
)<?php }?></font></span></div></td>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">支付状态: </span><span><?php if ($_smarty_tpl->tpl_vars['items']->value['paystatus']==1){?>已付<?php }else{ ?>未付<?php }?><font color=red><?php echo $_smarty_tpl->tpl_vars['backarray']->value[$_smarty_tpl->tpl_vars['items']->value['is_reback']];?>
</font></span></div></td>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">下单来源: </span><span><?php echo $_smarty_tpl->tpl_vars['ordertypearr']->value[$_smarty_tpl->tpl_vars['items']->value['ordertype']];?>
</span></div></td>
			     
            </tr>
			 
			
			  <tr style="height:20px;line-height:20px;">
		            <td ><div><span  style=" color:#076f9c; font-size:14px; ">取货地址: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopaddress'];?>
</span></div></td>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">收货地址: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['buyeraddress'];?>
</span></div></td>
		        <?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?>
				<td ><div><span  style=" color:#076f9c; font-size:14px; ">物品类型: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['movegoodstype'];?>
</span></div></td>
                <?php }?>     	
		            </tr>
				<tr style="height:20px;line-height:20px;">
		             <?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?><td ><div><span  style=" color:#076f9c; font-size:14px; ">取货电话: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['shopphone'];?>
</span></div></td><?php }?>
		             	<td ><div><span  style=" color:#076f9c; font-size:14px; ">收货电话: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['buyerphone'];?>
</span></div></td>
		             	<td ><div><span  style=" color:#076f9c; font-size:14px; ">联系人: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['buyername'];?>
</span></div></td>
		            </tr>
			
			<?php if ($_smarty_tpl->tpl_vars['items']->value['pttype']==1){?>
			  <tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">总重量: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['ptkg'];?>
公斤</span></div></td>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">公斤金额: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['allkgcost'];?>
元</span></div></td>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">物品价值: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['movegoodscost'];?>
</span></div></td>
            
			 </tr> 
			 
			<?php }?>
			<tr>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">距离: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['ptkm'];?>
千米</span></div></td>             
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">千米金额: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['allkmcost'];?>
元</span></div></td>
                <?php if ($_smarty_tpl->tpl_vars['items']->value['farecost']!=0){?><td ><div><span  style=" color:#076f9c; font-size:14px; ">小费: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['farecost'];?>
元</span></div></td><?php }?>
                <td ><div><span  style=" color:#076f9c; font-size:14px; ">里程总金额: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['allcost'];?>
元</span></div></td>
             
            </tr>
            <tr>
                <td colspan="4" ><div><span  style=" color:#076f9c; font-size:14px; ">跑腿需求: </span><span><?php echo $_smarty_tpl->tpl_vars['items']->value['content'];?>
</span></div></td>
			     
            </tr>

<?php }?>			  
			  

          </table>
		 </td>
	  </tr>
	  <tr class="noord"><td colspan="8"></td></tr>
	 <?php } ?> 
   </table>
		
		</div>
	<div class="blank20"></div> 
 		 <center>
	<div class="blank20"></div> 
		
		 <div class="page_newc" style="    margin-top: 50px;">
                 	    
                       <div class="show_page"><ul><?php echo $_smarty_tpl->tpl_vars['pagecontent']->value;?>
</ul></div>
                 </div>
                <div class="blank20"></div>
		
		 </center>
		
		
		
	 
	</div>
	
	
		
		
</div>
	
	
	
	
	
	
	
	
	  <div id="palywave" style="display:none;"></div>
 
	 
 <script>
   
 	<?php if ($_smarty_tpl->tpl_vars['showdet']->value!=1){?>
 	   $('.xqOrderlist').hide();
 	<?php }?>
 	<?php if ($_smarty_tpl->tpl_vars['playwave']->value!=2){?> 
 	var playwave = true;  	
 	<?php }else{ ?>
 		var playwave = false;  	
 	<?php }?>
		$(function(){
			//$(document.body).height()
			 var newiheight = Number($(window).height())- 300;
			$('.main').css('min-height',newiheight+'px');
			 $("input[name='playwave']").click(function(){
			 	   if($(this).is(':checked') == true){
			 	   	   playwave =   true;
						  ajaxback('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/wavecontrol/type/openwave/datatype/json"),$_smarty_tpl);?>
',''); 
			 	   }else{
			 	   		  playwave = false;
					    	ajaxback('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/wavecontrol/type/closewave/datatype/json"),$_smarty_tpl);?>
',''); 
			 	   } 
						
				});	 
			$(".chakan").click(function(){
			
				$(".showdet_"+$(this).attr('data')).toggle();
			});
			$("input[name='showdet']").click(function(){
				   if($(this).is(':checked') == true){
				   	$('.xqOrderlist').hide();
				  }else{
				  	$('.xqOrderlist').show();
				  }
			});
			$('.dingdanGl li').click(function(){
				   $(this).addClass('on').siblings().removeClass('on');
				   gorefresh();
			});
		});
	function gorefresh(){
		var statustype = $('.dingdanGl').find('.on').eq(0).attr('data');
		var dno = $('#dno').val();
		var showdet =   $("input[name='showdet']").is(':checked') == true?0:1;
		var firstarea =$("select[name='firstarea']").find("option:selected").val();
		var oplink = siteurl+'/index.php?ctrl=adminpage&action=order&module=ordertoday&statustype='+statustype+'&dno='+dno+'&showdet='+showdet+'&firstarea='+firstarea;
	    window.location.href=oplink;
	}
		 
 </script>
 <script type="text/javascript">
   function colorred(obj){
   	$(obj).css('background','#eee'); 
   }
   function tcolorred(obj){
   	$(obj).css('background','');
   }
    function unorder(orderid,dno,paystatus,paytype)
	 {
         if(paystatus==1 && paytype !=0){
             alert("在线支付订单请通过退款处理");
             return false;
         }
	 	   var htmls = '<div class="replayask">';
	 	   htmls +='<table border=0 width="250">';
        htmls +='<tbody>';
        htmls +='<tr> ';
        htmls +='<td style="border:none;text-align:left;"><textarea style="width:100%;height:100px;color:#ddd;" name="reason" id="reason" placeholder="关闭理由">关闭理由</textarea></td> </tr> '; 
       htmls +='<tr>   <td style="border:none;"><input type="checkbox" value="1" name="suresend" id="suresend">发送关闭理由给买家手机</td></tr>';
        htmls +='<tr>   <td style="border:none;"><a href="#" class="button fr saveImgInfo" style="margin-right:10px;" onclick="sureclose('+orderid+');">提交关闭</a></td>';
        htmls +='  </tr>  </tbody> </table> </div> '; 

	 	   
	 	   var dialog =  art.dialog({
	 	   	id:'coslid',
        title:'取消订单'+dno,
           content: htmls
        });
	 }
	  $('#reason').live("click", function() {   
 	 var checka = $(this).attr('placeholder');
 	 var checkb = $(this).val();
 	 if(checka == checkb){
 	    $(this).val('');
 	    $(this).css('color','#333');
 	 }
 });
 $('#reason').blur(function(){
 	     var checka = $(this).attr('placeholder');
 	    var checkb = $(this).val();
 	    if(checka == checkb){
 	      $(this).css('color','#ddd');
 	    }else{
 	       if(checkb == ''){
 	          $(this).val(checka);
 	           $(this).css('color','#ddd');
 	       }else{
 	       	$(this).css('color','#333');
 	      }
 	    }
 	    
  });

	 function sureclose(orderid)
	 {
	 	  var reasons = $('#reason').val();
	 	  var suresend = $("input[name='suresend']:checked").val();
	 	  if(reasons == undefined || reasons == '')
	 	  {
	 	  	alert('关闭理由不能为空');
	 	  	return false;
	 	  } 
	 	  if(reasons == $('#reason').attr('placeholder')){
	 	     alert('录入关闭理由');
	 	     return false;
	 	  }
	 	    var url = siteurl+'/index.php?ctrl=adminpage&action=order&module=ordercontrol&type=un&id='+orderid+'&reasons='+reasons+'&suresend='+suresend+'&datatype=json&random=@random@';
	 	    $.ajax({
     type: 'get',
     async:false, 
     url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {  
     	if(content.error == false){
     		diasucces('操作成功','');
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
	 var mydialog;
	 function psorder(orderid,dno){
	   //审核订单
	   mydialog = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=psuser&module=selectps&orderid='+orderid,{height:'550px',width:'850px'},false); 
	 	 mydialog.title('设置配送员'); 
	 	  
	 }
	  function selectdo(msg){
		diasucces(msg,'');
}
 </script>
<script>
function openlink(newlinkes){
					window.location.href=newlinkes;
}
function dofirch(obj){
	gorefresh(); 
}
  
 
</script> 
<!--加载声音-->
<script>
	$(function(){
	setTimeout("get_status()",1000); 	
 
});
function get_status(){//<span id="timeshow" data="20" style="color:#666;"></div>
	//alert('xxx');
	//firstarea
	//secarea
	
	var timeaction =  $('#showztai').attr('data');
	if(timeaction == 0){  
      $.ajax({
     type: 'get',
     async:false,
     data:{firstarea:'<?php echo $_smarty_tpl->tpl_vars['frinput']->value;?>
'},
     url: '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ajaxcheckorder/datatype/json"),$_smarty_tpl);?>
', 
     dataType: 'json',success: function(content) {  
     	  if(content.error == false){
     		  //  播放声音 文件 diasucces('操作成功','');
     		  	palywav();
     	  }else{ 
     			// location.reload();  
     			$('#showztai').attr('data',20); 
     			setTimeout("get_status()",1000); 
     			location.reload(); 	
       	}
		  },
      error: function(content) { 
     	  //location.reload();  
     	  $('#showztai').attr('data',20);
     	  setTimeout("get_status()",1000); 	
	    }
     }); 		
      	 
  }else{
 	var nowtime = Number(timeaction)-1;
 	$('#showztai').attr('data',nowtime);
 	$('#showztai').text(''+nowtime+'');
 	setTimeout("get_status()",1000); 	
 	
  }
}
function palywav(){
	if(playwave == true){
if(navigator.userAgent.indexOf("Chrome") > -1){  
$('#palywave').html('<audio src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" autoplay=”autoplay” hidden="true"></audio>');
}else if(navigator.userAgent.indexOf("Firefox")!=-1){  
$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" hidden="true" loop="false" mastersound></embed>');
}else if(navigator.appName.indexOf("Microsoft Internet Explorer")!=-1 && document.all){ 
$('#palywave').html('<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"><param name="AutoStart" value="1" /><param name="Src" value="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" /></object>');
}else if(navigator.appName.indexOf("Opera")!=-1){ 
$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mpeg" loop="false"></embed>');
}else{ 
$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" hidden="true" loop="false" mastersound></embed>'); 
} 
}

  // $('#palywave').html('<embed id=cct src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/wave.wav" loop="0" autostart="true" hidden="true"></embed>'); 
   setTimeout("playon()",5000); 	
}
function playon(){
	 
 	location.reload();  
}
</script>
<style>
.buttd a{margin-left:2px;}
.dingdanGl li{width:15%!important;}
.show_page  a {
color:#0076cf;
  padding:10px 20px;background:#d1cfd6;
}.show_page .current{background:#ff6500!important;color:#fff!important;}
</style>
</body>

</html><?php }} ?>