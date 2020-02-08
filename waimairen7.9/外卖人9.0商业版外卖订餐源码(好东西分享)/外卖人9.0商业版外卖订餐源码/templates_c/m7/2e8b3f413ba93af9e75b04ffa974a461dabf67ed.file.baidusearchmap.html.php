<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:27:49
         compiled from "D:\wwwroot\demo.52jscn.com\templates\m7\site\baidusearchmap.html" */ ?>
<?php /*%%SmartyHeaderCode:133875cd56035e7a086-22596555%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e8b3f413ba93af9e75b04ffa974a461dabf67ed' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\m7\\site\\baidusearchmap.html',
      1 => 1536024583,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '133875cd56035e7a086-22596555',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sitename' => 0,
    'keywords' => 0,
    'description' => 0,
    'metadata' => 0,
    'siteurl' => 0,
    'is_static' => 0,
    'controlname' => 0,
    'tempdir' => 0,
    'color' => 0,
    'map_comment_link' => 0,
    'map_javascript_key' => 0,
    'sitelogo' => 0,
    'member' => 0,
    'webcaption' => 0,
    'cook_adrlistcookie' => 0,
    'myid' => 0,
    'items' => 0,
    'CITY_NAME' => 0,
    'citylist' => 0,
    'cityname' => 0,
    'CITY_ID' => 0,
    'default_cityid' => 0,
    'appewm' => 0,
    'wxewm' => 0,
    'itv' => 0,
    'map_webservice_key' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd5603620b3c1_58451624',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd5603620b3c1_58451624')) {function content_5cd5603620b3c1_58451624($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
</title>

<meta name="Keywords" content="<?php echo $_smarty_tpl->tpl_vars['keywords']->value;?>
" />


<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
" />

<?php echo stripslashes($_smarty_tpl->tpl_vars['metadata']->value);?>

<script> 
var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
";
var is_static ="<?php echo $_smarty_tpl->tpl_vars['is_static']->value;?>
";
var controllername= '<?php echo $_smarty_tpl->tpl_vars['controlname']->value;?>
';
</script>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/mnew7/css/map-n.css" />
<?php if ($_smarty_tpl->tpl_vars['color']->value=="green"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/green.css">
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['color']->value=="yellow"){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/css/yellow.css">
<?php }?>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquerynew.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue" type="text/javascript" language="javascript"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
webapi.amap.com/maps?v=1.3&key=<?php echo $_smarty_tpl->tpl_vars['map_javascript_key']->value;?>
&plugin=AMap.Geolocation,AMap.CitySearch"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.cookie.js"></script>  
</head><body style="overflow:auto;background:none;">
<div class="guideTop">
  <div class="guideContent"> <a href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
" > <img <?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['sitelogo']->value)),$_smarty_tpl);?>
 /> </a>
    <div class="mmeberinfo">
      <ul>
        <?php if (!empty($_smarty_tpl->tpl_vars['member']->value['uid'])){?>
        <li><a  target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/base"),$_smarty_tpl);?>
" >你好，<?php echo $_smarty_tpl->tpl_vars['member']->value['username'];?>
</a></li>
        <li style="width:2px;">|</li>
        <li><a  target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/loginout"),$_smarty_tpl);?>
" >退出</a></li>
        <?php }else{ ?>
        <li><a  target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/login"),$_smarty_tpl);?>
" >登录</a></li>
        <li style="width:2px;">|</li>
        <li><a target="_bank" href="<?php echo FUNC_function(array('type'=>'url','link'=>"/member/regester"),$_smarty_tpl);?>
" >注册</a></li>
        <?php }?>
      </ul>
    </div>
  </div>
</div>
<div class="guidebox"style="position:relative; height:480px;">
  <div class="map" id="map">
    <div class="top">
      <div class="guider" style="visibility: visible;height:110px ;width:550px; "id="guider">
	  <img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['webcaption']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/images/guideBg.png";?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['webcaption']->value).$_tmp1),$_smarty_tpl);?>
 style="height:54px;width:550px"/>
	  </div>
      <div class="address clearfix" id="address" style="position:relative;" >
         <div class="fr history-address m-shadow"> <a id="historylist" href="javascript:;" title="历史地址"> <span>历史地址</span> <i class="i-triangle-down"></i> </a>
          <div class="  dialog-historyaddr" style="display: none;position: absolute;
    top: 72px;">
            <div class="map-dialog" style="left:750px; top: 75px;"> <i class="icon i-mapdialog-arr" style="left: 25px;"></i> 
			<?php if (empty($_smarty_tpl->tpl_vars['cook_adrlistcookie']->value)){?>
              <div class="content" style="width: 150px;">
                <div class="address-warp">
                  <ul>
                    <li class="empty">暂无历史地址</li>
                  </ul>
                </div>
              </div>
              <?php }else{ ?>
              <div class="content">
                <div class="address-warp">
                  <ul id="historylistBox">
                    <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cook_adrlistcookie']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?>
                    <?php if ($_smarty_tpl->tpl_vars['myid']->value<=5&&!empty($_smarty_tpl->tpl_vars['items']->value)){?> <a  style="width:240px; font-size:14px; color:#000000; height:50px; line-height:50px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    outline: 0!important; background:#fff; border-bottom: 1px solid #DEDEDE;"  class="name first" href="javascript:void(0)" title="<?php echo $_smarty_tpl->tpl_vars['items']->value[2];?>
">
                    <li  lng="<?php echo $_smarty_tpl->tpl_vars['items']->value[0];?>
" lat="<?php echo $_smarty_tpl->tpl_vars['items']->value[1];?>
" address="<?php echo $_smarty_tpl->tpl_vars['items']->value[2];?>
" adcode="<?php echo $_smarty_tpl->tpl_vars['items']->value[3];?>
"  style="padding:0px 10px;"  ><?php echo $_smarty_tpl->tpl_vars['items']->value[2];?>
</li>
                    </a> <?php }?>		 
                    <?php } ?>
                  </ul>
                </div>
              </div>
              <?php }?> </div>
          </div>
        </div>
        <div class="fl current-city m-shadow"  style="position:relative;"   > 
			<a href="javascript:void(0);" class="city" id="selectcity"> 
			<span id="cityNameText"  ><?php if (!empty($_smarty_tpl->tpl_vars['CITY_NAME']->value)){?><?php echo $_smarty_tpl->tpl_vars['CITY_NAME']->value;?>
<?php }else{ ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php }?></span>  
			</a> 
			
			 
			
			
			<div class="dialog dialog-citylist" id="showMoreCityList" style="display:none;z-index:999999999999999;">
			  <div class="map-dialog"  > <i class="icon i-mapdialog-arr" style="left: 58px;"></i>
				<div class="content jspScrollable" style="height: auto; overflow: auto; padding: 0px; width: 733px;" >
				  <div class="jspContainer" style="width: 733px; min-height: 200px;">
					<div class="jspPane" style="padding: 15px 25px; width: 661px; top: 0px;">
					  <div class="guess clearfix "><span class="fl">猜你在：</span><a id="guessCity"  data-cityid=""   data-name="" class="fl borderradius-2 city-target" href="javascript:void(0);"></a></div>
					  <div id="input-city" class="search-city ct-deepgrey "> <span class="fl">直接搜索</span>
						<input type="text" id="searchCityKey" class="input-city fl" value="" autocomplete="off" placeholder="城市名称">
						<i class="icon i-search"></i>
						<div id="city-content" style="display:none;" class="ct-deepgrey">
							<ul>
 							</ul>
						</div>
					  </div>
					 
					  <div class="field clearfix " id="citylistBox"> 
					  <?php if (!empty($_smarty_tpl->tpl_vars['citylist']->value)){?>
						<ul class="clearfix">
						<?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['citylist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
                                                <li><a  data-cityid="<?php echo $_smarty_tpl->tpl_vars['items']->value['adcode'];?>
"  data-name="<?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
" class="city-target" href="javascript:void(0);" title="<?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</a></li>
						 <?php } ?>
						</ul>
					 <?php }else{ ?>
						<ul class="clearfix">
							<div style="color:#949494;">未开通其它城市...</div>
						</ul>
					<?php }?>
					
					  </div> 
					</div> 
				  </div>
				</div>
			  </div>
			</div>

			
			
			
		</div>
        <div class="fl address-input">
          <div class="input-container clearfix m-shadow" style="position:relative;">
            <input type="text" id="searchKeywords" placeholder="输入地址搜索周边商家" class="fl" />
            <input type="hidden" name="cityname" id="cityname" value="<?php if (!empty($_smarty_tpl->tpl_vars['CITY_NAME']->value)){?><?php echo $_smarty_tpl->tpl_vars['CITY_NAME']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['cityname']->value;?>
<?php }?>" />
            <input type="hidden"  name="cityid" value="<?php if (!empty($_smarty_tpl->tpl_vars['CITY_ID']->value)){?><?php echo $_smarty_tpl->tpl_vars['CITY_ID']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['default_cityid']->value;?>
<?php }?>" />
            <a href="javascript:void(0);" class="fl" id="searchBtn">搜索</a> </div>
			
			
			
			
			
			
			
			
			
 <div class="search-show s-item search-sug" id="searchShowAdrList" style="display:none;" >
  <ul id="ghWaimaiAdd" >			 
	 
</ul>
</div>
			
			
			
			
			
			
			
			
			
			
        </div>
      </div>
    </div>
    
   </div>
</div>
 
  <div style=" display:;">
<div style="height:220px;   background:#fff; ">
	<ul style="width:400px; margin: 70px auto 0px;">
		<li style="float:left;">
			<img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['appewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6app_ewm.png";?><?php }?><?php $_tmp2=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['appewm']->value).$_tmp2),$_smarty_tpl);?>
 alt="手机APP下载" width="142" height="142"  /><br><br>
			<span style="margin-left:30px;font-size:14px">手机APP下载
			</span>
		</li>
		<li style="float:right; border-left:1px solid #e6e6e6; padding-left:55px; ">
			<img <?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['wxewm']->value==''){?><?php echo (string)$_smarty_tpl->tpl_vars['siteurl']->value;?><?php echo "/upload/app/m6wx_ewm.png";?><?php }?><?php $_tmp3=ob_get_clean();?><?php echo FUNC_function(array('type'=>'img','link'=>((string)$_smarty_tpl->tpl_vars['wxewm']->value).$_tmp3),$_smarty_tpl);?>
 alt="微信端扫描二维码" width="142" height="142"  /><br><br>
			<span style="margin-left:16px;font-size:14px">微信端扫描二维码</span>
		</li>
	</ul>
</div>
<div id="iCenter" style="display:none;"></div>
 <script>
var defaultCityName = '<?php echo $_smarty_tpl->tpl_vars['cityname']->value;?>
'; 
var defaultCityID = '<?php echo $_smarty_tpl->tpl_vars['default_cityid']->value;?>
'; 
 
var CITY_NAME = '<?php echo $_smarty_tpl->tpl_vars['CITY_NAME']->value;?>
';
var location_cityname;
var location_cityId;
var cityname;
var cityId;
var adrlistcookie = new Array();
$(function(){
	 getlocation();
	 
	 <?php if (!empty($_smarty_tpl->tpl_vars['cook_adrlistcookie']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cook_adrlistcookie']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
			var adrtempary = new Array();
			<?php  $_smarty_tpl->tpl_vars['itv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['itv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['itv']->key => $_smarty_tpl->tpl_vars['itv']->value){
$_smarty_tpl->tpl_vars['itv']->_loop = true;
?> 
				adrtempary.push('<?php echo $_smarty_tpl->tpl_vars['itv']->value;?>
');
			<?php } ?>
			adrlistcookie.push(adrtempary);
		<?php } ?>
 	<?php }?> 
	
});

function getlocation(){
	
		//添加定位组件，用于获取用户当前的精确位置
		var geolocation = new AMap.Geolocation({
			showCircle: false, //不显示定位结果的圆
			showMarker: false, //不显示定位结果的标记
			showButton: false, //不显示组件的定位按钮
			timeout: 5000 //浏览器定位超时时间5s
		});
		geolocation.getCurrentPosition(function(status, result) {
			
			 if (status == 'complete') {
				onLocateSuccess(result); //定位成功
			} else if (status == 'error') {
				//定位失败
				if (result.message.indexOf('Geolocation permission denied.') !== -1) {
				//Geolocation permission denied.表示用户禁用了浏览器或者APP的定位权限或者关闭了手机的定位服务
				//或者当前页面为非安全页面,Chrome或者IOS10等系统会禁用非安全页面的定位请求
				//如果您的页面还没有支持HTTPS请尽快升级
				//安全页面指的是支持HTTPS的Web站点，而且是通过https协议打开的页面。安全页面也包括本地页面
					//diaerror('您好，请在系统的隐私设置中打开当前应用的定位权限。');
					showCityInfo();
				} else {
					//diaerror('无法获取精确位置,将定位您所在的城市。');
					showCityInfo();
				}
			onLocateFailed();
			}
		})
		//定位失败之后进行城市定位
		var onLocateFailed = function() {
			geolocation.getCityInfo(function(status, result) {
				if (status == 'complete') {
					 
					if( result.status == 1 && result.info == 'SUCCESS' ){
						var center_lng = result.center.lng;
						var center_lat = result.center.lat;
						var location_adcode = result.adcode;
						checkOpenCity(location_adcode);
					}
				} else if (status == 'error') {
					//diaerror("获取位置失败");
				}
			})
		};
		//定位成功
		var onLocateSuccess = function(result) {
 			 if( result.status == 1 && result.info == 'SUCCESS' ){
				var location_lng = result.position.lng;
				var location_lat = result.position.lat;
				var location_adcode = result.addressComponent.adcode;
				
				location_cityname = result.addressComponent.city;
			    location_cityId = result.addressComponent.adcode; 
				
  				checkOpenCity(location_adcode);
			}
		};

	

}

 //获取用户所在城市信息
    function showCityInfo() {
        //实例化城市查询类
        var citysearch = new AMap.CitySearch();
        //自动获取用户IP，返回当前城市
        citysearch.getLocalCity(function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
				 
                if (result && result.city && result.bounds) {
			 
                   location_cityname = result.city;
					location_cityId = result.adcode;  
  				    checkOpenCity(location_cityId);
                }
            } else {
               // document.getElementById('tip').innerHTML = result.info;
            }
        });
    }
	
	
$("#historylist").click(function(){
	$(".dialog-historyaddr").toggle();
});

function checkOpenCity(adcode){
	var url= siteurl+'/index.php?ctrl=site&action=checkOpenCity&datatype=json&random=@random@';
    url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    var bk = ajaxback(url,{'adcode':adcode}); 
	if(bk.flag == false){
 		if( bk.content != '' ){ 
			cityname = bk.content.name;
			cityId = bk.content.adcode;  
		}
	}else{
		diaerror(bk.content);
	}
	func_inputvalue();
	
}

$("#selectcity").click(function(event){ 
	$('#guessCity').text(location_cityname);
	$('#guessCity').attr('data-cityid',location_cityId); 
	$('#guessCity').attr('data-name',location_cityname); 
	 $('#showMoreCityList').toggle();
	 event.stopPropagation(); 
});

$("#showMoreCityList").click(function(event){ 
 	 event.stopPropagation(); 
}); 
$("body").click(function(){ 
	 $('#showMoreCityList').hide(); 
});
  

var biaoqianval = false;
function bqzhi(){
	biaoqianval  = false;
}
$("#searchCityKey").bind('click',function(e){
searchAdCodelist();  
});
$("#searchCityKey").bind('keyup',function(e){
					if(biaoqianval == false){
						biaoqianval  = true;
						setTimeout("bqzhi()", 500 );  
							searchAdCodelist();	  
					}
}); 
function searchAdCodelist(){ 
	var searchval  = $("#searchCityKey").val();
 								if( searchval != '' && searchval != undefined ){
									$('#city-content').show();
									$('#city-content ul').html('');
									var info = {'searchval':searchval}; 
									var url = '<?php echo FUNC_function(array('type'=>'url','link'=>"/site/getcitylist/datatype/json"),$_smarty_tpl);?>
';
									  var backdata = ajaxback(url,info); 
									  if(backdata.flag == false){ 
 											var adrcodelist = backdata.content;
											if( adrcodelist.length > 0 ){
												var htmls = '';
												$.each(adrcodelist, function(i, newobj) {
												  htmls += '<li name="'+newobj.name+'"  adcode="'+newobj.adcode+'"  ><a href="javascript:void(0);" class="ca-deepgrey city-target active" data-pinyin="anda" data-cityid="231281">'+newobj.name+'</a></li>';
 												});
 												$('#city-content ul').html(htmls);
												
												$('#city-content ul li').click(function(){
													var adcode = $(this).attr('adcode');
 													var addname = $(this).attr('name');
													if( adcode != ''  && addname != ''  ){  
														cityname = addname;
														cityId = adcode;
														func_inputvalue();
														$('#city-content').hide();
														$('#func_inputvalue').hide();
													}
												});
												
											}else{
												$('#city-content ul').html('<span  class="ca-deepgrey notfound">没有找到符合条件的城市</span>');
 											}
											
									  }else{
 										 diaerror(backdata.content);
									  } 
								} else{
									$('#city-content').hide();
								}


} 

$('#guessCity').click(function(){ 
	cityId = $(this).attr('data-cityid');
	cityname = $(this).attr('data-name');
	func_inputvalue();  
	$('#showMoreCityList').hide();
});
$('#citylistBox ul li a').click(function(){ 
	cityId = $(this).attr('data-cityid');
	cityname = $(this).attr('data-name');
	func_inputvalue();  
	$('#showMoreCityList').hide();
});

function func_inputvalue(){ 
	 
	 if( ( cityname == '' || cityname == undefined ) &&  ( cityId == '' || cityId == undefined ) ){
		 cityname = defaultCityName;
		 cityid = defaultCityID;
	 }

	 $('#cityNameText').text(cityname);
	 $('input[name="cityname"]').val(cityname);
	 $('input[name="cityid"]').val(cityId); 
	 
	 var searchval  = $("#searchKeywords").val();
	 if( searchval != '' && searchval != undefined ){
		func_searchinputAdr();
	 }
	 
}
 
$('#searchBtn').click(function(){
	func_searchinputAdr();
});
 
 $("#searchKeywords").bind('keyup',function(e){
					if(biaoqianval == false){
						biaoqianval  = true;
						setTimeout("bqzhi()", 500 );   
						func_searchinputAdr();
					}
}); 
function func_searchinputAdr(){
		var searchval  = $("#searchKeywords").val();
		var cityname  = $('input[name="cityname"]').val();
		if( searchval != '' && searchval != undefined ){
				var addresslist = '<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
restapi.amap.com/v3/place/text?&keywords='+searchval+'&city='+cityname+'&output=json&offset=20&page=1&key=<?php echo $_smarty_tpl->tpl_vars['map_webservice_key']->value;?>
&extensions=all&callback=showaddresslist';						 
				$.getScript(addresslist); 
		} 								 
}

function showaddresslist(data){
 	var datas = eval(data); 
 	if(datas.info == "OK"  && datas.status == 1  && datas.pois.length > 0 ){
		$('#searchShowAdrList').show();
		$('#searchShowAdrList ul').html('');
		var addresslist = datas.pois;
		//console.log(addresslist);
 		var showhtmls = '';
		if( addresslist != '' ){
 			$.each(addresslist, function(i, newobj) {
			  showhtmls += '<li dataadcode="'+newobj.adcode+'"  dataname="'+newobj.name+'" datalnglat="'+newobj.location+'"    ><i></i><b>'+newobj.name+'</b></li>';
 			});
			 $('#searchShowAdrList ul').append(showhtmls);
 			 $('#searchShowAdrList ul li').bind('click',function(){
				 if( lockclick() ){
					var adcode = $('input[name="cityid"]').val();
					var lnglat = $(this).attr('datalnglat');
					var address = $(this).attr('dataname');
					if( lnglat != '' && address != '' && adcode != '' ){
  						 
						 var lnglatarr = lnglat.split(',');
						 var lng = lnglatarr[0];
						 var lat = lnglatarr[1];  
						 var info = {'lng':""+lng+"",'lat':""+lat+"",'address':""+address+"",'adcode':""+adcode+""}; 
						 var url = siteurl+'/index.php?ctrl=site&action=checkadrinfo&datatype=json';
 						 var backdata = ajaxback(url,info); 
						 if(backdata.flag == false){ 
								var temparr = new Array();
								 temparr.push(lng); 
								 temparr.push(lat); 
								 temparr.push(address); 
								 temparr.push(adcode); 
								 adrlistcookie.push(temparr);
								 var adrlistcookieList = adrlistcookie.join('#');
								 $.cookie("cook_adrlistcookie",adrlistcookieList,{ expires: 365*10});
								 location.href = siteurl;
						 }else{
 								  diaerror(backdata.content);
						}   
					}
				 }
			 });
		}else{
			$('#searchShowAdrList ul').html('');
			$('#searchShowAdrList').hide();
 		}
		 
	}else{
		$('#searchShowAdrList ul').html('');
			$('#searchShowAdrList').hide();
 	}
}


$('#historylistBox li').click(function(){
	if( lockclick() ){
 					var adcode = $(this).attr('adcode');
					var lng = $(this).attr('lng');
					var lat = $(this).attr('lat');
					var address = $(this).attr('address');
					if( lat != '' && lng != '' && address != '' && adcode != '' ){   
							var url= siteurl+'/index.php?ctrl=site&action=checkOpenCity&datatype=json&random=@random@';
							url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
							var bk = ajaxback(url,{'adcode':adcode}); 
							if(bk.flag == false){
								if( bk.content != '' ){
									 adcode = bk.content.adcode;  
									 var info = {'lng':""+lng+"",'lat':""+lat+"",'address':""+address+"",'adcode':""+adcode+""}; 
									 var url = siteurl+'/index.php?ctrl=site&action=checkadrinfo&datatype=json';
									 var backdata = ajaxback(url,info); 
									 if(backdata.flag == false){  
											 location.href = siteurl;
									 }else{
											  diaerror(backdata.content);
									}   
								}
							}else{
								diaerror(bk.content);
							}
						
					}
				 }
});
	 
</script>








</body>
</html>
<?php }} ?>