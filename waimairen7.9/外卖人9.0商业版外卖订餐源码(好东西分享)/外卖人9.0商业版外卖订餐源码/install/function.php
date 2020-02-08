<?php
function format_textarea($string) { 
	$chars = 'utf-8';
	if(CHARSET=='gbk') $chars = 'gb2312';
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,$chars)));
}
?>