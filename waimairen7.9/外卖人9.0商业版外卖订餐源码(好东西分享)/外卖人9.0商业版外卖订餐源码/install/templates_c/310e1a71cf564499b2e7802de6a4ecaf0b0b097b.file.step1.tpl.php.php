<?php /* Smarty version Smarty-3.1.10, created on 2018-11-14 10:08:07
         compiled from ".\templates\step1.tpl.php" */ ?>
<?php /*%%SmartyHeaderCode:161815beb8387617ef4-22193867%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '310e1a71cf564499b2e7802de6a4ecaf0b0b097b' => 
    array (
      0 => '.\\templates\\step1.tpl.php',
      1 => 1512459126,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '161815beb8387617ef4-22193867',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'licenset' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5beb8387648df1_64692237',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5beb8387648df1_64692237')) {function content_5beb8387648df1_64692237($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.tpl.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div class="body_box">
        <div class="main_box">
            <div class="hd">
            	<div class="bz a1"><div class="jj_bg"></div></div>
            </div>
            <div class="ct">
            	<div class="bg_t"></div>
                <div class="clr">
                    <div class="l"></div>
                    <div class="ct_box">
                    <div class="nr">
					<?php echo $_smarty_tpl->tpl_vars['licenset']->value;?>
 
					</div>
                    </div>
                </div>
                <div class="bg_b"></div>
            </div>
            <div class="btn_box"><a href="javascript:void(0);" class="is_btn" onclick="$('#install').submit();return false;">开始安装</a></div>
			<form id="install" action="index.php?" method="get">
			<input type="hidden" name="step" value="2">
			</form>
        </div>
    </div>
</body>
</html>
<?php }} ?>