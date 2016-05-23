/**
 * tex.iframe.js
 * iframe内でのtexを使うためのscript
 */
var TEX_IFR = new (function TEX_IFR() {
  var self = this;
  var $IFRAME;

  var __setMathJax = function(id) {
    MathJax.Hub.Queue(['Typeset', MathJax.Hub, 'preview_ifr']);
  };
  self.init = function($ifr) {
    $IFRAME = $ifr;
    // init MathJax
    MathJax.Hub.Config({
      tex2jax: { inlineMath: [['$$', '$$'], ['\\(', '\\)']] }
    });
    __setMathJax();
  };
})();
