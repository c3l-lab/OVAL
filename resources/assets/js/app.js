import * as htmx from 'htmx.org';

window.htmx = htmx;

document.body.addEventListener('htmx:configRequest', function(evt) {
  const token = document.querySelector('meta[name="_token"]').getAttribute('content');
  evt.detail.headers['X-CSRF-Token'] = token;
});
