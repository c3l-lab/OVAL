import Cookies from 'js-cookie';

function remeberTheme(theme) {
  Cookies.set('theme', theme, { expires: 365, secure: true, sameSite: 'none' } );
}

function toggleTheme() {
  const classList = document.body.classList;
  if (classList.contains('dark')) {
    classList.remove('dark');
    classList.add('light');
    remeberTheme('light');
  } else {
    classList.remove('light');
    classList.add('dark');
    remeberTheme('dark');
  }
}

function init() {
  $('.theme-switch input').on('change', function () {
    toggleTheme();
  });
}

init();
