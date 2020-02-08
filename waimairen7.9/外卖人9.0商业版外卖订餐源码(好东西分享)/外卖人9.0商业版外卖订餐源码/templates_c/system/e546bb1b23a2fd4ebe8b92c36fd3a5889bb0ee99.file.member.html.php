<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:14:00
         compiled from "D:\wwwroot\demo.52jscn.com\module\wxsite\template\member.html" */ ?>
<?php /*%%SmartyHeaderCode:167285cd56b082ee039-56692343%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e546bb1b23a2fd4ebe8b92c36fd3a5889bb0ee99' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\module\\wxsite\\template\\member.html',
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
  'nocache_hash' => '167285cd56b082ee039-56692343',
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
  'unifunc' => 'content_5cd56b088b5646_08171616',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56b088b5646_08171616')) {function content_5cd56b088b5646_08171616($_smarty_tpl) {?><!DOCTYPE html>
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
/public/wxsite/css/new_shopshow.css">


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

 
 
 
</head>
<body>  
 
	
	
<style>
.distributioncore_head_jump {
    height: 26px;
    padding: 0 7px 0 10px;
    margin-top: -13px;
    display: -webkit-box;
    display: -moz-box;
    -webkit-box-align: center;
    -moz-box-align: center;
    background: rgba(0,0,0,.25);
    border-radius: 50px 0 0 50px;
    position: absolute;
    top: 12%;
    right: 0;
    z-index: 999;
	font-size: 100%;
    color: #666;
    font-family: '微软雅黑';
}
.distributioncore_head_jump span {
    font-size: 12px;
    color: #fff;
    display: block;
}
.distributioncore_head_jump i {
    margin-left: 5px;
    font-size: 14px;
    color: #fff;
    display: block;
}
</style>

<div id="wrapper" style="top:0px;">
    <div id="scroller">


        <!-------------------------登录------------------------->
        <!--基本信息-->
        <div class="peceTopCon" style="position:relative;background-image:<?php if ($_smarty_tpl->tpl_vars['color']->value=='green'){?>url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/images/background02.png)<?php }elseif($_smarty_tpl->tpl_vars['color']->value=='yellow'){?>url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/images/background03.png)<?php }else{ ?>url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/images/background01.png)<?php }?>">
            <?php if ($_smarty_tpl->tpl_vars['is_open_distribution']->value==1&&!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
				<div style='top:50%;' class="distributioncore_head_jump" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/distribution_center"),$_smarty_tpl);?>
');">
					<span style='margin-top:0px'>分销中心</span>
					<i class="fa fa-angle-right"></i>
				</div>
			<?php }?>
			<div class="peceTopImg" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/myaccount"),$_smarty_tpl);?>
');">
			<?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])||$_smarty_tpl->tpl_vars['WeChatType']->value==1){?>
            <img <?php ob_start();?><?php echo (($tmp = @$_smarty_tpl->tpl_vars['member']->value['logo'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['userlogo']->value : $tmp);?>
<?php $_tmp1=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>$_tmp1),$_smarty_tpl);?>
 />
            <?php }else{ ?>
            <img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/images/toux.png" />
            <?php }?>
            </div>
            <span>
                <?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
                     <?php echo $_smarty_tpl->tpl_vars['member']->value['username'];?>

                 <?php }else{ ?>
                立即登录
                <?php }?>
            </span>
        </div>
    <!-----------------------账户信息----------------------->
    <div class="peceInfoCon">
        <ul>
            <li onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/memcard"),$_smarty_tpl);?>
');"><?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])||$_smarty_tpl->tpl_vars['WeChatType']->value==1){?><span style="color:#fe5858;font-size:16px;font-weight:bold;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['member']->value['cost'])===null||$tmp==='' ? '0' : $tmp);?>
</span> <?php }else{ ?><i class="icon icon_ye"></i> <?php }?><span>账号余额</span></li>
            <li onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/juan"),$_smarty_tpl);?>
');"><?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])||$_smarty_tpl->tpl_vars['WeChatType']->value==1){?><span style="color:#ff9500;font-size:16px;font-weight:bold;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['juanshu']->value)===null||$tmp==='' ? '0' : $tmp);?>
</span><?php }else{ ?> <i class="icon icon_yhq"></i><?php }?><span>优惠券</span></li>
            <li onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/gift"),$_smarty_tpl);?>
');"><?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])||$_smarty_tpl->tpl_vars['WeChatType']->value==1){?><span style="color:#5e5cdf;font-size:16px;font-weight:bold;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['member']->value['score'])===null||$tmp==='' ? '0' : $tmp);?>
</span><?php }else{ ?> <i class="icon icon_jf"></i><?php }?><span>积分</span></li>
        </ul>
    </div>
    <!-----------------------功能分类----------------------->
    <div class="peceFunclaCon">
        <ul class="peceFun">
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/address"),$_smarty_tpl);?>
');">
            <i class="icon icon_dizhi"></i>
            <ul>
                <li style="border:none;"><span>管理收货地址</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
        </ul>
        <ul class="peceFun">
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/collect"),$_smarty_tpl);?>
');">
            <i class="icon icon_shouc"></i>
            <ul>
                <li><span>我的收藏</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/giftlist"),$_smarty_tpl);?>
');">
            <i class="icon icon_duih"></i>
            <ul>
                <li <?php if ($_smarty_tpl->tpl_vars['userextensionsharejuan']->value==0&&$_smarty_tpl->tpl_vars['WeChatType']->value==1){?><?php }else{ ?> style="border:none;"<?php }?>><span>兑换礼品</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
            <?php if ($_smarty_tpl->tpl_vars['userextensionsharejuan']->value==0&&$_smarty_tpl->tpl_vars['WeChatType']->value==1){?>
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/memsharej"),$_smarty_tpl);?>
');">
                <i class="icon icon_hongb"></i>
                <ul>
                    <li style="border:none;"><span>邀请好友送红包</span><i class="fa fa-angle-right"></i></li>
                </ul>
            </li>
            <?php }?>
        </ul>
        <ul class="peceFun">
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/binding"),$_smarty_tpl);?>
');">
            <i class="icon icon_bdsjh"></i>
            <ul>
                <li ><span>绑定手机号</span><b><?php if (!empty($_smarty_tpl->tpl_vars['userinfo']->value['phone'])){?>（已绑定<?php echo $_smarty_tpl->tpl_vars['phone']->value;?>
）<?php }else{ ?>（绑定后才能获取到红包哦）<?php }?></b><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
			 <?php if ($_smarty_tpl->tpl_vars['WeChatType']->value==1){?>
            <?php if ($_smarty_tpl->tpl_vars['is_showbd']->value==1){?>
            <li class="peceCla" onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/bangdmem"),$_smarty_tpl);?>
');">
            <i class="icon icon_bdzh"></i>
            <ul>
                <li><span>绑定账号</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
            <?php }?>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['member']->value['group']!=3){?>
            <li class="peceCla applyshop">
            <i class="icon icon_sjrz"></i>
            <ul>
                <li style="border-bottom: 0px solid #e6e6e6;" ><span>商家入驻</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
            <?php }?>

        </ul>

        <ul class="peceFun">
            <?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
            <li class="peceCla"   onclick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/loginout"),$_smarty_tpl);?>
');">
            <i class="icon icon_outlogin"></i>
            <ul>
                <li style="border:none;"><span>退出登录</span><i class="fa fa-angle-right"></i></li>
            </ul>
            </li>
            <?php }?>
        </ul>
    </div>

<div style="height:13px;"></div>
</div>
</div>



<script>
    $(function(){
        function isWeiXin(){
            var ua = window.navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i) == 'micromessenger'){
                return true;
            }else{
                return false;
            }
        }

        var browser = {

            versions: function () {
                var u = navigator.userAgent, app = navigator.appVersion;

                return {
                    trident: u.indexOf('Trident') > -1, //IE内核
                    presto: u.indexOf('Presto') > -1, //opera内核
                    webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                    gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                    mobile: !!u.match(/AppleWebKit.*Mobile.*/) || !!u.match(/AppleWebKit/), //是否为移动终端
                    ios: !!u.match(/(i[^;]+;(U;)? CPU.+Mac OS X)/), //ios终端
                    android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
                    iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
                    iPad: u.indexOf('iPad') > -1, //是否iPad
                    webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
                };
            } (),
            language: (navigator.browserLanguage || navigator.language).toLowerCase()
        }
        if (browser.versions.mobile ) {


            if(isWeiXin()){
                $("#icon-bangdmem").show();
            <?php if ($_smarty_tpl->tpl_vars['wxLoginType']->value==1){?>
                $("#icon-exit").show();
            <?php }else{ ?>
                $("#icon-exit").hide();
            <?php }?>
            }else{

                //	$("#icon-bangdmem").hide();
                $("#icon-exit").show();

            }

        }

    });
	$(function(){
		$('.applyshop').bind('click',function(){
			var memshopid = '<?php echo $_smarty_tpl->tpl_vars['member']->value['shopid'];?>
';
			var is_pass = '<?php echo $_smarty_tpl->tpl_vars['shoppass']->value;?>
';
			//alert(is_pass);
			if(memshopid > 0){
				if(is_pass==0){
					Tmsg('你已提交入驻申请，正在审核中');
					return false;
				}else{
					Tmsg('你已入驻成功，请登录商家端管理店铺');
					return false;
				}				
			}else{
				var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/shopSettled"),$_smarty_tpl);?>
';
				window.location.href= url;
			}
		});
	});
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