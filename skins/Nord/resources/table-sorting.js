(function () {
 'use strict';
 function enhance() {
  document.querySelectorAll('.nord-body table.sortable').forEach(function(table) {
   if (table.dataset.nordSorting) { return; } table.dataset.nordSorting = 'true';
   function update() {
    table.querySelectorAll('th.headerSort').forEach(function(th) {
     var direction = th.classList.contains('headerSortUp') ? 'ascending' : th.classList.contains('headerSortDown') ? 'descending' : 'none';
     th.setAttribute('aria-sort',direction);
    });
   }
   $(table).on('sortEnd.tablesorter',update);
   new MutationObserver(update).observe(table,{attributes:true,attributeFilter:['class'],subtree:true});update();
  });
 }
 enhance();mw.hook('wikipage.content').add(enhance);
}());
