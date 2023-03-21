const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/assets/js/app.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css')
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
  .vue({ version: 2 })
  .version()
  .sourceMaps();