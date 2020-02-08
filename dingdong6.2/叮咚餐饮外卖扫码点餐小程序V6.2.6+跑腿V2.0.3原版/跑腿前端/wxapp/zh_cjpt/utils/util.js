function formatTime(t) {
    var e = t.getFullYear(), r = t.getMonth() + 1, o = t.getDate(), m = t.getHours(), n = t.getMinutes(), u = t.getSeconds();
    return [ e, r, o ].map(formatNumber).join("/") + " " + [ m, n, u ].map(formatNumber).join(":");
}

function formatNumber(t) {
    return (t = t.toString())[1] ? t : "0" + t;
}

module.exports = {
    formatTime: formatTime
};