<?php /* Smarty version Smarty-3.1.10, created on 2018-11-14 10:08:10
         compiled from ".\templates\step2.tpl.php" */ ?>
<?php /*%%SmartyHeaderCode:73865beb838a094fe8-06255666%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af7ffefdda2e038f951f24817976d40ead40808d' => 
    array (
      0 => '.\\templates\\step2.tpl.php',
      1 => 1512459126,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '73865beb838a094fe8-06255666',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'server' => 0,
    'info' => 0,
    'filesmod' => 0,
    'items' => 0,
    'is_right' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5beb838a0ee1c9_45276980',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5beb838a0ee1c9_45276980')) {function content_5beb838a0ee1c9_45276980($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.tpl.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div class="body_box">
        <div class="main_box">
            <div class="hd">
            	<div class="bz a2"><div class="jj_bg"></div></div>
            </div>
            <div class="ct">
            	<div class="bg_t"></div>
                <div class="clr">
                    <div class="l"></div>
                    <div class="ct_box nobrd i6v">
                    <div class="nr">
	 <table cellpadding="0" cellspacing="0" class="table_list">
                  <tr>
                    <th class="col1">检查项目</th>
                    <th class="col2">当前环境</th>
                    <th class="col3">建议</th>
                    <th class="col4">功能影响</th>
                  </tr>
                  <tr>
                    <td>操作系统</td>
                    <td><?php echo php_uname();?>
</td>
                    <td>Windows_NT/Linux/Freebsd</td>
                    <td><span><img src="images/correct.gif" /></span></td>
                  </tr>
                  <tr>
                    <td>WEB 服务器</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['server']->value['SERVER_SOFTWARE'];?>
</td>
                    <td>Apache/Nginx/IIS</td>
                    <td><span><img src="images/correct.gif" /></span></td>
                  </tr>
                  <tr>
                    <td>PHP 版本</td>
                    <td>PHP <?php echo phpversion();?>
</td>
                    <td>PHP 5.2.0 及以上</td>
                    <td> 
                    	<?php if ($_smarty_tpl->tpl_vars['info']->value['is_php']!=1){?>
                    	<span><img src="images/correct.gif" /></span> 
                    	<font class="red"><img src="images/error.gif" />&nbsp;无法安装</font>
                    	<?php }?> 
                    	</font></td>
                  </tr>
                  <tr>
                    <td>MYSQL 扩展</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['mysql']){?> √<?php }else{ ?>×<<?php ?>?php <?php }?> </td>
                    <td>必须开启</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['mysql']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;无法安装</font><?php }?></td>
                  </tr>
                  
                  <tr>
                    <td>ICONV/MB_STRING 扩展</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['iconv']){?>√<?php }else{ ?>×<?php }?></td>
                    <td>必须开启</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['iconv']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;无法安装</font><?php }?></td>
                  </tr>
                  
                  <tr>
                    <td>JSON扩展</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['PHP_JSON']){?>√<?php }else{ ?>×<?php }?></td>
                    <td>必须开启</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['PHP_JSON']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;不只持json,<a href="http://pecl.php.net/package/json" target="_blank">安装 PECL扩展</a></font><?php }?></td> 
                  </tr>
                  <tr>
                    <td>GD 扩展</td>
                     <td><?php if ($_smarty_tpl->tpl_vars['info']->value['PHP_GD']){?>√<?php }else{ ?>×<?php }?></td>
                    <td>建议开启</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['PHP_GD']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;不支持缩略图和水印</font><?php }?></td>  
                  </tr>                                    
                    
                                    
                 
				  
				          <tr>
                    <td>fsockopen</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['fsockopen']){?>√<?php }else{ ?>×<?php }?></td>
                    <td>建议打开</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['info']->value['fsockopen']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;不支持fsockopen函数</font><?php }?></td>   
                  </tr>
                  
                  <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['filesmod']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
				          <tr>
                    <td><?php echo $_smarty_tpl->tpl_vars['items']->value['cname'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['items']->value['file'];?>
</td>
                    <td>必须可写</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['items']->value['is_writable']){?><span><img src="images/correct.gif" /></span><?php }else{ ?><font class="red"><img src="images/error.gif" />&nbsp;请修改为可写</font><?php }?></td>   
                  </tr>
                  <?php } ?>
                  
                </table>
 					</div>
                    </div>
                </div>
                <div class="bg_b"></div>
            </div>
            <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a>
            <?php if ($_smarty_tpl->tpl_vars['is_right']->value){?>
            <a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn">下一步</a></div>
            <?php }else{ ?>
			<a onClick="alert('当前配置不满足Phpcms安装需求，无法继续安装！');" class="x_btn pre">检测不通过</a>
 			<?php }?>
			<form id="install" action="index.php?" method="get">
			<input type="hidden" name="step" value="3">
			</form>
        </div>
    </div>
</body>
</html>
<?php }} ?>