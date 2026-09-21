(function () {
 'use strict';
 var root = document.documentElement;
 var sidebar = document.querySelector('.nord-sidebar');
 var menu = document.querySelector('.nord-menu-button');
 var scrim = document.querySelector('.nord-scrim');
 var outline = document.querySelector('.nord-outline');
 var closeRight = document.querySelector('.nord-outline-close');
 var dock = document.querySelector('.nord-outline-dock');
 var fab = document.querySelector('.nord-contents-fab');
 var mobile = matchMedia('(max-width:850px)');
 var narrow = matchMedia('(max-width:1199px)');
 var hasContents = root.classList.contains('nord-has-toc');
 var leftCollapsed = false, rightCollapsed = false, drawerOpen = false, contentsOpen = false;
 try {
  leftCollapsed = localStorage.getItem('nord-left-collapsed') === 'true';
  rightCollapsed = localStorage.getItem('nord-right-collapsed') === 'true';
 } catch (e) { /* Browser storage can be disabled. */ }
 function save(key, value) {
  try { localStorage.setItem(key, String(value)); } catch (e) { /* Preference is session-only. */ }
 }
 function floating() { return narrow.matches || rightCollapsed; }
 function render() {
  var leftVisible = mobile.matches ? drawerOpen : !leftCollapsed;
  root.classList.toggle('nord-left-collapsed', leftCollapsed);
  root.classList.toggle('nord-menu-open', mobile.matches && drawerOpen);
  root.classList.toggle('nord-outline-floating', floating());
  root.classList.toggle('nord-outline-open', contentsOpen && floating());
  root.classList.toggle('nord-wide-content', !hasContents || floating());
  menu.setAttribute('aria-expanded', String(leftVisible));
  menu.setAttribute('aria-label', mw.msg(leftVisible ? 'nord-hide-navigation' : 'nord-show-navigation'));
  menu.title = menu.getAttribute('aria-label');
  sidebar.inert = !leftVisible;
  sidebar.setAttribute('aria-hidden', String(!leftVisible));
  scrim.hidden = !(mobile.matches && drawerOpen);
  outline.hidden = !hasContents || (floating() && !contentsOpen);
  fab.hidden = !hasContents || !floating() || drawerOpen;
  fab.setAttribute('aria-expanded', String(contentsOpen));
  dock.hidden = narrow.matches || !rightCollapsed;
  window.dispatchEvent(new Event('nord-layout-change'));
 }
 function closeDrawer(restoreFocus) {
  drawerOpen = false; render();
  if (restoreFocus) { menu.focus(); }
 }
 function closeContents(restoreFocus) {
  contentsOpen = false; render();
  if (restoreFocus) { fab.focus(); }
 }
 menu.addEventListener('click', function () {
  if (mobile.matches) {
   drawerOpen = !drawerOpen; contentsOpen = false;
  } else {
   leftCollapsed = !leftCollapsed; save('nord-left-collapsed', leftCollapsed);
  }
  render();
  if (drawerOpen) { sidebar.querySelector('a').focus(); }
 });
 scrim.addEventListener('click', function () { closeDrawer(true); });
 sidebar.addEventListener('click', function (e) {
  if (mobile.matches && e.target.closest('a')) { closeDrawer(true); }
 });
 closeRight.addEventListener('click', function () {
  if (floating()) { closeContents(true); } else {
   rightCollapsed = true; save('nord-right-collapsed', true); contentsOpen = false; render(); fab.focus();
  }
 });
 dock.addEventListener('click', function () {
  rightCollapsed = false; save('nord-right-collapsed', false); contentsOpen = false; render(); closeRight.focus();
 });
 fab.addEventListener('click', function () {
  contentsOpen = !contentsOpen; render();
  if (contentsOpen) { closeRight.focus(); }
 });
 outline.addEventListener('click', function (e) {
  if (floating() && e.target.closest('a[href^="#"]')) { closeContents(true); }
 });
 document.addEventListener('click', function (e) {
  if (contentsOpen && !outline.contains(e.target) && !fab.contains(e.target) && !e.target.closest('.nord-scroll-outline')) { closeContents(false); }
 });
 document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
   if (drawerOpen) { closeDrawer(true); }
   if (contentsOpen) { closeContents(true); }
  }
  if (e.key === 'Tab' && drawerOpen) {
   var controls = Array.from(sidebar.querySelectorAll('a[href],button')).filter(function (el) { return el.offsetParent !== null; });
   var first = controls[0], last = controls[controls.length - 1];
   if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
   else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }
 });
 outline.addEventListener('focusout', function () {
  setTimeout(function () {
   if (contentsOpen && !outline.contains(document.activeElement) && document.activeElement !== fab && !document.activeElement.matches('.nord-scroll-outline')) { closeContents(false); }
  }, 0);
 });
 function resized() { drawerOpen = false; contentsOpen = false; render(); }
 mobile.addEventListener('change', resized); narrow.addEventListener('change', resized);
 render();
}());
