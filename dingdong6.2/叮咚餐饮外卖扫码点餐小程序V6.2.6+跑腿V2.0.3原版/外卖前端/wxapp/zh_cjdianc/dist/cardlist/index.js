var app = getApp();

Component({
    externalClasses: [ "extra-class" ],
    options: {},
    properties: {
        content: {
            type: Object,
            value: {}
        },
        num: {
            type: String,
            value: "3"
        }
    },
    data: {},
    attached: function(t) {},
    methods: {
        hideDialog: function() {
            this.setData({
                isShow: !this.data.isShow
            });
        },
        showDialog: function() {
            this.setData({
                isShow: !this.data.isShow
            });
        },
        jumps: function(t) {
            var a = t.currentTarget.dataset.item;
            wx.setStorageSync("vr", a.src2);
        }
    }
});