const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/assets/js/app.js', 'public/js')
  .js('resources/assets/js/theme.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css')
  .sass('resources/assets/sass/theme.scss', 'public/css')
  .webpackConfig({
    output: {
      chunkFilename: 'js/[name].js?id=[chunkhash]',
    },
    resolve: {
      alias: {
        '@': path.resolve('resources/assets/js'),
      },
    },
  })
  .version()
  .sourceMaps();
