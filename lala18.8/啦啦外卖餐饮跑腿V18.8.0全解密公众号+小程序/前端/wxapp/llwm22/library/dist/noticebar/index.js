function t(t, i, e) {
    return i in t ? Object.defineProperty(t, i, {
        value: e,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : t[i] = e, t;
}

var i = {
    initZanNoticeBarScroll: function(t) {
        var i = this;
        this.zanNoticeBarNode = this.zanNoticeBarNode || {}, this.zanNoticeBarNode["" + t] = {
            width: void 0,
            wrapWidth: void 0,
            animation: null,
            resetAnimation: null
        };
        var e = this.zanNoticeBarNode["" + t];
        wx.createSelectorQuery().in(this).select("#" + t + "__content").boundingClientRect(function(n) {
            n && n.width ? (e.width = n.width, wx.createSelectorQuery().in(i).select("#" + t + "__content-wrap").boundingClientRect(function(n) {
                if (n && n.width && (clearTimeout(i.data[t].setTimeoutId), e.wrapWidth = n.width, 
                e.wrapWidth < e.width)) {
                    var a = e.width / 40 * 1e3;
                    e.animation = wx.createAnimation({
                        duration: a,
                        timingFunction: "linear"
                    }), e.resetAnimation = wx.createAnimation({
                        duration: 0,
                        timingFunction: "linear"
                    }), i.scrollZanNoticeBar(t, a);
                }
            }).exec()) : console.warn("页面缺少 noticebar 元素");
        }).exec();
    },
    scrollZanNoticeBar: function(i, e) {
        var n = this, a = this.zanNoticeBarNode["" + i], o = a.resetAnimation.translateX(a.wrapWidth).step();
        this.setData(t({}, i + ".animationData", o.export()));
        var r = a.animation.translateX(40 * -e / 1e3).step();
        setTimeout(function() {
            n.setData(t({}, i + ".animationData", r.export()));
        }, 100);
        var c = setTimeout(function() {
            n.scrollZanNoticeBar(i, e);
        }, e);
        this.setData(t({}, i + ".setTimeoutId", c));
    }
};

module.exports = i;