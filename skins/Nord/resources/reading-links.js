(function () {
 'use strict';
 function externalLinks() {
  document.querySelectorAll('a[href]').forEach(function(a) {
   var url = new URL(a.href,location.href);
   if ((url.protocol === 'https:' || url.protocol === 'http:') && url.origin !== location.origin) {
    a.target = '_blank'; a.relList.add('noopener');
   }
  });
 }
 externalLinks();mw.hook('wikipage.content').add(externalLinks);
 var root = document.documentElement, timer;
 function highlight() {
  clearTimeout(timer);root.classList.remove('nord-cite-dismissed');
  if (/^#cite_(note|ref)/.test(location.hash)) {
   timer = setTimeout(function() { root.classList.add('nord-cite-dismissed'); },4000);
  }
 }
 addEventListener('hashchange',highlight);
 document.addEventListener('click',function(e) {
  var a = e.target.closest('a[href]');
  if (a && a.origin === location.origin && a.pathname === location.pathname && /^#cite_(note|ref)/.test(a.hash)) { highlight(); }
  else { clearTimeout(timer);root.classList.add('nord-cite-dismissed'); }
 });
 highlight();
}());
