<{include file="header.tpl.php"}>
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

var dbhost = '<{$dbhost}>';
var dbuser = '<{$dbuser}>';
var dbpw = '<{$dbpw}>';
var dbname = '<{$dbname}>';
var tablepre = '<{$tablepre}>';
var dbcharset = '<{$dbcharset}>';
var pconnect = '<{$pconnect|default:'0'}>';
var username = '<{$username|default:''}>';
var password = '<{$password|default:''}>';
var email = '<{$email|default:''}>';
var ftp_user = '<{$dbuser|default:''}>';
var password_key = '<{$password_key|default:''}>';
var sitekey = '<{$sitekey}>';
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
