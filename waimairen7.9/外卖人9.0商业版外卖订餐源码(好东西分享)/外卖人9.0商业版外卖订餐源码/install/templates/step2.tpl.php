<{include file="header.tpl.php"}>
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
                    <td><{php_uname()}></td>
                    <td>Windows_NT/Linux/Freebsd</td>
                    <td><span><img src="images/correct.gif" /></span></td>
                  </tr>
                  <tr>
                    <td>WEB 服务器</td>
                    <td><{$server['SERVER_SOFTWARE']}></td>
                    <td>Apache/Nginx/IIS</td>
                    <td><span><img src="images/correct.gif" /></span></td>
                  </tr>
                  <tr>
                    <td>PHP 版本</td>
                    <td>PHP <{phpversion()}></td>
                    <td>PHP 5.2.0 及以上</td>
                    <td> 
                    	<{if $info['is_php']!=1}>
                    	<span><img src="images/correct.gif" /></span> 
                    	<font class="red"><img src="images/error.gif" />&nbsp;无法安装</font>
                    	<{/if}> 
                    	</font></td>
                  </tr>
                  <tr>
                    <td>MYSQL 扩展</td>
                    <td><{if $info['mysql']}> √<{else}>×<?php <{/if}> </td>
                    <td>必须开启</td>
                    <td><{if $info['mysql']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;无法安装</font><{/if}></td>
                  </tr>
                  
                  <tr>
                    <td>ICONV/MB_STRING 扩展</td>
                    <td><{if $info['iconv']}>√<{else}>×<{/if}></td>
                    <td>必须开启</td>
                    <td><{if $info['iconv']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;无法安装</font><{/if}></td>
                  </tr>
                  
                  <tr>
                    <td>JSON扩展</td>
                    <td><{if $info['PHP_JSON']}>√<{else}>×<{/if}></td>
                    <td>必须开启</td>
                    <td><{if $info['PHP_JSON']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;不只持json,<a href="http://pecl.php.net/package/json" target="_blank">安装 PECL扩展</a></font><{/if}></td> 
                  </tr>
                  <tr>
                    <td>GD 扩展</td>
                     <td><{if $info['PHP_GD']}>√<{else}>×<{/if}></td>
                    <td>建议开启</td>
                    <td><{if $info['PHP_GD']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;不支持缩略图和水印</font><{/if}></td>  
                  </tr>                                    
                    
                                    
                 
				  
				          <tr>
                    <td>fsockopen</td>
                    <td><{if $info['fsockopen']}>√<{else}>×<{/if}></td>
                    <td>建议打开</td>
                    <td><{if $info['fsockopen']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;不支持fsockopen函数</font><{/if}></td>   
                  </tr>
                  
                  <{foreach from=$filesmod item=items}>
				          <tr>
                    <td><{$items['cname']}></td>
                    <td><{$items['file']}></td>
                    <td>必须可写</td>
                    <td><{if $items['is_writable']}><span><img src="images/correct.gif" /></span><{else}><font class="red"><img src="images/error.gif" />&nbsp;请修改为可写</font><{/if}></td>   
                  </tr>
                  <{/foreach}>
                  
                </table>
 					</div>
                    </div>
                </div>
                <div class="bg_b"></div>
            </div>
            <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a>
            <{if $is_right}>
            <a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn">下一步</a></div>
            <{else}>
			<a onClick="alert('当前配置不满足Phpcms安装需求，无法继续安装！');" class="x_btn pre">检测不通过</a>
 			<{/if}>
			<form id="install" action="index.php?" method="get">
			<input type="hidden" name="step" value="3">
			</form>
        </div>
    </div>
</body>
</html>
