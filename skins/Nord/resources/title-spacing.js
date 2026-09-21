(function () {
 'use strict';
 // Namespace spacing is presentation only; never change identifiers or edit fields.
 var names = Object.values(mw.config.get('wgFormattedNamespaces')).filter(Boolean);
 var namespaces = names.map(function(name) { return mw.util.escapeRegExp(name); }).join('|');
 var pattern = new RegExp('(^|[\\s(])(' + namespaces + '):(?=\\S)', 'gi');
 function spaced(text) { return text.replace(pattern, '$1$2: '); }
 function formatTitles() {
  document.querySelectorAll('.firstHeading .mw-page-title-separator, .nord-location .mw-page-title-separator').forEach(function(separator) {
   separator.textContent = separator.textContent.replace(/:$/, ': ');
  });
  document.querySelectorAll('.firstHeading, .nord-location, #contentSub, .mw-search-result-heading, .mw-allpages-body a, .mw-category a, .mw-changeslist-title, .mw-contributions-title, .nord-portlet a').forEach(function(element) {
   var walker = document.createTreeWalker(element,NodeFilter.SHOW_TEXT), node;
   while ((node = walker.nextNode())) {
    if (!node.parentElement.closest('code,pre,textarea,script,style')) { node.nodeValue = spaced(node.nodeValue); }
   }
  });
  document.title = spaced(document.title);
 }
 formatTitles();mw.hook('wikipage.content').add(formatTitles);
}());
