( function () {
'use strict';
var root = document.documentElement;
var theme = document.querySelector('.nord-theme-button');
function setTheme(value) {
 root.dataset.nordTheme = value;
 theme.setAttribute('aria-pressed', String(value === 'dark'));
}
try { setTheme(localStorage.getItem('nord-theme') || 'dark'); } catch (e) { setTheme('dark'); }
theme.addEventListener('click', function () {
 var value = root.dataset.nordTheme === 'dark' ? 'light' : 'dark';
 setTheme(value);
 try { localStorage.setItem('nord-theme', value); } catch (e) { /* Storage may be disabled. */ }
});
var textSizeKey = 'nord-text-size';
var sizeButtons = document.querySelectorAll('[data-text-size]');
function setTextSize(value) {
 root.dataset.textSize = value;
 sizeButtons.forEach(function(button) { button.setAttribute('aria-pressed', String(button.dataset.textSize === value)); });
}
try { setTextSize(localStorage.getItem(textSizeKey) || 'standard'); } catch (e) { setTextSize('standard'); }
sizeButtons.forEach(function(button) { button.addEventListener('click', function() {
 setTextSize(button.dataset.textSize);
 try { localStorage.setItem(textSizeKey,button.dataset.textSize); } catch (e) { /* Storage may be disabled. */ }
}); });
document.querySelector('.nord-account-link').href = mw.util.getUrl(mw.config.get('wgUserName') ? 'Special:Preferences' : 'Special:UserLogin', mw.config.get('wgUserName') ? {} : {returnto:mw.config.get('wgPageName')});
document.addEventListener('keydown', function (e) {
 var editing = e.target.isContentEditable || e.target.matches('input, textarea, select');
 if (e.key === '/' && !editing && !e.ctrlKey && !e.metaKey && !e.altKey) { e.preventDefault(); document.getElementById('searchInput').focus(); }
 if (e.key === 'Escape') {
  document.querySelectorAll('.nord-dropdown[open]').forEach(function (d) { d.open = false; });
 }
});
document.addEventListener('click', function (e) {
 var watchPopupClick = e.composedPath().some(function(node) { return node instanceof Element && node.matches('.mw-watchlink-popup, #mw-watchstar-WatchlistPopup, .mw-notification'); });
 document.querySelectorAll('.nord-dropdown[open]').forEach(function(d) {
  if (d.classList.contains('nord-page-tools') && watchPopupClick) { return; }
  if (!d.contains(e.target)) { d.open = false; }
 });
});
// Authors can label plain pre blocks without depending on this skin for rendering.
document.querySelectorAll('.nord-body pre').forEach(function (pre) {
 var wrapper = document.createElement('div'), label = document.createElement('div');
 wrapper.className = 'nord-code-block'; label.className = 'nord-code-label';
 label.textContent = pre.dataset.language || 'Text';
 pre.before(wrapper); wrapper.append(label, pre);
});
var headings = Array.from(document.querySelectorAll('#mw-content-text .mw-parser-output h2, #mw-content-text .mw-parser-output h3'));
var list = document.getElementById('nord-toc');
var items = [];
headings.forEach(function (heading) {
 if (heading.closest('.toc')) { return; }
 var anchor = heading.id ? heading : heading.querySelector('[id]');
 if (!anchor) { return; }
 var li = document.createElement('li'), link = document.createElement('a');
 li.className = heading.tagName === 'H3' ? 'nord-toc-sub' : '';
 link.href = '#' + encodeURIComponent(anchor.id);
 var title = heading.cloneNode(true); title.querySelectorAll('.mw-editsection').forEach(function (e) { e.remove(); });
 link.textContent = title.textContent.trim(); li.appendChild(link); list.appendChild(li);
 items.push({ node: heading, link: link });
});
if (items.length) { root.classList.add('nord-has-toc'); } else { document.querySelector('.nord-outline').hidden = true; }
var scheduled = false;
function update() {
 var active = null; items.forEach(function (item) { if (item.node.getBoundingClientRect().top < 220) { active = item; } });
 items.forEach(function (item) { item.link.classList.toggle('is-active', item === active); if (item === active) { item.link.setAttribute('aria-current','location'); } else { item.link.removeAttribute('aria-current'); } });
 var max = document.documentElement.scrollHeight - innerHeight;
 document.querySelector('.nord-progress').style.transform = 'scaleX(' + (max > 0 ? scrollY / max : 0) + ')';
 scheduled = false;
}
addEventListener('scroll', function () { if (!scheduled) { scheduled = true; requestAnimationFrame(update); } }, {passive:true});
update();
}() );