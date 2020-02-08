<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 20:12:52
         compiled from "D:\wwwroot\demo.52jscn.com\module\wxsite\template\index.html" */ ?>
<?php /*%%SmartyHeaderCode:40165cd56ac4a05068-50223160%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47f90814c55dc0c399f5dbe483d7b4eb778ca4f9' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\module\\wxsite\\template\\index.html',
      1 => 1538804510,
      2 => 'file',
    ),
    '4b97aef3851e1132e5992791a8cc3a88d668229a' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\public\\wxsite.html',
      1 => 1538873332,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '40165cd56ac4a05068-50223160',
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
  'unifunc' => 'content_5cd56ac524a7a8_06596157',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd56ac524a7a8_06596157')) {function content_5cd56ac524a7a8_06596157($_smarty_tpl) {?><!DOCTYPE html>
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
 
<div data-role="page" > 


<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/weixinlunbo.css">
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/swiper-3.4.1.min.css">
<link rel="stylesheet"  href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/wxsite/css/tc114.css">
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/Swiper/idangerous.swiper.js"></script> 
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/js/jquerymobile/jquery.mobile.min.css" />  
<div class="home_change_head_top">
  <div class="home_change_head_topb" style='position:relative'>
    <div class="home_change_head_left" onClick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/choice"),$_smarty_tpl);?>
');"  > <img src="/templates/m7/public/wxsite/images/icon_home_dw.png" /> <span lag="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lat']->value)===null||$tmp==='' ? 0 : $tmp);?>
" lat="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lng']->value)===null||$tmp==='' ? 0 : $tmp);?>
" id="showareainfo" ><?php if ($_smarty_tpl->tpl_vars['areaid']->value>0){?><?php echo $_smarty_tpl->tpl_vars['mapname']->value;?>
<?php }else{ ?>定位中...<?php }?></span> <i class="fa fa-angle-right"></i> 
	</div>
<?php if ($_smarty_tpl->tpl_vars['is_open_weather']->value==1){?>	
	<div class="home_change_head_center" style='color:#fff;position: absolute;top: 10px;right: 10px;font-size:16px;'>
	    <img src='<?php echo $_smarty_tpl->tpl_vars['weatherinfo']->value['img'];?>
' style='width:25px;height:25px;margin-bottom: -6px;'><?php echo $_smarty_tpl->tpl_vars['weatherinfo']->value['tmp'];?>

	</div>
<?php }?>	
    <div class="home_change_head_right" onClick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/search"),$_smarty_tpl);?>
');"  >
      <div class="home_change_head_rightinp">
        <input type="text" readonly  placeholder="输入商家或商品名称" />
      </div>
    </div>
	 
  </div>
</div>
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['CITY_ID']->value;?>
" />
<div id="wxbglogo" style="width:100%;text-align:center;display:none;position: fixed; top: 0px;z-index:-1;
 "><img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxbglogo']->value)),$_smarty_tpl);?>
  alt="" style="width:40%;width:40%;margin-top:30px;"></div>

	
	 

 <div id="loadindecContent"> </div>
 <div id="nearnoshop" style="display:none;">
  <div id="nearnoshopshowBox" style="background: #fff;"  >
     <center>
      <div id="noshop1" style="margin-bottom: 0px;height: 115px;width: 250px;"><img style="width: 140px;" <?php echo FUNC_function(array('type'=>'img','link'=>"/upload/images/nearnoshop.png"),$_smarty_tpl);?>
 ></div>
      <div id="noshop2" style="height:55px;line-height:35px;color: #a6a6a6;font-size: 14px;">附近暂未覆盖商家，敬请期待 ... </div>
      <div id="noshop3" style="width: 100px;height:38px;line-height:38px;background: #ff6e6e;text-align:center;border-radius: 5px;" onClick="dolink('<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/choice"),$_smarty_tpl);?>
');"><span style="color:#fff;">修改地址</span></div>
    </center>
   </div>
</div>
 <script> 
 showLoading();
var can_show = true;
 var catid = <?php echo (($tmp = @$_smarty_tpl->tpl_vars['typeid']->value)===null||$tmp==='' ? 0 : $tmp);?>
;
var order = 0;
var qsjid = 0;
var typeid = <?php echo (($tmp = @$_smarty_tpl->tpl_vars['typeid']->value)===null||$tmp==='' ? 0 : $tmp);?>
;
var myaddress = '<?php echo $_smarty_tpl->tpl_vars['myaddress']->value;?>
';
var search_input = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['search_input']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var shopshowtype  = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['shopshowtype']->value)===null||$tmp==='' ? '0' : $tmp);?>
';
var checknext = false;
var lat = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lat']->value)===null||$tmp==='' ? 0 : $tmp);?>
';
var lng = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['lng']->value)===null||$tmp==='' ? 0 : $tmp);?>
';
var addressname = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['addressname']->value)===null||$tmp==='' ? '' : $tmp);?>
';
var CITY_ID = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['CITY_ID']->value)===null||$tmp==='' ? 0 : $tmp);?>
';
var is_loading = false;  
var GPS = {
    PI : 3.14159265358979324,
    x_pi : 3.14159265358979324 * 3000.0 / 180.0,
    delta : function (lat, lon) {
        // Krasovsky 1940
        //
        // a = 6378245.0, 1/f = 298.3
        // b = a * (1 - f)
        // ee = (a^2 - b^2) / a^2;
        var a = 6378245.0; //  a: 卫星椭球坐标投影到平面地图坐标系的投影因子。
        var ee = 0.00669342162296594323; //  ee: 椭球的偏心率。
        var dLat = this.transformLat(lon - 105.0, lat - 35.0);
        var dLon = this.transformLon(lon - 105.0, lat - 35.0);
        var radLat = lat / 180.0 * this.PI;
        var magic = Math.sin(radLat);
        magic = 1 - ee * magic * magic;
        var sqrtMagic = Math.sqrt(magic);
        dLat = (dLat * 180.0) / ((a * (1 - ee)) / (magic * sqrtMagic) * this.PI);
        dLon = (dLon * 180.0) / (a / sqrtMagic * Math.cos(radLat) * this.PI);
        return {'lat': dLat, 'lon': dLon};
    },
     
    //WGS-84 to GCJ-02
    gcj_encrypt : function (wgsLat, wgsLon) {
        if (this.outOfChina(wgsLat, wgsLon))
            return {'lat': wgsLat, 'lon': wgsLon};
 
        var d = this.delta(wgsLat, wgsLon);
        return {'lat' : wgsLat + d.lat,'lon' : wgsLon + d.lon};
    },
    //GCJ-02 to WGS-84
    gcj_decrypt : function (gcjLat, gcjLon) {
        if (this.outOfChina(gcjLat, gcjLon))
            return {'lat': gcjLat, 'lon': gcjLon};
         
        var d = this.delta(gcjLat, gcjLon);
        return {'lat': gcjLat - d.lat, 'lon': gcjLon - d.lon};
    },
    //GCJ-02 to WGS-84 exactly
    gcj_decrypt_exact : function (gcjLat, gcjLon) {
        var initDelta = 0.01;
        var threshold = 0.000000001;
        var dLat = initDelta, dLon = initDelta;
        var mLat = gcjLat - dLat, mLon = gcjLon - dLon;
        var pLat = gcjLat + dLat, pLon = gcjLon + dLon;
        var wgsLat, wgsLon, i = 0;
        while (1) {
            wgsLat = (mLat + pLat) / 2;
            wgsLon = (mLon + pLon) / 2;
            var tmp = this.gcj_encrypt(wgsLat, wgsLon)
            dLat = tmp.lat - gcjLat;
            dLon = tmp.lon - gcjLon;
            if ((Math.abs(dLat) < threshold) && (Math.abs(dLon) < threshold))
                break;
 
            if (dLat > 0) pLat = wgsLat; else mLat = wgsLat;
            if (dLon > 0) pLon = wgsLon; else mLon = wgsLon;
 
            if (++i > 10000) break;
        }
        //console.log(i);
        return {'lat': wgsLat, 'lon': wgsLon};
    },
    //GCJ-02 to BD-09
    bd_encrypt : function (gcjLat, gcjLon) {
        var x = gcjLon, y = gcjLat;  
        var z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * this.x_pi);  
        var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * this.x_pi);  
        bdLon = z * Math.cos(theta) + 0.0065;  
        bdLat = z * Math.sin(theta) + 0.006; 
        return {'lat' : bdLat,'lon' : bdLon};
    },
    //BD-09 to GCJ-02
    bd_decrypt : function (bdLat, bdLon) {
        var x = bdLon - 0.0065, y = bdLat - 0.006;  
        var z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * this.x_pi);  
        var theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * this.x_pi);  
        var gcjLon = z * Math.cos(theta);  
        var gcjLat = z * Math.sin(theta);
        return {'lat' : gcjLat, 'lon' : gcjLon};
    },
    //WGS-84 to Web mercator
    //mercatorLat -> y mercatorLon -> x
    mercator_encrypt : function(wgsLat, wgsLon) {
        var x = wgsLon * 20037508.34 / 180.;
        var y = Math.log(Math.tan((90. + wgsLat) * this.PI / 360.)) / (this.PI / 180.);
        y = y * 20037508.34 / 180.;
        return {'lat' : y, 'lon' : x};
        /*
        if ((Math.abs(wgsLon) > 180 || Math.abs(wgsLat) > 90))
            return null;
        var x = 6378137.0 * wgsLon * 0.017453292519943295;
        var a = wgsLat * 0.017453292519943295;
        var y = 3189068.5 * Math.log((1.0 + Math.sin(a)) / (1.0 - Math.sin(a)));
        return {'lat' : y, 'lon' : x};
        //*/
    },
    // Web mercator to WGS-84
    // mercatorLat -> y mercatorLon -> x
    mercator_decrypt : function(mercatorLat, mercatorLon) {
        var x = mercatorLon / 20037508.34 * 180.;
        var y = mercatorLat / 20037508.34 * 180.;
        y = 180 / this.PI * (2 * Math.atan(Math.exp(y * this.PI / 180.)) - this.PI / 2);
        return {'lat' : y, 'lon' : x};
        /*
        if (Math.abs(mercatorLon) < 180 && Math.abs(mercatorLat) < 90)
            return null;
        if ((Math.abs(mercatorLon) > 20037508.3427892) || (Math.abs(mercatorLat) > 20037508.3427892))
            return null;
        var a = mercatorLon / 6378137.0 * 57.295779513082323;
        var x = a - (Math.floor(((a + 180.0) / 360.0)) * 360.0);
        var y = (1.5707963267948966 - (2.0 * Math.atan(Math.exp((-1.0 * mercatorLat) / 6378137.0)))) * 57.295779513082323;
        return {'lat' : y, 'lon' : x};
        //*/
    },
    // two point's distance
    distance : function (latA, lonA, latB, lonB) {
        var earthR = 6371000.;
        var x = Math.cos(latA * this.PI / 180.) * Math.cos(latB * this.PI / 180.) * Math.cos((lonA - lonB) * this.PI / 180);
        var y = Math.sin(latA * this.PI / 180.) * Math.sin(latB * this.PI / 180.);
        var s = x + y;
        if (s > 1) s = 1;
        if (s < -1) s = -1;
        var alpha = Math.acos(s);
        var distance = alpha * earthR;
        return distance;
    },
    outOfChina : function (lat, lon) {
        if (lon < 72.004 || lon > 137.8347)
            return true;
        if (lat < 0.8293 || lat > 55.8271)
            return true;
        return false;
    },
    transformLat : function (x, y) {
        var ret = -100.0 + 2.0 * x + 3.0 * y + 0.2 * y * y + 0.1 * x * y + 0.2 * Math.sqrt(Math.abs(x));
        ret += (20.0 * Math.sin(6.0 * x * this.PI) + 20.0 * Math.sin(2.0 * x * this.PI)) * 2.0 / 3.0;
        ret += (20.0 * Math.sin(y * this.PI) + 40.0 * Math.sin(y / 3.0 * this.PI)) * 2.0 / 3.0;
        ret += (160.0 * Math.sin(y / 12.0 * this.PI) + 320 * Math.sin(y * this.PI / 30.0)) * 2.0 / 3.0;
        return ret;
    },
    transformLon : function (x, y) {
        var ret = 300.0 + x + 2.0 * y + 0.1 * x * x + 0.1 * x * y + 0.1 * Math.sqrt(Math.abs(x));
        ret += (20.0 * Math.sin(6.0 * x * this.PI) + 20.0 * Math.sin(2.0 * x * this.PI)) * 2.0 / 3.0;
        ret += (20.0 * Math.sin(x * this.PI) + 40.0 * Math.sin(x / 3.0 * this.PI)) * 2.0 / 3.0;
        ret += (150.0 * Math.sin(x / 12.0 * this.PI) + 300.0 * Math.sin(x / 30.0 * this.PI)) * 2.0 / 3.0;
        return ret;
    }
};
 	<?php if (!empty($_smarty_tpl->tpl_vars['lng']->value)&&!empty($_smarty_tpl->tpl_vars['lat']->value)&&!empty($_smarty_tpl->tpl_vars['addressname']->value)){?>
 		  loadindexcontent();
		  $('#showareainfo').text(addressname);
    <?php }else{ ?>   
		<?php if (is_array($_smarty_tpl->tpl_vars['signPackage']->value)&&$_smarty_tpl->tpl_vars['Taction']->value=='index'){?>
		
		<?php }else{ ?>
		var options = {
			enableHighAccuracy: true,
			maximumAge: 30000,
			timeout: 12000
		} 
		window.locationCallback = function(data1,data2){
		   gpstolng(data1,data2);
		}
		var str = '<iframe src="javascript:(function(){window.navigator.geolocation.getCurrentPosition(function(position){  parent && parent.locationCallback && parent.locationCallback(position.coords.latitude,position.coords.longitude);        }, function(err){}, {enableHighAccuracy : '+ options.enableHighAccuracy +', maximumAge : '+ options.maximumAge +', timeout :'+ options.timeout +'});})()" style="display:none;"></iframe>';
		$('body').append(str);
	   <?php }?>
    <?php }?> 

function demoAddress(){
		var lng = '113.543806';
		var lat = '34.80233';
		 <!-- var GPS = new GPS; --> ,
		var arr2 = GPS.gcj_encrypt(34.803722, 113.54554);
		//alert(arr2['lat']+","+arr2['lon']);
		console.log(arr2['lat']+","+arr2['lon']);
		<!-- var loationdo = 	new WGS84_to_GCJ02().transform(34.80233, 113.543806); -->
		<!-- alert(loationdo); -->
		var formatted_address = '河南省电子商务产业园';
		var adcode = '410100';
		 $.ajax({
           type: 'GET', 
           url: '<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/saveloation/datatype/json"),$_smarty_tpl);?>
',
           async:false,
 		   data: {'lat':lat,'lng':lng,'addressname':formatted_address,'adcode':adcode},
           dataType: 'json',success: function(content) { 
               if(content.error == false){ 
					 var areainfo = content.msg.areainfoone;
  					 if( areainfo == '' || areainfo.name == undefined ){
						 setTimeout('goChoiceAdr()',1000);
					 }else{
						CITY_ID = areainfo.adcode;
						loadindexcontent();
					 }
					 
             }else{
             	  loadindexcontent();
             }
	    	  },
           error: function(content) { 
				loadindexcontent();
	        }
       });
	$("#showareainfo").attr('lng',lng);
	$("#showareainfo").attr('lat',lat);
	$("#showareainfo").text(formatted_address);
}  





function getLocation(){
     if (navigator.geolocation)
    { 
       navigator.geolocation.getCurrentPosition(showPosition,showError);
    }
   else{
    
	loadindexcontent();
     $('#showareainfo').text("浏览器不支持定位");
	 setTimeout('goChoiceAdr()',1000);

   }
}  
function showPosition(position)
{  
	gpstolng(position.coords.latitude,position.coords.longitude);
}
function gpstolng(lat,lng){
	var changelnglaturl = '<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
restapi.amap.com/v3/assistant/coordinate/convert?locations='+lng+','+lat+'&coordsys=gps&output=json&key=<?php echo $_smarty_tpl->tpl_vars['map_webservice_key']->value;?>
&callback=changelnglat';
      $.getScript(changelnglaturl); 
} 
function changelnglat(datas){
 	if(datas.status == 1   && datas.info == 'ok' ) {
		var locations = datas.locations;
  		 var getaddurl = '<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
restapi.amap.com/v3/geocode/regeo?output=json&location='+locations+'&key=<?php echo $_smarty_tpl->tpl_vars['map_webservice_key']->value;?>
&radius=1000&extensions=all&callback=newrenderReverse';
		$.getScript(getaddurl);
	} 
} 




function newrenderReverse(datas){
  	if(datas.status == 1   && datas.info == 'OK' ) {
	    var lnglat = '';
		var adcode = datas.regeocode.addressComponent.adcode;
		var aois = datas.regeocode.aois;
		var pois = datas.regeocode.pois;
		var roads = datas.regeocode.roads;
		if( aois.length > 0 ){ 
			var lnglat  = aois[0].location; 
			var formatted_address = aois[0].name;
		}else if( pois.length > 0 ){
			var lnglat  = pois[0].location; 
			var formatted_address = pois[0].address;
		}else if( roads.length > 0 ){
			var lnglat  = roads[0].location; 
			var formatted_address = roads[0].name;
		} 
		if( lnglat != '' ){
				var lnglatarr = lnglat.split(',');
				lng = lnglatarr[0];
				lat = lnglatarr[1];
		}  

		 $.ajax({
           type: 'POST', 
           url: '<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/saveloation/datatype/json"),$_smarty_tpl);?>
',
           async:false,
 		   data: {'lat':lat,'lng':lng,'addressname':formatted_address,'adcode':adcode},
           dataType: 'json',success: function(content) { 
               if(content.error == false){ 
					 var areainfo = content.msg.areainfoone;
  					 if( areainfo == '' || areainfo.name == undefined ){
						 setTimeout('goChoiceAdr()',1000);
					 }else{
						CITY_ID = areainfo.adcode;
						loadindexcontent();
					 }
					 
             }else{
             	  loadindexcontent();
             }
	    	  },
           error: function(content) { 
				loadindexcontent();
	        }
       });  
		 
	 }else{
		 $('#showareainfo').text('定位失败');
		 setTimeout('goChoiceAdr()',1000);
		 loadindexcontent();
 	 }
	$("#showareainfo").attr('lng',lng);
	$("#showareainfo").attr('lat',lat);
	$("#showareainfo").text(formatted_address);
}

 
  function showError(error)
  {
	  setTimeout('goChoiceAdr()',1000);
	  loadindexcontent();
	  $('#showareainfo').text(error.code);
  	$('#showareainfo').text("定位失败");
  	Tmsg("定位失败,请手动选择"); 
   switch(error.code) 
    { 
    case error.PERMISSION_DENIED:
      //x.innerHTML="User denied the request for Geolocation."
    //  $('#showareainfo').text("User denied the request for Geolocation.");
      break;
    case error.POSITION_UNAVAILABLE:
     // x.innerHTML="Location information is unavailable."
      $('#showareainfo').text("Location information is unavailable.");
      break;
    case error.TIMEOUT:
    //  x.innerHTML="The request to get user location timed out."
    //$('#showareainfo').text("The request to get user location timed out.");
      break;
    case error.UNKNOWN_ERROR:
     // x.innerHTML="An unknown error occurred."
     //  $('#showareainfo').text("An unknown error occurred.");
      break;
    } 

	
	
  } 
  
  function loadindexcontent(){
		
		if( CITY_ID <= 0 ){ 
			  var winHeight = $(window).height()-40-33-46-40;
 			  var allHeight = 115+25+50+38;
 			  var paddHeight = (winHeight-allHeight)/1.3;
  			  $('#nearnoshopshowBox').css({'height':winHeight+'px','paddingTop':paddHeight+'px'});
			  $('#loadindecContent').html("");
			  $('#loadindecContent').html( $("#nearnoshop").html() );
			 newhideLoading();
		}else{
				var ajaxurl = '<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/loadindexcontent"),$_smarty_tpl);?>
'; 
				$.ajax({
				   type: 'POST',
				   async:true,
				   url: ajaxurl,
				   data: {},
				  dataType: 'html',success: function(content) {  
						$('#loadindecContent').html(content);  
						newhideLoading();
						
				  },
				  error: function(content) { 
						console.log("加载失败");
						newhideLoading();
				   }
				  });
		} 
   
		 
		
		  
		 
		  
  }
  
function htmlback(url,info)
{
	var backmessage = {'flag':true,'content':''};
	$.ajax({
       type: 'POST',
       async:true,
       url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
       data: info,
      dataType: 'html',success: function(content) {  
	   backmessage['flag'] = false;
      	   backmessage['content'] = content; 
		  },
      error: function(content) { 
      backmessage['content'] = '获取失败';
	   }
   });  
   return backmessage;
}

function goChoiceAdr(){
	 //location.href = '<?php echo FUNC_function(array('type'=>'url','link'=>"/wxsite/choice"),$_smarty_tpl);?>
';
 	 var winHeight = $(window).height()-40-33-46-40;
 			  var allHeight = 115+25+50+38;
 			  var paddHeight = (winHeight-allHeight)/2;
  			  $('#nearnoshopshowBox').css({'height':winHeight+'px','paddingTop':paddHeight+'px'});
			  $('#loadindecContent').html("");
			  $('#loadindecContent').html( $("#nearnoshop").html() );
			  newhideLoading();
}  
$(function(){
		$(window).resize(function(){
			$('#dev_wrapper').css({"max-height":$(window).height()});
			$('body').css({"height":$(window).height()});
		});
	
	}); 
</script>
 <style>
body{-webkit-overflow-scrolling: touch;}
.popup_content{height:80%!important;margin-top:20%;}
</style>  

    <?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tempdir']->value)."/public/bottom.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


</div>
  
 
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