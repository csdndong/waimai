/*
    无刷新异步上传插件
    2013-10-16 Devotion Created
*/
(function ($) {
    var defaultSettings = {
        url: "",                                 //上传地址
        buttonFeature: true,                    //true:点击按钮时仅选择文件； false:选择完文件后立即上传
        fileSuffixs: ["jpg", "png"],             //允许上传的文件后缀名列表
        errorText: "不能上传后缀为 {0} 的文件！", //错误提示文本，其中{0}将会被上传文件的后缀名替换
        onCheckUpload: function (text) { //上传时检查文件后缀名不包含在fileSuffixs属性中时触发的回调函数，(text为错误提示文本)
            alert(text);
        },
        onComplete: function (msg) { //上传完成后的回调函数[不管成功或失败，它都将被触发](msg为服务端的返回字符串)
        },


        onChosen: function (file, obj) { //选择文件后的回调函数，(file为选中文件的本地路径;obj为当前的上传控件实例)
            //alert(file);
        },
        maximumFilesUpload: 5,//最大文件上传数(当此属性大于1时，buttonFeature属性只能为true)
        onSubmitHandle: function (uploadFileNumber) { //提交上传时的回调函数，uploadFileNumber为当前上传的文件数量
            //在此回调中返回false上传提交将被阻止
            return true;
        },
        onSameFilesHandle: function (file) { //当重复选择相同的文件时触发
            //在此回调中返回false当前选择的文件将从上传队列中取消
            return true;
        },
        perviewImageElementId: "",//用于预览上传图片的元素id（请传入一个div元素的id）


        perviewImgStyle: null//用于设置图片预览时的样式（可不设置，在不设置的情况下多文件上传时只能显示一张图片），如{ width: '100px', height: '100px', border: '1px solid #ebebeb' }
    };


    $.fn.uploadFile = function (settings) {


        settings = $.extend({}, defaultSettings, settings || {});


        if (settings.perviewImageElementId) {
            //设置图片预览元素的必须样式
            if (!settings.perviewImgStyle) {
                var perviewImg = document.getElementById(settings.perviewImageElementId);
                perviewImg.style.overflow = "hidden";
            }
        }


        return this.each(function () {
            var self = $(this);


            var upload = new UploadAssist(settings);


            upload.createIframe(this);


            //绑定当前按钮点击事件
            self.bind("click", function (e) {
                upload.chooseFile();
            });


            //将上传辅助类的实例，存放到当前对象中，方便外部获取
            self.data("uploadFileData", upload);


            //创建的iframe中的那个iframe，它的事件需要延迟绑定
            window.setTimeout(function () {


                //为创建的iframe内部的iframe绑定load事件
                $(upload.getIframeContentDocument().body.lastChild).on("load", function () {
                    var dcmt = upload.getInsideIframeContentDocument();
                    if (dcmt.body.innerHTML) {


                        if (settings.onComplete) {
                            settings.onComplete(dcmt.body.innerHTML);
                        }


                        dcmt.body.innerHTML = "";
                    }
                });
            }, 100);
        });
    };
})(jQuery);


//上传辅助类
function UploadAssist(settings) {
    //保存设置
    this.settings = settings;
    //已选择文件的路径集合
    this.choseFilePath = [];
    //创建的iframe唯一名称
    this.iframeName = "upload" + this.getInputFileName();
    return this;
}


UploadAssist.prototype = {
    //辅助类构造器
    constructor: UploadAssist,


    //创建iframe
    createIframe: function (/*插件中指定的dom对象*/elem) {


        var html = "<html>"
                + "<head>"
                + "<title>upload</title>"
                + "<script>"
                + "function getDCMT(){return window.frames['dynamic_creation_upload_iframe'].document;}"
                + "</" + "script>"
                + "</head>"
                + "<body>"
                + "<form method='post' target='dynamic_creation_upload_iframe' enctype='multipart/form-data' action='" + this.settings.url + "'>"
                + "</form>"
                + "<iframe name='dynamic_creation_upload_iframe'></iframe>"
                + "</body>"
                + "</html>";


        this.iframe = $("<iframe name='" + this.iframeName + "'></iframe>")[0];
        this.iframe.style.width = "0px";
        this.iframe.style.height = "0px";
        this.iframe.style.border = "0px solid #fff";
        this.iframe.style.margin = "0px";
        elem.parentNode.insertBefore(this.iframe, elem);
        var iframeDocument = this.getIframeContentDocument();
        iframeDocument.write(html);
    },


    //获取上传控件名称
    getInputFileName: function () {
        return (new Date()).valueOf();
    },


    //创建上传控件到创建的iframe中
    createInputFile: function () {
        var that = this;
        var dcmt = this.getIframeContentDocument();
        var input = dcmt.createElement("input");
        input.type = "file";
        input.setAttribute("name", "input" + this.getInputFileName());
        input.onchange = function () {


            var fileSuf = this.value.substring(this.value.lastIndexOf(".") + 1);


            //检查是否为允许上传的文件
            if (!that.checkFileIsUpload(fileSuf, that.settings.fileSuffixs)) {
                that.settings.onCheckUpload(that.settings.errorText.replace("{0}", fileSuf));
                return;
            }


            //选中后的回调
            that.settings.onChosen(this.value, this);




            if (that.checkFileIsExist(this.value)) {
                //保存已经选择的文件路径
                that.choseFilePath.push({ "name": this.name, "value": this.value });
                var status = that.settings.onSameFilesHandle(this.value);
                if (typeof status === "boolean" && !status) {
                    that.removeFile(this.value);
                    return;
                }
            } else {
                //保存已经选择的文件路径
                that.choseFilePath.push({ "name": this.name, "value": this.value });
            }


            //是否开启了图片预览
            if (that.settings.perviewImageElementId) {
                if (!that.settings.perviewImgStyle) {
                    perviewImage.beginPerview(this, that.settings.perviewImageElementId);
                } else {
                    var ul = perviewImage.getPerviewRegion(that.settings.perviewImageElementId);
                    var main = perviewImage.createPreviewElement(this.value);
                    var li = document.createElement("li");
                    //li.style.float = "left";
                    if ($.browser.msie) {
                        li.style.styleFloat = "left";
                    }
                    else {
                        li.style.cssFloat = "left";
                    }


                    li.style.margin = "5px";
                    li.appendChild(main);
                    ul.appendChild(li);
                    var div = $(main).children("div").get(0);
                    $(main).children("img").hover(function () {
                        this.src = perviewImage.closeImg.after;
                    }, function () {
                        this.src = perviewImage.closeImg.before;
                    }).click(function () {
                        that.removeFile($(this).attr("filepath"));
                        $(this).parents("li").remove("li");
                    });
                    perviewImage.beginPerview(this, div, dcmt);
                }
            }


            if (!that.settings.buttonFeature) {
                that.submitUpload();
            }
        };
        dcmt.forms[0].appendChild(input);
        return input;
    },


    //获取创建的iframe中的document对象
    getIframeContentDocument: function () {
        return this.iframe.contentDocument || this.iframe.contentWindow.document;
    },


    //获取创建的iframe所在的window对象
    getIframeWindow: function () {
        return this.iframe.contentWindow || this.iframe.contentDocument.parentWindow;
    },


    //获取创建的iframe内部iframe的document对象
    getInsideIframeContentDocument: function () {
        return this.getIframeWindow().getDCMT();
    },


    //获取上传input控件
    getUploadInput: function () {
        var inputs = this.getIframeContentDocument().getElementsByTagName("input");
        var len = inputs.length;


        if (len > 0) {
            if (!inputs[len - 1].value) {
                return inputs[len - 1];
            } else {
                return this.createInputFile();
            }
        }
        return this.createInputFile();
    },


    //forEach迭代函数
    forEach: function (/*数组*/arr, /*代理函数*/fn) {
        var len = arr.length;
        for (var i = 0; i < len; i++) {
            var tmp = arr[i];
            if (fn.call(tmp, i, tmp) == false) {
                break;
            }
        }
    },


    //提交上传
    submitUpload: function () {
        var status = this.settings.onSubmitHandle(this.choseFilePath.length);
        if (typeof status === "boolean") {
            if (!status) {
                return;
            }
        }
        this.clearedNotChooseFile();
        var dcmt = this.getIframeContentDocument();
        dcmt.forms[0].submit();
    },


    //检查文件是否可以上传
    checkFileIsUpload: function (fileSuf, suffixs) {


        var status = false;
        this.forEach(suffixs, function (i, n) {
            if (fileSuf.toLowerCase() === n.toLowerCase()) {
                status = true;
                return false;
            }
        });
        return status;
    },


    //检查上传的文件是否已经存在上传队列中
    checkFileIsExist: function (/*当前上传的文件*/file) {


        var status = false;
        this.forEach(this.choseFilePath, function (i, n) {
            if (n.value == file) {
                status = true;
                return false;
            }
        });
        return status;
    },


    //清除未选择文件的上传控件
    clearedNotChooseFile: function () {
        var files = this.getIframeContentDocument().getElementsByTagName("input");


        this.forEach(files, function (i, n) {
            if (!n.value) {
                n.parentNode.removeChild(n);
                return false;
            }
        });
    },


    //将指定上传的文件从上传队列中删除
    removeFile: function (file) {
        var that = this;
        var files = this.getIframeContentDocument().getElementsByTagName("input");
        this.forEach(this.choseFilePath, function (i, n) {
            if (n.value == file) {
                that.forEach(files, function (j, m) {
                    if (m.name == n.name) {
                        m.parentNode.removeChild(m);
                        return false;
                    }
                });
                that.choseFilePath.splice(i, 1);
                return false;
            }
        });
    },


    //清空上传队列
    clearUploadQueue: function () {
        this.choseFilePath.length = 0;
        this.getIframeContentDocument().forms[0].innerHTML = "";
    },


    //选择上传文件
    chooseFile: function () {
        var uploadfile;
        if (this.choseFilePath.length == this.settings.maximumFilesUpload) {
            if (this.settings.maximumFilesUpload <= 1) {
                this.choseFilePath.length = 0;
                var files = this.getIframeContentDocument().getElementsByTagName("input");
                if (!files.length) {
                    uploadfile = this.getUploadInput();
                    $(uploadfile).click();
                    return;
                } else {
                    uploadfile = files[0];
                    $(uploadfile).click();
                    return;
                }
            } else {
                return;
            }
        }
        uploadfile = this.getUploadInput();
        $(uploadfile).click();
    }
};


//图片预览操作
var perviewImage = {
    timers: [],
    closeImg: {
        before: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAOVSURBVHjaYtTUdGdAA4K/fv3U+Pv3rw+QLfT//3+Gf//+vWNiYtjCwsJyAyj2HiT2//8/sGKAAGJB1gkU9Pj581eNnJyctaamMgM/Py8DIyMDw+fPXxlu3rxfdfPmjaPMzIwtTEzMO2B6AAKIBaH5fw4LC1tHeHgQt7u7PYOOjhIDNzcb2IBfv/4x3LjxiGHr1n3WK1duXPPx45sKJiamKSB9AAHECPIC0GZ3ZmbWzQkJkazu7rYMLCyMDD9//gYZCzWcgYGVlRUozsxw9Oh5hv7+Gb8/fXrnC+TvBAggZhERZb7fv3/PdnCwV7C3twT69w+DlpYcw5s3HxkeP34FdP53IPsDg6qqNAMXFxvQIA4GoGXMFy9eVgK6eg1AADH9/ftbW0hIxEpFRQms0MBAlYGDg51BQ0OegZ2dneH58zdAMRUGKSlhBnFxQYY7dx4CvfSHQVBQyAqkFyCAmIWEFDOlpaVtgQHH8O7dB4aXLz8wqKjIMHBysoE1SUqKMCgoSIC90te3lGHNmu0MDx8+Yfjx4xvQmz9eAgQQCzAwhBiBIfX69RugwC+GR4+eAl3yliEx0Y+Bl5eDQU5ODBwG3d0LGdau3QH0AjMwLFiBruQEBjCTEEAAsYBC+du3HwxPnjxnAMY90JCfoLBlePXqLdAAabDNX778AHvl37+/QP9DYubfP0haAAggJlAi+fr1M8Pbt2+Bml4z8PBwMxQURDMoK0uDbf78+QfYJY2N2Qy2thZA//8CGsIMtOg70MI/7wACiAkYkluAfmH48+cPMOHwMbS1FTJoaspB/bwYqHE6w4cP3xn4+DgYWltzgAGqywCMNbABQBdsAQggJmAsX/3+/esxkPNAoX7jxgNQomKYMWMtw65dRxkuXLjGMHHiEobv338x3Lv3DEhDLAO6+hjQq1cBAohRWdkOqOGvOwcHz2Z1dU1WcXEJBgkJYYbbtx+AExIogH/9+s2gra0KDOgPwLTxmOHKlfO/v3z55AtM0jsBAggYjfKg0Lz769eP958/f7FnZ2djAyYUBhERQWBUcgLDhItBWFiY4f37j8AYeshw/frVr1++fCwFal4O8iZAAIENAKdpRoZTwLg99/Llc8VPnz7JffnyFWQwMAa+Mdy/fw+YmW4w3Lp1/eiPH19zgJqXwfIQQACBvQDNiaBsC/K/IDCQNICKfNjYWIVAYQNMH++AIb4FGPrg7IycgwECDADIUZC5UWvTuwAAAABJRU5ErkJggg==",
        after: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA3XAAAN1wFCKJt4AAAAB3RJTUUH1wwbFhkQHxvdFgAAArpJREFUeNplz1to1QUAx/HP/3/O2c7OrudsR8vmamOL2hw5LaQHgxKCLvQSRVgPFhJKo4cyMsmxKNQgfOuhfKxICQTpocvAIFhai/WSTNIcnK3pbu12tnO2cwsfbZ+H3+MXfgGbfURDgn0xHgiIFskUuPgOE/7njsBxahsYqAnDN1p3765N9/QIYzGL4+Myw8PlbC53fp23j5HZFBhgSxPft27f3tfX36+ure2O+sbKiitnzrg2MjK/xjNH+RUCeJbo41xsSyb3PtR/WLQuLhIGVAJKlEsUSyUBrp8966+xsdkl+o7zTwiP8mqSvffu6ZMLs+565YAVWRvL1xSWrpufvmHr/v3y1dVu30pHIuk4pxCEiCQ4nIyEYk15heakmm3bdR06Zrq0LjM3ruPNI+o7O8V7uy1NTkjV10vw4ns0Rw6Q7uCTdLwS1LfMyM+Mytxa0r73aVse2adp52Oau7pd/eGCq4MHNUxPKc4sy1dEylwOt3FPNUH09gTrWhILSr+d9N3pQfGGpHRXjz9/+tHoqRe0t8yqasyK1lKNKlrDdYpVCEqUixRLLBZTep98DkD7zofFO3pVGqlqJlJDDDEK4TkmQlbXc+QWGZ9P2TMwpHXHLpnL541+dVRtMuWp00NmGncpxCkUCLDGWDjJWp6LG0XmJtn6xOu2PrjLzUvn3fr6JdVXP3btmyNqGlN2vHbC/Dxry5SZ+pTfIyh3MN3Jyxt54cKVYYXVKZPn3peIFkTK5G5ckp34W+bLE7IjeWGOOT78gp8DQPwzBtt5dymkkqTmbuIpYnUEAblZsldIrLLB0AGeX2QlAEDD5xy7n7eWia2GlKsREhaIb9CEZS58wKFRplGJAGD9W36JMXwf9cmKdF1RIlGgrmS1zOU/GDjIyZv8iwoENguRQKqblnqiI8yUWUAWJQD4D4Cg/5i7WltRAAAAAElFTkSuQmCC"
    },


    //获取预览元素
    getElementObject: function (elem) {
        if (elem.nodeType && elem.nodeType === 1) {
            return elem;
        } else {
            return document.getElementById(elem);
        }
    },
    //开始图片预览
    beginPerview: function (/*文件上传控件实例*/file, /*需要显示的元素id或元素实例*/perviewElemId,dcmt) {
        for (var t = 0; t < this.timers.length; t++) {
            window.clearInterval(this.timers[t]);
        }
        this.timers.length = 0;


        var preview_div = this.getElementObject(perviewElemId);


        var MAXWIDTH = preview_div.clientWidth;
        var MAXHEIGHT = preview_div.clientHeight;


        if (file.files && file.files[0]) { //此处为Firefox，Chrome以及IE10的操作
            preview_div.innerHTML = "";
            var img = document.createElement("img");
            preview_div.appendChild(img);
            img.style.visibility = "hidden";
            img.onload = function () {
                var rect = perviewImage.clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                img.style.width = rect.width + 'px';
                img.style.height = rect.height + 'px';
                img.style.marginLeft = rect.left + 'px';
                img.style.marginTop = rect.top + 'px';
                img.style.visibility = "visible";
            }


            var reader = new FileReader();
            reader.onload = function (evt) {
                img.src = evt.target.result;
            }
            reader.readAsDataURL(file.files[0]);
        }
        else {//此处为IE6，7，8，9的操作
            file.select();
            var src = dcmt.selection.createRange().text;


            var div_sFilter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale',src='" + src + "')";
            var img_sFilter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src='" + src + "')";


            preview_div.innerHTML = "";
            var img = document.createElement("div");
            preview_div.appendChild(img);
            img.style.filter = img_sFilter;
            img.style.visibility = "hidden";
            img.style.width = "100%";
            img.style.height = "100%";


            function setImageDisplay() {
                var rect = perviewImage.clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                preview_div.innerHTML = "";
                var div = document.createElement("div");
                div.style.width = rect.width + 'px';
                div.style.height = rect.height + 'px';
                div.style.marginLeft = rect.left + 'px';
                div.style.marginTop = rect.top + 'px';
                div.style.filter = div_sFilter;
                preview_div.appendChild(div);
            }


            //图片加载计数
            var tally = 0;


            var timer = window.setInterval(function () {
                if (img.offsetHeight != MAXHEIGHT) {
                    window.clearInterval(timer);
                    setImageDisplay()
                } else {
                    tally++;
                }
                //如果超过两秒钟图片还不能加载，就停止当前的轮询
                if (tally > 20) {
                    window.clearInterval(timer);
                    setImageDisplay()
                }
            }, 100);


            this.timers.push(timer);
        }
    },
    //按比例缩放图片
    clacImgZoomParam: function (maxWidth, maxHeight, width, height) {
        var param = { width: width, height: height };
        if (width > maxWidth || height > maxHeight) {
            var rateWidth = width / maxWidth;
            var rateHeight = height / maxHeight;


            if (rateWidth > rateHeight) {
                param.width = maxWidth;
                param.height = Math.round(height / rateWidth);
            } else {
                param.width = Math.round(width / rateHeight);
                param.height = maxHeight;
            }
        }


        param.left = Math.round((maxWidth - param.width) / 2);
        param.top = Math.round((maxHeight - param.height) / 2);
        return param;
    },
    //创建预览元素
    createPreviewElement: function (/*上传时的文件名*/file, /*预览时的样式*/style) {
        style = style || { width: '100px', height: '100px', border: '1px solid #ebebeb' };
        var img = document.createElement("div");
        img.title = file;
        img.style.overflow = "hidden";
        for (var s in style) {
            img.style[s] = style[s];
        }
        var text = document.createElement("div");
        text.style.width = style.width;
        text.style.overflow = "hidden";
        text.style.textOverflow = "ellipsis";
        text.style.whiteSpace = "nowrap";
        text.innerHTML = file;




        var top = 0 - window.parseInt(style.width) - 15;
        var right = 0 - window.parseInt(style.width) + 14;
        var close = document.createElement("img");
        close.setAttribute("filepath", file);
        close.src = this.closeImg.before;
        close.style.position = "relative";
        close.style.top = top + "px";
        close.style.right = right + "px";
        close.style.cursor = "pointer";


        var main = document.createElement("div");
        main.appendChild(img);
        main.appendChild(text);
        main.appendChild(close);
        return main;
    },


    //获取预览区域
    getPerviewRegion: function (elem) {
        var perview = $(this.getElementObject(elem));
        if (!perview.find("ul").length) {
            var ul = document.createElement("ul");
            ul.style.listStyleType = "none";
            ul.style.margin = "0px";
            ul.style.padding = "0px";


            var div = document.createElement("div");
            div.style.clear = "both";
            perview.append(ul).append(div);
            return ul;
        } else {
            return perview.children("ul").get(0);
        }
    }
}