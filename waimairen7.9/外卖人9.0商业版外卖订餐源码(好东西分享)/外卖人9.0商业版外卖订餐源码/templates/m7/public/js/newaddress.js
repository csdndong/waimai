$(function(){
	loadaddreslist();
});
var isexist = 0;
var pscost = 0 ;
var psway = pstype;
var juli = 0; 
var nopsparghtml = '';


var tagname = new Array();
tagname[1] = '家';
tagname[2] = '公司';
tagname[3] = '学校';
tagname[0] = '无';


var outaddresshtml = '<h3 class="addresstype">新增收货地址</h3>'
 +'<div class="c_address">'

  +'<div class="c_form_con">'
   +'<label><font>*</font>收货人姓名：</label>'
   +'<div class="c_form_right">'
    +'<input class="c_khname" type="text" placeholder="请输入收货人姓名" maxlength="20"> '
   +'</div>'
   +'<div class="c_clear"></div>'
  +'</div>'
 
    +'<div class="c_form_con">'
    +' <label><font>*</font>手机号：</label>'
    +' <div class="c_form_right">'
    +'  <input class="c_details_contact" type="text" placeholder="请输入手机号" maxlength="11"> '
    +'</div>'
    +' <div class="c_clear"></div>'
    +'</div>'
 
    +'<div class="c_form_con" style="margin-top: 10px;">'
  +' <label><font>*</font>收餐地址：</label>'
   +'<div class="c_form_right" style="position: relative;">'
   +' <input class="c_address" id="searchKeywords" style="width: 359px;margin-top:0px;padding-left:25px;background:url(/templates/m7/public/images/poi_icon.png) 8px  center no-repeat;background-size: 14px;" type="text" placeholder="小区/写字楼/学校" /> '
   +' <input id="addresslnglat"  type="hidden" value="" /> '
   +'<div  style="display:block;;position: absolute; top:-16px;   z-index: 200000;    height: 15px;line-height:15px;font-size:12px; color:#fa9700; width: 386px;">请输入地址，并在下拉框中选择</div>'
   +'<div id="searchAddresslist" style="display:none;position: absolute; top:36px; max-height:213px;padding:0px 0px; background:#FFF;    z-index: 200000;border: 1px solid #ccc;border-top: 0px;overflow-y: scroll; width: 386px;"></div>'
   +'</div>'
   +'<div class="c_clear"></div>'
  +'</div>'

  
  
  +'<div class="c_form_con">'
  +' <label><font>*</font>详细地址：</label>'
   +'<div class="c_form_right">'
   +' <input class="c_details_address" type="text" placeholder="请输入详细地址，如：单元楼、门牌号等信息" maxlength="50"> '
   +'</div>'
   +'<div class="c_clear"></div>'
  +'</div>'
  
  
    +'<div class="c_form_con">'
  +' <label><font></font>标签：</label>'
   +'<div class="c_form_right">'
   +' <select name="c_tag" style="height: 36px;line-height: 36px;border: 1px solid #ddd;padding: 0px 10px;"><option value="1">家</option><option value="2">公司</option><option value="3">学校</option><option value="0">无</option></select>'
   +'</div>'
   +'<div class="c_clear"></div>'
  +'</div>'
  

 +' <div class="c_form_con" style="margin-top:10px;">'
  +' <input type="hidden" name="i_is_main" value="0"><button class="c_save" onclick="saveAddress(0);">保存</button><button id="c_save_other" class="c_save_other">取消</button>'
  +'</div>'
   +'<div style="height:60px;" class="c_clear"></div>'
+' </div>';
 	
	 
function loadaddreslist(){ 	//记载购物车 地址有关信息
 		var url= siteurl+'/index.php?ctrl=member&action=address&datatype=json&random=@random@';
        url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,''); 
 	    if(bk.flag == false){
			 var addresslist = bk.content.addresslist;
  				if(addresslist.length > 0){
					
				 $('#guestaddresslist').hide();
                 $('#guestaddresslist').find('select[name="areaid[]"]').remove();
				 $('#useraddresslist').show();
				 $('#addressitemlist').html('');
				 var showhtml = '';
				 $.each(addresslist, function(i, newobj) {
 											   checkPsrangePscost(newobj); 
												  if(  isexist == 0 ){
													  nopsparghtml = '<span style="margin-left:15px;    color: #F00;">不在配送范围内</span>'; 
 												  }else{
													  nopsparghtml = '';
 												  } 
 												 
												
											 if( nopsparghtml == undefined) { nopsparghtml = ''; }	 
							 
					 
										if(newobj.default ==1){  
												if(  isexist == 0 ){
 													  $('#showtj').css('background','#CCC');
 													  $("#showtj").attr("onclick", "null");
												  }else{
 													  $('#showtj').css('background','#e52843');
													  $("#showtj").attr("onclick", "orderSubmit()");
												  }  
					  
									  $('input[name="contactname"]').val(newobj.contactname);
									   $('input[name="phone"]').val(newobj.phone);
									   $('input[name="addresss"]').val(newobj.address);
									   var addresslng = newobj.lng;
									   var addresslat = newobj.lat;
										if( addresslng != '' &&  addresslat != '' ){
											  buyerlat = addresslat;
											  buyerlng = addresslng; 
											   
											 $('input[name="buyerlat"]').val(addresslat);
											 $('input[name="buyerlng"]').val(addresslng);
										}else{
											$('input[name="buyerlat"]').val('');
											 $('input[name="buyerlng"]').val('');
											
										}
						   
 					  }
					  var tempselect = newobj.default == 1?' select ':'';
					  var addresslnglat = addresslng+','+addresslat;
 					  showhtml +='<li  onclick="changeAddr(this);" class="addr-item  '+tempselect+' "  data-is_exist="'+isexist+'" data-pscost="'+pscost+'" data-juli="'+juli+'"  data-pstype="'+psway+'" data-id="'+newobj.id+'" data-detaddress="'+newobj.detailadr+'" data-areaids="'+newobj.areaids+'" data-contact="'+newobj.contactname+'"  data-tag="'+newobj.tag+'"  data-phone="'+newobj.phone+'" data-address="'+newobj.bigadr+'" data-addresslnglat="'+addresslnglat+'" data-sex="'+newobj.sex+'" data-ismain="'+newobj.default+'">'  
				+'<div class="addr-title"  > '        
					+'<div class="addr-user" >   '         
						+'<span class="name">'+newobj.contactname+'</span>  '         
						+'<span class="sex">'+newobj.phone+'</span>'         
					+'</div>   '      
					+'<div class="addr-action"> '            
						+'<a class="addr-edit" addid="499" style="cursor:pointer;">编辑</a> '            
						+'<a class="addr-remove"   style="cursor:pointer;">删除</a> '       
					+'</div>'     
				+'</div> '    
				+'<div class="addr-con" >  '       
 					+'<p class="addr-desc">'+newobj.address+''+nopsparghtml+'</p>  '   
				+'</div> <span class="select-ico"></span> '    
							+'</li>';
				 });
				 
				 
				 $('#addressitemlist').html(showhtml); 
 				 
 				 if($('#addressitemlist').html() != '' && $('#addressitemlist').html(showhtml) != undefined){
                     if($('#addressitemlist li').hasClass('select') == false){
                            var obj = $('#addressitemlist li:first').find('.addr-con');
                           changeAddr(obj);
                      }
                 }
				  
			  
				 
			 }else{
				 $('#guestaddresslist').show();
				 $('#useraddresslist').hide();
 			 }
						 
						 
		}else{
			diaerror(bk.content);
		}
		
 	 bindaddressclick();
	 freshcart();
		
}  


function bindaddressclick(){
	$('.add-new').unbind();
	$('.addr-edit').unbind();
	$('.addr-remove').unbind();

	$('.add-new').bind("click", function() { 
	    art.dialog({
			id: 'testID4',
			title:'收货地址管理',
			content: outaddresshtml
		  });
		//  loadarea(0,0,'');
		   searckKeyUp();
		
		  
		   $('.c_save_other').bind("click", function() { 
		        art.dialog({id: 'testID4'}).close(); 
		  });
        $("input[name=i_sex]").unbind();
        $("input[name='i_sex']").bind("click", function() {
            $("input[name='i_sex']").removeClass('i_sex_check');
            $(this).addClass('i_sex_check');
        });
        $('.addresstype').text('新增收货地址');
	});
	
	
	  
	
	$('.addr-edit').bind("click", function() { 
		 
		art.dialog({
			id: 'testID4',
			title:'管理收货地址',
			content: outaddresshtml
		  });
		  var findobj = $(this).parents('li');
		  $('input[class="c_khname"]').val($(findobj).attr('data-contact'));
		  $('input[class="c_details_address"]').val($(findobj).attr('data-detaddress'));
		  $('input[class="c_address"]').val($(findobj).attr('data-address'));
		  $('#addresslnglat').val($(findobj).attr('data-addresslnglat'));
		  $('input[class="c_details_contact"]').val($(findobj).attr('data-phone'));
		  $('.c_save').attr('onclick','saveAddress('+$(findobj).attr('data-id')+')');
		  $('input[name="i_is_main"]').val($(findobj).attr('data-ismain'));
		  var dataTag = $(findobj).attr('data-tag');
		  if( dataTag == '' ) { dataTag = 0; }
		  if( dataTag > 0 ){
			  $('select[name="c_tag"]').find('option').eq(dataTag-1).attr("selected",true);
		  }else{
			  $('select[name="c_tag"]').find('option').eq(3).attr("selected",true);
		  }
         $('.addresstype').text('编辑收货地址');
            var sex = $(findobj).attr('data-sex');
            if(sex == 1){
                $("input[name=i_sex]:eq(0)").attr("checked",'checked');
                $("input[name=i_sex]").removeClass('i_sex_check');
                $("input[name=i_sex]:eq(0)").addClass('i_sex_check');
            }else{
                $("input[name=i_sex]:eq(1)").attr("checked",'checked');
                $("input[name=i_sex]").removeClass('i_sex_check');
                $("input[name=i_sex]:eq(1)").addClass('i_sex_check');
            }
        $("input[name=i_sex]").unbind();
        $("input[name='i_sex']").bind("click", function() {
            $("input[name='i_sex']").removeClass('i_sex_check');
            $(this).addClass('i_sex_check');
        });
	//	  loadarea(0,0, $(findobj).attr('data-areaids'));
		  
		  $('.c_save_other').bind("click", function() { 
		        art.dialog({id: 'testID4'}).close(); 
		  });
		
		searckKeyUp();
		
		
 	});

	$('.addr-remove').bind("click",function(){
		var findobj = $(this).parents('li');
 		var addressid = $(findobj).attr('data-id');
		if(confirm('确定操作吗？')){
		 
			var checkinfo = ajaxback(siteurl+'/index.php?ctrl=area&action=deladdress&datatype=json',
			{'tijiao':'del','addressid':addressid});
			if(checkinfo.flag == false){
 				 loadaddreslist();
			}else{ 
				diaerror(checkinfo.content); 
			}
			
		 }
	});
	
	
	
	
}	
 

function saveAddress(id){
	 
	var tempaddress = new Array();
	 
	if($('.c_khname').val() == ''){
		diaerror('联系人不能为空');
		return false;
	}
	 if($('.c_details_contact').val() == ''){
		diaerror('联系电话不能为空');
		return false;
	} 
	if($('#searchKeywords').val() == ''){
		diaerror('请输入地址，并在下拉框中选择');
		return false;
	} 
	if($('#addresslnglat').val() == ''){
		diaerror('请重新输入地址，并在下拉框中选择');
		return false;
	} 
	if($('.c_details_address').val() == ''){
		diaerror('所在地址详情不能为空');
		return false;
	} 
	var address = $('#searchKeywords').val()+$('.c_details_address').val();
	var addresslnglat = $('#addresslnglat').val();
	var addresslnglatarr = addresslnglat.split(',');
	var lng = addresslnglatarr[0];
	var lat = addresslnglatarr[1];
	
		var checkinfo = ajaxback(siteurl+'/index.php?ctrl=area&action=saveaddress&datatype=json&random=@random@',
		{'laiyuan':0,'addressid':id,'contactname':$('.c_khname').val(),'detailadr':$('.c_details_address').val(),'tag':$('select[name="c_tag"]').val(),'add_new':address,'bigadr':$('#searchKeywords').val(),'lng':lng,'lat':lat,'phone':$('.c_details_contact').val() });
		if(checkinfo.flag == false){
			 art.dialog({id: 'testID4'}).close(); 
			 loadaddreslist();
		}else{ 
			diaerror(checkinfo.content); 
		}
		
}
 
 
var biaoqianval = false;
function bqzhi(){
	biaoqianval  = false;
} 
function showaddresslist(data){
	var datas = eval(data); 
 	if(datas.info == "OK"  && datas.status == 1  && datas.pois.length > 0 ){
		$('#searchAddresslist').html('');
		$('#searchAddresslist').show();
		var addresslist = datas.pois;
 		var showhtmls = '';
		$.each(addresslist, function(i, newobj) {
  		  showhtmls += '<div style="cursor:pointer;" class="selADditem J_returnLng" data-lng-lat="'+newobj.location+'"  ><div class="txt"><div class="poicard-name">'+newobj.name+'</div> <div class="poicard-addr">'+newobj.address+'</div></div></div>';
  		});
 		  $('#searchAddresslist').append(showhtmls);
 		 bindclickadd();
	}
}
function bindclickadd(){
		$("#searchAddresslist .selADditem").bind('click',function(){ 
		var  name = $(this).find('.poicard-name').text();
 		var  lnglat = $(this).attr('data-lng-lat'); 
		$('#searchKeywords').val(name);
		$('#addresslnglat').val(lnglat);
		$("#searchAddresslist").hide();
	});
}

function searckKeyUp(){
			  
 $("#searchKeywords").bind('keyup',function(e){
  		if(biaoqianval == false){
			biaoqianval  = true;
			setTimeout("bqzhi()", 500 ); 
			
			 var keyCode = window.event ? e.keyCode:e.which;	
			if( keyCode ==13){
				bindsearchclick();
			}else{
					var searchval  = $("#searchKeywords").val();
					if( searchval != '' && searchval != undefined ){
						var addresslist = map_comment_link+'restapi.amap.com/v3/place/text?&keywords='+searchval+'&city='+site_city+'&output=json&offset=20&page=1&key='+map_webservice_key+'&extensions=all&callback=showaddresslist';						 
						$.getScript(addresslist); 
					} 
					
			}
		
		
		}
	}); 
			  
 $("#searchKeywords").bind('focus',function(e){
  		if(biaoqianval == false){
			biaoqianval  = true;
			setTimeout("bqzhi()", 500 ); 
			
			 var keyCode = window.event ? e.keyCode:e.which;	
			if( keyCode ==13){
				bindsearchclick();
			}else{
					var searchval  = $("#searchKeywords").val();
					if( searchval != '' && searchval != undefined ){
					}else{
						if( mapname != '' ){
							searchval = name;
						}
					} 
						var addresslist = map_comment_link+'restapi.amap.com/v3/place/text?&keywords='+searchval+'&city='+site_city+'&output=json&offset=20&page=1&key='+map_webservice_key+'&extensions=all&callback=showaddresslist';						 
						$.getScript(addresslist);  
					
					
			}
		
		
		}
	}); 
		  
}
 
 
function changeAddr(obj){
 	var liobj = $(obj);
	$('#addressitemlist li').removeClass('select');
	$(liobj).addClass('select');
 	$('input[name="contactname"]').val($(liobj).attr('data-contact'));
    $('input[name="phone"]').val($(liobj).attr('data-phone'));
	var address = $(liobj).attr('data-address');
	var detaddress = $(liobj).attr('data-detaddress');
    $('input[name="addresss"]').val(address+detaddress);
	var addresslnglat = $(liobj).attr('data-addresslnglat');
	var addressid = $(liobj).attr('data-id');
	var addresslnglatarr = addresslnglat.split(',');
	var isexist = $(liobj).attr('data-is_exist');
	
	if(  isexist == 0 ){
 		 $('#subbutton').css('background','#CCC');
		 $('#subbutton').attr("disabled",true);
	}else{
 		 $('#subbutton').css('background','#00B174');
		$('#subbutton').attr("disabled",false);
	}
	
	if( addresslnglatarr.length > 0 ){
		  buyerlat = addresslnglatarr[1];
		  buyerlng = addresslnglatarr[0];
		 $('input[name="buyerlat"]').val(addresslnglatarr[1]);
		 $('input[name="buyerlng"]').val(addresslnglatarr[0]);
	}else{
		$('input[name="buyerlat"]').val('');
		$('input[name="buyerlng"]').val('');
	}
 	 
	var info = {'addressid':addressid}; 
 	var url = siteurl + '/index.php?ctrl=area&action=setmydefadid&random=@random@&datatype=json';
	var backdata = ajaxback(url,info);
		  if(backdata.flag == false){ 
 			 loadaddreslist(); 
 		  }else{ 
		     diaerror(backdata.content);
		  }
	
	
	
}



function checkPsrangePscost(newobj){
 	 isexist = 0;
	 pscost = 0 ;
	 psway = pstype;
	juli = 0; 
	nopsparghtml = '';
	 var newobj = newobj;
	 var addresslnglat = newobj.lng+','+newobj.lat;
 									 if( pstype == 1 ){
											 var checkinfo = ajaxback(siteurl+'/index.php?ctrl=member&action=checkIsPaPsrange&datatype=json&random=@random@',{'shopid':shopid,'addressid':newobj.id});
										    if(checkinfo.flag == false){
 												 var psdata = checkinfo.content.psdata; 
												 if( psdata.canps == 1 ){
													 isexist = 1;
													 pscost = psdata.pscost;
													 psway = psdata.pstype;
													 juli = psdata.juli; 
												 }else{
													isexist = 0;
													pscost = psdata.pscost;
													psway = psdata.pstype;
													juli = psdata.juli; 
													nopsparghtml =  '<li><b style="padding: 2px 4px;margin-left: 5px;font-size: 12px;color: #fe7223;background-color: #fff4e5;display: block;font-weight: normal;">不在配送范围</b></li>';
												 }
										    } 
  										
										
									}else{
 										 var  is_exist = checkIsPaPsrange(addresslnglat); //检测配送地址是否在平台的配送范围内
    										if(is_exist == false){
											isexist = 0;
											pscost = 0;
											psway = 0;
											juli = 0; 
										    nopsparghtml =  '<li><b style="padding: 2px 4px;margin-left: 5px;font-size: 12px;color: #fe7223;background-color: #fff4e5;display: block;font-weight: normal;">不在配送范围</b></li>';
										}else{
											var checkinfo = ajaxback(siteurl+'/index.php?ctrl=member&action=checkIsPaPsrange&datatype=json&random=@random@',{'shopid':shopid,'addressid':newobj.id});
 										    if(checkinfo.flag == false){
 												 var psdata = checkinfo.content.psdata; 
 												 if( psdata.canps == 1 ){
													 isexist = 1;
													 pscost = psdata.pscost;
													 psway = psdata.pstype;
													 juli = psdata.juli; 
												 }else{
													isexist = 0;
													pscost = psdata.pscost;
													psway = psdata.pstype;
													juli = psdata.juli; 
													nopsparghtml =  '<li><b style="padding: 2px 4px;margin-left: 5px;font-size: 12px;color: #fe7223;background-color: #fff4e5;display: block;font-weight: normal;">不在配送范围</b></li>';
												 }
										    } 
										}
											 
									}
									  
}
 