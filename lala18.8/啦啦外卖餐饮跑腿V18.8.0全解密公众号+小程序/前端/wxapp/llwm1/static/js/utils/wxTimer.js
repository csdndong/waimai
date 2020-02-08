function e(e, i, t) {
    return i in e ? Object.defineProperty(e, i, {
        value: t,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : e[i] = t, e;
}

var i = function(e) {
    e = e || {}, this.endTime = e.endTime, this.interval = e.interval || 0, this.complete = e.complete, 
    this.intervalFn = e.intervalFn, this.name = e.name, this.issplit = !!e.issplit && e.issplit, 
    this.intervarID;
};

i.prototype = {
    start: function(i) {
        function t() {
            var t, a = (r.systemEndTime - new Date().getTime()) / 1e3, s = Math.floor(a / 3600 / 24), m = Math.floor(a / 3600 % 24), o = Math.floor(a / 60 % 60), l = Math.floor(a % 60);
            s = s < 10 ? "0" + s : s, m = m < 10 ? "0" + m : m, o = o < 10 ? "0" + o : o, l = l < 10 ? "0" + l : l;
            var T = i.data.wxTimerList || [];
            r.issplit ? (s = String(s).split(""), m = String(m).split(""), o = String(o).split(""), 
            l = String(l).split(""), T[r.name] = e({
                wxTimerSecond: l,
                wxTimerDay: s,
                wxTimerHour: m,
                wxTimerMinute: o
            }, "wxTimerSecond", l)) : T[r.name] = e({
                wxTimerSecond: l,
                wxTimerDay: s,
                wxTimerHour: m,
                wxTimerMinute: o
            }, "wxTimerSecond", l), i.setData((t = {
                wxTimerSecond: l,
                wxTimerDay: s,
                wxTimerHour: m,
                wxTimerMinute: o
            }, e(t, "wxTimerSecond", l), e(t, "wxTimerList", T), t)), 0 == (n - 1) % r.interval && r.intervalFn && r.intervalFn(), 
            a <= 0 && (r.complete && r.complete(), r.stop());
        }
        var r = this;
        this.systemEndTime = new Date(this.endTime).getTime();
        var n = 0;
        t(), this.intervarID = setInterval(t, 1e3);
    },
    stop: function() {
        clearInterval(this.intervarID);
    }
}, module.exports = i;