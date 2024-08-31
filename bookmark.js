javascript: (function() {
    var a = window,
        b = document,
        c = encodeURIComponent,
        d = a.open("https://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=" + c(b.location) + "&title=" + c(b.title), "bkmk_popup", "left=" + ((a.screenX || a.screenLeft) + 10) + ",top=" + ((a.screenY || a.screenTop) + 10) + ",height=510px,width=550px,resizable=1,alwaysRaised=1");
    a.setTimeout(function() {
        d.focus()
    }, 300)
})();
