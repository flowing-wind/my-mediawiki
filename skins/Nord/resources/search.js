(function () {
 'use strict';
 var form = document.querySelector('.mw-search-form-wrapper form');
 if (!form) { return; }
 var info = form.querySelector('.results-info');
 var help = document.getElementById('mw-indicator-mw-helplink');
 var meta = document.createElement('div');meta.className = 'nord-search-meta';
 form.querySelector('.mw-search-profile-tabs').before(meta);
 if (info) { meta.appendChild(info); }
 if (help) { meta.appendChild(help); }
}());
