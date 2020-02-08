<?php /* Smarty version Smarty-3.1.10, created on 2019-05-10 19:24:14
         compiled from "D:\wwwroot\demo.52jscn.com\templates\adminpage\psuser\psymap.html" */ ?>
<?php /*%%SmartyHeaderCode:118025cd55f5edf5066-15652372%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbda3f28eb88584f7de6719031b36a020521f53c' => 
    array (
      0 => 'D:\\wwwroot\\demo.52jscn.com\\templates\\adminpage\\psuser\\psymap.html',
      1 => 1536024618,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118025cd55f5edf5066-15652372',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'siteurl' => 0,
    'tempdir' => 0,
    'map_comment_link' => 0,
    'map_javascript_key' => 0,
    'baidulng' => 0,
    'baidulat' => 0,
    'arealist' => 0,
    'items' => 0,
    'dno' => 0,
    'searchvalue' => 0,
    'toplink' => 0,
    'sitename' => 0,
    'beian' => 0,
    'footerdata' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.10',
  'unifunc' => 'content_5cd55f5f133913_60653584',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5cd55f5f133913_60653584')) {function content_5cd55f5f133913_60653584($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\wwwroot\\demo.52jscn.com\\lib\\Smarty\\libs\\plugins\\modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>配送员-位置</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/commom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/css/shangjiaAdmin.css" />
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/jquery.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/allj.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/artDialog.js?skin=blue"></script> 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/artdialog/plugins/iframeTools.js" type="text/javascript" language="javascript"></script>
 
<script src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/js/datepicker/WdatePicker.js" type="text/javascript" language="javascript"></script>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
cache.amap.com/lbs/static/main1119.css"/>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
webapi.amap.com/maps?v=1.3&key=<?php echo $_smarty_tpl->tpl_vars['map_javascript_key']->value;?>
"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['map_comment_link']->value;?>
cache.amap.com/lbs/static/addToolbar.js"></script>
<script> 
	var siteurl = "<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
"; 
</script>
</head>
<body>
	<div style="position: fixed;top: 0;left: 0;right: 0;bottom: 0;z-index: -1;background:url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/background.png);"></div>
<!---header start--->
<div class="header" style=" height:50px;">
  <div class="top" style=" height:50px;">
   
   
    <div class="topRight fr">  <span style=" height:50px; line-height:50px;cursor: pointer;" class="username" onclick="openlink('<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/orderlist"),$_smarty_tpl);?>
');">返回后台管理<img src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/usernameBg.png" /></span> </div>
    <div class="cl"></div>
    <div class="shangjiaTop" style=" top:-22px; background: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/peisongTopBg.png) no-repeat; margin-left:-150px;">
		<div class="sjglaa11" style="background: url(<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/templates/<?php echo $_smarty_tpl->tpl_vars['tempdir']->value;?>
/public/shopcenter/images/psimgBg.png) no-repeat;"> </div>
    </div>
  </div>
</div>

<!---header end---> 
	<style>
	.dingdanGl li {
    width: 16%;
}
	</style>
	
<div class="main">
	
	<div class="main_titile">
	<div class="main_tl">
		<div class="qhaddress fl">
			 <select name="psarea" onchange="getnewdata();">
            	   	   <option value="0" lng="<?php echo $_smarty_tpl->tpl_vars['baidulng']->value;?>
" lat="<?php echo $_smarty_tpl->tpl_vars['baidulat']->value;?>
">不限制区域</option>
            	   	   <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['arealist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
?>
            	   	   <option value="<?php echo $_smarty_tpl->tpl_vars['items']->value['id'];?>
" lng="<?php echo $_smarty_tpl->tpl_vars['items']->value['lng'];?>
" lat="<?php echo $_smarty_tpl->tpl_vars['items']->value['lat'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['name'];?>
</option>
            	   	   <?php } ?>
            	   	 </select>
		</div>
		<div  class="auto_time fl">
			<span id="showztai" style="color:#666;"  data="20">  </span>
		</div>
		 
		  <div class="closeVoi fl" style="width:30%;">
        <input type="checkbox" name="playwave" id="playwave" value="1"   style="cursor: pointer;">
         播放 待审核提示音</div>
	</div>	 
		 <div class="dingdanGl fl">
		 
			<ul>
				<li   style="cursor: pointer;"   data="0" class="on"><span>配送员位置</span> </li> 
				<li  style="cursor: pointer;"  data="2"><span>待抢调度</span> </li>
				<li   style="cursor: pointer;"  data="3"><span>待完成</span> </li>
				<li   style="cursor: pointer;"  data="4"><span>已完成</span> </li>
				<li   style="cursor: pointer;"  data="5"><span>统计</span> </li>
			</ul>
			<div class="cl"></div>
		 </div>
		
		
	</div>
	<div class="cl"></div>
	
	
	<div class="main_ord_list" style="display:none;" id="main_ord_list">
		<div style="margin-bottom:15px;">
		<div class="chaxun fl">
		
			<input class="chainp" placeholder="输入订单号" type="text" name="dno" id="dno" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['dno']->value)===null||$tmp==='' ? '' : $tmp);?>
" />
			<input class="chaxunhBg" type="button" name="" value="" onclick="getorderdata();" style="cursor: pointer;">
			<div class="cl"></div>
		</div>
		
		<div class="ycOrd fr">
			 
		  
			<label>
				 <input type="checkbox" name="showdet" id="showdet" value="0"  >
					  显示订单详情
			</label>
			
		</div>
			<div class="cl"></div> 
		</div>
		
		
		
		
		<div class="order_list_show" id="order_list_show" >
		<!--订单table-->
			 
		<!--订单table-->
		</div> 
		
		
	</div>
	<!-- 配送员地址-->
	<div class="main_ord_map" id="main_ord_map" style="padding-bottom:30px;">
	
		<div id="psylist">
               	 <ul>
               	 	  <li>配送员1</li>
               	 	  <li>配送员2</li>
               	 	 </ul>
              </div>
		<div id="tagscontent" style="height:700px;width:83%;float:left;">

             

		</div>
		<div style="clear:both;"></div>
	</div>
	
	<div class="main_ord_list" style="display:none;" id="main_ord_listpeytj">
		<div style="margin-bottom:15px;">
		<div class="chaxun fl">
		<input class="chainp" placeholder="输入订单号" type="text" name="searchvalue" id="searchvalue" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['searchvalue']->value)===null||$tmp==='' ? '' : $tmp);?>
" />
		&nbsp;&nbsp;&nbsp;
			从<input style="height:27px;" class="Wdate datefmt" type="text" name="starttime" id="starttime" value="<?php echo smarty_modifier_date_format(time(),"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />  
			到<input style="height:27px;" class="Wdate datefmt" type="text" name="endtime" id="endtime" value="<?php echo smarty_modifier_date_format(time(),"%Y-%m-%d");?>
"  onFocus="WdatePicker({isShowClear:false,readOnly:true});" />                  

		 
			<input  class="chaxunhBg" type="button" name="" value="" onclick="orderpstj();" style="height:29px;background-size: 71px 29px; margin-left:20px;cursor: pointer;float:right;">
			<div class="cl"></div>
		</div>
		
		<div class="ycOrd fr">
			 
		  
			 
			
		</div>
			<div class="cl"></div> 
		</div>
		
		
		
		
		<div class="order_list_show" id="order_list_showxxx" >
		<!--订单table-->
			 
		<!--订单table-->
		</div> 
		
		
	</div>
	
		
		
</div>
	
	
	
	
	
	
	
	
	  <div id="palywave" style="display:none;"></div>
<!------以下是公共的底部部分------->
    <div class="footer">
    	<div class="foot1">
        <center>
        	<div class="db">
        	   <?php if (!empty($_smarty_tpl->tpl_vars['toplink']->value)){?>
	 	      <?php $_smarty_tpl->tpl_vars['toplink'] = new Smarty_variable(unserialize($_smarty_tpl->tpl_vars['toplink']->value), null, 0);?>
		  	  <?php  $_smarty_tpl->tpl_vars['items'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['items']->_loop = false;
 $_smarty_tpl->tpl_vars['myid'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toplink']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['items']->key => $_smarty_tpl->tpl_vars['items']->value){
$_smarty_tpl->tpl_vars['items']->_loop = true;
 $_smarty_tpl->tpl_vars['myid']->value = $_smarty_tpl->tpl_vars['items']->key;
?> 
			         <a href="<?php echo $_smarty_tpl->tpl_vars['items']->value['typeurl'];?>
"><?php echo $_smarty_tpl->tpl_vars['items']->value['typename'];?>
</a> | 
			    <?php } ?>
			<?php }?> 
         
            </div></center>
            <div class="cl"></div>
        </div>
        <div class="foot2">
        	<p>@2008-2012 <?php echo $_smarty_tpl->tpl_vars['sitename']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['beian']->value;?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['footerdata']->value);?>
</p>
        </div>
    </div>
 <script>
   
 	 
 	    
		 
 </script>
 
<!--加载声音-->
  <style>
 	#psylist{
 	width: 15%;
float: left;
height: 700px;
OVERFLOW-Y: auto;
OVERFLOW-X: hidden;
background:#27a9e3;
padding:0px 1%;

 	}
 	#psylist li{
 	  border-bottom:1px solid #ccc;
 	  text-align:left;
 	  height:30px;
 	  line-height:30px;
 color:#fff;
 	}
 	#psylist li:hover,#psylist li.on{
 	  background-color:#f60;
 	  color:#fff;
 	}
 	</style>
<script>
$(function(){
	$('.dingdanGl li').click(function(){
	   $(this).addClass('on').siblings().removeClass('on'); 
		var checkid = $(this).attr('data');
		if(checkid == 0){
		   $('#main_ord_map').show();
		   $('#main_ord_list').hide();
		   $('#main_ord_listpeytj').hide();
		   freshdata();
		}else if(checkid == 5){
		     $('#main_ord_list').hide();
			$('#main_ord_map').hide();
			$('#main_ord_listpeytj').show();
		    orderpstj();
		}else{
			$('#main_ord_list').show();
			$('#main_ord_map').hide();
			$('#main_ord_listpeytj').hide();
			getorderdata();
		}
				
	});
});
function  orderpstj(){
  var url = siteurl+"/index.php?ctrl=adminpage&action=psuser&module=psytj";
  var starttime = $('#starttime').val();
  var endtime = $('#endtime').val();
  var areaid = $("select[name='psarea']").find("option:selected").val(); 
  var searchvalue = $('#searchvalue').val();
   $.post(url,{'starttime':starttime,'endtime':endtime,'areaid':areaid,'searchvalue':searchvalue},function (data, textStatus){ 
		$('#order_list_showxxx').html(data);//hc_list_cont_div3 
	    $('.chakans').click(function(){
			var psuid = $(this).attr('data');
			var url_p = siteurl+"/index.php?ctrl=adminpage&action=psuser&module=getpsyorderlist";
			var starttime_p = $('#starttime').val();
			var endtime_p = $('#endtime').val();
			var areaid_p = $("select[name='psarea']").find("option:selected").val();  
		    $.post(url_p,{'starttime':starttime_p,'endtime':endtime_p,'psuid':psuid},function (data, textStatus){  
				 $('#doshow_psorderlist_'+psuid).html(data);//hc_list_cont_div3 
			
			}, 'html');
		   
		});
		$('.chakanxs').click(function(){
			var psuid = $(this).attr('data');
			
			var psname = $(this).attr('ndata');
			 var htmls = '<div class="replayask">';
	 	   htmls +='<table border=0 width="250">';
        htmls +='<tbody>';
        htmls +='<tr> ';
        htmls +='<td style="border:none;text-align:left;"><textarea style="width:100%;height:100px;color:#ddd;" name="reason" id="reason" placeholder="修改原因">修改原因</textarea></td> </tr> '; 
       htmls +='<tr>   <td style="border:none;">金额：<input type="text" value="" name="costd" id="costd" ></td></tr>';
	   htmls +='<tr>   <td style="border:none;"><input type="radio" value="1" name="suresend"   checked>增加<input type="radio" value="2" name="suresend"   checked>减少</td></tr>';
        htmls +='<tr>   <td style="border:none;"><a href="#" class="button fr saveImgInfo" style="margin-right:10px;" onclick="dopayy('+psuid+');">提交保存</a></td>';
        htmls +='  </tr>  </tbody> </table> </div> '; 

	 	   
	 	   var dialog =  art.dialog({
	 	   	id:'coslid',
			title:'添加或者减少收入，配送员'+psname,
            content: htmls
        });
		
		
		 
		   
		});
		
		
	}, 'html');
}
function dopayy(psuid){
	var reasons = $('#reason').val();
	var suresend = $("input[name='suresend']:checked").val();
	var cost = $('#costd').val();
	if(reasons == undefined || reasons == '')
	 	{
	 	  	alert('修改原因不能为空');
	 	  	return false;
	 	} 
		if(reasons == $('#reason').attr('placeholder')){
	 	     alert('修改原因不能为空');
	 	     return false;
	 	}
		if(cost < 1){
			alert('金额不能小于1');
	 	  	return false;
		}
	 	var url = siteurl+'/index.php?ctrl=adminpage&action=psuser&module=docost&psuid='+psuid+'&reasons='+reasons+'&suresend='+suresend+'&cost='+cost+'&datatype=json&random=@random@';
		$.ajax({
			type: 'get',
			async:false, 
			url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
			dataType: 'json',success: function(content) {  
				if(content.error == false){
					art.dialog({id:'coslid'}).close(); 
					 orderpstj();
				}else{
					if(content.error == true)
					{
						diaerror(content.msg); 
					}else{
						diaerror(content); 
					}
				} 
			},
		error: function(content) { 
			diaerror('数据获取失败'); 
		}
	}); 
}
function openlink(newlinkes){
					window.location.href=newlinkes;
}
function getorderdata(){
	var statustype = $('.dingdanGl').find('.on').eq(0).attr('data');
   var dno = $('#dno').val();
   var areaid = $("select[name='psarea']").find("option:selected").val(); 
    var url = siteurl+"/index.php?ctrl=adminpage&action=psuser&module=ordertodaytable";
	$.post(url,{'statustype':statustype,'areaid':areaid,'dno':dno},function (data, textStatus){ 
		$('#order_list_show').html(data);//hc_list_cont_div3 
			//autosize();
		if($('#showdet').is(':checked') == true){
	       $('.xqOrderlist').show();
		}else{
		
		}
		$(".chakan").click(function(){
			
				$(".showdet_"+$(this).attr('data')).toggle();
		});
	}, 'html');
	 
	
}
$(function(){
	$("input[name='showdet']").click(function(){
		if($(this).is(':checked') == true){
			$('.xqOrderlist').show();
		}else{
			$('.xqOrderlist').hide();
		}
	});
});
function getnewdata(){
	var statustype = $('.dingdanGl').find('.on').eq(0).attr('data');
	if(statustype == 0){
		freshdata();
	}else if(statustype == 5){
	    orderpstj();
	}else{
		getorderdata();
	}
}
function unorder(orderid,dno)
	 { 
	 	   var htmls = '<div class="replayask">';
	 	   htmls +='<table border=0 width="250">';
        htmls +='<tbody>';
        htmls +='<tr> ';
        htmls +='<td style="border:none;text-align:left;"><textarea style="width:100%;height:100px;color:#ddd;" name="reason" id="reason" placeholder="关闭理由">关闭理由</textarea></td> </tr> '; 
       htmls +='<tr>   <td style="border:none;"><input type="checkbox" value="1" name="suresend" id="suresend">发送关闭理由给买家手机</td></tr>';
        htmls +='<tr>   <td style="border:none;"><a href="#" class="button fr saveImgInfo" style="margin-right:10px;" onclick="sureclose('+orderid+');">提交关闭</a></td>';
        htmls +='  </tr>  </tbody> </table> </div> '; 

	 	   
	 	   var dialog =  art.dialog({
	 	   	id:'coslid',
        title:'取消订单'+dno,
           content: htmls
        });
	 }
$('#reason').live("click", function() {   
 	 var checka = $(this).attr('placeholder');
 	 var checkb = $(this).val();
 	 if(checka == checkb){
 	    $(this).val('');
 	    $(this).css('color','#333');
 	 }
 });
 $('#reason').blur(function(){
 	     var checka = $(this).attr('placeholder');
 	    var checkb = $(this).val();
 	    if(checka == checkb){
 	      $(this).css('color','#ddd');
 	    }else{
 	       if(checkb == ''){
 	          $(this).val(checka);
 	           $(this).css('color','#ddd');
 	       }else{
 	       	$(this).css('color','#333');
 	      }
 	    }
 	    
});

function sureclose(orderid)
{
	var reasons = $('#reason').val();
	var suresend = $("input[name='suresend']:checked").val();
	if(reasons == undefined || reasons == '')
	 	{
	 	  	alert('关闭理由不能为空');
	 	  	return false;
	 	} 
		if(reasons == $('#reason').attr('placeholder')){
	 	     alert('录入关闭理由');
	 	     return false;
	 	}
	 	var url = siteurl+'/index.php?ctrl=adminpage&action=order&module=ordercontrol&type=un&id='+orderid+'&reasons='+reasons+'&suresend='+suresend+'&datatype=json&random=@random@';
		$.ajax({
			type: 'get',
			async:false, 
			url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
			dataType: 'json',success: function(content) {  
				if(content.error == false){
					getorderdata();
				}else{
					if(content.error == true)
					{
						diaerror(content.msg); 
					}else{
						diaerror(content); 
					}
				} 
			},
		error: function(content) { 
			diaerror('数据获取失败'); 
		}
	});   
	 	  
} 
var mydialog;
function psorder(orderid,dno){ 
	   mydialog = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=psuser&module=selectps&orderid='+orderid,{height:'550px',width:'850px'},false); 
	 	 mydialog.title('设置配送员');  
}
function selectdo(msg){
		diasucces(msg,'');
}
function remind(obj){
  if(confirm('确定操作吗？')){
    var url = $(obj).attr('href'); 
	 $.ajax({
     type: 'get',
     async:false,
     data:$(obj).serialize(),
     url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
     dataType: 'json',success: function(content) {  
     	if(content.error == false){
     		getorderdata();
     	}else{
     		if(content.error == true)
     		{
     			diaerror(content.msg); 
     		}else{
     			diaerror(content); 
     		}
     	} 
		},
		error: function(content) { 
    	diaerror('数据获取失败'); 
	  }
   });   
  }
  return false;
}
</script>
<script>
    var map = new AMap.Map("tagscontent", {
        resizeEnable: true,
        zoom:17,
    });
    var infoWindow = new AMap.InfoWindow({offset: new AMap.Pixel(0, -30)});
/*自动加载*/
$(function(){  
    freshdata();//刷新数据
	//setTimeout("get_status()",1000); 
});

function freshdata(){
    markerslist2=new Array();
    var areaid = $("select[name='psarea']").find("option:selected").val();
    $('#psylist ul').html('');
	var url = siteurl+'/index.php?ctrl=adminpage&action=psuser&module=ajaxpsy&areaid='+areaid+'&random=@random@&datatype=json'; 
	$.ajax({ 
        type: 'post',
        async:false, 
        url: url.replace('@random@', 1+Math.round(Math.random()*1000)), 
        dataType: 'json',success: function(content) {   
         	if(content.error == false){
                $.each(content.msg, function(i,val){
                    lng = Number(val.lng);
                    lat = Number(val.lat);
                    if(!isNaN(lng) && !isNaN(lat) && lat!=0 && lng !=0){
                        var icon = new AMap.Icon("/upload/map/psdsystem11.gif", new AMap.Size(50,50));
                        var marker = new AMap.Marker({
                            position:[lng,lat],
                            icon:icon,
                            map: map
                        });
                        var contentm = '<div id="newmap"  ><div id="closedoinfo" class="close_btn" onclick="closeinfo();"><a class="close"></a></div><div class="shoplogo"></div><div class="shopinfo"><div class="shopname">'+val.username+'<a href="#" onclick="dosee(\''+val.uid+'\',\''+val.username+'\');" style="color:blue;">查看详情</a></div><div class="shopopentime">待送：'+val.waitps+'</div><div class="shopaddress">已送：'+val.overps+'</div><div></div></div></div>';
                        markerslist2.push(contentm);
                        marker.content = contentm;
                        marker.on('click', markerClick);
                        marker.emit('click', {target: marker});
                        $('#psylist ul').append('<li style="font-weight:bold;overflow: hidden; font-size:14px;" data="'+val.uid+'" lng="'+val.lng+'" lat="'+val.lat+'">'+val.username+'<font style="line-height:10px;font-size:10px;">&nbsp;&nbsp;&nbsp;(已抢单:'+val.waitps+',已完成'+val.overps+')</font></li>');
                    }
                });
                function markerClick(e) {
                    infoWindow.setContent(e.target.content);
                    infoWindow.open(map, e.target.getPosition());
                }
                map.setFitView();
                $('#psylist li').bind("click", function() {
                    $(this).addClass('on').siblings().removeClass('on');
                    var checkobj = $(this).index();
                    var lng = $(this).attr('lng');
                    var lat = $(this).attr('lat');
                    infoWindow.setContent(markerslist2[checkobj]);
                    infoWindow.open(map,[lng,lat]);
                });
         	}else{
         	   diaerror(content.msg);
         	}
	    },
        error: function(content) {   alert('shiba'); }
    });  
}
 
function dosee(uid,username){
 dialogs = art.dialog.open(siteurl+'/index.php?ctrl=adminpage&action=psuser&module=getpsorder&userid='+uid,{height:'300px',width:'600px'},false);
 dialogs.title('配送员:'+username+',当天送货情况'); 
}

$(function(){
	setTimeout("get_status()",1000); 	
 
});
function get_status(){//<span id="timeshow" data="20" style="color:#666;"></div>
	//alert('xxx');
	//firstarea
	//secarea
	
	var timeaction =  $('#showztai').attr('data');
	if(timeaction == 0){  
		if($("input[name='playwave']:checked").is(':checked') == true){
			$.ajax({
			type: 'get',
			async:false,
			data:{firstarea:$("select[name='psarea']").find("option:selected").val()},
			url: '<?php echo FUNC_function(array('type'=>'url','link'=>"/adminpage/order/module/ajaxcheckorder/datatype/json"),$_smarty_tpl);?>
', 
			dataType: 'json',success: function(content) {  
				if(content.error == false){
					//  播放声音 文件 diasucces('操作成功','');
					palywav();
				}else{ 
					// location.reload();  
					$('#showztai').attr('data',20); 
					setTimeout("get_status()",1000); 	
				}
			},
			error: function(content) { 
				//location.reload();  
				$('#showztai').attr('data',20);
				setTimeout("get_status()",1000); 	
			}
		}); 	
		}else{
			$('#showztai').attr('data',20);
			setTimeout("get_status()",1000); 
		}	
      	 
  }else{
 	var nowtime = Number(timeaction)-1;
 	$('#showztai').attr('data',nowtime);
 	//$('#showztai').text(''+nowtime+'');
 	setTimeout("get_status()",1000); 	
 	
  }
}
function palywav(){ 
	if(navigator.userAgent.indexOf("Chrome") > -1){  
		$('#palywave').html('<audio src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" autoplay=”autoplay” hidden="true"></audio>');
	}else if(navigator.userAgent.indexOf("Firefox")!=-1){  
		$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" hidden="true" loop="false" mastersound></embed>');
	}else if(navigator.appName.indexOf("Microsoft Internet Explorer")!=-1 && document.all){ 
		$('#palywave').html('<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"><param name="AutoStart" value="1" /><param name="Src" value="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" /></object>');
	}else if(navigator.appName.indexOf("Opera")!=-1){ 
		$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mpeg" loop="false"></embed>');
	}else{ 
		$('#palywave').html('<embed src="<?php echo $_smarty_tpl->tpl_vars['siteurl']->value;?>
/upload/wave.mp3" type="audio/mp3" hidden="true" loop="false" mastersound></embed>'); 
	}  
}


	
</script>

</body>

</html><?php }} ?>