var t = getApp();

Page({
    data: {
        type: 0,
        comments: {
            page: 1,
            psize: 15,
            loaded: !1,
            empty: !1,
            data: []
        },
        replyDialog: !1,
        replyContent: ""
    },
    onLoad: function(t) {
        this.onPullDownRefresh();
    },
    onChangeType: function(t) {
        var e = this, a = t.currentTarget.dataset.type;
        if (a == e.data.type) return !1;
        e.setData({
            type: a,
            comments: {
                page: 1,
                psize: 15,
                loaded: !1,
                empty: !1,
                data: []
            }
        }), e.onReachBottom();
    },
    onShowReplyDialog: function(t) {
        var e = this;
        e.setData({
            replyDialog: !e.data.replyDialog
        }), t && t.currentTarget && t.currentTarget.dataset && t.currentTarget.dataset.id && e.setData({
            commentId: t.currentTarget.dataset.id
        });
    },
    onSelectReply: function(t) {
        var e = this, a = t.currentTarget.dataset.index;
        e.setData({
            replyIndex: a,
            replyContent: e.data.store.comment_reply[a]
        });
    },
    onGetTextarea: function(t) {
        this.setData({
            replyContent: t.detail.value
        });
    },
    onPullDownRefresh: function() {
        var t = this;
        t.setData({
            type: 0,
            comments: {
                page: 1,
                psize: 15,
                loaded: !1,
                empty: !1,
                data: []
            },
            replyDialog: !1,
            replyContent: ""
        }), t.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.comments.loaded) return !1;
        t.util.request({
            url: "manage/service/comment",
            data: {
                type: e.data.type,
                page: e.data.comments.page,
                psize: e.data.comments.psize
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) t.util.toast(n.message); else {
                    n = n.message;
                    var o = e.data.comments.data.concat(n.comments);
                    e.data.comments.data = o, e.data.comments.page++, o.length || (e.data.comments.empty = !0), 
                    n.comments.length < e.data.comments.psize && (e.data.comments.loaded = !0), e.setData({
                        comments: e.data.comments,
                        store: n.store
                    });
                }
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onShowImage: function(e) {
        t.util.showImage(e);
    }
});