/***
----公共加载函数 
***/
$(function(){
      
	 showEmpty();
	
}); 
var click_button = false;
function doubleclick(){
	click_button = false;
}
function lockclick(){
	 if(click_button == false){
			click_button = true;
			setTimeout("doubleclick()", 400); 
			return true;
	 }else{
		 return false;
	 }
} 
 
//加载页面函数
function loading(){
	if(typeof html5_config == 'undefined'){
		alert('获取失败');
	}else{ 
	   
	    var topheith = 0;
		if(html5_config.showheader == true){
			$('#header').show();
			topheith = 42;
		}
		var bottomheight = 0;
		if(html5_config.showfooter == true){
			$('#footer').show();
			bottomheight=49;
		}
		 
		if(html5_config.bodyscller == true){
			$('#wrapper').css({'top':topheith+'px','bottom':bottomheight+'px'});  
			 setTimeout(function(){  
				$('#wrapper').show();    
				 addfresh(); 
			 },100);
		}else{
			setTimeout(function(){  
				$('#wrapper').show();    
				$("#loading").hide();
			 },100);
		}
		if(html5_config.titilename != ''){
			$("#header .titC h2").text(html5_config.titilename);
		}
		if(html5_config.Canfresh == false){
			$("#uprefuBox").remove();
		}
		if(html5_config.CanloadMore == false){
			$("#lodingmore").remove();
		}
		 
		
	}
}
var pullDownOffset;
var pullUpOffset;
var loaddataflag =  false;
var scolltop = 1;
var defaultSwiper; //默认的滚动控件名 
var holdPosition = 0; 
var maxposition = 0;
function addfresh(){ 
	document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
	if($("#uprefuBox").length>0){
		pullDownOffset = document.getElementById('uprefuBox').offsetHeight; 
	}
    if($("#lodingmore").length>0){
		pullUpOffset = document.getElementById('lodingmore').offsetHeight;
	}
	
 
	defaultSwiper = new iScroll('wrapper', { 
		hScrollbar:false, 
		vScrollbar:false,
		useTransition: true,  
	    topOffset: pullDownOffset,
        onRefresh: function () {
			 
        },
        onScrollMove: function () {
			
				if (this.y < 0 && this.y > -50 ) { 
				 //   console.log('下拉'+this.y); 
					this.minScrollY = 0;  
					if($("#uprefuBox").length>0){
						$('#uprefuBox .refuFang').addClass('flipup');
						$('#uprefuBox .refuFang').removeClass('flipdown');
						$('#uprefuBox .refuFang').text('下拉刷新');  
					}
				}else if(this.y > 0){
					if($("#uprefuBox").length>0){
						$('#uprefuBox .refuFang').addClass('flipdown');
						$('#uprefuBox .refuFang').removeClass('flipup');
						$('#uprefuBox .refuFang').text('松开刷新'); 
						scolltop = 2;
					}
					this.minScrollY = 0; 
					
				}else if (this.y < this.maxScrollY && this.y-this.maxScrollY < -30) { 
					//	console.log('下拉小于'+this.y+'|'+this.maxScrollY); 
				   // $('#lodingmore').show();
				   if(pageend == false){
						$("#lodingmore span").text("上拉更多");
						scolltop = 3;
					}
				}else{
					scolltop = 4;
				}
			 
        },
		onScrollEnd: function () {
			
				if(scolltop == 2 &&loaddataflag == false){ 
					//$('#uprefuBox .refuFang').addClass('flipdown');
					//$('#uprefuBox .refuFang').removeClass('flipup');
					$('#uprefuBox .refuFang').text('刷新中...');
					loaddataflag = true; 
					setTimeout(function(){  freshpage();},100);
				}else if(scolltop == 3 &&loaddataflag == false){
					if($("#lodingmore").length>0){
						 if(pageend == false){
							$('#lodingmore .moreLoading i').removeClass('iconstartloading');
							$('#lodingmore .moreLoading i').removeClass('iconOverload');
							$('#lodingmore .moreLoading i').addClass('iconloading');
							$("#lodingmore span").text("加载中.."); 
							loaddataflag = true;
							setTimeout(function(){  loadmore();},100);
						 }
					}
				}
			 
		}	
    }); 
 	
  //自动家在刷新 
  loaddataflag = true;
   $('#loading').hide();
   freshpage(); 
} 
function hidefresh() {    
	//console.log('加载更多');
	loaddataflag=false;
	scolltop = 1;
	defaultSwiper.refresh(); 
//	defaultSwiper.setWrapperTranslate(0,0,0);
//	defaultSwiper.params.onlyExternal=false;  
//	defaultSwiper.updateActiveSlide(0); 
}   
function hideloadmore(){  
	//$('#lodingmore').hide(); 	
	loaddataflag=false;
	scolltop = 1;
	defaultSwiper.refresh(); 
	//defaultSwiper.params.onlyExternal=false;
	//defaultSwiper.updateActiveSlide(0);  
}  
function htmlback(url,info)
{
	var backmessage = {'flag':true,'content':''};
	$.ajax({
       type: 'POST',
       async:false,
       url: url.replace('@random@', 1+Math.round(Math.random()*1000)),
       data: info,
      dataType: 'html',success: function(content) {  
	   backmessage['flag'] = false;
      	   backmessage['content'] = content; 
		  },
      error: function(content) { 
      backmessage['content'] = '数据获取失败';
	   }
   });  
   return backmessage;
}

 
//确认/取消 弹出层
function Suremsg(msgtitle,msgcontent){
	
}
//
function showEmpty(){
	var emptyhtmls = ''; 
		 emptyhtmls +='   <div id="emptyIng"  style="display: none;"> ';
		 emptyhtmls +=' 		<p class="refuImg"><img   src="/css/html5/images/wmrloading.gif"  ></p> ';
		 emptyhtmls +=' 		<p class="refuFang" >Empty...</p> ';
		 emptyhtmls +=' 	</div> ';
	$("body").append(emptyhtmls);
}
//跳转url
function loadurl(urls){
	window.location.href=urls;
}

