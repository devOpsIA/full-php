! function(t, e) {
 var n;
 t.pageTitleNotification = (n = {
  currentTitle: null,
  interval: null
 }, {
  on: function(l, i) {
   n.interval || (n.currentTitle = e.title, n.interval = t.setInterval(function() {
    e.title = n.currentTitle === e.title ? l : n.currentTitle
   }, i || 1e3))
  },
  off: function() {
   t.clearInterval(n.interval), n.interval = null, e.title = n.currentTitle
  }
 })
}(window, document);