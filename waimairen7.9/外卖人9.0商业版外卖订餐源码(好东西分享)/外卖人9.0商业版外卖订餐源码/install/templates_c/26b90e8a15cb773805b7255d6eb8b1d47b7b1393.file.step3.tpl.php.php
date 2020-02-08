<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 18:11:25
         compiled from ".\templates\step3.tpl.php" */ ?>
<?php /*%%SmartyHeaderCode:42125beb838c6c4d19-67538612%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '26b90e8a15cb773805b7255d6eb8b1d47b7b1393' => 
    array (
      0 => '.\\templates\\step3.tpl.php',
      1 => 1553564616,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '42125beb838c6c4d19-67538612',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5beb838c6e82e9_56152768',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5beb838c6e82e9_56152768')) {function content_5beb838c6e82e9_56152768($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.tpl.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type="text/javascript">
  $(document).ready(function() {
	 
  })
</script>
	<div class="body_box">
        <div class="main_box">
            <div class="hd">
            	<div class="bz a3"><div class="jj_bg"></div></div>
            </div>
            <div class="ct">
            	<div class="bg_t"></div>
                <div class="clr">
                    <div class="l"></div>
                    <div class="ct_box nobrd i6v">
                    <div class="nr">
			<form id="install" name="myform" action="index.php?" method="post">	
			<input type="hidden" name="step" value="4">	
            
<fieldset>
	<legend>填写数据库信息</legend>
	<div class="content">
    	<table width="100%" cellspacing="1" cellpadding="0" >
			<tr>
			<th width="20%" align="right" >数据库主机：</th>
			<td>
			<input name="dbhost" type="text" id="dbhost" value="localhost" class="input-text" />
			</td>
			</tr>
			<tr>
			<th align="right">数据库帐号：</th>
			<td><input name="dbuser" type="text" id="dbuser" value="root" class="input-text" /></td>
			</tr>
			<tr>
			<th align="right">数据库密码：</th>
			<td><input name="dbpw" type="password" id="dbpw" value="" class="input-text" /></td>
			</tr>
			<tr>
			<th align="right">数据库名称：</th>
			<td><input name="dbname" type="text" id="dbname" value="" class="input-text" /></td>
			</tr>
			<tr>
			<th align="right">数据表前缀：</th>
			<td><input name="tablepre" type="text" id="tablepre" value="xiaozu_" class="input-text" />  <img src="./images/help.png" style="cursor:pointer;" title="可自行请修改表前缀" align="absmiddle" />
			<span id='helptablepre'></span></td>
			</tr> 
			</table>
    </div>
</fieldset>

<fieldset>
	<legend>程序KEY</legend>
	<div class="content">
    	<table width="100%" cellspacing="1" cellpadding="0">
			  <tr>
				<th width="20%" align="right">购买程序获得的KEY</th>
				<td><input name="sitekey" type="text" id="sitekey" value="98548554004rt555ry5ty54675664" class="input-text" />
				  <a href="http://www.baidu.com">技术支持</a></td>
			  </tr>
			  <tr> 
			</table>
    </div>
</fieldset>
			</form>
                   </div>
                   </div>
                </div>
                <div class="bg_b"></div>
            </div>
            <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a><a href="javascript:void(0);"  onClick="checkdb();return false;" class="x_btn">下一步</a></div>
        </div>
    </div>
</body>
</html>
<script language="JavaScript">
<!--
var errmsg = new Array();
errmsg[0] = '您已经安装过Phpcms，系统会自动删除老数据！是否继续？';
errmsg[2] = '无法连接数据库服务器，请检查配置！';
errmsg[3] = '成功连接数据库，但是指定的数据库不存在并且无法自动创建，请先通过其他方式建立数据库！';
errmsg[6] = '数据库版本低于Mysql 4.0，无法安装Phpcms，请升级数据库版本！';

function checkdb() 
{
	var url = '?step=dbtest&dbhost='+$('#dbhost').val()+'&dbuser='+$('#dbuser').val()+'&dbpw='+$('#dbpw').val()+'&dbname='+$('#dbname').val()+'&tablepre='+$('#tablepre').val()+'&sid='+Math.random()*5;
    $.get(url, function(data){
		if(data > 1) {
			alert(errmsg[data]);
			return false;
		}
		else if(data == 1 || (data == 0 && confirm(errmsg[0]))) {
			$('#install').submit();
		}
	});
    return false;
}
//-->
</script><?php }} ?>