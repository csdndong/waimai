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

$(function(){ 
	getadmylist(shopid); 
	loadaddreslist();  
	
	$('.toptitBox .adr_toptitL').bind("click", function() {    
  				if( backtype == 1 ){
					$('#otheraddress').hide();
					$("#gdcart").show();
					backtype = 0;
				}else if( backtype == 2 ){
					$("#addareass_1").hide();
					$("#address_2").show();
					$("#gdcart").show();
					backtype = 1;
				}else{
					history.back();
				}
			});
});  
function loadaddreslist(){ 	//记载购物车 地址有关信息
 		var url= siteurl+'/index.php?ctrl=member&action=address&datatype=json&random=@random@';
        url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,''); 
 	    if(bk.flag == false){
			 var addresslist = bk.content.addresslist;
 				if(addresslist.length > 0){
						 $('#guestaddresslist').hide();
						 $('#useraddresslist').show();
						 $('#addressitemlist').html('');
						 var html = ''; 
						 $.each(addresslist, function(i, newobj) { 
   									  if(newobj.default ==1){
  											 	  checkPsrangePscost(newobj); 
													if(  isexist == 0 ){
													  nopsparghtml = '<span style="margin-left:15px;    color: #F00;">不在配送范围内</span>'; 
													  $('.intexchabuttInput input').css('background','#CCC');
													  $('.intexchabuttInput input').attr("disabled",true);
												  }else{
													  nopsparghtml = '';
													  $('.intexchabuttInput input').css('background','#ff6e6e');
													  $('.intexchabuttInput input').attr("disabled",false);
												  } 
												 
												  if(  isexist == 0 ){
														 Tmsg('不在配送范围内');
												  }
 										  var completeaddress = newobj.address ;
										  $('input[name="contactname"]').val(newobj.contactname);
										   $('input[name="phone"]').val(newobj.phone);
										   $('input[name="addresss"]').val(completeaddress);
										   $('input[name="areaids"]').val(newobj.areaids);
  										   $('input[name="addresslng"]').val(newobj.lng);
										   $('input[name="addresslat"]').val(newobj.lat);
										   $('input[name="adrpscost"]').val(pscost);
										   var addhtmls = '<div class="takesettlb"><ul><li><span id="contactname">'+newobj.contactname+'</span><b id="phone">'+newobj.phone+'</b>'+nopsparghtml+'</li><li id="addresss">'+completeaddress+'</li><input type="hidden" id="addresslng" value='+newobj.lng+' /><input type="hidden" id="addresslat" value="'+newobj.lat+'" /></ul></div>';
 									 
										  $('#orderaddress .takesettladdto').html(addhtmls);
									  }		
									   
						 	
						}); 		 
						
						 }else{
							 $('#orderaddress .takesettladdto').html('<div class="takesettla"><i class="icon-dw4"></i><span>请先添加地址</span></div>');
						 }
						 
						 
		}else{
			newTmsg(bk.content);
		}
		
		 bindaddressclick();
		
}   
function bindaddressclick(){ 
	$("#orderaddress").bind('click',function(){

		if(lockclick()){ 
			//changeaddress2(); 
			baitchangeaddress2();
		} 
	}); 
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
 
 
 

	 //获取素有用户数据   
	 function getadmylist(shopid){
	  if(checknext ==  false){ 
    	 checknext = true; 
     	$('#loading').show();
		$('#address_2').html('');
     	  url = siteurl+'/index.php?ctrl=wxsite&action=myajaxadlist&random=@random@&datatype=json';
      	  url = url.replace('@random@', 1+Math.round(Math.random()*1000));
           $.ajax({        
                 url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
                 dataType: "json",
                 async:true,
                 data:{'shopid':shopid},
                 success:function(content) { 
                  	if(content.error ==  false){
					  if(content.msg.length == 0){
					     $('#addareass_1').show();
						    $('#address_2').hide();
						 
					  }else{
					      $('#address_2').show();
						   $('#addareass_1').hide();
						  var tempcontent = '<div class="myaddressCon"   onclick="edite_myaddress(this)"   id_data="" contactname="" tag="" sex="" phone=""  lat=""  lng=""   bigadr="点击选择地址"   detailadr=""   add_new=""  > <div class="myaddressBox"><i>添加收货地址</i></div> </div> ';
							$('#address_2').append(tempcontent);
							
 						   for(var i=0;i<content.msg.length;i++){
						       var tempinfo = content.msg[i];
							   
 										checkPsrangePscost(tempinfo);		
							   
											if (tempinfo.sex == 1){
												var sexname = '先生';
											}else if (tempinfo.sex == 2){
												var sexname = '女士';
											} 
											if (tempinfo.default == 1){
												var defaultdiv = ' <i  id="default_icon_'+tempinfo.id+'" class="difyd"></i> ';
											}else{
												var defaultdiv = ' <i  id="default_icon_'+tempinfo.id+'"  class="difwb setdefault"   gid="'+tempinfo.id+'"></i> ';
											}
											if (tempinfo.default == 1){
												var setdefault = ' 	  <span  id="default_text_'+tempinfo.id+'">默认地址</span> ' ;
											}else{
												var setdefault = ' 		  <span   id="default_text_'+tempinfo.id+'" class="setdefault"    gid="'+tempinfo.id+'">设为默认</span> ';
											}
											
											if( tempinfo.bigadr == '' && tempinfo.detailadr == ''  &&  tempinfo.address != ''  ){
												var addressshow =  ' <li><p>'+tempinfo.address+'</p></li>  ';
											}else{
												var addressshow =  ' <li><p>'+tempinfo.bigadr+'<span>'+tempinfo.detailadr+'</span></p></li>  ';
											}
											 if( tempinfo.bigadr == '' && tempinfo.detailadr == ''  &&  tempinfo.address != ''  ){
												var  setaddress =  '   	 	 <li  id="dosho_my_'+tempinfo.id+'"  id_data="'+tempinfo.id+'" contactname="'+tempinfo.contactname+'" tag="'+tempinfo.tag+'"  sex="'+tempinfo.sex+'" phone="'+tempinfo.phone+'"  lat=""  lng=""   bigadr=""    detailadr=""   add_new="'+tempinfo.address+'"  onclick="edite_myaddress(this)" class="bjddress"  gid="'+tempinfo.id+'"><a><i class="fa fa-edit"></i>编辑</a></li> '
											}else{
												var  setaddress = '   	 	 <li  id="dosho_my_'+tempinfo.id+'"  id_data="'+tempinfo.id+'" contactname="'+tempinfo.contactname+'"  tag="'+tempinfo.tag+'"sex="'+tempinfo.sex+'" phone="'+tempinfo.phone+'"  lat="'+tempinfo.lat+'"  lng="'+tempinfo.lng+'"   bigadr="'+tempinfo.bigadr+'"  newpscost = "'+tempinfo.newpscost+'"   detailadr="'+tempinfo.detailadr+'"   add_new="'+tempinfo.address+'"  onclick="edite_myaddress(this)" class="bjddress"  gid="'+tempinfo.id+'"><a><i class="fa fa-edit"></i>编辑</a></li> '
											}
											
											
							   
 							   var tempcontentlist = '<div class="defaultadd"  id="defaultaddt_'+tempinfo.id+'"   data-is_exist="'+isexist+'" data-pscost="'+pscost+'" data-tag="'+tempinfo.tag+'" data-juli="'+juli+'"  data-pstype="'+psway+'"  data-id="'+tempinfo.id+'" data-detaddress="'+tempinfo.detailadr+'"  data-contact="'+tempinfo.contactname+'" data-phone="'+tempinfo.phone+'" data-address="'+tempinfo.address+'" data-sex="'+tempinfo.sex+'" data-ismain="'+tempinfo.default+'"   > '
											 +'   	 <div class="defaultaddt"  onclick="semy_isdefault('+tempinfo.id+','+tempinfo.canps+');" lng="'+tempinfo.lng+'"  lat="'+tempinfo.lat+'" canps="'+tempinfo.canps+'"> '
											 +'   	  <div class="difaultbox"> '
											 +'   	   <ul class="difaultbot"> '
											+'   	 	<li style="position:relative;"> '
											+'   	 	 <ul class="difaultxx"> '
											+'   	 	  <li><a>'+tempinfo.contactname+'</a></li> '
 											+'   	 	  <li>&nbsp;'+tempinfo.phone+'</li> '
											+'   	 	 </ul> ' 
											
											+'   	 	  <div class="difau_lab addtagBg_'+tempinfo.tag+'"> '
											+'   	 	 	 <span>'+tagname[tempinfo.tag]+'<{/if}></span> '
											+'   	 	  </div> '
											 
											
											
											+'   	 	</li> '
											+'   	    '+addressshow+'	 '
											 +'   	   </ul> '
											 +'   	  </div> '
											+'   	  </div> '
											+'   	  <div class="defaultaddb"> '
											+'   	   <div class="difaultbox"> '
											+'   	    <ul class="difaultl"  onclick="semy_isdefault('+tempinfo.id+');"  lng="'+tempinfo.lng+'"  lat="'+tempinfo.lat+'" canps="'+tempinfo.canps+'"> '
											+'   	 	<li><label><input type="radio" name="sex" checked="checked">'+defaultdiv+' </label></li> '
											+'   	 	<li> '+setdefault+'</li> '
											+'   	    </ul> '
										   +'   	    <ul class="difaultl"> '
 										  +' 	'+nopsparghtml+'  '
										   +'   	 	<li></li> '
										   +'   	    </ul> '
											+'   	    <ul class="difaultr"> '
											+'   	   '+setaddress+' ' 
											+'   	 	<li class="deladdress" onclick="deladdress('+tempinfo.id+');"  gid="'+tempinfo.id+'"><a><i class="fa fa-trash"></i>删除</a></li> '
											+'   	    </ul> '
											+'   	   </div> '
											+'   	  </div> '
											+'   	 </div>';

							    $('#address_2').append(tempcontentlist);

								
								
								
							 
							   
						   }

						  $("#scroll_address_2").css("top","40px");
						   
						 
					  }
                 	    	//alert(content.msg.length);
                 	}else{
                 		newTmsg(content.msg);
                 	}
                  	$('#loading').hide();
                 
                 },
                 error:function(){
                  $('#loading').hide();
                 }
        }); 
        setTimeout("myyanchi()", 500 );
	 
	    }    
	
	 }
	 function edite_myaddress(obj){

		 var tag = $(obj).attr('tag'); 
			if( tag > 0 ){
				$('.selectLabBox span').text(tagname[tag]);
				$('input[name="i_tag"]').val(tag);
			}else{
				$('.selectLabBox span').text(tagname[0]);
				$('input[name="i_tag"]').val(0);
			}
 		 $('#contactname_value').val($(obj).attr('contactname'));
		 if($(obj).attr('sex') == 1){
			 $('input[name="sex"]').eq(0).attr("checked",'checked'); 
		 }
		 if($(obj).attr('sex') == 2){
			$('input[name="sex"]').eq(1).attr("checked",'checked'); 
		 }
		 $('#mobile').val($(obj).attr('phone'));		 
		 $('.newinp1').attr('lng',$(obj).attr('lng'));
		 $('.newinp1').attr('lat',$(obj).attr('lat'));
		 $('.newinp1').attr('lnglat',$(obj).attr('lng')+','+$(obj).attr('lat'));
		 if ( addAreaType == 1){
			$('.newinp1').val($(obj).attr('add_new'));
		 }else{
			$('.newinp1').text($(obj).attr('bigadr'));
		 }
		 $('.newinp2').val($(obj).attr('detailadr'));
		 		 
	     $('input[name="add_addressid"]').val($(obj).attr('id_data'));
		  $('#address_2').hide("slow");
         $('#addareass_1').show("slow"); 

		 $("#scroll_address_2").hide();
			
		 backtype = 2;

	 }
	
	 function semy_isdefault(myadid){
	     $("#gdcart").show("slow");
	       $('#otheraddress').hide("slow");
		  var info = {'addressid':myadid}; 
 	      var url = siteurl + '/index.php?ctrl=wxsite&action=setmydefadid&random=@random@&datatype=json';
		  var backdata = ajaxback(url,info);
		  if(backdata.flag == false){
		  $(".difaultl li span").text('设为默认');
		  $(".difaultl li i").removeClass('difyd');
		  $(".difaultl li i").addClass('difwb');
		  $('#default_icon_'+myadid).addClass('difyd');
		  $('#default_text_'+myadid).text('默认地址');
	 
 			 loadaddreslist();
			  freshcart();
		  }else{ 
		     newTmsg(backdata.content);
		  }
	 }
	 function baitchangeaddress2(){

		$(".toptitC h3").text('管理地址'); 
	   $("#gdcart").hide("slow");
	   $('#otheraddress').show("slow");
	   
	   // $("#scroll_address_2").css('position','static');
	    $("#scroll_address_2").css('overflow','auto');
	   
	    $("#scroll_address_2").css('position','absolute');
	    $("#scroll_address_2").show();
	  $("#scroll_address_2").css('width','100%');
	
	   myScroll2.refresh();
	   backtype = 1;
	  func_bindadrmapopear();
	   
	 }
	  function changeaddress2(){
		$(".toptitC h3").text('管理地址'); 
	   $("#gdcart").hide("slow");
	   $('#otheraddress').show("slow");
 	   myScroll2.refresh();
	   backtype = 1;
	   func_bindadrmapopear();
	   
	 }
	 
	 function deladdress(gid){
		 if(confirm('确认删除？')) {
	         	var info = {'tijiao':'del','addressid':gid};
 	        	var url = siteurl + '/index.php?ctrl=area&action=deladdress&random=@random@&datatype=json';
	        	    url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	          var backinfo = ajaxback(url,info);
	          if(backinfo.flag ==  false){
	            // location.reload(); 
				 $("#defaultaddt_"+gid).remove();
				 myScroll2.refresh();
	          }else{
	          	$('#loading').hide();
	             newTmsg(backinfo.content);
	          }
		 }
	 }
     
	 
	  function scorllerfreach(scoller_name,elements_name){

		   if(typeof(scoller_name) != 'undefined'){
			   scoller_name.destroy();
		   }
		   scoller_name = new iScroll(''+elements_name +'', {
			   hScroll:false,hScrollbar:false, vScrollbar:false
		   });
		   return scoller_name;
	   } 
var selectSendAddress;
function bqzhi(){
				biaoqianval  = false;
			}
function func_bindadrmapopear(){
	
	
			var map,market;
			var biaoqianval = false;
			var back = 1;
			
			
			$("#selectSendAddress").bind('click',function(){
 				
				if (lockclick()) {
					back = 1
					
					$("#selectAddress").show();
					$(".addressbox").hide();
					$("#selectAddress").html('');
					var content = htmlback(siteurl + '/index.php?ctrl=wxsite&action=gaodewebapi', {});
					if (content.flag == false) {
						$("#selectAddress").html(content.content);

					}
					 
				 
					 
					

				}
				 
				
				 $("#searchKeywordss").bind('click',function(){
						    back = 1;
 							$('#searchAddresslist').show();
							 $('#searchAddresslist').css({'position':'absolute','top':'0px','marginTop':'42px','width':'100%','background':'#FFF','zIndex':'99999999999999','height':($(window).height()-42)+'px'}); 
							 selectSendAddress = scorllerfreach(selectSendAddress,'searchAddresslist');
							 bindsearchclick();
				 });
				  $("#searchKeywordss").bind('keyup', function (e) {

							var searchval = $("#searchKeywordss").val();
							if (searchval != '' && searchval != undefined) {
								var addresslist = map_comment_link+'restapi.amap.com/v3/place/text?&keywords='+searchval+'&city='+site_city+'&output=json&offset=20&page=1&key='+map_webservice_key+'&extensions=all&callback=showaddresslist';
								$.getScript(addresslist);
							}else{
								$('#searchAddresslist div').html('');
							}
					  
					});
					
					$("#houtuiimg").bind('click',function(){
						
								if(back == 1){
									$("#selectAddress").hide();
									$(".addressbox").show();
								}else{
									back = 1;
									$("#searchAddresslist").hide();
									$('#selectadd').show();
								}
						
				   });
				   
				});
				
			
	 $('.selectLabBox').bind('click',function(){
		if( lockclick() ){
			
			$('.selectLab li').removeClass('show');
			var curtagid = $('input[name="i_tag"]').val();
			console.log(curtagid);
			if( curtagid > 0 ){
				$('.selectLab li').eq(curtagid-1).addClass('show');
			}else{
				$('.selectLab li').eq(3).addClass('show');
			}
 			$('.selectLab').toggle();
			if($(".selectLab").is(":hidden")){
				$('.selectLabBox i').addClass('fa-caret-down');
				$('.selectLabBox i').removeClass('fa-caret-up');
			}else{
				$('.selectLabBox i').addClass('fa-caret-up');
				$('.selectLabBox i').removeClass('fa-caret-down');
			}
			
			$('.selectLab li').bind('click',function(){
				if( lockclick() ){
 					$('input[name="i_tag"]').val($(this).attr('tagid'));
					$('.selectLabBox span').text($(this).attr('tagname'));
					$('.selectLab').toggle();
					if($(".selectLab").is(":hidden")){
						$('.selectLabBox i').addClass('fa-caret-down');
						$('.selectLabBox i').removeClass('fa-caret-up');
					}else{
						$('.selectLabBox i').addClass('fa-caret-up');
						$('.selectLabBox i').removeClass('fa-caret-down');
					}
				}
			});
			
		}
	});

		 
	
	
}	   
  
function showaddresslist(data){
	$("#searchAddresslist").show();
	var datas = eval(data);
	if(datas.info == "OK"  && datas.status == 1  && datas.pois.length > 0 ){
		$('#searchAddresslist div').html('');
		var addresslist = datas.pois;

		var showhtmls = '';
		$.each(addresslist, function(i, newobj) {
			showhtmls += '<div class="selADditem J_returnLng" data-lng-lat="'+newobj.location+'"  ><div class="txt"><div class="poicard-name">'+newobj.name+'</div> <div class="poicard-addr">'+newobj.address+'</div></div></div>';
		});
		$('#searchAddresslist div').append(showhtmls);
		selectSendAddress.refresh();
		bindsearchclick();
	}
}


function bindsearchclick(){
	$('#searchAddresslist .selADditem').bind('click',function(){
		var name = $(this).find('.poicard-name').text();
		var lnglat = $(this).attr('data-lng-lat');
		choiceaddresslnglat(name,lnglat);
	}); 
}
function choiceaddresslnglat(name,lnglat){
	$("#selectAddress").hide();
	$(".addressbox").show();
	colde_type = 0;
	if(  name != '' && lnglat!='' ){
		$('#selectSendAddress').text(name);
		$('#selectSendAddress').attr('lnglat',lnglat);
	}else{
		newTmsg('选择地址获取失败，请重新选择');
	}
}


function saveaddress(){ 
		$('#loading').show();
  		var tag = $('input[name="i_tag"]').val();
  		var detailadr = $(".newinp2").val();
 		if ( addAreaType == 1){
			var bigadr = $(".newinp1").val();
			var tempaddress = $(".newinp1").val();
		}else{
			var bigadr = $(".newinp1").text();
			var tempaddress = $(".newinp1").text()+$(".newinp2").val();
		}
			$lnglatstr= $(".newinp1").attr("lnglat");
			$lnglatarr = $lnglatstr.split(',');

		var lng = $lnglatarr[0];
		var lat = $lnglatarr[1];
         var info = {'contactname':$('#contactname_value').val(),'tag':tag,'lng':lng,'lat':lat,'phone':$('#mobile').val(),'check_message':$('#phoneyan').val(),'bigadr':bigadr,'detailadr':detailadr,'add_new':tempaddress,'addressid':$('input[name="add_addressid"]').val()};
 	  	var url = siteurl + '/index.php?ctrl=area&action=saveaddress&random=@random@&datatype=json';
		  var backdata = ajaxback(url,info); 
		  if(backdata.flag == false){ 
		  	 $("#gdcart").show("slow");
			$('#otheraddress').hide("slow");
			   window.location.reload();
			   getadmylist(shopid);
		  }else{
		  	$('#loading').hide();
		     newTmsg(backdata.content);
		  }
		  
	}
//获取手机验证码
function clickyanzheng(){
	var tempurl = siteurl + '/index.php?ctrl=area&action=areaAddressPhone&random=@random@&phone=@phone@';
	tempurl = tempurl.replace('@random@', 1 + Math.round(Math.random() * 1000)).replace('@phone@', $('#mobile').val());
	$.getScript(tempurl);
}
function areashowsend(phone,time){
	$('input[name="phone"]').val(phone);
	$('#dosendbtn').attr('time',time);
	setTimeout("btntime();",1000);
}
function noshow(msg){
	alert(msg);
}
function  btntime(){

	var nowtime = Number($('#dosendbtn').attr('time'));
	if(nowtime > 0){
		$('#dosendbtn').attr('disabled',true);
		$('#dosendbtn').addClass('signmebg1');
		var c = Number(nowtime)-1;
		$('#dosendbtn').attr('time',c);
		var  mx = 120-(120 - Number(c));
		$('#dosendbtn').attr('value','剩余'+mx+'秒');
		setTimeout("btntime();",1000);
	}else{
		$('#dosendbtn').attr('disabled',false);
		$('#dosendbtn').removeClass('signmebg1');
		$('#dosendbtn').attr('value','重新发送');
	}

}   
	   
 