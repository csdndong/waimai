<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 21:37:56
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\member\regester.html" */ ?>
<?php /*%%SmartyHeaderCode:76025cd57eb4a76170-15128933%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '26aa372f5dbda9565588cb719d96ef631c7eeedd' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\member\\regester.html',
      1 => 1536024583,
      2 => 'file',
    ),
    '217afa0808885dd89807d7541c2a8af7ba13208d' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\public\\site.html',
      1 => 1538873286,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '76025cd57eb4a76170-15128933',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'sitename' => 0,
    'keywords' => 0,
    'description' => 0,
    'metadata' => 0,
    'siteurl' => 0,
    'color' => 0,
    'sitebk' => 0,
    'is_static' => 0,
    'controlname' => 0,
    'mapname' => 0,
    'member' => 0,
    'sitelogo' => 0,
    'footlink' => 0,
    'items' => 0,
    'search_input' => 0,
    'list' => 0,
    'list2' => 0,
    'itv' => 0,
    'appewm' => 0,
    'wxewm' => 0,
    'litel' => 0,
    'toplink' => 0,
    'beian' => 0,
    'footerdata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd57eb512bc62_09943620',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd57eb512bc62_09943620')) {function content_5cd57eb512bc62_09943620($_smarty_tpl) {?><?php if (!is_callable('smarty_function_load_data')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\function.load_data.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
 <title> 用户注册-<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title>
<meta name="Keywords" content="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" />
<?php echo stripslashes($_smarty_tpl->tpl_vars['metadata']->value);?>

	  <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-16x16.png" type="image/png" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-16x16.png" type="image/png" sizes="16x16" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-32x32.png" type="image/png" sizes="32x32" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-48x48.png" type="image/png" sizes="48x48" />
    <link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/favicon/favicon-64x64.png" type="image/png" sizes="64x64" />
<link href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/common.css?v=9.0">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtype.css?v=9.0"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/wmr_login.css?v=9.0">
    

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopcommon.css?v=9.0"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopjquery-ui-1.10.4.custom.min.css?v=9.0">
<?php if ($_smarty_tpl->tpl_vars['color']->value=="green"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/green.css?v=9.0">
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['color']->value=="yellow"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/yellow.css?v=9.0">
<?php }?> 
<!-- <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/newtop/ntopstyles.css">  -->


<div class="mmbg" <?php if (!empty($_smarty_tpl->tpl_vars['sitebk']->value)){?>style="background:url(<?php echo $_smarty_tpl->tpl_vars['sitebk']->value;?>
) repeat;"<?php }?>></div> 
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquerynew.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/template.min.js?v=9.0" type="text/javascript" language="javascript"></script>
 <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.lazyload.min.js?v=9.0" type="text/javascript" language="javascript"></script> 
 
  
 <script>
 	$(function() { 
$("img").lazyload({ 
effect : "fadeIn" 
}); 
}); 
</script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/regestercode.js" type="text/javascript" language="javascript"></script> 

 <script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	var controllername= '<?php echo $_smarty_tpl->tpl_vars['controlname']->value;?>
';
</script>

<!--[if lte IE 6]>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/DD_belatedPNG_0.0.8a.js" type="text/javascript"></script>
    <script type="text/javascript">
        DD_belatedPNG.fix('div, ul, img, li, input , a'); 
    </script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/ie6.js" type="text/javascript"></script>


<![endif]--> 
</head> 
<body>

<!--谁去拿外卖 -->

       <div id="modal-shuiqunawaimai" class="modal-who-get-dishes modal hide fade" aria-hidden="true" tabindex="-1">
      <div class="modal-header"> <a href="#" class="close" aria-hidden="true">×</a>
        <h3>谁去拿外卖</h3>
      </div>
      <div class="modal-body">
        <div class="who-get-dishes-wrapper">
          <h2 class="wgd-badge"></h2>
          <a id="trigger" class="wgd-btn"></a> <span class="wgd-rules">随机到最小数字的人去拿外卖</span> <span id="lastResult" class="wgd-bg-text">↓ Start</span>
          <ul id="result" class="wgd-result-list">
          </ul>
        </div>
      </div>
    </div>

<script type="text/javascript">

  var eleme = eleme || {};


</script>

<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/whoqunawaimai.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/whonawaimai.css">
<!--谁去拿外卖  end-->







<div class="topCon">
    <div class="topBox">
        <div class="topL">
            <div class="topLbox"><i></i><span>当前位置：</span><b><?php echo (($tmp = @$_smarty_tpl->tpl_vars['mapname']->value)===null||$tmp==='' ? '' : $tmp);?>
</b><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/guide"),$_smarty_tpl);?>
">[切换地址]</a></div>
        </div>
        <div class="topR">
            <div class="topRsignin">
                <ul>
                    <!--<li><span>您好请登录</span></li>-->
                    <?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
                    <li>
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/base"),$_smarty_tpl);?>
">您好，<?php echo $_smarty_tpl->tpl_vars['member']->value['username'];?>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/loginout"),$_smarty_tpl);?>
">退出</a>
                    </li>
                    <?php }else{ ?>
                    <li>
                        <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/login"),$_smarty_tpl);?>
">登录</a>&nbsp;&nbsp;&nbsp;&nbsp;
                       <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/regester"),$_smarty_tpl);?>
">注册</a>
                    </li>
                    <?php }?>
                </ul>
            </div>
         </div>
    </div>
</div>
<!---------------------------------------顶部结束--------------------------------------->

<!---------------------------------------头部开始--------------------------------------->
<div class="headCon">
    <div class="headBox">
        <div class="headLogo"><a href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"><img style="margin-top:5px;" <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['sitelogo']->value)),$_smarty_tpl);?>
></a></div>
        <div class="headNav" style="background:none;height:0;">
            <ul>
                <?php if (!empty($_smarty_tpl->tpl_vars['footlink']->value)){?>
                <?php $_smarty_tpl->tpl_vars['footlink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['footlink']->value), null, 0);?>
                <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['footlink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
                <li ><a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</a></li>
                <?php } ?>
                <?php }?>
				<!-- class="headNavaA" -->
            </ul>
        </div>
        <div class="headSearch"><input type="text" class="headSeaText" id="search_input" placeholder="输入商家/美食" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['search_input']->value)===null||$tmp==='' ? '' : $tmp);?>
"><input type="button" id="comtopsearch" class="headSeaBut" style="border:0px solid #fff;"></div>
    </div>
</div>
<script type="text/javascript">
    $('#comtopsearch').click(function(){

        search();
    })

    function search()
    {
        var searchname = $('#search_input').val();

        if( searchname != '' )
        {
            var url = siteurl+'/index.php?ctrl=site&action=shoplist&shopsearch='+searchname;
            location.href=url;
        }else{

            alert("搜索条件不能为空！");

        }
    }

</script>

<div style="clear:both;"></div>


 



 <div class="registerCon">
     <div class="registerBox">
         <div class="registerMain">
             <div class="registerTit"><h2>用户注册</h2></div>
             <div class="register">
                 <form id="regForm" method="post" action="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/saveregester/datatype/json"),$_smarty_tpl);?>
">
                 <div class="registerL">
                     <div class="registerLinp">
                         <ul>
                             <li><span>邮箱</span>
                                 <input type="text" name="email" class="input1" placeholder="此邮箱用于找回密码"><i>*</i>
                             </li>
                             <li><span>账号</span>
                                 <input type="text" name="tname" class="input1" placeholder="输入账号">
                             </li>
                             <li><span>手机号</span>
                                 <input type="text"  name="phone" class="input1" id="phone" placeholder="输入手机号">
                             </li>
                            
							 <?php if ($_smarty_tpl->tpl_vars['regestercode']->value==1){?>
							 <li id="showgetcode" style="display:none;" ><span>验证码</span>
                                 <input type="text" name="phoneyan" id="phoneyan" class="veco input1" placeholder="输入验证码">
                                 <input type="button" class="vecobut" value="获取验证码" onclick="clickyanzheng();" style="padding:0;font-size:12px;" class="inputgetcheck" time="0" id="dosendbtn">
                             </li>
                             <?php }?>
							 <li><span>密码</span>
                                 <input type="password" name="pwd" class="input1" placeholder="输入密码">
                             </li>
                             <li><span>重复密码</span>
                                 <input type="password" name="pwd2" class="input1" placeholder="重复密码">
                             </li>
							
							 <!-- 
							   <?php if ($_smarty_tpl->tpl_vars['allowedcode']->value==1){?>
							   
							     <li><span>验证码</span>
									<input style="width:80px;" placeholder="验证码" name="Captcha"  class="input1 input2" value=""/>
									<img style="float:left;" src="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/getCaptcha"),$_smarty_tpl);?>
"   id="captchaimg" />
									<span class="span1"><a href="javascript:freshcode();">换一张</a></span>
								 </li>
	 
						   	<?php }?> -->
							 
							 
                         </ul>
                     </div>
                     <div class="registerLbutBox">
                         <div class="registerLbut"><input type="button" class="login-button" value="同意协议并注册"></div>
                         <div class="registerLbutChebox">
                             <div class="registerLbutChe"><i></i><input name="reg[agree]" checked="checked" type="checkbox" ></div>
                             <span>我接受<a target="_blank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/single/show/xieyi"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
服务协议</a></span>
                         </div>
                     </div>
                 </div>
                 </form>
                 <div class="registerR">
                     <div class="registerRprom"><span>已有<?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
账号？</span><b>请点击 </b><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/login"),$_smarty_tpl);?>
">直接登录</a></div>
                     <div class="registerRthpalo">
                         <span>使用以下账户直接登录</span>
                         <ul id="type_login">
                             <?php echo smarty_function_load_data(array('assign'=>"apiloginlist",'table'=>"otherlogin",'fileds'=>"*",'limit'=>"10"),$_smarty_tpl);?>

                             <?php if (count($_smarty_tpl->tpl_vars['apiloginlist']->value['list'])>0){?>
                             <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['apiloginlist']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['items']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['items']->index++;
 $_smarty_tpl->tpl_vars['items']->first = $_smarty_tpl->tpl_vars['items']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['loop']['first'] = $_smarty_tpl->tpl_vars['items']->first;
?>
                             <li>
                                 <a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/linktest/logintype/".((string)$_smarty_tpl->tpl_vars['items']->value['loginname'])),$_smarty_tpl);?>
">
                                 <i class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['loop']['first']){?>icon_qq<?php }else{ ?>icon_xl<?php }?>"></i>
                                 </a>
                             </li>
                             <?php } ?>
                             <?php }?>
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <script type="text/javascript">
     var regestercode = '<?php echo $_smarty_tpl->tpl_vars['regestercode']->value;?>
';
     $('.login-button').click(function(){

         subform('<?php echo FUNC_function(array('type'=>'url','link'=>"/member/base"),$_smarty_tpl);?>
',$('#regForm'));
     })
     $(".input1").change(function(j){

         var v=$(this).val();
         var p=$(this).attr("name");
         switch(p){
             case "email":
                 var patrn= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                 if(!patrn.exec(v)){
                     alert('邮箱格式错误');
                 }else{
                     $.ajax({
                         type: "POST",
                         url: "<?php echo FUNC_function(array('type'=>'url','link'=>"/member/checkemail/datatype/json"),$_smarty_tpl);?>
",
                         dataType: "json",
                         data: "uname=" + v,
                         success: function(msg){
                             if(msg.error ==true){
                                 $('[name="email"]').val();
                                 alert('邮箱已经存在');
                             }
                         }
                     });
                 }
                 break;
             case "tname":
                 var patrn=/[u4e00-u9fa5]/;
                 if(v == '' ||v == null){
                     alert('账号不能为空');
                 }else{
                     $.ajax({
                         type: "POST",
                         url: "<?php echo FUNC_function(array('type'=>'url','link'=>"/member/checkname/datatype/json"),$_smarty_tpl);?>
",
                         dataType: "json",
                         data: "uname=" + v,
                         success: function(msg){
                             if(msg.error==true){
                                 $('[name="tname"]').val('');
                                 alert(msg.msg);
                                  
                             }
                         }
                     });
                 }
                 break;
             case "phone":
                 var patrn= /(.){4,16}/;
                 if(!patrn.exec(v)){
                     alert('手机格式错误');
                 }
                 break;
             case "pwd":
                 var patrn=/(.){4,16}/;
                 if(!patrn.exec(v)){
                     alert('密码是4-16位的字母、数字,区分大小写');
                 }
                 break;
         }
     });
    /* $("#type_login li").eq(0).addClass("icon_qq");
     $("#type_login li").eq(1).addClass("icon_xl");*/

 </script>
<style>
.registerCon .registerBox .registerMain .registerR .registerRthpalo ul li{
    float:left;
    margin-right:30px;
}
.registerCon .registerBox .registerMain .registerR .registerRthpalo ul li {
    width: 44px;
    height: 44px;
    display: block;
    background-image: none;
    cursor: pointer;
}
.registerLbutChe input {
    opacity:1!important;
	}
</style>
 
<style>

.footer01 .dianhua{  width:250px; height:83px; float:left; font-family:微软雅黑;}
.footer01 .dianhua ul{}
.footer01 .dianhua li{ float:left; display:inline; width:154px; padding-left:80px; color:#FFFFFF}
.footer01 .dianhua li b{ color:#6CCB3B}
.footer01 .dianhua li b#font24{ font-size:20px; color:#ffffff;}
.footer01 .dianhua li.li1{ background:url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/img/index_r62_c6.png) left center no-repeat; height:74px; width:170px}
.footer01 .dianhua li.li2{ background: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/img/iconfont-mark.png) left center no-repeat; height:74px;background-size: 50px 50px;}
.footer01 .dianhua li b#font24 a {
color: #fff;
}
</style>



<div class="footer01" style="height:245px;">
<div class="zdy1" style="background:#f38383; height:20px;"></div>
		  <div class="zdy2"  style="background:#f38383; width:100%; margin:0 auto;height:170px;">
				  <div class="box02">

					
					   <div style="float:left; width:490px">
					   
						 <?php echo smarty_function_load_data(array('assign'=>"list",'table'=>"newstype",'where'=>"displaytype=1 and parent_id=0",'fileds'=>"*",'limit'=>3,'orderby'=>"orderid asc"),$_smarty_tpl);?>
 
								 <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['mykey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['mykey']->value = $_smarty_tpl->tpl_vars['items']->key;
?>   
					   
						<dl>
						   <dt ><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</dt>
								<?php if ($_smarty_tpl->tpl_vars['items']->value['type']==2){?>
									  <?php echo smarty_function_load_data(array('assign'=>"list2",'table'=>"newstype",'fileds'=>"*",'where'=>"parent_id=".((string)$_smarty_tpl->tpl_vars['items']->value['id']),'limit'=>4),$_smarty_tpl);?>
 
									   <?php  $_smarty_tpl->tpl_vars['itv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list2']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itv']->key => $_smarty_tpl->tpl_vars['itv']->value){
$_smarty_tpl->tpl_vars['itv']->_loop = true;
?>  
						   
						   <dd><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/newstype/id/".((string)$_smarty_tpl->tpl_vars['itv']->value['id'])),$_smarty_tpl);?>
" ><?php echo $_smarty_tpl->tpl_vars['itv']->value['name'];?>
</a></dd>
						   
							<?php } ?> 
							
							
							
									  
									  
								  <?php }else{ ?>
									  <?php echo smarty_function_load_data(array('assign'=>"list2",'table'=>"news",'fileds'=>"id,title,typeid",'where'=>"typeid=".((string)$_smarty_tpl->tpl_vars['items']->value['id']),'limit'=>4),$_smarty_tpl);?>
 
									  <?php  $_smarty_tpl->tpl_vars['itv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list2']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itv']->key => $_smarty_tpl->tpl_vars['itv']->value){
$_smarty_tpl->tpl_vars['itv']->_loop = true;
?>    
									   <dd><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/site/news/id/".((string)$_smarty_tpl->tpl_vars['itv']->value['id'])),$_smarty_tpl);?>
" ><?php echo $_smarty_tpl->tpl_vars['itv']->value['title'];?>
</a></dd>
									  
										<?php } ?> 
								 <?php }?>
						  
						</dl>
						 <?php } ?>   
						</div>
						
						
						   <div class="dianhua" style="width:320px;">
						 <ul>
						   <li class="li1" style="background:none; margin-left:20px; width:135px; padding-left:0px;">
                               <img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['appewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6app_ewm.png";?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['appewm']->value).$_tmp1),$_smarty_tpl);?>
 alt="手机APP下载" width="122" height="122"  /><br><br>

                               <span style="margin-left:15px;font-size:14px">手机APP下载</span>
							</li>
						   <li class="li2" style="background:none; margin-left:20px;  width:135px;padding-left:0px;">

                               <img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['wxewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6wx_ewm.png";?><?php }?><?php $_tmp2=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxewm']->value).$_tmp2),$_smarty_tpl);?>
  alt="微信端扫描二维码" width="122" height="122"  /><br><br>
                               <span style="margin-left:8px;font-size:14px">微信端扫描二维码</span>
						   </li>
						 </ul>
					   </div>
						
						
						   <div class="dianhua" style="float:right;">
						 <ul>
						   <li class="li1"><strong>客服电话</strong><br /><b id="font24"><?php echo $_smarty_tpl->tpl_vars['litel']->value;?>
</b><br />周一至周日&nbsp;09:30-21:30
						   
						  
											 </li>
						   <li class="li2"><strong>欢迎留言 </strong><br /><b id="font24"><a href="<?php echo FUNC_function(array('type'=>'url','link'=>"/ask/index"),$_smarty_tpl);?>
" >反馈留言</a></b><br />您的意见对我们至关重要</li>
						 </ul>
					   </div>
						
						
				</div> 
		</div>
				 <div class="footer02" style="height:50px; margin-top:5px;">
			  <p>
			  
			  <P class="" >
			     <?php if (!empty($_smarty_tpl->tpl_vars['toplink']->value)){?>
	 	     	<?php $_smarty_tpl->tpl_vars['toplink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['toplink']->value), null, 0);?>
       <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toplink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['items']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['items']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
 $_smarty_tpl->tpl_vars['items']->iteration++;
 $_smarty_tpl->tpl_vars['items']->last = $_smarty_tpl->tpl_vars['items']->iteration === $_smarty_tpl->tpl_vars['items']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['listname']['last'] = $_smarty_tpl->tpl_vars['items']->last;
?> 
			        
					   <a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
" class=""  data="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><span style="line-height:16px;"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</span>
					    <?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['listname']['last']){?><span> &nbsp;&nbsp;|&nbsp;&nbsp; </span><?php }?>
						</a> 
		  <?php } ?>
		  <?php }?>
         	
			  
			  </P>
			  <P class="zdy3">Copyright©2018-2019 <?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
  | <?php echo $_smarty_tpl->tpl_vars['beian']->value;?>
    <?php echo $_smarty_tpl->tpl_vars['footerdata']->value;?>
 </P>
			   
			  </div> 

 
</div>



</body>
</html><?php }} ?>