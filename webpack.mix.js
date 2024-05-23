const mix = require('laravel-mix');
const path = require('path');
const tailwindcss = require('tailwindcss');

mix.js('resources/assets/js/app.js', 'public/js')
  .js('resources/assets/js/theme.js', 'public/js')
  .js('resources/assets/js/player.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css')
  .options({
    processCssUrls: false,
    postCss: [tailwindcss('./tailwind.config.js')],
  })
  .sass('resources/assets/sass/theme.scss', 'public/css')
  .sass('resources/assets/sass/player.scss', 'public/css')
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
