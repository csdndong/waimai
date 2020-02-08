/*************************全局初始化*****************************/
$(function () {
    var windowheight = $(window).height();
    document.getElementById("viewport").content = "width=device-width, height=" + windowheight + ", initial-scale=1.0, maximum-scale=1.0, user-scalable=no";

    $('body').delegate('.tap-click', 'click', function () {
        window.location.href = $(this).attr("href");
    });
    $(function () {
        new FastClick(document.body);
    });
});



/******页面显示相关****/
function hideBottomBar() {
    $("#bottom-bar-warp").hide();
 }



 template.helper('arrayContains', function (element, arry) {

     for (var i = 0; i < arry.length; i++) {
         if (arry[i] == element) {
             return true;
         }
     }
     return false;
 });


 window.utilities = {
     versions: function () {
         var u = navigator.userAgent, app = navigator.appVersion;
         return {//移动终端浏览器版本信息
             trident: u.indexOf('Trident') > -1, //IE内核
             presto: u.indexOf('Presto') > -1, //opera内核
             webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
             gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
             mobile: !!u.match(/AppleWebKit.*Mobile.*/) || !!u.match(/AppleWebKit/), //是否为移动终端
             ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
             android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
             iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
             iPad: u.indexOf('iPad') > -1, //是否iPad
             webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
         };
     } (),
     language: (navigator.browserLanguage || navigator.language).toLowerCase(),
     reg: {
         //检验Email
         reMail: /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
         //手机号，13 14 15 18开头的11位数字
         reCellphone: /^1[3|4|5|8][0-9]\d{8}$/i,
         //6-16数字或字母
         rePassword: /^[\da-zA-Z]{6,16}$/i,
         //手机，座机
         rePhone: /((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/,
         reSpecialChar: /^[0-9a-zA-Z\u4E00-\u9FA5]+$/i,
         reRecieptChar: /^[\(\)\[\]\&\'\,\.0-9a-zA-Z\u4E00-\u9FA5]+$/i,
         reCheckChar: /[|\/!\\@#$%\^&<>\r\+\uff01\u300a\u300b]/

     },
     hash: {
         get: function (isSearch) {
             var str = isSearch ? location.search : location.hash;
             var kvs = str.substring(1).split("&");
             var ret = {}
             for (var i = 0; i < kvs.length; i++) {
                 var item = kvs[i].split("=");
                 if (item[1] == null) {
                     continue;
                 }
                 ret[item[0]] = item[1];
             };
             return ret;
         },
         set: function (data, isSearch) {
             location.href = "#" + $.param(data);
         },

         add: function (data) {
             this.set($.extend(this.get(), data));
         },
         remove: function (keys) {
             var data = this.get();
             for (var i = 0; i < keys.length; i++) {
                 delete data[keys[i]];
             };
             this.set(data);
         }
     },
     ui: {
         msg: function (text) {
             var msg = $('<div class="instantMessage" style="display: none"><span>' + text + '</span></div>')
             msg.appendTo("body").fadeIn(200);
             setTimeout(function () {
                 msg.fadeOut(200, function () {
                     msg.remove();
                 })
             }, 3000)
         },
         showErrorMsg: function (input, msg) {
             var self = this;
             var parent = input.parent().parent().parent();
             if (!parent.hasClass("errorShow")) {
                 parent.addClass("errorShow")
             }
             parent.find(".errorMsg").html("<p>" + msg + "</p>");
             if (input.timer) {
                 clearTimeout(input.timer);
                 input.timer = null;
             }
             input.timer = setTimeout(function () {
                 self.hideErrorMsg(input);
             }, 5000)

         },
         hideErrorMsg: function (input) {
             if (input.timer) {
                 clearTimeout(input.timer);
                 input.timer = null;
             }
             var parent = input.parent().parent().parent()
             if (parent.hasClass("errorShow")) {
                 parent.removeClass("errorShow")
             }

         },
         $busy: $('<div class="busyview"><div class="busyviewinner"><div><span></span></div></div></div>'),
         showBusy: function () {
             this.$busy.appendTo("body");
         },
         hideBusy: function () {
             this.$busy.remove();
         },
         confirm: function (opt) {
             if (!this.$confirm) {
                 this.$confirm = $("#globle-confirm");
             }
             var self = this;
             this.$confirm.find(".title").html(opt.title || "");
             this.$confirm.find(".content").html(opt.content || "");
             var btns = this.$confirm.find(".page-button")
             btns.eq(0).html(opt.btn1 || "").unbind().click(function () {
                 opt.cb1 && opt.cb1();
                 if (!opt.cb1ConfirmHide) {
                     self.$confirm.fadeOut();
                 }
             });
             btns.eq(1).html(opt.btn2 || "").unbind().click(function () {
                 opt.cb2 && opt.cb2();
                 self.$confirm.fadeOut();
             });
             this.$confirm.fadeIn();
             return this.$confirm;
         },
         messageBox: function (opt) {
             if (!this.$messageBox) {
                 this.$messageBox = $("#globle-messageBox");
             }
             var self = this;
             this.$messageBox.find(".title").html(opt.title || "");
             this.$messageBox.find(".content").html(opt.content || "");
             var btns = this.$messageBox.find(".page-button");
             btns.eq(0).html(opt.btn1 || "").unbind().click(function () {
                 opt.cb1 && opt.cb1();
                 self.$messageBox.fadeOut();
             });
             this.$messageBox.fadeIn();
         }
     }
 };
 /*
 ; (function ($, window, document, undefined) {

 // our plugin constructor
 var OnePageNav = function (elem, options) {
 this.elem = elem;
 this.$elem = $(elem);
 this.options = options;
 this.metadata = this.$elem.data('plugin-options');
 this.$win = $(window);
 this.sections = {};
 this.didScroll = false;
 this.$doc = $(document);
 this.docHeight = this.$doc.height();
 };

 // the plugin prototype
 OnePageNav.prototype = {
 defaults: {
 navItems: 'a',
 currentClass: 'current',
 changeHash: false,
 easing: 'swing',
 filter: '',
 scrollSpeed: 550,
 scrollThreshold: 0.5,
 begin: false,
 end: false,
 scrollChange: false
 },

 init: function () {
 supperfoodListIScroll.on('scroll', updatePosition);
 supperfoodListIScroll.on('scrollEnd', updatePosition);
 // Introduce defaults that can be extended either
 // globally or using an object literal.
 this.config = $.extend({}, this.defaults, this.options, this.metadata);
 this.$nav = this.$elem.find(this.config.navItems);

 //Filter any links out of the nav
 if (this.config.filter !== '') {
 this.$nav = this.$nav.filter(this.config.filter);
 }

 //Handle clicks on the nav
 this.$nav.on('click.onePageNav', $.proxy(this.handleClick, this));

 //Get the section positions
 this.getPositions();

 //Handle scroll changes
 this.bindInterval();

 //Update the positions on resize too
 this.$win.on('resize.onePageNav', $.proxy(this.getPositions, this));

 return this;
 },

 adjustNav: function (self, $parent) {
 self.$elem.find('.' + self.config.currentClass).removeClass(self.config.currentClass);
 $parent.addClass(self.config.currentClass);
 },

 bindInterval: function () {
 var self = this;
 var docHeight;

 self.$win.on('scroll.onePageNav', function () {
 self.didScroll = true;
 });

 self.t = setInterval(function () {
 docHeight = self.$doc.height();
 //If it was scrolled
 if (self.didScroll) {
 self.didScroll = false;
 self.scrollChange();
 }

 //If the document height changes
 if (docHeight !== self.docHeight) {
 self.docHeight = docHeight;
 self.getPositions();
 }
 }, 250);
 },

 getHash: function ($link) {
 return $link.attr('href').split('#')[1];
 },

 getPositions: function () {
 var self = this;
 var linkHref;
 var topPos;
 var $target;

 self.$nav.each(function () {
 linkHref = self.getHash($(this));
 $target = $('#' + linkHref);

 if ($target.length) {
 topPos = $target.offset().top;
 self.sections[linkHref] = Math.round(topPos);
 }
 });
 },

 getSection: function (windowPos) {
 var returnValue = null;
 var windowHeight = Math.round(this.$win.height() * this.config.scrollThreshold);
 for (var section in this.sections) {

 if ((this.sections[section]) - 60 < windowPos) {
 returnValue = section;
 }
 }

 return returnValue;
 },

 handleClick: function (e) {
 var self = this;
 var $link = $(e.currentTarget);
 var $parent = $link.parent();
 var newLoc = '#' + self.getHash($link);

 if (!$parent.hasClass(self.config.currentClass)) {
 //Start callback
 if (self.config.begin) {
 self.config.begin();
 }

 //Change the highlighted nav item
 self.adjustNav(self, $parent);

 //Removing the auto-adjust on scroll
 self.unbindInterval();

 //Scroll to the correct position
 self.scrollTo(newLoc, function () {
 //Do we need to change the hash?
 if (self.config.changeHash) {
 window.location.hash = newLoc;
 }

 //Add the auto-adjust on scroll back in
 self.bindInterval();

 //End callback
 if (self.config.end) {
 self.config.end();
 }
 });
 }

 e.preventDefault();
 },

 scrollChange: function () {
 var windowTop = this.$win.scrollTop();
 var position = this.getSection(windowTop);
 var $parent;

 //If the position is set
 if (position !== null) {
 $parent = this.$elem.find('div[href$="#' + position + '"]').parent();

 //If it's not already the current section
 if (!$parent.hasClass(this.config.currentClass)) {
 //Change the highlighted nav item
 this.adjustNav(this, $parent);

 //If there is a scrollChange callback
 if (this.config.scrollChange) {
 this.config.scrollChange($parent);
 }
 }
 }
 },

 scrollTo: function (target, callback) {
 var offset = $(target).offset().top - 45;
 $("html,body").animate({
 scrollHeight: offset
 }, 500);


 $('html, body').animate({
 scrollTop: offset
 }, this.config.scrollSpeed, this.config.easing, callback);

 },

 unbindInterval: function () {
 clearInterval(this.t);
 this.$win.unbind('scroll.onePageNav');
 }
 };

 OnePageNav.defaults = OnePageNav.prototype.defaults;

 $.fn.onePageNav = function (options) {
 return this.each(function () {
 new OnePageNav(this, options).init();
 });
 };

 })(Zepto, window, document);


 */
