<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:14:09
         compiled from "D:\wwwroot\demo.52jscn.com\module\wxsite\template\login.html" */ ?>
<?php /*%%SmartyHeaderCode:322075cd56b119a1759-51897266%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '059e9c8883405a124111c8a17aad48e81e88ccb4' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\module\\wxsite\\template\\login.html',
      1 => 1536024580,
      2 => 'file',
    ),
    '4b97aef3851e1132e5992791a8cc3a88d668229a' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\public\\wxsite.html',
      1 => 1538873332,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '322075cd56b119a1759-51897266',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tempdir' => 0,
    'siteurl' => 0,
    'color' => 0,
    'is_static' => 0,
    'Taction' => 0,
    'https' => 0,
    'lat' => 0,
    'lng' => 0,
    'sitename' => 0,
    'description' => 0,
    'signPackage' => 0,
    'sitelogo' => 0,
    'map_comment_link' => 0,
    'addressname' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd56b11c6a7c4_67597762',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56b11c6a7c4_67597762')) {function content_5cd56b11c6a7c4_67597762($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="MobileOptimized" content="320">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta name="HandheldFriendly" content="true">
<meta name="author" content="johnye">
<meta name="shenma-site-verification" content="f28da5e2e3fb6e2afd372a3eedfda998">
<meta name="baidu-site-verification" content="eafwEzRbnz">
<title><?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title> 
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/public1.css?v=9.0"> 
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/newweixin.css?v=9.0"> 
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/newcss/index.css?v=9.0">
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/newcss/font-awesome.min.css?v=9.0">
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/scrllo_function.css?v=9.0">
<?php if ($_smarty_tpl->tpl_vars['color']->value=="green"){?>
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/newcss/green.css?v=9.0"> 
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['color']->value=="yellow"){?>
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/newcss/yellow.css?v=9.0"> 
<?php }?>

<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/jquerymobile/jquery-1.6.4.min.js?v=9.0"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/public.js?v=9.0"></script>  
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/adminpage/public/js/allj.js?v=9.0"></script>  
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/swipe.js?v=9.0"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/iscroll.js?v=9.0"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/newiscroll.js?v=9.0"></script>  
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/scrllo_function.js?v=9.0?v=1.0.0"></script>  
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/jquery.cookie.js?v=9.0"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.lazyload.min.js?v=9.0" type="text/javascript" language="javascript"></script> 
   
 <link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/gift.css">

 
  <script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/template.min.js"></script>    

<script>  
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
	var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
	var taction = "<?php echo $_smarty_tpl->tpl_vars['Taction']->value;?>
"; 
	var https = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['https']->value)===null||$tmp==='' ? '' : $tmp);?>
';
    var lat = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lat']->value)===null||$tmp==='' ? 0 : $tmp);?>
';
    var lng = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lng']->value)===null||$tmp==='' ? 0 : $tmp);?>
';
	if ( taction != 'member' &&  taction != 'login' &&  taction != 'reg'  ){
		var cururl = window.location.href;
		$.cookie('wxCurUrl', cururl);
	} 
</script>
 


<script> 
		var myScroll;
function loaded() {
	myScroll = new iScroll('wrapper', {
		useTransform: false,
		onBeforeScrollStart: function (e) {
			var target = e.target;
			while (target.nodeType != 1) target = target.parentNode;

			if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
				e.preventDefault();
		}
	});
}
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false); 
document.addEventListener('DOMContentLoaded', function () { setTimeout(loaded, 200); }, false);
</script>
<script>
 	$(function(){
 	$('.loginbtn').click(function(){ 
 		var checkuname = $('#logEmailOrPhone').val();
 		if(checkuname != ''){
 		  
  	 	showLoading();
  	  	var info = {'uname':$('#logEmailOrPhone').val(),'pwd':$('#logPassword').val(),'logintype':'html5'}; 
  	 	  var  url = siteurl+'/index.php?ctrl=member&action=login&datatype=json&random=@random@' 
  	 	   $.ajax({ 
             url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
            dataType: "json", 
            data:info, 
            success:function(content) {   
            	if(content.msg ==  false){
					newhideLoading();
                    window.location.href = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['web_extend_link']->value)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['siteurl']->value : $tmp);?>
';
            	}else{ 
					newhideLoading();
            	   Tmsg(content.msg);
              }
            	
            }, 
            error:function(){
            	newhideLoading();

            } 
         });  
    }else{
    	Tmsg('用户帐号不能为空');
    }
		  
	 }); 
	});

 </script>
 <script>
 	$(function(){
 	$('.intexbg1fastlogin').click(function(){ 	     
 		var phone = $('#phone').val();
		var phoneyan = $('#phoneyan').val();
		var invitecode = $('input[name="showinvitecode"]').val();
 		if(phone < 1){
		     Tmsg('请输入手机号');
			 return false;
		}
		if(phoneyan < 1){
		     Tmsg('请输入验证码');
			 return false;
		} 	  
  	 	showLoading(); 
  	  	var info = {'phone':phone,'phoneyan':phoneyan,'invitecode':invitecode}; 
  	 	  var  url = siteurl+'/index.php?ctrl=member&action=fastlogin&datatype=json&random=@random@' 
  	 	   $.ajax({ 
             url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
            dataType: "json", 
            data:info, 
            success:function(content) {     
            	if(content.error ==  false){
					newhideLoading();
					if(content.msg=='success1'){
					    window.location.href= siteurl+'/index.php?ctrl=wxsite&action=updatepwdd';
					}else{
					    window.location.href= siteurl+'/index.php?ctrl=wxsite&action=member';
					}
                    
            	}else{ 
					newhideLoading();
            	   Tmsg(content.msg);
                }
            	
            }, 
            error:function(){
            	newhideLoading();
            } 
         });  
  
		  
	 }); 
	});

 </script>

 
 
 
</head>
<body>  
 
<div class="toptitCon">
 <div class="toptitBox">
  <div class="toptitL"><i></i></div>
  <div class="toptitC"><h3>登录</h3><span onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/reg"),$_smarty_tpl);?>
');"  style='color:#fff;position: absolute;top: 9px;right: 10px;font-size:80%'>立即注册</span></div>
 </div>
</div>

	
	 
 <div id="wrapper" style="top:40px;">
	<div id="scroller">
<!--登录-->
<div class="signinregistertit" >
 <ul style='border:none;'>
  <li class="ainregaA userloginbtn">账号密码登录</li>
  <li class='fastloginbtn'>手机号快捷登录</li>
 </ul>
</div>
<script>
$('.userloginbtn').click(function(){
    $('.userloginbtn').addClass('ainregaA');
    $('.userlogin').show();
	$('.fastloginbtn').removeClass('ainregaA');
	$('.fastlogin').hide();
})
$('.fastloginbtn').click(function(){
    $('.fastloginbtn').addClass('ainregaA');
    $('.fastlogin').show();
	$('.userloginbtn').removeClass('ainregaA');
	$('.userlogin').hide();
})
</script>
<!--输入用户名密码-->
<!-------------------账号密码登录开始----------------------->
<div class="signininput userlogin" style='border:none;'>
 <ul>
  <li><i class="signinuser"></i><input type="text"  id="logEmailOrPhone"  placeholder="账号"></li>
  <li><i class="signinpassw"></i><input type="password" id="logPassword" placeholder="密码"></li>
 </ul>
</div>
<div class="intexchabutt userlogin"  id ="userlogin" style='margin-top:15px;' ><input type="button" value="登录" style='border-radius: 5px;background-color:#cccccc!important' disabled="disabled" class="loginbtn" ><span  onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/forpwd"),$_smarty_tpl);?>
');"  class="sinzhmm">找回密码</span></div>
<script>

    document.getElementById("logEmailOrPhone").oninput = function () {
                 check();
     }
   document.getElementById("logPassword").oninput = function () {
		 check();
	 }
	function  check(){
		var logEmailOrPhone = $("#logEmailOrPhone").val();
		var logPassword = $("#logPassword").val();
		var colorset = '<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
';
		if(colorset == 'yellow'){
		    color = '#ff7600';
		}else if(colorset == 'green'){
		    color = '#00cd85';
		}else{
		    color = '#ff6e6e';
		}
		if((logEmailOrPhone =='') || (logPassword =='') ){     
			$(".loginbtn").css("background-color","#cccccc"); 
			$('.loginbtn').attr('disabled',"true");
		}else{
			$('.loginbtn').removeAttr("disabled"); 
			$(".loginbtn").css("background-color",color); 
		}
	}	 
		 
		 
</script>

<!-------------------账号密码登录结束----------------------->

<!-------------------手机快捷登录开始----------------------->
<div class="signininput fastlogin" style='border:none;display:none'>
 <ul>
 
  <li><i class="signinpho"></i><input type="text" id="phone" value="" placeholder="手机号"><input type="button"  onclick="clickyanzheng();"  style='border-radius: 5px;display:inline-block;background-color:#cccccc;margin-top: 10px;' time="0" id="dosendbtn"  value="发送验证码" class="signmeinput"></li>
    <li id="showgetcode" class="signmehide"><i class="signinmess"></i><input type="text" name="phoneyan" id="phoneyan"   value="" placeholder="验证码"></li> 
  <li class="signmehide"><i class="signinmess"></i><input type="text"  placeholder="输入短信验证码"><input type="button" value="剩余120秒" class="signmeinput signmebg1"></li>
  <li class="showinvitecode" style='display:none'><i class="signinmess"></i><input type="text" name="showinvitecode"  value="" placeholder="邀请码(可不填)"></li> 
 </ul>
</div>
<div class="intexchabutt fastlogin" style='display:none;margin-top:15px;' ><input type="button" value="登录" style='border-radius: 5px;background-color:#cccccc!important' disabled="disabled" id="fastloginll"  class="intexbg1 intexbg1fastlogin"><span  onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/forpwd"),$_smarty_tpl);?>
');"  class="sinzhmm">找回密码</span></div>

<script>
	document.getElementById("phoneyan").oninput = function () {
		checkfastlogin();
	}
	document.getElementById("dosendbtn").oninput = function () {
		checkPhoneOver();
	}
	document.getElementById("phone").oninput = function () {
		checkPhoneOver();
	}
	function  checkfastlogin(){
		var phone = $("#phone").val();
		var phoneyan = $("#phoneyan").val();
		var colorset = '<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
';
		if(colorset == 'yellow'){
		    color = '#ff7600';
		}else if(colorset == 'green'){
		    color = '#00cd85';
		}else{
		    color = '#ff6e6e';
		}
		if( (phone =='') || (phoneyan =='') ){  
			$("#fastloginll").css('background-color',"#ccc!important"); 
			$('#fastloginll').attr('disabled',"true");
		}else{
			$('#fastloginll').removeAttr("disabled"); 
			$("#fastloginll").css("background-color",color); 
		}
	}	
	function checkPhoneOver(){
		var phonelen = $("#phone").val().length;
		var colorset = '<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
';
			if(colorset == 'yellow'){
				var color = '#ff7600';
			}else if(colorset == 'green'){
				var color = '#00cd85';
			}else{
				var color = '#ff6e6e';
			}
		//console.log(phonelen);
		if(phonelen == 11){	
            showinvitecode($("#phone").val());	
			$('#dosendbtn').css('background-color',color);			
		}else{
		    $('.showinvitecode').hide();
			$('#dosendbtn').css('background-color','#cccccc');
		} 
	}
	function showinvitecode(phone){
		var info = {'phone':phone}; 
		var  url = siteurl+'/index.php?ctrl=member&action=checkinvitecode&datatype=json&random=@random@' 
		$.ajax({ 
			url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
			dataType: "json", 
			data:info, 
			success:function(content) {             	 
				if(content.error ==  false){
				    $('.showinvitecode').show();
				}else{ 
				    $('.showinvitecode').hide();
				}

			}, 
			error:function(){
			    $('.showinvitecode').hide();
			} 
		});
	 
	
	
	
	
	
	
	}
</script>
<!-------------------手机快捷登录结束----------------------->
		 <div style="height:10px;"></div>
        <style>
            .signinFast {
                width: 100%;
                padding-top:100px;
                text-align: center;
            }
            .signinFast .xL {
                width: 90px;
                height: 2px;
                background-color: #edecec;
                display: block;
                float: left;
                margin-top: 9px;
            }
            .signinFast .xR {
                width: 90px;
                height: 2px;
                background-color: #edecec;
                display: block;
                float: right;
                margin-top: 9px;
            }
            .signinFast span {
                line-height: 22px;
                margin-right: 10px;
                font-size: 14px;
                color: #333333;
            }
            .signinFast span {
                line-height: 22px;
                margin-right: 10px;
                font-size: 14px;
                color: #333333;
            }
            .signinFast span {
                line-height: 22px;
                margin-right: 10px;
                font-size: 14px;
                color: #333333;
            }
            #logintype li{
                float:left;
                margin-left:20px;
            }
            #morelogin img{
                width:60px;
                height:60px;
            }
            #morelogin a{
                margin-left:10px;
            }
			 
.signininput ul li {
    border-bottom: 1px solid #f5f5f5!important;     
}
 
        </style>
        <?php if ($_smarty_tpl->tpl_vars['is_wx']->value!=1&&!empty($_smarty_tpl->tpl_vars['is_installqq']->value)){?>
        <div style="padding:0px 10px;">
            <div class="signinFast">
                <span class="xL"></span>
                <span style="color:#cccccc;font-size:15px;">其它登录方式</span>
                <span class="xR"></span>
            </div>
        </div>

        <div style="text-align:center;width:100%;height:100px;margin-top:20px;" id="morelogin">
            <a href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/plug/login/qqphone/login.php"><img src="/templates/m7/public/wxsite/images/h5_QQ_login.png" alt=""></a>
            <!--<a href=""><img src="/templates/m7/public/wxsite/images/h5_wx_login.png" alt=""></a>-->
        </div>
        <?php }?>
	</div>
</div>
<script>
$('#showgetcode').show();
function noshow(msg){  
    	Tmsg(msg);
}
//获取手机验证码
function clickyanzheng(){ 	 
	if(lockclick()){
		 var tempurl = siteurl+'/index.php?ctrl=member&action=fastloginphone&random=@random@&phone=@phone@';
		 tempurl = tempurl.replace('@random@', 1+Math.round(Math.random()*1000)).replace('@phone@',$('#phone').val());
		 $.getScript(tempurl); 
	}    
}
function showsend(phone,time){  
  	$('input[name="phone"]').val(phone);
        $('#dosendbtn').attr('time',time);
        setTimeout("btntime();",0);   
} 
	function  btntime(){
  
	   var nowtime = Number($('#dosendbtn').attr('time'));
        var colorset = '<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
';
		if(colorset == 'yellow'){
		    color = '#ff7600';
		}else if(colorset == 'green'){
		    color = '#00cd85';
		}else{
		    color = '#ff6e6e';
		}   
	   if(nowtime > 0){
	      $('#dosendbtn').attr('disabled',true);
	      $('#dosendbtn').addClass('signmebg1');
	      var c = Number(nowtime)-1;
	       $('#dosendbtn').attr('time',c);
	       var  mx = 120-(120 - Number(c));
	        $('#dosendbtn').attr('value','剩余'+mx+'秒');
             $("#dosendbtn").css("background-color",'#ccc'); 
	         setTimeout("btntime();",1000);
	   }else{
	   	 $('#dosendbtn').attr('disabled',false);
		 $('#dosendbtn').removeClass('signmebg1');
         //$("#dosendbtn").css("background-color",color); 
	   	 $('#dosendbtn').attr('value','重新发送');
     }
  
}
	
	
	

 </script>





  
 
<?php if ($_smarty_tpl->tpl_vars['Taction']->value!='shopshow'&&$_smarty_tpl->tpl_vars['Taction']->value!='foodshow'&&$_smarty_tpl->tpl_vars['Taction']->value!='mkshopshow'&&$_smarty_tpl->tpl_vars['Taction']->value!='shopcart'){?> 
 
 <?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tempdir']->value)."/public/bottom.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

 
<?php }?> 
 
<script>
 var sharetitle = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['sitename']->value)===null||$tmp==='' ? '' : $tmp);?>
';
 var sharedesc = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['description']->value)===null||$tmp==='' ? '' : $tmp);?>
';
 var shareimgUrl = '<?php if (!empty($_smarty_tpl->tpl_vars['signPackage']->value['shareimg'])){?><?php echo $_smarty_tpl->tpl_vars['signPackage']->value['shareimg'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
<?php echo $_smarty_tpl->tpl_vars['sitelogo']->value;?>
<?php }?>';
 var sharelink = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['signPackage']->value['url'])===null||$tmp==='' ? '' : $tmp);?>
';

</script>
<?php if (is_array($_smarty_tpl->tpl_vars['signPackage']->value)&&$_smarty_tpl->tpl_vars['Taction']->value!='togethersay'&&$_smarty_tpl->tpl_vars['Taction']->value!='togethersaydata'&&$_smarty_tpl->tpl_vars['Taction']->value!='fabiaozhuti'){?> 
<script src="<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
res.wx.qq.com/open/js/jweixin-1.2.0.js?v=9.0" type="text/javascript" language="javascript"></script> 
<script> 
    wx.config({
      debug: false,
      appId: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['appId'];?>
',
      timestamp: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['timestamp'];?>
',
      nonceStr: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['nonceStr'];?>
',
      signature: '<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['signature'];?>
',
      jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareQZone',
		'openLocation'
      ] 
  }); 
 // alert('<?php echo $_smarty_tpl->tpl_vars['signPackage']->value['appId'];?>
');
 wx.ready(function(){
	//分享到朋友圈
	wx.onMenuShareTimeline({
		title: sharetitle, // 分享标题
		link: sharelink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
		imgUrl: shareimgUrl, // 分享图标
		success: function () { 
			// 用户确认分享后执行的回调函数
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
		}
	});
	//分享给朋友
	wx.onMenuShareAppMessage({
		title: sharetitle, // 
		desc: sharedesc, // 
		link: sharelink, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
		imgUrl: shareimgUrl, // 分享图标
		type: 'link', // 分享类型,music、video或，不填默认为link
		dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		success: function () { 
			// 用户确认分享后执行的回调函数
			//Tmsg(shareimgUrl);
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
			//Tmsg('取消分享');
		}
	}); 
	wx.onMenuShareQQ({
		title: sharetitle, // 分享标题
		desc: sharedesc, // 分享描述
		link: sharelink, // 分享链接
		imgUrl: shareimgUrl, // 分享图标
		success: function () { 
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () { 
		   // 用户取消分享后执行的回调函数
		}
	});
	
	wx.onMenuShareWeibo({
		title: sharetitle, // 分享标题
		desc: sharedesc, // 分享描述
		link: sharelink, // 分享链接
		imgUrl: shareimgUrl, // 分享图标
		success: function () { 
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
		}
	}); 
	wx.onMenuShareQZone({
		title: sharetitle, // 分享标题
		desc: sharedesc, // 分享描述
		link: sharelink, // 分享链接
		imgUrl: shareimgUrl, // 分享图标
		success: function () { 
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
		}
	});
	<?php if ($_smarty_tpl->tpl_vars['Taction']->value=='index'){?>  
		<?php if (empty($_smarty_tpl->tpl_vars['lng']->value)||empty($_smarty_tpl->tpl_vars['lat']->value)||empty($_smarty_tpl->tpl_vars['addressname']->value)){?>
		wx.getLocation({
		type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
		success: function (res) {
		var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
		var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。 
		 gpstolng(latitude,longitude);
		}
		});
		<?php }?>
	<?php }?>
});
wx.error(function(res){ 
	// alert(res.errMsg);
});




</script> 
<?php }?>
 
</body>
</html>
 <?php }} ?>