(function () {
 'use strict';
 var root = document.documentElement;
 var sidebar = document.querySelector('.nord-sidebar');
 var isChinese = (document.documentElement.lang || '').indexOf('zh') === 0;
 function makeRail(target, isPage, isOutline) {
  var rail = document.createElement('div'), thumb = document.createElement('span');
  rail.className = 'nord-scroll-rail' + (isPage ? '' : isOutline ? ' nord-scroll-outline' : ' nord-scroll-sidebar');
  rail.setAttribute('role', 'scrollbar'); rail.tabIndex = 0;
  rail.setAttribute('aria-label', isChinese ? (isPage ? '页面阅读位置' : (isOutline ? '目录滚动位置' : '侧栏滚动位置')) : (isPage ? 'Page reading position' : (isOutline ? 'Contents scroll position' : 'Sidebar scroll position')));
  rail.setAttribute('aria-controls', isPage ? 'content' : isOutline ? 'nord-toc' : 'nord-navigation');
  rail.setAttribute('aria-orientation', 'vertical'); rail.setAttribute('aria-valuemin', '0');rail.setAttribute('aria-valuemax', '100');
  thumb.className = 'nord-scroll-thumb'; rail.appendChild(thumb); document.body.appendChild(rail);
  var track = 0, size = 0, max = 0, dragOffset = 0;
  function position() { return isPage ? window.scrollY : target.scrollTop; }
  function update() {
   var viewport = isPage ? innerHeight : target.clientHeight;
   max = Math.max(0, target.scrollHeight - viewport); rail.hidden = max === 0;
   if (isOutline) {
    var box = target.getBoundingClientRect();rail.hidden = max === 0 || box.height === 0;
    rail.style.left = (box.right - 5) + 'px';rail.style.top = box.top + 'px';rail.style.height = box.height + 'px';rail.style.bottom = 'auto';
   } else if (!isPage) { rail.style.left = (sidebar.offsetWidth - 8) + 'px'; }
   track = rail.clientHeight; size = Math.min(track, Math.max(32, track * viewport / target.scrollHeight));
   thumb.style.height = size + 'px';thumb.style.transform = 'translateY(' + (max ? position() / max * (track - size) : 0) + 'px)';
   rail.setAttribute('aria-valuenow', String(Math.round(max ? position() / max * 100 : 0)));
  }
  function move(value) { target.scrollTo({top:Math.max(0, Math.min(max, value)), behavior:'instant'}); }
  rail.addEventListener('pointerdown', function (e) {
   if (e.button !== 0) { return; }
   e.preventDefault();rail.focus();rail.setPointerCapture(e.pointerId);
   var rect = thumb.getBoundingClientRect();
   dragOffset = e.clientY >= rect.top && e.clientY <= rect.bottom ? e.clientY - rect.top : size / 2;
   seek(e);
  });
  function seek(e) { if (track > size) { move((e.clientY - rail.getBoundingClientRect().top - dragOffset) / (track - size) * max); } }
  rail.addEventListener('pointermove', function(e) { if (rail.hasPointerCapture(e.pointerId)) { seek(e); } });
  rail.addEventListener('pointerup', function(e) { if (rail.hasPointerCapture(e.pointerId)) { rail.releasePointerCapture(e.pointerId); } });
  rail.addEventListener('keydown', function(e) {
   var step = isPage ? innerHeight : target.clientHeight;
   var values = {ArrowDown:position()+48,ArrowUp:position()-48,PageDown:position()+step*.85,PageUp:position()-step*.85,Home:0,End:max};
   if (Object.prototype.hasOwnProperty.call(values,e.key)) { e.preventDefault();move(values[e.key]); }
  });
  rail.addEventListener('wheel', function(e) {
   if (e.ctrlKey || e.metaKey) { return; }
   e.preventDefault();
   var viewport = isPage ? innerHeight : target.clientHeight;
   move(position() + e.deltaY * (e.deltaMode === 1 ? 16 : e.deltaMode === 2 ? viewport : 1));
  }, {passive:false});
  (isPage ? window : target).addEventListener('scroll',update,{passive:true});
  new ResizeObserver(update).observe(isPage ? document.body : target);
  if (!isPage) { new ResizeObserver(update).observe(isOutline ? target.querySelector('ol') : sidebar.querySelector('.nord-navigation')); }
  if (isOutline) { window.addEventListener('scroll',update,{passive:true}); }
  window.addEventListener('resize',update);window.addEventListener('nord-layout-change',update);update();
 }
 makeRail(document.scrollingElement,true);makeRail(sidebar,false);makeRail(document.querySelector('.nord-outline-sticky'),false,true);
 root.classList.add('nord-custom-scroll');
}());
