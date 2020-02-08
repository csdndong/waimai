<?php /* Smarty version Smarty-3.1.10, created on 2018-11-14 10:08:49
         compiled from ".\templates\step4.tpl.php" */ ?>
<?php /*%%SmartyHeaderCode:244845beb83b1c29417-00636096%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '49975db84945a6e811a8eae271f74eb6e5732f11' => 
    array (
      0 => '.\\templates\\step4.tpl.php',
      1 => 1512459126,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '244845beb83b1c29417-00636096',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'dbhost' => 0,
    'dbuser' => 0,
    'dbpw' => 0,
    'dbname' => 0,
    'tablepre' => 0,
    'dbcharset' => 0,
    'pconnect' => 0,
    'username' => 0,
    'password' => 0,
    'email' => 0,
    'password_key' => 0,
    'sitekey' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5beb83b1c7c0c7_33784811',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5beb83b1c7c0c7_33784811')) {function content_5beb83b1c7c0c7_33784811($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.tpl.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div class="body_box">
        <div class="main_box">
            <div class="hd">
            	<div class="bz a4"><div class="jj_bg"></div></div>
            </div>
            <div class="ct">
            	<div class="bg_t"></div>
                <div class="clr">
                    <div class="l"></div>
                    <div class="ct_box">
                     <div class="nr">
                  	<div id="installmessage" >正在准备安装 ...<br /></div>
                     </div>
                    </div>
                </div>
                <div class="bg_b"></div>
            </div>
            <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a><a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn pre" id="finish">安装中..</a></div>            
        </div>
    </div>
    <div id="hiddenop"></div>
	<form id="install" action="finsh.php?" method="post">
	<input type="hidden" name="module" id="module" value="" /> 
	<input type="hidden" id="selectmod" name="selectmod" value="admin,mkconfig" />
	<input type="hidden" name="step" value="5">
	</form>
</body>
<script language="JavaScript">
<!--
$().ready(function() {
reloads();
})
var n = 0;
var setting =  new Array();
setting['admin'] = '导入数据到数据库......';
setting['mkconfig'] = '生成配置文件......'; 

var dbhost = '<?php echo $_smarty_tpl->tpl_vars['dbhost']->value;?>
';
var dbuser = '<?php echo $_smarty_tpl->tpl_vars['dbuser']->value;?>
';
var dbpw = '<?php echo $_smarty_tpl->tpl_vars['dbpw']->value;?>
';
var dbname = '<?php echo $_smarty_tpl->tpl_vars['dbname']->value;?>
';
var tablepre = '<?php echo $_smarty_tpl->tpl_vars['tablepre']->value;?>
';
var dbcharset = '<?php echo $_smarty_tpl->tpl_vars['dbcharset']->value;?>
';
var pconnect = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['pconnect']->value)===null||$tmp==='' ? '0' : $tmp);?>
';
var username = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['username']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var password = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['password']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var email = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['email']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var ftp_user = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['dbuser']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var password_key = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['password_key']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var sitekey = '<?php echo $_smarty_tpl->tpl_vars['sitekey']->value;?>
';
function reloads() {
	var module = $('#selectmod').val();
	m_d = module.split(',');
	$.ajax({
		   type: "POST",
		   url: 'index.php',
		   data: "step=installmodule&module="+m_d[n]+"&dbhost="+dbhost+"&dbuser="+dbuser+"&dbpw="+dbpw+"&dbname="+dbname+"&tablepre="+tablepre+"&dbcharset="+dbcharset+"&pconnect="+pconnect+"&username="+username+"&password="+password+"&email="+email+"&ftp_user="+ftp_user+"&password_key="+password_key+"&sitekey="+sitekey+"&sid="+Math.random()*5,
		   success: function(msg){
			   if(msg==1) {
				   alert('指定的数据库不存在，系统也无法创建，请先通过其他方式建立好数据库！');
			   } else if(msg==2) {
				   $('#installmessage').append("<font color='#ff0000'>"+m_d[n]+"/install/mysql.sql 数据库文件不存在</font>");
			   } else if(msg.length>20) {
				   $('#installmessage').append("<font color='#ff0000'>错误信息：</font>"+msg);
			   } else {
				   $('#installmessage').append(setting[m_d[n]] + msg + "<img src='images/correct.gif' /><br>");				   
					n++;
					if(n < m_d.length) {
						reloads();
					} else { 
						$('#installmessage').append("<font color='yellow'>安装完成</font>");
						$('#finish').removeClass('pre');
						$('#finish').html('安装完成');
						setTimeout("$('#install').submit();",1000); 						
					}
					document.getElementById('installmessage').scrollTop = document.getElementById('installmessage').scrollHeight;
			   }	
		}	
		});
}
//-->
</script>
</html>
<?php }} ?>