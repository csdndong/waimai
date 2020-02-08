<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:14:03
         compiled from "D:\wwwroot\demo.52jscn.com\module\wxsite\template\loginmode.html" */ ?>
<?php /*%%SmartyHeaderCode:302935cd56b0b9046a7-69728472%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1b849fd3ac1fb5e35fa25777fd9e52ba3ccd0e8' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\module\\wxsite\\template\\loginmode.html',
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
  'nocache_hash' => '302935cd56b0b9046a7-69728472',
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
  'unifunc' => 'content_5cd56b0bb6a192_06936040',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56b0bb6a192_06936040')) {function content_5cd56b0bb6a192_06936040($_smarty_tpl) {?><!DOCTYPE html>
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
  



<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/js/wxshop.js"></script>

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
 



 
 
 
</head>
<body>  
 
	
	
<style>
    .web_content{
        max-width: 500px;
        height: 100%;
        margin: 0 auto;
        background-color: #fff;
    }
    .web_content.bg_gray{
        background-color: #f0f0f0;
    }
    .wx_signin_content{
        padding: 0 20px;
    }
    .wx_signin_head{
        padding: 50px 0 24px;
        text-align: center;
        position: relative;
    }

    .wx_signin_head:after,
    .wx_signin_other_box ul li:after{
        content: '';
        width: 100%;
        height: 1px;
        background-color: #e6e6e6;
        transform: scaleY(.4);
        position: absolute;
        left: 0;
        bottom: 0;
        z-index: 1;
    }
    .wx_signin_head img{
        width: 65px;
        height: 65px;
        border-radius: 4px;
    }
    .wx_signin_head span{
        margin-top: 2px;
        font-size: 15px;
        color: #333333;
        display: block;
    }
    .wx_signin_prompt{
        padding: 18px 0 0 0;
        line-height: 22px;
        text-align: center;
        font-size: 15px;
        color: #333333;
    }

    .wx_signin_btn input{
        width: 100%;
        height: 38px;
        margin-top: 20px;
        font-size: 15px;
        background-color: transparent;
        border: 1px solid #11850e;
        border-radius: 4px;
    }

    .wx_signin_btn input:first-child{
        color: #fff;
        background-color: #19ad17;
    }

    .wx_signin_btn input:last-child{
        color: #19ad17;
        background-color: #fff;
    }

    .wx_signin_other_box ul li,
    .wx_signin_other_btn{
        line-height: 40px;
        text-align: center;
        font-size: 14px;
        color: #333333;
        background-color: #fff;
    }

    .wx_signin_other_box ul li{
        position: relative;
    }
    .wx_signin_other_box ul li img{
        width: 18px;
        height: 18px;
        margin-right: 6px;
        vertical-align: -4px;
    }
    .wx_signinsj_box span{
        font-size: 14px;
        color: #333333;
    }
    .wx_signinsj_inp input{
        width: 100%;
        height: 29px;
        font-size: 14px;
        color: #333333;
    }

    .wx_signinsj_btn input{
        width: 100%;
        height: 40px;
        font-size: 12px;
        color: #ffffff;
        background-color: #cccccc;
        border: 1px solid #cccccc;
        border-radius: 4px;
    }

    .wx_signinsj_btn.navaA input{
        background-color: #19ad17;
        border: 1px solid #11850e;
    }
</style>
<div class="web_content">
    <div class="wx_signin_content">
        <div class="wx_signin_head">
            <img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['loginlogo']->value)),$_smarty_tpl);?>
 />
            <span><?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</span>
        </div>
        <div class="wx_signin_prompt">账号太多记不住？</br>使用微信一键登录，安全又方便</div>
        <div class="wx_signin_btn">
            <?php if ($_smarty_tpl->tpl_vars['is_wxlogin']->value==1){?>
            <input type="button" value="微信安全登录"   onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/setlogin"),$_smarty_tpl);?>
');"  />
            <?php }?>
            <input type="button" value="其它登录方式"  id="moerlogin" />
            <!-- onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/login/ulogin/1"),$_smarty_tpl);?>
');"  -->
        </div>
    </div>
</div>
<style>
    #parentdiv{
        position:absolute;
        top:0px;
        left:0px;
        width:100%;
        height:100%;
        z-index:888;
        background: rgba(0,0,0,.3);

    }
   #loginmode{
       position:fixed;
       bottom:-150px;
       width:100%;
   }
    #loginmode div{
        height:44px;
        width:100%;
        background-color:#fff;
        text-align:center;
        line-height:40px;
        color:#000;
    }
</style>
<div id="parentdiv" style="display:none;">
    <div id="loginmode">
        <div onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/login/ulogin/1"),$_smarty_tpl);?>
');"><img src="/templates/m7/public/wxsite/images/iphone.png" style="width:18px;vertical-align:middle;position:relative;top:-2px;" alt="">&nbsp;&nbsp;&nbsp;账号登录</div>
        <div onclick="dolink('<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/plug/login/qqphone/login.php')" style="border-top:1px solid #e6e6e6;margin-bottom:8px;"><img src="/templates/m7/public/wxsite/images/qq.png" style="width:18px;vertical-align:middle;position:relative;top:-2px;" alt="">&nbsp;&nbsp;&nbsp;QQ登录</div>
        <div id="close">取消</div>
    </div>
</div>
<script>
    $("#moerlogin").click(function(){
        $("#parentdiv").show();
        $("#loginmode").animate({bottom:'2px'},200);
    })
    $("#close").click(function(){
        $("#parentdiv").hide();
        $("#loginmode").css({bottom:'-150px'});
    })
    $("#parentdiv").click(function(){
        $("#parentdiv").hide();
        $("#loginmode").css({bottom:'-150px'});
    })
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