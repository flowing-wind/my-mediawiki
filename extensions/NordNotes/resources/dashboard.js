(function () {
 'use strict';
 var root = document.getElementById('nord-maintenance');
 if (!root) { return; }
 var api = new mw.Api();
 root.querySelectorAll('[data-report]').forEach(async function (card) {
  var report = card.dataset.report, target = card.querySelector('.nord-report-results');
  var params = {action:'query',formatversion:2};
  if (report === 'AllPages') { Object.assign(params,{list:'allpages',aplimit:5,apnamespace:0}); }
  else if (report === 'Categories') { Object.assign(params,{list:'allpages',apnamespace:14,aplimit:5}); }
  else { Object.assign(params,{list:'querypage',qppage:report,qplimit:5}); }
  try {
   var data = (await api.get(params)).query;
   var pages = report === 'AllPages' || report === 'Categories' ? data.allpages : data.querypage.results;
   target.replaceChildren();
   if (!pages.length) { target.textContent = 'None found.';return; }
   var list = document.createElement('ul');
   pages.forEach(function (page) { var item=document.createElement('li'),link=document.createElement('a');link.href=mw.util.getUrl(page.title);link.textContent=page.title;item.append(link);list.append(item); });
   target.append(list);
  } catch (error) { target.textContent = 'Preview unavailable. Open the heading to view the full report.'; }
 });
}());
