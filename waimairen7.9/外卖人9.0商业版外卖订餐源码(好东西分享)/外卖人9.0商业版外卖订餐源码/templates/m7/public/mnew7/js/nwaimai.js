
function gocars_(){	$.post("",{shop:wm_shopid},function(data,status){if(status=="success"){if(data=="login"){$("#fenshu").html("0\u4efd");	$("#jiage").html("\uffe50")}else{if(data!="no"||datda!=0||data!="0"){var vlen=data.split(",").length;for(i=0;i<data.split(",").length-1;i++){var divid=data.split(",")[i].split("|")[0];var divnum=data.split(",")[i].split("|")[1];$("#cars_nums_"+divid).hide();$("#cars_num_"+divid).html(divnum).show()}var wmd_num=Number(data.split(",")[vlen-1].split("|")[1]);var wmd_price=Number(data.split(",")[vlen-1].split("|")[0]);$("#fenshu").html(wmd_num+"\u4efd");$("#jiage").html("\uffe5"+wmd_price);$("#fenshu").data("num",wmd_num);$("#jiage").data("num",wmd_price);if(wm_freight==1&&wmd_num>=5){$("#freight").html("+0")}else{if(wm_freight==2&&wmd_price>=50){$("#freight").html("+0")}else{if(wm_freight>24&&Number(wmd_price)>=wm_freight){$("#freight").html("+0")}}}if(wmd_num==0){$(".mycaars_btn a").hide()}else{$(".mycaars_btn a").show()}}else{$("#fenshu").html("0\u4efd");$("#jiage").html("\uffe50");$(".mycaars_btn a").hide()}}}else{lo_mes.html("&#35835;&#21462;&#22833;&#36133;")
}})}function delcars(id){$("#mycar_main").html('<div class="kong"><img src="images/loading.gif" /></div>');$.post("",{pro:id},function(data,status){if(status=="success"){if(data=="no"){layer.msg("\u6e05\u7a7a\u5916\u5356\u5355\u5931\u8d25",function(){});$("#mycar_main").html('<div class="kong" onclick="delcars('+id+')">\u6e05\u7a7a\u5931\u8d25\uff0c\u70b9\u51fb\u91cd\u8bd5</div>')}else{if(wm_freight==3){$("#freight").html("+0")}else{$("#freight").html("+5")}layer.msg("\u5916\u5356\u5355\u5df2\u6e05\u7a7a\uff0c\u53ef\u4ee5\u7ee7\u7eed\u8d2d\u7269\u5566");$(".mycaars_btn a").hide();$("#mycar_main").html('<div class="kong">\u5916\u5356\u5355\u8fd8\u662f\u7a7a\u7684\u54e6</div>');$("#mycars_box").height($("#mycar_main").height()+20);var onheight=$(window).height()-$("#mycars_box").height()-40;$("#mycars_box").animate({top:onheight});$("#mycars").attr("data-btn","true");$(".aswm-probtn .aswmspans").hide();$("#fenshu").html("0\u4efd");$("#fenshu").data("num",0);var jiage_nums=0;$("#jiage").html("\uffe5"+Number(jiage_nums));$("#jiage").data("num",Number(jiage_nums))}}else{$("#mycar_main").html('<div class="kong" onclick="delcars('+id+')">\u6e05\u7a7a\u5931\u8d25\uff0c\u70b9\u51fb\u91cd\u8bd5</div>')}})}function Reduction(id,proids,price){	$("#pronum_box"+id).html('<img src="images/loading.gif" width="16" height="16" />');	$.post("",{proid:id,types:0},function(data,status){if(status=="success"){if(data=="nocars"){layer.msg("\u5916\u5356\u5355\u4e2d\u6ca1\u6709\u6b64\u5546\u54c1",function(){})}else{if(data==0){$("#cars_nums_"+proids).hide();$("#cars_num_"+proids).html(0).hide();$("#car_box_"+id).fadeOut();$("#mycars_box").animate({top:onheight});var fenshu_num=$("#fenshu").data("num");var jiage_num=$("#jiage").data("num");$("#fenshu").html(fenshu_num-1+"\u4efd");$("#fenshu").data("num",fenshu_num-1);var jiage_nums=(jiage_num-price).toFixed(2);$("#jiage").html("\uffe5"+Number(jiage_nums));$("#jiage").data("num",Number(jiage_nums));if(wm_freight==1&&Number(fenshu_num)-1<5){$("#freight").html("+5")}else{if(wm_freight==2&&Number(jiage_nums)<50){$("#freight").html("+5")}else{if(wm_freight>24&&Number(jiage_nums)<wm_freight){$("#freight").html("+5")}}}}else{$("#pronum_box"+id).html('<input type="text" class="pro_num_input" value="'+data+'" />');$("#cars_num_"+proids).html(data);var fenshu_num=$("#fenshu").data("num");var jiage_num=$("#jiage").data("num");$("#fenshu").html(fenshu_num-1+"\u4efd");$("#fenshu").data("num",fenshu_num-1);var jiage_nums=(jiage_num-price).toFixed(2);$("#jiage").html("\uffe5"+Number(jiage_nums));$("#jiage").data("num",Number(jiage_nums));if(wm_freight==1&&Number(fenshu_num)-1<5){$("#freight").html("+5")}else{if(wm_freight==2&&Number(jiage_nums)<50){$("#freight").html("+5")}else{if(wm_freight>24&&Number(jiage_nums)<wm_freight){$("#freight").html("+5")}}}}}}else{layer.msg("\u63d0\u4ea4\u9519\u8bef\uff0c\u8bf7\u68c0\u67e5\u7f51\u7edc",function(){})}})}function add_cars(id,proids,price){$("#pronum_box"+id).html('<img src="images/loading.gif" width="16" height="16" />');$.post("",{proid:id,types:1},function(data,status){if(status=="success"){if(data=="nocars"){}else{if(data==0){layer.msg("\u5355\u54c1\u6570\u91cf\u5df2\u8fbe\u4e0a\u9650",function(){})}else{$("#pronum_box"+id).html('<input type="text" class="pro_num_input" value="'+data+'" />');$("#cars_num_"+proids).html(data);var fenshu_num=$("#fenshu").data("num");var jiage_num=$("#jiage").data("num");$("#fenshu").html(fenshu_num+1+"\u4efd");$("#fenshu").data("num",fenshu_num+1);var jiage_nums=(jiage_num+price).toFixed(2);$("#jiage").html("\uffe5"+Number(jiage_nums));$("#jiage").data("num",Number(jiage_nums));if(wm_freight==1&&Number(fenshu_num)+1>=5){$("#freight").html("+0")}else{if(wm_freight==2&&Number(jiage_nums)>=50){$("#freight").html("+0")}else{if(wm_freight>24&&Number(jiage_nums)>=wm_freight){$("#freight").html("+0")}}}}}}else{layer.msg("\u64cd\u4f5c\u6709\u8bef\uff0c\u8bf7\u68c0\u67e5\u7f51\u7edc",function(){})}})}function gotop(s){$(window).scrollTop(0);return false}$(function(){var $root=$("html, body");
$(".aswm-show-class-box a").click(function(){
	$(".aswm-show-class-box a").removeClass("onbtns");$(this).addClass("onbtns");
	$root.animate({scrollTop:$("#s"+$.attr(this,"data-href")).offset().top},500);return false});
	$(".search-index-box .s_btns").click(function(){var keys=$("#inx_search").val();if(keys==""){layer.msg("\u8bf7\u8f93\u5165\u5173\u952e\u8bcd\u8fdb\u884c\u641c\u7d22",function(){});$("#inx_search").focus()}else{window.location.href="/search.asp?key="+keys}});
	$(".aswmpro span").click(function(){
			$(".aswmpro span").removeClass("onbtn");$(this).addClass("onbtn");
			if($(this).data("name")=="li"){
				$(".aswm-product-li_on").removeClass("aswm-product-lis");
				$(".aswm-product-li_on").addClass("aswm-product-li");
					$('.btnCart').css('bottom','14px');
					$('.xuanguige').css('bottom','14px');
					$('.newjiacart').css('bottom','14px');
					$('.wmr_cx_info').css('width','80px');
					$('.wmr_cx_info').css('height','80px');
					$('.wmr_cx_info').css('top','20px');
					$('.wmr_cx_info').css('left','20px');
					$('.wmr_cx_info p').css('font-size','14px');
					$('.wmr_cx_info p').css('height','25px');
					$('.wmr_cx_info p span').css('font-size','24px');
			}else{
				if($(this).data("name")=="lis"){
					$('.btnCart').css('bottom','22px');
					$('.xuanguige').css('bottom','22px');
					$('.newjiacart').css('bottom','22px');
					$('.wmr_cx_info').css('width','60px');
					$('.wmr_cx_info').css('height','60px');
					$('.wmr_cx_info').css('top','5px');
					$('.wmr_cx_info').css('left','500px');
					$('.wmr_cx_info p').css('font-size','12px');
					$('.wmr_cx_info p').css('height','20px');
					$('.wmr_cx_info p span').css('font-size','12px');
				}
				
				$(".aswm-product-li_on").removeClass("aswm-product-li");
				$(".aswm-product-li_on").addClass("aswm-product-lis")}});
				$("#mycars").click(function(){
					$("#mycars_box").height($("#mycar_main").height()+20);
					var topnum=$(window).height()-43;
					if($("#mycars").attr("data-btn")=="true"){
						$("#mycars_box").animate({top:topnum});
						$("#mycars").attr("data-btn","false")
					}else{
						var onheight=$(window).height()-$("#mycars_box").height()-40;
						var postzt=$("#mycar_main").data("post");
						$("#mycars_box").animate({top:onheight});
						$("#mycars").attr("data-btn","true");
						if($("#mycar_main").attr("data-post")=="false"){
							var lashop=$("#mycar_main").attr("data-shopid");
$.post("",{shop:lashop},function(data,status){if(status=="success"){if(data=="no"){$("#mycar_main").html('<div class="kong">\u5916\u5356\u5355\u8bfb\u53d6\u5931\u8d25</div>')}else{if(data=="login"){layer.msg("\u767b\u5f55\u4e4b\u540e\u624d\u80fd\u4eab\u53d7\u7f8e\u98df\u54e6",function(){});$("#mycar_main").html('<div class="kong cus" onclick="login();">\u767b\u5f55\u4e4b\u540e\u624d\u80fd\u4eab\u53d7\u7f8e\u98df\u54e6\uff0c\u70b9\u51fb\u767b\u5f55</div>')}else{$("#mycar_main").html(data);$("#mycar_main").attr("data-post","true");$("#mycars_box").height($("#mycar_main").height()+20);$("#mycars_box").animate({top:$(window).height()-$("#mycars_box").height()-40})}}}else{$("#mycar_main").html('<div class="kong">\u5916\u5356\u5355\u8bfb\u53d6\u5931\u8d25</div>')}})}}})});