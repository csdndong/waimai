var h = "";
var st = 0;
var arrDinner = new Array();


function closeNav() {
    $(".openClose").toggle();
}

function ckonos(n) {
    var ckImage = $("#img" + n);
    var src = ckImage.attr("src");
    if (src.indexOf("shoppingcartok") > 0) {
        ckImage.attr("src", "../src/shoppingcart.png");
        delItem(n)
    }
    else {
        ckImage.attr("src", "../src/shoppingcartok.png");
        addItem(n);
    }
}
function showlku() {
    $("#kuloading").fadeIn()
}
function closelku() {
    $("#kuloading").fadeOut()
}



function showsellinfo(a, b, c) {
    $("#showsell").attr("src", a)
    $("#showsell").css("width", "100%")
    $("#showititle").html(b)
    $("#showicom").html(c)
    $("#showsinfo").fadeIn("slow");
}
function closeshowsell() {
    $("#showsinfo").fadeOut("slow");
}
function showsearch() {
    if (st == 0) {
        $("#searchcom").fadeIn("slow");
        $("#sbts").attr("src", "../src/searchx.png");
        st = 1;
    } else {
        $("#searchcom").fadeOut("slow");
        $("#sbts").attr("src", "../src/search.png");
        st = 0;
    }

}

function searchclick() {
    if (document.getElementById("searcht").value == "") {
        $("#searchno").fadeIn();

    } else {
        window.open("main.aspx?type=search&key=" + document.getElementById("searcht").value, "_self");
    }
}
function GoUrl(value) {
    window.open("main.aspx?type=menu&id=" + value, "_self");
}

function addItem(n) {
    var name = 'itemid';
    var ownehid = "";
    var arrx = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arrx != null) (ownehid = arrx[2]);

    var sstt = "";
    var strs = new Array(); //定义一数组 
    strs = ownehid.split("t"); //字符分割 
    for (i = 0; i < strs.length; i++) {

        if (strs[i] != n && strs[i] != "") {
            sstt += "t" + strs[i]
        }

    }

    var value = sstt + "t" + n;

    var Days = 1; //此 cookie 将被保存 30 天
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    $("#count").html(value.split("t").length - 1);
}
function delItem(n) {
    var name = 'itemid';
    var ownehid = "";
    var arrx = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arrx != null) (ownehid = arrx[2]);

    var sstt = "";
    var strs = new Array(); //定义一数组 
    strs = ownehid.split("t"); //字符分割
    for (i = 0; i < strs.length; i++) {

        if (strs[i] != n && strs[i] != "") {
            sstt += "t" + strs[i]
        }

    }

    var value = sstt;

    var Days = 1; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
    $("#count").html(value.split("t").length - 1);
}
function LoadData() {
    var arrx = document.cookie.match(new RegExp("(^| )itemid=([^;]*)(;|$)"));
    var ownehid = "";
    if (arrx != null)
        ownehid = arrx[2];
    var strs = new Array(); //定义一数组 
    strs = ownehid.split("t"); //字符分割
    $("#count").html(strs.length - 1);
}
function loadSelected() {
    var arrx = document.cookie.match(new RegExp("(^| )itemid=([^;]*)(;|$)"));
    var ownehid = "";
    if (arrx != null)
        ownehid = arrx[2];
    var strs = new Array(); //定义一数组 
    strs = ownehid.split("t"); //字符分割
    var noIds = new Array();
    for (var i = 0; i < strs.length; i++) {
        var obj = $("#img" + strs[i]);
        if (obj == null || obj == 'undefine' || obj == '') {
            continue;
        }
        obj.attr("src", "../src/shoppingcartok.png");
    }
}
function GoCart(url) {
    window.open(url, "_self");
}
function SaveTouchMan() {
    var tel = $("#tel");
    var name = 'tellPhone';
    var Days = 1; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    var guest_name = $("#guest_name");
    var address = $("#address");
    var value = escape(tel.val()) + "@" + escape(guest_name.val()) + "@" + escape(address.val());
    document.cookie = name + "=" + value + ";expires=" + exp.toGMTString() + ";path=/";

}
function SaveuserName() {
    var userName = $("#txtName");
    var name = 'userman';
    var Days = 1; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    var Phone = $("#txtPhone");
    var Address = $("#txtAddress");
    var value = escape(userName.val()) + "@" + escape(Phone.val()) + "@" + escape(Address.val());
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + value + ";expires=" + exp.toGMTString() + ";path=/";

}
function LoadUserName() {
    var value = document.cookie.match(new RegExp("(^| )userman=([^;]*)(;|$)"));
    if (value == null || value == '') return;
    var v = value[2];
    if (unescape(v.split('@')[0]) != 'undefined' && unescape(v.split('@')[0]) != null) {
        $("#txtName").val(unescape(v.split('@')[0]));
    }
    if (unescape(v.split('@')[1]) != 'undefined' && unescape(v.split('@')[1]) != null) {
        $("#txtPhone").val(unescape(v.split('@')[1]));
    }
    if (unescape(v.split('@')[2]) != 'undefined' && unescape(v.split('@')[2]) != null) {
        $("#txtAddress").val(unescape(v.split('@')[2]));
    }
}
function GetTouchMan() {
    var value = document.cookie.match(new RegExp("(^| )tellPhone=([^;]*)(;|$)"));
    var v = value[2];
    $("#tel").val(unescape(v.split('@')[0]));
    $("#guest_name").val(unescape(v.split('@')[1]));
    $("#address").val(unescape(v.split('@')[2]));

}

function switchMenu() {
    var TPL = '<li class="dish_item">\
                    <span class="dishName">{name}</span>\
                    <i>{price}元/份</i>\
                    <span class="btn_common" onclick="slideOn(this, event);">添加备注</span>\
                    <div><textarea name="description" onblur="changeDescription(this, event, {dishes_id});">{description}</textarea></div>\
                    <section class="bbox" dishname="{name}" onclick="changeCount(this, event, {dishes_id});">\
                        <input class="btn-reduce" type="button" value="-">\
                        <input class="numBox" name="numBox" type="text" value="{selected_count}" price="{price}" readonly="readonly">\
                        <input type="button" class="btn-plus" value="+">\
                    </section>\
                    </li>';
    var myorder = document.getElementById("myorder");
    var totalPrice = 0;
    var count_freight = 0;
    myorder.innerHTML = iTemplate.makeList(TPL, window.res.data, function (k, v) {
        if (v.discount_price) {

            v.price = v.discount_price;
        }
        count_freight = v.count_freight;
        totalPrice += parseInt(v.price) * parseInt(v.selected_count);
        return {
            description: v.description || ""
        }
    });
    if(count_freight)
        document.getElementById("countfreight").innerHTML = "(含" + parseInt(count_freight) + "元送餐费)";

    document.getElementById("total").innerHTML = parseInt(totalPrice) + parseInt(count_freight);
}



function changeCount(thi, evt, dishes_id) {
    if ("button" == evt.target.type) {
        var counter = thi.querySelectorAll("input[name='numBox']")[0];
        var val = parseInt(counter.value);
        if ("btn-reduce" == evt.target.className) {
            val--;
            if (val == 0) {
                delItem(dishes_id);
                window.location.reload();
                return; 
             }
        } else {
            val++;
        }
        counter.value = Math.max(0, val);
        var totalPrice = 0;
        for (var i = 0, ci; ci = window.res.data[i]; i++) {
            if (dishes_id == ci.dishes_id) {
                ci.selected_count = counter.value;
                //break;
            }
            totalPrice += parseInt(ci.price) * parseInt(ci.selected_count);
        }
        document.getElementById("total").innerHTML = totalPrice;
        //switchMenu();
    }
}



function changeDescription(thi, evt, dishes_id) {
    for (var i = 0; i < window.res.data.length; i++) {
        var ci = window.res.data[i];

        if (dishes_id == ci.dishes_id) {
            ci.description = thi.value;
            break;
        }
    }
}
function clearAll() {
    MDialog.confirm(
            '', '是否清空菜单？', null,
            '确定', function () {
                $.ajax({
                    'url': '../TemleteHandle.ashx?type=ClearOrder',
                    'success': function (db) {
                        $('#myorder').empty();
                        $('#total').text('0');
                        $('#submit_form').hide();
                        window.location.href = 'main.aspx';
                    }
                });

            }, null,
            '取消', null, null,
            null, true, true
        );
}
function slideOn(thi, evt) {
    var li = $(thi).closest("li");
    li["toggleClass"]("on");
}