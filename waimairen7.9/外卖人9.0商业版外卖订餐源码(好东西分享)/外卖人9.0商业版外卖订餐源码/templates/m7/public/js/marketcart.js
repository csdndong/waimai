$(function(){ 
	 $('body').append('<div id="addload" style="position: absolute; z-index: 1000; left: 30%; top: 30%; opacity: 1; display: none; height: 30px;width: 30px;border-radius: 15px;line-height: 12px;overflow:hidden; background: #ea5413;"></div>'); 
   freshcart();
   
});

//加购物车动画
function cartimg(obj){
	     $("#addload").show(); 
        var pos =$(obj).offset();
       var topval = pos.top;
       $("#addload").css("top", topval + "px"); 
       $("#addload").css("left", pos.left + "px");
        $("#addload").css({'width':'30px','height':'30px'}); 
       var target_ob = $('.cart-title').eq(0).offset();
       var target_top = Number(target_ob.top);
       var target_left = Number(target_ob.left);
       
         $("#addload").animate({top:target_top,left:target_left, 'width':'0px','height':'0px'},"slow",function(){
           freshcart();
          });   
        
} 

/*****12-15新增代码****/
function addproduct(gid,tshopid,num,obj){//调用选择规格界面
	if(locationfalse ==  true){
		artopen();
		  return;
	}
	if(allowedguestbuy !=  1){
	  if(memberid == 0){
		   loginjs();
		   return;
   	}
  }


	 var objccc = $(obj).parents('.goodsli');
    $('#product_one').show();  
	$('#product_one').css({'width':$(objccc).width(),'left':$(objccc).offset().left,'top':$(obj).offset().top});
	$('#product_one .productloding').show();
	$('#product_one .cart_products_content_area').hide();
	var url= siteurl+'/index.php?ctrl=site&action=selectproduct&goods_id='+gid+'&shopid='+tshopid+'&random=@random@';
	  url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
	  /*****  *****/
	  
	  
	  $('#product_one .cart_products_content_area').eq(0).load(url, function() {
			//alert("Load was performed.");//siteurl 
			$('#product_one .productloding').hide();
			$('#product_one .cart_products_content_area').show(); 
			bindclcikattr();
		});  
}
function bindclcikattr(){
	$('.productggli li').bind("click", function() { 
	   if($(this).hasClass('disable') == true){
			diaerror('商品无库存或者库存不足');
	   }else{
			var obj = $('#product_one .productggli');
			var tarray= new Array();
			var liarray = new Array();
			$.each(obj, function(i, newobj){   
				 if($(newobj).find('.checked').eq(0).html() != undefined){
					 
					 tarray.push($(newobj).find('.checked').eq(0).attr('childid'));
				 }else{
					 liarray.push($(newobj).attr('data'));
				 }
			});
			if(tarray.length == $(obj).length){//已全则还需要 重置查询条件
				$('#product_one .checked').removeClass('checked');
			}
		
		
		
			$(this).addClass('checked').siblings().removeClass('checked');
			freshproduct();
	   }
         	
    });
}
function producttocart(){
	if($('#producttocart').hasClass('disable') == true){
		
	}else{
		uponeproduct($('input[name="selectproductid"]').val(),$('input[name="product_shopid"]').val(),1);
		closeproductdiv();		
	}
}
function freshproduct(){
	var obj = $('#product_one .productggli');
	var tarray= new Array();
	var liarray = new Array();
	$.each(obj, function(i, newobj){   
		 if($(newobj).find('.checked').eq(0).html() != undefined){
			 
			 tarray.push($(newobj).find('.checked').eq(0).attr('childid'));
		 }else{
			 liarray.push($(newobj).attr('data'));
		 }
	});
	//alert(tarray.join(','));
	 
	 
	var shopid = $('input[name="product_shopid"]').val();
	var goodsid = $('input[name="product_goodsid"]').val();
	var ggdetids = tarray.join(',');
	if(tarray.length == $(obj).length){ 
		var url= siteurl+'/index.php?ctrl=site&action=doselectproduct&goods_id='+goodsid+'&shopid='+shopid+'&ggdetids='+ggdetids+'&type=1&datatype=json&random=@random@';
        url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,'');
	    if(bk.flag == false){ //需获取出当前商品的productid 并获取相关库存 已在购物中的数量
		     var productinfo = bk.content;
             //alert(productinfo.id);		
            // uponeproduct(productinfo.id,shopid,1);	
             if(productinfo.stock < 1){
				  $('#producttocart').addClass('disable');
				  diaerror('商品库存不足');
			 }else{
				 $('input[name="selectproductid"]').val(productinfo.id);
				 $('#producttocart').removeClass('disable');
				 $('#product_s_cost').text("￥"+productinfo.cost+"元");
			 }
	    }else{
			$('input[name="selectproductid"]').val('');
			$('#producttocart').addClass('disable');
	    	diaerror(bk.content);
			
	    } 
	}else{
		//构造未选择完刷新提交数据
		$('#producttocart').addClass('disable');
		$('input[name="selectproductid"]').val('');
		var url= siteurl+'/index.php?ctrl=site&action=doselectproduct&goods_id='+goodsid+'&shopid='+shopid+'&ggdetids='+ggdetids+'&datatype=json&random=@random@';
        url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
		     var tempids = bk.content;
		     var checkarray = tempids.split(','); 
			 for(var i=0;i<liarray.length;i++){
				 var tempobj = $('#productggli_'+liarray[i]);
				 var liboj = $(tempobj).find('li');
				  $.each(liboj, function(j, newobj){  
                      if($.inArray( $(newobj).attr('pid'), checkarray ) >=0){ 
					      $(newobj).removeClass('disable');
					  }else{
						  $(newobj).addClass('disable');
					  }    				  
				  });
			 }
			 
			 
			  // if($.inArray( $(this).attr('data'), temparray ) >=0){ 
		// }else{
		     // temparray.push($(this).attr('data'));
		// } 	
			 
	    }else{
	    	diaerror(bk.content);
	    } 
    }		
	
}
function closeproductdiv(){
	 $('#product_one').hide(); 
}
function uponeproduct(gid,tshopid,num){ 

	if(allowedguestbuy !=  1){
	  if(memberid == 0){
		   loginjs();
		   return;
   	}
  }
	var url= siteurl+'/index.php?ctrl=site&action=addcart&goods_id='+gid+'&shopid='+tshopid+'&num=1&gdtype=2&datatype=json&random=@random@';
      url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
	
}
function removeoneproduct(gid,tshopid,num){ 
	if(allowedguestbuy !=  1){
	  if(memberid == 0){
		   loginjs();
		   return;
   	}
  }
	 $('#loading').show();
	 url = siteurl+'/index.php?ctrl=site&action=downcart&goods_id='+gid+'&shopid='+tshopid+'&num=1&gdtype=2&datatype=json&random=@random@';
	  	 url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
	
}
function delproduct(gid,tshopid){  //删除某规格
 if(allowedguestbuy !=  1){
	  if(memberid == 0){
		   loginjs();
		   return;
   	}
  }
	url = siteurl+'/index.php?ctrl=site&action=delcartgoods&goods_id='+gid+'&shopid='+tshopid+'&num=1&gdtype=2&datatype=json&random=@random@';
	url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
	
}
/*****12-15新增代码结束****/
//添加购物车
function addonedish(gid,tshopid,num,obj){  
	if(locationfalse ==  true){
		artopen();
	}else{
	var url= siteurl+'/index.php?ctrl=site&action=addcart&goods_id='+gid+'&shopid='+tshopid+'&num=1&datatype=json&random=@random@';
      url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	  
	      cartimg(obj);
 
	    }else{
	    	diaerror(bk.content);
	    }
	}
}
function uponedish(gid,tshopid,num){ 
	var url= siteurl+'/index.php?ctrl=site&action=addcart&goods_id='+gid+'&shopid='+tshopid+'&num=1&datatype=json&random=@random@';
      url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
    	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
}
	
function delshopcart(){
	if(confirm('确认清空购物车')){
	var url= siteurl+'/index.php?ctrl=site&action=clearcart&shopid='+shopid+'&num=1&datatype=json&random=@random@';
      url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
  }
  return false;
}

function removeonedish(gid,tshopid,num){ 
	 $('#loading').show();
	 url = siteurl+'/index.php?ctrl=site&action=downcart&goods_id='+gid+'&shopid='+tshopid+'&num=1&datatype=json&random=@random@';
	  	 url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
	  
}
 
//删除商品
function removesupplierdish(gid,tshopid){
 
	url = siteurl+'/index.php?ctrl=site&action=delcartgoods&goods_id='+gid+'&shopid='+tshopid+'&num=1&datatype=json&random=@random@';
	url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	diaerror(bk.content);
	    }
}
//修改购物车数量
function modifycart(gid,oldnum,tshopid){  
	var nowgscount = 	$('#cart_count'+gid).val();
	url = siteurl+'/index.php?ctrl=site&action=modifycart&goods_id='+gid+'&shopid='+tshopid+'&num='+nowgscount+'&datatype=json&random=@random@';
	url = url.replace('@random@', 1+Math.round(Math.random()*1000));
	var bk = ajaxback(url,'');
	    if(bk.flag == false){ 
	       freshcart();
	    }else{
	    	$('#cart_count'+gid).val(oldnum);
	    	diaerror(bk.content);
	    }
}

//刷新购物车
function freshcart(){ 
   
	    url = siteurl+'/index.php?ctrl=site&action=marketcart&shopid='+shopid+'&random=@random@';
	    url = url.replace('@random@', 1+Math.round(Math.random()*1000)); 
	    $.get(url, function(result){  
        $("#cartinfodet").html(result);
        $('#cartinfodet .sc-unit').bind("hover",function(){
        	$('.J_TBSCdelete').css('display','none');
          	$(this).find('.J_TBSCdelete').css('display','inline');
        }); 
        $('.J_CartNum').text($('#tjdata').attr('allshu'));
        $('.J_CartPrice').text($('#tjdata').attr('goodscost'));
        $('.J_CartWeight').text($('#tjdata').attr('areacost'));
        $('.J_CartMsg').text($('#tjdata').attr('cxshodata'));
        var checkobj = $('#cartinfodet').find('.sc-unit');
        if($(checkobj).length == 0 ){
        	$('.mini-bd').addClass('.sc-non-empty');
        }else{
        	var widths = Number($(checkobj).length)*92;
        	$('#cartinfodet').css('width',widths);
        	if($(checkobj).length > 7){
        	   $('.mini-next').find('a').removeClass('disable');
        	}
        } 
         
      }); 
}
function gonextcart(){ 
   var position = $('#cartinfodet').offset();  
   var checkleft= Number(position.left-955);
   var basewidth = $('#cartinfodet').width();
   var checkleftm = Number(checkleft)+92; 
   var checkmb = basewidth+checkleft -92-644; 
  
   $('.mini-next').find('a').addClass('disable');
   if(checkmb > 0){
   	  var m = checkleft - 620;//92*2 
   	  $('#cartinfodet').css('left',m);
   	  checkmbc = basewidth+checkleft -1248; 
   	  if(checkmbc > 0){
   	  $('.mini-next').find('a').removeClass('disable');
     	}
     	$('.mini-prev').find('a').removeClass('disable');
   } 
}
function goupcart(){
	var position = $('#cartinfodet').offset(); 
	var checkpostion = position.left;
	var checkleft= Number(position.left - 955);
	var basewidth = $('#cartinfodet').width();
	 var checkmb = basewidth+checkleft +666; 
   
	if(checkmb < 2120){
	    var m = checkleft + 666;
   	  $('#cartinfodet').css('left',m);
   	   $('.mini-next').find('a').removeClass('disable');
   	  if(m == 0){
   	  	$('.mini-prev').find('a').addClass('disable');
   	  } 
	} 
}
function hidedeta(){
	$('.mini-cart-count').css('width','66px');
	$('.mini-cart-open').hide();
	$('.mini-cart-close').show();
	$('.mini-bd').hide();
}
function hidedetas(){
	$('.mini-cart-count').css('width','286px');
	$('.mini-cart-open').show();
	$('.mini-cart-close').hide();
	$('.mini-bd').show();
}
function ajaxdoorder(tshopid){
$("#top").hide();
	/*创建遮照层*/   
	$('body').append('<div id="cat_zhe" class="cart_zhe"></div>');
	$('body').append('<div id="goosshow" class="cart_out_cat"></div>'); 
	var  urlnew = siteurl+'/index.php?ctrl=site&action=showcatax&showtype=market&random=@random@';
	    urlnew = urlnew.replace('@random@', 1+Math.round(Math.random()*1000)); 
		 $.post(urlnew, {shopid:tshopid},function (data, textStatus){ 
			 	 $('#goosshow').append(data); 
			 	 jifenduihuan();  
			 	  if($('body').attr('addrestemp') != undefined && $('body').attr('addrestemp') != '' && $('body').attr('addrestemp') != null){
			 	 	  $('#addrestemp').val($('body').attr('addrestemp'));
			 	 }
			 	 if($('body').attr('phone') != undefined && $('body').attr('phone') != '' && $('body').attr('phone') != null){
			 	 	  $('#phone').val($('body').attr('phone'));
			 	 }
			 	 if($('body').attr('recieve_name') != undefined && $('body').attr('recieve_name') != '' && $('body').attr('recieve_name') != null){
			 	 	  $('#recieve_name').val($('body').attr('recieve_name'));
			 	 }
			}, 'html');
}
function close_ajaxcart(){
	$('body').attr('addrestemp',$('#addrestemp').val());
	$('body').attr('phone',$('#phone').val());
	$('body').attr('recieve_name',$('#recieve_name').val()); 
	
	if($("#showtj2").is(":hidden")){
	  $('#goosshow').remove();
	  $('#cat_zhe').remove();
	  $("#top").show();
  }else{
    alert('订单提交中请捎后关闭');
  }
}
function freachperson(){
	 var allcost = $('#caipincost').val();// $('input[name="allcost"]').val();
    var persongnum = $('#cartallcost').val();
    $('#CMoney').text(allcost);
   $('#waimaibtn').text(persongnum);
}
function addperson(num){
	$('#person_num').val(Number($('#person_num').val())+1);
	freachperson();
}
function downperson(num){
	var checknum = Number($('#person_num').val());
	if(checknum > 1){
	  	$('#person_num').val(Number($('#person_num').val())-1);
	}else{
		$('#person_num').val(1);
	}
	freachperson();
}
function modifyperson(){
  var checknum =	$('#person_num').val();
  if(checknum > 0){
  }else{
     $('#person_num').val(1);
  }
  freachperson();
}
function jifenduihuan(){

	if($("#jifendkou").is(":hidden")){
	}else{ 
	 
		var myjifen = Number($('#jifendkou').attr('data'));
		var jifenbili = Number($('#jifendkou').attr('jfendata'));
		var rslt = Number(myjifen)/Number(jifenbili); //除 
	  var canduihuan = rslt - Math.floor(rslt) > 0?Math.floor(rslt):Math.floor(rslt);//总页面数量
	  var shopcost = Number($('#surecost').attr('data')); 
	  var cancost = Math.ceil(shopcost) > canduihuan ? canduihuan:Math.ceil(shopcost); 
	  
	  var htmlbottom = '';
	  if(jifenbili > 0){ 
	      for(var i=1;i<=cancost;i++){
	      	var jifenall = Number(i)*jifenbili;
	      	htmlbottom += '<option value="'+i+'">使用'+jifenall+'积分抵扣'+i+'元</option>';
	      }
	      if(htmlbottom != ''){ 
	      	$('#activeJifenselect').html('<select name="jfdk" onchange="jisuanjf();" style="height:30px; border-radius: 4px 4px 4px 4px;"><option value="0">不使用积分抵扣</option>'+htmlbottom+'</select>');
	      	 
	      }else{
	      	$('#activeJifenselect').html('我的积分'+myjifen+'个');
	      	 
	      }  
	  }else{
	  	$('#jifenPromotion2').hide();
	  }
	}
	jisuanjf();
}
function jisuanjf(){
  var nowcost = $('#surecost').attr('alldata');
  var hcost = $('#surecost').attr('data');
  var dikou = $("select[name='jfdk']").find("option:selected").val();  
  dikou = dikou == undefined?0:dikou;
  var juanid = $("input[name='buyjuan']:checked").val();//data
  var juancost  = 0;
  if(juanid != undefined){
    	juancost = $("input[name='buyjuan']:checked").attr('data');
  }
  //返填数据
  $('#jfcost').text(''+dikou);
  $('#yhjcost').text(''+juancost);
  var checkcost = Number(dikou)+Number(juancost);
  if(checkcost > hcost){
  	  var result = Number(nowcost)-Number(hcost);
     $('#surecost').text(result);
  }else{
  	  var result = Number(nowcost)-Number(checkcost);
     $('#surecost').text(result);
  }
   
}
 
$('input[name="buyjuan"]').live("click", function() {    
	 jisuanjf();
});
 
function strToJson(str){ 
  return JSON.parse(str); 
}
 


function  orderSubmit(){
	//  url = siteurl+'/index.php?ctrl=shop&action=order&random=@random@';
	 $('#showtj').hide();
	 $('#showtj2').show();
	  url = submithtml.replace('@random@', 1+Math.round(Math.random()*1000));
   $.ajax({         //script定义
             url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
            dataType: "json",
            async:true,
            data:{'phonecode':$('#phonecode').val(),shopid:shopid,'remark':$('#remark').val(),'username':$('#recieve_name').val(),'mobile':$('#phone').val(),'addressdet':$('#addrestemp').val(),'areaid1':$("#areaid1").find("option:selected").val(),'areaid2':$("#areaid2").find("option:selected").val(),'areaid3':$("#areaid3").find("option:selected").val(),'senddate':$("select[name='senddate']").find("option:selected").val(),'minit':$("#timeid").find("option:selected").val(),'paytype':$("input[name='paytype']:checked").val(),'dikou':$("select[name='jfdk']").find("option:selected").val(),'juanid':$("input[name='buyjuan']:checked").val()},
            success:function(content) { 
            //	$('#loading').toggle();
             if(content.error == true){ 
                $('#showtj2').hide();
	              $('#showtj').show();
	              alert(content.msg);
             }else{
             
	             window.location.href= orderhtml.replace('@orderid@', content.msg);
             }
            },
            error:function(){
            	//$('#loading').toggle();
            	  $('#showtj2').hide();
	              $('#showtj').show();
            	alert('数据提交失败');
            }
   });     
}
function showorder(datas){
	//
  window.location.href='subshow.html?orderid='+datas.data;//alert(datas);
}
//构造送货日期和送货时间
function maketimenenu(){
	var selectdate = $("select[name='senddate']").find("option:selected").val(); 
  $('#orderTime').html(''); 
  var dotime = ''; 
  	dotime = starttime.split('|'); 
  for(var i=0;i<dotime.length;i++){ 
  		var splittime = dotime[i].split('-');
  		if(splittime.length > 0){
  			//开始时间  结束时间
  			var begindtime = selectdate+' '+splittime[0]+':00';
  			var endtime = selectdate+' '+splittime[1]+':00'; 
  			addhtml(begindtime,endtime);
  		}
  } 
}
function addhtml(begindtime,endtime){ 
	var begindate = new Date(begindtime.replace(/-/g,'/'));
	var enddate = new Date(endtime.replace(/-/g,'/'));
	var bkdate = new Date(nowtime.replace(/-/g,'/'));
	var begindates = Number(begindate.getTime());
	var enddates = Number(enddate.getTime());
	var bkdates = Number(bkdate.getTime());
	var makenewtime =  new Date(maketime.replace(/-/g,'/'));
	if(bkdates > begindates && bkdates > enddates){ 
		 
  }else{  
  	var setptime = 15*60*1000; 
       for(var i=begindates;i<enddates;i=i+setptime){
       	 if(i > bkdates && i>makenewtime){ 
       	   var d = new Date()
            d.setTime(i);
            var nowhour = d.getHours();
            var timesee =  Number(nowhour) < 10?'0'+nowhour:nowhour;
            var nowminit = d.getMinutes();
            timesee += Number(nowminit) < 10?':0'+nowminit:':'+nowminit;
            $('#orderTime').append('<option value="'+timesee+'">'+timesee+'</option>'); 
         }
	     }
	} 
}
 


